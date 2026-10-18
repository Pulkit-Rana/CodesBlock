<?php
/**
 * Theme Syntax & Structure Unit Tests.
 *
 * @package CodesBlock\Tests
 */

declare(strict_types=1);

require_once dirname( __DIR__ ) . '/bootstrap.php';

class TestThemeSyntax extends CBTestCase {

	public function testCoreThemeFilesExist(): void {
		$theme_dir = dirname( __DIR__, 2 );
		$core_files = array(
			'functions.php',
			'index.php',
			'header.php',
			'footer.php',
			'front-page.php',
			'single-course.php',
			'style.css',
			'inc.php',
		);

		foreach ( $core_files as $file ) {
			$path = $theme_dir . '/' . $file;
			$this->assertFileExists( $path, "Core theme file {$file} is missing" );
		}
	}

	public function testDirectAccessGuards(): void {
		$theme_dir = dirname( __DIR__, 2 );
		$guarded_files = array(
			'functions.php',
		);

		foreach ( $guarded_files as $file ) {
			$content = file_get_contents( $theme_dir . '/' . $file );
			$has_guard = ( false !== strpos( $content, "defined( 'ABSPATH' )" ) ) || ( false !== strpos( $content, "defined('ABSPATH')" ) );
			$this->assertTrue( $has_guard, "File {$file} must contain direct execution guard defined('ABSPATH')" );
		}
	}

	public function testNoForbiddenDebugFunctionsInTheme(): void {
		$theme_dir = dirname( __DIR__, 2 );
		$files = glob( $theme_dir . '/*.php' );
		$this->assertTrue( ! empty( $files ), 'No PHP files found in theme root' );

		$forbidden_patterns = array(
			'/\bvar_dump\s*\(/i' => 'var_dump() left in code',
			'/\bprint_r\s*\([^,)]+\)/i' => 'print_r() without return flag',
			'/\berror_log\s*\(\s*["\']test/i' => 'Temporary test error_log left in code',
		);

		foreach ( $files as $file ) {
			$content = file_get_contents( $file );
			foreach ( $forbidden_patterns as $pattern => $description ) {
				$matched = preg_match( $pattern, $content );
				$this->assertEquals( 0, $matched, "Forbidden debug statement found in " . basename( $file ) . ": {$description}" );
			}
		}
	}
}
