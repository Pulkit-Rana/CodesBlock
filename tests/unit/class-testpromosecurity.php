<?php
/**
 * Promotional link sanitizer security tests.
 *
 * @package CodesBlock\Tests
 */

declare(strict_types=1);

require_once dirname( __DIR__ ) . '/bootstrap.php';
require_once dirname( __DIR__ ) . '/fixtures/wp-url-mocks.php';
require_once dirname( __DIR__, 2 ) . '/functions.php';

/**
 * Tests for the promotional link sanitizer.
 *
 * @package CodesBlock\Tests
 */
class TestPromoSecurity extends CBTestCase {

	/**
	 * Accepts safe anchors, relative paths, and HTTP(S) links.
	 *
	 * @return void
	 */
	public function testAllowsSafePromoLinkForms(): void {
		$this->assertEquals( '#courses', codesblock_sanitize_promo_url( '#courses' ) );
		$this->assertEquals( '/courses/?level=beginner', codesblock_sanitize_promo_url( '/courses/?level=beginner' ) );
		$this->assertEquals( 'https://codesblock.io/courses/', codesblock_sanitize_promo_url( 'https://codesblock.io/courses/' ) );
		$this->assertEquals( 'http://codesblock.io/courses/', codesblock_sanitize_promo_url( 'http://codesblock.io/courses/' ) );
	}

	/**
	 * Rejects script schemes and protocol-relative external hosts.
	 *
	 * @return void
	 */
	public function testRejectsUnsafePromoLinkSchemesAndHosts(): void {
		$this->assertEquals( '', codesblock_sanitize_promo_url( 'javascript:alert(1)' ) );
		$this->assertEquals( '', codesblock_sanitize_promo_url( 'data:text/html,<script>alert(1)</script>' ) );
		$this->assertEquals( '', codesblock_sanitize_promo_url( '//attacker.example/path' ) );
	}

	/**
	 * Rejects malformed fragment identifiers.
	 *
	 * @return void
	 */
	public function testRejectsMalformedPromoAnchors(): void {
		$this->assertEquals( '', codesblock_sanitize_promo_url( '#1starts-with-number' ) );
		$this->assertEquals( '', codesblock_sanitize_promo_url( '#space in anchor' ) );
	}
}
