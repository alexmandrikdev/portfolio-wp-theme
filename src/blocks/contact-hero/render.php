<?php
$block_title       = $attributes['title'] ?? '';
$block_description = $attributes['description'] ?? '';
$block_cta_text    = $attributes['cta_text'] ?? '';
?>

<div <?php echo get_block_wrapper_attributes( array( 'class' => 'contact-hero' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="container">
		<?php if ( $block_title ) : ?>
			<h1 class="contact-hero__title"><?php echo esc_html( $block_title ); ?></h1>
		<?php endif; ?>

		<?php if ( $block_description ) : ?>
			<p class="contact-hero__description"><?php echo esc_html( $block_description ); ?></p>
		<?php endif; ?>

		<?php if ( $block_cta_text ) : ?>
			<button
				class="btn-primary"
				data-wp-interactive="contactFormModal"
				data-wp-on--click="actions.openModal"
			>
				<?php echo esc_html( $block_cta_text ); ?>
			</button>
		<?php endif; ?>
	</div>
</div>
