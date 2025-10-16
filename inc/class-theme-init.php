<?php

namespace AMPortfolioTheme;

defined( 'ABSPATH' ) || exit;

class Theme_Init {

	public static function init() {
		$self = new self();

		add_action( 'init', array( $self, 'register_block_types' ) );
		add_action( 'wp_enqueue_scripts', array( $self, 'enqueue_scripts' ) );

		return $self;
	}

	public function register_block_types() {
		if ( function_exists( 'wp_register_block_types_from_metadata_collection' ) ) {
			wp_register_block_types_from_metadata_collection( get_theme_file_path( '/build/blocks' ), get_theme_file_path( '/build/blocks-manifest.php' ) );
			return;
		}
	}

	public function enqueue_scripts() {
		$asset = include get_theme_file_path( 'build/blocks/global/view.asset.php' );

		wp_enqueue_style(
			'portfolio-style',
			get_parent_theme_file_uri( 'build/blocks/global/style-index.css' ),
			$asset['dependencies'],
			$asset['version']
		);

		wp_enqueue_script(
			'portfolio-script',
			get_parent_theme_file_uri( 'build/blocks/global/view.js' ),
			$asset['dependencies'],
			$asset['version'],
			true
		);
	}
}
