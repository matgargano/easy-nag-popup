<?php

/**
 * Main plugin functions and setting up plugin.
 *
 * @package     EasyNagPopup
 * @subpackage  Admin
 * @license     http://www.gnu.org/licenses/gpl-2.0.html  GNU General Public License, version 2
 * @author      Mat Gargano <mgargano@gmail.com>
 * @version     2.1.5
 */


/*
  Plugin Name: Easy Nag Popup
  Plugin URI: http://matgargano.com
  Description: Adds a nag popup to your site
  Version: 2.1.5
  Author: matstars
  Author URI: http://matgargano.com
  License: GPL2
*/

include( 'inc/class-easy-nag-popup-admin.php' );
include( 'inc/Mobile_Detect.php' );
include( 'inc/class-easy-nag-popup.php' );
Easy_nag_popup_admin::init();
Easy_nag_popup::init();








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


if ( !function_exists('sanitize_float') ) {
  /**
   *
   * Sanitize float
   *
   * @param mixed $sanitizee variable that needs to be sanitized
   * @return int sanitized integer
   */
  function sanitize_float( $float ) {
    return (float)$float;
  }
}