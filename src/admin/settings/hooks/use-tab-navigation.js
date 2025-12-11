import { useState, useEffect, useCallback } from '@wordpress/element';

/**
 * Custom hook for managing tab navigation with URL synchronization.
 *
 * @param {string}        defaultTab - The default tab name to use when no tab is specified in the URL.
 * @param {Array<string>} validTabs  - Array of valid tab names for validation.
 * @return {Object} An object containing the current active tab and a function to change tabs.
 */
export function useTabNavigation(
	defaultTab = 'general',
	validTabs = [ 'general', 'recaptcha', 'contact', 'zoho-mail' ]
) {
	const [ activeTab, setActiveTab ] = useState( () => {
		// Get initial tab from URL parameters
		const urlParams = new URLSearchParams( window.location.search );
		const urlTab = urlParams.get( 'tab' );

		// Validate the tab from URL
		if ( urlTab && validTabs.includes( urlTab ) ) {
			return urlTab;
		}

		// Return default tab if URL tab is invalid or not present
		return defaultTab;
	} );

	/**
	 * Update the URL with the specified tab parameter.
	 *
	 * @param {string} tabName - The tab name to set in the URL.
	 */
	const updateUrl = useCallback(
		( tabName ) => {
			const url = new URL( window.location );

			if ( tabName === defaultTab ) {
				// Remove the tab parameter if it's the default tab
				url.searchParams.delete( 'tab' );
			} else {
				// Set the tab parameter
				url.searchParams.set( 'tab', tabName );
			}

			// Update the URL without reloading the page
			window.history.pushState( { tab: tabName }, '', url.toString() );
		},
		[ defaultTab ]
	);

	/**
	 * Change the active tab and update the URL.
	 *
	 * @param {string} tabName - The tab name to switch to.
	 */
	const changeTab = useCallback(
		( tabName ) => {
			// Validate the tab name
			if ( ! validTabs.includes( tabName ) ) {
				// eslint-disable-next-line no-console
				console.warn(
					`Invalid tab name: ${ tabName }. Falling back to default: ${ defaultTab }`
				);
				tabName = defaultTab;
			}

			if ( activeTab === tabName ) {
				return;
			}

			setActiveTab( tabName );
			updateUrl( tabName );
		},
		[ validTabs, defaultTab, updateUrl ]
	);

	/**
	 * Handle browser back/forward navigation.
	 */
	const handlePopState = useCallback(
		( event ) => {
			if ( event.state && event.state.tab ) {
				// Tab was stored in the state object
				const newTab = event.state.tab;
				if ( validTabs.includes( newTab ) ) {
					setActiveTab( newTab );
				} else {
					setActiveTab( defaultTab );
				}
			} else {
				// Extract tab from URL
				const urlParams = new URLSearchParams( window.location.search );
				const urlTab = urlParams.get( 'tab' );

				if ( urlTab && validTabs.includes( urlTab ) ) {
					setActiveTab( urlTab );
				} else {
					setActiveTab( defaultTab );
				}
			}
		},
		[ validTabs, defaultTab ]
	);

	// Set up event listener for browser back/forward navigation
	useEffect( () => {
		window.addEventListener( 'popstate', handlePopState );

		return () => {
			window.removeEventListener( 'popstate', handlePopState );
		};
	}, [ handlePopState ] );

	// Initialize URL on first render if needed
	useEffect( () => {
		const urlParams = new URLSearchParams( window.location.search );
		const urlTab = urlParams.get( 'tab' );

		// If URL doesn't have a tab parameter but we're not on the default tab,
		// update the URL to reflect the current tab
		if ( ! urlTab && activeTab !== defaultTab ) {
			updateUrl( activeTab );
		}
		// If URL has a tab parameter but it's different from current active tab,
		// update the active tab to match the URL (should only happen on direct navigation)
		else if (
			urlTab &&
			urlTab !== activeTab &&
			validTabs.includes( urlTab )
		) {
			setActiveTab( urlTab );
		}
	}, [ activeTab, defaultTab, validTabs, updateUrl ] );

	return {
		activeTab,
		changeTab,
	};
}
