<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$email_data = get_query_var( 'email_data', array() );
?>

<div class="submission-details">
	<div class="field-group">
		<span class="field-label"><?php echo esc_html( __( 'Subject', 'am-portfolio-theme' ) ); ?></span>
		<div class="field-value">
			<?php
			echo ! empty( $email_data['subject'] )
				? esc_html( $email_data['subject'] )
				: '<em>' . esc_html( __( 'No subject provided', 'am-portfolio-theme' ) ) . '</em>';
			?>
		</div>
	</div>

	<div class="field-group">
		<span class="field-label"><?php echo esc_html( __( 'Full Name', 'am-portfolio-theme' ) ); ?></span>
		<div class="field-value"><?php echo esc_html( $email_data['name'] ?? '' ); ?></div>
	</div>

	<div class="field-group">
		<span class="field-label"><?php echo esc_html( __( 'Email', 'am-portfolio-theme' ) ); ?></span>
		<div class="field-value"><?php echo esc_html( $email_data['email'] ?? '' ); ?></div>
	</div>

	<div class="field-group">
		<span class="field-label"><?php echo esc_html( __( 'Message', 'am-portfolio-theme' ) ); ?></span>
		<div class="field-value message-content"><?php echo wp_kses_post( $email_data['message'] ); ?></div>
	</div>
</div>