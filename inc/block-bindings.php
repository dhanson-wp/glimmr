<?php
/**
 * Block binding sources + the archive-cover render bridge.
 *
 * Binding sources:
 *   - glimmr/featured-image : the current post's featured image URL/alt/media id.
 *     The single-photo screen binds a core/image to the featured image so it can
 *     use core's native lightbox.
 *   - glimmr/term-image : the queried term's featured image URL (from the taxonomy
 *     featured-image feature). core/cover's `url` is not a bindable/writable
 *     attribute, so the archive hero is painted by a scoped render filter below;
 *     the source is still registered so the binding metadata resolves and shows in
 *     `wp block binding list`.
 *   - glimmr/term-count : the queried term's post count, bound to core/paragraph
 *     content for archive hero/section readouts.
 *   - glimmr/search-count : the current search result count, bound to
 *     core/paragraph content on the search template.
 *   - glimmr/photo-count : the total published photo count, bound to
 *     core/paragraph content in global template readouts.
 *   - glimmr/author-photo-count : the current post author's published photo
 *     count, bound to the single-photo author byline.
 *   - glimmr/comment-count-title : the current post's public comment count,
 *     bound to the single-photo comments heading.
 *   - glimmr/author-name : the current post/term collection author's display
 *     name, bound to hero, archive bylines, and profile-style patterns.
 *   - glimmr/author-handle : the current post/term collection author's
 *     lightweight handle, bound to profile-style patterns.
 *   - glimmr/site-title : the current site title, bound to theme identity copy
 *     where a core Site Title block is not the right semantic element.
 *   - glimmr/site-copyright : the current year + site title, bound to the
 *     footer's core/paragraph copyright line.
 *
 * @package Glimmr
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the theme binding sources on init.
 */
function glimmr_register_binding_sources() {
	register_block_bindings_source(
		'glimmr/featured-image',
		array(
			'label'              => __( 'Featured image (Glimmr)', 'glimmr' ),
			'uses_context'       => array( 'postId' ),
			'get_value_callback' => 'glimmr_featured_image_binding',
		)
	);

	register_block_bindings_source(
		'glimmr/term-image',
		array(
			'label'              => __( 'Term featured image (Glimmr)', 'glimmr' ),
			'get_value_callback' => 'glimmr_term_image_binding',
		)
	);

	register_block_bindings_source(
		'glimmr/term-count',
		array(
			'label'              => __( 'Term photo count (Glimmr)', 'glimmr' ),
			'get_value_callback' => 'glimmr_term_count_binding',
		)
	);

	register_block_bindings_source(
		'glimmr/search-count',
		array(
			'label'              => __( 'Search photo count (Glimmr)', 'glimmr' ),
			'get_value_callback' => 'glimmr_search_count_binding',
		)
	);

	register_block_bindings_source(
		'glimmr/photo-count',
		array(
			'label'              => __( 'Published photo count (Glimmr)', 'glimmr' ),
			'get_value_callback' => 'glimmr_photo_count_binding',
		)
	);

	register_block_bindings_source(
		'glimmr/author-photo-count',
		array(
			'label'              => __( 'Author photo count (Glimmr)', 'glimmr' ),
			'uses_context'       => array( 'postId' ),
			'get_value_callback' => 'glimmr_author_photo_count_binding',
		)
	);

	register_block_bindings_source(
		'glimmr/comment-count-title',
		array(
			'label'              => __( 'Comment count title (Glimmr)', 'glimmr' ),
			'uses_context'       => array( 'postId' ),
			'get_value_callback' => 'glimmr_comment_count_title_binding',
		)
	);

	register_block_bindings_source(
		'glimmr/author-name',
		array(
			'label'              => __( 'Photo author name (Glimmr)', 'glimmr' ),
			'uses_context'       => array( 'postId' ),
			'get_value_callback' => 'glimmr_author_name_binding',
		)
	);

	register_block_bindings_source(
		'glimmr/author-handle',
		array(
			'label'              => __( 'Photo author handle (Glimmr)', 'glimmr' ),
			'uses_context'       => array( 'postId' ),
			'get_value_callback' => 'glimmr_author_handle_binding',
		)
	);

	register_block_bindings_source(
		'glimmr/site-title',
		array(
			'label'              => __( 'Site title (Glimmr)', 'glimmr' ),
			'get_value_callback' => 'glimmr_site_title_binding',
		)
	);

	register_block_bindings_source(
		'glimmr/site-copyright',
		array(
			'label'              => __( 'Site copyright (Glimmr)', 'glimmr' ),
			'get_value_callback' => 'glimmr_site_copyright_binding',
		)
	);
}
add_action( 'init', 'glimmr_register_binding_sources' );

