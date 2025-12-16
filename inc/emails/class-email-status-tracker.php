<?php

namespace AMPortfolioTheme\Emails;

defined( 'ABSPATH' ) || exit;

class Email_Status_Tracker {

	// Status constants.
	const STATUS_PENDING  = 'pending';
	const STATUS_SENT     = 'sent';
	const STATUS_FAILED   = 'failed';
	const STATUS_RETRYING = 'retrying';

	// Retry configuration.
	const MAX_ATTEMPTS = 5;
	const RETRY_DELAYS = array( 300, 900, 3600, 10800, 32400 ); // 5min, 15min, 1hr, 3hr, 9hr in seconds.
	// const RETRY_DELAYS = array( 10, 20, 30, 40, 50 );

	// Meta field names.
	const META_STATUS       = '_sender_email_status';
	const META_ATTEMPTS     = '_sender_email_attempts';
	const META_LAST_ATTEMPT = '_sender_email_last_attempt';
	const META_NEXT_RETRY   = '_sender_email_next_retry';
	const META_ERROR        = '_sender_email_error';
	const META_RETRY_ACTION = '_sender_email_retry_action';

	// Action hook for retries.
	const RETRY_ACTION_HOOK = 'am_portfolio_sender_email_retry';

	/**
	 * Initialize the tracker.
	 */
	public static function init() {
		$self = new self();

		// Register retry action hook.
		add_action( self::RETRY_ACTION_HOOK, array( $self, 'handle_retry' ), 10, 1 );

		return $self;
	}

	/**
	 * Track an email sending attempt.
	 *
	 * @param int    $submission_id Contact submission post ID.
	 * @param bool   $success Whether the email was sent successfully.
	 * @param string $error_message Error message if failed.
	 * @return bool True on success, false on failure.
	 */
	public static function track_email_attempt( $submission_id, $success, $error_message = '' ) {
		if ( ! $submission_id ) {
			return false;
		}

		$current_time = time();
		$attempts     = self::get_attempts( $submission_id ) + 1;

		// Update common fields.
		update_post_meta( $submission_id, self::META_ATTEMPTS, $attempts );
		update_post_meta( $submission_id, self::META_LAST_ATTEMPT, $current_time );

		if ( $success ) {
			// Email sent successfully.
			update_post_meta( $submission_id, self::META_STATUS, self::STATUS_SENT );
			update_post_meta( $submission_id, self::META_ERROR, '' );
			update_post_meta( $submission_id, self::META_NEXT_RETRY, 0 );

			// Clean up any scheduled retry.
			self::unschedule_retry( $submission_id );

			log_message( sprintf( 'Email sent successfully for submission %d (attempt %d)', $submission_id, $attempts ), 'Email_Status_Tracker', 'info' );
			return true;
		} else {
			// Email failed.
			update_post_meta( $submission_id, self::META_STATUS, self::STATUS_FAILED );
			update_post_meta( $submission_id, self::META_ERROR, sanitize_text_field( $error_message ) );

			log_message( sprintf( 'Email failed for submission %d (attempt %d): %s', $submission_id, $attempts, $error_message ), 'Email_Status_Tracker', 'error' );

			// Schedule retry if we haven't reached max attempts.
			if ( $attempts < self::MAX_ATTEMPTS ) {
				return self::schedule_retry( $submission_id, $attempts );
			} else {
				// Max attempts reached, no more retries.
				update_post_meta( $submission_id, self::META_NEXT_RETRY, 0 );
				log_message( sprintf( 'Max retry attempts reached for submission %d', $submission_id ), 'Email_Status_Tracker', 'warning' );
				return false;
			}
		}
	}

	/**
	 * Schedule a retry attempt.
	 *
	 * @param int $submission_id Contact submission post ID.
	 * @param int $attempt_count Current attempt number (1-based).
	 * @return bool True if scheduled, false otherwise.
	 */
	public static function schedule_retry( $submission_id, $attempt_count ) {
		if ( $attempt_count < 1 || $attempt_count >= self::MAX_ATTEMPTS ) {
			return false;
		}

		// Calculate delay based on attempt count (0-based index for delays array).
		$delay_index = $attempt_count - 1;
		if ( ! isset( self::RETRY_DELAYS[ $delay_index ] ) ) {
			$delay_index = count( self::RETRY_DELAYS ) - 1; // Use last delay as fallback.
		}

		$delay          = self::RETRY_DELAYS[ $delay_index ];
		$scheduled_time = time() + $delay;

		// Update status to retrying.
		update_post_meta( $submission_id, self::META_STATUS, self::STATUS_RETRYING );
		update_post_meta( $submission_id, self::META_NEXT_RETRY, $scheduled_time );

		// Schedule the retry action.
		$args      = array( 'submission_id' => $submission_id );
		$scheduled = wp_schedule_single_event( $scheduled_time, self::RETRY_ACTION_HOOK, array( $args ) );

		if ( $scheduled ) {
			// Store the action ID for cleanup.
			$action_id = self::generate_action_id( $submission_id );
			update_post_meta( $submission_id, self::META_RETRY_ACTION, $action_id );

			log_message( sprintf( 'Retry scheduled for submission %d (attempt %d) in %d seconds', $submission_id, $attempt_count + 1, $delay ), 'Email_Status_Tracker', 'info' );
			return true;
		} else {
			log_message( sprintf( 'Failed to schedule retry for submission %d', $submission_id ), 'Email_Status_Tracker', 'error' );
			return false;
		}
	}

