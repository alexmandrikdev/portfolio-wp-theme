<?php

namespace AMPortfolioTheme;

defined( 'ABSPATH' ) || exit;

class Asset_Helper {

	public static function enqueue_component( $component_name, $options = array() ) {
		$defaults = array(
			'handle_prefix' => 'portfolio',
			'js_in_footer'  => true,
			'css'           => true,
			'js'            => true,
		);

		$options = wp_parse_args( $options, $defaults );

		$asset_path = get_theme_file_path( "build/components/{$component_name}/index.asset.php" );

		if ( ! file_exists( $asset_path ) ) {
			return false;
		}

		$asset = include $asset_path;

		$handle = "{$options['handle_prefix']}-{$component_name}";

		if ( $options['css'] ) {
			$css_path = "build/components/{$component_name}/style-index.css";
			if ( file_exists( get_theme_file_path( $css_path ) ) ) {
				wp_enqueue_style(
					$handle . '-style',
					get_parent_theme_file_uri( $css_path ),
					$asset['dependencies'] ?? array(),
					$asset['version']
				);
			}
		}

		if ( $options['js'] ) {
			$js_path = "build/components/{$component_name}/index.js";

			if ( file_exists( get_theme_file_path( $js_path ) ) ) {
				wp_enqueue_script_module(
					$handle . '-script',
					get_parent_theme_file_uri( $js_path ),
					$asset['dependencies'] ?? array(),
					$asset['version'],
					array(
						'in_footer' => $options['js_in_footer'],
					)
				);
			}
		}

		return true;
	}
}
