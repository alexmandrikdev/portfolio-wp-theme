<?php
use AMPortfolioTheme\Helpers\Media_Data_Loader;
use AMPortfolioTheme\Helpers\Media_Display;

$block_title    = $attributes['title'] ?? '';
$block_subtitle = $attributes['subtitle'] ?? '';
$package_cards  = $attributes['package_cards'] ?? array();

// Collect all media IDs for bulk loading.
$media_ids = array();
foreach ( $package_cards as $package ) {
	$icon_id = $package['icon'] ?? 0;
	if ( $icon_id ) {
		$media_ids[] = $icon_id;
	}
}

// Load media data in bulk.
$media_data = array();
if ( ! empty( $media_ids ) ) {
	$media_data = Media_Data_Loader::load_media_data_bulk( $media_ids );
}

?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'services-packages' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="services-packages__container container">
		<?php if ( ! empty( $block_title ) ) : ?>
			<h2 class="services-packages__title scroll-fade"><?php echo esc_html( $block_title ); ?></h2>
		<?php endif; ?>
		
		<?php if ( ! empty( $block_subtitle ) ) : ?>
			<p class="services-packages__subtitle scroll-fade"><?php echo esc_html( $block_subtitle ); ?></p>
		<?php endif; ?>

		<?php if ( ! empty( $package_cards ) ) : ?>
			<div class="services-packages__grid">
				<?php foreach ( $package_cards as $index => $package ) : ?>
					<?php
					$is_featured       = $package['is_featured'] ?? false;
					$icon_id           = $package['icon'] ?? 0;
					$package_title     = $package['title'] ?? '';
					$description       = $package['description'] ?? '';
					$highlighted_value = $package['highlighted_value'] ?? '';
					$design_approach   = $package['design_approach'] ?? '';
					$features          = $package['features'] ?? array();
					$button_text       = $package['button_text'] ?? '';

					$icon_data = $icon_id && isset( $media_data[ $icon_id ] ) ? $media_data[ $icon_id ] : null;

					$card_classes = 'services-packages__card';
					if ( $is_featured ) {
						$card_classes .= ' services-packages__card--featured';
					}
					?>
					<div class="<?php echo esc_attr( $card_classes ); ?> scroll-fade">
						<?php
						if ( $is_featured ) :
							?>
							<div class="services-packages__badge"><?php echo esc_html( $package['featured_label'] ?? __( 'Most Popular', 'am-portfolio-theme' ) ); ?></div>
						<?php endif; ?>
						
						<?php if ( $icon_data ) : ?>
							<div class="services-packages__icon">
								<?php
								Media_Display::display_media_item(
									$icon_data,
									array(
										'width'  => 32,
										'height' => 32,
										'class'  => 'services-packages__icon-image',
									)
								);
								?>
							</div>
						<?php endif; ?>
						
						<?php if ( ! empty( $package_title ) ) : ?>
							<h3 class="services-packages__card-title"><?php echo esc_html( $package_title ); ?></h3>
						<?php endif; ?>
						
						<?php if ( ! empty( $description ) ) : ?>
							<p class="services-packages__description"><?php echo esc_html( $description ); ?></p>
						<?php endif; ?>
						
						<?php if ( ! empty( $highlighted_value ) ) : ?>
							<div class="services-packages__highlighted-value">
								<h4 class="services-packages__highlighted-title"><?php esc_html_e( 'Highlighted Value', 'am-portfolio-theme' ); ?></h4>
								<div class="services-packages__highlighted-content">
									<?php echo wp_kses_post( $highlighted_value ); ?>
								</div>
							</div>
						<?php endif; ?>
						
						<?php if ( ! empty( $design_approach ) ) : ?>
							<div class="services-packages__design-approach">
								<h4 class="services-packages__design-title"><?php esc_html_e( 'Design Approach', 'am-portfolio-theme' ); ?></h4>
								<div class="services-packages__design-content">
									<?php echo wp_kses_post( $design_approach ); ?>
								</div>
							</div>
						<?php endif; ?>
						
						<?php if ( ! empty( $features ) ) : ?>
							<ul class="services-packages__features">
								<?php foreach ( $features as $feature ) : ?>
									<?php if ( ! empty( $feature ) ) : ?>
										<li class="services-packages__feature-item"><?php echo esc_html( $feature ); ?></li>
									<?php endif; ?>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
						
						<?php if ( ! empty( $button_text ) ) : ?>
							<button 
								class="services-packages__button btn-primary"
								data-wp-interactive="contactFormModal"
								data-wp-on--click="actions.openModal"
							>
								<?php echo esc_html( $button_text ); ?>
							</button>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
