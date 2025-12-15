<?php

namespace AMPortfolioTheme\Api;

defined( 'ABSPATH' ) || exit;

class Technology_Order_API {

	const REST_NAMESPACE = 'portfolio/v1';
	const REST_ROUTE     = '/technologies';

	/**
	 * Initialize the class.
	 *
	 * @return self
	 */
	public static function init() {
		$self = new self();
		add_action( 'rest_api_init', array( $self, 'register_rest_routes' ) );
		return $self;
	}

	/**
	 * Register REST routes.
	 */
	public function register_rest_routes() {
		register_rest_route(
			self::REST_NAMESPACE,
			self::REST_ROUTE,
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_technologies' ),
					'permission_callback' => array( $this, 'permission_check' ),
				),
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'update_order' ),
					'permission_callback' => array( $this, 'permission_check' ),
				),
			)
		);
	}

	/**
	 * Permission check for REST route.
	 *
	 * @param \WP_REST_Request $request The request.
	 * @return bool|\WP_Error
	 */
	public function permission_check( $request ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return new \WP_Error(
				'rest_forbidden',
				__( 'You do not have permission to manage technologies.', 'am-portfolio-theme' ),
				array( 'status' => 403 )
			);
		}

		$nonce = $request->get_header( 'X-WP-Nonce' );
		if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
			return new \WP_Error(
				'rest_invalid_nonce',
				__( 'Security check failed.', 'am-portfolio-theme' ),
				array( 'status' => 403 )
			);
		}

		return true;
	}

	/**
	 * Get all technologies with their order and icon data.
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function get_technologies() {
		$technologies = get_terms(
			array(
				'taxonomy'   => 'project_technology',
				'hide_empty' => false,
			)
		);

		if ( is_wp_error( $technologies ) ) {
			return new \WP_Error(
				'technologies_fetch_failed',
				__( 'Failed to fetch technologies.', 'am-portfolio-theme' ),
				array( 'status' => 500 )
			);
		}

		$formatted_technologies = array();
		foreach ( $technologies as $technology ) {
			$icon_id  = get_term_meta( $technology->term_id, 'technology_icon', true );
			$icon_url = $icon_id ? wp_get_attachment_image_url( $icon_id, 'thumbnail' ) : '';
			$order    = get_term_meta( $technology->term_id, 'technology_order', true );
			$order    = $order ? intval( $order ) : 0;

			$formatted_technologies[] = array(
				'id'          => $technology->term_id,
				'name'        => $technology->name,
				'description' => $technology->description,
				'slug'        => $technology->slug,
				'icon_url'    => $icon_url,
				'order'       => $order,
				'count'       => $technology->count,
			);
		}

		usort(
			$formatted_technologies,
			function ( $a, $b ) {
				return $a['order'] - $b['order'];
			}
		);

		return new \WP_REST_Response(
			array(
				'success'      => true,
				'technologies' => $formatted_technologies,
			)
		);
	}

	/**
	 * Update technology order.
	 *
	 * @param \WP_REST_Request $request The request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function update_order( $request ) {
		$params = $request->get_json_params();

		if ( ! isset( $params['technologies'] ) || ! is_array( $params['technologies'] ) ) {
			return new \WP_Error(
				'invalid_data',
				__( 'Invalid data provided.', 'am-portfolio-theme' ),
				array( 'status' => 400 )
			);
		}

		$technologies = $params['technologies'];
		$updated      = array();

		foreach ( $technologies as $index => $tech_data ) {
			if ( ! isset( $tech_data['id'] ) ) {
				continue;
			}

			$term_id = intval( $tech_data['id'] );
			$order   = intval( $index ); // Use index as order (0-based).

			// Verify term exists.
			$term = get_term( $term_id, 'project_technology' );
			if ( ! $term || is_wp_error( $term ) ) {
				continue;
			}

			// Update order.
			update_term_meta( $term_id, 'technology_order', $order );

			$updated[] = array(
				'id'    => $term_id,
				'order' => $order,
			);
		}

		return new \WP_REST_Response(
			array(
				'success' => true,
				'message' => __( 'Technology order updated successfully.', 'am-portfolio-theme' ),
				'updated' => $updated,
				'count'   => count( $updated ),
			)
		);
	}
}
