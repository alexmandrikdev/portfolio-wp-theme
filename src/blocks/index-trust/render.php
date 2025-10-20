<?php if ( ! empty( $attributes['trust_items'] ) && is_array( $attributes['trust_items'] ) ) : ?>
	<section class="trust">
		<div class="container">
			<div class="trust__grid">
				<?php
				foreach ( $attributes['trust_items'] as $item ) :
					$number = isset( $item['number'] ) ? $item['number'] : '';
					$label  = isset( $item['label'] ) ? $item['label'] : '';

					if ( ! empty( $number ) && ! empty( $label ) ) :
						?>
						<div class="trust__item scroll-fade">
							<div class="trust__number"><?php echo esc_html( $number ); ?></div>
							<div class="trust__label"><?php echo esc_html( $label ); ?></div>
						</div>
						<?php
					endif;
				endforeach;
				?>
			</div>
		</div>
	</section>
<?php endif; ?>