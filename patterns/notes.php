<?php
/**
 * Title: Notes
 * Slug: glimmr/notes
 * Categories: glimmr-journal, posts
 * Description: A compact, dateline-led list of short notes — no thumbnails.
 */
?>
<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}},"layout":{"type":"constrained","contentSize":"680px"}} -->
<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)">
	<!-- wp:heading {"fontSize":"x-large"} -->
	<h2 class="wp-block-heading has-x-large-font-size">Notes</h2>
	<!-- /wp:heading -->
	<!-- wp:query {"queryId":21,"query":{"perPage":6,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","sticky":"","inherit":false}} -->
	<div class="wp-block-query">
		<!-- wp:post-template {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}}} -->
			<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|10","padding":{"bottom":"var:preset|spacing|30"}},"border":{"bottom":{"color":"var:preset|color|hairline","width":"1px"}}},"layout":{"type":"constrained"}} -->
			<div class="wp-block-group" style="border-bottom:1px solid var(--wp--preset--color--hairline);padding-bottom:var(--wp--preset--spacing--30)">
				<!-- wp:post-date {"fontSize":"meta","textColor":"muted"} /-->
				<!-- wp:post-excerpt {"showMoreOnNewLine":false,"excerptLength":30} /-->
			</div>
			<!-- /wp:group -->
		<!-- /wp:post-template -->
	</div>
	<!-- /wp:query -->
</div>
<!-- /wp:group -->
