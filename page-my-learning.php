<?php
/**
 * Template Name: My Learning
 *
 * A dedicated learner dashboard for progress, profile, and membership details.
 *
 * @package CodesBlock
 */

get_header();

$is_admin_session = function_exists( 'cbcommerce_user_can_access_admin' )
	? cbcommerce_user_can_access_admin()
	: current_user_can( 'manage_options' );
$is_member        = function_exists( 'cbcommerce_is_frontend_member' )
	? cbcommerce_is_frontend_member()
	: ( is_user_logged_in() && ! $is_admin_session );
$course_archive   = get_post_type_archive_link( 'course' ) ?: home_url( '/courses/' );

$member_user        = null;
$member_courses     = array();
$member_progress    = array();
$member_average     = 0;
$member_completed   = 0;
$membership_level   = false;
$membership_name    = __( 'Starter', 'codesblock' );
$membership_detail  = __( 'Free member access', 'codesblock' );
$profile_edit_url   = function_exists( 'cbcommerce_profile_edit_url' ) ? cbcommerce_profile_edit_url() : '';

if ( $is_member ) {
	$member_user = wp_get_current_user();
	if ( function_exists( 'pmpro_getMembershipLevelForUser' ) ) {
		$membership_level = pmpro_getMembershipLevelForUser( $member_user->ID );
		if ( $membership_level && ! empty( $membership_level->name ) ) {
			$membership_name = $membership_level->name;
		}
		if ( $membership_level && ! empty( $membership_level->enddate ) ) {
			$membership_detail = sprintf(
				/* translators: %s: membership expiry date. */
				__( 'Access active through %s', 'codesblock' ),
				wp_date( get_option( 'date_format' ), (int) $membership_level->enddate )
			);
		} elseif ( $membership_level ) {
			$membership_detail = __( 'Active with no scheduled expiry', 'codesblock' );
		}
	}

	if ( function_exists( 'cbcommerce_get_user_course_progress' ) ) {
		$member_progress = cbcommerce_get_user_course_progress( $member_user->ID );
	}

	if ( $member_progress ) {
		$progress_query = new WP_Query(
			array(
				'post_type'      => 'course',
				'post_status'    => 'publish',
				'posts_per_page' => 12,
				'post__in'       => array_map( 'absint', array_keys( $member_progress ) ),
				'orderby'        => 'post__in',
			)
		);
		$member_courses = $progress_query->posts;
		wp_reset_postdata();

		$progress_total = 0;
		foreach ( $member_courses as $member_course ) {
			$progress_record = $member_progress[ $member_course->ID ] ?? array();
			$percent         = is_array( $progress_record ) ? absint( $progress_record['percent'] ?? 0 ) : absint( $progress_record );
			$progress_total += $percent;
			if ( 100 === $percent ) {
				$member_completed++;
			}
		}
		if ( $member_courses ) {
			$member_average = (int) round( $progress_total / count( $member_courses ) );
		}
	}
}
?>

