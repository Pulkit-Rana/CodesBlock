<?php
/**
 * Theme setup and assets.
 *
 * @package CodesBlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once get_template_directory() . '/inc.php';

function codesblock_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo', array( 'height' => 80, 'width' => 240, 'flex-height' => true, 'flex-width' => true ) );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/editor.css' );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'codesblock' ),
			'footer'  => __( 'Footer Menu', 'codesblock' ),
		)
	);
}
add_action( 'after_setup_theme', 'codesblock_setup' );

function codesblock_assets() {
	$main_css_path   = get_template_directory() . '/assets/css/main.css';
	$member_css_path = get_template_directory() . '/assets/css/member.css';
	$account_css_path = get_template_directory() . '/assets/css/member-dashboard.css';
	$polish_css_path = get_template_directory() . '/assets/css/polish.css';
	$main_js_path    = get_template_directory() . '/assets/js/main.js';
	$member_js_path  = get_template_directory() . '/assets/js/member.js';
	wp_enqueue_style( 'codesblock-fonts', 'https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;500;600;700&display=swap', array(), null );
	wp_enqueue_style( 'codesblock-main', get_template_directory_uri() . '/assets/css/main.css', array(), file_exists( $main_css_path ) ? (string) filemtime( $main_css_path ) : '2.0.0' );
	wp_enqueue_style( 'codesblock-member', get_template_directory_uri() . '/assets/css/member.css', array( 'codesblock-main' ), file_exists( $member_css_path ) ? (string) filemtime( $member_css_path ) : '1.1.0' );
	wp_enqueue_style( 'codesblock-polish', get_template_directory_uri() . '/assets/css/polish.css', array( 'codesblock-main', 'codesblock-member' ), file_exists( $polish_css_path ) ? (string) filemtime( $polish_css_path ) : '2.0.0' );
	if ( is_page_template( 'page-my-learning.php' ) ) {
		wp_enqueue_style( 'codesblock-member-dashboard', get_template_directory_uri() . '/assets/css/member-dashboard.css', array( 'codesblock-polish' ), file_exists( $account_css_path ) ? (string) filemtime( $account_css_path ) : '1.0.0' );
	}
	wp_enqueue_script( 'codesblock-main', get_template_directory_uri() . '/assets/js/main.js', array(), file_exists( $main_js_path ) ? (string) filemtime( $main_js_path ) : '2.0.0', true );
	wp_enqueue_script( 'codesblock-member', get_template_directory_uri() . '/assets/js/member.js', array( 'codesblock-main' ), file_exists( $member_js_path ) ? (string) filemtime( $member_js_path ) : '1.1.0', true );
	wp_localize_script(
		'codesblock-member',
		'cbMemberData',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php', 'relative' ),
			'nonce'   => wp_create_nonce( 'cbcommerce_member' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'codesblock_assets' );

/**
 * Course portal assets — loaded only on course archive & single course pages.
 */
