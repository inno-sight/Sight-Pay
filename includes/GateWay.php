<?php
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

			//$this->activate();
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
			$order->update_status('pending-payment ', __('Pay Later', 'woocommerce'));

			// Remove cart
			$woocommerce->cart->empty_cart();

			// Return thankyou redirect
			return [
				'result'   => 'success',
				'redirect' => $this->get_return_url($order)
			];
		}
	}
}
