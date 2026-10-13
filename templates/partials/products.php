<?php
/**
 * Large product cards. All products are rendered; the first two matching the
 * active filter are shown (by default without JavaScript, too).
 *
 * @var array $args
 */

defined( 'ABSPATH' ) || exit;

$limit = 2;
?>
<div class="tpa-products" data-tpa-products data-limit="<?php echo esc_attr( (string) $limit ); ?>">
	<?php
	foreach ( $args['products'] as $index => $product ) {
		TPA_Template::render(
			'partials/product-card',
			array(
				'product' => $product,
				'index'   => $index,
				'hidden'  => $index >= $limit,
			)
		);
	}
	?>
	<p class="tpa-empty" data-tpa-empty<?php echo $args['products'] ? ' hidden' : ''; ?>>
		<?php esc_html_e( 'No products match your selection.', 'toposel-assignment' ); ?>
	</p>
</div>
