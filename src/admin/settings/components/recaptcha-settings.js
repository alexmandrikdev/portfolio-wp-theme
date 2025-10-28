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

export function RecaptchaSettings( { settings, onSave } ) {
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
					'reCAPTCHA settings saved successfully!',
					'portfolio'
				),
			} );
		} catch ( error ) {
			setNotice( {
				type: 'error',
				message:
					error.message ||
					__( 'Error saving reCAPTCHA settings.', 'portfolio' ),
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
				<h2>{ __( 'Google reCAPTCHA Settings', 'portfolio' ) }</h2>
				<p className="description">
					{ __(
						'Configure Google reCAPTCHA for spam protection.',
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
							label={ __( 'reCAPTCHA Site Key', 'portfolio' ) }
							value={ localSettings.recaptcha_site_key || '' }
							onChange={ ( value ) =>
								updateSetting( 'recaptcha_site_key', value )
							}
							help={ __(
								'Your Google reCAPTCHA site key. This is used in the frontend.',
								'portfolio'
							) }
							placeholder="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI"
						/>
						<TextControl
							label={ __( 'reCAPTCHA Secret Key', 'portfolio' ) }
							value={ localSettings.recaptcha_secret_key || '' }
							onChange={ ( value ) =>
								updateSetting( 'recaptcha_secret_key', value )
							}
							help={ __(
								'Your Google reCAPTCHA secret key. Keep this secure - it is used for server-side verification.',
								'portfolio'
							) }
							type="password"
							placeholder="6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe"
						/>
					</div>

					<div className="portfolio-settings-help">
						<h3>{ __( 'Setup Instructions', 'portfolio' ) }</h3>
						<p>
							{ __(
								'To use reCAPTCHA, you need to:',
								'portfolio'
							) }
						</p>
						<ol>
							<li>
								{ __( 'Visit the', 'portfolio' ) }{ ' ' }
								<a
									href="https://www.google.com/recaptcha/admin"
									target="_blank"
									rel="noopener noreferrer"
								>
									{ __(
										'Google reCAPTCHA Admin Console',
										'portfolio'
									) }
								</a>
							</li>
							<li>
								{ __(
									'Register your site and get Site Key and Secret Key',
									'portfolio'
								) }
							</li>
							<li>
								{ __(
									'Enter your keys in the fields above',
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
						__( 'Save reCAPTCHA Settings', 'portfolio' )
					) }
				</Button>
			</div>
		</div>
	);
}
