<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
	exit;
}

final class TPA_Meta {
	private const NONCE_ACTION = 'tpa_save_landing_meta';
	private const NONCE_NAME = '_tpa_nonce';

	// Reference UI data (BuyMore / Explore).
	public const META_BRAND_NAME = '_tpa_brand_name';
	public const META_USER_NAME = '_tpa_user_name';
	public const META_ORDERS_COUNT = '_tpa_orders_count';
	public const META_ORDERS_PERIOD = '_tpa_orders_period';

	/** @var string meta: array{left:string,right:string} */
	public const META_TOP_TABS = '_tpa_top_tabs';

	/** @var string meta: array<int,array{label:string}> */
	public const META_NAV_ITEMS = '_tpa_nav_items';

	/** @var string meta: array<int,array{label:string}> */
	public const META_QUICK_ACTIONS = '_tpa_quick_actions';

	/** @var string meta: array<int,array{title:string,subtitle:string}> */
	public const META_LAST_ORDERS = '_tpa_last_orders';

	/** @var string meta: array{a:string,b:string,c:string} */
	public const META_MAIN_TABS = '_tpa_main_tabs';

	/** @var string meta: array{title:string,cta_label:string,image_left_id:int,image_right_id:int} */
	public const META_PROMO_PRIMARY = '_tpa_promo_primary';

	/** @var string meta: array{title:string,subtitle:string,image_id:int} */
	public const META_PROMO_SECONDARY = '_tpa_promo_secondary';

	/** @var string meta: array{badge:string,title:string,price:string,image_id:int,dots:string} */
	public const META_PRODUCT_1 = '_tpa_product_1';

	/** @var string meta: array{badge:string,title:string,price:string,image_id:int,dots:string} */
	public const META_PRODUCT_2 = '_tpa_product_2';

	/** @var string meta: array{pill:string,image_id:int} */
	public const META_OFFER_CARD = '_tpa_offer_card';

	/** @var string meta: array{title:string,image_a_id:int,image_b_id:int,cta_label:string} */
	public const META_FAVOURITES_CARD = '_tpa_favourites_card';

	/** @var string meta: array{title:string,subtitle:string,image_id:int} */
	public const META_BOTTOM_BANNER = '_tpa_bottom_banner';

	public static function init(): void {
		add_action('add_meta_boxes', [self::class, 'register_metaboxes']);
		add_action('save_post_' . TPA_CPT::POST_TYPE, [self::class, 'save'], 10, 2);
		add_filter('manage_' . TPA_CPT::POST_TYPE . '_posts_columns', [self::class, 'add_columns']);
		add_action('manage_' . TPA_CPT::POST_TYPE . '_posts_custom_column', [self::class, 'render_columns'], 10, 2);
	}

	public static function register_metaboxes(): void {
		add_meta_box(
			'tpa_landing_content',
			__('Landing Page Content', 'toposel-assignment'),
			[self::class, 'render_metabox'],
			TPA_CPT::POST_TYPE,
			'normal',
			'high'
		);
	}

