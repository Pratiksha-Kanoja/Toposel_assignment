<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
	exit;
}

final class TPA_Plugin {
	/** @var self|null */
	private static $instance = null;

	private function __construct() {}

	public static function instance(): self {
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function init(): void {
		// Core.
		TPA_CPT::init();
		TPA_Meta::init();
		TPA_Shortcode::init();
		TPA_Assets::init();
		TPA_Router::init();

		add_action('init', ['TPA_Activator', 'maybe_upgrade']);

		add_action('admin_notices', static function (): void {
			if (!current_user_can('manage_options')) {
				return;
			}
			$page_id = (int) get_transient('tpa_activation_notice_page_id');
			if ($page_id <= 0) {
				return;
			}
			delete_transient('tpa_activation_notice_page_id');
			$url = get_permalink($page_id);
			if (!$url) {
				return;
			}
			echo '<div class="notice notice-success is-dismissible"><p>'
				. esc_html__('Toposel Assignment plugin activated. Demo page created:', 'toposel-assignment')
				. ' <a href="' . esc_url($url) . '" target="_blank" rel="noopener noreferrer">'
				. esc_html__('Open Demo', 'toposel-assignment')
				. '</a></p></div>';
		});

		// Convenience: add "Settings" link to plugins list.
		add_filter(
			'plugin_action_links_' . plugin_basename(TPA_PLUGIN_FILE),
			static function (array $links): array {
				$url = admin_url('edit.php?post_type=tp_landing');
				$links[] = '<a href="' . esc_url($url) . '">' . esc_html__('Landing Pages', 'toposel-assignment') . '</a>';
				return $links;
			}
		);
	}
}

