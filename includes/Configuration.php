<?php

namespace dcms\lemans\includes;

use dcms\lemans\database\ExternalDB;

class Configuration {
	public function __construct() {
		add_action( 'wp_ajax_dcms_migrate_initial_category', [ $this, 'migrate_initial_category' ] );
	}

	public function migrate_initial_category() {
		$external = new ExternalDB();

		$paths = get_urls_menu();

		$external->migrate_categories( $paths[0] );

		$res = [ 'message' => "categorías migradas", 'status' => 1, 'data' => null ];
		wp_send_json( $res );
	}

}