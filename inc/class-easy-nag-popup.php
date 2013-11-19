<?php

/**
 * This file contains methods that handle the displaying of popups
 *
 * @package     EasyNagPopup
 * @subpackage  Display
 * @license     http://www.gnu.org/licenses/gpl-2.0.html  GNU General Public License, version 2
 * @author      Mat Gargano <mgargano@gmail.com>
 * @version     2.1.7
 */

class Easy_nag_popup {

	/**
     * @var string $post_type The post type we are defining for this project
	 */

	public static $post_type = 'easy_nag_popup';
	
    /**
	 * @var string $file_name The file name we are going to use for JS/CSS/other assets relating to this package
	 */

	public static $file_name = 'easy-nag-popup';
	
    /**
     * @var string $ver The version of this package.
	 */

	public static $ver = '2.1.7';

	/**
     * Initialize this subpackage.
	 *
	 * @return void
	 */

	public static function init(){
		add_action( 'init', array( __CLASS__, 'register_post_type' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
		
	}

	/**
	 * Obtains the latest easy nag popup's post id
	 *
	 * @return int $id the post's ID
	 */

	public static function latest_active(){
		if ( is_admin() ) return;
		$posts = self::get();
		while ( $posts->have_posts() ) : $posts->the_post(); 
		  $id = get_the_ID();
		endwhile;
		wp_reset_postdata();
        return $id;
	}

	/**
	 * Registers the post type for the easy nag popups
	 *
	 * @return void
	 */

	public static function register_post_type(){
		$labels = array(
			'name'               => 'Easy Nag Popup',
			'singular_name'      => 'Easy Nag Popup',
			'add_new'            => 'Add New',
			'add_new_item'       => 'Add New Nag Popup',
			'edit_item'          => 'Edit Nag Popup',
			'new_item'           => 'New Nag Popup',
			'all_items'          => 'All Nag Popups',
			'view_item'          => 'View Nag Popup',
			'search_items'       => 'Search Nag Popups',
			'not_found'          => 'No Nag Popups found',
			'not_found_in_trash' => 'No Nag Popups found in Trash',
			'parent_item_colon'  => '',
			'menu_name'          => 'Easy Nag Popup'
		);

		$args = array(
			'labels'               => $labels,
			'public'               => false,
			'hierarchical'         => false,
			'has_archive'          => false,
			'rewrite'              => false,
	        'publicly_queryable'   => true,
            'exclude_from_search'  => true,
            'show_ui'         	   => true,
    		'supports'             => array( 'title', 'thumbnail', 'page-attributes' ),
    		'menu_icon' 		   => plugins_url( 'img/' .self::$file_name .'.png', dirname( __FILE__ ) )
		);

		register_post_type( self::$post_type, $args );
	}
	
	/**
	 * Enqueues scripts for this package/plugin
	 *
	 * @return void
	 */

	static function enqueue(){
		if ( is_admin() ) return;
        $post_id = self::latest_active();
        $image = get_the_post_thumbnail( $post_id, 'full' );
        error_log($image);
        if ( ! $image ) return;
        $home_only = get_post_meta( $post_id, 'home_only', true );
        $hide_mobile = get_post_meta( $post_id, 'hide_mobile', true );
        $hide_tablet = get_post_meta( $post_id, 'hide_tablet', true );
        if ( $hide_mobile || $hide_tablet ){
        	$detect = new Mobile_Detect;
			$device_type = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');
        	if ( ( $hide_mobile && $device_type === 'phone' ) || ( $hide_tablet && $device_type === 'tablet' ) ) return;
        }
        if ( $home_only && ! is_home() ) return;
        $url_to_send_user = get_post_meta( $post_id, 'url_to_send_user', true );
        $open_new_window  = get_post_meta( $post_id, 'open_new_window', true );
        $number_times_to_show = get_post_meta( $post_id, 'number_times_to_show', true );
        $hours_between_show = get_post_meta( $post_id, 'hours_between_show', true );
        $pass_array = array(
              "postId" => $post_id,
              "image" => $image,
              "urlToSendUser" => $url_to_send_user,
              "openNewWindow" => $open_new_window,
              "numberTimesToShow" => $number_times_to_show,
              "hoursBetweenShow" => $hours_between_show
        );
		wp_enqueue_style( self::$file_name, plugins_url('css/' .self::$file_name .'.css', dirname( __FILE__ ) ), false, self::$ver );
		wp_enqueue_script( 'store', plugins_url('js/store+json2.min.js', dirname( __FILE__ ) ), array( 'jquery' ), self::$ver );
		wp_enqueue_script( self::$file_name, plugins_url('js/' .self::$file_name .'.js', dirname( __FILE__ ) ), array( 'store' ), self::$ver );
        wp_localize_script( self::$file_name, 'enp', $pass_array );
		
	}

	/**
	 * Gets easy nag popup posts using the WP_Query class
	 *
	 * @return array $args list of arguments to override defaults
	 */

	static function get( $args = null ){
		$args = (array)$args;
		$defaults = array(
			'post_type' => self::$post_type,
			'posts_per_page' => 1,
			'orderby' => 'date',
			'order' => 'desc',
			'post_status' => 'publish'
		);
		$args = array_merge( $defaults, $args );
		$results = new WP_Query( $args );
		return $results;
	}
}