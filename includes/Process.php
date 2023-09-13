<?php

namespace dcms\lemans\includes;

use dcms\lemans\database\Database;
use SplFileObject;

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
//		error_log( "step: " . $step . " - count: " . $count );

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
				foreach ( $ids_categories as $id_category ) {
					$id_woo_category = $db->get_woo_category_id_from_external_id( $id_category );

					if ( $id_woo_category ) {
						$sku            = $product->build_sku( $row );
						$id_woo_product = $product->get_id_product_by_sku( $sku );

						if ( ! $id_woo_product ) {
							$id_woo_product = $product->create_product( $row, $id_woo_category );
							
							if ( $id_woo_product ) {
								error_log( print_r( "Producto creado: $id_woo_product", true ) );
							}
						} else {
							$product->update_product( $id_woo_product, $id_woo_category );
						}

					}

				}

			}

		}

		// TODO:
		// - Capturar la categoría de cada registro
		// - Verificar si la categoría existe en la base de datos de WooCommerce
		// - Si existe, verificar el resto de campos del producto
		// - Verificar el SKU, no debe haber un SKU repetido
		// - Verificar si es un producto variable o simple


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