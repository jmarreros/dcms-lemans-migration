<?php

namespace dcms\lemans\includes;


class Configuration {
	public function __construct() {
		add_action( 'wp_ajax_dcms_migrate_initial_category', [ $this, 'migrate_initial_category' ] );
	}

	public function migrate_initial_category() {
		$categories = new Categories();

		$paths = get_urls_menu();

		foreach ( $paths as $path ) {
			error_log( print_r( "--- Path migrado: " . $path . " ---", true ) );
			$categories->migrate_categories( $path );
		}

		$res = [ 'message' => "categorías migradas", 'status' => 1, 'data' => null ];
		wp_send_json( $res );
	}

}