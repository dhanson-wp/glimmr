<?php
/**
 * Glimmr theme bootstrap.
 *
 * Almost all design lives in theme.json + style variations. PHP here is kept to a
 * minimum: asset loading, pattern categories, the one custom feature (taxonomy
 * featured image), theme block-binding sources, and a couple of cheap seams for the
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

	$script_rel = 'assets/js/glimmr.js';
	wp_enqueue_script(
		'glimmr-theme',
		get_theme_file_uri( $script_rel ),
		array(),
		(string) filemtime( get_theme_file_path( $script_rel ) ),
		array( 'in_footer' => true )
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

/**
 * Editor-visible style hooks for core blocks that need Glimmr-specific layout.
 */
function glimmr_register_block_styles() {
	register_block_style(
		'core/post-template',
		array(
			'name'  => 'glimmr-masonry',
			'label' => __( 'Glimmr masonry', 'glimmr' ),
		)
	);
}
add_action( 'init', 'glimmr_register_block_styles' );

/**
 * Get the current site title for theme-owned identity readouts.
 *
 * @return string
 */
function glimmr_get_site_title_text() {
	$site_name = trim( wp_strip_all_tags( get_bloginfo( 'name' ) ) );

	return '' !== $site_name ? $site_name : __( 'glimmr', 'glimmr' );
}

/**
 * Return a bundled theme image only when the file actually ships with the theme.
 *
 * @param string $relative_path Theme-relative image path.
 * @return string
 */
function glimmr_get_optional_theme_image_url( $relative_path ) {
	$relative_path = ltrim( (string) $relative_path, '/' );

	if ( '' === $relative_path || ! file_exists( get_theme_file_path( $relative_path ) ) ) {
		return '';
	}

	return get_theme_file_uri( $relative_path );
}

/**
 * Resolve live photo URLs for pattern previews.
 *
 * @param int    $limit Number of URLs to return.
 * @param string $size  WordPress image size.
 * @return string[]
 */
function glimmr_get_pattern_image_urls( $limit = 6, $size = 'large' ) {
	static $cache = array();

	$limit = max( 1, absint( $limit ) );
	$size  = is_string( $size ) && '' !== $size ? $size : 'large';
	$key   = $limit . ':' . $size;

	if ( isset( $cache[ $key ] ) ) {
		return $cache[ $key ];
	}

	$urls          = array();
	$thumbnail_ids = array();
	$post_ids      = get_posts(
		array(
			'fields'         => 'ids',
			'meta_key'       => '_thumbnail_id',
			'no_found_rows'  => true,
			'post_status'    => 'publish',
			'post_type'      => 'post',
			'posts_per_page' => $limit,
		)
	);

	foreach ( $post_ids as $post_id ) {
		$thumbnail_id = (int) get_post_thumbnail_id( $post_id );
		if ( ! $thumbnail_id ) {
			continue;
		}

		$url = wp_get_attachment_image_url( $thumbnail_id, $size );
		if ( $url ) {
			$urls[]          = $url;
			$thumbnail_ids[] = $thumbnail_id;
		}
	}

	if ( count( $urls ) < $limit ) {
		$attachment_ids = get_posts(
			array(
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'post__not_in'   => $thumbnail_ids,
				'post_mime_type' => 'image',
				'post_status'    => 'inherit',
				'post_type'      => 'attachment',
				'posts_per_page' => $limit * 2,
			)
		);

		foreach ( $attachment_ids as $attachment_id ) {
			$url = wp_get_attachment_image_url( (int) $attachment_id, $size );
			if ( $url ) {
				$urls[] = $url;
			}

			if ( count( $urls ) >= $limit ) {
				break;
			}
		}
	}

	$cache[ $key ] = array_slice( array_values( array_unique( $urls ) ), 0, $limit );

	return $cache[ $key ];
}

/**
 * Resolve one live photo URL for a pattern, looping when fewer images exist.
 *
 * @param int    $index Zero-based image index.
 * @param string $size  WordPress image size.
 * @return string
 */
function glimmr_get_pattern_image_url( $index = 0, $size = 'large' ) {
	$index = max( 0, absint( $index ) );
	$urls  = glimmr_get_pattern_image_urls( max( 6, $index + 1 ), $size );

	if ( empty( $urls ) ) {
		return '';
	}

	return $urls[ $index % count( $urls ) ];
}

/**
 * Keep identity patterns on core/avatar while using the bundled portrait artwork.
 *
 * @param string $block_content Rendered avatar markup.
 * @param array  $block         Parsed avatar block.
 * @return string
 */
