<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$email_data    = get_query_var( 'email_data', array() );
$language      = $email_data['language'];
$show_language = $email_data['show_language'] ?? false;
$show_timezone = $email_data['show_timezone'] ?? false;
?>

<div class="submission-details">
	<?php if ( ! empty( $email_data['subject'] ) ) : ?>
	<div class="field-group">
		<span class="field-label"><?php echo esc_html( pll_translate_string( 'Subject', $language ) ); ?></span>
		<div class="field-value">
			<?php echo esc_html( $email_data['subject'] ); ?>
		</div>
	</div>
	<?php endif; ?>

	<div class="field-group">
		<span class="field-label"><?php echo esc_html( pll_translate_string( 'Full Name', $language ) ); ?></span>
		<div class="field-value"><?php echo esc_html( $email_data['name'] ?? '' ); ?></div>
	</div>

	<div class="field-group">
		<span class="field-label"><?php echo esc_html( pll_translate_string( 'Email', $language ) ); ?></span>
		<div class="field-value"><?php echo esc_html( $email_data['email'] ?? '' ); ?></div>
	</div>

	<div class="field-group">
		<span class="field-label"><?php echo esc_html( pll_translate_string( 'Message', $language ) ); ?></span>
		<div class="field-value message-content"><?php echo wp_kses_post( $email_data['message'] ); ?></div>
	</div>

	<?php if ( ! empty( $email_data['language'] ) && $show_language ) : ?>
	<div class="field-group">
		<span class="field-label"><?php esc_html_e( 'Language', 'am-portfolio-theme' ); ?></span>
		<div class="field-value"><?php echo esc_html( $email_data['language'] ); ?></div>
	</div>
	<?php endif; ?>

	<?php if ( ! empty( $email_data['timezone'] ) && $show_timezone ) : ?>
	<div class="field-group">
		<span class="field-label"><?php echo esc_html( pll_translate_string( 'Timezone', $language ) ); ?></span>
		<div class="field-value"><?php echo esc_html( $email_data['timezone'] ); ?></div>
	</div>
	<?php endif; ?>
</div>