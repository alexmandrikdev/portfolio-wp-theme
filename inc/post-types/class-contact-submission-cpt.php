<?php

namespace AMPortfolioTheme\PostTypes;

use AMPortfolioTheme\Helpers\Markdown_Helper;
use Exception;
use Parsedown;

defined( 'ABSPATH' ) || exit;

class Contact_Submission_CPT {

	public static function init() {
		$self = new self();

		add_action( 'init', array( $self, 'register_contact_submission_post_type' ) );
		add_filter( 'manage_contact_submission_posts_columns', array( $self, 'set_custom_columns' ) );
		add_action( 'manage_contact_submission_posts_custom_column', array( $self, 'custom_column_content' ), 10, 2 );
		add_action( 'add_meta_boxes', array( $self, 'add_contact_submission_meta_box' ) );
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
		$columns['title']    = __( 'Title', 'am-portfolio-theme' );
		$columns['subject']  = __( 'Subject', 'am-portfolio-theme' );
		$columns['name']     = __( 'From', 'am-portfolio-theme' );
		$columns['email']    = __( 'Email', 'am-portfolio-theme' );
		$columns['language'] = __( 'Language', 'am-portfolio-theme' );
		$columns['date']     = __( 'Date', 'am-portfolio-theme' );
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
		}
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
}