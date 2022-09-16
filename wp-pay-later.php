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

// Autoloader
require 'vendor/autoload.php';

// Main Plugin Class
if (!class_exists('WPPayLater')) {
	class WPPayLater
	{
		public function __construct()
		{
			add_action('plugins_loaded', [$this, 'constant']);
		}

		public function constant()
		{
			define('WPL_VERSION', rand());
			define('WPL_DIR', plugin_dir_path(__FILE__));
			define('WPL_URL', plugin_dir_url(__FILE__));
			define('WPL_TEXTDOMAIN', 'wp-pay-later');
		}

		public function activate()
		{
		}

		public function deactivate()
		{
		}
	}
	// instantiate the plugin class
	$wp_plugin_template = new WPPayLater();
}
