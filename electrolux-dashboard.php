<?php

/**
 * Plugin Name:       Electrolux Dashboard
 * Plugin URI:        https://electrolux-dashboard.com
 * Description:       Dashboard Backend
 * Version:           1.0.0
 * Author:            Electorlux
 * Author URI:        https://electrolux-dashboard.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       electrolux-dashboard
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

final class Electrolux_Dashboard {

    /**
     * Static Property To Hold Singleton Instance
     */
    private static $instance;

    /**
     * Throw Error While Trying To Clone Object
     *
     * @since 1.0.0
     * @return void
     */
    public function __clone() {
        _doing_it_wrong( __FUNCTION__, __( 'Cloning is forbidden.', 'pharmalys-essential' ), '1.0.0' );
    }

    /**
     * Disabling Un-serialization Of This Class
     */
    public function __wakeup() {
        _doing_it_wrong( __FUNCTION__, __( 'Unserializing instances of this class is forbidden.', 'pharmalys-essential' ), '1.0.0' );
    }

    /**
     * Singleton Instance
     *
     * @since 1.0.0
     * @return void
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Setup Plugin Requirements
     *
     * @since 1.0.0
     * @return void
     */
    private function __construct() {
        // Always load translation
        add_action( 'plugins_loaded', [ $this, 'load_text_domain' ] );

        // Initialize plugin functionalities or quit
        $this->initialize_modules();
    }

    /**
     * Load Localization Files
     *
     * @since 1.0
     * @return void
     */
    public function load_text_domain() {
        $locale = apply_filters( 'plugin_locale', get_user_locale(), 'electrolux-dashboard' );

        unload_textdomain( 'electrolux-dashboard' );
        load_textdomain( 'electrolux-dashboard', WP_LANG_DIR . '/electrolux-dashboard/electrolux-dashboard-' . $locale . '.mo' );
        load_plugin_textdomain( 'electrolux-dashboard', false, self::get_plugin_dir() . 'languages/' );
    }

    /**
     * Initialize Plugin Modules
     *
     * @since 1.0.0
     * @return void
     */
    private function initialize_modules() {
        register_activation_hook( self::get_plugin_file(), [ $this, 'activate' ] );
        register_deactivation_hook( self::get_plugin_file(), [ $this, 'deactivate' ] );
        
		// Initialize all modules through plugins_loaded
		add_action( 'plugins_loaded', [ $this, 'init' ] );
    }

	public function init(){
        $this->initialize_constants();
        $this->include_files();
        $this->initialize_components();
	}

    /**
     * Include All Required Files
     *
     * @since 1.0.0
     * @return void
     */
    private function include_files() {
        require_once ELUX_DB_PLUGIN_DIR . 'inc/add-menu-page.php';
        require_once ELUX_DB_PLUGIN_DIR . 'inc/api.php';
    }

    /**
     * Initialize All Components
     *
     * @since 1.0.0
     * @return void
     */
    private function initialize_components() {

        // Register scripts and styles first
        if ( $this->is_request( 'admin' ) ) {
            add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ] );
        }

        if ( $this->is_request( 'frontend' ) ) {
            add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
        }
        
        // Core\Admin\Menu::instance()->init();
        ( new \Elux_DB\Elux_DB_API() )->init();
    }

    /**
     * Register scripts and styles for admin
     *
     * @return void
     */
    public function admin_scripts() {
       // var_dump('ahdjgashd'); die();
        wp_enqueue_script(
            'elux-db-react-script',
            ELUX_DB_PLUGIN_URL . 'public/js/index.js',
            [],
            rand(1,10),
            true
        );

        $localize_array = [
            'homeUrl'   => site_url(),
            'assetsUrl' => ELUX_DB_PLUGIN_URL . 'public',
        ];
        wp_localize_script( 'elux-db-react-script', 'eluxDashboard', $localize_array );
    }

    /**
     * Register scripts and styles for frontend
     *
     * @return void
     */
    public function frontend_scripts() {
    }

    /**
     * What type of request
     *
     * @param string $type admin,frontend, ajax, cron
     * @return boolean
     */
    private function is_request( $type ) {
        switch ( $type ) {
            case 'admin':
                return is_admin();
            case 'ajax':
                return defined( 'DOING_AJAX' );
            case 'cron':
                return defined( 'DOING_CRON' );
            case 'frontend':
                return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' ) && ! $this->is_rest_api_request();
        }
    }

    /**
     * Returns if the request is non-legacy REST API request.
     *
     * @return boolean
     */
    private function is_rest_api_request() {
        $server_request = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : false;

        if ( ! $server_request ) {
            return false;
        }

        $rest_prefix        = trailingslashit( rest_get_url_prefix() );
        $is_rest_request    = ( false !== strpos( $server_request, $rest_prefix ) );

		return $is_rest_request;
    }

    /**
     * Called Only Once While Activation
     *
     * @return void
     */
    public function activate() {

    }

    /**
     * Called Only Once While Deactivation
     *
     * @return void
     */
    public function deactivate() {

    }

    /**
     * Plugin Current Production Version
     *
     * @return string
     */
    public static function get_version() {
        return '1.0.0';
    }

    /**
     * Setup Plugin Constants
     *
     * @since 1.0.0
     * @return void
     */
    private function initialize_constants() {

        // Plugin Version
        define( 'ELUX_DB_VERSION', self::get_version() );

        // Plugin Main File
        define( 'ELUX_DB_PLUGIN_FILE', __FILE__ );

        // Plugin File Basename
        define( 'ELUX_DB_PLUGIN_BASE', self::get_plugin_basename() );

        // Plugin Main Directory Path
        define( 'ELUX_DB_PLUGIN_DIR', self::get_plugin_dir() );

        // Plugin Main Directory URL
        define( 'ELUX_DB_PLUGIN_URL', self::get_plugin_url() );

        // Plugin Assets Directory URL
        define( 'ELUX_DB_ASSETS_URL', self::get_assets_url() );

        // Plugin Assets Directory Path
        define( 'ELUX_DB_ASSETS_DIR', self::get_assets_dir() );

    }

    /**
     * Plugin Main File
     *
     * @return string
     */
    public static function get_plugin_file() {
        return __FILE__;
    }

    /**
     * Plugin Base Directory Path
     *
     * @return void
     */
    public static function get_plugin_dir() {
        return trailingslashit( plugin_dir_path( self::get_plugin_file() ) );
    }

    /**
     * Assets Directory URL
     *
     * @since 1.0.0
     * @return void
     */
    public static function get_assets_url() {
        return trailingslashit( ELUX_DB_PLUGIN_URL . 'assets' );
    }

    /**
     * Assets Directory Path
     *
     * @since 1.0.0
     * @return void
     */
    public static function get_assets_dir() {
        return trailingslashit( ELUX_DB_PLUGIN_DIR . 'assets' );
    }

    /**
     * Plugin Directory URL
     *
     * @return void
     */
    public static function get_plugin_url() {
        return trailingslashit( plugin_dir_url( ELUX_DB_PLUGIN_FILE ) );
    }

    /**
     * Plugin Basename
     *
     * @return void
     */
    public static function get_plugin_basename() {
        return plugin_basename( ELUX_DB_PLUGIN_FILE );
    }

}

Electrolux_Dashboard::get_instance();
