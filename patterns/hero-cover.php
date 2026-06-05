<?php
/**
 * Title: Hero — Full-bleed cover
 * Slug: glimmr/hero-cover
 * Categories: glimmr-photo, banner
 * Description: One photo edge-to-edge with a pill, title, tagline and a pink button.
 */
?>
<!-- wp:cover {"url":"<?php echo esc_url( get_theme_file_uri( 'assets/img/hero.jpg' ) ); ?>","dimRatio":40,"overlayColor":"ink","isDark":true,"minHeight":78,"minHeightUnit":"vh","align":"full","contentPosition":"bottom left","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}}}} -->
<div class="wp-block-cover alignfull is-dark has-custom-content-position is-position-bottom-left" style="padding-top:var(--wp--preset--spacing--60);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--60);padding-left:var(--wp--preset--spacing--50);min-height:78vh">
	<span aria-hidden="true" class="wp-block-cover__background has-ink-background-color has-background-dim-40 has-background-dim"></span>
	<img class="wp-block-cover__image-background" src="<?php echo esc_url( get_theme_file_uri( 'assets/img/hero.jpg' ) ); ?>" alt=""/>
	<div class="wp-block-cover__inner-container">
		<!-- wp:group {"layout":{"type":"constrained","contentSize":"1400px"},"style":{"spacing":{"blockGap":"var:preset|spacing|30"}}} -->
		<div class="wp-block-group">
			<!-- wp:paragraph {"className":"pill","backgroundColor":"pink","textColor":"white","fontSize":"chip","style":{"border":{"radius":"9999px"},"spacing":{"padding":{"top":"4px","bottom":"4px","left":"12px","right":"12px"}}}} -->
			<p class="pill has-white-color has-pink-background-color has-background has-chip-font-size" style="border-radius:9999px;padding-top:4px;padding-right:12px;padding-bottom:4px;padding-left:12px">Sunsets · Tuscany</p>
			<!-- /wp:paragraph -->
			<!-- wp:heading {"fontSize":"xx-large","textColor":"white"} -->
			<h2 class="wp-block-heading has-white-color has-text-color has-xx-large-font-size">The last hour of light.</h2>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"textColor":"white","fontSize":"large"} -->
			<p class="has-white-color has-text-color has-large-font-size">Shot on available light, edited with a light hand.</p>
			<!-- /wp:paragraph -->
			<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}}} -->
			<div class="wp-block-buttons"><!-- wp:button -->
				<div class="wp-block-button"><a class="wp-block-button__link wp-element-button">View album</a></div>
			<!-- /wp:button --></div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:group -->
	</div>
</div>
<!-- /wp:cover -->
