<?php
/**
 * Compatibility helpers.
 *
 * @package CodesBlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the durable Articles landing URL even when WordPress shows posts on home.
 */
function codesblock_articles_url() {
	$articles_page = get_page_by_path( 'articles' );
	return $articles_page ? get_permalink( $articles_page ) : home_url( '/articles/' );
}

/**
 * Return the course archive URL when available, or its public landing path.
 */
function codesblock_courses_url() {
	$archive_url = get_post_type_archive_link( 'course' );
	return $archive_url ? $archive_url : home_url( '/courses/' );
}

/**
 * Classify one curriculum line so the course page can label labs, drills and mocks.
 *
 * @param string $text Curriculum line without the leading dash.
 * @return string One of lesson, lab, drill, fix, mock, case, capstone or deliverable.
 */
function codesblock_course_item_kind( $text ) {
	$text     = strtolower( trim( (string) $text ) );
	$prefixes = array(
		'break it at 10x' => 'drill',
		'fix the failing' => 'fix',
		'mock'            => 'mock',
		'capstone'        => 'capstone',
		'lab'             => 'lab',
		'diagnostic'      => 'lab',
		'case'            => 'case',
		'deliverable'     => 'deliverable',
		'portfolio'       => 'deliverable',
	);

	foreach ( $prefixes as $prefix => $kind ) {
		$next = substr( $text, strlen( $prefix ), 1 );
		if ( 0 === strpos( $text, $prefix ) && ! ctype_alpha( $next ) ) {
			return $kind;
		}
	}

	return 'lesson';
}

/**
 * Parse the saved "## Module" / "- Lesson" curriculum into structured modules.
 *
 * Headings such as "Module 3 · Safe change (Week 4)" are split into a label
 * ("Module 3"), a title ("Safe change") and a schedule note ("Week 4").
 *
 * @param string $syllabus Raw curriculum text from the course editor.
 * @return array{modules: array<int, array>, totals: array<string, int>}
 */
function codesblock_parse_course_syllabus( $syllabus ) {
	$modules = array();
	$totals  = array(
		'modules' => 0,
		'items'   => 0,
	);
	$current = null;

	foreach ( preg_split( '/\r\n|\r|\n/', (string) $syllabus ) as $raw_line ) {
		$line = trim( $raw_line );
		if ( '' === $line ) {
			continue;
		}

		if ( 0 === strpos( $line, '##' ) ) {
			if ( null !== $current ) {
				$modules[] = $current;
			}

			$heading  = trim( ltrim( $line, '#' ) );
			$schedule = '';
			if ( preg_match( '/\(([^()]*)\)\s*$/u', $heading, $match ) ) {
				$schedule = trim( $match[1] );
				$heading  = trim( substr( $heading, 0, -strlen( $match[0] ) ) );
			}

			$label = '';
			$title = $heading;
			$parts = preg_split( '/\s+[·:\-–—]\s+/u', $heading, 2 );
			if ( 2 === count( $parts ) && mb_strlen( $parts[0] ) <= 14 ) {
				$label = trim( $parts[0] );
				$title = trim( $parts[1] );
			}

			$current = array(
				'label'    => $label,
				'title'    => $title,
				'schedule' => $schedule,
				'is_bonus' => 0 === stripos( $label, 'bonus' ),
				'items'    => array(),
				'counts'   => array(),
			);
			continue;
		}

		if ( 0 !== strpos( $line, '-' ) ) {
			continue;
		}

		if ( null === $current ) {
			$current = array(
				'label'    => '',
				'title'    => '',
				'schedule' => '',
				'is_bonus' => false,
				'items'    => array(),
				'counts'   => array(),
			);
		}

		$text = trim( substr( $line, 1 ) );
		if ( '' === $text ) {
			continue;
		}

		$kind               = codesblock_course_item_kind( $text );
		$current['items'][] = array(
			'text' => $text,
			'kind' => $kind,
		);
		$current['counts'][ $kind ] = isset( $current['counts'][ $kind ] ) ? $current['counts'][ $kind ] + 1 : 1;
		$totals[ $kind ]            = isset( $totals[ $kind ] ) ? $totals[ $kind ] + 1 : 1;
		++$totals['items'];
	}

	if ( null !== $current ) {
		$modules[] = $current;
	}

	foreach ( $modules as $module ) {
		if ( ! $module['is_bonus'] ) {
			++$totals['modules'];
		}
	}

	return array(
		'modules' => $modules,
		'totals'  => $totals,
	);
}

