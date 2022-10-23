<?php
defined('ABSPATH') || exit;

class Create_Page
{
	public function __construct()
	{
		$this->add_page();
		//register_activation_hook(__FILE__, [$this, 'add_my_custom_page']);
	}

	public function add_page()
	{
		if (!current_user_can('activate_plugins')) {
			return;
		}

		global $wpdb;

		if (null === $wpdb->get_row("SELECT post_name FROM {$wpdb->prefix}posts WHERE post_name = 'wp-pay-later'", 'ARRAY_A')) {
			$current_user = wp_get_current_user();

			// create post object
			$page = [
				'post_title'   => __('WP Pay Later'),
				'post_content' => '<!-- wp:shortcode -->[print_available_payment]<!-- /wp:shortcode -->',
				'post_status'  => 'publish',
				'post_author'  => $current_user->ID,
				'post_type'    => 'page',
			];

			// insert the post into the database
			wp_insert_post($page);
		}
	}
}
