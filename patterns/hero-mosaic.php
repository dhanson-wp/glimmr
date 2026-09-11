<?php
/**
 * Title: Hero — Photo mosaic
 * Slug: glimmr/hero-mosaic
 * Categories: glimmr-photo, gallery
 * Description: A mixed-aspect six-photo mosaic with a compact section title.
 */
$glimmr_mosaic_images = array();
for ( $glimmr_i = 0; $glimmr_i < 6; $glimmr_i++ ) {
	$glimmr_mosaic_images[] = function_exists( 'glimmr_get_pattern_image_url' ) ? glimmr_get_pattern_image_url( $glimmr_i, 'large' ) : '';
}
?>
<!-- wp:group {"align":"wide","className":"glmr-hero-mosaic","style":{"spacing":{"blockGap":"var:preset|spacing|30","padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide glmr-hero-mosaic" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)">
	<!-- wp:gallery {"linkTo":"none","className":"glmr-mosaic"} -->
	<figure class="wp-block-gallery has-nested-images columns-default is-cropped glmr-mosaic">
		<!-- wp:image {"sizeSlug":"large","linkDestination":"none","className":"m-a"} -->
		<figure class="wp-block-image size-large m-a"><?php if ( '' !== $glimmr_mosaic_images[0] ) : ?><img src="<?php echo esc_url( $glimmr_mosaic_images[0] ); ?>" alt=""/><?php endif; ?></figure>
		<!-- /wp:image -->
		<!-- wp:image {"sizeSlug":"large","linkDestination":"none","className":"m-b"} -->
		<figure class="wp-block-image size-large m-b"><?php if ( '' !== $glimmr_mosaic_images[1] ) : ?><img src="<?php echo esc_url( $glimmr_mosaic_images[1] ); ?>" alt=""/><?php endif; ?></figure>
		<!-- /wp:image -->
		<!-- wp:image {"sizeSlug":"large","linkDestination":"none","className":"m-tall"} -->
		<figure class="wp-block-image size-large m-tall"><?php if ( '' !== $glimmr_mosaic_images[2] ) : ?><img src="<?php echo esc_url( $glimmr_mosaic_images[2] ); ?>" alt=""/><?php endif; ?></figure>
		<!-- /wp:image -->
		<!-- wp:image {"sizeSlug":"large","linkDestination":"none"} -->
		<figure class="wp-block-image size-large"><?php if ( '' !== $glimmr_mosaic_images[3] ) : ?><img src="<?php echo esc_url( $glimmr_mosaic_images[3] ); ?>" alt=""/><?php endif; ?></figure>
		<!-- /wp:image -->
		<!-- wp:image {"sizeSlug":"large","linkDestination":"none"} -->
		<figure class="wp-block-image size-large"><?php if ( '' !== $glimmr_mosaic_images[4] ) : ?><img src="<?php echo esc_url( $glimmr_mosaic_images[4] ); ?>" alt=""/><?php endif; ?></figure>
		<!-- /wp:image -->
		<!-- wp:image {"sizeSlug":"large","linkDestination":"none","className":"m-b"} -->
		<figure class="wp-block-image size-large m-b"><?php if ( '' !== $glimmr_mosaic_images[5] ) : ?><img src="<?php echo esc_url( $glimmr_mosaic_images[5] ); ?>" alt=""/><?php endif; ?></figure>
		<!-- /wp:image -->
	</figure>
	<!-- /wp:gallery -->
	<!-- wp:group {"className":"glmr-mosaic-title","layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"bottom"}} -->
	<div class="wp-block-group glmr-mosaic-title">
		<!-- wp:heading {"fontSize":"x-large"} -->
		<h2 class="wp-block-heading has-x-large-font-size">Selected work</h2>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"glimmr/photo-count","args":{"prefix":"a cross-section of the archive · "}}}},"fontSize":"meta","textColor":"muted"} -->
		<p class="has-muted-color has-text-color has-meta-font-size">a cross-section of the archive · 0 photos</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
