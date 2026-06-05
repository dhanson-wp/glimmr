<?php
/**
 * Title: Link in bio
 * Slug: glimmr/link-in-bio
 * Categories: glimmr-photo, call-to-action
 * Description: A centred avatar, one line of copy and a stack of full-width link buttons. Replaces the social row.
 */
?>
<!-- wp:group {"className":"glmr-linkbio","style":{"spacing":{"blockGap":"var:preset|spacing|30","padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}},"layout":{"type":"constrained","contentSize":"420px"}} -->
<div class="wp-block-group glmr-linkbio" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)">
	<!-- wp:image {"width":"96px","height":"96px","scale":"cover","sizeSlug":"large","align":"center","style":{"border":{"radius":"9999px"}}} -->
	<figure class="wp-block-image aligncenter size-large is-resized has-custom-border"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/portrait.jpg' ) ); ?>" alt="" style="border-radius:9999px;object-fit:cover;width:96px;height:96px"/></figure>
	<!-- /wp:image -->
	<!-- wp:heading {"textAlign":"center","level":2,"fontSize":"large","style":{"typography":{"fontWeight":"600"}}} -->
	<h2 class="wp-block-heading has-text-align-center has-large-font-size" style="font-weight:600">glimmr</h2>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"align":"center","textColor":"muted","fontSize":"meta"} -->
	<p class="has-text-align-center has-muted-color has-text-color has-meta-font-size">A quiet place for photographs.</p>
	<!-- /wp:paragraph -->
	<!-- wp:buttons {"layout":{"type":"flex","orientation":"vertical"},"style":{"spacing":{"blockGap":"var:preset|spacing|20","margin":{"top":"var:preset|spacing|20"}}}} -->
	<div class="wp-block-buttons">
		<!-- wp:button {"width":100} -->
		<div class="wp-block-button has-custom-width wp-block-button__width-100"><a class="wp-block-button__link wp-element-button">Latest album</a></div>
		<!-- /wp:button -->
		<!-- wp:button {"width":100,"className":"is-style-outline"} -->
		<div class="wp-block-button has-custom-width wp-block-button__width-100 is-style-outline"><a class="wp-block-button__link wp-element-button">Print shop</a></div>
		<!-- /wp:button -->
		<!-- wp:button {"width":100,"className":"is-style-outline"} -->
		<div class="wp-block-button has-custom-width wp-block-button__width-100 is-style-outline"><a class="wp-block-button__link wp-element-button">Newsletter</a></div>
		<!-- /wp:button -->
		<!-- wp:button {"width":100,"className":"is-style-outline"} -->
		<div class="wp-block-button has-custom-width wp-block-button__width-100 is-style-outline"><a class="wp-block-button__link wp-element-button">Email me</a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
