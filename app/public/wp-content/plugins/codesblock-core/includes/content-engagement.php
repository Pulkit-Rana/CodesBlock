<?php
/**
 * First-party article and course engagement metrics.
 *
 * The site deliberately keeps these measurements local: no IP addresses or
 * third-party trackers are stored. A browser gets a random first-party ID so
 * refreshes do not inflate a view count and a visitor can change one rating.
 *
 * @package CodesBlockCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CBCORE_ENGAGEMENT_VERSION', '1.0.0' );

/**
 * @return string
 */
function cbcore_engagement_table_name() {
	global $wpdb;
	return $wpdb->prefix . 'cbcore_engagement';
}

/**
 * Create the small table that prevents duplicate ratings and follows.
 */
function cbcore_install_engagement_storage() {
	global $wpdb;

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$table_name      = cbcore_engagement_table_name();
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE {$table_name} (
		post_id bigint(20) unsigned NOT NULL,
		visitor_hash char(64) NOT NULL,
		user_id bigint(20) unsigned NOT NULL default 0,
		rating tinyint(3) unsigned NOT NULL default 0,
		subscribed tinyint(1) unsigned NOT NULL default 0,
		viewed_at datetime NULL default NULL,
		modified_at datetime NOT NULL,
		PRIMARY KEY  (post_id,visitor_hash),
		KEY user_id (user_id),
		KEY post_rating (post_id,rating),
		KEY post_subscribed (post_id,subscribed)
	) {$charset_collate};";

	dbDelta( $sql );
	update_option( 'cbcore_engagement_version', CBCORE_ENGAGEMENT_VERSION, false );
}

/**
 * Run the schema upgrade once after an existing site updates the plugin.
 */
function cbcore_maybe_install_engagement_storage() {
	if ( CBCORE_ENGAGEMENT_VERSION !== get_option( 'cbcore_engagement_version' ) ) {
		cbcore_install_engagement_storage();
	}
}
add_action( 'plugins_loaded', 'cbcore_maybe_install_engagement_storage', 20 );

/**
 * @param int $post_id Post or course ID.
 * @return bool
 */
function cbcore_is_engageable_content( $post_id ) {
	return $post_id > 0
		&& 'publish' === get_post_status( $post_id )
		&& in_array( get_post_type( $post_id ), array( 'post', 'course' ), true );
}

/**
 * Supply a random, first-party visitor ID without fingerprinting a person.
 */
function cbcore_ensure_engagement_visitor_cookie() {
	$cookie_name = 'cbcore_visitor';
	$cookie      = isset( $_COOKIE[ $cookie_name ] ) ? (string) wp_unslash( $_COOKIE[ $cookie_name ] ) : '';

	if ( preg_match( '/^[a-f0-9-]{36}$/i', $cookie ) ) {
		return $cookie;
	}

	$cookie = wp_generate_uuid4();
	if ( ! headers_sent() ) {
		setcookie( $cookie_name, $cookie, time() + YEAR_IN_SECONDS, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, is_ssl(), true );
		$_COOKIE[ $cookie_name ] = $cookie;
	}

	return $cookie;
}

/**
 * @return array{hash:string,user_id:int}
 */
function cbcore_get_engagement_identity() {
	$user_id = get_current_user_id();
	if ( $user_id ) {
		return array(
			'hash'    => hash( 'sha256', 'user:' . $user_id ),
			'user_id' => $user_id,
		);
	}

	return array(
		'hash'    => hash( 'sha256', 'visitor:' . cbcore_ensure_engagement_visitor_cookie() ),
		'user_id' => 0,
	);
}

/**
 * @param int $post_id Post or course ID.
 * @return array{views:int,rating_average:float,rating_count:int,subscribers:int}
 */
function cbcore_get_engagement_metrics( $post_id ) {
	$post_id = absint( $post_id );
	return array(
		'views'          => absint( get_post_meta( $post_id, '_cbcore_views', true ) ),
		'rating_average' => (float) get_post_meta( $post_id, '_cbcore_rating_average', true ),
		'rating_count'   => absint( get_post_meta( $post_id, '_cbcore_rating_count', true ) ),
		'subscribers'    => absint( get_post_meta( $post_id, '_cbcore_subscriber_count', true ) ),
	);
}

/**
 * Count members who have actually started a course in the existing progress
 * store. This intentionally replaces the old display-only fallback number.
 *
 * @param int $course_id Course ID.
 * @return int
 */