	/**
	 * Handle a retry attempt.
	 *
	 * @param array $args Arguments containing submission_id.
	 */
	public function handle_retry( $args ) {
		$submission_id = $args['submission_id'] ?? 0;

		if ( ! $submission_id ) {
			return;
		}

		// Clear the stored action ID.
		delete_post_meta( $submission_id, self::META_RETRY_ACTION );

		// Trigger the email sending again.
		$result = self::send_email_with_tracking( $submission_id );

		if ( ! $result ) {
			// The track_email_attempt method will handle scheduling the next retry.
			log_message( sprintf( 'Retry attempt failed for submission %d', $submission_id ), 'Email_Status_Tracker', 'warning' );
		} else {
			log_message( sprintf( 'Retry attempt succeeded for submission %d', $submission_id ), 'Email_Status_Tracker', 'info' );
		}
	}

	/**
	 * Send email with status tracking.
	 *
	 * @param int $submission_id Contact submission post ID.
	 * @return bool True on success, false on failure.
	 */
	private static function send_email_with_tracking( $submission_id ) {
		// send_immediately already tracks the attempt.
		return Sender_Confirmation_Email::send_immediately( $submission_id );
	}

	/**
	 * Initialize email status for a new submission.
	 *
	 * @param int $submission_id Contact submission post ID.
	 * @return bool True on success.
	 */
	public static function initialize_status( $submission_id ) {
		if ( ! $submission_id ) {
			return false;
		}

		// Only initialize if status meta doesn't exist.
		$existing_status = get_post_meta( $submission_id, self::META_STATUS, true );
		if ( ! empty( $existing_status ) ) {
			return false;
		}

		update_post_meta( $submission_id, self::META_STATUS, self::STATUS_PENDING );
		update_post_meta( $submission_id, self::META_ATTEMPTS, 0 );
		update_post_meta( $submission_id, self::META_LAST_ATTEMPT, 0 );
		update_post_meta( $submission_id, self::META_NEXT_RETRY, 0 );
		update_post_meta( $submission_id, self::META_ERROR, '' );

		return true;
	}

	/**
	 * Get email status for a submission.
	 *
	 * @param int $submission_id Contact submission post ID.
	 * @return array Status data.
	 */
	public static function get_email_status( $submission_id ) {
		$status = get_post_meta( $submission_id, self::META_STATUS, true );
		if ( empty( $status ) ) {
			$status = self::STATUS_PENDING;
		}

		$attempts = (int) get_post_meta( $submission_id, self::META_ATTEMPTS, true );

		return array(
			'status'        => $status,
			'attempts'      => $attempts,
			'last_attempt'  => (int) get_post_meta( $submission_id, self::META_LAST_ATTEMPT, true ),
			'next_retry'    => (int) get_post_meta( $submission_id, self::META_NEXT_RETRY, true ),
			'error_message' => get_post_meta( $submission_id, self::META_ERROR, true ),
			'is_failed'     => in_array( $status, array( self::STATUS_FAILED, self::STATUS_RETRYING ), true ),
			'can_retry'     => ( self::STATUS_FAILED === $status && $attempts < self::MAX_ATTEMPTS ),
			'max_attempts'  => self::MAX_ATTEMPTS,
		);
	}

