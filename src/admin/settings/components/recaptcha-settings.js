import { __ } from '@wordpress/i18n';
import { Card, CardBody } from '@wordpress/components';

export function RecaptchaSettings() {
	return (
		<div className="portfolio-settings-section">
			<div className="portfolio-settings-section-header">
				<h2>{ __( 'Google reCAPTCHA Settings', 'portfolio' ) }</h2>
				<p className="description">
					{ __(
						'Configure Google reCAPTCHA for spam protection.',
						'portfolio'
					) }
				</p>
			</div>

			<Card style={ { marginTop: '1rem' } }>
				<CardBody>
					<div className="portfolio-settings-help">
						<h3>{ __( 'Setup Instructions', 'portfolio' ) }</h3>
						<p>
							{ __(
								'To use reCAPTCHA, you need to:',
								'portfolio'
							) }
						</p>
						<ol>
							<li>
								{ __( 'Visit the', 'portfolio' ) }{ ' ' }
								<a
									href="https://www.google.com/recaptcha/admin"
									target="_blank"
									rel="noopener noreferrer"
								>
									{ __(
										'Google reCAPTCHA Admin Console',
										'portfolio'
									) }
								</a>
							</li>
							<li>
								{ __(
									'Register your site and get Site Key and Secret Key',
									'portfolio'
								) }
							</li>
							<li>
								{ __(
									'Define PORTFOLIO_RECAPTCHA_SITE_KEY and PORTFOLIO_RECAPTCHA_SECRET_KEY constants in your wp-config.php file.',
									'portfolio'
								) }
							</li>
						</ol>
					</div>
				</CardBody>
			</Card>
		</div>
	);
}
