import { __ } from '@wordpress/i18n';
import { useState, useEffect } from '@wordpress/element';
import { Notice, Spinner, Card, CardBody } from '@wordpress/components';
import { TechnologyList } from './technology-list';
import { useTechnologyAPI } from '../hooks/use-technology-api';

export function TechnologyOrderApp() {
	const { technologies, loading, error, saveOrder } = useTechnologyAPI();
	const [ localTechnologies, setLocalTechnologies ] = useState( [] );
	const [ saveStatus, setSaveStatus ] = useState( {
		type: null,
		message: '',
	} );

	// Initialize local technologies when data loads
	useEffect( () => {
		if ( technologies.length > 0 ) {
			setLocalTechnologies( technologies );
		}
	}, [ technologies ] );

	const handleOrderChange = ( newOrder ) => {
		setLocalTechnologies( newOrder );
	};

	const handleSave = async () => {
		setSaveStatus( {
			type: 'info',
			message: __( 'Saving order…', 'am-portfolio-theme' ),
		} );

		try {
			await saveOrder( localTechnologies );
			setSaveStatus( {
				type: 'success',
				message: __(
					'Order saved successfully!',
					'am-portfolio-theme'
				),
			} );

			// Clear success message after 3 seconds
			setTimeout( () => {
				setSaveStatus( { type: null, message: '' } );
			}, 3000 );
		} catch ( err ) {
			setSaveStatus( {
				type: 'error',
				message:
					err.message ||
					__(
						'An error occurred while saving. Please try again.',
						'am-portfolio-theme'
					),
			} );
		}
	};

	if ( loading ) {
		return (
			<div className="portfolio-technology-order-loading">
				<Spinner />
				<p>{ __( 'Loading technologies…', 'am-portfolio-theme' ) }</p>
			</div>
		);
	}

	if ( error ) {
		return (
			<Notice status="error" isDismissible={ false }>
				{ error }
			</Notice>
		);
	}

	return (
		<div className="portfolio-technology-order-app">
			{ saveStatus.type && (
				<Notice status={ saveStatus.type } isDismissible={ false }>
					{ saveStatus.message }
				</Notice>
			) }

			<Card>
				<CardBody>
					<div className="portfolio-technology-order-header">
						<h2>
							{ __( 'Technology Order', 'am-portfolio-theme' ) }
						</h2>
						<p className="description">
							{ __(
								'Drag and drop technologies to reorder them.',
								'am-portfolio-theme'
							) }
						</p>
					</div>

					{ localTechnologies.length === 0 ? (
						<Notice status="warning" isDismissible={ false }>
							{ __(
								'No technologies found. Add some technologies first.',
								'am-portfolio-theme'
							) }
						</Notice>
					) : (
						<>
							<TechnologyList
								technologies={ localTechnologies }
								onOrderChange={ handleOrderChange }
							/>

							<div
								className="portfolio-technology-order-actions"
								style={ { marginTop: '1rem' } }
							>
								<button
									type="button"
									className="button button-primary"
									onClick={ handleSave }
									disabled={ saveStatus.type === 'info' }
								>
									{ saveStatus.type === 'info' ? (
										<>
											<Spinner />
											{ __(
												'Saving…',
												'am-portfolio-theme'
											) }
										</>
									) : (
										__( 'Save Order', 'am-portfolio-theme' )
									) }
								</button>

								<p
									className="description"
									style={ { marginTop: '0.5rem' } }
								>
									{ __(
										'Drag technologies to reorder, then click "Save Order" to apply changes.',
										'am-portfolio-theme'
									) }
								</p>
							</div>
						</>
					) }
				</CardBody>
			</Card>
		</div>
	);
}
