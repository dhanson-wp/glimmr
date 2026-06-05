<?php
/**
 * Custom feature: a Featured image control on Edit Category / Edit Tag.
 *
 * Stores an attachment id as term meta (`featured_image_id`). That image becomes
 * the archive cover hero, surfaced through the `glimmr/term-image` binding source
 * + a scoped core/cover render filter (see inc/block-bindings.php).
 *
 * Content-agnostic: no term slugs are referenced anywhere. The control is added to
 * every category and tag; the value is per-term data the site owner sets.
 *
 * @package Glimmr
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Taxonomies that receive the control. */
function glimmr_term_image_taxonomies() {
	return apply_filters( 'glimmr_term_image_taxonomies', array( 'category', 'post_tag' ) );
}

/**
 * Read a term's featured image id.
 *
 * @param int $term_id Term id.
 * @return int Attachment id, or 0.
 */
function glimmr_get_term_featured_image_id( $term_id ) {
	return (int) get_term_meta( (int) $term_id, 'featured_image_id', true );
}

/**
 * Resolve a term's featured image URL.
 *
 * @param int    $term_id Term id.
 * @param string $size    Image size. Default 'full'.
 * @return string URL, or '' when unset/missing.
 */
function glimmr_get_term_featured_image_url( $term_id, $size = 'full' ) {
	$id = glimmr_get_term_featured_image_id( $term_id );
	if ( ! $id ) {
		return '';
	}
	$url = wp_get_attachment_image_url( $id, $size );
	return $url ? $url : '';
}

/**
 * Field on the "Add new term" form (compact).
 */
function glimmr_term_image_add_field() {
	wp_nonce_field( 'glimmr_term_image', 'glimmr_term_image_nonce' );
	?>
	<div class="form-field glimmr-term-image-field">
		<label><?php esc_html_e( 'Featured image', 'glimmr' ); ?></label>
		<div class="glimmr-term-image" data-glimmr-term-image>
			<input type="hidden" name="glimmr_featured_image_id" value="" data-glimmr-input />
			<div class="glimmr-term-image__preview" data-glimmr-preview></div>
			<button type="button" class="button glimmr-term-image__set" data-glimmr-set><?php esc_html_e( 'Set featured image', 'glimmr' ); ?></button>
			<button type="button" class="button-link is-destructive glimmr-term-image__remove" data-glimmr-remove hidden><?php esc_html_e( 'Remove featured image', 'glimmr' ); ?></button>
		</div>
		<p><?php esc_html_e( 'Shown as the cover hero on this term’s archive.', 'glimmr' ); ?></p>
	</div>
	<?php
}
foreach ( glimmr_term_image_taxonomies() as $glimmr_tax ) {
	add_action( "{$glimmr_tax}_add_form_fields", 'glimmr_term_image_add_field' );
}

/**
 * Field on the "Edit term" form (table row).
 *
 * @param WP_Term $term Current term.
 */
function glimmr_term_image_edit_field( $term ) {
	$id  = glimmr_get_term_featured_image_id( $term->term_id );
	$url = $id ? wp_get_attachment_image_url( $id, 'medium' ) : '';
	wp_nonce_field( 'glimmr_term_image', 'glimmr_term_image_nonce' );
	?>
	<tr class="form-field glimmr-term-image-field">
		<th scope="row"><label><?php esc_html_e( 'Featured image', 'glimmr' ); ?></label></th>
		<td>
			<div class="glimmr-term-image" data-glimmr-term-image>
				<input type="hidden" name="glimmr_featured_image_id" value="<?php echo esc_attr( (string) $id ); ?>" data-glimmr-input />
				<div class="glimmr-term-image__preview" data-glimmr-preview>
					<?php if ( $url ) : ?>
						<img src="<?php echo esc_url( $url ); ?>" alt="" />
					<?php endif; ?>
				</div>
				<button type="button" class="button glimmr-term-image__set" data-glimmr-set>
					<?php echo $id ? esc_html__( 'Replace image', 'glimmr' ) : esc_html__( 'Set featured image', 'glimmr' ); ?>
				</button>
				<button type="button" class="button-link is-destructive glimmr-term-image__remove" data-glimmr-remove <?php echo $id ? '' : 'hidden'; ?>><?php esc_html_e( 'Remove featured image', 'glimmr' ); ?></button>
			</div>
			<p class="description"><?php esc_html_e( 'Shown as the cover hero on this term’s archive.', 'glimmr' ); ?></p>
		</td>
	</tr>
	<?php
}
foreach ( glimmr_term_image_taxonomies() as $glimmr_tax ) {
	add_action( "{$glimmr_tax}_edit_form_fields", 'glimmr_term_image_edit_field' );
}

/**
 * Persist the value on term create / update.
 *
 * @param int $term_id Term id.
 */
function glimmr_save_term_image( $term_id ) {
	if ( ! isset( $_POST['glimmr_term_image_nonce'] )
		|| ! wp_verify_nonce( sanitize_key( $_POST['glimmr_term_image_nonce'] ), 'glimmr_term_image' ) ) {
		return;
	}
	if ( ! current_user_can( 'manage_categories' ) ) {
		return;
	}
	$id = isset( $_POST['glimmr_featured_image_id'] ) ? absint( $_POST['glimmr_featured_image_id'] ) : 0;
	if ( $id > 0 ) {
		update_term_meta( $term_id, 'featured_image_id', $id );
	} else {
		delete_term_meta( $term_id, 'featured_image_id' );
	}
}
foreach ( glimmr_term_image_taxonomies() as $glimmr_tax ) {
	add_action( "created_{$glimmr_tax}", 'glimmr_save_term_image' );
	add_action( "edited_{$glimmr_tax}", 'glimmr_save_term_image' );
}

/**
 * Enqueue the media picker on the term-edit screens only.
 *
 * @param string $hook Current admin page.
 */
function glimmr_term_image_admin_assets( $hook ) {
	if ( 'edit-tags.php' !== $hook && 'term.php' !== $hook ) {
		return;
	}
	$tax = isset( $_REQUEST['taxonomy'] ) ? sanitize_key( $_REQUEST['taxonomy'] ) : '';
	if ( ! in_array( $tax, glimmr_term_image_taxonomies(), true ) ) {
		return;
	}
	wp_enqueue_media();
	$rel = 'assets/js/term-image.js';
	wp_enqueue_script(
		'glimmr-term-image',
		get_theme_file_uri( $rel ),
		array( 'jquery' ),
		(string) filemtime( get_theme_file_path( $rel ) ),
		true
	);
	wp_localize_script(
		'glimmr-term-image',
		'glimmrTermImage',
		array(
			'title'  => __( 'Featured image', 'glimmr' ),
			'button' => __( 'Set featured image', 'glimmr' ),
			'set'    => __( 'Set featured image', 'glimmr' ),
			'replace' => __( 'Replace image', 'glimmr' ),
		)
	);
	wp_add_inline_style(
		'common',
		'.glimmr-term-image__preview img{max-width:160px;height:auto;display:block;margin-bottom:8px;border:1px solid #dcdcde}'
		. '.glimmr-term-image__remove{margin-left:8px;color:#b32d2e}'
	);
}
add_action( 'admin_enqueue_scripts', 'glimmr_term_image_admin_assets' );
