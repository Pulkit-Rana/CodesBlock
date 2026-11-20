<?php
/**
 * Template Name: My Learning
 *
 * The signed-in home: continue learning, plan, newsletter and account in one place.
 *
 * @package CodesBlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$codesblock_courses_url = get_post_type_archive_link( 'course' ) ?: home_url( '/courses/' );
$codesblock_home_url    = function_exists( 'cbcommerce_member_home_url' ) ? cbcommerce_member_home_url() : home_url( '/my-learning/' );
?>

<main id="main" class="cba-page">
<?php if ( is_user_logged_in() ) : ?>
	<?php
	$codesblock_user     = wp_get_current_user();
	$codesblock_is_admin = function_exists( 'cbcommerce_user_can_access_admin' ) ? cbcommerce_user_can_access_admin() : current_user_can( 'manage_options' );
	$codesblock_paid     = function_exists( 'cbcommerce_user_has_paid_access' ) ? cbcommerce_user_has_paid_access() : $codesblock_is_admin;
	$codesblock_first    = $codesblock_user->first_name ? $codesblock_user->first_name : $codesblock_user->display_name;

	/* Courses in progress, most recent first. */
	$codesblock_progress = function_exists( 'cbcommerce_get_user_course_progress' ) ? cbcommerce_get_user_course_progress( $codesblock_user->ID ) : array();
	$codesblock_courses  = array();
	foreach ( $codesblock_progress as $codesblock_course_id => $codesblock_record ) {
		if ( 'publish' !== get_post_status( $codesblock_course_id ) ) {
			continue;
		}
		$codesblock_first_lesson = function_exists( 'cbcore_get_first_course_lesson' ) ? cbcore_get_first_course_lesson( $codesblock_course_id ) : null;
		$codesblock_courses[]    = array(
			'id'       => (int) $codesblock_course_id,
			'percent'  => (int) $codesblock_record['percent'],
			'updated'  => (int) $codesblock_record['updated_at'],
			'continue' => $codesblock_first_lesson ? get_permalink( $codesblock_first_lesson ) : get_permalink( $codesblock_course_id ),
		);
	}
	$codesblock_completed = count( array_filter( $codesblock_courses, function ( $course ) { return 100 === $course['percent']; } ) );
	$codesblock_average   = $codesblock_courses ? (int) round( array_sum( wp_list_pluck( $codesblock_courses, 'percent' ) ) / count( $codesblock_courses ) ) : 0;
	$codesblock_featured  = $codesblock_courses ? array_shift( $codesblock_courses ) : null;

	/* Suggestions: published courses the member has not started. */
	$codesblock_started     = array_map( 'absint', array_keys( $codesblock_progress ) );
	$codesblock_suggestions = get_posts(
		array(
			'post_type'      => 'course',
			'post_status'    => 'publish',
			'posts_per_page' => 3,
			'post__not_in'   => $codesblock_started,
			'no_found_rows'  => true,
		)
	);

	/* Plan. */
	$codesblock_level     = function_exists( 'pmpro_getMembershipLevelForUser' ) ? pmpro_getMembershipLevelForUser( $codesblock_user->ID ) : false;
	$codesblock_plan_name = $codesblock_is_admin ? __( 'Administrator', 'codesblock' ) : ( $codesblock_paid && $codesblock_level && ! empty( $codesblock_level->name ) ? $codesblock_level->name : __( 'Free account', 'codesblock' ) );
	$codesblock_end       = $codesblock_paid && $codesblock_level && ! empty( $codesblock_level->enddate ) ? (int) $codesblock_level->enddate : 0;
	$codesblock_start     = $codesblock_paid && $codesblock_level && ! empty( $codesblock_level->startdate ) ? (int) $codesblock_level->startdate : 0;
	$codesblock_days_left = $codesblock_end ? max( 0, (int) ceil( ( $codesblock_end - time() ) / DAY_IN_SECONDS ) ) : 0;
	$codesblock_remaining = ( $codesblock_end && $codesblock_start && $codesblock_end > $codesblock_start ) ? max( 0, min( 100, (int) round( 100 * ( $codesblock_end - time() ) / ( $codesblock_end - $codesblock_start ) ) ) ) : 0;
	$codesblock_orders    = function_exists( 'pmpro_url' ) ? pmpro_url( 'invoice' ) : '';
	$codesblock_edit_url  = function_exists( 'cbcommerce_profile_edit_url' ) ? cbcommerce_profile_edit_url() : '';

	/* Newsletter status (subscribed to the CodesBlock list in MailPoet). */
	$codesblock_news = 'none';
	if ( class_exists( 'MailPoet\\API\\API' ) ) {
		try {
			$codesblock_subscriber = MailPoet\API\API::MP( 'v1' )->getSubscriber( $codesblock_user->user_email );
			$codesblock_list_id    = function_exists( 'cbcommerce_mailpoet_list_id' ) ? cbcommerce_mailpoet_list_id() : 0;
			$codesblock_on_list    = false;
			foreach ( (array) ( $codesblock_subscriber['subscriptions'] ?? array() ) as $codesblock_subscription ) {
				if ( (int) ( $codesblock_subscription['segment_id'] ?? 0 ) === (int) $codesblock_list_id && 'subscribed' === ( $codesblock_subscription['status'] ?? '' ) ) {
					$codesblock_on_list = true;
				}
			}
			if ( $codesblock_on_list ) {
				$codesblock_news = 'subscribed' === ( $codesblock_subscriber['status'] ?? '' ) ? 'subscribed' : 'unconfirmed';
			}
		} catch ( Throwable $error ) {
			$codesblock_news = 'none';
		}
	}

	/* How this member signs in (WP Social records the provider used). */
	$codesblock_methods = array();
	$codesblock_social  = (string) get_user_meta( $codesblock_user->ID, 'xs_social_register_by', true );
	if ( '' !== $codesblock_social ) {
		$codesblock_methods[] = 'github' === strtolower( $codesblock_social ) ? 'GitHub' : ucfirst( strtolower( $codesblock_social ) );
	}
	if ( ! $codesblock_methods ) {
		$codesblock_methods[] = __( 'Email and password', 'codesblock' );
	}
	?>

	<section class="cba-hero" aria-labelledby="cba-title">
		<div class="container cba-hero-inner">
			<div class="cba-identity">
				<?php echo get_avatar( $codesblock_user->ID, 72, '', '', array( 'class' => 'cba-avatar' ) ); ?>
				<div>
					<p class="cba-kicker"><?php esc_html_e( 'My learning', 'codesblock' ); ?></p>
					<h1 id="cba-title"><?php echo esc_html( sprintf( __( 'Welcome back, %s', 'codesblock' ), $codesblock_first ) ); ?></h1>
					<span class="cba-plan-pill<?php echo $codesblock_paid ? ' is-paid' : ''; ?>"><?php echo esc_html( $codesblock_plan_name ); ?></span>
				</div>
			</div>
			<ul class="cba-stats" role="list">
				<li><strong><?php echo esc_html( number_format_i18n( count( $codesblock_courses ) + ( $codesblock_featured ? 1 : 0 ) ) ); ?></strong><span><?php esc_html_e( 'Courses started', 'codesblock' ); ?></span></li>
				<li><strong><?php echo esc_html( number_format_i18n( $codesblock_completed ) ); ?></strong><span><?php esc_html_e( 'Completed', 'codesblock' ); ?></span></li>
				<li><strong><?php echo esc_html( $codesblock_average ); ?>%</strong><span><?php esc_html_e( 'Average progress', 'codesblock' ); ?></span></li>
			</ul>
		</div>
	</section>

	<div class="container cba-grid">
		<div class="cba-main">
			<?php if ( $codesblock_is_admin ) : ?>
				<p class="cba-note"><?php esc_html_e( 'You are signed in as an administrator. Course progress is recorded for learner accounts only; this view shows what members see.', 'codesblock' ); ?> <a href="<?php echo esc_url( admin_url() ); ?>"><?php esc_html_e( 'Open WP Admin', 'codesblock' ); ?> &rarr;</a></p>
			<?php endif; ?>

			<?php if ( $codesblock_featured ) : ?>
				<section class="cba-continue" aria-labelledby="cba-continue-title">
					<a class="cba-continue-cover" href="<?php echo esc_url( $codesblock_featured['continue'] ); ?>" tabindex="-1" aria-hidden="true">
						<?php echo get_the_post_thumbnail( $codesblock_featured['id'], 'medium_large' ); ?>
					</a>
					<div class="cba-continue-body">
						<p class="cba-kicker"><?php esc_html_e( 'Continue where you left off', 'codesblock' ); ?></p>
						<h2 id="cba-continue-title"><a href="<?php echo esc_url( get_permalink( $codesblock_featured['id'] ) ); ?>"><?php echo esc_html( get_the_title( $codesblock_featured['id'] ) ); ?></a></h2>
						<div class="cba-progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?php echo esc_attr( $codesblock_featured['percent'] ); ?>" aria-label="<?php esc_attr_e( 'Course progress', 'codesblock' ); ?>"><span style="width:<?php echo esc_attr( $codesblock_featured['percent'] ); ?>%"></span></div>
						<p class="cba-meta">
							<strong><?php echo esc_html( $codesblock_featured['percent'] ); ?>% <?php esc_html_e( 'complete', 'codesblock' ); ?></strong>
							<?php if ( $codesblock_featured['updated'] ) : ?>
								· <?php echo esc_html( sprintf( __( 'last studied %s ago', 'codesblock' ), human_time_diff( $codesblock_featured['updated'] ) ) ); ?>
							<?php endif; ?>
						</p>
						<a class="cba-btn cba-btn-primary" href="<?php echo esc_url( $codesblock_featured['continue'] ); ?>"><?php echo esc_html( 100 === $codesblock_featured['percent'] ? __( 'Review course', 'codesblock' ) : __( 'Continue learning', 'codesblock' ) ); ?> &rarr;</a>
					</div>
				</section>
			<?php else : ?>
				<section class="cba-empty" aria-labelledby="cba-empty-title">
					<span class="cba-empty-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M5 3l14 9-14 9V3z"/></svg></span>
					<div>
						<h2 id="cba-empty-title"><?php esc_html_e( 'Start your first course', 'codesblock' ); ?></h2>
						<p><?php esc_html_e( 'Open any course and press Start. It appears here with your progress, so you can always pick up where you left off.', 'codesblock' ); ?></p>
					</div>
				</section>
			<?php endif; ?>

			<?php if ( $codesblock_courses ) : ?>
				<section class="cba-card" aria-labelledby="cba-courses-title">
					<div class="cba-card-head"><h2 id="cba-courses-title"><?php esc_html_e( 'Your other courses', 'codesblock' ); ?></h2></div>
					<ul class="cba-course-list" role="list">
						<?php foreach ( $codesblock_courses as $codesblock_course ) : ?>
							<li>
								<a href="<?php echo esc_url( $codesblock_course['continue'] ); ?>"><?php echo esc_html( get_the_title( $codesblock_course['id'] ) ); ?></a>
								<div class="cba-progress is-small" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?php echo esc_attr( $codesblock_course['percent'] ); ?>"><span style="width:<?php echo esc_attr( $codesblock_course['percent'] ); ?>%"></span></div>
								<span><?php echo esc_html( $codesblock_course['percent'] ); ?>%</span>
							</li>
						<?php endforeach; ?>
					</ul>
				</section>
			<?php endif; ?>

			<?php if ( $codesblock_suggestions ) : ?>
				<section class="cba-card" aria-labelledby="cba-next-title">
					<div class="cba-card-head">
						<h2 id="cba-next-title"><?php echo esc_html( $codesblock_featured ? __( 'Recommended next', 'codesblock' ) : __( 'Pick a course to begin', 'codesblock' ) ); ?></h2>
						<a href="<?php echo esc_url( $codesblock_courses_url ); ?>"><?php esc_html_e( 'All courses', 'codesblock' ); ?> &rarr;</a>
					</div>
					<div class="cba-suggest">
						<?php foreach ( $codesblock_suggestions as $codesblock_suggestion ) : ?>
							<?php
							$codesblock_price   = trim( (string) get_post_meta( $codesblock_suggestion->ID, '_course_price', true ) );
							$codesblock_is_free = '' === $codesblock_price || 'free' === strtolower( $codesblock_price );
							?>
							<a class="cba-suggest-card" href="<?php echo esc_url( get_permalink( $codesblock_suggestion ) ); ?>">
								<span class="cba-suggest-thumb"><?php echo get_the_post_thumbnail( $codesblock_suggestion, 'medium' ); ?></span>
								<span class="cba-suggest-tag"><?php echo esc_html( $codesblock_is_free ? __( 'Free', 'codesblock' ) : __( 'Full access', 'codesblock' ) ); ?></span>
								<strong><?php echo esc_html( get_the_title( $codesblock_suggestion ) ); ?></strong>
								<span class="cba-suggest-meta"><?php echo esc_html( (string) get_post_meta( $codesblock_suggestion->ID, '_course_duration', true ) ); ?></span>
							</a>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endif; ?>
		</div>

		<aside class="cba-side" aria-label="<?php esc_attr_e( 'Plan and account', 'codesblock' ); ?>">
			<section class="cba-card cba-plan<?php echo $codesblock_paid ? ' is-paid' : ''; ?>" id="membership" aria-labelledby="cba-plan-title">
				<p class="cba-kicker"><?php esc_html_e( 'Your plan', 'codesblock' ); ?></p>
				<h2 id="cba-plan-title"><?php echo esc_html( $codesblock_plan_name ); ?></h2>
				<?php if ( $codesblock_is_admin ) : ?>
					<p><?php esc_html_e( 'Full access to every course for site management.', 'codesblock' ); ?></p>
				<?php elseif ( $codesblock_paid && $codesblock_end ) : ?>
					<p><?php echo esc_html( sprintf( __( 'Full access until %s', 'codesblock' ), wp_date( get_option( 'date_format' ), $codesblock_end ) ) ); ?></p>
					<div class="cba-progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?php echo esc_attr( $codesblock_remaining ); ?>" aria-label="<?php esc_attr_e( 'Access time remaining', 'codesblock' ); ?>"><span style="width:<?php echo esc_attr( $codesblock_remaining ); ?>%"></span></div>
					<p class="cba-meta"><?php echo esc_html( sprintf( _n( '%s day left', '%s days left', $codesblock_days_left, 'codesblock' ), number_format_i18n( $codesblock_days_left ) ) ); ?></p>
					<?php if ( $codesblock_days_left <= 14 ) : ?>
						<button class="cba-btn cba-btn-primary cba-btn-block js-open-paywall" type="button"><?php esc_html_e( 'Extend access', 'codesblock' ); ?></button>
					<?php endif; ?>
				<?php elseif ( $codesblock_paid ) : ?>
					<p><?php esc_html_e( 'Full access with no expiry. Thank you for being a founding member.', 'codesblock' ); ?></p>
				<?php else : ?>
					<p><?php esc_html_e( 'Free lessons in every course, saved progress and all articles.', 'codesblock' ); ?></p>
					<ul class="cba-unlocks" role="list">
						<li><?php esc_html_e( 'Every paid course and new launch', 'codesblock' ); ?></li>
						<li><?php esc_html_e( 'Labs, drills and scored mock interviews', 'codesblock' ); ?></li>
						<li><?php esc_html_e( 'One payment, no auto-renewal', 'codesblock' ); ?></li>
					</ul>
					<button class="cba-btn cba-btn-primary cba-btn-block js-open-paywall" type="button"><?php esc_html_e( 'Get full access', 'codesblock' ); ?></button>
				<?php endif; ?>
				<?php if ( $codesblock_paid && ! $codesblock_is_admin && $codesblock_orders ) : ?>
					<a class="cba-link" href="<?php echo esc_url( $codesblock_orders ); ?>"><?php esc_html_e( 'Orders and receipts', 'codesblock' ); ?> &rarr;</a>
				<?php endif; ?>
			</section>

			<section class="cba-card cba-news" aria-labelledby="cba-news-title">
				<?php if ( 'subscribed' === $codesblock_news ) : ?>
					<p class="cba-kicker"><?php esc_html_e( 'Newsletter', 'codesblock' ); ?></p>
					<h2 id="cba-news-title"><?php esc_html_e( 'You get the weekly build note', 'codesblock' ); ?></h2>
					<p class="cba-meta"><?php esc_html_e( 'Unsubscribe anytime from the link at the bottom of any email.', 'codesblock' ); ?></p>
				<?php elseif ( 'unconfirmed' === $codesblock_news ) : ?>
					<p class="cba-kicker"><?php esc_html_e( 'Newsletter', 'codesblock' ); ?></p>
					<h2 id="cba-news-title"><?php esc_html_e( 'Confirm your subscription', 'codesblock' ); ?></h2>
					<p class="cba-meta"><?php echo esc_html( sprintf( __( 'We sent a confirmation link to %s.', 'codesblock' ), $codesblock_user->user_email ) ); ?></p>
				<?php else : ?>
					<span id="cba-news-title" class="screen-reader-text"><?php esc_html_e( 'Newsletter', 'codesblock' ); ?></span>
					<?php get_template_part( 'template-parts/newsletter', null, array( 'variant' => 'account', 'email' => $codesblock_user->user_email ) ); ?>
				<?php endif; ?>
			</section>

			<section class="cba-card cba-account" id="account" aria-labelledby="cba-account-title">
				<p class="cba-kicker"><?php esc_html_e( 'Account', 'codesblock' ); ?></p>
				<h2 id="cba-account-title" class="screen-reader-text"><?php esc_html_e( 'Account details', 'codesblock' ); ?></h2>
				<dl>
					<div><dt><?php esc_html_e( 'Name', 'codesblock' ); ?></dt><dd><?php echo esc_html( $codesblock_user->display_name ); ?></dd></div>
					<div><dt><?php esc_html_e( 'Email', 'codesblock' ); ?></dt><dd><?php echo esc_html( $codesblock_user->user_email ); ?></dd></div>
					<div><dt><?php esc_html_e( 'Signs in with', 'codesblock' ); ?></dt><dd><?php echo esc_html( implode( ', ', $codesblock_methods ) ); ?></dd></div>
					<div><dt><?php esc_html_e( 'Member since', 'codesblock' ); ?></dt><dd><?php echo esc_html( wp_date( 'F Y', strtotime( $codesblock_user->user_registered ) ) ); ?></dd></div>
				</dl>
				<div class="cba-account-actions">
					<?php if ( $codesblock_edit_url && ! $codesblock_is_admin ) : ?>
						<a href="<?php echo esc_url( $codesblock_edit_url ); ?>"><?php esc_html_e( 'Edit profile', 'codesblock' ); ?></a>
					<?php endif; ?>
					<a href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>"><?php esc_html_e( 'Sign out', 'codesblock' ); ?></a>
				</div>
			</section>
		</aside>
	</div>

