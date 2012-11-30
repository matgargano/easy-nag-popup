<?php

class easy_nag_popup {
    
        protected   $plugin_version         = "0.1";
        protected   $jquery_ui_version      = "1.8.2";
        protected   $option_name            = "easy-nag-popup";
        protected   $localize_js            = "popup";
        protected   $transient_post_id      = "easy-nag-popup-post-id";
        protected   $post_type              = "easy-nag-popup";
        protected   $prefix                 = "popup_";
        protected   $plugin_name            = "Easy Nag Popup";
        protected   $plugin_shortname       = "Nag Popup";
        protected   $refresh_rate           = 120; // set the data to cache for 2 minutes
        protected   $admin_enqueued         = false;
        protected   $is_content             = false;
        protected   $plugin_location;
        protected   $post_id;
    
        /**
         * Construct, sets the plugin location, registers popup post type, adds meta boxes for meta data for popup posts, adds hook to save the meta data, enqueues scripts for admin and frontend, registers this class method activate to the activation hook
         * @param none
         * @return void
         * @since 0.1
         */
    
        function __construct(){
                $this->plugin_location = plugin_dir_url(dirname(__FILE__));
                add_action( 'init', array($this, 'register_post_type') );
                add_action( 'add_meta_boxes', array($this, "add_meta_boxes"));
                add_action( 'save_post', array($this, 'save_meta'), 1, 2 );
                add_action( 'admin_enqueue_scripts', array($this, 'admin_enqueues') );
                add_action( 'wp_enqueue_scripts', array($this, 'site_enqueues') );
                register_activation_hook(__FILE__, array($this, 'activate') );
                add_action ( 'transition_post_status', array($this, 'check_kill_transient'));
                add_action( 'template_redirect', array($this,'block_page'));
        }
        
        /*
         * Permenantly redirect users to home page if this post_type is not content (set via class boolean variable $is_content in the class definition)
         * @param none
         * @return void
         * @since 0.1
         */ 
        function block_page(){
                global $post;
                if ($this->post_type !== $post->post_type || $this->is_content === true) return;
                wp_redirect( home_url(), 301 );
                exit;
        }
    
        /**
         * Adds metabox for the post type's admin screen
         * @param none
         * @return void
         * @since 0.1
         */ 

        function add_meta_boxes(){
                add_meta_box('meta_info', "&nbsp;", array($this, 'meta_info'), $this->post_type, 'normal', 'high');                                            
        }

        /*
         * Register the post type
         *
         * @param none
         * @return void
         * @since 0.1
         *
         */ 
        
        function register_post_type() {
                $labels = array( 
                        'name' => _x( $this->plugin_shortname, $this->post_type ),
                        'singular_name' => _x( $this->plugin_shortname, $this->post_type ),
                        'add_new' => _x( 'Add New', $this->post_type ),
                        'add_new_item' => _x( 'Add New ' . $this->plugin_shortname, $this->post_type ),
                        'edit_item' => _x( 'Edit ' . $this->plugin_shortname, $this->post_type ),
                        'new_item' => _x( 'New ' . $this->plugin_shortname, $this->post_type ),
                        'view_item' => _x( 'View ' . $this->plugin_shortname, $this->post_type ),
                        'search_items' => _x( 'Search ' . $this->plugin_shortname, $this->post_type ),
                        'not_found' => _x( 'No ' . $this->plugin_shortname . 's found', $this->post_type ),
                        'not_found_in_trash' => _x( 'No ' . $this->plugin_shortname . ' found in Trash', $this->post_type ),
                        'parent_item_colon' => _x( 'Parent ' . $this->plugin_shortname . ':', $this->post_type ),
                        'menu_name' => _x( '' . $this->plugin_shortname, $this->post_type ),
                );
                $args = array( 
                    'labels' => $labels,
                    'hierarchical' => false,
                    'supports' => array( 'title', 'thumbnail'),
                    'public' => true,
                    'show_ui' => true,
                    'show_in_menu' => true,
                    'show_in_nav_menus' => true,
                    'publicly_queryable' => true,
                    'exclude_from_search' => false,
                    'has_archive' => true,
                    'query_var' => true,
                    'can_export' => true,
                    'rewrite' => true,
                    'capability_type' => 'post',
                    'menu_icon' => $this->plugin_location . "images/" . $this->option_name . ".png"
                );
                register_post_type( $this->post_type, $args );
        }
    
        /*
         * Create the metabox for adding/editing popup post in backend
         * 
         * @param none
         * @return void
         * @since 0.1
         *
         */
    
