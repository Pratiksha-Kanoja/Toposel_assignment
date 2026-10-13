<?php
/**
 * "BuyMore" admin menu grouping settings, products, categories and promotions.
 */

defined( 'ABSPATH' ) || exit;

final class TPA_Admin_Menu {

	public const SLUG = 'tpa-explore';

	public static function hooks(): void {
		// Top-level page must exist before post types attach their submenus (priority 10).
		add_action( 'admin_menu', array( self::class, 'register' ), 9 );
		add_action( 'admin_menu', array( self::class, 'register_taxonomy_page' ), 20 );
		add_filter( 'parent_file', array( self::class, 'highlight_taxonomy_page' ) );
	}

	public static function register(): void {
		add_menu_page(
			__( 'BuyMore Explore', 'toposel-assignment' ),
			__( 'BuyMore', 'toposel-assignment' ),
			'manage_options',
			self::SLUG,
			array( TPA_Settings::class, 'render_page' ),
			'dashicons-store',
			26
		);

		add_submenu_page(
			self::SLUG,
			__( 'Page Settings', 'toposel-assignment' ),
			__( 'Page Settings', 'toposel-assignment' ),
			'manage_options',
			self::SLUG,
			array( TPA_Settings::class, 'render_page' )
		);
	}

	public static function register_taxonomy_page(): void {
		add_submenu_page(
			self::SLUG,
			__( 'Product Categories', 'toposel-assignment' ),
			__( 'Categories', 'toposel-assignment' ),
			'manage_categories',
			'edit-tags.php?taxonomy=' . TPA_Post_Types::PRODUCT_CAT . '&post_type=' . TPA_Post_Types::PRODUCT
		);
	}

	public static function highlight_taxonomy_page( $parent_file ) {
		$screen = get_current_screen();
		if ( $screen && TPA_Post_Types::PRODUCT_CAT === $screen->taxonomy ) {
			return self::SLUG;
		}
		return $parent_file;
	}
}
