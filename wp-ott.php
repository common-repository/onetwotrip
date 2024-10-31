<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://codecanyon.net/user/wonderburo
 * @since             1.0.0
 * @package           Wp_Ott
 *
 * @wordpress-plugin
 * Plugin Name:       OneTwoTrip
 * Plugin URI:        https://wordpress.org/plugins/wp-ott/
 * Description:       Easily add airline tickets, train tickets and hotel booking forms and listings to your site. Register at <a href="https://partner.onetwotrip.com/">partner.onetwotrip.com</a> and earn a comission with each successful transaction.
 * Version:           1.0.0
 * Author:            OneTwoTrip
 * Author URI:        http://partner.onetwotrip.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-ott
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-ott-activator.php
 */
function activate_wp_ott() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-ott-activator.php';
	Wp_Ott_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-ott-deactivator.php
 */
function deactivate_wp_ott() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-ott-deactivator.php';
	Wp_Ott_Deactivator::deactivate();
}

/**
 * The code that runs during plugin uninstallation.
 * This action is documented in includes/class-wp-ott-uninstaller.php
 */
function uninstall_wp_ott() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-ott-uninstaller.php';
	Wp_Ott_Uninstaller::uninstall();
}

register_activation_hook(__FILE__, 'activate_wp_ott');
register_deactivation_hook(__FILE__, 'deactivate_wp_ott');
register_uninstall_hook(__FILE__, 'uninstall_wp_ott');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-ott.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_ott() {

	$plugin = new Wp_Ott();
	$plugin->run();

}
run_wp_ott();
