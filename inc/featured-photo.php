<?php
/**
 * Featured photo block: post resolution, rotation pool, and photostream de-duplication.
 *
 * The block (blocks/featured-photo) is a context provider. It resolves one post,
 * renders its inner blocks under that post's context, and remembers the pick so the
 * front-page photostream can leave it out. Nothing here writes options, theme mods,
 * or post meta: state lives in block attributes. The rotation pool transient is a
 * disposable cache that is safe to delete at any time.
 *
 * @package Glimmr
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GLIMMR_FEATURED_PHOTO_POOL_KEY', 'glimmr_featured_photo_pool' );
define( 'GLIMMR_FEATURED_PHOTO_POOL_SIZE', 50 );

/**
 * Sanitize a stored mode against the allowed set. Never trust a stored value.
 *
 * @param mixed $mode Raw attribute value.
 * @return string One of recent|daily|manual.
 */
function glimmr_featured_photo_sanitize_mode( $mode ) {
	return in_array( $mode, array( 'recent', 'daily', 'manual' ), true ) ? $mode : 'recent';
}

/**
 * Whether a post can be the hero: a published post that still has a featured image.
 *
 * The cover uses `useFeaturedImage`, so a post without one would render blank.
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function glimmr_featured_photo_qualifies( $post_id ) {
	$post = get_post( absint( $post_id ) );
	if ( ! $post instanceof WP_Post ) {
		return false;
	}

	return 'post' === $post->post_type
		&& 'publish' === $post->post_status
		&& (int) get_post_thumbnail_id( $post ) > 0;
}

/**
 * IDs of the newest published posts with a featured image, newest first.
 *
 * Cached in a transient and rebuilt on demand; see glimmr_featured_photo_flush_pool().
 *
 * @param bool $refresh Rebuild even if a cached pool exists.
 * @return int[]
 */
function glimmr_featured_photo_pool( $refresh = false ) {
	$pool = $refresh ? false : get_transient( GLIMMR_FEATURED_PHOTO_POOL_KEY );
	if ( is_array( $pool ) ) {
		return array_map( 'intval', $pool );
	}

	$pool = get_posts(
		array(
			'fields'                 => 'ids',
			'ignore_sticky_posts'    => true,
			'no_found_rows'          => true,
			'numberposts'            => GLIMMR_FEATURED_PHOTO_POOL_SIZE,
			'orderby'                => 'date',
			'order'                  => 'DESC',
			'post_status'            => 'publish',
			'post_type'              => 'post',
			'meta_query'             => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Bounded to 50 rows and cached in a transient.
				array(
					'key'     => '_thumbnail_id',
					'value'   => 0,
					'compare' => '>',
					'type'    => 'NUMERIC',
				),
			),
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);
	$pool = array_values( array_map( 'intval', (array) $pool ) );

	set_transient( GLIMMR_FEATURED_PHOTO_POOL_KEY, $pool, DAY_IN_SECONDS );

	return $pool;
}

/**
 * Drop the cached pool. Hooked to the post lifecycle so publishing, trashing,
 * deleting, or changing a featured image shows up on the next request.
 */
function glimmr_featured_photo_flush_pool() {
	delete_transient( GLIMMR_FEATURED_PHOTO_POOL_KEY );
}
add_action( 'save_post', 'glimmr_featured_photo_flush_pool' );
add_action( 'deleted_post', 'glimmr_featured_photo_flush_pool' );
add_action( 'transition_post_status', 'glimmr_featured_photo_flush_pool' );

/**
 * Also drop the pool when a featured image changes without a post save.
 *
 * @param int    $meta_id   Meta row ID (unused).
 * @param int    $object_id Post ID (unused).
 * @param string $meta_key  Meta key.
 */
