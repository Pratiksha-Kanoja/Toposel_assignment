<?php
/**
 * Sidebar: brand, navigation, quick actions, recent orders and auth link.
 *
 * @var array $args
 */

defined( 'ABSPATH' ) || exit;

$settings = $args['settings'];
list( $brand_main, $brand_accent ) = $args['brand'];
?>
<aside class="tpa-sidebar" id="<?php echo esc_attr( $args['uid'] . '-sidebar' ); ?>" aria-label="<?php esc_attr_e( 'Store navigation', 'toposel-assignment' ); ?>">
	<div class="tpa-sidebar__head">
		<a class="tpa-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php echo esc_html( $brand_main ); ?><?php if ( '' !== $brand_accent ) : ?><span class="tpa-brand__accent"><?php echo esc_html( $brand_accent ); ?></span><?php endif; ?>
		</a>
		<button type="button" class="tpa-icon-btn tpa-sidebar__close" data-tpa-nav-close aria-label="<?php esc_attr_e( 'Close menu', 'toposel-assignment' ); ?>">
			<?php echo TPA_Icons::svg( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</button>
	</div>

	<?php if ( ! empty( $settings['nav_items'] ) ) : ?>
		<nav class="tpa-nav">
			<ul>
				<?php foreach ( $settings['nav_items'] as $item ) : ?>
					<li>
						<a class="tpa-nav__link<?php echo $item['active'] ? ' is-active' : ''; ?>" href="<?php echo esc_url( TPA_Content::link( $item['url'] ) ); ?>"<?php echo $item['active'] ? ' aria-current="page"' : ''; ?>>
							<?php echo TPA_Icons::svg( $item['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<span><?php echo esc_html( $item['label'] ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</nav>
	<?php endif; ?>

	<?php if ( ! empty( $settings['quick_actions'] ) ) : ?>
		<div class="tpa-sidebar__section">
			<p class="tpa-sidebar__label"><?php echo esc_html( $settings['quick_actions_title'] ); ?></p>
			<ul class="tpa-quick">
				<?php foreach ( $settings['quick_actions'] as $action ) : ?>
					<li>
						<a href="<?php echo esc_url( TPA_Content::link( $action['url'] ) ); ?>">
							<?php echo TPA_Icons::svg( 'plus' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<span><?php echo esc_html( $action['label'] ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $settings['recent_orders'] ) ) : ?>
		<div class="tpa-sidebar__section">
			<p class="tpa-sidebar__label">
				<?php echo esc_html( $settings['orders_title'] ); ?>
				<strong><?php echo esc_html( number_format_i18n( (float) $settings['orders_count'] ) ); ?></strong>
			</p>
			<ul class="tpa-orders">
				<?php foreach ( $settings['recent_orders'] as $order ) : ?>
					<li>
						<a href="<?php echo esc_url( TPA_Content::link( $order['url'] ) ); ?>">
							<?php
							if ( $order['image_id'] ) {
								echo wp_get_attachment_image( (int) $order['image_id'], 'thumbnail', false, array( 'class' => 'tpa-orders__thumb', 'alt' => '' ) );
							}
							?>
							<span class="tpa-orders__title"><?php echo esc_html( $order['title'] ); ?></span>
							<span class="tpa-orders__link"><?php esc_html_e( 'view order', 'toposel-assignment' ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
			<a class="tpa-sidebar__more" href="<?php echo esc_url( TPA_Content::link( $settings['recent_orders_url'] ) ); ?>"><?php esc_html_e( 'See all', 'toposel-assignment' ); ?></a>
		</div>
	<?php endif; ?>

	<a class="tpa-logout" href="<?php echo esc_url( $args['auth']['url'] ); ?>">
		<?php echo TPA_Icons::svg( 'logout' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<span><?php echo esc_html( $args['auth']['label'] ); ?></span>
	</a>
</aside>
