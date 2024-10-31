<?php

/**
 * The file that defines the standard WP-based list table
 *
 * @link       https://codecanyon.net/user/wonderburo
 * @since      1.0.0
 *
 * @package    Wp_Ott
 * @subpackage Wp_Ott/admin
 */

/**
 * See: https://gist.github.com/Latz/7f923479a4ed135e35b2
 *
 * @since      1.0.0
 * @package    Wp_Ott
 * @subpackage Wp_Ott/admin
 * @author     Sergey Aksenov <sergeax@gmail.com>
 */

if (!class_exists('WP_List_Table')) {
	require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class Wp_Ott_List_Table extends WP_List_Table {

	/**
	 * Instruments type
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wp_Ott_API    $type  Type of instruments to display (forms/tables)
	 */
	protected $type;

	/**
	 * API interaction object
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wp_Ott_API    $api  Array of data to be loaded
	 */
	protected $api;

	/**
	 * Data to be loaded from API
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wp_Ott_API    $api_data  Array of data to be loaded
	 */
	protected $api_data;

	/**
	 * Initialize class, set instrument type
	 *
	 * @since    1.0.0
	 */
	public function __construct($type = '') {

		$this->api = new Wp_Ott_API();
		if (!in_array($type, $this->api->allowed_types)) {
			$type = array_pop($allowed_types);
		}
		$this->type = $type;
		switch ($this->type) {
			case 'forms':
				$singular = __('Form', 'wp-ott');
				$plural = __('Forms', 'wp-ott');
				break;
			case 'tables':
				$singular = __('Table', 'wp-ott');
				$plural = __('Tables', 'wp-ott');
				break;
			case 'banners':
				$singular = __('Banner', 'wp-ott');
				$plural = __('Banners', 'wp-ott');
				break;
			case 'links':
				$singular = __('Link', 'wp-ott');
				$plural = __('Links', 'wp-ott');
				break;
		}

		parent::__construct(array(
			'singular'	=> $singular,	//singular name of the listed records
			'plural'	=> $plural,		//plural name of the listed records
			'ajax'		=> false,		//does this table support ajax?
		));

	}

	/**
	 * Show no items message instead of list
	 *
	 * @since    1.0.0
	 */
	public function no_items() {
		switch ($this->type) {
			case 'forms':
				_e('No forms are created within your partner API.');
				break;
			case 'tables':
				_e('No tables are created within your partner API.');
				break;
			case 'banners':
				_e('No banners are created within your partner API.');
				break;
			case 'links':
				_e('No links are created within your partner API.');
				break;
		}
	}

	/**
	 * Prepare data
	 *
	 * @since    1.0.0
	 */
	function prepare_items() {
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);
		$this->api_data = $this->api->instrument_list($this->type);
		usort($this->api_data, array(&$this, 'usort_reorder'));
		$per_page = 20;
		$current_page = $this->get_pagenum();
		$total_items = count($this->api_data);
		$this->set_pagination_args(array(
			'total_items' => $total_items, // WE have to calculate the total number of items
			'per_page'    => $per_page     // WE have to determine how many items to show on a page
		));
		$this->items = array_slice($this->api_data, (($current_page - 1) * $per_page), $per_page);
	}

	/**
	 * List columns
	 *
	 * @since    1.0.0
	 */
	function get_columns() {
		return array(
			'cb'				=> '<input type="checkbox" />',
			'wp_ott_id'			=> __( 'ID', 'wp-ott'),
			'wp_ott_title'		=> __( 'Title', 'wp-ott'),
			'wp_ott_shortcode'	=> __( 'Shortcode', 'wp-ott'),
			'wp_ott_active'		=> __( 'Active?', 'wp-ott'),
		);
	}

	/**
	 * Sortable columns
	 *
	 * @since    1.0.0
	 */
	public function get_sortable_columns() {
		return array(
			'wp_ott_id'		=> array('wp_ott_id', false),
			'wp_ott_title'	=> array('wp_ott_title', false),
			'wp_ott_active'	=> array('wp_ott_active', false),
		);
	}

	/**
	 * Rows custom sort routine
	 *
	 * @since    1.0.0
	 */
	function usort_reorder($a, $b) {
		// If no sort, default to ID
		$orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'wp_ott_id';
		// If no order, default to asc
		$order = (!empty($_GET['order'])) ? $_GET['order'] : 'asc';
		// Determine sort order
		$result = strcmp($a[$orderby], $b[$orderby]);
		// Send final sort direction to usort
		return ($order === 'asc') ? $result : -$result;
	}

	function column_wp_ott_title($item) {
		$actions = array(
			'edit'		=> sprintf('<a href="?page=%s&action=%s&id=%s">Edit</a>',$_REQUEST['page'],'edit',$item['wp_ott_id']),
			//'delete'	=> sprintf('<a href="?page=%s&action=%s&id=%s">Delete</a>',$_REQUEST['page'],'delete',$item['wp_ott_id']),
		);
		return sprintf('<strong>%1$s</strong> %2$s', $item['wp_ott_title'], $this->row_actions($actions));
	}

	function column_wp_ott_shortcode($item) {
		$id = $item['wp_ott_id'];
		$shortcode = $this->api->shortcode($id);
		return sprintf('%s <button class="clip" onclick="return false;" data-clipboard-text="%s"><span class="dashicons dashicons-clipboard"></span></button>', $shortcode, esc_attr($shortcode));
	}

	public function get_bulk_actions() {
		return array(
			'activate' => __('Activate', 'wp-ott'),
			'deactivate' => __('Deactivate', 'wp-ott'),
		);
	}

	function column_cb($item) {
		return sprintf('<input type="checkbox" name="item[]" value="%s" />', $item['wp_ott_id']);
	}

	public function column_default($item, $column_name) {
		switch ($column_name) {
			case 'wp_ott_ddtitle':
				return '<strong>' . $item[$column_name] . '</strong>';
				break;
			case 'wp_ott_active':
				return ($item[$column_name] == 1) ? __('Yes', 'wp-ott') : __('No', 'wp-ott');
				break;
			default:
		    	return $item[$column_name];
    	}
	}

}
