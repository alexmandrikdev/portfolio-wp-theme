import { useState, useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import {
	TextControl,
	Button,
	Spinner,
	Notice,
	Card,
	CardBody,
} from '@wordpress/components';

export function ContactSettings( { settings, onSave } ) {
	const [ localSettings, setLocalSettings ] = useState( settings || {} );
	const [ saving, setSaving ] = useState( false );
	const [ notice, setNotice ] = useState( null );

	useEffect( () => {
		setLocalSettings( settings || {} );
	}, [ settings ] );

	const handleSave = async () => {
		setSaving( true );
		setNotice( null );

		try {
			await onSave( localSettings );
			setNotice( {
				type: 'success',
				message: __(
					'Contact settings saved successfully!',
					'portfolio'
				),
			} );
		} catch ( error ) {
			setNotice( {
				type: 'error',
				message:
					error.message ||
					__( 'Error saving contact settings.', 'portfolio' ),
			} );
		} finally {
			setSaving( false );
		}
	};

	const updateSetting = ( key, value ) => {
		setLocalSettings( ( prev ) => ( {
			...prev,
			[ key ]: value,
		} ) );
	};

	return (
		<div className="portfolio-settings-section">
			<div className="portfolio-settings-section-header">
				<h2>{ __( 'Contact Settings', 'portfolio' ) }</h2>
				<p className="description">
					{ __(
						'Configure contact information for your portfolio.',
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
						<TextControl
							label={ __( 'Contact Email', 'portfolio' ) }
							value={ localSettings.contact_email || '' }
							onChange={ ( value ) =>
								updateSetting( 'contact_email', value )
							}
							help={ __(
								'Email address for contact form submissions and alternative contact methods.',
								'portfolio'
							) }
							type="email"
							placeholder="hello@example.com"
						/>
						<TextControl
							label={ __( 'GitHub URL', 'portfolio' ) }
							value={ localSettings.github_url || '' }
							onChange={ ( value ) =>
								updateSetting( 'github_url', value )
							}
							help={ __(
								'Your GitHub profile URL for social media links.',
								'portfolio'
							) }
							type="url"
							placeholder="https://github.com/username"
						/>
						<TextControl
							label={ __( 'LinkedIn URL', 'portfolio' ) }
							value={ localSettings.linkedin_url || '' }
							onChange={ ( value ) =>
								updateSetting( 'linkedin_url', value )
							}
							help={ __(
								'Your LinkedIn profile URL for social media links.',
								'portfolio'
							) }
							type="url"
							placeholder="https://linkedin.com/in/username"
						/>
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
						__( 'Save Contact Settings', 'portfolio' )
					) }
				</Button>
			</div>
		</div>
	);
}
