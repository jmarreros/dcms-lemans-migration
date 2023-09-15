<?php

namespace dcms\lemans\includes;

use Automattic\WooCommerce\Admin\RemoteInboxNotifications\EvaluationLogger;
use WC_Product_Simple;
use Exception;

class Product {

	public function __construct() {
		dcms_include_files_library();
	}

	public function get_id_product_by_sku( $sku ): int {
		return wc_get_product_id_by_sku( $sku );
	}

	public function create_product( $row, $ids_woo_categories ): int {
		$product_sku         = $this->build_sku( $row );
		$product_name        = $row['product_name'];
		$product_price       = $this->build_price( $row['product_price'] );
		$product_description = $row['product_desc'];
		$product_file_urls   = $row['file_url'];

		$product_woo = new WC_Product_Simple();

		try {
			$product_woo->set_sku( $product_sku );
			$product_woo->set_name( $product_name );
			$product_woo->set_category_ids( $ids_woo_categories );
			$product_woo->set_price( $product_price );
			$product_woo->set_regular_price( $product_price );
			$product_woo->set_description( $product_description );

			$ids_images = $this->get_ids_images_product_from_server( $product_file_urls );
			if ( count( $ids_images ) > 0 ) {
				$product_woo->set_image_id( $ids_images[0] );
				$product_woo->set_gallery_image_ids( $ids_images );
			}

			return $product_woo->save();

		} catch ( Exception $e ) {
			error_log( "Exception - create product: $product_sku ", $e->getMessage() );

			return 0;
		}

	}

	public function update_product( $id_woo_product, $id_woo_category ) {
		error_log( print_r( "update - " . $id_woo_product, true ) );
//		error_log( print_r( $id_woo_category, true ) );
	}

	public function build_sku( $row ): string {
		return $row['product_sku'] . ' - ' . $row['virtuemart_product_id'];
	}

	public function build_price( $price ): float {
		return $price / 100;
	}


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
				$id_image   = media_handle_sideload( $file_array, 0, null, [] );

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

//	public function get_ids_images_product_from_url( $file_path ): array {
//		$url_images = get_images_route( $file_path, DCMS_LEMANS_EXTERNAL_DOMAIN ); // to array
//
//		$ids_images = [];
//		foreach ( $url_images as $url_image ) {
//			$id_image = media_sideload_image( $url_image, 0, null, 'id' );
//			if ( ! is_wp_error( $id_image ) ) {
//				$ids_images[] = $id_image;
//			}
//		}
//
//		return $ids_images;
//	}

}