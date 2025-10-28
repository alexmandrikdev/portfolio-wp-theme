<?php
// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
// phpcs:disable WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared

namespace AMPortfolioTheme\Components;

defined( 'ABSPATH' ) || exit;

class Project_Card_Data_Loader {

	public static function load_project_cards_data( array $post_ids = array() ): array {
		$posts_data        = self::load_projects_base_data( $post_ids );
		$thumbnails_data   = self::load_projects_thumbnails_data( $post_ids );
		$technologies_data = self::load_projects_technologies_data( $post_ids );

		$result = array();
		foreach ( $posts_data as $post_id => $post_data ) {
			$result[ $post_id ] = new Project_Card_Data(
				$post_id,
				$post_data->post_title,
				$post_data->post_excerpt,
				get_permalink( $post_id ),
				isset( $thumbnails_data[ $post_id ] ) ? $thumbnails_data[ $post_id ] : array(
					'url' => '',
					'alt' => '',
				),
				isset( $technologies_data[ $post_id ] ) ? $technologies_data[ $post_id ] : array()
			);
		}

		return $result;
	}

	private static function load_projects_base_data( array $post_ids = array() ): array {
		global $wpdb;

		$base_query = "SELECT ID, post_title, post_excerpt, post_name 
		               FROM {$wpdb->posts} 
		               WHERE post_type = 'project' AND post_status = 'publish'";

		if ( ! empty( $post_ids ) ) {
			$post_ids_placeholders = implode( ',', array_fill( 0, count( $post_ids ), '%d' ) );
			$query                 = $base_query . " AND ID IN ($post_ids_placeholders)";
			$query                 = $wpdb->prepare( $query, $post_ids );
		} else {
			$query = $base_query;
		}

		$results = $wpdb->get_results( $query, OBJECT_K );

		return $results ? $results : array();
	}

	private static function load_projects_thumbnails_data( array $post_ids = array() ): array {
		global $wpdb;

		$base_query = "SELECT p.ID as post_id, pm.meta_value as thumbnail_id, 
		                      att_meta.meta_value as alt_text
		               FROM {$wpdb->posts} p
		               LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_thumbnail_id'
		               LEFT JOIN {$wpdb->postmeta} att_meta ON pm.meta_value = att_meta.post_id AND att_meta.meta_key = '_wp_attachment_image_alt'
		               WHERE p.post_type = 'project' AND p.post_status = 'publish'";

		if ( ! empty( $post_ids ) ) {
			$post_ids_placeholders = implode( ',', array_fill( 0, count( $post_ids ), '%d' ) );
			$query                 = $base_query . " AND p.ID IN ($post_ids_placeholders)";
			$query                 = $wpdb->prepare( $query, $post_ids );
		} else {
			$query = $base_query;
		}

		$thumbnails = $wpdb->get_results( $query );

		$result = array();
		foreach ( $thumbnails as $thumb ) {
			$thumbnail_url = '';
			if ( $thumb->thumbnail_id ) {
				$thumbnail_url = wp_get_attachment_image_url( $thumb->thumbnail_id, 'medium' );
			}

			$result[ $thumb->post_id ] = array(
				'url' => $thumbnail_url ? $thumbnail_url : '',
				'alt' => $thumb->alt_text ? $thumb->alt_text : '',
			);
		}

		return $result;
	}

	private static function load_projects_technologies_data( array $post_ids = array() ): array {
		global $wpdb;

		$base_query = "SELECT tr.object_id, t.term_id, t.name
                  FROM {$wpdb->term_relationships} tr
                  INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                  INNER JOIN {$wpdb->terms} t ON tt.term_id = t.term_id";

		$where_conditions = array( "tt.taxonomy = 'project_technology'" );
		$order_by         = 'ORDER BY tr.object_id, t.name';

		if ( empty( $post_ids ) ) {
			$base_query        .= " INNER JOIN {$wpdb->posts} p ON tr.object_id = p.ID";
			$where_conditions[] = "p.post_type = 'project'";
			$where_conditions[] = "p.post_status = 'publish'";

			$query = $base_query . ' WHERE ' . implode( ' AND ', $where_conditions ) . ' ' . $order_by;
			$terms = $wpdb->get_results( $query );
		} else {
			$post_ids_placeholders = implode( ',', array_fill( 0, count( $post_ids ), '%d' ) );
			$where_conditions[]    = "tr.object_id IN ($post_ids_placeholders)";

			$query = $base_query . ' WHERE ' . implode( ' AND ', $where_conditions ) . ' ' . $order_by;
			$query = $wpdb->prepare( $query, $post_ids );
			$terms = $wpdb->get_results( $query );
		}

		$result = array();
		foreach ( $terms as $term ) {
			if ( ! isset( $result[ $term->object_id ] ) ) {
				$result[ $term->object_id ] = array();
			}
			$result[ $term->object_id ][] = array(
				'id'   => $term->term_id,
				'name' => $term->name,
			);
		}

		return $result;
	}
}
