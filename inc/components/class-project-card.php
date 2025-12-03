<?php

namespace AMPortfolioTheme\Components;

use AMPortfolioTheme\Asset_Helper;

defined( 'ABSPATH' ) || exit;

class Project_Card {

	private Project_Card_Data $data;
	private array $attributes;

	public function __construct( Project_Card_Data $data, array $attributes = array() ) {
		$this->data = $data;

		$this->attributes = wp_parse_args(
			$attributes,
			array(
				'class'     => '',
				'img_sizes' => '(max-width: 767px) 100vw, (max-width: 1023px) 50vw, 33vw',
			)
		);

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	public function enqueue_assets(): void {
		Asset_Helper::enqueue_component(
			'project-card',
			array(
				'js' => false,
			)
		);
	}

	public function render(): string {
		$template_path = get_theme_file_path( 'build/components/project-card/render.php' );

		if ( ! file_exists( $template_path ) ) {
			return '<p>Project card template not found.</p>';
		}

		$attributes = $this->attributes;
		$data       = $this->data;

		ob_start();
		include $template_path;
		return ob_get_clean();
	}

	public static function display( Project_Card_Data $data, array $attributes = array() ): void {
		$project_card = new self( $data, $attributes );
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $project_card->render();
	}

	public static function get( Project_Card_Data $data, array $attributes = array() ): string {
		$project_card = new self( $data, $attributes );
		return $project_card->render();
	}
}
