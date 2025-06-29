<?php
/**
 * Reports for WooCommerce - General Section Settings
 *
 * @version 2.0.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Reports_Settings_General' ) ) :

class Alg_WC_Reports_Settings_General extends Alg_WC_Reports_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function __construct() {

		$this->id   = '';
		$this->desc = __( 'General', 'reports-for-woocommerce' );

		parent::__construct();

	}

	/**
	 * get_settings.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 *
	 * @todo    (desc) `alg_wc_reports_menu_product_cat`: better desc
	 * @todo    (desc) `alg_wc_reports_skip_zero_orders`: better desc
	 * @todo    (dev) `alg_wc_reports_enabled_section_orders` etc. instead of array (same for `alg_wc_reports_enabled_reports`)
	 */
	function get_settings() {

		$reports_settings = array(
			array(
				'title'    => __( 'Reports', 'reports-for-woocommerce' ),
				'desc_tip' => __( 'Advanced WooCommerce reports.', 'reports-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_reports_options',
			),
		);
		$is_enabled = get_option( 'alg_wc_reports_enabled_section', array() );
		foreach ( alg_wc_reports()->core->report_types as $type ) {
			$reports_settings = array_merge( $reports_settings, array(
				array(
					'title'    => alg_wc_reports()->core->reports[ $type ]->title,
					'desc'     => __( 'Enable', 'reports-for-woocommerce' ),
					'desc_tip' => (
						! isset( $is_enabled[ $type ] ) || 'yes' === $is_enabled[ $type ] ?
						'<a target="_blank" href="' . admin_url( 'admin.php?page=wc-reports&tab=' . $type ) . '">' .
							sprintf(
								/* Translators: %s: Report title. */
								__( 'WooCommerce > Reports > %s', 'reports-for-woocommerce' ),
								alg_wc_reports()->core->reports[ $type ]->title
							) .
						'</a>' :
						''
					),
					'id'       => "alg_wc_reports_enabled_section[{$type}]",
					'default'  => 'yes',
					'type'     => 'checkbox',
				),
				array(
					'desc_tip' => __( 'Select reports to enable. Leave blank to enable all reports.', 'reports-for-woocommerce' ),
					'id'       => "alg_wc_reports_enabled_reports[{$type}]",
					'default'  => '',
					'type'     => 'multiselect',
					'class'    => 'chosen_select',
					'options'  => alg_wc_reports()->core->reports[ $type ]->get_reports(),
				),
			) );
		}
		$reports_settings = array_merge( $reports_settings, array(
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_reports_options',
			),
		) );

		$advanced_settings = array(
			array(
				'title'    => __( 'Advanced', 'reports-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_reports_advanced_options',
			),
			array(
				'title'    => __( 'Orders date range', 'reports-for-woocommerce' ),
				'id'       => 'alg_wc_reports_orders_date_range',
				'default'  => 'date_created',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => array(
					'date_created'   => __( 'Date created', 'reports-for-woocommerce' ),
					'date_modified'  => __( 'Date modified', 'reports-for-woocommerce' ),
					'date_completed' => __( 'Date completed', 'reports-for-woocommerce' ),
					'date_paid'      => __( 'Date paid', 'reports-for-woocommerce' ),
				),
			),
			array(
				'title'    => __( 'Orders sorting', 'reports-for-woocommerce' ),
				'id'       => 'alg_wc_reports_orders_orderby',
				'default'  => 'date',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => array(
					'none'     => __( 'None', 'reports-for-woocommerce' ),
					'ID'       => __( 'ID', 'reports-for-woocommerce' ),
					'name'     => __( 'Name', 'reports-for-woocommerce' ),
					'type'     => __( 'Type', 'reports-for-woocommerce' ),
					'rand'     => __( 'Random', 'reports-for-woocommerce' ),
					'date'     => __( 'Date', 'reports-for-woocommerce' ),
					'modified' => __( 'Modified', 'reports-for-woocommerce' ),
				),
			),
			array(
				'id'       => 'alg_wc_reports_orders_orderby_order',
				'default'  => 'DESC',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => array(
					'DESC' => __( 'Descending', 'reports-for-woocommerce' ),
					'ASC'  => __( 'Ascending', 'reports-for-woocommerce' ),
				),
			),
			array(
				'title'    => __( 'Data as chart', 'reports-for-woocommerce' ),
				'desc_tip' => __( 'Show data as chart.', 'reports-for-woocommerce' ),
				'desc'     => __( 'Show', 'reports-for-woocommerce' ),
				'id'       => 'alg_wc_reports_show_chart',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Data as table', 'reports-for-woocommerce' ),
				'desc_tip' => __( 'Additionally show data as table.', 'reports-for-woocommerce' ),
				'desc'     => __( 'Show', 'reports-for-woocommerce' ),
				'id'       => 'alg_wc_reports_show_data_as_table',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Detailed data', 'reports-for-woocommerce' ),
				'desc_tip' => __( 'Show detailed data table.', 'reports-for-woocommerce' ),
				'desc'     => __( 'Show', 'reports-for-woocommerce' ),
				'id'       => 'alg_wc_reports_show_detailed_data',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Skip zero orders', 'reports-for-woocommerce' ),
				'desc_tip' => __( 'Skips orders with zero total.', 'reports-for-woocommerce' ),
				'desc'     => __( 'Skip', 'reports-for-woocommerce' ),
				'id'       => 'alg_wc_reports_skip_zero_orders',
				'default'  => 'no',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Product categories', 'reports-for-woocommerce' ),
				'desc_tip' => __( 'Product categories menu.', 'reports-for-woocommerce' ),
				'id'       => 'alg_wc_reports_menu_product_cat',
				'default'  => 'multiple',
				'type'     => 'select',
				'class'    => 'chosen_select',
				'options'  => array(
					'single'   => __( 'Single', 'reports-for-woocommerce' ),
					'multiple' => __( 'Multiple', 'reports-for-woocommerce' ),
				),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_reports_advanced_options',
			),
		);

		return array_merge(
			$reports_settings,
			$advanced_settings,
			apply_filters( 'alg_wc_reports_settings_custom', array() )
		);

	}

}

endif;

return new Alg_WC_Reports_Settings_General();
