<?php
/**
 * Landing page interface (BuyMore / Explore).
 *
 * @var array<string,mixed> $tpa Data from TPA_Shortcode::get_data()
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
	exit;
}

$brand_name = (string) $tpa['brand_name'];
$user_name = (string) $tpa['user_name'];
$orders_count = (int) $tpa['orders_count'];
$orders_period = (string) $tpa['orders_period'];
$top_tabs = (array) $tpa['top_tabs'];
$nav_items = (array) $tpa['nav_items'];
$quick_actions = (array) $tpa['quick_actions'];
$last_orders = (array) $tpa['last_orders'];
$main_tabs = array_values(array_filter(array_map('strval', (array) $tpa['main_tabs'])));
$promo_primary = (array) $tpa['promo_primary'];
$promo_secondary = (array) $tpa['promo_secondary'];
$products = [(array) $tpa['product_1'], (array) $tpa['product_2']];
$offer_card = (array) $tpa['offer_card'];
$favourites_card = (array) $tpa['favourites_card'];
$bottom_banner = (array) $tpa['bottom_banner'];

/**
 * Media Library image if one is set, otherwise the bundled default for the slot.
 */
$image = static function (array $card, string $field, string $slot, string $class, string $size = 'large', string $alt = ''): string {
	$id = (int) ($card[$field] ?? 0);
	if ($id > 0) {
		$html = wp_get_attachment_image($id, $size, false, ['class' => $class, 'alt' => $alt, 'loading' => 'lazy']);
		if ($html !== '') {
			return $html;
		}
	}
	$fallback = TPA_Defaults::image_url($slot . '.' . $field);
	if ($fallback !== '') {
		return '<img class="' . esc_attr($class) . '" src="' . esc_url($fallback) . '" alt="' . esc_attr($alt) . '" loading="lazy" decoding="async" />';
	}
	return '<span class="' . esc_attr($class) . ' tpa-ph" aria-hidden="true"></span>';
};

$has_url = static function ($url): bool {
	$url = trim((string) $url);
	return $url !== '' && $url !== '#';
};

$logout_url = wp_logout_url(home_url(wp_unslash((string) ($_SERVER['REQUEST_URI'] ?? '/'))));

$nav_icons = [
	'<path d="M13 2L3 14h7l-1 8 10-12h-7l1-8z"/>',
	'<circle cx="12" cy="12" r="9"/><path d="M16 8l-2 6-6 2 2-6 6-2z"/>',
	'<path d="M8 3l4 3 4-3 4 2-2 4v12H6V9L4 5l4-2z"/>',
	'<path d="M20 12v9H4v-9"/><path d="M2 7h20v5H2z"/><path d="M12 21V7"/><path d="M12 7c-1.5-3-2.5-4-4-4a2 2 0 0 0 0 4h4zm0 0c1.5-3 2.5-4 4-4a2 2 0 0 1 0 4h-4z"/>',
	'<path d="M9 18h6"/><path d="M10 22h4"/><path d="M12 2a7 7 0 0 0-4 12.7c.6.5 1 1.3 1 2.3h6c0-1 .4-1.8 1-2.3A7 7 0 0 0 12 2z"/>',
];

$icon = static function (string $paths, int $size = 16): string {
	return '<svg viewBox="0 0 24 24" width="' . $size . '" height="' . $size . '" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $paths . '</svg>';
};

