<?php
/**
 * Banner Adsense - An advanced image widget for placing multiple banner images or ads.
 *
 * @package             BannerAdsense
 * @version             1.0.2
 *
 * @wordpress-plugin
 * Plugin Name:         Banner Adsense
 * Plugin URI:          http://www.perfomatix.com
 * Description:         An advanced image widget for placing multiple banner images or ads.
 * Version:             1.0.2
 * Author:              Perfomatix
 * Author URI:          http://www.perfomatix.com
 * License:             MIT
 * License URI:         https://opensource.org/licenses/MIT
 * Text Domain:         banner-adsense
 * Domain Path:         /languages
 * Requires PHP:        5.6
 * Requires at least:   5.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Include the class files.
require_once 'includes/class-banner-adsense.php';
require_once 'includes/class-admin-settings.php';
require_once 'includes/class-widget.php';

// Call after plugin activation.
register_activation_hook( __FILE__, 'banner_adsense_activate' );

// Call after plugin deactivation.
register_deactivation_hook( __FILE__, 'banner_adsense_deactivate' );

// Load the plugin when WordPress is ready.
add_action( 'plugins_loaded', 'banner_adsense_load_self' );

/**
 * Activate the plugin.
 *
 * @return void
 */
function banner_adsense_activate() {
	flush_rewrite_rules( true );
}

/**
 * Deactivate the plugin.
 *
 * @return void
 */
function banner_adsense_deactivate() {
	flush_rewrite_rules( true );
}

/**
 * Initiate the autoloader and call main plugin class.
 *
 * @return void
 */
function banner_adsense_load_self() {

	// Initiate the plugin.
	$self = ( \Perfomatix\BannerAdsense\Banner_Adsense::instance() )
		->set_basename( plugin_basename( __FILE__ ) )
		->set_path( plugin_dir_path( __FILE__ ) )
		->set_url( plugin_dir_url( __FILE__ ) )
		->set_version( '1.0.2' )
		->init();

	if ( is_admin() ) {
		$self->admin_init();
	}
}
