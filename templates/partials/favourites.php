<?php
/**
 * Favourites carousel.
 *
 * @var array $args
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $args['favourites'] ) ) {
	return;
}

$settings   = $args['settings'];
$heading_id = $args['uid'] . '-favourites';
?>
<section class="tpa-favs" data-tpa-favs aria-labelledby="<?php echo esc_attr( $heading_id ); ?>">
	<div class="tpa-favs__head">
		<h2 class="tpa-favs__title" id="<?php echo esc_attr( $heading_id ); ?>"><?php echo esc_html( $settings['favourites_title'] ); ?></h2>
		<div class="tpa-favs__nav">
			<button type="button" data-tpa-favs-prev aria-label="<?php esc_attr_e( 'Previous', 'toposel-assignment' ); ?>">
				<?php echo TPA_Icons::svg( 'chevron-left' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</button>
			<button type="button" data-tpa-favs-next aria-label="<?php esc_attr_e( 'Next', 'toposel-assignment' ); ?>">
				<?php echo TPA_Icons::svg( 'chevron-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</button>
		</div>
	</div>

	<div class="tpa-favs__body">
		<ul class="tpa-favs__track" data-tpa-favs-track>
			<?php foreach ( $args['favourites'] as $index => $product ) : ?>
				<li class="tpa-favs__item" data-tpa-product
					data-cats="<?php echo esc_attr( implode( ' ', $product['categories'] ) ); ?>"
					data-price="<?php echo esc_attr( (string) $product['price'] ); ?>"
					data-title="<?php echo esc_attr( strtolower( $product['title'] ) ); ?>"
					data-order="<?php echo esc_attr( (string) $index ); ?>">
					<a href="<?php echo esc_url( $product['url'] ); ?>" aria-label="<?php echo esc_attr( $product['title'] ); ?>">
						<?php
						if ( $product['image_id'] ) {
							echo wp_get_attachment_image( $product['image_id'], 'medium_large', false, array( 'alt' => '' ) );
						}
						?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
		<p class="tpa-empty tpa-empty--compact" data-tpa-empty hidden><?php esc_html_e( 'No favourites here yet.', 'toposel-assignment' ); ?></p>
		<a class="tpa-favs__all" href="<?php echo esc_url( TPA_Content::link( $settings['favourites_url'] ) ); ?>"><?php echo esc_html( $settings['favourites_cta'] ); ?></a>
	</div>
</section>