/**
 * Resolve a post's featured image id from block context or the main loop.
 *
 * @param WP_Block|null $block_instance Block instance (for postId context).
 * @return int
 */
function glimmr_get_featured_image_id_from_context( $block_instance = null ) {
	$post_id = null;
	if ( $block_instance instanceof WP_Block && isset( $block_instance->context['postId'] ) ) {
		$post_id = (int) $block_instance->context['postId'];
	}
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}
	if ( ! $post_id ) {
		return 0;
	}

	return (int) get_post_thumbnail_id( $post_id );
}

/**
 * Resolve the current post's featured image for a bound image/media attribute.
 *
 * @param array         $source_args    Binding args (unused).
 * @param WP_Block|null $block_instance Block instance (for postId context).
 * @param string        $attribute_name Attribute being bound (url|alt).
 * @return string|null
 */
function glimmr_featured_image_binding( $source_args, $block_instance = null, $attribute_name = 'url' ) {
	$thumb_id = glimmr_get_featured_image_id_from_context( $block_instance );
	if ( ! $thumb_id ) {
		return null;
	}
	if ( 'id' === $attribute_name || 'mediaId' === $attribute_name ) {
		return (int) $thumb_id;
	}
	if ( 'alt' === $attribute_name ) {
		$alt = get_post_meta( $thumb_id, '_wp_attachment_image_alt', true );
		return $alt ? $alt : get_the_title();
	}
	$url = wp_get_attachment_image_url( $thumb_id, 'large' );
	return $url ? $url : null;
}

/**
 * Resolve the term-owned cover image for a term archive.
 *
 * Term featured images are intentional editorial choices. Missing images are
 * handled by a quieter title-band fallback in the cover render filter.
 *
 * @param int    $term_id Term id.
 * @param string $size    Image size.
 * @return string
 */
function glimmr_get_term_archive_image_url( $term_id, $size = 'full' ) {
	$term_id = (int) $term_id;
	$size    = is_string( $size ) && '' !== $size ? $size : 'full';

	return glimmr_get_term_featured_image_url( $term_id, $size );
}

/**
 * Resolve the queried term's featured image URL.
 *
 * @return string|null
 */
function glimmr_term_image_binding() {
	$term = get_queried_object();
	if ( $term instanceof WP_Term ) {
		$url = glimmr_get_term_archive_image_url( $term->term_id, 'full' );
		return $url ? $url : null;
	}
	return null;
}

/**
 * Resolve the queried term's post count as photo copy.
 *
 * @return string|null
 */
function glimmr_term_count_binding() {
	$term = get_queried_object();
	if ( ! $term instanceof WP_Term ) {
		return null;
	}

	return sprintf(
		/* translators: %s: Number of photos in the term. */
		_n( '%s photo', '%s photos', (int) $term->count, 'glimmr' ),
		number_format_i18n( (int) $term->count )
	);
}

/**
 * Resolve the current search result count as photo copy.
 *
 * @return string|null
 */
function glimmr_search_count_binding() {
	if ( ! is_search() ) {
		return null;
	}

	$search_query = trim( (string) get_search_query( false ) );
	if ( '' === $search_query ) {
		$count = 0;
	} else {
		$query_args = array(
			'fields'              => 'ids',
			'ignore_sticky_posts' => true,
			'post_status'         => 'publish',
			'post_type'           => 'post',
			'posts_per_page'      => 1,
		);
		$tax_query  = function_exists( 'glimmr_get_photo_search_tax_query' ) ? glimmr_get_photo_search_tax_query( $search_query ) : array();
		if ( ! empty( $tax_query ) ) {
			$query_args['tax_query'] = $tax_query;
		} else {
			$query_args['s'] = $search_query;
		}

		$results = new WP_Query(
			$query_args
		);
		$count   = (int) $results->found_posts;
	}

	return sprintf(
		/* translators: %s: Number of photos in the search results. */
		_n( '%s photo', '%s photos', $count, 'glimmr' ),
		number_format_i18n( $count )
	);
}

/**
 * Resolve the total published photo count.
 *
 * Supported args:
 * - prefix: prepend copy before the count.
 * - suffix: append a quiet " · {suffix}" phrase.
 * - since: append the earliest published post year.
 *
 * @param array $source_args Binding args.
 * @return string
 */
