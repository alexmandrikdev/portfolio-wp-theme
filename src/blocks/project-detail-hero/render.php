<?php
/**
 * PHP template for the Project Detail Hero block.
 *
 * @package portfolio
 */

$title_html   = get_the_title();
$excerpt_html = get_the_excerpt();

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'project-detail-hero' ) );
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
			<a href="<?php echo esc_url( $back_to_portfolio_link ); ?>" class="btn-text project-detail-hero__back-link scroll-fade">
				<svg xmlns="http://www.w3.org/2000/svg" width="14" height="16" fill="currentColor" viewBox="0 0 512 512"><path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.3 288H480c17.7 0 32-14.3 32-32s-14.3-32-32-32H109.3l105.4-105.4c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/></svg>
				<?php esc_html_e( 'Vissza a portfólióhoz', 'am-portfolio-theme' ); ?>
			</a>
		<?php endif; ?>

		<h1 class="project-detail-hero__title scroll-fade"><?php echo esc_html( $title_html ); ?></h1>
		<p class="project-detail-hero__tagline scroll-fade">
			<?php echo esc_html( $excerpt_html ); ?>
		</p>

		<div class="project-detail-hero__meta-items">
			<?php
			if ( ! empty( $attributes['meta_items'] ) ) {
				foreach ( $attributes['meta_items'] as $item ) {
					?>
					<div class="project-detail-hero__meta-item scroll-fade">
						<span class="project-detail-hero__meta-label"><?php echo esc_html( $item['label'] ); ?></span>
						<span class="project-detail-hero__meta-value"><?php echo esc_html( $item['value'] ); ?></span>
					</div>
					<?php
				}
			}
			?>
		</div>

		<?php if ( ! empty( $attributes['live_project_url'] ) ) : ?>
		<div class="project-detail-hero__actions">
			<a
				href="<?php echo esc_url( $attributes['live_project_url'] ); ?>"
				target="_blank"
				rel="noopener noreferrer"
				class="btn-primary project-detail-hero__action-button scroll-fade"
			>
				<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 512 512" fill="currentColor"><path d="M432 320h-32a16 16 0 0 0-16 16v112H64V128h144a16 16 0 0 0 16-16V80a16 16 0 0 0-16-16H48a48 48 0 0 0-48 48v352a48 48 0 0 0 48 48h352a48 48 0 0 0 48-48V336a16 16 0 0 0-16-16ZM488 0H360c-21.37 0-32.05 25.91-17 41l35.73 35.73L135 320.37a24 24 0 0 0 0 34L157.67 377a24 24 0 0 0 34 0l243.61-243.68L471 169c15 15 41 4.5 41-17V24a24 24 0 0 0-24-24Z"/></svg>
				<?php esc_html_e( 'Megtekintem az élő projektet', 'am-portfolio-theme' ); ?>
			</a>
		</div>
		<?php endif; ?>
	</div>
</section>