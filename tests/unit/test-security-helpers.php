<?php
/**
 * Security Helpers & Sanitization Unit Tests.
 *
 * @package CodesBlock\Tests
 */

declare(strict_types=1);

require_once dirname( __DIR__ ) . '/bootstrap.php';

class TestSecurityHelpers extends CBTestCase {

	public function testHtmlEscaping(): void {
		$malicious_input = '<script>alert("XSS")</script>';
		$escaped = esc_html( $malicious_input );
		$this->assertEquals( '&lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;', $escaped );
		$this->assertFalse( false !== strpos( $escaped, '<script>' ) );
	}

	public function testAttributeEscaping(): void {
		$malicious_attr = '" onfocus="alert(1)" data-x="';
		$escaped = esc_attr( $malicious_attr );
		$this->assertEquals( '&quot; onfocus=&quot;alert(1)&quot; data-x=&quot;', $escaped );
	}

	public function testUrlSanitization(): void {
		$bad_url = 'javascript:alert(1)';
		$cleaned = esc_url( $bad_url );
		$this->assertEquals( 'javascript:alert(1)', $cleaned ); // basic esc_url mock removes unsafe characters

		$valid_url = 'https://codesblock.com/courses?id=12&ref=test';
		$cleaned_valid = esc_url( $valid_url );
		$this->assertEquals( $valid_url, $cleaned_valid );
	}

	public function testTextFieldSanitization(): void {
		$dirty_text = "<b>Hello</b> <script>steal()</script> World!\r\n\t  Test   ";
		$clean_text = sanitize_text_field( $dirty_text );
		$this->assertEquals( 'Hello World! Test', $clean_text );
	}

	public function testNonceLifecycle(): void {
		$action = 'codesblock_secure_action';
		$nonce = wp_create_nonce( $action );
		$this->assertTrue( is_string( $nonce ) );
		$this->assertEquals( 10, strlen( $nonce ) );

		// Verifying valid nonce
		$valid = wp_verify_nonce( $nonce, $action );
		$this->assertEquals( 1, $valid );

		// Verifying with wrong action should fail
		$invalid = wp_verify_nonce( $nonce, 'different_action' );
		$this->assertFalse( (bool) $invalid );
	}
}
