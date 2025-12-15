<?php

namespace AMPortfolioTheme\Api;

use AMPortfolioTheme\Admin\Settings_Helper;

defined( 'ABSPATH' ) || exit;

class Zoho_Token_Manager {

	/**
	 * Ensure the access token is valid; refresh if expired.
	 *
	 * @param array $settings Current theme settings.
	 * @return string|\WP_Error Valid access token, or WP_Error on failure.
	 */
	public static function ensure_valid_token( $settings = null ) {
		if ( null === $settings ) {
			$settings = Settings_Helper::get_current_settings();
		}

		$credentials = self::extract_credentials( $settings );

		// If token is missing, error.
		if ( empty( $credentials['access_token'] ) ) {
			return new \WP_Error( 'zoho_no_token', __( 'No access token available.', 'portfolio' ) );
		}

		// Check if token is still valid (with a 5-minute buffer).
		if ( self::is_token_valid( $credentials['expires'] ) ) {
			return $credentials['access_token'];
		}

		// Token expired or about to expire; refresh it.
		if ( ! self::has_refresh_credentials( $credentials ) ) {
			return new \WP_Error( 'zoho_refresh_missing', __( 'Missing credentials for token refresh.', 'portfolio' ) );
		}

		$new_token_data = self::refresh_token( $credentials );
		if ( is_wp_error( $new_token_data ) ) {
			return $new_token_data;
		}

		self::update_token_settings( $new_token_data['access_token'], $new_token_data['expires'] );

		return $new_token_data['access_token'];
	}

	/**
	 * Extract credentials from settings and constants.
	 *
	 * @param array $settings Theme settings.
	 * @return array Extracted credentials.
	 */
	private static function extract_credentials( $settings ) {
		return array(
			'access_token'    => $settings['zoho_access_token'] ?? '',
			'refresh_token'   => $settings['zoho_refresh_token'] ?? '',
			'expires'         => $settings['zoho_token_expires'] ?? 0,
			'client_id'       => defined( 'PORTFOLIO_ZOHO_CLIENT_ID' ) ? \PORTFOLIO_ZOHO_CLIENT_ID : '',
			'client_secret'   => defined( 'PORTFOLIO_ZOHO_CLIENT_SECRET' ) ? \PORTFOLIO_ZOHO_CLIENT_SECRET : '',
			'accounts_server' => $settings['zoho_accounts_server'] ?? '',
		);
	}

	/**
	 * Check if token is still valid (with a 5-minute buffer).
	 *
	 * @param int $expires Token expiration timestamp.
	 * @return bool True if token is valid.
	 */
	private static function is_token_valid( int $expires ) {
		return $expires > ( time() + 300 );
	}

	/**
	 * Check if refresh credentials are present.
	 *
	 * @param array $credentials Extracted credentials.
	 * @return bool True if all required credentials are present.
	 */
	private static function has_refresh_credentials( $credentials ) {
		return ! empty( $credentials['refresh_token'] )
			&& ! empty( $credentials['client_id'] )
			&& ! empty( $credentials['client_secret'] )
			&& ! empty( $credentials['accounts_server'] );
	}

	/**
	 * Refresh the access token using refresh token.
	 *
	 * @param array $credentials Extracted credentials.
	 * @return array|\WP_Error New token data (access_token, expires) or WP_Error.
	 */
	private static function refresh_token( $credentials ) {
		$refresh_url = rtrim( $credentials['accounts_server'], '/' ) . '/oauth/v2/token';

		$args = array(
			'body' => array(
				'refresh_token' => $credentials['refresh_token'],
				'grant_type'    => 'refresh_token',
				'client_id'     => $credentials['client_id'],
				'client_secret' => $credentials['client_secret'],
			),
		);

		$response = wp_remote_post( $refresh_url, $args );

		if ( is_wp_error( $response ) ) {
			return new \WP_Error( 'zoho_refresh_failed', $response->get_error_message() );
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( empty( $data['access_token'] ) ) {
			log_message(
				'Failed to refresh token. Response code: ' . wp_remote_retrieve_response_code( $response ),
				'Zoho_Token_Manager',
				'error'
			);
			return new \WP_Error( 'zoho_refresh_invalid', __( 'Failed to refresh access token.', 'portfolio' ) );
		}

		$new_expires_in = $data['expires_in'] ?? 3600;
		return array(
			'access_token' => $data['access_token'],
			'expires'      => time() + $new_expires_in,
		);
	}

	/**
	 * Update settings with new token and expiration.
	 *
	 * @param string $new_access_token New access token.
	 * @param int    $new_expires New expiration timestamp.
	 * @return void
	 */
	private static function update_token_settings( $new_access_token, $new_expires ) {
		$partial_settings = array(
			'zoho_access_token'  => $new_access_token,
			'zoho_token_expires' => $new_expires,
		);

		Settings_Helper::update_settings( $partial_settings );
	}
}
