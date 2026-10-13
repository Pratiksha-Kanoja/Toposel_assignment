<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
	exit;
}

final class TPA_CPT {
	public const POST_TYPE = 'tp_landing';

	public static function init(): void {
		add_action('init', [self::class, 'register_post_type']);
	}

	public static function register_post_type(): void {
		$labels = [
			'name' => __('Landing Pages', 'toposel-assignment'),
			'singular_name' => __('Landing Page', 'toposel-assignment'),
			'add_new' => __('Add New', 'toposel-assignment'),
			'add_new_item' => __('Add New Landing Page', 'toposel-assignment'),
			'edit_item' => __('Edit Landing Page', 'toposel-assignment'),
			'new_item' => __('New Landing Page', 'toposel-assignment'),
			'view_item' => __('View Landing Page', 'toposel-assignment'),
			'search_items' => __('Search Landing Pages', 'toposel-assignment'),
			'not_found' => __('No landing pages found.', 'toposel-assignment'),
			'not_found_in_trash' => __('No landing pages found in Trash.', 'toposel-assignment'),
			'menu_name' => __('Landing Pages', 'toposel-assignment'),
		];

		register_post_type(
			self::POST_TYPE,
			[
				'labels' => $labels,
				'public' => false,
				'show_ui' => true,
				'show_in_menu' => true,
				'menu_icon' => 'dashicons-layout',
				'supports' => ['title'],
				'capability_type' => 'post',
				'hierarchical' => false,
				'show_in_rest' => false,
			]
		);
	}
}