function codesblock_course_portal_assets() {
	$is_course = is_singular( 'course' ) || is_post_type_archive( 'course' );
	$is_premium_post = is_singular( 'post' ) && (bool) get_post_meta( get_the_ID(), '_codesblock_premium', true );
	$course_css_path = get_template_directory() . '/assets/css/course-portal.css';
	$system_design_css_path = get_template_directory() . '/assets/css/system-design-course.css';
	$course_js_path  = get_template_directory() . '/assets/js/course-portal.js';

	if ( ! $is_course && ! $is_premium_post ) {
		return;
	}

	wp_enqueue_style(
		'codesblock-course-portal',
		get_template_directory_uri() . '/assets/css/course-portal.css',
		array( 'codesblock-main' ),
		file_exists( $course_css_path ) ? (string) filemtime( $course_css_path ) : '2.0.0'
	);

	if ( is_singular( 'course' ) && in_array( get_post_field( 'post_name', get_the_ID() ), array( 'system-design-interview-sprint', 'system-design-interview-lab', 'crack-the-system-design-interview' ), true ) ) {
		wp_enqueue_style(
			'codesblock-system-design-course',
			get_template_directory_uri() . '/assets/css/system-design-course.css',
			array( 'codesblock-course-portal', 'codesblock-polish' ),
			file_exists( $system_design_css_path ) ? (string) filemtime( $system_design_css_path ) : '1.0.0'
		);
	}

	wp_enqueue_script(
		'codesblock-course-portal',
		get_template_directory_uri() . '/assets/js/course-portal.js',
		array( 'codesblock-main' ),
		file_exists( $course_js_path ) ? (string) filemtime( $course_js_path ) : '2.0.0',
		true
	);

	/* Pass course-specific data only when the current visitor may view the course. */
	if ( is_singular( 'course' ) ) {
		$post_id         = get_the_ID();
		$user_can_access = ! function_exists( 'codesblock_user_can_view_protected_content' ) || codesblock_user_can_view_protected_content( $post_id );
		$ai_faqs_raw     = $user_can_access ? get_post_meta( $post_id, '_course_ai_faqs', true ) : '';
		$ai_faqs         = $ai_faqs_raw ? json_decode( $ai_faqs_raw, true ) : array();

		wp_localize_script(
			'codesblock-course-portal',
			'cbPortalData',
			array(
				'title'       => wp_strip_all_tags( get_the_title( $post_id ) ),
				'summary'     => $user_can_access ? wp_strip_all_tags( get_post_meta( $post_id, '_course_ai_summary', true ) ?: get_the_excerpt() ) : '',
				'faqs'        => is_array( $ai_faqs ) ? $ai_faqs : array(),
				'canUseGuide' => $user_can_access,
				'startCourseOnLoad' => $user_can_access && is_user_logged_in() && absint( isset( $_GET['cb_course_start'] ) ? $_GET['cb_course_start'] : 0 ) === $post_id,
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'codesblock_course_portal_assets' );

/**
 * Load the course map only inside the lesson-reading experience.
 */
function codesblock_course_learning_assets() {
	if ( ! is_singular( 'course_lesson' ) ) {
		return;
	}

	$learning_css_path = get_template_directory() . '/assets/css/course-learning.css';
	$learning_js_path  = get_template_directory() . '/assets/js/course-learning.js';

	wp_enqueue_style(
		'codesblock-course-learning',
		get_template_directory_uri() . '/assets/css/course-learning.css',
		array( 'codesblock-polish' ),
		file_exists( $learning_css_path ) ? (string) filemtime( $learning_css_path ) : '1.0.0'
	);

	wp_enqueue_script(
		'codesblock-course-learning',
		get_template_directory_uri() . '/assets/js/course-learning.js',
		array(),
		file_exists( $learning_js_path ) ? (string) filemtime( $learning_js_path ) : '1.0.0',
		true
	);
}
add_action( 'wp_enqueue_scripts', 'codesblock_course_learning_assets' );

/**
 * Keep the shared reading scale last in the cascade on every front-end view.
 */
function codesblock_typography_assets() {
	$typography_css_path = get_template_directory() . '/assets/css/typography.css';
	$responsive_css_path = get_template_directory() . '/assets/css/responsive.css';
	$dependencies        = array( 'codesblock-polish' );

	foreach ( array( 'codesblock-course-portal', 'codesblock-system-design-course', 'codesblock-course-learning' ) as $style_handle ) {
		if ( wp_style_is( $style_handle, 'enqueued' ) ) {
			$dependencies[] = $style_handle;
		}
	}

	wp_enqueue_style(
		'codesblock-typography',
		get_template_directory_uri() . '/assets/css/typography.css',
		$dependencies,
		file_exists( $typography_css_path ) ? (string) filemtime( $typography_css_path ) : '1.0.0'
	);

	wp_enqueue_style(
		'codesblock-responsive',
		get_template_directory_uri() . '/assets/css/responsive.css',
		array( 'codesblock-typography' ),
		file_exists( $responsive_css_path ) ? (string) filemtime( $responsive_css_path ) : '1.0.0'
	);
}
add_action( 'wp_enqueue_scripts', 'codesblock_typography_assets', 99 );

/**
 * Convert the editable "## Module" / "- Lesson" curriculum into a course tree.
 *
 * @param int $course_id Course post ID.
 * @return array<int, array{title:string,lessons:array<int,string>}>
 */
function codesblock_get_course_outline( $course_id ) {
	$syllabus = (string) get_post_meta( absint( $course_id ), '_course_syllabus', true );
	$modules  = array();
	$current  = -1;

	foreach ( preg_split( '/\R/', $syllabus ) as $raw_line ) {
		$line = trim( $raw_line );
		if ( '' === $line ) {
			continue;
		}

		if ( 0 === strpos( $line, '##' ) ) {
			$modules[] = array(
				'title'   => trim( substr( $line, 2 ) ),
				'lessons' => array(),
			);
			$current = count( $modules ) - 1;
			continue;
		}

		if ( 0 === strpos( $line, '-' ) ) {
			if ( $current < 0 ) {
				$modules[] = array(
					'title'   => __( 'Course lessons', 'codesblock' ),
					'lessons' => array(),
				);
				$current = 0;
			}
			$modules[ $current ]['lessons'][] = trim( substr( $line, 1 ) );
		}
	}

	return $modules;
}

/**
 * Add conservative browser protections that do not interfere with checkout,
 * social login, the Customizer, or embedded course media.
 */
function codesblock_security_headers() {
	if ( is_admin() || headers_sent() ) {
		return;
	}

	header( 'X-Content-Type-Options: nosniff' );
	header( 'X-Frame-Options: SAMEORIGIN' );
	header( "Content-Security-Policy: frame-ancestors 'self'" );
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );
	header( 'Permissions-Policy: camera=(), microphone=(), geolocation=()' );
}
add_action( 'send_headers', 'codesblock_security_headers' );

function codesblock_sanitize_checkbox( $checked ) {
	return ( isset( $checked ) && true === (bool) $checked );
}

function codesblock_sanitize_promo_mode( $mode ) {
	$allowed = array( 'smart', 'free-account', 'newsletter', 'paid-offer', 'custom' );
	return in_array( $mode, $allowed, true ) ? $mode : 'smart';
}

function codesblock_sanitize_promo_scheme( $scheme ) {
	$allowed = array( 'brand', 'midnight', 'emerald', 'sunset' );
	return in_array( $scheme, $allowed, true ) ? $scheme : 'brand';
}

function codesblock_sanitize_promo_scope( $scope ) {
	$allowed = array( 'all', 'home', 'learning' );
	return in_array( $scope, $allowed, true ) ? $scope : 'all';
}

function codesblock_sanitize_promo_audience( $audience ) {
	$allowed = array( 'everyone', 'visitors', 'members' );
	return in_array( $audience, $allowed, true ) ? $audience : 'everyone';
}

function codesblock_sanitize_promo_dismissal( $duration ) {
	$allowed = array( 'session', '24', '168', '720' );
	return in_array( (string) $duration, $allowed, true ) ? (string) $duration : '168';
}

function codesblock_sanitize_promo_icon( $icon ) {
	$icon = trim( wp_strip_all_tags( (string) $icon ) );
	if ( function_exists( 'grapheme_substr' ) ) {
		return grapheme_substr( $icon, 0, 4 );
	}
	return function_exists( 'mb_substr' ) ? mb_substr( $icon, 0, 4 ) : substr( $icon, 0, 8 );
}

/**
 * Accept a safe external URL, a root-relative path, or an in-page anchor.
 */
function codesblock_sanitize_promo_url( $url ) {
	$url = trim( (string) $url );
	if ( '' === $url ) {
		return '';
	}

	if ( preg_match( '/^#[A-Za-z][A-Za-z0-9_:.\-]*$/', $url ) ) {
		return $url;
	}

	if ( 0 === strpos( $url, '/' ) && 0 !== strpos( $url, '//' ) ) {
		return esc_url_raw( $url );
	}

	if ( preg_match( '#^https?://#i', $url ) ) {
		return esc_url_raw( $url, array( 'http', 'https' ) );
	}

	return '';
}

function codesblock_customize_promo_is_custom( $control ) {
	$setting = $control->manager->get_setting( 'codesblock_promo_mode' );
	return $setting && 'custom' === $setting->value();
}

function codesblock_customize_promo_is_dismissible( $control ) {
	$setting = $control->manager->get_setting( 'codesblock_promo_dismissible' );
	return $setting && (bool) $setting->value();
}

/**
 * Sanitize a date and time entered in the site's WordPress timezone.
 */
function codesblock_sanitize_local_datetime( $value ) {
	$value = sanitize_text_field( $value );
	if ( '' === $value ) {
		return '';
	}

	$date = DateTimeImmutable::createFromFormat( 'Y-m-d\TH:i', $value, wp_timezone() );
	return $date && $date->format( 'Y-m-d\TH:i' ) === $value ? $value : '';
}

/**
 * Convert a Customizer date and time to a Unix timestamp.
 */
function codesblock_promo_datetime_to_timestamp( $value ) {
	$value = codesblock_sanitize_local_datetime( $value );
	if ( '' === $value ) {
		return 0;
	}

	$date = DateTimeImmutable::createFromFormat( 'Y-m-d\TH:i', $value, wp_timezone() );
	return $date ? $date->getTimestamp() : 0;
}

/**
 * Prevent an invalid or backwards campaign window from being published.
 */
function codesblock_validate_promo_datetime( $validity, $value, $setting ) {
	$value = (string) $value;
	if ( '' !== $value && '' === codesblock_sanitize_local_datetime( $value ) ) {
		$validity->add( 'invalid_datetime', __( 'Enter a valid date and time.', 'codesblock' ) );
		return $validity;
	}

	$posted_values = $setting->manager->unsanitized_post_values();
	$start_value   = 'codesblock_promo_start' === $setting->id
		? $value
		: ( array_key_exists( 'codesblock_promo_start', $posted_values ) ? $posted_values['codesblock_promo_start'] : get_theme_mod( 'codesblock_promo_start', '' ) );
	$end_value     = 'codesblock_promo_end' === $setting->id
		? $value
		: ( array_key_exists( 'codesblock_promo_end', $posted_values ) ? $posted_values['codesblock_promo_end'] : get_theme_mod( 'codesblock_promo_end', '' ) );
	$start         = codesblock_promo_datetime_to_timestamp( $start_value );
	$end           = codesblock_promo_datetime_to_timestamp( $end_value );

	if ( $start && $end && $end <= $start ) {
		$validity->add( 'invalid_campaign_window', __( 'Campaign end must be later than campaign start.', 'codesblock' ) );
	}

	return $validity;
}

/**
 * Build one truthful announcement-bar action for the current visitor.
 */
function codesblock_get_promo_config() {
	$mode        = codesblock_sanitize_promo_mode( get_theme_mod( 'codesblock_promo_mode', 'smart' ) );
	$campaign    = sanitize_key( get_theme_mod( 'codesblock_promo_campaign', 'smart-membership-launch' ) );
	$dismissible = (bool) get_theme_mod( 'codesblock_promo_dismissible', true );
	$scope       = codesblock_sanitize_promo_scope( get_theme_mod( 'codesblock_promo_scope', 'all' ) );
	$audience    = codesblock_sanitize_promo_audience( get_theme_mod( 'codesblock_promo_audience', 'everyone' ) );
	$starts_at   = codesblock_promo_datetime_to_timestamp( get_theme_mod( 'codesblock_promo_start', '' ) );
	$ends_at     = codesblock_promo_datetime_to_timestamp( get_theme_mod( 'codesblock_promo_end', '' ) );
	$now         = current_datetime()->getTimestamp();
	$is_preview  = is_customize_preview();
	$config      = array(
		'visible'     => (bool) get_theme_mod( 'codesblock_promo_enabled', true ),
		'campaign'    => $campaign ?: 'smart-membership-launch',
		'scheme'      => codesblock_sanitize_promo_scheme( get_theme_mod( 'codesblock_promo_scheme', 'brand' ) ),
		'icon'        => codesblock_sanitize_promo_icon( get_theme_mod( 'codesblock_promo_icon', '✦' ) ),
		'badge'       => __( 'Free account', 'codesblock' ),
		'text'        => __( 'Save course progress and unlock the starter library.', 'codesblock' ),
		'cta'         => __( 'Join free', 'codesblock' ),
		'url'         => '#member-overlay',
		'classes'     => array( 'js-open-member' ),
		'member_view' => 'register',
		'dismissible' => $dismissible,
		'dismissal'   => codesblock_sanitize_promo_dismissal( get_theme_mod( 'codesblock_promo_dismissal', '168' ) ),
		'countdown'   => (bool) get_theme_mod( 'codesblock_promo_countdown', false ) && $ends_at > $now,
		'ends_at'     => $ends_at,
		'mobile'      => (bool) get_theme_mod( 'codesblock_promo_mobile', true ),
		'new_tab'     => false,
		'preview'     => $is_preview,
		'status'      => '',
	);

	$is_admin_session = function_exists( 'cbcommerce_user_can_access_admin' )
		? cbcommerce_user_can_access_admin()
		: current_user_can( 'manage_options' );
	if ( $is_admin_session && ! $is_preview ) {
		$config['visible'] = false;
		return $config;
	}

	$is_frontend_member = function_exists( 'cbcommerce_is_frontend_member' )
		? cbcommerce_is_frontend_member()
		: is_user_logged_in();
	$has_paid_access = $is_frontend_member && function_exists( 'cbcommerce_user_has_paid_access' ) && cbcommerce_user_has_paid_access();
	$offer           = function_exists( 'cbcommerce_promo_offer' ) ? cbcommerce_promo_offer() : false;
	$is_learning     = ( is_home() && ! is_front_page() ) || is_singular( array( 'post', 'course' ) ) || is_post_type_archive( 'course' );

	if ( ! $is_preview && (
		( $starts_at && $now < $starts_at ) ||
		( $ends_at && $now >= $ends_at ) ||
		( 'home' === $scope && ! is_front_page() ) ||
		( 'learning' === $scope && ! $is_learning ) ||
		( 'visitors' === $audience && $is_frontend_member ) ||
		( 'members' === $audience && ! $is_frontend_member )
	) ) {
		$config['visible'] = false;
		return $config;
	}

	if ( 'custom' === $mode ) {
		$config['badge']   = sanitize_text_field( get_theme_mod( 'codesblock_promo_badge', 'New' ) ) ?: __( 'New', 'codesblock' );
		$config['text']    = sanitize_text_field( get_theme_mod( 'codesblock_promo_text', 'Practical courses and interview prep for working developers.' ) ) ?: __( 'Practical courses and interview prep for working developers.', 'codesblock' );
		$config['cta']     = sanitize_text_field( get_theme_mod( 'codesblock_promo_cta_text', 'Explore courses' ) );
		$config['url']     = codesblock_sanitize_promo_url( get_theme_mod( 'codesblock_promo_cta_url', '#courses' ) );
		$config['classes'] = array();
		$config['member_view'] = '';
		$config['new_tab'] = (bool) get_theme_mod( 'codesblock_promo_new_tab', false );
		return $config;
	}

	if ( $has_paid_access ) {
		$config['visible'] = false;
		return $config;
	}

	if ( 'newsletter' === $mode ) {
		$config['campaign'] = $campaign ?: 'weekly-build-note';
		$config['badge']    = __( 'Weekly note', 'codesblock' );
		$config['text']     = __( 'One practical engineering idea and one focused learning prompt.', 'codesblock' );
		$config['cta']      = __( 'Get the email', 'codesblock' );
		$config['url']      = '#newsletter';
		$config['classes']  = array();
		$config['member_view'] = '';
		return $config;
	}

	if ( 'paid-offer' === $mode && ! $offer ) {
		if ( $is_preview ) {
			$config['campaign']   = 'paid-offer-setup-needed';
			$config['badge']      = __( 'Setup needed', 'codesblock' );
			$config['text']       = __( 'No live PMPro offer is available. Configure checkout or choose Smart membership funnel.', 'codesblock' );
			$config['cta']        = '';
			$config['url']        = '';
			$config['classes']    = array();
			$config['member_view'] = '';
			$config['dismissible'] = false;
			$config['status']      = 'setup-needed';
		} else {
			$config['visible'] = false;
		}
		return $config;
	}

	if ( ( 'smart' === $mode || 'paid-offer' === $mode ) && $offer ) {
		$config['campaign'] = sanitize_key( $offer['campaign'] );
		$config['badge']    = $offer['badge'];
		$config['text']     = $offer['text'];
		$config['cta']      = $offer['cta'];
		$config['url']      = $offer['url'];
		$config['classes']  = array();
		$config['member_view'] = '';
		return $config;
	}

	if ( $is_frontend_member ) {
		$config['campaign'] = 'starter-course-path';
		$config['badge']    = __( 'Starter', 'codesblock' );
		$config['text']     = __( 'Paid enrollment is being prepared. Keep building with your free courses.', 'codesblock' );
		$config['cta']      = __( 'Browse courses', 'codesblock' );
		$config['url']      = get_post_type_archive_link( 'course' ) ?: home_url( '/courses/' );
		$config['classes']  = array();
		$config['member_view'] = '';
	}

	return $config;
}

function codesblock_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'codesblock_homepage',
		array(
			'title'    => __( 'CodesBlock Homepage', 'codesblock' ),
			'priority' => 35,
		)
	);
	$wp_customize->add_section(
		'codesblock_announcement',
		array(
			'title'       => __( 'Top Announcement Bar', 'codesblock' ),
			'description' => __( 'Preview changes here before publishing. Smart modes supply their own copy; choose Custom link and copy to edit the message and CTA. Use a real end time for countdowns and change the Campaign ID only for a genuinely new campaign.', 'codesblock' ),
			'priority'    => 34,
		)
	);

	$fields = array(
		'codesblock_hero_eyebrow' => array(
			'label'   => __( 'Hero eyebrow', 'codesblock' ),
			'default' => 'Stay Relevant. Stay Curious.',
			'type'    => 'text',
		),
		'codesblock_hero_title'   => array(
			'label'   => __( 'Hero title', 'codesblock' ),
			'default' => 'CodesBlock',
			'type'    => 'text',
		),
		'codesblock_hero_lede'    => array(
			'label'   => __( 'Hero description', 'codesblock' ),
			'default' => 'Production AI engineering, system design, and interview practice for developers who want practical proof - not another passive tutorial catalog.',
			'type'    => 'textarea',
		),
		'codesblock_hero_primary_label' => array(
			'label'   => __( 'Primary button label', 'codesblock' ),
			'default' => 'Explore paths',
			'type'    => 'text',
		),
		'codesblock_hero_secondary_label' => array(
			'label'   => __( 'Secondary button label', 'codesblock' ),
			'default' => 'Read free lessons',
			'type'    => 'text',
		),
	);

	foreach ( $fields as $setting_id => $field ) {
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => $field['default'],
				'sanitize_callback' => 'textarea' === $field['type'] ? 'sanitize_textarea_field' : 'sanitize_text_field',
			)
		);

		$wp_customize->add_control(
			$setting_id,
			array(
				'label'   => $field['label'],
				'section' => 'codesblock_homepage',
				'type'    => $field['type'],
			)
		);
	}

	$wp_customize->add_setting(
		'codesblock_promo_enabled',
		array(
			'default'           => true,
			'sanitize_callback' => 'codesblock_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'codesblock_promo_enabled',
		array(
			'label'   => __( 'Show announcement bar', 'codesblock' ),
			'section' => 'codesblock_announcement',
			'type'    => 'checkbox',
		)
	);

	$promo_selects = array(
		'codesblock_promo_scheme' => array(
			__( 'Colour style', 'codesblock' ),
			'brand',
			'codesblock_sanitize_promo_scheme',
			array(
				'brand'    => __( 'CodesBlock blue', 'codesblock' ),
				'midnight' => __( 'Midnight blue', 'codesblock' ),
				'emerald'  => __( 'Emerald', 'codesblock' ),
				'sunset'   => __( 'Warm sunset', 'codesblock' ),
			),
		),
		'codesblock_promo_scope' => array(
			__( 'Where to show it', 'codesblock' ),
			'all',
			'codesblock_sanitize_promo_scope',
			array(
				'all'      => __( 'Entire site', 'codesblock' ),
				'home'     => __( 'Homepage only', 'codesblock' ),
				'learning' => __( 'Articles and course pages', 'codesblock' ),
			),
		),
		'codesblock_promo_audience' => array(
			__( 'Audience', 'codesblock' ),
			'everyone',
			'codesblock_sanitize_promo_audience',
			array(
				'everyone' => __( 'Everyone (smart mode still hides irrelevant offers)', 'codesblock' ),
				'visitors' => __( 'Signed-out visitors only', 'codesblock' ),
				'members'  => __( 'Signed-in members only', 'codesblock' ),
			),
		),
	);

	foreach ( $promo_selects as $setting_id => $field ) {
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => $field[1],
				'sanitize_callback' => $field[2],
			)
		);
		$wp_customize->add_control(
			$setting_id,
			array(
				'label'   => $field[0],
				'section' => 'codesblock_announcement',
				'type'    => 'select',
				'choices' => $field[3],
			)
		);
	}

	$wp_customize->add_setting(
		'codesblock_promo_icon',
		array(
			'default'           => '✦',
			'sanitize_callback' => 'codesblock_sanitize_promo_icon',
		)
	);
	$wp_customize->add_control(
		'codesblock_promo_icon',
		array(
			'label'       => __( 'Decorative icon or emoji', 'codesblock' ),
			'description' => __( 'Keep this to one short symbol, for example ✦, ⚡, or 🔥.', 'codesblock' ),
			'section'     => 'codesblock_announcement',
			'type'        => 'text',
			'input_attrs' => array( 'maxlength' => 8 ),
		)
	);

	$wp_customize->add_setting(
		'codesblock_promo_mobile',
		array(
			'default'           => true,
			'sanitize_callback' => 'codesblock_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'codesblock_promo_mobile',
		array(
			'label'       => __( 'Show on phones', 'codesblock' ),
			'description' => __( 'Recommended: the compact mobile layout uses little screen space.', 'codesblock' ),
			'section'     => 'codesblock_announcement',
			'type'        => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'codesblock_promo_mode',
		array(
			'default'           => 'smart',
			'sanitize_callback' => 'codesblock_sanitize_promo_mode',
		)
	);
	$wp_customize->add_control(
		'codesblock_promo_mode',
		array(
			'label'       => __( 'Campaign goal', 'codesblock' ),
			'description' => __( 'Smart, account, newsletter, and paid-offer modes use system copy. Select Custom to edit the fields below.', 'codesblock' ),
			'section'     => 'codesblock_announcement',
			'type'        => 'select',
			'choices'     => array(
				'smart'        => __( 'Smart membership funnel', 'codesblock' ),
				'free-account' => __( 'Free account', 'codesblock' ),
				'newsletter'   => __( 'MailPoet newsletter', 'codesblock' ),
				'paid-offer'   => __( 'PMPro paid offer (requires live checkout)', 'codesblock' ),
				'custom'       => __( 'Custom link and copy', 'codesblock' ),
			),
		)
	);

	$wp_customize->add_setting(
		'codesblock_promo_campaign',
		array(
			'default'           => 'smart-membership-launch',
			'sanitize_callback' => 'sanitize_key',
		)
	);
	$wp_customize->add_control(
		'codesblock_promo_campaign',
		array(
			'label'       => __( 'Campaign ID', 'codesblock' ),
			'description' => __( 'Used in analytics and to reset a previous dismissal.', 'codesblock' ),
			'section'     => 'codesblock_announcement',
			'type'        => 'text',
			'input_attrs' => array( 'maxlength' => 64 ),
		)
	);

	$wp_customize->add_setting(
		'codesblock_promo_dismissible',
		array(
			'default'           => true,
			'sanitize_callback' => 'codesblock_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'codesblock_promo_dismissible',
		array(
			'label'   => __( 'Allow visitors to dismiss it', 'codesblock' ),
			'section' => 'codesblock_announcement',
			'type'    => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'codesblock_promo_dismissal',
		array(
			'default'           => '168',
			'sanitize_callback' => 'codesblock_sanitize_promo_dismissal',
		)
	);
	$wp_customize->add_control(
		'codesblock_promo_dismissal',
		array(
			'label'       => __( 'Show again after dismissal', 'codesblock' ),
			'description' => __( '7 days is the recommended balance. A new campaign ID can show a genuinely new message sooner.', 'codesblock' ),
			'section'     => 'codesblock_announcement',
			'type'        => 'select',
			'active_callback' => 'codesblock_customize_promo_is_dismissible',
			'choices'     => array(
				'session' => __( 'Next browser session', 'codesblock' ),
				'24'      => __( '1 day', 'codesblock' ),
				'168'     => __( '7 days (recommended)', 'codesblock' ),
				'720'     => __( '30 days', 'codesblock' ),
			),
		)
	);

	foreach ( array( 'start' => __( 'Campaign starts', 'codesblock' ), 'end' => __( 'Campaign ends', 'codesblock' ) ) as $suffix => $label ) {
		$setting_id = 'codesblock_promo_' . $suffix;
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => '',
				'sanitize_callback' => 'codesblock_sanitize_local_datetime',
				'validate_callback' => 'codesblock_validate_promo_datetime',
			)
		);
		$wp_customize->add_control(
			$setting_id,
			array(
				'label'       => $label,
				'description' => 'start' === $suffix ? __( 'Optional. Uses the timezone set under Settings → General.', 'codesblock' ) : __( 'Optional. The bar hides automatically at this time.', 'codesblock' ),
				'section'     => 'codesblock_announcement',
				'type'        => 'datetime-local',
				'input_attrs' => array( 'step' => 60 ),
			)
		);
	}

	$wp_customize->add_setting(
		'codesblock_promo_countdown',
		array(
			'default'           => false,
			'sanitize_callback' => 'codesblock_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'codesblock_promo_countdown',
		array(
			'label'       => __( 'Show countdown to campaign end', 'codesblock' ),
			'description' => __( 'Only use for a real deadline. It appears only when a valid campaign end is set.', 'codesblock' ),
			'section'     => 'codesblock_announcement',
			'type'        => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'codesblock_promo_new_tab',
		array(
			'default'           => false,
			'sanitize_callback' => 'codesblock_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'codesblock_promo_new_tab',
		array(
			'label'       => __( 'Open custom CTA in a new tab', 'codesblock' ),
			'description' => __( 'Leave off for CodesBlock pages and account actions.', 'codesblock' ),
			'section'     => 'codesblock_announcement',
			'type'        => 'checkbox',
			'active_callback' => 'codesblock_customize_promo_is_custom',
		)
	);

	$promo_fields = array(
		'codesblock_promo_badge'     => array(
			'label'             => __( 'Promo badge', 'codesblock' ),
			'default'           => 'New',
			'type'              => 'text',
			'sanitize_callback' => 'sanitize_text_field',
			'input_attrs'       => array( 'maxlength' => 24 ),
		),
		'codesblock_promo_text'      => array(
			'label'             => __( 'Promo text', 'codesblock' ),
			'default'           => 'Practical courses and interview prep for working developers.',
			'type'              => 'text',
			'sanitize_callback' => 'sanitize_text_field',
			'input_attrs'       => array( 'maxlength' => 120 ),
		),
		'codesblock_promo_cta_text'  => array(
			'label'             => __( 'Promo button text', 'codesblock' ),
			'default'           => 'Explore courses',
			'type'              => 'text',
			'sanitize_callback' => 'sanitize_text_field',
			'input_attrs'       => array( 'maxlength' => 30 ),
		),
		'codesblock_promo_cta_url'   => array(
			'label'             => __( 'Promo button URL', 'codesblock' ),
			'default'           => '#courses',
			'type'              => 'url',
			'sanitize_callback' => 'esc_url_raw',
		),
		'codesblock_hero_primary_url' => array(
			'label'             => __( 'Primary button URL', 'codesblock' ),
			'default'           => '#courses',
			'type'              => 'text',
			'sanitize_callback' => 'codesblock_sanitize_promo_url',
			'description'       => __( 'Use a full URL, /courses/, or an anchor such as #courses.', 'codesblock' ),
		),
		'codesblock_hero_secondary_url' => array(
			'label'             => __( 'Secondary button URL', 'codesblock' ),
			'default'           => '/articles/',
			'type'              => 'url',
			'sanitize_callback' => 'esc_url_raw',
		),
	);

	foreach ( $promo_fields as $setting_id => $field ) {
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => $field['default'],
				'sanitize_callback' => $field['sanitize_callback'],
			)
		);

		$wp_customize->add_control(
			$setting_id,
			array(
				'label'           => $field['label'],
				'description'     => $field['description'] ?? '',
				'section'         => 0 === strpos( $setting_id, 'codesblock_promo_' ) ? 'codesblock_announcement' : 'codesblock_homepage',
				'type'            => $field['type'],
				'input_attrs'     => $field['input_attrs'] ?? array(),
				'active_callback' => 0 === strpos( $setting_id, 'codesblock_promo_' ) ? 'codesblock_customize_promo_is_custom' : '__return_true',
			)
		);
	}

	$wp_customize->add_section(
		'codesblock_footer',
		array(
			'title'    => __( 'CodesBlock Footer & Social', 'codesblock' ),
			'priority' => 36,
		)
	);

	$footer_fields = array(
		'codesblock_footer_description' => array( __( 'Footer description', 'codesblock' ), 'AI-assisted courses, interview guides, and practical articles built around real developer growth.', 'textarea', 'sanitize_textarea_field' ),
		'codesblock_newsletter_heading'  => array( __( 'Newsletter heading', 'codesblock' ), 'Join the weekly build note', 'text', 'sanitize_text_field' ),
		'codesblock_newsletter_copy'     => array( __( 'Newsletter description', 'codesblock' ), 'One practical engineering idea and one focused learning prompt, delivered without the noise.', 'textarea', 'sanitize_textarea_field' ),
		'codesblock_youtube_url'         => array( __( 'YouTube URL', 'codesblock' ), 'https://www.youtube.com/@codesblock', 'url', 'esc_url_raw' ),
		'codesblock_instagram_url'       => array( __( 'Instagram URL', 'codesblock' ), 'https://www.instagram.com/codesblock', 'url', 'esc_url_raw' ),
		'codesblock_github_url'          => array( __( 'GitHub URL', 'codesblock' ), 'https://github.com/Pulkit-Rana/CodesBlock', 'url', 'esc_url_raw' ),
		'codesblock_support_url'         => array( __( 'Support / Buy me a coffee URL', 'codesblock' ), 'https://www.buymeacoffee.com/codesblock', 'url', 'esc_url_raw' ),
	);

	foreach ( $footer_fields as $setting_id => $field ) {
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => $field[1],
				'sanitize_callback' => $field[3],
			)
		);
		$wp_customize->add_control(
			$setting_id,
			array(
				'label'   => $field[0],
				'section' => 'codesblock_footer',
				'type'    => $field[2],
			)
		);
	}
}
add_action( 'customize_register', 'codesblock_customize_register' );

