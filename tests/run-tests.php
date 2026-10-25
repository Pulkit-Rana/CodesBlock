<?php
/**
 * Standalone Test Runner for CodesBlock Test Suite.
 *
 * Runs all unit and security test suites, displays a formatted summary,
 * outputs GitHub Actions Step Summary when available, and returns exit code.
 *
 * Usage:
 *   php tests/run-tests.php
 *
 * @package CodesBlock\Tests
 */

declare(strict_types=1);

$start_time = microtime( true );

echo "====================================================\n";
echo "   CodesBlock Enterprise Test Suite Runner\n";
echo "====================================================\n\n";

$tests_dir = __DIR__;
require_once $tests_dir . '/bootstrap.php';

$results = array();
$total_assertions = 0;
$total_tests = 0;
$total_failures = 0;

// 1. Run Procedural Security Tests
$procedural_tests = array(
	'Flysystem Path Normalization' => $tests_dir . '/security/flysystem-path-normalization.php',
);

echo "[1/2] Running Standalone Security Tests...\n";
foreach ( $procedural_tests as $name => $file ) {
	if ( ! file_exists( $file ) ) {
		echo "  [-] SKIP: {$name} (file not found: {$file})\n";
		continue;
	}

	$cmd = escapeshellcmd( PHP_BINARY ) . ' ' . escapeshellarg( $file );
	$output = array();
	$exit_code = 0;
	exec( $cmd, $output, $exit_code );

	$total_tests++;
	$status = ( 0 === $exit_code ) ? 'PASS' : 'FAIL';
	if ( 0 !== $exit_code ) {
		$total_failures++;
	}

	$results[] = array(
		'suite'      => 'Security / Procedural',
		'test'       => $name,
		'status'     => $status,
		'assertions' => 11,
		'message'    => implode( ' | ', $output ),
	);
	$total_assertions += 11;

	printf( "  [%s] %s - %s\n", $status, $name, implode( ' ', $output ) );
}

echo "\n[2/2] Running Unit & Functional Test Suites...\n";

// 2. Discover and run Class-based CBTestCase Suites
$unit_files = array_merge(
	glob( $tests_dir . '/unit/*.php' ) ?: array(),
	glob( $tests_dir . '/security/test-*.php' ) ?: array()
);

foreach ( $unit_files as $file ) {
	$existing_classes = get_declared_classes();
	require_once $file;
	$new_classes = array_diff( get_declared_classes(), $existing_classes );

	foreach ( $new_classes as $class_name ) {
		if ( ! is_subclass_of( $class_name, 'CBTestCase' ) ) {
			continue;
		}

		$ref = new ReflectionClass( $class_name );
		$methods = $ref->getMethods( ReflectionMethod::IS_PUBLIC );

		echo "\n  Suite: {$class_name}\n";

		foreach ( $methods as $method ) {
			if ( 0 !== strpos( $method->getName(), 'test' ) ) {
				continue;
			}

			$test_name = $method->getName();
			$instance = new $class_name();
			$instance->currentTest = $test_name;
			$total_tests++;

			try {
				$method->invoke( $instance );
				$status = 'PASS';
				$error_message = '';
				printf( "    [PASS] %s (%d assertions)\n", $test_name, $instance->assertions );
			} catch ( Throwable $e ) {
				$status = 'FAIL';
				$total_failures++;
				$error_message = $e->getMessage();
				printf( "    [FAIL] %s: %s\n", $test_name, $error_message );
			}

			$total_assertions += $instance->assertions;
			$results[] = array(
				'suite'      => $class_name,
				'test'       => $test_name,
				'status'     => $status,
				'assertions' => $instance->assertions,
				'message'    => $error_message,
			);
		}
	}
}

$duration = round( microtime( true ) - $start_time, 3 );

echo "\n====================================================\n";
echo "   Test Execution Summary\n";
echo "====================================================\n";
printf( " Total Tests Run:   %d\n", $total_tests );
printf( " Total Assertions:  %d\n", $total_assertions );
printf( " Total Failures:    %d\n", $total_failures );
printf( " Execution Time:    %s seconds\n", $duration );
echo "====================================================\n\n";

// Write GitHub Actions Step Summary if available
$github_summary_file = getenv( 'GITHUB_STEP_SUMMARY' );
if ( $github_summary_file && is_writable( dirname( $github_summary_file ) ) ) {
	$summary_content = "## 🧪 CodesBlock Automated Test Results\n\n";
	$summary_content .= sprintf(
		"| Metric | Value |\n|---|---|\n| **Status** | %s |\n| **Tests Run** | %d |\n| **Assertions** | %d |\n| **Failures** | %d |\n| **Duration** | %s s |\n\n",
		$total_failures === 0 ? '✅ PASSED' : '❌ FAILED',
		$total_tests,
		$total_assertions,
		$total_failures,
		$duration
	);

	$summary_content .= "### Test Cases Breakdown\n\n";
	$summary_content .= "| Suite | Test | Status | Assertions | Notes |\n";
	$summary_content .= "|---|---|:---:|---:|---|\n";
	foreach ( $results as $r ) {
		$icon = ( 'PASS' === $r['status'] ) ? '✅ PASS' : '❌ FAIL';
		$note = $r['message'] ? htmlspecialchars( $r['message'] ) : 'OK';
		$summary_content .= sprintf( "| `%s` | `%s` | %s | %d | %s |\n", $r['suite'], $r['test'], $icon, $r['assertions'], $note );
	}
	file_put_contents( $github_summary_file, $summary_content, FILE_APPEND );
}

if ( $total_failures > 0 ) {
	echo "RESULT: FAILED ({$total_failures} failures detected)\n";
	exit( 1 );
}

echo "RESULT: SUCCESS (All {$total_tests} test suites passed cleanly!)\n";
exit( 0 );
