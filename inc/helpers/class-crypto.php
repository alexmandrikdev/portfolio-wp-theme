<?php

namespace AMPortfolioTheme\Helpers;

defined( 'ABSPATH' ) || exit;

/**
 * Class for encryption functionality.
 *
 * Based on WP Mail SMTP's Crypto class with modifications for this theme.
 * Requires PORTFOLIO_CRYPTO_KEY constant to be defined.
 *
 * @link https://www.php.net/manual/en/intro.sodium.php
 */
class Crypto {

	/**
	 * Get a secret key for encrypt/decrypt.
	 *
	 * @return string Secret key.
	 * @throws \Exception If PORTFOLIO_CRYPTO_KEY constant is not defined.
	 */
	public static function get_secret_key() {

		if ( ! defined( 'PORTFOLIO_CRYPTO_KEY' ) || empty( \PORTFOLIO_CRYPTO_KEY ) ) {
			throw new \Exception(
				__( 'PORTFOLIO_CRYPTO_KEY constant must be defined for encryption/decryption.', 'portfolio' ) // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
			);
		}

		// The constant should contain the base64 encoded key.
		$secret_key = base64_decode( \PORTFOLIO_CRYPTO_KEY ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode

		if ( false === $secret_key ) {
			throw new \Exception(
				__( 'PORTFOLIO_CRYPTO_KEY constant must contain a valid base64 encoded key.', 'portfolio' ) // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
			);
		}

		return $secret_key;
	}

	/**
	 * Encrypt a message.
	 *
	 * @param string $message Message to encrypt.
	 * @return string Encrypted message (base64 encoded).
	 * @throws \Exception If encryption fails or key is not defined.
	 */
	public static function encrypt( $message ) {

		if ( empty( $message ) ) {
			return $message;
		}

		$key = self::get_secret_key();

		// Check if Sodium is available for modern encryption.
		if ( function_exists( 'sodium_crypto_secretbox' ) && function_exists( 'random_bytes' ) ) {
			// Create a nonce for this operation.
			$nonce = random_bytes( SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );

			// Encrypt message and combine with nonce.
			$cipher = base64_encode( // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
				$nonce .
				sodium_crypto_secretbox(
					$message,
					$nonce,
					$key
				)
			);

			// Try to zero out sensitive data.
			try {
				sodium_memzero( $message );
				sodium_memzero( $key );
			} catch ( \Exception $e ) {
				// Ignore errors on memory zeroing.
				log_message(
					sprintf( 'Failed to zero memory: %s', $e->getMessage() ),
					'Crypto',
					'warning'
				);
			}

			return $cipher;
		} else {
			// Fallback to simple obfuscation if Sodium is not available.
			// This is not secure but provides basic obfuscation.
			return base64_encode( $message . '|' . wp_hash( $key ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		}
	}

	/**
	 * Decrypt a message.
	 * Returns encrypted message on any failure and the decrypted message on success.
	 *
	 * @param string $encrypted Encrypted message (base64 encoded).
	 * @return string Decrypted message or original encrypted string on failure.
	 */
	public static function decrypt( $encrypted ) {

		if ( empty( $encrypted ) ) {
			return $encrypted;
		}

		try {
			$key = self::get_secret_key();
		} catch ( \Exception $e ) {
			// If key is not defined, return the encrypted string.
			return $encrypted;
		}

		// Unpack base64 message.
		$decoded = base64_decode( $encrypted ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode

		if ( false === $decoded ) {
			return $encrypted;
		}

		// Check if Sodium is available and the encrypted data looks like Sodium format.
		if ( function_exists( 'sodium_crypto_secretbox_open' ) &&
			strlen( $decoded ) >= ( SODIUM_CRYPTO_SECRETBOX_NONCEBYTES + SODIUM_CRYPTO_SECRETBOX_MACBYTES ) ) {

			// Pull nonce and ciphertext out of unpacked message.
			$nonce      = substr( $decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
			$ciphertext = substr( $decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );

			// Decrypt it.
			$message = sodium_crypto_secretbox_open(
				$ciphertext,
				$nonce,
				$key
			);

			// Check for decryption failures.
			if ( false === $message ) {
				return $encrypted;
			}

			// Try to zero out sensitive data.
			try {
				sodium_memzero( $ciphertext );
				sodium_memzero( $key );
			} catch ( \Exception $e ) {
				// Ignore errors on memory zeroing.
				log_message(
					sprintf( 'Failed to zero memory: %s', $e->getMessage() ),
					'Crypto',
					'warning'
				);
			}

			return $message;
		} else {
			// Fallback decryption for simple obfuscation.
			$decrypted = base64_decode( $encrypted ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
			if ( false === $decrypted ) {
				return $encrypted;
			}

			$parts = explode( '|', $decrypted );
			if ( 2 === count( $parts ) && wp_hash( $key ) === $parts[1] ) {
				return $parts[0];
			}

			return $encrypted;
		}
	}

	/**
	 * Check if a string appears to be encrypted.
	 *
	 * @param string $encrypted_string String to check.
	 * @return bool True if the string appears to be encrypted.
	 */
	public static function is_encrypted( $encrypted_string ) {
		if ( empty( $encrypted_string ) ) {
			return false;
		}

		// Check if it's base64 encoded.
		$decoded = base64_decode( $encrypted_string, true ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
		if ( false === $decoded ) {
			return false;
		}

		// If it's long enough to contain Sodium nonce + ciphertext, assume it's encrypted.
		if ( function_exists( 'sodium_crypto_secretbox_open' ) &&
			strlen( $decoded ) >= ( SODIUM_CRYPTO_SECRETBOX_NONCEBYTES + SODIUM_CRYPTO_SECRETBOX_MACBYTES ) ) {
			return true;
		}

		// Check for our fallback format.
		$parts = explode( '|', $decoded );
		return 2 === count( $parts );
	}
}
