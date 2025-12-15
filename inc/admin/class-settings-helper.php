<?php

namespace AMPortfolioTheme\Admin;

use AMPortfolioTheme\Helpers\Crypto;

defined( 'ABSPATH' ) || exit;

class Settings_Helper {

	public static function get_default_settings() {
		return array(
			'projects_listing_page_ids' => array(),
			'contact_email'             => '',
			'github_url'                => '',
			'linkedin_url'              => '',
			'google_analytics_id'       => '',
			'zoho_access_token'         => '',
			'zoho_refresh_token'        => '',
			'zoho_token_expires'        => 0,
			'zoho_accounts_server'      => '',
			'zoho_account_id'           => '',
			'zoho_base_api_url'         => 'https://mail.zoho.com',
			// Add more default settings here as needed.
		);
	}

	public static function get_current_settings() {
		$settings = get_option( Settings_Page::OPTION_NAME, array() );
		$settings = wp_parse_args( $settings, self::get_default_settings() );

		$settings = self::decrypt_tokens( $settings );

		return $settings;
	}

	public static function get_settings_schema() {
		return array(
			'projects_listing_page_ids' => array(
				'description'          => __( 'Projects Listing Page IDs by language.', 'portfolio' ),
				'type'                 => 'object',
				'properties'           => array(), // Dynamically populated based on languages.
				'required'             => false,
				'sanitize_callback'    => array( self::class, 'sanitize_projects_listing_page_ids' ),
				'additionalProperties' => array(
					'type' => 'integer',
				),
			),
			'contact_email'             => array(
				'description'       => __( 'Contact email address for form submissions and alternative contact methods.', 'portfolio' ),
				'type'              => 'string',
				'required'          => false,
				'sanitize_callback' => 'sanitize_email',
			),
			'github_url'                => array(
				'description'       => __( 'GitHub profile URL for social media links.', 'portfolio' ),
				'type'              => 'string',
				'required'          => false,
				'sanitize_callback' => 'esc_url_raw',
			),
			'linkedin_url'              => array(
				'description'       => __( 'LinkedIn profile URL for social media links.', 'portfolio' ),
				'type'              => 'string',
				'required'          => false,
				'sanitize_callback' => 'esc_url_raw',
			),
			'google_analytics_id'       => array(
				'description'       => __( 'Google Analytics tracking ID for website analytics.', 'portfolio' ),
				'type'              => 'string',
				'required'          => false,
				'sanitize_callback' => 'sanitize_text_field',
			),
			'zoho_access_token'         => array(
				'description'       => __( 'Zoho Mail OAuth Access Token (automatically managed, stored encrypted).', 'portfolio' ),
				'type'              => 'string',
				'required'          => false,
				'sanitize_callback' => 'sanitize_text_field',
			),
			'zoho_refresh_token'        => array(
				'description'       => __( 'Zoho Mail OAuth Refresh Token (automatically managed, stored encrypted).', 'portfolio' ),
				'type'              => 'string',
				'required'          => false,
				'sanitize_callback' => 'sanitize_text_field',
			),
			'zoho_token_expires'        => array(
				'description'       => __( 'Zoho Mail OAuth Token Expiration Timestamp.', 'portfolio' ),
				'type'              => 'integer',
				'required'          => false,
				'sanitize_callback' => 'absint',
			),
			'zoho_accounts_server'      => array(
				'description'       => __( 'Zoho Accounts Server URL (e.g., https://accounts.zoho.eu).', 'portfolio' ),
				'type'              => 'string',
				'required'          => false,
				'sanitize_callback' => 'esc_url_raw',
			),
			'zoho_account_id'           => array(
				'description'       => __( 'Zoho Mail Account ID (found in Zoho Mail settings).', 'portfolio' ),
				'type'              => 'string',
				'required'          => false,
				'sanitize_callback' => 'sanitize_text_field',
			),
			'zoho_base_api_url'         => array(
				'description'       => __( 'Zoho Mail API base URL (e.g., https://mail.zoho.com).', 'portfolio' ),
				'type'              => 'string',
				'required'          => false,
				'sanitize_callback' => 'esc_url_raw',
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

	/**
	 * Update settings with new values.
	 *
	 * @param array $new_settings New settings to update (can be partial).
	 * @return array|bool If successful, returns the updated settings array; false on failure.
	 */
	public static function update_settings( $new_settings ) {

		$sanitized_settings = self::sanitize_settings( $new_settings );
		$current_settings   = self::get_current_settings();
		$updated_settings   = array_merge( $current_settings, $sanitized_settings );
		$updated_settings   = self::encrypt_tokens( $updated_settings );

		// Check if settings have actually changed.
		if ( $current_settings === $updated_settings ) {
			// No changes needed, return current settings as success.
			return $current_settings;
		}

		$result = update_option( Settings_Page::OPTION_NAME, $updated_settings );

		if ( $result ) {
			return $updated_settings;
		}

		// update_option returned false but settings were different.
		// This indicates an actual database failure.
		return false;
	}

	public static function sanitize_projects_listing_page_ids( $page_ids ) {
		$sanitized_page_ids = array();
		if ( is_array( $page_ids ) ) {
			foreach ( $page_ids as $lang_code => $page_id ) {
				$sanitized_page_ids[ sanitize_key( $lang_code ) ] = absint( $page_id );
			}
		}
		return $sanitized_page_ids;
	}

	public static function get_all_pages_by_language() {
		$pages_by_language = array();

		if ( function_exists( 'pll_languages_list' ) ) {
			$languages = pll_languages_list();
			foreach ( $languages as $lang_code ) {
				$args = array(
					'post_type'      => 'page',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'lang'           => $lang_code,
					'orderby'        => 'title',
					'order'          => 'ASC',
					'fields'         => 'ids', // Only get IDs for performance.
				);

				$page_ids = get_posts( $args );
				$pages    = array();
				foreach ( $page_ids as $page_id ) {
					$pages[] = array(
						'id'    => $page_id,
						'title' => get_the_title( $page_id ),
					);
				}
				$pages_by_language[ $lang_code ] = $pages;
			}
		} else {
			// Fallback for non-Polylang environments.
			$args     = array(
				'post_type'      => 'page',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
				'fields'         => 'ids',
			);
			$page_ids = get_posts( $args );
			$pages    = array();
			foreach ( $page_ids as $page_id ) {
				$pages[] = array(
					'id'    => $page_id,
					'title' => get_the_title( $page_id ),
				);
			}
			$pages_by_language['default'] = $pages; // Use 'default' key for single language.
		}

		return $pages_by_language;
	}

	public static function get_polylang_languages() {
		if ( function_exists( 'pll_languages_list' ) ) {
			$languages           = pll_the_languages( array( 'raw' => 1 ) );
			$formatted_languages = array();
			foreach ( $languages as $lang ) {
				$formatted_languages[] = array(
					'code' => $lang['slug'],
					'name' => $lang['name'],
				);
			}
			return $formatted_languages;
		}
		return array(
			array(
				'code' => 'default',
				'name' => __( 'Default Language', 'portfolio' ),
			),
		);
	}

	/**
	 * Decrypt tokens in settings array.
	 *
	 * @param array $settings Settings array.
	 * @return array Settings with decrypted tokens.
	 */
	private static function decrypt_tokens( $settings ) {
		if ( isset( $settings['zoho_access_token'] ) && ! empty( $settings['zoho_access_token'] ) ) {
			try {
				$settings['zoho_access_token'] = Crypto::decrypt( $settings['zoho_access_token'] );
			} catch ( \Exception $e ) {
				// If decryption fails, keep original.
				log_message(
					sprintf( 'Failed to decrypt access token: %s', $e->getMessage() ),
					'Settings_Helper',
					'error'
				);
			}
		}

		if ( isset( $settings['zoho_refresh_token'] ) && ! empty( $settings['zoho_refresh_token'] ) ) {
			try {
				$settings['zoho_refresh_token'] = Crypto::decrypt( $settings['zoho_refresh_token'] );
			} catch ( \Exception $e ) {
				// If decryption fails, keep original.
				log_message(
					sprintf( 'Failed to decrypt refresh token: %s', $e->getMessage() ),
					'Settings_Helper',
					'error'
				);
			}
		}

		return $settings;
	}

	/**
	 * Encrypt tokens in settings array.
	 *
	 * @param array $settings Settings array.
	 * @return array Settings with encrypted tokens.
	 */
	private static function encrypt_tokens( $settings ) {
		if ( isset( $settings['zoho_access_token'] ) && ! empty( $settings['zoho_access_token'] ) ) {
			try {
				$settings['zoho_access_token'] = Crypto::encrypt( $settings['zoho_access_token'] );
			} catch ( \Exception $e ) {
				// If encryption fails, keep original.
				log_message(
					sprintf( 'Failed to encrypt access token: %s', $e->getMessage() ),
					'Settings_Helper',
					'error'
				);
			}
		}

		if ( isset( $settings['zoho_refresh_token'] ) && ! empty( $settings['zoho_refresh_token'] ) ) {
			try {
				$settings['zoho_refresh_token'] = Crypto::encrypt( $settings['zoho_refresh_token'] );
			} catch ( \Exception $e ) {
				// If encryption fails, keep original.
				log_message(
					sprintf( 'Failed to encrypt refresh token: %s', $e->getMessage() ),
					'Settings_Helper',
					'error'
				);
			}
		}

		return $settings;
	}
}
