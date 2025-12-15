import { useState, useEffect, useCallback } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import {
	TextControl,
	Button,
	Spinner,
	Notice,
	Card,
	CardBody,
	SelectControl,
	BaseControl,
} from '@wordpress/components';

export function ZohoMailSettings( { settings, onSave } ) {
	const [ localSettings, setLocalSettings ] = useState( settings || {} );
	const [ saving, setSaving ] = useState( false );
	const [ notice, setNotice ] = useState( null );
	const [ fetchingAccounts, setFetchingAccounts ] = useState( false );
	const [ accounts, setAccounts ] = useState( [] );
	const [ accountsError, setAccountsError ] = useState( null );
	const [ autoFetchAttempts, setAutoFetchAttempts ] = useState( 0 );
	const MAX_AUTO_FETCH_ATTEMPTS = 3;

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

	const fetchAccounts = useCallback( async () => {
		setFetchingAccounts( true );
		setAccountsError( null );
		try {
			const response = await apiFetch( {
				path: '/portfolio/v1/zoho-accounts',
				method: 'GET',
			} );
			setAccounts( response.accounts || [] );
			setAutoFetchAttempts( 0 ); // Reset attempts on success
		} catch ( error ) {
			setAccountsError(
				error.message || __( 'Failed to fetch accounts.', 'portfolio' )
			);
			setAutoFetchAttempts( ( prev ) => prev + 1 ); // Increment attempts on error
		} finally {
			setFetchingAccounts( false );
		}
	}, [] );

	const handleFetchAccounts = () => {
		setAutoFetchAttempts( 0 ); // Reset attempts when manually triggered
		fetchAccounts();
	};

	// Auto-fetch accounts when access token is present and accounts are empty.
	useEffect( () => {
		if (
			localSettings.zoho_access_token &&
			accounts.length === 0 &&
			! fetchingAccounts &&
			autoFetchAttempts < MAX_AUTO_FETCH_ATTEMPTS
		) {
			fetchAccounts();
		}
	}, [
		localSettings.zoho_access_token,
		accounts.length,
		fetchingAccounts,
		autoFetchAttempts,
		fetchAccounts,
	] );

	// Reset auto-fetch attempts when access token changes (new token may work)
	useEffect( () => {
		setAutoFetchAttempts( 0 );
	}, [ localSettings.zoho_access_token ] );

	const hasCredentials =
		window.portfolioSettings?.zoho_client_id &&
		window.portfolioSettings?.zoho_client_secret;

	// Build authorization URL
	const getAuthorizationUrl = () => {
		const clientId = window.portfolioSettings?.zoho_client_id || '';
		const redirectUri =
			window.portfolioSettings?.zoho_redirect_uri ||
			`${ window.location.origin }/wp-json/portfolio/v1/zoho-auth/callback`;
		const scope = 'ZohoMail.messages.CREATE,ZohoMail.accounts.READ';
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
						<BaseControl
							id="zoho-account-id"
							label={ __( 'Account ID', 'portfolio' ) }
							help={ __(
								'Your Zoho Mail Account ID (found in Zoho Mail settings).',
								'portfolio'
							) }
						>
							<div
								style={ {
									display: 'flex',
									alignItems: 'flex-start',
									gap: '0.5rem',
								} }
							>
								{ accounts.length > 0 ? (
									<SelectControl
										value={
											localSettings.zoho_account_id || ''
										}
										onChange={ ( value ) =>
											updateSetting(
												'zoho_account_id',
												value
											)
										}
										options={ [
											{
												label: __(
													'Select an account…',
													'portfolio'
												),
												value: '',
											},
											...accounts.map( ( account ) => ( {
												label: `${ account.displayName } (${ account.email })`,
												value: account.id,
											} ) ),
										] }
										style={ { flex: 1 } }
									/>
								) : (
									<TextControl
										value={
											localSettings.zoho_account_id || ''
										}
										onChange={ ( value ) =>
											updateSetting(
												'zoho_account_id',
												value
											)
										}
										placeholder="123456789"
										style={ { flex: 1 } }
									/>
								) }
								<Button
									variant="secondary"
									size="compact"
									onClick={ handleFetchAccounts }
									disabled={
										fetchingAccounts ||
										! localSettings.zoho_access_token
									}
									isBusy={ fetchingAccounts }
								>
									{ fetchingAccounts
										? __( 'Fetching…', 'portfolio' )
										: __( 'Fetch Accounts', 'portfolio' ) }
								</Button>
							</div>
							{ accountsError && (
								<Notice status="error" isDismissible={ false }>
									{ accountsError }
								</Notice>
							) }
						</BaseControl>
						<TextControl
							label={ __( 'Base API URL', 'portfolio' ) }
							value={ localSettings.zoho_base_api_url || '' }
							onChange={ ( value ) =>
								updateSetting( 'zoho_base_api_url', value )
							}
							help={ __(
								'Zoho Mail API base URL (e.g., https://mail.zoho.com).',
								'portfolio'
							) }
							placeholder="https://mail.zoho.com"
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
									'Create a new OAuth client for Zoho Mail',
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
									'Define PORTFOLIO_ZOHO_CLIENT_ID and PORTFOLIO_ZOHO_CLIENT_SECRET constants in your wp-config.php file.',
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
