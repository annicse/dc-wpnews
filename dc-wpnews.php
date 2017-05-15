<?php
/**
 * Plugin Name: DC WPNews
 * Plugin URI: http://designcontainer.no/
 * Description: Shows news from custom posts. Ajax loading.
 * Version: 1.0.0
 * Author: DC
 * Author URI: http://designcontainer.no/
 * Requires at least: 4.0.0
 * Tested up to: 4.0.0
 *
 * Text Domain: dc-wpnews
 * Domain Path: /languages/
 *
 * @package Dc_wpnews
 * @category Core
 * @author Matty
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Returns the main instance of Dc_wpnews to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Dc_wpnews
 */
function Dc_wpnews() {
	return Dc_wpnews::instance();
} // End Dc_wpnews()

add_action( 'plugins_loaded', 'Dc_wpnews' );

/**
 * Main Dc_wpnews Class
 *
 * @class Dc_wpnews
 * @version	1.0.0
 * @since 1.0.0
 * @package	Dc_wpnews
 * @author DC
 */
final class Dc_wpnews {
	/**
	 * Dc_wpnews The single instance of Dc_wpnews.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $token;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $version;

	/**
	 * The plugin directory URL.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $plugin_url;

	/**
	 * The plugin directory path.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $plugin_path;

	// Admin - Start
	/**
	 * The admin object.
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $admin;

	/**
	 * The settings object.
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings;
	// Admin - End

	// Post Types - Start
	/**
	 * The post types we're registering.
	 * @var     array
	 * @access  public
	 * @since   1.0.0
	 */
	public $post_types = array();
	// Post Types - End
	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 */
	public function __construct () {
		$this->token 			= 'dc-wpnews';
		$this->plugin_url 		= plugin_dir_url( __FILE__ );
		$this->plugin_path 		= plugin_dir_path( __FILE__ );
		$this->version 			= '1.0.0';

		// Admin - Start
		require_once( 'classes/class-dc-wpnews-settings.php' );
			$this->settings = Dc_wpnews_Settings::instance();

		if ( is_admin() ) {
			require_once( 'classes/class-dc-wpnews-admin.php' );
			$this->admin = Dc_wpnews_Admin::instance();
		}
		// Admin - End

		// Post Types - Start
		require_once( 'classes/class-dc-wpnews-post-type.php' );
		require_once( 'classes/class-dc-wpnews-taxonomy.php' );

		// Register an example post type. To register other post types, duplicate this line.
		$this->post_types['news'] = new Dc_wpnews_Post_Type( 'news', __( 'News', 'dc-wpnews' ), __( 'News', 'dc-wpnews' ), array( 'menu_icon' => 'dashicons-carrot' ) );
		// Post Types - End
		register_activation_hook( __FILE__, array( $this, 'install' ) );

		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		require_once( 'classes/class-dc-wpnews-views.php' );
		$this->view = new Dc_wpnews_views;

	} // End __construct()

	/**
	 * Main Dc_wpnews Instance
	 *
	 * Ensures only one instance of Dc_wpnews is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Dc_wpnews()
	 * @return Main Dc_wpnews instance
	 */
	public static function instance () {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	} // End instance()

	/**
	 * Load the localisation file.
	 * @access  public
	 * @since   1.0.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'dc-wpnews', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	} // End load_plugin_textdomain()

	/**
	 * Cloning is forbidden.
	 * @access public
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 * @access public
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	} // End __wakeup()

	/**
	 * Installation. Runs on activation.
	 * @access  public
	 * @since   1.0.0
	 */
	public function install () {
		$this->_log_version_number();
	} // End install()

	/**
	 * Log the plugin version number.
	 * @access  private
	 * @since   1.0.0
	 */
	private function _log_version_number () {
		// Log the version number.
		update_option( $this->token . '-version', $this->version );
	} // End _log_version_number()

	public function load_publicview () {
		echo 'hello world';
	}
} // End Class
