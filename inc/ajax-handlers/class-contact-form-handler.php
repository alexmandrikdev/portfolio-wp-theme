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
		$validation_result = $this->validate_submission();
		if ( is_wp_error( $validation_result ) ) {
			$error_data  = $validation_result->get_error_data();
			$status_code = isset( $error_data['status'] ) ? $error_data['status'] : 400;
			wp_send_json_error(
				array(
					'message' => $validation_result->get_error_message(),
					'errors'  => isset( $error_data['errors'] ) ? $error_data['errors'] : array(),
				),
				$status_code
			);
		}

		$submission_data = $this->prepare_submission_data();

		try {
			$post_id = $this->create_submission_post( $submission_data );
			$this->schedule_notification_emails( $post_id );

			wp_send_json_success(
				array(
					'post_id' => $post_id,
				)
			);

		} catch ( \Exception $e ) {
			log_message(
				sprintf(
					'[Contact Form] Exception during submission: %s, IP: %s, User Agent: %s',
					$e->getMessage(),
					$this->get_client_ip(),
					$this->get_user_agent()
				),
				'Contact_Form_Handler',
				'error'
			);
			wp_send_json_error(
				array(
					'message' => pll__( 'Sorry, there was an error submitting your form. Please try again.' ),
					'debug'   => ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? $e->getMessage() : '',
				),
				500
			);
		}
	}

	/**
	 * Validate the contact form submission.
	 *
	 * @return \WP_Error|true Returns WP_Error on validation failure, true on success.
	 */
	private function validate_submission() {
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- wp_verify_nonce handles validation
		$nonce = isset( $_POST['nonce'] ) ? wp_unslash( $_POST['nonce'] ) : '';
		if ( ! wp_verify_nonce( $nonce, 'am_contact_form_nonce' ) ) {
			log_message(
				sprintf(
					'[Contact Form] Nonce verification failed. Nonce: %s, IP: %s, User Agent: %s',
					$nonce ? substr( $nonce, 0, 8 ) . '...' : '(empty)',
					$this->get_client_ip(),
					$this->get_user_agent()
				),
				'Contact_Form_Handler',
				'warning'
			);
			return new \WP_Error(
				'security_failed',
				pll__( 'Security verification failed.' ),
				array( 'status' => 403 )
			);
		}

		$recaptcha_errors = $this->validate_recaptcha();
		if ( ! empty( $recaptcha_errors ) ) {
			log_message(
				sprintf(
					'[Contact Form] reCAPTCHA validation failed. Errors: %s, IP: %s',
					wp_json_encode( $recaptcha_errors ),
					$this->get_client_ip()
				),
				'Contact_Form_Handler',
				'warning'
			);
			return new \WP_Error(
				'recaptcha_failed',
				pll__( 'Security verification failed.' ),
				array(
					'status' => 403,
					'errors' => $recaptcha_errors,
				)
			);
		}

		$required_fields = array( 'name', 'email', 'message' );
		$errors          = array();

		foreach ( $required_fields as $field ) {
			if ( empty( $_POST[ $field ] ) ) {
				/* translators: %s: field name */
				switch ( $field ) {
					case 'name':
						$errors[ $field ] = pll__( 'Full name is required' );
						break;
					case 'email':
						$errors[ $field ] = pll__( 'Please enter a valid email address' );
						break;
					case 'message':
						$errors[ $field ] = pll__( 'Message is required' );
						break;
				}
			}
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Will be sanitized with sanitize_email
		$email = isset( $_POST['email'] ) ? wp_unslash( $_POST['email'] ) : '';
		if ( ! empty( $email ) && ! is_email( $email ) ) {
			$errors['email'] = pll__( 'Please enter a valid email address' );
		}

		if ( ! empty( $errors ) ) {
			log_message(
				sprintf(
					'[Contact Form] Field validation failed. Errors: %s, Submitted data: name=%s, email=%s, subject=%s',
					wp_json_encode( $errors ),
					isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '(empty)',
					isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '(empty)',
					isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '(empty)'
				),
				'Contact_Form_Handler',
				'warning'
			);
			return new \WP_Error(
				'validation_failed',
				pll__( 'Please review and correct the highlighted fields' ),
				array(
					'status' => 400,
					'errors' => $errors,
				)
			);
		}

		return true;
	}

	/**
	 * Prepare sanitized submission data from POST.
	 *
	 * @return array Sanitized submission data.
	 */
	private function prepare_submission_data() {
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce already verified in validate_submission()
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Already validated in validate_submission()
		$email = isset( $_POST['email'] ) ? wp_unslash( $_POST['email'] ) : '';
		return array(
			'name'     => isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '',
			'email'    => sanitize_email( wp_unslash( $email ) ),
			'subject'  => isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '',
			'message'  => isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '',
			'timezone' => isset( $_POST['timezone'] ) ? $this->sanitize_timezone( sanitize_text_field( wp_unslash( $_POST['timezone'] ) ) ) : '',
		);
		// phpcs:enable WordPress.Security.NonceVerification.Missing
	}

	/**
	 * Create a contact submission post from sanitized data.
	 *
	 * @param array $submission_data Sanitized submission data.
	 * @return int Post ID.
	 * @throws \Exception If post creation fails.
	 */
	private function create_submission_post( $submission_data ) {
		$current_language = '';
		if ( function_exists( 'pll_current_language' ) ) {
			$current_language = pll_current_language();
		}

		$post_data = array(
			'post_title'  => $this->generate_submission_title( $submission_data ),
			'post_type'   => 'contact_submission',
			'post_status' => 'publish',
			'meta_input'  => array(
				'_contact_submission_subject'    => $submission_data['subject'],
				'_contact_submission_name'       => $submission_data['name'],
				'_contact_submission_email'      => $submission_data['email'],
				'_contact_submission_message'    => $submission_data['message'],
				'_contact_submission_timezone'   => $submission_data['timezone'],
				'_contact_submission_ip'         => $this->get_client_ip(),
				'_contact_submission_user_agent' => $this->get_user_agent(),
				'_contact_submission_language'   => $current_language,
			),
		);

		$post_id = wp_insert_post( $post_data, true );
		if ( is_wp_error( $post_id ) ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- Internal exception, not output to user.
			throw new \Exception( $post_id->get_error_message() );
		}

		return $post_id;
	}

	/**
	 * Schedule notification emails for a submission.
	 *
	 * @param int $post_id Post ID of the contact submission.
	 * @return void
	 */
	private function schedule_notification_emails( $post_id ) {
		Admin_Contact_Notification::schedule( $post_id );
		Sender_Confirmation_Email::schedule( $post_id );
	}

	private function validate_recaptcha() {
		$errors = array();

		$recaptcha_secret_key = defined( 'PORTFOLIO_RECAPTCHA_SECRET_KEY' ) ? \PORTFOLIO_RECAPTCHA_SECRET_KEY : '';

		if ( empty( $recaptcha_secret_key ) ) {
			return $errors;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce already verified in handle_contact_submission()
		$recaptcha_token = isset( $_POST['recaptcha_token'] ) ? sanitize_text_field( wp_unslash( $_POST['recaptcha_token'] ) ) : '';

		if ( empty( $recaptcha_token ) ) {
			$errors['recaptcha'] = pll__( 'Failed to verify reCAPTCHA. Please try again.' );
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
			$errors['recaptcha'] = pll__( 'Failed to verify reCAPTCHA. Please try again.' );
			return $errors;
		}

		$verification_body   = wp_remote_retrieve_body( $verification_response );
		$verification_result = json_decode( $verification_body, true );

		if ( ! $verification_result['success'] ) {
			$errors['recaptcha'] = pll__( 'Failed to verify reCAPTCHA. Please try again.' );
			return $errors;
		}

		$score_threshold = apply_filters( 'am_portfolio_recaptcha_score_threshold', 0.5 );
		if ( isset( $verification_result['score'] ) && $verification_result['score'] < $score_threshold ) {
			$errors['recaptcha'] = pll__( 'Failed to verify reCAPTCHA. Please try again.' );
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
		// Check Cloudflare header first if present.
		if ( ! empty( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Will be sanitized with sanitize_text_field
			$ip = wp_unslash( $_SERVER['HTTP_CF_CONNECTING_IP'] );
			if ( $this->is_valid_public_ip( $ip ) ) {
				return sanitize_text_field( $ip );
			}
		}

		// Check other headers in order of preference.
		$ip_keys = array(
			'HTTP_X_REAL_IP',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_CLIENT_IP',
		);

		foreach ( $ip_keys as $key ) {
			if ( ! empty( $_SERVER[ $key ] ) ) {
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Will be sanitized with sanitize_text_field
				$ip_value = wp_unslash( $_SERVER[ $key ] );
				$ip       = $this->extract_valid_ip_from_header( $ip_value, $key );
				if ( false !== $ip ) {
					return sanitize_text_field( $ip );
				}
			}
		}

		// Fallback to REMOTE_ADDR.
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Will be sanitized with sanitize_text_field
		$remote_addr = isset( $_SERVER['REMOTE_ADDR'] ) ? wp_unslash( $_SERVER['REMOTE_ADDR'] ) : '0.0.0.0';
		return sanitize_text_field( $remote_addr );
	}

	/**
	 * Extract a valid IP address from a header value.
	 *
	 * For X-Forwarded-For headers with multiple IPs (client, proxy1, proxy2...),
	 * check all IPs in the chain to find the first valid public IP.
	 *
	 * @param string $ip_value The raw header value.
	 * @param string $header_name The header name (for logging/debugging).
	 * @return string|false The valid IP address, or false if none found.
	 */
	private function extract_valid_ip_from_header( $ip_value, $header_name ) {
		// Split by commas if multiple IPs.
		$ip_candidates = array_map( 'trim', explode( ',', $ip_value ) );

		// For X-Forwarded-For, the client IP is usually the first one,
		// but proxies might prepend their IPs. We'll check all candidates.
		foreach ( $ip_candidates as $candidate ) {
			if ( $this->is_valid_public_ip( $candidate ) ) {
				return $candidate;
			}
		}

		// If no public IP found but we have at least one valid IP (even private),
		// return the first valid IP for X-Forwarded-For (this handles trusted proxy scenarios).
		if ( 'HTTP_X_FORWARDED_FOR' === $header_name && ! empty( $ip_candidates[0] ) ) {
			$first_ip = $ip_candidates[0];
			if ( filter_var( $first_ip, FILTER_VALIDATE_IP ) ) {
				return $first_ip;
			}
		}

		return false;
	}

	/**
	 * Check if an IP address is a valid public IP (not private or reserved).
	 *
	 * @param string $ip The IP address to check.
	 * @return bool True if valid public IP, false otherwise.
	 */
	private function is_valid_public_ip( $ip ) {
		return filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) !== false;
	}

	private function get_user_agent() {
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Will be sanitized with sanitize_text_field
		$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) : '';
		return sanitize_text_field( $user_agent );
	}

	/**
	 * Sanitize and validate timezone string.
	 *
	 * @param string $timezone Timezone string to validate.
	 * @return string Valid timezone string or empty string if invalid.
	 */
	private function sanitize_timezone( $timezone ) {
		if ( empty( $timezone ) ) {
			return '';
		}

		// Check if it's a valid timezone identifier.
		if ( in_array( $timezone, timezone_identifiers_list(), true ) ) {
			return $timezone;
		}

		// If not a valid timezone identifier, return empty string.
		return '';
	}
}
