<?php

namespace dcms\lemans\database;

use wpdb;

class ExternalDB {
	public wpdb $cn;

	public function __construct() {
		$data_cn = data_connection_external_db();
		extract( $data_cn );

		$this->cn = new wpdb( $database_user, $database_pass, $database_name, $database_server );
	}


	public function get_menu_parent_id_from_path( $path ): ?int {
		$wpdb = $this->cn;
		// Get current menu path data
		$sql = "SELECT parent_id FROM evhfm_menu WHERE `path` = '$path'";

		return $wpdb->get_var( $sql );
	}

	public function get_menu_data_from_id( $id ): object {
		$wpdb = $this->cn;
		$sql  = "SELECT id, title, alias, link, parent_id, level, ordering  FROM evhfm_menu WHERE id = $id";

		return $wpdb->get_row( $sql );
	}

	public function get_menu_data_from_path( $path ): object {
		$wpdb = $this->cn;
		// Get current menu path data
		$sql = "SELECT id, title, alias, link, parent_id, level, ordering  FROM evhfm_menu WHERE `path` = '$path'";

		return $wpdb->get_row( $sql );
	}

	public function get_menu_items_data_from_parent_id( $id ): array {
		$wpdb = $this->cn;
		$sql  = "SELECT id, title, alias, link, parent_id, level, ordering  FROM evhfm_menu WHERE parent_id = $id";

		return $wpdb->get_results( $sql );
	}

	public function get_menu_id_from_path( $path ): int {
		$wpdb = $this->cn;
		$sql  = "SELECT id  FROM evhfm_menu WHERE `path` = '$path'";

		return $wpdb->get_var( $sql );
	}

	public function get_url_image_category( $id ): ?string {
		$wpdb = $this->cn;
		$sql  = "SELECT m.file_url  FROM evhfm_virtuemart_category_medias cm 
				INNER JOIN evhfm_virtuemart_medias m ON cm.virtuemart_media_id = m.virtuemart_media_id
				WHERE cm.virtuemart_category_id = $id  AND m.published = 1";

		return $wpdb->get_var( $sql );
	}


	public function get_link_from_id_menu( $id_menu ): ?string {
		$wpdb = $this->cn;
		$sql  = "SELECT link FROM evhfm_menu WHERE id = $id_menu";

		return $wpdb->get_var( $sql );
	}
}
