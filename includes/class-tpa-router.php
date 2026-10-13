<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Renders landing pages as a full-screen interface, without the active theme's
 * header, footer or page title.
 *
 * Applies to:
 * - /tp-landing/{id}
 * - any page/post whose content contains [tp_landing id="..."]
 */
final class TPA_Router {
	private const QUERY_VAR = 'tpa_landing_id';

	/** @var int */
	private static $landing_id = 0;

	public static function init(): void {
		add_action('init', [self::class, 'add_rewrite']);
		add_filter('query_vars', [self::class, 'add_query_vars']);
		// Must run before `template_redirect` (priority 0), where WP decides whether to show the admin bar.
		add_action('wp', [self::class, 'detect']);
		add_filter('template_include', [self::class, 'template_include'], 99);
	}

	public static function add_rewrite(): void {
		add_rewrite_rule(
			'^tp-landing/([0-9]+)/?$',
			'index.php?' . self::QUERY_VAR . '=$matches[1]',
			'top'
		);

		if (get_option('tpa_rewrite_flushed_v1') !== '1') {
			flush_rewrite_rules(false);
			update_option('tpa_rewrite_flushed_v1', '1', true);
		}
	}

	/**
	 * @param array<int,string> $vars
	 * @return array<int,string>
	 */
	public static function add_query_vars(array $vars): array {
		$vars[] = self::QUERY_VAR;
		return $vars;
	}

	public static function current_landing_id(): int {
		return self::$landing_id;
	}

	public static function detect(): void {
		if (is_admin()) {
			return;
		}

		$requested = (int) get_query_var(self::QUERY_VAR);
		if ($requested > 0) {
			if (!self::is_landing($requested)) {
				global $wp_query;
				$wp_query->set_404();
				status_header(404);
				return;
			}
			self::$landing_id = $requested;
		} elseif (is_singular()) {
			$post = get_queried_object();
			if ($post instanceof \WP_Post) {
				self::$landing_id = self::find_landing_id_in_content((string) $post->post_content);
			}
		}

		if (self::$landing_id > 0) {
			add_filter('show_admin_bar', '__return_false', 1000);
		}
	}

	public static function template_include(string $template): string {
		if (self::$landing_id <= 0) {
			return $template;
		}
		$shell = TPA_PLUGIN_DIR . 'templates/app-shell.php';
		return file_exists($shell) ? $shell : $template;
	}

	private static function find_landing_id_in_content(string $content): int {
		if (!has_shortcode($content, TPA_Shortcode::SHORTCODE)) {
			return 0;
		}
		if (!preg_match_all('/' . get_shortcode_regex([TPA_Shortcode::SHORTCODE]) . '/', $content, $matches, PREG_SET_ORDER)) {
			return 0;
		}
		foreach ($matches as $match) {
			$atts = shortcode_parse_atts($match[3]);
			$id = is_array($atts) ? (int) ($atts['id'] ?? 0) : 0;
			if (self::is_landing($id)) {
				return $id;
			}
		}
		return 0;
	}

	private static function is_landing(int $post_id): bool {
		if ($post_id <= 0) {
			return false;
		}
		$post = get_post($post_id);
		return $post instanceof \WP_Post && $post->post_type === TPA_CPT::POST_TYPE;
	}
}
