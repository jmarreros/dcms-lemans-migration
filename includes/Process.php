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
		$batch = 100;
		$total = $_REQUEST['total'] ?? false;
		$step  = $_REQUEST['step'] ?? 0;
		$count = $step * $batch;


		// Procesamos la información
		sleep( 0.5 );
		error_log( "step: " . $step . " - count: " . $count );
		// ----

		// TODO: contar la cantidad de registros para el total del archivo data.csv
		error_log( print_r( "Total: $total", true ) );

		$step ++;

		// Obtenemos el total
		if ( ! $total ) {
			$total = ( new FileCSV() )->get_total_rows_file();
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