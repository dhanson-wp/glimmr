( function () {
	function markCopied( button, status, label, visibleLabel ) {
		var textLabel = button.querySelector( '.glmr-action__label' );
		var previousLabel = textLabel ? textLabel.textContent : '';

		button.classList.add( 'is-copied' );
		if ( textLabel && visibleLabel ) {
			textLabel.textContent = visibleLabel;
		}
		if ( status ) {
			status.textContent = label;
		}
		window.setTimeout( function () {
			button.classList.remove( 'is-copied' );
			if ( textLabel && visibleLabel ) {
				textLabel.textContent = previousLabel;
			}
			if ( status ) {
				status.textContent = '';
			}
		}, 1600 );
	}

	function canUseNativeShare( data ) {
		if ( ! navigator.share ) {
			return false;
		}

		if ( ! navigator.canShare ) {
			return true;
		}

		try {
			return navigator.canShare( data );
		} catch ( error ) {
			return false;
		}
	}

	function legacyCopy( text ) {
		var field = document.createElement( 'textarea' );
		var copied = false;

		field.value = text;
		field.setAttribute( 'readonly', '' );
		field.style.position = 'fixed';
		field.style.top = '-9999px';
		field.style.left = '-9999px';
		document.body.appendChild( field );
		field.focus();
		field.select();
		field.setSelectionRange( 0, field.value.length );

		try {
			copied = document.execCommand( 'copy' );
		} catch ( error ) {
			copied = false;
		}

		document.body.removeChild( field );
		return copied;
	}

	function manualCopy( button, status, url, manualLabel, manualStatus ) {
		window.prompt( manualLabel, url );
		markCopied( button, status, manualStatus );
	}

	function copyShareUrl( button, status, url, copiedLabel, copiedText, manualLabel, manualStatus ) {
		if ( navigator.clipboard && navigator.clipboard.writeText ) {
			navigator.clipboard.writeText( url ).then( function () {
				markCopied( button, status, copiedLabel, copiedText );
			} ).catch( function () {
				if ( legacyCopy( url ) ) {
					markCopied( button, status, copiedLabel, copiedText );
					return;
				}
				manualCopy( button, status, url, manualLabel, manualStatus );
			} );
			return;
		}

		if ( legacyCopy( url ) ) {
			markCopied( button, status, copiedLabel, copiedText );
			return;
		}

		manualCopy( button, status, url, manualLabel, manualStatus );
	}

	function wireShare() {
		document.querySelectorAll( '.glmr-action--share' ).forEach( function ( button ) {
			button.addEventListener( 'click', function () {
				var url = button.dataset.shareUrl;
				var title = button.dataset.shareTitle;
				var copiedLabel = button.dataset.copiedLabel || 'Link copied to clipboard.';
				var copiedText = button.dataset.copiedText || 'Copied';
				var sharedLabel = button.dataset.sharedLabel || 'Shared.';
				var sharedText = button.dataset.sharedText || 'Shared';
				var manualLabel = button.dataset.manualLabel || 'Copy this link:';
				var manualStatus = button.dataset.manualStatus || 'Copy the link from the dialog.';
				var status = button.querySelector( '.glmr-action__status' );
				var shareData = { title: title, url: url };

				if ( canUseNativeShare( shareData ) ) {
					navigator.share( shareData ).then( function () {
						markCopied( button, status, sharedLabel, sharedText );
					} ).catch( function ( error ) {
						if ( error && 'AbortError' === error.name ) {
							return;
						}
						copyShareUrl( button, status, url, copiedLabel, copiedText, manualLabel, manualStatus );
					} );
					return;
				}

				copyShareUrl( button, status, url, copiedLabel, copiedText, manualLabel, manualStatus );
			} );
		} );

		document.querySelectorAll( '.glmr-action-menu__copy' ).forEach( function ( button ) {
			button.addEventListener( 'click', function () {
				var menu = button.closest( '.glmr-action-menu' );
				var url = button.dataset.shareUrl;
				var copiedLabel = button.dataset.copiedLabel || 'Link copied to clipboard.';
				var manualLabel = button.dataset.manualLabel || 'Copy this link:';
				var manualStatus = button.dataset.manualStatus || 'Copy the link from the dialog.';
				var status = menu ? menu.querySelector( '.glmr-action__status' ) : null;

				copyShareUrl( button, status, url, copiedLabel, 'Copied', manualLabel, manualStatus );
				if ( menu ) {
					window.setTimeout( function () {
						menu.open = false;
					}, 200 );
				}
			} );
		} );
	}

	function text( selector ) {
		var selectors = Array.isArray( selector ) ? selector : [ selector ];
		var node = null;

		selectors.some( function ( item ) {
			node = document.querySelector( item );
			return !! node;
		} );

		return node ? ( node.innerText || node.textContent || '' ).trim().replace( /\s+/g, ' ' ) : '';
	}

	function lightboxMeta() {
		var aperture = text( [
			'.glmr-exif-field--aperture .glmr-exif-field__value',
			'.wp-block-x3p0-media-data-field--aperture .wp-block-x3p0-media-data-field__value'
		] );
		var shutter = text( [
			'.glmr-exif-field--shutter-speed .glmr-exif-field__value',
			'.wp-block-x3p0-media-data-field--shutter-speed .wp-block-x3p0-media-data-field__value'
		] );
		var iso = text( [
			'.glmr-exif-field--iso .glmr-exif-field__value',
			'.wp-block-x3p0-media-data-field--iso .wp-block-x3p0-media-data-field__value'
		] );
		var values = [ aperture, shutter, iso ? 'ISO ' + iso.replace( /^ISO\s+/i, '' ) : '' ].filter( Boolean );

		return {
			title: text( '.glmr-side-title' ),
			meta: values.join( ' \u00b7 ' )
		};
	}

	function enhanceLightbox() {
		var overlay = document.querySelector( '.wp-lightbox-overlay.active' );
		var data;
		var caption;
		var signature;
		var title;
		var meta;

		if ( ! overlay ) {
			return;
		}

		data = lightboxMeta();
		if ( ! data.title && ! data.meta ) {
			return;
		}

		caption = overlay.querySelector( '.glmr-lightbox-caption' );
		signature = data.title + '\n' + data.meta;
		if ( caption && caption.dataset.glmrCaption === signature ) {
			return;
		}

		if ( ! caption ) {
			caption = document.createElement( 'div' );
			caption.className = 'glmr-lightbox-caption';
			overlay.appendChild( caption );
		}

		caption.dataset.glmrCaption = signature;
		caption.textContent = '';
		if ( data.title ) {
			title = document.createElement( 'div' );
			title.className = 'glmr-lightbox-caption__title';
			title.textContent = data.title;
			caption.appendChild( title );
		}
		if ( data.meta ) {
			meta = document.createElement( 'div' );
			meta.className = 'glmr-lightbox-caption__meta';
			meta.textContent = data.meta;
			caption.appendChild( meta );
		}
	}

	function labelLightboxTriggers() {
		document.querySelectorAll( '.glmr-photo .lightbox-trigger' ).forEach( function ( button ) {
			if ( ! button.getAttribute( 'aria-label' ) ) {
				button.setAttribute( 'aria-label', 'Enlarge photo' );
			}
		} );
	}

	function wireLightbox() {
		var observer;

		labelLightboxTriggers();

		if ( ! window.MutationObserver ) {
			return;
		}

		observer = new MutationObserver( function () {
			labelLightboxTriggers();
			enhanceLightbox();
		} );
		observer.observe( document.body, {
			attributes: true,
			attributeFilter: [ 'class' ],
			childList: true,
			subtree: true
		} );
		enhanceLightbox();
	}

	function init() {
		wireShare();
		wireLightbox();
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
}() );
