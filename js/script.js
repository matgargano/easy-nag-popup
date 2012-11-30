/*
 *
 * Set varibles
 * push_down_image - # of pixels to push the popup image from the very top
 * push_top_close - # of pixels to push the close button from the very top
 * push_left_close - # of pixels to push the close button to the left ( a negative value will push it right )
 *
 * Don't touch the following unless you are ABSOLUTELY SURE YOU KNOW WHAT YOU ARE DOING!!
 * 
 * hyperlink - caches a trimmed version of the hyperlink to send the user
 * cookie_name - sets the name of the cookie to use, it is essentially popup_POST_ID_OF_popup
 * number_views - caches the number of views to show the popup window before stopping (pushed through using wp_localize_script )
 * $lightsout_class - cached version of the lightsout class which dims the background (default value is  ".popup-lightsout")
 * $popup_image_class = cached version of the popup image class (default value is  ".popup-image")
 * $close_image_class = cached version of the close image class (default value is  ".popup-close")
 * 



 * 
 */


$lightsout_class = ".popup-lightsout";
$popup_image_class = ".popup-image";
$close_image_class = ".popup-close";

var push_top_close  = popup.push_close_button_top;
var push_left_close = popup.push_close_button_left;
var hyperlink;
var hyperlink_prefix="";
var hyperlink_suffix="";
var target_destination = "";
var $close_image = "";
var cookie_name = "popup_" + popup.post_id;
hyperlink = jQuery.trim(popup.hyperlink);
var number_views = parseInt(popup.number_views);
if (popup.new_window) target_destination = ' target="_BLANK" ';
if (popup.include_x) $close_image = '<div class="' + $close_image_class.replace(".","") + '"></div>';


if (hyperlink){   /* if there is a hyperlink set, let's prepare it to work */
    hyperlink_prefix = '<a class="popup-a" ' + target_destination + 'href="' + hyperlink + '">';
    hyperlink_suffix = '';
}



/*
 * Tests if a number is numeric... wrote this because isNaN was not seeming to work
 *
 * @param input
 * @return bool whether or not the input was numeric
 *
 */

function IsNumeric(input) {
    return (input - 0) == input && input.length > 0;
}


/*
 * Centers the popup to the users window. Also builds/rebuilds the lightsout div
 *
 * @param none
 * @return none
 *
 */

function position_popup(){
        doc_width = jQuery(window).width();
        doc_height = jQuery(window).height();
        $lightsout = jQuery($lightsout_class);
        $popupimage = jQuery($popup_image_class);
        $closeimage = jQuery($close_image_class)
        $popupimage = jQuery($popup_image_class);
        $left_val = (doc_width/2)-(popup.width/2);
        $top_val = (doc_height/2)-(popup.height/2);
        $lightsout.css("height",  doc_width + "px");
        $lightsout.css("width",  doc_width + "px");
        $popupimage.css("left",$left_val+"px");
        $popupimage.css("top",$top_val+"px");
        $closeimage.css("left",$left_val+parseInt(popup.width)+parseInt(push_left_close) + "px");
        push_top_close_adjusted = parseInt(push_top_close)+parseInt($top_val);
        $closeimage.css("top",push_top_close_adjusted + "px");
}

/*
 * Creates the popup image n.b. the lightsout div is built by position_popup
 *
 * @param none
 * @return none
 *
 */

function prepare_popup(){
    jQuery("body").remove($lightsout_class);
    
    jQuery("body").append('<div class="' + $lightsout_class.replace(".","") + '"></div>'+ hyperlink_prefix +'<img class="' + $popup_image_class.replace(".","") + '" src="' + popup.image + '" />'+ hyperlink_suffix +'</a>' + $close_image);
        position_popup();        
}


/*
 * Gets rid of the popup window i.e. when user clicks close or clicks off of it or hits escape
 *
 * @param none
 * @return none
 *
 */

function kill_popup(){
        jQuery($close_image_class).fadeOut().remove();
        jQuery($popup_image_class).fadeOut().remove();
        jQuery($lightsout_class).fadeOut().remove();
    }
    
    
    

/*
 * Sends the user to where the popup hyperlinks to, if hyperlink is set (see above)
 *
 * @param none
 * @return none
 *
 */

function go_popup(){
        if (hyperlink && jQuery("a.popup-a").length>0) {
                var a = jQuery('a.popup-a')[0];
                var e = document.createEvent('MouseEvents');
                e.initEvent( 'click', true, true );
                a.dispatchEvent(e);
            
        }
    }
    
    
    
jQuery(document).ready(function($){

     jQuery("a.popup-a").live("click", function(e){
            kill_popup();
        
        });
     
     
    var num = (jQuery.cookie(cookie_name));  //gets cookie value
    if (!IsNumeric(num)) num=1;  // tests if cookie value is a number, if not initalizes it
    else num++;   //if a number, add one to it
    console.log(number_views + "<<");
    if (number_views === 0 || num<=number_views) {  // if the user has not exhausted # of views for this popup, create it!
        jQuery.cookie(cookie_name, num, { expires: 7, path: '/' });
        prepare_popup();

        $lightsout.live("click", function(){  //if user clicks on the lightsout div, kill the popup (no need to test if popup is visible, the lightsout is visible only when the popup is visible)
            kill_popup();
        });
    
        $closeimage.live("click", function(){  //if user clicks on the (X), kill the popup (no need to test if popup is visible, the lightsout is visible only when the popup is visible)
            kill_popup();
        });
        
        $(window).resize(function() {  //if user resizes the window & popup is visible, reposition the lightsout and popup
            if (jQuery($popup_image_class).length>0){
                position_popup();
            }
            
        });
        
        jQuery(document).keyup(function(e) {    //handle keypresses from keyboard when the popup is up
          if (jQuery($popup_image_class).length>0){
            if (e.which == 27) kill_popup();   // esc
          }
        });

    }    
         var maxZ = Math.max.apply(null, 
        jQuery.map(jQuery('body > *'), function(e,n) {
            if (jQuery(e).css('position') != 'static')
            return parseInt(jQuery(e).css('z-index')) || 1;
        }));
     maxZ = Math.max(maxZ, 10000);
     popup_z_index = maxZ+2;
     lightsout_z_index = maxZ+1;
     $lightsout = jQuery($lightsout_class);
     jQuery(".popup-close, .popup-image, .popup-a").css("z-index", popup_z_index);
     $lightsout.css("z-index", lightsout_z_index);
});


