<?php

namespace dcms\lemans\includes;

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
		$batch = 10;
		$total = $_REQUEST['total'] ?? false;
		$step  = $_REQUEST['step'] ?? 0;
		$count = $step * $batch;

		$fileCSV = new FileCSV();

		// Procesamos la información
		error_log( "step: " . $step . " - count: " . $count );
		$data = $fileCSV->get_data_range_csv_file( $count + 1, $count + $batch );
		error_log( print_r( $data, true ) );

		$step ++;

		// Obtenemos el total
		if ( ! $total ) {
//			$total = $fileCSV->get_total_rows_file();
			$total = 100;
		}

		// Comprobamos la finalización
		if ( $count < $total ) {
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