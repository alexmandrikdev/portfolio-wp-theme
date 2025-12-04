<?php

namespace AMPortfolioTheme\Emails;

use AMPortfolioTheme\Helpers\Markdown_Helper;
use AMPortfolioTheme\Api\Zoho_Mail_Service;

defined( 'ABSPATH' ) || exit;

class Admin_Contact_Notification {

	private $submission_data;
	private $submission_id;
	private $data;

	const SCHEDULED_ACTION_HOOK = 'am_portfolio_send_admin_notification';

	public function __construct( $submission_data, $submission_id ) {
		$this->submission_data = $submission_data;
		$this->submission_id   = $submission_id;

		$this->initialize_data();
	}

	private function initialize_data() {
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

	private function get_admin_submission_url() {
		if ( ! function_exists( 'admin_url' ) ) {
			return '';
		}

		return admin_url( 'post.php?post=' . $this->submission_id . '&action=edit' );
	}

	public function render() {
		$template_path = get_theme_file_path( 'templates/emails/admin-contact-notification.php' );

		$data = $this->data;

		ob_start();
		include $template_path;
		return ob_get_clean();
	}

	public static function display( $submission_data, $submission_id, $attributes = array() ) {
		$notification = new self( $submission_data, $submission_id, $attributes );
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $notification->render();
	}

	public static function get( $submission_data, $submission_id, $attributes = array() ) {
		$notification = new self( $submission_data, $submission_id, $attributes );
		return $notification->render();
	}

	public static function schedule( $submission_id ) {
		$args = array(
			'submission_id' => $submission_id,
		);

		$args = apply_filters( 'am_portfolio_admin_notification_schedule_args', $args, $submission_id );

		$scheduled = wp_schedule_single_event(
			time(),
			self::SCHEDULED_ACTION_HOOK,
			array( $args )
		);

		if ( false === $scheduled ) {
			return self::send_immediately( $submission_id );
		}

		return true;
	}

	public static function handle_scheduled_send( $args ) {
		$submission_id = $args['submission_id'] ?? 0;

		if ( ! $submission_id ) {
			log_message( 'Invalid submission ID', 'Admin_Contact_Notification', 'warning' );
			return false;
		}

		return self::send_immediately( $submission_id );
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
			log_message( 'Could not load submission data for ID: ' . $submission_id, 'Admin_Contact_Notification', 'warning' );
			return false;
		}

		$settings = get_option( 'portfolio_theme_settings', array() );
		$to       = $settings['contact_email'] ?? get_option( 'admin_email' );

		if ( ! $to ) {
			log_message( 'No admin email address found', 'Admin_Contact_Notification', 'warning' );
			return false;
		}

		$subject = sprintf(
		/* translators: %s: email subject or name */
			__( 'New Contact Form Submission: %s', 'am-portfolio-theme' ),
			( ! empty( $submission_data['subject'] ) ? $submission_data['subject'] : $submission_data['name'] )
		);

		try {
			$message = self::get( $submission_data, $submission_id );

			$result = Zoho_Mail_Service::send_email( $to, $subject, $message );

			if ( ! $result ) {
				throw new \Exception( 'wp_mail returned false' );
			}

			log_message( 'Email sent successfully to ' . $to, 'Admin_Contact_Notification', 'info' );

			return true;

		} catch ( \Exception $e ) {
			log_message( 'Failed to send email - ' . $e->getMessage(), 'Admin_Contact_Notification', 'error' );

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
