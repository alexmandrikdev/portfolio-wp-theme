<?php

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'project-overview',
	)
);

$task_card_items     = $attributes['task_card_items'] ?? array();
$solution_card_items = $attributes['solution_card_items'] ?? array();

?>

<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block_wrapper_attributes already escaped. ?>>
	<div class="container">
		<div class="project-overview__grid">
			<div class="project-overview__card scroll-fade">
				<h3 class="project-overview__header">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" width="18" height="24" fill="currentColor"><path d="M311.4 32l8.6 0c35.3 0 64 28.7 64 64l0 352c0 35.3-28.7 64-64 64L64 512c-35.3 0-64-28.7-64-64L0 96C0 60.7 28.7 32 64 32l8.6 0C83.6 12.9 104.3 0 128 0L256 0c23.7 0 44.4 12.9 55.4 32zM248 112c13.3 0 24-10.7 24-24s-10.7-24-24-24L136 64c-13.3 0-24 10.7-24 24s10.7 24 24 24l112 0zM128 256a32 32 0 1 0 -64 0 32 32 0 1 0 64 0zm32 0c0 13.3 10.7 24 24 24l112 0c13.3 0 24-10.7 24-24s-10.7-24-24-24l-112 0c-13.3 0-24 10.7-24 24zm0 128c0 13.3 10.7 24 24 24l112 0c13.3 0 24-10.7 24-24s-10.7-24-24-24l-112 0c-13.3 0-24 10.7-24 24zM96 416a32 32 0 1 0 0-64 32 32 0 1 0 0 64z"/></svg>
					<?php echo esc_html( pll__( 'The Brief', 'am-portfolio-theme' ) ); ?>
				</h3>
				<ul class="project-overview__list">
					<?php foreach ( $task_card_items as $item ) : ?>
						<li class="project-overview__item"><?php echo esc_html( $item['text'] ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>

			<div class="project-overview__card scroll-fade">
				<h3 class="project-overview__header">

					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" width="18" height="24" fill="currentColor"><path d="M292.9 384c7.3-22.3 21.9-42.5 38.4-59.9 32.7-34.4 52.7-80.9 52.7-132.1 0-106-86-192-192-192S0 86 0 192c0 51.2 20 97.7 52.7 132.1 16.5 17.4 31.2 37.6 38.4 59.9l201.7 0zM288 432l-192 0 0 16c0 44.2 35.8 80 80 80l32 0c44.2 0 80-35.8 80-80l0-16zM184 112c-39.8 0-72 32.2-72 72 0 13.3-10.7 24-24 24s-24-10.7-24-24c0-66.3 53.7-120 120-120 13.3 0 24 10.7 24 24s-10.7 24-24 24z"/></svg>
					<?php echo esc_html( pll__( 'The Solution', 'am-portfolio-theme' ) ); ?>
				</h3>
				<ul class="project-overview__list">
					<?php foreach ( $solution_card_items as $item ) : ?>
						<li class="project-overview__item"><?php echo esc_html( $item['text'] ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</div>
</section>
