<?php
$testimonials = $attributes['testimonials'] ?? array();

if ( empty( $testimonials ) ) {
	return;
}

$section_title = pll__( 'Client Feedback' );
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
					<?php if ( ! empty( $author ) ) : ?>
						<p class="project-testimonial__author"><?php echo esc_html( $author ); ?></p>
					<?php endif; ?>
					<?php if ( ! empty( $author_role ) ) : ?>
						<p class="project-testimonial__role"><?php echo esc_html( $author_role ); ?></p>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
