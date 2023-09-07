<?php

namespace dcms\lemans\database;

use wpdb;

class ExternalDB {

	public function connection(): wpdb {
		$data_cn = data_connection_external_db();
		extract( $data_cn );

		return new wpdb( $database_user, $database_pass, $database_name, $database_server );
	}


	public function get_menu_parent_id_from_path( $path ): int {
		$wpdb = $this->connection();
		// Get current menu path data
		$sql = "SELECT parent_id FROM evhfm_menu WHERE `path` = '$path'";

		return $wpdb->get_var( $sql );
	}

	public function get_menu_data_from_id( $id ): object {
		$wpdb = $this->connection();
		$sql  = "SELECT id, title, alias, link, parent_id, level, ordering  FROM evhfm_menu WHERE id = $id";

		return $wpdb->get_row( $sql );
	}

	public function get_menu_data_from_path( $path ): object {
		$wpdb = $this->connection();
		// Get current menu path data
		$sql = "SELECT id, title, alias, link, parent_id, level, ordering  FROM evhfm_menu WHERE `path` = '$path'";

		return $wpdb->get_row( $sql );
	}

	public function get_menu_items_data_from_parent_id( $id ): array {
		$wpdb = $this->connection();
		$sql  = "SELECT id, title, alias, link, parent_id, level, ordering  FROM evhfm_menu WHERE parent_id = $id";

		return $wpdb->get_results( $sql );
	}

	public function get_menu_id_from_path( $path ): int {
		$wpdb = $this->connection();
		$sql  = "SELECT id  FROM evhfm_menu WHERE `path` = '$path'";

		return $wpdb->get_var( $sql );
	}
}
