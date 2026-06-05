<?php
/**
 * Block binding sources + the archive-cover render bridge.
 *
 * Two sources:
 *   - glimmr/featured-image : the current post's featured image URL/alt. Bound to a
 *     core/image (whose `url`/`alt` ARE bindable and write into the <img>), giving
 *     the single-photo screen a real native lightbox on the dynamic featured image.
 *   - glimmr/term-image : the queried term's featured image URL (from the taxonomy
 *     featured-image feature). core/cover's `url` is not a bindable/writable
 *     attribute, so the archive hero is painted by a scoped render filter below;
 *     the source is still registered so the binding metadata resolves and shows in
 *     `wp block binding list`.
 *
 * @package Glimmr
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register both sources on init.
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
}
add_action( 'init', 'glimmr_register_binding_sources' );

/**
 * Resolve the current post's featured image for a bound core/image attribute.
 *
 * @param array         $source_args    Binding args (unused).
 * @param WP_Block|null $block_instance Block instance (for postId context).
 * @param string        $attribute_name Attribute being bound (url|alt).
 * @return string|null
 */
function glimmr_featured_image_binding( $source_args, $block_instance = null, $attribute_name = 'url' ) {
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
	$thumb_id = get_post_thumbnail_id( $post_id );
	if ( ! $thumb_id ) {
		return null;
	}
	// x3p0/media-data reads its source image from `mediaId` (the attachment id).
	// Bound through this source so we never touch the protected `_thumbnail_id`
	// meta key, which core/post-meta bindings are not allowed to read.
	if ( 'mediaId' === $attribute_name ) {
		return (int) $thumb_id;
	}
	if ( 'alt' === $attribute_name ) {
		$alt = get_post_meta( $thumb_id, '_wp_attachment_image_alt', true );
		return $alt ? $alt : get_the_title( $post_id );
	}
	$url = wp_get_attachment_image_url( $thumb_id, 'large' );
	return $url ? $url : null;
}

/**
 * Allow the x3p0/media-data block to bind its `mediaId` attribute (WP 6.9+ filter).
 * Harmless if the plugin isn't active.
 *
 * @param array $attributes Supported bindable attributes.
 * @return array
 */
function glimmr_allow_media_data_binding( $attributes ) {
	if ( ! in_array( 'mediaId', $attributes, true ) ) {
		$attributes[] = 'mediaId';
	}
	return $attributes;
}
add_filter( 'block_bindings_supported_attributes_x3p0/media-data', 'glimmr_allow_media_data_binding' );

/**
 * Resolve the queried term's featured image URL.
 *
 * @return string|null
 */
function glimmr_term_image_binding() {
	$term = get_queried_object();
	if ( $term instanceof WP_Term ) {
		$url = glimmr_get_term_featured_image_url( $term->term_id, 'large' );
		return $url ? $url : null;
	}
	return null;
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
		? glimmr_get_term_featured_image_url( $term->term_id, 'large' )
		: '';

	if ( '' === $url ) {
		// No term image: leave the ink overlay as a solid, intentional fallback.
		return $block_content;
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
