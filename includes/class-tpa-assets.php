<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
	exit;
}

final class TPA_Assets {
	private static $frontend_enqueued = false;

	public static function init(): void {
		add_action('admin_enqueue_scripts', [self::class, 'admin_assets']);
		add_action('wp_enqueue_scripts', [self::class, 'maybe_enqueue_frontend']);
	}

	public static function maybe_enqueue_frontend(): void {
		if (TPA_Router::current_landing_id() > 0) {
			self::enqueue_frontend();
		}
	}

	public static function admin_assets(string $hook): void {
		// Only on edit screens for our CPT.
		if (!in_array($hook, ['post.php', 'post-new.php'], true)) {
			return;
		}
		$screen = function_exists('get_current_screen') ? get_current_screen() : null;
		if (!$screen || $screen->post_type !== TPA_CPT::POST_TYPE) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_script(
			'tpa-admin-meta',
			TPA_PLUGIN_URL . 'assets/js/admin-meta.js',
			['jquery'],
			(string) filemtime(TPA_PLUGIN_DIR . 'assets/js/admin-meta.js'),
			true
		);
	}

	public static function enqueue_frontend(): void {
		if (self::$frontend_enqueued) {
			return;
		}
		self::$frontend_enqueued = true;

		wp_enqueue_style(
			'tpa-font',
			'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap',
			[],
			null
		);
		wp_enqueue_style(
			'tpa-frontend',
			TPA_PLUGIN_URL . 'assets/css/frontend.css',
			['tpa-font'],
			(string) filemtime(TPA_PLUGIN_DIR . 'assets/css/frontend.css')
		);
		wp_enqueue_script(
			'tpa-frontend',
			TPA_PLUGIN_URL . 'assets/js/frontend.js',
			[],
			(string) filemtime(TPA_PLUGIN_DIR . 'assets/js/frontend.js'),
			true
		);
	}
}

