var instruction_cookie = "mg-easy-nag-popup-instructions";

function show_instructions(){
      jQuery(".instructions").show();
      jQuery(".show-instructions").hide();
      jQuery(".hide-instructions").show();
}

function hide_instructions(){
      jQuery(".instructions").hide();
      jQuery(".hide-instructions").hide();
      jQuery(".show-instructions").show();      
}

function valid_url(textval) {
      var urlregex = new RegExp(
            "^(http|https|ftp)\://([a-zA-Z0-9\.\-]+(\:[a-zA-Z0-9\.&amp;%\$\-]+)*@)*((25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])|([a-zA-Z0-9\-]+\.)*[a-zA-Z0-9\-]+\.(com|edu|gov|int|mil|net|org|biz|arpa|info|name|pro|aero|coop|museum|[a-zA-Z]{2}))(\:[0-9]+)*(/($|[a-zA-Z0-9\.\,\?\'\\\+&amp;%\$#\=~_\-]+))*$");
      return urlregex.test(textval);
    }
	
	
function set_push_close_button_spinner(){
	jQuery('input#push_close_button_left').spinner({ min: -200, max: 100});
	jQuery('input#push_close_button_left').css("width", 50);
	jQuery('input#push_close_button_top').spinner({ min: -200, max: 100});
	jQuery('input#push_close_button_top').css("width", 50);
	jQuery('#push_close_button_wrap .ui-spinner-button').click(function() { jQuery(this).siblings('input').change();});
}

function update_demo_display(){
      top_value = parseInt(jQuery(".box-demo").css("top")) + .5*parseInt(jQuery("#push_close_button_top").val());
      left_value = parseInt(jQuery(".box-demo").css("width")) + parseInt(jQuery(".box-demo").css("left")) + .2*parseInt(jQuery("#push_close_button_left").val());
      jQuery(".x-demo").css("top", top_value + "px");
      jQuery(".x-demo").css("left", left_value + "px");
}

function update_close_button_display(){
	
	if (jQuery("#include_x").attr("checked")==="checked") {
				setTimeout(function(){
					set_push_close_button_spinner();
					}, 10);
		jQuery("#push_close_button_wrap").show();
	} else {
		jQuery('input#push_close_button_top').spinner("destroy");
		jQuery('input#push_close_button_left').spinner("destroy");
		jQuery("#push_close_button_wrap").hide();
	}	
}


jQuery(document).ready(function($){
      var hide_instructions_value = parseInt((jQuery.cookie(instruction_cookie)));
      if (hide_instructions_value===1){
            hide_instructions();   
      }
      
      
      jQuery(".show-instructions").live("click", function(){
            show_instructions();
            jQuery.cookie(instruction_cookie, 0, { expires: 365, path: '/' });
      });
      
      jQuery(".hide-instructions").live("click", function(){
            hide_instructions();
            jQuery.cookie(instruction_cookie, 1, { expires: 365, path: '/' });
      });
    
    
      jQuery("#push_close_button_left, #push_close_button_top").live("change keyup keydown keypress", function(){
         update_demo_display();   
      });
      
      update_demo_display();
      update_close_button_display();
      $('#include_x').change(function(){
          update_close_button_display();
      });


	
	$('input#number_views').spinner({ min: 0, max: 50 });
	
    $('input#end').datetimepicker({
    	ampm: true
    });
	jQuery("input#publish").live("click", function(e){
		var error_notices = [];
		if ($("#remove-post-thumbnail").length === 0){
			error_notices.push("Please set a popup image as the post's Featured Image");	
		}
		var hyperlink = $("input#hyperlink").val();
		if (hyperlink !== "" && !valid_url(hyperlink)){
			error_notices.push("If you are going to input a URL, please make sure it is valid (e.g. http://example.com/). If you do not wish to link the popup, please make sure the URL textbox is blank.");	
		}
		
		
		
		if (error_notices.length > 0){
			if (("#popup-wrap").length === 0) return false;
			e.preventDefault();
			jQuery("#popup-error").removeClass("hidden");
			if (error_notices.length>0){
					var notices = "";
					for (var i = 0; i < error_notices.length; i++){
							
							notices = notices + "<p>" + error_notices[i] + "</p>";                
					}
			}
		jQuery('#popup-error').html(notices);
		jQuery('#ajax-loading').remove();
		
		jQuery("html, body").animate({ scrollTop: jQuery('#popup-error').offset().top - 50 }, 100);
		}
	});
});