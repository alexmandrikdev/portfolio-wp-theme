<?php

namespace AMPortfolioTheme;

class Polylang_String_Registration {

	const STRING_GROUP = 'AM Portfolio Theme';

	public static function init() {
		$self = new self();
		add_action( 'init', array( $self, 'register_strings' ) );
	}

	public function register_strings() {
		if ( ! function_exists( 'pll_register_string' ) ) {
			return;
		}

		pll_register_string( 'Project Overview - The Solution Title', 'The Solution', self::STRING_GROUP );
		pll_register_string( 'Project Overview - The Brief Title', 'The Brief', self::STRING_GROUP );
		pll_register_string( 'Project Results - Section Title', 'The Results', self::STRING_GROUP );
		pll_register_string( 'Project Tech Details - Section Title', 'Technical Implementation', self::STRING_GROUP );
		pll_register_string( 'Project Tech Details - Tech Stack Title', 'Tech Stack', self::STRING_GROUP );
		pll_register_string( 'Project Tech Details - Challenges Title', 'Key Challenges & Solutions', self::STRING_GROUP );
		pll_register_string( 'Project Tech Details - Solution Title', 'Solution:', self::STRING_GROUP );
	}
}
