<?php
/*
  Plugin Name: Easy Nag Popup
  Plugin URI: http://matgargano.com
  Description: Adds a nag popup to your site
  Version: 2.0
  Author: matstars
  Author URI: http://matgargano.com
  License: GPL2

*/


foreach ( glob( plugin_dir_path(__FILE__) . "inc/*.php" ) as $filename ) include $filename;

Easy_nag_popup::init();
Easy_nag_popup_admin::init();





if ( !function_exists('sanitize_int') ) {
  /**
   *
   * Sanitize integer
   *
   * @param $sanitizee
   *
   * @return int
   */
  function sanitize_int( $int ) {
    return (int)$int;
  }
}

