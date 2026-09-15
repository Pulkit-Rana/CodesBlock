<?php
/**
 * Plugin Name: CodesBlock Core
 * Description: Keeps Courses, course fields, content priorities, and protected previews independent from the active theme.
 * Version: 1.3.1
 * Requires at least: 6.5
 * Requires PHP: 7.4
 * Author: CodesBlock
 * Text Domain: codesblock-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CBCORE_VERSION', '1.3.1' );
define( 'CBCORE_PATH', plugin_dir_path( __FILE__ ) );
define( 'CBCORE_URL', plugin_dir_url( __FILE__ ) );

require_once CBCORE_PATH . 'includes/content-model.php';
require_once CBCORE_PATH . 'includes/access-control.php';
require_once CBCORE_PATH . 'includes/course-offers.php';
require_once CBCORE_PATH . 'includes/admin.php';

/**
 * Prepare the small amount of durable site structure needed by the theme.
 */
function cbcore_activate() {
	cbcore_register_content_model();

	if ( ! get_page_by_path( 'articles' ) ) {
		$page_id = wp_insert_post(
			array(
				'post_title'   => __( 'Articles', 'codesblock-core' ),
				'post_name'    => 'articles',
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'page_template' => 'page-articles.php',
			)
		);

		if ( ! is_wp_error( $page_id ) ) {
			update_post_meta( $page_id, '_wp_page_template', 'page-articles.php' );
		}
	}

	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'cbcore_activate' );
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );

/**
 * Register new public content routes safely on plugin updates without
 * flushing rewrite rules on every request.
 */
function cbcore_maybe_flush_rewrite_rules() {
	if ( CBCORE_VERSION !== get_option( 'cbcore_rewrite_version' ) ) {
		flush_rewrite_rules();
		update_option( 'cbcore_rewrite_version', CBCORE_VERSION, false );
	}
}
add_action( 'init', 'cbcore_maybe_flush_rewrite_rules', 20 );
