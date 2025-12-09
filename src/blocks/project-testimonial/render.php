<?php
use AMPortfolioTheme\Helpers\Media_Data_Loader;
use AMPortfolioTheme\Helpers\Media_Display;

$testimonials = $attributes['testimonials'] ?? array();

if ( empty( $testimonials ) ) {
	return;
}

$section_title = pll__( 'Client Feedback' );

// Collect all profile picture media IDs for bulk loading.
$profile_picture_ids = array();
foreach ( $testimonials as $testimonial ) {
	$media_id = $testimonial['profile_picture'] ?? 0;
	if ( $media_id ) {
		$profile_picture_ids[] = $media_id;
	}
}

$media_data = array();
if ( ! empty( $profile_picture_ids ) ) {
	$media_data = Media_Data_Loader::load_media_data_bulk( $profile_picture_ids );
}
?>

<section <?php echo get_block_wrapper_attributes( array( 'class' => 'project-testimonial' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="project-testimonial__container container">
		<h2 class="project-testimonial__title scroll-fade"><?php echo esc_html( $section_title ); ?></h2>
		
		<div class="project-testimonial__cards scroll-fade">
			<?php foreach ( $testimonials as $testimonial ) : ?>
				<?php
				$quote       = $testimonial['quote'] ?? '';
				$author      = $testimonial['author'] ?? '';
				$author_role = $testimonial['role'] ?? '';

				if ( empty( $quote ) ) {
					continue;
				}
				?>
				<div class="project-testimonial__card">
					<div class="project-testimonial__quote-icon">"</div>
					<p class="project-testimonial__quote">"<?php echo wp_kses_post( $quote ); ?>"</p>
					
					<?php
					$profile_picture_id   = $testimonial['profile_picture'] ?? 0;
					$social_media_link    = $testimonial['social_media_link'] ?? '';
					$profile_picture_data = $profile_picture_id && isset( $media_data[ $profile_picture_id ] ) ? $media_data[ $profile_picture_id ] : null;
					$has_social_link      = ! empty( $social_media_link );
					?>

					<?php if ( $has_social_link ) : ?>
						<a href="<?php echo esc_url( $social_media_link ); ?>" class="project-testimonial__author-info project-testimonial__author-info--linked" target="_blank" rel="noopener noreferrer">
					<?php else : ?>
						<div class="project-testimonial__author-info">
					<?php endif; ?>

						<?php if ( $profile_picture_data ) : ?>
							<div class="project-testimonial__profile-picture">
								<?php
								Media_Display::display_media_item(
									$profile_picture_data,
									array(
										'size'  => 'thumbnail',
										'class' => 'project-testimonial__profile-picture-image',
									)
								);
								?>
							</div>
						<?php endif; ?>
						
						<div class="project-testimonial__author-details">
							<?php if ( ! empty( $author ) ) : ?>
								<div class="project-testimonial__author"><?php echo esc_html( $author ); ?></div>
							<?php endif; ?>
							<?php if ( ! empty( $author_role ) ) : ?>
								<div class="project-testimonial__role"><?php echo esc_html( $author_role ); ?></div>
							<?php endif; ?>
						</div>

					<?php if ( $has_social_link ) : ?>
						</a>
					<?php else : ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
