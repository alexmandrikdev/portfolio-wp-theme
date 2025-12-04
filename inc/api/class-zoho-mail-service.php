<?php

namespace AMPortfolioTheme\Api;

use AMPortfolioTheme\Admin\Settings_Helper;
use AMPortfolioTheme\Api\Zoho_Token_Manager;
use WP_Error;

defined( 'ABSPATH' ) || exit;

class Zoho_Mail_Service {

	/**
	 * Send an email via Zoho Mail API.
	 *
	 * @param string $to      Recipient email address.
	 * @param string $subject Email subject.
	 * @param string $message Email body (HTML).
	 * @return bool True on success, false on failure.
	 */
	public static function send_email( $to, $subject, $message ) {
		$settings = Settings_Helper::get_current_settings();

		if ( ! self::validate_configuration( $settings ) ) {
			return false;
		}

		$token = Zoho_Token_Manager::ensure_valid_token( $settings );
		if ( is_wp_error( $token ) ) {
			error_log( 'Zoho_Mail_Service: Token validation failed - ' . $token->get_error_message() );
			return false;
		}

		$from = self::get_from_address( $settings );
		if ( empty( $from ) ) {
			error_log( 'Zoho_Mail_Service: No from address configured.' );
			return false;
		}

		$endpoint = self::build_endpoint( $settings );
		$body     = self::build_request_body( $from, $to, $subject, $message );
		$args     = self::build_request_args( $token, $body );

		$response = wp_remote_post( $endpoint, $args );

		return self::handle_response( $response, $to );
	}

	/**
	 * Validate that required Zoho Mail configuration is present.
	 *
	 * @param array $settings The current settings.
	 * @return bool True if configuration is valid, false otherwise.
	 */
	private static function validate_configuration( $settings ) {
		if ( empty( $settings['zoho_access_token'] ) || empty( $settings['zoho_account_id'] ) ) {
			error_log( 'Zoho_Mail_Service: Missing access token or account ID.' );
			return false;
		}
		return true;
	}

	/**
	 * Determine the from address.
	 *
	 * @param array $settings The current settings.
	 * @return string The from email address.
	 */
	private static function get_from_address( $settings ) {
		$email = $settings['contact_email'] ?? get_option( 'admin_email' );
		if ( empty( $email ) ) {
			return '';
		}
		$site_title = get_bloginfo( 'name' );
		if ( ! empty( $site_title ) ) {
			// Format: "Site Title <email>".
			return sprintf( '%s <%s>', $site_title, $email );
		}
		return $email;
	}

	/**
	 * Build the API endpoint URL.
	 *
	 * @param array $settings The current settings.
	 * @return string The full endpoint URL.
	 */
	private static function build_endpoint( $settings ) {
		$account_id   = $settings['zoho_account_id'];
		$base_api_url = $settings['zoho_base_api_url'] ?? 'https://mail.zoho.com';
		return rtrim( $base_api_url, '/' ) . "/api/accounts/{$account_id}/messages";
	}

	/**
	 * Build the request body for the Zoho Mail API.
	 *
	 * @param string $from    Sender email address.
	 * @param string $to      Recipient email address.
	 * @param string $subject Email subject.
	 * @param string $message Email body (HTML).
	 * @return array The request body.
	 */
	private static function build_request_body( $from, $to, $subject, $message ) {
		return array(
			'fromAddress' => $from,
			'toAddress'   => $to,
			'subject'     => $subject,
			'content'     => $message,
			'askReceipt'  => 'yes',
		);
	}

	/**
	 * Build the request arguments for wp_remote_post.
	 *
	 * @param string $token The OAuth token.
	 * @param array  $body  The request body.
	 * @return array The request arguments.
	 */
	private static function build_request_args( $token, $body ) {
		return array(
			'headers' => array(
				'Accept'        => 'application/json',
				'Content-Type'  => 'application/json',
				'Authorization' => 'Zoho-oauthtoken ' . $token,
			),
			'body'    => wp_json_encode( $body ),
			'timeout' => 30,
		);
	}

	/**
	 * Handle the API response.
	 *
	 * @param array|WP_Error $response The response from wp_remote_post.
	 * @param string         $to       The recipient email address (for logging).
	 * @return bool True on success, false on failure.
	 */
	private static function handle_response( $response, $to ) {
		if ( is_wp_error( $response ) ) {
			error_log( 'Zoho_Mail_Service: API request failed - ' . $response->get_error_message() );
			return false;
		}

		$status_code = wp_remote_retrieve_response_code( $response );
		$body        = wp_remote_retrieve_body( $response );

		if ( $status_code >= 200 && $status_code < 300 ) {
			// Success.
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'Zoho_Mail_Service: Email sent successfully to ' . $to );
			}
			return true;
		} else {
			// Log error.
			error_log( 'Zoho_Mail_Service: API error ' . $status_code . ' - ' . $body );
			return false;
		}
	}
}
