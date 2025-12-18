import { store, getElement, getContext } from '@wordpress/interactivity';
import EasyMDE from 'easymde';

let easyMDEInstance = null;

// localStorage configuration
const STORAGE_KEY = 'contactFormDraft';
const EXPIRATION_DAYS = 7;
const DEBOUNCE_DELAY = 300; // milliseconds

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
		savingField: '',
		saveDebounceTimer: null,
		hideIndicatorTimer: null,
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
		get isSavingIndicatorVisible() {
			const { field } = getContext();

			return state.savingField === field;
		},
	},
	actions: {
		/**
		 * Save form data to localStorage with expiration
		 * @param {Object} formData - The form data to save
		 */
		saveFormDataToStorage: ( formData ) => {
			try {
				const dataToSave = {
					formData: {
						subject: formData.subject || '',
						name: formData.name || '',
						email: formData.email || '',
						message: formData.message || '',
					},
					timestamp: Date.now(),
				};
				window.localStorage.setItem(
					STORAGE_KEY,
					JSON.stringify( dataToSave )
				);
			} catch ( error ) {
				// eslint-disable-next-line no-console
				console.error(
					'Failed to save form data to localStorage:',
					error
				);
			}
		},

		/**
		 * Load form data from localStorage, checking expiration
		 * @return {Object|null} Form data if valid and not expired, null otherwise
		 */
		loadFormDataFromStorage: () => {
			try {
				const savedData = window.localStorage.getItem( STORAGE_KEY );
				if ( ! savedData ) {
					return null;
				}

				const parsedData = JSON.parse( savedData );

				// Check if data has expired (7 days)
				const expirationTime = EXPIRATION_DAYS * 24 * 60 * 60 * 1000; // Convert days to milliseconds
				const isExpired =
					Date.now() - parsedData.timestamp > expirationTime;

				if ( isExpired ) {
					// Clear expired data
					window.localStorage.removeItem( STORAGE_KEY );
					return null;
				}

				return parsedData.formData;
			} catch ( error ) {
				// eslint-disable-next-line no-console
				console.error(
					'Failed to load form data from localStorage:',
					error
				);
				// Clear corrupted data
				window.localStorage.removeItem( STORAGE_KEY );
				return null;
			}
		},

		/**
		 * Clear saved form data from localStorage
		 */
		clearFormDataFromStorage: () => {
			try {
				window.localStorage.removeItem( STORAGE_KEY );
			} catch ( error ) {
				// eslint-disable-next-line no-console
				console.error(
					'Failed to clear form data from localStorage:',
					error
				);
			}
		},

		/**
		 * Show save indicator for a specific field
		 * @param {string} fieldName - The field name to show indicator for
		 */
		showSaveIndicator: ( fieldName ) => {
			if ( state.hideIndicatorTimer ) {
				clearTimeout( state.hideIndicatorTimer );
				state.hideIndicatorTimer = null;
			}

			// Set the field that's being saved
			state.savingField = fieldName;

			// Hide indicator after 1 second
			state.hideIndicatorTimer = setTimeout( () => {
				state.savingField = '';
				state.hideIndicatorTimer = null;
			}, 1000 );
		},

		/**
		 * Debounced save function to prevent excessive localStorage writes
		 * @param {Object} formData  - The form data to save
		 * @param {string} fieldName - Optional field name to show save indicator for
		 */
		debouncedSaveFormData: ( formData, fieldName = '' ) => {
			if ( state.saveDebounceTimer ) {
				clearTimeout( state.saveDebounceTimer );
			}

			// Show save indicator immediately when user starts typing
			if ( fieldName ) {
				actions.showSaveIndicator( fieldName );
			}

			state.saveDebounceTimer = setTimeout( () => {
				actions.saveFormDataToStorage( formData );
				state.saveDebounceTimer = null;
			}, DEBOUNCE_DELAY );
		},

		openModal: () => {
			state.isOpen = true;

			trackGAEvent( 'contact_modal_opened', {
				interaction_type: 'direct_open',
			} );

			// Load saved form data from localStorage
			const savedFormData = actions.loadFormDataFromStorage();
			if ( savedFormData ) {
				state.formData = { ...state.formData, ...savedFormData };
			}

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

			// Save form data to localStorage with debouncing
			actions.debouncedSaveFormData( state.formData, fieldName );

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

				// Clear saved form data from localStorage after successful submission
				actions.clearFormDataFromStorage();

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
			state.savingField = '';
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
						// Save form data to localStorage when EasyMDE content changes
						actions.debouncedSaveFormData(
							state.formData,
							'message'
						);
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
