<?php
/**
 * Course preview visual for the System Design course.
 *
 * @package CodesBlock
 */

if ( has_post_thumbnail() ) {
	the_post_thumbnail( 'large', array(
		'class' => 'sd-hero-visual-img',
		'style' => 'width: 100%; height: 100%; display: block; object-fit: cover;',
		'loading' => 'lazy',
	) );
}
?>
