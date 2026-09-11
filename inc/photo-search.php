<?php
/**
 * Keep frontend search scoped to the photostream.
 *
 * @package Glimmr
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Designed photo stream page size.
 *
 * @return int
 */
function glimmr_photo_stream_per_page() {
	return 12;
}

/**
 * Build the taxonomy search clause used by public photo search.
 *
 * @param string $search Search term.
 * @return array
 */
function glimmr_get_photo_search_tax_query( $search ) {
	$search = trim( (string) $search );
	if ( '' === $search ) {
		return array();
	}

	$terms = get_terms(
		array(
			'taxonomy'   => array( 'category', 'post_tag' ),
			'hide_empty' => true,
			'search'     => $search,
		)
	);

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return array();
	}

	$needle  = function_exists( 'mb_strtolower' ) ? mb_strtolower( $search, 'UTF-8' ) : strtolower( $search );
	$matched = array();
	foreach ( $terms as $term ) {
		$name = function_exists( 'mb_strtolower' ) ? mb_strtolower( $term->name, 'UTF-8' ) : strtolower( $term->name );
		$slug = function_exists( 'mb_strtolower' ) ? mb_strtolower( $term->slug, 'UTF-8' ) : strtolower( $term->slug );

		if ( $needle === $slug || $needle === $name ) {
			$matched[ $term->taxonomy ][] = (int) $term->term_id;
		}
	}

	if ( empty( $matched ) ) {
		return array();
	}

	$tax_query = array( 'relation' => 'OR' );
	foreach ( $matched as $taxonomy => $term_ids ) {
		$tax_query[] = array(
			'taxonomy' => $taxonomy,
			'field'    => 'term_id',
			'terms'    => array_values( array_unique( $term_ids ) ),
		);
	}

	return $tax_query;
}

/**
 * Limit public search to photo posts and let category/tag names resolve to photos.
 *
 * @param WP_Query $query Query object.
 */
function glimmr_prepare_photo_search( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() ) {
		return;
	}

	$search = trim( (string) $query->get( 's' ) );

	$query->set( 'post_type', 'post' );
	$query->set( 'posts_per_page', glimmr_photo_stream_per_page() );
	$query->set( 'ignore_sticky_posts', true );

	if ( '' === $search ) {
		return;
	}

	$tax_query = glimmr_get_photo_search_tax_query( $search );
	if ( empty( $tax_query ) ) {
		return;
	}

	$query->set( 'tax_query', $tax_query );
	$query->set( 'glimmr_tax_search', true );
}
add_action( 'pre_get_posts', 'glimmr_prepare_photo_search' );

/**
 * Keep photo taxonomy archives on the same page size as the designed Query Loop.
 *
 * Archive templates use an inherited core/query block so category and tag context
 * stays native. In that mode WordPress reads the main query's posts_per_page, not
 * the block attribute, so set the public archive query to the designed page size.
 *
 * @param WP_Query $query Query object.
 */
function glimmr_prepare_photo_archive( $query ) {
	if ( is_admin() || is_feed() || ! $query->is_main_query() || ( ! $query->is_category() && ! $query->is_tag() ) ) {
		return;
	}

	$query->set( 'post_type', 'post' );
	$query->set( 'posts_per_page', glimmr_photo_stream_per_page() );
	$query->set( 'ignore_sticky_posts', true );
}
add_action( 'pre_get_posts', 'glimmr_prepare_photo_archive' );

/**
 * When a search term maps directly to a taxonomy, the taxonomy query is the
 * search. Keep the visible search term intact, but do not also require the text
 * to appear in post title/content.
 *
 * @param string   $search SQL search fragment.
 * @param WP_Query $query  Query object.
 * @return string
 */
function glimmr_use_taxonomy_search_clause( $search, $query ) {
	if ( ! is_admin() && $query->is_main_query() && $query->is_search() && $query->get( 'glimmr_tax_search' ) ) {
		return '';
	}

	return $search;
}
add_filter( 'posts_search', 'glimmr_use_taxonomy_search_clause', 10, 2 );
