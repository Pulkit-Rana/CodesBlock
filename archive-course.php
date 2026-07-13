<?php
/**
 * Course archive / listing page.
 *
 * @package CodesBlock
 */

get_header();
?>
<main id="main" class="archive-main">

	<!-- ── Hero ──────────────────────────────────────────────── -->
	<section class="course-archive-hero">
		<div class="container">
			<p class="eyebrow"><?php esc_html_e( 'Level Up Your Career', 'codesblock' ); ?></p>
			<h1><?php esc_html_e( 'Master Software Engineering', 'codesblock' ); ?></h1>
			<p><?php esc_html_e( 'Hands-on courses with AI-powered guidance — from foundations to senior-level mastery.', 'codesblock' ); ?></p>

			<!-- Filter pills -->
			<div class="course-filter-pills" role="group" aria-label="<?php esc_attr_e( 'Filter courses by level', 'codesblock' ); ?>">
				<button class="filter-pill is-active" data-filter="all"><?php esc_html_e( 'All Courses', 'codesblock' ); ?></button>
				<button class="filter-pill" data-filter="beginner"><?php esc_html_e( 'Beginner', 'codesblock' ); ?></button>
				<button class="filter-pill" data-filter="intermediate"><?php esc_html_e( 'Intermediate', 'codesblock' ); ?></button>
				<button class="filter-pill" data-filter="advanced"><?php esc_html_e( 'Advanced', 'codesblock' ); ?></button>
				<button class="filter-pill" data-filter="free"><?php esc_html_e( 'Free', 'codesblock' ); ?></button>
			</div>
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
									<?php the_post_thumbnail( 'medium_large' ); ?>
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
