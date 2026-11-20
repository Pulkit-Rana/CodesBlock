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

/**
 * Group curriculum modules into phases for the course explorer.
 *
 * Long courses (10+ core modules) follow the Dual-Spine shape: classic
 * foundations, AI systems, then leading and shipping. Shorter courses get one
 * phase. Bonus modules always form their own group.
 *
 * @param array $modules Parsed modules from codesblock_parse_course_syllabus().
 * @return array[] List of phases: key, label, summary and module indexes.
 */
function codesblock_course_phases( array $modules ) {
	$core  = array();
	$bonus = array();
	foreach ( $modules as $index => $module ) {
		if ( ! empty( $module['is_bonus'] ) ) {
			$bonus[] = $index;
		} else {
			$core[] = $index;
		}
	}

	$phases = array();
	if ( count( $core ) >= 10 ) {
		$phases[] = array(
			'key'     => 'classic',
			'label'   => __( 'Classic spine', 'codesblock' ),
			'summary' => __( 'Scale, correctness and safe change', 'codesblock' ),
			'modules' => array_slice( $core, 0, 5 ),
		);
		$phases[] = array(
			'key'     => 'ai',
			'label'   => __( 'AI spine', 'codesblock' ),
			'summary' => __( 'Gateways, RAG, evals, agents and voice', 'codesblock' ),
			'modules' => array_slice( $core, 5, count( $core ) - 7 ),
		);
		$phases[] = array(
			'key'     => 'lead',
			'label'   => __( 'Lead and ship', 'codesblock' ),
			'summary' => __( 'Incidents, launch reviews and capstone', 'codesblock' ),
			'modules' => array_slice( $core, -2 ),
		);
	} elseif ( $core ) {
		$phases[] = array(
			'key'     => 'core',
			'label'   => __( 'Core modules', 'codesblock' ),
			'summary' => '',
			'modules' => $core,
		);
	}

	if ( $bonus ) {
		$phases[] = array(
			'key'     => 'bonus',
			'label'   => __( 'Bonus', 'codesblock' ),
			'summary' => __( 'Extra cases for repeat mocks', 'codesblock' ),
			'modules' => $bonus,
		);
	}

	return apply_filters( 'codesblock_course_phases', $phases, $modules );
}

/**
 * Progress after reaching a lesson: never moves backwards and never reaches 100%
 * before the final lesson.
 *
 * @param int $current_step Position of the lesson in the planned outline (1-based).
 * @param int $planned_total Number of lessons in the planned outline.
 * @param int $saved        Progress already stored for the course.
 * @return int Percentage to store.
 */
function codesblock_lesson_progress_percent( $current_step, $planned_total, $saved ) {
	$current_step  = absint( $current_step );
	$planned_total = absint( $planned_total );
	$saved         = min( 100, absint( $saved ) );
	if ( ! $current_step || ! $planned_total ) {
		return max( 1, $saved );
	}

	$reached = $current_step >= $planned_total ? 100 : (int) min( 99, max( 1, floor( ( $current_step / $planned_total ) * 100 ) ) );
	return max( $saved, $reached );
}

/**
 * Record a member's visit to a lesson: progress moves forward on its own and the
 * course page's "Continue" button returns to this lesson.
 *
 * @param int $course_id     Course ID.
 * @param int $lesson_id     Lesson ID.
 * @param int $current_step  Position of the lesson in the outline (1-based).
 * @param int $planned_total Number of lessons in the outline.
 */
function codesblock_record_lesson_visit( $course_id, $lesson_id, $current_step, $planned_total ) {
	$user_id = get_current_user_id();
	if ( ! $user_id || ! function_exists( 'cbcommerce_user_can_track_course' ) || ! cbcommerce_user_can_track_course( $course_id ) ) {
		return;
	}

	$saved   = function_exists( 'cbcommerce_get_course_progress' ) ? cbcommerce_get_course_progress( $course_id ) : 0;
	$percent = codesblock_lesson_progress_percent( $current_step, $planned_total, $saved );
	if ( function_exists( 'cbcommerce_set_course_progress' ) ) {
		cbcommerce_set_course_progress( $course_id, $percent );
	}

	$last = get_user_meta( $user_id, 'codesblock_last_lesson', true );
	$last = is_array( $last ) ? $last : array();
	$last[ absint( $course_id ) ] = absint( $lesson_id );
	update_user_meta( $user_id, 'codesblock_last_lesson', $last );
}

/**
 * The lesson a member last opened in a course, if it is still published.
 *
 * @param int $course_id Course ID.
 * @return string Lesson URL or an empty string.
 */
function codesblock_course_resume_url( $course_id ) {
	$user_id = get_current_user_id();
	if ( ! $user_id ) {
		return '';
	}

	$last      = get_user_meta( $user_id, 'codesblock_last_lesson', true );
	$lesson_id = is_array( $last ) && isset( $last[ absint( $course_id ) ] ) ? absint( $last[ absint( $course_id ) ] ) : 0;
	if ( ! $lesson_id || 'publish' !== get_post_status( $lesson_id ) ) {
		return '';
	}

	return (string) get_permalink( $lesson_id );
}
