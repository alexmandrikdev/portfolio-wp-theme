import { useState, useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import {
	SelectControl,
	Button,
	Spinner,
	Notice,
	Card,
	CardBody,
} from '@wordpress/components';

export function GeneralSettings( { settings, onSave } ) {
	const [ localSettings, setLocalSettings ] = useState( settings || {} );
	const [ saving, setSaving ] = useState( false );
	const [ notice, setNotice ] = useState( null );

	useEffect( () => {
		setLocalSettings( settings || {} );
	}, [ settings ] );

	const { languages, pages } = window.portfolioSettings;

	const handlePageChange = ( langCode, pageId ) => {
		setLocalSettings( ( prev ) => ( {
			...prev,
			projects_listing_page_ids: {
				...prev.projects_listing_page_ids,
				[ langCode ]: parseInt( pageId, 10 ),
			},
		} ) );
	};

	const handleSave = async () => {
		setSaving( true );
		setNotice( null );

		try {
			await onSave( localSettings );
			setNotice( {
				type: 'success',
				message: __(
					'General settings saved successfully!',
					'portfolio'
				),
			} );
		} catch ( error ) {
			setNotice( {
				type: 'error',
				message:
					error.message ||
					__( 'Error saving general settings.', 'portfolio' ),
			} );
		} finally {
			setSaving( false );
		}
	};

	return (
		<div className="portfolio-settings-section">
			<div className="portfolio-settings-section-header">
				<h2>{ __( 'General Settings', 'portfolio' ) }</h2>
				<p className="description">
					{ __(
						'Configure general settings for your portfolio theme.',
						'portfolio'
					) }
				</p>
			</div>

			{ notice && (
				<Notice status={ notice.type } isDismissible={ false }>
					{ notice.message }
				</Notice>
			) }

			<Card style={ { marginTop: '1rem' } }>
				<CardBody>
					<div className="portfolio-settings-fields">
						{ languages.map( ( lang ) => (
							<SelectControl
								key={ lang.code }
								label={ `Projects Listing Page (${ lang.name })` }
								value={
									localSettings.projects_listing_page_ids[
										lang.code
									] || ''
								}
								options={ [
									{
										label: __(
											'Select a page',
											'portfolio'
										),
										value: '',
									},
									...( pages[ lang.code ] || [] ).map(
										( page ) => ( {
											label: page.title,
											value: page.id,
										} )
									),
								] }
								onChange={ ( pageId ) =>
									handlePageChange( lang.code, pageId )
								}
							/>
						) ) }
					</div>
				</CardBody>
			</Card>

			<div
				className="portfolio-settings-actions"
				style={ { marginTop: '1rem' } }
			>
				<Button
					variant="primary"
					onClick={ handleSave }
					disabled={ saving }
				>
					{ saving ? (
						<>
							<Spinner />
							{ __( 'Saving…', 'portfolio' ) }
						</>
					) : (
						__( 'Save General Settings', 'portfolio' )
					) }
				</Button>
			</div>
		</div>
	);
}
