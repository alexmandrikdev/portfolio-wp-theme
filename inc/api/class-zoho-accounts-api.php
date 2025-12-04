<?php

namespace AMPortfolioTheme\Api;

use AMPortfolioTheme\Admin\Settings_Helper;

defined( 'ABSPATH' ) || exit;

class Zoho_Accounts_API {

	const REST_NAMESPACE = 'portfolio/v1';
	const REST_ROUTE     = '/zoho-accounts';

	/**
	 * Initialize the class.
	 *
	 * @return self
	 */
	public static function init() {
		$self = new self();
		add_action( 'rest_api_init', array( $self, 'register_rest_routes' ) );
		return $self;
	}

	/**
	 * Register REST routes.
	 */
	public function register_rest_routes() {
		register_rest_route(
			self::REST_NAMESPACE,
			self::REST_ROUTE,
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_accounts' ),
					'permission_callback' => array( $this, 'permission_check' ),
				),
			)
		);
	}

	/**
	 * Permission check for REST route.
	 *
	 * @param \WP_REST_Request $request The request.
	 * @return bool|\WP_Error
	 */
	public function permission_check( $request ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return new \WP_Error(
				'rest_forbidden',
				__( 'You do not have permission to manage settings.', 'portfolio' ),
				array( 'status' => 403 )
			);
		}

		$nonce = $request->get_header( 'X-WP-Nonce' );
		if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
			return new \WP_Error(
				'rest_invalid_nonce',
				__( 'Security check failed.', 'portfolio' ),
				array( 'status' => 403 )
			);
		}

		return true;
	}

	/**
	 * Get Zoho Mail accounts via API.
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function get_accounts() {
		$settings = Settings_Helper::get_current_settings();

		// Check if we have an access token.
		if ( empty( $settings['zoho_access_token'] ) ) {
			return new \WP_Error(
				'zoho_no_token',
				__( 'Zoho Mail access token is missing. Please authorize first.', 'portfolio' ),
				array( 'status' => 400 )
			);
		}

		// Ensure token is valid and refresh if needed.
		$token = Zoho_Token_Manager::ensure_valid_token( $settings );
		if ( is_wp_error( $token ) ) {
			return $token;
		}

		$base_api_url = $settings['zoho_base_api_url'] ?? 'https://mail.zoho.com';
		$endpoint     = rtrim( $base_api_url, '/' ) . '/api/accounts';

		$args = array(
			'headers' => array(
				'Accept'        => 'application/json',
				'Content-Type'  => 'application/json',
				'Authorization' => 'Zoho-oauthtoken ' . $token,
			),
			'timeout' => 30,
		);

		$response = wp_remote_get( $endpoint, $args );

		if ( is_wp_error( $response ) ) {
			return new \WP_Error(
				'zoho_api_error',
				$response->get_error_message(),
				array( 'status' => 500 )
			);
		}

		$status_code = wp_remote_retrieve_response_code( $response );
		$body        = wp_remote_retrieve_body( $response );

		if ( $status_code >= 200 && $status_code < 300 ) {
			$data     = json_decode( $body, true );
			$accounts = $this->extract_accounts( $data );
			$response = new \WP_REST_Response(
				array(
					'success'  => true,
					'accounts' => $accounts,
				)
			);
			$response->header( 'Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0' );
			return $response;
		} else {
			// Log error.
			error_log( 'Zoho_Accounts_API: API error ' . $status_code . ' - ' . $body );
			return new \WP_Error(
				'zoho_api_failed',
				__( 'Failed to fetch accounts from Zoho Mail.', 'portfolio' ),
				array( 'status' => $status_code )
			);
		}
	}

	/**
	 * Extract relevant account data from Zoho API response.
	 *
	 * @param array $data The decoded API response.
	 * @return array List of accounts with id, email, displayName, etc.
	 */
	private function extract_accounts( $data ) {
		$accounts = array();

		if ( empty( $data['data'] ) || ! is_array( $data['data'] ) ) {
			return $accounts;
		}

		foreach ( $data['data'] as $account ) {
			$account_id = $account['accountId'] ?? '';
			if ( empty( $account_id ) ) {
				continue;
			}

			$primary_email = '';
			if ( isset( $account['primaryEmailAddress'] ) ) {
				$primary_email = $account['primaryEmailAddress'];
			} elseif ( isset( $account['emailAddress'][0]['mailId'] ) ) {
				$primary_email = $account['emailAddress'][0]['mailId'];
			}

			$display_name = $account['accountDisplayName'] ?? $account['displayName'] ?? $primary_email;

			$accounts[] = array(
				'id'          => $account_id,
				'email'       => $primary_email,
				'displayName' => $display_name,
				'type'        => $account['type'] ?? '',
				'enabled'     => $account['enabled'] ?? false,
			);
		}

		return $accounts;
	}
}
