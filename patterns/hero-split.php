<?php
/**
 * Title: Hero — Split intro
 * Slug: glimmr/hero-split
 * Categories: glimmr-photo, banner
 * Description: A photo beside a light display title, lead paragraph and a pink button.
 */
$glimmr_split_image = function_exists( 'glimmr_get_pattern_image_url' ) ? glimmr_get_pattern_image_url( 1, 'large' ) : '';
?>
<!-- wp:columns {"verticalAlignment":"center","align":"wide","className":"glmr-hero-split","style":{"spacing":{"blockGap":{"left":"0"},"padding":{"top":"0","bottom":"0"}}}} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center glmr-hero-split" style="padding-top:0;padding-bottom:0">
	<!-- wp:column {"verticalAlignment":"center","width":"55%","className":"glmr-hero-split__media"} -->
	<div class="wp-block-column is-vertically-aligned-center glmr-hero-split__media" style="flex-basis:55%">
		<!-- wp:image {"sizeSlug":"large","className":"glmr-hero-split__image"} -->
		<figure class="wp-block-image size-large glmr-hero-split__image"><?php if ( '' !== $glimmr_split_image ) : ?><img src="<?php echo esc_url( $glimmr_split_image ); ?>" alt=""/><?php endif; ?></figure>
		<!-- /wp:image -->
	</div>
	<!-- /wp:column -->
	<!-- wp:column {"verticalAlignment":"center","className":"glmr-hero-split__body"} -->
	<div class="wp-block-column is-vertically-aligned-center glmr-hero-split__body">
		<!-- wp:paragraph {"className":"glmr-eyebrow","metadata":{"bindings":{"content":{"source":"glimmr/author-name","args":{"prefix":"Photographs by "}}}},"fontSize":"chip","textColor":"muted"} -->
		<p class="glmr-eyebrow has-muted-color has-text-color has-chip-font-size">Photographs by the photographer</p>
		<!-- /wp:paragraph -->
		<!-- wp:heading {"fontSize":"xx-large"} -->
		<h2 class="wp-block-heading has-xx-large-font-size">Light, weather, and the long way round.</h2>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"fontSize":"large","textColor":"muted"} -->
		<p class="has-muted-color has-text-color has-large-font-size">A personal photostream — no feed, no noise. Just the pictures, in the order they were made.</p>
		<!-- /wp:paragraph -->
		<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}}}} -->
		<div class="wp-block-buttons"><!-- wp:button {"url":"/"} -->
			<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/">View the latest</a></div>
		<!-- /wp:button --></div>
		<!-- /wp:buttons -->
	</div>
	<!-- /wp:column -->
</div>
<!-- /wp:columns -->
