<?php
use AMPortfolioTheme\Components\Project_Card;
use AMPortfolioTheme\Components\Project_Card_Data_Loader;

?>
<section id="projects" class="projects-section">
	<div class="projects-section__container container">
		<h2 class="projects-section__title scroll-fade">
			<?php echo esc_html( $attributes['title'] ); ?>
		</h2>

		<?php
		$projects_data = Project_Card_Data_Loader::load_project_cards_data( limit: 3 );

		$post_count = count( $projects_data );
		$grid_class = 'projects-section__grid' . ( 0 !== $post_count % 2 ? ' projects-section__grid--odd' : '' );
		?>

		<div class="<?php echo esc_attr( $grid_class ); ?>">
			<?php
			foreach ( $projects_data as $project_data ) :
				Project_Card::display( $project_data, array( 'class' => 'scroll-fade' ) );
			endforeach;
			?>
		</div>
	</div>
</section>