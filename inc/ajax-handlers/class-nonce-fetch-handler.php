<?php

namespace AMPortfolioTheme\AjaxHandlers;

defined( 'ABSPATH' ) || exit;

class Nonce_Fetch_Handler {

	public static function init() {
		$self = new self();

		add_action( 'wp_ajax_get_fresh_contact_nonce', array( $self, 'handle_nonce_fetch' ) );
		add_action( 'wp_ajax_nopriv_get_fresh_contact_nonce', array( $self, 'handle_nonce_fetch' ) );
	}

	public function handle_nonce_fetch() {
		// Generate a fresh nonce for the contact form.
		$nonce = wp_create_nonce( 'am_contact_form_nonce' );

		// Send the nonce as a JSON response.
		wp_send_json_success(
			array(
				'nonce' => $nonce,
			)
		);
	}
}
