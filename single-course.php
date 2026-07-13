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
$course_preview = get_the_excerpt() ? get_the_excerpt() : wp_trim_words( wp_strip_all_tags( get_the_content() ), 45 );

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

/* ── Course guide suggestion chips (shown in panel) ─────────── */
$ai_suggestions = array(
	__( 'What will I learn?', 'codesblock' ),
	__( 'How long does it take?', 'codesblock' ),
	__( 'What level is this course?', 'codesblock' ),
	__( 'What\'s the price?', 'codesblock' ),
	__( 'What are the prerequisites?', 'codesblock' ),
);
?>

<main id="main" class="single-course-wrap">

	<!-- ══════════════════════════════════════════════════
	     COURSE HERO BANNER
	══════════════════════════════════════════════════ -->
	<section class="course-hero-banner">
		<div class="container">
			<div class="course-hero-inner">

				<!-- Left: text -->
				<div class="course-hero-content">
					<?php if ( $badge ) : ?>
						<span class="course-badge-hero"><?php echo esc_html( $badge ); ?></span>
					<?php endif; ?>

					<h1><?php the_title(); ?></h1>
					<p class="course-subtitle"><?php echo esc_html( get_the_excerpt() ); ?></p>

					<div class="course-hero-meta">
						<?php if ( $level ) : ?>
							<span>
								<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
								<strong><?php echo esc_html( $level ); ?></strong>
							</span>
						<?php endif; ?>
						<?php if ( $duration ) : ?>
							<span>
								<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
								<strong><?php echo esc_html( $duration ); ?></strong>
							</span>
						<?php endif; ?>
						<span>
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
							<strong><?php esc_html_e( 'Interactive course guide', 'codesblock' ); ?></strong>
						</span>
					</div>
				</div>

				<!-- Right: sticky enrollment card -->
				<aside aria-label="<?php esc_attr_e( 'Course enrollment', 'codesblock' ); ?>">
					<div class="course-sticky-card">
						<!-- Thumbnail -->
						<div class="sticky-card-thumb">
							<?php if ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'large' ); ?>
							<?php else : ?>
								<div style="height:100%;background:linear-gradient(135deg,#101828,#173a8a);"></div>
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
								<a class="btn-enroll" id="btn-enroll-main" href="#course-progress"><?php esc_html_e( 'Continue learning', 'codesblock' ); ?> &rarr;</a>
							<?php elseif ( $codesblock_is_frontend_member ) : ?>
								<a class="btn-enroll" id="btn-enroll-main" href="<?php echo esc_url( function_exists( 'cbcommerce_checkout_url' ) ? cbcommerce_checkout_url( 'pro' ) : home_url( '/#member' ) ); ?>"><?php esc_html_e( 'Upgrade to unlock', 'codesblock' ); ?> &rarr;</a>
							<?php elseif ( $codesblock_is_admin_session ) : ?>
								<a class="btn-enroll" id="btn-enroll-main" href="<?php echo esc_url( get_edit_post_link( $post_id ) ); ?>"><?php esc_html_e( 'Edit course in WP Admin', 'codesblock' ); ?> &rarr;</a>
							<?php else : ?>
								<button
									class="btn-enroll js-open-paywall"
									id="btn-enroll-main"
									aria-haspopup="dialog"
									aria-controls="paywall-overlay"
								>
									<?php esc_html_e( 'Enroll Now', 'codesblock' ); ?> &rarr;
								</button>
							<?php endif; ?>

							<p class="enroll-guarantee">
								<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
								<?php echo esc_html( $is_free ? __( 'No payment required', 'codesblock' ) : __( 'Clear pricing before checkout', 'codesblock' ) ); ?>
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

	<!-- ══════════════════════════════════════════════════
	     MAIN CONTENT LAYOUT
	══════════════════════════════════════════════════ -->
	<div class="container">
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

				<!-- About this Course -->
				<section class="course-section" aria-labelledby="about-heading">
					<h2 id="about-heading"><?php esc_html_e( 'About This Course', 'codesblock' ); ?></h2>
					<?php if ( $user_has_access ) : ?>
						<div class="entry-content"><?php the_content(); ?></div>
					<?php else : ?>
						<!-- Paywall gate: only the server-generated preview reaches the browser. -->
						<div class="content-gate-wrapper">
							<div class="content-gate-blur entry-content">
								<p><?php echo esc_html( $course_preview ); ?></p>
							</div>
							<div class="content-gate-overlay">
								<h3><?php esc_html_e( 'Enroll to read the full course overview', 'codesblock' ); ?></h3>
								<p><?php esc_html_e( 'Join Pro or Lifetime to unlock the complete course and member learning tools.', 'codesblock' ); ?></p>
								<button class="button button-primary js-open-paywall" aria-haspopup="dialog" aria-controls="paywall-overlay">
									<?php esc_html_e( 'Unlock Access', 'codesblock' ); ?>
								</button>
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
						ob_start();

						foreach ( $lines as $raw ) {
							$line = trim( $raw );
							if ( empty( $line ) ) continue;

							if ( strpos( $line, '##' ) === 0 ) {
								/* Close previous module */
								if ( $in_module ) echo '</ul></div>';
								$title = esc_html( trim( substr( $line, 2 ) ) );
								echo '<div class="syllabus-module">';
								echo '<button class="syllabus-module-header" type="button" aria-expanded="false">';
								echo '<span>' . $title . '</span>';
								echo '<svg class="syllabus-chevron" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>';
								echo '</button>';
								echo '<ul class="syllabus-module-lessons" role="list">';
								$in_module = true;
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

				<!-- Newsletter mini box -->
				<div class="course-section" style="margin-bottom:20px;">
					<p class="eyebrow"><?php esc_html_e( 'Stay Updated', 'codesblock' ); ?></p>
					<h2 style="font-size:1.1rem;margin-bottom:8px;"><?php esc_html_e( 'Get new course alerts', 'codesblock' ); ?></h2>
					<p style="color:var(--muted);font-size:.85rem;margin-bottom:14px;"><?php esc_html_e( 'Be first to know when new courses and free lessons drop.', 'codesblock' ); ?></p>
					<?php
					/* If Newsletter plugin active: echo do_shortcode('[newsletter]'); */
					/* If Mailchimp for WP active: echo do_shortcode('[mc4wp_form id="YOUR_FORM_ID"]'); */
					?>
					<form class="cb-newsletter-form" style="display:flex;flex-direction:column;gap:8px;" action="#" method="post" novalidate>
						<label class="screen-reader-text" for="nl-email-sidebar"><?php esc_html_e( 'Email address', 'codesblock' ); ?></label>
						<input
							id="nl-email-sidebar"
							type="email"
							name="email"
							autocomplete="email"
							inputmode="email"
							placeholder="<?php esc_attr_e( 'you@example.com', 'codesblock' ); ?>"
							style="border:1px solid var(--line);border-radius:8px;padding:9px 12px;font:inherit;width:100%;outline:none;"
							required
						>
						<button
							type="submit"
							class="button button-primary"
							style="width:100%;justify-content:center;"
						>
							<?php esc_html_e( 'Notify Me', 'codesblock' ); ?>
						</button>
						<input class="cb-honeypot" type="text" name="company" tabindex="-1" autocomplete="off" aria-hidden="true">
						<p class="cb-form-feedback" role="status" aria-live="polite"></p>
					</form>
				</div>

				<!-- Social share -->
				<div class="course-section">
					<strong style="display:block;margin-bottom:12px;font-size:.9rem;"><?php esc_html_e( 'Share this course', 'codesblock' ); ?></strong>
					<div style="display:flex;gap:10px;">
						<a
							href="https://twitter.com/intent/tweet?text=<?php echo rawurlencode( get_the_title() . ' — ' . get_permalink() ); ?>"
							target="_blank"
							rel="noreferrer"
							class="btn-view-course"
							style="flex:1;text-align:center;"
						>Twitter / X</a>
						<a
							href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo rawurlencode( get_permalink() ); ?>"
							target="_blank"
							rel="noreferrer"
							class="btn-view-course"
							style="flex:1;text-align:center;background:#0a66c2;"
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
	aria-expanded="false"
	aria-controls="ai-tutor-panel"
	aria-label="<?php esc_attr_e( 'Open Course Guide', 'codesblock' ); ?>"
>
	<div class="ai-tutor-toggle-inner">
		<div class="ai-tutor-pulse" aria-hidden="true"></div>
		<span style="writing-mode:vertical-rl;text-orientation:mixed;letter-spacing:.05em;">Guide</span>
	</div>
</button>

<!-- Slide-in panel -->
<div
	id="ai-tutor-panel"
	class="ai-tutor-panel"
	role="dialog"
	aria-modal="false"
	aria-label="<?php esc_attr_e( 'Course Guide', 'codesblock' ); ?>"
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
			aria-label="<?php esc_attr_e( 'Close Course Guide', 'codesblock' ); ?>"
		>&#x2715;</button>
	</div>

	<!-- Tabs -->
	<div class="ai-tutor-tabs" role="tablist">
		<button class="ai-tutor-tab is-active" data-tab="summary" role="tab" aria-selected="true"><?php esc_html_e( 'Summary', 'codesblock' ); ?></button>
		<button class="ai-tutor-tab" data-tab="chat" role="tab" aria-selected="false"><?php esc_html_e( 'Ask Guide', 'codesblock' ); ?></button>
	</div>

	<!-- Summary tab -->
	<div class="ai-tutor-tab-content is-active" id="ai-tab-summary" role="tabpanel">
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
	<div class="ai-tutor-tab-content" id="ai-tab-chat" role="tabpanel">
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
				aria-label="<?php esc_attr_e( 'Send message', 'codesblock' ); ?>"
			>
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
			</button>
		</div>
	</div>
</div>
<?php endif; ?>

<?php get_footer(); ?>
