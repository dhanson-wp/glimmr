/**
 * Glimmr frontend guardrails.
 *
 * Core Navigation owns the drawer state. This only recovers from stale modal
 * locks that can survive browser/page interruptions and leave the document frozen.
 */
( function () {
	'use strict';

	var root = document.documentElement;
	var timer = null;
	var followUpTimer = null;

	function isVisible( element ) {
		var styles;
		var rect;

		if ( ! element ) {
			return false;
		}

		styles = window.getComputedStyle( element );
		rect = element.getBoundingClientRect();

		return 'none' !== styles.display && 'hidden' !== styles.visibility && rect.width > 0 && rect.height > 0;
	}

	function hasVisibleMenuButton( container ) {
		var nav = container.closest( '.wp-block-navigation' );
		var button = nav ? nav.querySelector( '.wp-block-navigation__responsive-container-open' ) : null;

		return isVisible( button );
	}

	function hasActiveLightbox() {
		return isVisible( document.querySelector( '.wp-lightbox-overlay.active' ) );
	}

	function isActiveNavigationModal( container ) {
		return isVisible( container ) && hasVisibleMenuButton( container );
	}

	function clearStaleNavigationState() {
		document.querySelectorAll( '.wp-block-navigation__responsive-container.is-menu-open' ).forEach( function ( container ) {
			if ( isActiveNavigationModal( container ) ) {
				return;
			}

			container.classList.remove( 'has-modal-open', 'is-menu-open' );
			container.removeAttribute( 'role' );
			container.removeAttribute( 'aria-modal' );
		} );
	}

	function hasActiveModal() {
		if ( hasActiveLightbox() ) {
			return true;
		}

		return Array.prototype.some.call(
			document.querySelectorAll( '.wp-block-navigation__responsive-container.is-menu-open' ),
			isActiveNavigationModal
		);
	}

	function clearInlineScrollLock( element ) {
		if ( ! element || ! element.style ) {
			return;
		}

		element.style.removeProperty( 'overflow' );
		element.style.removeProperty( 'overflow-y' );
		element.style.removeProperty( 'position' );
		element.style.removeProperty( 'top' );
		element.style.removeProperty( 'width' );
	}

	function recoverScrollLock() {
		clearStaleNavigationState();

		if ( hasActiveModal() ) {
			return;
		}

		root.classList.remove( 'has-modal-open' );
		clearInlineScrollLock( root );

		if ( document.body ) {
			document.body.classList.remove( 'has-modal-open' );
			clearInlineScrollLock( document.body );
		}
	}

	function scheduleRecover() {
		window.clearTimeout( timer );
		window.clearTimeout( followUpTimer );
		timer = window.setTimeout( function () {
			recoverScrollLock();
			followUpTimer = window.setTimeout( recoverScrollLock, 320 );
		}, 80 );
	}

	window.addEventListener( 'pageshow', scheduleRecover );
	window.addEventListener( 'resize', scheduleRecover );
	window.addEventListener( 'orientationchange', scheduleRecover );
	window.addEventListener( 'visibilitychange', scheduleRecover );
	window.addEventListener( 'wheel', scheduleRecover, { passive: true } );
	window.addEventListener( 'touchmove', scheduleRecover, { passive: true } );
	document.addEventListener( 'click', scheduleRecover, true );
	document.addEventListener( 'keyup', function ( event ) {
		if ( 'Escape' === event.key ) {
			scheduleRecover();
		}
	}, true );

	if ( 'MutationObserver' in window ) {
		new window.MutationObserver( scheduleRecover ).observe( root, {
			attributeFilter: [ 'class', 'style' ],
			attributes: true,
			childList: true,
			subtree: true
		} );
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', scheduleRecover );
	} else {
		scheduleRecover();
	}
}() );
