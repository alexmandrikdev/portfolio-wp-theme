<?php

namespace AMPortfolioTheme\PostTypes;

use AMPortfolioTheme\Helpers\Markdown_Helper;
use AMPortfolioTheme\Emails\Email_Status_Tracker;

defined( 'ABSPATH' ) || exit;

class Contact_Submission_CPT {

	public static function init() {
		$self = new self();

		add_action( 'init', array( $self, 'register_contact_submission_post_type' ) );
		add_filter( 'manage_contact_submission_posts_columns', array( $self, 'set_custom_columns' ) );
		add_action( 'manage_contact_submission_posts_custom_column', array( $self, 'custom_column_content' ), 10, 2 );
		add_action( 'add_meta_boxes', array( $self, 'add_contact_submission_meta_box' ) );
		add_action( 'admin_post_retry_email', array( $self, 'handle_retry_email' ) );
		add_action( 'admin_menu', array( $self, 'add_menu_badge' ), 999 );
	}

	public function register_contact_submission_post_type() {
		$labels = array(
			'name'          => _x( 'Contact Submissions', 'Post Type General Name', 'am-portfolio-theme' ),
			'singular_name' => _x( 'Contact Submission', 'Post Type Singular Name', 'am-portfolio-theme' ),
			'menu_name'     => __( 'Contact Submissions', 'am-portfolio-theme' ),
			'all_items'     => __( 'All Submissions', 'am-portfolio-theme' ),
			'add_new_item'  => __( 'Add New Submission', 'am-portfolio-theme' ),
			'add_new'       => __( 'Add New', 'am-portfolio-theme' ),
			'new_item'      => __( 'New Submission', 'am-portfolio-theme' ),
			'edit_item'     => __( 'View Submission', 'am-portfolio-theme' ),
			'update_item'   => __( 'Update Submission', 'am-portfolio-theme' ),
			'view_item'     => __( 'View Submission', 'am-portfolio-theme' ),
			'view_items'    => __( 'View Submissions', 'am-portfolio-theme' ),
		);

		$args = array(
			'label'               => __( 'Contact Submission', 'am-portfolio-theme' ),
			'labels'              => $labels,
			'supports'            => array( 'title' ),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 25,
			'menu_icon'           => 'dashicons-email-alt',
			'show_in_admin_bar'   => false,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'capability_type'     => 'post',
			'capabilities'        => array(
				'create_posts' => false,
			),
			'map_meta_cap'        => true,
		);

		register_post_type( 'contact_submission', $args );
	}

	public function set_custom_columns( $columns ) {
		unset( $columns['date'] );
		$columns['title']        = __( 'Title', 'am-portfolio-theme' );
		$columns['subject']      = __( 'Subject', 'am-portfolio-theme' );
		$columns['name']         = __( 'From', 'am-portfolio-theme' );
		$columns['email']        = __( 'Email', 'am-portfolio-theme' );
		$columns['language']     = __( 'Language', 'am-portfolio-theme' );
		$columns['email_status'] = __( 'Email Status', 'am-portfolio-theme' );
		$columns['date']         = __( 'Date', 'am-portfolio-theme' );
		return $columns;
	}

	public function custom_column_content( $column, $post_id ) {
		switch ( $column ) {
			case 'subject':
				echo esc_html( get_post_meta( $post_id, '_contact_submission_subject', true ) );
				break;
			case 'name':
				echo esc_html( get_post_meta( $post_id, '_contact_submission_name', true ) );
				break;
			case 'email':
				echo esc_html( get_post_meta( $post_id, '_contact_submission_email', true ) );
				break;
			case 'language':
				$language = get_post_meta( $post_id, '_contant_submission_language', true );
				if ( ! empty( $language ) ) {
					echo esc_html( $language );
				} else {
					echo '<span aria-hidden="true">—</span><span class="screen-reader-text">' . esc_html__( 'Not specified', 'am-portfolio-theme' ) . '</span>';
				}
				break;
			case 'email_status':
				$status_data = Email_Status_Tracker::get_email_status( $post_id );
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_status_badge returns escaped HTML.
				echo Email_Status_Tracker::get_status_badge( $status_data['status'] );
				if ( $status_data['attempts'] > 0 ) {
					echo '<br><small>' . sprintf(
						// translators: %d is the number of attempts.
						esc_html__( 'Attempts: %d', 'am-portfolio-theme' ),
						esc_html( $status_data['attempts'] )
					) . '</small>';
				}
				break;
		}
	}

