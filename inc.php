<?php
/**
 * Compatibility helpers.
 *
 * @package CodesBlock
 */

function codesblock_default_menu() {
	$courses_url = get_post_type_archive_link( 'course' ) ?: home_url( '/courses/' );
	?>
	<ul class="menu">
		<li class="<?php echo is_front_page() ? 'current-menu-item' : ''; ?>"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
		<li class="<?php echo ( is_post_type_archive( 'course' ) || is_singular( 'course' ) ) ? 'current-menu-item' : ''; ?>"><a href="<?php echo esc_url( $courses_url ); ?>">Courses</a></li>
		<li class="<?php echo ( is_page( 'articles' ) || ( is_home() && ! is_front_page() ) || is_singular( 'post' ) ) ? 'current-menu-item' : ''; ?>"><a href="<?php echo esc_url( codesblock_articles_url() ); ?>">Articles</a></li>
		<li><a href="<?php echo esc_url( home_url( '/#practice' ) ); ?>">Interview Guides</a></li>
		<li class="menu-item-start-here"><a href="<?php echo esc_url( home_url( '/#start' ) ); ?>">Start</a></li>
	</ul>
	<?php
}

/**
 * Return the durable Articles landing URL even when WordPress shows posts on home.
 */
function codesblock_articles_url() {
	$articles_page = get_page_by_path( 'articles' );
	return $articles_page ? get_permalink( $articles_page ) : home_url( '/articles/' );
}
