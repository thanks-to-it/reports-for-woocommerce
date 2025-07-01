<?php
/**
 * Reports for WooCommerce - Menu Class
 *
 * @version 2.0.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Reports_Menu' ) ) :

class Alg_WC_Reports_Menu {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 *
	 * @todo    (dev) move report specific menus to report file (e.g., dates menu to `Alg_WC_Report_Orders`)
	 */
	function __construct() {
		return true;
	}

	/**
	 * get_menus.
	 *
	 * @version 1.6.0
	 * @since   1.0.0
	 */
	function get_menus( $data, $name, $report_id ) {
		$menu_html = '';

		// Orders and Taxes menu
		if ( in_array( $report_id, array( 'orders', 'taxes' ) ) ) {
			$menu_html .= $this->get_date_menu();
			$menu_html .= $this->get_order_status_menu();
			if ( in_array( $name, array( 'alg_wc_report_sales_by_product_cat' ) ) ) {
				if ( 'single' === get_option( 'alg_wc_reports_menu_product_cat', 'multiple' ) ) {
					$menu_html .= $this->get_product_cat_menu();
				}
				if ( 'multiple' === get_option( 'alg_wc_reports_menu_product_cat', 'multiple' ) ) {
					$menu_html .= $this->get_product_cats_menu();
				}
			}
		}

		// Products menu
		if ( 'products' === $report_id ) {
			$menu_html .= $this->get_product_type_menu();
		}

		// Data menu
		if ( ! empty( $data ) ) {
			$menu_html .= $this->get_data_menus( $name );
		}

		// Filter
		$menu_html = apply_filters(
			'alg_wc_reports_menu_get_html',
			$menu_html,
			$data,
			$name,
			$report_id,
			$this
		);

		// Result
		if ( ! empty( $menu_html ) ) {
			return '<h3>' . __( 'Menu', 'reports-for-woocommerce' ) . '</h3>' .
				'<table class="widefat striped">' . $menu_html . '</table>';
		} else {
			return '';
		}

	}

	/**
	 * get_menu.
	 *
	 * @version 1.5.0
	 * @since   1.0.0
	 *
	 * @todo    (dev) `'<p>' . $label . ': ' . implode( ' | ', $links ) ) . '</p>';`
	 */
	function get_menu( $label, $links, $append = '' ) {
		return '<tr>' .
			'<th style="width:30%;">' . $label . '</th>' .
			'<td>' . implode( ' | ', $links ) . $append . '</td>' .
		'</tr>';
	}

	/**
	 * get_hidden_inputs.
	 *
	 * @version 1.6.0
	 * @since   1.6.0
	 */
	function get_hidden_inputs( $skip_keys = array() ) {
		$res = '';
		foreach ( $_GET as $id => $value ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( ! in_array( esc_attr( $id ), $skip_keys ) ) {
				if ( is_array( $value ) ) {
					foreach ( $value as $_value ) {
						$res .= '<input' .
							' type="hidden"' .
							' name="' . esc_attr( wc_clean( $id ) ) . '[]"' .
							' value="' . esc_attr( wc_clean( $_value ) ) . '"' .
						'>';
					}
				} else {
					$res .= '<input' .
						' type="hidden"' .
						' name="' . esc_attr( wc_clean( $id ) ) . '"' .
						' value="' . esc_attr( wc_clean( $value ) ) . '"' .
					'>';
				}
			}
		}
		return $res;
	}

	/**
	 * get_data_menus.
	 *
	 * @version 1.2.0
	 * @since   1.0.0
	 */
	function get_data_menus( $report ) {
		$html = '';
		$html .= $this->get_chart_type_menu( $report );
		if (
			in_array(
				$report,
				array(
					'alg_wc_report_sales_by_billing_country',
					'alg_wc_report_sales_by_shipping_country',
				)
			)
		) {
			$html .= $this->get_data_type_menu();
		}
		if (
			! in_array(
				$report,
				array(
					'alg_wc_report_products_by_category',
					'alg_wc_report_products_by_tag',
				)
			) &&
			false !== strpos( $report, 'alg_wc_report_products_by_' )
		) {
			$html .= $this->get_data_scale_menu();
		}
		if (
			in_array(
				$report,
				array(
					'alg_wc_report_sales_by_product',
					'alg_wc_report_sales_by_product_cat',
				)
			)
		) {
			$html .= $this->get_item_data_type_menu();
		}
		return $html;
	}

	/**
	 * get_product_cat_menu.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 */
	function get_product_cat_menu() {
		$key   = 'product_cat';
		$terms = get_terms( array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => false,
		) );
		$data  = array(
			array(
				'key'   => '',
				'title' => __( 'All', 'reports-for-woocommerce' ),
			),
		);
		if ( $terms && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$data[] = array(
					'key'   => strval( $term->term_id ),
					'title' => $term->name,
				);
			}
		}
		$links   = array();
		$current = $this->get_current_value( $key, '' );
		foreach ( $data as $value ) {
			$url     = add_query_arg( array( $key => $value['key'] ) );
			$active  = ( $current === $value['key'] ? 'font-weight:bold;color:black;' : '' );
			$links[] = '<a href="' . $url . '" style="text-decoration:none;' . $active . '">' .
				$value['title'] .
			'</a>';
		}
		return $this->get_menu( __( 'Product categories', 'reports-for-woocommerce' ), $links );
	}

	/**
	 * get_product_cat.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 */
	function get_product_cat() {
		return $this->get_current_value( 'product_cat', '' );
	}

	/**
	 * get_product_cats_menu.
	 *
	 * @version 1.6.0
	 * @since   1.4.0
	 *
	 * @todo    (feature) "select/deselect all"
	 * @todo    (dev) `'<a class="button" style="margin-left:5px;" href="#" onclick="document.getElementById(\'' . $key . '\').submit();">' . esc_html__( 'Apply', 'reports-for-woocommerce' ) . '</a>'`
	 */
	function get_product_cats_menu() {

		$key = 'product_cats';

		// Terms
		$terms = get_terms( array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => false,
		) );
		if ( $terms && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$data[] = array(
					'key'        => strval( $term->term_id ),
					'title'      => $term->name,
				);
			}
		}

		// Select `<option>`s
		$options = '';
		$current = $this->get_current_value( $key, array() );
		foreach ( $data as $value ) {
			$options .= (
				'<option' .
					' value="' . $value['key'] . '"' .
					' ' . selected( in_array( $value['key'], $current ), true, false ) .
				'>' .
					$value['title'] .
				'</option>'
			);
		}

		// Final `<form>`
		return '<tr>' .
			'<th style="width:30%;">' .
				__( 'Product categories', 'reports-for-woocommerce' ) .
			'</th>' .
			'<td>' .
				'<form method="get" id="' . $key . '">' .
					'<select' .
						' multiple' .
						' class="chosen_select"' .
						' name="' . $key . '[]"' .
					'>' .
						$options .
					'</select>' .
					'<input' .
						' type="submit"' .
						' name="' . $key . '_submit"' .
						' value="' . esc_attr__( 'Apply', 'reports-for-woocommerce' ) . '"' .
						' class="button"' .
						' style="margin:4px;"' .
					'>' .
					$this->get_hidden_inputs( array( $key, $key . '_submit' ) ) .
				'</form>' .
			'</td>' .
		'</tr>';

	}

	/**
	 * get_product_cats.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 */
	function get_product_cats() {
		return $this->get_current_value( 'product_cats', array() );
	}

	/**
	 * get_item_data_type_menu.
	 *
	 * @version 1.2.0
	 * @since   1.2.0
	 *
	 * @todo    (dev) code refactoring: merge this with `get_chart_type_menu()`, etc.
	 */
	function get_item_data_type_menu() {
		$key  = 'item_data_type';
		$data = array(
			array(
				'key'        => 'quantity',
				'title'      => __( 'Qty', 'reports-for-woocommerce' ),
			),
			array(
				'key'        => 'line_total',
				'title'      => __( 'Sum', 'reports-for-woocommerce' ),
			),
		);
		$links   = array();
		$current = $this->get_current_value( $key, 'quantity' );
		foreach ( $data as $value ) {
			$url     = add_query_arg( array( $key => $value['key'] ) );
			$active  = ( $current === $value['key'] ? 'font-weight:bold;color:black;' : '' );
			$links[] = '<a href="' . $url . '" style="text-decoration:none;' . $active . '">' .
				$value['title'] .
			'</a>';
		}
		return $this->get_menu( __( 'Item data', 'reports-for-woocommerce' ), $links );
	}

	/**
	 * get_item_data_type.
	 *
	 * @version 1.2.0
	 * @since   1.2.0
	 */
	function get_item_data_type() {
		return $this->get_current_value( 'item_data_type', 'quantity' );
	}

	/**
	 * get_product_type_menu.
	 *
	 * @version 1.2.0
	 * @since   1.0.0
	 *
	 * @todo    (dev) `variation`
	 * @todo    (dev) option to select multiple types
	 */
	function get_product_type_menu() {
		$key     = 'product_type';
		$data    = array_merge(
			array( 'any' => __( 'All', 'reports-for-woocommerce' ) ),
			wc_get_product_types(),
			array( 'variation' => __( 'Variations', 'reports-for-woocommerce' ) )
		);
		$links   = array();
		$current = $this->get_current_value( $key, 'any' );
		foreach ( $data as $k => $value ) {
			$url     = add_query_arg( array( $key => $k ) );
			$active  = ( $current === $k ? 'font-weight:bold;color:black;' : '' );
			$links[] = '<a href="' . $url . '" style="text-decoration:none;' . $active . '">' .
				$value .
			'</a>';
		}
		return $this->get_menu( __( 'Product Type', 'reports-for-woocommerce' ), $links );
	}

	/**
	 * get_product_type.
	 *
	 * @version 1.2.0
	 * @since   1.0.0
	 */
	function get_product_type() {
		return $this->get_current_value( 'product_type', 'any' );
	}

	/**
	 * get_order_status_menu.
	 *
	 * @version 1.5.0
	 * @since   1.0.0
	 *
	 * @todo    (dev) option to select multiple statuses
	 */
	function get_order_status_menu() {
		if ( ! apply_filters( 'alg_wc_reports_menu_order_status', true ) ) {
			return '';
		}
		$key     = 'order_status';
		$data    = array_merge(
			array( 'wc-any' => __( 'All', 'reports-for-woocommerce' ) ),
			wc_get_order_statuses()
		);
		$links   = array();
		$current = $this->get_current_value( $key, 'any' );
		foreach ( $data as $k => $value ) {
			$k       = substr( $k, 3 );
			$url     = add_query_arg( array( $key => $k ) );
			$active  = ( $current === $k ? 'font-weight:bold;color:black;' : '' );
			$links[] = '<a href="' . $url . '" style="text-decoration:none;' . $active . '">' .
				$value .
			'</a>';
		}
		return $this->get_menu( __( 'Order status', 'reports-for-woocommerce' ), $links );
	}

	/**
	 * get_order_status.
	 *
	 * @version 1.2.0
	 * @since   1.0.0
	 */
	function get_order_status() {
		return $this->get_current_value( 'order_status', 'any' );
	}

	/**
	 * get_data_scale_menu.
	 *
	 * @version 1.2.0
	 * @since   1.0.0
	 *
	 * @todo    (dev) fully customizable `$scale` (input)
	 */
	function get_data_scale_menu() {
		$key  = 'data_scale';
		$data = array(
			array(
				'key'        => 1,
				'title'      => 1,
			),
			array(
				'key'        => 10,
				'title'      => 10,
			),
			array(
				'key'        => 100,
				'title'      => 100,
			),
			array(
				'key'        => 1000,
				'title'      => 1000,
			),
			array(
				'key'        => 10000,
				'title'      => 10000,
			),
			array(
				'key'        => 100000,
				'title'      => 100000,
			),
		);
		$links   = array();
		$current = $this->get_current_value( $key, 10 );
		foreach ( $data as $value ) {
			$url     = add_query_arg( array( $key => $value['key'] ) );
			$active  = ( $current == $value['key'] ? 'font-weight:bold;color:black;' : '' );
			$links[] = '<a href="' . $url . '" style="text-decoration:none;' . $active . '">' .
				$value['title'] .
			'</a>';
		}
		return $this->get_menu( __( 'Scale', 'reports-for-woocommerce' ), $links );
	}

	/**
	 * get_data_scale.
	 *
	 * @version 1.2.0
	 * @since   1.0.0
	 */
	function get_data_scale() {
		return $this->get_current_value( 'data_scale', 10 );
	}

	/**
	 * get_data_type_menu.
	 *
	 * @version 1.2.0
	 * @since   1.0.0
	 */
	function get_data_type_menu() {
		$key  = 'data_type';
		$data = array(
			array(
				'key'        => 'country',
				'title'      => __( 'Countries', 'reports-for-woocommerce' ),
			),
			array(
				'key'        => 'continent',
				'title'      => __( 'Continents', 'reports-for-woocommerce' ),
			),
		);
		$links   = array();
		$current = $this->get_current_value( $key, 'country' );
		foreach ( $data as $value ) {
			$url     = add_query_arg( array( $key => $value['key'] ) );
			$active  = ( $current === $value['key'] ? 'font-weight:bold;color:black;' : '' );
			$links[] = '<a href="' . $url . '" style="text-decoration:none;' . $active . '">' .
				$value['title'] .
			'</a>';
		}
		return $this->get_menu( __( 'Data', 'reports-for-woocommerce' ), $links );
	}

	/**
	 * get_data_type.
	 *
	 * @version 1.2.0
	 * @since   1.0.0
	 */
	function get_data_type() {
		return $this->get_current_value( 'data_type', 'country' );
	}

	/**
	 * get_chart_type_menu.
	 *
	 * @version 1.5.0
	 * @since   1.0.0
	 */
	function get_chart_type_menu( $report ) {
		if ( 'no' === get_option( 'alg_wc_reports_show_chart', 'yes' ) ) {
			return '';
		}
		$key  = 'chart_type';
		$data = array(
			array(
				'key'        => 'bar',
				'title'      => __( 'Bar', 'reports-for-woocommerce' ),
			),
			array(
				'key'        => 'pie',
				'title'      => __( 'Pie', 'reports-for-woocommerce' ),
			),
		);
		if ( in_array( $report, array(
			'alg_wc_report_sales_by_billing_country',
			'alg_wc_report_sales_by_shipping_country',
		) ) ) {
			$data[] = array(
				'key'        => 'map',
				'title'      => __( 'Map', 'reports-for-woocommerce' ),
			);
		}
		if ( in_array( $report, array(
			'alg_wc_report_sales_by_billing_state',
		) ) ) {
			$data[] = array(
				'key'        => 'map_usa',
				'title'      => __( 'Map (USA)', 'reports-for-woocommerce' ),
			);
		}
		$links   = array();
		$current = $this->get_current_value( $key, 'bar' );
		foreach ( $data as $value ) {
			$url     = add_query_arg( array( $key => $value['key'] ) );
			$active  = ( $current === $value['key'] ? 'font-weight:bold;color:black;' : '' );
			$links[] = '<a href="' . $url . '" style="text-decoration:none;' . $active . '">' .
				$value['title'] .
			'</a>';
		}
		return $this->get_menu( __( 'Chart type', 'reports-for-woocommerce' ), $links );
	}

	/**
	 * get_chart_type.
	 *
	 * @version 1.2.0
	 * @since   1.0.0
	 */
	function get_chart_type() {
		return $this->get_current_value( 'chart_type', 'bar' );
	}

	/**
	 * get_date_menu.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 *
	 * @todo    (dev) maybe even more ranges (e.g., "last 36 months" etc.)
	 */
	function get_date_menu() {

		// Fixed dates
		// phpcs:disable WordPress.DateTime.RestrictedFunctions.date_date
		$time   = (int) current_time( 'timestamp' );
		$ranges = array(
			array(
				'key'        => 'last_year',
				'start_date' => date( 'Y-01-01', strtotime( 'last year', $time ) ),
				'end_date'   => date( 'Y-12-31', strtotime( 'last year', $time ) ),
				'title'      => __( 'Last year', 'reports-for-woocommerce' ),
			),
			array(
				'key'        => 'year',
				'start_date' => date( 'Y-01-01', $time ),
				'end_date'   => date( 'Y-m-d', $time ),
				'title'      => __( 'Year', 'reports-for-woocommerce' ),
			),
			array(
				'key'        => 'prev_3_months',
				'start_date' => date( 'Y-m-d', strtotime( '-3 months', $time ) ),
				'end_date'   => date( 'Y-m-d', $time ),
				'title'      => __( 'Last 3 months', 'reports-for-woocommerce' ),
			),
			array(
				'key'        => 'last_month',
				'start_date' => date( 'Y-m-d', strtotime( 'first day of last month', $time ) ),
				'end_date'   => date( 'Y-m-d', strtotime( 'last day of last month', $time ) ),
				'title'      => __( 'Last month', 'reports-for-woocommerce' ),
			),
			array(
				'key'        => 'prev_30_days',
				'start_date' => date( 'Y-m-d', strtotime( '-30 days', $time ) ),
				'end_date'   => date( 'Y-m-d', $time ),
				'title'      => __( 'Last 30 days', 'reports-for-woocommerce' ),
			),
			array(
				'key'        => 'month',
				'start_date' => date( 'Y-m-01', $time ),
				'end_date'   => date( 'Y-m-d', $time ),
				'title'      => __( 'This month', 'reports-for-woocommerce' ),
			),
			array(
				'key'        => '7day',
				'start_date' => date( 'Y-m-d', strtotime( '-6 days', $time ) ),
				'end_date'   => date( 'Y-m-d', $time ),
				'title'      => __( 'Last 7 days', 'reports-for-woocommerce' ),
			),
			array(
				'key'        => 'yesterday',
				'start_date' => date( 'Y-m-d', strtotime( 'yesterday', $time ) ),
				'end_date'   => date( 'Y-m-d', strtotime( 'yesterday', $time ) ),
				'title'      => __( 'Yesterday', 'reports-for-woocommerce' ),
			),
			array(
				'key'        => 'today',
				'start_date' => date( 'Y-m-d', $time ),
				'end_date'   => date( 'Y-m-d', $time ),
				'title'      => __( 'Today', 'reports-for-woocommerce' ),
			),
		);
		// phpcs:enable WordPress.DateTime.RestrictedFunctions.date_date
		$links   = array();
		$current = $this->get_current_value( 'range', '7day' );
		foreach ( $ranges as $range ) {
			$url     = add_query_arg( array( 'range' => $range['key'], 'start_date' => $range['start_date'], 'end_date' => $range['end_date'] ) );
			$active  = ( $current === $range['key'] ? 'font-weight:bold;color:black;' : '' );
			$links[] = '<a href="' . $url . '" style="text-decoration:none;' . $active . '">' .
				$range['title'] .
			'</a>';
		}

		// Custom date range
		$custom_range = '<form method="get" style="margin-top:10px;margin-bottom:10px;">' .
			'<input' .
				' type="date"' .
				' name="start_date"' .
				' value="' . $this->get_current_value( 'start_date', '' ) . '"' .
			'>' .
			'<input' .
				' type="date"' .
				' name="end_date"' .
				' value="' . $this->get_current_value( 'end_date', '' ) . '"' .
			'>' .
			'<input' .
				' type="hidden"' .
				' name="range"' .
				' value="custom"' .
			'>' .
			$this->get_hidden_inputs( array( 'start_date', 'end_date' ) ) .
			'<input' .
				' type="submit"' .
				' class="button"' .
				' value="' . esc_attr__( 'Apply', 'reports-for-woocommerce' ) . '"' .
			'>' .
		'</form>';

		// Result
		return $this->get_menu( __( 'Period', 'reports-for-woocommerce' ), $links, $custom_range );

	}

	/**
	 * get_report_date.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 */
	function get_report_date() {
		if ( isset( $_GET['start_date'], $_GET['end_date'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return sprintf( '%s...%s',
				wc_clean( wp_unslash( $_GET['start_date'] ) ) . ' ' . '00:00:00',   // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended
				wc_clean( wp_unslash( $_GET['end_date'] ) )   . ' ' . '23:59:59' ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended
		} else {
			$time = (int) current_time( 'timestamp' );
			return sprintf( '%s...%s',
				date( 'Y-m-d 00:00:00', strtotime( '-6 days', $time ) ), // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
				date( 'Y-m-d 23:59:59', $time ) );                       // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
		}
	}

	/**
	 * get_current_value.
	 *
	 * @version 2.0.0
	 * @since   1.2.0
	 */
	function get_current_value( $key, $default ) {
		return (
			isset( $_GET[ $key ] ) ? // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			wc_clean( wp_unslash( $_GET[ $key ] ) ) : // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended
			$default
		);
	}

}

endif;

return new Alg_WC_Reports_Menu();