/**
 * Social sign-in providers the account dialog can offer, in display order.
 *
 * Uses WP Social (Wpmet). A provider appears only when it is enabled under
 * WP Social > Social Login > Providers, so Google shows up automatically once
 * its keys are saved there.
 *
 * @return array<string, array{label: string, url: string}>
 */
function codesblock_social_providers() {
	$labels    = array(
		'google' => __( 'Google', 'codesblock' ),
		'github' => __( 'GitHub', 'codesblock' ),
	);
	$providers = array();

	if ( ! class_exists( 'WP_Social\\App\\Settings' ) || ! method_exists( 'WP_Social\\App\\Settings', 'get_enabled_provider_conf_login' ) ) {
		return $providers;
	}

	$enabled = (array) \WP_Social\App\Settings::get_enabled_provider_conf_login();
	foreach ( $labels as $provider_id => $label ) {
		if ( empty( $enabled[ $provider_id ]['enable'] ) ) {
			continue;
		}
		$providers[ $provider_id ] = array(
			'label' => $label,
			'url'   => home_url( '/wp-json/wslu-social-login/type/' . $provider_id ),
		);
	}

	return $providers;
}

/**
 * Add a validated same-site return address to a social sign-in URL.
 *
 * WP Social reads the standard WordPress `redirect_to` parameter.
 *
 * @param string $login_url Provider login URL without a redirect.
 * @param string $redirect  Where the visitor should land after signing in.
 * @return string
 */
function codesblock_social_login_url( $login_url, $redirect ) {
	$redirect = codesblock_member_redirect( $redirect );
	return $redirect ? add_query_arg( 'redirect_to', rawurlencode( $redirect ), $login_url ) : $login_url;
}

/**
 * Accept a post-sign-in destination only when it is a public page on this site.
 *
 * @param string $url Requested destination.
 * @return string The URL, or an empty string when it is not allowed.
 */
function codesblock_member_redirect( $url ) {
	$url = wp_validate_redirect( (string) $url, '' );
	if ( '' === $url ) {
		return '';
	}

	$path = (string) wp_parse_url( $url, PHP_URL_PATH );
	foreach ( array( '/wp-admin', 'wp-login.php', 'admin-ajax.php', '/wp-json' ) as $blocked ) {
		if ( false !== strpos( $path, $blocked ) ) {
			return '';
		}
	}

	return $url;
}

/**
 * Whether paid checkout is wired to a real payment gateway.
 *
 * Knit Pay's "Test" gateway lets anyone mark a payment as paid, so plans
 * must not lead to checkout while it is the configured gateway.
 *
 * @return bool
 */
function codesblock_checkout_uses_test_gateway() {
	if ( 'knit_pay' !== get_option( 'pmpro_gateway' ) ) {
		return false;
	}

	$config_ids = array_filter( array_map( 'absint', (array) get_option( 'pmpro_knit_pay_config_id' ) ) );
	if ( ! $config_ids ) {
		return true;
	}

	foreach ( $config_ids as $config_id ) {
		if ( 'test' === get_post_meta( $config_id, '_pronamic_gateway_id', true ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Whether visitors can buy a pass right now.
 *
 * @return bool
 */
function codesblock_payments_live() {
	$ready = function_exists( 'cbcommerce_payments_ready' ) ? cbcommerce_payments_ready() : false;
	return $ready && ! codesblock_checkout_uses_test_gateway();
}
