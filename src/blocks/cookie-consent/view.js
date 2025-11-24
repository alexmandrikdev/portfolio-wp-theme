import { store } from '@wordpress/interactivity';

const { state, actions } = store( 'cookieConsent', {
	state: {
		isAnimating: false,
		hasInteracted: false,
	},
	callbacks: {
		initConsent() {
			const consentData = actions.getConsentData();

			if ( consentData ) {
				state.consentGiven = true;
				state.analyticsConsent = consentData.analytics;
				state.isVisible = false;

				actions.updateGoogleAnalyticsConsent( consentData.analytics );
			} else {
				setTimeout( () => {
					state.isVisible = true;
				}, 500 );
			}
		},

		checkConsentStatus() {
			const consentData = actions.getConsentData();

			if ( consentData && ! state.consentGiven ) {
				state.consentGiven = true;
				state.analyticsConsent = consentData.analytics;
				state.isVisible = false;
				actions.updateGoogleAnalyticsConsent( consentData.analytics );
			}
		},
	},
	actions: {
		acceptAll() {
			if ( state.isAnimating ) {
				return;
			}

			state.isAnimating = true;
			state.hasInteracted = true;

			const consentData = {
				analytics: true,
				necessary: true,
				timestamp: Date.now(),
				expiry: state.expiryDays * 24 * 60 * 60 * 1000, // Convert days to milliseconds
			};

			actions.saveConsentData( consentData );
			state.consentGiven = true;
			state.analyticsConsent = true;
			state.showCustomize = false;

			setTimeout( () => {
				state.isVisible = false;
				state.isAnimating = false;
			}, 300 );

			actions.updateGoogleAnalyticsConsent( true );
		},

		rejectAll() {
			if ( state.isAnimating ) {
				return;
			}

			state.isAnimating = true;
			state.hasInteracted = true;

			const consentData = {
				analytics: false,
				necessary: true, // Always true for necessary cookies
				timestamp: Date.now(),
				expiry: state.expiryDays * 24 * 60 * 60 * 1000,
			};

			actions.saveConsentData( consentData );
			state.consentGiven = true;
			state.analyticsConsent = false;
			state.showCustomize = false;

			setTimeout( () => {
				state.isVisible = false;
				state.isAnimating = false;
			}, 300 );

			actions.updateGoogleAnalyticsConsent( false );
		},

		showCustomize() {
			if ( state.isAnimating ) {
				return;
			}

			state.isAnimating = true;
			state.hasInteracted = true;
			state.showCustomize = true;

			setTimeout( () => {
				state.isAnimating = false;
			}, 300 );
		},

		closeCustomize() {
			if ( state.isAnimating ) {
				return;
			}

			state.isAnimating = true;
			state.showCustomize = false;

			setTimeout( () => {
				state.isAnimating = false;
			}, 300 );
		},

		toggleAnalytics( event ) {
			state.analyticsConsent = event.target.checked;
			state.hasInteracted = true;
		},

		savePreferences() {
			if ( state.isAnimating ) {
				return;
			}

			state.isAnimating = true;

			const consentData = {
				analytics: state.analyticsConsent,
				necessary: true, // Always true for necessary cookies
				timestamp: Date.now(),
				expiry: state.expiryDays * 24 * 60 * 60 * 1000,
			};

			actions.saveConsentData( consentData );
			state.consentGiven = true;
			state.showCustomize = false;

			setTimeout( () => {
				state.isVisible = false;
				state.isAnimating = false;
			}, 300 );

			actions.updateGoogleAnalyticsConsent( state.analyticsConsent );
		},

		getConsentData() {
			try {
				const stored = window.localStorage.getItem(
					'portfolio_cookie_consent'
				);
				if ( ! stored ) {
					return null;
				}

				const consentData = JSON.parse( stored );

				const now = Date.now();
				if ( now - consentData.timestamp > consentData.expiry ) {
					window.localStorage.removeItem(
						'portfolio_cookie_consent'
					);
					return null;
				}

				return consentData;
			} catch ( error ) {
				return null;
			}
		},

		saveConsentData( consentData ) {
			try {
				window.localStorage.setItem(
					'portfolio_cookie_consent',
					JSON.stringify( consentData )
				);
			} catch ( error ) {
				// Silently fail if localStorage is not available
			}
		},

		updateGoogleAnalyticsConsent( analyticsEnabled ) {
			if ( typeof window.gtag === 'function' ) {
				const consentState = {
					analytics_storage: analyticsEnabled ? 'granted' : 'denied',
					ad_storage: 'denied', // We don't use advertising cookies
					ad_user_data: 'denied',
					ad_personalization: 'denied',
				};

				window.gtag( 'consent', 'update', consentState );
			}
		},

		open() {
			if ( state.isAnimating ) {
				return;
			}

			state.isAnimating = true;
			state.isVisible = true;
			state.showCustomize = false;

			setTimeout( () => {
				state.isAnimating = false;
			}, 300 );
		},
	},
} );