function cbcore_get_course_learners_count( $course_id ) {
	$course_id = absint( $course_id );
	if ( 'course' !== get_post_type( $course_id ) || ! function_exists( 'cbcommerce_get_user_course_progress' ) ) {
		return 0;
	}

	$user_ids = get_users(
		array(
			'fields'   => 'ids',
			'meta_key' => 'codesblock_course_progress',
		)
	);
	$count = 0;
	foreach ( $user_ids as $user_id ) {
		$progress = cbcommerce_get_user_course_progress( $user_id );
		if ( isset( $progress[ $course_id ] ) && absint( $progress[ $course_id ]['percent'] ) > 0 ) {
			$count++;
		}
	}

	return $count;
}

/**
 * Keep public aggregate values in post meta so templates never need to query
 * personal engagement rows.
 *
 * @param int $post_id Post or course ID.
 * @return array{views:int,rating_average:float,rating_count:int,subscribers:int}
 */
function cbcore_refresh_engagement_aggregates( $post_id ) {
	global $wpdb;

	$post_id = absint( $post_id );
	$table   = cbcore_engagement_table_name();
	$ratings = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT COUNT(*) AS total, AVG(rating) AS average FROM {$table} WHERE post_id = %d AND rating BETWEEN 1 AND 5",
			$post_id
		)
	);
	$followers = (int) $wpdb->get_var(
		$wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE post_id = %d AND subscribed = 1", $post_id )
	);

	update_post_meta( $post_id, '_cbcore_rating_count', isset( $ratings->total ) ? (int) $ratings->total : 0 );
	update_post_meta( $post_id, '_cbcore_rating_average', isset( $ratings->average ) ? round( (float) $ratings->average, 1 ) : 0 );
	update_post_meta( $post_id, '_cbcore_subscriber_count', $followers );

	return cbcore_get_engagement_metrics( $post_id );
}

/**
 * Record at most one view per visitor per article/course in a 12-hour window.
 *
 * @param int $post_id Post or course ID.
 * @return array{views:int,rating_average:float,rating_count:int,subscribers:int}
 */
function cbcore_record_unique_view( $post_id ) {
	global $wpdb;

	$post_id = absint( $post_id );
	if ( ! cbcore_is_engageable_content( $post_id ) ) {
		return cbcore_get_engagement_metrics( $post_id );
	}

	$identity = cbcore_get_engagement_identity();
	$table    = cbcore_engagement_table_name();
	$last_seen = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT viewed_at FROM {$table} WHERE post_id = %d AND visitor_hash = %s",
			$post_id,
			$identity['hash']
		)
	);

	if ( $last_seen && ( time() - strtotime( $last_seen . ' UTC' ) ) < ( 12 * HOUR_IN_SECONDS ) ) {
		return cbcore_get_engagement_metrics( $post_id );
	}

	$now = current_time( 'mysql', true );
	$wpdb->query(
		$wpdb->prepare(
			"INSERT INTO {$table} (post_id, visitor_hash, user_id, viewed_at, modified_at)
			 VALUES (%d, %s, %d, %s, %s)
			 ON DUPLICATE KEY UPDATE user_id = IF(VALUES(user_id) > 0, VALUES(user_id), user_id), viewed_at = VALUES(viewed_at), modified_at = VALUES(modified_at)",
			$post_id,
			$identity['hash'],
			$identity['user_id'],
			$now,
			$now
		)
	);

	$views = absint( get_post_meta( $post_id, '_cbcore_views', true ) ) + 1;
	update_post_meta( $post_id, '_cbcore_views', $views );
	return cbcore_get_engagement_metrics( $post_id );
}

/**
 * Count the initial page render for posts and courses. The REST request is
 * retained for cached pages where PHP did not render the current request.
 */
function cbcore_track_singular_engagement_view() {
	if ( is_singular( array( 'post', 'course' ) ) ) {
		cbcore_record_unique_view( get_queried_object_id() );
	}
}
add_action( 'template_redirect', 'cbcore_track_singular_engagement_view', 1 );

/**
 * @param int $post_id Post or course ID.
 * @param int $rating Rating from 1 to 5.
 * @return array{views:int,rating_average:float,rating_count:int,subscribers:int}
 */
function cbcore_save_article_rating( $post_id, $rating ) {
	global $wpdb;

	$post_id = absint( $post_id );
	$rating  = absint( $rating );
	if ( ! cbcore_is_engageable_content( $post_id ) || $rating < 1 || $rating > 5 ) {
		return cbcore_get_engagement_metrics( $post_id );
	}

	$identity = cbcore_get_engagement_identity();
	$table    = cbcore_engagement_table_name();
	$now      = current_time( 'mysql', true );
	$wpdb->query(
		$wpdb->prepare(
			"INSERT INTO {$table} (post_id, visitor_hash, user_id, rating, modified_at)
			 VALUES (%d, %s, %d, %d, %s)
			 ON DUPLICATE KEY UPDATE user_id = IF(VALUES(user_id) > 0, VALUES(user_id), user_id), rating = VALUES(rating), modified_at = VALUES(modified_at)",
			$post_id,
			$identity['hash'],
			$identity['user_id'],
			$rating,
			$now
		)
	);

	return cbcore_refresh_engagement_aggregates( $post_id );
}

