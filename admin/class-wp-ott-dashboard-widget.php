<?php

/**
 * Dashboard widget
 *
 * @link 	  https://codecanyon.net/user/wonderburo
 * @since	  1.0.0
 *
 * @package	Wp_Ott
 * @subpackage Wp_Ott/admin
 */

/**
 * Dashboard widget class.
 *
 * This class defines Admin dashboard widget.
 *
 * @since	  1.0.0
 * @package	Wp_Ott
 * @subpackage Wp_Ott/admin
 * @author	 Sergey Aksenov <sergeax@gmail.com>
 */
class Wp_Ott_Dashboard_Widget {

	/**
	 * The id of this widget.
	 */
	const wid = 'wp_ott_dashboard_widget';

	/**
	 * Valid categories
	 */
	private static $categories, $intervals, $instruments;

	/**
	 * Hook to wp_dashboard_setup to add the widget.
	 */
	public static function init() {
		// Init lists
		self::$categories = array(
			'all' => __('All categories', 'wp-ott'),
			'planes' => __('Plane tickets', 'wp-ott'),
			'hotels' => __('Hotels', 'wp-ott'),
			'rail' => __('Rail tickets', 'wp-ott'),
		);
		self::$intervals = array(
			'week' => __('Week', 'wp-ott'),
			'month' => __('Month', 'wp-ott'),
			'year' => __('Year', 'wp-ott'),
		);
		self::$instruments = array(
			'all' => __('All instruments', 'wp-ott'),
		);
		//Register widget settings...
		self::update_dashboard_widget_options(
			self::wid,								  //The  widget id
			array(									  //Associative array of options & default values
//				'category' => array_keys(self::$categories)[0],
//				'instrument' => array_keys(self::$instruments)[0],
				'interval' => array_keys(self::$intervals)[0],
			),
			true										//Add only (will not update existing options)
		);

		//Register the widget...
		wp_add_dashboard_widget(
			self::wid,											 	//A unique slug/ID
			__( 'OneTwoTrip Partner Dashboard Widget', 'wp-ott' ),	//Visible name for the widget
			array('Wp_Ott_Dashboard_Widget','widget'),				//Callback for the main widget content
			array('Wp_Ott_Dashboard_Widget','config')			 	//Optional callback for widget configuration content
		);

	 	// Put our widget to the top

		global $wp_meta_boxes;
 	
	 	// Get the regular dashboard widgets array 
	 	// (which has our new widget already but at the end)
	 	$normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
	 	
	 	// Backup and delete our new dashboard widget from the end of the array
	 	$widget_backup = array( self::wid => $normal_dashboard[self::wid] );
	 	unset( $normal_dashboard[self::wid] );
	 
	 	// Merge the two arrays together so our widget is at the beginning
	 	$sorted_dashboard = array_merge( $widget_backup, $normal_dashboard );
	 
	 	// Save the sorted array back into the original metaboxes 
	 	$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
	}

	/**
	 * Load the widget code
	 */
	public static function widget() {
		require_once( plugin_dir_path( __FILE__ ) . 'partials/wp-ott-dashboard-widget.php' );
	}

	/**
	 * Load widget config code.
	 *
	 * This is what will display when an admin clicks
	 */
	public static function config() {
		if (isset($_POST['update-widget']) && $_POST['update-widget'] === self::wid) {
//			$category = (isset($_POST['category'])) ? stripslashes($_POST['category'] ) : array_keys(self::$categories)[0];
//			if (!array_key_exists($category, self::$categories)) $category = array_keys(self::$categories)[0];
			$interval = (isset($_POST['interval'])) ? stripslashes($_POST['interval'] ) : array_keys(self::$intervals)[0];
			if (!array_key_exists($interval, self::$intervals)) $interval = array_keys(self::$intervals)[0];
//			$instrument = (isset($_POST['instrument'])) ? stripslashes($_POST['instrument'] ) : array_keys(self::$instruments)[0];
//			if (!array_key_exists($instrument, self::$instruments)) $instrument = array_keys(self::$instruments)[0];
			self::update_dashboard_widget_options(
				self::wid,								  // The widget id
				array(									  // Associative array of options & default values
//					'category' => $category,
					'interval' => $interval,
//					'instrument' => $instrument,
				)
			);
		}
//		$category = self::get_dashboard_widget_option(self::wid, 'category');
		$interval = self::get_dashboard_widget_option(self::wid, 'interval');
//		$instrument = self::get_dashboard_widget_option(self::wid, 'instrument');
		require_once( plugin_dir_path( __FILE__ ) . 'partials/wp-ott-dashboard-widget-config.php' );
	}

	/**
	 * Gets the options for a widget of the specified name.
	 *
	 * @param string $widget_id Optional. If provided, will only get options for the specified widget.
	 * @return array An associative array containing the widget's options and values. False if no opts.
	 */
	public static function get_dashboard_widget_options($widget_id='') {
		//Fetch ALL dashboard widget options from the db...
		$opts = get_option( self::wid . '_options' );

		//If no widget is specified, return everything
		if ( empty( $widget_id ) )
			return $opts;

		//If we request a widget and it exists, return it
		if ( isset( $opts[$widget_id] ) )
			return $opts[$widget_id];

		//Something went wrong...
		return false;
	}

	/**
	 * Gets one specific option for the specified widget.
	 * @param $widget_id
	 * @param $option
	 * @param null $default
	 *
	 * @return string
	 */
	public static function get_dashboard_widget_option( $widget_id, $option, $default=NULL ) {

		$opts = self::get_dashboard_widget_options($widget_id);

		//If widget opts dont exist, return false
		if ( ! $opts )
			return false;

		//Otherwise fetch the option or use default
		if ( isset( $opts[$option] ) && ! empty($opts[$option]) )
			return $opts[$option];
		else
			return ( isset($default) ) ? $default : false;

	}

	/**
	 * Saves an array of options for a single dashboard widget to the database.
	 * Can also be used to define default values for a widget.
	 *
	 * @param string $widget_id The name of the widget being updated
	 * @param array $args An associative array of options being saved.
	 * @param bool $add_only If true, options will not be added if widget options already exist
	 */
	public static function update_dashboard_widget_options( $widget_id , $args=array(), $add_only=false )
	{
		//Fetch ALL dashboard widget options from the db...
		$opts = get_option( self::wid . '_options' );

		//Get just our widget's options, or set empty array
		$w_opts = ( isset( $opts[$widget_id] ) ) ? $opts[$widget_id] : array();

		if ( $add_only ) {
			//Flesh out any missing options (existing ones overwrite new ones)
			$opts[$widget_id] = array_merge( $args, $w_opts );
		}
		else {
			//Merge new options with existing ones, and add it back to the widgets array
			$opts[$widget_id] = array_merge( $w_opts, $args );
		}

		//Save the entire widgets array back to the db
		return update_option( self::wid . '_options', $opts );
	}
}
