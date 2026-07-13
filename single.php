<?php
/**
 * Single post template.
 *
 * @package CodesBlock
 */

get_header();

function codesblock_prepare_article_content( $content ) {
	$toc_items = array();
	$used_ids  = array();

	$content = preg_replace_callback(
		'/<h([2-3])([^>]*)>(.*?)<\/h\1>/is',
		function ( $matches ) use ( &$toc_items, &$used_ids ) {
			$level = (int) $matches[1];
			$attrs = $matches[2];
			$text  = trim( wp_strip_all_tags( $matches[3] ) );

			if ( '' === $text ) {
				return $matches[0];
			}

			if ( preg_match( '/\sid=["\']([^"\']+)["\']/i', $attrs, $id_match ) ) {
				$id = sanitize_title( $id_match[1] );
			} else {
				$id = sanitize_title( $text );
			}

			$base_id = $id ? $id : 'section';
			$count   = 2;

			while ( in_array( $id, $used_ids, true ) ) {
				$id = $base_id . '-' . $count;
				$count++;
			}

			$used_ids[]  = $id;
			$toc_items[] = array(
				'id'    => $id,
				'level' => $level,
				'text'  => $text,
			);

			$attrs = preg_replace( '/\sid=["\'][^"\']+["\']/i', '', $attrs );

			return '<h' . $level . $attrs . ' id="' . esc_attr( $id ) . '">' . $matches[3] . '</h' . $level . '>';
		},
		$content
	);

	return array(
		'content' => $content,
		'toc'     => $toc_items,
	);
}
?>
<main id="main" class="single-main article-reading-page">
	<?php
	while ( have_posts() ) :
		the_post();

		$prepared = codesblock_prepare_article_content( apply_filters( 'the_content', get_the_content() ) );

		$is_premium = (bool) get_post_meta( get_the_ID(), '_codesblock_premium', true );
		$user_has_full_access = function_exists( 'codesblock_user_can_view_protected_content' )
			? codesblock_user_can_view_protected_content( get_the_ID() )
			: ! $is_premium;

		$show_gate = $is_premium && ! $user_has_full_access;

		$recommended_posts = new WP_Query(
			array(
				'posts_per_page'      => 5,
				'post_status'         => 'publish',
				'post__not_in'        => array( get_the_ID() ),
				'ignore_sticky_posts' => true,
				'meta_key'            => '_codesblock_recommended',
				'meta_value'          => '1',
				'orderby'             => 'date',
				'order'               => 'DESC',
			)
		);

		$popular_posts = new WP_Query(
			array(
				'posts_per_page'      => 5,
				'post_status'         => 'publish',
				'post__not_in'        => array( get_the_ID() ),
				'ignore_sticky_posts' => true,
				'orderby'             => 'date',
				'order'               => 'DESC',
			)
		);
		?>
		<article <?php post_class( 'article-shell' ); ?>>
			<header class="article-header article-reading-header">
				<div class="container article-title-wrap">
					<a class="text-link" href="<?php echo esc_url( home_url( '/articles/' ) ); ?>">Back to articles</a>
					<h1><?php the_title(); ?></h1>
					<p class="article-meta"><?php echo esc_html( get_the_date() ); ?> &middot; <?php echo esc_html( get_the_author() ); ?></p>
				</div>
			</header>

			<div class="container article-reading-layout">
				<aside class="article-toc-panel" aria-label="Table of contents">
					<div class="article-side-card">
						<h2>Table of Contents</h2>
						<?php if ( ! empty( $prepared['toc'] ) ) : ?>
							<nav class="article-toc">
								<?php foreach ( $prepared['toc'] as $toc_item ) : ?>
									<a class="toc-level-<?php echo esc_attr( $toc_item['level'] ); ?>" href="#<?php echo esc_attr( $toc_item['id'] ); ?>">
										<?php echo esc_html( $toc_item['text'] ); ?>
									</a>
								<?php endforeach; ?>
							</nav>
						<?php else : ?>
							<p class="side-empty">Add headings like H2 and H3 to this article and they will appear here automatically.</p>
						<?php endif; ?>
					</div>
				</aside>

				<div class="article-main-column">
					<?php if ( has_post_thumbnail() ) : ?>
						<div class="featured-image-wrap article-featured-media">
							<?php the_post_thumbnail( 'large' ); ?>
						</div>
					<?php endif; ?>

					<?php if ( is_active_sidebar( 'article-top-ad' ) ) : ?>
						<div class="article-ad-slot article-ad-top" aria-label="Advertisement">
							<?php dynamic_sidebar( 'article-top-ad' ); ?>
						</div>
					<?php endif; ?>

					<?php if ( $show_gate ) : ?>
						<div class="article-premium-gate-wrapper">
							<div class="article-content article-content-faded">
								<?php echo $prepared['content']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</div>
							<div class="article-premium-gate-overlay">
								<div class="article-premium-gate-card">
									<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-bottom:12px; color:var(--blue);"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
									<h3 style="font-size:1.4rem; margin-bottom:8px;">Read the full story</h3>
									<p style="color:var(--muted); font-size:1rem; margin-bottom:20px;">Join Pro to read this article and unlock the complete paid course library.</p>
									<button class="button button-primary js-open-paywall" aria-haspopup="dialog" aria-controls="paywall-overlay">Unlock Access</button>
								</div>
							</div>
						</div>
					<?php else : ?>
						<div class="article-content">
							<?php echo $prepared['content']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
					<?php endif; ?>
				</div>

				<aside class="article-recommend-panel" aria-label="Recommended reading">
					<div class="article-side-card">
						<h2>Popular Guides</h2>
						<div class="side-link-list">
							<?php
							if ( $recommended_posts->have_posts() ) :
								while ( $recommended_posts->have_posts() ) :
									$recommended_posts->the_post();
									?>
									<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
									<?php
								endwhile;
								wp_reset_postdata();
							else :
								?>
								<a href="<?php echo esc_url( home_url( '/articles/' ) ); ?>">AI interview practice guide</a>
								<a href="<?php echo esc_url( home_url( '/articles/' ) ); ?>">System design notes</a>
								<a href="<?php echo esc_url( home_url( '/#courses' ) ); ?>">Explore practical developer courses</a>
							<?php endif; ?>
						</div>
					</div>

					<div class="article-side-card">
						<h2>Most Read Blogs</h2>
						<div class="side-link-list">
							<?php
							if ( $popular_posts->have_posts() ) :
								while ( $popular_posts->have_posts() ) :
									$popular_posts->the_post();
									?>
									<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
									<?php
								endwhile;
								wp_reset_postdata();
							else :
								?>
								<a href="<?php echo esc_url( home_url( '/articles/' ) ); ?>">Coding patterns that repeat</a>
								<a href="<?php echo esc_url( home_url( '/articles/' ) ); ?>">AI tools for developers</a>
							<?php endif; ?>
						</div>
					</div>

					<?php if ( is_active_sidebar( 'article-sidebar-ad' ) ) : ?>
						<div class="article-side-card article-ad-slot" aria-label="Advertisement">
							<?php dynamic_sidebar( 'article-sidebar-ad' ); ?>
						</div>
					<?php endif; ?>
				</aside>
			</div>
		</article>
		<?php
		comments_template();
	endwhile;
	?>
</main>
<?php
get_footer();
