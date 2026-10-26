<?php
/**
 * Interview practice routing, access, and session records.
 *
 * @package CodesBlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve a visitor's interview-session entitlement.
 *
 * @param bool $logged_in Whether the visitor is signed in.
 * @param bool $paid      Whether the visitor has a paid CodesBlock membership.
 * @param bool $free_used Whether the one free session has been reserved.
 * @return string
 */
function codesblock_interview_access_state( $logged_in, $paid, $free_used ) {
	if ( ! $logged_in ) {
		return 'login_required';
	}

	if ( $paid ) {
		return 'paid_session';
	}

	return $free_used ? 'membership_required' : 'free_session';
}

/** Return the unique option name used for one account's free-session claim. */
function codesblock_interview_free_session_option( $user_id ) {
	return 'codesblock_interview_free_session_' . absint( $user_id );
}

/** Check whether an account has already claimed its free interview session. */
function codesblock_interview_free_session_used( $user_id ) {
	return (bool) get_option( codesblock_interview_free_session_option( $user_id ), false );
}

/** Return the saved free-session ID when the reservation has completed. */
function codesblock_interview_free_session_id( $user_id ) {
	$value = get_option( codesblock_interview_free_session_option( $user_id ), false );
	return is_numeric( $value ) ? absint( $value ) : 0;
}

/** Atomically reserve an account's one free session using wp_options.option_name uniqueness. */
function codesblock_interview_reserve_free_session( $user_id ) {
	if ( ! absint( $user_id ) ) {
		return false;
	}

	return add_option( codesblock_interview_free_session_option( $user_id ), 'reserved', '', false );
}

/** Confirm that a post is an interview record owned by the requested account. */
function codesblock_interview_session_belongs_to_user( $session, $user_id ) {
	return is_object( $session )
		&& isset( $session->post_type, $session->post_author )
		&& 'cb_interview_session' === $session->post_type
		&& absint( $session->post_author ) === absint( $user_id )
		&& absint( $user_id ) > 0;
}

/**
 * Return the first-phase interview tracks and starter prompts.
 *
 * @return array<string,array{label:string,prompt:string}>
 */
function codesblock_interview_tracks() {
	return array(
		'system-design' => array(
			'label'  => __( 'System design', 'codesblock' ),
			'prompt' => __( 'Design a URL shortening service that supports 100 million new links each month. Walk through your assumptions, APIs, data model, and the tradeoffs you would validate first.', 'codesblock' ),
		),
		'ai-engineering' => array(
			'label'  => __( 'AI engineering', 'codesblock' ),
			'prompt' => __( 'Design a reliable retrieval-augmented generation service for an internal knowledge base. Explain ingestion, retrieval, latency, evaluation, and how you would handle stale or missing sources.', 'codesblock' ),
		),
		'behavioral' => array(
			'label'  => __( 'Behavioral & leadership', 'codesblock' ),
			'prompt' => __( 'Tell me about a project where a technical decision created an unexpected problem. Explain the context, the choice you made, how you responded, and what you would change now.', 'codesblock' ),
		),
	);
}

/**
 * Validate and normalize interview setup values.
 *
 * @param mixed $track      Requested track.
 * @param mixed $difficulty Requested difficulty.
 * @return array{track:string,difficulty:string}|false
 */
function codesblock_interview_normalize_setup( $track, $difficulty ) {
	$track      = sanitize_key( (string) $track );
	$difficulty = sanitize_key( (string) $difficulty );
	$tracks     = codesblock_interview_tracks();
	$levels     = array( 'foundation', 'intermediate', 'advanced' );

	if ( ! isset( $tracks[ $track ] ) || ! in_array( $difficulty, $levels, true ) ) {
		return false;
	}

	return array(
		'track'      => $track,
		'difficulty' => $difficulty,
	);
}