function glimmr_featured_photo_flush_pool_on_thumbnail( $meta_id, $object_id, $meta_key ) {
	if ( '_thumbnail_id' === $meta_key ) {
		glimmr_featured_photo_flush_pool();
	}
}
add_action( 'added_post_meta', 'glimmr_featured_photo_flush_pool_on_thumbnail', 10, 3 );
add_action( 'updated_post_meta', 'glimmr_featured_photo_flush_pool_on_thumbnail', 10, 3 );
add_action( 'deleted_post_meta', 'glimmr_featured_photo_flush_pool_on_thumbnail', 10, 3 );

/**
 * Pick an ID out of a pool for a mode.
 *
 * `daily` is a pure function of the site-timezone date, so every visitor sees the
 * same photo all day and it changes at local midnight. That is what lets the page
 * survive edge caching: the HTML really is identical for the whole day. Never use
 * rand() here; a cached page would freeze on one pick.
 *
 * @param int[]  $pool Candidate IDs, newest first.
 * @param string $mode recent|daily.
 * @return int 0 when the pool is empty.
 */
function glimmr_featured_photo_pick_from( array $pool, $mode ) {
	$count = count( $pool );
	if ( 0 === $count ) {
		return 0;
	}
	if ( 'daily' !== $mode ) {
		return (int) $pool[0];
	}

	$day   = (string) wp_date( 'Y-z' );
	$index = (int) sprintf( '%u', crc32( $day ) ) % $count;

	return (int) $pool[ $index ];
}

/**
 * Pick the hero for `recent` or `daily`, rebuilding the pool once if the cache is stale.
 *
 * @param string $mode recent|daily.
 * @return int 0 when no post qualifies.
 */
function glimmr_featured_photo_pick( $mode ) {
	$post_id = glimmr_featured_photo_pick_from( glimmr_featured_photo_pool(), $mode );
	if ( $post_id && glimmr_featured_photo_qualifies( $post_id ) ) {
		return $post_id;
	}
	if ( $post_id ) {
		$post_id = glimmr_featured_photo_pick_from( glimmr_featured_photo_pool( true ), $mode );
		if ( $post_id && glimmr_featured_photo_qualifies( $post_id ) ) {
			return $post_id;
		}
	}

	return 0;
}

/**
 * Resolve the hero post for a mode.
 *
 * `manual` falls back to `recent` when its post is missing, unpublished, trashed, or
 * has lost its featured image, rather than rendering an empty hero.
 *
 * @param string $mode    recent|daily|manual (sanitized here).
 * @param int    $post_id Chosen post for `manual`.
 * @return array{id:int,fallback:bool} `id` is 0 when nothing qualifies; `fallback` is
 *                                     true when `manual` could not use its post.
 */
function glimmr_featured_photo_resolve( $mode, $post_id = 0 ) {
	$mode    = glimmr_featured_photo_sanitize_mode( $mode );
	$post_id = absint( $post_id );

	if ( 'manual' === $mode ) {
		if ( glimmr_featured_photo_qualifies( $post_id ) ) {
			return array(
				'id'       => $post_id,
				'fallback' => false,
			);
		}

		return array(
			'id'       => glimmr_featured_photo_pick( 'recent' ),
			'fallback' => true,
		);
	}

	return array(
		'id'       => glimmr_featured_photo_pick( $mode ),
		'fallback' => false,
	);
}

/**
 * The hero post rendered on this request, if any.
 *
 * Set by blocks/featured-photo/render.php; read by the photostream filter below.
 * Request-scoped only: a static, never stored.
 *
 * @param int|null $post_id Pass an ID to record it; omit to read.
 * @return int 0 until a hero has rendered.
 */
function glimmr_featured_photo_current_id( $post_id = null ) {
	static $current_id = 0;

	if ( null !== $post_id ) {
		$current_id = absint( $post_id );
	}

	return $current_id;
}

