<?php
/**
 * Shared renderer for the interactive Glimmr heart button.
 *
 * @package Glimmr
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the filled Material Symbols heart path used by Glimmr.
 *
 * @return string
 */
function glimmr_get_heart_icon_svg() {
	return '<svg viewBox="0 -960 960 960" aria-hidden="true" focusable="false"><path d="M480-147q-14 0-28.5-5T426-168l-69-63q-106-97-191.5-192.5T80-634q0-94 63-157t157-63q53 0 100 22.5t80 61.5q33-39 80-61.5T660-854q94 0 157 63t63 157q0 115-85 211T602-230l-68 62q-11 11-25.5 16t-28.5 5Z"/></svg>';
}

/**
 * Convert an attribute map to escaped HTML attributes.
 *
 * @param array $attrs Attribute map.
 * @return string
 */
function glimmr_html_attrs( $attrs ) {
	$html = '';

	foreach ( $attrs as $name => $value ) {
		if ( null === $value || false === $value ) {
			continue;
		}

		if ( true === $value ) {
			$html .= sprintf( ' %s', esc_attr( $name ) );
			continue;
		}

		$html .= sprintf( ' %1$s="%2$s"', esc_attr( $name ), esc_attr( (string) $value ) );
	}

	return $html;
}

/**
 * Register server-side derived state for the heart Interactivity API store.
 */
function glimmr_register_heart_interactivity_state() {
	if ( ! function_exists( 'wp_interactivity_state' ) || ! function_exists( 'wp_interactivity_get_context' ) ) {
		return;
	}

	static $registered = false;
	if ( $registered ) {
		return;
	}

	wp_interactivity_state(
		'glimmr/heart',
		array(
			'ariaLabel' => function () {
				$context = wp_interactivity_get_context( 'glimmr/heart' );

				return ! empty( $context['liked'] ) ? __( 'Unlike this photo', 'glimmr' ) : __( 'Like this photo', 'glimmr' );
			},
			'countText' => function () {
				$context = wp_interactivity_get_context( 'glimmr/heart' );

				return number_format_i18n( isset( $context['count'] ) ? absint( $context['count'] ) : 0 );
			},
			'label'     => function () {
				$context = wp_interactivity_get_context( 'glimmr/heart' );

				return ! empty( $context['liked'] ) ? __( 'Liked', 'glimmr' ) : __( 'Like', 'glimmr' );
			},
		)
	);

	$registered = true;
}

/**
 * Render an Interactivity API-powered heart button for a post.
 *
 * @param array $args Button args.
 * @return string
 */
function glimmr_render_heart_button( $args = array() ) {
	$post_id = isset( $args['post_id'] ) ? absint( $args['post_id'] ) : get_the_ID();
	if ( ! $post_id ) {
		return '';
	}

	glimmr_register_heart_interactivity_state();

	$variant    = isset( $args['variant'] ) && is_string( $args['variant'] ) ? sanitize_html_class( $args['variant'] ) : 'action';
	$show_count = array_key_exists( 'show_count', $args ) ? (bool) $args['show_count'] : true;
	$show_label = array_key_exists( 'show_label', $args ) ? (bool) $args['show_label'] : true;
	$classes    = array_filter(
		array(
			'wp-block-glimmr-heart-button',
			'glmr-heart',
			'glmr-heart--' . $variant,
			! empty( $args['class'] ) && is_string( $args['class'] ) ? $args['class'] : '',
		)
	);
	$like_meta  = get_post_meta( $post_id, '_glimmr_like_count', true );
	$likes      = '' === $like_meta ? 0 : absint( $like_meta );
	$context    = array(
		'postId'    => $post_id,
		'baseCount' => $likes,
		'count'     => $likes,
		'liked'     => false,
	);

	$context_attr = function_exists( 'wp_interactivity_data_wp_context' )
		? wp_interactivity_data_wp_context( $context )
		: sprintf( 'data-wp-context="%s"', esc_attr( wp_json_encode( $context ) ) );

	$attrs = array(
		'class'                       => implode( ' ', $classes ),
		'type'                        => 'button',
		'aria-label'                  => __( 'Like this photo', 'glimmr' ),
		'aria-pressed'                => 'false',
		'data-wp-interactive'         => 'glimmr/heart',
		'data-wp-init'                => 'callbacks.init',
		'data-wp-on--click'           => 'actions.toggle',
		'data-wp-bind--aria-label'    => 'state.ariaLabel',
		'data-wp-bind--aria-pressed'  => 'context.liked',
		'data-wp-class--is-liked'     => 'context.liked',
		'data-wp-class--is-hydrated'  => 'context.ready',
	);

	ob_start();
	?>
	<button<?php echo glimmr_html_attrs( $attrs ); ?> <?php echo $context_attr; ?>>
		<span class="glmr-heart__icon"><?php echo glimmr_get_heart_icon_svg(); ?></span>
		<?php if ( $show_count ) : ?>
			<span class="glmr-heart__count" data-wp-text="state.countText"><?php echo esc_html( number_format_i18n( $likes ) ); ?></span>
		<?php endif; ?>
		<?php if ( $show_label ) : ?>
			<span class="glmr-heart__label" data-wp-text="state.label"><?php esc_html_e( 'Like', 'glimmr' ); ?></span>
		<?php endif; ?>
	</button>
	<?php
	return trim( ob_get_clean() );
}
