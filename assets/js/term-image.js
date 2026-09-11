/**
 * Glimmr — taxonomy featured-image picker.
 *
 * A small wp.media() bridge for the Featured image control on Edit Category /
 * Edit Tag. No build step; depends on jQuery + wp.media (enqueued in PHP).
 */
( function ( $ ) {
	'use strict';

	var frameKey = 'glimmrTermImageFrame';

	function parts( root ) {
		var $root = $( root );

		return {
			root: $root,
			input: $root.find( '[data-glimmr-input]' ),
			preview: $root.find( '[data-glimmr-preview]' ),
			setState: $root.find( '[data-glimmr-set-state]' ),
			empty: $root.find( '[data-glimmr-empty]' ),
			remove: $root.find( '[data-glimmr-remove]' ),
			status: $root.find( '[data-glimmr-status]' )
		};
	}

	function speak( root, message ) {
		var field = parts( root );

		if ( field.status.length ) {
			field.status.text( message );
		}
		if ( window.wp && window.wp.a11y && window.wp.a11y.speak ) {
			window.wp.a11y.speak( message );
		}
	}

	function showSetState( root, src ) {
		var field = parts( root );
		var labels = window.glimmrTermImage || {};
		if ( src ) {
			field.preview.empty().append( $( '<img>', { src: src, alt: labels.alt || 'Current featured image' } ) );
		}
		field.setState.prop( 'hidden', false );
		field.empty.prop( 'hidden', true );
		field.remove.prop( 'hidden', false );
	}

	function showEmptyState( root ) {
		var field = parts( root );
		field.preview.empty();
		field.setState.prop( 'hidden', true );
		field.empty.prop( 'hidden', false );
		field.remove.prop( 'hidden', true );
	}

	function openFrame( root ) {
		var field = parts( root );
		var labels = window.glimmrTermImage || {};
		var frame = field.root.data( frameKey );
		var attempts = field.root.data( 'glimmrTermImageAttempts' ) || 0;

		if ( ! window.wp || ! window.wp.media ) {
			if ( attempts < 80 ) {
				field.root
					.data( 'glimmrTermImageAttempts', attempts + 1 )
					.addClass( 'is-loading' )
					.attr( 'aria-busy', 'true' );
				window.setTimeout( function () {
					openFrame( field.root );
				}, 50 );
			}
			return;
		}

		field.root
			.removeClass( 'is-loading' )
			.removeAttr( 'aria-busy' )
			.removeData( 'glimmrTermImageAttempts' );

		if ( ! frame ) {
			frame = wp.media( {
				title: labels.title || 'Featured image',
				button: { text: labels.button || 'Set featured image' },
				frame: 'select',
				library: { type: 'image' },
				multiple: false,
				state: 'library'
			} );
			frame.on( 'open', function () {
				var id = parseInt( parts( field.root ).input.val(), 10 );
				var selection = frame.state().get( 'selection' );
				if ( frame.content && frame.content.mode ) {
					frame.content.mode( 'browse' );
				}
				if ( id && selection ) {
					selection.reset( [ wp.media.attachment( id ) ] );
				}
			} );
			frame.on( 'select', function () {
				var att = frame.state().get( 'selection' ).first().toJSON();
				var src = att.sizes && att.sizes.medium ? att.sizes.medium.url : att.url;
				field.input.val( att.id );
				showSetState( field.root, src );
				speak( field.root, labels.selected || 'Featured image selected.' );
				window.setTimeout( function () {
					frame.close();
					parts( field.root ).setState.find( '[data-glimmr-set]' ).first().trigger( 'focus' );
				}, 0 );
			} );
			field.root.data( frameKey, frame );
		}

		frame.open();
	}

	$( document )
		.on( 'click.glimmrTermImage', '[data-glimmr-term-image] [data-glimmr-set]', function ( e ) {
			e.preventDefault();
			openFrame( $( this ).closest( '[data-glimmr-term-image]' ) );
		} )
		.on( 'click.glimmrTermImage', '[data-glimmr-term-image] [data-glimmr-remove]', function ( e ) {
			e.preventDefault();
			var $root = $( this ).closest( '[data-glimmr-term-image]' );
			var labels = window.glimmrTermImage || {};
			parts( $root ).input.val( '' );
			showEmptyState( $root );
			speak( $root, labels.removed || 'Featured image removed.' );
			parts( $root ).empty.trigger( 'focus' );
		} );

	$( function () {
		// The "Add term" form clears itself via AJAX after submit; re-empty preview.
		$( document ).on( 'wp-async-response', function () {
			var $add = $( '#addtag [data-glimmr-term-image]' );
			if ( $add.length ) {
				parts( $add ).input.val( '' );
				showEmptyState( $add );
			}
		} );
	} );
}( jQuery ) );
