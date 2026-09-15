<?php
/**
 * Server-side protected content previews.
 *
 * @package CodesBlockCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function codesblock_is_protected_content( $post_id ) {
	if ( 'course_lesson' === get_post_type( $post_id ) ) {
		/* Every lesson requires a learner session, including lessons in free courses. */
		return true;
	}

	if ( 'course' === get_post_type( $post_id ) ) {
		$price = trim( (string) get_post_meta( $post_id, '_course_price', true ) );
		return '' !== $price && 'free' !== strtolower( $price );
	}

	return 'post' === get_post_type( $post_id ) && (bool) get_post_meta( $post_id, '_codesblock_premium', true );
}

function codesblock_user_can_view_protected_content( $post_id ) {
	if ( current_user_can( 'edit_post', $post_id ) ) {
		return true;
	}

	if ( 'course_lesson' === get_post_type( $post_id ) ) {
		$course_id = function_exists( 'cbcore_get_lesson_course_id' ) ? cbcore_get_lesson_course_id( $post_id ) : 0;
		$is_member = function_exists( 'cbcommerce_is_frontend_member' ) ? cbcommerce_is_frontend_member() : is_user_logged_in();
		if ( ! $course_id || ! $is_member ) {
			return false;
		}

		return ! codesblock_is_protected_content( $course_id ) || ( function_exists( 'cbcommerce_user_has_paid_access' ) && cbcommerce_user_has_paid_access() );
	}

	if ( ! codesblock_is_protected_content( $post_id ) ) {
		return true;
	}

	return function_exists( 'cbcommerce_user_has_paid_access' ) && cbcommerce_user_has_paid_access();
}

function codesblock_protected_preview( $post_id ) {
	$excerpt = get_post_field( 'post_excerpt', $post_id );
	if ( ! $excerpt ) {
		$excerpt = wp_trim_words( wp_strip_all_tags( get_post_field( 'post_content', $post_id ) ), 55 );
	}

	return wpautop( esc_html( $excerpt ) );
}

function cbcore_filter_protected_content( $content ) {
	$post_id = get_the_ID();
	if ( $post_id && codesblock_is_protected_content( $post_id ) && ! codesblock_user_can_view_protected_content( $post_id ) ) {
		return codesblock_protected_preview( $post_id );
	}

	return $content;
}
add_filter( 'the_content', 'cbcore_filter_protected_content', 99 );
add_filter( 'the_content_feed', 'cbcore_filter_protected_content', 99 );

function cbcore_protect_rest_content( $response, $post ) {
	if ( $post instanceof WP_Post && codesblock_is_protected_content( $post->ID ) && ! codesblock_user_can_view_protected_content( $post->ID ) ) {
		$data = $response->get_data();
		if ( isset( $data['content'] ) && is_array( $data['content'] ) ) {
			$data['content']['rendered']  = codesblock_protected_preview( $post->ID );
			$data['content']['protected'] = true;
		}
		$response->set_data( $data );
	}

	return $response;
}
add_filter( 'rest_prepare_post', 'cbcore_protect_rest_content', 10, 2 );
add_filter( 'rest_prepare_course', 'cbcore_protect_rest_content', 10, 2 );
add_filter( 'rest_prepare_course_lesson', 'cbcore_protect_rest_content', 10, 2 );
