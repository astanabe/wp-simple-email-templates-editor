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
add_filter('wp_mail_content_type', 'wp_simple_email_templates_editor_plain_text_email');

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
	if (get_option('wp_simple_email_templates_editor_welcome_email_subject') == false) {
		update_option('wp_simple_email_templates_editor_welcome_email_subject', '[{site_title}] Welcome {user_login}');
	}
	if (get_option('wp_simple_email_templates_editor_welcome_email_body') == false) {
		update_option('wp_simple_email_templates_editor_welcome_email_body', "Hello {user_login},\n\nThank you for registering to {site_title}.\nWe added your account to {site_title}\nYour username of this site is \"{user_login}\" and your registered E-mail address is \"{user_email}\".\nYou can login to your account using above username and configured password via the following URL.\n{login_url}\n\nBest regards,\n-- \n{site_title} admin team\n");
	}
	if (get_option('wp_simple_email_templates_editor_reset_password_email_subject') == false) {
		update_option('wp_simple_email_templates_editor_reset_password_email_subject', '[{site_title}] Password Reset Requested for {user_login}');
	}
	if (get_option('wp_simple_email_templates_editor_reset_password_email_body') == false) {
		update_option('wp_simple_email_templates_editor_reset_password_email_body', "Hello {user_login},\n\nSomeone requested that the password is reset for your account \"{user_login}\".\nIf this was a mistake, just ignore this email and nothing will happen.\nTo reset your password, visit the following URL.\n{resetpass_url}\nThis password reset request originated from the IP address \"{user_ip}\".\n\nBest regards,\n-- \n{site_title} admin team\n");
	}
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

// Disable "Login Details" email
function wp_simple_email_templates_editor_disable_login_details_email($wp_new_user_notification_email, $user, $blogname) {
	$wp_new_user_notification_email['to'] = '';
	return $wp_new_user_notification_email;
}
add_filter('wp_new_user_notification_email', 'wp_simple_email_templates_editor_disable_login_details_email', 10, 3);

// Disable BuddyPress welcome email
function wp_simple_email_templates_editor_disable_buddypress_welcome_email() {
	if (function_exists('bp_send_welcome_email')) {
		remove_action('bp_core_activated_user', 'bp_send_welcome_email', 10);
	}
}
add_action('bp_loaded', 'wp_simple_email_templates_editor_disable_buddypress_welcome_email');

// Add welcome email
function wp_simple_email_templates_editor_replace_welcome_email($user_id) {
	$user = get_userdata($user_id);
	$login_url = wp_login_url();
	$home_url = home_url();
	if (function_exists('bp_members_get_user_url')) {
		$profile_url = bp_members_get_user_url($user_id);
	}
	$site_title = get_bloginfo('name');
	$subject = get_option('wp_simple_email_templates_editor_welcome_email_subject', '[{site_title}] Welcome {user_login}');
	$subject = str_replace(
		['{user_login}', '{site_title}'],
		[$user->user_login, $site_title],
		(string) ($subject ?? '')
	);
	$body = get_option('wp_simple_email_templates_editor_welcome_email_body', "Hello {user_login},\n\nThank you for registering to {site_title}.\nWe added your account to {site_title}\nYour username of this site is \"{user_login}\" and your registered E-mail address is \"{user_email}\".\nYou can login to your account using above username and configured password via the following URL.\n{login_url}\n\nBest regards,\n-- \n{site_title} admin team\n");
	$body = str_replace(
		['{user_login}', '{user_email}', '{login_url}', '{home_url}', '{site_title}'],
		[$user->user_login, $user->user_email, $login_url, $home_url, $site_title],
		(string) ($body ?? '')
	);
	if (isset($profile_url)) {
		$body = str_replace('{profile_url}', $profile_url, (string) ($body ?? ''));
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
		[
		'action' => 'rp',
		'key'    => $key,
		'login'  => rawurlencode($user_login),
		],
		$login_url
	);
	$user_ip = wp_simple_email_templates_editor_get_client_ip();
	$body = get_option('wp_simple_email_templates_editor_reset_password_email_body', "Hello {user_login},\n\nSomeone requested that the password is reset for your account \"{user_login}\".\nIf this was a mistake, just ignore this email and nothing will happen.\nTo reset your password, visit the following URL.\n{resetpass_url}\nThis password reset request originated from the IP address \"{user_ip}\".\n\nBest regards,\n-- \n{site_title} admin team\n");
	$body = str_replace(
		['{user_login}', '{user_email}', '{login_url}', '{home_url}', '{site_title}', '{resetpass_url}', '{user_ip}'],
		[$user_login, $user_data->user_email, $login_url, $home_url, $site_title, $resetpass_url, $user_ip],
		(string) ($body ?? '')
	);
	if (isset($profile_url)) {
		$body = str_replace('{profile_url}', $profile_url, (string) ($body ?? ''));
	}
	return $body;
}
add_filter('retrieve_password_message', 'wp_simple_email_templates_editor_replace_reset_password_email_body', 10, 4);