	/**
	 * Get the number of failed emails.
	 *
	 * @return int Count of submissions with failed email status.
	 */
	public static function get_failed_email_count() {
		global $wpdb;

		$failed_statuses = array( self::STATUS_FAILED, self::STATUS_RETRYING );
		$placeholders    = array_fill( 0, count( $failed_statuses ), '%s' );
		$placeholders    = implode( ',', $placeholders );

		$query = "SELECT COUNT(DISTINCT pm.post_id)
			FROM {$wpdb->postmeta} pm
			INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
			WHERE pm.meta_key = %s
			AND pm.meta_value IN ($placeholders)
			AND p.post_type = 'contact_submission'
			AND p.post_status = 'publish'";

		$params = array_merge( array( self::META_STATUS ), $failed_statuses );
		$query  = $wpdb->prepare( $query, $params ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		return (int) $wpdb->get_var( $query );
	}

	/**
	 * Get all submissions with failed email status.
	 *
	 * @return array Array of submission IDs.
	 */
	public static function get_failed_submissions() {
		global $wpdb;

		$failed_statuses = array( self::STATUS_FAILED, self::STATUS_RETRYING );
		$placeholders    = array_fill( 0, count( $failed_statuses ), '%s' );
		$placeholders    = implode( ',', $placeholders );

		$query = "SELECT DISTINCT pm.post_id
			FROM {$wpdb->postmeta} pm
			INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
			WHERE pm.meta_key = %s
			AND pm.meta_value IN ($placeholders)
			AND p.post_type = 'contact_submission'
			AND p.post_status = 'publish'
			ORDER BY pm.post_id DESC";

		$params = array_merge( array( self::META_STATUS ), $failed_statuses );
		$query  = $wpdb->prepare( $query, $params ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		return $wpdb->get_col( $query );
	}

	/**
	 * Manually trigger a retry for a submission.
	 *
	 * @param int $submission_id Contact submission post ID.
	 * @return bool True on success.
	 */
	public static function manual_retry( $submission_id ) {
		$status = self::get_email_status( $submission_id );

		if ( ! $status['is_failed'] || ! $status['can_retry'] ) {
			return false;
		}

		// Clear any existing retry schedule.
		self::unschedule_retry( $submission_id );

		// Trigger immediate retry.
		return self::send_email_with_tracking( $submission_id );
	}

	/**
	 * Clean up retry data for a submission.
	 *
	 * @param int $submission_id Contact submission post ID.
	 */
	private static function cleanup_retry_data( $submission_id ) {
		delete_post_meta( $submission_id, self::META_RETRY_ACTION );
		delete_post_meta( $submission_id, self::META_NEXT_RETRY );
	}

	/**
	 * Unschedule any pending retry for a submission.
	 *
	 * @param int $submission_id Contact submission post ID.
	 */
	private static function unschedule_retry( $submission_id ) {
		$action_id = get_post_meta( $submission_id, self::META_RETRY_ACTION, true );

		if ( $action_id ) {
			// WordPress doesn't provide a direct way to unschedule by custom ID,
			// but we can clear the scheduled event by time and hook.
			wp_clear_scheduled_hook( self::RETRY_ACTION_HOOK, array( array( 'submission_id' => $submission_id ) ) );
			self::cleanup_retry_data( $submission_id );
		}
	}

	/**
	 * Get the number of attempts for a submission.
	 *
	 * @param int $submission_id Contact submission post ID.
	 * @return int Number of attempts.
	 */
	private static function get_attempts( $submission_id ) {
		return (int) get_post_meta( $submission_id, self::META_ATTEMPTS, true );
	}

	/**
	 * Generate a unique action ID for tracking scheduled retries.
	 *
	 * @param int $submission_id Contact submission post ID.
	 * @return string Unique action ID.
	 */
	private static function generate_action_id( $submission_id ) {
		return 'retry_' . $submission_id . '_' . wp_generate_uuid4();
	}

	/**
	 * Format status for display.
	 *
	 * @param string $status Status constant.
	 * @return string Human-readable status.
	 */
	public static function format_status( $status ) {
		switch ( $status ) {
			case self::STATUS_SENT:
				return __( 'Sent', 'am-portfolio-theme' );
			case self::STATUS_FAILED:
				return __( 'Failed', 'am-portfolio-theme' );
			case self::STATUS_RETRYING:
				return __( 'Retrying', 'am-portfolio-theme' );
			case self::STATUS_PENDING:
			default:
				return __( 'Pending', 'am-portfolio-theme' );
		}
	}

	/**
	 * Get status badge HTML.
	 *
	 * @param string $status Status constant.
	 * @return string HTML badge.
	 */
	public static function get_status_badge( $status ) {
		$status_text = self::format_status( $status );

		switch ( $status ) {
			case self::STATUS_SENT:
				$class = 'status-sent';
				$color = '#46b450'; // Green.
				break;
			case self::STATUS_FAILED:
				$class = 'status-failed';
				$color = '#dc3232'; // Red.
				break;
			case self::STATUS_RETRYING:
				$class = 'status-retrying';
				$color = '#f0ad4e'; // Orange/Yellow.
				break;
			case self::STATUS_PENDING:
			default:
				$class = 'status-pending';
				$color = '#72777c'; // Gray.
				break;
		}

		return sprintf(
			'<span class="email-status-badge %s" style="display: inline-block; padding: 2px 8px; border-radius: 3px; background-color: %s; color: white; font-size: 12px; font-weight: 600;">%s</span>',
			esc_attr( $class ),
			esc_attr( $color ),
			esc_html( $status_text )
		);
	}
}
