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
		echo 'Hello Pay Later';
	}
}
