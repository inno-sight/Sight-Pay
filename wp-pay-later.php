<?php
/**
 * Plugin Name:       WP Pay Later
 * Plugin URI:        #
 * Description:       Buy now and pay later with WooCommerce payment system.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.4
 * Author:            rakibwordpress
 * Author URI:        https://profiles.wordpress.org/rakibwordpress/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
 * Text Domain:       wp-pay-later
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
defined('ABSPATH') || exit;

define('WPL_VERSION', rand());
define('WPL_DIR', plugin_dir_path(__FILE__));
define('WPL_URL', plugin_dir_url(__FILE__));
define('WPL_TEXTDOMAIN', 'wp-pay-later');

// Make sure WooCommerce is active
if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
	add_action('admin_notices', 'wpl_admin_notice');
	return;
}
function wpl_admin_notice()
{
	?>
    <div class="notice notice-error">
        <p><?php _e('Please install and active WooCommerce!', 'wp-pay-later'); ?></p>
    </div>
    <?php
}

// Include all the class
include_once 'includes/CreatePage.php';
include_once 'includes/ShortCode.php';
include_once 'includes/GateWay.php';

class WP_PAY_LATER_MAIN
{
	public function __construct()
	{
		// Register activation hook
		register_activation_hook(__FILE__, [$this, 'activate']);

		//ShortCode
		$this->call_shortcode_class();

		// Generate link to thank you page
		add_action('woocommerce_thankyou', [$this, 'generate_pay_later_link']);
	}

	// Activation function
	public function activate()
	{
		// Create Page to admin
		$create_page = new Create_Page();
	}

	//Create Page Content ShortCode
	public function call_shortcode_class()
	{
		$shortcode = new Create_Short_Code();
	}

	public function generate_pay_later_link($order_id)
	{
		global $woocommerce;
		$order     = new WC_Order($order_id);

		$pay_later_page  = get_permalink(get_page_by_title('WP Pay Later'));
		$key             = '/?order_id=' . $order_id;
		$final_url       = '<div class="wp-pay-later-wrapper">';
		$final_url .= '<p>';
		$final_url .= 'Save the below link to pay later';
		$final_url .= '</p>';
		$final_url .= '<a>';
		$final_url .= $pay_later_page . $key  ;
		$final_url .= '</a>';
		$final_url .= '</div>';
		echo $final_url;
	}
}

// Call The Class
$wp_pay_later_main = new WP_PAY_LATER_MAIN();
