<?php
// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
// phpcs:disable WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared

namespace AMPortfolioTheme\Components;

use AMPortfolioTheme\Helpers\Media_Data_Loader;
use AMPortfolioTheme\Helpers\Project_Permalink_Helper;

defined( 'ABSPATH' ) || exit;

class Project_Card_Data_Loader {

	public static function load_project_cards_data(
		array $post_ids = array(),
		bool $load_types = false,
		int $limit = 0
	): array {
		$posts_data        = self::load_projects_base_data( $post_ids, $limit );
		$thumbnails_data   = self::load_projects_thumbnails_data( $post_ids );
		$technologies_data = self::load_projects_technologies_data( $post_ids );

		$types_data = array();
		if ( $load_types ) {
			$types_data = self::load_projects_types_data( $post_ids );
		}

		$result = array();
		foreach ( $posts_data as $post_id => $post_data ) {
			$language = $post_data->language_code ?? 'default';

			$result[ $post_id ] = new Project_Card_Data(
				$post_id,
				$post_data->post_title,
				$post_data->post_excerpt,
				Project_Permalink_Helper::get_instance()->get_project_permalink( $post_data->post_name, $language ),
				isset( $thumbnails_data[ $post_id ] ) ? $thumbnails_data[ $post_id ] : null,
				isset( $technologies_data[ $post_id ] ) ? $technologies_data[ $post_id ] : array(),
				isset( $types_data[ $post_id ] ) ? $types_data[ $post_id ] : array()
			);
		}

		return $result;
	}

	private static function load_projects_base_data(
		array $post_ids = array(),
		int $limit = 0
	): array {
		global $wpdb;

		$base_query = "SELECT p.ID, p.post_title, p.post_excerpt, p.post_name,
		                      t.slug as language_code
		               FROM {$wpdb->posts} p
		               LEFT JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
		               LEFT JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
		               LEFT JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
		               WHERE p.post_type = 'project' AND p.post_status = 'publish'
		               AND tt.taxonomy = 'language' AND t.slug = %s";

		$parameters = array( pll_current_language() );

		if ( ! empty( $post_ids ) ) {
			$post_ids_placeholders = implode( ',', array_fill( 0, count( $post_ids ), '%d' ) );
			$query                 = $base_query . " AND p.ID IN ($post_ids_placeholders)";
			$parameters            = array_merge( $parameters, $post_ids );
		} else {
			$query = $base_query;
		}

		$query = $wpdb->prepare( $query, $parameters );

		$query .= ' ORDER BY p.menu_order';

		if ( $limit > 0 ) {
			$query .= " LIMIT {$limit}";
		}

		$results = $wpdb->get_results( $query, OBJECT_K );

		return $results ? $results : array();
	}

	private static function load_projects_thumbnails_data( array $post_ids = array() ): array {
		global $wpdb;

		$base_query = "SELECT p.ID as post_id, pm.meta_value as thumbnail_id
		               FROM {$wpdb->posts} p
		               LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_thumbnail_id'
		               WHERE p.post_type = 'project' AND p.post_status = 'publish'";

		if ( ! empty( $post_ids ) ) {
			$post_ids_placeholders = implode( ',', array_fill( 0, count( $post_ids ), '%d' ) );
			$query                 = $base_query . " AND p.ID IN ($post_ids_placeholders)";
			$query                 = $wpdb->prepare( $query, $post_ids );
		} else {
			$query = $base_query;
		}

		$thumbnails = $wpdb->get_results( $query );

		$thumbnail_ids      = array();
		$post_thumbnail_map = array();

		foreach ( $thumbnails as $thumb ) {
			if ( $thumb->thumbnail_id ) {
				$thumbnail_id                          = intval( $thumb->thumbnail_id );
				$thumbnail_ids[]                       = $thumbnail_id;
				$post_thumbnail_map[ $thumb->post_id ] = $thumbnail_id;
			}
		}

		$media_data = array();
		if ( ! empty( $thumbnail_ids ) ) {
			$media_data = Media_Data_Loader::load_media_data_bulk( $thumbnail_ids );
		}

		$result = array();
		foreach ( $post_thumbnail_map as $post_id => $thumbnail_id ) {
			if ( isset( $media_data[ $thumbnail_id ] ) ) {
				$result[ $post_id ] = $media_data[ $thumbnail_id ];
			}
		}

		return $result;
	}

	private static function load_projects_taxonomy_data( array $post_ids, string $taxonomy ): array {
		global $wpdb;

		$base_query = "SELECT tr.object_id, t.term_id, t.name
	                 FROM {$wpdb->term_relationships} tr
	                 INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
	                 INNER JOIN {$wpdb->terms} t ON tt.term_id = t.term_id";

		if ( 'project_technology' === $taxonomy ) {
			$base_query .= " LEFT JOIN {$wpdb->termmeta} tm ON t.term_id = tm.term_id AND tm.meta_key = 'technology_order'";
		}

		$where_conditions = array( 'tt.taxonomy = %s' );
		$parameters       = array( $taxonomy );

		// For project_technology taxonomy, order by term_order then name, otherwise just by name.
		if ( 'project_technology' === $taxonomy ) {
			$order_by = 'ORDER BY tr.object_id, CAST(COALESCE(tm.meta_value, 0) AS UNSIGNED), t.name';
		} else {
			$order_by = 'ORDER BY tr.object_id, t.name';
		}

		if ( empty( $post_ids ) ) {
			$base_query        .= " INNER JOIN {$wpdb->posts} p ON tr.object_id = p.ID";
			$where_conditions[] = "p.post_type = 'project'";
			$where_conditions[] = "p.post_status = 'publish'";

			$query = $base_query . ' WHERE ' . implode( ' AND ', $where_conditions ) . ' ' . $order_by;
		} else {
			$post_ids_placeholders = implode( ',', array_fill( 0, count( $post_ids ), '%d' ) );
			$where_conditions[]    = "tr.object_id IN ($post_ids_placeholders)";
			$parameters            = array_merge( $parameters, $post_ids );

			$query = $base_query . ' WHERE ' . implode( ' AND ', $where_conditions ) . ' ' . $order_by;
		}

		$query = $wpdb->prepare( $query, $parameters );
		$terms = $wpdb->get_results( $query );

		$result = array();
		foreach ( $terms as $term ) {
			if ( ! isset( $result[ $term->object_id ] ) ) {
				$result[ $term->object_id ] = array();
			}
			$result[ $term->object_id ][ $term->term_id ] = array(
				'id'   => $term->term_id,
				'name' => $term->name,
			);
		}

		return $result;
	}

	private static function load_projects_technologies_data( array $post_ids = array() ): array {
		return self::load_projects_taxonomy_data( $post_ids, 'project_technology' );
	}

	private static function load_projects_types_data( array $post_ids = array() ): array {
		return self::load_projects_taxonomy_data( $post_ids, 'project_type' );
	}
}