function codesblock_core_dependency_notice() {
	if ( current_user_can( 'activate_plugins' ) && ! defined( 'CBCORE_VERSION' ) ) {
		echo '<div class="notice notice-warning"><p><strong>' . esc_html__( 'CodesBlock Core is inactive.', 'codesblock' ) . '</strong> ' . esc_html__( 'Activate it to manage Courses and protected content independently from the theme.', 'codesblock' ) . '</p></div>';
	}
}
add_action( 'admin_notices', 'codesblock_core_dependency_notice' );

function codesblock_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Article Top Ad', 'codesblock' ),
			'id'            => 'article-top-ad',
			'description'   => __( 'Optional ad area below the article title and before the article body.', 'codesblock' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);

	register_sidebar(
		array(
			'name'          => __( 'Article Sidebar Ad', 'codesblock' ),
			'id'            => 'article-sidebar-ad',
			'description'   => __( 'Optional compact ad area inside the right article rail.', 'codesblock' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
	register_sidebar(
		array(
			'name'          => __( 'Home Sidebar Ad', 'codesblock' ),
			'id'            => 'home-sidebar-ad',
			'description'   => __( 'Optional ad area on the home page course sidebar.', 'codesblock' ),
			'before_widget' => '<div id="%1$s" class="side-card widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<div class="side-card-heading"><span>',
			'after_title'   => '</span></div>',
		)
	);
}
add_action( 'widgets_init', 'codesblock_widgets_init' );

function codesblock_excerpt_length() {
	return 24;
}
add_filter( 'excerpt_length', 'codesblock_excerpt_length' );

function codesblock_excerpt_more() {
	return '...';
}
add_filter( 'excerpt_more', 'codesblock_excerpt_more' );

function codesblock_seed_navigation() {
	if ( get_option( 'codesblock_seeded_navigation' ) ) {
		return;
	}

	$menu_name = 'CodesBlock Primary';
	$menu_id   = wp_create_nav_menu( $menu_name );

	if ( ! is_wp_error( $menu_id ) ) {
		$items = array(
			array( 'title' => 'Home', 'url' => home_url( '/' ) ),
			array( 'title' => 'Courses', 'url' => home_url( '/courses/' ) ),
			array( 'title' => 'Articles', 'url' => home_url( '/articles/' ) ),
			array( 'title' => 'Interview Guides', 'url' => home_url( '/#practice' ) ),
			array( 'title' => 'Start Here', 'url' => home_url( '/#start' ) ),
		);

		foreach ( $items as $item ) {
			wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'  => $item['title'],
					'menu-item-url'    => $item['url'],
					'menu-item-status' => 'publish',
				)
			);
		}

		set_theme_mod( 'nav_menu_locations', array( 'primary' => $menu_id, 'footer' => $menu_id ) );
	}

	update_option( 'codesblock_seeded_navigation', 1 );
}
add_action( 'after_switch_theme', 'codesblock_seed_navigation' );