/** Register the interview URL and private session record type. */
function codesblock_register_interview_feature() {
	register_post_type(
		'cb_interview_session',
		array(
			'labels'          => array( 'name' => __( 'Interview sessions', 'codesblock' ) ),
			'public'          => false,
			'show_ui'         => false,
			'show_in_rest'    => false,
			'exclude_from_search' => true,
			'supports'        => array( 'title', 'author' ),
			'capability_type' => 'post',
			'map_meta_cap'    => true,
		)
	);

	add_rewrite_rule( '^interview/?$', 'index.php?cb_interview_page=1', 'top' );

	if ( '1' !== (string) get_option( 'codesblock_interview_rewrite_version', '' ) ) {
		flush_rewrite_rules( false );
		update_option( 'codesblock_interview_rewrite_version', '1', false );
	}
}
add_action( 'init', 'codesblock_register_interview_feature', 9 );

/** Make the interview route available to the main query. */
function codesblock_interview_query_vars( $vars ) {
	$vars[] = 'cb_interview_page';
	return $vars;
}
add_filter( 'query_vars', 'codesblock_interview_query_vars' );

/** Select the interview page template for /interview/. */
function codesblock_interview_template( $template ) {
	if ( get_query_var( 'cb_interview_page' ) ) {
		$interview_template = get_theme_file_path( '/page-interview.php' );
		if ( file_exists( $interview_template ) ) {
			return $interview_template;
		}
	}

	return $template;
}
add_filter( 'template_include', 'codesblock_interview_template' );

/**
 * Check the existing CodesBlock membership entitlement.
 *
 * @param int $user_id User ID.
 * @return bool
 */
function codesblock_interview_user_has_paid_access( $user_id ) {
	if ( function_exists( 'cbcommerce_user_has_paid_access' ) ) {
		return (bool) cbcommerce_user_has_paid_access( $user_id );
	}

	return user_can( $user_id, 'manage_options' );
}

/** Return an error when a logged-in user does not own the requested session. */
function codesblock_interview_get_session( WP_REST_Request $request ) {
	$session_id = absint( $request['id'] );
	$session    = get_post( $session_id );

	if ( ! codesblock_interview_session_belongs_to_user( $session, get_current_user_id() ) ) {
		return new WP_Error( 'codesblock_interview_not_found', __( 'That interview session could not be found.', 'codesblock' ), array( 'status' => 404 ) );
	}

	return new WP_REST_Response(
		array(
			'id'         => $session_id,
			'track'      => get_post_meta( $session_id, '_cb_interview_track', true ),
			'difficulty' => get_post_meta( $session_id, '_cb_interview_difficulty', true ),
			'prompt'     => get_post_meta( $session_id, '_cb_interview_prompt', true ),
		),
		200
	);
}

