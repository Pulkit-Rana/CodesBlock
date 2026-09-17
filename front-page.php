<?php
/**
 * Custom landing page.
 *
 * @package CodesBlock
 */

get_header();

if ( 'page' === get_option( 'show_on_front' ) && have_posts() ) {
	while ( have_posts() ) {
		the_post();

		$codesblock_front_page_id      = get_the_ID();
		$codesblock_front_page_content = trim( get_post_field( 'post_content', $codesblock_front_page_id ) );
		$codesblock_is_elementor_page  = (bool) get_post_meta( $codesblock_front_page_id, '_elementor_edit_mode', true );

		if ( $codesblock_is_elementor_page || '' !== $codesblock_front_page_content ) {
			?>
			<main id="main" class="elementor-front-page">
				<?php the_content(); ?>
			</main>
			<?php
			get_footer();
			return;
		}
	}

	rewind_posts();
}

$hero_eyebrow = get_theme_mod( 'codesblock_hero_eyebrow', 'System Design Mastery' );
$hero_title   = get_theme_mod( 'codesblock_hero_title', 'Crack the System Design Interview' );
$hero_lede    = get_theme_mod( 'codesblock_hero_lede', 'Classical distributed systems + the architecture behind modern AI products.' );
$codesblock_published_course_count = (int) wp_count_posts( 'course' )->publish;
$codesblock_published_post_count   = (int) wp_count_posts( 'post' )->publish;

$codesblock_recommended_articles = new WP_Query(
	array(
		'posts_per_page'      => 3,
		'post_status'         => 'publish',
		'ignore_sticky_posts' => true,
		'meta_key'            => '_codesblock_recommended',
		'meta_value'          => '1',
		'orderby'             => 'date',
		'order'               => 'DESC',
	)
);

$codesblock_recommended_courses = new WP_Query(
	array(
		'post_type'           => 'course',
		'posts_per_page'      => 3,
		'post_status'         => 'publish',
		'ignore_sticky_posts' => true,
		'meta_key'            => '_codesblock_recommended',
		'meta_value'          => '1',
		'orderby'             => 'date',
		'order'               => 'DESC',
	)
);

$codesblock_home_course_posts = array();
$codesblock_featured_courses  = new WP_Query(
	array(
		'post_type'           => 'course',
		'posts_per_page'      => 6,
		'post_status'         => 'publish',
		'ignore_sticky_posts' => true,
		'meta_key'            => '_codesblock_recommended',
		'meta_value'          => '1',
		'orderby'             => 'date',
		'order'               => 'DESC',
	)
);

if ( $codesblock_featured_courses->have_posts() ) {
	$codesblock_home_course_posts = $codesblock_featured_courses->posts;
}

if ( count( $codesblock_home_course_posts ) < 6 ) {
	$codesblock_latest_courses = new WP_Query(
		array(
			'post_type'           => 'course',
			'posts_per_page'      => 6 - count( $codesblock_home_course_posts ),
			'post_status'         => 'publish',
			'post__not_in'        => wp_list_pluck( $codesblock_home_course_posts, 'ID' ),
			'ignore_sticky_posts' => true,
			'orderby'             => 'date',
			'order'               => 'DESC',
		)
	);

	if ( $codesblock_latest_courses->have_posts() ) {
		$codesblock_home_course_posts = array_merge( $codesblock_home_course_posts, $codesblock_latest_courses->posts );
	}
}

wp_reset_postdata();

$codesblock_is_admin_session = function_exists( 'cbcommerce_user_can_access_admin' )
	? cbcommerce_user_can_access_admin()
	: current_user_can( 'manage_options' );
$codesblock_is_frontend_member = function_exists( 'cbcommerce_is_frontend_member' )
	? cbcommerce_is_frontend_member()
	: ( is_user_logged_in() && ! $codesblock_is_admin_session );
