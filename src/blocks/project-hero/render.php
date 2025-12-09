<?php
$title_html   = get_the_title();
$excerpt_html = get_the_excerpt();

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'project-hero' ) );
?>

<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block_wrapper_attributes already escaped. ?>>
	<div class="container">
		<?php
		$settings                  = \AMPortfolioTheme\Admin\Settings_Helper::get_current_settings();
		$projects_listing_page_ids = $settings['projects_listing_page_ids'];

		$current_language = function_exists( 'pll_current_language' ) ? pll_current_language() : 'default';

		$back_to_portfolio_link = '';
		if ( isset( $projects_listing_page_ids[ $current_language ] ) && $projects_listing_page_ids[ $current_language ] ) {
			$back_to_portfolio_link = get_permalink( $projects_listing_page_ids[ $current_language ] );
		}

		if ( ! empty( $back_to_portfolio_link ) ) :
			?>
			<a href="<?php echo esc_url( $back_to_portfolio_link ); ?>" class="btn-text project-hero__back-link scroll-fade">
				<svg xmlns="http://www.w3.org/2000/svg" width="14" height="16" fill="currentColor" viewBox="0 0 512 512"><path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.3 288H480c17.7 0 32-14.3 32-32s-14.3-32-32-32H109.3l105.4-105.4c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/></svg>
				<?php pll_esc_html_e( 'Back to Portfolio' ); ?>
			</a>
		<?php endif; ?>

		<h1 class="project-hero__title scroll-fade"><?php echo esc_html( $title_html ); ?></h1>
		<p class="project-hero__tagline scroll-fade">
			<?php echo esc_html( $excerpt_html ); ?>
		</p>

		<div class="project-hero__meta-items">
			<?php
			if ( ! empty( $attributes['meta_items'] ) ) {
				foreach ( $attributes['meta_items'] as $item ) {
					?>
					<div class="project-hero__meta-item scroll-fade">
						<span class="project-hero__meta-label"><?php echo esc_html( $item['label'] ); ?></span>
						<span class="project-hero__meta-value"><?php echo esc_html( $item['value'] ); ?></span>
					</div>
					<?php
				}
			}
			?>
		</div>

		<?php
		$has_live_url   = ! empty( $attributes['live_project_url'] );
		$has_source_url = ! empty( $attributes['source_code_url'] );
		?>
		
		<?php if ( $has_live_url || $has_source_url ) : ?>
		<div class="project-hero__actions">
			<?php if ( $has_live_url ) : ?>
				<a
					href="<?php echo esc_url( $attributes['live_project_url'] ); ?>"
					target="_blank"
					rel="noopener noreferrer"
					class="btn-primary project-hero__action-button scroll-fade"
				>
					<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 512 512" fill="currentColor"><path d="M432 320h-32a16 16 0 0 0-16 16v112H64V128h144a16 16 0 0 0 16-16V80a16 16 0 0 0-16-16H48a48 48 0 0 0-48 48v352a48 48 0 0 0 48 48h352a48 48 0 0 0 48-48V336a16 16 0 0 0-16-16ZM488 0H360c-21.37 0-32.05 25.91-17 41l35.73 35.73L135 320.37a24 24 0 0 0 0 34L157.67 377a24 24 0 0 0 34 0l243.61-243.68L471 169c15 15 41 4.5 41-17V24a24 24 0 0 0-24-24Z"/></svg>
					<?php pll_esc_html_e( 'View Live Project' ); ?>
				</a>
			<?php endif; ?>
			
			<?php if ( $has_source_url ) : ?>
				<a
					href="<?php echo esc_url( $attributes['source_code_url'] ); ?>"
					target="_blank"
					rel="noopener noreferrer"
					class="btn-secondary project-hero__action-button scroll-fade"
				>
					<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 512 512" fill="currentColor"><path d="M173.9 397.4c0 2-2.3 3.6-5.2 3.6-3.3.3-5.6-1.3-5.6-3.6 0-2 2.3-3.6 5.2-3.6 3-.3 5.6 1.3 5.6 3.6zm-31.1-4.5c-.7 2 1.3 4.3 4.3 4.9 2.6 1 5.6 0 6.2-2s-1.3-4.3-4.3-5.2c-2.6-.7-5.5.3-6.2 2.3zm44.2-1.7c-2.9.7-4.9 2.6-4.6 4.9.3 2 2.9 3.3 5.9 2.6 2.9-.7 4.9-2.6 4.6-4.6-.3-1.9-3-3.2-5.9-2.9zM252.8 8C114.1 8 8 113.3 8 252c0 110.9 69.8 205.8 169.5 239.2 12.8 2.3 17.3-5.6 17.3-12.1 0-6.2-.3-40.4-.3-61.4 0 0-70 15-84.7-29.8 0 0-11.4-29.1-27.8-36.6 0 0-22.9-15.7 1.6-15.4 0 0 24.9 2 38.6 25.8 21.9 38.6 58.6 27.5 72.9 20.9 2.3-16 8.8-27.1 16-33.7-55.9-6.2-112.3-14.3-112.3-110.5 0-27.5 7.6-41.3 23.6-58.9-2.6-6.5-11.1-33.3 2.6-67.9 20.9-6.5 69 27 69 27 20-5.6 41.5-8.5 62.8-8.5s42.8 2.9 62.8 8.5c0 0 48.1-33.6 69-27 13.7 34.7 5.2 61.4 2.6 67.9 16 17.7 25.8 31.5 25.8 58.9 0 96.5-58.9 104.2-114.8 110.5 9.2 7.9 17 22.9 17 46.4 0 33.7-.3 75.4-.3 83.6 0 6.5 4.6 14.4 17.3 12.1C436.2 457.8 504 362.9 504 252 504 113.3 391.5 8 252.8 8zM105.2 352.9c-1.3 1-1 3.3.7 5.2 1.6 1.6 3.9 2.3 5.2 1 1.3-1 1-3.3-.7-5.2-1.6-1.6-3.9-2.3-5.2-1zm-10.8-8.1c-.7 1.3.3 2.9 2.3 3.9 1.6 1 3.6.7 4.3-.7.7-1.3-.3-2.9-2.3-3.9-2-.6-3.6-.3-4.3.7zm32.4 35.6c-1.6 1.3-1 4.3 1.3 6.2 2.3 2.3 5.2 2.6 6.5 1 1.3-1.3.7-4.3-1.3-6.2-2.2-2.3-5.2-2.6-6.5-1zm-11.4-14.7c-1.6 1-1.6 3.6 0 5.9s4.3 3.3 5.6 2.3c1.6-1.3 1.6-3.9 0-6.2-1.4-2.3-4-3.3-5.6-2z"/></svg>
					<?php pll_esc_html_e( 'View Source Code' ); ?>
				</a>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	</div>
</section>