<?php

namespace dcms\lemans\includes;

use dcms\lemans\database\Database;


class Process {

	public function migrate_categories() {
		$categories = new Categories();

		$paths = get_urls_menu();

		foreach ( $paths as $path ) {
			error_log( print_r( "--- Path migrado: " . $path . " ---", true ) );
			$categories->migrate_categories( $path );
		}

		$res = [ 'message' => "categorías migradas", 'status' => 1, 'data' => null ];
		wp_send_json( $res );
	}

	public function migrate_batch_products() {
		$batch = 100;
		$total = $_REQUEST['total'] ?? false;
		$step  = $_REQUEST['step'] ?? 0;
		$count = $step * $batch;

		$fileCSV = new FileCSV();
		$db      = new Database();
		$product = new Product();

		// Procesamos la información

		$length = $count + $batch;
		if ( $total ) {
			if ( $length > $total ) {
				$length = $total;
			}
		}

		// Get data range
		$data = $fileCSV->get_data_range_csv_file( $count + 1, $length );

		foreach ( $data as $row ) {
			if ( ! empty( $row['category_id'] ) ) {

				$ids_categories = array_map( 'intval', explode( '|', $row['category_id'] ) );

				// Fill categories woo
				$ids_woo_categories = [];
				foreach ( $ids_categories as $id_category ) {

					$id_woo_category = $db->get_woo_category_id_from_external_id( $id_category );

					if ( $id_woo_category ) {
						$ids_woo_categories[] = $id_woo_category;
					}
				}

				// If it has at least one valid Woo category, create or update product
				if ( count( $ids_woo_categories ) > 0 ) {
					$sku            = $product->build_sku( $row );
					$id_woo_product = $product->get_id_product_by_sku( $sku );

					if ( ! $id_woo_product ) {
						$id_woo_product = $product->create_product( $row, $ids_woo_categories );

						if ( $id_woo_product ) {
							error_log( print_r( "Producto creado: $id_woo_product", true ) );
						}
					} else {
						$product->update_product( $id_woo_product );
					}
				}

			}
		}

		$step ++;

		// Obtenemos el total
		if ( ! $total ) {
			$total = $fileCSV->get_total_rows_file();
		}

		// Comprobamos la finalización
		if ( $length < $total ) {
			$status = 0;
		} else {
			$status = 1;
		}

		// Construimos la respuesta
		$res = [
			'status' => $status,
			'step'   => $step,
			'count'  => $count,
			'total'  => $total,
		];

		wp_send_json( $res );
	}

}