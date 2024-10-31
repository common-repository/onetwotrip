<?php

/**
 * The file that defines the API handling class
 *
 * @link       https://codecanyon.net/user/wonderburo
 * @since      1.0.0
 *
 * @package    Wp_Ott
 * @subpackage Wp_Ott/includes
 */

/**
 *
 * @since      1.0.0
 * @package    Wp_Ott
 * @subpackage Wp_Ott/includes
 * @author     Sergey Aksenov <sergeax@gmail.com>
 */
class Wp_Ott_API {

	/**
	 * Allowed instrument types
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wp_Ott_API    $allowed_types   Contains array of strings with allowed types slugs
	 */
	public $allowed_types = array('forms', 'tables', 'banners', 'links');

	/**
	 * Base API URL
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wp_Ott_API    $base_url    Contains link to base API url
	 */
	protected $base_url;

	/**
	 * API JWT token
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wp_Ott_API    $token    Contains API token
	 */
	protected $token;

	/**
	 * Base API URL
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wp_Ott_API    $request_error  Contains text for http layer error notice
	 */
	protected $request_error;

	/**
	 * Base API URL
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wp_Ott_API    $last_error  Contains text for app layer error notice
	 */
	protected $last_error;

	/**
	 * Initialize API
	 *
	 * @since    1.0.0
	 */
	public function __construct($base_url = 'https://partner.onetwotrip.com/api/v1') {

		$this->base_url = $base_url;
		$this->request_error = '';
		$this->last_error = '';
		$this->token = '';

	}

