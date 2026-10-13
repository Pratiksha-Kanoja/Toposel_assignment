<?php
/**
 * Plugin Name: Toposel Assignment – Landing Page
 * Description: Implements a responsive landing page as a plugin (no page builders) with dynamic, CMS-manageable content.
 * Version: 0.1.0
 * Author: Toposel Assignment
 * Requires at least: 6.2
 * Requires PHP: 7.4
 * License: GPLv2 or later
 * Text Domain: toposel-assignment
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
	exit;
}

define('TPA_PLUGIN_FILE', __FILE__);
define('TPA_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TPA_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once TPA_PLUGIN_DIR . 'includes/class-tpa-plugin.php';
require_once TPA_PLUGIN_DIR . 'includes/class-tpa-cpt.php';
require_once TPA_PLUGIN_DIR . 'includes/class-tpa-meta.php';
require_once TPA_PLUGIN_DIR . 'includes/class-tpa-defaults.php';
require_once TPA_PLUGIN_DIR . 'includes/class-tpa-shortcode.php';
require_once TPA_PLUGIN_DIR . 'includes/class-tpa-assets.php';
require_once TPA_PLUGIN_DIR . 'includes/class-tpa-router.php';
require_once TPA_PLUGIN_DIR . 'includes/class-tpa-activator.php';

register_activation_hook(__FILE__, ['TPA_Activator', 'activate']);

add_action('plugins_loaded', static function () {
	TPA_Plugin::instance()->init();
});

