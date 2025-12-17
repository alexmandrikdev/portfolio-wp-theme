<?php

namespace AMPortfolioTheme\Emails;

use AMPortfolioTheme\Helpers\Markdown_Helper;
use AMPortfolioTheme\Api\Zoho_Mail_Service;

defined( 'ABSPATH' ) || exit;

class Sender_Confirmation_Email extends Base_Email_Notification {

	const SCHEDULED_ACTION_HOOK = 'am_portfolio_send_sender_confirmation';

	protected function initialize_data() {
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

	protected function get_template_path() {
		return get_theme_file_path( 'templates/emails/sender-confirmation.php' );
	}

	protected static function get_scheduled_action_hook() {
		return self::SCHEDULED_ACTION_HOOK;
	}

	protected static function before_schedule( $submission_id ) {
		// Initialize email status tracking.
		Email_Status_Tracker::initialize_status( $submission_id );
	}

	protected static function get_schedule_filter_name() {
		return 'am_portfolio_sender_confirmation_schedule_args';
	}

	public static function send_immediately( $submission_id ) {
		$submission_data = static::get_submission_data( $submission_id );
		if ( empty( $submission_data ) ) {
			log_message( 'Could not load submission data for ID: ' . $submission_id, static::class, 'warning' );
			Email_Status_Tracker::track_email_attempt( $submission_id, false, __( 'Could not load submission data', 'am-portfolio-theme' ) );
			return false;
		}

		$to = $submission_data['email'] ?? '';

		if ( ! $to ) {
			log_message( 'No recipient email address found', static::class, 'warning' );
			Email_Status_Tracker::track_email_attempt( $submission_id, false, __( 'No recipient email address found', 'am-portfolio-theme' ) );
			return false;
		}

		$language = $submission_data['language'];
		$subject  = pll_translate_string( 'Thank You for Reaching Out', $language );

		try {
			$message = static::get( $submission_data, $submission_id );

			$result = Zoho_Mail_Service::send_email( $to, $subject, $message );

			if ( ! $result ) {
				throw new \Exception( 'wp_mail returned false' );
			}

			log_message( 'Email sent successfully to ' . $to, static::class, 'info' );
			Email_Status_Tracker::track_email_attempt( $submission_id, true );

			return true;

		} catch ( \Exception $e ) {
			$error_message = $e->getMessage();
			log_message( 'Failed to send email - ' . $error_message, static::class, 'error' );
			Email_Status_Tracker::track_email_attempt( $submission_id, false, $error_message );

			if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
				wp_mail(
					get_option( 'admin_email' ),
					__( 'Contact Form Confirmation Email Delivery Failed', 'am-portfolio-theme' ),
					sprintf(
						/* translators: 1: recipient email, 2: error message */
						__( 'Failed to send contact form confirmation email to %1$s. Error: %2$s', 'am-portfolio-theme' ),
						$to,
						$error_message
					)
				);
			}

			return false;
		}
	}
}
