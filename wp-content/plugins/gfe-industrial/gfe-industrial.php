<?php
/**
 * Plugin Name: GFE Industrial Product Template
 * Description: Dedicated WooCommerce product layout for the 3M VFlex 9105 (product 1825).
 * Version: 1.0.0
 */
defined('ABSPATH') || exit;

function gfei_is_target() {
    return function_exists('is_product') && is_product() && get_queried_object_id() === 1825;
}

add_filter('template_include', function ($template) {
    return gfei_is_target() ? __DIR__ . '/product.php' : $template;
}, 999);

add_action('wp_enqueue_scripts', function () {
    if (gfei_is_target()) {
        wp_enqueue_style('gfe-industrial', plugins_url('product.css', __FILE__), array(), '1.0.0');
    }
});
