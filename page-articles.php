<?php
/**
 * Articles page template.
 *
 * @package CodesBlock
 */

get_header();

$current_category = isset( $_GET['article_category'] ) ? sanitize_text_field( wp_unslash( $_GET['article_category'] ) ) : '';
$search_query     = isset( $_GET['article_search'] ) ? sanitize_text_field( wp_unslash( $_GET['article_search'] ) ) : '';
$categories       = get_categories(
	array(
		'hide_empty' => false,
		'orderby'    => 'name',
		'order'      => 'ASC',
	)
);

$recommended_args = array(
	'posts_per_page'      => 6,
	'post_status'         => 'publish',
	'ignore_sticky_posts' => true,
	'meta_key'            => '_codesblock_recommended',
	'meta_value'          => '1',
	'orderby'             => 'date',
	'order'               => 'DESC',
);

if ( $current_category ) {
	$recommended_args['category_name'] = $current_category;
}

if ( $search_query ) {
	$recommended_args['s'] = $search_query;
}

$recommended_posts = new WP_Query( $recommended_args );
$recommended_ids   = wp_list_pluck( $recommended_posts->posts, 'ID' );

$latest_args = array(
	'posts_per_page'      => 12,
	'post_status'         => 'publish',
	'ignore_sticky_posts' => true,
	'post__not_in'        => $recommended_ids,
	'orderby'             => 'date',
	'order'               => 'DESC',
);

if ( $current_category ) {
	$latest_args['category_name'] = $current_category;
}

if ( $search_query ) {
	$latest_args['s'] = $search_query;
}

$latest_posts = new WP_Query( $latest_args );
$bento_posts  = array_merge( $recommended_posts->posts, $latest_posts->posts );

$course_query = new WP_Query(
	array(
		'post_type'           => 'course',
		'posts_per_page'      => 4,
		'post_status'         => 'publish',
		'ignore_sticky_posts' => true,
		'meta_key'            => '_codesblock_recommended',
		'meta_value'          => '1',
		'orderby'             => 'date',
		'order'               => 'DESC',
	)
);
?>

