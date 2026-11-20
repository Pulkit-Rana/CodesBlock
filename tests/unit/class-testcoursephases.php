<?php
/**
 * Course curriculum phases and automatic lesson progress.
 *
 * @package CodesBlock\Tests
 */

declare(strict_types=1);

require_once dirname( __DIR__ ) . '/bootstrap.php';
require_once dirname( __DIR__, 2 ) . '/inc.php';

/**
 * Check phase grouping and the progress maths used when a lesson is opened.
 */
class TestCoursePhases extends CBTestCase {

	/**
	 * Long courses split into classic, AI and lead phases plus a bonus group.
	 */
	public function testGroupsLongCourseIntoPhases(): void {
		$modules = array();
		for ( $i = 0; $i < 13; $i++ ) {
			$modules[] = array( 'is_bonus' => false );
		}
		$modules[] = array( 'is_bonus' => true );

		$phases = codesblock_course_phases( $modules );
		$this->assertEquals( 4, count( $phases ) );
		$this->assertEquals( array( 0, 1, 2, 3, 4 ), $phases[0]['modules'] );
		$this->assertEquals( array( 5, 6, 7, 8, 9, 10 ), $phases[1]['modules'] );
		$this->assertEquals( array( 11, 12 ), $phases[2]['modules'] );
		$this->assertEquals( array( 13 ), $phases[3]['modules'] );
		$this->assertEquals( 'bonus', $phases[3]['key'] );
	}

	/**
	 * Short courses keep every module in one phase.
	 */
	public function testShortCourseHasOnePhase(): void {
		$phases = codesblock_course_phases( array( array( 'is_bonus' => false ), array( 'is_bonus' => false ) ) );
		$this->assertEquals( 1, count( $phases ) );
		$this->assertEquals( array( 0, 1 ), $phases[0]['modules'] );
	}

	/**
	 * Progress only moves forward and reaches 100% on the final lesson.
	 */
	public function testLessonProgressPercent(): void {
		$this->assertEquals( 10, codesblock_lesson_progress_percent( 10, 100, 0 ) );
		$this->assertEquals( 40, codesblock_lesson_progress_percent( 10, 100, 40 ) );
		$this->assertEquals( 99, codesblock_lesson_progress_percent( 99, 100, 0 ) );
		$this->assertEquals( 100, codesblock_lesson_progress_percent( 100, 100, 0 ) );
		$this->assertEquals( 1, codesblock_lesson_progress_percent( 1, 500, 0 ) );
		$this->assertEquals( 1, codesblock_lesson_progress_percent( 0, 0, 0 ) );
	}
}
