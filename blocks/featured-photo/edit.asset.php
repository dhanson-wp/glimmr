<?php
/**
 * Script dependencies for blocks/featured-photo/edit.js.
 *
 * Hand-written on purpose: the theme has no build step, so there is no generated
 * asset file. Keep this list in sync with the `wp.*` globals edit.js touches.
 *
 * @package Glimmr
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'dependencies' => array(
		'wp-blocks',
		'wp-element',
		'wp-components',
		'wp-block-editor',
		'wp-data',
		'wp-core-data',
		'wp-i18n',
		'wp-html-entities',
	),
	'version'      => (string) filemtime( __DIR__ . '/edit.js' ),
);
