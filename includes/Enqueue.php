<?php

namespace dcms\lemans\includes;

/**
 * Class for enqueue javascript and styles files in WordPress
 */
class Enqueue {

	public function __construct() {
		add_action( 'admin_enqueue_scripts', [ $this, 'register_scripts_backend' ] );
	}

	// Backend scripts
	public function register_scripts_backend() {

		// Javascript
		wp_register_script( 'lemans-script',
			DCMS_LEMANS_URL . '/assets/script.js',
			[ 'jquery' ],
			DCMS_LEMANS_VERSION,
			true );

		wp_localize_script( 'lemans-script',
			'dcms_lemans',
			[
				'ajaxurl'      => admin_url( 'admin-ajax.php' ),
				'nonce_lemans' => wp_create_nonce( 'ajax-nonce-lemans' ),
				'sending'      => __( 'Enviando...', 'lemans-woocommerce' ),
				'processing'   => __( 'Procesando...', 'syscom-woocommerce' )
			] );

		wp_enqueue_script( 'lemans-script' );


		// CSS
		wp_register_style( 'lemans-style',
			DCMS_LEMANS_URL . '/assets/style.css',
			[],
			DCMS_LEMANS_VERSION );

		wp_enqueue_style( 'lemans-style' );
	}

}