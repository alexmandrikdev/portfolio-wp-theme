import { store, getElement } from '@wordpress/interactivity';
import EasyMDE from 'easymde';

let easyMDEInstance = null;

const trackGAEvent = ( eventName, parameters = {} ) => {
	if ( typeof window.gtag === 'function' ) {
		window.gtag( 'event', eventName, parameters );
	}
};

const { state, actions } = store( 'contactFormModal', {
	state: {
		isOpen: false,
		isSubmitting: false,
		isSuccess: false,
		hasErrors: false,
		errorMessage: '',
		isEasyMdeFullscreen: false,
		fieldErrors: {
			subject: '',
			name: '',
			email: '',
			message: '',
		},
		formData: {
			subject: '',
			name: '',
			email: '',
			message: '',
		},
		get submitButtonText() {
			return state.isSubmitting
				? state.submitButtonSendingText
				: state.submitButtonReadyText;
		},
	},
	actions: {
		openModal: () => {
			state.isOpen = true;

			trackGAEvent( 'contact_modal_opened', {
				interaction_type: 'direct_open',
			} );

			// Fetch a fresh nonce to avoid caching issues
			actions.fetchNonce();
		},
		fetchNonce: async () => {
			try {
				const formData = new FormData();
				formData.append( 'action', 'get_fresh_contact_nonce' );
				const response = await fetch( state.ajaxUrl, {
					method: 'POST',
					body: formData,
				} );
				const result = await response.json();
				if ( result.success ) {
					state.nonce = result.data.nonce;
				} else {
					// eslint-disable-next-line no-console
					console.error(
						'Failed to fetch nonce:',
						result.data?.message
					);
					// Keep existing nonce (if any)
				}
			} catch ( error ) {
				// eslint-disable-next-line no-console
				console.error( 'Error fetching nonce:', error );
			}
		},
		closeModal: () => {
			state.isOpen = false;
			actions.cleanupEasyMDE();

			trackGAEvent( 'contact_modal_closed', {
				form_submitted: state.isSuccess,
			} );

			setTimeout( () => {
				actions.resetForm();
			}, 300 );
		},
		closeModalWithOverlay: ( e ) => {
			if ( e.target.id === 'modalOverlay' ) {
				actions.closeModal();
			}
		},
		cleanupEasyMDE: () => {
			if ( easyMDEInstance ) {
				if (
					state.isEasyMdeFullscreen &&
					easyMDEInstance.isFullscreenActive &&
					easyMDEInstance.isFullscreenActive()
				) {
					easyMDEInstance.toggleFullScreen();
				}
				easyMDEInstance.toTextArea();
				easyMDEInstance = null;
				state.isEasyMdeFullscreen = false;
			}
		},
		updateFormField: ( event ) => {
			const { attributes } = getElement();
			const fieldName = attributes.name;
			const value = event.target.value;

			state.formData[ fieldName ] = value;

			// Real-time validation
			if ( state.fieldErrors[ fieldName ] ) {
				actions.validateField( fieldName, value );
			}
		},
		validateField: ( fieldName, value ) => {
			let error = '';

			// Subject is optional, others are required
			if ( ! value.trim() && fieldName !== 'subject' ) {
				switch ( fieldName ) {
					case 'name':
						error = state.nameRequiredError;
						break;
					case 'email':
						error = state.emailRequiredError;
						break;
					case 'message':
						error = state.messageRequiredError;
						break;
					default:
						error = `${
							fieldName.charAt( 0 ).toUpperCase() +
							fieldName.slice( 1 )
						} is required`;
						break;
				}
			} else if ( fieldName === 'email' && value.trim() ) {
				const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
				if ( ! emailRegex.test( value ) ) {
					error = state.emailRequiredError;
				}
			}

			state.fieldErrors[ fieldName ] = error;
			state.hasErrors =
				Object.values( state.fieldErrors ).some(
					( fieldError ) => fieldError !== ''
				) || state.errorMessage !== '';
		},
		validateForm: () => {
			let isValid = true;

			// Validate all fields
			Object.keys( state.formData ).forEach( ( fieldName ) => {
				actions.validateField( fieldName, state.formData[ fieldName ] );
				if (
					state.fieldErrors[ fieldName ] &&
					fieldName !== 'subject'
				) {
					isValid = false;
				}
			} );

			return isValid;
		},
		submitForm: async ( recaptchaToken ) => {
			// Reset previous states
			state.isSubmitting = true;
			state.hasErrors = false;
			state.errorMessage = '';

			// Reset field errors
			Object.keys( state.fieldErrors ).forEach( ( key ) => {
				state.fieldErrors[ key ] = '';
			} );

			// Client-side validation
			if ( ! actions.validateForm() ) {
				state.hasErrors = true;
				state.isSubmitting = false;

				// Scroll to first error
				setTimeout( () => {
					const firstError = document.querySelector(
						'.form__input--invalid, .form__textarea--invalid'
					);
					if ( firstError ) {
						firstError.scrollIntoView( {
							behavior: 'smooth',
							block: 'center',
						} );
						firstError.focus();
					}
				}, 100 );
				return;
			}

			try {
				const formData = new FormData();
				formData.append( 'action', 'handle_contact_form' );
				formData.append( 'nonce', state.nonce );
				if ( recaptchaToken ) {
					formData.append( 'recaptcha_token', recaptchaToken );
				}

				// Detect and add user timezone
				const timezone =
					Intl.DateTimeFormat().resolvedOptions().timeZone;
				formData.append( 'timezone', timezone );

				// Add all form fields
				Object.entries( state.formData ).forEach(
					( [ key, value ] ) => {
						formData.append( key, value );
					}
				);

				const response = await fetch( state.ajaxUrl, {
					method: 'POST',
					body: formData,
				} );

				const result = await response.json();

				if ( response.status === 400 && ! result.success ) {
					// Handle validation errors
					if ( result.data?.errors ) {
						// Update field-specific errors
						Object.keys( result.data.errors ).forEach( ( key ) => {
							if ( state.fieldErrors.hasOwnProperty( key ) ) {
								state.fieldErrors[ key ] =
									result.data.errors[ key ];
							}
						} );
						state.hasErrors = true;
					}
					state.errorMessage =
						result.data?.message || state.validationGenericError;

					// Scroll to first error
					setTimeout( () => {
						const firstError = document.querySelector(
							'.form__input--invalid, .form__textarea--invalid'
						);
						if ( firstError ) {
							firstError.scrollIntoView( {
								behavior: 'smooth',
								block: 'center',
							} );
							firstError.focus();
						}
					}, 100 );
					return;
				}

				if ( ! response.ok ) {
					throw new Error( result.data?.message || 'Request failed' );
				}

				state.isSuccess = true;

				const filledFields = Object.values( state.formData ).filter(
					( value ) => value.trim() !== ''
				).length;
				trackGAEvent( 'contact_form_submitted', {
					form_fields_filled: filledFields,
					has_subject: state.formData.subject.trim() !== '',
					has_name: state.formData.name.trim() !== '',
					has_email: state.formData.email.trim() !== '',
					has_message: state.formData.message.trim() !== '',
				} );
			} catch ( error ) {
				// eslint-disable-next-line no-console
				console.error( 'Form submission error:', error );
				state.hasErrors = true;

				state.errorMessage = error.message || state.networkErrorMessage;
			} finally {
				state.isSubmitting = false;
			}
		},
		resetForm: () => {
			state.formData = {
				subject: '',
				name: '',
				email: '',
				message: '',
			};
			state.fieldErrors = {
				subject: '',
				name: '',
				email: '',
				message: '',
			};
			state.hasErrors = false;
			state.errorMessage = '';
			state.isSuccess = false;
			state.isSubmitting = false;
			state.isEasyMdeFullscreen = false;
		},
	},
	callbacks: {
		bodyScrollLock: () => {
			if ( state.isOpen ) {
				document.body.style.overflow = 'hidden';
			} else {
				document.body.style.overflow = '';
			}
		},
		focusFirstInput: () => {
			if ( state.isOpen && ! state.isSuccess ) {
				const firstInput = document.querySelector( '#subject' );
				if ( firstInput ) {
					setTimeout( () => firstInput.focus(), 100 );
				}
			}
		},
		initEasyMDE: () => {
			if ( state.isOpen && ! state.isSuccess && ! easyMDEInstance ) {
				const textarea = document.querySelector( '#message' );

				if ( textarea ) {
					easyMDEInstance = new EasyMDE( {
						element: textarea,
						initialValue: state.formData.message,
						spellChecker: false,
						status: false,
						minHeight: '150px',
						placeholder: textarea.getAttribute( 'placeholder' ),
						onToggleFullScreen: ( fullscreen ) => {
							state.isEasyMdeFullscreen = fullscreen;
						},
					} );

					easyMDEInstance.codemirror.on( 'change', () => {
						state.formData.message = easyMDEInstance.value();
					} );
				}
			}

			if ( ( ! state.isOpen || state.isSuccess ) && easyMDEInstance ) {
				actions.cleanupEasyMDE();
			}
		},
	},
} );

window.onContactFormSubmit = ( token ) => {
	actions.submitForm( token );
};
