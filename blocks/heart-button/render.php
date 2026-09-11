<?php
/**
 * Render the interactive heart button block.
 *
 * @package Glimmr
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$variant = isset( $attributes['variant'] ) && is_string( $attributes['variant'] ) ? $attributes['variant'] : 'action';

echo glimmr_render_heart_button(
	array(
		'variant'    => $variant,
		'show_count' => isset( $attributes['showCount'] ) ? (bool) $attributes['showCount'] : true,
		'show_label' => isset( $attributes['showLabel'] ) ? (bool) $attributes['showLabel'] : true,
	)
);
