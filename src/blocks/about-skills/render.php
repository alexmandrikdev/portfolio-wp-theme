<?php
use AMPortfolioTheme\Helpers\Media_Data_Loader;
use AMPortfolioTheme\Helpers\Media_Display;

$heading          = $attributes['heading'] ?? '';
$skill_categories = $attributes['skill_categories'] ?? array();

$media_ids  = array_filter( array_column( $skill_categories, 'icon' ) );
$media_data = array();
if ( ! empty( $media_ids ) ) {
	$media_data = Media_Data_Loader::load_media_data_bulk( $media_ids );
}
?>

<section <?php echo get_block_wrapper_attributes( array( 'class' => 'about-skills' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="about-skills__container container">
		<?php if ( ! empty( $heading ) ) : ?>
			<h2 class="about-skills__heading scroll-fade">
				<?php echo esc_html( $heading ); ?>
			</h2>
		<?php endif; ?>

		<?php if ( ! empty( $skill_categories ) ) : ?>
			<div class="about-skills__grid">
				<?php foreach ( $skill_categories as $category ) : ?>
					<?php
					$category_media_data = null;
					if ( ! empty( $category['icon'] ) && isset( $media_data[ $category['icon'] ] ) ) {
						$category_media_data = $media_data[ $category['icon'] ];
					}
					?>
					<div class="about-skills__category scroll-fade">
						<div class="about-skills__icon">
							<?php
								Media_Display::display_media_item(
									$category_media_data,
									array(
										'class'  => 'about-skills__icon-image',
										'size'   => 'medium',
										'width'  => 24,
										'height' => 24,
									)
								);
							?>
						</div>
						
						<?php if ( ! empty( $category['title'] ) ) : ?>
							<h3 class="about-skills__title">
								<?php echo esc_html( $category['title'] ); ?>
							</h3>
						<?php endif; ?>
						
						<?php if ( ! empty( $category['skills'] ) ) : ?>
							<ul class="about-skills__list">
								<?php foreach ( $category['skills'] as $skill ) : ?>
									<?php if ( ! empty( $skill ) ) : ?>
										<li class="about-skills__list-item">
											<?php echo esc_html( $skill ); ?>
										</li>
									<?php endif; ?>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
