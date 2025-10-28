<?php

namespace AMPortfolioTheme\Admin;

defined( 'ABSPATH' ) || exit;

class Settings_Helper {

	public static function get_default_settings() {
		return array(
			'recaptcha_site_key'   => '',
			'recaptcha_secret_key' => '',
			// Add more default settings here as needed.
		);
	}

	public static function get_current_settings() {
		$settings = get_option( Settings_Page::OPTION_NAME, array() );
		return wp_parse_args( $settings, self::get_default_settings() );
	}

	public static function get_settings_schema() {
		return array(
			'recaptcha_site_key'   => array(
				'description'       => __( 'Google reCAPTCHA site key for frontend integration.', 'portfolio' ),
				'type'              => 'string',
				'required'          => false,
				'sanitize_callback' => 'sanitize_text_field',
			),
			'recaptcha_secret_key' => array(
				'description'       => __( 'Google reCAPTCHA secret key for server-side verification.', 'portfolio' ),
				'type'              => 'string',
				'required'          => false,
				'sanitize_callback' => 'sanitize_text_field',
			),
			// Add more fields schema here as needed.
		);
	}

	public static function sanitize_settings( $settings ) {
		$sanitized = array();
		$schema    = self::get_settings_schema();

		foreach ( $settings as $key => $value ) {
			if ( isset( $schema[ $key ] ) ) {
				$sanitize_callback = $schema[ $key ]['sanitize_callback'] ?? 'sanitize_text_field';

				if ( is_callable( $sanitize_callback ) ) {
					$sanitized[ $key ] = call_user_func( $sanitize_callback, $value );
				} else {
					$sanitized[ $key ] = sanitize_text_field( $value );
				}
			} else {
				$sanitized[ $key ] = sanitize_text_field( $value );
			}
		}

		return $sanitized;
	}
}
