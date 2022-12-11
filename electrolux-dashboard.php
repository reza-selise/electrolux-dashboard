<?php

/**
 * @wordpress-plugin
 * Plugin Name:       Electrolux Dashboard
 * Plugin URI:        https://edb.com
 * Description:       Dashboard Backend
 * Version:           1.0.0
 * Author:            Electorlux
 * Author URI:        https://edb.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       electrolux-dashboard
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
define( 'ELECTROLUX_DASHBOARD_VERSION', '1.0.0' );

require plugin_dir_path( __FILE__ ) . 'inc/add-menu-page.php';

// plugin looaded hook

add_action( 'plugins_loaded', 'el_load_necessary_files' );





function el_load_necessary_files(){
	// die();
}
add_action( 'admin_enqueue_scripts', 'el_load_scripts' );

function el_load_scripts(){
	// die();
	wp_enqueue_script( 
		'el-dashboard-react-script', 
		plugin_dir_url( __FILE__ ). 'public/js/index.js'  , 
		[],
		rand(1,100),
		true
	);
	$localize_array = [
		'homeUrl'   => site_url(),
		'assetsUrl' => plugin_dir_url( __FILE__ ) . 'public'
	];
	wp_localize_script( 'el-dashboard-react-script', 'eluxDashboard', $localize_array );
}