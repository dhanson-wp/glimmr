<?php
/**
 * Title: Journal query loop
 * Slug: glimmr/journal-loop
 * Categories: glimmr-journal, posts
 * Description: A calmer, text-forward loop — a small thumbnail beside a title, date and excerpt.
 */
?>
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}},"layout":{"type":"constrained","contentSize":"760px"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)">
	<!-- wp:heading {"fontSize":"x-large"} -->
	<h2 class="wp-block-heading has-x-large-font-size">From the journal</h2>
	<!-- /wp:heading -->
	<!-- wp:query {"queryId":20,"query":{"perPage":5,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","sticky":"","inherit":false}} -->
	<div class="wp-block-query">
		<!-- wp:post-template {"style":{"spacing":{"blockGap":"var:preset|spacing|40"}}} -->
			<!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|40"}}}} -->
			<div class="wp-block-columns are-vertically-aligned-center">
				<!-- wp:column {"verticalAlignment":"center","width":"34%"} -->
				<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:34%">
					<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"4/3"} /-->
				</div>
				<!-- /wp:column -->
				<!-- wp:column {"verticalAlignment":"center"} -->
				<div class="wp-block-column is-vertically-aligned-center">
					<!-- wp:post-date {"fontSize":"meta","textColor":"muted"} /-->
					<!-- wp:post-title {"isLink":true,"fontSize":"large","style":{"typography":{"fontWeight":"600"}}} /-->
					<!-- wp:post-excerpt {"textColor":"muted","excerptLength":22} /-->
				</div>
				<!-- /wp:column -->
			</div>
			<!-- /wp:columns -->
		<!-- /wp:post-template -->
	</div>
	<!-- /wp:query -->
</div>
<!-- /wp:group -->
