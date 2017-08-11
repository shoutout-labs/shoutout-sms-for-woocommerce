<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @since      1.0.0
 * @package    ShoutOUT_Sms_For_Woo
 * @subpackage ShoutOUT_Sms_For_Woo/admin/partials
 */
class ShoutOUT_SMS_For_Woo_Admin_Display {

    /**
     * Hook in methods
     * @since    1.0.0
     * @access   static
     */
    public static function init() {
        add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
        add_action( 'woocommerce_settings_tabs_settings_tab_demo', __CLASS__ . '::settings_tab' );
        add_action( 'woocommerce_update_options_settings_tab_demo', __CLASS__ . '::update_settings' );
        add_action('shoutout_sms_for_woo_setting', array(__CLASS__, 'shoutout_sms_for_woo_setting_field'));
        add_action('wp_ajax_shoutout_sms_send_test_sms', array(__CLASS__, 'shoutout_sms_send_test_sms'));
    }

    /**
     * Add a new settings tab to the WooCommerce settings tabs array.
     * @since    1.0.0
     * @access   public
     *
     * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
     * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
     */
    public static function add_settings_tab( $settings_tabs ) {
        $settings_tabs['settings_tab_demo'] = __( 'ShoutOUT SMS', 'shoutout-sms-for-woo' );
        return $settings_tabs;
    }

    /**
     * Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
     *
     * @uses woocommerce_admin_fields()
     * @uses self::get_settings()
     */
    public static function settings_tab() {
        woocommerce_admin_fields( self::shoutout_sms_for_woo_general_setting_save_field() );
    }

    /**
     * Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
     *
     * @uses woocommerce_update_options()
     * @uses self::get_settings()
     */
    public static function update_settings() {
        woocommerce_update_options( self::shoutout_sms_for_woo_general_setting_save_field() );
    }

