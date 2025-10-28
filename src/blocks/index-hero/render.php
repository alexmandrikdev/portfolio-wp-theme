<section class="hero">
	<div class="hero__container container">
		<div class="hero__content scroll-fade">
			<?php if ( ! empty( $attributes['title'] ) ) : ?>
				<h1 class="hero__title">
					<?php echo esc_html( $attributes['title'] ); ?>
				</h1>
			<?php endif; ?>
			
			<?php if ( ! empty( $attributes['subtitle'] ) ) : ?>
				<p class="hero__subtitle">
					<?php echo esc_html( $attributes['subtitle'] ); ?>
				</p>
			<?php endif; ?>
			
			<div class="hero__cta">
				<button 
					class="btn-primary"
					data-wp-interactive="contactFormModal"
					data-wp-on--click="actions.openModal"
				>
					Get a Free Project Estimate
				</button>
			</div>
			
			<?php if ( ! empty( $attributes['cta_note'] ) ) : ?>
				<p class="hero__note">
					<?php echo esc_html( $attributes['cta_note'] ); ?>
				</p>
			<?php endif; ?>
		</div>

		<div class="hero__visual scroll-fade">
			<div class="code-visual">
				<div class="code-visual__header">
					<div class="code-visual__dot code-visual__dot--red"></div>
					<div class="code-visual__dot code-visual__dot--yellow"></div>
					<div class="code-visual__dot code-visual__dot--green"></div>
				</div>
				<div class="code-visual__content">
					<div class="code-visual__line">
						<span class="code-visual__accent">function</span> createAmazingWeb() {
					</div>
					<div class="code-visual__line code-visual__line--indent-1">
						<span class="code-visual__accent">const</span> performance = <span class="code-visual__accent">optimize</span>();
					</div>
					<div class="code-visual__line code-visual__line--indent-1">
						<span class="code-visual__accent">const</span> design = <span class="code-visual__accent">craftPixelPerfect</span>();
					</div>
					<div class="code-visual__line code-visual__line--indent-1">
						<span class="code-visual__accent">return</span> { performance, design };
					</div>
					<div class="code-visual__line">}</div>
					<div class="code-visual__line">&nbsp;</div>
					<div class="code-visual__line">
						<span class="code-visual__accent">const</span> yourProject = createAmazingWeb();
					</div>
				</div>
			</div>
		</div>
	</div>
</section>