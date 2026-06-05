<?php
/**
 * Title: Closing CTA band
 * Slug: glimmr/closing-cta
 * Categories: glimmr-photo, call-to-action
 * Description: A full-bleed photo band to close a page — title and a single pink button.
 */
?>
<!-- wp:cover {"url":"<?php echo esc_url( get_theme_file_uri( 'assets/img/feature.jpg' ) ); ?>","dimRatio":50,"overlayColor":"ink","isDark":true,"minHeight":52,"minHeightUnit":"vh","align":"full","contentPosition":"center center","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}}} -->
<div class="wp-block-cover alignfull is-dark" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60);min-height:52vh">
	<span aria-hidden="true" class="wp-block-cover__background has-ink-background-color has-background-dim-50 has-background-dim"></span>
	<img class="wp-block-cover__image-background" src="<?php echo esc_url( get_theme_file_uri( 'assets/img/feature.jpg' ) ); ?>" alt=""/>
	<div class="wp-block-cover__inner-container">
		<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"constrained","contentSize":"560px"}} -->
		<div class="wp-block-group">
			<!-- wp:heading {"textAlign":"center","fontSize":"xx-large","textColor":"white"} -->
			<h2 class="wp-block-heading has-text-align-center has-white-color has-text-color has-xx-large-font-size">See where the light went.</h2>
			<!-- /wp:heading -->
			<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
			<div class="wp-block-buttons"><!-- wp:button -->
				<div class="wp-block-button"><a class="wp-block-button__link wp-element-button">Browse the stream</a></div>
			<!-- /wp:button --></div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:group -->
	</div>
</div>
<!-- /wp:cover -->
