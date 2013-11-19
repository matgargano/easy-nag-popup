<?php

/**
 * This file contains methods that handle the administration of popups
 *
 * @package     EasyNagPopup
 * @subpackage  Admin
 * @license     http://www.gnu.org/licenses/gpl-2.0.html  GNU General Public License, version 2
 * @author      Mat Gargano <mgargano@gmail.com>
 * @version     2.1.5
 */

class Easy_nag_popup_admin {

	/**
	 * @var string $text_domain Domain for this plugin/packages text so that it can be translated.
	 */

	public static $text_domain = 'easy_nag_popup';
	/**
	 * @var string $nonce_name Name to use for the security nonce when submitting data to be handled by the backend.
	 */

	public static $nonce_name = 'easy_nag_popup';

	/**
	 * @var string $ver The version of this package.
	 */

	public static $ver;

	/**
	 * @var string $file_name The file name we are going to use for JS/CSS/other assets relating to this package.
	 */

	public static $file_name;

	/**
	 * @var array $post_meta The post meta to be saved with each post of post type Easy Nag Popup.
	 */

	public static $post_meta;

	/**
	 * @var string $post_type The post type we are defining for this project
	 */

	public static $post_type;

	/**
	 * Initialize this subpackage.
	 *
	 * @return void
	 */

	public static function init(){
		self::$post_type = Easy_nag_popup::$post_type;
		self::$ver = Easy_nag_popup::$ver;
		self::$file_name = Easy_nag_popup::$file_name;
		add_action( 'load-post.php', array( __CLASS__, 'post_meta_boxes_setup' ) );
		add_action( 'load-post-new.php', array( __CLASS__, 'post_meta_boxes_setup' ) );
		self::$post_meta = array(
			array(
				'name' => 'home_only',
				'sanitize' => '',
				'description' => __( 'Only display popup on the homepage?', self::$text_domain ),
				'type' => 'checkbox',
				'class' => '',
			),
			array(
				'name' => 'hide_mobile',
				'sanitize' => '',
				'description' => __( 'Do not display popup on mobile', self::$text_domain ),
				'type' => 'checkbox',
				'class' => '',
			),
			array(
				'name' => 'hide_tablet',
				'sanitize' => '',
				'description' => __( 'Do not display popup on tablets', self::$text_domain ),
				'type' => 'checkbox',
				'class' => '',
			),
			array(
				'name' => 'url_to_send_user',
				'sanitize' => 'esc_url',
				'description' => __( 'URL to send user that clicks on popup', self::$text_domain ),
				'type' => 'text',
			),
			array(
				'name' => 'open_new_window',
				'sanitize' => '',
				'description' => __( 'Open link in a new window', self::$text_domain ),
				'type' => 'checkbox',
				'class' => '',
			),			
			array(
				'name' => 'number_times_to_show',
				'sanitize' => 'sanitize_int',
        		'description' => __( 'Number of times to show modal to users', self::$text_domain ),
				'type' => 'text',
				'class' => '',
        		'default' => '1'
			),
			array(
				'name' => 'hours_between_show',
				'sanitize' => 'sanitize_float',
				'description' => __( 'Number of hours to wait before showing a user the modal again' ),
				'class' => ''
			),
			

		);
	}

	/**
	 * Adds meta box on the 'add_meta_boxes' hook. Adds save post meta on the 'save_post' hook.
	 *
	 * @return void
	 */

	public static function post_meta_boxes_setup() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_post_meta_boxes' ) );
		add_action( 'save_post', array( __CLASS__, 'save_post_class_meta' ), 10, 2 );
	}

	/**
	 * Add and attach meta box to Easy Nag Popup post type.
	 *
	 * @return void
	 */

	public static function add_post_meta_boxes() {
		add_meta_box( 'easy-nag-popup', esc_html__( 'Easy Nag Popup Settings', self::$text_domain ), array( __CLASS__, 'post_class_meta_box' ), self::$post_type, 'normal', 'default' );
	}

	/**
	 * Prepare and display the meta box's contents
	 *
	 * @param object $post post object
	 * @return void
	 */

	public static function post_class_meta_box( $post ) { ?>

		<?php wp_nonce_field( basename( __FILE__ ), self::$nonce_name ); ?>
		<?php foreach ( self::$post_meta as $meta ) : ?>
		<?php $defaults = array(
			'class' => 'widefat',
			'type' => 'text'
		);
		
		$meta = array_merge($defaults, $meta);
		?>
		<?php if ( isset( $meta['type'] ) && 'text' === $meta['type'] ) : ?>
			<p>
				<label for="<?php echo $meta['name']; ?>"><?php echo $meta['description']; ?></label>
				<br />
				<input class="<?php echo $meta['class']; ?>" type="text" name="<?php echo $meta['name']; ?>" id="<?php echo $meta['name']; ?>" value="<?php echo get_post_meta( $post->ID, $meta['name'], true ); ?>" size="30" />
			</p>
		<?php elseif( isset( $meta['type'] ) && 'checkbox' === $meta['type'] ) : ?>
			<p>
				<input class="<?php echo $meta['class']; ?>" type="checkbox" name="<?php echo $meta['name']; ?>" id="<?php echo $meta['name']; ?>" <?php checked( get_post_meta( $post->ID, $meta['name'], true ), 'on', 1 );?> />
				<label for="<?php echo $meta['name']; ?>"><?php echo $meta['description']; ?></label>
			</p>
		<?php endif;?>
		<?php endforeach; ?>
	<?php }

	/**
	 * Handle the saving of meta data for the Easy Nag Popup posts
	 *
	 * @param int $post_id The post id.
	 * @param object $post The post object.
	 *
	 * @return bool Returns true if completes the save action.
	 */

	public static function save_post_class_meta( $post_id, $post ) {

		/* Verify the nonce before proceeding. */
		if ( !isset( $_POST[ self::$nonce_name ] ) || ! wp_verify_nonce( $_POST[ self::$nonce_name ], basename( __FILE__ ) ) )
			return $post_id;

		/* Get the post type object. */
		$post_type = get_post_type_object( $post->post_type );

		/* Check if the current user has permission to edit the post. */
		if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
			return $post_id;



		foreach ( self::$post_meta as $meta ) :
			/* Get the posted data and sanitize it for use as an HTML class. */

			if ( ! $meta['sanitize'] ) $meta['sanitize'] = 'esc_attr';
			$new_meta_value = ( isset( $_POST[ $meta['name'] ] ) ? call_user_func( $meta['sanitize'], $_POST[ $meta['name'] ] ) : '' );
      if ( ! $new_meta_value && isset( $meta['default'] ) ) $new_meta_value = $meta['default'];
			/* Get the meta value of the custom field key. */
			$meta_value = get_post_meta( $post_id, $meta['name'], true );

			/* If a new meta value was added and there was no previous value, add it. */
			if ( $new_meta_value && '' == $meta_value ){
				add_post_meta( $post_id, $meta['name'], $new_meta_value, true );
			}

			/* If the new meta value does not match the old value, update it. */
			elseif ( $new_meta_value && $new_meta_value != $meta_value ){
				update_post_meta( $post_id, $meta['name'], $new_meta_value );
			}

			/* If there is no new meta value but an old value exists, delete it. */
			elseif ( '' == $new_meta_value && $meta_value ) {
				delete_post_meta( $post_id, $meta['name'], $meta_value );
			}
		endforeach;

		return true;
	}
}