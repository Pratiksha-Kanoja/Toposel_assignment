<?php
/**
 * Template loader. Themes can override any file by copying it to
 * `{theme}/toposel-assignment/{name}.php`.
 */

defined( 'ABSPATH' ) || exit;

final class TPA_Template {

	public static function locate( string $name ): string {
		$theme_file = locate_template( array( 'toposel-assignment/' . $name . '.php' ) );
		return $theme_file ? $theme_file : TPA_PLUGIN_DIR . 'templates/' . $name . '.php';
	}

	/**
	 * Includes a template; variables are available to it as `$args`.
	 */
	public static function render( string $name, array $args = array() ): void {
		$file = self::locate( $name );
		if ( is_readable( $file ) ) {
			include $file;
		}
	}

	public static function capture( string $name, array $args = array() ): string {
		ob_start();
		self::render( $name, $args );
		return (string) ob_get_clean();
	}
}
