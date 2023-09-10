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
}