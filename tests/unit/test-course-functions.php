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

	public function testHomepageUsesConfiguredTitlesAndSiteIcon(): void {
		require_once dirname( __DIR__, 2 ) . '/functions.php';

		$previous = $GLOBALS['_cb_mock_context']['is_front_page'];
		$GLOBALS['_cb_mock_context']['is_front_page'] = true;
		try {
			$this->assertEquals( 'Search title', apply_filters( 'wpseo_title', 'Search title' ) );
			$this->assertEquals( 'Social title', apply_filters( 'wpseo_opengraph_title', 'Social title' ) );
			$icon = 'https://codesblock.test/wp-content/uploads/site-icon.png';
			$this->assertEquals( $icon, apply_filters( 'get_site_icon_url', $icon, 32 ) );
		} finally {
			$GLOBALS['_cb_mock_context']['is_front_page'] = $previous;
		}
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
