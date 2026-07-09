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
?>

<main id="main">
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
				<div class="sketch-label">Today&apos;s study board</div>
				<div class="sketch-card lesson-card">
					<p>AI lesson plan</p>
					<h2>AI Study Coach</h2>
					<div class="lesson-line long"></div>
					<div class="lesson-line"></div>
					<div class="lesson-line short"></div>
				</div>
				<div class="sketch-card quiz-card">
					<span>AI feedback</span>
					<strong>Explain your approach before coding.</strong>
					<div class="choice active">Great constraint check</div>
					<div class="choice">Try a clearer edge case</div>
				</div>
				<div class="sketch-card code-card">
					<code>ai.coach("mock interview");<br>ai.explain(withExamples);</code>
				</div>
				<div class="sketch-card extra-card">
					<span>AI feedback</span>
					<strong>Explain your approach before coding.</strong>
					<div class="choice active">Great constraint check</div>
					<div class="choice">Try a clearer edge case</div>
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
				<p class="courses-subhead">Focused tracks for free &amp; paid AI-assisted learning &mdash; <strong>prices start at free</strong>, paid plans from <strong>$9/mo</strong>. Get <strong>50% off</strong> your first course.</p>
			</div>
			<div class="courses-workspace">
				<div class="course-desk">
					<article class="course-card featured">
						<div class="course-card-inner">
							<div class="course-card-face course-card-front">
								<p class="tag">Paid · AI assistant</p>
								<h3>DSA Interview Sprint</h3>
								<p class="card-desc">Pattern-first prep with AI hints &amp; mock interview feedback.</p>
								<div class="course-meta"><span>42 lessons</span><span>AI coach</span><span>Mock plan</span></div>
								<div class="card-footer-row">
									<span class="card-enrolled">&#128100; 1,240 enrolled</span>
									<span class="card-price">From $9<small>/mo</small></span>
								</div>
							</div>
							<div class="course-card-face course-card-back">
								<p class="back-title">What you&rsquo;ll master</p>
								<ul class="back-bullets">
									<li>Arrays, strings &amp; recursion patterns</li>
									<li>Trees, graphs &amp; dynamic programming</li>
									<li>AI-reviewed solutions &amp; mock rounds</li>
								</ul>
								<a class="back-cta" href="#practice">Preview practice &rarr;</a>
							</div>
						</div>
					</article>
					<article class="course-card">
						<div class="course-card-inner">
							<div class="course-card-face course-card-front">
								<p class="tag">Free course</p>
								<h3>Backend Foundations</h3>
								<p class="card-desc">APIs, databases, auth, caching &amp; deployment basics.</p>
								<div class="course-meta"><span>24 lessons</span><span>Projects</span></div>
								<div class="card-footer-row">
									<span class="card-enrolled">&#128100; 870 reading</span>
									<span class="card-price free-badge">Free</span>
								</div>
							</div>
							<div class="course-card-face course-card-back">
								<p class="back-title">What you&rsquo;ll learn</p>
								<ul class="back-bullets">
									<li>HTTP, request flow &amp; REST design</li>
									<li>Database modeling &amp; migrations</li>
									<li>Auth, caching &amp; small service patterns</li>
								</ul>
								<a class="back-cta" href="#articles">Read related articles &rarr;</a>
							</div>
						</div>
					</article>
					<article class="course-card">
						<div class="course-card-inner">
							<div class="course-card-face course-card-front">
								<p class="tag">Article series</p>
								<h3>System Design Basics</h3>
								<p class="card-desc">Capacity thinking, queues, storage choices &amp; tradeoffs.</p>
								<div class="course-meta"><span>18 lessons</span><span>Diagrams</span></div>
								<div class="card-footer-row">
									<span class="card-enrolled">&#128100; 540 reading</span>
									<span class="card-price free-badge">Free</span>
								</div>
							</div>
							<div class="course-card-face course-card-back">
								<p class="back-title">What you&rsquo;ll cover</p>
								<ul class="back-bullets">
									<li>Constraints, bottlenecks &amp; scaling</li>
									<li>Queue &amp; storage tradeoffs</li>
									<li>Failure modes &amp; calm reasoning</li>
								</ul>
								<a class="back-cta" href="#contact">Get notified &rarr;</a>
							</div>
						</div>
					</article>
					<article class="course-card">
						<div class="course-card-inner">
							<div class="course-card-face course-card-front">
								<p class="tag">Paid &middot; 50% off launch</p>
								<h3>Frontend Performance</h3>
								<p class="card-desc">Core Web Vitals, rendering &amp; real-world bundle trimming.</p>
								<div class="course-meta"><span>30 lessons</span><span>AI hints</span></div>
								<div class="card-footer-row">
									<span class="card-enrolled">&#128100; 390 enrolled</span>
									<span class="card-price offer-badge">$4.50 <s>$9</s></span>
								</div>
							</div>
							<div class="course-card-face course-card-back">
								<p class="back-title">What you&rsquo;ll optimise</p>
								<ul class="back-bullets">
									<li>LCP, CLS &amp; INP deep-dives</li>
									<li>Lazy-loading &amp; code-splitting</li>
									<li>DevTools &amp; Lighthouse profiling</li>
								</ul>
								<a class="back-cta" href="#contact">Get early access &rarr;</a>
							</div>
						</div>
					</article>
					<article class="course-card">
						<div class="course-card-inner">
							<div class="course-card-face course-card-front">
								<p class="tag">Free course</p>
								<h3>React Patterns</h3>
								<p class="card-desc">Hooks, context, composition &amp; state patterns for scale.</p>
								<div class="course-meta"><span>20 lessons</span><span>Exercises</span></div>
								<div class="card-footer-row">
									<span class="card-enrolled">&#128100; 680 reading</span>
									<span class="card-price free-badge">Free</span>
								</div>
							</div>
							<div class="course-card-face course-card-back">
								<p class="back-title">What you&rsquo;ll practice</p>
								<ul class="back-bullets">
									<li>Custom hooks &amp; reducer patterns</li>
									<li>Context, memoisation &amp; composition</li>
									<li>Reusable hook libraries</li>
								</ul>
								<a class="back-cta" href="#contact">Start learning &rarr;</a>
							</div>
						</div>
					</article>
					<div class="desk-note">
						<span>AI assistant included</span>
						<strong>Paid courses pair lessons with hints, examples, mock questions, and answer reviews.</strong>
					</div>
				</div>
				<aside class="course-sidebars" aria-label="Course recommendations">
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
					<h2>Role-specific prep with AI-guided feedback.</h2>
				</div>
				<a class="button button-primary" href="#contact">Explore guides</a>
			</div>
			<div class="practice-bento">

				<!-- Role selector -->
				<div class="bento-cell bento-roles">
					<p class="bento-label">Choose your track</p>
					<div class="role-pills">
						<button class="role-pill active" data-role="senior">Sr. Engineer</button>
						<button class="role-pill" data-role="ai">AI Roles</button>
						<button class="role-pill" data-role="manager">Manager</button>
					</div>
					<p class="role-desc" id="role-desc">Architecture, tradeoffs, ownership, debugging judgment, and system-level thinking.</p>
				</div>

				<!-- Visual feature showcase -->
				<div class="bento-cell bento-image-showcase">
					<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/interview-coach.jpg' ); ?>" alt="AI Interview Coach Dashboard Preview" class="bento-showcase-img">
					<div class="bento-image-overlay">
						<p class="bento-label"><span class="ai-dot"></span> AI Coach Dashboard</p>
					</div>
				</div>

				<!-- Mock practice stat -->
				<div class="bento-cell bento-mock">
					<div class="mock-pulse-ring"></div>
					<p class="bento-label">Mock interview</p>
					<strong class="mock-status">Live feedback mode</strong>
					<p class="mock-hint">Answer &rarr; AI scores &rarr; you improve</p>
					<a href="#contact" class="mock-cta">Start a round &rarr;</a>
				</div>

				<!-- Feature pills row -->
				<div class="bento-cell bento-features">
					<span class="feat-pill">&#129302; AI hints</span>
					<span class="feat-pill">&#128221; Answer reviews</span>
					<span class="feat-pill">&#127919; Mock rounds</span>
					<span class="feat-pill">&#128200; Progress tracking</span>
					<span class="feat-pill">&#128172; Follow-up questions</span>
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
				<a href="#contact"><span class="action-icon">M</span><strong>Member</strong><small>early access</small></a>
				<a href="<?php echo esc_url( wp_login_url() ); ?>"><span class="action-icon">L</span><strong>Login</strong><small>paid users</small></a>
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
					<form class="signup-form" action="mailto:hello@codesblock.com" method="post" enctype="text/plain">
						<label class="screen-reader-text" for="contact-name">Name</label>
						<input id="contact-name" name="name" type="text" placeholder="Your name">
						<label class="screen-reader-text" for="contact-email">Email</label>
						<input id="contact-email" name="email" type="email" placeholder="you@example.com">
						<button type="submit">Send message</button>
					</form>
					<?php
				}
				?>
			</div>
		</div>
	</section>
</main>

<?php
get_footer();