function glimmr_render_identity_avatar( $block_content, $block ) {
	$class_name = $block['attrs']['className'] ?? '';
	$is_identity_avatar = false !== strpos( $class_name, 'glmr-about-avatar' )
		|| false !== strpos( $class_name, 'glmr-linkbio-avatar' )
		|| false !== strpos( $class_name, 'glmr-journal-author-avatar' );

	if ( ! $is_identity_avatar ) {
		return $block_content;
	}

	$portrait_url = glimmr_get_optional_theme_image_url( 'assets/img/portrait.jpg' );
	if ( '' === $portrait_url ) {
		return $block_content;
	}

	$avatar_alt = function_exists( 'glimmr_get_primary_photo_author_name' )
		? glimmr_get_primary_photo_author_name()
		: glimmr_get_site_title_text();

	if ( '' === trim( $block_content ) ) {
		$size = isset( $block['attrs']['size'] ) ? absint( $block['attrs']['size'] ) : 42;
		$size = $size > 0 ? $size : 42;

		return sprintf(
			'<div class="wp-block-avatar %1$s"><img src="%2$s" alt="%3$s" class="avatar avatar-%4$d photo wp-block-avatar__image" height="%4$d" width="%4$d" style="border-radius:9999px;" loading="lazy" decoding="async"/></div>',
			esc_attr( $class_name ),
			esc_url( $portrait_url ),
			esc_attr( $avatar_alt ),
			$size
		);
	}

	if ( ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
		return $block_content;
	}

	$processor = new WP_HTML_Tag_Processor( $block_content );
	if ( ! $processor->next_tag( 'img' ) ) {
		return $block_content;
	}

	$processor->set_attribute( 'src', $portrait_url );
	$processor->remove_attribute( 'srcset' );
	$processor->remove_attribute( 'sizes' );
	$processor->set_attribute( 'alt', $avatar_alt );

	return $processor->get_updated_html();
}
add_filter( 'render_block_core/avatar', 'glimmr_render_identity_avatar', 10, 2 );

/**
 * Return the live category terms that behave as Glimmr albums.
 *
 * @return WP_Term[]
 */
function glimmr_get_album_terms() {
	$terms = get_terms(
		array(
			'taxonomy'   => 'category',
			'hide_empty' => true,
		)
	);

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return array();
	}

	usort(
		$terms,
		static function ( $a, $b ) {
			if ( (int) $a->count === (int) $b->count ) {
				return strcasecmp( $a->name, $b->name );
			}

			return (int) $b->count <=> (int) $a->count;
		}
	);

	return $terms;
}

/**
 * Build cover-card data for album patterns and templates.
 *
 * @param int $limit Maximum number of live albums. Zero means all.
 * @return array[]
 */
function glimmr_get_album_cards( $limit = 0 ) {
	$terms     = glimmr_get_album_terms();
	$fallbacks = glimmr_get_pattern_image_urls( max( 4, count( $terms ) ), 'large' );
	$cards     = array();

	if ( $limit > 0 ) {
		$terms = array_slice( $terms, 0, $limit );
	}

	foreach ( $terms as $index => $term ) {
		if ( ! $term instanceof WP_Term ) {
			continue;
		}

		$url = get_term_link( $term );
		if ( is_wp_error( $url ) ) {
			continue;
		}

		$image = function_exists( 'glimmr_get_term_featured_image_url' )
			? glimmr_get_term_featured_image_url( $term->term_id, 'large' )
			: '';
		if ( '' === $image && ! empty( $fallbacks ) ) {
			$image = $fallbacks[ $index % count( $fallbacks ) ];
		}

		$cards[] = array(
			'img'   => $image,
			'title' => $term->name,
			'url'   => $url,
			'count' => sprintf(
				/* translators: %s: Number of photos in an album/category. */
				_n( '%s photo', '%s photos', (int) $term->count, 'glimmr' ),
				number_format_i18n( (int) $term->count )
			),
		);
	}

	if ( empty( $cards ) ) {
		$default_images = glimmr_get_pattern_image_urls( 4, 'large' );
		$cards = array(
			array( 'img' => $default_images[0] ?? '', 'title' => __( 'Beaches', 'glimmr' ), 'url' => home_url( '/albums/' ), 'count' => __( '0 photos', 'glimmr' ) ),
			array( 'img' => $default_images[1] ?? '', 'title' => __( 'Food', 'glimmr' ), 'url' => home_url( '/albums/' ), 'count' => __( '0 photos', 'glimmr' ) ),
			array( 'img' => $default_images[2] ?? '', 'title' => __( 'Lego', 'glimmr' ), 'url' => home_url( '/albums/' ), 'count' => __( '0 photos', 'glimmr' ) ),
			array( 'img' => $default_images[3] ?? '', 'title' => __( 'Portraits', 'glimmr' ), 'url' => home_url( '/albums/' ), 'count' => __( '0 photos', 'glimmr' ) ),
		);
	}

	return $cards;
}

/**
 * Render the complete Albums index pattern from live album card data.
 *
 * @return string
 */
