<?php

namespace AMPortfolioTheme\Helpers;

defined( 'ABSPATH' ) || exit;

class Media_Display {

	public static function display_media_item(
		Media_Data $media_data,
		array $args = array()
	): void {
		$defaults = array(
			'as_img'  => false,
			'size'    => 'full',
			'width'   => '',
			'height'  => '',
			'class'   => '',
			'sizes'   => '100vw',
			'loading' => 'lazy',
		);

		$args = wp_parse_args( $args, $defaults );

		if ( ! $media_data->media_id ) {
			return;
		}

		$width  = $args['width'];
		$height = $args['height'];

		if ( empty( $width ) && empty( $height ) ) {
			$size_data = $media_data->sizes[ $args['size'] ] ?? $media_data->sizes['full'] ?? array();
			if ( $size_data ) {
				$width  = $size_data['width'] ?? '';
				$height = $size_data['height'] ?? '';
			}
		}

		if ( empty( $width ) && empty( $height ) ) {
			$width  = $media_data->metadata['width'] ?? '';
			$height = $media_data->metadata['height'] ?? '';
		}

		$class = esc_attr( $args['class'] );

		$output = '';

		if ( $media_data->is_svg && ! $args['as_img'] ) {
			$output = self::render_inline_svg(
				$media_data,
				$class,
				$width,
				$height
			);
		} else {
			$output = self::render_image(
				$media_data,
				$class,
				$width,
				$height,
				$args['size'],
				$args['sizes'],
				$args['loading']
			);
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $output;
	}

	private static function render_inline_svg(
		Media_Data $media_data,
		string $class_name,
		$width,
		$height
	): string {
		if ( ! $media_data->file_path || ! file_exists( $media_data->file_path ) ) {
			return '';
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$svg_content = file_get_contents( $media_data->file_path );
		if ( ! $svg_content ) {
			return '';
		}

		return self::add_attributes_to_svg(
			$svg_content,
			$class_name,
			$width,
			$height
		);
	}

	private static function add_attributes_to_svg(
		string $svg_content,
		string $class_name,
		$width,
		$height
	): string {
		if ( ! preg_match( '/<svg[^>]*>/', $svg_content, $matches ) ) {
			return $svg_content;
		}

		$svg_tag     = $matches[0];
		$new_svg_tag = $svg_tag;

		if ( $class_name ) {
			if ( strpos( $svg_tag, 'class=' ) !== false ) {
				$new_svg_tag = preg_replace(
					'/class="([^"]*)"/',
					'class="$1 ' . esc_attr( $class_name ) . '"',
					$new_svg_tag
				);
			} else {
				$new_svg_tag = preg_replace(
					'/<svg/',
					'<svg class="' . esc_attr( $class_name ) . '"',
					$new_svg_tag
				);
			}
		}

		if ( $width ) {
			if ( strpos( $svg_tag, 'width=' ) !== false ) {
				$new_svg_tag = preg_replace(
					'/width="[^"]*"/',
					'width="' . intval( $width ) . '"',
					$new_svg_tag
				);
			} else {
				$new_svg_tag = preg_replace(
					'/<svg/',
					'<svg width="' . intval( $width ) . '"',
					$new_svg_tag
				);
			}
		}

		if ( $height ) {
			if ( strpos( $svg_tag, 'height=' ) !== false ) {
				$new_svg_tag = preg_replace(
					'/height="[^"]*"/',
					'height="' . intval( $height ) . '"',
					$new_svg_tag
				);
			} else {
				$new_svg_tag = preg_replace(
					'/<svg/',
					'<svg height="' . intval( $height ) . '"',
					$new_svg_tag
				);
			}
		}

		return str_replace( $svg_tag, $new_svg_tag, $svg_content );
	}

	private static function render_image(
		Media_Data $media_data,
		string $class_name,
		$width,
		$height,
		string $size,
		string $sizes = '100vw',
		string $loading = 'lazy'
	): string {
		$size_data = $media_data->sizes[ $size ] ?? $media_data->sizes['full'] ?? array();
		$image_url = $size_data['url'] ?? '';

		if ( empty( $image_url ) ) {
			return '';
		}

		$img_attrs = array(
			'src'     => esc_url( $image_url ),
			'alt'     => esc_attr( $media_data->alt_text ),
			'loading' => esc_attr( $loading ),
		);

		if ( $class_name ) {
			$img_attrs['class'] = esc_attr( $class_name );
		}

		if ( $width ) {
			$img_attrs['width'] = intval( $width );
		} else {
			$size_width = $size_data['width'] ?? 0;
			if ( $size_width > 0 ) {
				$img_attrs['width'] = $size_width;
			}
		}

		if ( $height ) {
			$img_attrs['height'] = intval( $height );
		} else {
			$size_height = $size_data['height'] ?? 0;
			if ( $size_height > 0 ) {
				$img_attrs['height'] = $size_height;
			}
		}

		$srcset = self::generate_srcset( $media_data );
		if ( ! empty( $srcset ) ) {
			$img_attrs['srcset'] = $srcset;
			$img_attrs['sizes']  = esc_attr( $sizes );
		}

		$img_html = '<img';
		foreach ( $img_attrs as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$img_html .= ' ' . $attr . '="' . $value . '"';
			}
		}
		$img_html .= ' />';

		return $img_html;
	}

	private static function generate_srcset( Media_Data $media_data ): string {
		$srcset_parts = array();

		foreach ( $media_data->sizes as $size_name => $size_data ) {
			if ( ! empty( $size_data['url'] ) && ! empty( $size_data['width'] ) ) {
				$srcset_parts[] = esc_url( $size_data['url'] ) . ' ' . intval( $size_data['width'] ) . 'w';
			}
		}

		return implode( ', ', $srcset_parts );
	}
}
