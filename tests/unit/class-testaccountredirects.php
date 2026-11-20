<?php
/**
 * Account sign-in redirect unit tests.
 *
 * @package CodesBlock\Tests
 */

declare(strict_types=1);

require_once dirname( __DIR__ ) . '/bootstrap.php';
require_once dirname( __DIR__, 2 ) . '/inc.php';

if ( ! function_exists( 'wp_validate_redirect' ) ) {
	/**
	 * Same-host check matching WordPress behaviour for these tests.
	 */
	function wp_validate_redirect( $location, $fallback = '' ) {
		$host = parse_url( (string) $location, PHP_URL_HOST );
		return ( $host && 'codesblock.test' === $host ) ? $location : $fallback;
	}
}

/**
 * Members return to the course or checkout they started from, never to an admin URL.
 */
class TestAccountRedirects extends CBTestCase {

	/**
	 * Allow public pages on this site only.
	 */
	public function testMemberRedirectAllowsOnlyPublicSitePages(): void {
		$this->assertEquals( 'https://codesblock.test/courses/x/?cb_course_start=34', codesblock_member_redirect( 'https://codesblock.test/courses/x/?cb_course_start=34' ) );
		$this->assertEquals( '', codesblock_member_redirect( 'https://evil.example/phish' ) );
		$this->assertEquals( '', codesblock_member_redirect( 'https://codesblock.test/wp-admin/users.php' ) );
		$this->assertEquals( '', codesblock_member_redirect( 'https://codesblock.test/wp-login.php?action=logout' ) );
	}

	/**
	 * Add the encoded return address to a provider link, or leave it unchanged.
	 */
	public function testSocialLoginUrlAddsEncodedRedirect(): void {
		$url = codesblock_social_login_url( 'https://codesblock.test/wp-login.php?loginSocial=google', 'https://codesblock.test/my-learning/' );
		$this->assertContains( 'redirect=', $url );
		$this->assertContains( rawurlencode( rawurlencode( 'https://codesblock.test/my-learning/' ) ), $url );
		$this->assertEquals( 'https://codesblock.test/wp-login.php?loginSocial=google', codesblock_social_login_url( 'https://codesblock.test/wp-login.php?loginSocial=google', 'https://evil.example/' ) );
	}

	/**
	 * Remember the first valid destination for the social redirect filters.
	 */
	public function testRequestedSocialRedirectIsValidated(): void {
		codesblock_requested_social_redirect( 'https://evil.example/' );
		$this->assertEquals( '', codesblock_requested_social_redirect() );
		codesblock_requested_social_redirect( 'https://codesblock.test/courses/x/' );
		$this->assertEquals( 'https://codesblock.test/courses/x/', codesblock_requested_social_redirect() );
	}
}
