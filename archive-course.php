<?php
/**
 * Course archive / listing page.
 *
 * @package CodesBlock
 */

$course_count        = (int) wp_count_posts( 'course' )->publish;
$free_course_query   = new WP_Query(
	array(
		'post_type'      => 'course',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'meta_query'     => array(
			'relation' => 'OR',
			array(
				'key'     => '_course_price',
				'value'   => 'Free',
				'compare' => '=',
			),
			array(
				'key'     => '_course_price',
				'compare' => 'NOT EXISTS',
			),
		),
	)
);
$free_course_count   = (int) $free_course_query->found_posts;
$payments_ready      = function_exists( 'cbcommerce_payments_ready' ) && cbcommerce_payments_ready();
$pro_checkout_url    = function_exists( 'cbcommerce_checkout_url' ) ? cbcommerce_checkout_url( 'pro' ) : wp_registration_url();
$annual_checkout_url = function_exists( 'cbcommerce_checkout_url' ) ? cbcommerce_checkout_url( 'pro_annual' ) : wp_registration_url();
$lifetime_url        = function_exists( 'cbcommerce_checkout_url' ) ? cbcommerce_checkout_url( 'lifetime' ) : wp_registration_url();
$pro_level           = function_exists( 'cbcommerce_level_id' ) && class_exists( 'PMPro_Membership_Level' ) ? new PMPro_Membership_Level( cbcommerce_level_id( 'pro' ) ) : false;
$annual_level        = function_exists( 'cbcommerce_level_id' ) && class_exists( 'PMPro_Membership_Level' ) ? new PMPro_Membership_Level( cbcommerce_level_id( 'pro_annual' ) ) : false;
$lifetime_level      = function_exists( 'cbcommerce_level_id' ) && class_exists( 'PMPro_Membership_Level' ) ? new PMPro_Membership_Level( cbcommerce_level_id( 'lifetime' ) ) : false;
$pro_amount          = $pro_level ? (float) $pro_level->initial_payment : 999;
$annual_amount       = $annual_level ? (float) $annual_level->initial_payment : 8499;
$lifetime_amount     = $lifetime_level ? (float) $lifetime_level->initial_payment : 19999;
$pro_price           = $pro_level && function_exists( 'pmpro_formatPrice' ) ? pmpro_formatPrice( $pro_amount ) : '₹999';
$annual_price        = $annual_level && function_exists( 'pmpro_formatPrice' ) ? pmpro_formatPrice( $annual_amount ) : '₹8,499';
$lifetime_price      = $lifetime_level && function_exists( 'pmpro_formatPrice' ) ? pmpro_formatPrice( $lifetime_amount ) : '₹19,999';
$annual_saving       = $pro_amount > 0 ? max( 0, (int) round( ( 1 - ( $annual_amount / ( $pro_amount * 12 ) ) ) * 100 ) ) : 0;
$is_admin_session    = function_exists( 'cbcommerce_user_can_access_admin' )
	? cbcommerce_user_can_access_admin()
	: current_user_can( 'manage_options' );
$member_learning_url = function_exists( 'cbcommerce_member_home_url' )
	? cbcommerce_member_home_url()
	: home_url( '/my-learning/' );

