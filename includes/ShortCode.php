<?php
defined('ABSPATH') || exit;

class Create_Short_Code
{
	public function __construct()
	{
		add_shortcode('print_available_payment', [$this, 'create_shortcode']);
	}

	public function create_shortcode()
	{
		$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : 0;
		return $this->order_details($order_id);
	}

	public function order_details($order_id)
	{
		$order = wc_get_order($order_id);
		foreach ($order->get_items() as $item_id => $item) {
			$product_id   = $item->get_product_id();
			$variation_id = $item->get_variation_id();
			$product      = $item->get_product(); // see link above to get $product info
			$product_name = $item->get_name();
			echo '<h1> Product Name: ' . $product_name . '</h1>';
			$quantity     = $item->get_quantity();
			echo '<br><h3> Product Quantity: ' . $quantity . '</h3>';
			$subtotal     = $item->get_subtotal();
			$total        = $item->get_total();
			$tax          = $item->get_subtotal_tax();
			$tax_class    = $item->get_tax_class();
			$tax_status   = $item->get_tax_status();
			$allmeta      = $item->get_meta_data();
			$somemeta     = $item->get_meta('_whatever', true);
			$item_type    = $item->get_type(); // e.g. "line_item"
		}

		global $woocommerce;

		$available_gatewayz = WC()->payment_gateways->get_available_payment_gateways();

		if ($available_gatewayz) { ?>
    	<form id="add_payment_method" method="post">
        <div id="payment" class="woocommerce-Payment">
            <ul class="woocommerce-PaymentMethods payment_methods methods">
                <?php
				// Chosen Method.
				if (count($available_gatewayz)) {
					current($available_gatewayz)->set_current();
				}

				foreach ($available_gatewayz as $gatewayz) {
					?>
                    <li class="woocommerce-PaymentMethod woocommerce-PaymentMethod--<?php echo esc_attr($gatewayz->id); ?> payment_method_<?php echo esc_attr($gatewayz->id); ?>">
                        <input id="payment_method_<?php echo esc_attr($gatewayz->id); ?>" type="radio" class="input-radio" name="payment_method" value="<?php echo esc_attr($gatewayz->id); ?>" <?php checked($gatewayz->chosen, true); ?> />
                        <label for="payment_method_<?php echo esc_attr($gatewayz->id); ?>"><?php echo wp_kses_post($gatewayz->get_title()); ?> <?php echo wp_kses_post($gatewayz->get_icon()); ?></label>
                        <?php
						if ($gatewayz->has_fields() || $gatewayz->get_description()) {
							echo '<div class="woocommerce-PaymentBox woocommerce-PaymentBox--' . esc_attr($gatewayz->id) . ' payment_box payment_method_' . esc_attr($gatewayz->id) . '" style="display: none;">';
							$gatewayz->payment_fields();
							echo '</div>';
						} ?>
                    </li>
                    <?php
				}
		} ?>
            </ul>
		  <?php

		//return $order;
	}
}
