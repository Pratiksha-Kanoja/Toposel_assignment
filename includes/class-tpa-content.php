<?php
/**
 * Reads products, categories, promotions and settings and turns them into
 * plain arrays for the templates.
 */

defined( 'ABSPATH' ) || exit;

final class TPA_Content {

	private const CATEGORY_ICONS = array(
		'men'    => 'man',
		'man'    => 'man',
		'male'   => 'man',
		'women'  => 'woman',
		'woman'  => 'woman',
		'female' => 'woman',
	);

	public static function view_model(): array {
		$settings = TPA_Settings::all();
		$products = self::products();

		return array(
			'settings'   => $settings,
			'brand'      => self::brand_parts( (string) $settings['brand_name'] ),
			'user'       => self::user( $settings ),
			'team'       => array_slice( array_filter( array_map( 'absint', explode( ',', (string) $settings['team_avatar_ids'] ) ) ), 0, 3 ),
			'auth'       => self::auth_link(),
			'categories' => self::categories(),
			'products'   => $products,
			'favourites' => array_values( array_filter( $products, static function ( array $product ): bool {
				return $product['favourite'];
			} ) ),
			'promos'     => self::promos(),
		);
	}

	/**
	 * Published products, featured first, then by menu order.
	 */
	public static function products(): array {
		$posts = get_posts(
			array(
				'post_type'      => TPA_Post_Types::PRODUCT,
				'post_status'    => 'publish',
				'posts_per_page' => (int) apply_filters( 'tpa_products_limit', 24 ),
				'orderby'        => array(
					'menu_order' => 'ASC',
					'title'      => 'ASC',
				),
			)
		);

		$products = array_map( array( self::class, 'product' ), $posts );

		$featured = array_filter( $products, static function ( array $p ): bool {
			return $p['featured'];
		} );
		$others   = array_filter( $products, static function ( array $p ): bool {
			return ! $p['featured'];
		} );

		return array_merge( array_values( $featured ), array_values( $others ) );
	}

	private static function product( WP_Post $post ): array {
		$terms = get_the_terms( $post, TPA_Post_Types::PRODUCT_CAT );
		$price = (float) TPA_Meta_Boxes::get( $post->ID, 'price' );

		return array(
			'id'          => $post->ID,
			'title'       => get_the_title( $post ),
			'url'         => self::link( (string) TPA_Meta_Boxes::get( $post->ID, 'url' ) ),
			'price'       => $price,
			'price_label' => $price > 0 ? self::format_price( $price ) : '',
			'eyebrow'     => (string) TPA_Meta_Boxes::get( $post->ID, 'eyebrow' ),
			'swatches'    => TPA_Fields::parse_colors( (string) TPA_Meta_Boxes::get( $post->ID, 'swatches' ) ),
			'image_id'    => (int) get_post_thumbnail_id( $post ),
			'categories'  => is_array( $terms ) ? wp_list_pluck( $terms, 'slug' ) : array(),
			'featured'    => (bool) TPA_Meta_Boxes::get( $post->ID, 'featured' ),
			'favourite'   => (bool) TPA_Meta_Boxes::get( $post->ID, 'favourite' ),
		);
	}

	public static function categories(): array {
		$terms = get_terms(
			array(
				'taxonomy'   => TPA_Post_Types::PRODUCT_CAT,
				'hide_empty' => true,
			)
		);
		if ( is_wp_error( $terms ) ) {
			return array();
		}

		return array_map(
			static function ( WP_Term $term ): array {
				return array(
					'slug' => $term->slug,
					'name' => $term->name,
					'icon' => self::CATEGORY_ICONS[ $term->slug ] ?? 'tag',
				);
			},
			$terms
		);
	}

	/**
	 * One promotion per layout slot; the lowest menu order wins.
	 *
	 * @return array<string,array>
	 */
	public static function promos(): array {
		$posts = get_posts(
			array(
				'post_type'      => TPA_Post_Types::PROMO,
				'post_status'    => 'publish',
				'posts_per_page' => 50,
				'orderby'        => array(
					'menu_order' => 'ASC',
					'date'       => 'DESC',
				),
			)
		);

		$promos = array();
		foreach ( $posts as $post ) {
			$slot = (string) TPA_Meta_Boxes::get( $post->ID, 'slot' );
			if ( isset( $promos[ $slot ] ) || ! array_key_exists( $slot, TPA_Post_Types::promo_slots() ) ) {
				continue;
			}
			$promos[ $slot ] = array(
				'id'                 => $post->ID,
				'slot'               => $slot,
				'title'              => get_the_title( $post ),
				'subtitle'           => (string) TPA_Meta_Boxes::get( $post->ID, 'subtitle' ),
				'button_label'       => (string) TPA_Meta_Boxes::get( $post->ID, 'button_label' ),
				'url'                => self::link( (string) TPA_Meta_Boxes::get( $post->ID, 'url' ) ),
				'bg'                 => (string) TPA_Meta_Boxes::get( $post->ID, 'bg_color' ),
				'image_id'           => (int) get_post_thumbnail_id( $post ),
				'secondary_image_id' => (int) TPA_Meta_Boxes::get( $post->ID, 'secondary_image_id' ),
			);
		}
		return $promos;
	}

	public static function format_price( float $price ): string {
		$decimals = floor( $price ) === $price ? 0 : 2;
		return TPA_Settings::get( 'currency' ) . number_format_i18n( $price, $decimals );
	}

	public static function link( string $url ): string {
		return '' !== $url ? $url : '#';
	}

	/**
	 * Splits "BuyMore" into ["Buy", "More"] so the second word can carry the accent.
	 *
	 * @return string[]
	 */
	private static function brand_parts( string $brand ): array {
		if ( preg_match( '/^(\p{Lu}?[^\p{Lu}]+)(\p{Lu}.*)$/u', $brand, $matches ) ) {
			return array( $matches[1], $matches[2] );
		}
		return array( $brand, '' );
	}

	private static function user( array $settings ): array {
		$current = wp_get_current_user();
		$name    = (string) $settings['user_name'];
		if ( '' === $name && $current->exists() ) {
			$name = $current->display_name;
		}

		$avatar = '';
		if ( $settings['user_avatar_id'] ) {
			$avatar = wp_get_attachment_image( (int) $settings['user_avatar_id'], 'thumbnail', false, array( 'class' => 'tpa-user__avatar', 'alt' => '' ) );
		} elseif ( $current->exists() ) {
			$avatar = get_avatar( $current->ID, 92, '', '', array( 'class' => 'tpa-user__avatar' ) );
		}

		return array(
			'name'   => $name,
			'avatar' => $avatar,
		);
	}

	private static function auth_link(): array {
		$here = get_permalink() ?: home_url( '/' );
		if ( is_user_logged_in() ) {
			return array(
				'url'   => wp_logout_url( $here ),
				'label' => __( 'Log out', 'toposel-assignment' ),
			);
		}
		return array(
			'url'   => wp_login_url( $here ),
			'label' => __( 'Log in', 'toposel-assignment' ),
		);
	}
}
