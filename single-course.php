<?php
/**
 * Single Course page.
 * Includes: course hero, detailed info, interactive course guide, and newsletter.
 *
 * @package CodesBlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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

$lessons_array   = function_exists( 'cbcore_get_course_lessons' ) ? cbcore_get_course_lessons( $post_id ) : array();
$lesson_count    = count( $lessons_array );
$course_metrics  = function_exists( 'cbcore_get_engagement_metrics' ) ? cbcore_get_engagement_metrics( $post_id ) : array( 'views' => 0, 'rating_average' => 0, 'rating_count' => 0 );
$course_rating   = ! empty( $course_metrics['rating_count'] ) ? number_format_i18n( $course_metrics['rating_average'], 1 ) : __( 'New', 'codesblock' );
$users_learning  = function_exists( 'cbcore_get_course_learners_count' ) ? cbcore_get_course_learners_count( $post_id ) : 0;

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
$codesblock_resume_url         = $codesblock_can_track_progress && function_exists( 'codesblock_course_resume_url' ) ? codesblock_course_resume_url( $post_id ) : '';
if ( $codesblock_resume_url ) {
	$codesblock_start_destination = $codesblock_resume_url;
}
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

/* ── Landing data derived from the saved curriculum and content ─ */
$codesblock_outline       = function_exists( 'codesblock_parse_course_syllabus' ) ? codesblock_parse_course_syllabus( (string) $syllabus ) : array( 'modules' => array(), 'totals' => array( 'modules' => 0, 'items' => 0 ) );
$codesblock_totals        = $codesblock_outline['totals'];
$codesblock_modules       = $codesblock_outline['modules'];
$codesblock_content_parts = get_extended( (string) get_post_field( 'post_content', $post_id ) );
$codesblock_has_more      = '' !== trim( (string) $codesblock_content_parts['extended'] );
$codesblock_faqs_raw      = (string) get_post_meta( $post_id, '_course_ai_faqs', true );
$codesblock_faqs          = $codesblock_faqs_raw ? json_decode( $codesblock_faqs_raw, true ) : array();
$codesblock_faqs          = is_array( $codesblock_faqs ) ? array_values(
	array_filter(
		$codesblock_faqs,
		function ( $faq ) {
			return is_array( $faq ) && ! empty( $faq['q'] ) && ! empty( $faq['a'] );
		}
	)
) : array();
$codesblock_feature_list  = $features ? array_values( array_filter( array_map( 'trim', explode( "\n", (string) $features ) ) ) ) : array();
$codesblock_learn_list    = $what_you_learn ? array_values( array_filter( array_map( 'trim', explode( "\n", (string) $what_you_learn ) ) ) ) : array();
$codesblock_kind_labels   = array(
	'lab'         => __( 'Lab', 'codesblock' ),
	'drill'       => __( '10x drill', 'codesblock' ),
	'fix'         => __( 'Debug lab', 'codesblock' ),
	'mock'        => __( 'Mock interview', 'codesblock' ),
	'case'        => __( 'Case study', 'codesblock' ),
	'capstone'    => __( 'Capstone', 'codesblock' ),
	'deliverable' => __( 'Deliverable', 'codesblock' ),
);
$codesblock_hands_on      = 0;
foreach ( array( 'lab', 'drill', 'fix', 'case', 'capstone' ) as $codesblock_kind ) {
	$codesblock_hands_on += isset( $codesblock_totals[ $codesblock_kind ] ) ? (int) $codesblock_totals[ $codesblock_kind ] : 0;
}
$codesblock_mocks = isset( $codesblock_totals['mock'] ) ? (int) $codesblock_totals['mock'] : 0;

/**
 * Print the visitor-appropriate enrolment action.
 *
 * @param string $extra_class Additional button classes.
 * @param bool   $is_primary  Whether this is the main card button (receives the stable ID).
 */
