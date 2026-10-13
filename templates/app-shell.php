<?php
/**
 * Full-screen document for landing pages (no theme header/footer).
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
	exit;
}

$tpa = TPA_Shortcode::get_data(TPA_Router::current_landing_id());
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class('tpa-app-body'); ?>>
	<?php wp_body_open(); ?>
	<?php include TPA_PLUGIN_DIR . 'templates/landing.php'; ?>
	<?php wp_footer(); ?>
</body>
</html>
