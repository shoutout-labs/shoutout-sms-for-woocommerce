<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    ShoutOUT_Sms_For_Woo
 * @subpackage ShoutOUT_Sms_For_Woo/admin
 * @author     shoutoutlabs
 */
class ShoutOUT_Sms_For_Woo_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    string    $plugin_name       The name of this plugin.
     * @param    string    $version           The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function shoutout_sms_for_woo_paypal_args($paypal_args) {
        $paypal_args['bn'] = 'mbjtechnolabs_SP';
        return $paypal_args;
    }

    public function shoutout_sms_for_woo_paypal_digital_goods_nvp_args($paypal_args) {
        $paypal_args['BUTTONSOURCE'] = 'mbjtechnolabs_SP';
        return $paypal_args;
    }

    public function shoutout_sms_for_woo_gateway_paypal_pro_payflow_request($paypal_args) {
        $paypal_args['BUTTONSOURCE'] = 'mbjtechnolabs_SP';
        return $paypal_args;
    }

    public function shoutout_sms_for_woo_gateway_paypal_pro_request($paypal_args) {
        $paypal_args['BUTTONSOURCE'] = 'mbjtechnolabs_SP';
        return $paypal_args;
    }

    public static function shoutout_send_customer_sms_for_woo_order_status_pending($order_id) {
        self::shoutout_sms_for_woo_send_customer_notification($order_id, "pending");
    }

    public static function shoutout_send_customer_sms_for_woo_order_status_failed($order_id) {
        self::shoutout_sms_for_woo_send_customer_notification($order_id, "failed");
    }

    public static function shoutout_send_customer_sms_for_woo_order_status_on_hold($order_id) {
        self::shoutout_sms_for_woo_send_customer_notification($order_id, "on-hold");
    }

    public static function shoutout_send_customer_sms_for_woo_order_status_processing($order_id) {
        self::shoutout_sms_for_woo_send_customer_notification($order_id, "processing");
    }

    public static function shoutout_send_customer_sms_for_woo_order_status_completed($order_id) {
        self::shoutout_sms_for_woo_send_customer_notification($order_id, "completed");
    }

    public static function shoutout_send_customer_sms_for_woo_order_status_refunded($order_id) {
        self::shoutout_sms_for_woo_send_customer_notification($order_id, "refunded");
    }

    public static function shoutout_send_customer_sms_for_woo_order_status_cancelled($order_id) {
        self::shoutout_sms_for_woo_send_customer_notification($order_id, "cancelled");
    }

    public static function shoutout_sms_for_woo_send_customer_notification($order_id, $status) {
        $order_details = new WC_Order($order_id);
        if ('yes' == get_option('shoutout_sms_woo_send_sms_' . $status)) {
            $message = get_option('shoutout_sms_woo_' . $status . '_sms_template', '');
            if (empty($message)) {
                $message = get_option('shoutout_sms_woo_default_sms_template');
            }
            $message = self::replace_message_body($message, $order_details);
            $phone = $order_details->get_billing_phone();
            self::send_customer_notification_sms_shoutout_sms_for_woo($phone, $message);
        }
    }

    public static function replace_message_body($message, $order_details) {
        $replacements_string = array(
            '{{shop_name}}' => get_bloginfo('name'),
            '{{order_id}}' => $order_details->get_order_number(),
            '{{order_amount}}' => $order_details->get_total(),
            '{{order_status}}' => ucfirst($order_details->get_status()),
        );
        return str_replace(array_keys($replacements_string), $replacements_string, $message);
    }

    public static function send_customer_notification_sms_shoutout_sms_for_woo($phone, $message) {

        $log = new ShoutOUT_SMS_For_Woo_Logger();

        if (file_exists(SHOUTOUT_PLUGIN_DIR_PATH . '/admin/partials/lib/ShoutOUT.php')) {
            require_once SHOUTOUT_PLUGIN_DIR_PATH . '/admin/partials/lib/ShoutOUT.php';
        }

        if($phone[0]=='+') {
            $phone = str_replace("+","",$phone);
        }

        require_once __DIR__ . '/../vendor/autoload.php';

        $apiKey = get_option("shoutout_sms_woo_auth_token");
        $from_number = get_option("shoutout_sms_woo_from_number");

        $client = new Swagger\Client\ShoutoutClient($apiKey, $debug, $verifySSL);

        $message = array(
            'source' => $from_number,
            'destinations' => [$phone],
            'content' => array(
                'sms' => $message
            ),
            'transports' => ['SMS']
        );
        try {
            $result = $client->sendMessage($message);
        } catch (Exception $e) {
            echo 'Exception when sending message: ', $e->getMessage(), PHP_EOL;
        }
    }

    public static function shoutout_send_admin_order_notification_sms($order_id) {
        $order_details = new WC_Order($order_id);
        if ('yes' == get_option('shoutout_sms_woo_enable_admin_sms')) {
            $message = get_option('shoutout_sms_woo_admin_sms_template', '');
            $message = self::replace_message_body($message, $order_details);
            $recipients_phone_arr = explode(',', trim(get_option('shoutout_sms_woo_admin_sms_recipients')));
            if (!empty($recipients_phone_arr)) {
                foreach ($recipients_phone_arr as $recipient_phone) {
                    try {
                        self::send_customer_notification_sms_shoutout_sms_for_woo($recipient_phone, $message);
                    } catch (Exception $e) {

                    }
                }
            }
        }
    }

}
