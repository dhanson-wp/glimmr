<?php
/**
 * Render the single-photo action row.
 *
 * @package Glimmr
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_singular( 'post' ) ) {
	return '';
}

$post_id   = get_the_ID();
$permalink = get_permalink( $post_id );
$title     = get_the_title( $post_id );
$image_url = get_the_post_thumbnail_url( $post_id, 'full' );
$download  = $image_url ? $image_url : $permalink;
$attrs     = get_block_wrapper_attributes( array( 'class' => 'glmr-photo-actions' ) );
$comments  = get_comments_number( $post_id );

$icons = array(
	'comment'  => '<svg viewBox="0 -960 960 960" aria-hidden="true"><path d="m240-240-92 92q-19 19-43.5 8.5T80-177v-623q0-33 23.5-56.5T160-880h640q33 0 56.5 23.5T880-800v480q0 33-23.5 56.5T800-240H240Z"/></svg>',
	'share'    => '<svg viewBox="0 -960 960 960" aria-hidden="true"><path d="M720-80q-50 0-85-35t-35-85q0-7 1-14.5t3-13.5L322-392q-17 15-38 23.5t-44 8.5q-50 0-85-35t-35-85q0-50 35-85t85-35q23 0 44 8.5t38 23.5l282-164q-2-6-3-13.5t-1-14.5q0-50 35-85t85-35q50 0 85 35t35 85q0 50-35 85t-85 35q-23 0-44-8.5T638-672L356-508q2 6 3 13.5t1 14.5q0 7-1 14.5t-3 13.5l282 164q17-15 38-23.5t44-8.5q50 0 85 35t35 85q0 50-35 85t-85 35Z"/></svg>',
	'download' => '<svg viewBox="0 -960 960 960" aria-hidden="true"><path d="M480-337q-8 0-15-2.5t-13-8.5L308-492q-12-12-11.5-28t11.5-28q12-12 28.5-12.5T365-549l75 75v-286q0-17 11.5-28.5T480-800q17 0 28.5 11.5T520-760v286l75-75q12-12 28.5-11.5T652-548q11 12 11.5 28T652-492L508-348q-6 6-13 8.5t-15 2.5ZM240-160q-33 0-56.5-23.5T160-240v-80q0-17 11.5-28.5T200-360q17 0 28.5 11.5T240-320v80h480v-80q0-17 11.5-28.5T760-360q17 0 28.5 11.5T800-320v80q0 33-23.5 56.5T720-160H240Z"/></svg>',
	'more'     => '<svg viewBox="0 -960 960 960" aria-hidden="true"><path d="M240-400q-33 0-56.5-23.5T160-480q0-33 23.5-56.5T240-560q33 0 56.5 23.5T320-480q0 33-23.5 56.5T240-400Zm240 0q-33 0-56.5-23.5T400-480q0-33 23.5-56.5T480-560q33 0 56.5 23.5T560-480q0 33-23.5 56.5T480-400Zm240 0q-33 0-56.5-23.5T640-480q0-33 23.5-56.5T720-560q33 0 56.5 23.5T800-480q0 33-23.5 56.5T720-400Z"/></svg>',
);
?>
<div <?php echo $attrs; ?>>
	<?php echo glimmr_render_heart_button( array( 'variant' => 'action', 'show_count' => true, 'show_label' => false, 'class' => 'glmr-action glmr-action--like' ) ); ?>
	<a class="glmr-action glmr-action--comment" href="#respond" aria-label="<?php esc_attr_e( 'Comment on this photo', 'glimmr' ); ?>">
		<?php echo $icons['comment']; ?>
		<span><?php echo esc_html( number_format_i18n( $comments ) ); ?></span>
	</a>
	<button class="glmr-action glmr-action--share" type="button" aria-label="<?php esc_attr_e( 'Share this photo', 'glimmr' ); ?>" data-share-url="<?php echo esc_url( $permalink ); ?>" data-share-title="<?php echo esc_attr( $title ); ?>" data-copied-label="<?php esc_attr_e( 'Link copied to clipboard.', 'glimmr' ); ?>" data-copied-text="<?php esc_attr_e( 'Copied', 'glimmr' ); ?>" data-shared-label="<?php esc_attr_e( 'Shared.', 'glimmr' ); ?>" data-shared-text="<?php esc_attr_e( 'Shared', 'glimmr' ); ?>" data-manual-label="<?php esc_attr_e( 'Copy this link:', 'glimmr' ); ?>" data-manual-status="<?php esc_attr_e( 'Copy the link from the dialog.', 'glimmr' ); ?>">
		<?php echo $icons['share']; ?>
		<span class="glmr-action__label"><?php esc_html_e( 'Share', 'glimmr' ); ?></span>
		<span class="screen-reader-text glmr-action__status" aria-live="polite"></span>
	</button>
	<a class="glmr-action glmr-action--download" href="<?php echo esc_url( $download ); ?>" download aria-label="<?php esc_attr_e( 'Download this photo', 'glimmr' ); ?>">
		<?php echo $icons['download']; ?>
		<span><?php esc_html_e( 'Download', 'glimmr' ); ?></span>
	</a>
	<details class="glmr-action-menu">
		<summary class="glmr-action glmr-action--more" aria-label="<?php esc_attr_e( 'More photo actions', 'glimmr' ); ?>" title="<?php esc_attr_e( 'More photo actions', 'glimmr' ); ?>">
			<?php echo $icons['more']; ?>
		</summary>
		<div class="glmr-action-menu__panel">
			<button class="glmr-action-menu__item glmr-action-menu__copy" type="button" data-share-url="<?php echo esc_url( $permalink ); ?>" data-copied-label="<?php esc_attr_e( 'Link copied to clipboard.', 'glimmr' ); ?>" data-manual-label="<?php esc_attr_e( 'Copy this link:', 'glimmr' ); ?>" data-manual-status="<?php esc_attr_e( 'Copy the link from the dialog.', 'glimmr' ); ?>">
				<?php esc_html_e( 'Copy link', 'glimmr' ); ?>
			</button>
			<?php if ( $image_url ) : ?>
				<a class="glmr-action-menu__item" href="<?php echo esc_url( $image_url ); ?>" target="_blank" rel="noopener">
					<?php esc_html_e( 'Open original', 'glimmr' ); ?>
				</a>
			<?php endif; ?>
			<span class="screen-reader-text glmr-action__status" aria-live="polite"></span>
		</div>
	</details>
</div>