function glimmr_render_albums_index_pattern() {
	$glimmr_albums = glimmr_get_album_cards();

	ob_start();
	?>
<!-- wp:group {"align":"wide","className":"glmr-album-showcase glmr-albums-index","style":{"spacing":{"blockGap":"var:preset|spacing|30","padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|50"}}},"layout":{"type":"constrained"}} -->
	<div class="wp-block-group alignwide glmr-album-showcase glmr-albums-index" style="padding-top:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--50)">
		<!-- wp:group {"className":"glmr-album-grid","style":{"spacing":{"blockGap":"14px"}},"layout":{"type":"grid","columnCount":4,"minimumColumnWidth":null}} -->
		<div class="wp-block-group glmr-album-grid">
			<?php foreach ( $glimmr_albums as $glimmr_album ) : ?>
			<?php
			$glimmr_album_attrs = array(
				'dimRatio'        => 30,
				'overlayColor'    => 'ink',
				'isDark'          => true,
				'contentPosition' => 'bottom left',
				'className'       => 'glmr-album-card',
				'style'           => array(
					'dimensions' => array(
						'aspectRatio' => '3/4',
					),
					'spacing'    => array(
						'padding' => array(
							'top'    => '18px',
							'bottom' => '18px',
							'left'   => '18px',
							'right'  => '18px',
						),
					),
				),
			);
			if ( ! empty( $glimmr_album['img'] ) ) {
				$glimmr_album_attrs['url'] = $glimmr_album['img'];
			}
			?>
			<!-- wp:cover <?php echo wp_json_encode( $glimmr_album_attrs ); ?> -->
			<div class="wp-block-cover is-dark has-custom-content-position is-position-bottom-left glmr-album-card" style="padding-top:18px;padding-right:18px;padding-bottom:18px;padding-left:18px">
				<span aria-hidden="true" class="wp-block-cover__background has-ink-background-color has-background-dim-30 has-background-dim"></span>
				<?php if ( ! empty( $glimmr_album['img'] ) ) : ?>
				<img class="wp-block-cover__image-background" src="<?php echo esc_url( $glimmr_album['img'] ); ?>" alt=""/>
				<?php endif; ?>
				<div class="wp-block-cover__inner-container">
				<!-- wp:heading {"level":2,"fontSize":"large","textColor":"white"} -->
				<h2 class="wp-block-heading has-white-color has-text-color has-large-font-size"><a href="<?php echo esc_url( $glimmr_album['url'] ); ?>"><?php echo esc_html( $glimmr_album['title'] ); ?></a></h2>
				<!-- /wp:heading -->
				<!-- wp:paragraph {"className":"glmr-album-count","fontSize":"meta"} -->
				<p class="glmr-album-count has-meta-font-size"><?php echo esc_html( $glimmr_album['count'] ); ?></p>
				<!-- /wp:paragraph -->
			</div>
		</div>
		<!-- /wp:cover -->
		<?php endforeach; ?>
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
	<?php
	return trim( ob_get_clean() );
}

/**
 * Register dynamic theme-owned patterns that need current site data.
 */
function glimmr_register_dynamic_patterns() {
	if ( WP_Block_Patterns_Registry::get_instance()->is_registered( 'glimmr/albums-index' ) ) {
		return;
	}

	register_block_pattern(
		'glimmr/albums-index',
		array(
			'title'       => __( 'Albums index', 'glimmr' ),
			'categories'  => array( 'glimmr-photo', 'gallery' ),
			'description' => __( 'A complete index of every non-empty album/category.', 'glimmr' ),
			'inserter'    => false,
			'content'     => glimmr_render_albums_index_pattern(),
		)
	);
}
add_action( 'init', 'glimmr_register_dynamic_patterns', 11 );

/**
 * Whether the current route belongs under the Latest navigation item.
 *
 * @return bool
 */
function glimmr_is_latest_navigation_route() {
	return is_front_page() || is_home() || is_search() || is_404() || is_singular( 'post' );
}

/**
 * Determine whether a header navigation URL represents the current route.
 *
 * @param string $url Navigation URL.
 * @return bool
 */
function glimmr_navigation_url_is_current( $url ) {
	$path = wp_parse_url( $url, PHP_URL_PATH );
	if ( ! is_string( $path ) ) {
		return false;
	}

	$path = trailingslashit( '/' . ltrim( $path, '/' ) );

	if ( '/' === $path ) {
		return glimmr_is_latest_navigation_route();
	}

	if ( '/albums/' === $path ) {
		return is_page( 'albums' ) || is_category();
	}

	if ( '/tags/' === $path ) {
		return is_page( 'tags' ) || is_tag();
	}

	if ( '/about/' === $path ) {
		return is_page( 'about' );
	}

	if ( '/popular/' === $path ) {
		return is_page( 'popular' );
	}

	$request_uri  = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	$request_path = wp_parse_url( $request_uri, PHP_URL_PATH );
	$request_path = is_string( $request_path ) ? trailingslashit( '/' . ltrim( $request_path, '/' ) ) : '';

	return $request_path === $path;
}

/**
 * Add current-menu classes to custom Navigation links that represent theme routes.
 *
 * Core page links get this for free. Glimmr keeps the header on core/navigation
 * with custom links, so this restores the standard current-state vocabulary for
 * CSS and assistive technology without replacing the block.
 *
 * @param string $block_content Rendered navigation link markup.
 * @param array  $block         Parsed navigation link block.
 * @return string
 */
