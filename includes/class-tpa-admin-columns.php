<?php
/**
 * Extra list-table columns so editors can scan products and promotions at a glance.
 */

defined( 'ABSPATH' ) || exit;

final class TPA_Admin_Columns {

	public static function hooks(): void {
		add_filter( 'manage_' . TPA_Post_Types::PRODUCT . '_posts_columns', array( self::class, 'product_columns' ) );
		add_action( 'manage_' . TPA_Post_Types::PRODUCT . '_posts_custom_column', array( self::class, 'product_cell' ), 10, 2 );
		add_filter( 'manage_' . TPA_Post_Types::PROMO . '_posts_columns', array( self::class, 'promo_columns' ) );
		add_action( 'manage_' . TPA_Post_Types::PROMO . '_posts_custom_column', array( self::class, 'promo_cell' ), 10, 2 );
	}

	public static function product_columns( array $columns ): array {
		return self::insert_after(
			self::insert_after( $columns, 'cb', array( 'tpa_image' => __( 'Image', 'toposel-assignment' ) ) ),
			'title',
			array(
				'tpa_price' => __( 'Price', 'toposel-assignment' ),
				'tpa_flags' => __( 'Shown in', 'toposel-assignment' ),
			)
		);
	}

	public static function product_cell( string $column, int $post_id ): void {
		switch ( $column ) {
			case 'tpa_image':
				echo get_the_post_thumbnail( $post_id, array( 48, 48 ) );
				break;
			case 'tpa_price':
				$price = TPA_Meta_Boxes::get( $post_id, 'price' );
				echo '' !== $price ? esc_html( TPA_Content::format_price( (float) $price ) ) : '—';
				break;
			case 'tpa_flags':
				$flags = array();
				if ( TPA_Meta_Boxes::get( $post_id, 'featured' ) ) {
					$flags[] = __( 'Featured', 'toposel-assignment' );
				}
				if ( TPA_Meta_Boxes::get( $post_id, 'favourite' ) ) {
					$flags[] = __( 'Favourites', 'toposel-assignment' );
				}
				echo esc_html( $flags ? implode( ', ', $flags ) : '—' );
				break;
		}
	}

	public static function promo_columns( array $columns ): array {
		return self::insert_after(
			self::insert_after( $columns, 'cb', array( 'tpa_image' => __( 'Image', 'toposel-assignment' ) ) ),
			'title',
			array( 'tpa_slot' => __( 'Position', 'toposel-assignment' ) )
		);
	}

	public static function promo_cell( string $column, int $post_id ): void {
		if ( 'tpa_image' === $column ) {
			echo get_the_post_thumbnail( $post_id, array( 48, 48 ) );
		} elseif ( 'tpa_slot' === $column ) {
			$slots = TPA_Post_Types::promo_slots();
			$slot  = (string) TPA_Meta_Boxes::get( $post_id, 'slot' );
			echo esc_html( $slots[ $slot ] ?? '—' );
		}
	}

	private static function insert_after( array $columns, string $after, array $new ): array {
		$result = array();
		foreach ( $columns as $key => $label ) {
			$result[ $key ] = $label;
			if ( $key === $after ) {
				$result += $new;
			}
		}
		return $result + $new;
	}
}
