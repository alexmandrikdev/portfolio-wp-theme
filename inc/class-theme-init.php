<?php

namespace AMPortfolioTheme;

defined( 'ABSPATH' ) || exit;

class Theme_Init {

	public static function init() {
		$self = new self();

		add_action( 'init', array( $self, 'register_block_types' ) );
		add_action( 'wp_enqueue_scripts', array( $self, 'enqueue_scripts' ) );
		add_action( 'wp_head', array( $self, 'add_head_script' ), 10 );

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
}