    public static function shoutout_sms_for_woo_general_setting_save_field() {
        $fields[] = array('title' => __('Admin Notifications', 'shoutout-sms-for-woo'), 'type' => 'title', 'desc' => '', 'id' => 'admin_notifications_options');
        $fields[] = array(
            'title' => __('Allow admin notifications for new orders.', 'shoutout-sms-for-woo'),
            'id' => 'shoutout_sms_woo_enable_admin_sms',
            'default' => 'no',
            'type' => 'checkbox'
        );
        $fields[] = array(
            'title' => __('Admin Mobile Number', 'shoutout-sms-for-woo'),
            'id' => 'shoutout_sms_woo_admin_sms_recipients',
            'desc' => __('Enter admin mobile number begining with your country code.(e.g. 94791234567). To send to multiple recipients, seperate numbers with a comma.', 'shoutout-sms-for-woo'),
            'default' => '94791234567',
            'type' => 'text'
        );
        $fields[] = array(
            'title' => __('Admin SMS Message', 'shoutout-sms-for-woo'),
            'id' => 'shoutout_sms_woo_admin_sms_template',
            'desc' => __('Customization tags for new order SMS: {{shop_name}}, {{order_id}}, {{order_amount}}. 160 Characters.', 'shoutout-sms-for-woo'),
            'css' => 'min-width:500px;',
            'default' => __('{{shop_name}} : You have a new order ({{order_id}}) for Rs.{{order_amount}}', 'shoutout-sms-for-woo'),
            'type' => 'textarea'
        );
        $fields[] = array('type' => 'sectionend', 'id' => 'general_options');
        $fields[] = array('title' => __('Customer Notifications', 'shoutout-sms-for-woo'), 'type' => 'title', 'desc' => '', 'id' => 'customer_notification_options');
        $fields[] = array(
            'title' => __('Enable SMS notifications for these customer actions', 'shoutout-sms-for-woo'),
            'desc' => __('Pending', 'shoutout-sms-for-woo'),
            'id' => 'shoutout_sms_woo_send_sms_pending',
            'default' => 'yes',
            'type' => 'checkbox',
            'checkboxgroup' => 'start'
        );
        $fields[] = array(
            'desc' => __('On-Hold', 'shoutout-sms-for-woo'),
            'id' => 'shoutout_sms_woo_send_sms_on-hold',
            'default' => 'yes',
            'type' => 'checkbox',
            'checkboxgroup' => '',
            'autoload' => false
        );
        $fields[] = array(
            'desc' => __('Processing', 'shoutout-sms-for-woo'),
            'id' => 'shoutout_sms_woo_send_sms_processing',
            'default' => 'yes',
            'type' => 'checkbox',
            'checkboxgroup' => '',
            'autoload' => false
        );
        $fields[] = array(
            'desc' => __('Completed', 'shoutout-sms-for-woo'),
            'id' => 'shoutout_sms_woo_send_sms_completed',
            'default' => 'yes',
            'type' => 'checkbox',
            'checkboxgroup' => '',
            'autoload' => false
        );
        $fields[] = array(
            'desc' => __('Cancelled', 'shoutout-sms-for-woo'),
            'id' => 'shoutout_sms_woo_send_sms_cancelled',
            'default' => 'yes',
            'type' => 'checkbox',
            'checkboxgroup' => '',
            'autoload' => false
        );
        $fields[] = array(
            'desc' => __('Refunded', 'shoutout-sms-for-woo'),
            'id' => 'shoutout_sms_woo_send_sms_refunded',
            'default' => 'yes',
            'type' => 'checkbox',
            'checkboxgroup' => '',
            'autoload' => false
        );
        $fields[] = array(
            'desc' => __('Failed', 'shoutout-sms-for-woo'),
            'id' => 'shoutout_sms_woo_send_sms_failed',
            'default' => 'yes',
            'type' => 'checkbox',
            'checkboxgroup' => 'end',
            'autoload' => false
        );
        $fields[] = array(
            'title' => __('Default SMS Content', 'shoutout-sms-for-woo'),
            'id' => 'shoutout_sms_woo_default_sms_template',
            'default' => __('Your order {{order_id}} is now {{order_status}}. Thank you for shopping at {{shop_name}}.', 'shoutout-sms-for-woo'),
            'type' => 'textarea',
            'css' => 'min-width:500px;'
        );
        $fields[] = array(
            'title' => __('Pending SMS Content', 'shoutout-sms-for-woo'),
            'id' => 'shoutout_sms_woo_pending_sms_template',
            'css' => 'min-width:500px;',
            'type' => 'textarea'
        );
        $fields[] = array(
            'title' => __('On-Hold SMS Content', 'shoutout-sms-for-woo'),
            'id' => 'shoutout_sms_woo_on-hold_sms_template',
            'css' => 'min-width:500px;',
            'type' => 'textarea'
        );
        $fields[] = array(
            'title' => __('Processing SMS Content', 'shoutout-sms-for-woo'),
            'id' => 'shoutout_sms_woo_processing_sms_template',
            'css' => 'min-width:500px;',
            'type' => 'textarea'
        );
        $fields[] = array(
            'title' => __('Completed Order SMS Content', 'shoutout-sms-for-woo'),
            'id' => 'shoutout_sms_woo_completed_sms_template',
            'css' => 'min-width:500px;',
            'type' => 'textarea'
        );
        $fields[] = array(
            'title' => __('Cancelled SMS Content', 'shoutout-sms-for-woo'),
            'id' => 'shoutout_sms_woo_cancelled_sms_template',
            'css' => 'min-width:500px;',
            'type' => 'textarea'
        );
        $fields[] = array(
            'title' => __('Refund SMS Content', 'shoutout-sms-for-woo'),
            'id' => 'shoutout_sms_woo_refunded_sms_template',
            'css' => 'min-width:500px;',
            'type' => 'textarea'
        );
        $fields[] = array(
            'title' => __('Failed Order SMS Content', 'shoutout-sms-for-woo'),
            'id' => 'shoutout_sms_woo_failed_sms_template',
            'css' => 'min-width:500px;',
            'type' => 'textarea'
        );
        $fields[] = array('type' => 'sectionend', 'id' => 'general_options');
        $fields[] = array('title' => __('ShoutOUT Settings', 'shoutout-sms-for-woo'), 'type' => 'title', 'desc' => '', 'id' => 'shoutout_settings_options');
        $fields[] = array(
            'title' => __('API Key', 'shoutout-sms-for-woo'),
            'id' => 'shoutout_sms_woo_auth_token',
            'desc' => __('API key available in your ShoutOUT account.', 'shoutout-sms-for-woo'),
            'type' => 'text',
            'css' => 'min-width:300px;',
        );
        $fields[] = array(
            'title' => __('Sender ID', 'shoutout-sms-for-woo'),
            'id' => 'shoutout_sms_woo_from_number',
            'desc' => __('Enter your ShoutOUT purchased SenderID.', 'shoutout-sms-for-woo'),
            'type' => 'text',
            'css' => 'min-width:300px;',
        );
        $fields[] = array(
            'desc' => __('Use if experiencing issues.', 'shoutout-sms-for-woo'),
            'title' => __('Log Api Errors', 'shoutout-sms-for-woo'),
            'id' => 'shoutout_sms_woo_log_errors',
            'default' => 'no',
            'type' => 'checkbox'
        );
        $fields[] = array('type' => 'sectionend', 'id' => 'general_options');
        return $fields;
    }
}

ShoutOUT_SMS_For_Woo_Admin_Display::init();
