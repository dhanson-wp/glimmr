<?php
/**
 * Glimmr theme bootstrap.
 *
 * Almost all design lives in theme.json + style variations. PHP here is kept to a
 * minimum: asset loading, pattern categories, the one custom feature (taxonomy
 * featured image), two block-binding sources, and a couple of cheap seams for the
 * future private-site / location features (see docs/future-features.md).
 *
 * @package Glimmr
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GLIMMR_VERSION', '1.0.0' );
define( 'GLIMMR_DIR', get_template_directory() );

/**
 * Front-end + editor styles.
 *
 * Figtree is loaded via theme.json `fontFace` (@font-face), so we only enqueue the
 * theme stylesheet that carries what theme.json can't express: the photostream
 * grid, hover scrim, the wordmark tittle, nav submenu chrome and the Journal rhythm.
 */
function glimmr_enqueue_assets() {
	$rel = 'assets/css/glimmr.css';
	wp_enqueue_style(
		'glimmr',
		get_theme_file_uri( $rel ),
		array(),
		(string) filemtime( get_theme_file_path( $rel ) )
	);
}
add_action( 'wp_enqueue_scripts', 'glimmr_enqueue_assets' );

/**
 * Mirror the theme stylesheet into the block editor / site editor canvas.
 */
function glimmr_setup() {
	add_editor_style( 'assets/css/glimmr.css' );
}
add_action( 'after_setup_theme', 'glimmr_setup' );

/**
 * Two pattern categories used by the bundled patterns.
 */
function glimmr_register_pattern_categories() {
	register_block_pattern_category(
		'glimmr-photo',
		array(
			'label'       => __( 'Glimmr: Photo', 'glimmr' ),
			'description' => __( 'Photo-first heroes, album showcases and link-in-bio.', 'glimmr' ),
		)
	);
	register_block_pattern_category(
		'glimmr-journal',
		array(
			'label'       => __( 'Glimmr: Journal', 'glimmr' ),
			'description' => __( 'Calmer, text-forward writing layouts.', 'glimmr' ),
		)
	);
}
add_action( 'init', 'glimmr_register_pattern_categories' );

/* -------------------------------------------------------------------------
 * Future-feature seams (do NOT build the features now — see future-features.md).
 * These cost almost nothing and turn both planned features into additive work.
 * ---------------------------------------------------------------------- */

/**
 * Private-site gate stub. Returns false today; flip the filter later to gate the
 * whole site behind login without touching templates.
 *
 * @return bool
 */
function glimmr_is_private() {
	/** Filter the private-site flag. Default false (public). */
	return (bool) apply_filters( 'glimmr_is_private', false );
}

/**
 * Register a low-privilege `family` role stub (clone of subscriber) so a future
 * invite-only private site has a viewer role ready. Idempotent.
 */
function glimmr_register_family_role() {
	if ( null === get_role( 'family' ) ) {
		$subscriber = get_role( 'subscriber' );
		$caps       = $subscriber ? $subscriber->capabilities : array( 'read' => true );
		add_role( 'family', __( 'Family', 'glimmr' ), $caps );
	}
}
add_action( 'init', 'glimmr_register_family_role' );

/**
 * Location seam: register coarse lat/lng/place post meta now (nothing renders them
 * yet) so the future "Where" line / map block is additive. Coarsened by default.
 */
function glimmr_register_location_meta() {
	foreach ( array(
		'glimmr_lat'   => 'number',
		'glimmr_lng'   => 'number',
		'glimmr_place' => 'string',
	) as $key => $type ) {
		register_post_meta(
			'post',
			$key,
			array(
				'show_in_rest' => true,
				'single'       => true,
				'type'         => $type,
				'auth_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}
}
add_action( 'init', 'glimmr_register_location_meta' );

/* -------------------------------------------------------------------------
 * The one custom feature + the block-binding sources.
 * ---------------------------------------------------------------------- */
require_once GLIMMR_DIR . '/inc/featured-term-image.php';
require_once GLIMMR_DIR . '/inc/block-bindings.php';