$codesblock_render_cta = function ( $extra_class = '', $is_primary = false ) use ( $post_id, $is_free, $codesblock_can_track_progress, $codesblock_course_progress, $codesblock_start_destination, $codesblock_is_admin_session, $codesblock_is_frontend_member, $codesblock_payments_ready, $codesblock_course_start_url, $codesblock_billing_url ) {
	$id_attr = $is_primary ? ' id="btn-enroll-main"' : '';
	$classes = trim( 'btn-enroll ' . $extra_class );

	if ( $codesblock_can_track_progress ) {
		$label = $codesblock_course_progress ? __( 'Continue learning', 'codesblock' ) : __( 'Start course', 'codesblock' );
		printf(
			'<a class="%1$s js-start-course"%2$s href="%3$s" data-course-id="%4$s" data-course-target="#about-heading" data-course-destination="%3$s">%5$s &rarr;</a>',
			esc_attr( $classes ),
			$id_attr, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static attribute.
			esc_url( $codesblock_start_destination ),
			esc_attr( $post_id ),
			esc_html( $label )
		);
		return;
	}

	if ( $codesblock_is_admin_session ) {
		printf(
			'<a class="%1$s"%2$s href="%3$s">%4$s &rarr;</a>',
			esc_attr( $classes ),
			$id_attr, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static attribute.
			esc_url( (string) get_edit_post_link( $post_id ) ),
			esc_html__( 'Edit course in WP Admin', 'codesblock' )
		);
		return;
	}

	$course_title = wp_strip_all_tags( get_the_title( $post_id ) );
	if ( $codesblock_is_frontend_member ) {
		/* Signed in, but this paid course needs a pass. */
		$trigger  = 'js-open-paywall';
		$target   = 'paywall-overlay';
		$label    = __( 'Get full access', 'codesblock' );
		$context  = '';
	} else {
		$trigger  = 'js-open-member';
		$target   = 'member-overlay';
		$label    = $is_free ? __( 'Start learning free', 'codesblock' ) : ( $codesblock_payments_ready ? __( 'Sign in to buy', 'codesblock' ) : __( 'Sign in for launch updates', 'codesblock' ) );
		$context  = sprintf(
			' data-member-kicker="%1$s" data-member-title="%2$s" data-member-redirect="%3$s"',
			esc_attr( $course_title ),
			esc_attr( $is_free ? __( 'Start this course with a free account', 'codesblock' ) : __( 'Sign in to choose your pass', 'codesblock' ) ),
			esc_url( $is_free ? $codesblock_course_start_url : $codesblock_billing_url )
		);
	}

	printf(
		'<button class="%1$s %2$s" type="button"%3$s aria-haspopup="dialog" aria-controls="%4$s"%5$s>%6$s &rarr;</button>',
		esc_attr( $classes ),
		esc_attr( $trigger ),
		$id_attr, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static attribute.
		esc_attr( $target ),
		$context, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped above.
		esc_html( $label )
	);
};
?>

