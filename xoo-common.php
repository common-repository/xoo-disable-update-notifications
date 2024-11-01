<?php

/**
 * xoo logging to debug.
 */
if(!function_exists('xoo_log')) {
	function xoo_log($str) {
		if (WP_DEBUG === true) {
			$str_prefix = get_plugin_data( __FILE__ )['Name'] . ': ';
			
			if (is_array($str) || is_object($str)) {
				error_log($str_prefix . print_r($str, true));
			} else {
				error_log($str_prefix . $str);
			}
		}
	}
}

/**
 * xoo boolean to string.
 */
if(!function_exists('xoo_eval')) {
	function xoo_eval($bool) {
		return $bool ? 'true' : 'false';
	}
}

/**
 * xoo force reload of all resources on debug.
 */
if(!function_exists('xoo_version_id')) {
	function xoo_version_id() {
	  if ( WP_DEBUG )
		return time();
	  return VERSION;
	}
}

/**
 * pretty-printed memory usage
 */
if(!function_exists('xoo_mem')) {
	function xoo_mem() {
		return number_format(memory_get_usage()/1000000,2) . 'MB';
	}
}

/**
 * xoo main sub menu page - will be removed and should never display.
 */
if(!function_exists('xoo_main_menu_page')) {
	function xoo_main_menu_page() {
		echo '<h2>XooCode Main Admin Settings</h2>';
		echo '<p>This is the main admin page for XooCode WordPress Plugins.</p>';
		echo '<p>For more information on all XooCode products please visit <a href="http://xoocode.com/wordpress/plugins/">XooCode WordPress Plugins</a> on the official xoocode.com webpage.';
	}
}
