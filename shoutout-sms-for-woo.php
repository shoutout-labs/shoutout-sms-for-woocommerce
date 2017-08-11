<?php

/**
 * @wordpress-plugin
 * Plugin Name:       ShoutOUT SMS for WooCommerce
 * Plugin URI:
 * Description:       Send SMS update notifications to your customers with ShoutOUT plugin for WooCommerce.
 * Version:           1.0.3
 * Author:            shoutoutlabs
 * Author URI:        http://getshoutout.com/
 * License:           GPLv3
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       shoutout-sms-for-woo
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
if (!defined('SHOUTOUT_SMS_FOR_WOO_LOG_DIR')) {
    $upload_dir = wp_upload_dir();
    define('SHOUTOUT_SMS_FOR_WOO_LOG_DIR', $upload_dir['basedir'] . '/shoutout-sms-for-woo-logs/');
}
if (!defined('SHOUTOUT_PLUGIN_DIR_PATH')) {
    define('SHOUTOUT_PLUGIN_DIR_PATH', untrailingslashit(plugin_dir_path(__FILE__)));
}

if (!defined('SHOUTOUT_PLUGIN_BASE_NAME')) {
    define('SHOUTOUT_PLUGIN_BASE_NAME', plugin_basename(__FILE__));
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-shoutout-sms-for-woo-activator.php
 */
function activate_shoutout_sms_for_woo() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-shoutout-sms-for-woo-activator.php';
    ShoutOUT_Sms_For_Woo_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-shoutout-sms-for-woo-deactivator.php
 */
function deactivate_shoutout_sms_for_woo() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-shoutout-sms-for-woo-deactivator.php';
    ShoutOUT_Sms_For_Woo_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_shoutout_sms_for_woo');
register_deactivation_hook(__FILE__, 'deactivate_shoutout_sms_for_woo');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-shoutout-sms-for-woo.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_shoutout_sms_for_woo() {

    $plugin = new ShoutOUT_Sms_For_Woo();
    $plugin->run();
}

run_shoutout_sms_for_woo();