function glimmr_mark_current_navigation_link( $block_content, $block ) {
	$url = $block['attrs']['url'] ?? '';
	if ( ! is_string( $url ) || '' === $url || ! glimmr_navigation_url_is_current( $url ) ) {
		return $block_content;
	}

	if ( class_exists( 'WP_HTML_Tag_Processor' ) ) {
		$processor = new WP_HTML_Tag_Processor( $block_content );
		if ( $processor->next_tag( 'li' ) && method_exists( $processor, 'add_class' ) ) {
			$processor->add_class( 'current-menu-item' );
		}
		if ( $processor->next_tag( 'a' ) ) {
			$processor->set_attribute( 'aria-current', 'page' );
		}

		return $processor->get_updated_html();
	}

	$updated = preg_replace( '/(<li\b[^>]*\bclass="[^"]*)"/', '$1 current-menu-item"', $block_content, 1 );
	$updated = is_string( $updated ) ? $updated : $block_content;
	$updated = preg_replace( '/<a\b(?![^>]*\baria-current=)/', '<a aria-current="page"', $updated, 1 );

	return is_string( $updated ) ? $updated : $block_content;
}
add_filter( 'render_block_core/navigation-link', 'glimmr_mark_current_navigation_link', 20, 2 );

/**
 * Mark the core Home Link as current on the photostream home route.
 *
 * @param string $block_content Rendered home-link markup.
 * @return string
 */
function glimmr_mark_current_home_link( $block_content ) {
	if ( ! glimmr_is_latest_navigation_route() ) {
		return $block_content;
	}

	if ( class_exists( 'WP_HTML_Tag_Processor' ) ) {
		$processor = new WP_HTML_Tag_Processor( $block_content );
		if ( $processor->next_tag( 'li' ) && method_exists( $processor, 'add_class' ) ) {
			$processor->add_class( 'current-menu-item' );
		}
		if ( $processor->next_tag( 'a' ) ) {
			$processor->set_attribute( 'aria-current', 'page' );
		}

		return $processor->get_updated_html();
	}

	$updated = preg_replace( '/(<li\b[^>]*\bclass="[^"]*)"/', '$1 current-menu-item"', $block_content, 1 );
	$updated = is_string( $updated ) ? $updated : $block_content;
	$updated = preg_replace( '/<a\b(?![^>]*\baria-current=)/', '<a aria-current="page"', $updated, 1 );

	return is_string( $updated ) ? $updated : $block_content;
}
add_filter( 'render_block_core/home-link', 'glimmr_mark_current_home_link', 20 );

/**
 * Add category counts to the Albums submenu while keeping the menu as core/navigation.
 *
 * @param string $block_content Rendered navigation link markup.
 * @param array  $block         Parsed navigation link block.
 * @return string
 */
function glimmr_render_album_navigation_link( $block_content, $block ) {
	$class_name = $block['attrs']['className'] ?? '';
	if ( false === strpos( $class_name, 'glmr-album-link' ) ) {
		return $block_content;
	}

	$url  = $block['attrs']['url'] ?? '';
	$path = wp_parse_url( $url, PHP_URL_PATH );
	if ( ! is_string( $path ) || ! preg_match( '#/category/([^/]+)/?$#', $path, $matches ) ) {
		return $block_content;
	}

	$term = get_term_by( 'slug', sanitize_title( $matches[1] ), 'category' );
	if ( ! $term || is_wp_error( $term ) ) {
		return $block_content;
	}

	$count_number = number_format_i18n( (int) $term->count );
	$count_label  = sprintf(
		/* translators: %s: Number of photos in an album/category. */
		_n( '%s photo', '%s photos', (int) $term->count, 'glimmr' ),
		$count_number
	);
	$link_label   = sprintf( '%s, %s', $term->name, $count_label );
	$count        = sprintf(
		'<span class="glmr-album-count" aria-hidden="true">%s</span>',
		esc_html( $count_number )
	);

	$updated = str_replace( '</a>', ' ' . $count . '</a>', $block_content );

	if ( class_exists( 'WP_HTML_Tag_Processor' ) ) {
		$processor = new WP_HTML_Tag_Processor( $updated );
		if ( $processor->next_tag( 'a' ) ) {
			$processor->set_attribute( 'aria-label', $link_label );
			return $processor->get_updated_html();
		}
	}

	$updated = preg_replace(
		'/<a\b(?![^>]*\baria-label=)/',
		'<a aria-label="' . esc_attr( $link_label ) . '"',
		$updated,
		1
	);

	return is_string( $updated ) ? $updated : $block_content;
}
add_filter( 'render_block_core/navigation-link', 'glimmr_render_album_navigation_link', 10, 2 );

