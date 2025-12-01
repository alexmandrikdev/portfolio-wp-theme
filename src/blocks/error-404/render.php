<section <?php echo get_block_wrapper_attributes( array( 'class' => 'error-404' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="error-404__container container">
		<div class="error-404__content scroll-fade">
			<div class="error-404__code">
				404
			</div>
			
			<h1 class="error-404__title">
				<?php pll_esc_html_e( 'Error 404 - Title' ); ?>
			</h1>
			
			<p class="error-404__message">
				<?php pll_esc_html_e( 'Error 404 - Message' ); ?>
			</p>
			
			<div class="error-404__actions">
				<a
					href="<?php echo esc_url( pll_home_url() ); ?>"
					class="btn-primary error-404__button"
				>
					<?php pll_esc_html_e( 'Error 404 - Home Button' ); ?>
				</a>
			</div>
		</div>
	</div>
</section>
