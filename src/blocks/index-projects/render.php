<?php
use AMPortfolioTheme\Components\Project_Card;

?>
<section id="projects" class="projects-section">
	<div class="projects-section__container container">
		<h2 class="projects-section__title scroll-fade">
			<?php echo esc_html( $attributes['title'] ); ?>
		</h2>

		<?php
		$post_count = count( $attributes['post_ids'] );
		$grid_class = 'projects-section__grid' . ( 0 !== $post_count % 2 ? ' projects-section__grid--odd' : '' );
		?>

		<div class="<?php echo esc_attr( $grid_class ); ?>">
			<?php
			foreach ( $attributes['post_ids'] as $item ) :
				Project_Card::display( $item['post_id'], array( 'class' => 'scroll-fade' ) );
			endforeach;
			?>
		</div>
	</div>
</section>