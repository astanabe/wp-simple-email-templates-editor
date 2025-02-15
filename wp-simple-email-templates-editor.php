<?php
/**
 * Plugin Name:       Simple Email Templates Editor
 * Plugin URI:        https://github.com/astanabe/wp-simple-email-templates-editor
 * Description:       A simple email templates editor plugin for WordPress
 * Author:            Akifumi S. Tanabe
 * Author URI:        https://github.com/astanabe
 * License:           GNU General Public License v2
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wp-simple-email-templates-editor
 * Domain Path:       /languages
 * Version:           0.1.0
 * Requires at least: 6.4
 *
 * @package           WP_Simple_Email_Templates_Editor
 */

// Security check
if (!defined('ABSPATH')) {
	exit;
}

// Enforce plain text email
function wp_simple_email_templates_editor_plain_text_email() {
	return 'text/plain';
}
add_filter( 'wp_mail_content_type', 'wp_simple_email_templates_editor_plain_text_email' );

// Add "Edit Email Templates" submenu to "Settings" menu of dashboard
function wp_simple_email_templates_editor_add_edit_email_templates_to_dashboard() {
	add_options_page(
		'Edit Email Templates',
		'Edit Email Templates',
		'manage_options',
		'wp-simple-email-templates-editor-edit-email-templates',
		'wp_simple_email_templates_editor_edit_email_templates_page_screen'
	);
}
add_action('admin_menu', 'wp_simple_email_templates_editor_add_edit_email_templates_to_dashboard');

// Screen function for "Edit Email Templates" submenu of "Settings" menu of dashboard
function wp_simple_email_templates_editor_edit_email_templates_page_screen() {
	?>
	<div class="wrap">
		<h1>Edit Email Templates</h1>
		<form method="post" action="options.php">
			<?php
			settings_fields('wp_simple_email_templates_editor_edit_email_templates');
			do_settings_sections('wp-simple-email-templates-editor-edit-email-templates');
			submit_button();
			?>
		</form>
	</div>
	<?php
}

// Register settings
function wp_simple_email_templates_editor_register_edit_email_templates() {
	register_setting('wp_simple_email_templates_editor_edit_email_templates', 'wp_simple_email_templates_editor_welcome_email_subject');
	register_setting('wp_simple_email_templates_editor_edit_email_templates', 'wp_simple_email_templates_editor_welcome_email_body');
	register_setting('wp_simple_email_templates_editor_edit_email_templates', 'wp_simple_email_templates_editor_reset_password_email_subject');
	register_setting('wp_simple_email_templates_editor_edit_email_templates', 'wp_simple_email_templates_editor_reset_password_email_body');
	add_settings_section(
		'wp_simple_email_templates_editor_email_section',
		'Email Templates',
		function() { echo '<p>Configure the email messages sent to users.</p><p>The following variables can be used in email subjects.</p><ul><li>{user_login}</li><li>{site_title}</li></ul><p>The following variables can be used in welcome email body.</p><ul><li>{user_login}</li><li>{user_email}</li><li>{login_url}</li><li>{home_url}</li>'; if (function_exists('bp_members_get_user_url')) { echo '<li>{profile_url}</li>';} echo '<li>{site_title}</li></ul><p>The following variables can be used in reset password email body.</p><ul><li>{user_login}</li><li>{user_email}</li><li>{login_url}</li><li>{home_url}</li>'; if (function_exists('bp_members_get_user_url')) { echo '<li>{profile_url}</li>';} echo '<li>{site_title}</li><li>{resetpass_url}</li><li>{user_ip}</li></ul>'; },
		'wp-simple-email-templates-editor-edit-email-templates'
	);
	add_settings_field(
		'wp_simple_email_templates_editor_welcome_email_subject',
		'Welcome Email Subject',
		'wp_simple_email_templates_editor_render_text_input',
		'wp-simple-email-templates-editor-edit-email-templates',
		'wp_simple_email_templates_editor_email_section',
		['label_for' => 'wp_simple_email_templates_editor_welcome_email_subject']
	);
	add_settings_field(
		'wp_simple_email_templates_editor_welcome_email_body',
		'Welcome Email Body',
		'wp_simple_email_templates_editor_render_textarea_input',
		'wp-simple-email-templates-editor-edit-email-templates',
		'wp_simple_email_templates_editor_email_section',
		['label_for' => 'wp_simple_email_templates_editor_welcome_email_body']
	);
	add_settings_field(
		'wp_simple_email_templates_editor_reset_password_email_subject',
		'Reset Password Email Subject',
		'wp_simple_email_templates_editor_render_text_input',
		'wp-simple-email-templates-editor-edit-email-templates',
		'wp_simple_email_templates_editor_email_section',
		['label_for' => 'wp_simple_email_templates_editor_reset_password_email_subject']
	);
	add_settings_field(
		'wp_simple_email_templates_editor_reset_password_email_body',
		'Reset Password Email Body',
		'wp_simple_email_templates_editor_render_textarea_input',
		'wp-simple-email-templates-editor-edit-email-templates',
		'wp_simple_email_templates_editor_email_section',
		['label_for' => 'wp_simple_email_templates_editor_reset_password_email_body']
	);
}
add_action('admin_init', 'wp_simple_email_templates_editor_register_edit_email_templates');

