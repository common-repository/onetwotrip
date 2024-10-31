<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://codecanyon.net/user/wonderburo
 * @since      1.0.0
 *
 * @package    Wp_Ott
 * @subpackage Wp_Ott/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Ott
 * @subpackage Wp_Ott/public
 * @author     Sergey Aksenov <sergeax@gmail.com>
 */
class Wp_Ott_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wp-ott-public.css', array(), $this->version, 'all');

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wp-ott-public.js', array('jquery'), $this->version, false);

	}

	/**
	 * Register shortcode
	 *
	 * @since    1.0.0
	 */
	public function init() {
		add_filter('the_content', array($this, 'process_content'));
		add_shortcode('wp-ott-module', array($this, 'process_shortcode'));
	}

	/**
	 * Process shortcode
	 *
	 * @since    1.0.0
	 */
	public function process_shortcode($atts) {
		$a = shortcode_atts(array('id' => ''), $atts);
		if ($a['id'] === '') {
			return __('Shortcode "wp-ott-module" error: attribute "id" must be specified.', 'wp-ott');
		}
		return sprintf('<script charset="utf-8" src="//partner.onetwotrip.com/build/widget/form.widget.load.js?id=%s" async></script>', $a['id']);
	}

	/**
	 * Process content to add shortcodes automatically
	 *
	 * @since    1.0.0
	 */
	public function process_content($content) {
		global $post, $wp_ott_recursion;
		if(!is_singular() || !is_main_query() || !in_the_loop() || $wp_ott_recursion) {
			return $content;
		}
		$api = new Wp_Ott_API();
		$cache = $api->get_autoinserts();
		if ($cache === false) { // No items to display at all
			return $content;
		}
		$filtered = array();
		$categories = wp_get_object_terms($post->ID, 'category', array('fields' => 'ids'));
		$tags = wp_get_object_terms($post->ID, 'post_tag', array('fields' => 'ids'));
		foreach ($cache as $id => $item) {
			if ($item['mode'] == 'disabled')
				continue;
			if (array_intersect($categories, $item['categories']) || array_intersect($tags, $item['tags'])) {
				$filtered[$id] = $item;
			}
		}
		if (empty($filtered)) { // No items found
			return $content;
		}

		$top = '';
		$bottom = '';
		foreach ($filtered as $id => $item) {
			$extra_content = do_shortcode($api->shortcode($id));
			if ($item['mode'] == 'top' || $item['mode'] == 'both') {
				$top .= $extra_content;
			}
			if ($item['mode'] == 'bottom' || $item['mode'] == 'both') {
				$bottom .= $extra_content;
			}
		}

		$wp_ott_recursion = true;
		if (!empty($top)) {
			$content = '<div class="wp_ott_container_top">' . $top . '</div>' . $content;
		}
		if (!empty($bottom)) {
			$content .= '<div class="wp_ott_container_bottom">' . $bottom . '</div>';
		}
		$wp_ott_recursion = false;

		return $content;
	}

}
