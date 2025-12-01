<?php

namespace AMPortfolioTheme\Managers;

use AMPortfolioTheme\Admin\Settings_Helper;
use AMPortfolioTheme\Helpers\Project_Permalink_Helper;

defined( 'ABSPATH' ) || exit;

class Project_Permalink_Manager {

	public static function init() {
		$self = new self();

		add_filter( 'post_type_link', array( $self, 'filter_project_permalink' ), 10, 3 );
		add_action( 'init', array( $self, 'add_project_rewrite_rules' ) );
	}

	public function filter_project_permalink( $permalink, $post, $leavename ) {
		if ( 'project' === $post->post_type ) {
			$language = function_exists( 'pll_get_post_language' ) ? pll_get_post_language( $post->ID ) : 'default';
			return Project_Permalink_Helper::get_instance()->get_project_permalink( $leavename ? '%postname%' : $post->post_name, $language );
		}
		return $permalink;
	}

	public function add_project_rewrite_rules() {
		$settings         = Settings_Helper::get_current_settings();
		$listing_page_ids = $settings['projects_listing_page_ids'] ?? array();

		$lang_code = function_exists( 'pll_default_language' ) ? pll_default_language() : 'default';
		// If polylang languages are not initialized yet, pll_default_language() may return false.
		if ( ! $lang_code || ! is_string( $lang_code ) ) {
			$lang_code = 'default';
		}

		// Ensure we have a listing page ID for this language.
		if ( ! isset( $listing_page_ids[ $lang_code ] ) || ! $listing_page_ids[ $lang_code ] ) {
			return;
		}

		$listing_page_id = $listing_page_ids[ $lang_code ];

		$listing_page_slug = get_post_field( 'post_name', $listing_page_id );
		if ( ! $listing_page_slug ) {
			return;
		}

		if ( 'default' === $lang_code || pll_default_language() === $lang_code ) {
			add_rewrite_rule(
				'^' . $listing_page_slug . '/([^/]+)/?$',
				'index.php?project=$matches[1]',
				'top'
			);
		} else {
			add_rewrite_rule(
				'^' . $lang_code . '/' . $listing_page_slug . '/([^/]+)/?$',
				'index.php?project=$matches[1]&lang=' . $lang_code,
				'top'
			);
		}
	}
}
