<?php
$settings           = get_option( 'portfolio_theme_settings', array() );
$recaptcha_site_key = $settings['recaptcha_site_key'] ?? '';

wp_interactivity_state(
	'contactFormModal',
	array(
		'nonce'   => wp_create_nonce( 'am_contact_form_nonce' ),
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
	)
);
?>

<div 
	data-wp-interactive="contactFormModal"
	class="modal" 
	id="modalOverlay" 
	data-wp-class--modal--active="state.isOpen"
	data-wp-class--modal--fullscreen="state.isEasyMdeFullscreen"
	data-wp-on--click="actions.closeModalWithOverlay"
	data-wp-watch="callbacks.bodyScrollLock"
	data-wp-watch--focus="callbacks.focusFirstInput"
	data-wp-watch--easymde="callbacks.initEasyMDE"
>
	<div class="modal__content">
		<div 
			class="modal__step" 
			data-wp-class--modal__step--active="!state.isSuccess"
			id="contactForm"
		>
			<div class="modal__header">
				<h2>Let's Start a Conversation</h2>
				<p>
					Have a project in mind or want to discuss opportunities? Fill out the form below and I'll get back to you within 24 hours.
				</p>
			</div>

			<form class="form">
				<div class="form__group">
					<label class="form__label" for="subject">Subject</label>
					<input
						type="text"
						class="form__input"
						id="subject"
						name="subject"
						data-wp-bind--value="state.formData.subject"
						data-wp-on--input="actions.updateFormField"
						data-wp-on--change="actions.updateFormField"
						data-wp-class--form__input--invalid="state.fieldErrors.subject"
						placeholder="e.g., Website Redesign Project"
					/>
					<div 
						class="form__field-error" 
						data-wp-class--form__field-error--visible="state.fieldErrors.subject"
					>
						<span data-wp-text="state.fieldErrors.subject"></span>
					</div>
				</div>

				<div class="form__row">
					<div class="form__group">
						<label class="form__label" for="name">Full Name</label>
						<input
							type="text"
							class="form__input"
							id="name"
							name="name"
							data-wp-bind--value="state.formData.name"
							data-wp-on--input="actions.updateFormField"
							data-wp-on--change="actions.updateFormField"
							data-wp-class--form__input--invalid="state.fieldErrors.name"
							placeholder="Your full name"
						/>
						<div 
							class="form__field-error" 
							data-wp-class--form__field-error--visible="state.fieldErrors.name"
						>
							<span data-wp-text="state.fieldErrors.name"></span>
						</div>
					</div>

					<div class="form__group">
						<label class="form__label" for="email">Email</label>
						<input
							type="email"
							class="form__input"
							id="email"
							name="email"
							data-wp-bind--value="state.formData.email"
							data-wp-on--input="actions.updateFormField"
							data-wp-on--change="actions.updateFormField"
							data-wp-class--form__input--invalid="state.fieldErrors.email"
							placeholder="name@company.com"
						/>
						<div 
							class="form__field-error" 
							data-wp-class--form__field-error--visible="state.fieldErrors.email"
						>
							<span data-wp-text="state.fieldErrors.email"></span>
						</div>
					</div>
				</div>

				<div class="form__group">
					<label class="form__label" for="message">Message</label>
					<textarea
						class="form__textarea"
						id="message"
						name="message"
						data-wp-bind--value="state.formData.message"
						data-wp-on--input="actions.updateFormField"
						data-wp-on--change="actions.updateFormField"
						data-wp-class--form__textarea--invalid="state.fieldErrors.message"
						placeholder="Tell me about your project goals, timeline, and any specific requirements..."
						rows="4"
					></textarea>
					<div 
						class="form__field-error" 
						data-wp-class--form__field-error--visible="state.fieldErrors.message"
					>
						<span data-wp-text="state.fieldErrors.message"></span>
					</div>
				</div>

				<!-- Non-field specific errors -->
				<div 
					class="form__error form__error--general" 
					data-wp-bind--hidden="!state.errorMessage.length"
				>
					<p data-wp-text="state.errorMessage"></p>
				</div>
			</form>

			<div class="modal__footer">
				<button 
					class="btn-secondary" 
					data-wp-on--click="actions.closeModal"
					data-wp-bind--disabled="state.isSubmitting"
				>
					Cancel
				</button>
				<button 
					class="btn-primary <?php echo ! empty( $recaptcha_site_key ) ? 'g-recaptcha' : ''; ?>" 
					<?php if ( ! empty( $recaptcha_site_key ) ) : ?>
						data-wp-bind--disabled="state.isSubmitting"
						data-sitekey="<?php echo esc_attr( $recaptcha_site_key ); ?>" 
						data-callback='onContactFormSubmit' 
						data-action='submit'
					<?php else : ?>
						data-wp-on--click="actions.submitForm"
					<?php endif; ?>
				>
					<span data-wp-text="state.submitButtonText"></span>
				</button>
			</div>
		</div>

		<div 
			class="modal__step" 
			data-wp-class--modal__step--active="state.isSuccess"
			id="successStep"
		>
			<div class="success-message">
				<div class="success-message__icon">✓</div>
				<h2>Message Sent Successfully!</h2>
				<p class="success-message__body-large">
					Thank you for reaching out. I've received your message and will review it carefully.
				</p>
				<p>
					I typically respond within 24 hours. A confirmation has been sent to
					<span data-wp-text="state.formData.email"></span>.
				</p>
				<button 
					class="btn-primary" 
					data-wp-on--click="actions.closeModal"
				>
					Got It
				</button>
			</div>
		</div>
	</div>
</div>