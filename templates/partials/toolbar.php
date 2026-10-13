<?php
/**
 * Section title, category filters, sort menu and search.
 *
 * @var array $args
 */

defined( 'ABSPATH' ) || exit;

$uid     = $args['uid'];
$heading = $args['canvas'] ? 'h1' : 'h2';
$sorts   = array(
	'featured'   => __( 'Featured', 'toposel-assignment' ),
	'price-asc'  => __( 'Price: low to high', 'toposel-assignment' ),
	'price-desc' => __( 'Price: high to low', 'toposel-assignment' ),
);
?>
<div class="tpa-toolbar">
	<<?php echo $heading; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> class="tpa-toolbar__title"><?php echo esc_html( $args['settings']['explore_title'] ); ?></<?php echo $heading; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>

	<div class="tpa-filters" role="group" aria-label="<?php esc_attr_e( 'Filter products by category', 'toposel-assignment' ); ?>">
		<button type="button" class="tpa-filter is-active" data-tpa-filter="all" aria-pressed="true">
			<?php echo TPA_Icons::svg( 'copy' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<span><?php esc_html_e( 'All', 'toposel-assignment' ); ?></span>
		</button>
		<?php foreach ( $args['categories'] as $category ) : ?>
			<button type="button" class="tpa-filter" data-tpa-filter="<?php echo esc_attr( $category['slug'] ); ?>" aria-pressed="false">
				<?php echo TPA_Icons::svg( $category['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span><?php echo esc_html( $category['name'] ); ?></span>
			</button>
		<?php endforeach; ?>
	</div>

	<div class="tpa-toolbar__actions">
		<div class="tpa-search">
			<label class="tpa-sr-only" for="<?php echo esc_attr( $uid . '-search' ); ?>"><?php esc_html_e( 'Search products', 'toposel-assignment' ); ?></label>
			<input type="search" id="<?php echo esc_attr( $uid . '-search' ); ?>" data-tpa-search placeholder="<?php esc_attr_e( 'Search products', 'toposel-assignment' ); ?>" autocomplete="off">
		</div>

		<div class="tpa-sort">
			<button type="button" class="tpa-pill-btn tpa-sort__btn" data-tpa-sort-toggle aria-expanded="false" aria-controls="<?php echo esc_attr( $uid . '-sort' ); ?>">
				<?php esc_html_e( 'Filters', 'toposel-assignment' ); ?>
				<span class="tpa-dot" aria-hidden="true"></span>
			</button>
			<div class="tpa-dropdown" id="<?php echo esc_attr( $uid . '-sort' ); ?>" data-tpa-sort-menu hidden>
				<p class="tpa-dropdown__label"><?php esc_html_e( 'Sort by', 'toposel-assignment' ); ?></p>
				<?php foreach ( $sorts as $value => $label ) : ?>
					<button type="button" class="tpa-dropdown__item" data-tpa-sort="<?php echo esc_attr( $value ); ?>" aria-pressed="<?php echo 'featured' === $value ? 'true' : 'false'; ?>">
						<?php echo esc_html( $label ); ?>
					</button>
				<?php endforeach; ?>
			</div>
		</div>

		<button type="button" class="tpa-icon-btn tpa-search-btn" data-tpa-search-toggle aria-expanded="false" aria-label="<?php esc_attr_e( 'Search products', 'toposel-assignment' ); ?>">
			<?php echo TPA_Icons::svg( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</button>
	</div>
</div>
