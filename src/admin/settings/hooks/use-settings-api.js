import { useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

export function useSettingsAPI() {
	const [ settings, setSettings ] = useState(
		window.portfolioSettings.settings || {}
	);
	const [ loading, setLoading ] = useState( false );
	const [ error, setError ] = useState( null );

	const fetchSettings = async () => {
		setLoading( true );
		setError( null );

		try {
			const response = await apiFetch( {
				path: window.portfolioSettings.api.path,
				method: 'GET',
			} );

			if ( response.success ) {
				setSettings( response.settings );
			} else {
				throw new Error( 'Failed to fetch settings' );
			}
		} catch ( err ) {
			setError(
				err.message || 'An error occurred while fetching settings'
			);
		} finally {
			setLoading( false );
		}
	};

	const saveSettings = async ( partialSettings = {} ) => {
		setError( null );
		setLoading( true );

		try {
			const updatedSettings = {
				...settings,
				...partialSettings,
			};

			const response = await apiFetch( {
				path: window.portfolioSettings.api.path,
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
				},
				body: JSON.stringify( updatedSettings ),
			} );

			if ( response.success ) {
				setSettings( response.settings );
				return response;
			}
			throw new Error( response.message || 'Failed to save settings' );
		} catch ( err ) {
			const message =
				err.message || 'An error occurred while saving settings';
			setError( message );
			throw new Error( message );
		} finally {
			setLoading( false );
		}
	};

	return {
		settings,
		loading,
		error,
		saveSettings,
		refetch: fetchSettings,
	};
}
