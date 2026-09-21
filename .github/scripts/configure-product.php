<?php
// Run with wp eval-file only after the deployment script has verified the site.
if (!defined('WP_CLI') || !WP_CLI) { exit(1); }
if (home_url() !== 'https://goodfruitph.com' || get_woocommerce_currency() !== 'PHP') {
    WP_CLI::error('Unexpected site or currency. No product changes made.');
}
$product = wc_get_product(1825);
if (!$product || !$product->is_type('simple') || stripos($product->get_name(), '9105') === false) {
    WP_CLI::error('Product 1825 does not match the expected simple 9105 product.');
}
$image_id = attachment_url_to_postid('https://goodfruitph.com/wp-content/uploads/2026/09/3M-N95-9105.webp');
if (!$image_id) { WP_CLI::error('The supplied image is not registered in the media library.'); }
$backup_dir = getenv('GFE_BACKUP');
if (!$backup_dir || !is_dir($backup_dir)) { WP_CLI::error('Missing backup directory.'); }
$snapshot = array('id' => 1825, 'regular_price' => $product->get_regular_price(), 'sale_price' => $product->get_sale_price(), 'price' => $product->get_price(), 'image_id' => $product->get_image_id(), 'sale_from' => $product->get_date_on_sale_from() ? $product->get_date_on_sale_from()->getTimestamp() : null, 'sale_to' => $product->get_date_on_sale_to() ? $product->get_date_on_sale_to()->getTimestamp() : null);
if (file_put_contents($backup_dir . '/product-before.json', wp_json_encode($snapshot, JSON_PRETTY_PRINT)) === false) { WP_CLI::error('Could not back up product settings.'); }
$product->set_regular_price('70');
$product->set_sale_price('');
$product->set_date_on_sale_from(null);
$product->set_date_on_sale_to(null);
$product->set_price('70');
$product->set_image_id($image_id);
$product->save();
WP_CLI::success('Product price set to PHP 70 per piece and supplied image selected. Stock settings preserved.');
