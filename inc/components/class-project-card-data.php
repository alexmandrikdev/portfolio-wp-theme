<?php

namespace AMPortfolioTheme\Components;

defined( 'ABSPATH' ) || exit;

class Project_Card_Data {
	public int $post_id;
	public string $title;
	public string $excerpt;
	public string $permalink;
	public array $thumbnail;
	public array $technologies;

	public function __construct( int $post_id, string $title, string $excerpt, string $permalink, array $thumbnail, array $technologies ) {
		$this->post_id      = $post_id;
		$this->title        = $title;
		$this->excerpt      = $excerpt;
		$this->permalink    = $permalink;
		$this->thumbnail    = $thumbnail;
		$this->technologies = $technologies;
	}
}
