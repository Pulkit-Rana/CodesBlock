<?php
/**
 * Context-aware course offer cards.
 *
 * @package CodesBlockCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register durable offer metadata on Courses.
 */
function cbcore_register_course_offer_meta() {
	$meta = array(
		'_cb_offer_enabled'     => array( 'boolean', 'rest_sanitize_boolean' ),
		'_cb_offer_show_home'      => array( 'boolean', 'rest_sanitize_boolean' ),
		'_cb_offer_show_courses'   => array( 'boolean', 'rest_sanitize_boolean' ),
		'_cb_offer_show_articles'  => array( 'boolean', 'rest_sanitize_boolean' ),
		'_cb_offer_allow_fallback' => array( 'boolean', 'rest_sanitize_boolean' ),
		'_cb_offer_eyebrow'     => array( 'string', 'sanitize_text_field' ),
		'_cb_offer_title'       => array( 'string', 'sanitize_text_field' ),
		'_cb_offer_description' => array( 'string', 'sanitize_textarea_field' ),
		'_cb_offer_badge'       => array( 'string', 'sanitize_text_field' ),
		'_cb_offer_highlights'  => array( 'string', 'sanitize_textarea_field' ),
		'_cb_offer_cta_label'   => array( 'string', 'sanitize_text_field' ),
		'_cb_offer_cta_url'     => array( 'string', 'esc_url_raw' ),
		'_cb_offer_priority'    => array( 'integer', 'absint' ),
		'_cb_offer_start'       => array( 'string', 'sanitize_text_field' ),
		'_cb_offer_end'         => array( 'string', 'sanitize_text_field' ),
	);

	foreach ( $meta as $key => $definition ) {
		register_post_meta(
			'course',
			$key,
			array(
				'type'              => $definition[0],
				'single'            => true,
				'show_in_rest'      => false,
				'sanitize_callback' => $definition[1],
				'auth_callback'     => function() {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}
}
add_action( 'init', 'cbcore_register_course_offer_meta' );

/**
 * Add the offer controls to the normal Course editor.
 */
function cbcore_add_course_offer_box() {
	add_meta_box(
		'cbcore_course_offer',
		__( 'Course offer popup', 'codesblock-core' ),
		'cbcore_render_course_offer_box',
		'course',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes_course', 'cbcore_add_course_offer_box' );

/**
 * Add a discoverable overview under Courses.
 */
function cbcore_register_course_offers_page() {
	add_submenu_page(
		'edit.php?post_type=course',
		__( 'Course Offers', 'codesblock-core' ),
		__( 'Course Offers', 'codesblock-core' ),
		'edit_posts',
		'cbcore-course-offers',
		'cbcore_render_course_offers_page'
	);
}
add_action( 'admin_menu', 'cbcore_register_course_offers_page' );

/**
 * Render the offer status and edit links for every course.
 */
function cbcore_render_course_offers_page() {
	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_die( esc_html__( 'You do not have permission to manage course offers.', 'codesblock-core' ) );
	}

	$courses = get_posts(
		array(
			'post_type'      => 'course',
			'post_status'    => array( 'publish', 'draft', 'pending', 'private' ),
			'posts_per_page' => 200,
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);
	?>
	<div class="wrap cbcore-offers-admin">
		<h1><?php esc_html_e( 'Course Offers', 'codesblock-core' ); ?></h1>
		<p><?php esc_html_e( 'Each course owns its offer. Related matching uses course titles/topics and article titles/categories/tags. Placement checkboxes control where priority and unrelated fallbacks are allowed.', 'codesblock-core' ); ?></p>
		<table class="widefat striped" style="margin-top:18px;">
			<thead><tr>
				<th><?php esc_html_e( 'Course', 'codesblock-core' ); ?></th>
				<th><?php esc_html_e( 'Offer', 'codesblock-core' ); ?></th>
				<th><?php esc_html_e( 'Allowed placements', 'codesblock-core' ); ?></th>
				<th><?php esc_html_e( 'Priority', 'codesblock-core' ); ?></th>
				<th><?php esc_html_e( 'Schedule', 'codesblock-core' ); ?></th>
				<th><?php esc_html_e( 'Actions', 'codesblock-core' ); ?></th>
			</tr></thead>
			<tbody>
			<?php if ( ! $courses ) : ?>
				<tr><td colspan="6"><?php esc_html_e( 'No courses found.', 'codesblock-core' ); ?></td></tr>
			<?php endif; ?>
			<?php foreach ( $courses as $course ) : ?>
				<?php
				$enabled    = (bool) get_post_meta( $course->ID, '_cb_offer_enabled', true );
				$placements = array();
				if ( cbcore_offer_placement_enabled( $course->ID, '_cb_offer_show_home', true ) ) {
					$placements[] = __( 'Homepage', 'codesblock-core' );
				}
				if ( cbcore_offer_placement_enabled( $course->ID, '_cb_offer_show_courses', true ) ) {
					$placements[] = __( 'Courses archive', 'codesblock-core' );
				}
				if ( cbcore_offer_placement_enabled( $course->ID, '_cb_offer_show_articles', false ) ) {
					$placements[] = __( 'Articles archive', 'codesblock-core' );
				}
				if ( cbcore_offer_placement_enabled( $course->ID, '_cb_offer_allow_fallback', false ) ) {
					$placements[] = __( 'Unrelated fallback', 'codesblock-core' );
				}
				$start = get_post_meta( $course->ID, '_cb_offer_start', true );
				$end   = get_post_meta( $course->ID, '_cb_offer_end', true );
				$edit  = get_edit_post_link( $course->ID );
				?>
				<tr>
					<td><strong><?php echo esc_html( get_the_title( $course ) ); ?></strong><br><small><?php echo esc_html( ucfirst( $course->post_status ) ); ?></small></td>
					<td><strong style="color:<?php echo $enabled ? '#047857' : '#64748b'; ?>;"><?php echo esc_html( $enabled ? __( 'On', 'codesblock-core' ) : __( 'Off', 'codesblock-core' ) ); ?></strong></td>
					<td><?php echo $enabled ? esc_html( $placements ? implode( ', ', $placements ) : __( 'Exact and related content only', 'codesblock-core' ) ) : '&mdash;'; ?></td>
					<td><?php echo esc_html( (string) absint( get_post_meta( $course->ID, '_cb_offer_priority', true ) ) ); ?></td>
					<td><?php echo esc_html( $start || $end ? sprintf( '%s - %s', $start ?: __( 'Any time', 'codesblock-core' ), $end ?: __( 'No end', 'codesblock-core' ) ) : __( 'Always', 'codesblock-core' ) ); ?></td>
					<td>
						<?php if ( $edit ) : ?><a class="button button-primary" href="<?php echo esc_url( $edit . '#cbcore_course_offer' ); ?>"><?php esc_html_e( 'Customize offer', 'codesblock-core' ); ?></a><?php endif; ?>
						<?php if ( 'publish' === $course->post_status ) : ?><a class="button" href="<?php echo esc_url( get_permalink( $course ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'View course', 'codesblock-core' ); ?></a><?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php
}

/**
 * Render the course offer editor.
 *
 * @param WP_Post $post Current course.
 */
function cbcore_render_course_offer_box( $post ) {
	wp_nonce_field( 'cbcore_save_course_offer', 'cbcore_course_offer_nonce' );
	$enabled = (bool) get_post_meta( $post->ID, '_cb_offer_enabled', true );
	?>
	<div class="cbcore-offer-fields">
		<p>
			<label><input type="checkbox" name="cb_offer_enabled" value="1" <?php checked( $enabled ); ?>> <strong><?php esc_html_e( 'Turn on the offer for this course', 'codesblock-core' ); ?></strong></label>
		</p>
		<p class="description"><?php esc_html_e( 'Its own course page uses this offer first. Related course and article pages can match by title, topic, category, or tag. Unrelated pages use it only when you explicitly allow fallback below.', 'codesblock-core' ); ?></p>
		<fieldset style="margin:16px 0;padding:12px 14px;border:1px solid #dcdcde;border-radius:4px;">
			<legend><strong><?php esc_html_e( 'Where this offer may appear', 'codesblock-core' ); ?></strong></legend>
			<p><label><input type="checkbox" name="cb_offer_show_home" value="1" <?php checked( cbcore_offer_placement_enabled( $post->ID, '_cb_offer_show_home', true ) ); ?>> <?php esc_html_e( 'Homepage priority offer', 'codesblock-core' ); ?></label></p>
			<p><label><input type="checkbox" name="cb_offer_show_courses" value="1" <?php checked( cbcore_offer_placement_enabled( $post->ID, '_cb_offer_show_courses', true ) ); ?>> <?php esc_html_e( 'Courses archive priority offer', 'codesblock-core' ); ?></label></p>
			<p><label><input type="checkbox" name="cb_offer_show_articles" value="1" <?php checked( cbcore_offer_placement_enabled( $post->ID, '_cb_offer_show_articles', false ) ); ?>> <?php esc_html_e( 'Articles archive priority offer', 'codesblock-core' ); ?></label></p>
			<p><label><input type="checkbox" name="cb_offer_allow_fallback" value="1" <?php checked( cbcore_offer_placement_enabled( $post->ID, '_cb_offer_allow_fallback', false ) ); ?>> <?php esc_html_e( 'Allow on unrelated course or article pages as a fallback', 'codesblock-core' ); ?></label></p>
			<p class="description"><?php esc_html_e( 'Exact course and genuinely related content can use the offer automatically. Keep unrelated fallback off unless this is a site-wide promotion.', 'codesblock-core' ); ?></p>
		</fieldset>
		<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:0 18px;">
			<p>
				<label for="cb_offer_eyebrow"><strong><?php esc_html_e( 'Small heading', 'codesblock-core' ); ?></strong></label><br>
				<input class="widefat" type="text" id="cb_offer_eyebrow" name="cb_offer_eyebrow" value="<?php echo esc_attr( get_post_meta( $post->ID, '_cb_offer_eyebrow', true ) ); ?>" placeholder="Course offer">
			</p>
			<p>
				<label for="cb_offer_badge"><strong><?php esc_html_e( 'Offer badge', 'codesblock-core' ); ?></strong></label><br>
				<input class="widefat" type="text" id="cb_offer_badge" name="cb_offer_badge" value="<?php echo esc_attr( get_post_meta( $post->ID, '_cb_offer_badge', true ) ); ?>" placeholder="Save 25%">
			</p>
		</div>
		<p>
			<label for="cb_offer_title"><strong><?php esc_html_e( 'Offer title', 'codesblock-core' ); ?></strong></label><br>
			<input class="widefat" type="text" id="cb_offer_title" name="cb_offer_title" value="<?php echo esc_attr( get_post_meta( $post->ID, '_cb_offer_title', true ) ); ?>" placeholder="Build stronger system design skills for less">
		</p>
		<p>
			<label for="cb_offer_description"><strong><?php esc_html_e( 'Description', 'codesblock-core' ); ?></strong></label><br>
			<textarea class="widefat" rows="3" id="cb_offer_description" name="cb_offer_description" placeholder="Explain the value and terms truthfully."><?php echo esc_textarea( get_post_meta( $post->ID, '_cb_offer_description', true ) ); ?></textarea>
		</p>
		<p>
			<label for="cb_offer_highlights"><strong><?php esc_html_e( 'Highlights', 'codesblock-core' ); ?></strong></label><br>
			<textarea class="widefat" rows="4" id="cb_offer_highlights" name="cb_offer_highlights" placeholder="One short benefit per line (up to four)"><?php echo esc_textarea( get_post_meta( $post->ID, '_cb_offer_highlights', true ) ); ?></textarea>
		</p>
		<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:0 18px;">
			<p>
				<label for="cb_offer_cta_label"><strong><?php esc_html_e( 'Button label', 'codesblock-core' ); ?></strong></label><br>
				<input class="widefat" type="text" id="cb_offer_cta_label" name="cb_offer_cta_label" value="<?php echo esc_attr( get_post_meta( $post->ID, '_cb_offer_cta_label', true ) ); ?>" placeholder="Claim this offer">
			</p>
			<p>
				<label for="cb_offer_cta_url"><strong><?php esc_html_e( 'Button URL', 'codesblock-core' ); ?></strong></label><br>
				<input class="widefat" type="url" id="cb_offer_cta_url" name="cb_offer_cta_url" value="<?php echo esc_attr( get_post_meta( $post->ID, '_cb_offer_cta_url', true ) ); ?>" placeholder="https://...">
				<span class="description"><?php esc_html_e( 'Leave blank to use the Pro checkout or launch-signup fallback.', 'codesblock-core' ); ?></span>
			</p>
			<p>
				<label for="cb_offer_priority"><strong><?php esc_html_e( 'Homepage priority', 'codesblock-core' ); ?></strong></label><br>
				<input class="small-text" type="number" min="0" max="100" id="cb_offer_priority" name="cb_offer_priority" value="<?php echo esc_attr( (string) get_post_meta( $post->ID, '_cb_offer_priority', true ) ); ?>" placeholder="0">
				<span class="description"><?php esc_html_e( '0–100. Higher wins.', 'codesblock-core' ); ?></span>
			</p>
			<p>
				<label for="cb_offer_start"><strong><?php esc_html_e( 'Start date', 'codesblock-core' ); ?></strong></label><br>
				<input type="date" id="cb_offer_start" name="cb_offer_start" value="<?php echo esc_attr( get_post_meta( $post->ID, '_cb_offer_start', true ) ); ?>">
			</p>
			<p>
				<label for="cb_offer_end"><strong><?php esc_html_e( 'End date', 'codesblock-core' ); ?></strong></label><br>
				<input type="date" id="cb_offer_end" name="cb_offer_end" value="<?php echo esc_attr( get_post_meta( $post->ID, '_cb_offer_end', true ) ); ?>">
			</p>
		</div>
		<p class="description"><?php esc_html_e( 'Dates use the WordPress site timezone. Leave either date blank for no limit. Paid members and site editors do not see offers.', 'codesblock-core' ); ?></p>
	</div>
	<?php
}

/**
 * Save the offer configuration.
 *
 * @param int $post_id Course ID.
 */
function cbcore_save_course_offer( $post_id ) {
	if ( ! isset( $_POST['cbcore_course_offer_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['cbcore_course_offer_nonce'] ) ), 'cbcore_save_course_offer' ) || ! current_user_can( 'edit_post', $post_id ) || wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
		return;
	}

	update_post_meta( $post_id, '_cb_offer_enabled', isset( $_POST['cb_offer_enabled'] ) ? '1' : '0' );
	foreach ( array( 'show_home', 'show_courses', 'show_articles', 'allow_fallback' ) as $placement ) {
		update_post_meta( $post_id, '_cb_offer_' . $placement, isset( $_POST[ 'cb_offer_' . $placement ] ) ? '1' : '0' );
	}

	$text_fields = array( 'eyebrow', 'title', 'badge', 'cta_label' );
	foreach ( $text_fields as $field ) {
		$value = isset( $_POST[ 'cb_offer_' . $field ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'cb_offer_' . $field ] ) ) : '';
		cbcore_update_or_delete_offer_meta( $post_id, '_cb_offer_' . $field, $value );
	}

	foreach ( array( 'description', 'highlights' ) as $field ) {
		$value = isset( $_POST[ 'cb_offer_' . $field ] ) ? sanitize_textarea_field( wp_unslash( $_POST[ 'cb_offer_' . $field ] ) ) : '';
		cbcore_update_or_delete_offer_meta( $post_id, '_cb_offer_' . $field, $value );
	}

	$url = isset( $_POST['cb_offer_cta_url'] ) ? esc_url_raw( wp_unslash( $_POST['cb_offer_cta_url'] ) ) : '';
	cbcore_update_or_delete_offer_meta( $post_id, '_cb_offer_cta_url', $url );

	$priority = isset( $_POST['cb_offer_priority'] ) ? min( 100, absint( $_POST['cb_offer_priority'] ) ) : 0;
	update_post_meta( $post_id, '_cb_offer_priority', $priority );

	foreach ( array( 'start', 'end' ) as $field ) {
		$value = isset( $_POST[ 'cb_offer_' . $field ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'cb_offer_' . $field ] ) ) : '';
		if ( $value && ! cbcore_valid_offer_date( $value ) ) {
			$value = '';
		}
		cbcore_update_or_delete_offer_meta( $post_id, '_cb_offer_' . $field, $value );
	}
}
add_action( 'save_post_course', 'cbcore_save_course_offer' );

/**
 * Save a value or remove an intentionally empty setting.
 */
function cbcore_update_or_delete_offer_meta( $post_id, $key, $value ) {
	if ( '' === $value ) {
		delete_post_meta( $post_id, $key );
	} else {
		update_post_meta( $post_id, $key, $value );
	}
}

/**
 * Validate an ISO date from the editor.
 */
function cbcore_valid_offer_date( $value ) {
	if ( ! preg_match( '/^(\d{4})-(\d{2})-(\d{2})$/', $value, $parts ) ) {
		return false;
	}
	return checkdate( (int) $parts[2], (int) $parts[3], (int) $parts[1] );
}

/**
 * Whether a course's configured offer may be selected today.
 */
function cbcore_course_offer_is_active( $course_id ) {
	if ( 'publish' !== get_post_status( $course_id ) || ! get_post_meta( $course_id, '_cb_offer_enabled', true ) ) {
		return false;
	}

	$today = current_time( 'Y-m-d' );
	$start = get_post_meta( $course_id, '_cb_offer_start', true );
	$end   = get_post_meta( $course_id, '_cb_offer_end', true );

	return ( ! $start || $start <= $today ) && ( ! $end || $end >= $today );
}

/**
 * Read a placement toggle while keeping sensible defaults for older offers.
 */
function cbcore_offer_placement_enabled( $course_id, $meta_key, $default = false ) {
	if ( ! metadata_exists( 'post', $course_id, $meta_key ) ) {
		return (bool) $default;
	}
	return rest_sanitize_boolean( get_post_meta( $course_id, $meta_key, true ) );
}

/**
 * Limit active offers to a placement explicitly enabled by the editor.
 */
function cbcore_course_offers_for_placement( $offers, $meta_key, $default = false ) {
	return array_values(
		array_filter(
			$offers,
			function( $offer_id ) use ( $meta_key, $default ) {
				return cbcore_offer_placement_enabled( $offer_id, $meta_key, $default );
			}
		)
	);
}

/**
 * Return every active offer, with highest priority first.
 */
function cbcore_active_course_offer_ids() {
	$offers = get_posts(
		array(
			'post_type'      => 'course',
			'post_status'    => 'publish',
			'posts_per_page' => 100,
			'meta_query'     => array(
				array(
					'key'   => '_cb_offer_enabled',
					'value' => '1',
				),
			),
			'orderby'        => 'date',
			'order'          => 'DESC',
			'fields'         => 'ids',
			'no_found_rows'  => true,
		)
	);
	$offers = array_values( array_filter( $offers, 'cbcore_course_offer_is_active' ) );
	usort(
		$offers,
		function( $left, $right ) {
			$priority_difference = (int) get_post_meta( $right, '_cb_offer_priority', true ) - (int) get_post_meta( $left, '_cb_offer_priority', true );
			return 0 !== $priority_difference ? $priority_difference : $right - $left;
		}
	);
	return $offers;
}

/**
 * Convert titles and taxonomy labels into useful matching terms.
 */
function cbcore_offer_tokens( $value ) {
	$value = strtolower( remove_accents( wp_strip_all_tags( (string) $value ) ) );
	$words = preg_split( '/[^a-z0-9]+/', $value );
	$stop  = array( 'about', 'access', 'build', 'course', 'early', 'from', 'into', 'interview', 'learn', 'learning', 'offer', 'practice', 'project', 'ready', 'real', 'strong', 'this', 'turn', 'with', 'work', 'world', 'your' );
	return array_values(
		array_unique(
			array_filter(
				$words,
				function( $word ) use ( $stop ) {
					return strlen( $word ) >= 4 && ! in_array( $word, $stop, true );
				}
			)
		)
	);
}

/**
 * Build matching terms for a Course or article.
 */
function cbcore_offer_post_terms( $post_id ) {
	$post_type = get_post_type( $post_id );
	$value     = get_the_title( $post_id );
	if ( 'course' === $post_type ) {
		$value .= ' ' . get_post_meta( $post_id, '_cb_offer_title', true );
		$value .= ' ' . get_post_meta( $post_id, '_cb_offer_highlights', true );
		$terms  = wp_get_post_terms( $post_id, 'course_topic', array( 'fields' => 'names' ) );
	} else {
		$terms = wp_get_post_terms( $post_id, array( 'category', 'post_tag' ), array( 'fields' => 'names' ) );
	}
	if ( ! is_wp_error( $terms ) ) {
		$value .= ' ' . implode( ' ', $terms );
	}
	return cbcore_offer_tokens( $value );
}

/**
 * Rank offers against the current course or article terms.
 */
function cbcore_best_matching_course_offer( $offers, $context_terms ) {
	$ranked = array();
	foreach ( $offers as $offer_id ) {
		$matches  = count( array_intersect( $context_terms, cbcore_offer_post_terms( $offer_id ) ) );
		$priority = min( 100, absint( get_post_meta( $offer_id, '_cb_offer_priority', true ) ) );
		$ranked[] = array(
			'id'      => (int) $offer_id,
			'matches' => $matches,
			'score'   => ( $matches * 1000 ) + $priority,
		);
	}
	usort(
		$ranked,
		function( $left, $right ) {
			return $right['score'] - $left['score'];
		}
	);
	return $ranked ? $ranked[0] : array( 'id' => 0, 'matches' => 0, 'score' => 0 );
}

/**
 * Resolve the active offer and explain why it was selected.
 */
function cbcore_resolve_contextual_course_offer() {
	static $resolved = null;
	if ( null !== $resolved ) {
		return $resolved;
	}

	$resolved = array( 'id' => 0, 'reason' => 'none' );
	if ( is_admin() || is_feed() || wp_doing_ajax() || current_user_can( 'edit_posts' ) || ( function_exists( 'cbcommerce_user_has_paid_access' ) && cbcommerce_user_has_paid_access() ) ) {
		return $resolved;
	}

	/* The course overview already contains its own enrollment and access card. */
	if ( is_singular( 'course' ) ) {
		return $resolved;
	}

	$supported_context = is_front_page() || is_home() || is_page( 'articles' ) || is_singular( 'post' ) || is_post_type_archive( 'course' ) || is_tax( 'course_topic' );
	if ( ! $supported_context ) {
		return $resolved;
	}

	$offers = cbcore_active_course_offer_ids();
	if ( ! $offers ) {
		return $resolved;
	}

	if ( is_singular( 'post' ) ) {
		$best     = cbcore_best_matching_course_offer( $offers, cbcore_offer_post_terms( get_queried_object_id() ) );
		if ( $best['matches'] ) {
			$resolved = array( 'id' => $best['id'], 'reason' => 'related-content' );
			return $resolved;
		}

		$fallbacks = cbcore_course_offers_for_placement( $offers, '_cb_offer_allow_fallback', false );
		if ( $fallbacks ) {
			$resolved = array( 'id' => (int) $fallbacks[0], 'reason' => 'unrelated-fallback' );
		}
		return $resolved;
	}

	if ( is_front_page() ) {
		$offers = cbcore_course_offers_for_placement( $offers, '_cb_offer_show_home', true );
		$reason = 'homepage-priority';
	} elseif ( is_post_type_archive( 'course' ) || is_tax( 'course_topic' ) ) {
		$offers = cbcore_course_offers_for_placement( $offers, '_cb_offer_show_courses', true );
		$reason = 'course-archive-priority';
	} else {
		$offers = cbcore_course_offers_for_placement( $offers, '_cb_offer_show_articles', false );
		$reason = 'articles-archive-priority';
	}

	if ( $offers ) {
		$resolved = array( 'id' => (int) $offers[0], 'reason' => $reason );
	}
	return $resolved;
}

/**
 * Choose one relevant offer for the current request.
 *
 * @return int Course ID or 0.
 */
function cbcore_get_contextual_course_offer() {
	$resolved = cbcore_resolve_contextual_course_offer();
	return (int) $resolved['id'];
}

/**
 * Load the tiny frontend bundle only when an offer can render.
 */
function cbcore_course_offer_assets() {
	if ( ! cbcore_get_contextual_course_offer() ) {
		return;
	}

	$css = CBCORE_PATH . 'assets/course-offers.css';
	$js  = CBCORE_PATH . 'assets/course-offers.js';
	wp_enqueue_style( 'cbcore-course-offers', CBCORE_URL . 'assets/course-offers.css', array(), file_exists( $css ) ? (string) filemtime( $css ) : CBCORE_VERSION );
	wp_enqueue_script(
		'cbcore-course-offers',
		CBCORE_URL . 'assets/course-offers.js',
		array(),
		file_exists( $js ) ? (string) filemtime( $js ) : CBCORE_VERSION,
		array(
			'in_footer' => false,
			'strategy'  => 'defer',
		)
	);
}
add_action( 'wp_enqueue_scripts', 'cbcore_course_offer_assets', 30 );

/**
 * Split and cap benefit lines for the compact card.
 */
function cbcore_course_offer_highlights( $course_id ) {
	$raw   = (string) get_post_meta( $course_id, '_cb_offer_highlights', true );
	$lines = preg_split( '/\r\n|\r|\n/', $raw );
	$lines = array_values( array_filter( array_map( 'trim', $lines ) ) );
	return array_slice( $lines, 0, 4 );
}

/**
 * Print the offer card near the end of the document.
 */
function cbcore_render_course_offer() {
	$selection = cbcore_resolve_contextual_course_offer();
	$course_id = (int) $selection['id'];
	if ( ! $course_id ) {
		return;
	}

	$course_title = get_the_title( $course_id );
	$eyebrow      = get_post_meta( $course_id, '_cb_offer_eyebrow', true ) ?: __( 'Course offer', 'codesblock-core' );
	$badge        = get_post_meta( $course_id, '_cb_offer_badge', true );
	$title        = get_post_meta( $course_id, '_cb_offer_title', true ) ?: sprintf( __( 'Go further with %s', 'codesblock-core' ), $course_title );
	$description  = get_post_meta( $course_id, '_cb_offer_description', true ) ?: get_the_excerpt( $course_id );
	$cta_label    = get_post_meta( $course_id, '_cb_offer_cta_label', true ) ?: __( 'Claim this offer', 'codesblock-core' );
	$cta_url      = get_post_meta( $course_id, '_cb_offer_cta_url', true );
	$image        = get_the_post_thumbnail_url( $course_id, 'medium_large' );
	$highlights   = cbcore_course_offer_highlights( $course_id );
	if ( ! $cta_url ) {
		$cta_url = function_exists( 'cbcommerce_checkout_url' ) ? cbcommerce_checkout_url( 'pro' ) : get_permalink( $course_id );
	}
	?>
	<aside class="cb-course-offer" id="cb-course-offer-<?php echo esc_attr( $course_id ); ?>" data-offer-id="<?php echo esc_attr( $course_id ); ?>" data-offer-context="<?php echo esc_attr( $selection['reason'] ); ?>" role="region" aria-live="polite" aria-labelledby="cb-course-offer-title-<?php echo esc_attr( $course_id ); ?>"<?php if ( $description ) : ?> aria-describedby="cb-course-offer-description-<?php echo esc_attr( $course_id ); ?>"<?php endif; ?> hidden>
		<button class="cb-course-offer__close" type="button" aria-label="<?php esc_attr_e( 'Dismiss this course offer', 'codesblock-core' ); ?>">
			<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
		</button>
		<?php if ( $image ) : ?>
			<div class="cb-course-offer__media" style="--cb-offer-image:url('<?php echo esc_url( $image ); ?>')" aria-hidden="true"></div>
		<?php endif; ?>
		<div class="cb-course-offer__body">
			<div class="cb-course-offer__topline">
				<span class="cb-course-offer__eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
				<?php if ( $badge ) : ?><span class="cb-course-offer__badge"><?php echo esc_html( $badge ); ?></span><?php endif; ?>
			</div>
			<h2 id="cb-course-offer-title-<?php echo esc_attr( $course_id ); ?>"><?php echo esc_html( $title ); ?></h2>
			<?php if ( $description ) : ?><p id="cb-course-offer-description-<?php echo esc_attr( $course_id ); ?>"><?php echo esc_html( $description ); ?></p><?php endif; ?>
			<?php if ( $highlights ) : ?>
				<ul class="cb-course-offer__highlights">
					<?php foreach ( $highlights as $highlight ) : ?><li><?php echo esc_html( $highlight ); ?></li><?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<div class="cb-course-offer__footer">
				<a class="cb-course-offer__cta" href="<?php echo esc_url( $cta_url ); ?>" data-offer-cta>
					<?php echo esc_html( $cta_label ); ?>
					<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
				</a>
				<span class="cb-course-offer__course"><?php echo esc_html( $course_title ); ?></span>
			</div>
		</div>
	</aside>
	<?php
}
add_action( 'wp_footer', 'cbcore_render_course_offer', 8 );
