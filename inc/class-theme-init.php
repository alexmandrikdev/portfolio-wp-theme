<?php

namespace AMPortfolioTheme;

use AMPortfolioTheme\Admin\Settings_Page;

defined( 'ABSPATH' ) || exit;

class Theme_Init {

	public static function init() {
		$self = new self();

		add_action( 'init', array( $self, 'register_block_types' ) );
		add_action( 'wp_enqueue_scripts', array( $self, 'enqueue_scripts' ) );
		add_action( 'wp_head', array( $self, 'add_head_script' ), 10 );

		add_action( 'admin_init', array( $self, 'check_recaptcha_settings' ) );

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

		$settings           = get_option( 'portfolio_theme_settings', array() );
		$recaptcha_site_key = $settings['recaptcha_site_key'] ?? '';

		if ( ! empty( $recaptcha_site_key ) ) {
			wp_enqueue_script(
				'google-recaptcha',
				'https://www.google.com/recaptcha/api.js',
				array(),
				'1.0.0',
				true
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

	public function check_recaptcha_settings() {
		if ( ! current_user_can( 'manage_options' ) || wp_doing_ajax() ) {
			return;
		}

		$settings             = get_option( 'portfolio_theme_settings', array() );
		$recaptcha_site_key   = $settings['recaptcha_site_key'] ?? '';
		$recaptcha_secret_key = $settings['recaptcha_secret_key'] ?? '';

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
				<a href="<?php echo esc_url( $settings_url ); ?>" class="button button-primary">
					<?php esc_html_e( 'Configure reCAPTCHA Settings', 'am-portfolio-theme' ); ?>
				</a>
				<a href="https://www.google.com/recaptcha/admin" target="_blank" class="button">
					<?php esc_html_e( 'Get reCAPTCHA Keys', 'am-portfolio-theme' ); ?>
				</a>
			</p>
		</div>
		<?php
	}
}