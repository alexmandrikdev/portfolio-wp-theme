<?php

namespace AMPortfolioTheme;

class Theme_Init
{
    public static function init()
    {
        $self = new self();

        add_action('init', [$self, 'register_block_types']);
        add_action('wp_enqueue_scripts', [$self, 'enqueue_scripts']);

        return $self;
    }

    public function register_block_types()
    {
        if (function_exists('wp_register_block_types_from_metadata_collection')) {
            // wp_register_block_types_from_metadata_collection(__DIR__ . '/build/blocks', __DIR__ . '/build/blocks-manifest.php');
            return;
        }
    }

    public function enqueue_scripts()
    {
        wp_enqueue_style(
            'portfolio-style',
            get_parent_theme_file_uri('build/index.css'),
        );
    }
}
