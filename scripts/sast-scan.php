<?php
/**
 * SAST Security Scanner for CodesBlock Theme.
 *
 * Scans theme files for OWASP Top 10 vulnerabilities, direct XSS, SQLi,
 * unsafe serialization, and direct script execution anti-patterns.
 *
 * @package CodesBlock\Security
 */

declare(strict_types=1);

$root_dir = dirname( __DIR__ );
$files = array_merge(
	glob( $root_dir . '/*.php' ) ?: array(),
	glob( $root_dir . '/template-parts/*.php' ) ?: array()
);

$issues = 0;
$patterns = array(
	'/\becho\s+\$_GET/i'                                => 'Unescaped echo $_GET directly to output (XSS vulnerability)',
	'/\becho\s+\$_POST/i'                               => 'Unescaped echo $_POST directly to output (XSS vulnerability)',
	'/\becho\s+\$_REQUEST/i'                            => 'Unescaped echo $_REQUEST directly to output (XSS vulnerability)',
	'/\beval\s*\(/i'                                    => 'Dangerous eval() usage detected (RCE risk)',
	'/\bunserialize\s*\(\s*\$_(GET|POST|REQUEST)/i'     => 'Unsafe unserialize() on untrusted user input',
	'/\$wpdb->query\s*\(\s*["\'].*\$_(GET|POST|REQUEST)/i' => 'Direct SQL query interpolation without $wpdb->prepare() (SQLi vulnerability)',
);

foreach ( $files as $file ) {
	$relative_name = str_replace( $root_dir . DIRECTORY_SEPARATOR, '', $file );
	$code = file_get_contents( $file );

	foreach ( $patterns as $pattern => $msg ) {
		if ( preg_match( $pattern, $code ) ) {
			fwrite( STDERR, "SECURITY VIOLATION [{$relative_name}]: {$msg}\n" );
			$issues++;
		}
	}
}

if ( $issues > 0 ) {
	fwrite( STDERR, "SAST Scan Failed: {$issues} security violation(s) detected.\n" );
	exit( 1 );
}

echo "SAST Scan Clean: All " . count( $files ) . " theme PHP files passed security checks.\n";
exit( 0 );
