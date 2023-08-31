<?php
/*
Plugin Name: Migration Lemans
Plugin URI: https://decodecms.com
Description: Plugin Migration Lemans
Version: 1.0
Author: Jhon Marreros Guzmán
Author URI: https://decodecms.com
Text Domain: dcms-lemans-migration
Domain Path: languages
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
*/

namespace dcms\lemans;

require __DIR__ . '/vendor/autoload.php';

use dcms\lemans\includes\Configuration;
use dcms\lemans\includes\Enqueue;
use dcms\lemans\includes\Plugin;
use dcms\lemans\includes\Submenu;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin class to handle settings constants and loading files
 **/
final class Loader {

	// Define all the constants we need
	public function define_constants(): void {
		define( 'DCMS_LEMANS_VERSION', '1.0' );
		define( 'DCMS_LEMANS_PATH', plugin_dir_path( __FILE__ ) );
		define( 'DCMS_LEMANS_URL', plugin_dir_url( __FILE__ ) );
		define( 'DCMS_LEMANS_BASE_NAME', plugin_basename( __FILE__ ) );
		define( 'DCMS_LEMANS_SUBMENU', 'tools.php' );
	}

	// Load tex domain
	public function load_domain() {
		add_action( 'plugins_loaded', function () {
			$path_languages = dirname( DCMS_LEMANS_BASE_NAME ) . '/languages/';
			load_plugin_textdomain( 'dcms-lemans-migration', false, $path_languages );
		} );
	}

	// Add link to plugin list
	public function add_link_plugin(): void {
		add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), function ( $links ) {
			return array_merge( array(
				'<a href="' . esc_url( admin_url( DCMS_LEMANS_SUBMENU . '?page=lemans-migration' ) ) . '">' . __( 'Settings', 'dcms-lemans-migration' ) . '</a>'
			), $links );
		} );
	}

	// Initialize all
	public function init(): void {
		$this->define_constants();
		$this->load_domain();
		$this->add_link_plugin();
		new Plugin();
		new SubMenu();
		new Enqueue();
		new Configuration();
	}
}

$dcms_lemans_process = new Loader();
$dcms_lemans_process->init();
