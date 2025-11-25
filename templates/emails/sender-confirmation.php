<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title><?php echo esc_html( pll_translate_string( 'Thank You for Reaching Out', $data['language'] ) ); ?></title>
	
	<?php
	$allowed_email_html = array(
		'strong' => array(),
		'em'     => array(),
		'b'      => array(),
		'i'      => array(),
		'u'      => array(),
		'br'     => array(),
	);
	?>
	
	<?php get_template_part( 'templates/emails/partials/styles' ); ?>
	
	<style>
		.confirmation-icon {
			text-align: center;
			margin-bottom: 24px;
		}

		.confirmation-icon div {
			background-color: #0c8;
			color: #fff;
			width: 60px;
			height: 60px;
			border-radius: 50%;
			display: inline-block;
			text-align: center;
			line-height: 60px;
			font-size: 28px;
			font-weight: bold;
		}

		.thank-you-message {
			text-align: center;
			margin-bottom: 32px;
		}

		.thank-you-message h2 {
			font-size: 20px;
			font-weight: 600;
			margin-bottom: 12px;
			color: #000;
		}

		.thank-you-message p {
			color: #5a5a5a;
			font-size: 16px;
		}

		.details-header {
			font-weight: 600;
			color: #5a5a5a;
			font-size: 16px;
			margin-bottom: 16px;
		}

		.next-steps {
			background-color: #f0f7ff;
			border-radius: 8px;
			padding: 20px;
			margin-bottom: 24px;
			border-left: 4px solid #06f;
		}

		.next-steps h3 {
			font-weight: 600;
			margin-bottom: 12px;
			color: #000;
		}

		.next-steps ul {
			padding-left: 20px;
		}

		.next-steps li {
			margin-bottom: 8px;
			color: #5a5a5a;
		}

		.contact-info {
			text-align: center;
			margin-top: 32px;
			padding-top: 24px;
			border-top: 1px solid #e5e5e5;
		}

		.contact-info p {
			color: #5a5a5a;
			margin-bottom: 8px;
		}

		.portfolio-link {
			color: #06f;
			text-decoration: none;
			font-weight: 600;
		}

		/* Dark theme specific styles */
		@media (prefers-color-scheme: dark) {
			.thank-you-message h2 {
				color: #fff;
			}

			.thank-you-message p {
				color: #a0a0a0;
			}

			.details-header {
				color: #a0a0a0;
			}

			.next-steps {
				background-color: #1a2a3a;
				border-left-color: #4d94ff;
			}

			.next-steps h3 {
				color: #fff;
			}

			.next-steps li {
				color: #a0a0a0;
			}

			.contact-info {
				border-top-color: #3a3a3a;
			}

			.contact-info p {
				color: #a0a0a0;
			}

			.portfolio-link {
				color: #4d94ff;
			}
		}

		@media (max-width: 480px) {
			.confirmation-icon div {
				width: 50px;
				height: 50px;
				font-size: 24px;
			}
		}
	</style>
</head>
<body>
	<div class="email-container">
		<div class="email-header">
			<h1><?php echo esc_html( pll_translate_string( 'Thank You for Reaching Out', $data['language'] ) ); ?></h1>
		</div>

		<div class="email-body">
			<div class="confirmation-icon">
				<div>✓</div>
			</div>

			<div class="thank-you-message">
				<h2><?php echo esc_html( pll_translate_string( 'Your Message Has Been Received', $data['language'] ) ); ?></h2>
				<p>
					<?php echo wp_kses( pll_translate_string( 'I\'ve received your contact form submission and will review it carefully.', $data['language'] ), $allowed_email_html ); ?>
				</p>

				<p>
					<?php
					echo esc_html( pll_translate_string( 'Here\'s a summary of the information you provided:', $data['language'] ) );
					?>
				</p>
			</div>

			<div class="timestamp">
				<?php
				$timestamp_string = pll_translate_string( 'Submitted on [date] at [time]', $data['language'] );
				$timestamp_string = str_replace(
					array( '[date]', '[time]' ),
					array( $data['date'], $data['time'] ),
					$timestamp_string
				);
				echo esc_html( $timestamp_string );
				?>
			</div>

			<?php
				$email_data = array_merge(
					$data,
					array(
						'show_language' => false,
						'show_timezone' => false,
					)
				);
				set_query_var( 'email_data', $email_data );
				get_template_part( 'templates/emails/partials/submission-details' );
				?>

			<div class="next-steps">
				<h3><?php echo esc_html( pll_translate_string( 'What Happens Next?', $data['language'] ) ); ?></h3>
				<ul>
					<li><?php echo wp_kses( pll_translate_string( 'I\'ll review your project requirements within 24 hours', $data['language'] ), $allowed_email_html ); ?></li>
					<li>
						<?php echo wp_kses( pll_translate_string( 'You\'ll receive a personalized response with initial thoughts and questions', $data['language'] ), $allowed_email_html ); ?>
					</li>
					<li>
						<?php echo wp_kses( pll_translate_string( 'We may schedule a call to discuss your project in more detail', $data['language'] ), $allowed_email_html ); ?>
					</li>
					<li>
						<?php echo wp_kses( pll_translate_string( 'I\'ll provide a project proposal if we decide to move forward', $data['language'] ), $allowed_email_html ); ?>
					</li>
				</ul>
			</div>

			<div class="contact-info">
				<p>
					<?php
					echo wp_kses(
						pll_translate_string( 'I\'m excited to learn more about your project and explore how we can bring your vision to life with high-performance web solutions.', $data['language'] ),
						$allowed_email_html
					);
					?>
				</p>
				<p>
					<?php echo wp_kses( pll_translate_string( 'If you have any additional questions, feel free to reply directly to this email.', $data['language'] ), $allowed_email_html ); ?>
				</p>
				<p>
					<?php echo wp_kses( str_replace( '[name]', $data['your_name'], pll_translate_string( 'Best regards,', $data['language'] ) ), $allowed_email_html ); ?><br />
				</p>
				<p>
					<a href="<?php echo esc_url( $data['portfolio_url'] ); ?>" class="portfolio-link">
						<?php echo esc_html( pll_translate_string( 'View My Portfolio', $data['language'] ) ); ?>
					</a>
				</p>
			</div>
		</div>

		<div class="email-footer">
			<p>
				<?php echo wp_kses( pll_translate_string( 'This is an automated confirmation of your contact form submission.', $data['language'] ), $allowed_email_html ); ?>
			</p>
		</div>
	</div>
</body>
</html>