<main id="main" class="articles-page articles-dashboard-page">
	<section class="dashboard-blog-shell">
		<div class="container">
			<div class="dashboard-blog-top">
				<div class="dashboard-intro">
					<p class="eyebrow">Articles Dashboard</p>
					<h1>Read what matters next.</h1>
					<p>AI, coding, tech reviews, career notes, and interview guides arranged in a clean Bento board for fast exploring.</p>
				</div>

				<form class="dashboard-search" action="<?php echo esc_url( home_url( '/articles/' ) ); ?>" method="get">
					<label class="screen-reader-text" for="article-search">Search articles</label>
					<span aria-hidden="true">/</span>
					<input id="article-search" name="article_search" type="search" value="<?php echo esc_attr( $search_query ); ?>" placeholder="Search AI, coding, system design...">
					<?php if ( $current_category ) : ?>
						<input type="hidden" name="article_category" value="<?php echo esc_attr( $current_category ); ?>">
					<?php endif; ?>
					<button type="submit">Search</button>
				</form>
			</div>

			<nav class="dashboard-category-bar" aria-label="Article categories">
				<a class="<?php echo empty( $current_category ) ? 'is-active' : ''; ?>" href="<?php echo esc_url( home_url( '/articles/' ) ); ?>">All</a>
				<?php foreach ( $categories as $category ) : ?>
						<a class="<?php echo esc_attr( $current_category === $category->slug ? 'is-active' : '' ); ?>" href="<?php echo esc_url( add_query_arg( array_filter( array( 'article_category' => $category->slug, 'article_search' => $search_query ) ), home_url( '/articles/' ) ) ); ?>">
						<?php echo esc_html( $category->name ); ?>
					</a>
				<?php endforeach; ?>
			</nav>

			<div class="dashboard-blog-layout">
				<section class="bento-grid" aria-label="Article feed">
					<?php if ( $bento_posts ) : ?>
						<?php
						foreach ( $bento_posts as $index => $post ) :
							setup_postdata( $post );

							$card_class = 'bento-small';
							if ( 0 === $index ) {
								$card_class = 'bento-hero';
							} elseif ( in_array( $index, array( 1, 2 ), true ) ) {
								$card_class = 'bento-wide';
							} elseif ( in_array( $index, array( 3, 4, 5 ), true ) ) {
								$card_class = 'bento-medium';
							}

							$is_recommended = (bool) get_post_meta( get_the_ID(), '_codesblock_recommended', true );
							$primary_cat    = get_the_category();
							$cat_slug       = ! empty( $primary_cat ) ? $primary_cat[0]->slug : 'general';
							$sample_image   = 'article-code.svg';

							if ( false !== strpos( $cat_slug, 'ai' ) ) {
								$sample_image = 'article-ai.svg';
							} elseif ( false !== strpos( $cat_slug, 'career' ) || false !== strpos( strtolower( get_the_title() ), 'interview' ) ) {
								$sample_image = 'article-career.svg';
							}
							?>
							<article <?php post_class( 'bento-card ' . $card_class ); ?>>
								<a class="bento-card-link" href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
									<div class="bento-media sample-<?php echo esc_attr( $cat_slug ); ?>">
										<?php if ( has_post_thumbnail() ) : ?>
											<?php the_post_thumbnail( 'large' ); ?>
										<?php else : ?>
											<img class="sample-article-image" src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/' . $sample_image ); ?>" alt="">
										<?php endif; ?>
									</div>
									<div class="bento-content">
										<div class="bento-meta">
											<?php if ( $is_recommended ) : ?>
												<span class="bento-pill">Recommended</span>
											<?php endif; ?>
											<?php if ( ! empty( $primary_cat ) ) : ?>
												<span><?php echo esc_html( $primary_cat[0]->name ); ?></span>
											<?php endif; ?>
											<span><?php echo esc_html( get_the_date() ); ?></span>
										</div>
										<h2><?php the_title(); ?></h2>
										<?php if ( 0 === $index || in_array( $index, array( 1, 2, 3, 4, 5 ), true ) ) : ?>
											<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?></p>
										<?php endif; ?>
									</div>
								</a>
							</article>
						<?php endforeach; ?>
						<?php wp_reset_postdata(); ?>
					<?php else : ?>
						<article class="bento-card bento-hero bento-empty">
							<div class="bento-content">
								<p class="bento-pill">No posts found</p>
								<h2>Try another search or category.</h2>
								<p>The board will fill with recommended posts first, then the newest matching articles.</p>
							</div>
						</article>
					<?php endif; ?>
				</section>

				<aside class="recommendations-panel" aria-label="Recommendations">
					<div class="recommendations-card">
						<div class="recommendations-heading">
							<p class="eyebrow">Recommendations</p>
							<h2>Chosen by you</h2>
						</div>

						<?php if ( $recommended_posts->have_posts() ) : ?>
							<div class="recommended-post-list">
								<?php
								while ( $recommended_posts->have_posts() ) :
									$recommended_posts->the_post();
									?>
									<a class="recommended-post" href="<?php the_permalink(); ?>">
										<span><?php echo esc_html( get_the_date() ); ?></span>
										<strong><?php the_title(); ?></strong>
									</a>
								<?php endwhile; ?>
								<?php wp_reset_postdata(); ?>
							</div>
						<?php else : ?>
							<div class="recommended-empty">
								<strong>No recommendations yet.</strong>
								<p>Open a post in WordPress and turn on “Prioritize as recommended”.</p>
							</div>
						<?php endif; ?>
					</div>

					<div class="recommendations-card course-tab">
						<p class="eyebrow">Recommended Courses</p>
						<?php if ( $course_query->have_posts() ) : ?>
							<div class="recommended-post-list">
								<?php
								while ( $course_query->have_posts() ) :
									$course_query->the_post();
									?>
									<a class="recommended-post" href="<?php the_permalink(); ?>">
										<span>Course</span>
										<strong><?php the_title(); ?></strong>
									</a>
								<?php endwhile; ?>
								<?php wp_reset_postdata(); ?>
							</div>
						<?php else : ?>
							<a class="recommended-post" href="<?php echo esc_url( home_url( '/#courses' ) ); ?>">
								<span>Course</span>
								<strong>Explore practical developer courses</strong>
							</a>
						<?php endif; ?>
					</div>
				</aside>
			</div>
		</div>
	</section>
</main>

<?php
get_footer();
