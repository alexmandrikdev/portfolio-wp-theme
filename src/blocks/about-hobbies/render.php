<?php
use AMPortfolioTheme\Helpers\Media_Data_Loader;
use AMPortfolioTheme\Helpers\Media_Display;

$heading = $attributes['heading'] ?? '';
$hobbies = $attributes['hobbies'] ?? array();

$media_ids  = array_filter( array_column( $hobbies, 'icon' ) );
$media_data = array();
if ( ! empty( $media_ids ) ) {
	$media_data = Media_Data_Loader::load_media_data_bulk( $media_ids );
}
?>

<section <?php echo get_block_wrapper_attributes( array( 'class' => 'about-hobbies' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="about-hobbies__container container">
		<?php if ( ! empty( $heading ) ) : ?>
			<h2 class="about-hobbies__heading scroll-fade">
				<?php echo esc_html( $heading ); ?>
			</h2>
		<?php endif; ?>

		<?php if ( ! empty( $hobbies ) ) : ?>
			<div class="about-hobbies__grid">
				<?php foreach ( $hobbies as $hobby ) : ?>
					<?php
					$hobby_media_data = null;
					if ( ! empty( $hobby['icon'] ) && isset( $media_data[ $hobby['icon'] ] ) ) {
						$hobby_media_data = $media_data[ $hobby['icon'] ];
					}
					?>
					<div class="about-hobbies__card scroll-fade">
						<div class="about-hobbies__icon">
							<?php
							Media_Display::display_media_item(
								$hobby_media_data,
								array(
									'class'  => 'about-hobbies__icon-image',
									'size'   => 'medium',
									'width'  => 32,
									'height' => 32,
								)
							);
							?>
						</div>
						
						<?php if ( ! empty( $hobby['title'] ) ) : ?>
							<h3 class="about-hobbies__title">
								<?php echo esc_html( $hobby['title'] ); ?>
							</h3>
						<?php endif; ?>
						
						<?php if ( ! empty( $hobby['description'] ) ) : ?>
							<p class="about-hobbies__description">
								<?php echo esc_html( $hobby['description'] ); ?>
							</p>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
