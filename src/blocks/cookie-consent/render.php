<?php
$privacy_policy_url = '';
if ( ! empty( $attributes['privacy_policy_page_id'] ) ) {
	$privacy_policy_url = get_permalink( $attributes['privacy_policy_page_id'] );
}

wp_interactivity_state(
	'cookieConsent',
	array(
		'expiryDays'       => absint( $attributes['expiry_days'] ?? 365 ),
		'isVisible'        => false,
		'showCustomize'    => false,
		'consentGiven'     => false,
		'analyticsConsent' => false,
		'necessaryConsent' => true, // Always true as it's required.
	)
);
?>

<div
	data-wp-interactive="cookieConsent"
	data-wp-init="callbacks.initConsent"
	data-wp-watch="callbacks.checkConsentStatus"
	class="cookie-consent"
	data-wp-class--cookie-consent--visible="state.isVisible"
	aria-live="polite"
	aria-label="<?php echo esc_attr( $attributes['cookie_preferences_text'] ?? 'Cookie Preferences' ); ?>"
>
	<!-- Main Banner -->
	<div
		class="cookie-consent__banner"
		data-wp-bind--hidden="state.showCustomize"
		role="dialog"
		aria-labelledby="cookie-consent-message"
	>
		<div class="cookie-consent__banner-content container">
			<div class="cookie-consent__message">
				<p id="cookie-consent-message"><?php echo esc_html( $attributes['consent_message'] ?? '' ); ?></p>
				<?php if ( ! empty( $privacy_policy_url ) ) : ?>
					<a
						href="<?php echo esc_url( $privacy_policy_url ); ?>"
						class="cookie-consent__privacy-link"
						target="_blank"
					>
						<?php echo esc_html( $attributes['privacy_policy_text'] ?? 'Privacy Policy' ); ?>
					</a>
				<?php endif; ?>
			</div>
			
			<div class="cookie-consent__actions">
				<button
					class="cookie-consent__button cookie-consent__button--secondary"
					data-wp-on--click="actions.rejectAll"
					type="button"
				>
					<?php echo esc_html( $attributes['reject_button_text'] ?? 'Reject All' ); ?>
				</button>
				
				<button
					class="cookie-consent__button cookie-consent__button--primary"
					data-wp-on--click="actions.acceptAll"
					type="button"
				>
					<?php echo esc_html( $attributes['accept_button_text'] ?? 'Accept All' ); ?>
				</button>
				
				<button
					class="cookie-consent__button cookie-consent__button--link"
					data-wp-on--click="actions.showCustomize"
					type="button"
				>
					<?php echo esc_html( $attributes['customize_button_text'] ?? 'Customize' ); ?>
				</button>
			</div>
		</div>
	</div>
</div>

<!-- Customization Modal -->
<div
	data-wp-interactive="cookieConsent"
	class="cookie-consent-modal"
	data-wp-bind--hidden="!state.showCustomize"
	role="dialog"
	aria-labelledby="cookie-preferences-title"
	aria-modal="true"
>
	<div class="cookie-consent-modal__overlay" data-wp-on--click="actions.closeCustomize"></div>
	
	<div class="cookie-consent-modal__content">
		<div class="cookie-consent-modal__header">
			<h3 id="cookie-preferences-title"><?php echo esc_html( $attributes['cookie_preferences_text'] ?? 'Cookie Preferences' ); ?></h3>
			<button
				class="cookie-consent-modal__close"
				data-wp-on--click="actions.closeCustomize"
				aria-label="<?php echo esc_attr( $attributes['close_preferences_text'] ?? 'Close preferences' ); ?>"
				type="button"
			>
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</button>
		</div>
		
		<div class="cookie-consent-modal__categories">
			<!-- Necessary Cookies (Always Enabled) -->
			<div class="cookie-consent-modal__category">
				<div class="cookie-consent-modal__category-header">
					<div class="cookie-consent-modal__category-info">
						<h4 class="cookie-consent-modal__category-title"><?php echo esc_html( $attributes['necessary_cookies_text'] ?? 'Necessary Cookies' ); ?></h4>
						<p class="cookie-consent-modal__category-description"><?php echo esc_html( $attributes['necessary_description'] ?? '' ); ?></p>
					</div>
					<div class="cookie-consent-modal__toggle">
						<input
							type="checkbox"
							id="necessary-cookies"
							checked
							disabled
							class="cookie-consent-modal__toggle-input"
							aria-describedby="necessary-cookies-description"
						>
						<label for="necessary-cookies" class="cookie-consent-modal__toggle-label">
							<span class="cookie-consent-modal__toggle-slider"></span>
							<span class="cookie-consent-modal__toggle-text"><?php echo esc_html( $attributes['necessary_cookies_text'] ?? 'Necessary Cookies' ); ?></span>
						</label>
					</div>
				</div>
			</div>
			
			<!-- Analytics Cookies -->
			<div class="cookie-consent-modal__category">
				<div class="cookie-consent-modal__category-header">
					<div class="cookie-consent-modal__category-info">
						<h4 class="cookie-consent-modal__category-title"><?php echo esc_html( $attributes['analytics_cookies_text'] ?? 'Analytics Cookies' ); ?></h4>
						<p class="cookie-consent-modal__category-description"><?php echo esc_html( $attributes['analytics_description'] ?? '' ); ?></p>
					</div>
					<div class="cookie-consent-modal__toggle">
						<input
							type="checkbox"
							id="analytics-cookies"
							data-wp-bind--checked="state.analyticsConsent"
							data-wp-on--change="actions.toggleAnalytics"
							class="cookie-consent-modal__toggle-input"
							aria-describedby="analytics-cookies-description"
						>
						<label for="analytics-cookies" class="cookie-consent-modal__toggle-label">
							<span class="cookie-consent-modal__toggle-slider"></span>
							<span class="cookie-consent-modal__toggle-text"><?php echo esc_html( $attributes['analytics_cookies_text'] ?? 'Analytics Cookies' ); ?></span>
						</label>
					</div>
				</div>
			</div>
		</div>
		
		<div class="cookie-consent-modal__actions">
			<button
				class="cookie-consent-modal__button cookie-consent-modal__button--primary"
				data-wp-on--click="actions.savePreferences"
				type="button"
			>
				<?php echo esc_html( $attributes['save_button_text'] ?? 'Save Preferences' ); ?>
			</button>
		</div>
	</div>
</div>
