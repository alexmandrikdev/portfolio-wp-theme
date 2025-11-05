<?php

namespace AMPortfolioTheme\Helpers;

use AMPortfolioTheme\Admin\Settings_Helper;

defined( 'ABSPATH' ) || exit;

class Project_Permalink_Helper {

	private static $instance = null;
	private $page_slugs      = array();

	public static function get_instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function get_project_permalink( $post_slug, $language ) {
		$listing_page_slug = $this->get_listing_page_slug( $language );

		$path = '';

		if ( ! $listing_page_slug ) {
			$path .= 'project/' . $post_slug . '/';
		} else {
			$path .= $listing_page_slug . '/' . $post_slug . '/';
		}

		return home_url( $path );
	}

	private function get_listing_page_slug( $language ) {
		if ( ! isset( $this->page_slugs[ $language ] ) ) {
			$settings = Settings_Helper::get_current_settings();

			$page_id = $settings['projects_listing_page_ids'][ $language ] ?? null;

			if ( ! $page_id && 'default' !== $language ) {
				$page_id = $settings['projects_listing_page_ids']['default'] ?? null;
			}

			if ( $page_id ) {
				$this->page_slugs[ $language ] = get_post_field( 'post_name', $page_id );
			} else {
				$this->page_slugs[ $language ] = null;
			}
		}

		return $this->page_slugs[ $language ];
	}
}
