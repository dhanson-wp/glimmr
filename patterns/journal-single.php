<?php
/**
 * Title: Journal single
 * Slug: glimmr/journal-single
 * Categories: glimmr-journal
 * Description: A single writing entry — constrained measure, looser line-height, title, date, body and tags.
 */
?>
<!-- wp:group {"className":"glmr-prose","align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|30","padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}},"layout":{"type":"constrained","contentSize":"680px"}} -->
<div class="wp-block-group glmr-prose alignwide" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)">
	<!-- wp:post-date {"fontSize":"meta","textColor":"muted"} /-->
	<!-- wp:post-title {"level":1,"fontSize":"xx-large"} /-->
	<!-- wp:post-content {"layout":{"type":"constrained"}} /-->
	<!-- wp:group {"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}},"border":{"top":{"color":"var:preset|color|hairline","width":"1px"}}},"layout":{"type":"flex","flexWrap":"wrap"}} -->
	<div class="wp-block-group" style="border-top:1px solid var(--wp--preset--color--hairline);padding-top:var(--wp--preset--spacing--30)">
		<!-- wp:post-terms {"term":"post_tag","className":"glmr-tags"} /-->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
