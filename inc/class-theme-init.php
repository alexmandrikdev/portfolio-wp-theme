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
			// phpcs:ignore Squiz.PHP.CommentedOutCode.Found, Squiz.Commenting.InlineComment.InvalidEndChar
			// wp_register_block_types_from_metadata_collection( __DIR__ . '/build/blocks', __DIR__ . '/build/blocks-manifest.php' );
			return;
		}
	}

	public function enqueue_scripts() {
		$asset = include get_theme_file_path( 'build/index.asset.php' );

		wp_enqueue_style(
			'portfolio-style',
			get_parent_theme_file_uri( 'build/index.css' ),
			$asset['dependencies'],
			$asset['version']
		);
	}
}
