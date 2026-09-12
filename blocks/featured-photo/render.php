<?php
/**
 * Render the featured photo block.
 *
 * This block emits no hero markup of its own. It resolves one post from `mode`, then
 * renders the inner blocks the template carries (the core cover composition) under
 * that post's context, the same way core/query feeds core/post-template. The theme
 * CSS depends on core's exact cover markup, so nothing here reproduces it.
 *
 * Registered with `skip_inner_blocks`, so core does not pre-render the inner blocks
 * under the wrong post; `$content` is always empty here.
 *
 * @package Glimmr
 *
 * @var array    $attributes Block attributes (`mode`, `postId`).
 * @var string   $content    Unused; see `skip_inner_blocks` above.
 * @var WP_Block $block      Block instance carrying the parsed inner blocks.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Compute the wrapper before rendering children: nested renders swap the block-supports target.
$glimmr_wrapper  = get_block_wrapper_attributes( array( 'class' => 'glmr-pin' ) );
$glimmr_mode     = glimmr_featured_photo_sanitize_mode( $attributes['mode'] ?? 'recent' );
$glimmr_resolved = glimmr_featured_photo_resolve( $glimmr_mode, $attributes['postId'] ?? 0 );
$glimmr_hero_id  = $glimmr_resolved['id'];

if ( ! $glimmr_hero_id ) {
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Wrapper attributes and block output are escaped by core.
	printf( '<div %1$s>%2$s</div>', $glimmr_wrapper, glimmr_featured_photo_empty_state() );
	return;
}

// Remember the pick so the photostream query can leave it out (inc/featured-photo.php).
glimmr_featured_photo_current_id( $glimmr_hero_id );

$glimmr_inner_blocks = array();
if ( $block instanceof WP_Block && ! empty( $block->parsed_block['innerBlocks'] ) && is_array( $block->parsed_block['innerBlocks'] ) ) {
	$glimmr_inner_blocks = $block->parsed_block['innerBlocks'];
}

/*
 * Hand the resolved post to every inner block: as block context (post-title,
 * post-terms, and the theme's binding sources read `postId`) and as the global
 * post (core/cover with `useFeaturedImage` reads the featured image from it).
 */
$glimmr_inject_context = static function ( $context ) use ( $glimmr_hero_id ) {
	$context['postId']   = $glimmr_hero_id;
	$context['postType'] = 'post';
	return $context;
};
add_filter( 'render_block_context', $glimmr_inject_context );

global $post;
$post = get_post( $glimmr_hero_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Restored by wp_reset_postdata() below.
setup_postdata( $post );

$glimmr_html = '';
foreach ( $glimmr_inner_blocks as $glimmr_inner_block ) {
	$glimmr_html .= render_block( $glimmr_inner_block );
}

wp_reset_postdata();
remove_filter( 'render_block_context', $glimmr_inject_context );

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Wrapper attributes and rendered block markup are escaped by core.
printf( '<div %1$s>%2$s</div>', $glimmr_wrapper, $glimmr_html );
