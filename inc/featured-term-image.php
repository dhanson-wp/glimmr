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
 * Register the stored attachment id as typed term meta.
 *
 * This keeps the custom field visible to core metadata tooling while preserving
 * the theme-owned admin UI that edits it.
 */
function glimmr_register_term_image_meta() {
	foreach ( glimmr_term_image_taxonomies() as $taxonomy ) {
		register_term_meta(
			$taxonomy,
			'featured_image_id',
			array(
				'type'              => 'integer',
				'description'       => __( 'Attachment id for the term archive featured image.', 'glimmr' ),
				'single'            => true,
				'sanitize_callback' => 'absint',
				'auth_callback'     => static function () use ( $taxonomy ) {
					$taxonomy_object = get_taxonomy( $taxonomy );
					$capability      = $taxonomy_object && isset( $taxonomy_object->cap->manage_terms )
						? $taxonomy_object->cap->manage_terms
						: 'manage_categories';

					return current_user_can( $capability );
				},
				'show_in_rest'      => array(
					'schema' => array(
						'type' => 'integer',
					),
				),
			)
		);
	}
}
add_action( 'init', 'glimmr_register_term_image_meta' );

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
 * Decorative image icon used by the empty admin control.
 *
 * @return string SVG markup.
 */
function glimmr_term_image_icon() {
	return '<svg class="glimmr-term-image__icon" viewBox="0 -960 960 960" aria-hidden="true" focusable="false"><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm40-160h480q12 0 18-11t-2-21L600-558q-6-8-16-8t-16 8L450-402l-74-99q-6-8-16-8t-16 8l-90 120q-8 10-2 21t18 11Z"/></svg>';
}

/**
 * Human-readable taxonomy name for the admin helper copy.
 *
 * @param string $taxonomy Taxonomy slug.
 * @return string Lowercase taxonomy name.
 */
function glimmr_term_image_taxonomy_name( $taxonomy ) {
	if ( 'category' === $taxonomy ) {
		return __( 'category', 'glimmr' );
	}
	if ( 'post_tag' === $taxonomy ) {
		return __( 'tag', 'glimmr' );
	}
	return __( 'term', 'glimmr' );
}

/**
 * Field on the "Add new term" form (compact).
 *
 * @param string $taxonomy Taxonomy slug.
 */
