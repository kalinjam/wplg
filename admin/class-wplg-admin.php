<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       jankalinowski.net@gmail.com
 * @since      1.0.0
 *
 * @package    wplg
 * @subpackage wplg/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    wplg
 * @subpackage wplg/admin
 * @author     Jan Kalinowski <jankalinowski.net@gmail.com>
 */
class wplg_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$this->register_shortcode();
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in wplg_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The wplg_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wplg-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in wplg_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The wplg_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		
		// wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wplg-admin.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'wplg-admin-js', plugin_dir_url( __FILE__)  . 'build/js/wplg-admin.min.js', array( 'jquery' ), '', true );          
		wp_localize_script( 'wplg-admin-js', 'wplg_params' , array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

	}
	
	public function register_shortcode() {
		add_shortcode( 'wplg_video', 'wplg_video_shortcode_callback' );
		function wplg_video_shortcode_callback( $atts ) {
			$wplg_video_src = get_post_meta($atts['id'], 'wplg_video_src_meta', true);
			$wplg_video_poster = get_post_meta($atts['id'], 'wplg_video_poster_meta', true);
			$wplg_video = '
						<div class="wplgvideo-container">
						<div class="wplgvideo" id="'.$atts['id'].'">
							<div class="overlay">            
							</div>
							<video controls playsinline controls="true" poster="'.$wplg_video_poster.'">
								<source src="'.$wplg_video_src.'" type="video/mp4">
							</video>
							<div class="controls">
								<button class="play" aria-label="play pause toggle">
								<img class="play-icon" src="'. esc_url( plugins_url( 'dist/images/play.svg', dirname(__FILE__) ) ) . '"</img>
								<img class="pause-icon" src="'. esc_url( plugins_url( 'dist/images/pause.svg', dirname(__FILE__) ) ) . '"</img>
								</button>
								<button class="rwd" aria-label="rewind">
								<img src="'. esc_url( plugins_url( 'dist/images/backward.svg', dirname(__FILE__) ) ) . '"</img>
								</button>
								<button class="fwd" aria-label="fast forward">
								<img src="'. esc_url( plugins_url( 'dist/images/fast-forward.svg', dirname(__FILE__) ) ) . '"</img>
								</button>
								<div class="timer">
									<div></div>
									<span aria-label="timer">00:00</span>
								</div>
								<button class="fullscreen-toggle" aria-label="fullscreen toggle">
								<img src="'. esc_url( plugins_url( 'dist/images/screen-full.svg', dirname(__FILE__) ) ) . '"</img>
								</button>
							</div>
						</div>
						</div>';
			return $wplg_video;
		}
		add_shortcode( 'wplg_wrapper', 'wplg_wrapper_shortcode_callback' );
		function wplg_wrapper_shortcode_callback( $atts, $content = null ) {
			if (!empty($content)) {
				return $content;
			}
		}
	}

	public function register_wplg_menu() {
		add_menu_page( 
			__( 'WPLG', 'wplg' ),
			'WPLG',
			'manage_options',
			'wplg_menu',
			'',
			'dashicons-smiley',
			5
		);
		add_submenu_page(
			'wplg_menu',
			__( 'Settings', 'wplg' ),
			__( 'Settings', 'wplg' ),
			'manage_options',
			'wplg_settings_menu',
			array($this, 'wplg_settings_menu_callback')
		);
	}

	public function wplg_settings_menu_callback() {
		// var_dump(get_option('wplg_leads'));
		?>
		<br><br>
		<button id="wplg_download_csv">Download Leads CSV</button>
		<?php
	}
	
	public function wplg_get_leads_ajax_handler() {
		$leads = get_option('wplg_leads');		
		exit(json_encode($leads));
	}
	
	public function register_wplg_video_post_type() {
		$labels = array(
			'name'                  => _x( 'wplg', 'Post Type General Name', 'wplg' ),
			'singular_name'         => _x( 'wplg', 'Post Type Singular Name', 'wplg' ),
			'menu_name'             => __( 'wplg', 'wplg' ),
			'name_admin_bar'        => __( 'wplg', 'wplg' ),
			'archives'              => __( 'Item Archives', 'wplg' ),
			'attributes'            => __( 'Item Attributes', 'wplg' ),
			'parent_item_colon'     => __( 'Parent Item:', 'wplg' ),
			'all_items'             => __( 'Videos', 'wplg' ),
			'add_new_item'          => __( 'Add New Item', 'wplg' ),
			'add_new'               => __( 'Add New', 'wplg' ),
			'new_item'              => __( 'New Item', 'wplg' ),
			'edit_item'             => __( 'Edit Item', 'wplg' ),
			'update_item'           => __( 'Update Item', 'wplg' ),
			'view_item'             => __( 'View Item', 'wplg' ),
			'view_items'            => __( 'View Items', 'wplg' ),
			'search_items'          => __( 'Search Item', 'wplg' ),
			'not_found'             => __( 'Not found', 'wplg' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'wplg' ),
			'featured_image'        => __( 'Featured Image', 'wplg' ),
			'set_featured_image'    => __( 'Set featured image', 'wplg' ),
			'remove_featured_image' => __( 'Remove featured image', 'wplg' ),
			'use_featured_image'    => __( 'Use as featured image', 'wplg' ),
			'insert_into_item'      => __( 'Insert into item', 'wplg' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'wplg' ),
			'items_list'            => __( 'Items list', 'wplg' ),
			'items_list_navigation' => __( 'Items list navigation', 'wplg' ),
			'filter_items_list'     => __( 'Filter items list', 'wplg' ),
		);
		$args = array(
			'label'                 => __( 'wplg_video', 'wplg' ),
			'Html'           => '',
			'labels'                => $labels,
			'supports'              => array( 'title' ),
			'taxonomies'            => array(),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => "wplg_menu",
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => false,
			'rewrite' 				=> array('slug' => 'wplg_video', 'with_front' => false),
			'exclude_from_search'   => true,
			'publicly_queryable'    => true,
			'capability_type'       => 'page',
			'menu_icon'				=> 'dashicons-smiley',
			'show_in_rest'			=> true
	
		);
		register_post_type( 'wplg_video', $args );
	}
	
	public function add_wplg_video_meta_boxes() {
		add_meta_box(
			'wplg_settings',
			__( 'wplg Settings', 'wplg' ),
			array( $this, 'wplg_video_settings_display' ),
			array( 'wplg_video' ),
			'advanced',
			'high'
		);		
	} 

	public function wplg_video_settings_display() {
		global $post;
		wp_nonce_field( 'wplg_repeatable_meta_box_nonce', 'wplg_repeatable_meta_box_nonce' );
		$wplg_video_src = get_post_meta($post->ID, 'wplg_video_src_meta', true);
		$wplg_video_poster = get_post_meta($post->ID, 'wplg_video_poster_meta', true);
		$wplg_actions = get_post_meta($post->ID, 'wplg_video_actions_meta', true);
		?>

		<form class="wplg-settings-form">
		<!-- Shortcode -->
		
		<h1>shortcode [wplg_video id="<?php echo get_the_ID();?>"]</h1><br><br>

		<!-- Shortcode END -->	
		
		<!-- Video source -->
		<table class="wplg-settings-table">
			<tbody>
				<tr>
					<td>
						<label for="wplg_video_src">Video Source/URL</label>
					</td>
					<td>
						<input required id="wplg_video_src" name="wplg_video_src" type="text" value="<?php echo($wplg_video_src);?>">
					</td>
				</tr>
				<tr>
					<td>
						<label for="wplg_video_poster">Poster</label>
					</td>
					<td>
						<input id="wplg_video_poster" name="wplg_video_poster" type="text" value="<?php echo($wplg_video_poster);?>">
					</td>
				</tr>
			</tbody>
		</table>
		<!-- Video source END -->	

		<!-- Repeatable actions -->
		<script type="text/javascript">
			jQuery(document).ready(function( $ ){
				$( '#add-row' ).on('click', function() {
					var row = $( '.empty-table.screen-reader-text' ).clone(true);
					row.find('input').each(function(){
						$(this).attr('required', 'required');
					})
					row.removeClass( 'empty-table screen-reader-text' );
					row.insertBefore( '#wplg_repeatable_actions_con > table:last' );
					return false;
				});
				$( '.remove-row' ).on('click', function() {
					$(this).parents('table').remove();
					return false;
				});
			});
		</script>
		
		<section id="wplg_repeatable_actions_con">
			<?php
			if ( $wplg_actions ) :
			foreach ( $wplg_actions as $field ) {
			?>
			<table>
				<tr>
					<td>
						<label for="">Time Marker</label><br>
					</td>
					<td>
						<input required type="text"  placeholder="00:00" name="TimeMarker[]" value="<?php if($field['TimeMarker'] != '') echo esc_attr( $field['TimeMarker'] ); ?>" />
					</td>
					<td>
						<label for="">FORM ID</label><br>
					</td>
					<td>
						<input required type="text" placeholder="form id" name="FormID[]" value="<?php if ($field['FormID'] != '') echo esc_attr( html_entity_decode($field['FormID']) ); ?>" />
					</td>
					<td style="vertical-align:bottom;"><a class="button remove-row" href="#1">Remove</a></td>
				</tr>
			</table>
			<?php
			}
			else :
			// show a blank one
			?>
			<table>
				<tr>
					<td>
						<label for="">Time Marker</label><br>
					</td>
					<td>
						<input required type="text" placeholder="00:00" title="TimeMarker" name="TimeMarker[]" /></td>
					</td>
					<td>
						<label for="">FORM ID</label><br>
					</td>
					<td>
						<input required type="text" placeholder="form id" name="FormID[]"/>
					</td>
					<td style="vertical-align:bottom;"><a class="button  cmb-remove-row-button button-disabled" href="#">Remove</a></td>
				</tr>
			</table>
			<?php endif; ?>
			<table class="empty-table screen-reader-text">
				<tr>
					<td>
						<label for="">Time Marker</label><br>
					</td>
					<td>
						<input type="text" placeholder="00:00" name="TimeMarker[]"/></td>
					</td>
					<td>
						<label for="">FORM ID</label><br>
					</td>
					<td>
						<input type="text" placeholder="form id" name="FormID[]"/>
					</td>
					<td style="vertical-align:bottom;"><a class="button remove-row" href="#">Remove</a></td>
				</tr>
			</table>
		</section>
		
		<p><a id="add-row" class="button" href="#">Add another</a></p>
		<!-- Repeatable actions END -->
		
		<p><button class="wplg-settings-form__save button">Save</button></p>

		</form>

		<br>

		<?php
	}

	public function wplg_video_settings_save_ajax_handler() {
		
		do_action('save_post');

		return json_encode(array(
			'response' => 'saved'
		));
	}

	public function wplg_video_remove_publish() {
		if(get_post_type($post_ID) == 'wplg_video'){
			?><style type="text/css">
			#publishing-action {display:none!important;}
			</style><?php
		}
	}

	public function wplg_video_settings_save($post_id) {
		if ( ! isset( $_POST['wplg_repeatable_meta_box_nonce'] ) ||
		! wp_verify_nonce( $_POST['wplg_repeatable_meta_box_nonce'], 'wplg_repeatable_meta_box_nonce' ) )
			return;

		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
			return;

		if (!current_user_can('edit_post', $post_id))
			return;

		/**
		 * Save wplg_video_src_meta
		 */
		$video_src_old = get_post_meta($post_id, 'wplg_video_src_meta', true);
		$video_src = $_POST['wplg_video_src'];

		if ( !empty( $video_src ) && $video_src != $video_src_old )
			update_post_meta( $post_id, 'wplg_video_src_meta', $video_src );
		elseif ( empty($video_src) && $video_src_old )
			delete_post_meta( $post_id, 'wplg_video_src_meta', $video_src_old );

		/**
		 * Save wplg_video_poster
		 */
		$wplg_video_poster_old = get_post_meta($post_id, 'wplg_video_poster_meta', true);
		$wplg_video_poster = $_POST['wplg_video_poster'];

		if ( !empty( $wplg_video_poster ) && $wplg_video_poster != $wplg_video_poster_old )
			update_post_meta( $post_id, 'wplg_video_poster_meta', $wplg_video_poster );
		elseif ( empty($wplg_video_poster) && $wplg_video_poster_old )
			delete_post_meta( $post_id, 'wplg_video_poster_meta', $wplg_video_poster_old );

		/**
		 * Save wplg_video_actions_meta
		 */
		$old_actions = get_post_meta($post_id, 'wplg_video_actions_meta', true);
		$new_actions = array();
		$timeMarkers = $_POST['TimeMarker'];
		$html = $_POST['FormID'];
		$count = count( $timeMarkers );
		for ( $i = 0; $i < $count; $i++ ) {
			if ( $timeMarkers[$i] != '' ) :
				$new_actions[$i]['TimeMarker'] = htmlentities($timeMarkers[$i]);
				$new_actions[$i]['FormID'] = htmlentities($html[$i]); 
			endif;
		}
		if ( !empty( $new_actions ) && $new_actions != $old_actions )
			update_post_meta( $post_id, 'wplg_video_actions_meta', $new_actions );
		elseif ( empty($new_actions) && $old_actions )
			delete_post_meta( $post_id, 'wplg_video_actions_meta', $old_actions );
	}

	public function register_wplg_form_post_type() {
		$labels = array(
			'name'                  => _x( 'wplg form', 'Post Type General Name', 'wplg' ),
			'singular_name'         => _x( 'wplg form', 'Post Type Singular Name', 'wplg' ),
			'menu_name'             => __( 'wplg form', 'wplg' ),
			'name_admin_bar'        => __( 'wplg form', 'wplg' ),
			'archives'              => __( 'Item Archives', 'wplg' ),
			'attributes'            => __( 'Item Attributes', 'wplg' ),
			'parent_item_colon'     => __( 'Parent Item:', 'wplg' ),
			'all_items'             => __( 'Forms', 'wplg' ),
			'add_new_item'          => __( 'Add New Item', 'wplg' ),
			'add_new'               => __( 'Add New', 'wplg' ),
			'new_item'              => __( 'New Item', 'wplg' ),
			'edit_item'             => __( 'Edit Item', 'wplg' ),
			'update_item'           => __( 'Update Item', 'wplg' ),
			'view_item'             => __( 'View Item', 'wplg' ),
			'view_items'            => __( 'View Items', 'wplg' ),
			'search_items'          => __( 'Search Item', 'wplg' ),
			'not_found'             => __( 'Not found', 'wplg' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'wplg' ),
			'featured_image'        => __( 'Featured Image', 'wplg' ),
			'set_featured_image'    => __( 'Set featured image', 'wplg' ),
			'remove_featured_image' => __( 'Remove featured image', 'wplg' ),
			'use_featured_image'    => __( 'Use as featured image', 'wplg' ),
			'insert_into_item'      => __( 'Insert into item', 'wplg' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'wplg' ),
			'items_list'            => __( 'Items list', 'wplg' ),
			'items_list_navigation' => __( 'Items list navigation', 'wplg' ),
			'filter_items_list'     => __( 'Filter items list', 'wplg' ),
		);
		$args = array(
			'label'                 => __( 'wplg_form', 'wplg' ),
			'Html'           => '',
			'labels'                => $labels,
			'supports'              => array( 'title' ),
			'taxonomies'            => array(),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => "wplg_menu",
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => false,
			'rewrite' 				=> array('slug' => 'wplg_form', 'with_front' => false),
			'exclude_from_search'   => true,
			'publicly_queryable'    => true,
			'capability_type'       => 'page',
			'show_in_rest'			=> true
			
		);
		register_post_type( 'wplg_form', $args );
	}

	public function add_wplg_form_meta_boxes() {
		add_meta_box(
			'wplg_settings',
			__( 'WPLG Form', 'wplg' ),
			array( $this, 'wplg_form_settings_display' ),
			array( 'wplg_form' ),
			'advanced',
			'high'
		);		
	} 

	public function wplg_form_settings_display() {
		global $post;
		wp_nonce_field( 'wplg_form_meta_box_nonce', 'wplg_form_meta_box_nonce' );
		$wplg_form = get_post_meta($post->ID, 'wplg_form_meta', true);
		$wplg_form_submit_txt = get_post_meta($post->ID, 'wplg_form_submit_txt_meta', true);
		echo("<br>");
		echo "ID: " . "<strong>" . get_the_ID() . "</strong>";
		echo("<br><br>");
		?>
			<label for="wplg_form_submit_txt">Submit button text</label><br>
			<input name="wplg_form_submit_txt" value="<?php echo $wplg_form_submit_txt;?>" /><br><br>
			<label for="wplg_form">Input tags</label><br>
			<textarea name="wplg_form" rows="10" cols="100"><?php echo $wplg_form;?></textarea><br>
			<br>
		<?php		
	}

	public function wplg_form_settings_save($post_id) {
		if ( ! isset( $_POST['wplg_form_meta_box_nonce'] ) ||
		! wp_verify_nonce( $_POST['wplg_form_meta_box_nonce'], 'wplg_form_meta_box_nonce' ) )
			return;

		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
			return;

		if (!current_user_can('edit_post', $post_id))
			return;
		
		/**
		 * Save wplg_form_meta
		 */
		$wplg_form_meta_old = get_post_meta($post_id, 'wplg_form_meta', true);
		$wplg_form_meta = $_POST['wplg_form'];

		if ( !empty( $wplg_form_meta ) && $wplg_form_meta != $wplg_form_meta_old )
			update_post_meta( $post_id, 'wplg_form_meta', $wplg_form_meta );
		elseif ( empty($wplg_form_meta) && $wplg_form_meta_old )
			delete_post_meta( $post_id, 'wplg_form_meta', $wplg_form_meta_old );

		/**
		 * Save wplg_form_submit_txt_meta
		 */
		$wplg_form_submit_txt_meta_old = get_post_meta($post_id, 'wplg_form_submit_txt_meta', true);
		$wplg_form_submit_txt_meta = $_POST['wplg_form_submit_txt'];

		if ( !empty( $wplg_form_submit_txt_meta ) && $wplg_form_submit_txt_meta != $wplg_form_submit_txt_meta_old )
			update_post_meta( $post_id, 'wplg_form_submit_txt_meta', $wplg_form_submit_txt_meta );
		elseif ( empty($wplg_form_submit_txt_meta) && $wplg_form_submit_txt_meta_old )
			delete_post_meta( $post_id, 'wplg_form_submit_txt_meta', $wplg_form_submit_txt_meta_old );

	}
	
}
