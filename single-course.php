<?php
/**
 * Single Course page.
 * Includes: course hero, detailed info, interactive course guide, and newsletter.
 *
 * @package CodesBlock
 */

get_header();

/* ── Course meta ─────────────────────────────────────────────── */
$post_id        = get_the_ID();
$price          = get_post_meta( $post_id, '_course_price', true );
$original_price = get_post_meta( $post_id, '_course_original_price', true );
$level          = get_post_meta( $post_id, '_course_level', true );
$duration       = get_post_meta( $post_id, '_course_duration', true );
$badge          = get_post_meta( $post_id, '_course_badge', true );
$features       = get_post_meta( $post_id, '_course_features', true );
$what_you_learn = get_post_meta( $post_id, '_course_what_you_learn', true );
$syllabus       = get_post_meta( $post_id, '_course_syllabus', true );
$ai_summary     = get_post_meta( $post_id, '_course_ai_summary', true ) ?: get_the_excerpt();
$is_free        = ( 'free' === strtolower( (string) $price ) || '' === trim( (string) $price ) );
$is_system_design_course = in_array( get_post_field( 'post_name', $post_id ), array( 'system-design-interview-sprint', 'system-design-interview-lab', 'crack-the-system-design-interview' ), true );

$lessons_array   = function_exists( 'cbcore_get_course_lessons' ) ? cbcore_get_course_lessons( $post_id ) : array();
$lesson_count    = count( $lessons_array ) > 0 ? count( $lessons_array ) : 206;
$mock_interviews = get_post_meta( $post_id, '_course_mock_interviews', true ) ?: 8;
$course_rating   = get_post_meta( $post_id, '_course_rating', true ) ?: '4.7';
$users_learning  = get_post_meta( $post_id, '_course_users_learning', true ) ?: ( 4000 + ( $post_id * 3 ) );

/* ── User access check ──────────────────────────────────────────
   If Paid Memberships Pro is active, check membership.
   Otherwise treat all visitors as "guests".
   ─────────────────────────────────────────────────────────────── */
$user_has_access = function_exists( 'codesblock_user_can_view_protected_content' )
	? codesblock_user_can_view_protected_content( $post_id )
	: $is_free;
$codesblock_is_admin_session = function_exists( 'cbcommerce_user_can_access_admin' )
	? cbcommerce_user_can_access_admin()
	: current_user_can( 'manage_options' );
$codesblock_is_frontend_member = function_exists( 'cbcommerce_is_frontend_member' )
	? cbcommerce_is_frontend_member()
	: ( is_user_logged_in() && ! $codesblock_is_admin_session );
$codesblock_can_track_progress = $codesblock_is_frontend_member && $user_has_access && function_exists( 'cbcommerce_user_can_track_course' ) && cbcommerce_user_can_track_course( $post_id );
$codesblock_course_progress    = $codesblock_can_track_progress && function_exists( 'cbcommerce_get_course_progress' ) ? cbcommerce_get_course_progress( $post_id ) : 0;
$codesblock_first_lesson       = function_exists( 'cbcore_get_first_course_lesson' ) ? cbcore_get_first_course_lesson( $post_id ) : null;
$codesblock_start_destination  = $codesblock_first_lesson ? get_permalink( $codesblock_first_lesson ) : '#about-heading';
$codesblock_course_start_url   = add_query_arg( 'cb_course_start', $post_id, get_permalink( $post_id ) );
$codesblock_payments_ready     = function_exists( 'cbcommerce_payments_ready' ) && cbcommerce_payments_ready();
$codesblock_billing_url        = function_exists( 'cbcommerce_checkout_url' ) ? cbcommerce_checkout_url( 'pro' ) : wp_login_url( get_permalink( $post_id ) );

/* ── Course guide suggestion chips (shown in panel) ─────────── */
$ai_suggestions = array(
	__( 'What will I learn?', 'codesblock' ),
	__( 'How long does it take?', 'codesblock' ),
	__( 'What level is this course?', 'codesblock' ),
	__( 'What\'s the price?', 'codesblock' ),
	__( 'What are the prerequisites?', 'codesblock' ),
);

if ( $is_system_design_course ) {
	$ai_suggestions = array(
		__( 'Explain this trade-off simply', 'codesblock' ),
		__( 'Research the stronger alternative', 'codesblock' ),
		__( 'Challenge my architecture', 'codesblock' ),
		__( 'Ask me an interviewer follow-up', 'codesblock' ),
		__( 'Turn this lesson into a drill', 'codesblock' ),
	);
}
?>