function glimmr_photo_count_binding( $source_args = array() ) {
	$count = wp_count_posts( 'post' );
	$count = isset( $count->publish ) ? (int) $count->publish : 0;

	$copy = sprintf(
		/* translators: %s: Number of published photos. */
		_n( '%s photo', '%s photos', $count, 'glimmr' ),
		number_format_i18n( $count )
	);

	if ( ! empty( $source_args['prefix'] ) && is_string( $source_args['prefix'] ) ) {
		$prefix = preg_replace( '/<[^>]*>/', '', $source_args['prefix'] );
		$copy   = ( is_string( $prefix ) ? $prefix : '' ) . $copy;
	}

	if ( ! empty( $source_args['since'] ) ) {
		$year = glimmr_earliest_photo_year();
		if ( null !== $year ) {
			$copy = sprintf(
				/* translators: 1: Photo count. 2: Year. */
				__( '%1$s · since %2$s', 'glimmr' ),
				$copy,
				$year
			);
		}
	}

	if ( ! empty( $source_args['suffix'] ) && is_string( $source_args['suffix'] ) ) {
		$copy = sprintf(
			/* translators: 1: Photo count. 2: Short suffix such as "newest first". */
			__( '%1$s · %2$s', 'glimmr' ),
			$copy,
			wp_strip_all_tags( $source_args['suffix'] )
		);
	}

	return $copy;
}

/**
 * Resolve the current post author's published photo count.
 *
 * @param array         $source_args    Binding args (unused).
 * @param WP_Block|null $block_instance Block instance (for postId context).
 * @return string|null
 */
function glimmr_author_photo_count_binding( $source_args = array(), $block_instance = null ) {
	$post_id = null;
	if ( $block_instance instanceof WP_Block && isset( $block_instance->context['postId'] ) ) {
		$post_id = (int) $block_instance->context['postId'];
	}
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}
	if ( ! $post_id ) {
		return null;
	}

	$author_id = glimmr_get_post_author_id( $post_id );
	if ( ! $author_id ) {
		return null;
	}

	$count = (int) count_user_posts( $author_id, 'post', true );

	return sprintf(
		/* translators: %s: Number of published photos by the author. */
		_n( '%s photo', '%s photos', $count, 'glimmr' ),
		number_format_i18n( $count )
	);
}

/**
 * Resolve the current post's public comment count as a comments heading.
 *
 * @param array         $source_args    Binding args (unused).
 * @param WP_Block|null $block_instance Block instance (for postId context).
 * @return string|null
 */
function glimmr_comment_count_title_binding( $source_args = array(), $block_instance = null ) {
	$post_id = null;
	if ( $block_instance instanceof WP_Block && isset( $block_instance->context['postId'] ) ) {
		$post_id = (int) $block_instance->context['postId'];
	}
	if ( ! $post_id && is_singular() ) {
		$post_id = get_queried_object_id();
	}
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}
	if ( ! $post_id ) {
		return null;
	}

	$count = (int) get_comments_number( $post_id );

	return sprintf(
		/* translators: %s: Number of comments on the current post. */
		_n( '%s comment', '%s comments', $count, 'glimmr' ),
		number_format_i18n( $count )
	);
}

/**
 * Resolve a post author id.
 *
 * @param int $post_id Post id.
 * @return int
 */
function glimmr_get_post_author_id( $post_id ) {
	$author_id = (int) get_post_field( 'post_author', (int) $post_id );

	return $author_id > 0 ? $author_id : 0;
}

/**
 * Resolve a display name from a user id.
 *
 * @param int $author_id User id.
 * @return string|null
 */
function glimmr_get_author_display_name_by_id( $author_id ) {
	$name = get_the_author_meta( 'display_name', (int) $author_id );

	return is_string( $name ) && '' !== trim( $name ) ? trim( $name ) : null;
}

/**
 * Resolve a display name from a post id.
 *
 * @param int $post_id Post id.
 * @return string|null
 */
function glimmr_get_post_author_display_name( $post_id ) {
	$author_id = glimmr_get_post_author_id( $post_id );
	if ( ! $author_id ) {
		return null;
	}

	return glimmr_get_author_display_name_by_id( $author_id );
}

/**
 * Resolve the current term collection's primary author id.
 *
 * @param WP_Term $term Queried term.
 * @return int
 */
