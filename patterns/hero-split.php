<?php
/**
 * Title: Hero — Split intro
 * Slug: glimmr/hero-split
 * Categories: glimmr-photo, banner
 * Description: A photo beside a light display title, lead paragraph and a pink button.
 */
?>
<!-- wp:columns {"verticalAlignment":"center","align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|50"},"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}}} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)">
	<!-- wp:column {"verticalAlignment":"center","width":"55%"} -->
	<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:55%">
		<!-- wp:image {"sizeSlug":"large"} -->
		<figure class="wp-block-image size-large"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/album-2.jpg' ) ); ?>" alt=""/></figure>
		<!-- /wp:image -->
	</div>
	<!-- /wp:column -->
	<!-- wp:column {"verticalAlignment":"center"} -->
	<div class="wp-block-column is-vertically-aligned-center">
		<!-- wp:paragraph {"className":"glmr-eyebrow","fontSize":"chip","textColor":"muted"} -->
		<p class="glmr-eyebrow has-muted-color has-text-color has-chip-font-size">A running archive</p>
		<!-- /wp:paragraph -->
		<!-- wp:heading {"fontSize":"xx-large"} -->
		<h2 class="wp-block-heading has-xx-large-font-size">Light, weather, and the long way round.</h2>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"fontSize":"large","textColor":"muted"} -->
		<p class="has-muted-color has-text-color has-large-font-size">Photographs from wherever the day takes me. No filters chasing a trend, just the picture that was actually there.</p>
		<!-- /wp:paragraph -->
		<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}}}} -->
		<div class="wp-block-buttons"><!-- wp:button -->
			<div class="wp-block-button"><a class="wp-block-button__link wp-element-button">Browse the stream</a></div>
		<!-- /wp:button --></div>
		<!-- /wp:buttons -->
	</div>
	<!-- /wp:column -->
</div>
<!-- /wp:columns -->
