<?php
/**
 * Plugin Name:       Sight Pay
 * Plugin URI:        https://wordpress.org/plugins/sight-pay
 * Description:       Sight Pay is an awesome new way to shop | Pay later plugin for woocommerce
 * Version:           1.2.2
 * Author:            innosight
 * Author URI:        https://theinnosight.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sight-pay
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
defined('ABSPATH') || exit;

define('INNOSP_VERSION', '1.2.2');
define('INNOSP_DIR', plugin_dir_path(__FILE__));
define('INNOSP_URL', plugin_dir_url(__FILE__));
define('INNOSP_TEXTDOMAIN', 'sight-pay');

// Make sure WooCommerce is active
if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
	add_action('admin_notices', 'innosp_admin_notice');
	return;
}
function innosp_admin_notice()
{
	?>
    <div class="notice notice-error">
        <p><?php esc_html_e('Please install and active WooCommerce!', INNOSP_TEXTDOMAIN); ?></p>
    </div>
    <?php
}

// Include all the class
include_once 'includes/GateWay.php';

class INNOSP_FREE
{
	public function __construct()
	{
		// Generate link to thank you page
		add_action('woocommerce_thankyou', [$this, 'generate_pay_later_link']);
	}

	public function generate_pay_later_link($order_id)
	{
		global $woocommerce;
		$order                  = new WC_Order($order_id);
		$checkout_URL           = wc_get_checkout_url();
		$key                    = $order->get_order_key();
		$pay_later_page         = $checkout_URL . 'order-pay/' . $order_id . '/?pay_for_order=true&key=' . $key;
		$wrapper_with_url       = '<div class="' . esc_attr('sight-pay-wrapper') . '">';
		$wrapper_with_url .= '<p>';
		$wrapper_with_url .= __('Save the below link to pay later', INNOSP_TEXTDOMAIN);
		$wrapper_with_url .= '</p>';
		$wrapper_with_url .= '<a href="' . esc_url($pay_later_page) . '">';
		$wrapper_with_url .= esc_url($pay_later_page);
		$wrapper_with_url .= '</a>';
		$wrapper_with_url .= '</div>';
		$allowed_html = [
			'a' => [
				'href'  => true,
				'title' => true,
			],
			'div' => [
				'class' => []
			],
			'p'=> [
			]
		];
		echo wp_kses($wrapper_with_url, $allowed_html);
	}
}

// Call The Class
$innosp_free = new INNOSP_FREE();
