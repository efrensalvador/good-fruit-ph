<?php
// Run with wp eval-file only after the deployment script has verified the site.
if (!defined('WP_CLI') || !WP_CLI) { exit(1); }
if (home_url() !== 'https://goodfruitph.com') {
    WP_CLI::error('Unexpected site. No shipping changes made.');
}
if (!class_exists('WC_Shipping_Zones') || !class_exists('WC_Shipping_Zone')) {
    WP_CLI::error('WooCommerce is not active. No shipping changes made.');
}
$backup_dir = getenv('GFE_BACKUP');
if (!$backup_dir || !is_dir($backup_dir)) { WP_CLI::error('Missing backup directory.'); }

// Snapshot every zone's shipping methods, plus the special "Locations not
// covered by your other zones" zone (id 0), before changing anything.
$zone_rows = WC_Shipping_Zones::get_zones();
$zone_ids = array(0);
foreach ($zone_rows as $zone_row) { $zone_ids[] = $zone_row['zone_id']; }

$snapshot = array('zones' => array());
$to_remove = array(); // [zone_id, instance_id, label]
foreach ($zone_ids as $zone_id) {
    $zone = new WC_Shipping_Zone($zone_id);
    $methods = $zone->get_shipping_methods(false, 'admin');
    if (empty($methods)) { continue; }
    $zone_snapshot = array('zone_id' => $zone->get_id(), 'zone_name' => $zone->get_zone_name(), 'methods' => array());
    foreach ($methods as $instance_id => $method) {
        $zone_snapshot['methods'][] = array(
            'instance_id' => $instance_id,
            'method_id' => $method->id,
            'title' => $method->title,
            'enabled' => $method->enabled,
        );
        if ($method->id === 'local_pickup') {
            $to_remove[] = array($zone->get_id(), $instance_id, "zone {$zone->get_id()} (\"{$zone->get_zone_name()}\") instance $instance_id \"{$method->title}\"");
        }
    }
    $snapshot['zones'][] = $zone_snapshot;
}

// WooCommerce's core "Local pickup" cart/checkout feature (8.3+) is a global
// toggle independent of shipping zones.
$pickup_settings = get_option('woocommerce_pickup_location_settings', array());
$snapshot['pickup_location_settings'] = $pickup_settings;
$pickup_was_enabled = !empty($pickup_settings) && ($pickup_settings['enabled'] ?? 'no') === 'yes';

if (file_put_contents($backup_dir . '/shipping-before.json', wp_json_encode($snapshot, JSON_PRETTY_PRINT)) === false) {
    WP_CLI::error('Could not back up shipping settings. No shipping changes made.');
}

foreach ($to_remove as $item) {
    list($zone_id, $instance_id) = $item;
    $zone = new WC_Shipping_Zone($zone_id);
    $zone->delete_shipping_method($instance_id);
    WP_CLI::success('Removed local pickup method from ' . $item[2]);
}

if ($pickup_was_enabled) {
    $pickup_settings['enabled'] = 'no';
    update_option('woocommerce_pickup_location_settings', $pickup_settings);
    WP_CLI::success('Disabled the core Local Pickup (pickup location) checkout feature.');
}

if (empty($to_remove) && !$pickup_was_enabled) {
    WP_CLI::warning('No local pickup shipping method or pickup-location feature was found enabled. Nothing changed.');
}
