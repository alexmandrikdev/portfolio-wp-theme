<?php

namespace AMPortfolioTheme\Components;

use AMPortfolioTheme\Asset_Helper;

defined( 'ABSPATH' ) || exit;

class Project_Card {

	private $post_id;
	private $attributes;
	private $data;

	public function __construct( $post_id, $attributes = array() ) {
		$this->post_id = $post_id;

		if ( get_post_type( $this->post_id ) !== 'project' ) {
			throw new \Exception( 'Invalid post type.' );
		}

		$this->attributes = wp_parse_args(
			$attributes,
			array(
				'class' => '',
			)
		);

		$this->initialize_data();

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	private function initialize_data() {
		$thumbnail_id = get_post_thumbnail_id( $this->post_id );

		$technologies = wp_get_post_terms(
			$this->post_id,
			'project_technology',
			array(
				'fields' => 'names',
			)
		);

		$this->data = array(
			'post_id'      => $this->post_id,
			'title'        => get_the_title( $this->post_id ),
			'excerpt'      => get_the_excerpt( $this->post_id ),
			'permalink'    => get_permalink( $this->post_id ),
			'thumbnail'    => array(
				'url' => get_the_post_thumbnail_url( $this->post_id, 'medium' ),
				'alt' => $thumbnail_id ? get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true ) : '',
			),
			'technologies' => is_array( $technologies ) ? $technologies : array(),
		);
	}

	public function enqueue_assets() {
		Asset_Helper::enqueue_component(
			'project-card',
			array(
				'js' => false,
			)
		);
	}

	public function render() {
		$template_path = get_theme_file_path( 'src/components/project-card/render.php' );

		if ( ! file_exists( $template_path ) ) {
			return '<p>Project card template not found.</p>';
		}

		$attributes = $this->attributes;
		$data       = $this->data;

		ob_start();
		include $template_path;
		return ob_get_clean();
	}

	public static function display( $post_id, $attributes = array() ) {
		$project_card = new self( $post_id, $attributes );
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $project_card->render();
	}

	public static function get( $post_id, $attributes = array() ) {
		$project_card = new self( $post_id, $attributes );
		return $project_card->render();
	}
}
