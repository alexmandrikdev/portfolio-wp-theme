<?php
use AMPortfolioTheme\Helpers\Media_Data_Loader;
use AMPortfolioTheme\Helpers\Media_Display;

$challenges = $attributes['challenges'] ?? array();

$section_title    = pll__( 'Technical Implementation' );
$tech_stack_title = pll__( 'Tech Stack' );
$challenges_title = pll__( 'Key Challenges & Solutions' );
$solution_title   = pll__( 'Solution:' );

$technologies = get_the_terms( get_the_ID(), 'project_technology' );

if ( $technologies && ! is_wp_error( $technologies ) ) {
	usort(
		$technologies,
		function ( $a, $b ) {
			$a_order = get_term_meta( $a->term_id, 'technology_order', true );
			$b_order = get_term_meta( $b->term_id, 'technology_order', true );

			$a_order = $a_order ? intval( $a_order ) : 0;
			$b_order = $b_order ? intval( $b_order ) : 0;

			// First compare by order.
			if ( $a_order !== $b_order ) {
				return $a_order - $b_order;
			}

			// If order is the same, compare by name.
			return strcmp( $a->name, $b->name );
		}
	);
}

if ( $technologies && ! is_wp_error( $technologies ) ) {
	$term_ids = wp_list_pluck( $technologies, 'term_id' );
	update_termmeta_cache( $term_ids );
}

$tech_icon_ids = array();
if ( $technologies && ! is_wp_error( $technologies ) ) {
	foreach ( $technologies as $technology ) {
		$icon_id = get_term_meta( $technology->term_id, 'technology_icon', true );
		if ( $icon_id ) {
			$tech_icon_ids[] = $icon_id;
		}
	}
}

$challenge_media_ids = array();
foreach ( $challenges as $challenge ) {
	$media_id = $challenge['icon'] ?? 0;
	if ( $media_id ) {
		$challenge_media_ids[] = $media_id;
	}
}

$all_media_ids  = array_merge( $challenge_media_ids, $tech_icon_ids );
$all_media_data = array();
if ( ! empty( $all_media_ids ) ) {
	$all_media_data = Media_Data_Loader::load_media_data_bulk( $all_media_ids );
}

?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'project-tech-details' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="project-tech-details__container container">
		<h2 class="project-tech-details__title scroll-fade"><?php echo esc_html( $section_title ); ?></h2>
		
		<div class="project-tech-details__tech-stack scroll-fade">
			<h3><?php echo esc_html( $tech_stack_title ); ?></h3>
			
			<?php if ( $technologies && ! is_wp_error( $technologies ) ) : ?>
				<div class="project-tech-details__tech-items">
					<?php foreach ( $technologies as $technology ) : ?>
						<?php
						$icon_id   = get_term_meta( $technology->term_id, 'technology_icon', true );
						$icon_data = $icon_id && isset( $all_media_data[ $icon_id ] ) ? $all_media_data[ $icon_id ] : null;
						?>
						<div class="project-tech-details__tech-item">
							<?php if ( $icon_data ) : ?>
								<div class="project-tech-details__tech-icon">
									<?php
									Media_Display::display_media_item(
										$icon_data,
										array(
											'size'  => 'small',
											'class' => 'project-tech-details__tech-icon-image',
										)
									);
									?>
								</div>
							<?php endif; ?>
							<span class="project-tech-details__tech-name"><?php echo esc_html( $technology->name ); ?></span>
						</div>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<p class="project-tech-details__no-tech"><?php esc_html_e( 'No technologies specified for this project.', 'am-portfolio-theme' ); ?></p>
			<?php endif; ?>
		</div>

		<div class="project-tech-details__challenges scroll-fade">
			<h3><?php echo esc_html( $challenges_title ); ?></h3>
			
			<?php if ( ! empty( $challenges ) ) : ?>
				<div class="project-tech-details__challenges-list">
					<?php foreach ( $challenges as $challenge ) : ?>
						<?php
						$icon_id         = $challenge['icon'] ?? 0;
						$challenge_title = $challenge['title'] ?? '';
						$description     = $challenge['description'] ?? '';
						$solution        = $challenge['solution'] ?? '';

						if ( empty( $challenge_title ) && empty( $description ) ) {
							continue;
						}
						?>
						<div class="project-tech-details__challenge-item">
							<div class="project-tech-details__challenge-header">
								<?php if ( $icon_id && isset( $all_media_data[ $icon_id ] ) ) : ?>
									<div class="project-tech-details__challenge-icon">
										<?php
										Media_Display::display_media_item(
											$all_media_data[ $icon_id ],
											array(
												'size'  => 'small',
												'class' => 'project-tech-details__challenge-icon-image',
											)
										);
										?>
									</div>
								<?php endif; ?>
								
								<?php if ( ! empty( $challenge_title ) ) : ?>
									<h4 class="project-tech-details__challenge-title"><?php echo esc_html( $challenge_title ); ?></h4>
								<?php endif; ?>
							</div>
							
							<?php if ( ! empty( $description ) ) : ?>
								<div class="project-tech-details__challenge-description">
									<?php echo wp_kses_post( $description ); ?>
								</div>
							<?php endif; ?>
							
							<?php if ( ! empty( $solution ) ) : ?>
								<div class="project-tech-details__challenge-solution">
									<strong class="project-tech-details__challenge-solution-heading"><?php echo esc_html( $solution_title ); ?></strong>
									<?php echo wp_kses_post( $solution ); ?>
								</div>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<p class="project-tech-details__no-challenges"><?php esc_html_e( 'No challenges specified for this project.', 'am-portfolio-theme' ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</section>
