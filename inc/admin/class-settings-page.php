<?php

namespace AMPortfolioTheme\Admin;

defined( 'ABSPATH' ) || exit;

class Settings_Page {

	const PAGE_SLUG = 'portfolio-settings';

	const SETTINGS_GROUP = 'portfolio_settings';

	const OPTION_NAME = 'portfolio_theme_settings';

	public static function init() {
		$self = new self();

		add_action( 'admin_menu', array( $self, 'add_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $self, 'enqueue_scripts' ) );

		return $self;
	}

	public function add_admin_menu() {
		add_theme_page(
			__( 'Portfolio Settings', 'portfolio' ),
			__( 'Portfolio Settings', 'portfolio' ),
			'manage_options',
			self::PAGE_SLUG,
			array( $this, 'render_settings_page' )
		);
	}

	public function enqueue_scripts( $hook ) {
		if ( 'appearance_page_' . self::PAGE_SLUG !== $hook ) {
			return;
		}

		$this->enqueue_react_app();

		$this->localize_script();
	}

	private function enqueue_react_app() {
		$asset_path = get_theme_file_path( 'build/admin/settings/index.asset.php' );

		if ( ! file_exists( $asset_path ) ) {
			wp_die( 'React settings app not built. Please run npm run build.' );
		}

		$asset = include $asset_path;

		$css_path = 'build/admin/settings/style-index.css';
		if ( file_exists( get_theme_file_path( $css_path ) ) ) {
			wp_enqueue_style(
				'portfolio-settings-style',
				get_parent_theme_file_uri( $css_path ),
				$asset['dependencies'],
				$asset['version']
			);
		}

		wp_enqueue_script(
			'portfolio-settings-script',
			get_parent_theme_file_uri( 'build/admin/settings/index.js' ),
			$asset['dependencies'],
			$asset['version'],
			true
		);

		wp_enqueue_style( 'wp-components' );
	}

	private function localize_script() {
		$settings = $this->get_current_settings();

		// Generate a state token for Zoho OAuth.
		$state         = wp_generate_password( 32, false );
		$transient_key = 'zoho_oauth_state_' . $state;
		set_transient( $transient_key, 'valid', 15 * MINUTE_IN_SECONDS );

		wp_localize_script(
			'portfolio-settings-script',
			'portfolioSettings',
			array(
				'api'              => array(
					'path' => 'portfolio/v1/settings',
				),
				'settings'         => $settings,
				'languages'        => Settings_Helper::get_polylang_languages(),
				'pages'            => Settings_Helper::get_all_pages_by_language(),
				'zoho_oauth_state' => $state,
			)
		);
	}

	private function get_current_settings() {
		return Settings_Helper::get_current_settings();
	}

	public function render_settings_page() {
		?>
		<div class="wrap">
			<div class="portfolio-settings-header">
				<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
				<p class="description">
					<?php esc_html_e( 'Customize your portfolio theme settings.', 'portfolio' ); ?>
				</p>
			</div>

			<div id="portfolio-settings-app">
				<div class="portfolio-settings-loading">
					<div class="spinner is-active" style="float:left;"></div>
					<p><?php esc_html_e( 'Loading settings...', 'portfolio' ); ?></p>
				</div>
			</div>

			<noscript>
				<div class="notice notice-error">
					<p><?php esc_html_e( 'JavaScript is required to use the settings page. Please enable JavaScript in your browser.', 'portfolio' ); ?></p>
				</div>
			</noscript>
		</div>
		<?php
	}
}