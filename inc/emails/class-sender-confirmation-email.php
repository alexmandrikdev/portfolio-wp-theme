<?php

namespace AMPortfolioTheme\Emails;

use AMPortfolioTheme\Helpers\Markdown_Helper;

defined( 'ABSPATH' ) || exit;

class Sender_Confirmation_Email {

	private $submission_data;
	private $submission_id;
	private $data;

	const SCHEDULED_ACTION_HOOK = 'am_portfolio_send_sender_confirmation';

	public function __construct( $submission_data, $submission_id ) {
		$this->submission_data = $submission_data;
		$this->submission_id   = $submission_id;

		$this->initialize_data();
	}

	private function initialize_data() {
		$current_timestamp    = time();
		$user_timezone_string = $this->submission_data['timezone'] ?? '';
		$user_timezone        = null;

		// Convert timezone string to DateTimeZone object if valid.
		if ( ! empty( $user_timezone_string ) ) {
			try {
				$user_timezone = new \DateTimeZone( $user_timezone_string );
			} catch ( \Exception $e ) {
				// Invalid timezone string, fall back to null (server timezone).
				$user_timezone = null;
			}
		}

		$date_format = pll_translate_string( get_option( 'date_format' ), $this->submission_data['language'] );
		$time_format = pll_translate_string( get_option( 'time_format' ), $this->submission_data['language'] );

		$this->data = array(
			'submission_id' => $this->submission_id,
			'name'          => $this->submission_data['name'] ?? '',
			'email'         => $this->submission_data['email'] ?? '',
			'subject'       => $this->submission_data['subject'] ?? '',
			'message'       => $this->submission_data['message'] ? Markdown_Helper::parse( $this->submission_data['message'] ) : '',
			'date'          => wp_date( $date_format, $current_timestamp, $user_timezone ),
			'time'          => wp_date( $time_format, $current_timestamp, $user_timezone ),
			'timezone'      => $user_timezone_string,
			'your_name'     => get_bloginfo( 'name' ),
			'portfolio_url' => home_url(),
			'language'      => $this->submission_data['language'],
		);
	}

	public function render() {
		$template_path = get_theme_file_path( 'templates/emails/sender-confirmation.php' );

		$data = $this->data;

		ob_start();
		include $template_path;
		return ob_get_clean();
	}

	public static function display( $submission_data, $submission_id ) {
		$email = new self( $submission_data, $submission_id );
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $email->render();
	}

	public static function get( $submission_data, $submission_id ) {
		$email = new self( $submission_data, $submission_id );
		return $email->render();
	}

	public static function schedule( $submission_id ) {
		if ( ! function_exists( 'as_enqueue_async_action' ) ) {
			return self::send_immediately( $submission_id );
		}

		$args = array(
			'submission_id' => $submission_id,
		);

		$args = apply_filters( 'am_portfolio_sender_confirmation_schedule_args', $args, $submission_id );

		as_enqueue_async_action(
			self::SCHEDULED_ACTION_HOOK,
			array( $args ),
			'am_portfolio_contact_emails'
		);

		return true;
	}

	public static function handle_scheduled_send( $args ) {
		$submission_id = $args['submission_id'] ?? 0;

		if ( ! $submission_id ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( 'Sender_Confirmation_Email: Invalid submission ID' );
			return false;
		}

		$result = self::send_immediately( $submission_id );

		return $result;
	}

	private static function get_submission_data( $submission_id ) {
		$language = get_post_meta( $submission_id, '_contant_submission_language', true );

		$submission_data = array(
			'name'     => get_post_meta( $submission_id, '_contact_submission_name', true ),
			'email'    => get_post_meta( $submission_id, '_contact_submission_email', true ),
			'subject'  => get_post_meta( $submission_id, '_contact_submission_subject', true ),
			'message'  => get_post_meta( $submission_id, '_contact_submission_message', true ),
			'timezone' => get_post_meta( $submission_id, '_contact_submission_timezone', true ),
			'language' => $language ? $language : pll_default_language(),
		);

		return $submission_data;
	}

	private static function send_immediately( $submission_id ) {
		$submission_data = self::get_submission_data( $submission_id );
		if ( empty( $submission_data ) ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( 'Sender_Confirmation_Email: Could not load submission data for ID: ' . $submission_id );
			return false;
		}

		$to = $submission_data['email'] ?? '';

		if ( ! $to ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( 'Sender_Confirmation_Email: No recipient email address found' );
			return false;
		}

		$language = $submission_data['language'];
		$subject  = pll_translate_string( 'Thank You for Reaching Out', $language );

		try {
			$message = self::get( $submission_data, $submission_id );

			$site_name = get_bloginfo( 'name' );
			$settings  = get_option( 'portfolio_theme_settings', array() );
			$from      = $settings['contact_email'] ?? get_option( 'admin_email' );
			$headers   = array(
				'Content-Type: text/html; charset=UTF-8',
				'From: ' . $site_name . ' <' . $from . '>',
				'Reply-To: ' . $site_name . ' <' . $from . '>',
			);

			$headers[] = 'X-Mailer: WordPress/' . get_bloginfo( 'version' );

			$result = wp_mail( $to, $subject, $message, $headers );

			if ( ! $result ) {
				throw new \Exception( 'wp_mail returned false' );
			}

			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				error_log( 'Sender_Confirmation_Email: Email sent successfully to ' . $to );
			}

			return true;

		} catch ( \Exception $e ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( 'Sender_Confirmation_Email: Failed to send email - ' . $e->getMessage() );

			if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
				wp_mail(
					get_option( 'admin_email' ),
					__( 'Contact Form Confirmation Email Delivery Failed', 'am-portfolio-theme' ),
					sprintf(
						/* translators: 1: recipient email, 2: error message */
						__( 'Failed to send contact form confirmation email to %1$s. Error: %2$s', 'am-portfolio-theme' ),
						$to,
						$e->getMessage()
					)
				);
			}

			return false;
		}
	}
}
