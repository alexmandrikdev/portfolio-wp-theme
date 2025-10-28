<?php

namespace AMPortfolioTheme\AjaxHandlers;

use AMPortfolioTheme\Emails\Admin_Contact_Notification;
use AMPortfolioTheme\Emails\Sender_Confirmation_Email;

defined( 'ABSPATH' ) || exit;

class Contact_Form_Handler {

	public static function init() {
		$self = new self();

		add_action( 'wp_ajax_handle_contact_form', array( $self, 'handle_contact_submission' ) );
		add_action( 'wp_ajax_nopriv_handle_contact_form', array( $self, 'handle_contact_submission' ) );
	}

	public function handle_contact_submission() {
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- wp_verify_nonce handles validation
		$nonce = isset( $_POST['nonce'] ) ? wp_unslash( $_POST['nonce'] ) : '';
		if ( ! wp_verify_nonce( $nonce, 'am_contact_form_nonce' ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Security verification failed.', 'am-portfolio-theme' ),
				),
				403
			);
		}

		$recaptcha_errors = $this->validate_recaptcha();
		if ( ! empty( $recaptcha_errors ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Security verification failed.', 'am-portfolio-theme' ),
					'errors'  => $recaptcha_errors,
				),
				403
			);
		}

		$required_fields = array( 'name', 'email', 'message' );
		$errors          = array();

		foreach ( $required_fields as $field ) {
			if ( empty( $_POST[ $field ] ) ) {
				/* translators: %s: field name */
				$errors[ $field ] = sprintf( __( '%s field is required.', 'am-portfolio-theme' ), ucfirst( $field ) );
			}
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Will be sanitized with sanitize_email
		$email = isset( $_POST['email'] ) ? wp_unslash( $_POST['email'] ) : '';
		if ( ! empty( $email ) && ! is_email( $email ) ) {
			$errors['email'] = __( 'Please provide a valid email address.', 'am-portfolio-theme' );
		}

		if ( ! empty( $errors ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Please correct the following errors:', 'am-portfolio-theme' ),
					'errors'  => $errors,
				),
				400
			);
		}

		$submission_data = array(
			'name'    => isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '',
			'email'   => sanitize_email( wp_unslash( $email ) ),
			'subject' => isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '',
			'message' => isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '',
		);

		$post_data = array(
			'post_title'  => $this->generate_submission_title( $submission_data ),
			'post_type'   => 'contact_submission',
			'post_status' => 'publish',
			'meta_input'  => array(
				'_contact_submission_subject'    => $submission_data['subject'],
				'_contact_submission_name'       => $submission_data['name'],
				'_contact_submission_email'      => $submission_data['email'],
				'_contact_submission_message'    => $submission_data['message'],
				'_contact_submission_ip'         => $this->get_client_ip(),
				'_contact_submission_user_agent' => $this->get_user_agent(),
			),
		);

		try {
			$post_id = wp_insert_post( $post_data, true );

			if ( is_wp_error( $post_id ) ) {
				throw new \Exception( $post_id->get_error_message() );
			}

			Admin_Contact_Notification::schedule( $submission_data, $post_id );
			Sender_Confirmation_Email::schedule( $submission_data, $post_id );

			wp_send_json_success(
				array(
					'message' => __( 'Thank you for your message! We will get back to you soon.', 'am-portfolio-theme' ),
					'post_id' => $post_id,
				)
			);

		} catch ( \Exception $e ) {
			wp_send_json_error(
				array(
					'message' => __( 'Sorry, there was an error submitting your form. Please try again.', 'am-portfolio-theme' ),
					'debug'   => ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? $e->getMessage() : '',
				),
				500
			);
		}
	}

	private function validate_recaptcha() {
		$errors = array();

		$settings             = get_option( 'portfolio_theme_settings', array() );
		$recaptcha_secret_key = $settings['recaptcha_secret_key'] ?? '';

		if ( empty( $recaptcha_secret_key ) ) {
			return $errors;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce already verified in handle_contact_submission()
		$recaptcha_token = isset( $_POST['recaptcha_token'] ) ? sanitize_text_field( wp_unslash( $_POST['recaptcha_token'] ) ) : '';

		if ( empty( $recaptcha_token ) ) {
			$errors['recaptcha'] = __( 'reCAPTCHA token is missing.', 'am-portfolio-theme' );
			return $errors;
		}

		$verification_url  = 'https://www.google.com/recaptcha/api/siteverify';
		$verification_data = array(
			'body' => array(
				'secret'   => $recaptcha_secret_key,
				'response' => $recaptcha_token,
				'remoteip' => $this->get_client_ip(),
			),
		);

		$verification_response = wp_remote_post( $verification_url, $verification_data );

		if ( is_wp_error( $verification_response ) ) {
			$errors['recaptcha'] = __( 'Failed to verify reCAPTCHA. Please try again.', 'am-portfolio-theme' );
			return $errors;
		}

		$verification_body   = wp_remote_retrieve_body( $verification_response );
		$verification_result = json_decode( $verification_body, true );

		if ( ! $verification_result['success'] ) {
			$errors['recaptcha'] = __( 'reCAPTCHA verification failed. Please try again.', 'am-portfolio-theme' );
			return $errors;
		}

		$score_threshold = apply_filters( 'am_portfolio_recaptcha_score_threshold', 0.5 );
		if ( isset( $verification_result['score'] ) && $verification_result['score'] < $score_threshold ) {
			$errors['recaptcha'] = __( 'reCAPTCHA verification failed. Please try again.', 'am-portfolio-theme' );
			return $errors;
		}

		return $errors;
	}

	private function generate_submission_title( $data ) {
		$title = $data['name'];

		if ( ! empty( $data['subject'] ) ) {
			$title .= ' - ' . $data['subject'];
		}

		$title .= ' - ' . current_time( 'Y-m-d H:i:s' );

		return $title;
	}

	private function get_client_ip() {
		$ip_keys = array(
			'HTTP_X_REAL_IP',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_CLIENT_IP',
			'REMOTE_ADDR',
		);

		foreach ( $ip_keys as $key ) {
			if ( ! empty( $_SERVER[ $key ] ) ) {
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Will be sanitized with sanitize_text_field
				$ip = wp_unslash( $_SERVER[ $key ] );
				if ( strpos( $ip, ',' ) !== false ) {
					$ips = explode( ',', $ip );
					$ip  = trim( $ips[0] );
				}
				if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ) {
					return sanitize_text_field( $ip );
				}
			}
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Will be sanitized with sanitize_text_field
		$remote_addr = isset( $_SERVER['REMOTE_ADDR'] ) ? wp_unslash( $_SERVER['REMOTE_ADDR'] ) : '0.0.0.0';
		return sanitize_text_field( $remote_addr );
	}

	private function get_user_agent() {
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Will be sanitized with sanitize_text_field
		$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) : '';
		return sanitize_text_field( $user_agent );
	}
}
