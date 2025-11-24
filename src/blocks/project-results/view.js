import { store, getContext } from '@wordpress/interactivity';

const { state } = store( 'projectResults', {
	state: {
		get isVisible() {
			const { index } = getContext();
			const { loadedItems } = state;

			return index < loadedItems;
		},
		get showLoadMore() {
			const { loadedItems } = state;
			const screenshotPairs = document.querySelectorAll(
				'.project-results__screenshot-pair'
			);
			return loadedItems < screenshotPairs.length;
		},
	},
	actions: {
		loadMore: () => {
			state.loadedItems += 3;
		},
	},
} );
