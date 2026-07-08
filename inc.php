<?php
/**
 * Compatibility helpers.
 *
 * @package CodesBlock
 */

function codesblock_default_menu() {
	?>
	<ul class="menu">
		<li><a href="<?php echo esc_url( home_url( '/articles/' ) ); ?>">Articles</a></li>
		<li><a href="<?php echo esc_url( home_url( '/#courses' ) ); ?>">Courses</a></li>
		<li><a href="<?php echo esc_url( home_url( '/#practice' ) ); ?>">Interview Guides</a></li>
		<li><a href="<?php echo esc_url( home_url( '/#start' ) ); ?>">Start Here</a></li>
	</ul>
	<?php
}
