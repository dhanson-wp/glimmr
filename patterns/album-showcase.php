<?php
/**
 * Title: Album showcase
 * Slug: glimmr/album-showcase
 * Categories: glimmr-photo, gallery
 * Description: Four cover cards, each a photo with an album title and count.
 */
$glimmr_albums = function_exists( 'glimmr_get_album_cards' )
	? glimmr_get_album_cards( 4 )
	: array(
		array( 'img' => '', 'title' => __( 'Beaches', 'glimmr' ), 'url' => home_url( '/albums/' ), 'count' => __( '0 photos', 'glimmr' ) ),
		array( 'img' => '', 'title' => __( 'Food', 'glimmr' ), 'url' => home_url( '/albums/' ), 'count' => __( '0 photos', 'glimmr' ) ),
		array( 'img' => '', 'title' => __( 'Lego', 'glimmr' ), 'url' => home_url( '/albums/' ), 'count' => __( '0 photos', 'glimmr' ) ),
		array( 'img' => '', 'title' => __( 'Portraits', 'glimmr' ), 'url' => home_url( '/albums/' ), 'count' => __( '0 photos', 'glimmr' ) ),
	);
?>
<!-- wp:group {"align":"wide","className":"glmr-album-showcase","style":{"spacing":{"blockGap":"var:preset|spacing|30","padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide glmr-album-showcase" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)">
	<!-- wp:group {"className":"glmr-albums-head","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"bottom"}} -->
	<div class="wp-block-group glmr-albums-head">
		<!-- wp:heading {"fontSize":"x-large"} -->
		<h2 class="wp-block-heading has-x-large-font-size">Browse albums</h2>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"fontSize":"meta"} -->
		<p class="has-meta-font-size"><a href="<?php echo esc_url( home_url( '/albums/' ) ); ?>">View all →</a></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
	<!-- wp:group {"className":"glmr-album-grid","style":{"spacing":{"blockGap":"14px"}},"layout":{"type":"grid","columnCount":4,"minimumColumnWidth":null}} -->
	<div class="wp-block-group glmr-album-grid">
		<?php foreach ( $glimmr_albums as $glimmr_album ) : ?>
		<?php
		$glimmr_album_attrs = array(
			'dimRatio'        => 30,
			'overlayColor'    => 'ink',
			'isDark'          => true,
			'contentPosition' => 'bottom left',
			'className'       => 'glmr-album-card',
			'style'           => array(
				'dimensions' => array(
					'aspectRatio' => '3/4',
				),
				'spacing'    => array(
					'padding' => array(
						'top'    => '18px',
						'bottom' => '18px',
						'left'   => '18px',
						'right'  => '18px',
					),
				),
			),
		);
		if ( ! empty( $glimmr_album['img'] ) ) {
			$glimmr_album_attrs['url'] = $glimmr_album['img'];
		}
		?>
		<!-- wp:cover <?php echo wp_json_encode( $glimmr_album_attrs ); ?> -->
		<div class="wp-block-cover is-dark has-custom-content-position is-position-bottom-left glmr-album-card" style="padding-top:18px;padding-right:18px;padding-bottom:18px;padding-left:18px">
			<span aria-hidden="true" class="wp-block-cover__background has-ink-background-color has-background-dim-30 has-background-dim"></span>
			<?php if ( ! empty( $glimmr_album['img'] ) ) : ?>
			<img class="wp-block-cover__image-background" src="<?php echo esc_url( $glimmr_album['img'] ); ?>" alt=""/>
			<?php endif; ?>
			<div class="wp-block-cover__inner-container">
				<!-- wp:heading {"level":3,"fontSize":"large","textColor":"white"} -->
				<h3 class="wp-block-heading has-white-color has-text-color has-large-font-size"><a href="<?php echo esc_url( $glimmr_album['url'] ); ?>"><?php echo esc_html( $glimmr_album['title'] ); ?></a></h3>
				<!-- /wp:heading -->
				<!-- wp:paragraph {"className":"glmr-album-count","fontSize":"meta"} -->
				<p class="glmr-album-count has-meta-font-size"><?php echo esc_html( $glimmr_album['count'] ); ?></p>
				<!-- /wp:paragraph -->
			</div>
		</div>
		<!-- /wp:cover -->
		<?php endforeach; ?>
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
