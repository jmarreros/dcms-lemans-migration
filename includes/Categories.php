<?php

namespace dcms\lemans\includes;


use dcms\lemans\database\ExternalDB;

class Categories {
	private ExternalDB $externalDb;

	public function __construct() {
		$this->externalDb = new ExternalDB();
	}

	public function migrate_categories( $path ) {
		$woo_last_id_category = $this->create_parent_category( $path );
		$woo_last_id_category = $this->create_current_category( $path, $woo_last_id_category );
		$this->create_child_categories( $path, $woo_last_id_category );
	}

	private function create_parent_category( $path ): int {
		// Get current menu path data
		$path_parent_id = $this->externalDb->get_menu_parent_id_from_path( $path );

		$data_categories = [];
		// Store data categories
		do {
			$row_parent_menu = $this->externalDb->get_menu_data_from_id( $path_parent_id );

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

		return $this->create_categories( $data_categories, true );
	}

	private function create_current_category( $path, $woo_last_id_category ): int {
		// Get current menu path data
		$row = $this->externalDb->get_menu_data_from_path( $path );

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

		return $this->create_categories( $data_categories, false, $woo_last_id_category );
	}

	private function create_child_categories( $path, $woo_last_id_category, $current_id_category = 0 ): void {
		// Get current menu path data
		if ( ! empty( $path ) ) {
			$current_id_category = $this->externalDb->get_menu_id_from_path( $path );
		}

		// Get all subcategories
		$items = $this->externalDb->get_menu_items_data_from_parent_id( $current_id_category );

		foreach ( $items as $item ) {
			$data_categories = [];

			// Data parent menu
			$data_category = [
				'id'        => $item->id,
				'title'     => $item->title,
				'slug'      => $item->alias,
				'order'     => $item->ordering,
				'parent_id' => $item->parent_id,
				'level'     => $item->level,
			];

			$data_categories[]        = $data_category;
			$woo_last_id_sub_category = $this->create_categories( $data_categories, false, $woo_last_id_category );
			$this->create_child_categories( '', $woo_last_id_sub_category, intval( $item->id ) );
		}

	}

	private function create_categories( $data_categories, $ancestors, $woo_parent_id = null ): int {
		if ( $ancestors ) {
			$data_categories = array_reverse( $data_categories );
		}

		$woo_category_id = 0;

		foreach ( $data_categories as $data_category ) {
			$category_title = $data_category['title'];
			$category_slug  = $data_category['slug'];
			$category_id    = $data_category['id'];
			$category_level = $data_category['level'];
			$category_order = $data_category['order'];

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
					add_term_meta( $woo_category_id, 'external_id', $category_id, true );
					add_term_meta( $woo_category_id, 'external_level', $category_level, true );
					update_term_meta( $woo_category_id, 'order', $category_order );
				} else {
					$woo_category_id = 0;
					error_log( print_r( $term_data->get_error_message(), true ) );
					error_log( print_r( "Error to create category", true ) );
					break;
				}
			}

			$woo_parent_id = $woo_category_id;
		}

		return $woo_category_id;
	}

}