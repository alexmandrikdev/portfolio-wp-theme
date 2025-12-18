import { __ } from '@wordpress/i18n';
import {
	ComboboxControl,
	Flex,
	TextControl,
	TextareaControl,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { store as coreDataStore } from '@wordpress/core-data';
import BlockCard from '../../js/shared/edit/components/block-card';

export default function Edit( { attributes, setAttributes } ) {
	const {
		consent_message: consentMessage = '',
		necessary_description: necessaryDescription = '',
		analytics_description: analyticsDescription = '',
		accept_button_text: acceptButtonText = '',
		reject_button_text: rejectButtonText = '',
		customize_button_text: customizeButtonText = '',
		save_button_text: saveButtonText = '',
		privacy_policy_page_id: privacyPolicyPageId = 0,
		privacy_policy_text: privacyPolicyText = '',
		cookie_preferences_text: cookiePreferencesText = '',
		necessary_cookies_text: necessaryCookiesText = '',
		analytics_cookies_text: analyticsCookiesText = '',
		close_preferences_text: closePreferencesText = '',
		expiry_days: expiryDays = 365,
	} = attributes;

	const pages =
		useSelect( ( select ) => {
			return select( coreDataStore ).getEntityRecords(
				'postType',
				'page',
				{
					per_page: -1,
				}
			);
		} ) || [];

	const pageOptions = [
		{ label: __( 'Select a page', 'am-portfolio-theme' ), value: '' },
		...pages.map( ( page ) => ( {
			label: page.title.rendered,
			value: page.id,
		} ) ),
	];

	const updateAttribute = ( attributeName, value ) => {
		setAttributes( { [ attributeName ]: value } );
	};

	return (
		<BlockCard title={ __( 'Cookie Consent', 'am-portfolio-theme' ) }>
			<Flex direction="column" gap={ 4 }>
				<TextareaControl
					__nextHasNoMarginBottom
					label={ __( 'Consent Message', 'am-portfolio-theme' ) }
					id="cookie-consent-message"
					value={ consentMessage }
					onChange={ ( value ) =>
						updateAttribute( 'consent_message', value )
					}
					placeholder={ __(
						'Enter the main cookie consent message',
						'am-portfolio-theme'
					) }
					rows={ 3 }
				/>

				<TextareaControl
					__nextHasNoMarginBottom
					label={ __(
						'Necessary Cookies Description',
						'am-portfolio-theme'
					) }
					id="cookie-consent-necessary-description"
					value={ necessaryDescription }
					onChange={ ( value ) =>
						updateAttribute( 'necessary_description', value )
					}
					placeholder={ __(
						'Describe what necessary cookies are used for',
						'am-portfolio-theme'
					) }
					rows={ 2 }
				/>

				<TextareaControl
					__nextHasNoMarginBottom
					label={ __(
						'Analytics Cookies Description',
						'am-portfolio-theme'
					) }
					id="cookie-consent-analytics-description"
					value={ analyticsDescription }
					onChange={ ( value ) =>
						updateAttribute( 'analytics_description', value )
					}
					placeholder={ __(
						'Describe what analytics cookies are used for',
						'am-portfolio-theme'
					) }
					rows={ 2 }
				/>

				<TextControl
					__nextHasNoMarginBottom
					label={ __( 'Accept Button Text', 'am-portfolio-theme' ) }
					id="cookie-consent-accept-button-text"
					value={ acceptButtonText }
					onChange={ ( value ) =>
						updateAttribute( 'accept_button_text', value )
					}
					placeholder={ __(
						'Enter accept button text',
						'am-portfolio-theme'
					) }
				/>

				<TextControl
					__nextHasNoMarginBottom
					label={ __( 'Reject Button Text', 'am-portfolio-theme' ) }
					id="cookie-consent-reject-button-text"
					value={ rejectButtonText }
					onChange={ ( value ) =>
						updateAttribute( 'reject_button_text', value )
					}
					placeholder={ __(
						'Enter reject button text',
						'am-portfolio-theme'
					) }
				/>

				<TextControl
					__nextHasNoMarginBottom
					label={ __(
						'Customize Button Text',
						'am-portfolio-theme'
					) }
					id="cookie-consent-customize-button-text"
					value={ customizeButtonText }
					onChange={ ( value ) =>
						updateAttribute( 'customize_button_text', value )
					}
					placeholder={ __(
						'Enter customize button text',
						'am-portfolio-theme'
					) }
				/>

				<TextControl
					__nextHasNoMarginBottom
					label={ __(
						'Save Preferences Button Text',
						'am-portfolio-theme'
					) }
					id="cookie-consent-save-button-text"
					value={ saveButtonText }
					onChange={ ( value ) =>
						updateAttribute( 'save_button_text', value )
					}
					placeholder={ __(
						'Enter save preferences button text',
						'am-portfolio-theme'
					) }
				/>

				<ComboboxControl
					__nextHasNoMarginBottom
					label={ __( 'Privacy Policy Page', 'am-portfolio-theme' ) }
					id="cookie-consent-privacy-policy-page"
					value={ privacyPolicyPageId }
					onChange={ ( value ) =>
						updateAttribute( 'privacy_policy_page_id', value )
					}
					options={ pageOptions }
					help={ __(
						'Select the page that contains your privacy policy',
						'am-portfolio-theme'
					) }
				/>

				<TextControl
					__nextHasNoMarginBottom
					label={ __( 'Privacy Policy Text', 'am-portfolio-theme' ) }
					id="cookie-consent-privacy-policy-text"
					value={ privacyPolicyText }
					onChange={ ( value ) =>
						updateAttribute( 'privacy_policy_text', value )
					}
					placeholder={ __(
						'Enter privacy policy link text',
						'am-portfolio-theme'
					) }
				/>

				<TextControl
					__nextHasNoMarginBottom
					label={ __(
						'Cookie Preferences Text',
						'am-portfolio-theme'
					) }
					id="cookie-consent-preferences-text"
					value={ cookiePreferencesText }
					onChange={ ( value ) =>
						updateAttribute( 'cookie_preferences_text', value )
					}
					placeholder={ __(
						'Enter cookie preferences title text',
						'am-portfolio-theme'
					) }
				/>

				<TextControl
					__nextHasNoMarginBottom
					label={ __(
						'Necessary Cookies Text',
						'am-portfolio-theme'
					) }
					id="cookie-consent-necessary-text"
					value={ necessaryCookiesText }
					onChange={ ( value ) =>
						updateAttribute( 'necessary_cookies_text', value )
					}
					placeholder={ __(
						'Enter necessary cookies title text',
						'am-portfolio-theme'
					) }
				/>

				<TextControl
					__nextHasNoMarginBottom
					label={ __(
						'Analytics Cookies Text',
						'am-portfolio-theme'
					) }
					id="cookie-consent-analytics-text"
					value={ analyticsCookiesText }
					onChange={ ( value ) =>
						updateAttribute( 'analytics_cookies_text', value )
					}
					placeholder={ __(
						'Enter analytics cookies title text',
						'am-portfolio-theme'
					) }
				/>

				<TextControl
					__nextHasNoMarginBottom
					label={ __(
						'Close Preferences Text',
						'am-portfolio-theme'
					) }
					id="cookie-consent-close-text"
					value={ closePreferencesText }
					onChange={ ( value ) =>
						updateAttribute( 'close_preferences_text', value )
					}
					placeholder={ __(
						'Enter close preferences button text',
						'am-portfolio-theme'
					) }
				/>

				<TextControl
					__nextHasNoMarginBottom
					label={ __( 'Cookie Expiry (Days)', 'am-portfolio-theme' ) }
					id="cookie-consent-expiry-days"
					value={ expiryDays }
					onChange={ ( value ) =>
						updateAttribute(
							'expiry_days',
							parseInt( value ) || 365
						)
					}
					placeholder={ __(
						'Enter cookie expiry in days',
						'am-portfolio-theme'
					) }
					type="number"
					min="1"
					max="730"
					help={ __(
						'How many days should the cookie consent be remembered for?',
						'am-portfolio-theme'
					) }
				/>
			</Flex>
		</BlockCard>
	);
}
