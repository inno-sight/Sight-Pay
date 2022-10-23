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
		//var_dump($order);
		return $order->get_status();
	}
}
