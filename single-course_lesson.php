<?php
/**
 * Individual Course Lesson page.
 *
 * @package CodesBlock
 */

$lesson_id = get_queried_object_id();
$course_id = function_exists( 'cbcore_get_lesson_course_id' ) ? cbcore_get_lesson_course_id( $lesson_id ) : 0;

if ( ! $course_id || 'course' !== get_post_type( $course_id ) ) {
	wp_safe_redirect( get_post_type_archive_link( 'course' ) ?: home_url( '/courses/' ) );
	exit;
}

$can_view  = function_exists( 'codesblock_user_can_view_protected_content' ) && codesblock_user_can_view_protected_content( $lesson_id );
$is_member = function_exists( 'cbcommerce_is_frontend_member' ) && cbcommerce_is_frontend_member();
$is_editor = current_user_can( 'edit_post', $lesson_id );
if ( ( ! $is_member && ! $is_editor ) || ! $can_view ) {
	wp_safe_redirect( get_permalink( $course_id ) );
	exit;
}

$lessons  = function_exists( 'cbcore_get_course_lessons' ) ? cbcore_get_course_lessons( $course_id ) : array();
$index    = array_search( $lesson_id, wp_list_pluck( $lessons, 'ID' ), true );
$previous = false !== $index && isset( $lessons[ $index - 1 ] ) ? $lessons[ $index - 1 ] : null;
$next     = false !== $index && isset( $lessons[ $index + 1 ] ) ? $lessons[ $index + 1 ] : null;

$module_index   = absint( get_post_meta( $lesson_id, '_cbcore_lesson_module', true ) );
$lesson_position = max( 1, absint( get_post_meta( $lesson_id, '_cbcore_lesson_position', true ) ) );
$outline         = function_exists( 'codesblock_get_course_outline' ) ? codesblock_get_course_outline( $course_id ) : array();
$module_title    = isset( $outline[ $module_index ]['title'] ) ? $outline[ $module_index ]['title'] : sprintf( __( 'Module %d', 'codesblock' ), $module_index );
$planned_total   = 0;
$current_step    = 0;
$running_step    = 0;

foreach ( $outline as $outline_module_index => $outline_module ) {
	$planned_total += count( $outline_module['lessons'] );
	foreach ( $outline_module['lessons'] as $outline_lesson_index => $outline_lesson ) {
		$running_step++;
		if ( $outline_module_index === $module_index && ( $outline_lesson_index + 1 ) === $lesson_position ) {
			$current_step = $running_step;
		}
	}
}

$progress_percent = $planned_total && $current_step ? max( 2, round( ( $current_step / $planned_total ) * 100, 2 ) ) : 2;

get_header();
get_template_part(
	'template-parts/course-map',
	null,
	array(
		'course_id' => $course_id,
		'lesson_id' => $lesson_id,
	)
);
?>
<main id="main" class="cb-lesson-page">
	<section class="cb-lesson-hero">
		<div class="container">
			<nav class="cb-lesson-breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'codesblock' ); ?>">
				<a href="<?php echo esc_url( get_permalink( $course_id ) ); ?>"><?php echo esc_html( get_the_title( $course_id ) ); ?></a>
				<span aria-hidden="true">/</span>
				<span><?php echo esc_html( $module_title ); ?></span>
			</nav>
			<p class="cb-lesson-kicker">
				<?php
				printf(
					/* translators: 1: module number, 2: lesson number */
					esc_html__( 'Module %1$d · Lesson %2$d', 'codesblock' ),
					absint( $module_index ),
					absint( $lesson_position )
				);
				?>
			</p>
			<h1><?php the_title(); ?></h1>
			<p class="cb-lesson-intro"><?php echo esc_html( get_the_excerpt() ?: __( 'A guided course lesson with a practical exercise.', 'codesblock' ) ); ?></p>
			<?php if ( $planned_total && $current_step ) : ?>
				<div class="cb-lesson-progress" style="--cb-lesson-progress: <?php echo esc_attr( $progress_percent ); ?>%;">
					<span aria-hidden="true"></span>
					<small>
						<?php
						printf(
							/* translators: 1: current lesson number, 2: total planned lessons */
							esc_html__( 'Step %1$d of %2$d in the course roadmap', 'codesblock' ),
							absint( $current_step ),
							absint( $planned_total )
						);
						?>
					</small>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<div class="cb-lesson-shell">
		<article class="cb-lesson-article">
			<div class="cb-lesson-content entry-content">
				<?php the_content(); ?>
			</div>

			<nav class="cb-lesson-nav" aria-label="<?php esc_attr_e( 'Lesson navigation', 'codesblock' ); ?>">
				<?php if ( $previous ) : ?>
					<a href="<?php echo esc_url( get_permalink( $previous ) ); ?>">
						<small><?php esc_html_e( 'Previous lesson', 'codesblock' ); ?></small>
						<strong>&larr; <?php echo esc_html( get_the_title( $previous ) ); ?></strong>
					</a>
				<?php else : ?>
					<a href="<?php echo esc_url( get_permalink( $course_id ) ); ?>">
						<small><?php esc_html_e( 'Course home', 'codesblock' ); ?></small>
						<strong>&larr; <?php esc_html_e( 'Back to course overview', 'codesblock' ); ?></strong>
					</a>
				<?php endif; ?>

				<?php if ( $next ) : ?>
					<a href="<?php echo esc_url( get_permalink( $next ) ); ?>">
						<small><?php esc_html_e( 'Next lesson', 'codesblock' ); ?></small>
						<strong><?php echo esc_html( get_the_title( $next ) ); ?> &rarr;</strong>
					</a>
				<?php endif; ?>
			</nav>
		</article>
	</div>
</main>
<?php get_footer(); ?>
