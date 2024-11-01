<?php

/**
* Plugin Name: Xoo Disable Updates
* Plugin URI: http://xoocode.com/xoo-disable-updates?utm_source=plugin
* Description: Completely disable automatic updates for chosen wordpress themes and plugins.
* Author: Peter Valenta
* Author URI: http://xoocode.com/?utm_source=plugin
* Version: 1.0.0
* License: GPL2
* License URI: https://www.gnu.org/licenses/gpl-2.0.html
* Text Domain: xoo-xdu
* Domain Path: /languages
*/

// If this file is called directly or plugin is already defined, abort.
if (!defined('WPINC')) {
	die;
}

include 'xoo-common.php';

define('XDU_VERSION', '1.0.0');
define('XDU_FILE_PATH', dirname(__FILE__));
define('XDU_DIR_NAME', basename(XDU_FILE_PATH));
define('XDU_FOLDER', dirname(plugin_basename(__FILE__)));
define('XDU_NAME', plugin_basename(__FILE__));
define('XDU_DIR', WP_CONTENT_DIR . '/plugins/' . XDU_FOLDER);
define('XDU_OPTIONS', 'xdu_' . get_current_blog_id() . '_options');

/**
 * xdu main page.
 */
function xdu_page() {
	if (!current_user_can('manage_options')) {
		wp_die(__('You do not have sufficient privilages to use Xoo Disable Plugins. You need manage_options rights, talk to your administrator.'));
	}
	$options = xdu_load();
	if(isset($options['theme'])) {
		$themes = $options['theme'];
	}
	if(isset($options['plugin'])) {
		$plugins = $options['plugin'];
	} 
	$checked = [];
	foreach($options as $option) {
		foreach($option as $name => $value) {
			if(filter_var($value, FILTER_VALIDATE_BOOLEAN)) {
				$checked[$name] = ' checked';
			} else {
				$checked[$name] = '';
			}
		}
	}
?>
<div class="wrap">
	<h1><?php echo _e('Xoo Disable Updates','xoo-xdu'); ?></h1>
	<div class="xoo-options-expl">
		<p><?php echo _e('Allthough we do not advise you to regurarly disable updates to plugins and themes in Wordpress there are some valid reasons to do so. In certain scenarios it can be necessary or prudent to disable the reminder for updates or even the update itself of specific plugins and themes. Use the sliders below to achieve just that. You can leave a plugin or themes update notification disabled temporarily for as long as you like, or permanently.','xoo-xdu'); ?></p>
		<p><?php echo _e('For general use we recommend never to disable any updates or themes in Wordpress. Updates make sure you always have the latest code for your plugins and themes. This code usually includes important updates, bugfixes and maybe most crucial security measures to keep you and your assets safe. Make sure you have a valid reason before temporarily disabling any update reminder with this plugin.','xoo-xdu'); ?></p>
	</div>
	<div class="xoo-select-wrapper">
	
		<?php // Themes HTML ?>
			<div id="xdu_theme_wrapper" class="xoo-list-toggles">
				<h2><?php echo _e('Themes','xoo-xdu'); ?></h2>
				<div class="xoo-toggle-all-wrapper">
					<input type="submit" id="xdu_theme_disable" class="button-secondary xoo-toggle-all" value="<?php _e("Uncheck all", "xoo-xdu"); ?>"/>
					<input type="submit" id="xdu_theme_enable" class="button-secondary xoo-toggle-all" value="<?php _e("Disable all", "xoo-xdu"); ?>"/>
				</div>
				
				<?php 
				foreach(wp_get_themes() as $theme_slug => $theme) { 
				?>
				<div id="<?php echo esc_attr('xdu_theme_' . $theme_slug); ?>" class="theme xoo-toggle-wrap">
					<label class="xoo-switch">
						<input type="checkbox" id="<?php echo esc_attr($theme_slug); ?>" class="xdu-trigger xdu-theme-check"<?php if(isset($checked[$theme_slug])) echo esc_attr($checked[$theme_slug]); ?>>
						<span class="xoo-toggle"></span>
					</label><span class="xoo-label"><?php echo esc_html(wp_get_theme($theme_slug)); ?></span>
				</div>
				<?php
				}
				?>
			</div>
		<?php // End themes HTML ?>

		<?php // Plugins HTML ?>
			<div id="xdu_plugin_wrapper" class="xoo-list-toggles">
				<h2><?php echo _e('Plugins','xoo-xdu'); ?></h2>
				<div class="xoo-toggle-all-wrapper">
					<input type="submit" id="xdu_plugin_disable" class="button-secondary xoo-toggle-all" value="<?php _e("Uncheck all", "xoo-xdu"); ?>"/>
					<input type="submit" id="xdu_plugin_enable" class="button-secondary xoo-toggle-all" value="<?php _e("Disable all", "xoo-xdu"); ?>"/>
				</div>
				
				<?php 
				foreach(get_plugins() as $plugin_path => $plugin) { 
				?>
				<div id="<?php echo esc_attr('xdu_plugin_' . $plugin_path); ?>" class="plugin xoo-toggle-wrap">
					<label class="xoo-switch">
						<input type="checkbox" id="<?php echo esc_attr($plugin_path); ?>" class="xdu-trigger xdu-plugin-check"<?php if(isset($checked[$plugin_path])) echo esc_attr($checked[$plugin_path]); ?>>
						<span class="xoo-toggle"></span>
					</label><span class="xoo-label"><?php echo esc_html($plugin['Name']); ?></span>
				</div>
				<?php
				}
				?>
			</div>	
		<?php // End plugins HTML ?> 
		
	</div>
	<div class="xoo-footer-info"><p><?php echo _e('Using PHP vesion: ' . phpversion(),'xoo-xdu'); ?></p></div>
</div>
<?php
}

