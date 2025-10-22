<section id="contact" class="cta-section">
	<div class="container">
		<?php if ( ! empty( $attributes['title'] ) ) : ?>
			<h2 class="cta-section__title scroll-fade"><?php echo esc_html( $attributes['title'] ); ?></h2>
		<?php endif; ?>

		<?php
		$primary_button_text      = $attributes['primary_button_text'] ?? '';
		$secondary_button_text    = $attributes['secondary_button_text'] ?? '';
		$secondary_button_page_id = $attributes['secondary_button_page_id'] ?? null;

		$secondary_button_url = '#';
		if ( ! empty( $secondary_button_page_id ) ) {
			$secondary_button_url = get_permalink( $secondary_button_page_id );
		}

		if ( ! empty( $primary_button_text ) || ! empty( $secondary_button_text ) ) :
			?>
			<div class="cta-section__buttons">
				<?php if ( ! empty( $secondary_button_text ) && ! empty( $secondary_button_page_id ) ) : ?>
					<a href="<?php echo esc_url( $secondary_button_url ); ?>" class="btn-primary scroll-fade">
						<?php echo esc_html( $secondary_button_text ); ?>
					</a>
				<?php endif; ?>

				<?php if ( ! empty( $primary_button_text ) ) : ?>
					<button class="btn-secondary scroll-fade"><?php echo esc_html( $primary_button_text ); ?></button>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</section>