<main id="main" class="single-course-wrap cbl-page-wrap">
	<div class="container cbl-page">

		<!-- ══════════════ HERO ══════════════ -->
		<section class="cbl-hero" aria-labelledby="cbl-title">
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
			<div class="cbl-hero-tags">
				<?php if ( $badge ) : ?>
					<span class="cbl-pill cbl-pill-accent"><?php echo esc_html( $badge ); ?></span>
				<?php endif; ?>
				<?php if ( $level ) : ?>
					<span class="cbl-pill"><?php echo esc_html( $level ); ?></span>
				<?php endif; ?>
			</div>

			<h1 id="cbl-title"><?php the_title(); ?></h1>
			<?php if ( has_excerpt() ) : ?>
				<p class="cbl-hero-lead"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>

			<?php
			$codesblock_facts = array();
			if ( $codesblock_totals['modules'] ) {
				$codesblock_facts[] = array( number_format_i18n( $codesblock_totals['modules'] ), __( 'modules', 'codesblock' ) );
			}
			if ( $codesblock_totals['items'] ) {
				$codesblock_facts[] = array( number_format_i18n( $codesblock_totals['items'] ), __( 'lessons & labs', 'codesblock' ) );
			}
			if ( $codesblock_hands_on ) {
				$codesblock_facts[] = array( number_format_i18n( $codesblock_hands_on ), __( 'hands-on drills', 'codesblock' ) );
			}
			if ( $codesblock_mocks ) {
				$codesblock_facts[] = array( number_format_i18n( $codesblock_mocks ), __( 'scored mocks', 'codesblock' ) );
			}
			?>
			<?php if ( $codesblock_facts ) : ?>
				<dl class="cbl-facts">
					<?php foreach ( $codesblock_facts as $codesblock_fact ) : ?>
						<div><dt><?php echo esc_html( $codesblock_fact[0] ); ?></dt><dd><?php echo esc_html( $codesblock_fact[1] ); ?></dd></div>
					<?php endforeach; ?>
				</dl>
			<?php endif; ?>

			<a class="cbl-ai-flag" href="#ai-tutor-heading">
				<span class="cbl-ai-flag-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 3l1.8 4.6L18.5 9l-4.7 1.4L12 15l-1.8-4.6L5.5 9l4.7-1.4z"/><path d="M19 15l.8 2.2L22 18l-2.2.8L19 21l-.8-2.2L16 18l2.2-.8z"/></svg></span>
				<span><strong><?php esc_html_e( 'AI tutor built in.', 'codesblock' ); ?></strong> <?php esc_html_e( 'Highlight any line and ask, by text or voice.', 'codesblock' ); ?></span>
				<svg class="cbl-ai-flag-arrow" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
			</a>

			<div class="cbl-hero-actions">
				<?php $codesblock_render_cta( 'cbl-btn cbl-btn-primary' ); ?>
				<?php if ( $codesblock_modules ) : ?>
					<a class="cbl-btn cbl-btn-ghost" href="#syllabus-heading"><?php esc_html_e( 'See the curriculum', 'codesblock' ); ?></a>
				<?php endif; ?>
			</div>

			<ul class="cbl-hero-meta" role="list">
				<?php if ( $duration ) : ?>
					<li><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg><?php echo esc_html( $duration ); ?></li>
				<?php endif; ?>
				<?php if ( $lesson_count ) : ?>
					<li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12l4 4L19 6"/></svg><?php echo esc_html( sprintf( _n( '%s lesson live now', '%s lessons live now', $lesson_count, 'codesblock' ), number_format_i18n( $lesson_count ) ) ); ?></li>
				<?php endif; ?>
				<?php if ( ! empty( $course_metrics['rating_count'] ) ) : ?>
					<li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3l2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1-4.4-4.3 6.1-.9z"/></svg><?php echo esc_html( sprintf( __( '%1$s rating (%2$s)', 'codesblock' ), $course_rating, number_format_i18n( $course_metrics['rating_count'] ) ) ); ?></li>
				<?php endif; ?>
				<?php if ( $users_learning >= 10 ) : ?>
					<li><span class="pulse-dot" aria-hidden="true"></span><?php echo esc_html( sprintf( __( '%s learners enrolled', 'codesblock' ), number_format_i18n( $users_learning ) ) ); ?></li>
				<?php endif; ?>
				<li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 12a8 8 0 1 1-2.3-5.7M20 4v5h-5"/></svg><?php echo esc_html( sprintf( __( 'Updated %s', 'codesblock' ), get_the_modified_date() ) ); ?></li>
			</ul>
		</section>

		<!-- ══════════════ ENROLMENT CARD (sticky) ══════════════ -->
		<aside class="cbl-aside" aria-label="<?php esc_attr_e( 'Course enrollment', 'codesblock' ); ?>">
			<div class="cbl-card" id="cbl-enrol">
				<div class="cbl-card-thumb">
					<?php if ( has_post_thumbnail() ) : ?>
						<?php the_post_thumbnail( 'large', array( 'loading' => 'eager', 'fetchpriority' => 'high' ) ); ?>
					<?php else : ?>
						<div class="sticky-card-placeholder" aria-hidden="true"><span>CB</span></div>
					<?php endif; ?>
				</div>
				<div class="cbl-card-body">
					<div class="cbl-price">
						<?php if ( $is_free ) : ?>
							<strong class="is-free"><?php esc_html_e( 'Free', 'codesblock' ); ?></strong>
							<span><?php esc_html_e( 'during early access', 'codesblock' ); ?></span>
						<?php elseif ( $price ) : ?>
							<strong><?php echo esc_html( $price ); ?></strong>
							<?php if ( $original_price && '00' !== trim( (string) $original_price ) ) : ?>
								<s><?php echo esc_html( $original_price ); ?></s>
							<?php endif; ?>
						<?php endif; ?>
					</div>

					<?php $codesblock_render_cta( 'cbl-btn cbl-btn-primary cbl-btn-block', true ); ?>

					<p class="cbl-card-note">
						<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
						<?php echo esc_html( $is_free ? __( 'No card needed · Sign in with Google', 'codesblock' ) : __( 'One-time passes. No automatic renewal.', 'codesblock' ) ); ?>
					</p>

					<p class="cbl-card-ai">
						<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3l1.8 4.6L18.5 9l-4.7 1.4L12 15l-1.8-4.6L5.5 9l4.7-1.4z"/></svg>
						<span><?php esc_html_e( 'Includes the AI tutor: ask by text or voice inside every lesson', 'codesblock' ); ?></span>
					</p>

					<?php if ( $codesblock_feature_list ) : ?>
						<p class="cbl-card-label"><?php esc_html_e( 'This course includes', 'codesblock' ); ?></p>
						<ul class="cbl-checks" role="list">
							<?php foreach ( array_slice( $codesblock_feature_list, 0, 6 ) as $codesblock_feature ) : ?>
								<li><?php echo esc_html( $codesblock_feature ); ?></li>
							<?php endforeach; ?>
						</ul>
						<?php if ( count( $codesblock_feature_list ) > 6 ) : ?>
							<details class="cbl-more">
								<summary><?php echo esc_html( sprintf( __( '+ %d more', 'codesblock' ), count( $codesblock_feature_list ) - 6 ) ); ?></summary>
								<ul class="cbl-checks" role="list">
									<?php foreach ( array_slice( $codesblock_feature_list, 6 ) as $codesblock_feature ) : ?>
										<li><?php echo esc_html( $codesblock_feature ); ?></li>
									<?php endforeach; ?>
								</ul>
							</details>
						<?php endif; ?>
					<?php endif; ?>

					<div class="cbl-share">
						<span><?php esc_html_e( 'Share', 'codesblock' ); ?></span>
						<a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo rawurlencode( get_permalink() ); ?>" target="_blank" rel="noopener noreferrer">LinkedIn</a>
						<a href="https://twitter.com/intent/tweet?text=<?php echo rawurlencode( get_the_title() . ' — ' . get_permalink() ); ?>" target="_blank" rel="noopener noreferrer">X</a>
					</div>
				</div>
			</div>
		</aside>

		<!-- ══════════════ MAIN COLUMN ══════════════ -->
		<div class="cbl-main">

			<?php get_template_part( 'template-parts/course-ai-tutor', null, array( 'course_id' => $post_id, 'modules' => $codesblock_modules ) ); ?>

			<?php if ( $codesblock_learn_list ) : ?>
				<section class="cbl-section" aria-labelledby="learn-heading">
					<p class="cbl-eyebrow"><?php esc_html_e( 'Outcomes', 'codesblock' ); ?></p>
					<h2 id="learn-heading"><?php esc_html_e( "What you'll be able to do", 'codesblock' ); ?></h2>
					<ul class="cbl-learn" role="list">
						<?php foreach ( $codesblock_learn_list as $codesblock_item ) : ?>
							<li><?php echo esc_html( $codesblock_item ); ?></li>
						<?php endforeach; ?>
					</ul>
				</section>
			<?php endif; ?>

			<?php if ( $codesblock_modules ) : ?>
				<?php
				get_template_part(
					'template-parts/course-curriculum',
					null,
					array(
						'modules'     => $codesblock_modules,
						'totals'      => $codesblock_totals,
						'kind_labels' => $codesblock_kind_labels,
					)
				);
				?>
			<?php endif; ?>

			<?php if ( '' !== trim( (string) $codesblock_content_parts['main'] ) ) : ?>
				<section class="cbl-landing entry-content" aria-labelledby="about-heading">
					<h2 id="about-heading" class="screen-reader-text"><?php esc_html_e( 'About this course', 'codesblock' ); ?></h2>
					<?php
					/* Content before the WordPress "More" block is the public course story. */
					echo apply_filters( 'the_content', $codesblock_content_parts['main'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Editor-authored post content filtered by WordPress.
					?>
				</section>
			<?php elseif ( $ai_summary ) : ?>
				<section class="cbl-section" aria-labelledby="about-heading">
					<p class="cbl-eyebrow"><?php esc_html_e( 'About this course', 'codesblock' ); ?></p>
					<h2 id="about-heading"><?php esc_html_e( 'Course overview', 'codesblock' ); ?></h2>
					<p><?php echo esc_html( $ai_summary ); ?></p>
				</section>
			<?php endif; ?>

			<?php if ( $codesblock_has_more ) : ?>
				<section class="cbl-section <?php echo $user_has_access ? 'course-material-section' : 'course-access-section'; ?>" aria-labelledby="material-heading">
					<h2 id="material-heading"><?php echo esc_html( $user_has_access ? __( 'Course material', 'codesblock' ) : __( 'Course access', 'codesblock' ) ); ?></h2>
					<?php if ( $user_has_access ) : ?>
						<div class="entry-content">
							<?php echo apply_filters( 'the_content', $codesblock_content_parts['extended'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Editor-authored post content filtered by WordPress. ?>
						</div>
					<?php else : ?>
						<p class="cbl-muted"><?php esc_html_e( 'Open the lessons and materials with a CodesBlock access pass. Progress saves as you learn.', 'codesblock' ); ?></p>
						<?php $codesblock_render_cta( 'cbl-btn cbl-btn-primary' ); ?>
					<?php endif; ?>
				</section>
			<?php endif; ?>

			<?php if ( $codesblock_faqs ) : ?>
				<section class="cbl-section cbl-faq" aria-labelledby="faq-heading">
					<p class="cbl-eyebrow"><?php esc_html_e( 'FAQ', 'codesblock' ); ?></p>
					<h2 id="faq-heading"><?php esc_html_e( 'Questions before you start', 'codesblock' ); ?></h2>
					<?php foreach ( $codesblock_faqs as $codesblock_faq ) : ?>
						<details>
							<summary><?php echo esc_html( $codesblock_faq['q'] ); ?><svg class="cbl-chevron" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg></summary>
							<p><?php echo esc_html( $codesblock_faq['a'] ); ?></p>
						</details>
					<?php endforeach; ?>
				</section>
			<?php endif; ?>

			<section class="cbl-final" aria-labelledby="cbl-final-heading">
				<div>
					<h2 id="cbl-final-heading"><?php echo esc_html( sprintf( __( 'Start %s today', 'codesblock' ), get_the_title() ) ); ?></h2>
					<p><?php echo esc_html( $is_free ? __( 'Free during early access. Sign in, open Module 0 and pick up where you left off.', 'codesblock' ) : __( 'Choose a pass and open the lessons. Your progress saves as you learn.', 'codesblock' ) ); ?></p>
				</div>
				<?php $codesblock_render_cta( 'cbl-btn cbl-btn-light' ); ?>
			</section>

		</div>
	</div>

	<!-- Mobile purchase bar -->
	<div class="cbl-mobile-bar" data-cbl-mobile-bar>
		<div>
			<strong><?php echo esc_html( $is_free ? __( 'Free', 'codesblock' ) : (string) $price ); ?></strong>
			<span><?php echo esc_html( get_the_title() ); ?></span>
		</div>
		<?php $codesblock_render_cta( 'cbl-btn cbl-btn-primary' ); ?>
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