function xdu_trigger() {
	// Verify and sanitize
	if(!isset($_POST['xdu_nonce']) || !wp_verify_nonce($_POST['xdu_nonce'], 'xdu-nonce')) {
		die('Unauthorised');
	}
	if(!isset($_POST['options']) || !is_array($_POST['options'])) {
		die();
	}
	foreach($_POST['options'] as &$option) {
		if(!isset($option['type']) || !is_string($option['type'])) {
			die();
		} else {
			$option['type'] = sanitize_text_field($option['type']);
		}
		if(!isset($option['slug']) || !is_string($option['slug'])) {
			die();
		} else {
			$option['slug'] = sanitize_text_field($option['slug']);
		}
		if(!isset($option['checked']) || !is_string($option['checked']) || !($option['checked'] === 'true' || $option['checked'] === 'false')) {
			die();
		} else {
			$option['checked'] = sanitize_text_field($option['checked']);
		}
	}
	
	// Load & save/overwrite options
	$saved_options = xdu_load();
	foreach($_POST['options'] as $sanitized_option) {	
		$saved_options[$sanitized_option['type']][$sanitized_option['slug']] = $sanitized_option['checked'];
	}
	xoo_log(XDU_OPTIONS);
	update_option(XDU_OPTIONS, $saved_options, '', 'no');
}
add_action('wp_ajax_xdu_trigger', 'xdu_trigger');

/**
* Disable select updates.
*/
function xdu_plugins($transient_plugins) {
	$options = xdu_load();
	if(!isset($options['plugin']) || count($options['plugin']) < 1) {
		return $transient_plugins;
	}
	$plugins = $options['plugin'];
	foreach($plugins as $plugin => $value) {
		if(filter_var($value, FILTER_VALIDATE_BOOLEAN)) {
			unset($transient_plugins->response[$plugin]);
		}
	}
	return $transient_plugins;
}
add_filter('site_transient_update_plugins', 'xdu_plugins');

/**
* Disable select themes.
*/
function xdu_themes($transient_themes) {
	$options = xdu_load();
	if(!isset($options['theme']) || count($options['theme']) < 1) {
		return $transient_themes;
	}
	$themes = $options['theme'];
	foreach($themes as $theme => $value) {
		if(filter_var($value, FILTER_VALIDATE_BOOLEAN)) {
			if(isset($transient_themes->response[$theme])) {
				unset($transient_themes->response[$theme]);
			}
		}
	}
	return $transient_themes;
}
add_filter('site_transient_update_themes', 'xdu_themes');

// Get xdu settings for blog
function xdu_load() {
	return get_option(XDU_OPTIONS);
}

/**
 * Register xdu main menu under the xoo main menu. 
 * Also register xoo main menu if this is the first xoo plugin installed.
 */
function xdu_menu_register() {
	// Add the main menu page for Xoo if it doesnt exist
	if (empty($GLOBALS['admin_page_hooks']['xoo_menu'])) {
		// Will add a duplicate submenu
		add_menu_page('Xoo', 'Xoo', 'manage_options', 'xoo_menu', 'xoo_main_menu_page', plugins_url(XDU_FOLDER . '/static/img/xoocode.favicon.menu.png'), '98.999999999901');
		$main_menu_was_set = true;
	}
	
	// Adds the sub page for xdu
	global $xdu_page;
    $xdu_page = add_submenu_page('xoo_menu', 'Xoo Disable Updates', 'Xoo Disable Updates', 'manage_options', 'xoo_disable_updates', 'xdu_page');
	
	// Remove the duplicate submenu if it was set above
	if(isset($main_menu_was_set)) {
		remove_submenu_page('xoo_menu','xoo_menu');
	}
}
add_action('admin_menu', 'xdu_menu_register');

function xdu_load_scripts($hook) {
	global $xdu_page;
	if($hook != $xdu_page) 
		return;	
	wp_enqueue_style('xdu-css', plugin_dir_url(__FILE__) . 'css/xdu.css', '', xoo_version_id());
	wp_enqueue_script('xdu-ajax', plugin_dir_url(__FILE__) . 'js/xdu-ajax.js', array('jquery'), xoo_version_id());
	wp_localize_script('xdu-ajax', 'xdu_vars', array('xdu_nonce' => wp_create_nonce('xdu-nonce')));
}
add_action('admin_enqueue_scripts', 'xdu_load_scripts');