<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://codecanyon.net/user/wonderburo
 * @since      1.0.0
 *
 * @package    Wp_Ott
 * @subpackage Wp_Ott/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Ott
 * @subpackage Wp_Ott/admin
 * @author     Sergey Aksenov <sergeax@gmail.com>
 */
class Wp_Ott_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Ott_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Ott_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wp-ott-admin.css', array(), $this->version, 'all');
		wp_enqueue_style('jqplot', plugin_dir_url(__FILE__) . 'css/jquery.jqplot.min.css', array(), '1.0.9', 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Ott_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Ott_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wp-ott-admin.js', array('jquery'), $this->version, false);
		wp_enqueue_script('clipboard-js', plugin_dir_url(__FILE__) . 'js/clipboard.min.js', array(), '1.5.15', false);
		wp_enqueue_script('jqplot', plugin_dir_url(__FILE__) . 'js/jquery.jqplot.min.js', array('jquery'), '1.0.9', false);
		wp_enqueue_script('jqplot.dateAxisRenderer', plugin_dir_url(__FILE__) . 'js/plugins/jqplot.dateAxisRenderer.js', array('jqplot'), '1.0.9', false);
		wp_enqueue_script('jqplot.barRenderer', plugin_dir_url(__FILE__) . 'js/plugins/jqplot.barRenderer.js', array('jqplot'), '1.0.9', false);
		
	}

	/**
	 * Init admin menu and its items
	 *
	 * @since    1.0.0
	 */
	public function init_menu() {

		add_menu_page(
			__('OneTwoTrip', 'wp-ott'),
			__('OneTwoTrip','wp-ott'),
			'manage_options',
			'wp-ott',
			array(&$this, 'main_page'),
			'dashicons-products',
			22
		);

		add_submenu_page(
			'wp-ott',
			__('Forms', 'wp-ott'),
			__('Forms','wp-ott'),
			'manage_options',
			'wp-ott-forms',
			array(&$this, 'forms_page')
		);

		add_submenu_page(
			'wp-ott',
			__('Tables', 'wp-ott'),
			__('Tables','wp-ott'),
			'manage_options',
			'wp-ott-tables',
			array(&$this, 'tables_page')
		);

		add_submenu_page(
			'wp-ott',
			__('Banners', 'wp-ott'),
			__('Banners','wp-ott'),
			'manage_options',
			'wp-ott-banners',
			array(&$this, 'banners_page')
		);

		add_submenu_page(
			'wp-ott',
			__('Links', 'wp-ott'),
			__('Links','wp-ott'),
			'manage_options',
			'wp-ott-links',
			array(&$this, 'links_page')
		);

	}

	/**
	 * Process form input
	 *
	 * @since    1.0.0
	 */
	public function process_input() {
		$action = empty($_POST['wp_ott_action']) ? '' : $_POST['wp_ott_action'];
		if ($action == 'login') {
			$username = empty($_POST['wp_ott_username']) ? '' : $_POST['wp_ott_username'];
			$password = empty($_POST['wp_ott_password']) ? '' : $_POST['wp_ott_password'];
			if (empty($username) || empty($password)) {
				add_action('admin_notices', array(&$this, 'empty_login_error'));
			} else {
				$api = new Wp_Ott_API();
				if ($api->login($username, $password)) {
					add_action('admin_notices', array(&$this, 'login_success'));
				};
			}
		}
		if ($action == 'logout') {
			$api = new Wp_Ott_API();
			if ($api->logout()) {
				add_action('admin_notices', array(&$this, 'logout_success'));
			};
		}
	}

	/**
	 * Empty username or password notice callback
	 *
	 * @since    1.0.0
	 */
	public function empty_login_error() {
		$class = 'notice notice-error';
		$message = __('Username or password should not be blank.', 'wp-ott');
		printf('<div class="%1$s"><p>%2$s</p></div>', $class, $message);
	}

	/**
	 * User succesfully logged in to API
	 *
	 * @since    1.0.0
	 */
	public function login_success() {
		$class = 'notice notice-success';
		$message = __('You are now connected to OTT Partner API. Your may now edit your forms and tables.', 'wp-ott');
		printf('<div class="%1$s"><p>%2$s</p></div>', $class, $message);
	}

	/**
	 * User succesfully logged out from API
	 *
	 * @since    1.0.0
	 */
	public function logout_success() {
		$class = 'notice notice-success';
		$message = __('You have been disconnected from OTT Partner API. Your shortcodes will continue to work.', 'wp-ott');
		printf('<div class="%1$s"><p>%2$s</p></div>', $class, $message);
	}

	/**
	 * Stats/login page callback
	 *
	 * @since    1.0.0
	 */
	public function main_page() {
    	$token = get_option('wp_ott_token');
    	if (empty($token)) {
    		// Display login form
			require_once(plugin_dir_path(__FILE__) . 'partials/wp-ott-login.php');
    	} else {
    		// Display stats
			$api = new Wp_Ott_API();
			require_once(plugin_dir_path(__FILE__) . 'partials/wp-ott-stats.php');
    	}
	}

	/**
	 * Handles edit form submit
	 *
	 * @since    1.0.0
	 */
	private function unified_page($type) {
		$api = new Wp_Ott_API();
		if (isset($_GET['action']) && $_GET['action'] == 'edit') {
			$instrument = $api->get_instrument($_GET['id']);
			if ($instrument === false) {
				echo sprintf(__('Instrument with id=%s is not found.'), $_GET['id']);
			} else {
				if (!empty($_POST['wp_ott_action']) && $_POST['wp_ott_action'] == 'update') {
					$api->set_autoinsert($instrument['id'], array(
						'mode' 			=> empty($_POST['wp_ott_autoinsert_mode']) ? 'disabled' : $_POST['wp_ott_autoinsert_mode'],
						'categories'	=> empty($_POST['wp_ott_categories']) ? array() : $_POST['wp_ott_categories'],
						'tags'			=> empty($_POST['wp_ott_tags']) ? array() : $_POST['wp_ott_tags'],
					));
				}
				require_once(plugin_dir_path(__FILE__) . 'partials/wp-ott-edit.php');
			}
		} else {
			require_once(plugin_dir_path(__FILE__) . 'partials/wp-ott-list.php');
		}

	}

	/**
	 * Forms page callback
	 *
	 * @since    1.0.0
	 */
	public function forms_page() {
		$this->unified_page('form');
	}

	/**
	 * Tables page callback
	 *
	 * @since    1.0.0
	 */
	public function tables_page() {
		$this->unified_page('table');
	}

	/**
	 * Tables page callback
	 *
	 * @since    1.0.0
	 */
	public function banners_page() {
		$this->unified_page('banner');
	}

	/**
	 * Tables page callback
	 *
	 * @since    1.0.0
	 */
	public function links_page() {
		$this->unified_page('link');
	}

}
