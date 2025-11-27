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

		pll_register_string( 'Project Card - Button Text', 'View Project Details', self::STRING_GROUP );

		pll_register_string( 'Project Hero - Back to Portfolio', 'Back to Portfolio', self::STRING_GROUP );
		pll_register_string( 'Project Hero - View Live Project', 'View Live Project', self::STRING_GROUP );
		pll_register_string( 'Project Overview - The Solution Title', 'The Solution', self::STRING_GROUP );
		pll_register_string( 'Project Overview - The Brief Title', 'The Brief', self::STRING_GROUP );
		pll_register_string( 'Project Results - Section Title', 'The Results', self::STRING_GROUP );
		pll_register_string( 'Project Results - Load More', 'Load More', self::STRING_GROUP );
		pll_register_string( 'Project Tech Details - Section Title', 'Technical Implementation', self::STRING_GROUP );
		pll_register_string( 'Project Tech Details - Tech Stack Title', 'Tech Stack', self::STRING_GROUP );
		pll_register_string( 'Project Tech Details - Challenges Title', 'Key Challenges & Solutions', self::STRING_GROUP );
		pll_register_string( 'Project Tech Details - Solution Title', 'Solution:', self::STRING_GROUP );
		pll_register_string( 'Project Testimonial - Section Title', 'Client Feedback', self::STRING_GROUP );

		pll_register_string( 'Error 404 - Title', 'Error 404 - Title', self::STRING_GROUP );
		pll_register_string( 'Error 404 - Message', 'Error 404 - Message', self::STRING_GROUP, true );
		pll_register_string( 'Error 404 - Home Button', 'Error 404 - Home Button', self::STRING_GROUP );

		pll_register_string( 'Contact Form - Name Required Error', 'Full name is required', self::STRING_GROUP );
		pll_register_string( 'Contact Form - Email Required Error', 'Please enter a valid email address', self::STRING_GROUP );
		pll_register_string( 'Contact Form - Message Required Error', 'Message is required', self::STRING_GROUP );
		pll_register_string( 'Contact Form - Security Error', 'Security verification failed.', self::STRING_GROUP );
		pll_register_string( 'Contact Form - Submission General Error', 'Sorry, there was an error submitting your form. Please try again.', self::STRING_GROUP, true );
		pll_register_string( 'Contact Form - Validation Errors Header', 'Please review and correct the highlighted fields', self::STRING_GROUP, true );
		pll_register_string( 'Contact Form - reCAPTCHA Verification Failed', 'Failed to verify reCAPTCHA. Please try again.', self::STRING_GROUP, true );

		pll_register_string( 'Contact Form - Subject Label', 'Subject', self::STRING_GROUP );
		pll_register_string( 'Contact Form - Full Name Label', 'Full Name', self::STRING_GROUP );
		pll_register_string( 'Contact Form - Email Label', 'Email', self::STRING_GROUP );
		pll_register_string( 'Contact Form - Message Label', 'Message', self::STRING_GROUP );

		pll_register_string( 'Email - Thank You for Reaching Out', 'Thank You for Reaching Out', self::STRING_GROUP );
		pll_register_string( 'Email - Your Message Has Been Received', 'Your Message Has Been Received', self::STRING_GROUP, true );
		pll_register_string( 'Email - Message Received Description', 'I\'ve received your contact form submission and will review it carefully.', self::STRING_GROUP, true );
		pll_register_string( 'Email - Summary Header', 'Here\'s a summary of the information you provided:', self::STRING_GROUP, true );
		pll_register_string( 'Email - Submitted On Timestamp', 'Submitted on [date] at [time]', self::STRING_GROUP );
		pll_register_string( 'Email - What Happens Next', 'What Happens Next?', self::STRING_GROUP );
		pll_register_string( 'Email - Review Requirements', 'I\'ll review your project requirements within 24 hours', self::STRING_GROUP, true );
		pll_register_string( 'Email - Personalized Response', 'You\'ll receive a personalized response with initial thoughts and questions', self::STRING_GROUP, true );
		pll_register_string( 'Email - Schedule Call', 'We may schedule a call to discuss your project in more detail', self::STRING_GROUP, true );
		pll_register_string( 'Email - Project Proposal', 'I\'ll provide a project proposal if we decide to move forward', self::STRING_GROUP, true );
		pll_register_string( 'Email - Project Excitement', 'I\'m excited to learn more about your project and explore how we can bring your vision to life with high-performance web solutions.', self::STRING_GROUP, true );
		pll_register_string( 'Email - Additional Questions', 'If you have any additional questions, feel free to reply directly to this email.', self::STRING_GROUP, true );
		pll_register_string( 'Email - Best Regards', 'Best regards,', self::STRING_GROUP );
		pll_register_string( 'Email - View My Portfolio', 'View My Portfolio', self::STRING_GROUP );
		pll_register_string( 'Email - Automated Confirmation', 'This is an automated confirmation of your contact form submission.', self::STRING_GROUP );
	}
}