/** Create a private session after checking and reserving its entitlement. */
function codesblock_interview_create_session( WP_REST_Request $request ) {
	$user_id = get_current_user_id();
	$params  = (array) $request->get_json_params();
	$tracks  = codesblock_interview_tracks();
	$setup = codesblock_interview_normalize_setup( $params['track'] ?? '', $params['difficulty'] ?? 'intermediate' );
	if ( false === $setup ) {
		return new WP_Error( 'codesblock_interview_invalid_setup', __( 'Choose a valid interview track and experience level.', 'codesblock' ), array( 'status' => 400 ) );
	}
	$track = $setup['track'];
	$level = $setup['difficulty'];

	$free_key = codesblock_interview_free_session_option( $user_id );
	$paid     = codesblock_interview_user_has_paid_access( $user_id );
	$free_used = codesblock_interview_free_session_used( $user_id );
	$access    = codesblock_interview_access_state( is_user_logged_in(), $paid, $free_used );

	if ( 'login_required' === $access ) {
		return new WP_Error( 'codesblock_interview_login_required', __( 'Sign in to start an interview session.', 'codesblock' ), array( 'status' => 401 ) );
	}

	if ( 'membership_required' === $access ) {
		return new WP_Error( 'codesblock_interview_membership_required', __( 'Your free interview session has been used. Choose a CodesBlock membership to continue.', 'codesblock' ), array( 'status' => 403 ) );
	}

	$reserved_free_session = false;
	if ( 'free_session' === $access ) {
		$reserved_free_session = codesblock_interview_reserve_free_session( $user_id );
		if ( ! $reserved_free_session ) {
			return new WP_Error( 'codesblock_interview_membership_required', __( 'Your free interview session has been used. Choose a CodesBlock membership to continue.', 'codesblock' ), array( 'status' => 403 ) );
		}
	}

	$session_id = wp_insert_post(
		array(
			'post_type'   => 'cb_interview_session',
			'post_status' => 'private',
			'post_author' => $user_id,
			'post_title'  => sprintf( '%s interview · %s', $tracks[ $track ]['label'], current_time( 'Y-m-d H:i' ) ),
		),
		true
	);

	if ( is_wp_error( $session_id ) || ! $session_id ) {
		if ( $reserved_free_session ) {
			delete_option( $free_key );
		}
		return new WP_Error( 'codesblock_interview_create_failed', __( 'We could not start your interview. Please try again.', 'codesblock' ), array( 'status' => 500 ) );
	}

	update_post_meta( $session_id, '_cb_interview_track', $track );
	update_post_meta( $session_id, '_cb_interview_difficulty', $level );
	update_post_meta( $session_id, '_cb_interview_prompt', $tracks[ $track ]['prompt'] );
	if ( $reserved_free_session ) {
		update_option( $free_key, absint( $session_id ), false );
	}

	return new WP_REST_Response(
		array(
			'id'         => absint( $session_id ),
			'track'      => $track,
			'difficulty' => $level,
			'prompt'     => $tracks[ $track ]['prompt'],
			'url'        => add_query_arg( 'session', absint( $session_id ), home_url( '/interview/' ) ),
		),
		201
	);
}

/** Register cookie-authenticated, owner-scoped interview session endpoints. */
function codesblock_register_interview_rest_routes() {
	register_rest_route(
		'codesblock/v1',
		'/interview-sessions',
		array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'codesblock_interview_create_session',
			'permission_callback'  => 'is_user_logged_in',
		),
	);
	register_rest_route(
		'codesblock/v1',
		'/interview-sessions/(?P<id>\d+)',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'codesblock_interview_get_session',
			'permission_callback'  => 'is_user_logged_in',
		),
	);
}
add_action( 'rest_api_init', 'codesblock_register_interview_rest_routes' );

/** Load the first-phase interview setup and workspace interface. */
function codesblock_interview_assets() {
	if ( ! get_query_var( 'cb_interview_page' ) ) {
		return;
	}

	$css_path = get_theme_file_path( '/assets/css/interview-session.css' );
	$js_path  = get_theme_file_path( '/assets/js/interview-session.js' );
	wp_enqueue_style( 'codesblock-interview-session', get_theme_file_uri( '/assets/css/interview-session.css' ), array( 'codesblock-responsive' ), file_exists( $css_path ) ? (string) filemtime( $css_path ) : '1.0.0' );
	wp_enqueue_script( 'codesblock-interview-session', get_theme_file_uri( '/assets/js/interview-session.js' ), array(), file_exists( $js_path ) ? (string) filemtime( $js_path ) : '1.0.0', true );
	wp_localize_script(
		'codesblock-interview-session',
		'cbInterviewData',
		array(
			'apiUrl'   => esc_url_raw( rest_url( 'codesblock/v1/interview-sessions' ) ),
			'nonce'    => wp_create_nonce( 'wp_rest' ),
			'loggedIn' => is_user_logged_in(),
			'freeUsed' => is_user_logged_in() && codesblock_interview_free_session_used( get_current_user_id() ),
			'loginUrl' => wp_login_url( home_url( '/interview/' ) ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'codesblock_interview_assets', 100 );
