<?php
/**
 * Explore page layout.
 *
 * @var array $args View model from TPA_Content::view_model() plus `uid` and `canvas`.
 */

defined( 'ABSPATH' ) || exit;

$promos  = $args['promos'];
$classes = $args['canvas'] ? 'tpa-explore' : 'tpa-explore alignfull';
?>
<div class="<?php echo esc_attr( $classes ); ?>" id="<?php echo esc_attr( $args['uid'] ); ?>" data-tpa-explore>
	<div class="tpa-app">
		<?php TPA_Template::render( 'partials/sidebar', $args ); ?>
		<div class="tpa-overlay" data-tpa-nav-close hidden></div>

		<div class="tpa-main">
			<?php TPA_Template::render( 'partials/topbar', $args ); ?>
			<?php TPA_Template::render( 'partials/toolbar', $args ); ?>

			<div class="tpa-grid">
				<div class="tpa-col">
					<?php
					foreach ( array( 'hero', 'spotlight' ) as $slot ) {
						if ( ! empty( $promos[ $slot ] ) ) {
							TPA_Template::render( 'partials/promo', array( 'promo' => $promos[ $slot ] ) );
						}
					}
					?>
					<div class="tpa-duo">
						<?php
						if ( ! empty( $promos['offer'] ) ) {
							TPA_Template::render( 'partials/promo', array( 'promo' => $promos['offer'] ) );
						}
						TPA_Template::render( 'partials/favourites', $args );
						?>
					</div>
				</div>

				<div class="tpa-col">
					<?php
					TPA_Template::render( 'partials/products', $args );
					if ( ! empty( $promos['banner'] ) ) {
						TPA_Template::render( 'partials/promo', array( 'promo' => $promos['banner'] ) );
					}
					?>
				</div>
			</div>
		</div>
	</div>
</div>
