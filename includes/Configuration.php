<?php

namespace dcms\lemans\includes;


class Configuration {
	public function __construct() {
		add_action( 'wp_ajax_dcms_migrate_initial_category', [ $this, 'migrate_initial_category' ] );
		add_action( 'wp_ajax_dcms_process_batch_ajax_migration', [ $this, 'batch_process_ajax_migration_products' ] );
		add_action( 'wp_ajax_dcms_process_related_products', [ $this, 'process_related_products' ] );
	}

	public function migrate_initial_category() {
		$process_categories = new Process();
		$process_categories->migrate_categories();
	}

	public function batch_process_ajax_migration_products() {
		$process_products = new Process();
		$process_products->migrate_batch_products();
	}

	public function process_related_products() {
		wp_send_json( [ 'message' => "Productos relacionados procesados", 'status' => 1, 'data' => null ] );
		
	}

}