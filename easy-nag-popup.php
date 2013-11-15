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


if ( is_admin() ){
  include( 'inc/class-easy-nag-popup-admin.php' )
} else {
  include( 'inc/mobile-detect.php' )
  include( 'inc/class-easy-nag-popup.php' )
}

Easy_nag_popup::init();
Easy_nag_popup_admin::init();





if ( !function_exists('sanitize_int') ) {
  /**
   *
   * Sanitize integer
   *
   * @param mixed $sanitizee variable that needs to be sanitized
   * @return int sanitized integer
   */
  function sanitize_int( $int ) {
    return (int)$int;
  }
}

