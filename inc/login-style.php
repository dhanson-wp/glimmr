<?php
/**
 * Dress the native WordPress login screen in the Glimmr design system.
 *
 * @package Glimmr
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue the login-only stylesheet.
 */
function glimmr_login_enqueue_assets() {
	$rel = 'assets/css/login.css';
	wp_enqueue_style(
		'glimmr-login',
		get_theme_file_uri( $rel ),
		array( 'login' ),
		(string) filemtime( get_theme_file_path( $rel ) )
	);
}
add_action( 'login_enqueue_scripts', 'glimmr_login_enqueue_assets' );

/**
 * Point the login wordmark back to the site.
 *
 * @return string
 */
function glimmr_login_header_url() {
	return home_url( '/' );
}
add_filter( 'login_headerurl', 'glimmr_login_header_url' );

/**
 * Use the site title as the login wordmark.
 *
 * @return string
 */
function glimmr_login_header_text() {
	return glimmr_get_wordmark_html();
}
add_filter( 'login_headertext', 'glimmr_login_header_text' );

/**
 * Get posts that can feed the login montage.
 *
 * @return int[]
 */
function glimmr_login_montage_ids() {
	static $ids = null;

	if ( null !== $ids ) {
		return $ids;
	}

	$ids = get_posts(
		array(
			'numberposts' => 24,
			'fields'      => 'ids',
			'meta_key'    => '_thumbnail_id',
			'post_type'   => 'post',
			'post_status' => 'publish',
		)
	);

	if ( ! empty( $ids ) && 24 > count( $ids ) ) {
		$pool = $ids;
		while ( 24 > count( $ids ) ) {
			$ids = array_merge( $ids, $pool );
		}
		$ids = array_slice( $ids, 0, 24 );
	}

	return $ids;
}

/**
 * Mark the quiet fallback state when the montage has no images.
 *
 * @param string[] $classes Login page body classes.
 * @return string[]
 */
function glimmr_login_body_class( $classes ) {
	if ( empty( glimmr_login_montage_ids() ) ) {
		$classes[] = 'glmr-login-no-montage';
	}

	return $classes;
}
add_filter( 'login_body_class', 'glimmr_login_body_class' );

/**
 * Add a full-bleed contact-sheet montage behind the native login card.
 */
function glimmr_login_montage() {
	$ids = glimmr_login_montage_ids();

	if ( empty( $ids ) ) {
		return;
	}

	echo '<div class="glmr-login-montage" aria-hidden="true">';
	foreach ( $ids as $id ) {
		echo wp_kses_post(
			get_the_post_thumbnail(
				$id,
				'medium',
				array(
					'loading' => 'lazy',
					'alt'     => '',
				)
			)
		);
	}
	echo '</div>';
}
add_action( 'login_header', 'glimmr_login_montage' );

/**
 * Add the designed native-login footer line without replacing the core form.
 */
function glimmr_login_footer_line() {
	printf(
		'<div class="glmr-login-foot" aria-hidden="true">&copy; %1$s %2$s &middot; %3$s</div>',
		esc_html( date_i18n( 'Y' ) ),
		esc_html( get_bloginfo( 'name' ) ),
		esc_html__( 'Made with available light.', 'glimmr' )
	);
}
add_action( 'login_footer', 'glimmr_login_footer_line' );

/**
 * Sentence-case a few core login strings for the Glimmr surface only.
 *
 * @param string $translation Translated text.
 * @param string $text        Source text.
 * @return string
 */
function glimmr_login_sentence_case( $translation, $text ) {
	if ( 'wp-login.php' !== ( $GLOBALS['pagenow'] ?? '' ) ) {
		return $translation;
	}

	$replacements = array(
		'Username or Email Address' => 'Username or email address',
		'Remember Me'               => 'Remember me',
		'Log In'                    => 'Log in',
		'Get New Password'          => 'Get new password',
		'Save Password'             => 'Save password',
	);

	return $replacements[ $text ] ?? $translation;
}
add_filter( 'gettext', 'glimmr_login_sentence_case', 10, 2 );

/**
 * Remove the literal text arrow from the core "Go to site" footer link.
 *
 * @param string $translation Translated text.
 * @param string $text        Source text.
 * @param string $context     Translation context.
 * @return string
 */
function glimmr_login_context_strings( $translation, $text, $context ) {
	if ( 'wp-login.php' !== ( $GLOBALS['pagenow'] ?? '' ) ) {
		return $translation;
	}

	if ( 'site' === $context && '&larr; Go to %s' === $text ) {
		return 'Go to %s';
	}

	return $translation;
}
add_filter( 'gettext_with_context', 'glimmr_login_context_strings', 10, 3 );
