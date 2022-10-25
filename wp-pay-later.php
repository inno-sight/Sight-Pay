<?php
/**
 * Plugin Name:       Sight Pay
 * Plugin URI:        https://wordpress.org/plugins/sight-pay
 * Description:       Buy now and pay later with WooCommerce payment system.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.4
 * Author:            innosight
 * Author URI:        https://theinnosight.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       sight-pay
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
defined('ABSPATH') || exit;

define('SP_VERSION', '1.0.0');
define('SP_DIR', plugin_dir_path(__FILE__));
define('SP_URL', plugin_dir_url(__FILE__));
define('SP_TEXTDOMAIN', 'sight-pay');

// Make sure WooCommerce is active
if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
	add_action('admin_notices', 'sp_admin_notice');
	return;
}
function sp_admin_notice()
{
	?>
    <div class="notice notice-error">
        <p><?php _e('Please install and active WooCommerce!', SP_TEXTDOMAIN); ?></p>
    </div>
    <?php
}

// Include all the class
include_once 'includes/GateWay.php';

class SP_FREE
{
	public function __construct()
	{
		// Generate link to thank you page
		add_action('woocommerce_thankyou', [$this, 'generate_pay_later_link']);
	}

	public function generate_pay_later_link($order_id)
	{
		global $woocommerce;
		$order           = new WC_Order($order_id);
		$checkout_URL    = wc_get_checkout_url();
		$key             = $order->get_order_key();
		$pay_later_page  = $checkout_URL . 'order-pay/' . $order_id . '/?pay_for_order=true&key=' . $key;
		$final_url       = '<div class="sight-pay-wrapper">';
		$final_url .= '<p>';
		$final_url .= __('Save the below link to pay later', SP_TEXTDOMAIN);
		$final_url .= '</p>';
		$final_url .= '<a href="' . $pay_later_page . '">';
		$final_url .= $pay_later_page;
		$final_url .= '</a>';
		$final_url .= '</div>';
		echo $final_url;
	}
}

// Call The Class
$sp_free = new SP_FREE();
