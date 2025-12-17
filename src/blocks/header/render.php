<?php
	$menu_items = $attributes['menu_items'] ?? array();
	$cta_text   = $attributes['cta_text'] ?? 'Get a Free Project Estimate';
?>

<div 
	<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo get_block_wrapper_attributes();
	?>
	data-wp-interactive="header"
	data-wp-watch="callbacks.bodyScrollLock"
	<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo wp_interactivity_data_wp_context(
		array(
			'isOpen' => false,
		)
	);
	?>
>
	<div class="wp-block-portfolio-header__container">
		<div class="wp-block-portfolio-header__inner">
			<a class="wp-block-portfolio-header__logo" href="<?php echo esc_url( pll_home_url() ); ?>">Alex Mándrik</a>

			<button 
				class="wp-block-portfolio-header__menu-button"
				data-wp-on--click="actions.toggleMenu"
			>
				<span data-wp-bind--hidden="context.isOpen">
					<svg 				
						width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M1 4H19" stroke="currentColor" stroke-width="2"/>
						<path d="M1 10H19" stroke="currentColor" stroke-width="2"/>
						<path d="M1 16H19" stroke="currentColor" stroke-width="2"/>
					</svg>
				</span>

				<span data-wp-bind--hidden="!context.isOpen">
					<svg 
						width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M1 1L19 19" stroke="currentColor" stroke-width="2"/>
						<path d="M19 1L1 19" stroke="currentColor" stroke-width="2"/>
					</svg>
				</span>
			</button>
		</div>

		<div 
			class="wp-block-portfolio-header__menu"
			data-wp-class--wp-block-portfolio-header__menu--open="context.isOpen"
		>
			<div class="wp-block-portfolio-header__menu-items">
				<?php foreach ( $menu_items as $menu_item ) : ?>
					<?php
					$page_id                = $menu_item['page_id'] ?? 0;
					$translated_post_id     = pll_get_post( $page_id );
					$translated_post_status = get_post_status( $translated_post_id );
					?>
					<?php if ( $translated_post_id && 'publish' === $translated_post_status ) : ?>
						<a
							class="wp-block-portfolio-header__menu-item"
							href="<?php echo esc_url( get_permalink( $translated_post_id ) ); ?>"
						>
							<?php echo esc_html( get_the_title( $translated_post_id ) ); ?>
						</a>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>

			<div class="wp-block-portfolio-header__actions">
				<button
					data-wp-on--click="actions.openContactFormModal"
					class="btn-primary"><?php echo esc_html( $cta_text ); ?></button>

				<div class="wp-block-portfolio-header__languages">
					<?php foreach ( pll_the_languages( array( 'raw' => 1 ) ) as $code => $details ) : ?>
						<?php
						if ( pll_current_language() === $code ) {
							continue;
						}
						?>
						<a 
							class="wp-block-portfolio-header__language"
							href="<?php echo esc_url( $details['url'] ); ?>"
						>
							<img 
								src="<?php echo esc_url( $details['flag'] ); ?>" 
								alt="<?php echo esc_attr( $details['name'] ); ?>"
								width="20"
								height="14"
							>	
						</a>
					<?php endforeach; ?>
				</div>

				<div 
					class="wp-block-portfolio-header__theme-switcher"	
					data-wp-init="callbacks.initThemeSwitcher"
				>
					<!-- Light theme -->
					<button 
						class="wp-block-portfolio-header__theme-switcher-button"
						data-wp-on--click="actions.switchToTargetTheme" 
						data-target-theme="light" 
						data-wp-class--wp-block-portfolio-header__theme-switcher-button--active="state.isLightTheme"
					>
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"><circle cx="12" cy="12" r="5" stroke="currentColor" stroke-width="2"/><path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M12 2v2M12 20v2M4 12H2M22 12h-2M19.07 4.93l-1.41 1.41M6.34 17.66l-1.41 1.41M19.07 19.07l-1.41-1.41M6.34 6.34 4.93 4.93"/></svg>
					</button>
					
					<!-- Dark theme -->
					<button 
						class="wp-block-portfolio-header__theme-switcher-button"
						data-wp-on--click="actions.switchToTargetTheme" 
						data-target-theme="dark" 
						data-wp-class--wp-block-portfolio-header__theme-switcher-button--active="state.isDarkTheme"
					>
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z"/></svg>
					</button>
					
					<!-- Auto theme -->
					<button 
						class="wp-block-portfolio-header__theme-switcher-button"
						data-wp-on--click="actions.switchToTargetTheme" 
						data-target-theme="auto" 
						data-wp-class--wp-block-portfolio-header__theme-switcher-button--active="state.isAutoTheme"
					>
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"><rect width="14" height="11" x="5" y="4" stroke="currentColor" stroke-width="1.5" rx="1"/><path stroke="currentColor" stroke-linecap="round" stroke-width="1.5" d="M8 15h8M7 18h10"/><circle cx="12" cy="9.5" r="1.5" fill="currentColor"/></svg>
					</button>
				</div>
			</div>
		</div>
	</div>
</div> 

<div class="wp-block-portfolio-header__spacer"></div>
