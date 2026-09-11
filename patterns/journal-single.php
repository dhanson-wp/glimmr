<?php
/**
 * Title: Journal single
 * Slug: glimmr/journal-single
 * Categories: glimmr-journal
 * Description: A single writing entry with constrained measure, loose line-height, title, date, body, and tags.
 */
?>
<!-- wp:group {"className":"glmr-prose glmr-journal-single","align":"wide","fontSize":"large","style":{"spacing":{"blockGap":"var:preset|spacing|30","padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}},"typography":{"lineHeight":"1.75"}},"layout":{"type":"constrained","contentSize":"680px"}} -->
<div class="wp-block-group glmr-prose glmr-journal-single alignwide has-large-font-size" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);line-height:1.75">
	<!-- wp:group {"className":"glmr-journal-single-head","style":{"spacing":{"blockGap":"var:preset|spacing|20","margin":{"bottom":"var:preset|spacing|40"}}},"layout":{"type":"constrained"}} -->
	<div class="wp-block-group glmr-journal-single-head" style="margin-bottom:var(--wp--preset--spacing--40)">
		<!-- wp:post-terms {"term":"category","prefix":"Journal · ","separator":" · ","className":"glmr-journal-kicker"} /-->
		<!-- wp:post-title {"level":1,"fontSize":"xx-large"} /-->
		<!-- wp:group {"className":"glmr-journal-meta","layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"center"}} -->
		<div class="wp-block-group glmr-journal-meta">
			<!-- wp:avatar {"size":30,"className":"glmr-journal-author-avatar","style":{"border":{"radius":"9999px"}}} /-->
			<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"glimmr/author-name"}}},"fontSize":"meta","textColor":"muted"} -->
			<p class="has-muted-color has-text-color has-meta-font-size">Photographer</p>
			<!-- /wp:paragraph -->
			<!-- wp:paragraph {"className":"glmr-journal-meta__sep","fontSize":"meta","textColor":"faint"} -->
			<p class="glmr-journal-meta__sep has-faint-color has-text-color has-meta-font-size">·</p>
			<!-- /wp:paragraph -->
			<!-- wp:post-date {"fontSize":"meta","textColor":"muted"} /-->
			<!-- wp:paragraph {"className":"glmr-journal-meta__sep","fontSize":"meta","textColor":"faint"} -->
			<p class="glmr-journal-meta__sep has-faint-color has-text-color has-meta-font-size">·</p>
			<!-- /wp:paragraph -->
			<!-- wp:paragraph {"fontSize":"meta","textColor":"muted"} -->
			<p class="has-muted-color has-text-color has-meta-font-size">4 min read</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
	<!-- wp:post-featured-image {"aspectRatio":"2/1","className":"glmr-journal-lead"} /-->
	<!-- wp:post-content {"layout":{"type":"constrained"}} /-->
	<!-- wp:group {"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}},"border":{"top":{"color":"var:preset|color|hairline","width":"1px"}}},"layout":{"type":"flex","flexWrap":"wrap"}} -->
	<div class="wp-block-group" style="border-top:1px solid var(--wp--preset--color--hairline);padding-top:var(--wp--preset--spacing--30)">
		<!-- wp:post-terms {"term":"post_tag","className":"glmr-tags"} /-->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