/**
 * Ensure active blue highlight is applied strictly to the exact current page.
 */
function codesblock_filter_nav_menu_css_class( $classes, $item ) {
	$request_uri  = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '/';
	$current_path = (string) wp_parse_url( $request_uri, PHP_URL_PATH );
	$current_path = rtrim( $current_path, '/' );
	if ( '' === $current_path ) {
		$current_path = '/';
	}

	$item_url      = isset( $item->url ) ? (string) $item->url : '';
	$item_path     = (string) wp_parse_url( $item_url, PHP_URL_PATH );
	$item_fragment = (string) wp_parse_url( $item_url, PHP_URL_FRAGMENT );
	$item_title    = isset( $item->title ) ? strtolower( trim( wp_strip_all_tags( $item->title ) ) ) : '';
	$item_path = rtrim( $item_path, '/' );
	if ( '' === $item_path ) {
		$item_path = '/';
	}

	// WordPress marks parent URLs as current even when they only share a path.
	$classes = array_diff( (array) $classes, array( 'current-menu-item', 'current_page_item', 'current-menu-ancestor', 'current-menu-parent', 'current_page_parent', 'current_page_ancestor' ) );
	// A posts-front request is both the blog index and the front page; it belongs to Home only.
	$is_articles_context = is_singular( 'post' ) || ( is_home() && ! is_front_page() );
	$is_courses_context  = is_singular( 'course' ) || is_post_type_archive( 'course' );

	if ( '' === $item_fragment && $current_path === $item_path ) {
		$classes[] = 'current-menu-item';
	} elseif ( $is_courses_context && ( 'courses' === $item_fragment || '/courses' === $item_path || '/course' === $item_path ) ) {
		$classes[] = 'current-menu-item';
	} elseif ( $is_articles_context && ( '/articles' === $item_path || '/blog' === $item_path ) ) {
		$classes[] = 'current-menu-item';
	}

	if ( 'start' === $item_fragment || 'start here' === $item_title ) {
		$classes[] = 'menu-item-start-here';
	}

	return array_unique( $classes );
}
add_filter( 'nav_menu_css_class', 'codesblock_filter_nav_menu_css_class', 10, 2 );

