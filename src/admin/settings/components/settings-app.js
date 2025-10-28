import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { TabPanel, Notice } from '@wordpress/components';
import { useSettingsAPI } from '../hooks/use-settings-api';
import { RecaptchaSettings } from './recaptcha-settings';

export function SettingsApp() {
	const [ , setActiveTab ] = useState( 'recaptcha' );
	const { settings, error, saveSettings } = useSettingsAPI();

	const tabs = [
		{
			name: 'recaptcha',
			title: __( 'reCAPTCHA', 'portfolio' ),
			className: 'tab-recaptcha',
		},
	];

	const renderTabContent = ( tabName ) => {
		switch ( tabName ) {
			case 'recaptcha':
				return (
					<RecaptchaSettings
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
