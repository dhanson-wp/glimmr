<?php
/**
 * Title: Album showcase
 * Slug: glimmr/album-showcase
 * Categories: glimmr-photo, gallery
 * Description: Three cover cards, each a photo with an album title and count. Editable placeholders — point them at real albums.
 */
$glimmr_albums = array(
	array( 'img' => 'assets/img/album-1.jpg', 'title' => 'Abstract', 'count' => '24 photos' ),
	array( 'img' => 'assets/img/album-2.jpg', 'title' => 'On the road', 'count' => '38 photos' ),
	array( 'img' => 'assets/img/album-3.jpg', 'title' => 'Close to home', 'count' => '17 photos' ),
);
?>
<!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|40","padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)">
	<!-- wp:heading {"fontSize":"x-large"} -->
	<h2 class="wp-block-heading has-x-large-font-size">Albums</h2>
	<!-- /wp:heading -->
	<!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"var:preset|spacing|40"}}}} -->
	<div class="wp-block-columns">
		<?php foreach ( $glimmr_albums as $glimmr_album ) : ?>
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:cover {"url":"<?php echo esc_url( get_theme_file_uri( $glimmr_album['img'] ) ); ?>","dimRatio":30,"overlayColor":"ink","isDark":true,"minHeight":300,"minHeightUnit":"px","contentPosition":"bottom left","className":"glmr-album-card","style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}}} -->
			<div class="wp-block-cover is-dark has-custom-content-position is-position-bottom-left glmr-album-card" style="padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30);min-height:300px">
				<span aria-hidden="true" class="wp-block-cover__background has-ink-background-color has-background-dim-30 has-background-dim"></span>
				<img class="wp-block-cover__image-background" src="<?php echo esc_url( get_theme_file_uri( $glimmr_album['img'] ) ); ?>" alt=""/>
				<div class="wp-block-cover__inner-container">
					<!-- wp:heading {"level":3,"fontSize":"large","textColor":"white"} -->
					<h3 class="wp-block-heading has-white-color has-text-color has-large-font-size"><?php echo esc_html( $glimmr_album['title'] ); ?></h3>
					<!-- /wp:heading -->
					<!-- wp:paragraph {"fontSize":"meta","style":{"color":{"text":"#d7d9da"}}} -->
					<p class="has-meta-font-size" style="color:#d7d9da"><?php echo esc_html( $glimmr_album['count'] ); ?></p>
					<!-- /wp:paragraph -->
				</div>
			</div>
			<!-- /wp:cover -->
		</div>
		<!-- /wp:column -->
		<?php endforeach; ?>
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
