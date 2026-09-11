<?php
/**
 * Title: Journal query loop
 * Slug: glimmr/journal-loop
 * Categories: glimmr-journal, posts
 * Description: A calmer, text-forward loop with a small thumbnail, title, date, and excerpt.
 */
?>
<!-- wp:group {"align":"wide","className":"glmr-journal glmr-journal-loop","fontSize":"large","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}},"typography":{"lineHeight":"1.75"}},"layout":{"type":"constrained","contentSize":"680px"}} -->
<div class="wp-block-group alignwide glmr-journal glmr-journal-loop has-large-font-size" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);line-height:1.75">
	<!-- wp:heading {"className":"glmr-journal-head","fontSize":"meta"} -->
	<h2 class="wp-block-heading glmr-journal-head has-meta-font-size">From the journal</h2>
	<!-- /wp:heading -->
	<!-- wp:query {"queryId":20,"query":{"perPage":5,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","sticky":"exclude","inherit":false}} -->
	<div class="wp-block-query">
		<!-- wp:post-template {"style":{"spacing":{"blockGap":"var:preset|spacing|40"}}} -->
			<!-- wp:columns {"verticalAlignment":"center","isStackedOnMobile":false,"className":"glmr-journal-row","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|40"}}}} -->
			<div class="wp-block-columns are-vertically-aligned-center is-not-stacked-on-mobile glmr-journal-row">
				<!-- wp:column {"verticalAlignment":"center","width":"168px"} -->
				<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:168px">
					<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"3/2"} /-->
				</div>
				<!-- /wp:column -->
				<!-- wp:column {"verticalAlignment":"center"} -->
				<div class="wp-block-column is-vertically-aligned-center">
					<!-- wp:post-date {"fontSize":"meta","textColor":"muted"} /-->
					<!-- wp:post-title {"isLink":true,"fontSize":"large","style":{"typography":{"fontWeight":"600"}}} /-->
					<!-- wp:post-excerpt {"className":"glmr-journal-excerpt","textColor":"muted","moreText":"","showMoreOnNewLine":false,"excerptLength":22} /-->
					<!-- wp:read-more {"content":"Read more","className":"glmr-journal-more"} /-->
				</div>
				<!-- /wp:column -->
			</div>
			<!-- /wp:columns -->
		<!-- /wp:post-template -->
	</div>
	<!-- /wp:query -->
</div>
<!-- /wp:group -->