// Render function for subject
function wp_simple_email_templates_editor_render_text_input($args) {
	$option = get_option($args['label_for'], '');
	echo '<input type="text" id="' . esc_attr($args['label_for']) . '" name="' . esc_attr($args['label_for']) . '" value="' . esc_attr($option) . '" class="regular-text">';
}

// Render function for body
function wp_simple_email_templates_editor_render_textarea_input($args) {
	$option = get_option($args['label_for'], '');
	echo '<textarea id="' . esc_attr($args['label_for']) . '" name="' . esc_attr($args['label_for']) . '" rows="5" class="large-text">' . esc_textarea($option) . '</textarea>';
}

// Replace welcome email
function wp_simple_email_templates_editor_replace_welcome_email($user_id) {
	$user = get_userdata($user_id);
	$login_url = wp_login_url();
	$home_url = home_url();
	if (function_exists('bp_members_get_user_url')) {
		$profile_url = bp_members_get_user_url($user_id);
	}
	$site_title = get_bloginfo('name');
	$subject = get_option('wp_simple_email_templates_editor_welcome_email_subject', '[{site_title}] Welcome {user_login}!');
	$subject = str_replace(
		array('{user_login}', '{site_title}'),
		array($user->user_login, $site_title),
		$subject
	);
	$body = get_option('wp_simple_email_templates_editor_welcome_email_body', "Hi {user_login},\n\nThank you for registering!\n\nRegards,\n{site_title}");
	$body = str_replace(
		array('{user_login}', '{user_email}', '{login_url}', '{home_url}', '{site_title}'),
		array($user->user_login, $user->user_email, $login_url, $home_url, $site_title),
		$body
	);
	if (isset($profile_url)) {
		$body = str_replace('{profile_url}', $profile_url, $body);
	}
	wp_mail($user->user_email, $subject, $body);
}
add_action('user_register', 'wp_simple_email_templates_editor_replace_welcome_email');

// Replace reset password email body
function wp_simple_email_templates_editor_replace_reset_password_email_body($message, $key, $user_login, $user_data) {
	$login_url = wp_login_url();
	$home_url = home_url();
	if (function_exists('bp_members_get_user_url')) {
		$profile_url = bp_members_get_user_url($user_data->ID);
	}
	$site_title = get_bloginfo('name');
	$resetpass_url = add_query_arg(
		array(
		'action' => 'rp',
		'key'    => $key,
		'login'  => rawurlencode($user_login),
		),
		$login_url
	);
	$user_ip = wp_simple_email_templates_editor_get_client_ip();
	$body = get_option('wp_simple_email_templates_editor_reset_password_email_body', "Hi {user_login},\n\nClick the link below to reset your password:\n{resetpass_url}\n\nRegards,\n{site_title}");
	$body = str_replace(
		array('{user_login}', '{user_email}', '{login_url}', '{home_url}', '{site_title}', '{resetpass_url}', '{user_ip}'),
		array($user_login, $user_data->user_email, $login_url, $home_url, $site_title, $resetpass_url, $user_ip),
		$body
	);
	if (isset($profile_url)) {
		$body = str_replace('{profile_url}', $profile_url, $body);
	}
	return $body;
}
add_filter('retrieve_password_message', 'wp_simple_email_templates_editor_replace_reset_password_email_body', 10, 4);

// Replace reset password email subject
function wp_simple_email_templates_editor_replace_reset_password_email_subject($title, $user_login, $user_data) {
	$site_title = get_bloginfo('name');
	$subject = get_option('wp_simple_email_templates_editor_reset_password_email_subject', '[{site_title}] Password Reset Requested');
	$subject = str_replace(
		array('{user_login}', '{site_title}'),
		array($user_login, $site_title),
		$subject
	);
	return $subject;
}
add_filter('retrieve_password_title', 'wp_simple_email_templates_editor_replace_reset_password_email_subject', 10, 3);