/**
 * Keep aria-current synchronized with the normalized visual active state.
 */
function codesblock_filter_nav_menu_link_attributes( $atts, $item ) {
	$classes = codesblock_filter_nav_menu_css_class( isset( $item->classes ) ? $item->classes : array(), $item );

	if ( in_array( 'current-menu-item', $classes, true ) ) {
		$atts['aria-current'] = 'page';
	} else {
		unset( $atts['aria-current'] );
	}

	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'codesblock_filter_nav_menu_link_attributes', 10, 2 );

/**
 * Use a concise, descriptive homepage title in browser tabs and Yoast output.
 */
function codesblock_homepage_title( $title ) {
	if ( is_front_page() || is_home() ) {
		return 'CodesBlock | Production AI Engineering for Developers';
	}

	return $title;
}
add_filter( 'pre_get_document_title', 'codesblock_homepage_title', 100 );
add_filter( 'wpseo_title', 'codesblock_homepage_title', 100 );
add_filter( 'wpseo_opengraph_title', 'codesblock_homepage_title', 100 );

/**
 * Always use the compact glossy CB favicon instead of the larger header logo.
 */
function codesblock_site_icon_url( $url, $size = 512 ) {
	$icon_size = 512;

	if ( $size <= 32 ) {
		$icon_size = ( $size <= 16 ) ? 16 : 32;
	} elseif ( $size <= 192 ) {
		$icon_size = 192;
	}

	$relative_path = '/assets/images/favicon-cb-transparent-' . $icon_size . '.png';
	$file_path     = get_template_directory() . $relative_path;
	$icon_url      = get_template_directory_uri() . $relative_path;

	if ( file_exists( $file_path ) ) {
		$icon_url = add_query_arg( 'ver', (string) filemtime( $file_path ), $icon_url );
		return $icon_url;
	}

	return $url;
}
add_filter( 'get_site_icon_url', 'codesblock_site_icon_url', 100, 2 );

