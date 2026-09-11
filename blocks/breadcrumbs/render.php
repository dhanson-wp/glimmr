<?php
/**
 * Render the single-photo breadcrumb trail.
 *
 * @package Glimmr
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_singular( 'post' ) ) {
	return '';
}

$separator = '<svg class="glmr-ic" viewBox="0 -960 960 960" aria-hidden="true" focusable="false"><path d="M504-480 348-636q-11-11-11-28t11-28q11-11 28-11t28 11l184 184q6 6 8.5 13t2.5 15q0 8-2.5 15t-8.5 13L404-268q-11 11-28 11t-28-11q-11-11-11-28t11-28l156-156Z"/></svg>';
$parts     = array(
	sprintf(
		'<a href="%1$s">%2$s</a>',
		esc_url( home_url( '/' ) ),
		esc_html__( 'Home', 'glimmr' )
	),
);

$categories = get_the_category();
if ( ! empty( $categories ) ) {
	$category = $categories[0];
	$parts[]  = sprintf(
		'<a href="%1$s">%2$s</a>',
		esc_url( get_category_link( $category ) ),
		esc_html( $category->name )
	);
}

$parts[] = sprintf(
	'<span class="current" aria-current="page">%s</span>',
	esc_html( get_the_title() )
);

$attrs = get_block_wrapper_attributes( array( 'class' => 'crumbs' ) );

printf(
	'<nav %1$s aria-label="%2$s">%3$s</nav>',
	$attrs,
	esc_attr__( 'Breadcrumb', 'glimmr' ),
	implode( $separator, $parts )
);
