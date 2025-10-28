<?php

namespace AMPortfolioTheme\Admin;

defined( 'ABSPATH' ) || exit;

class Settings_API {

	const REST_NAMESPACE = 'portfolio/v1';

	const REST_ROUTE = '/settings/';

	public static function init() {
		$self = new self();

		add_action( 'rest_api_init', array( $self, 'register_rest_routes' ) );

		return $self;
	}

	public function register_rest_routes() {
		register_rest_route(
			self::REST_NAMESPACE,
			self::REST_ROUTE,
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_settings' ),
					'permission_callback' => array( $this, 'permission_check' ),
				),
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_settings' ),
					'permission_callback' => array( $this, 'permission_check' ),
					'args'                => Settings_Helper::get_settings_schema(),
				),
			)
		);
	}

	public function permission_check( $request ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return new \WP_Error(
				'rest_forbidden',
				__( 'You do not have permission to manage settings.', 'portfolio' ),
				array( 'status' => 403 )
			);
		}

		$nonce = $request->get_header( 'X-WP-Nonce' );
		if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
			return new \WP_Error(
				'rest_invalid_nonce',
				__( 'Security check failed.', 'portfolio' ),
				array( 'status' => 403 )
			);
		}

		return true;
	}

	public function get_settings() {
		$settings = Settings_Helper::get_current_settings();

		return rest_ensure_response(
			array(
				'success'  => true,
				'settings' => $settings,
			)
		);
	}

	public function update_settings( $request ) {
		$new_settings = $request->get_json_params();

		if ( ! is_array( $new_settings ) ) {
			return new \WP_Error(
				'rest_invalid_data',
				__( 'Invalid settings data.', 'portfolio' ),
				array( 'status' => 400 )
			);
		}

		$sanitized_settings = Settings_Helper::sanitize_settings( $new_settings );

		$current_settings = Settings_Helper::get_current_settings();
		$updated_settings = array_merge( $current_settings, $sanitized_settings );

		$result = update_option( Settings_Page::OPTION_NAME, $updated_settings );

		if ( $result ) {
			return rest_ensure_response(
				array(
					'success'  => true,
					'message'  => __( 'Settings saved successfully.', 'portfolio' ),
					'settings' => $updated_settings,
				)
			);
		} else {
			return new \WP_Error(
				'rest_save_failed',
				__( 'Failed to save settings.', 'portfolio' ),
				array( 'status' => 500 )
			);
		}
	}
}
