<?php
use AMPortfolioTheme\Helpers\Media_Data_Loader;
use AMPortfolioTheme\Helpers\Media_Display;

if ( empty( $attributes['title'] ) || empty( $attributes['items'] ) ) {
	return;
}

$media_ids  = array_filter( array_column( $attributes['items'], 'icon' ) );
$media_data = array();
if ( ! empty( $media_ids ) ) {
	$media_data = Media_Data_Loader::load_media_data_bulk( $media_ids );
}
?>

<section id="services" class="services">
	<div class="services__container container">
		<h2 class="services__title scroll-fade"><?php echo esc_html( $attributes['title'] ); ?></h2>
		<div class="services__grid">
			<?php foreach ( $attributes['items'] as $item ) : ?>
				<?php
				$item_media_data = null;
				if ( ! empty( $item['icon'] ) && isset( $media_data[ $item['icon'] ] ) ) {
					$item_media_data = $media_data[ $item['icon'] ];
				}
				?>
				<div class="services__item scroll-fade">
					<?php if ( ! empty( $item['icon'] ) && $item_media_data ) : ?>
						<div class="services__icon">
							<?php
							Media_Display::display_media_item(
								$item_media_data,
								array(
									'class'  => 'services__icon-image',
									'size'   => 'medium',
									'width'  => 24,
									'height' => 24,
								)
							);
							?>
						</div>
					<?php endif; ?>
					
					<?php if ( ! empty( $item['title'] ) ) : ?>
						<h3 class="services__item-title"><?php echo esc_html( $item['title'] ); ?></h3>
					<?php endif; ?>
					
					<?php if ( ! empty( $item['text'] ) ) : ?>
						<p class="services__item-text"><?php echo esc_html( $item['text'] ); ?></p>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>