<?php
/**
 * Interview session access tests.
 *
 * @package CodesBlock\Tests
 */

declare(strict_types=1);

require_once dirname( __DIR__ ) . '/bootstrap.php';
require_once dirname( __DIR__, 2 ) . '/inc-interview.php';

class TestInterviewAccess extends CBTestCase {

	public function testAnonymousUsersMustSignIn(): void {
		$this->assertEquals( 'login_required', codesblock_interview_access_state( false, false, false ) );
	}

	public function testFreeMemberCanStartOneSession(): void {
		$this->assertEquals( 'free_session', codesblock_interview_access_state( true, false, false ) );
	}

	public function testFreeMemberCannotStartAnotherSession(): void {
		$this->assertEquals( 'membership_required', codesblock_interview_access_state( true, false, true ) );
	}

	public function testPaidMemberCanStartSessionsAfterFreeAllowanceIsUsed(): void {
		$this->assertEquals( 'paid_session', codesblock_interview_access_state( true, true, true ) );
	}

	public function testPaidMembershipDoesNotUnlockAnonymousRequests(): void {
		$this->assertEquals( 'login_required', codesblock_interview_access_state( false, true, false ) );
	}

	public function testValidSessionSetupKeepsOnlyAllowedValues(): void {
		$this->assertEquals(
			array( 'track' => 'system-design', 'difficulty' => 'advanced' ),
			codesblock_interview_normalize_setup( 'system-design', 'advanced' )
		);
	}

	public function testInvalidSessionTrackOrDifficultyIsRejected(): void {
		$this->assertFalse( codesblock_interview_normalize_setup( 'unknown-track', 'advanced' ) );
		$this->assertFalse( codesblock_interview_normalize_setup( 'system-design', 'expert' ) );
	}

	public function testFreeSessionReservationCanBeClaimedOnlyOnce(): void {
		$GLOBALS['_cb_mock_options'] = array();
		$this->assertTrue( codesblock_interview_reserve_free_session( 913 ) );
		$this->assertFalse( codesblock_interview_reserve_free_session( 913 ) );
		$this->assertTrue( codesblock_interview_free_session_used( 913 ) );
	}

	public function testClaimedSessionCanBeResumedButReservationIsNotAValidSessionId(): void {
		$key = codesblock_interview_free_session_option( 913 );
		$GLOBALS['_cb_mock_options'][ $key ] = 'reserved';
		$this->assertEquals( 0, codesblock_interview_free_session_id( 913 ) );
		$GLOBALS['_cb_mock_options'][ $key ] = '245';
		$this->assertEquals( 245, codesblock_interview_free_session_id( 913 ) );
	}

	public function testSessionRecordsAreOnlyVisibleToTheirOwner(): void {
		$session = (object) array( 'post_type' => 'cb_interview_session', 'post_author' => 913 );
		$this->assertTrue( codesblock_interview_session_belongs_to_user( $session, 913 ) );
		$this->assertFalse( codesblock_interview_session_belongs_to_user( $session, 914 ) );
		$other_post = (object) array( 'post_type' => 'post', 'post_author' => 913 );
		$this->assertFalse( codesblock_interview_session_belongs_to_user( $other_post, 913 ) );
	}
}
