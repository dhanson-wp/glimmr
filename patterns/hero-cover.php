<?php
/**
 * Title: Hero — Full-bleed cover
 * Slug: glimmr/hero-cover
 * Categories: glimmr-photo, banner
 * Description: One photo edge-to-edge with a pill, title, tagline and a pink button.
 */
$glimmr_hero_image = function_exists( 'glimmr_get_pattern_image_url' ) ? glimmr_get_pattern_image_url( 0, 'full' ) : '';
$glimmr_cover_attrs = array(
	'dimRatio'        => 40,
	'overlayColor'    => 'ink',
	'isDark'          => true,
	'minHeight'       => 600,
	'minHeightUnit'   => 'px',
	'align'           => 'full',
	'className'       => 'glmr-hero-cover glmr-hero-pattern',
	'contentPosition' => 'bottom left',
	'style'           => array(
		'spacing' => array(
			'padding' => array(
				'top'    => 'var:preset|spacing|60',
				'bottom' => '56px',
				'left'   => '0',
				'right'  => '0',
			),
		),
	),
);
if ( '' !== $glimmr_hero_image ) {
	$glimmr_cover_attrs['url'] = $glimmr_hero_image;
}
?>
<!-- wp:cover <?php echo wp_json_encode( $glimmr_cover_attrs ); ?> -->
<div class="wp-block-cover alignfull is-dark has-custom-content-position is-position-bottom-left glmr-hero-cover glmr-hero-pattern" style="padding-top:var(--wp--preset--spacing--60);padding-right:0;padding-bottom:56px;padding-left:0;min-height:600px">
	<span aria-hidden="true" class="wp-block-cover__background has-ink-background-color has-background-dim-40 has-background-dim"></span>
	<?php if ( '' !== $glimmr_hero_image ) : ?>
	<img class="wp-block-cover__image-background" src="<?php echo esc_url( $glimmr_hero_image ); ?>" alt=""/>
	<?php endif; ?>
	<div class="wp-block-cover__inner-container">
		<!-- wp:group {"className":"glmr-hero-pattern__inner","layout":{"type":"constrained","contentSize":"1400px"},"style":{"spacing":{"blockGap":"0"}}} -->
		<div class="wp-block-group glmr-hero-pattern__inner">
			<!-- wp:paragraph {"className":"pill glmr-hero-pattern__pill","backgroundColor":"pink","textColor":"white","fontSize":"chip","style":{"border":{"radius":"9999px"},"spacing":{"padding":{"top":"5px","bottom":"5px","left":"13px","right":"13px"}}}} -->
			<p class="pill glmr-hero-pattern__pill has-white-color has-pink-background-color has-text-color has-background has-chip-font-size" style="border-radius:9999px;padding-top:5px;padding-right:13px;padding-bottom:5px;padding-left:13px"><?php esc_html_e( 'Sunsets · Tuscany', 'glimmr' ); ?></p>
			<!-- /wp:paragraph -->
			<!-- wp:heading {"fontSize":"xx-large","textColor":"white","className":"glmr-hero-pattern__title"} -->
			<h2 class="wp-block-heading glmr-hero-pattern__title has-white-color has-text-color has-xx-large-font-size"><?php esc_html_e( 'The last hour of light.', 'glimmr' ); ?></h2>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"textColor":"white","fontSize":"large","className":"glmr-hero-pattern__tagline"} -->
			<p class="glmr-hero-pattern__tagline has-white-color has-text-color has-large-font-size"><?php esc_html_e( 'A running archive of golden-hour fields, shot on available light and edited with a light hand.', 'glimmr' ); ?></p>
			<!-- /wp:paragraph -->
			<!-- wp:buttons {"className":"glmr-hero-pattern__cta","style":{"spacing":{"margin":{"top":"0"}}}} -->
			<div class="wp-block-buttons glmr-hero-pattern__cta" style="margin-top:0"><!-- wp:button {"url":"/"} -->
				<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/"><?php esc_html_e( 'View album', 'glimmr' ); ?></a></div>
			<!-- /wp:button --></div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:group -->
	</div>
</div>
<!-- /wp:cover -->
