<?php

namespace AMPortfolioTheme\Emails;

use AMPortfolioTheme\Helpers\Markdown_Helper;
use AMPortfolioTheme\Api\Zoho_Mail_Service;

defined( 'ABSPATH' ) || exit;

class Admin_Contact_Notification extends Base_Email_Notification {

	const SCHEDULED_ACTION_HOOK = 'am_portfolio_send_admin_notification';

	protected function initialize_data() {
		$current_timestamp = time();

		$this->data = array(
			'submission_id' => $this->submission_id,
			'name'          => $this->submission_data['name'] ?? '',
			'email'         => $this->submission_data['email'] ?? '',
			'subject'       => $this->submission_data['subject'] ?? '',
			'message'       => $this->submission_data['message'] ? Markdown_Helper::parse( $this->submission_data['message'] ) : '',
			'date'          => date_i18n( get_option( 'date_format' ), $current_timestamp ),
			'time'          => date_i18n( get_option( 'time_format' ), $current_timestamp ),
			'timezone'      => $this->submission_data['timezone'] ?? '',
			'admin_url'     => $this->get_admin_submission_url(),
			'language'      => $this->submission_data['language'] ?? '',
		);
	}

	protected function get_template_path() {
		return get_theme_file_path( 'templates/emails/admin-contact-notification.php' );
	}

	protected static function get_scheduled_action_hook() {
		return self::SCHEDULED_ACTION_HOOK;
	}

	private function get_admin_submission_url() {
		if ( ! function_exists( 'admin_url' ) ) {
			return '';
		}

		return admin_url( 'post.php?post=' . $this->submission_id . '&action=edit' );
	}


	protected static function before_schedule( $submission_id ) {
		// No special handling needed.
	}

	protected static function get_schedule_filter_name() {
		return 'am_portfolio_admin_notification_schedule_args';
	}

	public static function send_immediately( $submission_id ) {
		$submission_data = static::get_submission_data( $submission_id );
		if ( empty( $submission_data ) ) {
			log_message( 'Could not load submission data for ID: ' . $submission_id, static::class, 'warning' );
			return false;
		}

		$settings = get_option( 'portfolio_theme_settings', array() );
		$to       = $settings['contact_email'] ?? get_option( 'admin_email' );

		if ( ! $to ) {
			log_message( 'No admin email address found', static::class, 'warning' );
			return false;
		}

		$subject = sprintf(
			/* translators: %s: email subject or name */
			__( 'New Contact Form Submission: %s', 'am-portfolio-theme' ),
			( ! empty( $submission_data['subject'] ) ? $submission_data['subject'] : $submission_data['name'] )
		);

		try {
			$message = static::get( $submission_data, $submission_id );

			$result = Zoho_Mail_Service::send_email( $to, $subject, $message );

			if ( ! $result ) {
				throw new \Exception( 'wp_mail returned false' );
			}

			log_message( 'Email sent successfully to ' . $to, static::class, 'info' );

			return true;

		} catch ( \Exception $e ) {
			log_message( 'Failed to send email - ' . $e->getMessage(), static::class, 'error' );

			if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
				wp_mail(
					get_option( 'admin_email' ),
					__( 'Contact Form Email Delivery Failed', 'am-portfolio-theme' ),
					sprintf(
						/* translators: %s: error message */
						__( 'Failed to send contact form notification email. Error: %s', 'am-portfolio-theme' ),
						$e->getMessage()
					)
				);
			}

			return false;
		}
	}
}
