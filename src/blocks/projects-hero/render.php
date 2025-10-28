<section class="projects-hero">
	<div class="container">
		<?php if ( ! empty( $attributes['title'] ) ) : ?>
			<h1 class="projects-hero__title scroll-fade">
				<?php echo esc_html( $attributes['title'] ); ?>
			</h1>
		<?php endif; ?>

		<?php if ( ! empty( $attributes['description'] ) ) : ?>
			<div class="projects-hero__description scroll-fade">
				<?php echo wp_kses_post( $attributes['description'] ); ?>
			</div>
		<?php endif; ?>
	</div>
</section>