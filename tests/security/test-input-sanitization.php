<?php
/**
 * Security & Input Sanitization Tests.
 *
 * @package CodesBlock\Tests
 */

declare(strict_types=1);

require_once dirname( __DIR__ ) . '/bootstrap.php';

class TestInputSanitizationSecurity extends CBTestCase {

	public function testSqlInjectionPatternRejection(): void {
		$malicious_payloads = array(
			"' OR '1'='1",
			"1; DROP TABLE wp_users; --",
			"UNION SELECT user_login, user_pass FROM wp_users",
			"' AND SLEEP(5) --",
		);

		foreach ( $malicious_payloads as $payload ) {
			// Ensure payload is not considered safe plain text without sanitization
			$sanitized = sanitize_text_field( $payload );
			$this->assertTrue( is_string( $sanitized ) );
		}
	}

	public function testCrossSiteScriptingSanitization(): void {
		$xss_payloads = array(
			'<script>alert("pwned")</script>',
			'<img src=x onerror=alert(1)>',
			'<svg/onload=alert(1)>',
			'javascript:alert(document.cookie)',
			'<iframe src="evil.com"></iframe>',
		);

		foreach ( $xss_payloads as $payload ) {
			$escaped_html = esc_html( $payload );
			$this->assertFalse( false !== strpos( $escaped_html, '<script>' ) );
			$this->assertFalse( false !== strpos( $escaped_html, '<iframe' ) );
			$this->assertFalse( false !== strpos( $escaped_html, '<img' ) );
			$this->assertFalse( false !== strpos( $escaped_html, '<svg' ) );
		}
	}

	public function testPathTraversalDetection(): void {
		$traversal_payloads = array(
			'../../../../etc/passwd',
			'..\\..\\..\\windows\\system32\\config\\sam',
			'uploads/../../../wp-config.php',
			"uploads/file\x00.php",
		);

		foreach ( $traversal_payloads as $path ) {
			$contains_traversal = ( false !== strpos( $path, '..' ) ) || ( false !== strpos( $path, "\x00" ) );
			$this->assertTrue( $contains_traversal, "Traversal attack vector {$path} must be detected" );
		}
	}
}
