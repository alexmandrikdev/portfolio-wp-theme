import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { TabPanel, Notice } from '@wordpress/components';
import { useSettingsAPI } from '../hooks/use-settings-api';
import { RecaptchaSettings } from './recaptcha-settings';
import { GeneralSettings } from './general-settings';
import { ContactSettings } from './contact-settings';
import { ZohoMailSettings } from './zoho-mail-settings';

export function SettingsApp() {
	const [ , setActiveTab ] = useState( 'general' );
	const { settings, error, saveSettings } = useSettingsAPI();

	const tabs = [
		{
			name: 'general',
			title: __( 'General', 'portfolio' ),
			className: 'tab-general',
		},
		{
			name: 'recaptcha',
			title: __( 'reCAPTCHA', 'portfolio' ),
			className: 'tab-recaptcha',
		},
		{
			name: 'contact',
			title: __( 'Contact', 'portfolio' ),
			className: 'tab-contact',
		},
		{
			name: 'zoho-mail',
			title: __( 'Zoho Mail', 'portfolio' ),
			className: 'tab-zoho-mail',
		},
	];

	const renderTabContent = ( tabName ) => {
		switch ( tabName ) {
			case 'general':
				return (
					<GeneralSettings
						settings={ settings }
						onSave={ saveSettings }
					/>
				);
			case 'recaptcha':
				return (
					<RecaptchaSettings
						settings={ settings }
						onSave={ saveSettings }
					/>
				);
			case 'contact':
				return (
					<ContactSettings
						settings={ settings }
						onSave={ saveSettings }
					/>
				);
			case 'zoho-mail':
				return (
					<ZohoMailSettings
						settings={ settings }
						onSave={ saveSettings }
					/>
				);
			default:
				return (
					<Notice status="warning" isDismissible={ false }>
						{ __( 'Tab content not found.', 'portfolio' ) }
					</Notice>
				);
		}
	};

	return (
		<div className="portfolio-settings-app">
			{ error && (
				<Notice status="error" isDismissible={ false }>
					{ error }
				</Notice>
			) }

			<div className="portfolio-settings-container">
				<TabPanel
					className="portfolio-settings-tabs"
					activeClass="is-active"
					onSelect={ setActiveTab }
					tabs={ tabs }
				>
					{ ( tab ) => (
						<div
							className={ `portfolio-settings-tab-content ${ tab.className }` }
						>
							{ renderTabContent( tab.name ) }
						</div>
					) }
				</TabPanel>
			</div>
		</div>
	);
}
