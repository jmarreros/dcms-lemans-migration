<?php

namespace dcms\lemans\database;

class Database {

	private $wpdb;

	public function __construct() {
		global $wpdb;

		$this->wpdb = $wpdb;
	}


	public function get_woo_category_id_from_external_id( $id ): ?int {
		$tbl_terms = $this->wpdb->prefix . 'termmeta';
		$sql       = "SELECT term_id FROM $tbl_terms WHERE meta_key = 'external_id' AND meta_value = '$id'";

		$id_woo_category = $this->wpdb->get_var( $sql );

		return $id_woo_category ? (int) $id_woo_category : null;
	}

}


