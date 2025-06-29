<?php
/**
 * Reports for WooCommerce - Report - Taxes
 *
 * @version 1.7.2
 * @since   1.5.0
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Report_Taxes' ) ) :

class Alg_WC_Report_Taxes extends Alg_WC_Report {

	/**
	 * Constructor.
	 *
	 * @version 1.5.0
	 * @since   1.5.0
	 */
	function __construct() {
		$this->id    = 'taxes';
		$this->title = __( 'Taxes', 'reports-for-woocommerce' );
	}

	/**
	 * get_reports.
	 *
	 * @version 1.5.0
	 * @since   1.5.0
	 */
	function get_reports() {
		return array(
			'alg_wc_report_taxes_by_order' => __( 'Taxes by order', 'reports-for-woocommerce' ),
		);
	}

	/**
	 * get_data.
	 *
	 * @version 1.5.0
	 * @since   1.5.0
	 */
	function get_data( $name ) {
		switch ( $name ) {
			case 'alg_wc_report_taxes_by_order':
				return $this->get_data_taxes_by_order();
		}
		return array( 'data' => array(), 'detailed_data' => array() );
	}

	/**
	 * get_data_taxes_by_order.
	 *
	 * @version 1.7.2
	 * @since   1.5.0
	 */
	function get_data_taxes_by_order() {

		// Get orders
		$date_key = get_option( 'alg_wc_reports_orders_date_range', 'date_created' );
		$orders   = wc_get_orders(
			apply_filters(
				'alg_wc_reports_taxes_get_data_taxes_by_order_args',
				array(
					'type'    => 'shop_order',
					'limit'   => -1,
					$date_key => $this->get_menu()->get_report_date(),
					'status'  => $this->get_menu()->get_order_status(),
					'orderby' => get_option( 'alg_wc_reports_orders_orderby', 'date' ),
					'order'   => get_option( 'alg_wc_reports_orders_orderby_order', 'DESC' ),
				),
				$this
			)
		);

		// Orders loop
		$data          = array();
		$detailed_data = array();
		foreach ( $orders as $order ) {

			// Skip orders without taxes
			if ( 0 == $order->get_total_tax() ) {
				continue;
			}

			// Taxes
			$taxes = array();
			foreach ( $order->get_taxes() as $tax ) {

				// Init
				$tax_total = $tax->get_tax_total();
				$label     = sprintf( '%s (%s%%) [#%d]', $tax->get_label(), $tax->get_rate_percent(), $tax->get_rate_id() );

				// Data
				$data[ $label ] = ( isset( $data[ $label ] ) ? ( $data[ $label ] + $tax_total ) : $tax_total );

				// Detailed data
				$taxes[ $label ] = array( 'label' => $label, 'tax_total' => $tax_total );

			}

			// Detailed data
			$detailed_data[] = apply_filters(
				'alg_wc_reports_taxes_detailed_data_row',
				array(
					'number'    => '<a href="' . admin_url( 'post.php?post=' . $order->get_id() . '&action=edit' ) . '">' .
						$order->get_order_number() .
					'</a>',
					'date'      => $order->get_date_created()->date( get_option( 'date_format' ) ),
					'taxes'     => $taxes,
					'total_tax' => $order->get_total_tax(),
				),
				$order
			);

		}

		// Sort tax by label
		ksort( $data );

		// Format detailed data
		$detailed_data = $this->format_detailed_data_taxes_by_order( $detailed_data, $data );

		// Result
		return array( 'data' => $data, 'detailed_data' => $detailed_data );

	}

	/**
	 * format_detailed_data_taxes_by_order.
	 *
	 * @version 1.7.2
	 * @since   1.5.0
	 */
	function format_detailed_data_taxes_by_order( $detailed_data, $data ) {
		$_detailed_data = array();

		// Header
		$_header = array(
			'<strong>' . esc_html__( 'Order Number', 'reports-for-woocommerce' ) . '</strong>',
			'<strong>' . esc_html__( 'Date', 'reports-for-woocommerce' ) . '</strong>',
		);
		foreach ( array_keys( $data ) as $label ) {
			$_header[] = '<strong>' . $label . '</strong>';
		}
		$_header[] = '<strong>' . esc_html__( 'Total Tax', 'reports-for-woocommerce' ) . '</strong>';
		$_detailed_data[] = apply_filters( 'alg_wc_reports_taxes_detailed_data_heading', $_header );

		// Loop
		foreach ( $detailed_data as $row ) {
			$_row = array();
			foreach ( $row as $id => $value ) {
				if ( 'taxes' === $id ) {
					foreach ( array_keys( $data ) as $label ) {
						$_row[] = ( isset( $value[ $label ] ) ? $value[ $label ]['tax_total'] : '' );
					}
				} else {
					$_row[] = $value;
				}
			}
			$_detailed_data[] = $_row;
		}

		// Footer
		$_footer = array(
			'',
			'<strong>' . esc_html__( 'Totals per tax category', 'reports-for-woocommerce' ) . '</strong>',
		);
		foreach ( $data as $value ) {
			$_footer[] = $value;
		}
		$_footer[] = array_sum( $data );
		$_detailed_data[] = $_footer;

		// Result
		return $_detailed_data;
	}

}

endif;

return new Alg_WC_Report_Taxes();
