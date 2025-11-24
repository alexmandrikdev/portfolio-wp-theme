<?php

use AMPortfolioTheme\Helpers\Media_Data_Loader;
use AMPortfolioTheme\Helpers\Media_Display;

$screenshots = $attributes['screenshots'] ?? array();

$section_title = pll__( 'The Final Product' );

wp_interactivity_state(
	'projectResults',
	array(
		'loadedItems' => 3,
		'isVisible'   => function () {
			$state = wp_interactivity_state();
			$context = wp_interactivity_get_context();

			return $context['index'] < $state['loadedItems'];
		},
	)
);
?>
<section 
	<?php echo get_block_wrapper_attributes( array( 'class' => 'project-results' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	data-wp-interactive="projectResults"
>
	<div class="project-results__container container">
		<h2 class="project-results__title scroll-fade"><?php echo esc_html( $section_title ); ?></h2>

		<?php if ( ! empty( $screenshots ) ) : ?>
			<div
				class="project-results__screenshots-grid scroll-fade"
			>
				<?php
				$screenshot_media_ids = array();
				foreach ( $screenshots as $screenshot ) {
					$desktop_media_id = $screenshot['desktop_screenshot_id'] ?? 0;
					$mobile_media_id  = $screenshot['mobile_screenshot_id'] ?? 0;

					if ( $desktop_media_id ) {
						$screenshot_media_ids[] = $desktop_media_id;
					}
					if ( $mobile_media_id ) {
						$screenshot_media_ids[] = $mobile_media_id;
					}
				}

				$screenshot_media_data = array();
				if ( ! empty( $screenshot_media_ids ) ) {
					$screenshot_media_data = Media_Data_Loader::load_media_data_bulk( $screenshot_media_ids );
				}

				foreach ( $screenshots as $index => $screenshot ) :
					$screenshot_title = $screenshot['title'] ?? '';
					$desktop_media_id = $screenshot['desktop_screenshot_id'] ?? 0;
					$mobile_media_id  = $screenshot['mobile_screenshot_id'] ?? 0;

					$has_desktop = $desktop_media_id && isset( $screenshot_media_data[ $desktop_media_id ] );
					$has_mobile  = $mobile_media_id && isset( $screenshot_media_data[ $mobile_media_id ] );

					if ( $has_desktop || $has_mobile ) :
						?>
						<div
							class="project-results__screenshot-pair scroll-fade scroll-fade--no-delay"
							data-wp-context='{ "index": <?php echo esc_attr( $index ); ?> }'
							data-wp-bind--hidden="!state.isVisible"
						>
							<?php if ( $screenshot_title ) : ?>
								<h3 class="project-results__pair-title"><?php echo esc_html( $screenshot_title ); ?></h3>
							<?php endif; ?>
							
							<div class="project-results__pair-content">
								<?php if ( $has_desktop ) : ?>
									<div class="project-results__desktop-screenshot">
										<div class="project-results__device-frame project-results__device-frame--desktop">
											<img 
												src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/desktop-frame.webp' ); ?>" 
												alt="Desktop Frame" 
												class="project-results__frame-image"
												width="800"
												height="489"
												loading="lazy"
											>
											<div class="project-results__screenshot-content">
												<?php
												Media_Display::display_media_item(
													$screenshot_media_data[ $desktop_media_id ],
													array(
														'size' => 'large',
														'sizes' => '(max-width: 767px) 100vw, 80vw',
													)
												);
												?>
											</div>
										</div>
									</div>
								<?php endif; ?>
								
								<?php if ( $has_mobile ) : ?>
									<div class="project-results__mobile-screenshot">
										<div class="project-results__device-frame project-results__device-frame--mobile">
											<img 
												src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/mobile-frame.webp' ); ?>" 
												alt="Mobile Frame"
												class="project-results__frame-image"
												width="267"
												height="489"
												loading="lazy"
											>
											<div class="project-results__screenshot-content">
												<?php
												Media_Display::display_media_item(
													$screenshot_media_data[ $mobile_media_id ],
													array(
														'size' => 'large',
														'sizes' => '(max-width: 767px) 100vw, 20vw',
													)
												);
												?>
											</div>
										</div>
									</div>
								<?php endif; ?>
							</div>
						</div>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>

			<div 
				class="project-results__load-more-container" 					
				data-wp-bind--hidden="!state.showLoadMore"
			>
				<button
					class="btn-primary"
					data-wp-on--click="actions.loadMore"
				>
					<?php pll_esc_html_e( 'Load More' ); ?>
				</button>
			</div>
		<?php endif; ?>
	</div>
</section>
