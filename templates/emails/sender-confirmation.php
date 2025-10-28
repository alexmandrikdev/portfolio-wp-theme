<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title><?php echo esc_html( __( 'Thank You for Your Message', 'am-portfolio-theme' ) ); ?></title>
	
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
			display: inline-flex;
			align-items: center;
			justify-content: center;
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
			transition: color 0.3s ease;
		}

		.thank-you-message p {
			color: #5a5a5a;
			font-size: 16px;
			transition: color 0.3s ease;
		}

		.details-header {
			font-weight: 600;
			color: #5a5a5a;
			font-size: 16px;
			margin-bottom: 16px;
			transition: color 0.3s ease;
		}

		.next-steps {
			background-color: #f0f7ff;
			border-radius: 8px;
			padding: 20px;
			margin-bottom: 24px;
			border-left: 4px solid #06f;
			transition: background-color 0.3s ease;
		}

		.next-steps h3 {
			font-weight: 600;
			margin-bottom: 12px;
			color: #000;
			transition: color 0.3s ease;
		}

		.next-steps ul {
			padding-left: 20px;
		}

		.next-steps li {
			margin-bottom: 8px;
			color: #5a5a5a;
			transition: color 0.3s ease;
		}

		.contact-info {
			text-align: center;
			margin-top: 32px;
			padding-top: 24px;
			border-top: 1px solid #e5e5e5;
			transition: border-color 0.3s ease;
		}

		.contact-info p {
			color: #5a5a5a;
			margin-bottom: 8px;
			transition: color 0.3s ease;
		}

		.portfolio-link {
			color: #06f;
			text-decoration: none;
			font-weight: 600;
			transition: color 0.3s ease;
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
			<h1><?php echo esc_html( __( 'Thank You for Reaching Out', 'am-portfolio-theme' ) ); ?></h1>
		</div>

		<div class="email-body">
			<div class="confirmation-icon">
				<div>✓</div>
			</div>

			<div class="thank-you-message">
				<h2><?php echo esc_html( __( 'Your Message Has Been Received', 'am-portfolio-theme' ) ); ?></h2>
				<p>
					<?php echo esc_html( __( "I've received your contact form submission and will review it carefully. Here's a copy of the information you provided:", 'am-portfolio-theme' ) ); ?>
				</p>
			</div>

			<div class="timestamp">
				<?php
				printf(
					/* translators: 1: date, 2: time */
					esc_html__( 'Submitted on %1$s at %2$s', 'am-portfolio-theme' ),
					esc_html( $data['date'] ),
					esc_html( $data['time'] )
				);
				?>
			</div>

			<?php
				set_query_var( 'email_data', $data );
				get_template_part( 'templates/emails/partials/submission-details' );
			?>

			<div class="next-steps">
				<h3><?php echo esc_html( __( 'What Happens Next?', 'am-portfolio-theme' ) ); ?></h3>
				<ul>
					<li><?php echo esc_html( __( "I'll review your project requirements within 24 hours", 'am-portfolio-theme' ) ); ?></li>
					<li>
						<?php echo esc_html( __( "You'll receive a personalized response with initial thoughts and questions", 'am-portfolio-theme' ) ); ?>
					</li>
					<li>
						<?php echo esc_html( __( 'We may schedule a call to discuss your project in more detail', 'am-portfolio-theme' ) ); ?>
					</li>
					<li>
						<?php echo esc_html( __( "I'll provide a project proposal if we decide to move forward", 'am-portfolio-theme' ) ); ?>
					</li>
				</ul>
			</div>

			<div class="contact-info">
				<p>
					<?php echo esc_html( __( 'If you have any additional questions, feel free to reply directly to this email.', 'am-portfolio-theme' ) ); ?>
				</p>
				<p>
					<?php echo esc_html( __( 'Best regards,', 'am-portfolio-theme' ) ); ?><br />
					<strong><?php echo esc_html( $data['your_name'] ); ?></strong>
				</p>
				<p>
					<a href="<?php echo esc_url( $data['portfolio_url'] ); ?>" class="portfolio-link">
						<?php echo esc_html( __( 'View My Portfolio', 'am-portfolio-theme' ) ); ?>
					</a>
				</p>
			</div>
		</div>

		<div class="email-footer">
			<p>
				<?php echo esc_html( __( 'This is an automated confirmation of your contact form submission.', 'am-portfolio-theme' ) ); ?>
			</p>
			<p>
				<?php echo esc_html( __( 'Please do not reply to this email if you need to make changes to your submission.', 'am-portfolio-theme' ) ); ?>
			</p>
		</div>
	</div>
</body>
</html>