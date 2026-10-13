<?php
/**
 * Product card.
 *
 * @var array $args { product: array, index: int, hidden: bool }
 */

defined( 'ABSPATH' ) || exit;

$product = $args['product'];
?>
<article class="tpa-product" data-tpa-product
	data-cats="<?php echo esc_attr( implode( ' ', $product['categories'] ) ); ?>"
	data-price="<?php echo esc_attr( (string) $product['price'] ); ?>"
	data-title="<?php echo esc_attr( strtolower( $product['title'] ) ); ?>"
	data-order="<?php echo esc_attr( (string) $args['index'] ); ?>"<?php echo $args['hidden'] ? ' hidden' : ''; ?>>
	<div class="tpa-product__media">
		<?php
		if ( $product['image_id'] ) {
			echo wp_get_attachment_image( $product['image_id'], 'large', false, array( 'alt' => $product['title'] ) );
		}
		?>
		<?php if ( $product['swatches'] ) : ?>
			<div class="tpa-swatches">
				<span class="tpa-sr-only"><?php esc_html_e( 'Available colours', 'toposel-assignment' ); ?></span>
				<?php foreach ( $product['swatches'] as $color ) : ?>
					<span class="tpa-swatch" style="<?php echo esc_attr( '--tpa-swatch:' . $color ); ?>"></span>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<button type="button" class="tpa-like" data-tpa-like="product-<?php echo esc_attr( (string) $product['id'] ); ?>" aria-pressed="false" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: product name */ __( 'Save %s', 'toposel-assignment' ), $product['title'] ) ); ?>">
			<?php echo TPA_Icons::svg( 'heart' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</button>
	</div>

	<div class="tpa-product__info">
		<div class="tpa-product__text">
			<?php if ( '' !== $product['eyebrow'] ) : ?>
				<p class="tpa-product__eyebrow"><?php echo esc_html( $product['eyebrow'] ); ?></p>
			<?php endif; ?>
			<h3 class="tpa-product__title"><a href="<?php echo esc_url( $product['url'] ); ?>"><?php echo esc_html( $product['title'] ); ?></a></h3>
		</div>
		<?php if ( '' !== $product['price_label'] ) : ?>
			<a class="tpa-price" href="<?php echo esc_url( $product['url'] ); ?>" tabindex="-1"><?php echo esc_html( $product['price_label'] ); ?></a>
		<?php endif; ?>
	</div>
</article>
