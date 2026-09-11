<?php
/**
 * Title: Email subscribe CTA
 * Slug: glimmr/subscribe
 * Categories: glimmr-photo, call-to-action
 * Description: A calm subscribe band with heading, one line of copy, and a button. Optional; not placed in any template.
 */
?>
<!-- wp:group {"align":"full","className":"glmr-subscribe-cta","backgroundColor":"ink","textColor":"on-ink","style":{"spacing":{"padding":{"top":"72px","bottom":"72px","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}},"layout":{"type":"constrained","contentSize":"560px"}} -->
<div class="wp-block-group alignfull glmr-subscribe-cta has-on-ink-color has-ink-background-color has-text-color has-background" style="padding-top:72px;padding-right:var(--wp--preset--spacing--40);padding-bottom:72px;padding-left:var(--wp--preset--spacing--40)">
	<!-- wp:heading {"textAlign":"center","fontSize":"x-large","textColor":"on-ink"} -->
	<h2 class="wp-block-heading has-text-align-center has-on-ink-color has-text-color has-x-large-font-size">Get new photos in your inbox.</h2>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"align":"center","className":"glmr-subscribe-copy","fontSize":"large"} -->
	<p class="has-text-align-center glmr-subscribe-copy has-large-font-size">A quiet email when a new album goes up — no more than once a month, never anything else.</p>
	<!-- /wp:paragraph -->
	<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"},"style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}}}} -->
	<div class="wp-block-buttons"><!-- wp:button {"url":"/newsletter/"} -->
		<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/newsletter/">Subscribe</a></div>
	<!-- /wp:button --></div>
	<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
