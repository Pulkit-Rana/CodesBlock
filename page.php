<?php
/**
 * Page template.
 *
 * @package CodesBlock
 */

get_header();
?>
<main id="main" class="single-main">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article <?php post_class( 'article-shell' ); ?>>
			<header class="article-header">
				<div class="container narrow">
					<?php
					get_template_part(
						'template-parts/breadcrumbs',
						null,
						array(
							'current' => get_the_title(),
						)
					);
					?>
					<h1><?php the_title(); ?></h1>
				</div>
			</header>
			<div class="container narrow article-content">
				<?php the_content(); ?>
			</div>
		</article>
		<?php
	endwhile;
	?>
</main>
<?php
get_footer();
