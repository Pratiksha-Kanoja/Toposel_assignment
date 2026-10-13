<?php
/**
 * Detail fields for products and promotions.
 */

defined( 'ABSPATH' ) || exit;

final class TPA_Meta_Boxes {

	public const PREFIX = '_tpa_';
	private const NONCE = 'tpa_meta_nonce';

	public static function hooks(): void {
		add_action( 'add_meta_boxes', array( self::class, 'add' ) );
		add_action( 'save_post_' . TPA_Post_Types::PRODUCT, array( self::class, 'save' ), 10, 2 );
		add_action( 'save_post_' . TPA_Post_Types::PROMO, array( self::class, 'save' ), 10, 2 );
	}

	public static function schemas(): array {
		return array(
			TPA_Post_Types::PRODUCT => array(
				'title'  => __( 'Product details', 'toposel-assignment' ),
				'fields' => array(
					'price'     => array( 'type' => 'number', 'label' => __( 'Price', 'toposel-assignment' ), 'step' => '0.01' ),
					'eyebrow'   => array( 'type' => 'text', 'label' => __( 'Card label', 'toposel-assignment' ), 'placeholder' => __( 'Our Picks', 'toposel-assignment' ) ),
					'url'       => array( 'type' => 'url', 'label' => __( 'Product link', 'toposel-assignment' ) ),
					'swatches'  => array( 'type' => 'colors', 'label' => __( 'Colour swatches', 'toposel-assignment' ), 'placeholder' => '#ffaaee, #fbe599', 'description' => __( 'Comma-separated hex colours.', 'toposel-assignment' ) ),
					'featured'  => array( 'type' => 'checkbox', 'label' => __( 'Featured – shown first in the large product cards', 'toposel-assignment' ) ),
					'favourite' => array( 'type' => 'checkbox', 'label' => __( 'Show in the Favourites carousel', 'toposel-assignment' ) ),
				),
			),
			TPA_Post_Types::PROMO   => array(
				'title'  => __( 'Promotion details', 'toposel-assignment' ),
				'fields' => array(
					'slot'               => array(
						'type'        => 'select',
						'label'       => __( 'Position', 'toposel-assignment' ),
						'choices'     => TPA_Post_Types::promo_slots(),
						'description' => __( 'If several promotions share a position, the lowest "Order" wins.', 'toposel-assignment' ),
					),
					'subtitle'           => array( 'type' => 'text', 'label' => __( 'Subtitle', 'toposel-assignment' ) ),
					'button_label'       => array( 'type' => 'text', 'label' => __( 'Button label', 'toposel-assignment' ), 'description' => __( 'Leave empty to show a round arrow link instead.', 'toposel-assignment' ) ),
					'url'                => array( 'type' => 'url', 'label' => __( 'Link', 'toposel-assignment' ) ),
					'bg_color'           => array( 'type' => 'color', 'label' => __( 'Background colour', 'toposel-assignment' ), 'default' => '#f7f7f7' ),
					'secondary_image_id' => array( 'type' => 'image', 'label' => __( 'Secondary image (discount banner, left side)', 'toposel-assignment' ), 'description' => __( 'The main image is set in the "Main image" box.', 'toposel-assignment' ) ),
				),
			),
		);
	}

	public static function add(): void {
		foreach ( self::schemas() as $post_type => $schema ) {
			add_meta_box( 'tpa-details', $schema['title'], array( self::class, 'render' ), $post_type, 'normal', 'high' );
		}
	}

	public static function render( WP_Post $post ): void {
		$schema = self::schemas()[ $post->post_type ];
		wp_nonce_field( self::NONCE, self::NONCE );
		echo '<div class="tpa-fields">';
		foreach ( $schema['fields'] as $key => $field ) {
			$value = get_post_meta( $post->ID, self::PREFIX . $key, true );
			if ( '' === $value && isset( $field['default'] ) ) {
				$value = $field['default'];
			}
			TPA_Fields::row( $field, 'tpa_meta[' . $key . ']', $value );
		}
		echo '</div>';
	}

	public static function save( int $post_id, WP_Post $post ): void {
		$nonce = isset( $_POST[ self::NONCE ] ) ? sanitize_text_field( wp_unslash( $_POST[ self::NONCE ] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, self::NONCE ) ) {
			return;
		}
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$input = isset( $_POST['tpa_meta'] ) && is_array( $_POST['tpa_meta'] ) ? wp_unslash( $_POST['tpa_meta'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		foreach ( self::schemas()[ $post->post_type ]['fields'] as $key => $field ) {
			update_post_meta( $post_id, self::PREFIX . $key, TPA_Fields::sanitize( $field, $input[ $key ] ?? '' ) );
		}
	}

	/**
	 * @return mixed
	 */
	public static function get( int $post_id, string $key ) {
		return get_post_meta( $post_id, self::PREFIX . $key, true );
	}
}
