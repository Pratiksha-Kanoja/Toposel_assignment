<?php
/**
 * One-time demo content import: media, categories, products, promotions,
 * settings and an "Explore" page containing the shortcode.
 */

defined( 'ABSPATH' ) || exit;

final class TPA_Installer {

	public const PAGE_OPTION  = 'tpa_explore_page_id';
	private const DONE_OPTION = 'tpa_demo_imported';

	public static function maybe_install(): void {
		if ( get_option( self::DONE_OPTION ) ) {
			return;
		}
		// Mark first so concurrent requests don't import twice.
		update_option( self::DONE_OPTION, TPA_VERSION );

		if ( ! apply_filters( 'tpa_import_demo_content', true ) ) {
			return;
		}

		$images = self::import_images();
		self::create_categories();
		self::create_products( $images );
		self::create_promotions( $images );
		self::save_settings( $images );
		self::create_page();
	}

	/**
	 * @return array<string,int> Attachment IDs keyed by file name (without extension).
	 */
	private static function import_images(): array {
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$ids = array();
		foreach ( glob( TPA_PLUGIN_DIR . 'assets/demo/*.jpg' ) as $file ) {
			$name   = basename( $file, '.jpg' );
			$upload = wp_upload_bits( basename( $file ), null, (string) file_get_contents( $file ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			if ( ! empty( $upload['error'] ) ) {
				continue;
			}

			$attachment_id = wp_insert_attachment(
				array(
					'post_mime_type' => 'image/jpeg',
					'post_title'     => ucwords( str_replace( '-', ' ', $name ) ),
					'post_status'    => 'inherit',
				),
				$upload['file']
			);
			if ( ! $attachment_id || is_wp_error( $attachment_id ) ) {
				continue;
			}

			wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $upload['file'] ) );
			$ids[ $name ] = (int) $attachment_id;
		}
		return $ids;
	}

	private static function create_categories(): void {
		foreach ( array( 'men' => __( 'Men', 'toposel-assignment' ), 'women' => __( 'Women', 'toposel-assignment' ) ) as $slug => $name ) {
			if ( ! term_exists( $slug, TPA_Post_Types::PRODUCT_CAT ) ) {
				wp_insert_term( $name, TPA_Post_Types::PRODUCT_CAT, array( 'slug' => $slug ) );
			}
		}
	}

	private static function create_products( array $images ): void {
		$products = array(
			array( 'WMX Rubber Zebra sandal', 36, 'Our Picks', '#ffaaee, #fbe599', array( 'women' ), true, false, 'product-sandal' ),
			array( 'Supper Skiny jogger in brown', 89, 'Your Choice', '#e9d349, #24211c', array( 'men', 'women' ), true, false, 'product-jogger' ),
			array( 'Tie-dye pullover hoodie', 54, 'Trending', '#f6a38f, #2f6bea', array( 'men' ), false, true, 'fav-hoodie' ),
			array( 'Green tie-dye tee', 32, 'New In', '#9bd56b, #2f7d32', array( 'women' ), false, true, 'fav-tee' ),
			array( 'Chunky knit sweater', 65, 'Cosy Edit', '#f28c28, #f4e9e3', array( 'women' ), false, true, 'offer-bow' ),
			array( 'Hooded puffer parka', 129, 'Winter Ready', '#7b7466, #d8cbb5', array( 'men' ), false, true, 'promo-layers' ),
		);

		foreach ( $products as $order => list( $title, $price, $eyebrow, $swatches, $categories, $featured, $favourite, $image ) ) {
			$post_id = wp_insert_post(
				array(
					'post_type'   => TPA_Post_Types::PRODUCT,
					'post_status' => 'publish',
					'post_title'  => $title,
					'menu_order'  => $order,
				)
			);
			if ( ! $post_id || is_wp_error( $post_id ) ) {
				continue;
			}

			self::save_meta(
				$post_id,
				array(
					'price'     => $price,
					'eyebrow'   => $eyebrow,
					'url'       => '',
					'swatches'  => $swatches,
					'featured'  => $featured ? '1' : '',
					'favourite' => $favourite ? '1' : '',
				)
			);
			wp_set_object_terms( $post_id, $categories, TPA_Post_Types::PRODUCT_CAT );
			if ( isset( $images[ $image ] ) ) {
				set_post_thumbnail( $post_id, $images[ $image ] );
			}
		}
	}

