<?php
/**
 * Post card.
 *
 * @package CodesBlock
 */
?>
<article <?php post_class( 'post-card' ); ?>>
	<a class="post-card-media" href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'medium_large' ); ?>
		<?php else : ?>
			<span><?php echo esc_html( strtoupper( mb_substr( get_the_title(), 0, 2 ) ) ); ?></span>
		<?php endif; ?>
	</a>
	<div class="post-card-body">
		<p class="post-date"><?php echo esc_html( get_the_date() ); ?></p>
		<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<p><?php echo esc_html( get_the_excerpt() ); ?></p>
		<a class="text-link" href="<?php the_permalink(); ?>">Read article</a>
	</div>
</article>
