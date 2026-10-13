<?php
/**
 * Top bar: order stats, view switch, cart, team and user.
 *
 * @var array $args
 */

defined( 'ABSPATH' ) || exit;

$settings = $args['settings'];
$user     = $args['user'];
?>
<header class="tpa-topbar">
	<button type="button" class="tpa-icon-btn tpa-menu-btn" data-tpa-nav-open aria-controls="<?php echo esc_attr( $args['uid'] . '-sidebar' ); ?>" aria-expanded="false" aria-label="<?php esc_attr_e( 'Open menu', 'toposel-assignment' ); ?>">
		<?php echo TPA_Icons::svg( 'menu' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</button>

	<div class="tpa-stat">
		<span class="tpa-stat__value"><?php echo esc_html( number_format_i18n( (float) $settings['orders_count'] ) ); ?></span>
		<span class="tpa-stat__text">
			<span class="tpa-stat__label"><?php echo esc_html( $settings['orders_label'] ); ?></span>
			<span class="tpa-stat__period"><?php echo esc_html( $settings['orders_period'] ); ?></span>
		</span>
	</div>

	<nav class="tpa-switch" aria-label="<?php esc_attr_e( 'View', 'toposel-assignment' ); ?>">
		<a class="tpa-switch__item is-active" href="<?php echo esc_url( get_permalink() ?: '#' ); ?>" aria-current="page"><?php esc_html_e( 'Dashboard', 'toposel-assignment' ); ?></a>
		<a class="tpa-switch__item" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Website', 'toposel-assignment' ); ?></a>
	</nav>

	<div class="tpa-topbar__actions">
		<a class="tpa-pill-btn tpa-cart" href="<?php echo esc_url( TPA_Content::link( $settings['cart_url'] ) ); ?>">
			<?php echo TPA_Icons::svg( 'bag' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<span class="tpa-cart__label"><?php echo esc_html( $settings['cart_label'] ); ?></span>
		</a>

		<?php if ( $args['team'] ) : ?>
			<div class="tpa-team">
				<?php foreach ( $args['team'] as $index => $avatar_id ) : ?>
					<?php echo wp_get_attachment_image( $avatar_id, 'thumbnail', false, array( 'class' => 'tpa-team__avatar', 'alt' => '' ) ); ?>
					<?php if ( 0 === $index && '' !== $settings['team_extra'] ) : ?>
						<span class="tpa-team__more"><?php echo esc_html( $settings['team_extra'] ); ?></span>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( '' !== $user['name'] || '' !== $user['avatar'] ) : ?>
			<div class="tpa-user">
				<?php echo $user['avatar']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span class="tpa-user__name"><?php echo esc_html( $user['name'] ); ?></span>
			</div>
		<?php endif; ?>
	</div>
</header>