	private static function create_promotions( array $images ): void {
		$promos = array(
			array(
				'title'     => 'GET UP TO 50% OFF',
				'image'     => 'promo-hero-right',
				'secondary' => 'promo-hero-left',
				'meta'      => array( 'slot' => 'hero', 'subtitle' => '', 'button_label' => 'Get Discount', 'bg_color' => '#bdf0d2' ),
			),
			array(
				'title' => 'Winter’s weekend',
				'image' => 'promo-winter',
				'meta'  => array( 'slot' => 'spotlight', 'subtitle' => 'keep it casual', 'button_label' => '', 'bg_color' => '#feeb9e' ),
			),
			array(
				'title' => 'Knitwear season offer',
				'image' => 'offer-bow',
				'meta'  => array( 'slot' => 'offer', 'subtitle' => '', 'button_label' => 'Avail Offers', 'bg_color' => '#ebebeb' ),
			),
			array(
				'title' => 'Bring Bold Fashion',
				'image' => 'promo-layers',
				'meta'  => array( 'slot' => 'banner', 'subtitle' => 'Layers on Layers', 'button_label' => '', 'bg_color' => '#f0f6f6' ),
			),
		);

		foreach ( $promos as $order => $promo ) {
			$post_id = wp_insert_post(
				array(
					'post_type'   => TPA_Post_Types::PROMO,
					'post_status' => 'publish',
					'post_title'  => $promo['title'],
					'menu_order'  => $order,
				)
			);
			if ( ! $post_id || is_wp_error( $post_id ) ) {
				continue;
			}

			$meta = $promo['meta'] + array(
				'url'                => home_url( '/' ),
				'secondary_image_id' => isset( $promo['secondary'], $images[ $promo['secondary'] ] ) ? $images[ $promo['secondary'] ] : 0,
			);
			self::save_meta( $post_id, $meta );
			if ( isset( $images[ $promo['image'] ] ) ) {
				set_post_thumbnail( $post_id, $images[ $promo['image'] ] );
			}
		}
	}

	private static function save_settings( array $images ): void {
		$team = array_filter(
			array(
				$images['avatar-team-1'] ?? 0,
				$images['avatar-team-2'] ?? 0,
				$images['avatar-team-3'] ?? 0,
			)
		);

		$settings = array_merge(
			TPA_Settings::defaults(),
			array(
				'user_name'       => 'Ryana',
				'user_avatar_id'  => $images['avatar-ryana'] ?? 0,
				'team_avatar_ids' => implode( ',', $team ),
				'team_extra'      => '+8',
				'recent_orders'   => array(
					array( 'title' => 'DXC Nike Air Zoom', 'url' => '', 'image_id' => $images['product-jogger'] ?? 0 ),
					array( 'title' => 'Outerwear puffer parka', 'url' => '', 'image_id' => $images['promo-layers'] ?? 0 ),
				),
			)
		);
		update_option( TPA_Settings::OPTION, $settings );
	}

	private static function create_page(): void {
		$page_id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => __( 'Explore', 'toposel-assignment' ),
				'post_name'    => 'explore',
				'post_content' => "<!-- wp:shortcode -->\n[" . TPA_Shortcode::TAG . "]\n<!-- /wp:shortcode -->",
			)
		);
		if ( $page_id && ! is_wp_error( $page_id ) ) {
			update_option( self::PAGE_OPTION, (int) $page_id );
		}
	}

	private static function save_meta( int $post_id, array $values ): void {
		foreach ( $values as $key => $value ) {
			update_post_meta( $post_id, TPA_Meta_Boxes::PREFIX . $key, $value );
		}
	}
}