        function meta_info() {
                global $post,$wpdb;
                $end                    = get_post_meta($post->ID, 'end', true);
                $push_close_button_left = get_post_meta($post->ID, 'push_close_button_left', true);
                $push_close_button_top = get_post_meta($post->ID, 'push_close_button_top', true);
                $number_views           = get_post_meta($post->ID, 'number_views', true);
                $hyperlink              = get_post_meta($post->ID, 'hyperlink', true);
                $new_window             = get_post_meta($post->ID, 'new_window', true);
                $include_x              = get_post_meta($post->ID, 'include_x', true);
                if (!is_numeric($number_views) || $number_views > 100 || $number_views < 0){
                    $number_views =1;
                }
                if (!is_numeric($push_close_button_left)) $push_close_button_left = 0;
                if (!is_numeric($push_close_button_top)) $push_close_button_top = 0;
                ?>
                <div class="popup" id="popup-wrap">
                        <div id="plugin-error" class="hidden"></div>
                        <div class="plugin-notice hidden"></div>
                        <div class="plugin-settings">
                                <?php
                                    wp_nonce_field('update-popup','update-popup');
                                ?>
                                <div id="popup-error" class="hidden"></div>
                                <h2><?php echo $this->plugin_name; ?> Settings <span class="show-instructions">[show instructions]</span><span class="hide-instructions">[hide instructions]</span></h2>
                                <div class="instructions"><h4>Instructions </h4>
                                        <ul>
                                                <li><p>Please set your popup image as the featured image for this post. Note that the same size that you upload will display as the popup.</p></li>
                                                <li><p>Enter the settings below and then publish the post.</p></li>
                                                <li><p>You can schedule when to stop running the popup below - or you can set the post status to <strong>draft.</strong></p></li>
                                                <li><p>To set a start time, schedule the post using the right hand side's Publish module.</p></li>
                                                <li><p>This feature uses a cookie, you can customize how many times a user can see the popup window before not displaying any more under <strong>Number of Times to Display Per User</strong>.</p></li>
                                        </ul>
                                </div>
                                <label for="hyperlink">URL to Send User Who Clicks on Popup (optional)</label> <input type="text" value="<?php echo $hyperlink;?>"" id="hyperlink" name="hyperlink" /><div class="clearboth"></div>
                                <label for="end">When to Unpublish Popup (optional)</label> <input type="text" value="<?php echo $end;?>"" id="end" name="end" /><div class="clearboth"></div>
                                <label for="number_views">Number of Times to Display Per User (set 0 for no limit)</label> <input type="text" value="<?php echo $number_views;?>" id="number_views" name="number_views" /><div class="clearboth"></div>
                                <label for="new_window">Open Link in a New Window</label> <input type="checkbox" <?php checked($new_window, "on", true); ?> id="new_window" name="new_window" /><div class="clearboth"></div><div class="clearboth"></div>
                                <label for="include_x">Include close button.<br /></label> <input type="checkbox" <?php checked($include_x, "on", true); ?> id="include_x" name="include_x" /><div class="clearboth"></div>
                                <div class="mg-notice indent"><p><strong>Note</strong> The close button is not necessary. If user clicks outside of the popup image or presses escape it will close the popup.</p></div>
                                <div id="push_close_button_wrap">
                                        <h2>Position Close Button</h2>
                                        <p class="small"><strong>Note</strong>: Setting the two values to zero will position the close button in the upper right hand corner of the popup.</p>
                                        <label for="push_close_button_left"><strong>Horizontal positioning </strong><br /><span class="indent"><strong>Note:</strong> a negative value will position the close button to the left and positive will position the close button to the right.</span></label> <input type="text" value="<?php echo $push_close_button_left;?>" id="push_close_button_left" name="push_close_button_left" /><div class="clearboth"></div>
                                        <label for="push_close_button_top"><strong>Vertical positioning </strong><br /><strong>Note:</strong> a negative value will position the close button up and positive value will position the close button down.</span></label> <input type="text" value="<?php echo $push_close_button_top;?>" id="push_close_button_top" name="push_close_button_top" /><div class="clearboth"></div>
                                        <div id="demo-wrap">
                                                <h2>Positioning Tool for Close Button</h2>
                                                <p class="small">This is a tool to help position the close button. <br /><br /><strong>Note:</strong> This is not to scale and should be used in conjunction with trial and error to position the button properly.</p>
                                                <div class="inner">
                                                        <div class="box-demo">Demo Popup</div>
                                                        <div class="x-demo">X</div>
                                                </div>
                                        </div>
                                </div>
                        </div>
                </div>
                <div class="clearboth"></div><?php
        }
        
