<?php
/**
 * Title: About the photographer
 * Slug: glimmr/about-photographer
 * Categories: glimmr-photo, about
 * Description: A portrait beside a short bio and a couple of links.
 */
?>
<!-- wp:columns {"verticalAlignment":"center","align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|50"},"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}}} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)">
	<!-- wp:column {"verticalAlignment":"center","width":"34%"} -->
	<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:34%">
		<!-- wp:image {"width":"220px","height":"220px","scale":"cover","sizeSlug":"large","className":"is-style-default","style":{"border":{"radius":"9999px"}}} -->
		<figure class="wp-block-image size-large is-resized is-style-default has-custom-border"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/portrait.jpg' ) ); ?>" alt="" style="border-radius:9999px;object-fit:cover;width:220px;height:220px"/></figure>
		<!-- /wp:image -->
	</div>
	<!-- /wp:column -->
	<!-- wp:column {"verticalAlignment":"center"} -->
	<div class="wp-block-column is-vertically-aligned-center">
		<!-- wp:paragraph {"className":"glmr-eyebrow","fontSize":"chip","textColor":"muted"} -->
		<p class="glmr-eyebrow has-muted-color has-text-color has-chip-font-size">About</p>
		<!-- /wp:paragraph -->
		<!-- wp:heading {"fontSize":"x-large"} -->
		<h2 class="wp-block-heading has-x-large-font-size">Hi, I’m the one behind the camera.</h2>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"fontSize":"large","textColor":"muted"} -->
		<p class="has-muted-color has-text-color has-large-font-size">I photograph quiet light and the places it lands. This is where the pictures live, untethered from any feed. Stay a while.</p>
		<!-- /wp:paragraph -->
		<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}}}} -->
		<div class="wp-block-buttons"><!-- wp:button -->
			<div class="wp-block-button"><a class="wp-block-button__link wp-element-button">Follow</a></div>
		<!-- /wp:button -->
		<!-- wp:button {"className":"is-style-outline"} -->
			<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button">Read more</a></div>
		<!-- /wp:button --></div>
		<!-- /wp:buttons -->
	</div>
	<!-- /wp:column -->
</div>
<!-- /wp:columns -->
