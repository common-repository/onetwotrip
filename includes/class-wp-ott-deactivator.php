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
class Wp_Ott_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		$api = new Wp_Ott_API();
		$api->logout();
	}

}