        /*
         * Save the meta data
         *
         * @param int $post_id
         * @param post object $post
         * @return void
         * @since 0.1
         *
         */ 
        
    
        function save_meta($post_id, $post) {
            global $post;
            if ( empty($_POST) || !wp_verify_nonce($_POST['update-popup'],'update-popup') ){
              return;
            }
                if ( !current_user_can( 'edit_post', $post->ID ))
                    return $post->ID;
                $meta['end']                        = $_POST['end'];
                $meta['push_close_button_left']     = $_POST['push_close_button_left'];
                $meta['push_close_button_top']      = $_POST['push_close_button_top'];
                $meta['number_views']               = $_POST['number_views'];
                $meta['new_window']                 = $_POST['new_window'];
                $meta['include_x']                  = $_POST['include_x'];
                $meta['hyperlink']                  = $_POST['hyperlink'];
                foreach ($meta as $key => $value) { 
                    update_post_meta($post->ID, $key, $value);
                }
                //let's also kill the transients
                $this->kill_transient();
                
        }
        
        /*
         * Checks if the post type matches the current post type, if so, kills the transient
         *
         * @param none
         * @return void
         * @since 0.1
         *
         */ 
        
        function check_kill_transient(){
                global $post;
                if (get_post_type($post->ID)!==$this->post_type) return;
                $this->kill_transient();
        }
        
        /*
         * Kills the cached elements for this plugin
         *
         * @param none
         * @return void
         * @since 0.1
         *
         */ 
        
        function kill_transient(){
                delete_transient($this->transient_post_id);
        }
        
        /*
         * Updates a post's status - used to update a posts status when date passes the popup post's meta value for "end" (end date of the popup to show)
         *
         *
         * @param   int $post_id The ID of the post you'd like to change.
         *          string $status The post status publish|pending|draft|private|static|object|attachment|inherit|future|trash.
         * @return  void
         *
         */
        
        private function change_post_status($post_id,$status){
                $current_post = get_post( $post_id, 'ARRAY_A' );
                $current_post['post_status'] = $status;
                wp_update_post($current_post);
        }
    
    
        /*
         * Gets the post_id of the most recent active popup window. Also, performs a check if the current date/time is after the "end" set in the popup post meta. If it is it sets the status of the post to draft - thus turning off the popup window
         *
         * @param none
         * @return void
         * @since 0.1
         *
         */     
      
        private function get_popup_post_id(){
                global $wpdb;
                $post_id = get_transient( $this->transient_post_id );
                if ($post_id === false){
                    //$post_id = $wpdb->get_var($wpdb->prepare("SELECT SQL_CALC_FOUND_ROWS  $wpdb->posts.ID FROM $wpdb->posts WHERE 1=1  AND $wpdb->posts.post_type IN ('$this->post_type') AND $wpdb->posts.post_status IN ('publish') ORDER BY $wpdb->posts.post_date desc LIMIT 0, 1"));
                    
                        $args = array(
                                'numberposts'     => 1,
                                'post_type'       => $this->post_type,
                                'post_status'     => 'publish',
                                'suppress_filters' => true
                        );
                        $posts = get_posts($args);
                        if (count($posts)>0){
                                $post_id = $posts[0]->ID;
                        }
                        else return false;
                    set_transient($this->transient_post_id, $post_id, $this->refresh_rate);
                    $end = strtotime(get_post_meta($post_id, 'end', true));
                    if ($end && time()-$end>0){
                      $this->change_post_status($post_id, "draft");
                      unset($post_id);
                    }
                      }
                return $post_id;
        }
        
     
    
        /*
         * Enqueues scripts and styles for frontend display
         *
         * @param none
         * @return void
         * @since 0.1
         *
         */     
        