/**
 * Render the Albums submenu from live categories while preserving core/navigation.
 *
 * The template part keeps a core/navigation-submenu block so the Site Editor and
 * mobile overlay remain native. This scoped render pass only refreshes the
 * submenu items to match the site's current non-empty category albums.
 *
 * @param string $block_content Rendered navigation submenu markup.
 * @param array  $block         Parsed navigation submenu block.
 * @return string
 */
function glimmr_render_album_navigation_submenu( $block_content, $block ) {
	$class_name = $block['attrs']['className'] ?? '';
	if ( false === strpos( $class_name, 'glmr-nav-albums' ) ) {
		return $block_content;
	}

	$is_albums_current = is_category() || is_page( 'albums' );
	$terms = glimmr_get_album_terms();
	if ( empty( $terms ) ) {
		return $block_content;
	}

	$items = '';
	foreach ( $terms as $term ) {
		if ( ! $term instanceof WP_Term ) {
			continue;
		}

		$link = get_term_link( $term );
		if ( is_wp_error( $link ) ) {
			continue;
		}

		$is_current  = is_category( $term->term_id );
		$count_number = number_format_i18n( (int) $term->count );
		$count_label  = sprintf(
			/* translators: %s: Number of photos in an album/category. */
			_n( '%s photo', '%s photos', (int) $term->count, 'glimmr' ),
			$count_number
		);
		$link_label   = sprintf( '%s, %s', $term->name, $count_label );
		$items       .= sprintf(
			'<li class="wp-block-navigation-item glmr-album-link wp-block-navigation-link%4$s"><a class="wp-block-navigation-item__content" href="%1$s" aria-label="%6$s"%5$s><span class="wp-block-navigation-item__label">%2$s</span> <span class="glmr-album-count" aria-hidden="true">%3$s</span></a></li>',
			esc_url( $link ),
			esc_html( $term->name ),
			esc_html( $count_number ),
			$is_current ? ' current-menu-item' : '',
			$is_current ? ' aria-current="page"' : '',
			esc_attr( $link_label )
		);
	}

	$items .= sprintf(
		'<li class="wp-block-navigation-item glmr-album-foot wp-block-navigation-link%3$s"><a class="wp-block-navigation-item__content" href="%1$s"%4$s><span class="wp-block-navigation-item__label">%2$s</span></a></li>',
		esc_url( home_url( '/albums/' ) ),
		esc_html__( 'Browse all albums', 'glimmr' ),
		is_page( 'albums' ) ? ' current-menu-item' : '',
		is_page( 'albums' ) ? ' aria-current="page"' : ''
	);

	$updated = preg_replace(
		'/(<ul\b[^>]*\bwp-block-navigation__submenu-container\b[^>]*>).*?(<\/ul>)/s',
		'$1' . $items . '$2',
		$block_content,
		1
	);

	$updated = is_string( $updated ) ? $updated : $block_content;

	if ( $is_albums_current && class_exists( 'WP_HTML_Tag_Processor' ) ) {
		$processor = new WP_HTML_Tag_Processor( $updated );
		if ( $processor->next_tag( 'li' ) && method_exists( $processor, 'add_class' ) ) {
			$processor->add_class( 'current-menu-ancestor' );
			$updated = $processor->get_updated_html();
		}
	}

	return $updated;
}
add_filter( 'render_block_core/navigation-submenu', 'glimmr_render_album_navigation_submenu', 10, 2 );

/**
 * Build the Glimmr wordmark treatment from the current site title.
 *
 * @param bool $include_screen_reader_text Whether to include hidden fallback text.
 * @return string
 */
function glimmr_get_wordmark_html( $include_screen_reader_text = true ) {
	$site_name       = glimmr_get_site_title_text();
	$wordmark_source = function_exists( 'mb_strtolower' ) ? mb_strtolower( $site_name, 'UTF-8' ) : strtolower( $site_name );
	$wordmark_html   = esc_html( $wordmark_source );
	$wordmark_html   = preg_replace( '/i/', '<span class="glmr-wordmark__dotless">&#305;</span>', $wordmark_html, 1 );
	$wordmark_html   = is_string( $wordmark_html ) ? $wordmark_html : esc_html( $wordmark_source );

	$output = sprintf(
		'<span class="glmr-wordmark__text" aria-hidden="true">%s</span>',
		$wordmark_html
	);

	if ( $include_screen_reader_text ) {
		$output .= sprintf(
			'<span class="screen-reader-text">%s</span>',
			esc_html( $site_name )
		);
	}

	return $output;
}

/**
 * Render the Glimmr wordmark treatment while preserving the core Site Title block.
 *
 * The design uses a dotless-i with a custom pink tittle; the site title text
 * stays available to screen readers through the home link's accessible name.
 *
 * @param string $block_content Rendered site-title markup.
 * @param array  $block         Parsed site-title block.
 * @return string
 */
