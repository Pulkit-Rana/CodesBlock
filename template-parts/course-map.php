<?php
/**
 * Shared collapsible course outline for course and lesson pages.
 *
 * @package CodesBlock
 */

$course_id = isset( $args['course_id'] ) ? absint( $args['course_id'] ) : 0;
$lesson_id = isset( $args['lesson_id'] ) ? absint( $args['lesson_id'] ) : 0;

if ( ! $course_id || ! function_exists( 'codesblock_get_course_outline' ) ) {
	return;
}

$outline           = codesblock_get_course_outline( $course_id );
$published_lessons = function_exists( 'cbcore_get_course_lessons' ) ? cbcore_get_course_lessons( $course_id ) : array();

if ( empty( $outline ) ) {
	return;
}

$lesson_lookup = array();
foreach ( $published_lessons as $published_lesson ) {
	$module_index   = absint( get_post_meta( $published_lesson->ID, '_cbcore_lesson_module', true ) );
	$lesson_position = max( 1, absint( get_post_meta( $published_lesson->ID, '_cbcore_lesson_position', true ) ) );
	if ( ! isset( $lesson_lookup[ $module_index ][ $lesson_position ] ) ) {
		$lesson_lookup[ $module_index ][ $lesson_position ] = $published_lesson;
	}
}

$planned_total  = 0;
$current_step   = 0;
$running_step   = 0;
$active_module  = $lesson_id ? absint( get_post_meta( $lesson_id, '_cbcore_lesson_module', true ) ) : -1;
$active_position = $lesson_id ? max( 1, absint( get_post_meta( $lesson_id, '_cbcore_lesson_position', true ) ) ) : 0;

foreach ( $outline as $module_index => $module ) {
	$planned_total += count( $module['lessons'] );
	foreach ( $module['lessons'] as $lesson_index => $planned_lesson ) {
		$running_step++;
		if ( $lesson_id && $active_module === $module_index && $active_position === ( $lesson_index + 1 ) ) {
			$current_step = $running_step;
		}
	}
}

$is_editor       = current_user_can( 'edit_post', $course_id );
$is_member       = function_exists( 'cbcommerce_is_frontend_member' ) ? cbcommerce_is_frontend_member() : is_user_logged_in();
$has_access      = function_exists( 'codesblock_user_can_view_protected_content' ) ? codesblock_user_can_view_protected_content( $course_id ) : true;
$can_open_lessons = $is_editor || ( $is_member && $has_access );
$overview_active = ! $lesson_id;
$map_panel_id    = 'cb-course-map-panel-' . $course_id;
?>
<div
	class="cb-course-map<?php echo $lesson_id ? ' is-lesson-context' : ' is-overview-context'; ?>"
	data-course-map
	data-course-id="<?php echo esc_attr( $course_id ); ?>"
	data-default-open="<?php echo $lesson_id ? 'true' : 'false'; ?>"