function glimmr_term_image_add_field( $taxonomy = '' ) {
	$taxonomy_name  = glimmr_term_image_taxonomy_name( $taxonomy );
	$description_id = 'glimmr-term-image-description-' . sanitize_html_class( (string) $taxonomy );
	wp_nonce_field( 'glimmr_term_image', 'glimmr_term_image_nonce' );
	?>
	<div class="form-field glimmr-term-image-field">
		<label><?php esc_html_e( 'Featured image', 'glimmr' ); ?></label>
		<div class="glimmr-term-image" data-glimmr-term-image>
			<input type="hidden" name="glimmr_featured_image_id" value="" data-glimmr-input />
			<span class="screen-reader-text" data-glimmr-status aria-live="polite"></span>
			<div class="ft-set" data-glimmr-set-state hidden>
				<button type="button" class="ft-preview" data-glimmr-set aria-label="<?php esc_attr_e( 'Replace featured image', 'glimmr' ); ?>">
					<span data-glimmr-preview></span>
				</button>
				<div class="ft-actions">
					<button type="button" class="components-button is-secondary glimmr-term-image__set" data-glimmr-set><?php esc_html_e( 'Replace image', 'glimmr' ); ?></button>
					<button type="button" class="components-button is-link is-destructive glimmr-term-image__remove" data-glimmr-remove hidden><?php esc_html_e( 'Remove featured image', 'glimmr' ); ?></button>
				</div>
			</div>
			<button type="button" class="ft-set-btn" data-glimmr-empty data-glimmr-set aria-describedby="<?php echo esc_attr( $description_id ); ?>">
				<?php echo glimmr_term_image_icon(); ?>
				<span><?php esc_html_e( 'Set featured image', 'glimmr' ); ?></span>
			</button>
		</div>
		<p id="<?php echo esc_attr( $description_id ); ?>" class="description">
			<?php
			printf(
				/* translators: %s: taxonomy name, such as category or tag. */
				esc_html__( 'Used as the cover hero at the top of this %s archive. Recommended 1600 × 900 or larger.', 'glimmr' ),
				esc_html( $taxonomy_name )
			);
			?>
		</p>
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
	$id             = glimmr_get_term_featured_image_id( $term->term_id );
	$url            = $id ? wp_get_attachment_image_url( $id, 'medium' ) : '';
	$taxonomy_name  = glimmr_term_image_taxonomy_name( $term->taxonomy );
	$description_id = 'glimmr-term-image-description-' . (int) $term->term_id;
	wp_nonce_field( 'glimmr_term_image', 'glimmr_term_image_nonce' );
	?>
	<tr class="form-field glimmr-term-image-field feat-row">
		<th scope="row">
			<label><?php esc_html_e( 'Featured image', 'glimmr' ); ?></label>
			<span class="wpa-feat-flag"><?php esc_html_e( 'Glimmr', 'glimmr' ); ?></span>
		</th>
		<td>
			<div class="glimmr-term-image" data-glimmr-term-image>
				<input type="hidden" name="glimmr_featured_image_id" value="<?php echo esc_attr( (string) $id ); ?>" data-glimmr-input />
				<span class="screen-reader-text" data-glimmr-status aria-live="polite"></span>
				<div class="ft-set" data-glimmr-set-state <?php echo $id ? '' : 'hidden'; ?>>
					<button type="button" class="ft-preview" data-glimmr-set aria-label="<?php esc_attr_e( 'Replace featured image', 'glimmr' ); ?>">
						<span data-glimmr-preview>
							<?php if ( $url ) : ?>
								<img src="<?php echo esc_url( $url ); ?>" alt="<?php esc_attr_e( 'Current featured image', 'glimmr' ); ?>" />
							<?php endif; ?>
						</span>
					</button>
					<div class="ft-actions">
						<button type="button" class="components-button is-secondary glimmr-term-image__set" data-glimmr-set><?php esc_html_e( 'Replace image', 'glimmr' ); ?></button>
						<button type="button" class="components-button is-link is-destructive glimmr-term-image__remove" data-glimmr-remove <?php echo $id ? '' : 'hidden'; ?>><?php esc_html_e( 'Remove featured image', 'glimmr' ); ?></button>
					</div>
				</div>
				<button type="button" class="ft-set-btn" data-glimmr-empty data-glimmr-set aria-describedby="<?php echo esc_attr( $description_id ); ?>" <?php echo $id ? 'hidden' : ''; ?>>
					<?php echo glimmr_term_image_icon(); ?>
					<span><?php esc_html_e( 'Set featured image', 'glimmr' ); ?></span>
				</button>
			</div>
			<p id="<?php echo esc_attr( $description_id ); ?>" class="description">
				<?php
				printf(
					/* translators: %s: taxonomy name, such as category or tag. */
					esc_html__( 'Used as the cover hero at the top of this %s archive. Recommended 1600 × 900 or larger.', 'glimmr' ),
					esc_html( $taxonomy_name )
				);
				?>
			</p>
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
		|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['glimmr_term_image_nonce'] ) ), 'glimmr_term_image' ) ) {
		return;
	}

	$taxonomy        = isset( $_POST['taxonomy'] ) ? sanitize_key( wp_unslash( $_POST['taxonomy'] ) ) : '';
	$taxonomy_object = $taxonomy ? get_taxonomy( $taxonomy ) : null;
	$capability      = $taxonomy_object && isset( $taxonomy_object->cap->manage_terms )
		? $taxonomy_object->cap->manage_terms
		: 'manage_categories';

	if ( ! current_user_can( $capability ) ) {
		return;
	}
	$id = isset( $_POST['glimmr_featured_image_id'] ) ? absint( wp_unslash( $_POST['glimmr_featured_image_id'] ) ) : 0;
	if ( $id > 0 ) {
		if ( ! wp_attachment_is_image( $id ) ) {
			delete_term_meta( $term_id, 'featured_image_id' );
			return;
		}
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
	$tax = isset( $_REQUEST['taxonomy'] ) ? sanitize_key( wp_unslash( $_REQUEST['taxonomy'] ) ) : '';
	if ( ! in_array( $tax, glimmr_term_image_taxonomies(), true ) ) {
		return;
	}
	wp_enqueue_media();
	$css_rel = 'assets/css/admin.css';
	wp_enqueue_style(
		'glimmr-term-image-admin',
		get_theme_file_uri( $css_rel ),
		array(),
		(string) filemtime( get_theme_file_path( $css_rel ) )
	);
	$rel = 'assets/js/term-image.js';
	wp_enqueue_script(
		'glimmr-term-image',
		get_theme_file_uri( $rel ),
		array( 'jquery', 'wp-a11y' ),
		(string) filemtime( get_theme_file_path( $rel ) ),
		true
	);
	wp_localize_script(
		'glimmr-term-image',
		'glimmrTermImage',
		array(
			'title'    => __( 'Featured image', 'glimmr' ),
			'button'   => __( 'Set featured image', 'glimmr' ),
			'set'      => __( 'Set featured image', 'glimmr' ),
			'replace' => __( 'Replace image', 'glimmr' ),
			'selected' => __( 'Featured image selected.', 'glimmr' ),
			'removed'  => __( 'Featured image removed.', 'glimmr' ),
			'alt'      => __( 'Current featured image', 'glimmr' ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'glimmr_term_image_admin_assets' );
