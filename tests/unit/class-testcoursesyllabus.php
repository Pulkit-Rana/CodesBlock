<?php
/**
 * Course curriculum parser unit tests.
 *
 * @package CodesBlock\Tests
 */

declare(strict_types=1);

require_once dirname( __DIR__ ) . '/bootstrap.php';
require_once dirname( __DIR__, 2 ) . '/inc.php';

/**
 * Check how the course page reads the saved curriculum text.
 */
class TestCourseSyllabus extends CBTestCase {

	/**
	 * Split module headings into label, title and schedule, and count items by kind.
	 */
	public function testParsesModulesAndTotals(): void {
		$syllabus = "## Module 0 · Think like a designer (Week 1)\n- What System Design is\n- Lab: flash-sale waiting room\n- Break it at 10x: one hot tenant\n\n## Module 1 - Scale\n- Mock 1 (rubric-scored): reservations\n- Labelled test sets are not labs\n\n## Bonus · Extra cases\n- Case: fraud scoring";
		$parsed   = codesblock_parse_course_syllabus( $syllabus );

		$this->assertEquals( 3, count( $parsed['modules'] ) );
		$this->assertEquals( 'Module 0', $parsed['modules'][0]['label'] );
		$this->assertEquals( 'Think like a designer', $parsed['modules'][0]['title'] );
		$this->assertEquals( 'Week 1', $parsed['modules'][0]['schedule'] );
		$this->assertEquals( 'Scale', $parsed['modules'][1]['title'] );
		$this->assertTrue( $parsed['modules'][2]['is_bonus'] );
		$this->assertEquals( 2, $parsed['totals']['modules'] );
		$this->assertEquals( 6, $parsed['totals']['items'] );
		$this->assertEquals( 1, $parsed['totals']['lab'] );
		$this->assertEquals( 1, $parsed['totals']['drill'] );
		$this->assertEquals( 1, $parsed['totals']['mock'] );
		$this->assertEquals( 1, $parsed['totals']['case'] );
		$this->assertEquals( 'lesson', $parsed['modules'][1]['items'][1]['kind'] );
	}

	/**
	 * Keep lessons that appear before the first heading and ignore stray text.
	 */
	public function testHandlesMissingHeadingsAndEmptyInput(): void {
		$parsed = codesblock_parse_course_syllabus( "Intro text\n- First lesson\n-  \n" );
		$this->assertEquals( 1, count( $parsed['modules'] ) );
		$this->assertEquals( 1, $parsed['totals']['items'] );
		$this->assertEquals( 1, $parsed['totals']['modules'] );

		$empty = codesblock_parse_course_syllabus( '' );
		$this->assertEquals( 0, count( $empty['modules'] ) );
		$this->assertEquals( 0, $empty['totals']['items'] );
	}
}
