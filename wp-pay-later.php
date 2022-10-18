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
// public function constant()
// 		{
// 			define('WPL_VERSION', rand());
// 			define('WPL_DIR', plugin_dir_path(__FILE__));
// 			define('WPL_URL', plugin_dir_url(__FILE__));
// 			define('WPL_TEXTDOMAIN', 'wp-pay-later');
// 		}

// Main Plugin Class
//WPPAYLATER

/*
 * Register new method to the payment gateway
 */
add_filter('woocommerce_payment_gateways', 'wpl_add_to_gateway');
function wpl_add_to_gateway($gateways)
{
	$gateways[] = 'WP_PAY_LATER';
	return $gateways;
}

// Init the plugin
add_action('plugins_loaded', 'wpl_init_gateway_class');
function wpl_init_gateway_class()
{
	class WP_PAY_LATER extends WC_Payment_Gateway
	{
		public function __construct()
		{
			$this->id                 = 'wp_pay_later'; // payment gateway plugin ID
			$this->icon               = ''; // URL of the icon that will be displayed on checkout page near your gateway name
			$this->has_fields         = false; // in case you need a custom credit card form
			$this->method_title       = 'WP Pay Later';
			$this->method_description = 'Buy now and pay later'; // will be displayed on the options page

			// Method with all the options fields
			$this->init_form_fields();

			// Load the settings.
			$this->init_settings();
			$this->title           = $this->get_option('title');
			$this->description     = $this->get_option('description');
			$this->enabled         = $this->get_option('enabled');

			// This action hook saves the settings
			add_action('woocommerce_update_options_payment_gateways_' . $this->id, [$this, 'process_admin_options']);

			// You can also register a webhook here
			// add_action( 'woocommerce_api_{webhook name}', array( $this, 'webhook' ) );

			//$this->process_payment();

			add_action('woocommerce_thankyou', [$this, 'generate_pay_later_link']);
		}

		public function init_form_fields()
		{
			$this->form_fields = [
				'enabled' => [
					'title'       => 'Enable/Disable',
					'label'       => 'Enable WP Pay Later',
					'type'        => 'checkbox',
					'description' => '',
					'default'     => 'no'
				],
				'title' => [
					'title'       => 'Title',
					'type'        => 'text',
					'description' => 'This controls the title which the user sees during checkout.',
					'default'     => 'WP Pay Later',
					'desc_tip'    => true,
				],
				'description' => [
					'title'       => 'Description',
					'type'        => 'textarea',
					'description' => 'This controls the description which the user sees during checkout.',
					'default'     => 'Buy now and pay later',
				],
			];
		}

		public function process_payment($order_id)
		{
			global $woocommerce;
			$order = new WC_Order($order_id);

			// Mark as on-hold (we're awaiting the cheque)
			$order->update_status('on-hold', __('Pay Later', 'woocommerce'));

			// Remove cart
			$woocommerce->cart->empty_cart();

			// Return thankyou redirect
			return [
				'result'   => 'success',
				'redirect' => $this->get_return_url($order)
			];
		}

		public function generate_pay_later_link($order_id)
		{
			global $woocommerce;
			$order     = new WC_Order($order_id);
			$site_url  = site_url();
			$key       = '/?order_id=' . $order_id;
			$final_url = '<div class="wp-pay-later-wrapper">';
			$final_url .= '<p>';
			$final_url .= 'Save the below link to pay later';
			$final_url .= '</p>';
			$final_url .= '<a>';
			$final_url .= $site_url . $key  ;
			$final_url .= '</a>';
			$final_url .= '</div>';
			echo $final_url;
		}
	}
}
