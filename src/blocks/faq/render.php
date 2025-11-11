<?php
$section_title = $attributes['title'] ?? '';
$faq_items     = $attributes['items'] ?? array();

if ( empty( $section_title ) && empty( $faq_items ) ) {
	return '';
}
?>

<section <?php echo get_block_wrapper_attributes( array( 'class' => 'faq' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="faq__container container">
		<?php if ( ! empty( $section_title ) ) : ?>
			<h2 class="faq__title scroll-fade"><?php echo esc_html( $section_title ); ?></h2>
		<?php endif; ?>

		<?php if ( ! empty( $faq_items ) ) : ?>
			<div class="faq__grid scroll-fade" data-wp-interactive="faq">
				<?php foreach ( $faq_items as $index => $item ) : ?>
					<?php
					$question = $item['question'] ?? '';
					$answer   = $item['answer'] ?? '';

					if ( empty( $question ) && empty( $answer ) ) {
						continue;
					}
					?>

					<div 
						class="faq__item" 
						<?php echo wp_interactivity_data_wp_context( array( 'index' => $index ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						data-wp-class--faq__item--active="state.isOpen"
					>
						<button 
							class="faq__question" 
							data-wp-on--click="actions.toggleItem"
							data-wp-bind--aria-expanded="state.isOpen"
							aria-controls="faq-answer-<?php echo (int) $index; ?>"
						>
							<span class="faq__question-text"><?php echo esc_html( $question ); ?></span>
							<span class="faq__toggle" data-wp-class--faq__toggle--active="state.isOpen">
								<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M4 6L8 10L12 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
							</span>
						</button>

						<div 
							class="faq__answer" 
							id="faq-answer-<?php echo (int) $index; ?>"
							data-wp-class--faq__answer--active="state.isOpen"
							data-wp-bind--aria-hidden="!state.isOpen"
							role="region"
						>
							<div class="faq__answer-content">
								<?php echo wp_kses_post( wpautop( $answer ) ); ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
