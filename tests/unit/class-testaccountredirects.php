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
	 * Add the encoded WordPress redirect_to parameter, or leave the URL unchanged.
	 */
	public function testSocialLoginUrlAddsEncodedRedirect(): void {
		$base = 'https://codesblock.test/wp-json/wslu-social-login/type/github';
		$url  = codesblock_social_login_url( $base, 'https://codesblock.test/my-learning/' );
		$this->assertContains( 'redirect_to=', $url );
		$this->assertEquals( $base, codesblock_social_login_url( $base, 'https://evil.example/' ) );
	}

	/**
	 * Treat Knit Pay's Test gateway as "not ready", so plans never reach checkout.
	 */
	public function testTestGatewayIsNotLive(): void {
		$GLOBALS['_cb_mock_options']['pmpro_gateway']            = 'knit_pay';
		$GLOBALS['_cb_mock_options']['pmpro_knit_pay_config_id'] = array( 38 );
		$GLOBALS['_cb_mock_context']['post_meta']['_pronamic_gateway_id'] = 'test';
		$this->assertTrue( codesblock_checkout_uses_test_gateway() );

		$GLOBALS['_cb_mock_context']['post_meta']['_pronamic_gateway_id'] = 'razorpay';
		$this->assertFalse( codesblock_checkout_uses_test_gateway() );

		$GLOBALS['_cb_mock_options']['pmpro_knit_pay_config_id'] = array();
		$this->assertTrue( codesblock_checkout_uses_test_gateway() );

		$GLOBALS['_cb_mock_options']['pmpro_gateway'] = 'stripe';
		$this->assertFalse( codesblock_checkout_uses_test_gateway() );

		unset( $GLOBALS['_cb_mock_options']['pmpro_gateway'], $GLOBALS['_cb_mock_options']['pmpro_knit_pay_config_id'], $GLOBALS['_cb_mock_context']['post_meta']['_pronamic_gateway_id'] );
	}
}
