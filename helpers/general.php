<?php
if ( ! function_exists( 'data_connection_external_db' ) ) {
	function data_connection_external_db(): array {
		return [
			'database_name'   => 'jo_lemans2',
			'database_user'   => 'root',
			'database_pass'   => 'root',
			'database_server' => 'localhost'
		];
	}
}


if ( ! function_exists( 'get_urls_menu' ) ) {
	function get_urls_menu(): array {
		return [
			"llantas-vehiculo-actual-italianas-adaptables/llantas-adaptables-italianas",
//		"llantas-para-vehiculos-coches-clasicos/llantas-braid",
//		"llantas-para-vehiculos-coches-clasicos/llantas-japan-racing",
//		"llantas-vehiculo-actual-italianas-adaptables/llantas-carbonado"
		];
	}
}


// Include files for upload image
if ( ! function_exists( 'dcms_include_files_library' ) ) {
	function dcms_include_files_library(): void {
		require_once( ABSPATH . 'wp-admin/includes/media.php' );
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
	}
}

if ( ! function_exists( 'get_id_category_from_link' ) ) {
	function get_id_category_from_link( $link ) {

	}
}