>
	<button
		class="cb-course-map__toggle"
		type="button"
		aria-expanded="false"
		aria-controls="<?php echo esc_attr( $map_panel_id ); ?>"
		data-course-map-toggle
	>
		<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 4v16M5 7h5m-5 5h9m-9 5h12M10 5v4m4 1v4m3 1v4"/></svg>
		<span><?php esc_html_e( 'Course map', 'codesblock' ); ?></span>
	</button>

	<div class="cb-course-map__scrim" data-course-map-close aria-hidden="true"></div>

	<aside
		class="cb-course-map__panel"
		id="<?php echo esc_attr( $map_panel_id ); ?>"
		aria-label="<?php esc_attr_e( 'Course map', 'codesblock' ); ?>"
		aria-hidden="true"
		inert
	>
		<header class="cb-course-map__header">
			<div>
				<p><?php esc_html_e( 'Learning path', 'codesblock' ); ?></p>
				<strong><?php echo esc_html( get_the_title( $course_id ) ); ?></strong>
			</div>
			<button class="cb-course-map__close" type="button" data-course-map-close aria-label="<?php esc_attr_e( 'Close course map', 'codesblock' ); ?>">&times;</button>
		</header>

		<div class="cb-course-map__status" aria-live="polite">
			<span aria-hidden="true"></span>
			<?php if ( $current_step ) : ?>
				<?php
				printf(
					/* translators: 1: current lesson number, 2: total planned lessons */
					esc_html__( 'Roadmap step %1$d of %2$d', 'codesblock' ),
					absint( $current_step ),
					absint( $planned_total )
				);
				?>
			<?php else : ?>
				<?php
				printf(
					/* translators: 1: module count, 2: lesson count */
					esc_html__( '%1$d modules · %2$d planned lessons', 'codesblock' ),
					count( $outline ),
					absint( $planned_total )
				);
				?>
			<?php endif; ?>
		</div>

		<nav class="cb-course-map__nav" aria-label="<?php esc_attr_e( 'Course lessons', 'codesblock' ); ?>">
			<a class="cb-course-map__overview<?php echo $overview_active ? ' is-current' : ''; ?>" href="<?php echo esc_url( get_permalink( $course_id ) ); ?>"<?php echo $overview_active ? ' aria-current="page"' : ''; ?>>
				<span aria-hidden="true">⌂</span>
				<strong><?php esc_html_e( 'Course overview', 'codesblock' ); ?></strong>
			</a>

			<?php foreach ( $outline as $module_index => $module ) : ?>
				<?php
				$is_active_module = $lesson_id && $active_module === $module_index;
				$is_open_module   = $is_active_module || ( ! $lesson_id && 0 === $module_index );
				$module_panel_id  = 'cb-course-map-module-' . $course_id . '-' . $module_index;
				?>
				<section class="cb-course-map__module<?php echo $is_active_module ? ' is-active' : ''; ?>" data-course-map-module>
					<button
						class="cb-course-map__module-toggle"
						type="button"
						aria-expanded="<?php echo $is_open_module ? 'true' : 'false'; ?>"
						aria-controls="<?php echo esc_attr( $module_panel_id ); ?>"
						data-course-map-module-toggle
					>
						<span class="cb-course-map__module-number"><?php echo esc_html( str_pad( (string) $module_index, 2, '0', STR_PAD_LEFT ) ); ?></span>
						<span class="cb-course-map__module-title"><?php echo esc_html( $module['title'] ); ?></span>
						<svg viewBox="0 0 20 20" aria-hidden="true"><path d="m6 8 4 4 4-4"/></svg>
					</button>

					<ol id="<?php echo esc_attr( $module_panel_id ); ?>" class="cb-course-map__lessons"<?php echo $is_open_module ? '' : ' hidden'; ?>>
						<?php foreach ( $module['lessons'] as $lesson_index => $planned_lesson ) : ?>
							<?php
							$position       = $lesson_index + 1;
							$lesson_post    = isset( $lesson_lookup[ $module_index ][ $position ] ) ? $lesson_lookup[ $module_index ][ $position ] : null;
							$is_current      = $lesson_post && $lesson_id === $lesson_post->ID;
							$lesson_classes  = 'cb-course-map__lesson';
							$lesson_classes .= $is_current ? ' is-current' : '';
							$lesson_classes .= $lesson_post ? ' is-published' : ' is-planned';
							?>
							<li class="<?php echo esc_attr( $lesson_classes ); ?>">
								<?php if ( $lesson_post && $can_open_lessons ) : ?>
									<a href="<?php echo esc_url( get_permalink( $lesson_post ) ); ?>"<?php echo $is_current ? ' aria-current="page"' : ''; ?>>
										<span><?php echo esc_html( $position ); ?></span>
										<strong><?php echo esc_html( $planned_lesson ); ?></strong>
										<?php if ( $is_current ) : ?><small><?php esc_html_e( 'You are here', 'codesblock' ); ?></small><?php endif; ?>
									</a>
								<?php else : ?>
									<div>
										<span><?php echo esc_html( $position ); ?></span>
										<strong><?php echo esc_html( $planned_lesson ); ?></strong>
										<small><?php echo $lesson_post ? esc_html__( 'Sign in to open', 'codesblock' ) : esc_html__( 'Planned', 'codesblock' ); ?></small>
									</div>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ol>
				</section>
			<?php endforeach; ?>
		</nav>

		<footer class="cb-course-map__footer">
			<?php if ( $lesson_id ) : ?>
				<a href="<?php echo esc_url( get_permalink( $course_id ) ); ?>"><?php esc_html_e( 'Course overview', 'codesblock' ); ?> <span aria-hidden="true">&rarr;</span></a>
			<?php elseif ( $can_open_lessons && ! empty( $published_lessons ) ) : ?>
				<a href="<?php echo esc_url( get_permalink( $published_lessons[0] ) ); ?>"><?php esc_html_e( 'Open first lesson', 'codesblock' ); ?> <span aria-hidden="true">&rarr;</span></a>
			<?php elseif ( ! $can_open_lessons ) : ?>
				<button class="js-open-member" type="button" data-member-view="signin"><?php esc_html_e( 'Sign in to start learning', 'codesblock' ); ?></button>
			<?php endif; ?>
		</footer>
	</aside>
</div>
