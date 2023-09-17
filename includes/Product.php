<?php

namespace dcms\lemans\includes;

use dcms\lemans\database\ExternalDB;
use WC_Product;
use WC_Product_Attribute;
use WC_Product_Simple;
use WC_Product_Variable;
use WC_Product_Variation;
use Exception;

class Product {

	public ExternalDB $external_db;

	public function __construct() {
		$this->external_db = new ExternalDB();
		dcms_include_files_library();
	}

	public function get_id_product_by_sku( $sku ): int {
		return wc_get_product_id_by_sku( $sku );
	}

	public function create_product( $row, $ids_woo_categories ): int {
		$id_virtuemart      = $row['virtuemart_product_id'];
		$product_variations = $this->external_db->get_custom_data_cart_variant( $id_virtuemart );

		if ( $product_variations ) {
			return $this->create_variable_product( $row, $ids_woo_categories, $product_variations );
		}

		return $this->create_simple_product( $row, $ids_woo_categories );
	}

	private function create_simple_product( $row, $ids_woo_categories ): int {
		$product_woo = new WC_Product_Simple();
		try {
			$product_woo = $this->set_general_product_data( $product_woo, $ids_woo_categories, $row );

			return $product_woo->save();
		} catch ( Exception $e ) {
			error_log( "Exception - create simple product: " . $this->build_sku( $row ), $e->getMessage() );

			return 0;
		}
	}

	private function create_variable_product( $row, $ids_woo_categories, $product_variations ): int {
		$product_woo = new WC_Product_Variable();

		try {
			$product_woo    = $this->set_general_product_data( $product_woo, $ids_woo_categories, $row );
			$variation_name = make_title_variation( $product_variations[0]->custom_title );

			if ( ! $variation_name ) {
				return 0;
			}

			$attribute = new WC_Product_Attribute();
			$attribute->set_name( $variation_name );
			$attribute->set_options( [ 'elemento1', 'elemento2', 'elemento3' ] );
			$attribute->set_position( 0 );
			$attribute->set_visible( true );
			$attribute->set_variation( true );

			$product_woo->set_attributes( [ $attribute ] );

			$id_product_woo = $product_woo->save();

			$variation = new WC_Product_Variation();
			$variation->set_parent_id( $id_product_woo );
			$variation->set_attributes( [ $variation_name => 'elemento1' ] );
			$variation->set_regular_price( 100 );
			$variation->save();

			$variation = new WC_Product_Variation();
			$variation->set_parent_id( $id_product_woo );
			$variation->set_attributes( [ $variation_name => 'elemento2' ] );
			$variation->set_regular_price( 200 );
			$variation->save();

			return $id_product_woo;

		} catch ( Exception $e ) {
			error_log( "Exception - create variable product: " . $this->build_sku( $row ), $e->getMessage() );

			return 0;
		}
	}

	private function set_general_product_data( $product_woo, $ids_woo_categories, $row ): WC_Product {
		$product_sku         = $this->build_sku( $row );
		$product_name        = $row['product_name'];
		$product_price       = $this->build_price( $row['product_price'] );
		$product_description = $row['product_desc'];
		$product_file_urls   = $row['file_url'];
		$id_virtuemart       = $row['virtuemart_product_id'];

		$product_woo->set_sku( $product_sku );
		$product_woo->set_name( $product_name );
		$product_woo->set_category_ids( $ids_woo_categories );
		$product_woo->set_price( $product_price );
		$product_woo->set_regular_price( $product_price );
		$product_woo->set_description( $product_description );
		$product_woo->update_meta_data( 'id_virtuemart', $id_virtuemart );
		$product_woo->set_short_description( $this->get_custom_short_description( $id_virtuemart ) );

//		$ids_images = $this->get_ids_images_product_from_server( $product_file_urls );
//		if ( count( $ids_images ) > 0 ) {
//			$product_woo->set_image_id( $ids_images[0] );
//			$product_woo->set_gallery_image_ids( $ids_images );
//		}

		return $product_woo;
	}

	public function update_product( $id_woo_product, $id_woo_category ) {
		error_log( print_r( "update - " . $id_woo_product, true ) );
	}

	public function build_sku( $row ): string {
		return $row['product_sku'] . ' - ' . $row['virtuemart_product_id'];
	}

	public function build_price( $price ): float {
		return $price / 100;
	}

	// Observación: Siempre tomará la ruta remota, ya que la ruta VM la interpreta dinámicamente
	public function get_ids_images_product_from_server( $file_path ): array {
		$path_images = get_images_route( $file_path, DCMS_LEMANS_SERVER_PATH ); // to array

		$ids_images = [];
		foreach ( $path_images as $path_image ) {

			if ( file_exists( $path_image ) ) {
				// Get image from server
				$file_array = array(
					'name'     => wp_basename( $path_image ),
					'tmp_name' => $path_image
				);
				$id_image   = media_handle_sideload( $file_array );

			} else {
				// if image not exists, then get image from url
				error_log( print_r( 'Remote image from url', true ) );
				$url_image = str_replace( DCMS_LEMANS_SERVER_PATH, DCMS_LEMANS_EXTERNAL_DOMAIN, $path_image );
				$id_image  = media_sideload_image( $url_image, 0, null, 'id' );
			}

			if ( is_wp_error( $id_image ) ) {
				error_log( print_r( $id_image->get_error_message(), true ) );
				continue;
			}

			$ids_images[] = $id_image;
		}

		return $ids_images;
	}

	public function get_custom_short_description( $id_virtuemart ): ?string {
		return $this->external_db->get_custom_short_description( $id_virtuemart );
	}

	// Get related products
	//TODO: Deben guardarse todos los productos primero
	public function get_upsell_ids( $id_virtuemart ): array {
		return array_map( 'intval', $this->external_db->get_related_products( $id_virtuemart ) );
	}


}