function glimmr_render_wordmark_site_title( $block_content, $block ) {
	$class_name = $block['attrs']['className'] ?? '';
	if ( false === strpos( $class_name, 'glmr-wordmark' ) ) {
		return $block_content;
	}

	$wordmark = glimmr_get_wordmark_html( false );
	$count    = 0;

	$updated = preg_replace_callback(
		'/(<a\b[^>]*>).*?(<\/a>)/s',
		static function ( $matches ) use ( $wordmark ) {
			return $matches[1] . $wordmark . $matches[2];
		},
		$block_content,
		1,
		$count
	);

	if ( is_string( $updated ) && $count > 0 ) {
		$processor = new WP_HTML_Tag_Processor( $updated );
		if ( $processor->next_tag( 'a' ) ) {
			$processor->set_attribute( 'aria-label', glimmr_get_site_title_text() );
			$updated = $processor->get_updated_html();
		}

		return $updated;
	}

	$wordmark = glimmr_get_wordmark_html();
	$count    = 0;
	$updated  = preg_replace_callback(
		'/(<(p|h[1-6])\b[^>]*>).*?(<\/\2>)/s',
		static function ( $matches ) use ( $wordmark ) {
			return $matches[1] . $wordmark . $matches[3];
		},
		$block_content,
		1,
		$count
	);

	if ( is_string( $updated ) && $count > 0 ) {
		return $updated;
	}

	return $block_content;
}
add_filter( 'render_block_core/site-title', 'glimmr_render_wordmark_site_title', 10, 2 );

/**
 * Give linked photostream images an accessible name while keeping core blocks.
 *
 * The visual title overlay is a separate core/post-title link. When the thumbnail
 * alt text is empty, the linked core/post-featured-image can otherwise render as
 * an empty anchor.
 *
 * @param string $block_content Rendered featured-image markup.
 * @param array  $block         Parsed featured-image block.
 * @return string
 */
function glimmr_label_photo_tile_featured_image_link( $block_content, $block ) {
	$class_name = $block['attrs']['className'] ?? '';
	if ( empty( $block['attrs']['isLink'] ) || false === strpos( $class_name, 'photo-tile__img' ) ) {
		return $block_content;
	}

	$title = trim( wp_strip_all_tags( get_the_title() ) );
	if ( '' === $title ) {
		return $block_content;
	}

	$label = sprintf(
		/* translators: %s: Post title. */
		__( 'View %s', 'glimmr' ),
		$title
	);

	if ( class_exists( 'WP_HTML_Tag_Processor' ) ) {
		$processor = new WP_HTML_Tag_Processor( $block_content );
		if ( $processor->next_tag( array( 'tag_name' => 'A' ) ) && '' === trim( (string) $processor->get_attribute( 'aria-label' ) ) ) {
			$processor->set_attribute( 'aria-label', $label );
			return $processor->get_updated_html();
		}

		return $block_content;
	}

	$updated = preg_replace(
		'/<a\\b(?![^>]*\\baria-label=)/',
		'<a aria-label="' . esc_attr( $label ) . '"',
		$block_content,
		1
	);

	return is_string( $updated ) ? $updated : $block_content;
}
add_filter( 'render_block_core/post-featured-image', 'glimmr_label_photo_tile_featured_image_link', 10, 2 );

/**
 * Keep search.html on core/query-title while matching the designed title copy.
 *
 * Core can render the dynamic search term, but not the Glimmr split styling
 * ("Results for" plus a pink quoted term), so this filter is scoped to the
 * search template's `glmr-search-summary__title` class.
 *
 * @param string $block_content Rendered query-title markup.
 * @param array  $block         Parsed query-title block.
 * @return string
 */
function glimmr_render_search_query_title( $block_content, $block ) {
	$class_name = $block['attrs']['className'] ?? '';
	if ( ! is_search() || 'search' !== ( $block['attrs']['type'] ?? '' ) || false === strpos( $class_name, 'glmr-search-summary__title' ) ) {
		return $block_content;
	}

	$query = get_search_query();
	$title = sprintf(
		/* translators: %s: Search query. */
		esc_html__( 'Results for %s', 'glimmr' ),
		'<em>&ldquo;' . esc_html( $query ) . '&rdquo;</em>'
	);

	$trimmed = trim( $block_content );
	if ( preg_match( '/^<(h[1-6]|p)([^>]*)>.*<\\/\\1>$/s', $trimmed, $matches ) ) {
		return '<' . $matches[1] . $matches[2] . '>' . $title . '</' . $matches[1] . '>';
	}

	return $block_content;
}
add_filter( 'render_block_core/query-title', 'glimmr_render_search_query_title', 10, 2 );

/**
 * Render the archive chip rail with core/categories data and handoff-level markup.
 *
 * The template still owns a `core/categories` block. This scoped filter only swaps
 * the block's front-end HTML for the Glimmr chip shape so counts sit inside the
 * pill and empty current terms can remain visible. The template uses
 * `core/categories`; keep this as the album/category rail on tag archives too.
 *
 * @param string $block_content Rendered categories markup.
 * @param array  $block         Parsed categories block.
 * @return string
 */
