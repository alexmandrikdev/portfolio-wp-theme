<?php
use AMPortfolioTheme\Helpers\Media_Data_Loader;
use AMPortfolioTheme\Helpers\Media_Display;

$section_title = $attributes['title'] ?? '';
$value_cards   = $attributes['value_cards'] ?? array();

if ( empty( $section_title ) && empty( $value_cards ) ) {
	return '';
}

$icon_ids   = array_filter( array_column( $value_cards, 'icon' ) );
$media_data = array();
if ( ! empty( $icon_ids ) ) {
	$media_data = Media_Data_Loader::load_media_data_bulk( $icon_ids );
}
?>

<section <?php echo get_block_wrapper_attributes( array( 'class' => 'services-values' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="services-values__container container">
		<?php if ( ! empty( $section_title ) ) : ?>
			<h2 class="services-values__title scroll-fade"><?php echo esc_html( $section_title ); ?></h2>
		<?php endif; ?>

		<?php if ( ! empty( $value_cards ) ) : ?>
			<div class="services-values__grid scroll-fade">
				<?php foreach ( $value_cards as $card ) : ?>
					<?php
					$card_title       = $card['title'] ?? '';
					$card_description = $card['description'] ?? '';
					$icon_id          = $card['icon'] ?? '';

					if ( empty( $card_title ) && empty( $card_description ) ) {
						continue;
					}
					?>

					<div class="services-values__card">
						<?php if ( $icon_id && isset( $media_data[ $icon_id ] ) ) : ?>
							<div class="services-values__icon">
								<?php
								Media_Display::display_media_item(
									$media_data[ $icon_id ],
									array(
										'size'   => 'medium',
										'class'  => 'services-values__icon-image',
										'width'  => 24,
										'height' => 24,
									)
								);
								?>
							</div>
						<?php endif; ?>

						<?php if ( ! empty( $card_title ) ) : ?>
							<h3 class="services-values__card-title"><?php echo esc_html( $card_title ); ?></h3>
						<?php endif; ?>

						<?php if ( ! empty( $card_description ) ) : ?>
							<div class="services-values__card-description">
								<?php echo esc_html( $card_description ); ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
