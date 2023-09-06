<?php

namespace dcms\lemans\database;

use wpdb;

class ExternalDB {

	public function connection(): wpdb {
		$data_cn = data_connection_external_db();
		extract( $data_cn );

		return new wpdb( $database_user, $database_pass, $database_name, $database_server );
	}


	public function create_parent_category( $path ) {
		$wpdb = $this->connection();

		// Get current menu path data
		$sql      = "SELECT id, title, alias, link, parent_id, level, ordering  FROM evhfm_menu WHERE `path` = '$path'";
		$row_menu = $wpdb->get_row( $sql );

		// Get parent id
		$row_parent_id = $row_menu->parent_id;

		$data_categories = [];
		// Store data categories
		do {
			$sql             = "SELECT id, title, alias, link, parent_id, level, ordering  FROM evhfm_menu WHERE id = $row_parent_id";
			$row_parent_menu = $wpdb->get_row( $sql );

			// Update parent id
			$row_parent_id = $row_parent_menu->parent_id;
			$parent_level  = $row_parent_menu->level;

			// Data parent menu
			$data_category = [
				'id'        => $row_parent_menu->id,
				'title'     => $row_parent_menu->title,
				'slug'      => $row_parent_menu->alias,
				'parent_id' => $row_parent_id,
				'level'     => $parent_level,
			];

			$data_categories[] = $data_category;
		} while ( $parent_level != 0 );


		$parent_root = null;
		foreach ( array_reverse( $data_categories ) as $data_category ) {
			$category_title  = $data_category['title'];
			$category_slug   = $data_category['slug'];
			$category_parent = $data_category['parent_id'];
			$category_level  = $data_category['level'];

			$id_category = 0;

			// Check if category exists
			$term_data = term_exists( $category_title, 'product_cat' );

			// Category exists, get id_category
			if ( ! is_null( $term_data ) ) {
				$id_category = $term_data['term_id'];
			} // Category not exists, create category
			else {
				$term_data = wp_insert_term( $category_title, 'product_cat', [
					'slug'   => $category_slug,
					'parent' => $parent_root
				] );

				if ( ! is_wp_error( $term_data ) ) {
					$id_category = $term_data['term_id'];
				}
			}
			$parent_root = $id_category;
		}

	}


	private function create_category() {

	}

//	public function get_all_items(): array {
//		$wpdb = $this->connection();
//
//		$sql = "SELECT * FROM evhfm_menu
//				WHERE `link` LIKE '%option=com_virtuemart%'
//				AND published = 1
//				AND menutype = 'menu-principal-lemans'
//				AND `level` = 1";
//
//		return $wpdb->get_results( $sql, ARRAY_A );
//	}

}
