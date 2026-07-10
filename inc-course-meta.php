<?php
/**
 * Custom meta boxes for Courses.
 *
 * @package CodesBlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function codesblock_add_course_meta_boxes() {
	add_meta_box(
		'codesblock_course_details',
		__( 'Course Details (Pricing, Level, etc.)', 'codesblock' ),
		'codesblock_render_course_meta_box',
		'course',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'codesblock_add_course_meta_boxes' );

function codesblock_render_course_meta_box( $post ) {
	wp_nonce_field( 'codesblock_save_course_meta', 'codesblock_course_meta_nonce' );

	$price         = get_post_meta( $post->ID, '_course_price', true );
	$original_price = get_post_meta( $post->ID, '_course_original_price', true );
	$level         = get_post_meta( $post->ID, '_course_level', true );
	$duration      = get_post_meta( $post->ID, '_course_duration', true );
	$badge         = get_post_meta( $post->ID, '_course_badge', true );
	$features      = get_post_meta( $post->ID, '_course_features', true );
	$what_you_learn = get_post_meta( $post->ID, '_course_what_you_learn', true );
	$syllabus      = get_post_meta( $post->ID, '_course_syllabus', true );
	?>
	<div style="display: flex; flex-direction: column; gap: 15px; margin-top: 15px;">
		<div>
			<label for="course_price"><strong><?php esc_html_e( 'Current Price (e.g. $49 or Free)', 'codesblock' ); ?></strong></label><br>
			<input type="text" id="course_price" name="course_price" value="<?php echo esc_attr( $price ); ?>" style="width: 100%; max-width: 400px;">
		</div>
		<div>
			<label for="course_original_price"><strong><?php esc_html_e( 'Original Price (for strikethrough, e.g. $99)', 'codesblock' ); ?></strong></label><br>
			<input type="text" id="course_original_price" name="course_original_price" value="<?php echo esc_attr( $original_price ); ?>" style="width: 100%; max-width: 400px;">
		</div>
		<div>
			<label for="course_level"><strong><?php esc_html_e( 'Level (e.g. Beginner, Intermediate, Advanced)', 'codesblock' ); ?></strong></label><br>
			<input type="text" id="course_level" name="course_level" value="<?php echo esc_attr( $level ); ?>" style="width: 100%; max-width: 400px;">
		</div>
		<div>
			<label for="course_duration"><strong><?php esc_html_e( 'Duration (e.g. 12 hours, 4 weeks)', 'codesblock' ); ?></strong></label><br>
			<input type="text" id="course_duration" name="course_duration" value="<?php echo esc_attr( $duration ); ?>" style="width: 100%; max-width: 400px;">
		</div>
		<div>
			<label for="course_badge"><strong><?php esc_html_e( 'Badge Text (e.g. Bestseller, New, Popular)', 'codesblock' ); ?></strong></label><br>
			<input type="text" id="course_badge" name="course_badge" value="<?php echo esc_attr( $badge ); ?>" style="width: 100%; max-width: 400px;">
		</div>
		<div>
			<label for="course_features"><strong><?php esc_html_e( 'Course Features (Sidebar List - one per line)', 'codesblock' ); ?></strong></label><br>
			<textarea id="course_features" name="course_features" rows="4" style="width: 100%;"><?php echo esc_textarea( $features ); ?></textarea>
			<p class="description">e.g.<br>Lifetime Access<br>100+ Lessons<br>Certificate of completion</p>
		</div>
		<div>
			<label for="course_what_you_learn"><strong><?php esc_html_e( 'What You Will Learn (Checklist - one per line)', 'codesblock' ); ?></strong></label><br>
			<textarea id="course_what_you_learn" name="course_what_you_learn" rows="6" style="width: 100%;"><?php echo esc_textarea( $what_you_learn ); ?></textarea>
		</div>
		<div>
			<label for="course_syllabus"><strong><?php esc_html_e( 'Course Syllabus / Curriculum', 'codesblock' ); ?></strong></label><br>
			<p class="description">Format: Use `## Section Title` for sections, and `- Lesson name` for lessons.</p>
			<textarea id="course_syllabus" name="course_syllabus" rows="10" style="width: 100%; font-family: monospace;"><?php echo esc_textarea( $syllabus ); ?></textarea>
		</div>
		<div>
			<label for="course_ai_summary"><strong><?php esc_html_e( 'AI Tutor Summary (shown in the AI panel)', 'codesblock' ); ?></strong></label><br>
			<p class="description"><?php esc_html_e( 'A 2-3 sentence summary the AI Tutor reads when users ask "what is this course about?". If left blank, the post excerpt is used.', 'codesblock' ); ?></p>
			<textarea id="course_ai_summary" name="course_ai_summary" rows="4" style="width: 100%;"><?php echo esc_textarea( get_post_meta( $post->ID, '_course_ai_summary', true ) ); ?></textarea>
		</div>
		<div>
			<label for="course_ai_faqs"><strong><?php esc_html_e( 'AI Tutor FAQs (JSON, for smart answers)', 'codesblock' ); ?></strong></label><br>
			<p class="description"><?php esc_html_e( 'Optional. Enter as JSON array: [{"q":"Your question","a":"Your answer"}, ...]. The AI uses these to answer relevant user questions.', 'codesblock' ); ?></p>
			<textarea id="course_ai_faqs" name="course_ai_faqs" rows="6" style="width: 100%; font-family: monospace;"><?php echo esc_textarea( get_post_meta( $post->ID, '_course_ai_faqs', true ) ); ?></textarea>
		</div>
	</div>
	<?php
}

function codesblock_save_course_meta( $post_id ) {
	if ( ! isset( $_POST['codesblock_course_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['codesblock_course_meta_nonce'] ) ), 'codesblock_save_course_meta' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$fields = array(
		'course_price',
		'course_original_price',
		'course_level',
		'course_duration',
		'course_badge',
		'course_features',
		'course_what_you_learn',
		'course_syllabus',
		'course_ai_summary',
		'course_ai_faqs',
	);

	foreach ( $fields as $field ) {
		if ( isset( $_POST[ $field ] ) ) {
			if ( in_array( $field, array( 'course_features', 'course_what_you_learn', 'course_syllabus', 'course_ai_summary', 'course_ai_faqs' ), true ) ) {
				update_post_meta( $post_id, '_' . $field, sanitize_textarea_field( wp_unslash( $_POST[ $field ] ) ) );
			} else {
				update_post_meta( $post_id, '_' . $field, sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
			}
		}
	}
}
add_action( 'save_post_course', 'codesblock_save_course_meta' );
