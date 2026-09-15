<?php
/**
 * Theme-independent CodesBlock content types and editor fields.
 *
 * @package CodesBlockCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function cbcore_register_content_model() {
	register_post_type(
		'course',
		array(
			'labels'       => array(
				'name'               => __( 'Courses', 'codesblock-core' ),
				'singular_name'      => __( 'Course', 'codesblock-core' ),
				'add_new_item'       => __( 'Add New Course', 'codesblock-core' ),
				'edit_item'          => __( 'Edit Course', 'codesblock-core' ),
				'new_item'           => __( 'New Course', 'codesblock-core' ),
				'view_item'          => __( 'View Course', 'codesblock-core' ),
				'search_items'       => __( 'Search Courses', 'codesblock-core' ),
				'not_found'          => __( 'No courses found.', 'codesblock-core' ),
				'not_found_in_trash' => __( 'No courses found in Trash.', 'codesblock-core' ),
			),
			'public'       => true,
			'has_archive'  => true,
			'menu_icon'    => 'dashicons-welcome-learn-more',
			'menu_position'=> 6,
			'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'author', 'revisions' ),
			'show_in_rest' => true,
			'rewrite'      => array( 'slug' => 'courses', 'with_front' => false ),
		)
	);

	register_post_type(
		'course_lesson',
		array(
			'labels'       => array(
				'name'               => __( 'Course Lessons', 'codesblock-core' ),
				'singular_name'      => __( 'Course Lesson', 'codesblock-core' ),
				'add_new_item'       => __( 'Add New Lesson', 'codesblock-core' ),
				'edit_item'          => __( 'Edit Lesson', 'codesblock-core' ),
				'new_item'           => __( 'New Lesson', 'codesblock-core' ),
				'view_item'          => __( 'View Lesson', 'codesblock-core' ),
				'search_items'       => __( 'Search Lessons', 'codesblock-core' ),
				'not_found'          => __( 'No lessons found.', 'codesblock-core' ),
				'not_found_in_trash' => __( 'No lessons found in Trash.', 'codesblock-core' ),
			),
			'public'       => true,
			'show_in_menu' => 'edit.php?post_type=course',
			'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'author', 'revisions' ),
			'show_in_rest' => true,
			'rewrite'      => array( 'slug' => 'lessons', 'with_front' => false ),
		)
	);

	register_taxonomy(
		'course_topic',
		'course',
		array(
			'labels'            => array(
				'name'          => __( 'Course Topics', 'codesblock-core' ),
				'singular_name' => __( 'Course Topic', 'codesblock-core' ),
			),
			'public'            => true,
			'hierarchical'      => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'course-topic' ),
		)
	);

	$text_fields = array(
		'_course_price',
		'_course_original_price',
		'_course_level',
		'_course_duration',
		'_course_badge',
		'_course_features',
		'_course_what_you_learn',
		'_course_syllabus',
		'_course_ai_summary',
		'_course_ai_faqs',
	);

	foreach ( $text_fields as $meta_key ) {
		register_post_meta(
			'course',
			$meta_key,
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_textarea_field',
				'auth_callback'     => function() {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}

	foreach ( array( '_cbcore_lesson_course_id', '_cbcore_lesson_module', '_cbcore_lesson_position' ) as $meta_key ) {
		register_post_meta(
			'course_lesson',
			$meta_key,
			array(
				'type'              => 'integer',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'absint',
				'auth_callback'     => function() {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}

	foreach ( array( 'post', 'course' ) as $post_type ) {
		register_post_meta(
			$post_type,
			'_codesblock_recommended',
			array(
				'type'              => 'boolean',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'rest_sanitize_boolean',
				'auth_callback'     => function() {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}

	register_post_meta(
		'post',
		'_codesblock_premium',
		array(
			'type'              => 'boolean',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'rest_sanitize_boolean',
			'auth_callback'     => function() {
				return current_user_can( 'edit_posts' );
			},
		)
	);
}
add_action( 'init', 'cbcore_register_content_model' );

function cbcore_add_editor_boxes() {
	add_meta_box(
		'cbcore_course_details',
		__( 'Course setup', 'codesblock-core' ),
		'cbcore_render_course_box',
		'course',
		'normal',
		'high'
	);

	foreach ( array( 'post', 'course' ) as $screen ) {
		add_meta_box(
			'cbcore_content_priority',
			__( 'CodesBlock visibility', 'codesblock-core' ),
			'cbcore_render_priority_box',
			$screen,
			'side',
			'high'
		);
	}

	add_meta_box(
		'cbcore_lesson_setup',
		__( 'Lesson setup', 'codesblock-core' ),
		'cbcore_render_lesson_box',
		'course_lesson',
		'side',
		'high'
	);
}
add_action( 'add_meta_boxes', 'cbcore_add_editor_boxes' );

function cbcore_render_course_box( $post ) {
	wp_nonce_field( 'cbcore_save_course', 'cbcore_course_nonce' );
	$fields = array(
		'course_price'          => array( __( 'Current price', 'codesblock-core' ), __( 'Use Free or a display price such as $49.', 'codesblock-core' ), 'text' ),
		'course_original_price' => array( __( 'Original price', 'codesblock-core' ), __( 'Optional struck-through display price.', 'codesblock-core' ), 'text' ),
		'course_level'          => array( __( 'Level', 'codesblock-core' ), __( 'For example Beginner, Intermediate, or Advanced.', 'codesblock-core' ), 'text' ),
		'course_duration'       => array( __( 'Duration', 'codesblock-core' ), __( 'For example 12 hours or 4 weeks.', 'codesblock-core' ), 'text' ),
		'course_badge'          => array( __( 'Badge', 'codesblock-core' ), __( 'For example New, Popular, or Bestseller.', 'codesblock-core' ), 'text' ),
		'course_features'       => array( __( 'Course features', 'codesblock-core' ), __( 'One item per line.', 'codesblock-core' ), 'textarea' ),
		'course_what_you_learn' => array( __( 'What learners will achieve', 'codesblock-core' ), __( 'One outcome per line.', 'codesblock-core' ), 'textarea' ),
		'course_syllabus'       => array( __( 'Curriculum', 'codesblock-core' ), __( 'Use ## for section headings and - for lessons.', 'codesblock-core' ), 'textarea' ),
		'course_ai_summary'     => array( __( 'Interactive guide summary', 'codesblock-core' ), __( 'Optional. The excerpt is used when this is empty.', 'codesblock-core' ), 'textarea' ),
		'course_ai_faqs'        => array( __( 'Guide FAQs', 'codesblock-core' ), __( 'Optional JSON: [{"q":"Question","a":"Answer"}]', 'codesblock-core' ), 'textarea' ),
	);
	?>
	<div class="cbcore-fields">
		<?php foreach ( $fields as $name => $field ) : ?>
			<?php $value = get_post_meta( $post->ID, '_' . $name, true ); ?>
			<p>
				<label for="<?php echo esc_attr( $name ); ?>"><strong><?php echo esc_html( $field[0] ); ?></strong></label><br>
				<?php if ( 'textarea' === $field[2] ) : ?>
					<textarea class="widefat" rows="<?php echo 'course_syllabus' === $name ? '10' : '5'; ?>" id="<?php echo esc_attr( $name ); ?>" name="<?php echo esc_attr( $name ); ?>"><?php echo esc_textarea( $value ); ?></textarea>
				<?php else : ?>
					<input class="widefat" type="text" id="<?php echo esc_attr( $name ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>">
				<?php endif; ?>
				<span class="description"><?php echo esc_html( $field[1] ); ?></span>
			</p>
		<?php endforeach; ?>
	</div>
	<?php
}

function cbcore_render_priority_box( $post ) {
	wp_nonce_field( 'cbcore_save_priority', 'cbcore_priority_nonce' );
	?>
	<p><label><input type="checkbox" name="cbcore_recommended" value="1" <?php checked( (bool) get_post_meta( $post->ID, '_codesblock_recommended', true ) ); ?>> <?php esc_html_e( 'Feature this content', 'codesblock-core' ); ?></label></p>
	<p class="description"><?php esc_html_e( 'Featured items are shown before the latest content.', 'codesblock-core' ); ?></p>
	<?php if ( 'post' === $post->post_type ) : ?>
		<hr>
		<p><label><input type="checkbox" name="cbcore_premium" value="1" <?php checked( (bool) get_post_meta( $post->ID, '_codesblock_premium', true ) ); ?>> <?php esc_html_e( 'Members-only article', 'codesblock-core' ); ?></label></p>
		<p class="description"><?php esc_html_e( 'Visitors receive only the excerpt, never the full article HTML.', 'codesblock-core' ); ?></p>
	<?php endif; ?>
	<?php
}

function cbcore_render_lesson_box( $post ) {
	wp_nonce_field( 'cbcore_save_lesson', 'cbcore_lesson_nonce' );
	$course_id = absint( get_post_meta( $post->ID, '_cbcore_lesson_course_id', true ) );
	if ( ! $course_id && isset( $_GET['course_id'] ) ) {
		$course_id = absint( wp_unslash( $_GET['course_id'] ) );
	}
	$position = absint( get_post_meta( $post->ID, '_cbcore_lesson_position', true ) );
	$module   = absint( get_post_meta( $post->ID, '_cbcore_lesson_module', true ) );
	?>
	<p>
		<label for="cbcore_lesson_course_id"><strong><?php esc_html_e( 'Course', 'codesblock-core' ); ?></strong></label><br>
		<select class="widefat" id="cbcore_lesson_course_id" name="cbcore_lesson_course_id">
			<option value="0"><?php esc_html_e( 'Select a course', 'codesblock-core' ); ?></option>
			<?php foreach ( get_posts( array( 'post_type' => 'course', 'post_status' => array( 'publish', 'draft' ), 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC' ) ) as $course ) : ?>
				<option value="<?php echo esc_attr( $course->ID ); ?>" <?php selected( $course_id, $course->ID ); ?>><?php echo esc_html( $course->post_title ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<p>
		<label for="cbcore_lesson_module"><strong><?php esc_html_e( 'Module number', 'codesblock-core' ); ?></strong></label><br>
		<input class="small-text" type="number" min="0" step="1" id="cbcore_lesson_module" name="cbcore_lesson_module" value="<?php echo esc_attr( $module ); ?>">
		<span class="description"><?php esc_html_e( 'Use 0 for Module 0, 1 for Module 1, and so on.', 'codesblock-core' ); ?></span>
	</p>
	<p>
		<label for="cbcore_lesson_position"><strong><?php esc_html_e( 'Lesson order', 'codesblock-core' ); ?></strong></label><br>
		<input class="small-text" type="number" min="1" step="1" id="cbcore_lesson_position" name="cbcore_lesson_position" value="<?php echo esc_attr( $position ?: 1 ); ?>">
		<span class="description"><?php esc_html_e( 'Learners see the lowest number first.', 'codesblock-core' ); ?></span>
	</p>
	<?php
}

function cbcore_save_course_fields( $post_id ) {
	if ( ! isset( $_POST['cbcore_course_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['cbcore_course_nonce'] ) ), 'cbcore_save_course' ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$fields = array( 'course_price', 'course_original_price', 'course_level', 'course_duration', 'course_badge', 'course_features', 'course_what_you_learn', 'course_syllabus', 'course_ai_summary', 'course_ai_faqs' );
	foreach ( $fields as $field ) {
		if ( ! isset( $_POST[ $field ] ) ) {
			continue;
		}
		$value = sanitize_textarea_field( wp_unslash( $_POST[ $field ] ) );
		if ( '' === $value ) {
			delete_post_meta( $post_id, '_' . $field );
		} else {
			update_post_meta( $post_id, '_' . $field, $value );
		}
	}
}
add_action( 'save_post_course', 'cbcore_save_course_fields' );

function cbcore_save_lesson_fields( $post_id ) {
	if ( ! isset( $_POST['cbcore_lesson_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['cbcore_lesson_nonce'] ) ), 'cbcore_save_lesson' ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$course_id = isset( $_POST['cbcore_lesson_course_id'] ) ? absint( $_POST['cbcore_lesson_course_id'] ) : 0;
	$module    = isset( $_POST['cbcore_lesson_module'] ) ? absint( $_POST['cbcore_lesson_module'] ) : 0;
	$position  = isset( $_POST['cbcore_lesson_position'] ) ? max( 1, absint( $_POST['cbcore_lesson_position'] ) ) : 1;
	if ( $course_id && 'course' === get_post_type( $course_id ) ) {
		update_post_meta( $post_id, '_cbcore_lesson_course_id', $course_id );
		update_post_meta( $post_id, '_cbcore_lesson_module', $module );
		update_post_meta( $post_id, '_cbcore_lesson_position', $position );
	} else {
		delete_post_meta( $post_id, '_cbcore_lesson_course_id' );
		delete_post_meta( $post_id, '_cbcore_lesson_module' );
		delete_post_meta( $post_id, '_cbcore_lesson_position' );
	}
}
add_action( 'save_post_course_lesson', 'cbcore_save_lesson_fields' );

function cbcore_get_course_lessons( $course_id ) {
	$lessons = get_posts(
		array(
			'post_type'      => 'course_lesson',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'meta_key'       => '_cbcore_lesson_course_id',
			'meta_value'     => absint( $course_id ),
			'orderby'        => array( 'meta_value_num' => 'ASC', 'date' => 'ASC' ),
			'order'          => 'ASC',
		)
	);

	usort(
		$lessons,
		function( $left, $right ) {
			$left_module  = absint( get_post_meta( $left->ID, '_cbcore_lesson_module', true ) );
			$right_module = absint( get_post_meta( $right->ID, '_cbcore_lesson_module', true ) );
			if ( $left_module !== $right_module ) {
				return $left_module <=> $right_module;
			}

			$left_position  = max( 1, absint( get_post_meta( $left->ID, '_cbcore_lesson_position', true ) ) );
			$right_position = max( 1, absint( get_post_meta( $right->ID, '_cbcore_lesson_position', true ) ) );
			if ( $left_position !== $right_position ) {
				return $left_position <=> $right_position;
			}

			return $left->ID <=> $right->ID;
		}
	);

	return $lessons;
}

function cbcore_get_first_course_lesson( $course_id ) {
	$lessons = cbcore_get_course_lessons( $course_id );
	return ! empty( $lessons ) ? $lessons[0] : null;
}

function cbcore_get_lesson_course_id( $lesson_id ) {
	return absint( get_post_meta( $lesson_id, '_cbcore_lesson_course_id', true ) );
}

function cbcore_save_priority_fields( $post_id ) {
	if ( ! isset( $_POST['cbcore_priority_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['cbcore_priority_nonce'] ) ), 'cbcore_save_priority' ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	update_post_meta( $post_id, '_codesblock_recommended', isset( $_POST['cbcore_recommended'] ) ? '1' : '0' );
	if ( 'post' === get_post_type( $post_id ) ) {
		update_post_meta( $post_id, '_codesblock_premium', isset( $_POST['cbcore_premium'] ) ? '1' : '0' );
	}
}
add_action( 'save_post', 'cbcore_save_priority_fields' );
