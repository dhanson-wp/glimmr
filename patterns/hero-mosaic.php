<?php
/**
 * Title: Hero — Photo mosaic
 * Slug: glimmr/hero-mosaic
 * Categories: glimmr-photo, gallery
 * Description: A tight four-photo mosaic under a light display title.
 */
?>
<!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|40","padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)">
	<!-- wp:heading {"fontSize":"xx-large"} -->
	<h2 class="wp-block-heading has-xx-large-font-size">Recent frames.</h2>
	<!-- /wp:heading -->
	<!-- wp:gallery {"columns":4,"imageCrop":true,"linkTo":"none","className":"is-style-default","style":{"spacing":{"blockGap":{"top":"6px","left":"6px"}}}} -->
	<figure class="wp-block-gallery has-nested-images columns-4 is-cropped is-style-default">
		<!-- wp:image {"sizeSlug":"large","linkDestination":"none"} -->
		<figure class="wp-block-image size-large"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/album-1.jpg' ) ); ?>" alt=""/></figure>
		<!-- /wp:image -->
		<!-- wp:image {"sizeSlug":"large","linkDestination":"none"} -->
		<figure class="wp-block-image size-large"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/album-3.jpg' ) ); ?>" alt=""/></figure>
		<!-- /wp:image -->
		<!-- wp:image {"sizeSlug":"large","linkDestination":"none"} -->
		<figure class="wp-block-image size-large"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/feature.jpg' ) ); ?>" alt=""/></figure>
		<!-- /wp:image -->
		<!-- wp:image {"sizeSlug":"large","linkDestination":"none"} -->
		<figure class="wp-block-image size-large"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/album-2.jpg' ) ); ?>" alt=""/></figure>
		<!-- /wp:image -->
	</figure>
	<!-- /wp:gallery -->
</div>
<!-- /wp:group -->