// Replace reset password email subject
function wp_simple_email_templates_editor_replace_reset_password_email_subject($title, $user_login, $user_data) {
	$site_title = get_bloginfo('name');
	$subject = get_option('wp_simple_email_templates_editor_reset_password_email_subject', '[{site_title}] Password Reset Requested for {user_login}');
	$subject = str_replace(
		['{user_login}', '{site_title}'],
		[$user_login, $site_title],
		(string) ($subject ?? '')
	);
	return $subject;
}
add_filter('retrieve_password_title', 'wp_simple_email_templates_editor_replace_reset_password_email_subject', 10, 3);

// Function to obtain client IP
function wp_simple_email_templates_editor_get_client_ip() {
	if (!empty($_SERVER['CF-Connecting-IP']) && filter_var($_SERVER['CF-Connecting-IP'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
		return sanitize_text_field($_SERVER['CF-Connecting-IP']);
	}
	if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip_list = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
		foreach ($ip_list as $ip) {
			$ip = trim($ip);
			if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
				return sanitize_text_field($ip);
			}
		}
	}
	if (!empty($_SERVER['REMOTE_ADDR']) && filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
		return sanitize_text_field($_SERVER['REMOTE_ADDR']);
	}
	return 'UNKNOWN';
}

// Page for deactivation
function wp_simple_email_templates_editor_deactivate_page() {
	if (!current_user_can('manage_options')) {
		return;
	}
	if (isset($_POST['wp_simple_email_templates_editor_deactivate_confirm']) && check_admin_referer('wp_simple_email_templates_editor_deactivate_confirm', 'wp_simple_email_templates_editor_deactivate_confirm_nonce')) {
		if ($_POST['wp_simple_email_templates_editor_deactivate_confirm'] === 'remove') {
			update_option('wp_simple_email_templates_editor_uninstall_settings', 'remove');
		}
		else {
			update_option('wp_simple_email_templates_editor_uninstall_settings', 'keep');
		}
		deactivate_plugins(plugin_basename(__FILE__));
		wp_safe_redirect(admin_url('plugins.php?deactivated=true'));
		exit;
	}
	?>
	<div class="wrap">
		<h2>Deactivate Simple Email Templates Editor Plugin</h2>
		<form method="post">
			<?php wp_nonce_field('wp_simple_email_templates_editor_deactivate_confirm', 'wp_simple_email_templates_editor_deactivate_confirm_nonce'); ?>
			<p>Do you want to remove all settings of this plugin when uninstalling?</p>
			<p>
				<label>
					<input type="radio" name="wp_simple_email_templates_editor_deactivate_confirm" value="keep" checked />
					Leave settings (default)
				</label>
			</p>
			<p>
				<label>
					<input type="radio" name="wp_simple_email_templates_editor_deactivate_confirm" value="remove" />
					Remove all settings
				</label>
			</p>
			<p>
				<input type="submit" class="button button-primary" value="Deactivate" />
			</p>
		</form>
	</div>
	<?php
	exit;
}

// Intercept deactivation request and redirect to confirmation screen
function wp_simple_email_templates_editor_deactivate_hook() {
	if (isset($_GET['action']) && $_GET['action'] === 'deactivate' && isset($_GET['plugin']) && $_GET['plugin'] === plugin_basename(__FILE__)) {
		wp_safe_redirect(admin_url('admin.php?page=wp-simple-email-templates-editor-deactivate'));
		exit;
	}
}
add_action('admin_init', 'wp_simple_email_templates_editor_deactivate_hook');

// Add deactivation confirmation page to the admin menu
function wp_simple_email_templates_editor_add_deactivate_page() {
	add_submenu_page(
		null, // No parent menu, hidden page
		'Deactivate Simple Email Templates Editor Plugin',
		'Deactivate Simple Email Templates Editor Plugin',
		'manage_options',
		'wp-simple-email-templates-editor-deactivate',
		'wp_simple_email_templates_editor_deactivate_page'
	);
}
add_action('admin_menu', 'wp_simple_email_templates_editor_add_deactivate_page');

// Remove all settings when uninstalling if specified
function wp_simple_email_templates_editor_uninstall() {
	if (get_option('wp_simple_email_templates_editor_uninstall_settings') === 'remove') {
		delete_option('wp_simple_email_templates_editor_welcome_email_subject');
		delete_option('wp_simple_email_templates_editor_welcome_email_body');
		delete_option('wp_simple_email_templates_editor_reset_password_email_subject');
		delete_option('wp_simple_email_templates_editor_reset_password_email_body');
		delete_option('wp_simple_email_templates_editor_uninstall_settings');
	}
}
register_uninstall_hook(__FILE__, 'wp_simple_email_templates_editor_uninstall');
