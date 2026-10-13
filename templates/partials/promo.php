<?php
/**
 * Promotional banner. The `slot` decides the visual variant.
 *
 * @var array $args { promo: array }
 */

defined( 'ABSPATH' ) || exit;

$promo      = $args['promo'];
$slot       = $promo['slot'];
$is_offer   = 'offer' === $slot;
$has_button = '' !== $promo['button_label'];
$style      = $promo['bg'] ? '--tpa-promo-bg:' . $promo['bg'] : '';
?>
<article class="tpa-promo tpa-promo--<?php echo esc_attr( $slot ); ?>"<?php echo $style ? ' style="' . esc_attr( $style ) . '"' : ''; ?>>
	<?php if ( $promo['secondary_image_id'] ) : ?>
		<div class="tpa-promo__media tpa-promo__media--secondary">
			<?php echo wp_get_attachment_image( $promo['secondary_image_id'], 'large', false, array( 'alt' => '' ) ); ?>
		</div>
	<?php endif; ?>

	<?php if ( $promo['image_id'] ) : ?>
		<div class="tpa-promo__media">
			<?php echo wp_get_attachment_image( $promo['image_id'], 'large', false, array( 'alt' => $is_offer ? $promo['title'] : '' ) ); ?>
		</div>
	<?php endif; ?>

	<div class="tpa-promo__content">
		<h2 class="tpa-promo__title<?php echo $is_offer ? ' tpa-sr-only' : ''; ?>"><?php echo esc_html( $promo['title'] ); ?></h2>
		<?php if ( '' !== $promo['subtitle'] ) : ?>
			<p class="tpa-promo__subtitle"><?php echo esc_html( $promo['subtitle'] ); ?></p>
		<?php endif; ?>
		<?php if ( $has_button ) : ?>
			<a class="tpa-promo__btn" href="<?php echo esc_url( $promo['url'] ); ?>"><?php echo esc_html( $promo['button_label'] ); ?></a>
		<?php endif; ?>
	</div>

	<?php if ( $is_offer ) : ?>
		<button type="button" class="tpa-like" data-tpa-like="promo-<?php echo esc_attr( (string) $promo['id'] ); ?>" aria-pressed="false" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: item name */ __( 'Save %s', 'toposel-assignment' ), $promo['title'] ) ); ?>">
			<?php echo TPA_Icons::svg( 'heart' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</button>
	<?php elseif ( ! $has_button ) : ?>
		<a class="tpa-promo__arrow" href="<?php echo esc_url( $promo['url'] ); ?>" aria-label="<?php echo esc_attr( $promo['title'] ); ?>">
			<?php echo TPA_Icons::svg( 'arrow-up-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</a>
	<?php endif; ?>
</article>
