import { store, getElement } from '@wordpress/interactivity';

const { state, actions } = store( 'projectsListing', {
	state: {
		initialScrollPosition: 0,

		isFilterOpen: false,

		filters: {
			type: 'all',
			technologies: [],
		},

		get hasActiveFilters() {
			return state.activeFiltersCount > 0;
		},

		get activeFiltersCount() {
			let count = state.filters.technologies.length;

			if ( state.filters.type !== 'all' ) {
				count++;
			}

			return count;
		},

		get hasResults() {
			return Object.keys( state.filteredProjects ).length > 0;
		},

		get isTypeFilterActive() {
			const { attributes } = getElement();

			const filterValue = attributes[ 'data-filter-value' ];

			return state.filters.type === filterValue;
		},

		get isTechnologyFilterActive() {
			const { attributes } = getElement();

			const filterValue = attributes[ 'data-filter-value' ];

			return state.filters.technologies.includes( filterValue );
		},

		get isProjectVisible() {
			const { attributes } = getElement();

			const projectId = attributes[ 'data-project-id' ];

			return Object.hasOwn( state.filteredProjects, projectId );
		},
	},
	actions: {
		toggleFilter() {
			state.isFilterOpen = ! state.isFilterOpen;

			if ( state.isFilterOpen ) {
				state.initialScrollPosition = window.scrollY;
			} else {
				actions.applyFilters();
				window.scrollTo( 0, state.initialScrollPosition );
			}
		},

		applyTypeFilter() {
			const { attributes } = getElement();

			const filterValue = attributes[ 'data-filter-value' ];

			if ( state.filters.type === filterValue ) {
				state.filters.type = 'all';
			} else {
				state.filters.type = filterValue;
			}
		},

		applyTechnologyFilter() {
			const { attributes } = getElement();

			const filterValue = attributes[ 'data-filter-value' ];

			if ( state.filters.technologies.includes( filterValue ) ) {
				const index = state.filters.technologies.indexOf( filterValue );
				state.filters.technologies.splice( index, 1 );
				return;
			}

			state.filters.technologies.push( filterValue );
		},

		resetTechnologiesFilter() {
			state.filters.technologies = [];
		},

		resetAllFilters() {
			state.filters.type = 'all';
			state.filters.technologies = [];

			actions.applyFilters();
		},

		applyFilters() {
			const newFilteredProjects = {};
			Object.values( state.projects ).forEach( ( project ) => {
				if (
					state.filters.type !== 'all' &&
					Object.hasOwn( project.types, state.filters.type ) === false
				) {
					return false;
				}

				if (
					state.filters.technologies.length > 0 &&
					state.filters.technologies.some(
						( technology ) =>
							! Object.hasOwn( project.technologies, technology )
					)
				) {
					return false;
				}

				newFilteredProjects[ project.post_id ] = project;
			} );

			state.filteredProjects = newFilteredProjects;
		},
	},
} );
