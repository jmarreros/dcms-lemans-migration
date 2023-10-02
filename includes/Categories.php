<?php

namespace dcms\lemans\includes;


use dcms\lemans\database\ExternalDB;

class Categories {
	private ExternalDB $externalDb;

	public function __construct() {
		dcms_include_files_library();
		$this->externalDb = new ExternalDB();
	}

	public function migrate_categories( $path ) {
		$woo_last_id_category = $this->create_parent_category_menu( $path );
		$woo_last_id_category = $this->create_current_category_menu( $path, $woo_last_id_category );
		$this->create_child_categories_menu( $path, $woo_last_id_category );
		$this->create_child_categories_by_id( $woo_last_id_category );
	}

	private function create_parent_category_menu( $path ): ?int {
		// Get current menu path data
		$path_parent_id = $this->externalDb->get_menu_parent_id_from_path( $path );

		if ( is_null( $path_parent_id ) ) {
			return null;
		}

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

	private function create_current_category_menu( $path, $woo_last_id_category ): int {
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

	private function create_child_categories_menu( $path, $woo_last_id_category, $current_id_category = 0 ): void {
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
			$this->create_child_categories_menu( '', $woo_last_id_sub_category, intval( $item->id ) );
		}

	}

	public function create_child_categories_by_id( $woo_last_id_category, $current_id_category = 0 ) {

		// Get id_category external
		if ( ! $current_id_category ) {
			$current_id_category = intval( get_term_meta( $woo_last_id_category, 'external_id', true ) );
			if ( ! $current_id_category ) {
				return;
			}
		}

		// Get all subcategories
		$items = $this->externalDb->get_categories_from_id( $current_id_category );

		foreach ( $items as $item ) {
			$data_categories = [];

			// Data parent category
			$data_category = [
				'id'          => $item->id,
				'title'       => $item->category_name,
				'slug'        => $item->slug,
				'order'       => $item->ordering,
				'parent_id'   => $item->category_parent_id,
				'description' => $item->category_description,
				'level'       => null,
				'external_id' => $item->id, //Pass external virtuemart category id
			];

			$data_categories[]        = $data_category;
			$woo_last_id_sub_category = $this->create_categories( $data_categories, false, $woo_last_id_category );
			$this->create_child_categories_by_id( $woo_last_id_sub_category, intval( $item->id ) );
		}
	}

	private function create_categories( $data_categories, $ancestors, $woo_parent_id = null ): int {
		if ( $ancestors ) {
			$data_categories = array_reverse( $data_categories );
		}

		$woo_category_id = 0;

		foreach ( $data_categories as $data_category ) {
			$category_title       = $data_category['title'];
			$category_slug        = $data_category['slug'];
			$external_menu_id     = $data_category['id'];
			$category_level       = $data_category['level'];
			$category_order       = $data_category['order'];
			$category_desc        = $data_category['description'] ?? '';
			$category_external_id = $data_category['external_id'] ?? 0;

			// Check if category exists by slug
			$category_term = get_term_by( 'slug', $category_slug, 'product_cat', ARRAY_A );

			// Category exists, get id_category
			if ( $category_term ) {
				error_log( print_r( 'Category exists : ' . $category_title . '-' . $woo_category_id, true ) );
				$woo_category_id = $category_term['term_id'];
			} // Category not exists, create category
			else {
				$term_data = wp_insert_term( $category_title, 'product_cat', [
					'slug'        => $category_slug,
					'parent'      => $woo_parent_id,
					'description' => $category_desc
				] );

				if ( ! is_wp_error( $term_data ) ) {
					error_log( print_r( 'Category added : ' . $category_title . '-' . $woo_category_id, true ) );
					$woo_category_id = $term_data['term_id'];
					$id_category     = $category_external_id;

					if ( ! $id_category ) {
						$link        = $this->externalDb->get_link_from_id_menu( $external_menu_id );
						$id_category = get_id_category_from_link( $link );
					}

					if ( $id_category ) {
						// Add terms metadata
						add_term_meta( $woo_category_id, 'external_id', $id_category, true );
						add_term_meta( $woo_category_id, 'external_level', $category_level, true );
						update_term_meta( $woo_category_id, 'order', $category_order );

						//Add image category
						$image_url = $this->externalDb->get_url_image_category( $id_category );
						if ( $image_url ) {
							$image_url = DCMS_LEMANS_EXTERNAL_DOMAIN . $image_url;
							$id_image  = media_sideload_image( $image_url, 0, null, 'id' );
							if ( ! is_wp_error( $id_image ) ) {
								update_term_meta( $woo_category_id, 'thumbnail_id', $id_image );
							}
						}
					}

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