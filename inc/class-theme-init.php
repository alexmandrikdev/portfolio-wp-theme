<?php

namespace AMPortfolioTheme;

use AMPortfolioTheme\Admin\Settings_Page;
use AMPortfolioTheme\Emails\Admin_Contact_Notification;
use AMPortfolioTheme\Emails\Sender_Confirmation_Email;

defined( 'ABSPATH' ) || exit;

class Theme_Init {

	public static function init() {
		$self = new self();

		add_action( 'init', array( $self, 'register_block_types' ) );
		add_action( 'wp_enqueue_scripts', array( $self, 'enqueue_scripts' ) );
		add_action( 'wp_head', array( $self, 'add_head_script' ), 10 );

		add_action( 'admin_init', array( $self, 'check_recaptcha_settings' ) );
		add_action( 'admin_init', array( $self, 'check_zoho_settings' ) );
		add_action( 'admin_init', array( $self, 'check_crypto_key' ) );
		$self->register_email_scheduler_actions();

		return $self;
	}

	public function register_block_types() {
		if ( function_exists( 'wp_register_block_types_from_metadata_collection' ) ) {
			wp_register_block_types_from_metadata_collection( get_theme_file_path( '/build/blocks' ), get_theme_file_path( '/build/blocks-manifest.php' ) );
			return;
		}
	}

	public function enqueue_scripts() {
		Asset_Helper::enqueue_component( 'global' );

		$recaptcha_site_key = defined( 'PORTFOLIO_RECAPTCHA_SITE_KEY' ) ? \PORTFOLIO_RECAPTCHA_SITE_KEY : '';
		$settings           = get_option( 'portfolio_theme_settings', array() );
		$ga_id              = $settings['google_analytics_id'] ?? '';

		if ( ! empty( $recaptcha_site_key ) ) {
			wp_enqueue_script(
				'google-recaptcha',
				'https://www.google.com/recaptcha/api.js',
				array(),
				'1.0.0',
				true
			);
		}

		if ( ! empty( $ga_id ) ) {

			wp_enqueue_script(
				'google-analytics-gtag',
				'https://www.googletagmanager.com/gtag/js?id=' . $ga_id,
				array(),
				'1.0.0',
				array(
					'strategy' => 'async',
				)
			);

			wp_add_inline_script(
				'google-analytics-gtag',
				"
				window.dataLayer = window.dataLayer || [];
				function gtag(){dataLayer.push(arguments);}
				gtag('consent', 'default', {
					'ad_storage': 'denied',
					'ad_user_data': 'denied',
					'ad_personalization': 'denied',
					'analytics_storage': 'denied'
				});
				",
				'before'
			);

			wp_add_inline_script(
				'google-analytics-gtag',
				"
				window.dataLayer = window.dataLayer || [];
				function gtag(){dataLayer.push(arguments);}
				gtag('js', new Date());
				gtag('config', '" . esc_js( $ga_id ) . "');
				",
				'after'
			);
		}
	}

	public function add_head_script() {
		?>
		<script type="text/javascript">
			(function() {
				const storedTheme = localStorage.getItem('theme') || 'auto';
				
				let theme = storedTheme;
	  
				if (storedTheme === 'auto') {
					const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
					theme = systemDark ? 'dark' : 'light';
				}
				
				if(theme === 'dark') {
					document.documentElement.setAttribute('data-theme', 'dark');
				} else {
					document.documentElement.removeAttribute('data-theme');
				}
			})();
		</script>
		<?php
	}

	public function register_email_scheduler_actions() {
		add_action(
			Admin_Contact_Notification::SCHEDULED_ACTION_HOOK,
			array( Admin_Contact_Notification::class, 'handle_scheduled_send' ),
			10,
			1
		);

		add_action(
			Sender_Confirmation_Email::SCHEDULED_ACTION_HOOK,
			array( Sender_Confirmation_Email::class, 'handle_scheduled_send' ),
			10,
			1
		);
	}


	public function check_recaptcha_settings() {
		if ( ! current_user_can( 'manage_options' ) || wp_doing_ajax() ) {
			return;
		}

		$recaptcha_site_key   = defined( 'PORTFOLIO_RECAPTCHA_SITE_KEY' ) ? \PORTFOLIO_RECAPTCHA_SITE_KEY : '';
		$recaptcha_secret_key = defined( 'PORTFOLIO_RECAPTCHA_SECRET_KEY' ) ? \PORTFOLIO_RECAPTCHA_SECRET_KEY : '';

		if ( empty( $recaptcha_site_key ) || empty( $recaptcha_secret_key ) ) {
			add_action( 'admin_notices', array( $this, 'recaptcha_missing_notice' ) );
		}
	}