$arrow = $icon('<path d="M7 17L17 7"/><path d="M8 7h9v9"/>', 14);
$heart = $icon('<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1-1.1a5.5 5.5 0 0 0-7.8 7.8l1 1.1L12 21l7.8-7.5 1-1.1a5.5 5.5 0 0 0 0-7.8z"/>', 14);
?>
<div class="tpa-app" id="tpa-landing-<?php echo (int) $tpa['post_id']; ?>" data-category="all" data-view="dashboard">
	<aside class="tpa-sidebar" id="tpa-sidebar-<?php echo (int) $tpa['post_id']; ?>">
		<div class="tpa-brand"><?php echo esc_html($brand_name); ?></div>

		<nav aria-label="<?php esc_attr_e('Main menu', 'toposel-assignment'); ?>">
			<ul class="tpa-nav">
				<?php foreach ($nav_items as $i => $item) : ?>
					<?php
					$label = trim((string) ($item['label'] ?? ''));
					if ($label === '') {
						continue;
					}
					$is_active = !empty($item['active']);
					?>
					<li>
						<?php $nav_url = (string) ($item['url'] ?? ''); ?>
						<?php if ($has_url($nav_url)) : ?>
							<a class="tpa-nav__link<?php echo $is_active ? ' is-active' : ''; ?>" href="<?php echo esc_url($nav_url); ?>"<?php echo $is_active ? ' aria-current="page"' : ''; ?>>
						<?php else : ?>
							<button type="button" class="tpa-nav__link<?php echo $is_active ? ' is-active' : ''; ?>" data-tpa-nav<?php echo $is_active ? ' aria-current="page"' : ''; ?>>
						<?php endif; ?>
							<?php echo $icon($nav_icons[(int) $i % count($nav_icons)]); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<span><?php echo esc_html($label); ?></span>
						<?php if ($has_url($nav_url)) : ?>
							</a>
						<?php else : ?>
							</button>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</nav>

		<?php if ($quick_actions) : ?>
			<div class="tpa-side-block">
				<div class="tpa-side-block__title"><?php esc_html_e('Quick actions', 'toposel-assignment'); ?></div>
				<ul class="tpa-side-list">
					<?php foreach ($quick_actions as $action) : ?>
						<?php
						$label = trim((string) ($action['label'] ?? ''));
						$action_url = (string) ($action['url'] ?? '');
						$action_kind = stripos($label, 'member') !== false ? 'member' : 'request';
						?>
						<?php if ($label !== '') : ?>
							<li>
								<?php if ($has_url($action_url)) : ?>
									<a class="tpa-action" href="<?php echo esc_url($action_url); ?>">
								<?php else : ?>
									<button type="button" class="tpa-action" data-tpa-quick="<?php echo esc_attr($action_kind); ?>" data-title="<?php echo esc_attr($label); ?>">
								<?php endif; ?>
									<span class="tpa-action__plus" aria-hidden="true">+</span><?php echo esc_html($label); ?>
								<?php if ($has_url($action_url)) : ?>
									</a>
								<?php else : ?>
									</button>
								<?php endif; ?>
							</li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<?php if ($last_orders) : ?>
			<div class="tpa-side-block">
				<div class="tpa-side-block__title">
					<?php esc_html_e('Last orders', 'toposel-assignment'); ?>
					<strong><?php echo esc_html((string) $orders_count); ?></strong>
				</div>
				<ul class="tpa-side-list">
					<?php foreach ($last_orders as $order) : ?>
						<?php
						$title = trim((string) ($order['title'] ?? ''));
						$subtitle = trim((string) ($order['subtitle'] ?? ''));
						if ($title === '' && $subtitle === '') {
							continue;
						}
						?>
						<?php $order_url = (string) ($order['url'] ?? ''); ?>
						<li class="tpa-order">
							<span class="tpa-order__thumb" aria-hidden="true"></span>
							<span class="tpa-order__title"><?php echo esc_html($title); ?></span>
							<?php if ($has_url($order_url)) : ?>
								<a class="tpa-order__link" href="<?php echo esc_url($order_url); ?>"><?php echo esc_html($subtitle); ?></a>
							<?php else : ?>
								<button type="button" class="tpa-order__link" data-tpa-order data-title="<?php echo esc_attr($title); ?>"><?php echo esc_html($subtitle); ?></button>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
				<button type="button" class="tpa-side-block__more" data-tpa-orders><?php esc_html_e('See all', 'toposel-assignment'); ?></button>
			</div>
		<?php endif; ?>

		<a class="tpa-logout" href="<?php echo esc_url($logout_url); ?>">
			<?php echo $icon('<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/>'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<span><?php esc_html_e('Log out', 'toposel-assignment'); ?></span>
		</a>
	</aside>
	<div class="tpa-overlay" data-tpa-close hidden></div>

	<main class="tpa-main">
		<header class="tpa-top">
			<button type="button" class="tpa-menu-btn" data-tpa-menu aria-controls="tpa-sidebar-<?php echo (int) $tpa['post_id']; ?>" aria-expanded="false" aria-label="<?php esc_attr_e('Open menu', 'toposel-assignment'); ?>">
				<?php echo $icon('<path d="M3 6h18"/><path d="M3 12h18"/><path d="M3 18h18"/>', 18); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</button>

			<div class="tpa-stat">
				<span class="tpa-stat__num"><?php echo esc_html((string) $orders_count); ?></span>
				<span class="tpa-stat__meta">
					<span class="tpa-stat__label"><?php esc_html_e('Orders', 'toposel-assignment'); ?></span>
					<span class="tpa-stat__sub"><?php echo esc_html($orders_period); ?></span>
				</span>
			</div>

			<div class="tpa-switch" data-tpa-toggle-group>
				<button type="button" class="tpa-switch__btn is-active" data-tpa-view="dashboard"><?php echo esc_html((string) ($top_tabs['left'] ?? '')); ?></button>
				<button type="button" class="tpa-switch__btn" data-tpa-view="website"><?php echo esc_html((string) ($top_tabs['right'] ?? '')); ?></button>
			</div>

			<div class="tpa-userbar">
				<button type="button" class="tpa-pill" data-tpa-cart-open aria-expanded="false" aria-controls="tpa-cart-<?php echo (int) $tpa['post_id']; ?>">
					<?php echo $icon('<path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/>'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span><?php esc_html_e('Cart', 'toposel-assignment'); ?></span>
					<span class="tpa-cart-count" data-tpa-cart-count hidden>0</span>
				</button>
				<span class="tpa-team" aria-hidden="true">
					<span class="tpa-team__face"></span><span class="tpa-team__face"></span><span class="tpa-team__face"></span>
					<span class="tpa-team__more">+8</span>
				</span>
				<span class="tpa-user">
					<span class="tpa-user__pic" aria-hidden="true"></span>
					<span class="tpa-user__name"><?php echo esc_html($user_name); ?></span>
				</span>
			</div>
		</header>

		<div class="tpa-headrow">
			<h1 class="tpa-title"><?php esc_html_e('Explore', 'toposel-assignment'); ?></h1>

			<?php $tab_keys = ['all', 'men', 'women']; ?>
			<div class="tpa-tabs" data-tpa-toggle-group role="group" aria-label="<?php esc_attr_e('Categories', 'toposel-assignment'); ?>">
				<?php foreach ($main_tabs as $i => $tab) : ?>
					<?php $tab_key = $tab_keys[$i] ?? sanitize_title($tab); ?>
					<button type="button" class="tpa-tabs__btn<?php echo $i === 0 ? ' is-active' : ''; ?>" data-tpa-category="<?php echo esc_attr($tab_key); ?>" aria-pressed="<?php echo $i === 0 ? 'true' : 'false'; ?>"><?php echo esc_html($tab); ?></button>
				<?php endforeach; ?>
			</div>

			<div class="tpa-tools">
				<div class="tpa-filter">
					<button type="button" class="tpa-pill tpa-pill--dot" data-tpa-filters aria-expanded="false" aria-controls="tpa-filters-<?php echo (int) $tpa['post_id']; ?>"><?php esc_html_e('Filters', 'toposel-assignment'); ?></button>
					<div class="tpa-popover" id="tpa-filters-<?php echo (int) $tpa['post_id']; ?>" data-tpa-filter-panel hidden>
						<?php foreach ($main_tabs as $i => $tab) : ?>
							<?php $tab_key = $tab_keys[$i] ?? sanitize_title($tab); ?>
							<button type="button" class="tpa-popover__btn<?php echo $i === 0 ? ' is-active' : ''; ?>" data-tpa-category="<?php echo esc_attr($tab_key); ?>"><?php echo esc_html($tab); ?></button>
						<?php endforeach; ?>
					</div>
				</div>
				<div class="tpa-search">
					<input class="tpa-search__input" data-tpa-search-input type="search" hidden placeholder="<?php esc_attr_e('Search', 'toposel-assignment'); ?>" aria-label="<?php esc_attr_e('Search', 'toposel-assignment'); ?>">
					<button type="button" class="tpa-round-btn" data-tpa-search aria-expanded="false" aria-label="<?php esc_attr_e('Search', 'toposel-assignment'); ?>">
						<?php echo $icon('<circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/>', 18); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</button>
				</div>
			</div>
		</div>

		<div class="tpa-grid">
			<section class="tpa-card tpa-promo">
				<?php echo $image($promo_primary, 'image_left_id', 'promo_primary', 'tpa-promo__img tpa-promo__img--left', 'medium_large'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<div class="tpa-promo__body">
					<h2 class="tpa-promo__title"><?php echo esc_html((string) $promo_primary['title']); ?></h2>
					<?php $promo_url = (string) ($promo_primary['url'] ?? ''); ?>
					<?php if ($has_url($promo_url)) : ?>
						<a class="tpa-soft-btn" href="<?php echo esc_url($promo_url); ?>"><?php echo esc_html((string) $promo_primary['cta_label']); ?></a>
					<?php else : ?>
						<button type="button" class="tpa-soft-btn" data-tpa-discount><?php echo esc_html((string) $promo_primary['cta_label']); ?></button>
					<?php endif; ?>
				</div>
				<?php echo $image($promo_primary, 'image_right_id', 'promo_primary', 'tpa-promo__img tpa-promo__img--right', 'medium_large'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</section>

			<section class="tpa-card tpa-feature">
				<div class="tpa-feature__body">
					<h2 class="tpa-feature__title"><?php echo esc_html((string) $promo_secondary['title']); ?></h2>
					<p class="tpa-feature__sub"><?php echo esc_html((string) $promo_secondary['subtitle']); ?></p>
				</div>
				<?php echo $image($promo_secondary, 'image_id', 'promo_secondary', 'tpa-feature__img', 'medium_large'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php $feature_url = (string) ($promo_secondary['url'] ?? ''); ?>
				<?php if ($has_url($feature_url)) : ?>
					<a class="tpa-corner-btn" href="<?php echo esc_url($feature_url); ?>" aria-label="<?php esc_attr_e('Open', 'toposel-assignment'); ?>"><?php echo $arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
				<?php else : ?>
					<button type="button" class="tpa-corner-btn" data-tpa-detail data-title="<?php echo esc_attr((string) $promo_secondary['title']); ?>" data-text="<?php echo esc_attr((string) $promo_secondary['subtitle']); ?>" aria-label="<?php esc_attr_e('Open', 'toposel-assignment'); ?>"><?php echo $arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
				<?php endif; ?>
			</section>

			<?php
			$product_categories = ['women', 'men'];
			foreach ($products as $n => $product) :
				$dots = array_filter(array_map('sanitize_hex_color', array_map('trim', explode(',', (string) ($product['dots'] ?? '')))));
				$product_url = (string) ($product['url'] ?? '');
				$product_title = (string) $product['title'];
				$product_price = (string) $product['price'];
				$product_category = $product_categories[$n] ?? 'all';
				?>
				<section class="tpa-card tpa-product tpa-product--<?php echo (int) $n + 1; ?>" data-category="<?php echo esc_attr($product_category); ?>">
					<div class="tpa-product__media">
						<?php echo $image($product, 'image_id', 'product_' . ((int) $n + 1), 'tpa-product__img', 'large', $product_title); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php if ($dots) : ?>
							<span class="tpa-dots" aria-hidden="true">
								<?php foreach (array_slice($dots, 0, 3) as $color) : ?>
									<span style="background:<?php echo esc_attr($color); ?>"></span>
								<?php endforeach; ?>
							</span>
						<?php endif; ?>
						<button type="button" class="tpa-heart" data-title="<?php echo esc_attr($product_title); ?>" aria-pressed="false" aria-label="<?php esc_attr_e('Add to favourites', 'toposel-assignment'); ?>"><?php echo $heart; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
					</div>
					<div class="tpa-product__info">
						<div>
							<div class="tpa-product__badge"><?php echo esc_html((string) $product['badge']); ?></div>
							<h3 class="tpa-product__name"><?php echo esc_html($product_title); ?></h3>
						</div>
						<?php if ($has_url($product_url)) : ?>
							<a class="tpa-price" href="<?php echo esc_url($product_url); ?>"><?php echo esc_html($product_price); ?></a>
						<?php else : ?>
							<button type="button" class="tpa-price" data-tpa-add data-title="<?php echo esc_attr($product_title); ?>" data-price="<?php echo esc_attr($product_price); ?>"><?php echo esc_html($product_price); ?></button>
						<?php endif; ?>
					</div>
				</section>
			<?php endforeach; ?>

			<section class="tpa-card tpa-offer">
				<?php echo $image($offer_card, 'image_id', 'offer_card', 'tpa-offer__img', 'medium_large'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<button type="button" class="tpa-heart" data-title="<?php echo esc_attr((string) $offer_card['pill']); ?>" aria-pressed="false" aria-label="<?php esc_attr_e('Add to favourites', 'toposel-assignment'); ?>"><?php echo $heart; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
				<?php $offer_url = (string) ($offer_card['url'] ?? ''); ?>
				<?php if ($has_url($offer_url)) : ?>
					<a class="tpa-offer__pill" href="<?php echo esc_url($offer_url); ?>"><?php echo esc_html((string) $offer_card['pill']); ?></a>
				<?php else : ?>
					<button type="button" class="tpa-offer__pill" data-tpa-offer><?php echo esc_html((string) $offer_card['pill']); ?></button>
				<?php endif; ?>
			</section>

			<section class="tpa-card tpa-favs">
				<div class="tpa-favs__inner">
					<div class="tpa-favs__head">
						<h3 class="tpa-favs__title"><?php echo esc_html((string) $favourites_card['title']); ?></h3>
						<span class="tpa-favs__nav">
							<button type="button" class="tpa-favs__arrow" data-tpa-fav-prev aria-label="<?php esc_attr_e('Previous favourites', 'toposel-assignment'); ?>"><?php echo $icon('<path d="M15 18l-6-6 6-6"/>', 14); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
							<button type="button" class="tpa-favs__arrow" data-tpa-fav-next aria-label="<?php esc_attr_e('Next favourites', 'toposel-assignment'); ?>"><?php echo $icon('<path d="M9 18l6-6-6-6"/>', 14); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
						</span>
					</div>
					<div class="tpa-favs__items">
						<?php echo $image($favourites_card, 'image_a_id', 'favourites_card', 'tpa-favs__img', 'medium'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php echo $image($favourites_card, 'image_b_id', 'favourites_card', 'tpa-favs__img', 'medium'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
					<?php $favs_url = (string) ($favourites_card['url'] ?? ''); ?>
					<?php if ($has_url($favs_url)) : ?>
						<a class="tpa-favs__cta" href="<?php echo esc_url($favs_url); ?>"><?php echo esc_html((string) $favourites_card['cta_label']); ?></a>
					<?php else : ?>
						<button type="button" class="tpa-favs__cta" data-tpa-favs-all><?php echo esc_html((string) $favourites_card['cta_label']); ?></button>
					<?php endif; ?>
				</div>
			</section>

			<section class="tpa-card tpa-banner">
				<div class="tpa-banner__body">
					<h2 class="tpa-banner__title"><?php echo esc_html((string) $bottom_banner['title']); ?></h2>
					<p class="tpa-banner__sub"><?php echo esc_html((string) $bottom_banner['subtitle']); ?></p>
				</div>
				<?php echo $image($bottom_banner, 'image_id', 'bottom_banner', 'tpa-banner__img', 'medium_large'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php $banner_url = (string) ($bottom_banner['url'] ?? ''); ?>
				<?php if ($has_url($banner_url)) : ?>
					<a class="tpa-corner-btn" href="<?php echo esc_url($banner_url); ?>" aria-label="<?php esc_attr_e('Open', 'toposel-assignment'); ?>"><?php echo $arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
				<?php else : ?>
					<button type="button" class="tpa-corner-btn" data-tpa-detail data-title="<?php echo esc_attr((string) $bottom_banner['title']); ?>" data-text="<?php echo esc_attr((string) $bottom_banner['subtitle']); ?>" aria-label="<?php esc_attr_e('Open', 'toposel-assignment'); ?>"><?php echo $arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
				<?php endif; ?>
			</section>

			<p class="tpa-empty" data-tpa-empty hidden><?php esc_html_e('Nothing matches your search.', 'toposel-assignment'); ?></p>
		</div>
	</main>

	<aside class="tpa-cart" id="tpa-cart-<?php echo (int) $tpa['post_id']; ?>" data-tpa-cart hidden aria-label="<?php esc_attr_e('Cart', 'toposel-assignment'); ?>">
		<div class="tpa-cart__head">
			<h2><?php esc_html_e('Cart', 'toposel-assignment'); ?></h2>
			<button type="button" class="tpa-cart__close" data-tpa-cart-close aria-label="<?php esc_attr_e('Close cart', 'toposel-assignment'); ?>">×</button>
		</div>
		<p class="tpa-cart__empty" data-tpa-cart-empty><?php esc_html_e('Your cart is empty.', 'toposel-assignment'); ?></p>
		<ul class="tpa-cart__list" data-tpa-cart-list></ul>
		<div class="tpa-cart__foot" data-tpa-cart-foot hidden>
			<span><?php esc_html_e('Total', 'toposel-assignment'); ?></span>
			<strong data-tpa-cart-total></strong>
		</div>
	</aside>

	<dialog class="tpa-dialog" data-tpa-dialog>
		<form data-tpa-dialog-form action="#" method="post">
			<h2 data-tpa-dialog-title></h2>
			<div data-tpa-dialog-body></div>
			<div class="tpa-dialog__actions">
				<button type="button" class="tpa-dialog__btn" data-tpa-dialog-close><?php esc_html_e('Close', 'toposel-assignment'); ?></button>
				<button type="submit" class="tpa-dialog__btn tpa-dialog__btn--primary" data-tpa-dialog-submit><?php esc_html_e('Done', 'toposel-assignment'); ?></button>
			</div>
		</form>
	</dialog>
	<div class="tpa-toast" data-tpa-toast role="status" hidden></div>
</div>
