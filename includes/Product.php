<?php

namespace dcms\lemans\includes;

use WC_Product_Simple;
use Exception;

class Product {
	public function get_id_product_by_sku( $sku ): int {
		return wc_get_product_id_by_sku( $sku );
	}

	public function create_product( $row, $id_woo_category ): int {
		$product_sku         = $this->build_sku( $row );
		$product_name        = $row['product_name'];
		$product_price       = $this->build_price( $row['product_price'] );
		$product_description = $row['product_desc'];

		$product_woo = new WC_Product_Simple();

		try {
			$product_woo->set_sku( $product_sku );
			$product_woo->set_name( $product_name );
			$product_woo->set_category_ids( [ $id_woo_category ] );
			$product_woo->set_price( $product_price );
			$product_woo->set_regular_price( $product_price );
			$product_woo->set_description( $product_description );

			return $product_woo->save();

		} catch ( Exception $e ) {
			error_log( "Exception - create product: $product_sku ", $e->getMessage() );

			return 0;
		}

	}

	public function update_product( $id_woo_product, $id_woo_category ) {
		error_log( print_r( "update - " . $id_woo_product . " - " . $id_woo_category, true ) );
	}

	public function build_sku( $row ): string {
		return $row['product_sku'] . ' - ' . $row['virtuemart_product_id'];
	}

	public function build_price( $price ): float {
		return $price / 100;
	}
}