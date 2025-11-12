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
		pll_register_string( 'Project Testimonial - Section Title', 'Client Feedback', self::STRING_GROUP );

		pll_register_string( 'Contact Form - Name Required Error', 'Full name is required', self::STRING_GROUP );
		pll_register_string( 'Contact Form - Email Required Error', 'Please enter a valid email address', self::STRING_GROUP );
		pll_register_string( 'Contact Form - Message Required Error', 'Message is required', self::STRING_GROUP );
		pll_register_string( 'Contact Form - Security Error', 'Security verification failed.', self::STRING_GROUP );
		pll_register_string( 'Contact Form - Submission General Error', 'Sorry, there was an error submitting your form. Please try again.', self::STRING_GROUP );
		pll_register_string( 'Contact Form - Validation Errors Header', 'Please review and correct the highlighted fields', self::STRING_GROUP );
		pll_register_string( 'Contact Form - reCAPTCHA Verification Failed', 'Failed to verify reCAPTCHA. Please try again.', self::STRING_GROUP );

		pll_register_string( 'Contact Form - Subject Label', 'Subject', self::STRING_GROUP );
		pll_register_string( 'Contact Form - Full Name Label', 'Full Name', self::STRING_GROUP );
		pll_register_string( 'Contact Form - Email Label', 'Email', self::STRING_GROUP );
		pll_register_string( 'Contact Form - Message Label', 'Message', self::STRING_GROUP );
	}
}
