<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       jankalinowski.net@gmail.com
 * @since      1.0.0
 *
 * @package    wplg
 * @subpackage wplg/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    wplg
 * @subpackage wplg/public
 * @author     Jan Kalinowski <jankalinowski.net@gmail.com>
 */
class wplg_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( 'wplg-intl-tel-input-css', plugin_dir_url( __DIR__ ) . '/intl-tel-input/build/css/intlTelInput.css', array(), $this->version, 'all' );

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wplg-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( 'wplg-intl-tel-input-js', plugin_dir_url( __DIR__ )  . '/intl-tel-input/build/js/intlTelInput.min.js', array('jquery'), '', 'true' );

		wp_enqueue_script( 'wplg-public-js', plugin_dir_url( __FILE__)  . 'build/js/wplg-public.min.js', array( 'jquery' ), '', true );          
		wp_localize_script( 'wplg-public-js', 'wplg_params' , array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	}

	public function get_actions_ajax_handler() {
		$wplg_id = $_POST['wplg_id'];
		$response;
		if ($wplg_id) {
			$response = get_post_meta($wplg_id, 'wplg_video_actions_meta', true);			
		}
		exit(json_encode($response));
	} 

	public function wplg_save_lead_ajax_handler() {
		// delete_option('wplg_leads');
		$data = json_decode(stripslashes($_POST['data']));	
		$form_id = json_decode(stripslashes($_POST['form_id']));
		$default_value = [];
		
		if (false === get_option('wplg_leads') && false === update_option('wplg_leads',false)) add_option('wplg_leads', $default_value);
		
		if (get_option('wplg_leads') !== false) {
			$wplg_leads = get_option('wplg_leads');
			// array_push($wplg_leads, $data); 	
			if (!array_key_exists($form_id,$wplg_leads)) {
				$wplg_leads[$form_id] = [];
			}
			array_push($wplg_leads[$form_id], $data);			
			update_option('wplg_leads', $wplg_leads, true);
		}
		
		echo(json_encode($data));

		die();
	} 

	public function wplg_get_form_ajax_handler() {
		$form_id = $_POST['form_id'];
		if ($form_id) {
			$html = get_post_meta($form_id, 'wplg_form_meta', true);			
			$submit_txt = get_post_meta($form_id, 'wplg_form_submit_txt_meta', true);			
		}
		exit(json_encode(array(
			"html" => $html,
			"submit_txt" => $submit_txt
		)));
	} 

}
