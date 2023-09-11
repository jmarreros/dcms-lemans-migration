<?php

namespace dcms\lemans\includes;

use SplFileObject;

class FileCSV {
	const FILE_NAME = ABSPATH . "data.csv";

	// Get total rows file
	public function get_total_rows_file(): int {
		$file = new SplFileObject( self::FILE_NAME, 'r' );
		$file->seek( PHP_INT_MAX );

		return $file->key() + 1;
	}

	public function get_data_range_csv_file( $start, $end ): ?array {
		$file = new SplFileObject( self::FILE_NAME, 'r' );
		$file->seek( $start );
		$data = [];
		while ( $file->key() <= $end ) {
			$data[] = $file->current();
			$file->next();
		}

		$file->seek( 0 );
		$header = str_getcsv( $file->current() );

		if ( $data ) {
			$data = array_map( function ( $row ) use ( $header ) {
				if ( count( str_getcsv( $row ) ) != count( $header ) ) {
					return null;
				}

				return array_combine( $header, str_getcsv( $row ) );
			}, $data );
		}

		return $data;
	}
}