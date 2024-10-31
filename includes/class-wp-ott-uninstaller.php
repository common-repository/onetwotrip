<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://codecanyon.net/user/wonderburo
 * @since      1.0.0
 *
 * @package    Wp_Ott
 * @subpackage Wp_Ott/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Wp_Ott
 * @subpackage Wp_Ott/includes
 * @author     Sergey Aksenov <sergeax@gmail.com>
 */
class Wp_Ott_Uninstaller {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function uninstall() {
		if (!class_exists('Wp_Ott_API')) {
			require_once(plugin_dir_url(__FILE__) . 'class-wp-ott-api.php');
		}
		$api = new Wp_Ott_API();
		$api->logout();
	}

}