$codesblock_member_profile_url = function_exists( 'cbcommerce_member_profile_url' ) ? cbcommerce_member_profile_url() : home_url( '/my-learning/#profile' );
$codesblock_member_learning_url = function_exists( 'cbcommerce_member_home_url' ) ? cbcommerce_member_home_url() : home_url( '/my-learning/' );
?>

<main id="main">
	<section class="hero">
		<div class="container hero-grid">
			<div class="hero-copy">
				<p class="eyebrow"><?php echo esc_html( $hero_eyebrow ); ?></p>
				<h1><?php echo esc_html( $hero_title ); ?></h1>
				<p class="hero-lede"><?php echo esc_html( $hero_lede ); ?></p>
				<div class="hero-actions">
					<a class="button button-primary" href="<?php echo esc_url( get_theme_mod( 'codesblock_hero_primary_url', get_post_type_archive_link( 'course' ) ?: home_url( '/courses/' ) ) ); ?>"><?php echo esc_html( get_theme_mod( 'codesblock_hero_primary_label', 'Explore paths' ) ); ?></a>
					<a class="button button-secondary" href="<?php echo esc_url( get_theme_mod( 'codesblock_hero_secondary_url', home_url( '/articles/' ) ) ); ?>"><?php echo esc_html( get_theme_mod( 'codesblock_hero_secondary_label', 'Read free lessons' ) ); ?></a>
				</div>
				<div class="learning-search" aria-label="Search learning topics">
					<form class="hero-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get">
						<label class="screen-reader-text" for="hero-search">Search learning topics</label>
						<input id="hero-search" name="s" type="search" placeholder="Search distributed systems, AI architecture, load balancers...">
						<button type="submit">Search</button>
					</form>
					<div class="keyword-picker" aria-label="Popular learning keywords">
						<span>Popular:</span>
						<a href="<?php echo esc_url( home_url( '/?s=distributed%20systems' ) ); ?>">distributed systems</a>
						<a href="<?php echo esc_url( home_url( '/?s=AI%20architecture' ) ); ?>">AI architecture</a>
						<a href="<?php echo esc_url( home_url( '/?s=system%20design' ) ); ?>">system design</a>
						<a href="<?php echo esc_url( home_url( '/?s=microservices' ) ); ?>">microservices</a>
					</div>
				</div>
			
			</div>
			<div class="hero-sketch" aria-label="CodesBlock learning preview">
				<div class="sketch-label">System Design Path</div>
				<div class="sketch-card lesson-card">
					<p>Step-by-step Guides</p>
					<h2>Crack the System Design Interview</h2>
					<div class="lesson-line long"></div>
					<div class="lesson-line"></div>
					<div class="lesson-line short"></div>
				</div>
				<div class="sketch-card quiz-card">
					<span>Interactive Practice</span>
					<strong>Solve real-world engineering problems.</strong>
					<div class="choice active">Optimized Database Queries</div>
					<div class="choice">Scalable Architecture</div>
				</div>
				<div class="sketch-card code-card" style="padding-bottom: 24px;">
					<code>const score = evaluate(agent, cases);<br><br>if (score &lt; launchGate) {<br>&nbsp;&nbsp;rollback();<br>}</code>
				</div>
				<div class="sketch-card extra-card">
					<span>CodesBlock Community</span>
					<strong><span id="cb-course-counter"><?php echo esc_html( $codesblock_published_course_count ); ?></span> courses &middot; <span id="cb-article-counter"><?php echo esc_html( $codesblock_published_post_count ); ?></span> articles &middot; <span id="cb-learner-counter"><?php $u_count = count_users(); echo esc_html( $u_count['total_users'] ); ?></span> learners</strong>
					<p style="font-size: 0.8rem; color: #66788a; margin-top: 6px;"><?php esc_html_e( 'Join our growing community of developers.', 'codesblock' ); ?></p>
				</div>
			</div>
		</div>
	</section>

	<section class="learning-strip" id="start">
		<div class="container strip-grid">
			<a class="strip-item" href="<?php echo esc_url( codesblock_articles_url() ); ?>" aria-label="Go to Articles">
				<span>01</span>
				<h2>Articles</h2>
				<p>Sharp explainers, patterns, and career notes you can read between focused work sessions.</p>
				<span class="strip-arrow" aria-hidden="true">&rarr;</span>
			</a>
			<a class="strip-item" href="<?php echo esc_url( home_url( '/courses/' ) ); ?>" aria-label="Go to Paid Courses">
				<span>02</span>
				<h2>Paid Courses</h2>
				<p>Structured learning paths with practice sets, checkpoints, and deeper implementation notes.</p>
				<span class="strip-arrow" aria-hidden="true">&rarr;</span>
			</a>
			<a class="strip-item" href="<?php echo esc_url( home_url( '/courses/?type=free' ) ); ?>" aria-label="Go to Free Courses">
				<span>03</span>
				<h2>Free Courses</h2>
				<p>Starter tracks and public lessons to build momentum before choosing a deeper path.</p>
				<span class="strip-arrow" aria-hidden="true">&rarr;</span>
			</a>
		</div>
	</section>

	<section class="section courses" id="courses">
		<div class="container">
			<div class="section-heading-split-cta section-heading-courses">
				<div class="courses-title-row">
					<div>
						<p class="eyebrow">Courses</p>
						<h2 class="courses-h2">Pick a path and keep moving.</h2>
					</div>
					<a class="button button-secondary explore-courses-btn" href="<?php echo esc_url( get_post_type_archive_link( 'course' ) ?: home_url( '/courses/' ) ); ?>" id="explore-all-courses">Explore All &rarr;</a>
				</div>
			<p class="courses-subhead">Focused tracks for practical developer learning &mdash; <strong>start with free courses</strong>, then upgrade only when a paid course fits your goals.</p>
			</div>
			<div class="courses-workspace">
				<div class="course-desk">
					<?php if ( $codesblock_home_course_posts ) : ?>
						<?php
						foreach ( $codesblock_home_course_posts as $codesblock_course_index => $post ) :
							setup_postdata( $post );

							$codesblock_course_id       = get_the_ID();
							$codesblock_course_price    = get_post_meta( $codesblock_course_id, '_course_price', true );
							$codesblock_course_original = get_post_meta( $codesblock_course_id, '_course_original_price', true );
							$codesblock_course_level    = get_post_meta( $codesblock_course_id, '_course_level', true );
							$codesblock_course_duration = get_post_meta( $codesblock_course_id, '_course_duration', true );
							$codesblock_course_badge    = get_post_meta( $codesblock_course_id, '_course_badge', true );
							$codesblock_is_free_course  = ( 'free' === strtolower( (string) $codesblock_course_price ) || '' === trim( (string) $codesblock_course_price ) );
							$codesblock_course_tag      = $codesblock_course_badge ? $codesblock_course_badge : ( $codesblock_is_free_course ? __( 'Free course', 'codesblock' ) : __( 'Paid course', 'codesblock' ) );
							$codesblock_course_summary  = get_the_excerpt() ? get_the_excerpt() : wp_strip_all_tags( get_the_content() );
							$codesblock_course_meta     = array_filter( array( $codesblock_course_duration, $codesblock_course_level ) );
							?>
							<a class="course-card course-card-link <?php echo 0 === $codesblock_course_index ? 'featured' : ''; ?>" href="<?php the_permalink(); ?>">
								<div class="course-card-inner static">
									<div class="course-card-face course-card-front">
										<?php if ( has_post_thumbnail() ) : ?>
											<div class="course-card-image" style="margin: -16px -16px 12px; border-radius: 10px 10px 0 0; overflow: hidden; aspect-ratio: 16/9; flex-shrink: 0; background: #fff;">
												<?php the_post_thumbnail( 'medium_large', array( 'style' => 'width: 100%; height: 100%; object-fit: cover; display: block;' ) ); ?>
											</div>
										<?php endif; ?>
										<p class="tag"><?php echo esc_html( $codesblock_course_tag ); ?></p>
										<h3><?php the_title(); ?></h3>
										<p class="card-desc"><?php echo esc_html( wp_trim_words( $codesblock_course_summary, 18 ) ); ?></p>
										<?php if ( $codesblock_course_meta ) : ?>
											<div class="course-meta">
												<?php foreach ( $codesblock_course_meta as $codesblock_course_meta_item ) : ?>
													<span><?php echo esc_html( $codesblock_course_meta_item ); ?></span>
												<?php endforeach; ?>
											</div>
										<?php endif; ?>
										<div class="card-footer-row">
											<span class="card-enrolled"><?php esc_html_e( 'Self-paced course', 'codesblock' ); ?></span>
											<?php if ( $codesblock_is_free_course ) : ?>
												<span class="card-price free-badge"><?php esc_html_e( 'Free', 'codesblock' ); ?></span>
											<?php elseif ( $codesblock_course_price ) : ?>
												<span class="card-price">
													<?php echo esc_html( $codesblock_course_price ); ?>
													<?php if ( $codesblock_course_original ) : ?>
														<small><s><?php echo esc_html( $codesblock_course_original ); ?></s></small>
													<?php endif; ?>
												</span>
											<?php endif; ?>
										</div>
										<span class="course-card-cta"><?php esc_html_e( 'Open course', 'codesblock' ); ?></span>
									</div>
								</div>
							</a>
						<?php endforeach; ?>
						<?php wp_reset_postdata(); ?>


					<?php else : ?>
						<article class="empty-post-card">
							<p class="tag"><?php esc_html_e( 'No courses yet', 'codesblock' ); ?></p>
							<h3><?php esc_html_e( 'Create your first course', 'codesblock' ); ?></h3>
							<p><?php esc_html_e( 'Courses you publish from WordPress admin will appear here automatically.', 'codesblock' ); ?></p>
						</article>
					<?php endif; ?>
					<div class="desk-note">
						<span>Built for steady progress</span>
						<strong>Structured course outlines, member-only lessons, and saved progress keep learning focused.</strong>
					</div>
				</div>
				<aside class="course-sidebars" aria-label="Course recommendations">
					<?php if ( is_active_sidebar( 'home-sidebar-ad' ) ) : ?>
						<?php dynamic_sidebar( 'home-sidebar-ad' ); ?>
					<?php endif; ?>
					<div class="side-card recommendation-card">
						<div class="side-card-heading">
							<span>Recommended articles</span>
							<?php if ( current_user_can( 'edit_posts' ) ) : ?>
								<a href="<?php echo esc_url( admin_url( 'edit.php' ) ); ?>">Edit posts</a>
							<?php else : ?>
								<small>Newest first</small>
							<?php endif; ?>
						</div>
						<div class="side-card-list">
							<?php if ( $codesblock_recommended_articles->have_posts() ) : ?>
								<?php
								while ( $codesblock_recommended_articles->have_posts() ) :
									$codesblock_recommended_articles->the_post();
									$codesblock_recommendation_initials = strtoupper( substr( preg_replace( '/[^A-Za-z0-9]/', '', get_the_title() ), 0, 2 ) );
									?>
									<a class="side-card-link" href="<?php the_permalink(); ?>">
										<span class="side-card-thumb">
											<?php if ( has_post_thumbnail() ) : ?>
												<?php the_post_thumbnail( 'thumbnail', array( 'loading' => 'lazy' ) ); ?>
											<?php else : ?>
												<span class="side-card-thumb-fallback" aria-hidden="true"><?php echo esc_html( $codesblock_recommendation_initials ?: 'CB' ); ?></span>
											<?php endif; ?>
										</span>
										<span class="side-card-copy">
											<small><?php echo esc_html( get_the_date() ); ?></small>
											<strong><?php the_title(); ?></strong>
										</span>
									</a>
								<?php endwhile; ?>
								<?php wp_reset_postdata(); ?>
							<?php else : ?>
								<p class="side-card-empty">Mark any post as recommended from the CodesBlock Priority box.</p>
							<?php endif; ?>
						</div>
					</div>
					<div class="side-card courses-card">
						<div class="side-card-heading">
							<span>Recommended courses</span>
							<?php if ( current_user_can( 'edit_posts' ) ) : ?>
								<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=course' ) ); ?>">Edit courses</a>
							<?php else : ?>
								<small>Newest first</small>
							<?php endif; ?>
						</div>
						<div class="side-card-list">
							<?php if ( $codesblock_recommended_courses->have_posts() ) : ?>
								<?php
								while ( $codesblock_recommended_courses->have_posts() ) :
									$codesblock_recommended_courses->the_post();
									$codesblock_recommendation_initials = strtoupper( substr( preg_replace( '/[^A-Za-z0-9]/', '', get_the_title() ), 0, 2 ) );
									?>
									<a class="side-card-link" href="<?php the_permalink(); ?>">
										<span class="side-card-thumb side-card-thumb-course">
											<?php if ( has_post_thumbnail() ) : ?>
												<?php the_post_thumbnail( 'thumbnail', array( 'loading' => 'lazy' ) ); ?>
											<?php else : ?>
												<span class="side-card-thumb-fallback" aria-hidden="true"><?php echo esc_html( $codesblock_recommendation_initials ?: 'CB' ); ?></span>
											<?php endif; ?>
										</span>
										<span class="side-card-copy">
											<small><?php echo esc_html( get_the_date() ); ?></small>
											<strong><?php the_title(); ?></strong>
										</span>
									</a>
								<?php endwhile; ?>
								<?php wp_reset_postdata(); ?>
							<?php else : ?>
								<p class="side-card-empty">Mark any course as recommended from the CodesBlock Priority box.</p>
							<?php endif; ?>
						</div>
					</div>
				</aside>
			</div>
		</div>
	</section>

	<section class="section practice" id="practice">
		<div class="container">
			<div class="practice-header">
				<div>
					<p class="eyebrow">Interview Practice</p>
					<h2>Practice the decisions interviewers actually score.</h2>
				</div>
				<p class="practice-lede">Work through a realistic prompt, make your reasoning visible, and use focused review notes to improve the next attempt.</p>
			</div>

			<div class="practice-board">
				<div class="practice-workspace" aria-label="Example system design interview workspace">
					<div class="practice-window-bar">
						<span><i></i><i></i><i></i> Mock interview workspace</span>
						<strong>Guided mode</strong>
					</div>
					<div class="practice-prompt">
						<span>System design prompt</span>
						<h3>Design a distributed AI inference architecture.</h3>
						<p>Talk through GPU load balancing, vector database scaling, latency, and AI model serving tradeoffs.</p>
					</div>
					<div class="practice-checkpoints">
						<div><span>01</span><strong>Clarify</strong><small>Traffic, latency, limits</small></div>
						<div><span>02</span><strong>Design</strong><small>Data model, flow, storage</small></div>
						<div><span>03</span><strong>Defend</strong><small>Tradeoffs and failure modes</small></div>
					</div>
					<div class="practice-feedback">
						<span>Review note</span>
						<p><strong>Good:</strong> clear bottleneck analysis. <strong>Next:</strong> explain the recovery path when the shared store is unavailable.</p>
					</div>
				</div>

				<nav class="practice-track-list" aria-label="Interview guide tracks">
					<?php
					$codesblock_practice_courses = new WP_Query(
						array(
							'post_type'           => 'course',
							'posts_per_page'      => 3,
							'post_status'         => 'publish',
							'ignore_sticky_posts' => true,
							'orderby'             => 'date',
							'order'               => 'DESC',
						)
					);

					if ( $codesblock_practice_courses->have_posts() ) :
						$practice_index = 1;
						while ( $codesblock_practice_courses->have_posts() ) :
							$codesblock_practice_courses->the_post();
							$course_level = get_post_meta( get_the_ID(), '_course_level', true ) ?: 'All Levels';
							$is_featured = $practice_index === 2 ? 'is-featured' : '';
							?>
							<a class="practice-track-card <?php echo esc_attr( $is_featured ); ?>" href="<?php the_permalink(); ?>">
								<span><?php echo esc_html( sprintf( '%02d', $practice_index ) ); ?> / <?php echo esc_html( $course_level ); ?></span>
								<h3><?php the_title(); ?></h3>
								<p><?php echo esc_html( wp_trim_words( get_the_excerpt() ?: wp_strip_all_tags( get_the_content() ), 12 ) ); ?></p>
								<strong>Open track <b aria-hidden="true">&rarr;</b></strong>
							</a>
							<?php
							$practice_index++;
						endwhile;
						wp_reset_postdata();
					else :
					?>
						<a class="practice-track-card" href="<?php echo esc_url( home_url( '/system-design-interview-45-minute-framework/' ) ); ?>">
							<span>01 / System design</span>
							<h3>Senior Engineer</h3>
							<p>Architecture, tradeoffs, ownership, and debugging judgment.</p>
							<strong>Open track <b aria-hidden="true">&rarr;</b></strong>
						</a>
						<a class="practice-track-card is-featured" href="<?php echo esc_url( home_url( '/courses/build-production-ready-ai-agents/' ) ); ?>">
							<span>02 / Applied AI</span>
							<h3>AI Engineering</h3>
							<p>Agents, evaluation, data pipelines, and production tradeoffs.</p>
							<strong>Open track <b aria-hidden="true">&rarr;</b></strong>
						</a>
						<a class="practice-track-card" href="<?php echo esc_url( home_url( '/senior-engineer-interview-stories/' ) ); ?>">
							<span>03 / Leadership</span>
							<h3>Engineering Manager</h3>
							<p>Team building, conflict, delivery stories, and technical leadership.</p>
							<strong>Open track <b aria-hidden="true">&rarr;</b></strong>
						</a>
					<?php endif; ?>
				</nav>
			</div>
		</div>
	</section>

	<section class="section member-section" id="member">
		<div class="container member-strip">
			<div class="member-strip-copy">
				<p class="eyebrow">Community</p>
				<p class="community-subtext">Follow us for daily tips, open-source code &amp; updates:</p>
			</div>
			<nav class="community-grid" aria-label="Community links">
				<?php if ( $codesblock_is_frontend_member ) : ?>
					<a class="community-card cb-glass-pill" href="<?php echo esc_url( $codesblock_member_learning_url ); ?>">
						<span class="community-icon-badge member-icon">
							<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
						</span>
						<strong>My Learning</strong>
					</a>
					<a class="community-card cb-glass-pill" href="<?php echo esc_url( $codesblock_member_profile_url ); ?>">
						<span class="community-icon-badge profile-icon">
							<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
						</span>
						<strong>Profile</strong>
					</a>
				<?php elseif ( $codesblock_is_admin_session ) : ?>
					<a class="community-card cb-glass-pill" href="<?php echo esc_url( admin_url() ); ?>">
						<span class="community-icon-badge admin-icon">
							<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
						</span>
						<strong>WP Admin</strong>
					</a>
				<?php else : ?>
					<a class="community-card cb-glass-pill js-open-member" href="#member-overlay" data-member-view="register" aria-haspopup="dialog" aria-controls="member-overlay">
						<span class="community-icon-badge member-icon">
							<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
						</span>
						<strong><?php esc_html_e( 'Join free', 'codesblock' ); ?></strong>
					</a>
				<?php endif; ?>
				<?php if ( get_theme_mod( 'codesblock_support_url', 'https://www.buymeacoffee.com/codesblock' ) ) : ?>
					<a class="community-card cb-glass-pill coffee-card" href="<?php echo esc_url( get_theme_mod( 'codesblock_support_url', 'https://www.buymeacoffee.com/codesblock' ) ); ?>" target="_blank" rel="noopener noreferrer">
						<span class="community-icon-badge coffee-icon">
							<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 7h12l-1-2H7L6 7z" fill="#ffffff" stroke="#1e293b"/><path d="M6.5 9l1.2 10.5a2 2 0 0 0 2 1.8h4.6a2 2 0 0 0 2-1.8L17.5 9H6.5z" fill="#ffdd00" stroke="#1e293b"/><rect x="5.5" y="7" width="13" height="2" rx="1" fill="#ffffff" stroke="#1e293b"/></svg>
						</span>
						<strong>Buy Me a Coffee</strong>
					</a>
				<?php endif; ?>
				<?php if ( get_theme_mod( 'codesblock_youtube_url', 'https://www.youtube.com/@codesblock' ) ) : ?>
					<a class="community-card cb-glass-pill youtube-card" href="<?php echo esc_url( get_theme_mod( 'codesblock_youtube_url', 'https://www.youtube.com/@codesblock' ) ); ?>" target="_blank" rel="noopener noreferrer">
						<span class="community-icon-badge youtube-icon">
							<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
						</span>
						<strong>YouTube</strong>
					</a>
				<?php endif; ?>
				<?php if ( get_theme_mod( 'codesblock_instagram_url', 'https://www.instagram.com/codesblock' ) ) : ?>
					<a class="community-card cb-glass-pill instagram-card" href="<?php echo esc_url( get_theme_mod( 'codesblock_instagram_url', 'https://www.instagram.com/codesblock' ) ); ?>" target="_blank" rel="noopener noreferrer">
						<span class="community-icon-badge instagram-icon">
							<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
						</span>
						<strong>Instagram</strong>
					</a>
				<?php endif; ?>
				<?php if ( get_theme_mod( 'codesblock_github_url', 'https://github.com/Pulkit-Rana/CodesBlock' ) ) : ?>
					<a class="community-card cb-glass-pill github-card" href="<?php echo esc_url( get_theme_mod( 'codesblock_github_url', 'https://github.com/Pulkit-Rana/CodesBlock' ) ); ?>" target="_blank" rel="noopener noreferrer">
						<span class="community-icon-badge github-icon">
							<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/></svg>
						</span>
						<strong>GitHub</strong>
					</a>
				<?php endif; ?>
			</nav>
		</div>
	</section>

	<section class="section articles" id="articles">
		<div class="container">
			<div class="section-heading split">
				<div>
					<p class="eyebrow">Articles</p>
					<h2>Latest writing from the blog.</h2>
				</div>
				<a class="text-link" href="<?php echo esc_url( codesblock_articles_url() ); ?>">View all posts</a>
			</div>
			<div class="post-grid">
				<?php
				$codesblock_posts = new WP_Query(
					array(
						'posts_per_page'      => 3,
						'post_status'         => 'publish',
						'ignore_sticky_posts' => true,
					)
				);

				if ( $codesblock_posts->have_posts() ) :
					while ( $codesblock_posts->have_posts() ) :
						$codesblock_posts->the_post();
						get_template_part( 'template-parts/content', 'card' );
					endwhile;
					wp_reset_postdata();
				else :
					?>
					<article class="empty-post-card">
						<p class="tag">Draft idea</p>
						<h3>Start with your first article</h3>
						<p>Publish a strong guide like "How to approach coding interview patterns" and it will appear here automatically.</p>
					</article>
					<article class="empty-post-card">
						<p class="tag">Draft idea</p>
						<h3>Write a course preview</h3>
						<p>Turn one lesson into a public article to attract learners into the full course.</p>
					</article>
					<article class="empty-post-card">
						<p class="tag">Draft idea</p>
						<h3>Share a mock interview breakdown</h3>
						<p>Walk through question framing, clarifying questions, code, and final tradeoffs.</p>
					</article>
					<?php
				endif;
				?>
			</div>
		</div>
	</section>
</main>

<?php
get_footer();