	public function recaptcha_missing_notice() {
		$settings_url = menu_page_url( Settings_Page::PAGE_SLUG, false );
		?>
		<div class="notice notice-warning">
			<h3><?php esc_html_e( 'Portfolio Theme - reCAPTCHA Not Configured', 'am-portfolio-theme' ); ?></h3>
			<p><?php esc_html_e( 'To use the reCAPTCHA functionality for spam protection, please configure your reCAPTCHA keys.', 'am-portfolio-theme' ); ?></p>
			<p>
				<a href="<?php echo esc_url( $settings_url ); ?>&tab=recaptcha" class="button button-primary">
					<?php esc_html_e( 'Configure reCAPTCHA Settings', 'am-portfolio-theme' ); ?>
				</a>
				<a href="https://www.google.com/recaptcha/admin" target="_blank" class="button">
					<?php esc_html_e( 'Get reCAPTCHA Keys', 'am-portfolio-theme' ); ?>
				</a>
			</p>
		</div>
		<?php
	}

	public function check_zoho_settings() {
		if ( ! current_user_can( 'manage_options' ) || wp_doing_ajax() ) {
			return;
		}

		$client_id     = defined( 'PORTFOLIO_ZOHO_CLIENT_ID' ) ? \PORTFOLIO_ZOHO_CLIENT_ID : '';
		$client_secret = defined( 'PORTFOLIO_ZOHO_CLIENT_SECRET' ) ? \PORTFOLIO_ZOHO_CLIENT_SECRET : '';

		if ( empty( $client_id ) || empty( $client_secret ) ) {
			add_action( 'admin_notices', array( $this, 'zoho_missing_notice' ) );
		}
	}

	public function zoho_missing_notice() {
		$settings_url = menu_page_url( Settings_Page::PAGE_SLUG, false );
		?>
		<div class="notice notice-warning">
			<h3><?php esc_html_e( 'Portfolio Theme - Zoho Mail Not Configured', 'am-portfolio-theme' ); ?></h3>
			<p><?php esc_html_e( 'To use Zoho Mail for sending emails, please configure your Zoho Client ID and Client Secret.', 'am-portfolio-theme' ); ?></p>
			<p>
				<a href="<?php echo esc_url( $settings_url ); ?>&tab=zoho-mail" class="button button-primary">
					<?php esc_html_e( 'Configure Zoho Mail Settings', 'am-portfolio-theme' ); ?>
				</a>
				<a href="https://api-console.zoho.com/" target="_blank" class="button">
					<?php esc_html_e( 'Get Zoho API Credentials', 'am-portfolio-theme' ); ?>
				</a>
			</p>
		</div>
		<?php
	}

	/**
	 * Check if the encryption key is defined.
	 */
	public function check_crypto_key() {
		if ( ! current_user_can( 'manage_options' ) || wp_doing_ajax() ) {
			return;
		}

		if ( ! defined( 'PORTFOLIO_CRYPTO_KEY' ) || empty( \PORTFOLIO_CRYPTO_KEY ) ) {
			add_action( 'admin_notices', array( $this, 'crypto_key_missing_notice' ) );
		}
	}

	/**
	 * Admin notice for missing encryption key.
	 */
	public function crypto_key_missing_notice() {
		?>
		<div class="notice notice-warning">
			<h3><?php esc_html_e( 'Portfolio Theme - Encryption Key Missing', 'am-portfolio-theme' ); ?></h3>
			<p><?php esc_html_e( 'To securely store Zoho OAuth tokens, you must define the PORTFOLIO_CRYPTO_KEY constant in your wp-config.php file.', 'am-portfolio-theme' ); ?></p>
			<p>
				<?php esc_html_e( 'Generate a secure key using the following command:', 'am-portfolio-theme' ); ?>
				<code>openssl rand -base64 32</code>
			</p>
			<p>
				<?php esc_html_e( 'Then add this line to your wp-config.php:', 'am-portfolio-theme' ); ?>
				<code>define( 'PORTFOLIO_CRYPTO_KEY', 'your-base64-encoded-key-here' );</code>
			</p>
		</div>
		<?php
	}
}