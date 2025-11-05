<?php

namespace AMPortfolioTheme\Helpers;

defined( 'ABSPATH' ) || exit;

class Media_Data {
	public int $media_id;
	public string $mime_type;
	public bool $is_svg;
	public string $alt_text;
	public string $file_path;
	public array $sizes;
	public array $metadata;

	public function __construct(
		int $media_id,
		string $mime_type,
		string $alt_text,
		string $file_path,
		array $sizes,
		array $metadata
	) {
		$this->media_id  = $media_id;
		$this->mime_type = $mime_type;
		$this->is_svg    = 'image/svg+xml' === $mime_type;
		$this->alt_text  = $alt_text;
		$this->file_path = $file_path;
		$this->sizes     = $sizes;
		$this->metadata  = $metadata;
	}
}
