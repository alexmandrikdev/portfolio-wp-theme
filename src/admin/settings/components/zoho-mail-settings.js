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

export function ZohoMailSettings( { settings, onSave } ) {
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
					'Zoho Mail settings saved successfully!',
					'portfolio'
				),
			} );
		} catch ( error ) {
			setNotice( {
				type: 'error',
				message:
					error.message ||
					__( 'Error saving Zoho Mail settings.', 'portfolio' ),
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

	const hasCredentials =
		localSettings.zoho_client_id && localSettings.zoho_client_secret;

	// Build authorization URL
	const getAuthorizationUrl = () => {
		const clientId = localSettings.zoho_client_id;
		const redirectUri =
			window.portfolioSettings?.zoho_redirect_uri ||
			`${ window.location.origin }/wp-json/portfolio/v1/zoho-auth/callback`;
		const scope = 'ZohoMail.messages.CREATE';
		const state = window.portfolioSettings?.zoho_oauth_state || '';
		let url = `https://accounts.zoho.com/oauth/v2/auth?client_id=${ encodeURIComponent(
			clientId
		) }&response_type=code&redirect_uri=${ encodeURIComponent(
			redirectUri
		) }&scope=${ encodeURIComponent( scope ) }&access_type=offline`;
		if ( state ) {
			url += `&state=${ encodeURIComponent( state ) }`;
		}
		return url;
	};

	return (
		<div className="portfolio-settings-section">
			<div className="portfolio-settings-section-header">
				<h2>{ __( 'Zoho Mail Settings', 'portfolio' ) }</h2>
				<p className="description">
					{ __(
						'Configure Zoho Mail OAuth credentials for sending emails.',
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
							label={ __( 'Client ID', 'portfolio' ) }
							value={ localSettings.zoho_client_id || '' }
							onChange={ ( value ) =>
								updateSetting( 'zoho_client_id', value )
							}
							help={ __(
								'Your Zoho Mail OAuth Client ID.',
								'portfolio'
							) }
							placeholder="1000.XXXXXXXXXXXX"
						/>
						<TextControl
							label={ __( 'Client Secret', 'portfolio' ) }
							value={ localSettings.zoho_client_secret || '' }
							onChange={ ( value ) =>
								updateSetting( 'zoho_client_secret', value )
							}
							help={ __(
								'Your Zoho Mail OAuth Client Secret.',
								'portfolio'
							) }
							type="password"
							placeholder="XXXXXXXXXXXXXXXX"
						/>
					</div>

					<div className="portfolio-settings-help">
						<h3>{ __( 'Setup Instructions', 'portfolio' ) }</h3>
						<p>
							{ __(
								'To use Zoho Mail, you need to:',
								'portfolio'
							) }
						</p>
						<ol>
							<li>
								{ __( 'Visit the', 'portfolio' ) }{ ' ' }
								<a
									href="https://api-console.zoho.com"
									target="_blank"
									rel="noopener noreferrer"
								>
									{ __( 'Zoho API Console', 'portfolio' ) }
								</a>
							</li>
							<li>
								{ __(
									'Create a new OAuth client for Zoho Mail with the scope ZohoMail.messages.CREATE',
									'portfolio'
								) }
							</li>
							<li>
								{ __(
									'Set the redirect URL to:',
									'portfolio'
								) }{ ' ' }
								<code>
									{ window.location.origin +
										'/wp-json/portfolio/v1/zoho-auth/callback' }
								</code>
							</li>
							<li>
								{ __(
									'Enter your Client ID and Client Secret above',
									'portfolio'
								) }
							</li>
							<li>
								{ __(
									'Save the settings, then click "Authorize with Zoho" to grant access',
									'portfolio'
								) }
							</li>
						</ol>
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
						__( 'Save Zoho Mail Settings', 'portfolio' )
					) }
				</Button>

				{ hasCredentials && (
					<Button
						variant="secondary"
						href={ getAuthorizationUrl() }
						style={ { marginLeft: '1rem' } }
					>
						{ __( 'Authorize with Zoho', 'portfolio' ) }
					</Button>
				) }
			</div>

			{ ( localSettings.zoho_access_token ||
				localSettings.zoho_refresh_token ) && (
				<Card style={ { marginTop: '1rem' } }>
					<CardBody>
						<h3>{ __( 'Authentication Status', 'portfolio' ) }</h3>
						<p>
							{ localSettings.zoho_access_token ? (
								<>
									<strong>
										{ __( 'Access Token:', 'portfolio' ) }
									</strong>{ ' ' }
									{ __(
										'Present (automatically managed)',
										'portfolio'
									) }
								</>
							) : (
								<>
									<strong>
										{ __( 'Access Token:', 'portfolio' ) }
									</strong>{ ' ' }
									{ __( 'Not yet obtained', 'portfolio' ) }
								</>
							) }
						</p>
						<p>
							{ localSettings.zoho_refresh_token ? (
								<>
									<strong>
										{ __( 'Refresh Token:', 'portfolio' ) }
									</strong>{ ' ' }
									{ __( 'Present', 'portfolio' ) }
								</>
							) : (
								<>
									<strong>
										{ __( 'Refresh Token:', 'portfolio' ) }
									</strong>{ ' ' }
									{ __( 'Not yet obtained', 'portfolio' ) }
								</>
							) }
						</p>
						{ localSettings.zoho_token_expires && (
							<p>
								<strong>
									{ __( 'Token Expires:', 'portfolio' ) }
								</strong>{ ' ' }
								{ new Date(
									localSettings.zoho_token_expires * 1000
								).toLocaleString() }
							</p>
						) }
					</CardBody>
				</Card>
			) }
		</div>
	);
}
