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

$hero_eyebrow = get_theme_mod( 'codesblock_hero_eyebrow', 'Stay Relevant. Stay Curious.' );
$hero_title   = get_theme_mod( 'codesblock_hero_title', 'CodesBlock' );
$hero_lede    = get_theme_mod( 'codesblock_hero_lede', 'AI-assisted courses, practical articles, and guided learning paths for developers preparing for interviews, senior roles, and smarter day-to-day engineering.' );

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
		'posts_per_page'      => 5,
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

if ( count( $codesblock_home_course_posts ) < 5 ) {
	$codesblock_latest_courses = new WP_Query(
		array(
			'post_type'           => 'course',
			'posts_per_page'      => 5 - count( $codesblock_home_course_posts ),
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
$codesblock_member_user          = null;
$codesblock_member_courses       = array();
$codesblock_member_progress      = array();
$codesblock_member_average       = 0;
$codesblock_member_completed     = 0;
$codesblock_member_level_name    = __( 'Starter', 'codesblock' );
$codesblock_member_profile_url   = function_exists( 'cbcommerce_member_profile_url' ) ? cbcommerce_member_profile_url() : home_url( '/#my-learning' );
$codesblock_member_account_url   = function_exists( 'cbcommerce_member_account_url' ) ? cbcommerce_member_account_url() : home_url( '/#my-learning' );

if ( $codesblock_is_frontend_member ) {
	$codesblock_member_user = wp_get_current_user();
	if ( function_exists( 'pmpro_getMembershipLevelForUser' ) ) {
		$codesblock_member_level = pmpro_getMembershipLevelForUser( $codesblock_member_user->ID );
		if ( $codesblock_member_level && ! empty( $codesblock_member_level->name ) ) {
			$codesblock_member_level_name = $codesblock_member_level->name;
		}
	}

	if ( function_exists( 'cbcommerce_get_user_course_progress' ) ) {
		$codesblock_member_progress = cbcommerce_get_user_course_progress( $codesblock_member_user->ID );
	}

	if ( $codesblock_member_progress ) {
		$codesblock_progress_query = new WP_Query(
			array(
				'post_type'      => 'course',
				'post_status'    => 'publish',
				'posts_per_page' => 6,
				'post__in'       => array_keys( $codesblock_member_progress ),
				'orderby'        => 'post__in',
			)
		);
		$codesblock_member_courses = $codesblock_progress_query->posts;
		wp_reset_postdata();

		$codesblock_progress_total = 0;
		foreach ( $codesblock_member_courses as $codesblock_member_course ) {
			$codesblock_course_percent = absint( $codesblock_member_progress[ $codesblock_member_course->ID ]['percent'] );
			$codesblock_progress_total += $codesblock_course_percent;
			if ( 100 === $codesblock_course_percent ) {
				$codesblock_member_completed++;
			}
		}
		if ( $codesblock_member_courses ) {
			$codesblock_member_average = (int) round( $codesblock_progress_total / count( $codesblock_member_courses ) );
		}
	}
}
?>

<main id="main">
	<?php if ( $codesblock_is_frontend_member && $codesblock_member_user ) : ?>
		<section class="cb-learning-hub" id="my-learning" aria-labelledby="cb-learning-title">
			<div class="container">
				<div class="cb-learning-heading">
					<div>
						<p class="eyebrow"><?php esc_html_e( 'Your learning space', 'codesblock' ); ?></p>
						<h2 id="cb-learning-title"><?php printf( esc_html__( 'Welcome back, %s', 'codesblock' ), esc_html( $codesblock_member_user->display_name ) ); ?></h2>
						<p><?php esc_html_e( 'Continue where you left off and keep your CodesBlock profile in one place.', 'codesblock' ); ?></p>
					</div>
					<a class="button button-secondary" href="<?php echo esc_url( get_post_type_archive_link( 'course' ) ); ?>"><?php esc_html_e( 'Browse courses', 'codesblock' ); ?></a>
				</div>

				<div class="cb-learning-grid">
					<aside class="cb-profile-card" aria-label="<?php esc_attr_e( 'Member profile', 'codesblock' ); ?>">
						<div class="cb-profile-person">
							<?php echo get_avatar( $codesblock_member_user->ID, 72, '', '', array( 'class' => 'cb-profile-avatar' ) ); ?>
							<div>
								<strong><?php echo esc_html( $codesblock_member_user->display_name ); ?></strong>
								<span><?php echo esc_html( $codesblock_member_user->user_email ); ?></span>
							</div>
						</div>
						<span class="cb-member-level"><?php echo esc_html( $codesblock_member_level_name ); ?> <?php esc_html_e( 'member', 'codesblock' ); ?></span>
						<div class="cb-profile-actions">
							<a href="<?php echo esc_url( $codesblock_member_profile_url ); ?>"><?php esc_html_e( 'Edit profile', 'codesblock' ); ?></a>
							<a href="<?php echo esc_url( $codesblock_member_account_url ); ?>"><?php esc_html_e( 'Membership & billing', 'codesblock' ); ?></a>
							<a href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>"><?php esc_html_e( 'Sign out', 'codesblock' ); ?></a>
						</div>
					</aside>

					<div class="cb-progress-card">
						<div class="cb-progress-summary">
							<div>
								<span><?php esc_html_e( 'Average progress', 'codesblock' ); ?></span>
								<strong><?php echo esc_html( $codesblock_member_average ); ?>%</strong>
							</div>
							<p><?php printf( esc_html__( '%1$d active course(s) · %2$d completed', 'codesblock' ), count( $codesblock_member_courses ), $codesblock_member_completed ); ?></p>
						</div>

						<?php if ( $codesblock_member_courses ) : ?>
							<div class="cb-learning-list">
								<?php foreach ( $codesblock_member_courses as $codesblock_member_course ) : ?>
									<?php $codesblock_course_percent = absint( $codesblock_member_progress[ $codesblock_member_course->ID ]['percent'] ); ?>
									<a class="cb-learning-row" href="<?php echo esc_url( get_permalink( $codesblock_member_course ) ); ?>">
										<span class="cb-learning-row-copy"><strong><?php echo esc_html( get_the_title( $codesblock_member_course ) ); ?></strong><small><?php echo 100 === $codesblock_course_percent ? esc_html__( 'Completed', 'codesblock' ) : esc_html__( 'Continue learning', 'codesblock' ); ?></small></span>
										<span class="cb-learning-row-progress"><span><i style="width: <?php echo esc_attr( $codesblock_course_percent ); ?>%"></i></span><b><?php echo esc_html( $codesblock_course_percent ); ?>%</b></span>
									</a>
								<?php endforeach; ?>
							</div>
						<?php else : ?>
							<div class="cb-learning-empty">
								<strong><?php esc_html_e( 'Your first course is waiting.', 'codesblock' ); ?></strong>
								<p><?php esc_html_e( 'Open a course and save your progress to build your learning list here.', 'codesblock' ); ?></p>
								<a href="<?php echo esc_url( get_post_type_archive_link( 'course' ) ); ?>"><?php esc_html_e( 'Explore courses', 'codesblock' ); ?> &rarr;</a>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</section>
	<?php endif; ?>
	<section class="hero">
		<div class="container hero-grid">
			<div class="hero-copy">
				<p class="eyebrow"><?php echo esc_html( $hero_eyebrow ); ?></p>
				<h1><?php echo esc_html( $hero_title ); ?></h1>
				<p class="hero-lede"><?php echo esc_html( $hero_lede ); ?></p>
				<div class="hero-actions">
					<a class="button button-primary" href="#courses">Explore paths</a>
					<a class="button button-secondary" href="#articles">Read free lessons</a>
				</div>
				<div class="learning-search" aria-label="Search learning topics">
					<form class="hero-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get">
						<label class="screen-reader-text" for="hero-search">Search learning topics</label>
						<input id="hero-search" name="s" type="search" placeholder="Search AI interviews, DSA, system design...">
						<button type="submit">Search</button>
					</form>
					<div class="keyword-picker" aria-label="Popular learning keywords">
						<span>Popular:</span>
						<a href="<?php echo esc_url( home_url( '/?s=AI%20interview%20coach' ) ); ?>">AI interview coach</a>
						<a href="<?php echo esc_url( home_url( '/?s=DSA%20patterns' ) ); ?>">DSA patterns</a>
						<a href="<?php echo esc_url( home_url( '/?s=system%20design' ) ); ?>">system design</a>
						<a href="<?php echo esc_url( home_url( '/?s=mock%20interviews' ) ); ?>">mock interviews</a>
					</div>
				</div>
			
			</div>
			<div class="hero-sketch" aria-label="CodesBlock learning preview">
				<div class="sketch-label">CodesBlock Paths</div>
				<div class="sketch-card lesson-card">
					<p>Step-by-step Guides</p>
					<h2>Master System Design</h2>
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
				<div class="sketch-card code-card" style="transform: rotate(-1deg); padding-bottom: 24px;">
					<code>import { useCourse } from 'codesblock';<br><br>const course = useCourse('React Advanced');<br>course.startPractice();</code>
				</div>
				<div class="sketch-card extra-card" style="transform: rotate(1.5deg);">
					<span>Success Rate</span>
					<strong>+45% Offer Rate</strong>
					<p style="font-size: 0.8rem; color: #a8bcce; margin-top: 6px;">After completing 3 paths.</p>
				</div>
			</div>
		</div>
	</section>

	<section class="learning-strip" id="start">
		<div class="container strip-grid">
			<a class="strip-item" href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ?: home_url( '/articles/' ) ); ?>" aria-label="Go to Articles">
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
					<a class="button button-secondary explore-courses-btn" href="<?php echo esc_url( home_url( '/courses/' ) ); ?>" id="explore-all-courses">Explore All &rarr;</a>
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
										<div class="card-footer-row" style="margin-top:12px;">
											<span class="card-enrolled"><?php esc_html_e( 'Updated from WP admin', 'codesblock' ); ?></span>
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
									?>
									<a class="side-card-link" href="<?php the_permalink(); ?>">
										<small><?php echo esc_html( get_the_date() ); ?></small>
										<strong><?php the_title(); ?></strong>
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
									?>
									<a class="side-card-link" href="<?php the_permalink(); ?>">
										<small><?php echo esc_html( get_the_date() ); ?></small>
										<strong><?php the_title(); ?></strong>
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
					<p class="eyebrow">Interview Guides</p>
					<h2>Role-specific prep with guided feedback.</h2>
				</div>
				<p class="practice-lede">Compact tracks for senior, AI, and leadership interviews with checkpoints you can use before every round.</p>
			</div>

			<div class="practice-board">
				<div class="practice-track-grid" aria-label="Interview guide tracks">
					<a class="practice-track-card" href="#contact">
						<span>01</span>
						<h3>Sr. Engineer Track</h3>
						<p>Architecture, tradeoffs, ownership, debugging judgment, and system-level thinking.</p>
						<strong>Explore Track</strong>
					</a>
					<a class="practice-track-card is-featured" href="#contact">
						<span>02</span>
						<h3>AI Roles Track</h3>
						<p>Prompting, ML basics, agents, evaluation, and data pipeline tradeoffs.</p>
						<strong>Explore Track</strong>
					</a>
					<a class="practice-track-card" href="#contact">
						<span>03</span>
						<h3>Manager Track</h3>
						<p>Team building, conflict resolution, delivery stories, and technical leadership.</p>
						<strong>Explore Track</strong>
					</a>
				</div>

				<div class="practice-dashboard">
					<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/interview-coach.jpg' ); ?>" alt="Interview Prep Dashboard">
					<div class="practice-dashboard-overlay">
						<span>Live feedback</span>
						<strong>Round-ready review in one view</strong>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="section member-section" id="member">
		<div class="container member-strip">
			<div class="member-strip-copy">
				<p class="eyebrow">Community</p>
				<h2>Follow, support, and keep learning.</h2>
			</div>
			<nav class="community-links" aria-label="Community links">
				<?php if ( $codesblock_is_frontend_member ) : ?>
					<a href="#my-learning"><span class="action-icon">M</span><strong>My learning</strong><small>progress</small></a>
					<a href="<?php echo esc_url( $codesblock_member_profile_url ); ?>"><span class="action-icon">P</span><strong>Profile</strong><small>account</small></a>
				<?php elseif ( $codesblock_is_admin_session ) : ?>
					<a href="<?php echo esc_url( admin_url() ); ?>"><span class="action-icon">A</span><strong>WP Admin</strong><small>dashboard</small></a>
				<?php else : ?>
					<a class="js-open-paywall" href="#paywall-overlay" aria-haspopup="dialog" aria-controls="paywall-overlay"><span class="action-icon">M</span><strong>Member</strong><small>join free</small></a>
					<a class="js-open-member" data-member-view="signin" href="#paywall-overlay" aria-haspopup="dialog" aria-controls="paywall-overlay"><span class="action-icon">L</span><strong>Sign in</strong><small>members</small></a>
				<?php endif; ?>
				<a href="https://www.buymeacoffee.com/codesblock" target="_blank" rel="noreferrer"><span class="action-icon coffee-icon">C</span><strong>Coffee</strong><small>support</small></a>
				<a href="https://www.youtube.com/@codesblock" target="_blank" rel="noreferrer"><span class="social-icon youtube-icon">YT</span><strong>YouTube</strong><small>videos</small></a>
				<a href="https://www.instagram.com/codesblock" target="_blank" rel="noreferrer"><span class="social-icon instagram-icon">IG</span><strong>Instagram</strong><small>updates</small></a>
				<a href="https://github.com/codesblock" target="_blank" rel="noreferrer"><span class="social-icon github-icon">GH</span><strong>GitHub</strong><small>code</small></a>
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
				<a class="text-link" href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ?: home_url( '/blog/' ) ); ?>">View all posts</a>
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

	<section class="section cta" id="contact">
		<div class="container cta-inner">
			<div>
				<p class="eyebrow">Contact</p>
				<h2>Ask about courses, membership, or AI-assisted prep.</h2>
				<p>Send a note for paid-course access, interview guide questions, collaborations, or feedback on what to build next.</p>
			</div>
			<div class="contact-form-shell">
				<?php
				$codesblock_forms = get_posts(
					array(
						'post_type'      => 'wpcf7_contact_form',
						'posts_per_page' => 1,
						'post_status'    => 'publish',
					)
				);

				if ( ! empty( $codesblock_forms ) ) {
					echo do_shortcode( '[contact-form-7 id="' . absint( $codesblock_forms[0]->ID ) . '"]' );
				} else {
					?>
					<div class="contact-fallback">
						<p><?php esc_html_e( 'The contact form is being configured. You can still reach CodesBlock directly by email.', 'codesblock' ); ?></p>
						<a class="button button-primary" href="mailto:hello@codesblock.com"><?php esc_html_e( 'Email CodesBlock', 'codesblock' ); ?></a>
					</div>
					<?php
				}
				?>
			</div>
		</div>
	</section>
</main>

<?php
get_footer();