	public static function render_metabox(\WP_Post $post): void {
		wp_nonce_field(self::NONCE_ACTION, self::NONCE_NAME);

		$d = TPA_Shortcode::get_data($post->ID);
		$brand_name = (string) $d['brand_name'];
		$user_name = (string) $d['user_name'];
		$orders_count = (int) $d['orders_count'];
		$orders_period = (string) $d['orders_period'];
		$top_tabs = $d['top_tabs'];
		$nav_items = $d['nav_items'];
		$quick_actions = $d['quick_actions'];
		$last_orders = $d['last_orders'];
		$main_tabs = $d['main_tabs'];
		$promo_primary = $d['promo_primary'];
		$promo_secondary = $d['promo_secondary'];
		$product_1 = $d['product_1'];
		$product_2 = $d['product_2'];
		$offer_card = $d['offer_card'];
		$favourites_card = $d['favourites_card'];
		$bottom_banner = $d['bottom_banner'];

		$app_url = home_url('/tp-landing/' . (int) $post->ID . '/');
		?>
		<style>
			.tpa-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
			.tpa-field label{display:block;font-weight:600;margin-bottom:6px}
			.tpa-field input[type="text"],.tpa-field input[type="url"],.tpa-field input[type="number"],.tpa-field textarea{width:100%}
			.tpa-sep{margin:20px 0;border-top:1px solid #ddd}
			.tpa-repeater{border:1px solid #ddd;border-radius:6px;padding:12px}
			.tpa-row{display:grid;grid-template-columns:1fr 1fr auto;gap:10px;align-items:start;margin-bottom:10px}
			.tpa-row textarea{min-height:70px}
			.tpa-row .tpa-remove{margin-top:24px}
			.tpa-help{color:#666}
			.tpa-image-preview img{max-width:100%;max-height:160px;width:auto;height:auto;border:1px solid #ddd;border-radius:6px}
			.tpa-image-preview .description{margin:4px 0 0}
			@media (max-width: 1024px){.tpa-grid{grid-template-columns:1fr}}
		</style>

		<p class="tpa-help">
			<strong><?php esc_html_e('App URL (theme-independent):', 'toposel-assignment'); ?></strong>
			<a href="<?php echo esc_url($app_url); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($app_url); ?></a>
		</p>

		<h3 style="margin:0 0 10px"><?php esc_html_e('Header', 'toposel-assignment'); ?></h3>
		<div class="tpa-grid">
			<div class="tpa-field">
				<label for="tpa_brand_name"><?php esc_html_e('Brand Name', 'toposel-assignment'); ?></label>
				<input id="tpa_brand_name" name="tpa_brand_name" type="text" value="<?php echo esc_attr($brand_name); ?>" placeholder="<?php esc_attr_e('e.g. BuyMore', 'toposel-assignment'); ?>" />
			</div>
			<div class="tpa-field">
				<label for="tpa_user_name"><?php esc_html_e('User Name', 'toposel-assignment'); ?></label>
				<input id="tpa_user_name" name="tpa_user_name" type="text" value="<?php echo esc_attr($user_name); ?>" placeholder="<?php esc_attr_e('e.g. Ryana', 'toposel-assignment'); ?>" />
			</div>
			<div class="tpa-field">
				<label for="tpa_orders_count"><?php esc_html_e('Orders Count', 'toposel-assignment'); ?></label>
				<input id="tpa_orders_count" name="tpa_orders_count" type="number" min="0" value="<?php echo esc_attr((string) $orders_count); ?>" />
			</div>
			<div class="tpa-field">
				<label for="tpa_orders_period"><?php esc_html_e('Orders Period Label', 'toposel-assignment'); ?></label>
				<input id="tpa_orders_period" name="tpa_orders_period" type="text" value="<?php echo esc_attr($orders_period); ?>" placeholder="<?php esc_attr_e('e.g. Last 7 days', 'toposel-assignment'); ?>" />
			</div>
			<div class="tpa-field">
				<label for="tpa_top_tab_left"><?php esc_html_e('Top Tabs (Left)', 'toposel-assignment'); ?></label>
				<input id="tpa_top_tab_left" name="tpa_top_tabs[left]" type="text" value="<?php echo esc_attr((string) ($top_tabs['left'] ?? 'Dashboard')); ?>" />
			</div>
			<div class="tpa-field">
				<label for="tpa_top_tab_right"><?php esc_html_e('Top Tabs (Right)', 'toposel-assignment'); ?></label>
				<input id="tpa_top_tab_right" name="tpa_top_tabs[right]" type="text" value="<?php echo esc_attr((string) ($top_tabs['right'] ?? 'Website')); ?>" />
			</div>
		</div>

		<div class="tpa-sep"></div>

		<h3 style="margin:0 0 10px"><?php esc_html_e('Sidebar Menu (repeatable)', 'toposel-assignment'); ?></h3>
		<div class="tpa-repeater" data-repeater="nav">
			<div class="tpa-repeater-rows">
				<?php foreach ($nav_items as $idx => $row) : ?>
					<?php self::render_nav_row((int) $idx, is_array($row) ? $row : []); ?>
				<?php endforeach; ?>
			</div>
			<button type="button" class="button tpa-add" data-add="nav"><?php esc_html_e('Add Menu Item', 'toposel-assignment'); ?></button>
		</div>

		<div class="tpa-sep"></div>

		<h3 style="margin:0 0 10px"><?php esc_html_e('Quick Actions (repeatable)', 'toposel-assignment'); ?></h3>
		<div class="tpa-repeater" data-repeater="actions">
			<div class="tpa-repeater-rows">
				<?php foreach ($quick_actions as $idx => $row) : ?>
					<?php self::render_action_row((int) $idx, is_array($row) ? $row : []); ?>
				<?php endforeach; ?>
			</div>
			<button type="button" class="button tpa-add" data-add="actions"><?php esc_html_e('Add Action', 'toposel-assignment'); ?></button>
		</div>

		<div class="tpa-sep"></div>

		<h3 style="margin:0 0 10px"><?php esc_html_e('Last Orders (repeatable)', 'toposel-assignment'); ?></h3>
		<div class="tpa-repeater" data-repeater="orders">
			<div class="tpa-repeater-rows">
				<?php foreach ($last_orders as $idx => $row) : ?>
					<?php self::render_last_order_row((int) $idx, is_array($row) ? $row : []); ?>
				<?php endforeach; ?>
			</div>
			<button type="button" class="button tpa-add" data-add="orders"><?php esc_html_e('Add Last Order', 'toposel-assignment'); ?></button>
		</div>

		<div class="tpa-sep"></div>

		<h3 style="margin:0 0 10px"><?php esc_html_e('Main Tabs', 'toposel-assignment'); ?></h3>
		<div class="tpa-grid">
			<div class="tpa-field">
				<label><?php esc_html_e('Tab 1', 'toposel-assignment'); ?></label>
				<input name="tpa_main_tabs[a]" type="text" value="<?php echo esc_attr((string) ($main_tabs['a'] ?? 'All')); ?>" />
			</div>
			<div class="tpa-field">
				<label><?php esc_html_e('Tab 2', 'toposel-assignment'); ?></label>
				<input name="tpa_main_tabs[b]" type="text" value="<?php echo esc_attr((string) ($main_tabs['b'] ?? 'Men')); ?>" />
			</div>
			<div class="tpa-field">
				<label><?php esc_html_e('Tab 3', 'toposel-assignment'); ?></label>
				<input name="tpa_main_tabs[c]" type="text" value="<?php echo esc_attr((string) ($main_tabs['c'] ?? 'Women')); ?>" />
			</div>
		</div>

		<div class="tpa-sep"></div>

		<h3 style="margin:0 0 10px"><?php esc_html_e('Cards / Content', 'toposel-assignment'); ?></h3>

		<h4 style="margin:14px 0 10px"><?php esc_html_e('Primary Promo (green)', 'toposel-assignment'); ?></h4>
		<div class="tpa-grid">
			<div class="tpa-field">
				<label><?php esc_html_e('Title', 'toposel-assignment'); ?></label>
				<input name="tpa_promo_primary[title]" type="text" value="<?php echo esc_attr((string) ($promo_primary['title'] ?? 'GET UP TO 50% OFF')); ?>" />
			</div>
			<div class="tpa-field">
				<label><?php esc_html_e('Button Label', 'toposel-assignment'); ?></label>
				<input name="tpa_promo_primary[cta_label]" type="text" value="<?php echo esc_attr((string) ($promo_primary['cta_label'] ?? 'Get Discount')); ?>" />
			</div>
			<?php self::render_url_field(__('Button Link', 'toposel-assignment'), 'tpa_promo_primary[url]', (string) ($promo_primary['url'] ?? '')); ?>
			<div></div>
			<?php self::render_image_field(__('Primary Image (Left)', 'toposel-assignment'), 'tpa_promo_primary_image_left', 'tpa_promo_primary[image_left_id]', (int) ($promo_primary['image_left_id'] ?? 0), TPA_Defaults::image_url('promo_primary.image_left_id')); ?>
			<?php self::render_image_field(__('Primary Image (Right)', 'toposel-assignment'), 'tpa_promo_primary_image_right', 'tpa_promo_primary[image_right_id]', (int) ($promo_primary['image_right_id'] ?? 0), TPA_Defaults::image_url('promo_primary.image_right_id')); ?>
		</div>

		<h4 style="margin:14px 0 10px"><?php esc_html_e('Secondary Promo (yellow)', 'toposel-assignment'); ?></h4>
		<div class="tpa-grid">
			<div class="tpa-field">
				<label><?php esc_html_e('Title', 'toposel-assignment'); ?></label>
				<input name="tpa_promo_secondary[title]" type="text" value="<?php echo esc_attr((string) ($promo_secondary['title'] ?? "Winter's weekend")); ?>" />
			</div>
			<div class="tpa-field">
				<label><?php esc_html_e('Subtitle', 'toposel-assignment'); ?></label>
				<input name="tpa_promo_secondary[subtitle]" type="text" value="<?php echo esc_attr((string) ($promo_secondary['subtitle'] ?? 'keep it casual')); ?>" />
			</div>
			<?php self::render_url_field(__('Card Link (arrow button)', 'toposel-assignment'), 'tpa_promo_secondary[url]', (string) ($promo_secondary['url'] ?? '')); ?>
			<?php self::render_image_field(__('Image', 'toposel-assignment'), 'tpa_promo_secondary_image', 'tpa_promo_secondary[image_id]', (int) ($promo_secondary['image_id'] ?? 0), TPA_Defaults::image_url('promo_secondary.image_id')); ?>
		</div>

		<h4 style="margin:14px 0 10px"><?php esc_html_e('Product Card 1', 'toposel-assignment'); ?></h4>
		<div class="tpa-grid">
			<div class="tpa-field">
				<label><?php esc_html_e('Badge', 'toposel-assignment'); ?></label>
				<input name="tpa_product_1[badge]" type="text" value="<?php echo esc_attr((string) ($product_1['badge'] ?? 'Our Picks')); ?>" />
			</div>
			<div class="tpa-field">
				<label><?php esc_html_e('Title', 'toposel-assignment'); ?></label>
				<input name="tpa_product_1[title]" type="text" value="<?php echo esc_attr((string) ($product_1['title'] ?? 'WMX Rubber Zebra sandal')); ?>" />
			</div>
			<div class="tpa-field">
				<label><?php esc_html_e('Price', 'toposel-assignment'); ?></label>
				<input name="tpa_product_1[price]" type="text" value="<?php echo esc_attr((string) ($product_1['price'] ?? '$36')); ?>" />
			</div>
			<div class="tpa-field">
				<label><?php esc_html_e('Color dots (comma-separated)', 'toposel-assignment'); ?></label>
				<input name="tpa_product_1[dots]" type="text" value="<?php echo esc_attr((string) ($product_1['dots'] ?? '#ffd54f,#222222')); ?>" />
			</div>
			<?php self::render_url_field(__('Product Link (price button)', 'toposel-assignment'), 'tpa_product_1[url]', (string) ($product_1['url'] ?? '')); ?>
			<?php self::render_image_field(__('Image', 'toposel-assignment'), 'tpa_product_1_image', 'tpa_product_1[image_id]', (int) ($product_1['image_id'] ?? 0), TPA_Defaults::image_url('product_1.image_id')); ?>
		</div>

		<h4 style="margin:14px 0 10px"><?php esc_html_e('Product Card 2', 'toposel-assignment'); ?></h4>
		<div class="tpa-grid">
			<div class="tpa-field">
				<label><?php esc_html_e('Badge', 'toposel-assignment'); ?></label>
				<input name="tpa_product_2[badge]" type="text" value="<?php echo esc_attr((string) ($product_2['badge'] ?? 'Your Choice')); ?>" />
			</div>
			<div class="tpa-field">
				<label><?php esc_html_e('Title', 'toposel-assignment'); ?></label>
				<input name="tpa_product_2[title]" type="text" value="<?php echo esc_attr((string) ($product_2['title'] ?? 'Super Skiny jogger in brown')); ?>" />
			</div>
			<div class="tpa-field">
				<label><?php esc_html_e('Price', 'toposel-assignment'); ?></label>
				<input name="tpa_product_2[price]" type="text" value="<?php echo esc_attr((string) ($product_2['price'] ?? '$89')); ?>" />
			</div>
			<div class="tpa-field">
				<label><?php esc_html_e('Color dots (comma-separated)', 'toposel-assignment'); ?></label>
				<input name="tpa_product_2[dots]" type="text" value="<?php echo esc_attr((string) ($product_2['dots'] ?? '#ffd54f,#222222')); ?>" />
			</div>
			<?php self::render_url_field(__('Product Link (price button)', 'toposel-assignment'), 'tpa_product_2[url]', (string) ($product_2['url'] ?? '')); ?>
			<?php self::render_image_field(__('Image', 'toposel-assignment'), 'tpa_product_2_image', 'tpa_product_2[image_id]', (int) ($product_2['image_id'] ?? 0), TPA_Defaults::image_url('product_2.image_id')); ?>
		</div>

		<h4 style="margin:14px 0 10px"><?php esc_html_e('Offer Card', 'toposel-assignment'); ?></h4>
		<div class="tpa-grid">
			<div class="tpa-field">
				<label><?php esc_html_e('Pill Label', 'toposel-assignment'); ?></label>
				<input name="tpa_offer_card[pill]" type="text" value="<?php echo esc_attr((string) ($offer_card['pill'] ?? 'Avail Offers')); ?>" />
			</div>
			<?php self::render_url_field(__('Offer Link (pill button)', 'toposel-assignment'), 'tpa_offer_card[url]', (string) ($offer_card['url'] ?? '')); ?>
			<?php self::render_image_field(__('Image', 'toposel-assignment'), 'tpa_offer_card_image', 'tpa_offer_card[image_id]', (int) ($offer_card['image_id'] ?? 0), TPA_Defaults::image_url('offer_card.image_id')); ?>
		</div>

		<h4 style="margin:14px 0 10px"><?php esc_html_e('Favourites Card', 'toposel-assignment'); ?></h4>
		<div class="tpa-grid">
			<div class="tpa-field">
				<label><?php esc_html_e('Title', 'toposel-assignment'); ?></label>
				<input name="tpa_favourites_card[title]" type="text" value="<?php echo esc_attr((string) ($favourites_card['title'] ?? 'Favourites')); ?>" />
			</div>
			<div class="tpa-field">
				<label><?php esc_html_e('CTA Label', 'toposel-assignment'); ?></label>
				<input name="tpa_favourites_card[cta_label]" type="text" value="<?php echo esc_attr((string) ($favourites_card['cta_label'] ?? 'See All')); ?>" />
			</div>
			<?php self::render_url_field(__('CTA Link', 'toposel-assignment'), 'tpa_favourites_card[url]', (string) ($favourites_card['url'] ?? '')); ?>
			<div></div>
			<?php self::render_image_field(__('Image A', 'toposel-assignment'), 'tpa_favourites_card_image_a', 'tpa_favourites_card[image_a_id]', (int) ($favourites_card['image_a_id'] ?? 0), TPA_Defaults::image_url('favourites_card.image_a_id')); ?>
			<?php self::render_image_field(__('Image B', 'toposel-assignment'), 'tpa_favourites_card_image_b', 'tpa_favourites_card[image_b_id]', (int) ($favourites_card['image_b_id'] ?? 0), TPA_Defaults::image_url('favourites_card.image_b_id')); ?>
		</div>

		<h4 style="margin:14px 0 10px"><?php esc_html_e('Bottom Banner', 'toposel-assignment'); ?></h4>
		<div class="tpa-grid">
			<div class="tpa-field">
				<label><?php esc_html_e('Title', 'toposel-assignment'); ?></label>
				<input name="tpa_bottom_banner[title]" type="text" value="<?php echo esc_attr((string) ($bottom_banner['title'] ?? 'Bring Bold Fashion')); ?>" />
			</div>
			<div class="tpa-field">
				<label><?php esc_html_e('Subtitle', 'toposel-assignment'); ?></label>
				<input name="tpa_bottom_banner[subtitle]" type="text" value="<?php echo esc_attr((string) ($bottom_banner['subtitle'] ?? 'Layers on Layers')); ?>" />
			</div>
			<?php self::render_url_field(__('Banner Link (arrow button)', 'toposel-assignment'), 'tpa_bottom_banner[url]', (string) ($bottom_banner['url'] ?? '')); ?>
			<?php self::render_image_field(__('Image', 'toposel-assignment'), 'tpa_bottom_banner_image', 'tpa_bottom_banner[image_id]', (int) ($bottom_banner['image_id'] ?? 0), TPA_Defaults::image_url('bottom_banner.image_id')); ?>
		</div>

		<template id="tpa_tpl_nav">
			<?php self::render_nav_row('__i__', ['label' => '', 'url' => '', 'active' => 0]); ?>
		</template>
		<template id="tpa_tpl_actions">
			<?php self::render_action_row('__i__', ['label' => '', 'url' => '']); ?>
		</template>
		<template id="tpa_tpl_orders">
			<?php self::render_last_order_row('__i__', ['title' => '', 'subtitle' => '', 'url' => '']); ?>
		</template>
		<?php
	}

	/**
	 * @param int|string $idx
	 * @param array<string,mixed> $row
	 */
	private static function render_nav_row($idx, array $row): void {
		$label = isset($row['label']) ? (string) $row['label'] : '';
		$url = isset($row['url']) ? (string) $row['url'] : '';
		$active = !empty($row['active']);
		?>
		<div class="tpa-row" data-row="nav" style="grid-template-columns:1fr 1fr auto auto">
			<div class="tpa-field">
				<label><?php esc_html_e('Label', 'toposel-assignment'); ?></label>
				<input type="text" name="tpa_nav[<?php echo esc_attr((string) $idx); ?>][label]" value="<?php echo esc_attr($label); ?>" />
			</div>
			<div class="tpa-field">
				<label><?php esc_html_e('Link', 'toposel-assignment'); ?></label>
				<input type="url" name="tpa_nav[<?php echo esc_attr((string) $idx); ?>][url]" value="<?php echo esc_attr($url); ?>" placeholder="https://" />
			</div>
			<div class="tpa-field">
				<label><?php esc_html_e('Active', 'toposel-assignment'); ?></label>
				<label style="display:flex;gap:8px;align-items:center;margin-top:6px;font-weight:400">
					<input type="checkbox" name="tpa_nav[<?php echo esc_attr((string) $idx); ?>][active]" value="1" <?php checked($active); ?> />
					<span><?php esc_html_e('Selected', 'toposel-assignment'); ?></span>
				</label>
			</div>
			<div class="tpa-remove">
				<button type="button" class="button-link-delete tpa-remove-row"><?php esc_html_e('Remove', 'toposel-assignment'); ?></button>
			</div>
		</div>
		<?php
	}

	private static function render_action_row($idx, array $row): void {
		$label = isset($row['label']) ? (string) $row['label'] : '';
		$url = isset($row['url']) ? (string) $row['url'] : '';
		?>
		<div class="tpa-row" data-row="actions">
			<div class="tpa-field">
				<label><?php esc_html_e('Label', 'toposel-assignment'); ?></label>
				<input type="text" name="tpa_actions[<?php echo esc_attr((string) $idx); ?>][label]" value="<?php echo esc_attr($label); ?>" />
			</div>
			<div class="tpa-field">
				<label><?php esc_html_e('Link', 'toposel-assignment'); ?></label>
				<input type="url" name="tpa_actions[<?php echo esc_attr((string) $idx); ?>][url]" value="<?php echo esc_attr($url); ?>" placeholder="https://" />
			</div>
			<div class="tpa-remove">
				<button type="button" class="button-link-delete tpa-remove-row"><?php esc_html_e('Remove', 'toposel-assignment'); ?></button>
			</div>
		</div>
		<?php
	}

	private static function render_last_order_row($idx, array $row): void {
		$title = isset($row['title']) ? (string) $row['title'] : '';
		$subtitle = isset($row['subtitle']) ? (string) $row['subtitle'] : '';
		$url = isset($row['url']) ? (string) $row['url'] : '';
		?>
		<div class="tpa-row" data-row="orders" style="grid-template-columns:1fr 1fr 1fr auto">
			<div class="tpa-field">
				<label><?php esc_html_e('Title', 'toposel-assignment'); ?></label>
				<input type="text" name="tpa_last_orders[<?php echo esc_attr((string) $idx); ?>][title]" value="<?php echo esc_attr($title); ?>" />
			</div>
			<div class="tpa-field">
				<label><?php esc_html_e('Link Text', 'toposel-assignment'); ?></label>
				<input type="text" name="tpa_last_orders[<?php echo esc_attr((string) $idx); ?>][subtitle]" value="<?php echo esc_attr($subtitle); ?>" />
			</div>
			<div class="tpa-field">
				<label><?php esc_html_e('Link', 'toposel-assignment'); ?></label>
				<input type="url" name="tpa_last_orders[<?php echo esc_attr((string) $idx); ?>][url]" value="<?php echo esc_attr($url); ?>" placeholder="https://" />
			</div>
			<div class="tpa-remove">
				<button type="button" class="button-link-delete tpa-remove-row"><?php esc_html_e('Remove', 'toposel-assignment'); ?></button>
			</div>
		</div>
		<?php
	}

	private static function render_url_field(string $label, string $name, string $value): void {
		?>
		<div class="tpa-field">
			<label><?php echo esc_html($label); ?></label>
			<input type="url" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($value); ?>" placeholder="https://" />
		</div>
		<?php
	}

	private static function render_image_field(string $label, string $id_base, string $name, int $value, string $default_url = ''): void {
		$src = $value ? (string) wp_get_attachment_image_url($value, 'medium') : '';
		$input_id = $id_base . '_id';
		$preview_id = $id_base . '_preview';
		?>
		<div class="tpa-field">
			<label><?php echo esc_html($label); ?></label>
			<input type="hidden" id="<?php echo esc_attr($input_id); ?>" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr((string) $value); ?>" />
			<div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
				<button type="button" class="button tpa-image-pick" data-target="<?php echo esc_attr($input_id); ?>" data-preview="<?php echo esc_attr($preview_id); ?>">
					<?php esc_html_e('Choose Image', 'toposel-assignment'); ?>
				</button>
				<button type="button" class="button tpa-image-clear" data-target="<?php echo esc_attr($input_id); ?>" data-preview="<?php echo esc_attr($preview_id); ?>">
					<?php echo $default_url !== '' ? esc_html__('Use Default', 'toposel-assignment') : esc_html__('Clear', 'toposel-assignment'); ?>
				</button>
			</div>
			<div class="tpa-image-preview" id="<?php echo esc_attr($preview_id); ?>" data-default="<?php echo esc_url($default_url); ?>" style="margin-top:10px">
				<?php if ($src !== '') : ?>
					<img src="<?php echo esc_url($src); ?>" alt="" />
				<?php elseif ($default_url !== '') : ?>
					<img src="<?php echo esc_url($default_url); ?>" alt="" />
					<p class="description"><?php esc_html_e('Default image', 'toposel-assignment'); ?></p>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	public static function save(int $post_id, \WP_Post $post): void {
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}

		if (!isset($_POST[self::NONCE_NAME]) || !wp_verify_nonce((string) $_POST[self::NONCE_NAME], self::NONCE_ACTION)) {
			return;
		}

		if (!current_user_can('edit_post', $post_id)) {
			return;
		}

		$input = wp_unslash($_POST);

		update_post_meta($post_id, self::META_BRAND_NAME, sanitize_text_field((string) ($input['tpa_brand_name'] ?? '')));
		update_post_meta($post_id, self::META_USER_NAME, sanitize_text_field((string) ($input['tpa_user_name'] ?? '')));
		update_post_meta($post_id, self::META_ORDERS_COUNT, (int) ($input['tpa_orders_count'] ?? 0));
		update_post_meta($post_id, self::META_ORDERS_PERIOD, sanitize_text_field((string) ($input['tpa_orders_period'] ?? '')));

		$top_tabs_in = $input['tpa_top_tabs'] ?? [];
		$top_tabs_out = [
			'left' => sanitize_text_field((string) (($top_tabs_in['left'] ?? '') ?: '')),
			'right' => sanitize_text_field((string) (($top_tabs_in['right'] ?? '') ?: '')),
		];
		update_post_meta($post_id, self::META_TOP_TABS, $top_tabs_out);

		// Repeaters.
		$nav_in = $input['tpa_nav'] ?? [];
		$nav_out = [];
		if (is_array($nav_in)) {
			foreach ($nav_in as $row) {
				if (!is_array($row)) {
					continue;
				}
				$label = sanitize_text_field((string) ($row['label'] ?? ''));
				if ($label === '') {
					continue;
				}
				$nav_out[] = [
					'label' => $label,
					'url' => self::sanitize_url_value($row['url'] ?? ''),
					'active' => !empty($row['active']) ? 1 : 0,
				];
			}
		}
		update_post_meta($post_id, self::META_NAV_ITEMS, $nav_out);

		$actions_in = $input['tpa_actions'] ?? [];
		$actions_out = [];
		if (is_array($actions_in)) {
			foreach ($actions_in as $row) {
				if (!is_array($row)) {
					continue;
				}
				$label = sanitize_text_field((string) ($row['label'] ?? ''));
				if ($label === '') {
					continue;
				}
				$actions_out[] = ['label' => $label, 'url' => self::sanitize_url_value($row['url'] ?? '')];
			}
		}
		update_post_meta($post_id, self::META_QUICK_ACTIONS, $actions_out);

		$last_orders_in = $input['tpa_last_orders'] ?? [];
		$last_orders_out = [];
		if (is_array($last_orders_in)) {
			foreach ($last_orders_in as $row) {
				if (!is_array($row)) {
					continue;
				}
				$title = sanitize_text_field((string) ($row['title'] ?? ''));
				$subtitle = sanitize_text_field((string) ($row['subtitle'] ?? ''));
				if ($title === '' && $subtitle === '') {
					continue;
				}
				$last_orders_out[] = [
					'title' => $title,
					'subtitle' => $subtitle,
					'url' => self::sanitize_url_value($row['url'] ?? ''),
				];
			}
		}
		update_post_meta($post_id, self::META_LAST_ORDERS, $last_orders_out);

		// Main tabs.
		$main_tabs_in = $input['tpa_main_tabs'] ?? [];
		$main_tabs_out = [
			'a' => sanitize_text_field((string) ($main_tabs_in['a'] ?? '')),
			'b' => sanitize_text_field((string) ($main_tabs_in['b'] ?? '')),
			'c' => sanitize_text_field((string) ($main_tabs_in['c'] ?? '')),
		];
		update_post_meta($post_id, self::META_MAIN_TABS, $main_tabs_out);

		// Cards.
		$promo_primary_in = $input['tpa_promo_primary'] ?? [];
		$promo_primary_out = [
			'title' => sanitize_text_field((string) ($promo_primary_in['title'] ?? '')),
			'cta_label' => sanitize_text_field((string) ($promo_primary_in['cta_label'] ?? '')),
			'url' => self::sanitize_url_value($promo_primary_in['url'] ?? ''),
			'image_left_id' => (int) ($promo_primary_in['image_left_id'] ?? 0),
			'image_right_id' => (int) ($promo_primary_in['image_right_id'] ?? 0),
		];
		update_post_meta($post_id, self::META_PROMO_PRIMARY, $promo_primary_out);

		$promo_secondary_in = $input['tpa_promo_secondary'] ?? [];
		$promo_secondary_out = [
			'title' => sanitize_text_field((string) ($promo_secondary_in['title'] ?? '')),
			'subtitle' => sanitize_text_field((string) ($promo_secondary_in['subtitle'] ?? '')),
			'url' => self::sanitize_url_value($promo_secondary_in['url'] ?? ''),
			'image_id' => (int) ($promo_secondary_in['image_id'] ?? 0),
		];
		update_post_meta($post_id, self::META_PROMO_SECONDARY, $promo_secondary_out);

		$product_1_in = $input['tpa_product_1'] ?? [];
		$product_1_out = [
			'badge' => sanitize_text_field((string) ($product_1_in['badge'] ?? '')),
			'title' => sanitize_text_field((string) ($product_1_in['title'] ?? '')),
			'price' => sanitize_text_field((string) ($product_1_in['price'] ?? '')),
			'url' => self::sanitize_url_value($product_1_in['url'] ?? ''),
			'image_id' => (int) ($product_1_in['image_id'] ?? 0),
			'dots' => sanitize_text_field((string) ($product_1_in['dots'] ?? '')),
		];
		update_post_meta($post_id, self::META_PRODUCT_1, $product_1_out);

		$product_2_in = $input['tpa_product_2'] ?? [];
		$product_2_out = [
			'badge' => sanitize_text_field((string) ($product_2_in['badge'] ?? '')),
			'title' => sanitize_text_field((string) ($product_2_in['title'] ?? '')),
			'price' => sanitize_text_field((string) ($product_2_in['price'] ?? '')),
			'url' => self::sanitize_url_value($product_2_in['url'] ?? ''),
			'image_id' => (int) ($product_2_in['image_id'] ?? 0),
			'dots' => sanitize_text_field((string) ($product_2_in['dots'] ?? '')),
		];
		update_post_meta($post_id, self::META_PRODUCT_2, $product_2_out);

		$offer_in = $input['tpa_offer_card'] ?? [];
		$offer_out = [
			'pill' => sanitize_text_field((string) ($offer_in['pill'] ?? '')),
			'url' => self::sanitize_url_value($offer_in['url'] ?? ''),
			'image_id' => (int) ($offer_in['image_id'] ?? 0),
		];
		update_post_meta($post_id, self::META_OFFER_CARD, $offer_out);

		$fav_in = $input['tpa_favourites_card'] ?? [];
		$fav_out = [
			'title' => sanitize_text_field((string) ($fav_in['title'] ?? '')),
			'cta_label' => sanitize_text_field((string) ($fav_in['cta_label'] ?? '')),
			'url' => self::sanitize_url_value($fav_in['url'] ?? ''),
			'image_a_id' => (int) ($fav_in['image_a_id'] ?? 0),
			'image_b_id' => (int) ($fav_in['image_b_id'] ?? 0),
		];
		update_post_meta($post_id, self::META_FAVOURITES_CARD, $fav_out);

		$bottom_in = $input['tpa_bottom_banner'] ?? [];
		$bottom_out = [
			'title' => sanitize_text_field((string) ($bottom_in['title'] ?? '')),
			'subtitle' => sanitize_text_field((string) ($bottom_in['subtitle'] ?? '')),
			'url' => self::sanitize_url_value($bottom_in['url'] ?? ''),
			'image_id' => (int) ($bottom_in['image_id'] ?? 0),
		];
		update_post_meta($post_id, self::META_BOTTOM_BANNER, $bottom_out);
	}

	/**
	 * @param mixed $value
	 */
	private static function sanitize_url_value($value): string {
		return esc_url_raw(trim((string) $value));
	}

	public static function add_columns(array $cols): array {
		$new = [];
		foreach ($cols as $key => $label) {
			$new[$key] = $label;
			if ($key === 'title') {
				$new['tpa_shortcode'] = __('Shortcode', 'toposel-assignment');
				$new['tpa_app_url'] = __('App URL', 'toposel-assignment');
			}
		}
		return $new;
	}

	public static function render_columns(string $col, int $post_id): void {
		if ($col !== 'tpa_shortcode') {
			if ($col === 'tpa_app_url') {
				$url = home_url('/tp-landing/' . (int) $post_id . '/');
				echo '<a href="' . esc_url($url) . '" target="_blank" rel="noopener noreferrer">' . esc_html__('Open', 'toposel-assignment') . '</a>';
			}
			return;
		}
		echo '<code>[tp_landing id="' . esc_html((string) $post_id) . '"]</code>';
	}
}

