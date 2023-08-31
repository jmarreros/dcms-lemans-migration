<?php

namespace dcms\lemans\database;

use wpdb;

class ExternalDB {

	public function connection(): wpdb {
		$database_name   = 'jo_lemans2';
		$database_user   = 'root';
		$database_pass   = 'root';
		$database_server = 'localhost';

		return new wpdb( $database_user, $database_pass, $database_name, $database_server );
	}

	public function get_all_items(): array {
		$wpdb = $this->connection();

		$sql = "SELECT * FROM evhfm_menu 
				WHERE `link` LIKE '%option=com_virtuemart%' 
				AND published = 1 
				AND menutype = 'menu-principal-lemans'
				AND `level` = 1";

		return $wpdb->get_results( $sql, ARRAY_A );
	}
}