<main id="main" class="cb-account-page">
	<?php if ( $is_member && $member_user ) : ?>
		<section class="cb-account-hero" aria-labelledby="cb-account-title">
			<div class="container cb-account-hero-inner">
				<div class="cb-account-identity">
					<div class="cb-account-avatar-wrap">
						<?php echo get_avatar( $member_user->ID, 96, '', '', array( 'class' => 'cb-account-avatar' ) ); ?>
						<span class="cb-account-online" aria-label="<?php esc_attr_e( 'Account active', 'codesblock' ); ?>"></span>
					</div>
					<div>
						<p class="cb-account-kicker"><?php esc_html_e( 'Your CodesBlock workspace', 'codesblock' ); ?></p>
						<h1 id="cb-account-title"><?php printf( esc_html__( 'Welcome back, %s.', 'codesblock' ), esc_html( $member_user->display_name ) ); ?></h1>
						<p><?php esc_html_e( 'Pick up your next lesson, check access, and keep your learning identity in one calm place.', 'codesblock' ); ?></p>
					</div>
				</div>
				<div class="cb-account-hero-actions">
					<span class="cb-account-plan"><i aria-hidden="true"></i><?php echo esc_html( $membership_name ); ?></span>
					<a class="button button-secondary" href="<?php echo esc_url( $course_archive ); ?>"><?php esc_html_e( 'Explore courses', 'codesblock' ); ?> <span aria-hidden="true">&rarr;</span></a>
				</div>
			</div>
		</section>

		<div class="container cb-account-shell">
			<nav class="cb-account-nav" aria-label="<?php esc_attr_e( 'Learning account sections', 'codesblock' ); ?>">
				<a href="#overview" class="is-current"><span aria-hidden="true">&#9672;</span><?php esc_html_e( 'Overview', 'codesblock' ); ?></a>
				<a href="#learning"><span aria-hidden="true">&#9654;</span><?php esc_html_e( 'My learning', 'codesblock' ); ?></a>
				<a href="#profile"><span aria-hidden="true">&#9786;</span><?php esc_html_e( 'Profile', 'codesblock' ); ?></a>
				<a href="#membership"><span aria-hidden="true">&#9670;</span><?php esc_html_e( 'Membership', 'codesblock' ); ?></a>
			</nav>

			<div class="cb-account-content">
				<section class="cb-account-overview" id="overview" aria-labelledby="cb-overview-title">
					<div class="cb-account-section-heading">
						<div><span><?php esc_html_e( 'At a glance', 'codesblock' ); ?></span><h2 id="cb-overview-title"><?php esc_html_e( 'Your learning momentum', 'codesblock' ); ?></h2></div>
						<p><?php esc_html_e( 'Small, consistent steps beat passive course collecting.', 'codesblock' ); ?></p>
					</div>
					<div class="cb-account-stats">
						<article><span class="cb-stat-icon cb-stat-blue" aria-hidden="true">&#9654;</span><div><strong><?php echo esc_html( count( $member_courses ) ); ?></strong><small><?php esc_html_e( 'Active paths', 'codesblock' ); ?></small></div></article>
						<article><span class="cb-stat-icon cb-stat-green" aria-hidden="true">&#10003;</span><div><strong><?php echo esc_html( $member_completed ); ?></strong><small><?php esc_html_e( 'Completed', 'codesblock' ); ?></small></div></article>
						<article><span class="cb-stat-icon cb-stat-violet" aria-hidden="true">%</span><div><strong><?php echo esc_html( $member_average ); ?>%</strong><small><?php esc_html_e( 'Average progress', 'codesblock' ); ?></small></div></article>
					</div>
				</section>

				<section class="cb-account-card cb-learning-card" id="learning" aria-labelledby="cb-learning-title">
					<div class="cb-account-card-head">
						<div><span><?php esc_html_e( 'Continue learning', 'codesblock' ); ?></span><h2 id="cb-learning-title"><?php esc_html_e( 'Your courses', 'codesblock' ); ?></h2></div>
						<a href="<?php echo esc_url( $course_archive ); ?>"><?php esc_html_e( 'Browse all', 'codesblock' ); ?> <span aria-hidden="true">&rarr;</span></a>
					</div>

					<?php if ( $member_courses ) : ?>
						<div class="cb-dashboard-course-list">
							<?php foreach ( $member_courses as $index => $member_course ) : ?>
								<?php
								$progress_record = $member_progress[ $member_course->ID ] ?? array();
								$percent         = is_array( $progress_record ) ? absint( $progress_record['percent'] ?? 0 ) : absint( $progress_record );
								$first_lesson    = function_exists( 'cbcore_get_first_course_lesson' ) ? cbcore_get_first_course_lesson( $member_course->ID ) : null;
								$continue_url    = $first_lesson ? get_permalink( $first_lesson ) : get_permalink( $member_course );
								$course_level    = get_post_meta( $member_course->ID, '_course_level', true );
								$course_duration = get_post_meta( $member_course->ID, '_course_duration', true );
								?>
								<article class="cb-dashboard-course<?php echo 0 === $index ? ' is-featured' : ''; ?>">
									<a class="cb-dashboard-course-cover" href="<?php echo esc_url( $continue_url ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Continue %s', 'codesblock' ), get_the_title( $member_course ) ) ); ?>">
										<?php if ( has_post_thumbnail( $member_course ) ) : ?>
											<?php echo get_the_post_thumbnail( $member_course, 'medium_large' ); ?>
										<?php else : ?>
											<span>CB</span><small><?php esc_html_e( 'Learning path', 'codesblock' ); ?></small>
										<?php endif; ?>
									</a>
									<div class="cb-dashboard-course-body">
										<div class="cb-dashboard-course-meta"><span><?php echo esc_html( $course_level ?: __( 'Self-paced', 'codesblock' ) ); ?></span><?php if ( $course_duration ) : ?><span><?php echo esc_html( $course_duration ); ?></span><?php endif; ?></div>
										<h3><a href="<?php echo esc_url( $continue_url ); ?>"><?php echo esc_html( get_the_title( $member_course ) ); ?></a></h3>
										<div class="cb-dashboard-progress-label"><span><?php echo 100 === $percent ? esc_html__( 'Course complete', 'codesblock' ) : esc_html__( 'Course progress', 'codesblock' ); ?></span><strong><?php echo esc_html( $percent ); ?>%</strong></div>
										<div class="cb-dashboard-progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?php echo esc_attr( $percent ); ?>"><span style="width:<?php echo esc_attr( $percent ); ?>%"></span></div>
										<a class="cb-dashboard-continue" href="<?php echo esc_url( $continue_url ); ?>"><?php echo 100 === $percent ? esc_html__( 'Review course', 'codesblock' ) : esc_html__( 'Continue learning', 'codesblock' ); ?> <span aria-hidden="true">&rarr;</span></a>
									</div>
								</article>
							<?php endforeach; ?>
						</div>
					<?php else : ?>
						<div class="cb-dashboard-empty">
							<div class="cb-dashboard-empty-mark" aria-hidden="true"><span>01</span><i></i><b>&rarr;</b></div>
							<div><h3><?php esc_html_e( 'Choose your first learning path.', 'codesblock' ); ?></h3><p><?php esc_html_e( 'Open a course and start its first lesson. It will appear here automatically with saved progress.', 'codesblock' ); ?></p></div>
							<a class="button button-primary" href="<?php echo esc_url( $course_archive ); ?>"><?php esc_html_e( 'Find a course', 'codesblock' ); ?></a>
						</div>
					<?php endif; ?>
				</section>

				<div class="cb-account-detail-grid">
					<section class="cb-account-card cb-profile-detail" id="profile" aria-labelledby="cb-profile-title">
						<div class="cb-account-card-head"><div><span><?php esc_html_e( 'Personal details', 'codesblock' ); ?></span><h2 id="cb-profile-title"><?php esc_html_e( 'Profile', 'codesblock' ); ?></h2></div><span class="cb-card-status"><?php esc_html_e( 'Private', 'codesblock' ); ?></span></div>
						<dl class="cb-account-details">
							<div><dt><?php esc_html_e( 'Name', 'codesblock' ); ?></dt><dd><?php echo esc_html( $member_user->display_name ); ?></dd></div>
							<div><dt><?php esc_html_e( 'Email', 'codesblock' ); ?></dt><dd><?php echo esc_html( $member_user->user_email ); ?></dd></div>
							<div><dt><?php esc_html_e( 'Member since', 'codesblock' ); ?></dt><dd><?php echo esc_html( wp_date( 'F Y', strtotime( $member_user->user_registered ) ) ); ?></dd></div>
						</dl>
						<?php if ( $profile_edit_url ) : ?><a class="cb-account-text-link" href="<?php echo esc_url( $profile_edit_url ); ?>"><?php esc_html_e( 'Edit profile details', 'codesblock' ); ?> <span aria-hidden="true">&rarr;</span></a><?php endif; ?>
					</section>

					<section class="cb-account-card cb-membership-detail" id="membership" aria-labelledby="cb-membership-title">
						<div class="cb-account-card-head"><div><span><?php esc_html_e( 'Access & billing', 'codesblock' ); ?></span><h2 id="cb-membership-title"><?php esc_html_e( 'Membership', 'codesblock' ); ?></h2></div><span class="cb-card-status is-active"><?php esc_html_e( 'Active', 'codesblock' ); ?></span></div>
						<div class="cb-membership-plan"><span class="cb-membership-gem" aria-hidden="true">&#9670;</span><div><strong><?php echo esc_html( $membership_name ); ?></strong><small><?php echo esc_html( $membership_detail ); ?></small></div></div>
						<p><?php esc_html_e( 'Your course access and billing controls stay together here. CodesBlock never stores your payment details.', 'codesblock' ); ?></p>
						<a class="cb-account-text-link" href="<?php echo esc_url( $course_archive ); ?>"><?php esc_html_e( 'Browse courses for your membership', 'codesblock' ); ?> <span aria-hidden="true">&rarr;</span></a>
					</section>
				</div>

				<div class="cb-account-footer-actions">
					<p><strong><?php esc_html_e( 'Need a clean slate?', 'codesblock' ); ?></strong> <?php esc_html_e( 'You can sign out safely and return whenever you are ready.', 'codesblock' ); ?></p>
					<a href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>"><?php esc_html_e( 'Sign out', 'codesblock' ); ?></a>
				</div>
			</div>
		</div>
	<?php else : ?>
		<section class="cb-account-gate" aria-labelledby="cb-account-gate-title">
			<div class="container cb-account-gate-grid">
				<div class="cb-account-gate-story">
					<span class="cb-account-gate-brand">CB</span>
					<p class="cb-account-kicker"><?php esc_html_e( 'A calmer way to keep learning', 'codesblock' ); ?></p>
					<h1 id="cb-account-gate-title"><?php esc_html_e( 'Your learning belongs on its own page.', 'codesblock' ); ?></h1>
					<p><?php esc_html_e( 'Sign in to continue courses, see progress, and manage your profile and membership without crowding the homepage.', 'codesblock' ); ?></p>
					<div class="cb-account-gate-preview" aria-hidden="true"><span><i></i><i></i><i></i></span><strong>64%</strong><small><?php esc_html_e( 'Learning momentum', 'codesblock' ); ?></small></div>
				</div>
				<div class="cb-account-gate-card">
					<span class="cb-account-gate-icon" aria-hidden="true">&#9654;</span>
					<h2><?php esc_html_e( 'Welcome back.', 'codesblock' ); ?></h2>
					<p><?php esc_html_e( 'Use your CodesBlock account to open your private learning workspace.', 'codesblock' ); ?></p>
					<ul><li><?php esc_html_e( 'Continue directly into lessons', 'codesblock' ); ?></li><li><?php esc_html_e( 'See every saved course', 'codesblock' ); ?></li><li><?php esc_html_e( 'Manage profile and membership', 'codesblock' ); ?></li></ul>
					<?php if ( $is_admin_session ) : ?>
						<a class="button button-primary" href="<?php echo esc_url( admin_url() ); ?>"><?php esc_html_e( 'Return to WP Admin', 'codesblock' ); ?> <span aria-hidden="true">&rarr;</span></a>
					<?php else : ?>
						<button class="button button-primary js-open-member" type="button" data-member-view="signin" aria-haspopup="dialog" aria-controls="member-overlay"><?php esc_html_e( 'Sign in to my learning', 'codesblock' ); ?> <span aria-hidden="true">&rarr;</span></button>
					<?php endif; ?>
					<a class="cb-account-gate-browse" href="<?php echo esc_url( $course_archive ); ?>"><?php esc_html_e( 'Browse courses first', 'codesblock' ); ?></a>
				</div>
			</div>
		</section>
	<?php endif; ?>
</main>

<?php get_footer(); ?>
