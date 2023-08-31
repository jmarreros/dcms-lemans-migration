<?php

namespace dcms\lemans\includes;

class Configuration {
	public function __construct() {
		add_action( 'wp_ajax_dcms_migrate_initial_category', [ $this, 'migrate_initial_category' ] );
	}

	public function migrate_initial_category() {
		$res = [ 'message' => "categorías migradas", 'status' => 1 ];
		wp_send_json( $res );
	}
	
}