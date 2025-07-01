<?php
/*
Plugin Name: Reports for WooCommerce
Plugin URI: https://wordpress.org/plugins/reports-for-woocommerce/
Description: Advanced WooCommerce reports.
Version: 2.0.0
Author: Algoritmika Ltd
Author URI: https://profiles.wordpress.org/algoritmika/
Requires at least: 4.4
Text Domain: reports-for-woocommerce
Domain Path: /langs
WC tested up to: 9.9
Requires Plugins: woocommerce
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

defined( 'ABSPATH' ) || exit;

if ( 'reports-for-woocommerce.php' === basename( __FILE__ ) ) {
	/**
	 * Check if Pro plugin version is activated.
	 *
	 * @version 1.7.0
	 * @since   1.2.0
	 */
	$plugin = 'reports-for-woocommerce-pro/reports-for-woocommerce-pro.php';
	if (
		in_array( $plugin, (array) get_option( 'active_plugins', array() ), true ) ||
		(
			is_multisite() &&
			array_key_exists( $plugin, (array) get_site_option( 'active_sitewide_plugins', array() ) )
		)
	) {
		defined( 'ALG_WC_REPORTS_FILE_FREE' ) || define( 'ALG_WC_REPORTS_FILE_FREE', __FILE__ );
		return;
	}
}

defined( 'ALG_WC_REPORTS_VERSION' ) || define( 'ALG_WC_REPORTS_VERSION', '2.0.0' );

defined( 'ALG_WC_REPORTS_FILE' ) || define( 'ALG_WC_REPORTS_FILE', __FILE__ );

require_once plugin_dir_path( __FILE__ ) . 'includes/class-alg-wc-reports.php';

if ( ! function_exists( 'alg_wc_reports' ) ) {
	/**
	 * Returns the main instance of Alg_WC_Reports to prevent the need to use globals.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function alg_wc_reports() {
		return Alg_WC_Reports::instance();
	}
}

add_action( 'plugins_loaded', 'alg_wc_reports' );