function glimmr_get_term_collection_author_id( $term ) {
	$posts = get_posts(
		array(
			'fields'           => 'ids',
			'no_found_rows'    => true,
			'numberposts'      => 1,
			'order'            => 'DESC',
			'orderby'          => 'date',
			'post_status'      => 'publish',
			'post_type'        => 'post',
			'suppress_filters' => false,
			'tax_query'        => array(
				array(
					'taxonomy' => $term->taxonomy,
					'field'    => 'term_id',
					'terms'    => array( (int) $term->term_id ),
				),
			),
		)
	);

	if ( empty( $posts ) ) {
		return 0;
	}

	return glimmr_get_post_author_id( (int) $posts[0] );
}

/**
 * Resolve the current term collection's primary author.
 *
 * @param WP_Term $term Queried term.
 * @return string|null
 */
function glimmr_get_term_collection_author_name( $term ) {
	$author_id = glimmr_get_term_collection_author_id( $term );
	if ( ! $author_id ) {
		return null;
	}

	return glimmr_get_author_display_name_by_id( $author_id );
}

/**
 * Resolve the primary photo author id from the latest published photo.
 *
 * @return int
 */
function glimmr_get_primary_photo_author_id() {
	$posts = get_posts(
		array(
			'fields'           => 'ids',
			'no_found_rows'    => true,
			'numberposts'      => 1,
			'order'            => 'DESC',
			'orderby'          => 'date',
			'post_status'      => 'publish',
			'post_type'        => 'post',
			'suppress_filters' => false,
		)
	);

	if ( empty( $posts ) ) {
		return 0;
	}

	return glimmr_get_post_author_id( (int) $posts[0] );
}

/**
 * Resolve the primary photo author from the latest published photo.
 *
 * @return string
 */
function glimmr_get_primary_photo_author_name() {
	$author_id = glimmr_get_primary_photo_author_id();
	if ( $author_id ) {
		$name = glimmr_get_author_display_name_by_id( $author_id );
		if ( null !== $name ) {
			return $name;
		}
	}

	return glimmr_get_site_title_text();
}

/**
 * Resolve the current contextual author id, falling back to the photostream owner.
 *
 * @param WP_Block|null $block_instance Block instance (for postId context).
 * @return int
 */
function glimmr_get_contextual_author_id( $block_instance = null ) {
	$post_id = null;

	if ( $block_instance instanceof WP_Block && isset( $block_instance->context['postId'] ) ) {
		$post_id = (int) $block_instance->context['postId'];
	}
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}
	if ( $post_id ) {
		$author_id = glimmr_get_post_author_id( $post_id );
		if ( $author_id ) {
			return $author_id;
		}
	}

	$term = get_queried_object();
	if ( $term instanceof WP_Term ) {
		$author_id = glimmr_get_term_collection_author_id( $term );
		if ( $author_id ) {
			return $author_id;
		}
	}

	return glimmr_get_primary_photo_author_id();
}

/**
 * Resolve the display author for post hero and archive collection bylines.
 *
 * Supported args:
 * - prefix: prepend copy before the author name.
 *
 * @param array         $source_args    Binding args.
 * @param WP_Block|null $block_instance Block instance (for postId context).
 * @return string
 */
function glimmr_author_name_binding( $source_args = array(), $block_instance = null ) {
	$name      = null;
	$author_id = glimmr_get_contextual_author_id( $block_instance );
	if ( $author_id ) {
		$name = glimmr_get_author_display_name_by_id( $author_id );
	}

	if ( null === $name ) {
		$name = glimmr_get_primary_photo_author_name();
	}

	if ( ! empty( $source_args['prefix'] ) && is_string( $source_args['prefix'] ) ) {
		$prefix = preg_replace( '/<[^>]*>/', '', $source_args['prefix'] );
		return ( is_string( $prefix ) ? $prefix : '' ) . $name;
	}

	return $name;
}

/**
 * Resolve the display author handle for profile-style patterns.
 *
 * Supported args:
 * - prefix: prepend copy before the handle. Defaults to @.
 *
 * @param array         $source_args    Binding args.
 * @param WP_Block|null $block_instance Block instance (for postId context).
 * @return string
 */