	/**
	 * Load API token from wp_options for the first time
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_token($force = false) {
		if (empty($this->token) || $force) {
			$this->token = get_option('wp_ott_token');
			if (empty($this->token)) {
				$this->request_error = __('Unable to make secure request without API token. Please connect to API first.', 'wp-ott');
				add_action('admin_notices', array(&$this, 'api_error'));
				return false;
			}
		}
		return true;
	}

	/**
	 * Interact with API
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function request($action, $args = array(), $method = 'GET', $auth = true) {
		$url = $this->base_url . $action;
		$args['headers'] = array(
			'Content-Type' => 'application/json',
		);
		if ($auth) {
			if ($this->load_token()) {
				$args['headers']['Authorization'] = 'JWT ' . $this->token;
			} else {
				return null;
			}
		}
		if ($method == 'POST') {
			$result = wp_remote_post($url, $args);
		} else {
			$result = wp_remote_get($url, $args);
		}
		if (is_wp_error($result)) {
			$this->request_error = $result->get_error_message();
			add_action('admin_notices', array(&$this, 'api_error'));
			return null;
		}
		$result = json_decode($result['body'], true);
		if (is_null($result)) {
			$this->request_error = __('Empty or invalid API response. Please try again in a few minutes.', 'wp-ott');
			add_action('admin_notices', array(&$this, 'api_error'));
			return null;
		}
		if (isset($result['error'])) {
			$this->request_error = sprintf(__('API returned "%s"', 'wp-ott'), $result['error']);
			add_action('admin_notices', array(&$this, 'api_error'));
			return null;
		}
		return $result;
	}

	/**
	 * Load instrument list from cache or API
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function get_instruments($force = false) {
		if (!$force)
			$result = get_transient('wp_ott_instruments');
		else
			$result = false;
		if ($result === false) {
			$result = $this->request('/programs');
			if (!is_null($result)) {
				set_transient('wp_ott_instruments', $result, 15 * MINUTE_IN_SECONDS);
			}
		}
		return $result;
	}

	/**
	 * Load stats from cache or API
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	public function stats($filter = 'week', $id = 0, $force = true) {
		$slug = sprintf('wp_ott_stats_%s_%s', $filter, $id);
		if (!$force)
			$result = get_transient($slug);
		else
			$result = false;
		if ($result === false) {
			$params = build_query(array(
				'filter' => $filter,
				'section' => 'all',
				'programId' => $id,
			));
			if ($id == 0)
				$params = remove_query_arg('programId', $params);
			$result = $this->request('/stats?' . $params);
			if (!is_null($result)) {
				set_transient($slug, $result, 15 * MINUTE_IN_SECONDS);
			}
		}
		return $result;
	}

	/**
	 * Login to API, get JWT token in exchange for login/password pair
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function login($username, $password) {
		$this->last_error = '';
		$result = $this->request('/login', array(
			'body' => json_encode(array(
				'email' => $username,
				'password' => $password,
			)),
		), 'POST', false);
		if (is_null($result)) {
			return false;
		}
		if (isset($result['status']) && $result['status'] == 'error') {
			$this->last_error = __($result['message'], 'wp-ott');
			add_action('admin_notices', array(&$this, 'login_error'));
			return false;
		}
		if (isset($result['status']) && $result['status'] == 'success' && isset($result['token'])) {
			update_option('wp_ott_username', $username);
			update_option('wp_ott_token', $result['token']);
			return true;
		}
		$this->last_error = sprintf(__('Unknown error. Raw server response: %s', 'wp-ott'), json_encode($result));
		add_action('admin_notices', array(&$this, 'login_error'));
		return false;
	}

	/**
	 * Clear JWT token and all caches
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function logout() {
		$this->last_error = '';

		if (empty(get_option('wp_ott_username'))) {
			$this->last_error = __('No OTT username was set', 'wp-ott');
			add_action('admin_notices', array(&$this, 'logout_error'));
		}
		
		if (empty(get_option('wp_ott_token'))) {
			$this->last_error = __('No OTT token was set', 'wp-ott');
			add_action('admin_notices', array(&$this, 'logout_error'));
		}

		delete_option('wp_ott_username');
		delete_option('wp_ott_token');
		delete_option('wp_ott_autoinsert');
		// Clear all caches
		delete_transient('wp_ott_instruments');
		return empty($this->last_error);
	}

	/**
	 * Get list of instruments filtered by type
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function instrument_list($type = 'forms') {
		$this->last_error = '';
		$raw_data = $this->get_instruments();
		//echo '<pre>'; var_dump($raw_data); echo '</pre>'; 
		if (is_null($raw_data)) return array();
		switch ($type) {
			case 'forms':
				$filter = 'form';
				break;
			case 'tables':
				$filter = 'form_with_result';
				break;
			case 'banners':
				$filter = 'banner';
				break;
			case 'links':
				$filter = 'deeplink';
				break;
			case 'all':
				$filter = 'all';
				break;
		}
		$result = array();
		foreach ($raw_data as $instrument) {
			if ($instrument['type'] == $filter || $filter == 'all') {
				$result[] = array(
					'wp_ott_id' => $instrument['id'],
					'wp_ott_title' => $instrument['name'],
					'wp_ott_active' => $instrument['active'],
				);
			}
		}
		return $result;
	}

	/**
	 * Get single instrument as associative array
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function get_instrument($id) {
		$instruments = $this->get_instruments();
		if (is_null($instruments))
			return false;
		$key = array_search($id, array_column($instruments, 'id'));
		if ($key === false)
			return false;
		else
			return $instruments[$key];
	}

	/**
	 * Get instrument's shortcode
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function shortcode($id) {
		$instrument = $this->get_instrument($id);
		if ($instrument && is_array($instrument) && array_key_exists('type', $instrument) && ($instrument['type'] == 'link' || $instrument['type'] == 'deeplink')) {
			$result = $instrument['url'];
		} else {
			$result = sprintf('[wp-ott-module id="%s"]', $id);
		}
		return $result;
	}

	/**
	 * Get all instruments' autoinsert properties
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function get_autoinserts() {
		return get_option('wp_ott_autoinsert', false);
	}

	/**
	 * Get instrument's autoinsert properties
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function get_autoinsert($id) {
		$raw_data = get_option('wp_ott_autoinsert', array());
		if (array_key_exists($id, $raw_data)) {
			return $raw_data[$id];
		} else {
			return array(
				'mode' 			=> 'disabled',
				'categories'	=> array(),
				'tags'			=> array(),
			);
		}
	}

	/**
	 * Set instrument's autoinsert properties
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function set_autoinsert($id, $data) {
		$id = filter_var($id, FILTER_VALIDATE_INT, array("options" => array("min_range" => 1)));
		if ($id === false) return;
		$filtered_data = array(
			'mode' 			=> 'disabled',
			'categories'	=> array(),
			'tags'			=> array(),
		);
		if (isset($data['mode']) && in_array($data['mode'], array('disabled', 'top', 'bottom', 'both'))) {
			$filtered_data['mode'] = $data['mode'];
		}
		if (isset($data['categories']) && is_array($data['categories'])) {
			//TODO: check if all categories id's are valid
			$filtered_data['categories'] = $data['categories'];
		}
		if (isset($data['tags']) && is_array($data['tags'])) {
			//TODO: check if all tags id's are valid
			$filtered_data['tags'] = $data['tags'];
		}
		$raw_data = get_option('wp_ott_autoinsert', array());
		$raw_data[$id] = $filtered_data;
		update_option('wp_ott_autoinsert', $raw_data);
	}

	/**
	 * Common API error notice callback
	 *
	 * @since    1.0.0
	 */
	public function api_error() {
		$class = 'notice notice-error';
		$message = sprintf(__('Server error: %s.', 'wp-ott'), $this->request_error);
		printf('<div class="%1$s"><p>%2$s</p></div>', $class, $message);
	}

	/**
	 * Login error notice callback
	 *
	 * @since    1.0.0
	 */
	public function login_error() {
		$class = 'notice notice-error';
		$message = sprintf(__('Login error: %s.', 'wp-ott'), $this->last_error);
		printf('<div class="%1$s"><p>%2$s</p></div>', $class, $message);
	}

	/**
	 * Logout error notice callback
	 *
	 * @since    1.0.0
	 */
	public function logout_error() {
		$class = 'notice notice-error';
		$message = sprintf(__('Logout error: %s.', 'wp-ott'), $this->last_error);
		printf('<div class="%1$s"><p>%2$s</p></div>', $class, $message);
	}
}
