<?php
/**
 * Plugin Name: GFE Industrial Product Template
 * Description: Dedicated WooCommerce product layout for the 3M VFlex 9105 (product 1825).
 * Version: 1.1.0
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

// Minimum is three individual masks, not multiples of three.
add_filter('woocommerce_quantity_input_args', function ($args, $product) {
    if ($product && $product->get_id() === 1825) {
        $args['min_value'] = 3;
        $args['input_value'] = max(3, (int) $args['input_value']);
        $args['step'] = 1;
    }
    return $args;
}, 10, 2);

add_filter('woocommerce_store_api_product_quantity_minimum', function ($minimum, $product) {
    return $product && $product->get_id() === 1825 ? 3 : $minimum;
}, 10, 2);

function gfei_mask_cart_quantity($exclude_key = null) {
    $quantity = 0;
    if (WC()->cart) {
        foreach (WC()->cart->get_cart() as $key => $item) {
            if ($key !== $exclude_key && (int) $item['product_id'] === 1825) {
                $quantity += (int) $item['quantity'];
            }
        }
    }
    return $quantity;
}

function gfei_minimum_notice() {
    $message = 'Please order at least 3 pieces of the 3M VFlex 9105 respirator.';
    if (!wc_has_notice($message, 'error')) { wc_add_notice($message, 'error'); }
}

add_filter('woocommerce_add_to_cart_validation', function ($passed, $product_id, $quantity) {
    if ((int) $product_id === 1825 && gfei_mask_cart_quantity() + $quantity < 3) {
        gfei_minimum_notice();
        return false;
    }
    return $passed;
}, 10, 3);

add_filter('woocommerce_update_cart_validation', function ($passed, $cart_key, $item, $quantity) {
    $total = gfei_mask_cart_quantity($cart_key) + $quantity;
    if ((int) $item['product_id'] === 1825 && $total > 0 && $total < 3) {
        gfei_minimum_notice();
        return false;
    }
    return $passed;
}, 10, 4);

add_action('woocommerce_check_cart_items', function () {
    $quantity = gfei_mask_cart_quantity();
    if ($quantity > 0 && $quantity < 3) { gfei_minimum_notice(); }
});
