<?php
/**
 * Title: Closing CTA band
 * Slug: glimmr/closing-cta
 * Categories: glimmr-photo, call-to-action
 * Description: A full-bleed photo band to close a page with a title and one button.
 */
$glimmr_cta_image = function_exists( 'glimmr_get_pattern_image_url' ) ? glimmr_get_pattern_image_url( 4, 'full' ) : '';
$glimmr_cta_attrs = array(
	'dimRatio'        => 40,
	'overlayColor'    => 'ink',
	'isDark'          => true,
	'minHeight'       => 420,
	'minHeightUnit'   => 'px',
	'align'           => 'full',
	'className'       => 'glmr-closing-cta',
	'contentPosition' => 'center center',
	'style'           => array(
		'spacing' => array(
			'padding' => array(
				'top'    => 'var:preset|spacing|60',
				'bottom' => 'var:preset|spacing|60',
			),
		),
	),
);
if ( '' !== $glimmr_cta_image ) {
	$glimmr_cta_attrs['url'] = $glimmr_cta_image;
}
?>
<!-- wp:cover <?php echo wp_json_encode( $glimmr_cta_attrs ); ?> -->
<div class="wp-block-cover alignfull is-dark glmr-closing-cta" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60);min-height:420px">
	<span aria-hidden="true" class="wp-block-cover__background has-ink-background-color has-background-dim-40 has-background-dim"></span>
	<?php if ( '' !== $glimmr_cta_image ) : ?>
	<img class="wp-block-cover__image-background" src="<?php echo esc_url( $glimmr_cta_image ); ?>" alt=""/>
	<?php endif; ?>
	<div class="wp-block-cover__inner-container">
		<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"constrained","contentSize":"560px"}} -->
		<div class="wp-block-group">
			<!-- wp:heading {"textAlign":"center","fontSize":"xx-large","textColor":"white"} -->
			<h2 class="wp-block-heading has-text-align-center has-white-color has-text-color has-xx-large-font-size">See the full photostream.</h2>
			<!-- /wp:heading -->
			<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
			<div class="wp-block-buttons"><!-- wp:button {"url":"/","className":"is-style-outline","metadata":{"bindings":{"text":{"source":"glimmr/photo-count","args":{"prefix":"View all "}}}}} -->
				<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="/">View all photos</a></div>
			<!-- /wp:button --></div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:group -->
	</div>
</div>
<!-- /wp:cover -->
