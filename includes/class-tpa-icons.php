<?php
/**
 * Inline SVG icon set (24px grid, stroke-based).
 */

defined( 'ABSPATH' ) || exit;

final class TPA_Icons {

	private const PATHS = array(
		'bolt'          => '<path d="M13 2 4 14h7l-1 8 9-12h-7l1-8z"/>',
		'compass'       => '<circle cx="12" cy="12" r="9"/><path d="m15.5 8.5-2 5-5 2 2-5 5-2z"/>',
		'cart'          => '<circle cx="9" cy="20" r="1.2"/><circle cx="18" cy="20" r="1.2"/><path d="M2.5 3.5h3l2.4 11.2a1.8 1.8 0 0 0 1.8 1.4h8.1a1.8 1.8 0 0 0 1.8-1.4L21 7H6.3"/>',
		'archive'       => '<rect x="3" y="4" width="18" height="5" rx="1.5"/><path d="M5 9v9.5A1.5 1.5 0 0 0 6.5 20h11a1.5 1.5 0 0 0 1.5-1.5V9"/><path d="M10 13h4"/>',
		'bulb'          => '<path d="M9 18h6"/><path d="M10 21h4"/><path d="M12 3a6 6 0 0 0-3.6 10.8c.6.5 1.1 1.3 1.1 2.2h5c0-.9.5-1.7 1.1-2.2A6 6 0 0 0 12 3z"/>',
		'gift'          => '<path d="M20 12v9H4v-9"/><rect x="2" y="7" width="20" height="5" rx="1"/><path d="M12 21V7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/>',
		'star'          => '<path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2L12 17.3l-5.6 2.9 1.1-6.2L3 9.6l6.2-.9L12 3z"/>',
		'tag'           => '<path d="M20.6 13.4 13.4 20.6a2 2 0 0 1-2.8 0L3 13V3h10l7.6 7.6a2 2 0 0 1 0 2.8z"/><circle cx="7.5" cy="7.5" r="1"/>',
		'heart'         => '<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1-1.1a5.5 5.5 0 0 0-7.8 7.8l1 1.1L12 21.2l7.8-7.7 1-1.1a5.5 5.5 0 0 0 0-7.8z"/>',
		'plus'          => '<path d="M12 5v14M5 12h14"/>',
		'logout'        => '<path d="M15 5.3A8 8 0 1 0 15 18.7"/><path d="M11 12h10"/><path d="m18 9 3 3-3 3"/>',
		'bag'           => '<path d="M5 8h14l-1 12H6L5 8z"/><path d="M9 8V6.5a3 3 0 0 1 6 0V8"/>',
		'search'        => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.6-3.6"/>',
		'arrow-up-right' => '<path d="M7 17 17 7"/><path d="M8 7h9v9"/>',
		'chevron-left'  => '<path d="m15 18-6-6 6-6"/>',
		'chevron-right' => '<path d="m9 18 6-6-6-6"/>',
		'copy'          => '<rect x="8" y="8" width="12" height="12" rx="2.5"/><path d="M16 8V6.5A2.5 2.5 0 0 0 13.5 4h-7A2.5 2.5 0 0 0 4 6.5v7A2.5 2.5 0 0 0 6.5 16H8"/>',
		'man'           => '<circle cx="12" cy="4.5" r="2"/><path d="M9.5 21v-6h-1v-5a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v5h-1v6"/>',
		'woman'         => '<circle cx="12" cy="4.5" r="2"/><path d="M10.5 8h3l2.5 8h-2.5v5h-3v-5H8l2.5-8z"/>',
		'menu'          => '<path d="M4 7h16M4 12h16M4 17h16"/>',
		'close'         => '<path d="M18 6 6 18M6 6l12 12"/>',
	);

	/**
	 * Icons an editor may pick for sidebar navigation items.
	 *
	 * @return array<string,string>
	 */
	public static function choices(): array {
		return array(
			'bolt'    => __( 'Lightning', 'toposel-assignment' ),
			'compass' => __( 'Compass', 'toposel-assignment' ),
			'cart'    => __( 'Cart', 'toposel-assignment' ),
			'archive' => __( 'Box', 'toposel-assignment' ),
			'bulb'    => __( 'Light bulb', 'toposel-assignment' ),
			'gift'    => __( 'Gift', 'toposel-assignment' ),
			'star'    => __( 'Star', 'toposel-assignment' ),
			'heart'   => __( 'Heart', 'toposel-assignment' ),
			'tag'     => __( 'Tag', 'toposel-assignment' ),
			'bag'     => __( 'Bag', 'toposel-assignment' ),
		);
	}

	public static function svg( string $name, string $class = '' ): string {
		if ( ! isset( self::PATHS[ $name ] ) ) {
			$name = 'tag';
		}
		return sprintf(
			'<svg class="tpa-icon %1$s" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%2$s</svg>',
			esc_attr( $class ),
			self::PATHS[ $name ]
		);
	}
}
