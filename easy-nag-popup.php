<?php
/*
Plugin Name: Easy Nag Popup
Plugin URI: http://www.matgargano.com
Description: Creates a Fully Customizable Modal Window that Greets Users
Version: 1.0
Author: Mat Gargano
Author Email: mgargano@gmail.com
License:

  Copyright 2012 Mat Gargano (matgargano.com)
  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as 
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

/*
 * Include the necessary class to have a second featured image and instantiate the plugin's class, enabling the plugin
 *
 */

foreach (glob(plugin_dir_path(__FILE__) . "class/*.php") as $filename) include $filename; 
new easy_nag_popup;