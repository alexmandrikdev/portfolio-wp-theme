<?php
// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared

namespace AMPortfolioTheme\Helpers;

defined( 'ABSPATH' ) || exit;

class Media_Data_Loader {

	public static function load_media_data_bulk( array $media_ids ): array {
		if ( empty( $media_ids ) ) {
			return array();
		}

		$combined_data = self::load_media_combined_data( $media_ids );

		$result = array();
		foreach ( $combined_data as $media_id => $data ) {
			$result[ $media_id ] = new Media_Data(
				$media_id,
				$data['post_mime_type'],
				$data['alt_text'],
				$data['file_path'],
				$data['sizes'],
				$data['metadata']
			);
		}

		return $result;
	}

	public static function load_media_data_single( int $media_id ): Media_Data {
		return self::load_media_data_bulk( array( $media_id ) )[ $media_id ];
	}

	private static function load_media_combined_data( array $media_ids ): array {
		global $wpdb;

		$media_ids_placeholders = implode( ',', array_fill( 0, count( $media_ids ), '%d' ) );

		$query = "SELECT p.ID, p.post_mime_type, 
		                 pm_alt.meta_value as alt_text,
		                 pm_file.meta_value as file_path,
		                 pm_meta.meta_value as attachment_metadata
		          FROM {$wpdb->posts} p
		          LEFT JOIN {$wpdb->postmeta} pm_alt ON p.ID = pm_alt.post_id AND pm_alt.meta_key = '_wp_attachment_image_alt'
		          LEFT JOIN {$wpdb->postmeta} pm_file ON p.ID = pm_file.post_id AND pm_file.meta_key = '_wp_attached_file'
		          LEFT JOIN {$wpdb->postmeta} pm_meta ON p.ID = pm_meta.post_id AND pm_meta.meta_key = '_wp_attachment_metadata'
		          WHERE p.ID IN ($media_ids_placeholders) AND p.post_type = 'attachment'";

		$query   = $wpdb->prepare( $query, $media_ids );
		$results = $wpdb->get_results( $query, OBJECT_K );

		$combined_data = array();
		$upload_dir    = wp_upload_dir();
		$base_path     = $upload_dir['basedir'];

		foreach ( $results as $media_id => $row ) {
			$metadata = maybe_unserialize( $row->attachment_metadata );

			$sizes_data = array();
			if ( isset( $metadata['sizes'] ) ) {
				foreach ( $metadata['sizes'] as $size_name => $size_data ) {
					$sizes_data[ $size_name ] = array(
						'url'    => self::generate_image_url(
							$row->file_path,
							$size_data['file'] ?? '',
							$upload_dir['baseurl']
						),
						'width'  => $size_data['width'] ?? 0,
						'height' => $size_data['height'] ?? 0,
					);
				}
			}

			if ( ! empty( $row->file_path ) ) {
				$sizes_data['full'] = array(
					'url'    => $upload_dir['baseurl'] . '/' . $row->file_path,
					'width'  => $metadata['width'] ?? 0,
					'height' => $metadata['height'] ?? 0,
				);
			}

			$file_path = '';
			if ( ! empty( $row->file_path ) ) {
				$file_path = $base_path . '/' . $row->file_path;
			}

			$combined_data[ $media_id ] = array(
				'post_mime_type' => $row->post_mime_type,
				'alt_text'       => $row->alt_text ?? '',
				'file_path'      => $file_path,
				'sizes'          => $sizes_data,
				'metadata'       => $metadata ? $metadata : array(),
			);
		}

		return $combined_data;
	}

	private static function generate_image_url(
		string $original_file_path,
		string $size_file_name,
		string $base_url
	): string {
		if ( empty( $size_file_name ) ) {
			return $base_url . '/' . $original_file_path;
		}

		$file_dir = dirname( $original_file_path );
		if ( '.' === $file_dir ) {
			return $base_url . '/' . $size_file_name;
		}

		return $base_url . '/' . $file_dir . '/' . $size_file_name;
	}
}
