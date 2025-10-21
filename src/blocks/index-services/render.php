<?php
if ( empty( $attributes['title'] ) || empty( $attributes['items'] ) ) {
	return;
}
?>

<section id="services" class="services">
	<div class="services__container container">
		<h2 class="services__title scroll-fade"><?php echo esc_html( $attributes['title'] ); ?></h2>
		<div class="services__grid">
			<?php foreach ( $attributes['items'] as $item ) : ?>
				<div class="services__item scroll-fade">
					<?php if ( ! empty( $item['icon'] ) ) : ?>
						<div class="services__icon"><?php echo esc_html( $item['icon'] ); ?></div>
					<?php endif; ?>
					
					<?php if ( ! empty( $item['title'] ) ) : ?>
						<h3 class="services__item-title"><?php echo esc_html( $item['title'] ); ?></h3>
					<?php endif; ?>
					
					<?php if ( ! empty( $item['text'] ) ) : ?>
						<p class="services__item-text"><?php echo esc_html( $item['text'] ); ?></p>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>