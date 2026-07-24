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
	$polish_css_path = get_template_directory() . '/assets/css/polish.css';
	$main_js_path    = get_template_directory() . '/assets/js/main.js';
	$member_js_path  = get_template_directory() . '/assets/js/member.js';
	wp_enqueue_style( 'codesblock-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap', array(), null );
	wp_enqueue_style( 'codesblock-main', get_template_directory_uri() . '/assets/css/main.css', array(), file_exists( $main_css_path ) ? (string) filemtime( $main_css_path ) : '2.0.0' );
	wp_enqueue_style( 'codesblock-member', get_template_directory_uri() . '/assets/css/member.css', array( 'codesblock-main' ), file_exists( $member_css_path ) ? (string) filemtime( $member_css_path ) : '1.1.0' );
	wp_enqueue_style( 'codesblock-polish', get_template_directory_uri() . '/assets/css/polish.css', array( 'codesblock-main', 'codesblock-member' ), file_exists( $polish_css_path ) ? (string) filemtime( $polish_css_path ) : '2.0.0' );
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
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'codesblock_course_portal_assets' );

/**
 * Add conservative browser protections that do not interfere with checkout,
 * social login, the Customizer, or embedded course media.
 */
function codesblock_security_headers() {
	if ( is_admin() || headers_sent() ) {
		return;
	}

	header( 'X-Content-Type-Options: nosniff' );
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

/**
 * Build one truthful announcement-bar action for the current visitor.
 */
function codesblock_get_promo_config() {
	$mode        = codesblock_sanitize_promo_mode( get_theme_mod( 'codesblock_promo_mode', 'smart' ) );
	$campaign    = sanitize_key( get_theme_mod( 'codesblock_promo_campaign', 'smart-membership-launch' ) );
	$dismissible = (bool) get_theme_mod( 'codesblock_promo_dismissible', true );
	$config      = array(
		'visible'     => (bool) get_theme_mod( 'codesblock_promo_enabled', true ),
		'campaign'    => $campaign ?: 'smart-membership-launch',
		'badge'       => __( 'Free account', 'codesblock' ),
		'text'        => __( 'Save course progress and unlock the starter library.', 'codesblock' ),
		'cta'         => __( 'Join free', 'codesblock' ),
		'url'         => '#paywall-overlay',
		'classes'     => array( 'js-open-paywall' ),
		'member_view' => 'register',
		'dismissible' => $dismissible,
	);

	$is_admin_session = function_exists( 'cbcommerce_user_can_access_admin' )
		? cbcommerce_user_can_access_admin()
		: current_user_can( 'manage_options' );
	if ( $is_admin_session ) {
		$config['visible'] = false;
		return $config;
	}

	$is_frontend_member = function_exists( 'cbcommerce_is_frontend_member' )
		? cbcommerce_is_frontend_member()
		: is_user_logged_in();
	$has_paid_access = $is_frontend_member && function_exists( 'cbcommerce_user_has_paid_access' ) && cbcommerce_user_has_paid_access();
	$offer           = function_exists( 'cbcommerce_promo_offer' ) ? cbcommerce_promo_offer() : false;

	if ( 'custom' === $mode ) {
		$config['badge']   = get_theme_mod( 'codesblock_promo_badge', 'New' );
		$config['text']    = get_theme_mod( 'codesblock_promo_text', 'Practical courses and interview prep for working developers.' );
		$config['cta']     = get_theme_mod( 'codesblock_promo_cta_text', 'Explore courses' );
		$config['url']     = get_theme_mod( 'codesblock_promo_cta_url', '#courses' );
		$config['classes'] = array();
		$config['member_view'] = '';
		return $config;
	}

	if ( $has_paid_access ) {
		$config['campaign'] = 'member-learning-return';
		$config['badge']    = __( 'Member', 'codesblock' );
		$config['text']     = __( 'Your courses and saved progress are ready when you are.', 'codesblock' );
		$config['cta']      = __( 'Continue learning', 'codesblock' );
		$config['url']      = home_url( '/#my-learning' );
		$config['classes']  = array();
		$config['member_view'] = '';
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
			'description' => __( 'Smart mode promotes the live membership offer only when checkout is ready. Change the campaign ID whenever you start a new test.', 'codesblock' ),
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
			'label'   => __( 'Show top promo bar', 'codesblock' ),
			'section' => 'codesblock_announcement',
			'type'    => 'checkbox',
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
			'label'   => __( 'Campaign goal', 'codesblock' ),
			'section' => 'codesblock_announcement',
			'type'    => 'select',
			'choices' => array(
				'smart'        => __( 'Smart membership funnel', 'codesblock' ),
				'free-account' => __( 'Free account', 'codesblock' ),
				'newsletter'   => __( 'MailPoet newsletter', 'codesblock' ),
				'paid-offer'   => __( 'PMPro paid offer', 'codesblock' ),
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
			'label'   => __( 'Allow visitors to dismiss it for 7 days', 'codesblock' ),
			'section' => 'codesblock_announcement',
			'type'    => 'checkbox',
		)
	);

	$promo_fields = array(
		'codesblock_promo_badge'     => array(
			'label'             => __( 'Promo badge', 'codesblock' ),
			'default'           => 'New',
			'type'              => 'text',
			'sanitize_callback' => 'sanitize_text_field',
		),
		'codesblock_promo_text'      => array(
			'label'             => __( 'Promo text', 'codesblock' ),
			'default'           => 'Practical courses and interview prep for working developers.',
			'type'              => 'text',
			'sanitize_callback' => 'sanitize_text_field',
		),
		'codesblock_promo_cta_text'  => array(
			'label'             => __( 'Promo button text', 'codesblock' ),
			'default'           => 'Explore courses',
			'type'              => 'text',
			'sanitize_callback' => 'sanitize_text_field',
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
			'type'              => 'url',
			'sanitize_callback' => 'esc_url_raw',
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
				'label'   => $field['label'],
				'section' => str_starts_with( $setting_id, 'codesblock_promo_' ) ? 'codesblock_announcement' : 'codesblock_homepage',
				'type'    => $field['type'],
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
	$item_path = rtrim( $item_path, '/' );
	if ( '' === $item_path ) {
		$item_path = '/';
	}

	// WordPress marks parent URLs as current even when they only share a path.
	$classes = array_diff( (array) $classes, array( 'current-menu-item', 'current_page_item', 'current-menu-ancestor', 'current-menu-parent', 'current_page_parent', 'current_page_ancestor' ) );

	if ( '' === $item_fragment && $current_path === $item_path ) {
		$classes[] = 'current-menu-item';
	} elseif ( is_singular( 'course' ) && ( '/courses' === $item_path || '/course' === $item_path ) ) {
		$classes[] = 'current-menu-item';
	} elseif ( ( is_singular( 'post' ) || is_home() ) && ( '/articles' === $item_path || '/blog' === $item_path ) ) {
		$classes[] = 'current-menu-item';
	}

	return array_unique( $classes );
}
add_filter( 'nav_menu_css_class', 'codesblock_filter_nav_menu_css_class', 10, 2 );

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
