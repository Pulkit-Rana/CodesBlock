<?php
/**
 * A plain-language maintenance map inside WordPress.
 *
 * @package CodesBlockCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function cbcore_register_admin_page() {
	add_menu_page(
		__( 'CodesBlock', 'codesblock-core' ),
		__( 'CodesBlock', 'codesblock-core' ),
		'manage_options',
		'codesblock-setup',
		'cbcore_render_admin_page',
		'dashicons-welcome-learn-more',
		3
	);
}
add_action( 'admin_menu', 'cbcore_register_admin_page' );

function cbcore_render_admin_page() {
	$course_count = wp_count_posts( 'course' );
	$post_count   = wp_count_posts( 'post' );
	?>
	<div class="wrap cbcore-admin">
		<h1><?php esc_html_e( 'CodesBlock control centre', 'codesblock-core' ); ?></h1>
		<p class="cbcore-lede"><?php esc_html_e( 'The everyday parts of the site are editable in WordPress. Custom code should be reserved for design and product behavior.', 'codesblock-core' ); ?></p>

		<div class="cbcore-stats">
			<div><strong><?php echo esc_html( isset( $post_count->publish ) ? $post_count->publish : 0 ); ?></strong><span><?php esc_html_e( 'Published articles', 'codesblock-core' ); ?></span></div>
			<div><strong><?php echo esc_html( isset( $course_count->publish ) ? $course_count->publish : 0 ); ?></strong><span><?php esc_html_e( 'Published courses', 'codesblock-core' ); ?></span></div>
		</div>

		<div class="cbcore-grid">
			<section><h2><?php esc_html_e( 'Write an article', 'codesblock-core' ); ?></h2><p><?php esc_html_e( 'Use normal WordPress posts, categories, featured images, excerpts, and the Featured or Members-only checkboxes.', 'codesblock-core' ); ?></p><a class="button button-primary" href="<?php echo esc_url( admin_url( 'post-new.php' ) ); ?>"><?php esc_html_e( 'Add article', 'codesblock-core' ); ?></a></section>
			<section><h2><?php esc_html_e( 'Build a course', 'codesblock-core' ); ?></h2><p><?php esc_html_e( 'Course content and every selling detail live together in the Course editor. The theme only decides how they look.', 'codesblock-core' ); ?></p><a class="button button-primary" href="<?php echo esc_url( admin_url( 'post-new.php?post_type=course' ) ); ?>"><?php esc_html_e( 'Add course', 'codesblock-core' ); ?></a></section>
			<section><h2><?php esc_html_e( 'Change the brand', 'codesblock-core' ); ?></h2><p><?php esc_html_e( 'Update the logo, homepage message, promotion, footer copy, social links, and menus without editing PHP.', 'codesblock-core' ); ?></p><a class="button" href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>"><?php esc_html_e( 'Open design settings', 'codesblock-core' ); ?></a></section>
			<section><h2><?php esc_html_e( 'Sell access', 'codesblock-core' ); ?></h2><p><?php esc_html_e( 'Paid Memberships Pro handles plans, checkout, Stripe, accounts, and renewals. CodesBlock only connects access to the course experience.', 'codesblock-core' ); ?></p><a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=pmpro-dashboard' ) ); ?>"><?php esc_html_e( 'Open memberships', 'codesblock-core' ); ?></a></section>
		</div>

		<h2><?php esc_html_e( 'Lean plugin stack', 'codesblock-core' ); ?></h2>
		<p><?php esc_html_e( 'Keep only what has a clear job: Paid Memberships Pro for payments, MailPoet for newsletters, Nextend for optional Google sign-in, and Highlighting Code Block for technical articles.', 'codesblock-core' ); ?></p>
	</div>
	<style>
		.cbcore-admin{max-width:1100px}.cbcore-lede{font-size:16px;max-width:760px}.cbcore-stats,.cbcore-grid{display:grid;gap:16px;margin:24px 0}.cbcore-stats{grid-template-columns:repeat(2,minmax(0,220px))}.cbcore-stats div,.cbcore-grid section{background:#fff;border:1px solid #dcdcde;border-radius:12px;padding:20px}.cbcore-stats strong{display:block;font-size:28px;color:#12233f}.cbcore-stats span{color:#646970}.cbcore-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.cbcore-grid h2{margin-top:0;color:#12233f}@media(max-width:782px){.cbcore-grid,.cbcore-stats{grid-template-columns:1fr}}
	</style>
	<?php
}

