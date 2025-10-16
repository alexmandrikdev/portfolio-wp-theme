<?php
function my_theme_init()
{
	if (function_exists('wp_register_block_types_from_metadata_collection')) {
		// wp_register_block_types_from_metadata_collection(__DIR__ . '/build/blocks', __DIR__ . '/build/blocks-manifest.php');
		return;
	}
}
add_action('init', 'my_theme_init');

add_action('wp_enqueue_scripts', 'portfolio_enqueue_styles');

function portfolio_enqueue_styles()
{
	wp_enqueue_style(
		'portfolio-style',
		get_parent_theme_file_uri('build/index.css'),
	);
}
