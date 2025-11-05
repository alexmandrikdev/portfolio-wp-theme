<?php

use AMPortfolioTheme\Helpers\Media_Data_Loader;
use AMPortfolioTheme\Helpers\Media_Display;

$screenshots = $attributes['screenshots'] ?? array();

$section_title = pll__( 'The Final Product' );

$featured_image_id = get_post_thumbnail_id();

?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'project-results' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="project-results__container container">
		<h2 class="project-results__title scroll-fade"><?php echo esc_html( $section_title ); ?></h2>
		
		<?php if ( $featured_image_id ) : ?>
			<div class="project-results__featured-screenshot scroll-fade">
				<div class="project-results__browser-frame">
					<div class="project-results__browser-dots">
						<div class="project-results__browser-dot project-results__browser-dot--red"></div>
						<div class="project-results__browser-dot project-results__browser-dot--yellow"></div>
						<div class="project-results__browser-dot project-results__browser-dot--green"></div>
					</div>
				</div>
				<div class="project-results__featured-image">
					<?php
					$featured_media_data = Media_Data_Loader::load_media_data_single( $featured_image_id );
					Media_Display::display_media_item(
						$featured_media_data,
						array(
							'size' => 'full',
						)
					);
					?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $screenshots ) ) : ?>
			<div class="project-results__screenshots-grid  scroll-fade">
				<?php
				$screenshot_media_ids = array();
				foreach ( $screenshots as $screenshot ) {
					$media_id = $screenshot['media_id'] ?? 0;
					if ( $media_id ) {
						$screenshot_media_ids[] = $media_id;
					}
				}

				$screenshot_media_data = array();
				if ( ! empty( $screenshot_media_ids ) ) {
					$screenshot_media_data = Media_Data_Loader::load_media_data_bulk( $screenshot_media_ids );
				}

				foreach ( $screenshots as $screenshot ) :
					$media_id         = $screenshot['media_id'] ?? 0;
					$screenshot_title = $screenshot['title'] ?? '';

					if ( $media_id && isset( $screenshot_media_data[ $media_id ] ) ) :
						?>
						<div class="project-results__screenshot-item">
							<div class="project-results__screenshot-image">
								<?php
								Media_Display::display_media_item(
									$screenshot_media_data[ $media_id ],
									array(
										'size'  => 'large',
										'sizes' => '(max-width: 767px) 100vw, 50vw',
									)
								);
								?>
							</div>
							<?php if ( $screenshot_title ) : ?>
								<div class="project-results__screenshot-caption"><?php echo esc_html( $screenshot_title ); ?></div>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
