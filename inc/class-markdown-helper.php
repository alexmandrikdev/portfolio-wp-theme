<?php

namespace AMPortfolioTheme\Helpers;

use Exception;
use Parsedown;

defined( 'ABSPATH' ) || exit;

class Markdown_Helper {

	public static function parse( $markdown_text ) {
		if ( empty( $markdown_text ) ) {
			return '';
		}

		try {
			$parsedown = new Parsedown();
			$parsedown->setSafeMode( true );
			$parsedown->setMarkupEscaped( true );

			$html = $parsedown->text( $markdown_text );

			$html = wp_kses_post( $html );

			return $html;

		} catch ( Exception $e ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'Markdown parsing error: ' . $e->getMessage() );
			}

			return esc_html( $markdown_text );
		}
	}
}
