import { store, getContext } from '@wordpress/interactivity';

const { state } = store( 'faq', {
	state: {
		openIndex: null,

		get isOpen() {
			const { index } = getContext();

			return state.openIndex === index;
		},
	},
	actions: {
		toggleItem: () => {
			const { index } = getContext();

			if ( state.openIndex === index ) {
				state.openIndex = null;
			} else {
				state.openIndex = index;
			}
		},
	},
} );
