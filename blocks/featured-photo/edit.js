/**
 * Featured photo block: editor side.
 *
 * Plain JS on purpose. No JSX and no build step; this is one of the theme's best
 * properties for contributors and for WordPress.org.
 *
 * The block is a context provider. The canvas renders the block's own inner blocks
 * (the cover composition carried by the template) under the resolved post's context,
 * the same way core/post-template does, so the editor shows the real hero and the
 * composition stays editable. `recent` and `daily` are resolved by PHP with the same
 * code the front end uses (see inc/featured-photo.php); `manual` is checked here
 * against the REST API so the fallback notice tracks what the owner picks.
 *
 * @package Glimmr
 */
( function ( wp ) {
	'use strict';

	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var useMemo = wp.element.useMemo;
	var __ = wp.i18n.__;
	var registerBlockType = wp.blocks.registerBlockType;
	var blockEditor = wp.blockEditor;
	var components = wp.components;
	var useSelect = wp.data.useSelect;
	var decodeEntities = wp.htmlEntities.decodeEntities;

	var MODES = [ 'recent', 'daily', 'manual' ];
	var PICKER_QUERY = { per_page: 50, _fields: 'id,title,featured_media' };

	/**
	 * Server picks for the non-manual modes, printed by glimmr_featured_photo_editor_data().
	 *
	 * @return {Object} { recent: number, daily: number }
	 */
	function serverPicks() {
		return window.glimmrFeaturedPhoto || {};
	}

	/**
	 * Never trust a stored mode.
	 *
	 * @param {*} mode Raw attribute value.
	 * @return {string} One of recent|daily|manual.
	 */
	function sanitizeMode( mode ) {
		return MODES.indexOf( mode ) === -1 ? 'recent' : mode;
	}

	/**
	 * Work out which post the canvas should show and whether to warn.
	 *
	 * @param {string}     mode        Sanitized mode.
	 * @param {number}     manualId    Stored postId.
	 * @param {Array|null} manualMatch REST result for manualId: null while loading,
	 *                                 [] when it is not a published post, [post] otherwise.
	 * @return {Object} { postId: number, notice: string }
	 */
	function resolve( mode, manualId, manualMatch ) {
		var picks = serverPicks();
		var recent = parseInt( picks.recent, 10 ) || 0;

		if ( mode !== 'manual' ) {
			return { postId: parseInt( picks[ mode ], 10 ) || 0, notice: '' };
		}
		if ( ! manualId ) {
			return {
				postId: recent,
				notice: __( 'No photo chosen yet, so the most recent photo is shown.', 'glimmr' ),
			};
		}
		if ( manualMatch === null ) {
			// Still loading: show the chosen post rather than flashing a fallback.
			return { postId: manualId, notice: '' };
		}
		if ( manualMatch.length && manualMatch[ 0 ].featured_media > 0 ) {
			return { postId: manualId, notice: '' };
		}

		return {
			postId: recent,
			notice: __( 'The chosen photo is no longer available (unpublished, deleted, or without a featured image), so the most recent photo is shown instead.', 'glimmr' ),
		};
	}

	function Edit( props ) {
		var attributes = props.attributes;
		var setAttributes = props.setAttributes;
		var mode = sanitizeMode( attributes.mode );
		var manualId = parseInt( attributes.postId, 10 ) || 0;

		// Recent posts for the picker. Kept light: ids, titles, and whether a featured image exists.
		var recentPosts = useSelect( function ( select ) {
			return select( 'core' ).getEntityRecords( 'postType', 'post', PICKER_QUERY );
		}, [] );

		// The chosen post, fetched as a one-item collection so "not found" resolves to [] instead of hanging.
		var manualMatch = useSelect( function ( select ) {
			if ( mode !== 'manual' || ! manualId ) {
				return null;
			}
			return select( 'core' ).getEntityRecords( 'postType', 'post', {
				include: [ manualId ],
				per_page: 1,
				_fields: 'id,featured_media',
			} );
		}, [ mode, manualId ] );

		var resolved = resolve( mode, manualId, manualMatch );

		var pickerOptions = useMemo( function () {
			return ( recentPosts || [] )
				.filter( function ( post ) {
					return post.featured_media > 0;
				} )
				.map( function ( post ) {
					var title = post.title && post.title.rendered ? decodeEntities( post.title.rendered ) : '';
					return {
						value: String( post.id ),
						label: title || __( '(no title)', 'glimmr' ),
					};
				} );
		}, [ recentPosts ] );

		// Context for the inner blocks. An empty object leaves the template's own context alone.
		var context = useMemo( function () {
			return resolved.postId ? { postId: resolved.postId, postType: 'post' } : {};
		}, [ resolved.postId ] );

		var blockProps = blockEditor.useBlockProps( { className: 'glmr-pin' } );
		var innerBlocksProps = blockEditor.useInnerBlocksProps( blockProps );

		var inspector = el(
			blockEditor.InspectorControls,
			{},
			el(
				components.PanelBody,
				{ title: __( 'Featured photo', 'glimmr' ) },
				el( components.SelectControl, {
					label: __( 'Show', 'glimmr' ),
					value: mode,
					options: [
						{ label: __( 'Most recent', 'glimmr' ), value: 'recent' },
						{ label: __( 'Daily rotation', 'glimmr' ), value: 'daily' },
						{ label: __( 'Choose a photo', 'glimmr' ), value: 'manual' },
					],
					help:
						mode === 'daily'
							? __( 'One photo a day from your 50 newest, the same for every visitor, changing at midnight.', 'glimmr' )
							: mode === 'manual'
								? __( 'Only published photos with a featured image are listed.', 'glimmr' )
								: __( 'Always your newest published photo.', 'glimmr' ),
					onChange: function ( value ) {
						setAttributes( { mode: sanitizeMode( value ) } );
					},
					__nextHasNoMarginBottom: true,
					__next40pxDefaultSize: true,
				} ),
				mode === 'manual' &&
					el( components.ComboboxControl, {
						label: __( 'Photo', 'glimmr' ),
						value: manualId ? String( manualId ) : null,
						options: pickerOptions,
						onChange: function ( value ) {
							setAttributes( { postId: parseInt( value, 10 ) || 0 } );
						},
						__nextHasNoMarginBottom: true,
						__next40pxDefaultSize: true,
					} ),
				! resolved.postId &&
					el(
						components.Notice,
						{ status: 'info', isDismissible: false },
						__( 'No published photo with a featured image yet. Visitors see an empty state here.', 'glimmr' )
					),
				resolved.postId && resolved.notice &&
					el( components.Notice, { status: 'warning', isDismissible: false }, resolved.notice )
			)
		);

		return el(
			Fragment,
			{},
			inspector,
			el( blockEditor.BlockContextProvider, { value: context }, el( 'div', innerBlocksProps ) )
		);
	}

	function save() {
		var blockProps = blockEditor.useBlockProps.save( { className: 'glmr-pin' } );
		return el( 'div', blockProps, el( blockEditor.InnerBlocks.Content ) );
	}

	registerBlockType( 'glimmr/featured-photo', {
		edit: Edit,
		save: save,
	} );
} )( window.wp );
