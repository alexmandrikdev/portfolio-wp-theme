<?php

namespace AMPortfolioTheme\Admin;

defined( 'ABSPATH' ) || exit;

class Technology_Order_Page {

	const PAGE_SLUG = 'portfolio-technology-order';

	public static function init() {
		$self = new self();

		add_action( 'admin_menu', array( $self, 'add_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $self, 'enqueue_scripts' ) );

		return $self;
	}

	/**
	 * Add admin menu item under Projects menu.
	 */
	public function add_admin_menu() {
		add_submenu_page(
			'edit.php?post_type=project',
			__( 'Technology Order', 'am-portfolio-theme' ),
			__( 'Technology Order', 'am-portfolio-theme' ),
			'manage_options',
			self::PAGE_SLUG,
			array( $this, 'render_page' )
		);
	}

	/**
	 * Enqueue scripts and styles for the page.
	 *
	 * @param string $hook The current admin page.
	 */
	public function enqueue_scripts( $hook ) {
		if ( 'project_page_' . self::PAGE_SLUG !== $hook ) {
			return;
		}

		$this->enqueue_react_app();
	}

	/**
	 * Enqueue the React app.
	 */
	private function enqueue_react_app() {
		$asset_path = get_theme_file_path( 'build/admin/technology-order/index.asset.php' );

		if ( ! file_exists( $asset_path ) ) {
			wp_die( 'The technology ordering interface is not available. Please contact your administrator.' );
		}

		$asset = include $asset_path;

		$css_path = 'build/admin/technology-order/style-index.css';
		if ( file_exists( get_theme_file_path( $css_path ) ) ) {
			wp_enqueue_style(
				'portfolio-technology-order-style',
				get_parent_theme_file_uri( $css_path ),
				array(),
				$asset['version']
			);
		}

		wp_enqueue_script(
			'portfolio-technology-order-script',
			get_parent_theme_file_uri( 'build/admin/technology-order/index.js' ),
			$asset['dependencies'],
			$asset['version'],
			true
		);

		wp_enqueue_style( 'wp-components' );
	}

	/**
	 * Render the admin page.
	 */
	public function render_page() {
		?>
		<div class="wrap">
			<div class="portfolio-technology-order-header">
				<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			</div>

			<div id="portfolio-technology-order-app">
				<div class="portfolio-technology-order-loading">
					<div class="spinner is-active" style="float:left;"></div>
					<p><?php esc_html_e( 'Loading technologies...', 'am-portfolio-theme' ); ?></p>
				</div>
			</div>

			<noscript>
				<div class="notice notice-error">
					<p><?php esc_html_e( 'JavaScript is required to use the technology order page. Please enable JavaScript in your browser.', 'am-portfolio-theme' ); ?></p>
				</div>
			</noscript>
		</div>
		<?php
	}
}