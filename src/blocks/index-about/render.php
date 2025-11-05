<?php

use AMPortfolioTheme\Helpers\Media_Data_Loader;
use AMPortfolioTheme\Helpers\Media_Display;
?>

<section id="about" class="about container">
	<div class="about__image scroll-fade">
		<div class="about__profile-pic">
			<?php
			if ( ! empty( $attributes['profile_image'] ) ) {
				$media_data = Media_Data_Loader::load_media_data_single( $attributes['profile_image'] );

				Media_Display::display_media_item(
					$media_data,
					array(
						'class' => 'about__profile-pic-img',
						'size'  => 'medium',
						'sizes' => '300px',
					)
				);
			}
			?>
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