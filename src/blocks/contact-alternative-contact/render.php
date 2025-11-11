<?php
$block_text = $attributes['text'] ?? '';

$settings      = get_option( 'portfolio_theme_settings', array() );
$contact_email = $settings['contact_email'] ?? '';

if ( $contact_email ) {
	$email_link   = sprintf(
		'<a href="mailto:%1$s" class="contact-alternative-contact__email-link">%1$s</a>',
		esc_attr( $contact_email )
	);
	$display_text = str_replace( '[email]', $email_link, $block_text );
} else {
	$display_text = '';
}

if ( ! $display_text ) {
	return '';
}
?>

<div <?php echo get_block_wrapper_attributes( array( 'class' => 'contact-alternative-contact' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="container">
		<?php if ( $display_text ) : ?>
			<p class="contact-alternative-contact__text">
				<?php echo wp_kses_post( $display_text ); ?>
			</p>
		<?php endif; ?>
	</div>
</div>
