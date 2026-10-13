<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Default content for a landing page. Used to seed the sample page and as a
 * fallback for fields that have never been saved.
 */
final class TPA_Defaults {
	/**
	 * @return array<string,string> data key => meta key
	 */
	public static function fields(): array {
		return [
			'brand_name' => TPA_Meta::META_BRAND_NAME,
			'user_name' => TPA_Meta::META_USER_NAME,
			'orders_count' => TPA_Meta::META_ORDERS_COUNT,
			'orders_period' => TPA_Meta::META_ORDERS_PERIOD,
			'top_tabs' => TPA_Meta::META_TOP_TABS,
			'nav_items' => TPA_Meta::META_NAV_ITEMS,
			'quick_actions' => TPA_Meta::META_QUICK_ACTIONS,
			'last_orders' => TPA_Meta::META_LAST_ORDERS,
			'main_tabs' => TPA_Meta::META_MAIN_TABS,
			'promo_primary' => TPA_Meta::META_PROMO_PRIMARY,
			'promo_secondary' => TPA_Meta::META_PROMO_SECONDARY,
			'product_1' => TPA_Meta::META_PRODUCT_1,
			'product_2' => TPA_Meta::META_PRODUCT_2,
			'offer_card' => TPA_Meta::META_OFFER_CARD,
			'favourites_card' => TPA_Meta::META_FAVOURITES_CARD,
			'bottom_banner' => TPA_Meta::META_BOTTOM_BANNER,
		];
	}

	/**
	 * @return array<string,mixed> data key => default value
	 */
	public static function values(): array {
		return [
			'brand_name' => 'BuyMore',
			'user_name' => 'Ryana',
			'orders_count' => 37,
			'orders_period' => 'Last 7 days',
			'top_tabs' => ['left' => 'Dashboard', 'right' => 'Website'],
			'nav_items' => [
				['label' => 'Popular Products', 'url' => '', 'active' => 0],
				['label' => 'Explore New', 'url' => '', 'active' => 1],
				['label' => 'Clothing and Shoes', 'url' => '', 'active' => 0],
				['label' => 'Gifts and Living', 'url' => '', 'active' => 0],
				['label' => 'Inspiration', 'url' => '', 'active' => 0],
			],
			'quick_actions' => [
				['label' => 'Request for product', 'url' => ''],
				['label' => 'Add member', 'url' => ''],
			],
			'last_orders' => [
				['title' => 'DXC Nike...', 'subtitle' => 'view order', 'url' => ''],
				['title' => 'Outerwear...', 'subtitle' => 'view order', 'url' => ''],
			],
			'main_tabs' => ['a' => 'All', 'b' => 'Men', 'c' => 'Women'],
			'promo_primary' => [
				'title' => 'GET UP TO 50% OFF',
				'cta_label' => 'Get Discount',
				'url' => '',
				'image_left_id' => 0,
				'image_right_id' => 0,
			],
			'promo_secondary' => [
				'title' => "Winter's weekend",
				'subtitle' => 'keep it casual',
				'url' => '',
				'image_id' => 0,
			],
			'product_1' => [
				'badge' => 'Our Picks',
				'title' => 'WMX Rubber Zebra sandal',
				'price' => '$36',
				'url' => '',
				'image_id' => 0,
				'dots' => '#e879f9,#fde047',
			],
			'product_2' => [
				'badge' => 'Your Choice',
				'title' => 'Super Skiny jogger in brown',
				'price' => '$89',
				'url' => '',
				'image_id' => 0,
				'dots' => '#fde047,#111111',
			],
			'offer_card' => ['pill' => 'Avail Offers', 'url' => '', 'image_id' => 0],
			'favourites_card' => [
				'title' => 'Favourites',
				'cta_label' => 'See All',
				'url' => '',
				'image_a_id' => 0,
				'image_b_id' => 0,
			],
			'bottom_banner' => [
				'title' => 'Bring Bold Fashion',
				'subtitle' => 'Layers on Layers',
				'url' => '',
				'image_id' => 0,
			],
		];
	}

	/**
	 * Bundled photos shown until an image is picked from the Media Library.
	 *
	 * @return array<string,string> "card.field" => file in assets/images
	 */
	public static function images(): array {
		return [
			'promo_primary.image_left_id' => 'promo-left.jpg',
			'promo_primary.image_right_id' => 'promo-right.jpg',
			'promo_secondary.image_id' => 'feature.jpg',
			'product_1.image_id' => 'product-1.jpg',
			'product_2.image_id' => 'product-2.jpg',
			'offer_card.image_id' => 'offer.jpg',
			'favourites_card.image_a_id' => 'fav-a.jpg',
			'favourites_card.image_b_id' => 'fav-b.jpg',
			'bottom_banner.image_id' => 'banner.jpg',
		];
	}

	public static function image_url(string $slot): string {
		$file = self::images()[$slot] ?? '';
		return $file !== '' ? TPA_PLUGIN_URL . 'assets/images/' . $file : '';
	}
}
