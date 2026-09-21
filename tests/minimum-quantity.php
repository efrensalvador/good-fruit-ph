<?php
// Isolated rule checks; live WooCommerce checkout still needs integration testing.
define('ABSPATH', __DIR__);
$hooks = array();
$notices = array();
function add_filter($name, $callback, $priority = 10, $accepted = 1) { global $hooks; $hooks[$name] = $callback; }
function add_action($name, $callback, $priority = 10, $accepted = 1) { add_filter($name, $callback, $priority, $accepted); }
function wc_has_notice($message, $type) { global $notices; return in_array($message, $notices, true); }
function wc_add_notice($message, $type) { global $notices; $notices[] = $message; }
class TestCart { public $items = array(); public function get_cart() { return $this->items; } }
class TestProduct { private $id; public function __construct($id) { $this->id = $id; } public function get_id() { return $this->id; } }
$wc = (object) array('cart' => new TestCart());
function WC() { global $wc; return $wc; }
function check_rule($condition, $label) { if (!$condition) { throw new RuntimeException($label); } }
require __DIR__ . '/../wp-content/plugins/gfe-industrial/gfe-industrial.php';
$add = $hooks['woocommerce_add_to_cart_validation'];
check_rule(!$add(true, 1825, 1), 'One mask must fail');
check_rule(!$add(true, 1825, 2), 'Two masks must fail');
check_rule($add(true, 1825, 3), 'Three masks must pass');
check_rule($add(true, 1825, 4), 'Four masks must pass; not multiples of three');
check_rule($add(true, 99, 1), 'Other products must be unaffected');
check_rule(!$add(false, 1825, 3), 'Preserve existing validation errors');
$wc->cart->items = array('mask' => array('product_id' => 1825, 'quantity' => 3));
check_rule($add(true, 1825, 1), 'Additional masks are allowed');
$update = $hooks['woocommerce_update_cart_validation'];
check_rule(!$update(true, 'mask', $wc->cart->items['mask'], 2), 'Cart must reject reduction to two');
check_rule($update(true, 'mask', $wc->cart->items['mask'], 0), 'Removal must be allowed');
check_rule($update(true, 'mask', $wc->cart->items['mask'], 4), 'Cart must allow four');
$notices = array();
$wc->cart->items['mask']['quantity'] = 2;
$hooks['woocommerce_check_cart_items']();
check_rule(count($notices) === 1, 'Existing invalid cart must be caught at checkout');
check_rule($hooks['woocommerce_store_api_product_quantity_minimum'](1, new TestProduct(1825)) === 3, 'Store API minimum must be three');
check_rule($hooks['woocommerce_store_api_product_quantity_minimum'](1, new TestProduct(99)) === 1, 'Other Store API products unchanged');
echo "Minimum quantity rules passed.\n";
