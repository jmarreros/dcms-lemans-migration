<?php

namespace dcms\lemans\includes;

/**
 * Class for creating a dashboard submenu
 */
class Submenu {

	// Constructor
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'register_submenu' ] );
	}

	// Register submenu
	public function register_submenu(): void {
		add_submenu_page(
			DCMS_LEMANS_SUBMENU,
			__( 'Lemans Migration', 'dcms-lemans-migration' ),
			__( 'Lemans Migration', 'dcms-lemans-migration' ),
			'manage_options',
			'lemans-migration',
			[ $this, 'submenu_page_callback' ]
		);
	}

	// Callback, show view
	public function submenu_page_callback(): void {

		include_once( DCMS_LEMANS_PATH . '/views/main-screen.php' );
	}
}