<main id="main" class="single-course-wrap">
	<!-- ══════════════════════════════════════════════════
	     COURSE HERO BANNER
	══════════════════════════════════════════════════ -->
	<section class="course-hero-banner">
		<div class="container">
			<?php
			get_template_part(
				'template-parts/breadcrumbs',
				null,
				array(
					'items'   => array(
						array(
							'label' => __( 'Courses', 'codesblock' ),
							'url'   => get_post_type_archive_link( 'course' ) ?: home_url( '/courses/' ),
						),
					),
					'current' => get_the_title(),
				)
			);
			?>
			<div class="course-hero-inner">

				<!-- Left: text -->
				<div class="course-hero-content">
					<?php if ( $badge ) : ?>
						<span class="course-badge-hero"><?php echo esc_html( $badge ); ?></span>
					<?php endif; ?>

					<h1><?php the_title(); ?></h1>
					<p class="course-subtitle"><?php echo esc_html( get_the_excerpt() ); ?></p>

					<div class="course-hero-meta advanced-meta">
						<span class="meta-rating" title="<?php echo esc_attr( $course_rating ); ?> out of 5 stars">
							<span class="stars" aria-hidden="true" style="display:flex; align-items:center; gap:2px; color:#f59e0b;">
								<svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
								<svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
								<svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
								<svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
								<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
							</span>
							<span><?php echo esc_html( $course_rating ); ?></span>
						</span>
						<span class="meta-lessons">
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
							<span><?php echo esc_html( $lesson_count ); ?> Lessons</span>
						</span>
						<?php if ( $is_system_design_course || $mock_interviews > 0 ) : ?>
							<span class="meta-interviews">
								<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
								<span><?php echo esc_html( $mock_interviews ); ?> Mock Interviews</span>
							</span>
						<?php endif; ?>
						<span class="meta-updated">
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
							<span><?php esc_html_e( 'Updated this week', 'codesblock' ); ?></span>
						</span>
						<?php if ( $duration ) : ?>
							<span class="meta-duration">
								<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
								<span><?php echo esc_html( $duration ); ?></span>
							</span>
						<?php endif; ?>
						<span class="meta-users-learning">
							<span class="pulse-dot" aria-hidden="true"></span>
							<span><?php echo esc_html( number_format( $users_learning ) ); ?> users learning</span>
						</span>
					</div>
				</div>

				<!-- Right: sticky enrollment card -->
				<aside aria-label="<?php esc_attr_e( 'Course enrollment', 'codesblock' ); ?>">
					<div class="course-sticky-card">
						<!-- Thumbnail -->
						<div class="sticky-card-thumb">
							<?php if ( $is_system_design_course ) : ?>
								<?php get_template_part( 'template-parts/system-design-hero-visual' ); ?>
							<?php elseif ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'large' ); ?>
							<?php else : ?>
								<div class="sticky-card-placeholder" aria-hidden="true">
									<span>CB</span>
									<strong><?php esc_html_e( 'Build with confidence', 'codesblock' ); ?></strong>
								</div>
							<?php endif; ?>
						</div>

						<div class="sticky-card-body">
							<!-- Price -->
							<div class="sticky-price-row">
								<?php if ( $is_free ) : ?>
									<span class="sticky-price" style="color:var(--green);"><?php esc_html_e( 'Free', 'codesblock' ); ?></span>
								<?php elseif ( $price ) : ?>
									<span class="sticky-price"><?php echo esc_html( $price ); ?></span>
									<?php if ( $original_price ) : ?>
										<span class="sticky-price-original"><?php echo esc_html( $original_price ); ?></span>
									<?php endif; ?>
								<?php endif; ?>
							</div>

							<!-- Enroll CTA -->
							<?php if ( $codesblock_can_track_progress ) : ?>
								<a class="btn-enroll js-start-course" id="btn-enroll-main" href="<?php echo esc_url( $codesblock_start_destination ); ?>" data-course-id="<?php echo esc_attr( $post_id ); ?>" data-course-target="#about-heading" data-course-destination="<?php echo esc_url( $codesblock_start_destination ); ?>">
									<?php echo esc_html( $codesblock_course_progress ? __( 'Continue learning', 'codesblock' ) : __( 'Start course', 'codesblock' ) ); ?> &rarr;
								</a>
							<?php elseif ( $codesblock_is_admin_session ) : ?>
								<a class="btn-enroll" id="btn-enroll-main" href="<?php echo esc_url( get_edit_post_link( $post_id ) ); ?>"><?php esc_html_e( 'Edit course in WP Admin', 'codesblock' ); ?> &rarr;</a>
							<?php elseif ( $is_free ) : ?>
								<button class="btn-enroll js-open-course-auth" type="button" id="btn-enroll-main" aria-haspopup="dialog" aria-controls="course-auth-overlay">
									<?php esc_html_e( 'Sign in to start learning', 'codesblock' ); ?> &rarr;
								</button>
							<?php else : ?>
								<button
									class="btn-enroll <?php echo $codesblock_is_frontend_member ? 'js-open-paywall' : 'js-open-course-auth'; ?>"
									type="button"
									id="btn-enroll-main"
									aria-haspopup="dialog"
									aria-controls="<?php echo $codesblock_is_frontend_member ? 'paywall-overlay' : 'course-auth-overlay'; ?>"
								>
									<?php echo esc_html( $codesblock_is_frontend_member ? __( 'View access passes', 'codesblock' ) : ( $codesblock_payments_ready ? __( 'Sign in to buy', 'codesblock' ) : __( 'Sign in for launch updates', 'codesblock' ) ) ); ?> &rarr;
								</button>
							<?php endif; ?>

							<p class="enroll-guarantee">
								<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
								<?php echo esc_html( $is_free ? __( 'No payment required', 'codesblock' ) : __( 'One-time passes. No automatic renewal.', 'codesblock' ) ); ?>
							</p>

							<!-- Includes list -->
							<?php if ( $features ) : ?>
								<ul class="course-includes-list">
									<?php
									$codesblock_legacy_feature_labels = array(
										'AI Tutor included'          => __( 'Interactive course guide', 'codesblock' ),
										'Certificate of completion' => __( 'Saved learning progress', 'codesblock' ),
									);
									foreach ( array_filter( array_map( 'trim', explode( "\n", $features ) ) ) as $feat ) :
										$feature_label = isset( $codesblock_legacy_feature_labels[ $feat ] ) ? $codesblock_legacy_feature_labels[ $feat ] : $feat;
									?>
										<li>
											<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
											<?php echo esc_html( $feature_label ); ?>
										</li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
						</div>
					</div>
				</aside>
			</div>
		</div>
	</section>

	<?php if ( ! is_user_logged_in() ) : ?>
		<div id="course-auth-overlay" class="cb-member-overlay cb-auth-overlay" aria-hidden="true" hidden>
			<div class="cb-member-backdrop" data-member-close></div>
			<section class="cb-member-modal cb-auth-modal cb-course-auth-modal" role="dialog" aria-modal="true" aria-labelledby="course-auth-title" aria-describedby="course-auth-description" tabindex="-1">
				<button class="cb-member-close" type="button" data-member-close aria-label="<?php esc_attr_e( 'Close course sign-in dialog', 'codesblock' ); ?>">
					<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
				</button>
				<div class="cb-course-auth-layout">
					<div class="cb-course-auth-story">
						<a class="cb-course-auth-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" tabindex="-1" aria-hidden="true">CB</a>
						<span class="cb-course-auth-kicker"><?php echo esc_html( $is_free ? __( 'Free learning path', 'codesblock' ) : __( 'Protected learning path', 'codesblock' ) ); ?></span>
						<h2 id="course-auth-title"><?php echo esc_html( $is_free ? __( 'Your first lesson is one click away.', 'codesblock' ) : ( $codesblock_payments_ready ? __( 'Sign in before choosing access.', 'codesblock' ) : __( 'Sign in for the paid-course launch.', 'codesblock' ) ) ); ?></h2>
						<p id="course-auth-description"><?php echo esc_html( get_the_title( $post_id ) ); ?></p>
						<ol class="cb-course-auth-steps">
							<li><span>1</span><strong><?php esc_html_e( 'Continue securely', 'codesblock' ); ?></strong></li>
							<li><span>2</span><strong><?php echo esc_html( $is_free ? __( 'Open lesson one', 'codesblock' ) : ( $codesblock_payments_ready ? __( 'Choose your pass', 'codesblock' ) : __( 'Get launch updates', 'codesblock' ) ) ); ?></strong></li>
							<li><span>3</span><strong><?php esc_html_e( 'Keep progress saved', 'codesblock' ); ?></strong></li>
						</ol>
					</div>
					<div class="cb-course-auth-action">
						<span class="cb-course-auth-eyebrow"><?php esc_html_e( 'CodesBlock account', 'codesblock' ); ?></span>
						<h3><?php esc_html_e( 'Continue with Google', 'codesblock' ); ?></h3>
						<p><?php echo esc_html( $is_free ? __( 'We will open your first lesson immediately after sign-in.', 'codesblock' ) : ( $codesblock_payments_ready ? __( 'We will take you to secure billing immediately after sign-in.', 'codesblock' ) : __( 'We will take you to launch updates while payment setup is completed and verified.', 'codesblock' ) ) ); ?></p>
						<div class="cb-social-grid cb-course-social-grid" aria-label="<?php esc_attr_e( 'Social sign in', 'codesblock' ); ?>">
							<?php
							$codesblock_social_redirect = $is_free ? $codesblock_course_start_url : $codesblock_billing_url;
							$codesblock_social_buttons  = shortcode_exists( 'nextend_social_login' ) ? do_shortcode( '[nextend_social_login redirect="' . esc_url( $codesblock_social_redirect ) . '"]' ) : '';
							if ( false !== strpos( $codesblock_social_buttons, 'nsl-button' ) ) {
								echo $codesblock_social_buttons; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted provider shortcode output.
							} else {
								echo '<p class="cb-form-feedback">' . esc_html__( 'Social sign-in is not configured yet. Enable a provider to start this course.', 'codesblock' ) . '</p>';
							}
							?>
						</div>
						<div class="cb-course-auth-trust"><span aria-hidden="true">&#10003;</span><p><strong><?php esc_html_e( 'No password to remember', 'codesblock' ); ?></strong><small><?php esc_html_e( 'Only basic profile details are used for your learning account.', 'codesblock' ); ?></small></p></div>
					</div>
				</div>
			</section>
		</div>
	<?php endif; ?>

	<?php if ( $is_system_design_course ) : ?>
	<section class="course-value-rail" aria-label="<?php esc_attr_e( 'How this course helps you learn', 'codesblock' ); ?>">
		<div class="container">
			<div class="course-value-rail-inner">
				<div><span>01</span><strong><?php esc_html_e( 'Read for understanding', 'codesblock' ); ?></strong><small><?php esc_html_e( 'Clear lessons, diagrams, and worked decisions.', 'codesblock' ); ?></small></div>
				<div><span>02</span><strong><?php esc_html_e( 'Ask at the point of confusion', 'codesblock' ); ?></strong><small><?php esc_html_e( 'Select a line, request an explanation, and go deeper.', 'codesblock' ); ?></small></div>
				<div><span>03</span><strong><?php esc_html_e( 'Defend the trade-off', 'codesblock' ); ?></strong><small><?php esc_html_e( 'Turn understanding into interview-ready reasoning.', 'codesblock' ); ?></small></div>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<!-- ══════════════════════════════════════════════════
	     MAIN CONTENT LAYOUT
	══════════════════════════════════════════════════ -->
	<div class="container">
		<nav class="course-jump-nav" aria-label="<?php esc_attr_e( 'Course page sections', 'codesblock' ); ?>">
			<span><?php esc_html_e( 'On this page', 'codesblock' ); ?></span>
			<div>
				<?php if ( $what_you_learn ) : ?>
					<a href="#learn-heading"><?php esc_html_e( 'Outcomes', 'codesblock' ); ?></a>
				<?php endif; ?>
				<?php if ( $is_system_design_course ) : ?>
					<a href="#design-labs"><?php esc_html_e( 'Design labs', 'codesblock' ); ?></a>
				<?php endif; ?>
				<a href="#material-heading"><?php echo esc_html( $user_has_access ? __( 'Course material', 'codesblock' ) : __( 'Access', 'codesblock' ) ); ?></a>
				<?php if ( $syllabus ) : ?>
					<a href="#syllabus-heading"><?php esc_html_e( 'Curriculum', 'codesblock' ); ?></a>
				<?php endif; ?>
				<a href="<?php echo esc_url( get_post_type_archive_link( 'course' ) ?: home_url( '/courses/' ) ); ?>"><?php esc_html_e( 'All courses', 'codesblock' ); ?> &rarr;</a>
			</div>
		</nav>
		<?php if ( $is_system_design_course ) : ?>
			<?php get_template_part( 'template-parts/system-design-course-visuals' ); ?>
		<?php endif; ?>
		<div class="course-content-layout">

			<!-- ── Left: course details ─────────────────────── -->
			<div class="course-details-area">
				<?php if ( $codesblock_can_track_progress ) : ?>
					<section class="course-section cb-course-progress" id="course-progress" aria-labelledby="course-progress-heading">
						<div class="cb-course-progress-heading">
							<div>
								<p class="eyebrow"><?php esc_html_e( 'My learning', 'codesblock' ); ?></p>
								<h2 id="course-progress-heading"><?php esc_html_e( 'Track this course', 'codesblock' ); ?></h2>
							</div>
							<strong data-course-progress-value><?php echo esc_html( $codesblock_course_progress ); ?>%</strong>
						</div>
						<div class="cb-course-progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?php echo esc_attr( $codesblock_course_progress ); ?>">
							<span data-course-progress-fill style="width: <?php echo esc_attr( $codesblock_course_progress ); ?>%"></span>
						</div>
						<form class="cb-course-progress-form" data-course-progress-form>
							<input type="hidden" name="course_id" value="<?php echo esc_attr( $post_id ); ?>">
							<label for="cb-course-progress-select"><?php esc_html_e( 'Current progress', 'codesblock' ); ?></label>
							<select id="cb-course-progress-select" name="progress">
								<?php foreach ( array( 0, 25, 50, 75, 100 ) as $codesblock_progress_step ) : ?>
									<option value="<?php echo esc_attr( $codesblock_progress_step ); ?>" <?php selected( $codesblock_course_progress, $codesblock_progress_step ); ?>><?php echo esc_html( $codesblock_progress_step ); ?>%</option>
								<?php endforeach; ?>
							</select>
							<button class="button button-primary" type="submit"><?php esc_html_e( 'Save progress', 'codesblock' ); ?></button>
							<p class="cb-form-feedback" role="status" aria-live="polite"></p>
						</form>
					</section>
				<?php endif; ?>

				<?php if ( $is_system_design_course ) : ?>
				<section class="course-section course-ai-spotlight" aria-labelledby="ai-learning-heading">
					<div class="course-section-heading">
						<p class="eyebrow"><?php esc_html_e( 'The CodesBlock difference', 'codesblock' ); ?></p>
						<h2 id="ai-learning-heading"><?php esc_html_e( 'Keep the learning method that works. Add an AI thinking partner.', 'codesblock' ); ?></h2>
						<p><?php esc_html_e( 'Read carefully, sketch the system, and form your own answer first. When a sentence or trade-off slows you down, ask in context—without leaving the lesson or losing your train of thought.', 'codesblock' ); ?></p>
					</div>

					<div class="ai-learning-demo" aria-label="<?php esc_attr_e( 'Example AI-assisted learning flow', 'codesblock' ); ?>">
						<div class="ai-learning-selection">
							<span><?php esc_html_e( 'Selected from the lesson', 'codesblock' ); ?></span>
							<blockquote><?php esc_html_e( 'A cache reduces read latency, but it also creates a second place where data can become stale.', 'codesblock' ); ?></blockquote>
							<div class="ai-learning-actions" aria-hidden="true">
								<span><?php esc_html_e( 'Explain simply', 'codesblock' ); ?></span>
								<span><?php esc_html_e( 'Research trade-offs', 'codesblock' ); ?></span>
								<span><?php esc_html_e( 'Ask a follow-up', 'codesblock' ); ?></span>
							</div>
						</div>
						<div class="ai-learning-answer">
							<div class="ai-learning-answer-label"><span>AI</span><strong><?php esc_html_e( 'Study companion', 'codesblock' ); ?></strong><small><?php esc_html_e( 'Example learning flow', 'codesblock' ); ?></small></div>
							<p><?php esc_html_e( 'Caching makes repeated reads faster, but the cached copy may lag behind the database. In an interview, name the acceptable staleness, choose an invalidation strategy, and explain what happens during a cache miss.', 'codesblock' ); ?></p>
							<p class="ai-learning-followup"><?php esc_html_e( 'Follow-up: what would change if the product required read-your-own-writes consistency?', 'codesblock' ); ?></p>
						</div>
					</div>
					<p class="ai-integration-note"><strong><?php esc_html_e( 'Product preview:', 'codesblock' ); ?></strong> <?php esc_html_e( 'This shows the intended in-lesson experience. Live model answers and web research require the AI service to be connected and verified.', 'codesblock' ); ?></p>

					<ul class="ai-learning-benefits" role="list">
						<li><strong><?php esc_html_e( 'Explain', 'codesblock' ); ?></strong><span><?php esc_html_e( 'Turn dense architecture language into a mental model you can repeat.', 'codesblock' ); ?></span></li>
						<li><strong><?php esc_html_e( 'Research', 'codesblock' ); ?></strong><span><?php esc_html_e( 'Explore alternatives, caveats, and real-world context from the lesson.', 'codesblock' ); ?></span></li>
						<li><strong><?php esc_html_e( 'Go deeper', 'codesblock' ); ?></strong><span><?php esc_html_e( 'Keep asking until you can defend the decision without the assistant.', 'codesblock' ); ?></span></li>
					</ul>
				</section>
				<?php endif; ?>

				<!-- What You'll Learn -->
				<?php if ( $what_you_learn ) : ?>
					<section class="course-section" aria-labelledby="learn-heading">
						<h2 id="learn-heading"><?php esc_html_e( "What You'll Learn", 'codesblock' ); ?></h2>
						<ul class="learn-grid" role="list">
							<?php
							foreach ( array_filter( array_map( 'trim', explode( "\n", $what_you_learn ) ) ) as $item ) :
							?>
								<li>
									<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
									<?php echo esc_html( $item ); ?>
								</li>
							<?php endforeach; ?>
						</ul>
					</section>
				<?php endif; ?>

				<?php if ( $is_system_design_course ) : ?>
				<section class="course-section course-level-path" aria-labelledby="level-path-heading">
					<div class="course-section-heading">
						<p class="eyebrow"><?php esc_html_e( 'One course, three depths', 'codesblock' ); ?></p>
						<h2 id="level-path-heading"><?php esc_html_e( 'Start at your level. Grow into the next one.', 'codesblock' ); ?></h2>
						<p><?php esc_html_e( 'The core interview method stays the same; the expected depth, vocabulary, and trade-off pressure increase as you progress.', 'codesblock' ); ?></p>
					</div>
					<div class="course-level-grid">
						<article><span><?php esc_html_e( 'Intern / early career', 'codesblock' ); ?></span><h3><?php esc_html_e( 'Build the vocabulary', 'codesblock' ); ?></h3><p><?php esc_html_e( 'Learn requirements, APIs, databases, caching, queues, and how a complete system fits together.', 'codesblock' ); ?></p></article>
						<article><span><?php esc_html_e( 'Intermediate', 'codesblock' ); ?></span><h3><?php esc_html_e( 'Build the method', 'codesblock' ); ?></h3><p><?php esc_html_e( 'Use a repeatable 45-minute framework, estimate scale, and make choices from constraints instead of habit.', 'codesblock' ); ?></p></article>
						<article><span><?php esc_html_e( 'Advanced / senior', 'codesblock' ); ?></span><h3><?php esc_html_e( 'Defend the trade-offs', 'codesblock' ); ?></h3><p><?php esc_html_e( 'Stress-test reliability, consistency, cost, operations, and the deeper follow-ups that separate senior answers.', 'codesblock' ); ?></p></article>
					</div>
				</section>
				<?php endif; ?>

				<section class="course-section course-about-preview" aria-labelledby="about-heading">
					<p class="eyebrow"><?php esc_html_e( 'About this course', 'codesblock' ); ?></p>
					<h2 id="about-heading"><?php esc_html_e( 'Crack the interview. Build the system.', 'codesblock' ); ?></h2>
					<?php if ( $is_system_design_course ) : ?>
						<p><?php esc_html_e( 'Master real-world interview questions, understand the latest architectural trends, and see how AI is reshaping modern system designs.', 'codesblock' ); ?></p>
					<?php else : ?>
						<p><?php echo esc_html( $ai_summary ); ?></p>
					<?php endif; ?>
					<?php if ( $is_system_design_course ) : ?>
						<div class="course-lab-stats" aria-label="<?php esc_attr_e( 'Course at a glance', 'codesblock' ); ?>">
							<span><strong>12</strong><?php esc_html_e( 'modules', 'codesblock' ); ?></span>
							<span><strong>60+</strong><?php esc_html_e( 'guided lessons', 'codesblock' ); ?></span>
							<span><strong>12</strong><?php esc_html_e( 'design labs', 'codesblock' ); ?></span>
							<span><strong>1</strong><?php esc_html_e( 'capstone review', 'codesblock' ); ?></span>
						</div>
					<?php endif; ?>
				</section>

				<!-- Course material or access decision -->
				<section class="course-section <?php echo $user_has_access ? 'course-material-section' : 'course-access-section'; ?>" aria-labelledby="material-heading">
					<h2 id="material-heading"><?php echo esc_html( $user_has_access ? __( 'Course Material', 'codesblock' ) : __( 'Course Access', 'codesblock' ) ); ?></h2>
					<?php if ( $user_has_access ) : ?>
						<div class="entry-content"><?php the_content(); ?></div>
					<?php else : ?>
						<!-- The hero already provides the public preview; do not repeat it here. -->
						<div class="content-gate-wrapper content-gate-wrapper-compact">
							<div class="content-gate-overlay content-gate-overlay-static">
								<span class="content-gate-eyebrow"><?php esc_html_e( 'Premium course', 'codesblock' ); ?></span>
								<h3><?php esc_html_e( 'Continue with the complete course', 'codesblock' ); ?></h3>
								<p><?php esc_html_e( 'Open the lessons and curriculum shown on this page, then keep your progress saved with a CodesBlock access pass.', 'codesblock' ); ?></p>
								<ul class="content-gate-benefits" role="list">
									<li><?php esc_html_e( 'Every paid course', 'codesblock' ); ?></li>
									<li><?php esc_html_e( 'Saved progress', 'codesblock' ); ?></li>
									<li><?php esc_html_e( 'No automatic renewal', 'codesblock' ); ?></li>
								</ul>
								<div class="content-gate-actions">
								<button class="button button-primary <?php echo esc_attr( $codesblock_is_frontend_member ? 'js-open-paywall' : 'js-open-course-auth' ); ?>" type="button" aria-haspopup="dialog" aria-controls="<?php echo esc_attr( $codesblock_is_frontend_member ? 'paywall-overlay' : 'course-auth-overlay' ); ?>">
									<?php echo esc_html( $codesblock_is_frontend_member ? __( 'View access passes', 'codesblock' ) : __( 'Sign in to continue', 'codesblock' ) ); ?>
									</button>
									<?php if ( $codesblock_is_frontend_member ) : ?>
										<a class="content-gate-secondary" href="<?php echo esc_url( get_post_type_archive_link( 'course' ) ?: home_url( '/courses/' ) ); ?>"><?php esc_html_e( 'Browse all courses', 'codesblock' ); ?></a>
									<?php else : ?>
										<button class="content-gate-secondary js-open-member" type="button" data-member-view="signin" aria-haspopup="dialog" aria-controls="member-overlay">
											<?php esc_html_e( 'Already a member? Sign in', 'codesblock' ); ?>
										</button>
									<?php endif; ?>
								</div>
							</div>
						</div>
					<?php endif; ?>
				</section>

				<!-- Syllabus / Curriculum -->
				<?php if ( $syllabus ) : ?>
					<section class="course-section" aria-labelledby="syllabus-heading">
						<h2 id="syllabus-heading"><?php esc_html_e( 'Course Syllabus', 'codesblock' ); ?></h2>
						<?php
						/* Parse ## Section and - Lesson format into accordion */
						$lines      = explode( "\n", $syllabus );
						$in_module  = false;
						$module_index = 0;
						ob_start();

						foreach ( $lines as $raw ) {
							$line = trim( $raw );
							if ( empty( $line ) ) continue;

							if ( strpos( $line, '##' ) === 0 ) {
								/* Close previous module */
								if ( $in_module ) echo '</ul></div>';
								$title       = esc_html( trim( substr( $line, 2 ) ) );
								$is_first    = 0 === $module_index;
								$module_id   = 'syllabus-module-' . $module_index;
								$module_class = $is_first ? 'syllabus-module is-open' : 'syllabus-module';
								echo '<div class="' . esc_attr( $module_class ) . '">';
								echo '<button class="syllabus-module-header" type="button" aria-expanded="' . ( $is_first ? 'true' : 'false' ) . '" aria-controls="' . esc_attr( $module_id ) . '">';
								echo '<span>' . $title . '</span>';
								echo '<svg class="syllabus-chevron" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>';
								echo '</button>';
								echo '<ul class="syllabus-module-lessons" id="' . esc_attr( $module_id ) . '" role="list">';
								$in_module = true;
								$module_index++;
							} elseif ( strpos( $line, '-' ) === 0 ) {
								if ( ! $in_module ) {
									echo '<div class="syllabus-module"><ul class="syllabus-module-lessons" role="list">';
									$in_module = true;
								}
								$lesson = esc_html( trim( substr( $line, 1 ) ) );
								echo '<li><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polygon points="5 3 19 12 5 21 5 3"/></svg>' . $lesson . '</li>';
							}
						}
						if ( $in_module ) echo '</ul></div>';
						echo ob_get_clean();
						?>
					</section>
				<?php endif; ?>

				<?php if ( $user_has_access ) : ?>
				<!-- Course Guide CTA (inline) — open the fixed panel -->
				<section class="course-section" style="background:linear-gradient(135deg,#f0f5ff,#e8f5ee);border-color:#d4e0ff;">
					<div style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
						<div style="flex:1;min-width:200px;">
							<p class="eyebrow" style="color:var(--blue);"><?php esc_html_e( 'Course Guide', 'codesblock' ); ?></p>
							<h2 style="font-size:1.3rem;margin-bottom:6px;"><?php esc_html_e( 'Find your way through the course.', 'codesblock' ); ?></h2>
							<p style="color:var(--muted);margin:0;font-size:.9rem;"><?php esc_html_e( 'Use the interactive guide for the course summary, prerequisites, pricing, and curriculum details.', 'codesblock' ); ?></p>
						</div>
						<button
							class="button button-primary"
							id="open-ai-tutor-inline"
							type="button"
							style="white-space:nowrap;"
						>
							<?php esc_html_e( 'Open Course Guide', 'codesblock' ); ?>
						</button>
					</div>

					<!-- Suggestion chips inline -->
					<div class="ai-suggestion-chips" style="margin-top:18px;">
						<?php foreach ( $ai_suggestions as $s ) : ?>
							<button class="ai-suggestion-chip" type="button"><?php echo esc_html( $s ); ?></button>
						<?php endforeach; ?>
					</div>
				</section>
				<?php endif; ?>

			</div>

			<!-- ── Right: secondary sidebar ─────────────────── -->
			<aside class="course-details-sidebar" aria-label="<?php esc_attr_e( 'Course extras', 'codesblock' ); ?>">

				<?php if ( $is_system_design_course && ! is_user_logged_in() ) : ?>
				<div class="course-section course-start-card">
					<p class="eyebrow"><?php esc_html_e( 'Start free', 'codesblock' ); ?></p>
					<h2><?php esc_html_e( 'Create your learning workspace', 'codesblock' ); ?></h2>
					<p><?php esc_html_e( 'Save progress, personalize your path, and preview CodesBlock before choosing a paid access pass.', 'codesblock' ); ?></p>
					<button class="button button-primary js-open-course-auth" type="button" aria-haspopup="dialog" aria-controls="course-auth-overlay"><?php esc_html_e( 'Continue with Google', 'codesblock' ); ?></button>
					<small><?php esc_html_e( 'No password or card required. Course purchase stays separate.', 'codesblock' ); ?></small>
				</div>
				<?php endif; ?>

				<!-- Useful secondary actions; the previous unconnected newsletter form was removed. -->
				<div class="course-section course-utility-card">
					<p class="eyebrow"><?php esc_html_e( 'Keep exploring', 'codesblock' ); ?></p>
					<h2><?php esc_html_e( 'Choose your next step', 'codesblock' ); ?></h2>
					<a class="button button-secondary course-browse-link" href="<?php echo esc_url( get_post_type_archive_link( 'course' ) ?: home_url( '/courses/' ) ); ?>"><?php esc_html_e( 'Browse all courses', 'codesblock' ); ?> &rarr;</a>
					<strong class="course-share-label"><?php esc_html_e( 'Share this course', 'codesblock' ); ?></strong>
					<div class="course-share-links">
						<a
							href="https://twitter.com/intent/tweet?text=<?php echo rawurlencode( get_the_title() . ' — ' . get_permalink() ); ?>"
							target="_blank"
							rel="noopener noreferrer"
							class="btn-view-course"
						>Twitter / X</a>
						<a
							href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo rawurlencode( get_permalink() ); ?>"
							target="_blank"
							rel="noopener noreferrer"
							class="btn-view-course course-share-linkedin"
						>LinkedIn</a>
					</div>
				</div>

			</aside>
		</div>
	</div>

