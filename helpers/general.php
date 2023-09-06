<?php

function data_connection_external_db(): array {
	return [
		'database_name'   => 'jo_lemans2',
		'database_user'   => 'root',
		'database_pass'   => 'root',
		'database_server' => 'localhost'
	];
}

function get_urls_menu(): array {
	return [
		"llantas-vehiculo-actual-italianas-adaptables/llantas-adaptables-italianas",
//		"llantas-para-vehiculos-coches-clasicos/llantas-braid",
//		"llantas-para-vehiculos-coches-clasicos/llantas-japan-racing",
//		"llantas-vehiculo-actual-italianas-adaptables/llantas-carbonado"
	];
}