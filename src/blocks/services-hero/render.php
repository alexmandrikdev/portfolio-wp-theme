<section class="services-hero" <?php echo get_block_wrapper_attributes(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes is already escaped by WordPress. ?>>
	<div class="services-hero__container container">
		<?php if ( ! empty( $attributes['title'] ) ) : ?>
			<h1 class="services-hero__title scroll-fade">
				<?php echo esc_html( $attributes['title'] ); ?>
			</h1>
		<?php endif; ?>
		
		<?php if ( ! empty( $attributes['subtitle'] ) ) : ?>
			<p class="services-hero__subtitle scroll-fade">
				<?php echo esc_html( $attributes['subtitle'] ); ?>
			</p>
		<?php endif; ?>
	</div>
</section>