</main>

<?php if ( $user_has_access ) : ?>
<!-- Interactive course guide panel. -->

<!-- Toggle button anchored to right edge -->
<button
	class="ai-tutor-toggle"
	id="ai-tutor-toggle"
	type="button"
	aria-expanded="false"
	aria-controls="ai-tutor-panel"
	aria-label="<?php esc_attr_e( 'Open Course Guide', 'codesblock' ); ?>"
>
	<span class="ai-tutor-toggle-inner">
		<span class="ai-tutor-pulse" aria-hidden="true"></span>
		<span style="writing-mode:vertical-rl;text-orientation:mixed;letter-spacing:.05em;">Guide</span>
	</span>
</button>

<!-- Slide-in panel -->
<div
	id="ai-tutor-panel"
	class="ai-tutor-panel"
	role="dialog"
	aria-modal="false"
	aria-label="<?php esc_attr_e( 'Course Guide', 'codesblock' ); ?>"
	aria-hidden="true"
	inert
	hidden
>
	<!-- Header -->
	<div class="ai-tutor-panel-header">
		<div class="ai-tutor-avatar" aria-hidden="true">?</div>
		<div class="ai-tutor-panel-header-info">
			<strong><?php esc_html_e( 'Course Guide', 'codesblock' ); ?></strong>
			<small><?php echo esc_html( get_the_title() ); ?></small>
		</div>
		<button
			class="ai-tutor-close"
			id="ai-tutor-close"
			type="button"
			aria-label="<?php esc_attr_e( 'Close Course Guide', 'codesblock' ); ?>"
		>&#x2715;</button>
	</div>

	<!-- Tabs -->
	<div class="ai-tutor-tabs" role="tablist" aria-label="<?php esc_attr_e( 'Course Guide views', 'codesblock' ); ?>">
		<button class="ai-tutor-tab is-active" id="ai-tab-summary-control" type="button" data-tab="summary" role="tab" aria-selected="true" aria-controls="ai-tab-summary" tabindex="0"><?php esc_html_e( 'Summary', 'codesblock' ); ?></button>
		<button class="ai-tutor-tab" id="ai-tab-chat-control" type="button" data-tab="chat" role="tab" aria-selected="false" aria-controls="ai-tab-chat" tabindex="-1"><?php esc_html_e( 'Ask Guide', 'codesblock' ); ?></button>
	</div>

	<!-- Summary tab -->
	<div class="ai-tutor-tab-content is-active" id="ai-tab-summary" role="tabpanel" aria-labelledby="ai-tab-summary-control">
		<div class="ai-tutor-summary">
			<p class="ai-tutor-summary-title">
				<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
				<?php esc_html_e( 'Course Summary', 'codesblock' ); ?>
			</p>
			<p><?php echo esc_html( $ai_summary ); ?></p>
			<?php if ( $level || $duration ) : ?>
				<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:18px;">
					<?php if ( $level ) : ?>
						<div style="background:var(--bg);border:1px solid var(--line);border-radius:10px;padding:14px;text-align:center;">
							<small style="color:var(--muted);font-size:.7rem;font-weight:700;text-transform:uppercase;"><?php esc_html_e( 'Level', 'codesblock' ); ?></small>
							<strong style="display:block;font-size:.95rem;margin-top:4px;"><?php echo esc_html( $level ); ?></strong>
						</div>
					<?php endif; ?>
					<?php if ( $duration ) : ?>
						<div style="background:var(--bg);border:1px solid var(--line);border-radius:10px;padding:14px;text-align:center;">
							<small style="color:var(--muted);font-size:.7rem;font-weight:700;text-transform:uppercase;"><?php esc_html_e( 'Duration', 'codesblock' ); ?></small>
							<strong style="display:block;font-size:.95rem;margin-top:4px;"><?php echo esc_html( $duration ); ?></strong>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>

		<!-- Suggestion chips -->
		<div class="ai-suggestions">
			<p class="ai-suggestions-label"><?php esc_html_e( 'Ask me', 'codesblock' ); ?></p>
			<div class="ai-suggestion-chips">
				<?php foreach ( $ai_suggestions as $s ) : ?>
					<button class="ai-suggestion-chip" type="button"><?php echo esc_html( $s ); ?></button>
				<?php endforeach; ?>
			</div>
		</div>
	</div>

	<!-- Chat tab -->
	<div class="ai-tutor-tab-content" id="ai-tab-chat" role="tabpanel" aria-labelledby="ai-tab-chat-control" hidden>
		<div class="ai-chat-messages" id="ai-chat-messages" aria-live="polite" aria-label="<?php esc_attr_e( 'Chat messages', 'codesblock' ); ?>">
			<!-- Greeting message -->
			<div class="chat-msg chat-msg-ai">
				<div class="chat-avatar" aria-hidden="true">AI</div>
				<div class="chat-bubble">
					<?php
					printf(
						/* translators: %s course title */
						esc_html__( "Welcome to the course guide for %s. Ask about prerequisites, what you'll learn, pricing, or the course outline.", 'codesblock' ),
						esc_html( get_the_title() )
					);
					?>
				</div>
			</div>
		</div>

		<!-- Suggestion chips in chat tab -->
		<div class="ai-suggestions">
			<p class="ai-suggestions-label"><?php esc_html_e( 'Try asking', 'codesblock' ); ?></p>
			<div class="ai-suggestion-chips">
				<?php foreach ( array_slice( $ai_suggestions, 0, 3 ) as $s ) : ?>
					<button class="ai-suggestion-chip" type="button"><?php echo esc_html( $s ); ?></button>
				<?php endforeach; ?>
			</div>
		</div>

		<!-- Input area -->
		<div class="ai-chat-input-area">
			<label class="screen-reader-text" for="ai-chat-input"><?php esc_html_e( 'Your message', 'codesblock' ); ?></label>
			<textarea
				id="ai-chat-input"
				class="ai-chat-input"
				rows="2"
				placeholder="<?php esc_attr_e( 'Ask about this course…', 'codesblock' ); ?>"
				aria-label="<?php esc_attr_e( 'Ask the Course Guide', 'codesblock' ); ?>"
			></textarea>
			<button
				id="ai-chat-send"
				class="ai-chat-send"
				type="button"
				aria-label="<?php esc_attr_e( 'Send message', 'codesblock' ); ?>"
			>
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
			</button>
		</div>
	</div>
</div>
<?php endif; ?>

<?php get_footer(); ?>
