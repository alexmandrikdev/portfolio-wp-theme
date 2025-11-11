<?php
$heading = $attributes['heading'] ?? '';
$content = $attributes['content'] ?? '';

if ( empty( $heading ) && empty( $content ) ) {
	return '';
}
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'about-story' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="container">
		<div class="about-story__content">
			<?php if ( ! empty( $heading ) ) : ?>
				<h2 class="about-story__heading scroll-fade"><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>

			<?php if ( ! empty( $content ) ) : ?>
				<div class="about-story__text scroll-fade">
					<?php echo wp_kses_post( wpautop( $content ) ); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
