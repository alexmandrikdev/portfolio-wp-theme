<?php

namespace AMPortfolioTheme\Emails;

use AMPortfolioTheme\Helpers\Markdown_Helper;

defined( 'ABSPATH' ) || exit;

abstract class Base_Email_Notification {

	protected $submission_data;
	protected $submission_id;
	protected $data;

	public function __construct( $submission_data, $submission_id ) {
		$this->submission_data = $submission_data;
		$this->submission_id   = $submission_id;

		$this->initialize_data();
	}

	abstract protected function initialize_data();

	abstract protected function get_template_path();

	public function render() {
		$template_path = $this->get_template_path();

		$data = $this->data;

		ob_start();
		include $template_path;
		return ob_get_clean();
	}

	public static function display( $submission_data, $submission_id ) {
		$notification = new static( $submission_data, $submission_id );
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $notification->render();
	}

	public static function get( $submission_data, $submission_id ) {
		$notification = new static( $submission_data, $submission_id );
		return $notification->render();
	}

	protected static function get_submission_data( $submission_id ) {
		$language = get_post_meta( $submission_id, '_contact_submission_language', true );

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

	abstract protected static function get_scheduled_action_hook();

	protected static function before_schedule( $submission_id ) {
		// Override in child class if needed.
	}

	public static function schedule( $submission_id ) {
		static::before_schedule( $submission_id );

		$args = array(
			'submission_id' => $submission_id,
		);

		$filter_name = static::get_schedule_filter_name();
		$args        = apply_filters( $filter_name, $args, $submission_id );

		$scheduled = wp_schedule_single_event(
			time(),
			static::get_scheduled_action_hook(),
			array( $args )
		);

		if ( false === $scheduled ) {
			return static::send_immediately( $submission_id );
		}

		return true;
	}

	protected static function get_schedule_filter_name() {
		// Default filter name pattern.
		return 'am_portfolio_' . static::get_class_slug() . '_schedule_args';
	}

	protected static function get_class_slug() {
		// Convert class name to slug, e.g., "Admin_Contact_Notification" -> "admin_notification".
		$class_name = static::class;
		$parts      = explode( '\\', $class_name );
		$short_name = end( $parts );
		$slug       = strtolower( preg_replace( '/([a-z])([A-Z])/', '$1_$2', $short_name ) );
		return $slug;
	}

	public static function handle_scheduled_send( $args ) {
		$submission_id = $args['submission_id'] ?? 0;

		if ( ! $submission_id ) {
			log_message( 'Invalid submission ID', static::class, 'warning' );
			return false;
		}

		return static::send_immediately( $submission_id );
	}

	abstract public static function send_immediately( $submission_id );
}