/**
 * @param int $post_id Post or course ID.
 * @return array{metrics:array{views:int,rating_average:float,rating_count:int,subscribers:int},subscribed:bool}|WP_Error
 */
function cbcore_toggle_article_subscription( $post_id ) {
	global $wpdb;

	$post_id = absint( $post_id );
	if ( ! is_user_logged_in() ) {
		return new WP_Error( 'cbcore_sign_in_required', __( 'Please sign in to follow this article.', 'codesblock-core' ), array( 'status' => 401 ) );
	}
	if ( ! cbcore_is_engageable_content( $post_id ) ) {
		return new WP_Error( 'cbcore_invalid_content', __( 'This content is not available for following.', 'codesblock-core' ), array( 'status' => 404 ) );
	}

	$identity   = cbcore_get_engagement_identity();
	$table      = cbcore_engagement_table_name();
	$subscribed = (int) $wpdb->get_var(
		$wpdb->prepare( "SELECT subscribed FROM {$table} WHERE post_id = %d AND visitor_hash = %s", $post_id, $identity['hash'] )
	);
	$subscribed = $subscribed ? 0 : 1;
	$now        = current_time( 'mysql', true );
	$wpdb->query(
		$wpdb->prepare(
			"INSERT INTO {$table} (post_id, visitor_hash, user_id, subscribed, modified_at)
			 VALUES (%d, %s, %d, %d, %s)
			 ON DUPLICATE KEY UPDATE user_id = VALUES(user_id), subscribed = VALUES(subscribed), modified_at = VALUES(modified_at)",
			$post_id,
			$identity['hash'],
			$identity['user_id'],
			$subscribed,
			$now
		)
	);

	return array(
		'metrics'    => cbcore_refresh_engagement_aggregates( $post_id ),
		'subscribed' => (bool) $subscribed,
	);
}

/**
 * @param WP_REST_Request $request Request.
 * @return true|WP_Error
 */
function cbcore_verify_engagement_request( $request ) {
	$nonce = $request->get_header( 'X-WP-Nonce' );
	if ( $nonce && wp_verify_nonce( $nonce, 'wp_rest' ) ) {
		return true;
	}
	return new WP_Error( 'cbcore_invalid_request', __( 'Refresh this page before updating engagement.', 'codesblock-core' ), array( 'status' => 403 ) );
}

/**
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response
 */
function cbcore_rest_track_view( $request ) {
	return rest_ensure_response( cbcore_record_unique_view( absint( $request['id'] ) ) );
}

/**
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response
 */
function cbcore_rest_rate_content( $request ) {
	return rest_ensure_response( cbcore_save_article_rating( absint( $request['id'] ), absint( $request->get_param( 'rating' ) ) ) );
}

/**
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function cbcore_rest_toggle_subscription( $request ) {
	$result = cbcore_toggle_article_subscription( absint( $request['id'] ) );
	return is_wp_error( $result ) ? $result : rest_ensure_response( $result );
}

/**
 * Register small same-origin REST actions used by the article controls.
 */
function cbcore_register_engagement_routes() {
	$route = '/content/(?P<id>\\d+)/engagement';
	$args  = array(
		'args' => array(
			'id' => array(
				'validate_callback' => function ( $value ) {
					return cbcore_is_engageable_content( absint( $value ) );
				},
			),
		),
	);

	register_rest_route( 'codesblock/v1', $route . '/view', array_merge( $args, array(
		'methods'             => WP_REST_Server::CREATABLE,
		'callback'            => 'cbcore_rest_track_view',
		'permission_callback' => 'cbcore_verify_engagement_request',
	) ) );
	register_rest_route( 'codesblock/v1', $route . '/rating', array_merge( $args, array(
		'methods'             => WP_REST_Server::CREATABLE,
		'callback'            => 'cbcore_rest_rate_content',
		'permission_callback' => 'cbcore_verify_engagement_request',
		'args'                => array_merge( $args['args'], array( 'rating' => array( 'required' => true, 'sanitize_callback' => 'absint' ) ) ),
	) ) );
	register_rest_route( 'codesblock/v1', $route . '/subscription', array_merge( $args, array(
		'methods'             => WP_REST_Server::CREATABLE,
		'callback'            => 'cbcore_rest_toggle_subscription',
		'permission_callback' => 'cbcore_verify_engagement_request',
	) ) );
}
add_action( 'rest_api_init', 'cbcore_register_engagement_routes' );

/**
 * Add the metrics UI only where readers can act on it.
 */