        function site_enqueues(){
                $popup_exists = $show_popup = false;
                $popup_post_id = $this->get_popup_post_id();
                $number_views  = get_post_meta($popup_post_id, 'number_views', true);
                if (!is_numeric($popup_post_id) || $popup_post_id<0 || substr($_SERVER['HTTP_HOST'],0,2)==="m.") return false;
                $image_array =     wp_get_attachment_image_src( get_post_thumbnail_id($popup_post_id), 'full' );
                $image = $image_array[0];
                $cookie_val = $_COOKIE["popup_" . $popup_post_id];
                $cookie_limit =  get_post_meta($popup_post_id, 'number_views', true);
                if (is_numeric($popup_post_id) && $popup_post_id) $popup_exists = true;
                if ($number_views == 0 || !(is_numeric($cookie_val) && $cookie_val>=$cookie_limit)) $show_popup = true;
                if ($popup_exists && $show_popup){
                        $hyperlink              = get_post_meta($popup_post_id, 'hyperlink', true);
                        $push_close_button_left = get_post_meta($popup_post_id, 'push_close_button_left', true);
                        $push_close_button_top  = get_post_meta($popup_post_id, 'push_close_button_top', true);
                        $include_x              = get_post_meta($popup_post_id, 'include_x', true);
                        $new_window             = get_post_meta($popup_post_id, 'new_window', true);
                        if ($image && easy_nag_popup_helper::remote_file_exists($image)) {
                            $image_data = getimagesize($image);
                            $image_width = $image_data[0];
                            $image_height = $image_data[1];
                        } else {
                            if ($image && easy_nag_popup_helper::remote_file_exists($image)) {
                                $image_data = getimagesize($image);
                                $image_width = $image_data[0];
                                $image_height = $image_data[1];
                            } else {
                                $image_data = $image_width = $image_height = "";
                                return false;
                            }
                        }
                        $pass_array           = array(
                                              "post_id"                     => $popup_post_id,
                                              "image"                       => $image,
                                              "push_close_button_top"      => $push_close_button_top,
                                              "push_close_button_left"      => $push_close_button_left,
                                              "number_views"                => $number_views,
                                              "hyperlink"                   => $hyperlink,
                                              "width"                       => $image_width,
                                              "height"                      => $image_height,
                                              "new_window"                  => $new_window,
                                              "include_x"                   => $include_x
                                              );
                        wp_register_script( $this->prefix . 'js', $this->plugin_location . "js/script.js", array("jquery", "jquery-ui-core", "jquery-cookie"), $this->plugin_version);
                        wp_register_script( 'jquery-cookie', $this->plugin_location . "js/jquery.cookie.js", array("jquery", "jquery-ui-core"), $this->plugin_version);
                        wp_register_style( $this->prefix . 'css', $this->plugin_location . "css/style.css",  $this->plugin_version);
                        wp_enqueue_script('jquery-cookie');
                        wp_enqueue_style($this->prefix . 'css');
                        wp_enqueue_script($this->prefix . 'js');
                        wp_enqueue_script('jquery-ui-core');
                        wp_localize_script( $this->prefix . 'js', $this->localize_js, $pass_array );
                }
        }
    
        /*
         * Enqueues scripts and styles for backend/admin area
         *
         * @param none
         * @return void
         * @since 0.1
         *
         */     
        
        function admin_enqueues (){
                global $post_type;
                if( $this->post_type == $post_type ){
                    wp_register_script  ( $this->prefix . 'admin', $this->plugin_location . 'js/admin.js', array('jquery-ui-spinner', 'jquery-cookie', 'jquery-ui-timepicker', 'jquery-ui-sliderAccess'), $this->plugin_version);
                    wp_register_style   ( $this->prefix . 'admin', $this->plugin_location . 'css/admin.css',  $this->plugin_version);
                    wp_register_style   ( 'jquery-ui-style', 'http://code.jquery.com/ui/'.$this->jquery_ui_version.'/themes/base/jquery-ui.css', $this->jquery_ui_version);
                    wp_register_script  ( 'jquery-ui-spinner', $this->plugin_location . 'js/jquery.ui.spinner.min.js', array('jquery-ui-widget', 'jquery-ui-mouse'), $this->plugin_version);
                    wp_register_script  ( 'jquery-ui-timepicker', $this->plugin_location . 'js/jquery.ui.timepicker.min.js', array('jquery-ui-slider', 'jquery-ui-datepicker', 'jquery-ui-sliderAccess'), $this->plugin_version, true);
                    wp_register_script  ( 'jquery-ui-sliderAccess', $this->plugin_location . 'js/jquery-ui-sliderAccess.js', array('jquery-ui-slider', 'jquery-ui-button'), $this->plugin_version, true);
                    wp_register_script  ( 'jquery-cookie', $this->plugin_location . "js/jquery.cookie.js", array("jquery-ui-core"), $this->plugin_version);
                    wp_enqueue_script   ( 'jquery');
                    wp_enqueue_script   ( 'jquery-cookie');
                    wp_enqueue_script   ( 'thickbox');
                    wp_enqueue_script   ( 'media-upload');
                    wp_enqueue_script   ( $this->prefix . 'admin');
                    wp_enqueue_style    ( $this->prefix . 'admin');
                    wp_enqueue_style    ('jquery-ui-style');
                }
        }
        
        /*
         * Run the register post type on activation of plugin
         * @param none
         * @return void
         * @since 0.1
         *
         */
        
        function activate() {
           $this->register_post_type();
           flush_rewrite_rules();
        }
}