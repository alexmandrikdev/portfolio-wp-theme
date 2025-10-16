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
