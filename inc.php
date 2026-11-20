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
