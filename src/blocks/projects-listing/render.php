<?php
use AMPortfolioTheme\Components\Project_Card;
use AMPortfolioTheme\Components\Project_Card_Data_Loader;

$project_cards_data = Project_Card_Data_Loader::load_project_cards_data( load_types: true );

$project_types = array();
$technologies  = array();

foreach ( $project_cards_data as $project ) {
	foreach ( $project->types as $project_type ) {
		$project_types[ $project_type['id'] ] = $project_type;
	}

	foreach ( $project->technologies as $project_technology ) {
		$technologies[ $project_technology['id'] ] = $project_technology;
	}
}

usort(
	$project_types,
	function ( $a, $b ) {
		return strcmp( $a['name'], $b['name'] );
	}
);

usort(
	$technologies,
	function ( $a, $b ) {
		return strcmp( $a['name'], $b['name'] );
	}
);

wp_interactivity_state(
	'projectsListing',
	array(
		'projects'         => $project_cards_data,
		'filteredProjects' => $project_cards_data,
		'isProjectVisible' => true,
		'hasResults'       => count( $project_cards_data ) > 0,
	)
);

?>
<section id="projects" class="projects-section" data-wp-interactive="projectsListing">
	<div 
		class="projects-section__filter container"
		data-wp-class--projects-section__filter--active="state.isFilterOpen"
		>
		<button class="projects-section__filter-toggle scroll-fade" data-wp-on--click="actions.toggleFilter">
			<span class="projects-section__filter-toggle-text"><?php echo esc_html( $attributes['filter_toggle_text'] ); ?></span>
			<svg class="projects-section__filter-toggle-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"><path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/><circle cx="7" cy="6" r="2" fill="currentColor"/><circle cx="17" cy="12" r="2" fill="currentColor"/><circle cx="12" cy="18" r="2" fill="currentColor"/></svg>
			<span class="projects-section__filter-badge" data-wp-bind--hidden="!state.hasActiveFilters" data-wp-text="state.activeFiltersCount"></span>
		</button>
		<div class="projects-section__filter-content">
			<div class="projects-section__filter-header">
				<h3 class="projects-section__filter-title"><?php echo esc_html( $attributes['filter_title'] ); ?></h3>
				<button class="projects-section__filter-close" data-wp-on--click="actions.toggleFilter" aria-label="Close filters">
					<svg width="14" height="14" fill="none"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 1 1 13M1 1l12 12"/></svg>
				</button>
			</div>
			<div class="projects-section__filter-body">
				<div class="projects-section__filter-content-inner">
					<div class="projects-section__filter-group">
						<h4 class="projects-section__filter-group-title"><?php echo esc_html( $attributes['project_type_title'] ); ?></h4>
						<p class="projects-section__filter-group-description"><?php echo esc_html( $attributes['project_type_description'] ); ?></p>
						<div class="projects-section__filter-options">
							<button 
								class="projects-section__filter-option" 
								type="button" 
								data-filter-value="all" 
								data-wp-on--click="actions.applyTypeFilter" 
								data-wp-class--projects-section__filter-option--active="state.isTypeFilterActive"
							>
								<span class="projects-section__filter-option-check">
									<svg width="12" height="12" fill="none"><circle cx="6" cy="6" r="5" stroke="currentColor" stroke-width="2"/></svg>
									<svg width="12" height="12" fill="none" class="projects-section__filter-option-check--active"><circle cx="6" cy="6" r="6" fill="currentColor"/><path stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m4 6 1.5 1.5 2.5-3"/></svg>
								</span>
								<span class="projects-section__filter-option-label"><?php echo esc_html( $attributes['all_types_label'] ); ?></span>
							</button>
							<?php foreach ( $project_types as $project_type ) : ?>
							<button 
								class="projects-section__filter-option" 
								type="button" 
								data-filter-value="<?php echo esc_attr( $project_type['id'] ); ?>" 
								data-wp-on--click="actions.applyTypeFilter" 
								data-wp-class--projects-section__filter-option--active="state.isTypeFilterActive"
							>
								<span class="projects-section__filter-option-check">
									<svg width="12" height="12" fill="none"><circle cx="6" cy="6" r="5" stroke="currentColor" stroke-width="2"/></svg>
									<svg width="12" height="12" fill="none" class="projects-section__filter-option-check--active"><circle cx="6" cy="6" r="6" fill="currentColor"/><path stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m4 6 1.5 1.5 2.5-3"/></svg>
								</span>
								<span class="projects-section__filter-option-label"><?php echo esc_html( $project_type['name'] ); ?></span>
							</button>
							<?php endforeach; ?>
						</div>
					</div>

					<div class="projects-section__filter-group">
						<div class="projects-section__filter-group-header">
							<h4 class="projects-section__filter-group-title"><?php echo esc_html( $attributes['technologies_title'] ); ?></h4>
							<button 
								class="projects-section__filter-clear" 
								type="button" 
								data-wp-on--click="actions.resetTechnologiesFilter" 
								data-wp-bind--hidden="!state.filters.technologies.length"
							>
								<?php echo esc_html( $attributes['clear_all_label'] ); ?>
							</button>
						</div>
						<p class="projects-section__filter-group-description"><?php echo esc_html( $attributes['technologies_description'] ); ?></p>
						<div class="projects-section__filter-options">
							<?php foreach ( $technologies as $tech ) : ?>
							<button 
								class="projects-section__filter-option" 
								type="button" 
								data-filter-value="<?php echo esc_attr( $tech['id'] ); ?>" 
								data-wp-on--click="actions.applyTechnologyFilter" 
								data-wp-class--projects-section__filter-option--active="state.isTechnologyFilterActive"
							>
								<span class="projects-section__filter-option-check">
									<svg width="12" height="12" fill="none"><rect width="11" height="11" x=".5" y=".5" stroke="currentColor" rx="1.5"/></svg>
									<svg width="12" height="12" fill="none" class="projects-section__filter-option-check--active"><rect width="12" height="12" fill="currentColor" rx="2"/><path stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m3 6 2 2 4-4"/></svg>
								</span>
								<span class="projects-section__filter-option-label"><?php echo esc_html( $tech['name'] ); ?></span>
							</button>
							<?php endforeach; ?>
						</div>
					</div>

					<div class="projects-section__filter-actions">
						<button class="projects-section__filter-apply" type="button" data-wp-on--click="actions.toggleFilter">
							<?php echo esc_html( $attributes['apply_filters_label'] ); ?>
						</button>
					</div>
				</div>
			</div>
		</div>
		<div class="projects-section__filter-overlay" data-wp-on--click="actions.toggleFilter"></div>
	</div>

	<div class="projects-section__container container scroll-fade">
		<div class="projects-section__grid">
			<?php
			foreach ( $project_cards_data as $project_card_data ) :
				Project_Card::display(
					$project_card_data,
					array(
						'data-project-id'      => $project_card_data->post_id,
						'data-wp-bind--hidden' => '!state.isProjectVisible',
					)
				);
			endforeach;
			?>
		</div>

		<div class="projects-section__no-results" data-wp-bind--hidden="state.hasResults">
			<svg class="projects-section__no-results-icon" width="64" height="64" fill="none"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M42.667 42.667 32 32m0 0L21.333 21.333M32 32l10.667-10.667M32 32 21.333 42.667"/><path stroke="currentColor" stroke-width="2" d="M52 32c0 10.493-8.507 19-19 19s-19-8.507-19-19 8.507-19 19-19 19 8.507 19 19Z"/></svg>
			<h3 class="projects-section__no-results-title"><?php echo esc_html( $attributes['no_results_title'] ); ?></h3>
			<p class="projects-section__no-results-description">
				<?php
				if ( ! empty( $attributes['no_results_description'] ) ) {
					$description = preg_replace_callback(
						'/\[reset button:\s*(.*?)\]/',
						function ( $matches ) {
							$button_text = esc_html( trim( $matches[1] ) );
							return '<button class="projects-section__no-results-reset" data-wp-on--click="actions.resetAllFilters">' . $button_text . '</button>';
						},
						$attributes['no_results_description']
					);
					echo wp_kses_post( $description );
				}
				?>
			</p>
		</div>
	</div>
</section>