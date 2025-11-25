<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<style>
	/* Reset for email clients */
	* {
		margin: 0;
		padding: 0;
		box-sizing: border-box;
	}

	/* Light theme (default) */
	body {
		font-family: Arial, Helvetica, sans-serif;
		line-height: 1.6;
		color: #000;
		background-color: #f8f9fa;
		margin: 0;
		padding: 20px 0;
	}

	.email-container {
		max-width: 600px;
		margin: 0 auto;
		background-color: #fff;
		border-radius: 8px;
		overflow: hidden;
		box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
	}

	.email-header {
		background-color: #06f;
		color: #fff;
		padding: 24px;
		text-align: center;
	}

	.email-header h1 {
		font-size: 24px;
		font-weight: 600;
		margin: 0;
	}

	.email-body {
		padding: 32px 24px;
	}

	.submission-details {
		background-color: #f8f9fa;
		border-radius: 8px;
		padding: 20px;
		margin-bottom: 24px;
	}

	.field-group {
		margin-bottom: 20px;
	}

	.field-label {
		font-weight: 600;
		color: #5a5a5a;
		font-size: 14px;
		margin-bottom: 6px;
		display: block;
	}

	.field-value {
		color: #000;
		background-color: #fff;
		padding: 12px;
		border-radius: 4px;
		border: 1px solid #e5e5e5;
	}

	.message-content {
		line-height: 1.5;
	}

	.message-content h1,
	.message-content h2,
	.message-content h3,
	.message-content h4,
	.message-content h5,
	.message-content h6 {
		margin: 16px 0 8px 0;
		font-weight: 600;
		line-height: 1.3;
		color: #000;
	}

	.message-content h1 {
		font-size: 24px;
		border-bottom: 2px solid #06f;
		padding-bottom: 8px;
	}

	.message-content h2 {
		font-size: 20px;
		border-bottom: 1px solid #e5e5e5;
		padding-bottom: 6px;
	}

	.message-content h3 {
		font-size: 18px;
	}

	.message-content h4 {
		font-size: 16px;
	}

	.message-content h5 {
		font-size: 14px;
	}

	.message-content h6 {
		font-size: 13px;
		color: #5a5a5a;
	}

	.message-content p {
		margin-bottom: 12px;
		line-height: 1.6;
	}

	.message-content strong,
	.message-content b {
		font-weight: 600;
	}

	.message-content em,
	.message-content i {
		font-style: italic;
	}

	.message-content ul,
	.message-content ol {
		margin: 12px 0;
		padding-left: 24px;
	}

	.message-content ul {
		list-style-type: disc;
	}

	.message-content ol {
		list-style-type: decimal;
	}

	.message-content li {
		margin-bottom: 6px;
		line-height: 1.5;
	}

	.message-content ul ul,
	.message-content ol ul {
		list-style-type: circle;
		margin-top: 6px;
		margin-bottom: 6px;
	}

	.message-content ol ol,
	.message-content ul ol {
		list-style-type: lower-latin;
		margin-top: 6px;
		margin-bottom: 6px;
	}

	.message-content blockquote {
		border-left: 4px solid #06f;
		padding: 12px 16px;
		margin: 16px 0;
		background-color: #f0f7ff;
		font-style: italic;
	}

	.message-content blockquote p:last-child {
		margin-bottom: 0;
	}

	.message-content a {
		color: #06f;
		text-decoration: underline;
	}

	.message-content a:hover {
		color: #0052cc;
	}

	.message-content img {
		max-width: 100%;
		height: auto;
		display: block;
		margin: 12px 0;
		border-radius: 4px;
	}

	.email-footer {
		background-color: #f8f9fa;
		padding: 20px 24px;
		border-top: 1px solid #e5e5e5;
		text-align: center;
		color: #8a8a8a;
		font-size: 14px;
	}

	.timestamp {
		color: #8a8a8a;
		font-size: 14px;
		text-align: center;
		margin-bottom: 24px;
	}

	/* Dark theme styles */
	@media (prefers-color-scheme: dark) {
		body {
			background-color: #2a2a2a;
			color: #fff;
		}

		.email-container {
			background-color: #1a1a1a;
			box-shadow: 0 4px 20px rgba(0, 0, 0, 0.6);
		}

		.email-header {
			background-color: #4d94ff;
		}

		.email-body {
			background-color: #1a1a1a;
		}

		.submission-details {
			background-color: #2a2a2a;
		}

		.field-label {
			color: #a0a0a0;
		}

		.field-value {
			color: #fff;
			background-color: #1a1a1a;
			border: 1px solid #3a3a3a;
		}

		.message-content h1,
		.message-content h2,
		.message-content h3,
		.message-content h4,
		.message-content h5,
		.message-content h6 {
			color: #fff;
		}

		.message-content h1 {
			border-bottom: 2px solid #4d94ff;
		}

		.message-content h2 {
			border-bottom: 1px solid #3a3a3a;
		}

		.message-content h6 {
			color: #a0a0a0;
		}

		.message-content blockquote {
			border-left: 4px solid #4d94ff;
			background-color: #1a2a3a;
		}

		.message-content a {
			color: #4d94ff;
		}

		.message-content a:hover {
			color: #7ab2ff;
		}

		.email-footer {
			background-color: #2a2a2a;
			border-top: 1px solid #3a3a3a;
			color: #828282;
		}

		.timestamp {
			color: #828282;
		}
	}

	@media (max-width: 480px) {
		.email-body {
			padding: 24px 16px;
		}

		.email-header {
			padding: 20px 16px;
		}

		.email-header h1 {
			font-size: 20px;
		}
		
		.message-content ul,
		.message-content ol {
			padding-left: 20px;
		}
	}
</style>