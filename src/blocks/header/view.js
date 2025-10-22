import { store, getContext, getElement } from '@wordpress/interactivity';

const { actions, state } = store( 'header', {
	state: {
		theme: 'auto',

		get isLightTheme() {
			return state.theme === 'light';
		},

		get isDarkTheme() {
			return state.theme === 'dark';
		},

		get isAutoTheme() {
			return state.theme === 'auto';
		},
	},
	actions: {
		toggleMenu: () => {
			const context = getContext();
			context.isOpen = ! context.isOpen;
		},

		switchTheme: ( theme ) => {
			state.theme = theme;
			let visualTheme = theme;

			window.localStorage.setItem( 'theme', theme );

			if ( theme === 'auto' ) {
				visualTheme = window.matchMedia(
					'(prefers-color-scheme: dark)'
				).matches
					? 'dark'
					: 'light';
			}

			actions.applyTheme( visualTheme );
		},

		switchToTargetTheme: () => {
			const { attributes } = getElement();

			const theme = attributes[ 'data-target-theme' ];

			actions.switchTheme( theme );
		},

		applyTheme: ( theme ) => {
			if ( theme === 'dark' ) {
				document.documentElement.setAttribute( 'data-theme', 'dark' );
			} else {
				document.documentElement.removeAttribute( 'data-theme' );
			}
		},
	},
	callbacks: {
		initThemeSwitcher: () => {
			const theme = window.localStorage.getItem( 'theme' ) || 'auto';

			actions.switchTheme( theme );

			window
				.matchMedia( '(prefers-color-scheme: dark)' )
				.addEventListener( 'change', ( e ) => {
					if ( state.theme === 'auto' ) {
						actions.applyTheme( e.matches ? 'dark' : 'light' );
					}
				} );
		},
	},
} );
