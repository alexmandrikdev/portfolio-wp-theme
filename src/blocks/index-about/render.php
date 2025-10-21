<section id="about" class="about container">
	<div class="about__image scroll-fade">
		<div class="about__profile-pic">
			<?php if ( ! empty( $attributes['profile_image'] ) ) : ?>
				<?php
				$image_url = wp_get_attachment_image_url( $attributes['profile_image'], 'medium' );
				$image_alt = get_post_meta( $attributes['profile_image'], '_wp_attachment_image_alt', true );
				?>
				<img 
					src="<?php echo esc_url( $image_url ); ?>" 
					alt="<?php echo esc_attr( $image_alt ? $image_alt : $attributes['title'] ); ?>" 
					class="about__profile-pic-img"
				/>
			<?php endif; ?>
		</div>
	</div>
	
	<div class="about__content scroll-fade">
		<?php if ( ! empty( $attributes['title'] ) ) : ?>
			<h2 class="about__title"><?php echo esc_html( $attributes['title'] ); ?></h2>
		<?php endif; ?>
		
		<?php if ( ! empty( $attributes['name_highlight'] ) ) : ?>
			<p class="about__text about__text--large">
				<?php echo wp_kses_post( $attributes['name_highlight'] ); ?>
			</p>
		<?php endif; ?>
		
		<?php if ( ! empty( $attributes['description'] ) ) : ?>
			<p class="about__text">
				<?php echo wp_kses_post( $attributes['description'] ); ?>
			</p>
		<?php endif; ?>
	</div>
</section>