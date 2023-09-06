<?php

namespace dcms\lemans\database;

use wpdb;

class ExternalDB {

	public function connection(): wpdb {
		$data_cn = data_connection_external_db();
		extract( $data_cn );

		return new wpdb( $database_user, $database_pass, $database_name, $database_server );
	}

	public function migrate_categories( $path ) {
		$this->create_parent_category( $path );

		// TODO : find id Woo parent category to assign to current category
		$this->create_current_category( $path );
	}

	private function create_parent_category( $path ) {
		$wpdb = $this->connection();

		// Get current menu path data
		$sql            = "SELECT parent_id FROM evhfm_menu WHERE `path` = '$path'";
		$path_parent_id = $wpdb->get_var( $sql );

		$data_categories = [];
		// Store data categories
		do {
			$sql             = "SELECT id, title, alias, link, parent_id, level, ordering  FROM evhfm_menu WHERE id = $path_parent_id";
			$row_parent_menu = $wpdb->get_row( $sql );

			// Update parent id
			$path_parent_id = $row_parent_menu->parent_id;
			$parent_level   = $row_parent_menu->level;

			// Data parent menu
			$data_category = [
				'id'        => $row_parent_menu->id,
				'title'     => $row_parent_menu->title,
				'slug'      => $row_parent_menu->alias,
				'order'     => $row_parent_menu->ordering,
				'parent_id' => $path_parent_id,
				'level'     => $parent_level,
			];

			$data_categories[] = $data_category;
		} while ( $parent_level != 0 );

		$this->create_categories( $data_categories, true );
	}

	private function create_current_category( $path ): void {
		$wpdb = $this->connection();

		// Get current menu path data
		$sql = "SELECT id, title, alias, link, parent_id, level, ordering  FROM evhfm_menu WHERE `path` = '$path'";
		$row = $wpdb->get_row( $sql );

		$data_categories = [];

		// Data parent menu
		$data_category = [
			'id'        => $row->id,
			'title'     => $row->title,
			'slug'      => $row->alias,
			'order'     => $row->ordering,
			'parent_id' => $row->parent_id,
			'level'     => $row->level,
		];

		$data_categories[] = $data_category;

		$this->create_categories( $data_categories, false );
	}


	private function create_categories( $data_categories, $ancestors ): void {
		if ( $ancestors ) {
			$data_categories = array_reverse( $data_categories );
		}

		$woo_parent_id = null;

		foreach ( $data_categories as $data_category ) {
			$category_title = $data_category['title'];
			$category_slug  = $data_category['slug'];
			$category_id    = $data_category['id'];
			$category_level = $data_category['level'];
			$category_order = $data_category['order'];

			$woo_category_id = 0;

			// Check if category exists
			$term_data = term_exists( $category_title, 'product_cat' );

			// Category exists, get id_category
			if ( ! is_null( $term_data ) ) {
				$woo_category_id = $term_data['term_id'];
			} // Category not exists, create category
			else {
				$term_data = wp_insert_term( $category_title, 'product_cat', [
					'slug'   => $category_slug,
					'parent' => $woo_parent_id
				] );

				if ( ! is_wp_error( $term_data ) ) {
					$woo_category_id = $term_data['term_id'];
				}
			}

			if ( $woo_category_id != 0 ) {
				// Add metadata to term category
				add_term_meta( $woo_category_id, 'external_id', $category_id, true );
				add_term_meta( $woo_category_id, 'external_level', $category_level, true );
				update_term_meta( $woo_category_id, 'order', $category_order );
				$woo_parent_id = $woo_category_id;
			} else {
				error_log( print_r( "Error to create category", true ) );
				break;
			}

		}
	}

}
