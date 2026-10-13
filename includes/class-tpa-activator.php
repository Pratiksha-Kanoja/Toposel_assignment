<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
	exit;
}

final class TPA_Activator {
	private const DATA_VERSION = '2';

	/** Meta keys from the first (hero/features/FAQ) version of the plugin. */
	private const LEGACY_META = [
		'_tpa_hero_title',
		'_tpa_hero_subtitle',
		'_tpa_hero_cta_label',
		'_tpa_hero_cta_url',
		'_tpa_hero_image_id',
		'_tpa_features',
		'_tpa_faq',
	];

	public static function activate(): void {
		TPA_CPT::register_post_type();

		$landing_id = (int) get_option('tpa_sample_landing_id', 0);
		$page_id = (int) get_option('tpa_sample_page_id', 0);

		if ($landing_id <= 0 || get_post($landing_id) === null) {
			$landing_id = wp_insert_post(
				[
					'post_type' => TPA_CPT::POST_TYPE,
					'post_status' => 'publish',
					'post_title' => __('Toposel Landing (Sample)', 'toposel-assignment'),
				],
				true
			);
			if (!is_wp_error($landing_id)) {
				update_option('tpa_sample_landing_id', (int) $landing_id);
				self::seed_meta((int) $landing_id);
			} else {
				$landing_id = 0;
			}
		}

		if ($landing_id > 0 && ($page_id <= 0 || get_post($page_id) === null)) {
			$page_id = wp_insert_post(
				[
					'post_type' => 'page',
					'post_status' => 'publish',
					'post_title' => __('Toposel Landing Demo', 'toposel-assignment'),
					'post_content' => '[tp_landing id="' . (int) $landing_id . '"]',
				],
				true
			);
			if (!is_wp_error($page_id)) {
				update_option('tpa_sample_page_id', (int) $page_id);
				set_transient('tpa_activation_notice_page_id', (int) $page_id, MINUTE_IN_SECONDS * 10);
			}
		}

		update_option('tpa_data_version', self::DATA_VERSION);
		flush_rewrite_rules();
	}

	/**
	 * Re-seeds the sample landing page once when the content model changes.
	 */
	public static function maybe_upgrade(): void {
		if (get_option('tpa_data_version') === self::DATA_VERSION) {
			return;
		}

		$landing_id = (int) get_option('tpa_sample_landing_id', 0);
		if ($landing_id > 0 && get_post($landing_id) !== null) {
			foreach (self::LEGACY_META as $meta_key) {
				delete_post_meta($landing_id, $meta_key);
			}
			self::seed_meta($landing_id);
		}

		update_option('tpa_data_version', self::DATA_VERSION);
	}

	private static function seed_meta(int $post_id): void {
		$values = TPA_Defaults::values();
		foreach (TPA_Defaults::fields() as $key => $meta_key) {
			update_post_meta($post_id, $meta_key, $values[$key]);
		}
	}
}
