<?php
/**
 * Reports for WooCommerce - Settings
 *
 * @version 2.0.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Settings_Reports' ) ) :

class Alg_WC_Settings_Reports extends WC_Settings_Page {

	/**
	 * Constructor.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 */
	function __construct() {

		$this->id    = 'alg_wc_reports';
		$this->label = __( 'Reports', 'reports-for-woocommerce' );

		parent::__construct();

		// Sections
		require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-reports-settings-section.php';
		require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-reports-settings-general.php';

	}

	/**
	 * get_settings.
	 *
	 * @version 1.1.0
	 * @since   1.0.0
	 */
	function get_settings() {
		global $current_section;
		return array_merge(
			apply_filters( 'woocommerce_get_settings_' . $this->id . '_' . $current_section, array() ),
			array(
				array(
					'title'    => __( 'Reset Settings', 'reports-for-woocommerce' ),
					'type'     => 'title',
					'id'       => $this->id . '_' . $current_section . '_reset_options',
				),
				array(
					'title'    => __( 'Reset section settings', 'reports-for-woocommerce' ),
					'desc'     => '<strong>' . __( 'Reset', 'reports-for-woocommerce' ) . '</strong>',
					'desc_tip' => __( 'Check the box and save changes to reset.', 'reports-for-woocommerce' ),
					'id'       => $this->id . '_' . $current_section . '_reset',
					'default'  => 'no',
					'type'     => 'checkbox',
				),
				array(
					'type'     => 'sectionend',
					'id'       => $this->id . '_' . $current_section . '_reset_options',
				),
			)
		);
	}

	/**
	 * maybe_reset_settings.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 */
	function maybe_reset_settings() {
		global $current_section;
		if ( 'yes' === get_option( $this->id . '_' . $current_section . '_reset', 'no' ) ) {
			foreach ( $this->get_settings() as $value ) {
				if ( isset( $value['id'] ) ) {
					$id = explode( '[', $value['id'] );
					delete_option( $id[0] );
				}
			}
			add_action(
				'admin_notices',
				array( $this, 'admin_notices_settings_reset_success' ),
				PHP_INT_MAX
			);
		}
	}

	/**
	 * admin_notices_settings_reset_success.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function admin_notices_settings_reset_success() {
		echo '<div class="notice notice-warning is-dismissible"><p><strong>' .
			esc_html__( 'Your settings have been reset.', 'reports-for-woocommerce' ) .
		'</strong></p></div>';
	}

	/**
	 * Save settings.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function save() {
		parent::save();
		$this->maybe_reset_settings();
	}

}

endif;

return new Alg_WC_Settings_Reports();
