<?php

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'dump' ) ) {
	function dump( ...$vars ) {
		echo '<pre>';
		foreach ( $vars as $var ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_dump
			var_dump( $var );
		}
		echo '</pre>';
	}
}

if ( ! function_exists( 'dd' ) ) {
	function dd( ...$vars ) {
		dump( ...$vars );
		die();
	}
}

if ( ! function_exists( 'log_message' ) ) {
	/**
	 * Log data to error log with configurable log level.
	 *
	 * - 'error' and 'warning' levels are always logged, regardless of WP_DEBUG.
	 * - 'info' and 'debug' levels are logged only when WP_DEBUG is enabled.
	 *
	 * @param mixed  $data  Data to log. Arrays and objects will be printed with print_r.
	 * @param string $scope Optional scope identifier to prefix the log message.
	 * @param string $level Log level: 'error', 'warning', 'info', 'debug'. Default 'debug'.
	 */
	function log_message( $data, $scope = '', $level = 'debug' ) {
		$should_log = false;
		switch ( $level ) {
			case 'error':
			case 'warning':
				$should_log = true;
				break;
			case 'info':
			case 'debug':
			default:
				$should_log = defined( 'WP_DEBUG' ) && WP_DEBUG;
				break;
		}

		if ( ! $should_log ) {
			return;
		}

		$prefix = '';
		if ( ! empty( $scope ) ) {
			$prefix = '[' . $scope . ']: ';
		}

		if ( is_array( $data ) || is_object( $data ) ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log, WordPress.PHP.DevelopmentFunctions.error_log_print_r
			error_log( '[' . strtoupper( $level ) . '] ' . $prefix . print_r( $data, true ) );
		} else {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( '[' . strtoupper( $level ) . '] ' . $prefix . $data );
		}
	}
}
