<?php

namespace AMPortfolioTheme\Api;

use AMPortfolioTheme\Admin\Settings_Helper;
use AMPortfolioTheme\Admin\Settings_Page;

defined( 'ABSPATH' ) || exit;

class Zoho_OAuth_Handler {

	const REST_NAMESPACE = 'portfolio/v1';
	const REST_ROUTE     = '/zoho-auth/callback';

	public static function init() {
		$self = new self();
		add_action( 'rest_api_init', array( $self, 'register_rest_routes' ) );
		return $self;
	}

	public function register_rest_routes() {
		register_rest_route(
			self::REST_NAMESPACE,
			self::REST_ROUTE,
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'handle_callback' ),
					'permission_callback' => array( $this, 'permission_check' ),
					'args'                => array(
						'code'            => array(
							'required'          => true,
							'type'              => 'string',
							'sanitize_callback' => 'sanitize_text_field',
						),
						'accounts-server' => array(
							'required'          => true,
							'type'              => 'string',
							'sanitize_callback' => 'esc_url_raw',
						),
						'state'           => array(
							'required'          => true,
							'type'              => 'string',
							'sanitize_callback' => 'sanitize_text_field',
						),
					),
				),
			)
		);
	}

	public function permission_check() {
		// The callback is public because Zoho redirects the user here.
		// However, we should ensure the request is not malicious.
		// We could verify a state parameter if we had stored one.
		// For simplicity, allow any request.
		return true;
	}

	public function handle_callback( $request ) {
		$code            = $request->get_param( 'code' );
		$accounts_server = $request->get_param( 'accounts-server' );
		$state           = $request->get_param( 'state' );

		// Validate state.
		$state_validation = $this->validate_state( $state );
		if ( is_wp_error( $state_validation ) ) {
			return $state_validation;
		}

		// Get client credentials.
		$settings    = Settings_Helper::get_current_settings();
		$credentials = $this->get_client_credentials( $settings );
		if ( is_wp_error( $credentials ) ) {
			return $credentials;
		}
		$client_id     = $credentials['client_id'];
		$client_secret = $credentials['client_secret'];

		// Request token.
		$token_data = $this->request_token(
			$code,
			$accounts_server,
			$client_id,
			$client_secret
		);
		if ( is_wp_error( $token_data ) ) {
			return $token_data;
		}

		// Save tokens.
		$save_result = $this->save_tokens(
			$token_data,
			$accounts_server,
		);
		if ( is_wp_error( $save_result ) ) {
			return $save_result;
		}

		// Redirect to settings page.
		$this->redirect_to_settings();
	}

	/**
	 * Validate the OAuth state parameter.
	 *
	 * @param string $state The state parameter from the request.
	 * @return true|\WP_Error True if valid, WP_Error otherwise.
	 */
	private function validate_state( $state ) {
		if ( empty( $state ) ) {
			return new \WP_Error(
				'zoho_state_missing',
				__( 'State parameter is missing.', 'portfolio' ),
				array( 'status' => 400 )
			);
		}

		$transient_key = 'zoho_oauth_state_' . $state;
		$stored_state  = get_transient( $transient_key );

		if ( false === $stored_state ) {
			return new \WP_Error(
				'zoho_state_invalid',
				__( 'Invalid or expired state parameter.', 'portfolio' ),
				array( 'status' => 400 )
			);
		}

		// Clean up the transient after validation.
		delete_transient( $transient_key );
		return true;
	}

	/**
	 * Get client credentials from settings.
	 *
	 * @param array $settings The current settings.
	 * @return array|\WP_Error Array with client_id and client_secret, or WP_Error.
	 */
	private function get_client_credentials( $settings ) {
		$client_id     = $settings['zoho_client_id'] ?? '';
		$client_secret = $settings['zoho_client_secret'] ?? '';

		if ( empty( $client_id ) || empty( $client_secret ) ) {
			return new \WP_Error(
				'zoho_credentials_missing',
				__( 'Zoho OAuth credentials are not configured.', 'portfolio' ),
				array( 'status' => 400 )
			);
		}

		return compact( 'client_id', 'client_secret' );
	}

	/**
	 * Request access token from Zoho OAuth server.
	 *
	 * @param string $code            Authorization code.
	 * @param string $accounts_server Zoho accounts server URL.
	 * @param string $client_id       Client ID.
	 * @param string $client_secret   Client secret.
	 * @return array|\WP_Error Token data on success, WP_Error on failure.
	 */
	private function request_token( $code, $accounts_server, $client_id, $client_secret ) {
		// Build redirect URI (must match the one used in authorization request).
		$redirect_uri = rest_url( self::REST_NAMESPACE . self::REST_ROUTE );

		// Ensure the accounts server URL does not have a trailing slash.
		$accounts_server = rtrim( $accounts_server, '/' );

		$token_url = $accounts_server . '/oauth/v2/token';
		$args      = array(
			'body' => array(
				'code'          => $code,
				'grant_type'    => 'authorization_code',
				'client_id'     => $client_id,
				'client_secret' => $client_secret,
				'redirect_uri'  => $redirect_uri,
				'scope'         => 'ZohoMail.messages.CREATE,ZohoMail.accounts.READ',
			),
		);

		$response = wp_remote_post( $token_url, $args );

		if ( is_wp_error( $response ) ) {
			return new \WP_Error(
				'zoho_token_request_failed',
				$response->get_error_message(),
				array( 'status' => 500 )
			);
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( empty( $data['access_token'] ) ) {
			// Log error.
			error_log( 'Zoho OAuth error: ' . $body );
			return new \WP_Error(
				'zoho_token_invalid',
				__( 'Failed to obtain access token from Zoho.', 'portfolio' ),
				array( 'status' => 400 )
			);
		}

		return $data;
	}

	/**
	 * Save tokens to settings.
	 *
	 * @param array  $token_data      Token data from Zoho.
	 * @param string $accounts_server Zoho accounts server URL.
	 * @return bool|\WP_Error True on success, WP_Error on failure.
	 */
	private function save_tokens( $token_data, $accounts_server ) {
		$partial_settings = array(
			'zoho_access_token'    => $token_data['access_token'],
			'zoho_refresh_token'   => $token_data['refresh_token'] ?? '',
			'zoho_token_expires'   => time() + (int) ( $token_data['expires_in'] ?? 3600 ),
			'zoho_accounts_server' => $accounts_server,
		);

		$updated_settings = Settings_Helper::update_settings( $partial_settings );

		if ( false === $updated_settings ) {
			return new \WP_Error(
				'zoho_token_save_failed',
				__( 'Failed to save tokens.', 'portfolio' ),
				array( 'status' => 500 )
			);
		}

		return true;
	}

	/**
	 * Redirect to settings page with success message.
	 */
	private function redirect_to_settings() {
		$settings_url = admin_url( 'themes.php?page=' . Settings_Page::PAGE_SLUG . '&tab=zoho-mail&zoho-auth=success' );
		wp_safe_redirect( $settings_url );
		exit;
	}
}
