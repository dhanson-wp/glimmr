<?php
/**
 * Title: Notes
 * Slug: glimmr/notes
 * Categories: glimmr-journal, posts
 * Description: A compact, dateline-led list of short notes without thumbnails.
 */
?>
<!-- wp:group {"align":"wide","className":"glmr-journal glmr-notes","fontSize":"large","style":{"spacing":{"blockGap":"var:preset|spacing|30","padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}},"typography":{"lineHeight":"1.75"}},"layout":{"type":"constrained","contentSize":"680px"}} -->
<div class="wp-block-group alignwide glmr-journal glmr-notes has-large-font-size" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);line-height:1.75">
	<!-- wp:group {"className":"glmr-notes-head","layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"baseline"}} -->
	<div class="wp-block-group glmr-notes-head">
		<!-- wp:heading {"fontSize":"large"} -->
		<h2 class="wp-block-heading has-large-font-size">Notes</h2>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"fontSize":"meta","textColor":"faint"} -->
		<p class="has-faint-color has-text-color has-meta-font-size">short-form, between the longer pieces</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
	<!-- wp:query {"queryId":21,"query":{"perPage":6,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","sticky":"exclude","inherit":false}} -->
	<div class="wp-block-query">
		<!-- wp:post-template {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}}} -->
			<!-- wp:group {"className":"glmr-note-row","style":{"spacing":{"blockGap":"var:preset|spacing|10","padding":{"bottom":"var:preset|spacing|30"}},"border":{"bottom":{"color":"var:preset|color|hairline","width":"1px"}}},"layout":{"type":"constrained"}} -->
			<div class="wp-block-group glmr-note-row" style="border-bottom:1px solid var(--wp--preset--color--hairline);padding-bottom:var(--wp--preset--spacing--30)">
				<!-- wp:post-date {"fontSize":"meta","textColor":"muted"} /-->
				<!-- wp:post-excerpt {"className":"glmr-note-excerpt","moreText":"","showMoreOnNewLine":false,"excerptLength":30} /-->
			</div>
			<!-- /wp:group -->
		<!-- /wp:post-template -->
	</div>
	<!-- /wp:query -->
</div>
<!-- /wp:group -->
