<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<a href="<?php echo esc_url( $data->permalink ); ?>" 
	class="project-card <?php echo esc_attr( $attributes['class'] ); ?>"
	<?php
	foreach ( $attributes as $key => $value ) {
		if ( 'class' === $key ) {
			continue;
		}

		echo esc_attr( $key ) . '="' . esc_attr( $value ) . '" ';
	}
	?>
>
	<div class="project-card__image">
		<?php if ( ! empty( $data->thumbnail['url'] ) ) : ?>
			<img
				src="<?php echo esc_url( $data->thumbnail['url'] ); ?>"
				alt="<?php echo esc_attr( $data->thumbnail['alt'] ); ?>"
				class="project-card__img"
			/>
		<?php endif; ?>
		<div class="project-card__overlay">
			<span class="btn-primary">View Project</span>
		</div>
	</div>
	<div class="project-card__content">
		<?php if ( ! empty( $data->types ) ) : ?>
			<div class="project-card__types">
				<?php foreach ( $data->types as $item ) : ?>
					<span class="project-card__type">
						<?php echo esc_html( $item['name'] ); ?>
					</span>
				<?php endforeach; ?>
			</div>
		<?php endif ?>

		<h3 class="project-card__title"><?php echo esc_html( $data->title ); ?></h3>
		<p class="project-card__excerpt"><?php echo esc_html( $data->excerpt ); ?></p>
		
		<?php if ( ! empty( $data->technologies ) ) : ?>
			<div class="project-card__tags">
				<?php foreach ( $data->technologies as $technology ) : ?>
					<span class="project-card__tag">
						<?php echo esc_html( $technology['name'] ); ?>
					</span>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	  
		<span class="btn-secondary">View Project</span>
	</div>
</a>