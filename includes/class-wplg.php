<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       jankalinowski.net@gmail.com
 * @since      1.0.0
 *
 * @package    wplg
 * @subpackage wplg/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    wplg
 * @subpackage wplg/includes
 * @author     Jan Kalinowski <jankalinowski.net@gmail.com>
 */
class wplg {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      wplg_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'wplg_VERSION' ) ) {
			$this->version = wplg_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'wplg';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - wplg_Loader. Orchestrates the hooks of the plugin.
	 * - wplg_i18n. Defines internationalization functionality.
	 * - wplg_Admin. Defines all hooks for the admin area.
	 * - wplg_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wplg-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wplg-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wplg-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wplg-public.php';

		$this->loader = new wplg_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the wplg_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new wplg_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new wplg_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'register_wplg_menu' );

		$this->loader->add_action( 'init', $plugin_admin, 'register_wplg_video_post_type' );
		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'add_wplg_video_meta_boxes' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'wplg_video_remove_publish' );
		$this->loader->add_action( 'save_post', $plugin_admin, 'wplg_video_settings_save' );		


		$this->loader->add_action( 'init', $plugin_admin, 'register_wplg_form_post_type' );
		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'add_wplg_form_meta_boxes' );
		$this->loader->add_action( 'save_post', $plugin_admin, 'wplg_form_settings_save' );

		$this->loader->add_action( 'wp_ajax_wplg_get_leads', $plugin_admin, 'wplg_get_leads_ajax_handler' );
		$this->loader->add_action( 'wp_ajax_nopriv_wplg_get_leads', $plugin_admin, 'wplg_get_leads_ajax_handler' );

		$this->loader->add_action( 'wp_ajax_wplg_video_settings_save', $plugin_admin, 'wplg_video_settings_save_ajax_handler' );
		$this->loader->add_action( 'wp_ajax_nopriv_wplg_video_settings_save', $plugin_admin, 'wplg_video_settings_save_ajax_handler' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new wplg_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->add_action( 'wp_ajax_get_actions', $plugin_public, 'get_actions_ajax_handler' );
		$this->loader->add_action( 'wp_ajax_nopriv_get_actions', $plugin_public, 'get_actions_ajax_handler' );

		$this->loader->add_action( 'wp_ajax_wplg_save_lead', $plugin_public, 'wplg_save_lead_ajax_handler' );
		$this->loader->add_action( 'wp_ajax_nopriv_wplg_save_lead', $plugin_public, 'wplg_save_lead_ajax_handler' );

		$this->loader->add_action( 'wp_ajax_wplg_get_form', $plugin_public, 'wplg_get_form_ajax_handler' );
		$this->loader->add_action( 'wp_ajax_nopriv_wplg_get_form', $plugin_public, 'wplg_get_form_ajax_handler' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    wplg_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