function glimmr_author_handle_binding( $source_args = array(), $block_instance = null ) {
	$author_id = glimmr_get_contextual_author_id( $block_instance );
	$source    = '';

	if ( $author_id ) {
		$user = get_userdata( $author_id );
		if ( $user instanceof WP_User ) {
			$source = is_string( $user->user_nicename ) ? $user->user_nicename : '';
			if ( '' === trim( $source ) && is_string( $user->user_login ) ) {
				$source = $user->user_login;
			}
			if ( in_array( strtolower( trim( $source ) ), array( 'admin', 'administrator' ), true ) ) {
				$name_parts = preg_split( '/\s+/', (string) $user->display_name );
				$source     = is_array( $name_parts ) && ! empty( $name_parts[0] ) ? $name_parts[0] : $source;
			}
		}
	}

	$handle = sanitize_title( $source );
	if ( '' === $handle ) {
		$handle = sanitize_title( glimmr_get_primary_photo_author_name() );
	}
	if ( '' === $handle ) {
		$handle = sanitize_title( glimmr_get_site_title_text() );
	}

	$prefix = '@';
	if ( array_key_exists( 'prefix', $source_args ) && is_string( $source_args['prefix'] ) ) {
		$prefix = (string) preg_replace( '/<[^>]*>/', '', $source_args['prefix'] );
	}

	return $prefix . $handle;
}

/**
 * Find the year of the oldest published photo.
 *
 * @return string|null
 */
function glimmr_earliest_photo_year() {
	$posts = get_posts(
		array(
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'numberposts'    => 1,
			'order'          => 'ASC',
			'orderby'        => 'date',
			'post_status'    => 'publish',
			'post_type'      => 'post',
			'suppress_filters' => false,
		)
	);

	if ( empty( $posts ) ) {
		return null;
	}

	return get_the_date( 'Y', (int) $posts[0] );
}

/**
 * Resolve the site title for paragraph bindings.
 *
 * Supported args:
 * - prefix: prepend copy before the site title.
 *
 * @param array $source_args Binding args.
 * @return string
 */
function glimmr_site_title_binding( $source_args = array() ) {
	$site_name = glimmr_get_site_title_text();

	if ( ! empty( $source_args['prefix'] ) && is_string( $source_args['prefix'] ) ) {
		$prefix = preg_replace( '/<[^>]*>/', '', $source_args['prefix'] );
		return ( is_string( $prefix ) ? $prefix : '' ) . $site_name;
	}

	return $site_name;
}

/**
 * Resolve the current footer copyright line from site data.
 *
 * @return string
 */
function glimmr_site_copyright_binding() {
	return sprintf(
		/* translators: 1: Current year. 2: Site title. */
		__( '© %1$s %2$s', 'glimmr' ),
		date_i18n( 'Y', current_time( 'timestamp' ) ),
		glimmr_get_site_title_text()
	);
}

/**
 * Paint the archive cover hero from the queried term's featured image.
 *
 * core/cover can't take the image through a binding (its `url` has no writable HTML
 * source), so when a cover declares the glimmr/term-image binding we inject the
 * background <img> right after the dim layer.
 *
 * @param string $block_content Rendered cover HTML.
 * @param array  $block         Parsed block.
 * @return string
 */
function glimmr_render_term_cover( $block_content, $block ) {
	$source = $block['attrs']['metadata']['bindings']['url']['source'] ?? '';
	if ( 'glimmr/term-image' !== $source ) {
		return $block_content;
	}

	$term = get_queried_object();
	$url  = ( $term instanceof WP_Term )
		? glimmr_get_term_archive_image_url( $term->term_id, 'full' )
		: '';

	if ( '' === $url ) {
		if ( class_exists( 'WP_HTML_Tag_Processor' ) ) {
			$processor = new WP_HTML_Tag_Processor( $block_content );
			if ( $processor->next_tag( array( 'tag_name' => 'DIV', 'class_name' => 'wp-block-cover' ) ) ) {
				$processor->add_class( 'glmr-archive-hero--fallback' );
				return $processor->get_updated_html();
			}
		}

		$updated = preg_replace(
			'/class="([^"]*\\bglmr-archive-hero\\b[^"]*)"/',
			'class="$1 glmr-archive-hero--fallback"',
			$block_content,
			1
		);

		return is_string( $updated ) ? $updated : $block_content;
	}

	$img    = sprintf( '<img class="wp-block-cover__image-background" src="%s" alt="" />', esc_url( $url ) );
	$needle = '</span>'; // First </span> closes the cover background-dim layer.
	$pos    = strpos( $block_content, $needle );
	if ( false !== $pos ) {
		$at            = $pos + strlen( $needle );
		$block_content = substr( $block_content, 0, $at ) . $img . substr( $block_content, $at );
	}

	return $block_content;
}
add_filter( 'render_block_core/cover', 'glimmr_render_term_cover', 10, 2 );
