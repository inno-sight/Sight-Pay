<?php
/*
 * Register new method to the payment gateway
 */
add_filter('woocommerce_payment_gateways', 'innosp_add_to_gateway');
function innosp_add_to_gateway($gateways)
{
	$gateways[] = 'SIGHT_PAY';
	return $gateways;
}

// Init the plugin
add_action('plugins_loaded', 'innosp_init_gateway_class');
function innosp_init_gateway_class()
{
	class SIGHT_PAY extends WC_Payment_Gateway
	{
		public function __construct()
		{
			$this->id                 = 'sight_pay'; // payment gateway plugin ID
			$this->icon               = ''; // URL of the icon that will be displayed on checkout page near your gateway name
			$this->has_fields         = false; // in case you need a custom credit card form
			$this->method_title       = 'Sight Pay';
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
					'title'       => __('Enable/Disable', INNOSP_TEXTDOMAIN),
					'label'       => __('Enable Sight Pay', INNOSP_TEXTDOMAIN),
					'type'        => 'checkbox',
					'description' => '',
					'default'     => 'no'
				],
				'title' => [
					'title'       => 'Title',
					'type'        => 'text',
					'description' => __('This controls the title which the user sees during checkout.', INNOSP_TEXTDOMAIN),
					'default'     => __('Sight Pay', INNOSP_TEXTDOMAIN),
					'desc_tip'    => true,
				],
				'description' => [
					'title'       => 'Description',
					'type'        => 'textarea',
					'description' => __('This controls the description which the user sees during checkout.', INNOSP_TEXTDOMAIN),
					'default'     => __('Buy now and pay later', INNOSP_TEXTDOMAIN),
				],
			];
		}

		public function process_payment($order_id)
		{
			global $woocommerce;
			$order = new WC_Order($order_id);

			// Mark as on-hold (we're awaiting the cheque)
			$order->update_status('pending-payment ', __('Sight Pay', INNOSP_TEXTDOMAIN));

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