function cbcore_enqueue_engagement_assets() {
	$is_detail = is_singular( array( 'post', 'course' ) );
	$is_feed   = is_page_template( 'page-articles.php' );
	if ( ! $is_detail && ! $is_feed ) {
		return;
	}

	$style_path  = CBCORE_PATH . 'assets/css/content-engagement.css';
	$script_path = CBCORE_PATH . 'assets/js/content-engagement.js';
	wp_enqueue_style( 'cbcore-content-engagement', CBCORE_URL . 'assets/css/content-engagement.css', array(), file_exists( $style_path ) ? (string) filemtime( $style_path ) : CBCORE_VERSION );
	if ( ! $is_detail ) {
		return;
	}
	wp_enqueue_script( 'cbcore-content-engagement', CBCORE_URL . 'assets/js/content-engagement.js', array(), file_exists( $script_path ) ? (string) filemtime( $script_path ) : CBCORE_VERSION, true );
	wp_localize_script(
		'cbcore-content-engagement',
		'cbContentEngagement',
		array(
			'restUrl'  => esc_url_raw( rest_url( 'codesblock/v1/content/' . get_queried_object_id() . '/engagement/' ) ),
			'nonce'    => wp_create_nonce( 'wp_rest' ),
			'loggedIn' => is_user_logged_in(),
			'loginUrl'  => wp_login_url( get_permalink( get_queried_object_id() ) ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'cbcore_enqueue_engagement_assets', 20 );

/**
 * @param int    $post_id Post or course ID.
 * @param string $context 'detail' or 'compact'.
 */
function cbcore_render_engagement_summary( $post_id, $context = 'detail' ) {
	$metrics = cbcore_get_engagement_metrics( $post_id );
	$average = $metrics['rating_count'] ? number_format_i18n( $metrics['rating_average'], 1 ) : __( 'New', 'codesblock-core' );
	?>
	<div class="cb-engagement cb-engagement--<?php echo esc_attr( $context ); ?>" data-engagement-summary>
		<span title="<?php esc_attr_e( 'Unique article views', 'codesblock-core' ); ?>"><b aria-hidden="true">◉</b> <strong data-engagement-views><?php echo esc_html( number_format_i18n( $metrics['views'] ) ); ?></strong> <?php esc_html_e( 'views', 'codesblock-core' ); ?></span>
		<span title="<?php esc_attr_e( 'Reader rating', 'codesblock-core' ); ?>"><b aria-hidden="true">★</b> <strong data-engagement-average><?php echo esc_html( $average ); ?></strong> <small data-engagement-rating-count><?php echo $metrics['rating_count'] ? esc_html( sprintf( _n( '(%s rating)', '(%s ratings)', $metrics['rating_count'], 'codesblock-core' ), number_format_i18n( $metrics['rating_count'] ) ) ) : esc_html__( '(no ratings yet)', 'codesblock-core' ); ?></small></span>
		<?php if ( 'detail' === $context ) : ?>
			<span title="<?php esc_attr_e( 'Members following this article', 'codesblock-core' ); ?>"><b aria-hidden="true">◎</b> <strong data-engagement-subscribers><?php echo esc_html( number_format_i18n( $metrics['subscribers'] ) ); ?></strong> <?php esc_html_e( 'followers', 'codesblock-core' ); ?></span>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Render the interactive controls below a full article/course.
 *
 * @param int $post_id Post or course ID.
 */
function cbcore_render_engagement_controls( $post_id ) {
	$post_id = absint( $post_id );
	if ( ! cbcore_is_engageable_content( $post_id ) ) {
		return;
	}
	?>
	<section class="cb-engagement-actions" aria-labelledby="cb-engagement-heading">
		<div>
			<p class="cb-engagement-actions__eyebrow"><?php esc_html_e( 'Reader signals', 'codesblock-core' ); ?></p>
			<h2 id="cb-engagement-heading"><?php esc_html_e( 'Was this useful?', 'codesblock-core' ); ?></h2>
			<p><?php esc_html_e( 'Your rating helps us improve what we publish. Follow this article to keep it in your learning list.', 'codesblock-core' ); ?></p>
		</div>
		<div class="cb-engagement-actions__controls">
			<div class="cb-rating-control" role="group" aria-label="<?php esc_attr_e( 'Rate this article from one to five stars', 'codesblock-core' ); ?>">
				<?php for ( $rating = 1; $rating <= 5; $rating++ ) : ?>
					<button type="button" data-engagement-rating="<?php echo esc_attr( $rating ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Rate %d out of 5', 'codesblock-core' ), $rating ) ); ?>">★</button>
				<?php endfor; ?>
			</div>
			<button class="cb-follow-button" type="button" data-engagement-follow><?php esc_html_e( 'Follow article', 'codesblock-core' ); ?></button>
			<p class="cb-engagement-feedback" data-engagement-feedback aria-live="polite"></p>
		</div>
	</section>
	<?php
}
