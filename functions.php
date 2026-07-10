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
require_once get_template_directory() . '/inc-course-meta.php';

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
	wp_enqueue_style( 'codesblock-fonts', 'https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Outfit:wght@500;600;700;800;900&family=Sora:wght@500;600;700;800&display=swap', array(), null );
	wp_enqueue_style( 'codesblock-main', get_template_directory_uri() . '/assets/css/main.css', array(), '3.0.0' );
	wp_enqueue_script( 'codesblock-main', get_template_directory_uri() . '/assets/js/main.js', array(), '1.2.0', true );
}
add_action( 'wp_enqueue_scripts', 'codesblock_assets' );

/**
 * Course portal assets — loaded only on course archive & single course pages.
 */
function codesblock_course_portal_assets() {
	if ( ! is_singular( 'course' ) && ! is_post_type_archive( 'course' ) ) {
		return;
	}

	wp_enqueue_style(
		'codesblock-course-portal',
		get_template_directory_uri() . '/assets/css/course-portal.css',
		array( 'codesblock-main' ),
		'1.0.0'
	);

	wp_enqueue_script(
		'codesblock-course-portal',
		get_template_directory_uri() . '/assets/js/course-portal.js',
		array( 'codesblock-main' ),
		'1.0.0',
		true
	);

	/* Pass course-specific data to JS (used by the AI Tutor) */
	if ( is_singular( 'course' ) ) {
		$post_id  = get_the_ID();
		$ai_faqs_raw = get_post_meta( $post_id, '_course_ai_faqs', true );
		$ai_faqs     = $ai_faqs_raw ? json_decode( $ai_faqs_raw, true ) : array();

		wp_localize_script(
			'codesblock-course-portal',
			'cbPortalData',
			array(
				'title'   => get_the_title( $post_id ),
				'summary' => get_post_meta( $post_id, '_course_ai_summary', true ) ?: get_the_excerpt(),
				'faqs'    => is_array( $ai_faqs ) ? $ai_faqs : array(),
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'codesblock_course_portal_assets' );

function codesblock_add_adsense() {
	?>
	<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-5837563798509200" crossorigin="anonymous"></script>
	<?php
}
add_action( 'wp_head', 'codesblock_add_adsense' );

function codesblock_register_course_type() {
	register_post_type(
		'course',
		array(
			'labels'       => array(
				'name'          => __( 'Courses', 'codesblock' ),
				'singular_name' => __( 'Course', 'codesblock' ),
				'add_new_item'  => __( 'Add New Course', 'codesblock' ),
				'edit_item'     => __( 'Edit Course', 'codesblock' ),
			),
			'public'       => true,
			'has_archive'  => true,
			'menu_icon'    => 'dashicons-welcome-learn-more',
			'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
			'show_in_rest' => true,
			'rewrite'      => array( 'slug' => 'courses' ),
		)
	);
}
add_action( 'init', 'codesblock_register_course_type' );

function codesblock_add_recommended_meta_box() {
	foreach ( array( 'post', 'course' ) as $screen ) {
		add_meta_box(
			'codesblock_recommended',
			__( 'CodesBlock Priority', 'codesblock' ),
			'codesblock_render_recommended_meta_box',
			$screen,
			'side',
			'high'
		);
	}
}
add_action( 'add_meta_boxes', 'codesblock_add_recommended_meta_box' );

function codesblock_render_recommended_meta_box( $post ) {
	wp_nonce_field( 'codesblock_save_recommended', 'codesblock_recommended_nonce' );
	$is_recommended = (bool) get_post_meta( $post->ID, '_codesblock_recommended', true );
	?>
	<label>
		<input type="checkbox" name="codesblock_recommended" value="1" <?php checked( $is_recommended ); ?>>
		<?php esc_html_e( 'Prioritize as recommended', 'codesblock' ); ?>
	</label>
	<p><?php esc_html_e( 'Recommended posts appear before regular latest posts. Recommended courses appear in the Articles sidebar.', 'codesblock' ); ?></p>
	<?php
}

function codesblock_save_recommended_meta( $post_id ) {
	if ( ! isset( $_POST['codesblock_recommended_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['codesblock_recommended_nonce'] ) ), 'codesblock_save_recommended' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['codesblock_recommended'] ) ) {
		update_post_meta( $post_id, '_codesblock_recommended', '1' );
		return;
	}

	delete_post_meta( $post_id, '_codesblock_recommended' );
}
add_action( 'save_post', 'codesblock_save_recommended_meta' );

function codesblock_sanitize_checkbox( $checked ) {
	return ( isset( $checked ) && true === (bool) $checked );
}

function codesblock_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'codesblock_homepage',
		array(
			'title'    => __( 'CodesBlock Homepage', 'codesblock' ),
			'priority' => 35,
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
			'default' => 'AI-assisted courses, practical articles, and guided learning paths for developers preparing for interviews, senior roles, and smarter day-to-day engineering.',
			'type'    => 'textarea',
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
			'section' => 'codesblock_homepage',
			'type'    => 'checkbox',
		)
	);

	$promo_fields = array(
		'codesblock_promo_text'      => array(
			'label'             => __( 'Promo text', 'codesblock' ),
			'default'           => 'Flash sale: Get 60% off AI interview prep this week.',
			'type'              => 'text',
			'sanitize_callback' => 'sanitize_text_field',
		),
		'codesblock_promo_cta_text'  => array(
			'label'             => __( 'Promo button text', 'codesblock' ),
			'default'           => 'Claim offer',
			'type'              => 'text',
			'sanitize_callback' => 'sanitize_text_field',
		),
		'codesblock_promo_cta_url'   => array(
			'label'             => __( 'Promo button URL', 'codesblock' ),
			'default'           => '#courses',
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
				'section' => 'codesblock_homepage',
				'type'    => $field['type'],
			)
		);
	}
}
add_action( 'customize_register', 'codesblock_customize_register' );

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
			array( 'title' => 'Articles', 'url' => home_url( '/articles/' ) ),
			array( 'title' => 'Courses', 'url' => home_url( '/#courses' ) ),
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