/**
 * Keep the front-page photostream from repeating the hero.
 *
 * Targets the stream Query Loop in templates/front-page.html (queryId 1), which no
 * longer needs `sticky: exclude`: stickiness no longer picks the hero, so the stream
 * ignores it entirely and skips whichever post the hero resolved to.
 *
 * Known limitation: this relies on the hero rendering before the stream, which it
 * does in the template. If the block is moved below the stream the ID is unset and
 * the stream falls back to no exclusion.
 *
 * @param array    $query Query vars generated by core.
 * @param WP_Block $block Post Template block instance.
 * @return array
 */
function glimmr_featured_photo_stream_query_vars( $query, $block ) {
	if ( ! is_front_page() ) {
		return $query;
	}
	$query_id = isset( $block->context['queryId'] ) ? (int) $block->context['queryId'] : 0;
	if ( 1 !== $query_id ) {
		return $query;
	}

	$query['ignore_sticky_posts'] = true;

	$hero_id = glimmr_featured_photo_current_id();
	if ( ! $hero_id ) {
		return $query;
	}

	$not_in   = isset( $query['post__not_in'] ) && is_array( $query['post__not_in'] ) ? $query['post__not_in'] : array();
	$not_in[] = $hero_id;

	$query['post__not_in'] = array_values( array_unique( array_map( 'intval', $not_in ) ) );

	return $query;
}
add_filter( 'query_loop_block_query_vars', 'glimmr_featured_photo_stream_query_vars', 10, 2 );

/**
 * Empty state for the block, rendered through core so the group and buttons get the
 * same layout classes and styles as the old query-no-results markup.
 *
 * The admin link is gated: visitors who cannot edit see the message with no button.
 *
 * @return string Rendered HTML.
 */
function glimmr_featured_photo_empty_state() {
	$markup  = '<!-- wp:group {"align":"full","className":"glmr-empty glmr-empty--upload glmr-pin-empty","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"},"blockGap":"var:preset|spacing|40"}},"layout":{"type":"constrained","contentSize":"480px"}} -->';
	$markup .= '<div class="wp-block-group alignfull glmr-empty glmr-empty--upload glmr-pin-empty" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">';
	$markup .= '<!-- wp:paragraph {"align":"center","textColor":"muted","fontSize":"large"} -->';
	$markup .= '<p class="has-text-align-center has-muted-color has-text-color has-large-font-size">' . esc_html__( 'No featured photo yet.', 'glimmr' ) . '</p>';
	$markup .= '<!-- /wp:paragraph -->';

	if ( current_user_can( 'edit_posts' ) ) {
		$url     = esc_url( admin_url( 'post-new.php' ) );
		$markup .= '<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->';
		$markup .= '<div class="wp-block-buttons">';
		$markup .= '<!-- wp:button ' . serialize_block_attributes( array( 'url' => $url ) ) . ' -->';
		$markup .= '<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="' . $url . '">' . esc_html__( 'Upload', 'glimmr' ) . '</a></div>';
		$markup .= '<!-- /wp:button -->';
		$markup .= '</div>';
		$markup .= '<!-- /wp:buttons -->';
	}

	$markup .= '</div>';
	$markup .= '<!-- /wp:group -->';

	return do_blocks( $markup );
}

/**
 * Give the editor the server's current picks so the canvas previews the real hero.
 *
 * The block's editor script renders its inner blocks under a post context, the way
 * core/post-template does. `recent` and `daily` are resolved here with the same code
 * the front end uses; `manual` is checked in the editor against the REST API.
 */
function glimmr_featured_photo_editor_data() {
	$handle = generate_block_asset_handle( 'glimmr/featured-photo', 'editorScript' );
	if ( ! wp_script_is( $handle, 'registered' ) ) {
		return;
	}

	$data = array(
		'recent' => glimmr_featured_photo_resolve( 'recent' )['id'],
		'daily'  => glimmr_featured_photo_resolve( 'daily' )['id'],
	);

	wp_add_inline_script( $handle, 'window.glimmrFeaturedPhoto = ' . wp_json_encode( $data ) . ';', 'before' );
}
add_action( 'enqueue_block_editor_assets', 'glimmr_featured_photo_editor_data' );
