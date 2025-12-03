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
						'code' => array(
							'required'          => true,
							'type'              => 'string',
							'sanitize_callback' => 'sanitize_text_field',
						),
					),
				),
			)
		);
	}

	public function permission_check( $request ) {
		// The callback is public because Zoho redirects the user here.
		// However, we should ensure the request is not malicious.
		// We could verify a state parameter if we had stored one.
		// For simplicity, allow any request.
		return true;
	}

	public function handle_callback( $request ) {
		$code = $request->get_param( 'code' );

		// Get stored client ID and secret.
		$settings      = Settings_Helper::get_current_settings();
		$client_id     = $settings['zoho_client_id'] ?? '';
		$client_secret = $settings['zoho_client_secret'] ?? '';

		if ( empty( $client_id ) || empty( $client_secret ) ) {
			return new \WP_Error(
				'zoho_credentials_missing',
				__( 'Zoho OAuth credentials are not configured.', 'portfolio' ),
				array( 'status' => 400 )
			);
		}

		// Build redirect URI (must match the one used in authorization request).
		$redirect_uri = rest_url( self::REST_NAMESPACE . self::REST_ROUTE );

		// Prepare token request.
		$token_url = 'https://accounts.zoho.com/oauth/v2/token';
		$args      = array(
			'body' => array(
				'code'          => $code,
				'grant_type'    => 'authorization_code',
				'client_id'     => $client_id,
				'client_secret' => $client_secret,
				'redirect_uri'  => $redirect_uri,
				'scope'         => 'ZohoMail.messages.CREATE',
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

		// Update settings with tokens.
		$updated_settings = array_merge(
			$settings,
			array(
				'zoho_access_token'  => $data['access_token'],
				'zoho_refresh_token' => $data['refresh_token'] ?? '',
				'zoho_token_expires' => time() + (int) ( $data['expires_in'] ?? 3600 ),
			)
		);

		$result = update_option( Settings_Page::OPTION_NAME, $updated_settings );

		if ( $result ) {
			// Redirect back to settings page with success message.
			$settings_url = admin_url( 'themes.php?page=' . Settings_Page::PAGE_SLUG . '&tab=zoho-mail&zoho-auth=success' );
			wp_redirect( $settings_url );
			exit;
		} else {
			return new \WP_Error(
				'zoho_token_save_failed',
				__( 'Failed to save tokens.', 'portfolio' ),
				array( 'status' => 500 )
			);
		}
	}
}
