<?php
$personal_name        = $attributes['personal_name'] ?? '';
$personal_tagline     = $attributes['personal_tagline'] ?? '';
$personal_description = $attributes['personal_description'] ?? '';
$quick_links_title    = $attributes['quick_links_title'] ?? '';
$quick_links          = $attributes['quick_links'] ?? array();
$social_media_title   = $attributes['social_media_title'] ?? '';
$contact_title        = $attributes['contact_title'] ?? '';
$contact_text         = $attributes['contact_text'] ?? '';
$contact_cta_text     = $attributes['contact_cta_text'] ?? '';
$copyright_text       = $attributes['copyright_text'] ?? '';
$footer_note          = $attributes['footer_note'] ?? '';

// Replace [year] placeholder with current year.
$copyright_text = str_replace( '[year]', gmdate( 'Y' ), $copyright_text );

// Get social links from theme settings.
$settings     = get_option( 'portfolio_theme_settings', array() );
$github_url   = $settings['github_url'] ?? '';
$linkedin_url = $settings['linkedin_url'] ?? '';

$social_platforms = array();

if ( $github_url ) {
	$social_platforms[] = array(
		'name' => 'GitHub',
		'url'  => $github_url,
		'icon' => 'github',
	);
}

if ( $linkedin_url ) {
	$social_platforms[] = array(
		'name' => 'LinkedIn',
		'url'  => $linkedin_url,
		'icon' => 'linkedin',
	);
}
?>
<footer <?php echo get_block_wrapper_attributes( array( 'class' => 'footer' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="footer__content">
		<div class="footer__sections container">
			<!-- Personal Information Section -->
			<div class="footer__section">
				<?php if ( ! empty( $personal_name ) ) : ?>
					<h3 class="footer__title"><?php echo esc_html( $personal_name ); ?></h3>
				<?php endif; ?>
				
				<?php if ( ! empty( $personal_tagline ) ) : ?>
					<p class="footer__tagline"><?php echo esc_html( $personal_tagline ); ?></p>
				<?php endif; ?>
				
				<?php if ( ! empty( $personal_description ) ) : ?>
					<div class="footer__description">
						<?php echo wp_kses_post( $personal_description ); ?>
					</div>
				<?php endif; ?>
			</div>

			<!-- Quick Links Section -->
			<?php if ( ! empty( $quick_links ) ) : ?>
				<div class="footer__section">
					<?php if ( ! empty( $quick_links_title ) ) : ?>
						<h4 class="footer__subtitle"><?php echo esc_html( $quick_links_title ); ?></h4>
					<?php endif; ?>
					
					<ul class="footer__links">
						<?php foreach ( $quick_links as $quick_link ) : ?>
							<?php
							$page_id = $quick_link['page_id'] ?? 0;
							if ( $page_id ) :
								$translated_post_id     = pll_get_post( $page_id );
								$translated_post_status = get_post_status( $translated_post_id );
								?>
								<?php if ( $translated_post_id && 'publish' === $translated_post_status ) : ?>
									<li class="footer__link-item">
										<a
											class="footer__link"
											href="<?php echo esc_url( get_permalink( $translated_post_id ) ); ?>"
										>
											<?php echo esc_html( get_the_title( $translated_post_id ) ); ?>
										</a>
									</li>
								<?php endif; ?>
							<?php endif; ?>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<!-- Social Media Section -->
			<?php if ( ! empty( $social_platforms ) ) : ?>
				<div class="footer__section">
					<?php if ( ! empty( $social_media_title ) ) : ?>
						<h4 class="footer__subtitle"><?php echo esc_html( $social_media_title ); ?></h4>
					<?php endif; ?>
					
					<ul class="footer__social">
						<?php foreach ( $social_platforms as $platform ) : ?>
							<li>
								<a
									href="<?php echo esc_url( $platform['url'] ); ?>"
									class="footer__social-link"
									target="_blank"
									rel="noopener noreferrer"
									aria-label="<?php echo esc_html( $platform['name'] ); ?>"
								>
									<?php echo esc_html( $platform['name'] ); ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<!-- Contact Section -->
			<div class="footer__section">
				<?php if ( ! empty( $contact_title ) ) : ?>
					<h4 class="footer__subtitle"><?php echo esc_html( $contact_title ); ?></h4>
				<?php endif; ?>
				
				<div class="footer__contact">
					<?php if ( ! empty( $contact_text ) ) : ?>
						<div class="footer__contact-text">
							<?php echo wp_kses_post( $contact_text ); ?>
						</div>
					<?php endif; ?>
					
					<?php if ( ! empty( $contact_cta_text ) ) : ?>
						<button 
							class="footer__cta btn-primary"
							data-wp-interactive="contactFormModal"
							data-wp-on--click="actions.openModal"
						>
							<?php echo esc_html( $contact_cta_text ); ?>
						</button>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<!-- Bottom Section -->
		<div class="footer__bottom">
			<div class="container">
				<?php if ( ! empty( $copyright_text ) ) : ?>
					<div class="footer__copyright">
						<?php echo wp_kses_post( $copyright_text ); ?>
					</div>
				<?php endif; ?>
				
				<?php if ( ! empty( $footer_note ) ) : ?>
					<div class="footer__note">
						<?php echo wp_kses_post( $footer_note ); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</footer>