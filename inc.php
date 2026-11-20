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
