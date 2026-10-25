<?php
/**
 * Course and Navigation Functions Unit Tests.
 *
 * @package CodesBlock\Tests
 */

declare(strict_types=1);

require_once dirname( __DIR__ ) . '/bootstrap.php';
require_once dirname( __DIR__, 2 ) . '/inc.php';

class TestCourseFunctions extends CBTestCase {

	public function testArticlesUrlFallback(): void {
		$GLOBALS['_cb_mock_context']['page_by_path'] = array();
		$url = codesblock_articles_url();
		$this->assertEquals( 'https://codesblock.test/articles/', $url );
	}

	public function testArticlesUrlWithPage(): void {
		$GLOBALS['_cb_mock_context']['page_by_path']['articles'] = array( 'ID' => 123 );
		$url = codesblock_articles_url();
		$this->assertEquals( 'https://codesblock.test/page-123/', $url );
	}

	public function testHomepageTitleFilter(): void {
		require_once dirname( __DIR__, 2 ) . '/functions.php';

		// When on front page
		$GLOBALS['_cb_mock_context']['is_front_page'] = true;
		$GLOBALS['_cb_mock_context']['is_home'] = false;
		$title = codesblock_homepage_title( 'Default Title' );
		$this->assertEquals( 'CodesBlock | Production AI Engineering for Developers', $title );

		// When on another page
		$GLOBALS['_cb_mock_context']['is_front_page'] = false;
		$GLOBALS['_cb_mock_context']['is_home'] = false;
		$title = codesblock_homepage_title( 'Sample Course | CodesBlock' );
		$this->assertEquals( 'Sample Course | CodesBlock', $title );
	}

	public function testSiteIconUrlSizing(): void {
		require_once dirname( __DIR__, 2 ) . '/functions.php';

		// Small size (16px)
		$icon16 = codesblock_site_icon_url( 'default.png', 16 );
		$this->assertContains( 'favicon-cb-transparent-16.png', $icon16 );

		// Medium size (32px)
		$icon32 = codesblock_site_icon_url( 'default.png', 32 );
		$this->assertContains( 'favicon-cb-transparent-32.png', $icon32 );

		// Large size (512px)
		$icon512 = codesblock_site_icon_url( 'default.png', 512 );
		$this->assertContains( 'favicon-cb-transparent-512.png', $icon512 );
	}

	public function testStatsEndpointReturnsArray(): void {
		require_once dirname( __DIR__, 2 ) . '/functions.php';

		$stats = codesblock_get_stats();
		$this->assertTrue( is_array( $stats ) );
		$this->assertEquals( 42, $stats['courses'] );
		$this->assertEquals( 42, $stats['articles'] );
		$this->assertEquals( 1500, $stats['learners'] );
	}
}
