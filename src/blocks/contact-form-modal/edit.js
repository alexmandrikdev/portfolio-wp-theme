import { __ } from '@wordpress/i18n';
import {
	BaseControl,
	TextareaControl,
	TextControl,
} from '@wordpress/components';
import BlockCard from '../../js/shared/edit/components/block-card';

export default function Edit( { attributes, setAttributes } ) {
	const {
		modal_title: modalTitle = '',
		modal_description: modalDescription = '',
		subject_placeholder: subjectPlaceholder = '',
		name_placeholder: namePlaceholder = '',
		email_placeholder: emailPlaceholder = '',
		message_placeholder: messagePlaceholder = '',
		cancel_button_text: cancelButtonText = '',
		submit_button_ready_text: submitButtonReadyText = '',
		submit_button_sending_text: submitButtonSendingText = '',
		success_title: successTitle = '',
		success_body_large: successBodyLarge = '',
		success_body_small: successBodySmall = '',
		success_button_text: successButtonText = '',
		network_error_message: networkErrorMessage = '',
	} = attributes;

	const updateAttribute = ( attributeName, value ) => {
		setAttributes( { [ attributeName ]: value } );
	};

	return (
		<BlockCard title={ __( 'Contact Form Modal', 'am-portfolio-theme' ) }>
			<BaseControl
				__nextHasNoMarginBottom
				label={ __( 'Modal Title', 'am-portfolio-theme' ) }
				id="contact-form-modal-title"
			>
				<TextControl
					id="contact-form-modal-title"
					value={ modalTitle }
					onChange={ ( value ) =>
						updateAttribute( 'modal_title', value )
					}
					placeholder={ __(
						'Enter modal title',
						'am-portfolio-theme'
					) }
				/>
			</BaseControl>

			<BaseControl
				__nextHasNoMarginBottom
				label={ __( 'Modal Description', 'am-portfolio-theme' ) }
				id="contact-form-modal-description"
			>
				<TextareaControl
					id="contact-form-modal-description"
					value={ modalDescription }
					onChange={ ( value ) =>
						updateAttribute( 'modal_description', value )
					}
					placeholder={ __(
						'Enter modal description',
						'am-portfolio-theme'
					) }
					rows={ 2 }
				/>
			</BaseControl>

			<BaseControl
				__nextHasNoMarginBottom
				label={ __( 'Subject Placeholder', 'am-portfolio-theme' ) }
				id="contact-form-subject-placeholder"
			>
				<TextControl
					id="contact-form-subject-placeholder"
					value={ subjectPlaceholder }
					onChange={ ( value ) =>
						updateAttribute( 'subject_placeholder', value )
					}
					placeholder={ __(
						'Enter subject placeholder',
						'am-portfolio-theme'
					) }
				/>
			</BaseControl>

			<BaseControl
				__nextHasNoMarginBottom
				label={ __( 'Name Placeholder', 'am-portfolio-theme' ) }
				id="contact-form-name-placeholder"
			>
				<TextControl
					id="contact-form-name-placeholder"
					value={ namePlaceholder }
					onChange={ ( value ) =>
						updateAttribute( 'name_placeholder', value )
					}
					placeholder={ __(
						'Enter name placeholder',
						'am-portfolio-theme'
					) }
				/>
			</BaseControl>

			<BaseControl
				__nextHasNoMarginBottom
				label={ __( 'Email Placeholder', 'am-portfolio-theme' ) }
				id="contact-form-email-placeholder"
			>
				<TextControl
					id="contact-form-email-placeholder"
					value={ emailPlaceholder }
					onChange={ ( value ) =>
						updateAttribute( 'email_placeholder', value )
					}
					placeholder={ __(
						'Enter email placeholder',
						'am-portfolio-theme'
					) }
				/>
			</BaseControl>

			<BaseControl
				__nextHasNoMarginBottom
				label={ __( 'Message Placeholder', 'am-portfolio-theme' ) }
				id="contact-form-message-placeholder"
			>
				<TextControl
					id="contact-form-message-placeholder"
					value={ messagePlaceholder }
					onChange={ ( value ) =>
						updateAttribute( 'message_placeholder', value )
					}
					placeholder={ __(
						'Enter message placeholder',
						'am-portfolio-theme'
					) }
				/>
			</BaseControl>

			<BaseControl
				__nextHasNoMarginBottom
				label={ __( 'Cancel Button Text', 'am-portfolio-theme' ) }
				id="contact-form-cancel-button-text"
			>
				<TextControl
					id="contact-form-cancel-button-text"
					value={ cancelButtonText }
					onChange={ ( value ) =>
						updateAttribute( 'cancel_button_text', value )
					}
					placeholder={ __(
						'Enter cancel button text',
						'am-portfolio-theme'
					) }
				/>
			</BaseControl>

			<BaseControl
				__nextHasNoMarginBottom
				label={ __( 'Submit Button Ready Text', 'am-portfolio-theme' ) }
				id="contact-form-submit-ready-text"
			>
				<TextControl
					id="contact-form-submit-ready-text"
					value={ submitButtonReadyText }
					onChange={ ( value ) =>
						updateAttribute( 'submit_button_ready_text', value )
					}
					placeholder={ __(
						'Enter submit button ready text',
						'am-portfolio-theme'
					) }
				/>
			</BaseControl>

			<BaseControl
				__nextHasNoMarginBottom
				label={ __(
					'Submit Button Sending Text',
					'am-portfolio-theme'
				) }
				id="contact-form-submit-sending-text"
			>
				<TextControl
					id="contact-form-submit-sending-text"
					value={ submitButtonSendingText }
					onChange={ ( value ) =>
						updateAttribute( 'submit_button_sending_text', value )
					}
					placeholder={ __(
						'Enter submit button sending text',
						'am-portfolio-theme'
					) }
				/>
			</BaseControl>

			<BaseControl
				__nextHasNoMarginBottom
				label={ __( 'Success Title', 'am-portfolio-theme' ) }
				id="contact-form-success-title"
			>
				<TextControl
					id="contact-form-success-title"
					value={ successTitle }
					onChange={ ( value ) =>
						updateAttribute( 'success_title', value )
					}
					placeholder={ __(
						'Enter success title',
						'am-portfolio-theme'
					) }
				/>
			</BaseControl>

			<BaseControl
				__nextHasNoMarginBottom
				label={ __( 'Success Body Large', 'am-portfolio-theme' ) }
				id="contact-form-success-body-large"
			>
				<TextareaControl
					id="contact-form-success-body-large"
					value={ successBodyLarge }
					onChange={ ( value ) =>
						updateAttribute( 'success_body_large', value )
					}
					placeholder={ __(
						'Enter success body large text',
						'am-portfolio-theme'
					) }
					rows={ 2 }
				/>
			</BaseControl>

			<BaseControl
				__nextHasNoMarginBottom
				label={ __( 'Success Body Small', 'am-portfolio-theme' ) }
				id="contact-form-success-body-small"
			>
				<TextareaControl
					id="contact-form-success-body-small"
					value={ successBodySmall }
					onChange={ ( value ) =>
						updateAttribute( 'success_body_small', value )
					}
					placeholder={ __(
						'Enter success body small text',
						'am-portfolio-theme'
					) }
					help={ __(
						"Use [email] as a placeholder for the user's email address.",
						'am-portfolio-theme'
					) }
					rows={ 2 }
				/>
			</BaseControl>

			<BaseControl
				__nextHasNoMarginBottom
				label={ __( 'Success Button Text', 'am-portfolio-theme' ) }
				id="contact-form-success-button-text"
			>
				<TextControl
					id="contact-form-success-button-text"
					value={ successButtonText }
					onChange={ ( value ) =>
						updateAttribute( 'success_button_text', value )
					}
					placeholder={ __(
						'Enter success button text',
						'am-portfolio-theme'
					) }
				/>
			</BaseControl>

			<BaseControl
				__nextHasNoMarginBottom
				label={ __( 'Network Error Message', 'am-portfolio-theme' ) }
				id="contact-form-network-error-message"
			>
				<TextControl
					id="contact-form-network-error-message"
					value={ networkErrorMessage }
					onChange={ ( value ) =>
						updateAttribute( 'network_error_message', value )
					}
					placeholder={ __(
						'Enter network error message',
						'am-portfolio-theme'
					) }
				/>
			</BaseControl>
		</BlockCard>
	);
}
