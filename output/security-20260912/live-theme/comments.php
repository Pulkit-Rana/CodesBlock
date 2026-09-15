<?php
/**
 * Comments template.
 *
 * @package CodesBlock
 */

if ( post_password_required() ) {
	return;
}
?>
<section class="comments-area">
	<div class="container narrow">
		<?php
		if ( have_comments() ) :
			?>
			<h2><?php comments_number( 'No responses yet', 'One response', '% responses' ); ?></h2>
			<ol class="comment-list">
				<?php wp_list_comments( array( 'style' => 'ol', 'short_ping' => true ) ); ?>
			</ol>
			<?php the_comments_pagination(); ?>
			<?php
		endif;

		if ( comments_open() ) {
			comment_form();
		}
		?>
	</div>
</section>