<?php else : ?>
	<?php
	$codesblock_return = isset( $_GET['redirect_to'] ) && function_exists( 'codesblock_member_redirect' ) ? codesblock_member_redirect( esc_url_raw( wp_unslash( $_GET['redirect_to'] ) ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only return address, validated.
	$codesblock_pay_id = absint( get_option( 'pmpro_checkout_page_id' ) );
	$codesblock_pay    = $codesblock_pay_id ? (string) wp_parse_url( (string) get_permalink( $codesblock_pay_id ), PHP_URL_PATH ) : '';
	$codesblock_to_pay = $codesblock_return && '' !== $codesblock_pay && false !== strpos( $codesblock_return, $codesblock_pay );
	$codesblock_return = $codesblock_return ? $codesblock_return : $codesblock_home_url;
	?>
	<section class="cba-gate" aria-labelledby="cba-gate-title">
		<div class="cba-gate-card">
			<span class="cbx-brand" aria-hidden="true">CB</span>
			<h1 id="cba-gate-title"><?php echo esc_html( $codesblock_to_pay ? __( 'Sign in to continue to checkout', 'codesblock' ) : __( 'Sign in to your learning', 'codesblock' ) ); ?></h1>
			<p><?php echo esc_html( $codesblock_to_pay ? __( 'Your pass is linked to your CodesBlock account, so your access and receipts stay in one place. New here? Your free account is created when you continue.', 'codesblock' ) : __( 'Pick up where you left off, see your courses and manage your plan. New here? Your free account is created when you continue.', 'codesblock' ) ); ?></p>
			<?php get_template_part( 'template-parts/auth-providers', null, array( 'redirect' => $codesblock_return ) ); ?>
			<button class="cba-btn cba-btn-ghost cba-btn-block js-open-member" type="button" data-member-view="signin" data-member-email="open" data-member-redirect="<?php echo esc_url( $codesblock_return ); ?>"><?php esc_html_e( 'Use email instead', 'codesblock' ); ?></button>
			<a class="cba-link" href="<?php echo esc_url( $codesblock_courses_url ); ?>"><?php esc_html_e( 'Browse courses first', 'codesblock' ); ?> &rarr;</a>
		</div>
	</section>
<?php endif; ?>
</main>

<?php get_footer(); ?>