function glimmr_render_category_chips( $block_content, $block ) {
	$class_name = $block['attrs']['className'] ?? '';
	if ( false === strpos( $class_name, 'glmr-chips' ) || ! empty( $block['attrs']['displayAsDropdown'] ) ) {
		return $block_content;
	}

	$current  = get_queried_object();
	$taxonomy = isset( $block['attrs']['taxonomy'] ) && is_string( $block['attrs']['taxonomy'] ) ? $block['attrs']['taxonomy'] : 'category';

	if ( ! taxonomy_exists( $taxonomy ) ) {
		return $block_content;
	}

	$args = array(
		'taxonomy'   => $taxonomy,
		'hide_empty' => empty( $block['attrs']['showEmpty'] ),
		'orderby'    => 'name',
		'order'      => 'ASC',
	);
	if ( ! empty( $block['attrs']['showOnlyTopLevel'] ) ) {
		$args['parent'] = 0;
	}

	$terms = get_terms( $args );
	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return $block_content;
	}

	$classes = array( 'wp-block-categories-list', 'wp-block-categories-taxonomy-' . sanitize_html_class( $taxonomy ), 'wp-block-categories' );
	if ( ! empty( $block['attrs']['align'] ) ) {
		$classes[] = 'align' . sanitize_html_class( $block['attrs']['align'] );
	}
	foreach ( preg_split( '/\s+/', $class_name ) as $custom_class ) {
		if ( '' !== $custom_class ) {
			$classes[] = sanitize_html_class( $custom_class );
		}
	}

	$ancestors = ( $current instanceof WP_Term && is_taxonomy_hierarchical( $taxonomy ) )
		? get_ancestors( (int) $current->term_id, $taxonomy, 'taxonomy' )
		: array();
	$show_count = ! empty( $block['attrs']['showPostCounts'] );
	$items      = '';

	foreach ( $terms as $term ) {
		if ( ! $term instanceof WP_Term ) {
			continue;
		}

		$is_current_term = $current instanceof WP_Term && $current->taxonomy === $term->taxonomy && (int) $current->term_id === (int) $term->term_id;
		if ( 0 === (int) $term->count && ! $is_current_term && function_exists( 'glimmr_get_term_featured_image_url' ) && ! glimmr_get_term_featured_image_url( $term->term_id, 'thumbnail' ) ) {
			continue;
		}

		$link = get_term_link( $term );
		if ( is_wp_error( $link ) ) {
			continue;
		}

		$item_classes = array( 'cat-item', 'cat-item-' . (int) $term->term_id );
		if ( $current instanceof WP_Term && $current->taxonomy === $term->taxonomy ) {
			if ( $is_current_term ) {
				$item_classes[] = 'current-cat';
			} elseif ( in_array( (int) $term->term_id, array_map( 'intval', $ancestors ), true ) ) {
				$item_classes[] = 'current-cat-ancestor';
			}
		}

			$name       = function_exists( 'mb_strtolower' ) ? mb_strtolower( $term->name, 'UTF-8' ) : strtolower( $term->name );
			$count_text = '';
			$count      = ( $show_count && (int) $term->count > 0 )
				? sprintf( '<span class="ct" aria-hidden="true">%s</span>', esc_html( number_format_i18n( (int) $term->count ) ) )
				: '';
		if ( '' !== $count ) {
			$count_text = sprintf(
				/* translators: %s: Number of photos in a category or tag. */
				_n( '%s photo', '%s photos', (int) $term->count, 'glimmr' ),
				number_format_i18n( (int) $term->count )
			);
		}
		$aria_label   = '' !== $count_text ? sprintf( '%s, %s', $term->name, $count_text ) : $term->name;
			$aria_current = $is_current_term ? ' aria-current="page"' : '';
			$count_markup = '' !== $count ? ' ' . $count : '';

			$items .= sprintf(
				'<li class="%1$s"><a href="%2$s" aria-label="%6$s"%5$s><span class="glmr-chip-label">%3$s</span>%4$s</a></li>',
				esc_attr( implode( ' ', $item_classes ) ),
				esc_url( $link ),
				esc_html( $name ),
				$count_markup,
				$aria_current,
				esc_attr( $aria_label )
			);
	}

	return sprintf( '<ul class="%1$s">%2$s</ul>', esc_attr( trim( implode( ' ', array_unique( $classes ) ) ) ), $items );
}
add_filter( 'render_block_core/categories', 'glimmr_render_category_chips', 10, 2 );

/**
 * Match the Tags index to the Glimmr chip count treatment while keeping core/tag-cloud.
 *
 * Core includes parentheses in tag-cloud counts. The design system uses the same
 * quiet inline count as archive chips, so this is scoped to the Tags page block.
 *
 * @param string $block_content Rendered tag-cloud markup.
 * @param array  $block         Parsed tag-cloud block.
 * @return string
 */
