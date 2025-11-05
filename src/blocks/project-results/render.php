<?php

$screenshots = $attributes['screenshots'] ?? array();

$section_title = pll__( 'The Final Product' );

$featured_image_id  = get_post_thumbnail_id();
$featured_image_url = $featured_image_id ? wp_get_attachment_image_url( $featured_image_id, 'full' ) : '';
$featured_image_alt = $featured_image_id ? get_post_meta( $featured_image_id, '_wp_attachment_image_alt', true ) : '';

?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'project-results' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="project-results__container container">
		<h2 class="project-results__title scroll-fade"><?php echo esc_html( $section_title ); ?></h2>
		
		<?php if ( $featured_image_url ) : ?>
			<div class="project-results__featured-screenshot scroll-fade">
				<div class="project-results__browser-frame">
					<div class="project-results__browser-dots">
						<div class="project-results__browser-dot project-results__browser-dot--red"></div>
						<div class="project-results__browser-dot project-results__browser-dot--yellow"></div>
						<div class="project-results__browser-dot project-results__browser-dot--green"></div>
					</div>
				</div>
				<div class="project-results__featured-image">
					<img
						src="<?php echo esc_url( $featured_image_url ); ?>"
						alt="<?php echo esc_attr( $featured_image_alt ? $featured_image_alt : get_the_title() ); ?>"
						loading="lazy"
					>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $screenshots ) ) : ?>
			<div class="project-results__screenshots-grid  scroll-fade">
				<?php
				foreach ( $screenshots as $screenshot ) :
					$media_id         = $screenshot['media_id'] ?? 0;
					$screenshot_title = $screenshot['title'] ?? '';

					if ( $media_id ) :
						$image_url = wp_get_attachment_image_url( $media_id, 'large' );
						$image_alt = get_post_meta( $media_id, '_wp_attachment_image_alt', true );
						?>
						<div class="project-results__screenshot-item">
							<div class="project-results__screenshot-image">
								<img
									src="<?php echo esc_url( $image_url ); ?>"
									alt="<?php echo esc_attr( $image_alt ? $image_alt : $screenshot_title ); ?>"
									loading="lazy"
								>
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
