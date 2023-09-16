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
//			"llantas-vehiculo-actual-italianas-adaptables/llantas-adaptables-italianas",
//			"llantas-para-vehiculos-coches-clasicos/llantas-braid",
			"llantas-para-vehiculos-coches-clasicos/llantas-japan-racing",
//			"llantas-vehiculo-actual-italianas-adaptables/llantas-carbonado",

//          // TODO: es la misma categoría: "llantas-para-vehiculos-coches-clasicos/llantas-japan-racing"
//			/// "llantas-vehiculo-actual-italianas-adaptables/llantas-japan-racing",


//			"llantas-para-vehiculos-coches-clasicos/llantas-targa",
//			"llantas-para-vehiculos-coches-clasicos/llantas-cromodora",
//			"llantas-para-vehiculos-coches-clasicos/llantas-minilite-style",
//			"llantas-para-vehiculos-coches-clasicos/llantas-alpine-desing",
//			"llantas-para-vehiculos-coches-clasicos/llantas-lenso",
//
//			// TODO: revisar esta url que en realidad es la siguiente, hacer una redirección
////			--"llantas-y-neumaticos/llantas-para-vehiculos-clasicos/llantas-lenso",
////			--"llantas-y-neumaticos/clasicos-lemanscenter/llantas-para-vhiculo-clasico/llantas-targa",
//
//			"iluminacion/faros-y-accesorios",
//			"iluminacion/iluminacion/bombillas-y-leds",
//
//			"volantes/volantes-y-pinas",
//
//			"llantas-y-neumaticos/clasicos-lemanscenter/fundas-para-coches-clasicos",
//			"llantas-y-neumaticos/clasicos-lemanscenter/retrovisores",
//			"llantas-y-neumaticos/zona-equipamiento-automovil/competicion/instrumentacion-de-rally",
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
	function get_id_category_from_link( $link ): ?int {
		preg_match( '/.*virtuemart_category_id=(\d+).*/', $link, $matches );

		return $matches[1] ?? null;
	}
}

if ( ! function_exists( 'get_images_route' ) ) {
	function get_images_route( $file_url, $route_before ): array {
		if ( empty( $file_url ) ) {
			return [];
		}

		$urls = explode( '|', $file_url );

		return array_map( function ( $url ) use ( $route_before ) {
			return $route_before . $url;
		}, $urls );
	}
}