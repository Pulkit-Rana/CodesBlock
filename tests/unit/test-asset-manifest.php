<?php
/**
 * Asset Manifest and Integrity Unit Tests.
 *
 * @package CodesBlock\Tests
 */

declare(strict_types=1);

require_once dirname( __DIR__ ) . '/bootstrap.php';

class TestAssetManifest extends CBTestCase {

	public function testCoreCssAssetsExist(): void {
		$theme_dir = dirname( __DIR__, 2 );
		$css_assets = array(
			'assets/css/main.css',
			'assets/css/member.css',
			'assets/css/member-dashboard.css',
			'assets/css/polish.css',
			'assets/css/responsive.css',
			'assets/css/course-portal.css',
			'assets/css/course-learning.css',
			'assets/css/typography.css',
			'assets/css/editor.css',
		);

		foreach ( $css_assets as $asset ) {
			$file = $theme_dir . '/' . $asset;
			$this->assertFileExists( $file, "CSS asset missing: {$asset}" );
			$this->assertTrue( filesize( $file ) > 0, "CSS asset is empty: {$asset}" );
		}
	}

	public function testCoreJsAssetsExist(): void {
		$theme_dir = dirname( __DIR__, 2 );
		$js_assets = array(
			'assets/js/main.js',
			'assets/js/member.js',
			'assets/js/course-portal.js',
			'assets/js/course-learning.js',
		);

		foreach ( $js_assets as $asset ) {
			$file = $theme_dir . '/' . $asset;
			$this->assertFileExists( $file, "JS asset missing: {$asset}" );
			$this->assertTrue( filesize( $file ) > 0, "JS asset is empty: {$asset}" );
		}
	}

	public function testCoreFaviconAssetsExist(): void {
		$theme_dir = dirname( __DIR__, 2 );
		$favicons = array(
			'assets/images/favicon-cb-transparent-16.png',
			'assets/images/favicon-cb-transparent-32.png',
			'assets/images/favicon-cb-transparent-192.png',
			'assets/images/favicon-cb-transparent-512.png',
			'assets/images/favicon-cb-transparent.ico',
		);

		foreach ( $favicons as $asset ) {
			$file = $theme_dir . '/' . $asset;
			$this->assertFileExists( $file, "Favicon asset missing: {$asset}" );
		}
	}
}
