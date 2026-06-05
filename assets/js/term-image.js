/**
 * Glimmr — taxonomy featured-image picker.
 *
 * A small wp.media() bridge for the Featured image control on Edit Category /
 * Edit Tag. No build step; depends on jQuery + wp.media (enqueued in PHP).
 */
( function ( $ ) {
	'use strict';

	var L = window.glimmrTermImage || {};

	function wire( root ) {
		var $root    = $( root );
		var $input   = $root.find( '[data-glimmr-input]' );
		var $preview = $root.find( '[data-glimmr-preview]' );
		var $set     = $root.find( '[data-glimmr-set]' );
		var $remove  = $root.find( '[data-glimmr-remove]' );
		var frame;

		$set.on( 'click', function ( e ) {
			e.preventDefault();
			if ( frame ) {
				frame.open();
				return;
			}
			frame = wp.media( {
				title: L.title || 'Featured image',
				button: { text: L.button || 'Set featured image' },
				library: { type: 'image' },
				multiple: false
			} );
			frame.on( 'select', function () {
				var att = frame.state().get( 'selection' ).first().toJSON();
				var src = ( att.sizes && att.sizes.medium ) ? att.sizes.medium.url : att.url;
				$input.val( att.id );
				$preview.html( '<img src="' + src + '" alt="" />' );
				$set.text( L.replace || 'Replace image' );
				$remove.prop( 'hidden', false );
			} );
			frame.open();
		} );

		$remove.on( 'click', function ( e ) {
			e.preventDefault();
			$input.val( '' );
			$preview.empty();
			$set.text( L.set || 'Set featured image' );
			$remove.prop( 'hidden', true );
		} );
	}

	$( function () {
		$( '[data-glimmr-term-image]' ).each( function () {
			wire( this );
		} );

		// The "Add term" form clears itself via AJAX after submit; re-empty preview.
		$( document ).on( 'wp-async-response', function () {
			var $add = $( '#addtag [data-glimmr-term-image]' );
			if ( $add.length ) {
				$add.find( '[data-glimmr-input]' ).val( '' );
				$add.find( '[data-glimmr-preview]' ).empty();
				$add.find( '[data-glimmr-remove]' ).prop( 'hidden', true );
				$add.find( '[data-glimmr-set]' ).text( L.set || 'Set featured image' );
			}
		} );
	} );
}( jQuery ) );
