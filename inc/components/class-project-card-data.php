<?php

namespace AMPortfolioTheme\Components;

use AMPortfolioTheme\Helpers\Media_Data;

defined( 'ABSPATH' ) || exit;

class Project_Card_Data {
	public int $post_id;
	public string $title;
	public string $excerpt;
	public string $permalink;
	public ?Media_Data $thumbnail;
	public array $technologies;
	public array $types;

	public function __construct(
		int $post_id,
		string $title,
		string $excerpt,
		string $permalink,
		?Media_Data $thumbnail,
		array $technologies,
		array $types
	) {
		$this->post_id      = $post_id;
		$this->title        = $title;
		$this->excerpt      = $excerpt;
		$this->permalink    = $permalink;
		$this->thumbnail    = $thumbnail;
		$this->technologies = $technologies;
		$this->types        = $types;
	}
}
