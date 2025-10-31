<?php

namespace AMPortfolioTheme\Admin;

defined( 'ABSPATH' ) || exit;

class Settings_Helper {

	public static function get_default_settings() {
		return array(
			'recaptcha_site_key'        => '',
			'recaptcha_secret_key'      => '',
			'projects_listing_page_ids' => array(),
			// Add more default settings here as needed.
		);
	}

	public static function get_current_settings() {
		$settings = get_option( Settings_Page::OPTION_NAME, array() );
		return wp_parse_args( $settings, self::get_default_settings() );
	}

	public static function get_settings_schema() {
		return array(
			'recaptcha_site_key'        => array(
				'description'       => __( 'Google reCAPTCHA site key for frontend integration.', 'portfolio' ),
				'type'              => 'string',
				'required'          => false,
				'sanitize_callback' => 'sanitize_text_field',
			),
			'recaptcha_secret_key'      => array(
				'description'       => __( 'Google reCAPTCHA secret key for server-side verification.', 'portfolio' ),
				'type'              => 'string',
				'required'          => false,
				'sanitize_callback' => 'sanitize_text_field',
			),
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
}
