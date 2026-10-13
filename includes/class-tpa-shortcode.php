<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
	exit;
}

final class TPA_Shortcode {
	public const SHORTCODE = 'tp_landing';

	public static function init(): void {
		add_shortcode(self::SHORTCODE, [self::class, 'render']);
	}

	/**
	 * @return array<string,mixed>
	 */
	public static function get_data(int $post_id): array {
		$defaults = TPA_Defaults::values();
		$data = ['post_id' => $post_id];

		foreach (TPA_Defaults::fields() as $key => $meta_key) {
			$default = $defaults[$key];
			if (!metadata_exists('post', $post_id, $meta_key)) {
				$data[$key] = $default;
				continue;
			}

			$value = get_post_meta($post_id, $meta_key, true);
			if (is_array($default)) {
				$value = is_array($value) ? $value : [];
				// Lists (repeaters) are used as saved; single cards are filled with missing keys.
				$data[$key] = isset($default[0]) ? $value : wp_parse_args($value, $default);
			} elseif (is_int($default)) {
				$data[$key] = (int) $value;
			} else {
				$data[$key] = (string) $value;
			}
		}

		return $data;
	}

	/**
	 * @param array<string,mixed> $atts
	 */
	public static function render(array $atts = []): string {
		$atts = shortcode_atts(
			[
				'id' => 0,
			],
			$atts,
			self::SHORTCODE
		);

		$post_id = (int) ($atts['id'] ?? 0);
		if ($post_id <= 0) {
			return current_user_can('manage_options')
				? '<p><strong>Toposel:</strong> Missing landing page id. Use <code>[tp_landing id="123"]</code>.</p>'
				: '';
		}

		$post = get_post($post_id);
		if (!$post || $post->post_type !== TPA_CPT::POST_TYPE) {
			return current_user_can('manage_options')
				? '<p><strong>Toposel:</strong> Invalid landing page id.</p>'
				: '';
		}

		$data = self::get_data($post_id);

		TPA_Assets::enqueue_frontend();

		$template = TPA_PLUGIN_DIR . 'templates/landing.php';
		if (!file_exists($template)) {
			return '';
		}

		ob_start();
		/** @var array $tpa */
		$tpa = $data;
		include $template;
		return (string) ob_get_clean();
	}
}

