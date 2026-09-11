<?php
/**
 * Title: About the photographer
 * Slug: glimmr/about-photographer
 * Categories: glimmr-photo, about
 * Description: A portrait beside a short bio, practice note, and story link.
 */
?>
<!-- wp:columns {"verticalAlignment":"center","align":"wide","className":"glmr-about-card","style":{"spacing":{"blockGap":{"left":"40px"},"padding":{"top":"44px","right":"48px","bottom":"44px","left":"48px"}},"border":{"color":"var:preset|color|hairline","width":"1px","radius":"0px"}}} -->
<div class="wp-block-columns alignwide glmr-about-card are-vertically-aligned-center" style="border-color:var(--wp--preset--color--hairline);border-width:1px;border-radius:0px;padding-top:44px;padding-right:48px;padding-bottom:44px;padding-left:48px">
	<!-- wp:column {"verticalAlignment":"center","width":"160px"} -->
	<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:160px">
			<!-- wp:avatar {"size":160,"className":"glmr-about-avatar","style":{"border":{"radius":"9999px"}}} /-->
	</div>
	<!-- /wp:column -->
	<!-- wp:column {"verticalAlignment":"center"} -->
	<div class="wp-block-column is-vertically-aligned-center">
		<!-- wp:paragraph {"className":"glmr-eyebrow","fontSize":"chip","textColor":"pink"} -->
		<p class="glmr-eyebrow has-pink-color has-text-color has-chip-font-size">About the photographer</p>
		<!-- /wp:paragraph -->
		<!-- wp:heading {"metadata":{"bindings":{"content":{"source":"glimmr/author-name"}}},"fontSize":"x-large"} -->
		<h2 class="wp-block-heading has-x-large-font-size">Photographer</h2>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"className":"glmr-about-location","fontSize":"meta","textColor":"muted"} -->
		<p class="glmr-about-location has-muted-color has-text-color has-meta-font-size">Available light only</p>
		<!-- /wp:paragraph -->
		<!-- wp:paragraph {"fontSize":"large","textColor":"fg"} -->
		<p class="has-fg-color has-text-color has-large-font-size">I photograph light and weather — fields at golden hour, grey harbors, the long way round. Self-taught, available light only, and happiest a few hours from the nearest road.</p>
		<!-- /wp:paragraph -->
		<!-- wp:group {"className":"glmr-about-foot","style":{"spacing":{"blockGap":"var:preset|spacing|30","margin":{"top":"var:preset|spacing|30"}}},"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"center"}} -->
		<div class="wp-block-group glmr-about-foot" style="margin-top:var(--wp--preset--spacing--30)">
			<!-- wp:paragraph {"className":"glmr-about-stat","metadata":{"bindings":{"content":{"source":"glimmr/photo-count","args":{"since":true}}}},"fontSize":"meta","textColor":"muted"} -->
			<p class="glmr-about-stat has-muted-color has-text-color has-meta-font-size">0 photos</p>
			<!-- /wp:paragraph -->
			<!-- wp:buttons -->
			<div class="wp-block-buttons"><!-- wp:button {"url":"/about/","className":"is-style-outline"} -->
				<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="/about/">Read the full story</a></div>
			<!-- /wp:button --></div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:column -->
</div>
<!-- /wp:columns -->
