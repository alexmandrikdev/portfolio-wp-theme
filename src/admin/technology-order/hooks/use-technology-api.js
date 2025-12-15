import { useState, useEffect } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

export function useTechnologyAPI() {
	const [ technologies, setTechnologies ] = useState( [] );
	const [ loading, setLoading ] = useState( true );
	const [ error, setError ] = useState( null );

	const fetchTechnologies = async () => {
		setLoading( true );
		setError( null );

		try {
			const response = await apiFetch( {
				path: '/portfolio/v1/technologies',
				method: 'GET',
			} );

			if ( response.success ) {
				setTechnologies( response.technologies );
			} else {
				setError( response.message || 'Failed to fetch technologies' );
			}
		} catch ( err ) {
			setError(
				err.message || 'An error occurred while fetching technologies'
			);
		} finally {
			setLoading( false );
		}
	};

	const saveOrder = async ( technologiesArray ) => {
		try {
			const response = await apiFetch( {
				path: '/portfolio/v1/technologies',
				method: 'POST',
				data: {
					technologies: technologiesArray.map( ( tech ) => ( {
						id: tech.id,
						order: tech.order,
					} ) ),
				},
			} );

			if ( ! response.success ) {
				setError( response.message || 'Failed to save order' );
			}

			// Update local state with new order values
			const updatedTechnologies = technologiesArray.map(
				( tech, index ) => ( {
					...tech,
					order: index,
				} )
			);
			setTechnologies( updatedTechnologies );

			return response;
		} catch ( err ) {
			setError( err.message || 'An error occurred while saving order' );
		}
	};

	useEffect( () => {
		fetchTechnologies();
	}, [] );

	return {
		technologies,
		loading,
		error,
		saveOrder,
		refresh: fetchTechnologies,
	};
}
