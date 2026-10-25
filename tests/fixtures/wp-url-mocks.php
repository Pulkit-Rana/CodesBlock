<?php
/**
 * Minimal WordPress URL API mocks for isolated theme tests.
 *
 * @package CodesBlock\Tests
 */

declare(strict_types=1);

if ( ! function_exists( 'wp_parse_url' ) ) {
	/**
	 * Provides a parser fallback for the isolated unit test environment.
	 *
	 * @param string   $url URL to parse.
	 * @param int|null $component Requested URL component.
	 * @return array|string|int|null|false Parsed URL data.
	 */
	function wp_parse_url( $url, $component = -1 ) {
		return parse_url( $url, $component ); // phpcs:ignore WordPress.WP.AlternativeFunctions.parse_url_parse_url -- This is the test-only implementation of wp_parse_url().
	}
}

if ( ! function_exists( 'esc_url_raw' ) ) {
	/**
	 * Provides a URL sanitizer fallback for the isolated unit test environment.
	 *
	 * @param string        $url URL to sanitize.
	 * @param array<string> $protocols Allowed URL schemes.
	 * @return string Sanitized URL, or an empty string for a rejected scheme.
	 */
	function esc_url_raw( $url, $protocols = null ) {
		$url    = trim( (string) $url );
		$scheme = wp_parse_url( $url, PHP_URL_SCHEME );
		if ( false === $scheme ) {
			return '';
		}
		if ( null !== $protocols && null !== $scheme && ! in_array( strtolower( $scheme ), $protocols, true ) ) {
			return '';
		}
		return esc_url( $url );
	}
}
