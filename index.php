<?php
/**
 * Blog index.
 *
 * @package CodesBlock
 */

get_header();
?>
<main id="main" class="archive-main">
	<section class="archive-hero">
		<div class="container">
			<p class="eyebrow">CodesBlock Blog</p>
			<h1><?php single_post_title(); ?></h1>
			<p>Practical notes on programming, interviews, systems, and career growth.</p>
		</div>
	</section>
	<section class="section">
		<div class="container post-grid">
			<?php
			if ( have_posts() ) :
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/content', 'card' );
				endwhile;
			else :
				?>
				<p><?php esc_html_e( 'No posts yet. Your first article will appear here after publishing.', 'codesblock' ); ?></p>
				<?php
			endif;
			?>
		</div>
		<div class="container pagination-wrap">
			<?php the_posts_pagination(); ?>
		</div>
	</section>
</main>
<?php
get_footer();
