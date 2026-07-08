<?php
/**
 * Custom landing page.
 *
 * @package CodesBlock
 */

get_header();

$hero_eyebrow = get_theme_mod( 'codesblock_hero_eyebrow', 'Stay Relevant. Stay Curious.' );
$hero_title   = get_theme_mod( 'codesblock_hero_title', 'CodesBlock' );
$hero_lede    = get_theme_mod( 'codesblock_hero_lede', 'AI-assisted courses, practical articles, and guided learning paths for developers preparing for interviews, senior roles, and smarter day-to-day engineering.' );
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
			<article>
				<span>01</span>
				<h2>Articles</h2>
				<p>Sharp explainers, patterns, and career notes you can read between focused work sessions.</p>
			</article>
			<article>
				<span>02</span>
				<h2>Paid Courses</h2>
				<p>Structured learning paths with practice sets, checkpoints, and deeper implementation notes.</p>
			</article>
			<article>
				<span>03</span>
				<h2>Free Courses</h2>
				<p>Starter tracks and public lessons to build momentum before choosing a deeper path.</p>
			</article>
		</div>
	</section>

	<section class="section courses" id="courses">
		<div class="container">
			<div class="section-heading">
				<p class="eyebrow">Courses</p>
				<h2>Pick a path and keep moving.</h2>
				<p>Focused tracks for articles, free courses, and paid AI-assisted learning where developers can read, solve, ask for examples, and review with feedback.</p>
			</div>
			<div class="courses-workspace">
				<div class="course-desk">
					<article class="course-card featured">
						<div class="course-card-front">
							<p class="tag">Paid course + AI assistant</p>
							<h3>DSA Interview Sprint</h3>
							<p>Pattern-first preparation with an AI coach for hints, examples, and mock interview feedback.</p>
							<div class="course-meta"><span>42 lessons</span><span>AI assistant</span><span>Mock plan</span></div>
						</div>
						<div class="course-card-reveal">
							<p>Includes arrays, strings, recursion, trees, graphs, dynamic programming, solution reviews, AI explanations, and weekly interview prompts.</p>
							<a href="#practice">Preview practice</a>
						</div>
					</article>
					<article class="course-card">
						<div class="course-card-front">
							<p class="tag">Free course</p>
							<h3>Backend Foundations</h3>
							<p>APIs, databases, auth, caching, and deployment basics.</p>
							<div class="course-meta"><span>24 lessons</span><span>Projects</span></div>
						</div>
						<div class="course-card-reveal">
							<p>Start with HTTP, request flow, database modeling, small services, and debugging habits.</p>
							<a href="#articles">Read related articles</a>
						</div>
					</article>
					<article class="course-card">
						<div class="course-card-front">
							<p class="tag">Article series</p>
							<h3>System Design Basics</h3>
							<p>Capacity thinking, queues, storage choices, and clear tradeoffs.</p>
							<div class="course-meta"><span>18 lessons</span><span>Diagrams</span></div>
						</div>
						<div class="course-card-reveal">
							<p>Learn how to explain designs calmly: constraints, bottlenecks, scaling options, and failure modes.</p>
							<a href="#contact">Get notified</a>
						</div>
					</article>
					<div class="desk-note">
						<span>AI assistant included</span>
						<strong>Paid courses pair lessons with hints, examples, mock questions, and answer reviews.</strong>
					</div>
				</div>
				<aside class="course-sidebars" aria-label="Course recommendations">
					<div class="side-card recommendation-card">
						<span>Recommendations</span>
						<strong>AI-picked next lesson</strong>
						<small>Graph BFS after arrays, then mock follow-ups.</small>
					</div>
					<div class="side-card courses-card">
						<span>Courses</span>
						<strong>AI-guided paid paths</strong>
						<small>Examples, hints, reviews, and role-specific practice.</small>
					</div>
				</aside>
			</div>
		</div>
	</section>

	<section class="section practice" id="practice">
		<div class="container practice-grid">
			<div>
				<p class="eyebrow">Interview Guides</p>
				<h2>Role-specific prep with AI-guided feedback.</h2>
				<p>Use guides for senior engineering, AI-focused roles, and manager conversations. Each path keeps learning intact with examples, prompts, mock rounds, and review notes.</p>
				<a class="button button-primary" href="#contact">Explore guides</a>
			</div>
			<div class="practice-board">
				<article class="guide-card senior-guide">
					<div class="guide-visual">Sr</div>
					<span>Senior Software</span>
					<strong>Architecture, tradeoffs, ownership, and debugging judgment.</strong>
				</article>
				<article class="guide-card ai-guide">
					<div class="guide-visual">AI</div>
					<span>AI Based</span>
					<strong>Prompting, agents, evaluation, product thinking, and coding with AI.</strong>
				</article>
				<article class="guide-card manager-guide">
					<div class="guide-visual">Mgr</div>
					<span>Manager Role</span>
					<strong>People leadership, delivery stories, conflict, and decision quality.</strong>
				</article>
				<div class="guide-note">
					<span>AI coach</span>
					<strong>Get examples, follow-up questions, and answer reviews while learning each guide.</strong>
				</div>
			</div>
		</div>
	</section>

	<section class="section member-section" id="member">
		<div class="container member-grid">
			<div>
				<p class="eyebrow">Community</p>
				<h2>Follow the work, support the lessons, or join as a member.</h2>
			</div>
			<div class="member-actions">
				<a class="button button-primary" href="#contact"><span class="action-icon">M</span>Become a member</a>
				<a class="button button-secondary" href="<?php echo esc_url( wp_login_url() ); ?>"><span class="action-icon">L</span>Login for paid users</a>
				<a class="button coffee-button" href="https://www.buymeacoffee.com/codesblock" target="_blank" rel="noreferrer"><span class="action-icon">C</span>Buy me coffee</a>
			</div>
			<div class="social-links" aria-label="Social media handles">
				<a href="https://www.youtube.com/@codesblock" target="_blank" rel="noreferrer"><span class="social-icon youtube-icon">YT</span>Follow on YouTube</a>
				<a href="https://www.instagram.com/codesblock" target="_blank" rel="noreferrer"><span class="social-icon instagram-icon">IG</span>Follow on Instagram</a>
				<a href="https://github.com/codesblock" target="_blank" rel="noreferrer"><span class="social-icon github-icon">GH</span>Follow on GitHub</a>
			</div>
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
