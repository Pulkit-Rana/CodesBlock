<?php
/**
 * Featured article card.
 *
 * @package CodesBlock
 */

$categories = get_the_category();
?>
<article <?php post_class( 'featured-article-card glass-panel' ); ?>>
	<a class="featured-article-media" href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'large' ); ?>
		<?php else : ?>
			<span><?php echo esc_html( strtoupper( mb_substr( get_the_title(), 0, 2 ) ) ); ?></span>
		<?php endif; ?>
	</a>
	<div class="featured-article-body">
		<p class="tag">
			<?php echo ! empty( $categories ) ? esc_html( $categories[0]->name ) : esc_html__( 'Recommended', 'codesblock' ); ?>
		</p>
		<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<p><?php echo esc_html( get_the_excerpt() ); ?></p>
		<a class="text-link" href="<?php the_permalink(); ?>">Read article</a>
	</div>
</article>
