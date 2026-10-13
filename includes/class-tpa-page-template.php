<?php
/**
 * Swaps the theme template for a bare full-page canvas on pages that contain
 * the shortcode, when "Full page" layout is selected in settings.
 */

defined( 'ABSPATH' ) || exit;

final class TPA_Page_Template {

	/** @var bool */
	private static $active = false;

	public static function hooks(): void {
		add_filter( 'template_include', array( self::class, 'maybe_use_canvas' ), 99 );
	}

	public static function maybe_use_canvas( string $template ): string {
		if ( 'canvas' !== TPA_Settings::get( 'display_mode' ) || ! TPA_Assets::current_page_has_shortcode() ) {
			return $template;
		}
		self::$active = true;
		return TPA_Template::locate( 'canvas' );
	}

	public static function is_active(): bool {
		return self::$active;
	}
}