function glimmr_render_tag_index_counts( $block_content, $block ) {
	$class_name = $block['attrs']['className'] ?? '';
	if ( false === strpos( $class_name, 'glmr-tag-index' ) ) {
		return $block_content;
	}

	$updated = preg_replace_callback(
		'/<a\b([^>]*)>(.*?)<span class="tag-link-count">\s*\(?\s*([0-9.,]+)\s*\)?\s*<\/span><\/a>/s',
		static function ( $matches ) {
			$attrs         = preg_replace( '/\saria-label=(["\']).*?\1/', '', $matches[1] );
			$attrs         = is_string( $attrs ) ? $attrs : $matches[1];
			$name          = trim( wp_strip_all_tags( html_entity_decode( $matches[2], ENT_QUOTES, get_bloginfo( 'charset' ) ) ) );
			$count_raw     = preg_replace( '/[^\d]/', '', $matches[3] );
			$count         = is_string( $count_raw ) ? absint( $count_raw ) : 0;
			$count_display = number_format_i18n( $count );
			$count_label   = sprintf(
				/* translators: %s: Number of photos in a tag. */
				_n( '%s photo', '%s photos', $count, 'glimmr' ),
				$count_display
			);
			$aria_label    = '' !== $name ? sprintf( '%s, %s', $name, $count_label ) : $count_label;

			return sprintf(
				'<a%1$s aria-label="%2$s">%3$s <span class="tag-link-count" aria-hidden="true">%4$s</span></a>',
				$attrs,
				esc_attr( $aria_label ),
				$matches[2],
				esc_html( $count_display )
			);
		},
		$block_content
	);

	return is_string( $updated ) ? $updated : $block_content;
}
add_filter( 'render_block_core/tag-cloud', 'glimmr_render_tag_index_counts', 10, 2 );

/**
 * Register the small dynamic theme blocks that cannot be expressed in core markup.
 */
function glimmr_register_blocks() {
	register_block_type( GLIMMR_DIR . '/blocks/heart-button' );
	register_block_type( GLIMMR_DIR . '/blocks/photo-actions' );
	register_block_type( GLIMMR_DIR . '/blocks/breadcrumbs' );
}
add_action( 'init', 'glimmr_register_blocks' );

/**
 * Keep the native comment form, but tune its copy for the photo-page composer.
 *
 * @param array $defaults Comment form defaults.
 * @return array
 */
function glimmr_comment_form_defaults( $defaults ) {
	$defaults['title_reply']           = __( 'Add a comment', 'glimmr' );
	$defaults['label_submit']          = __( 'Post', 'glimmr' );
	$defaults['comment_notes_before']  = '';
	$defaults['comment_notes_after']   = '';
	$defaults['comment_field']         = sprintf(
		'<p class="comment-form-comment"><label class="screen-reader-text" for="comment">%1$s <span class="required">*</span></label><textarea id="comment" name="comment" cols="45" rows="5" maxlength="65525" placeholder="%2$s" required></textarea></p>',
		esc_html__( 'Comment', 'glimmr' ),
		esc_attr__( 'Add a comment…', 'glimmr' )
	);

	return $defaults;
}
add_filter( 'comment_form_defaults', 'glimmr_comment_form_defaults' );

/**
 * Add placeholders to the core logged-out identity fields without replacing the
 * form with custom markup.
 *
 * @param array $fields Comment form fields.
 * @return array
 */
function glimmr_comment_form_fields( $fields ) {
	$commenter = wp_get_current_commenter();
	$required  = (bool) get_option( 'require_name_email' );
	$req_mark  = $required ? ' <span class="required">*</span>' : '';
	$req_attr  = $required ? ' required' : '';

	$fields['author'] = sprintf(
		'<p class="comment-form-author"><label class="screen-reader-text" for="author">%1$s%2$s</label><input id="author" name="author" type="text" value="%3$s" size="30" maxlength="245" autocomplete="name" placeholder="%1$s"%4$s></p>',
		esc_html__( 'Name', 'glimmr' ),
		$req_mark,
		esc_attr( $commenter['comment_author'] ),
		$req_attr
	);

	$fields['email'] = sprintf(
		'<p class="comment-form-email"><label class="screen-reader-text" for="email">%1$s%2$s</label><input id="email" name="email" type="email" value="%3$s" size="30" maxlength="100" autocomplete="email" placeholder="%1$s"%4$s></p>',
		esc_html__( 'Email', 'glimmr' ),
		$req_mark,
		esc_attr( $commenter['comment_author_email'] ),
		$req_attr
	);

	unset( $fields['url'] );
	unset( $fields['cookies'] );

	return $fields;
}
add_filter( 'comment_form_default_fields', 'glimmr_comment_form_fields' );

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
require_once GLIMMR_DIR . '/inc/heart-button.php';
require_once GLIMMR_DIR . '/inc/block-bindings.php';
require_once GLIMMR_DIR . '/inc/related-query.php';
require_once GLIMMR_DIR . '/inc/login-style.php';
require_once GLIMMR_DIR . '/inc/photo-search.php';