/**
 * Add explicit legacy and 16px favicon declarations after WordPress' icon tags.
 */
function codesblock_favicon_links() {
	$favicon_directory = get_template_directory() . '/assets/images/';
	$favicon_uri       = get_template_directory_uri() . '/assets/images/';
	$favicon_16_path   = $favicon_directory . 'favicon-cb-transparent-16.png';
	$favicon_ico_path  = $favicon_directory . 'favicon-cb-transparent.ico';
	if ( ! file_exists( $favicon_16_path ) || ! file_exists( $favicon_ico_path ) ) {
		return;
	}

	$favicon_16 = add_query_arg( 'ver', (string) filemtime( $favicon_16_path ), $favicon_uri . 'favicon-cb-transparent-16.png' );
	$favicon_ico = add_query_arg( 'ver', (string) filemtime( $favicon_ico_path ), $favicon_uri . 'favicon-cb-transparent.ico' );
	?>
	<link rel="icon" type="image/png" sizes="16x16" href="<?php echo esc_url( $favicon_16 ); ?>">
	<link rel="shortcut icon" type="image/x-icon" href="<?php echo esc_url( $favicon_ico ); ?>">
	<?php
}
add_action( 'wp_head', 'codesblock_favicon_links', 100 );

add_action('rest_api_init', function () {
  register_rest_route('codesblock/v1', '/stats', array(
    'methods' => 'GET',
    'callback' => 'codesblock_get_stats',
    'permission_callback' => '__return_true'
  ));
});
function codesblock_get_stats() {
  \ = (int) wp_count_posts('course')->publish;
  \ = (int) wp_count_posts('post')->publish;
  \ = count_users();
  return array(
    'courses' => \,
    'articles' => \,
    'learners' => \['total_users']
  );
}

