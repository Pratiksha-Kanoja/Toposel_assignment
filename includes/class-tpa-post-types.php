<?php
/**
 * Content model: products, product categories and promotional banners.
 */

defined( 'ABSPATH' ) || exit;

final class TPA_Post_Types {

	public const PRODUCT     = 'tpa_product';
	public const PROMO       = 'tpa_promo';
	public const PRODUCT_CAT = 'tpa_product_cat';

	public static function register(): void {
		register_post_type(
			self::PRODUCT,
			array(
				'labels'       => array(
					'name'                  => __( 'Products', 'toposel-assignment' ),
					'singular_name'         => __( 'Product', 'toposel-assignment' ),
					'add_new_item'          => __( 'Add New Product', 'toposel-assignment' ),
					'edit_item'             => __( 'Edit Product', 'toposel-assignment' ),
					'all_items'             => __( 'Products', 'toposel-assignment' ),
					'search_items'          => __( 'Search Products', 'toposel-assignment' ),
					'not_found'             => __( 'No products found.', 'toposel-assignment' ),
					'featured_image'        => __( 'Product image', 'toposel-assignment' ),
					'set_featured_image'    => __( 'Set product image', 'toposel-assignment' ),
					'remove_featured_image' => __( 'Remove product image', 'toposel-assignment' ),
				),
				'public'       => false,
				'show_ui'      => true,
				'show_in_menu' => TPA_Admin_Menu::SLUG,
				'supports'     => array( 'title', 'thumbnail', 'page-attributes' ),
				'show_in_rest' => false,
			)
		);

		register_taxonomy(
			self::PRODUCT_CAT,
			self::PRODUCT,
			array(
				'labels'            => array(
					'name'          => __( 'Product Categories', 'toposel-assignment' ),
					'singular_name' => __( 'Product Category', 'toposel-assignment' ),
					'menu_name'     => __( 'Categories', 'toposel-assignment' ),
					'add_new_item'  => __( 'Add New Category', 'toposel-assignment' ),
				),
				'public'            => false,
				'show_ui'           => true,
				'show_admin_column' => true,
				'hierarchical'      => true,
				'show_in_rest'      => false,
			)
		);

		register_post_type(
			self::PROMO,
			array(
				'labels'       => array(
					'name'                  => __( 'Promotions', 'toposel-assignment' ),
					'singular_name'         => __( 'Promotion', 'toposel-assignment' ),
					'add_new_item'          => __( 'Add New Promotion', 'toposel-assignment' ),
					'edit_item'             => __( 'Edit Promotion', 'toposel-assignment' ),
					'all_items'             => __( 'Promotions', 'toposel-assignment' ),
					'not_found'             => __( 'No promotions found.', 'toposel-assignment' ),
					'featured_image'        => __( 'Main image', 'toposel-assignment' ),
					'set_featured_image'    => __( 'Set main image', 'toposel-assignment' ),
					'remove_featured_image' => __( 'Remove main image', 'toposel-assignment' ),
				),
				'public'       => false,
				'show_ui'      => true,
				'show_in_menu' => TPA_Admin_Menu::SLUG,
				'supports'     => array( 'title', 'thumbnail', 'page-attributes' ),
				'show_in_rest' => false,
			)
		);
	}

	/**
	 * Layout positions a promotion can occupy on the Explore page.
	 *
	 * @return array<string,string>
	 */
	public static function promo_slots(): array {
		return array(
			'hero'      => __( 'Discount banner (top left)', 'toposel-assignment' ),
			'spotlight' => __( 'Spotlight banner (middle left)', 'toposel-assignment' ),
			'offer'     => __( 'Photo offer card (bottom left)', 'toposel-assignment' ),
			'banner'    => __( 'Wide banner (bottom right)', 'toposel-assignment' ),
		);
	}
}