get_header();
?>
<main id="main" class="archive-main">

	<!-- ── Hero ──────────────────────────────────────────────── -->
	<section class="course-archive-hero">
		<div class="container">
			<p class="eyebrow"><?php esc_html_e( 'Learn by building', 'codesblock' ); ?></p>
			<h1><?php esc_html_e( 'Production AI, system design, and interview practice', 'codesblock' ); ?></h1>
			<p><?php esc_html_e( 'Focused paths with implementation labs, review checklists, and practical artifacts you can reuse at work or in interviews.', 'codesblock' ); ?></p>

			<div class="course-archive-proof" aria-label="<?php esc_attr_e( 'Course library summary', 'codesblock' ); ?>">
				<span><strong><?php echo esc_html( $course_count ); ?></strong> <?php esc_html_e( 'guided paths', 'codesblock' ); ?></span>
				<span><strong><?php echo esc_html( $free_course_count ); ?></strong> <?php esc_html_e( 'free starter', 'codesblock' ); ?></span>
				<span><strong><?php esc_html_e( 'Self-paced', 'codesblock' ); ?></strong> <?php esc_html_e( 'with saved progress', 'codesblock' ); ?></span>
			</div>

			<!-- Filter pills -->
			<div class="course-filter-pills" role="group" aria-label="<?php esc_attr_e( 'Filter courses by level', 'codesblock' ); ?>">
				<button class="filter-pill is-active" type="button" data-filter="all" aria-pressed="true"><?php esc_html_e( 'All Courses', 'codesblock' ); ?></button>
				<button class="filter-pill" type="button" data-filter="beginner" aria-pressed="false"><?php esc_html_e( 'Beginner', 'codesblock' ); ?></button>
				<button class="filter-pill" type="button" data-filter="intermediate" aria-pressed="false"><?php esc_html_e( 'Intermediate', 'codesblock' ); ?></button>
				<button class="filter-pill" type="button" data-filter="advanced" aria-pressed="false"><?php esc_html_e( 'Advanced', 'codesblock' ); ?></button>
				<button class="filter-pill" type="button" data-filter="free" aria-pressed="false"><?php esc_html_e( 'Free', 'codesblock' ); ?></button>
			</div>
			<p class="screen-reader-text" id="course-filter-status" aria-live="polite"></p>
		</div>
	</section>

	<!-- ── Course grid ───────────────────────────────────────── -->
	<section class="course-archive-section">
		<div class="container">
			<div class="course-archive-grid" id="course-grid">
				<?php
				if ( have_posts() ) :
					while ( have_posts() ) :
						the_post();

						$price          = get_post_meta( get_the_ID(), '_course_price', true );
						$original_price = get_post_meta( get_the_ID(), '_course_original_price', true );
						$level          = get_post_meta( get_the_ID(), '_course_level', true );
						$duration       = get_post_meta( get_the_ID(), '_course_duration', true );
						$badge          = get_post_meta( get_the_ID(), '_course_badge', true );
						$is_free        = ( 'free' === strtolower( (string) $price ) || '' === trim( (string) $price ) );

						/* Badge colour class */
						$badge_class = 'course-badge';
						if ( $badge ) {
							$bl = strtolower( $badge );
							if ( 'free' === $bl )                                     $badge_class .= ' course-badge-free';
							elseif ( in_array( $bl, array( 'hot', 'popular' ), true ) ) $badge_class .= ' course-badge-hot';
							elseif ( 'new' === $bl )                                  $badge_class .= ' course-badge-new';
						}

						/* Filter data attribute — level or "free" */
						$filter_level = $is_free ? 'free' : strtolower( (string) $level );
						?>
						<article
							id="course-<?php the_ID(); ?>"
							class="course-card-v2"
							data-level="<?php echo esc_attr( $filter_level ); ?>"
						>
							<!-- Thumbnail -->
							<a href="<?php the_permalink(); ?>" class="course-thumb" aria-label="<?php the_title_attribute(); ?>" tabindex="-1">
								<?php if ( has_post_thumbnail() ) : ?>
									<?php the_post_thumbnail( 'medium_large', array( 'style' => 'object-fit: cover; background: #fff; width: 100%; height: 100%;' ) ); ?>
								<?php elseif ( in_array( get_post_field( 'post_name', get_the_ID() ), array( 'system-design-interview-sprint', 'system-design-interview-lab' ), true ) ) : ?>
									<img class="system-design-cover" src="<?php echo esc_url( get_theme_file_uri( '/assets/images/system-design-interview-lab-cover.svg' ) ); ?>" alt="" loading="lazy" decoding="async">
								<?php else : ?>
									<div class="course-thumb-placeholder"><?php the_title(); ?></div>
								<?php endif; ?>
								<?php if ( $badge ) : ?>
									<span class="<?php echo esc_attr( $badge_class ); ?>"><?php echo esc_html( $badge ); ?></span>
								<?php endif; ?>
							</a>

							<!-- Card body -->
							<div class="card-body">
								<!-- Meta row -->
								<div class="card-meta-row">
									<?php if ( $level ) : ?>
										<span class="meta-chip">
											<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
											<?php echo esc_html( $level ); ?>
										</span>
									<?php endif; ?>
									<?php if ( $duration ) : ?>
										<span class="meta-chip">
											<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
											<?php echo esc_html( $duration ); ?>
										</span>
									<?php endif; ?>
								</div>

								<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
								<p class="card-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 16 ) ); ?></p>

								<!-- Footer -->
								<div class="card-footer">
									<div class="price-tag <?php echo esc_attr( $is_free ? 'price-free' : '' ); ?>">
										<?php if ( $is_free ) : ?>
											<?php esc_html_e( 'Free', 'codesblock' ); ?>
										<?php elseif ( $price ) : ?>
											<?php echo esc_html( $price ); ?>
											<?php if ( $original_price ) : ?>
												<span class="original"><?php echo esc_html( $original_price ); ?></span>
											<?php endif; ?>
										<?php endif; ?>
									</div>
									<a href="<?php the_permalink(); ?>" class="btn-view-course">
										<?php esc_html_e( 'View Course', 'codesblock' ); ?> &rarr;
									</a>
								</div>
							</div>
						</article>
						<?php
					endwhile;
				else :
					?>
					<p class="no-courses-msg"><?php esc_html_e( 'No courses found yet. Check back soon!', 'codesblock' ); ?></p>
					<?php
				endif;
				?>
			</div>

			<!-- Pagination -->
			<?php
			the_posts_pagination( array(
				'prev_text' => '&larr; ' . __( 'Previous', 'codesblock' ),
				'next_text' => __( 'Next', 'codesblock' ) . ' &rarr;',
			) );
			?>
		</div>
	</section>

	<!-- ── Newsletter strip ──────────────────────────────────── -->
	<section class="course-pricing-section" id="plans" aria-labelledby="course-pricing-title">
		<div class="container">
			<div class="course-pricing-heading">
				<div>
					<p class="eyebrow"><?php esc_html_e( 'Simple pricing', 'codesblock' ); ?></p>
					<h2 id="course-pricing-title"><?php esc_html_e( 'Start free. Choose a pass when you need more.', 'codesblock' ); ?></h2>
				</div>
				<p><?php esc_html_e( 'Every paid option is an upfront INR payment. Nothing renews automatically; extend access only when you decide.', 'codesblock' ); ?></p>
			</div>

			<div class="course-pricing-grid">
				<article class="course-plan-card">
					<span><?php esc_html_e( 'Starter', 'codesblock' ); ?></span>
					<h3><?php esc_html_e( 'Free', 'codesblock' ); ?></h3>
					<p><?php esc_html_e( 'Public articles, the free starter course, saved progress, and weekly learning notes.', 'codesblock' ); ?></p>
					<?php if ( ! is_user_logged_in() ) : ?>
						<a class="button button-secondary js-open-member" href="#member-overlay" data-member-view="register" aria-haspopup="dialog" aria-controls="member-overlay"><?php esc_html_e( 'Create free account', 'codesblock' ); ?></a>
					<?php elseif ( $is_admin_session ) : ?>
						<a class="button button-secondary" href="<?php echo esc_url( admin_url() ); ?>"><?php esc_html_e( 'Open WP Admin', 'codesblock' ); ?></a>
					<?php else : ?>
						<a class="button button-secondary" href="<?php echo esc_url( $member_learning_url ); ?>"><?php esc_html_e( 'Go to my learning', 'codesblock' ); ?></a>
					<?php endif; ?>
				</article>
				<article class="course-plan-card">
					<span><?php esc_html_e( '30-day pass', 'codesblock' ); ?></span>
					<h3><?php echo wp_kses_post( $pro_price ); ?> <small><?php esc_html_e( 'once', 'codesblock' ); ?></small></h3>
					<p><?php esc_html_e( 'Every paid course, implementation resources, certificates, and saved progress for 30 days.', 'codesblock' ); ?></p>
					<a class="button button-secondary" href="<?php echo esc_url( $pro_checkout_url ); ?>"><?php echo esc_html( $payments_ready ? __( 'Choose 30 days', 'codesblock' ) : __( 'Get launch update', 'codesblock' ) ); ?></a>
				</article>
				<article class="course-plan-card is-featured">
					<span><?php echo esc_html( $annual_saving ? sprintf( __( 'Save %d%%', 'codesblock' ), $annual_saving ) : __( 'Best value', 'codesblock' ) ); ?></span>
					<h3><?php echo wp_kses_post( $annual_price ); ?> <small><?php esc_html_e( 'once', 'codesblock' ); ?></small></h3>
					<p><?php esc_html_e( 'A full year of courses, implementation resources, certificates, and saved progress.', 'codesblock' ); ?></p>
					<a class="button button-primary" href="<?php echo esc_url( $annual_checkout_url ); ?>"><?php echo esc_html( $payments_ready ? __( 'Choose 1 year', 'codesblock' ) : __( 'Get launch update', 'codesblock' ) ); ?></a>
				</article>
				<article class="course-plan-card">
					<span><?php esc_html_e( 'Founding Lifetime', 'codesblock' ); ?></span>
					<h3><?php echo wp_kses_post( $lifetime_price ); ?> <small><?php esc_html_e( 'once', 'codesblock' ); ?></small></h3>
					<p><?php esc_html_e( 'Permanent access to current and future CodesBlock courses for early supporters.', 'codesblock' ); ?></p>
					<a class="button button-secondary" href="<?php echo esc_url( $lifetime_url ); ?>"><?php echo esc_html( $payments_ready ? __( 'Choose lifetime', 'codesblock' ) : __( 'Get launch update', 'codesblock' ) ); ?></a>
				</article>
			</div>
			<p class="course-coupon-callout">
				<?php if ( $payments_ready ) : ?>
					<strong><?php esc_html_e( 'Launch coupon:', 'codesblock' ); ?></strong> <?php esc_html_e( 'Use WELCOME25 to save 25% on the 30-day pass. Limited to 100 redemptions.', 'codesblock' ); ?>
				<?php else : ?>
					<strong><?php esc_html_e( 'Paid enrollment opens after gateway testing.', 'codesblock' ); ?></strong> <?php esc_html_e( 'Free membership is available now. Paid pass buttons will activate only after checkout and webhook verification.', 'codesblock' ); ?>
				<?php endif; ?>
			</p>
		</div>
	</section>

	<section class="section">
		<div class="container">
			<div class="newsletter-strip">
				<h2><?php esc_html_e( 'Get one coding insight every week', 'codesblock' ); ?></h2>
				<p><?php esc_html_e( 'AI-assisted learning prompts, interview tips, and early course access — straight to your inbox.', 'codesblock' ); ?></p>
				<?php
				/* If the Newsletter plugin or Mailchimp for WP is active,
				   replace the form shortcode below with the plugin's shortcode.
				   Example: echo do_shortcode('[newsletter]');
				   Example: echo do_shortcode('[mc4wp_form id="YOUR_ID"]');
				*/
				?>
				<form class="newsletter-form cb-newsletter-form" action="#" method="post" novalidate>
					<label class="screen-reader-text" for="nl-email-archive"><?php esc_html_e( 'Email address', 'codesblock' ); ?></label>
					<input id="nl-email-archive" type="email" name="email" autocomplete="email" inputmode="email" placeholder="<?php esc_attr_e( 'you@example.com', 'codesblock' ); ?>" required>
					<input class="cb-honeypot" type="text" name="company" tabindex="-1" autocomplete="off" aria-hidden="true">
					<button type="submit"><?php esc_html_e( 'Subscribe Free', 'codesblock' ); ?></button>
					<p class="cb-form-feedback" role="status" aria-live="polite"></p>
				</form>
				<p class="newsletter-trust-row">
					<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
					<?php esc_html_e( 'No spam. Unsubscribe anytime.', 'codesblock' ); ?>
				</p>
			</div>
		</div>
	</section>

</main>
<?php get_footer(); ?>