	/**
	 * Handle manual retry of failed email.
	 */
	public function handle_retry_email() {
		// Check nonce and permissions.
		if ( ! isset( $_GET['post_id'] ) || ! is_numeric( $_GET['post_id'] ) ) {
			wp_die( esc_html__( 'Invalid request.', 'am-portfolio-theme' ) );
		}

		$post_id = intval( $_GET['post_id'] );

		// Verify nonce.
		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'retry_email_' . $post_id ) ) {
			wp_die( esc_html__( 'Security check failed.', 'am-portfolio-theme' ) );
		}

		// Check user capabilities.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			wp_die( esc_html__( 'You do not have permission to retry this email.', 'am-portfolio-theme' ) );
		}

		// Get email status.
		$status_data = Email_Status_Tracker::get_email_status( $post_id );

		// Only allow retry for failed emails.
		if ( Email_Status_Tracker::STATUS_FAILED !== $status_data['status'] ) {
			wp_die( esc_html__( 'Email is not in a failed state.', 'am-portfolio-theme' ) );
		}

		// Trigger immediate retry.
		$result = Email_Status_Tracker::manual_retry( $post_id );

		// Redirect back to the contact submission edit page.
		$redirect_url = add_query_arg(
			array(
				'post'   => $post_id,
				'action' => 'edit',
				'retry'  => $result ? 'success' : 'failed',
			),
			admin_url( 'post.php' )
		);

		wp_safe_redirect( $redirect_url );
		exit;
	}

	public function add_contact_submission_meta_box() {
		add_meta_box(
			'contact-submission-details',
			__( 'Contact Submission Details', 'am-portfolio-theme' ),
			array( $this, 'display_contact_submission_meta_box' ),
			'contact_submission',
			'normal',
			'high'
		);
	}

	public function display_contact_submission_meta_box( $post ) {
		$name       = get_post_meta( $post->ID, '_contact_submission_name', true );
		$email      = get_post_meta( $post->ID, '_contact_submission_email', true );
		$subject    = get_post_meta( $post->ID, '_contact_submission_subject', true );
		$message    = get_post_meta( $post->ID, '_contact_submission_message', true );
		$timezone   = get_post_meta( $post->ID, '_contact_submission_timezone', true );
		$language   = get_post_meta( $post->ID, '_contant_submission_language', true );
		$ip_address = get_post_meta( $post->ID, '_contact_submission_ip', true );
		$user_agent = get_post_meta( $post->ID, '_contact_submission_user_agent', true );

		$rendered_message = Markdown_Helper::parse( $message );

		// Get email status data.
		$status_data = Email_Status_Tracker::get_email_status( $post->ID );

		?>
		<div class="am-portfolio-contact-submission-details">
			<style>
				.am-portfolio-contact-submission-details {
					padding: 1.5em;
					background: #f6f7f7;
					border-radius: 4px;
				}
				.am-portfolio-contact-submission-details .field {
					margin-bottom: 1.5em;
				}
				.am-portfolio-contact-submission-details label {
					display: block;
					font-weight: 600;
					margin-bottom: 0.5em;
					color: #1d2327;
				}
				.am-portfolio-contact-submission-details .value {
					padding: 0.75em;
					background: white;
					border: 1px solid #c3c4c7;
					border-radius: 4px;
					word-wrap: break-word;
				}
				.am-portfolio-contact-submission-details .email-status-actions {
					margin-top: 1em;
					padding-top: 1em;
					border-top: 1px solid #c3c4c7;
				}
				.am-portfolio-contact-submission-details .email-status-actions .button {
					margin-right: 0.5em;
				}
				.am-portfolio-contact-submission-details .email-status-error {
					color: #d63638;
					background: #fcf0f1;
					border: 1px solid #d63638;
					padding: 0.75em;
					border-radius: 4px;
					margin-top: 0.5em;
				}
			</style>

			<div class="field name">
				<label for="contact_submission_name"><?php esc_html_e( 'Name', 'am-portfolio-theme' ); ?></label>
				<div class="value"><?php echo esc_html( $name ); ?></div>
			</div>

			<div class="field email">
				<label for="contact_submission_email"><?php esc_html_e( 'Email', 'am-portfolio-theme' ); ?></label>
				<div class="value">
					<a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
				</div>
			</div>

			<div class="field subject">
				<label for="contact_submission_subject"><?php esc_html_e( 'Subject', 'am-portfolio-theme' ); ?></label>
				<div class="value"><?php echo esc_html( $subject ); ?></div>
			</div>

			<div class="field message">
				<label for="contact_submission_message"><?php esc_html_e( 'Message', 'am-portfolio-theme' ); ?></label>
				<div class="value"><?php echo wp_kses_post( $rendered_message ); ?></div>
			</div>

			<div class="field email-status">
				<label for="contact_submission_email_status"><?php esc_html_e( 'Sender Confirmation Email Status', 'am-portfolio-theme' ); ?></label>
				<div class="value">
					<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_status_badge returns escaped HTML.
					echo Email_Status_Tracker::get_status_badge( $status_data['status'] );
					?>
					
					<div class="email-status-details" style="margin-top: 0.75em;">
						<p><strong><?php esc_html_e( 'Attempts:', 'am-portfolio-theme' ); ?></strong> <?php echo esc_html( $status_data['attempts'] ); ?> / <?php echo esc_html( Email_Status_Tracker::MAX_ATTEMPTS ); ?></p>
						
						<?php if ( ! empty( $status_data['last_attempt'] ) ) : ?>
							<p><strong><?php esc_html_e( 'Last Attempt:', 'am-portfolio-theme' ); ?></strong>
								<?php echo esc_html( wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $status_data['last_attempt'] ) ); ?>
							</p>
						<?php endif; ?>
						
						<?php if ( ! empty( $status_data['next_retry'] ) ) : ?>
							<p><strong><?php esc_html_e( 'Next Retry:', 'am-portfolio-theme' ); ?></strong>
								<?php echo esc_html( wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $status_data['next_retry'] ) ); ?>
								(<?php echo esc_html( human_time_diff( time(), $status_data['next_retry'] ) ); ?>)
							</p>
						<?php endif; ?>
						
						<?php if ( ! empty( $status_data['error_message'] ) ) : ?>
							<div class="email-status-error">
								<strong><?php esc_html_e( 'Error:', 'am-portfolio-theme' ); ?></strong>
								<?php echo esc_html( $status_data['error_message'] ); ?>
							</div>
						<?php endif; ?>
					</div>
					
					<?php if ( $status_data['can_retry'] ) : ?>
						<div class="email-status-actions">
							<?php
							$retry_url = wp_nonce_url(
								add_query_arg(
									array(
										'action'  => 'retry_email',
										'post_id' => $post->ID,
									),
									admin_url( 'admin-post.php' )
								),
								'retry_email_' . $post->ID
							);
							?>
							<a href="<?php echo esc_url( $retry_url ); ?>" class="button button-primary">
								<?php esc_html_e( 'Retry Now', 'am-portfolio-theme' ); ?>
							</a>
							<span class="description">
								<?php esc_html_e( 'Manually trigger an immediate retry of the sender confirmation email.', 'am-portfolio-theme' ); ?>
							</span>
						</div>
					<?php endif; ?>
				</div>
			</div>

			<div class="field timezone">
				<label for="contact_submission_timezone"><?php esc_html_e( 'Timezone', 'am-portfolio-theme' ); ?></label>
				<div class="value">
					<?php if ( ! empty( $timezone ) ) : ?>
						<?php echo esc_html( $timezone ); ?>
					<?php else : ?>
						<span aria-hidden="true">—</span>
						<span class="screen-reader-text"><?php esc_html_e( 'Not specified', 'am-portfolio-theme' ); ?></span>
					<?php endif; ?>
				</div>
			</div>

			<div class="field language">
				<label for="contact_submission_language"><?php esc_html_e( 'Language', 'am-portfolio-theme' ); ?></label>
				<div class="value">
					<?php if ( ! empty( $language ) ) : ?>
						<?php echo esc_html( $language ); ?>
					<?php else : ?>
						<span aria-hidden="true">—</span>
						<span class="screen-reader-text"><?php esc_html_e( 'Not specified', 'am-portfolio-theme' ); ?></span>
					<?php endif; ?>
				</div>
			</div>

			<div class="field ip-address">
				<label for="contact_submission_ip"><?php esc_html_e( 'IP Address', 'am-portfolio-theme' ); ?></label>
				<div class="value">
					<?php if ( ! empty( $ip_address ) ) : ?>
						<?php echo esc_html( $ip_address ); ?>
					<?php else : ?>
						<span aria-hidden="true">—</span>
						<span class="screen-reader-text"><?php esc_html_e( 'Not recorded', 'am-portfolio-theme' ); ?></span>
					<?php endif; ?>
				</div>
			</div>

			<div class="field user-agent">
				<label for="contact_submission_user_agent"><?php esc_html_e( 'User Agent', 'am-portfolio-theme' ); ?></label>
				<div class="value">
					<?php if ( ! empty( $user_agent ) ) : ?>
						<code><?php echo esc_html( $user_agent ); ?></code>
					<?php else : ?>
						<span aria-hidden="true">—</span>
						<span class="screen-reader-text"><?php esc_html_e( 'Not recorded', 'am-portfolio-theme' ); ?></span>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Add badge to Contact Submissions menu item showing failed email count.
	 */
	public function add_menu_badge() {
		// Get failed email count.
		$failed_count = Email_Status_Tracker::get_failed_email_count();

		if ( $failed_count <= 0 ) {
			return;
		}

		// Add CSS and JavaScript for badge.
		add_action( 'admin_head', array( $this, 'add_menu_badge_css' ) );
	}

	/**
	 * Add CSS for menu badge.
	 */
	public function add_menu_badge_css() {
		$failed_count = Email_Status_Tracker::get_failed_email_count();

		if ( $failed_count <= 0 ) {
			return;
		}

		// Add badge via CSS and JavaScript.
		?>
		<script>
		(function($) {
			$(document).ready(function() {
				// Find the Contact Submissions menu item.
				var $menuItem = $('#menu-posts-contact_submission a.menu-top .wp-menu-name');
				if ($menuItem.length) {
					// Create badge.
					var badge = $('<span class="update-plugins count-<?php echo esc_js( $failed_count ); ?>"><span class="plugin-count"><?php echo esc_js( $failed_count ); ?></span></span>');
					$menuItem.append(badge);
				}
			});
		})(jQuery);
		</script>
		<style>
		#menu-posts-contact_submission .update-plugins {
			display: inline-block;
			vertical-align: top;
			margin: 1px 0 -1px 2px;
			padding: 0 5px;
			min-width: 18px;
			height: 18px;
			border-radius: 9px;
			background-color: #d63638;
			color: #fff;
			font-size: 9px;
			line-height: 17px;
			text-align: center;
			z-index: 26;
		}
		</style>
		<?php
	}
}