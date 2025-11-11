<?php
use AMPortfolioTheme\Helpers\Media_Data_Loader;
use AMPortfolioTheme\Helpers\Media_Display;

$heading          = $attributes['heading'] ?? '';
$description      = $attributes['description'] ?? '';
$button_text      = $attributes['button_text'] ?? '';
$profile_image_id = $attributes['profile_image'] ?? 0;

$profile_image_data = null;
if ( $profile_image_id ) {
	$profile_image_data = Media_Data_Loader::load_media_data_single( $profile_image_id );
}

?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'about-hero' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="about-hero__container container">
		<div class="about-hero__content">
			<?php if ( ! empty( $heading ) ) : ?>
				<h1 class="about-hero__heading scroll-fade"><?php echo esc_html( $heading ); ?></h1>
			<?php endif; ?>

			<?php if ( ! empty( $description ) ) : ?>
				<p class="about-hero__description scroll-fade"><?php echo esc_html( $description ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $button_text ) ) : ?>
				<button
					class="about-hero__button btn-primary scroll-fade"
					data-wp-interactive="contactFormModal"
					data-wp-on--click="actions.openModal"
				>
					<?php echo esc_html( $button_text ); ?>
				</button>
			<?php endif; ?>
		</div>

		<?php if ( $profile_image_data ) : ?>
			<div class="about-hero__image scroll-fade">
				<div class="about-hero__profile-pic">
						<?php
						Media_Display::display_media_item(
							$profile_image_data,
							array(
								'class' => 'about-hero__profile-image',
							)
						);
						?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>
