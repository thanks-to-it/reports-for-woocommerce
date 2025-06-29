<?php
/**
 * Reports for WooCommerce - Report - Orders
 *
 * @version 2.0.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Report_Orders' ) ) :

class Alg_WC_Report_Orders extends Alg_WC_Report {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function __construct() {
		$this->id    = 'orders';
		$this->title = __( 'Orders', 'reports-for-woocommerce' );
	}

	/**
	 * get_reports.
	 *
	 * @version 1.2.0
	 * @since   1.0.0
	 *
	 * @todo    (dev) rename all from `alg_wc_report_sales_` to `alg_wc_report_orders_`
	 * @todo    (feature) more country maps (i.e., except USA)
	 * @todo    (feature) reports by custom meta (e.g., '_billing_city')
	 */
	function get_reports() {
		return array(
			'alg_wc_report_sales_by_payment_method_title' => __( 'Sales by payment gateways', 'reports-for-woocommerce' ),
			'alg_wc_report_sales_by_billing_country'      => __( 'Sales by billing country', 'reports-for-woocommerce' ),
			'alg_wc_report_sales_by_billing_state'        => __( 'Sales by billing state', 'reports-for-woocommerce' ),
			'alg_wc_report_sales_by_billing_city'         => __( 'Sales by billing city', 'reports-for-woocommerce' ),
			'alg_wc_report_sales_by_shipping_country'     => __( 'Sales by shipping country', 'reports-for-woocommerce' ),
			'alg_wc_report_sales_by_order_currency'       => __( 'Sales by order currency', 'reports-for-woocommerce' ),
			'alg_wc_report_sales_by_customer_user'        => __( 'Sales by customer', 'reports-for-woocommerce' ),
			'alg_wc_report_sales_by_product'              => __( 'Sales by product', 'reports-for-woocommerce' ),
			'alg_wc_report_sales_by_product_cat'          => __( 'Sales by product category', 'reports-for-woocommerce' ),
		);
	}

	/**
	 * get_data.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 */
	function get_data( $name ) {
		$data = apply_filters(
			'alg_wc_reports_orders_data',
			$this->get_data_sales_by_meta( str_replace( array( 'alg_wc_report_sales_by' ), '', $name ) ),
			$name,
			$this
		);
		return (
			! empty( $data['data'] ) ?
			array(
				'data'          => $this->prepare_data( $data['data'], $name ),
				'detailed_data' => ( $data['detailed_data'] ?? array() ),
			) :
			$data
		);
	}

	/**
	 * get_data_sales_by_meta.
	 *
	 * @version 1.7.0
	 * @since   1.0.0
	 *
	 * @see     https://github.com/woocommerce/woocommerce/wiki/wc_get_orders-and-WC_Order_Query
	 *
	 * @todo    (dev) `$detailed_data_row[] = $order->get_date_created()` must match `get_option( 'alg_wc_reports_orders_date_range', 'date_created' )`?
	 * @todo    (dev) `orderby = date`                                    must match `get_option( 'alg_wc_reports_orders_date_range', 'date_created' )`?
	 * @todo    (dev) code refactoring
	 * @todo    (dev) `product_id` or `variation_id`
	 * @todo    (dev) rename (not really `by_meta`)
	 */
	function get_data_sales_by_meta( $meta_key ) {

		if ( apply_filters( 'alg_wc_reports_orders_is_custom_data', false, $meta_key, $this ) ) {
			return array( 'data' => array(), 'detailed_data' => array() );
		}

		// Prepare vars
		$data                = array();
		$detailed_data       = array();
		$detailed_data_keys  = array();
		$do_skip_zero_orders = ( 'yes' === get_option( 'alg_wc_reports_skip_zero_orders', 'no' ) );
		$item_data_type      = $this->get_menu()->get_item_data_type();
		$product_cat         = ( 'single'   === get_option( 'alg_wc_reports_menu_product_cat', 'multiple' ) ? $this->get_menu()->get_product_cat()  : '' );
		$product_cats        = ( 'multiple' === get_option( 'alg_wc_reports_menu_product_cat', 'multiple' ) ? $this->get_menu()->get_product_cats() : array() );
		$product_cats        = ( ! empty( $product_cats ) ? $product_cats : ( '' !== $product_cat ? array( $product_cat ) : array() ) );
		$date_range          = get_option( 'alg_wc_reports_orders_date_range', 'date_created' );
		$orderby             = get_option( 'alg_wc_reports_orders_orderby', 'date' );
		$orderby_order       = get_option( 'alg_wc_reports_orders_orderby_order', 'DESC' );

		// Get orders
		$orders = wc_get_orders(
			apply_filters(
				'alg_wc_reports_orders_get_data_sales_by_meta_args',
				array(
					'type'         => 'shop_order',
					'limit'        => -1,
					'return'       => 'ids',
					$date_range    => $this->get_menu()->get_report_date(),
					'status'       => $this->get_menu()->get_order_status(),
					'orderby'      => $orderby,
					'order'        => $orderby_order,
				),
				$meta_key,
				$this
			)
		);

		// Orders loop
		foreach ( $orders as $order_id ) {
			$order = wc_get_order( $order_id );

			// Skip orders
			if (
				! apply_filters( 'alg_wc_reports_orders_get_data_sales_by_meta_order', true, $order, $this ) ||
				( $do_skip_zero_orders && 0 == $order->get_total( 'edit' ) )
			) {
				continue;
			}

			// Detailed data row (start)
			$detailed_data_row = array();
			$detailed_data_row[] = '<a href="' . admin_url( 'post.php?post=' . $order->get_id() . '&action=edit' ) . '">' . $order->get_order_number() . '</a>';
			$detailed_data_row[] = $order->get_date_created()->date( 'Y-m-d' );
			$detailed_data_row[] = str_replace( ',', ' ', $order->get_billing_first_name() );
			$detailed_data_row[] = str_replace( ',', ' ', $order->get_billing_last_name() );
			$detailed_data_row[] = str_replace( ',', ' ', $order->get_billing_company() );
			$detailed_data_row[] = str_replace( ',', ' ', implode( ' ', array( $order->get_billing_address_1(), $order->get_billing_address_2() ) ) );
			$detailed_data_row[] = str_replace( ',', ' ', $order->get_billing_city() );
			$detailed_data_row[] = str_replace( ',', ' ', $order->get_billing_state() );
			$detailed_data_row[] = str_replace( ',', ' ', $order->get_billing_postcode() );
			$detailed_data_row[] = str_replace( ',', ' ', $order->get_billing_country() );

			if ( in_array( $meta_key, array( '_product', '_product_cat' ) ) ) {

				// Order product reports
				$order_data = array();
				foreach ( $order->get_items() as $item_id => $item ) {
					if ( '_product' === $meta_key ) {
						$value = sprintf( '%s (#%d)', $item['name'], $item['product_id'] );
						$res   = apply_filters( 'alg_wc_reports_orders_item_value', $item[ $item_data_type ],
							array( 'item_data_type' => $item_data_type, 'item_id' => $item_id, 'item' => $item, 'order_id' => $order_id, 'order' => $order, 'orders_report' => $this ) );
						if ( ! isset( $data[ $value ] ) ) {
							$data[ $value ] = 0;
						}
						$data[ $value ] += $res;
						$detailed_data_keys[] = $value;
						if ( ! isset( $order_data[ $value ] ) ) {
							$order_data[ $value ] = 0;
						}
						$order_data[ $value ] += $res;
					} elseif ( '_product_cat' === $meta_key ) {
						$terms = get_the_terms( $item['product_id'], 'product_cat' );
						if ( $terms && ! is_wp_error( $terms ) ) {
							foreach ( $terms as $term ) {
								if ( ! empty( $product_cats ) && ! in_array( $term->term_id, $product_cats ) ) {
									continue;
								}
								$value = sprintf( '%s (#%d)', $term->name, $term->term_id );
								$res   = apply_filters( 'alg_wc_reports_orders_item_value', $item[ $item_data_type ],
									array( 'item_data_type' => $item_data_type, 'item_id' => $item_id, 'item' => $item, 'order_id' => $order_id, 'order' => $order, 'orders_report' => $this ) );
								if ( ! isset( $data[ $value ] ) ) {
									$data[ $value ] = 0;
								}
								$data[ $value ] += $res;
								$detailed_data_keys[] = $value;
								if ( ! isset( $order_data[ $value ] ) ) {
									$order_data[ $value ] = 0;
								}
								$order_data[ $value ] += $res;
							}
						}
					}
				}
				if ( empty( $order_data ) ) {
					continue;
				}
				$detailed_data_row['raw_data'] = $order_data;

			} else {

				// Order meta reports
				$value = $order->get_meta( $meta_key );
				if ( ! isset( $data[ $value ] ) ) {
					$data[ $value ] = 0;
				}
				$data[ $value ]++;
				$detailed_data_keys[] = __( 'Value', 'reports-for-woocommerce' );
				$detailed_data_row[]  = $value;

			}

			// Detailed data (add row)
			$detailed_data[] = apply_filters( 'alg_wc_reports_orders_detailed_data_row', $detailed_data_row,
				array( 'order_id' => $order_id, 'order' => $order, 'orders_report' => $this ) );

		}

		// Detailed data
		$detailed_data_keys = apply_filters( 'alg_wc_reports_orders_detailed_data_keys', array_unique( $detailed_data_keys ), array( 'orders_report' => $this ) );
		$heading            = apply_filters( 'alg_wc_reports_orders_detailed_data_heading', array_merge( array(
				__( 'Order', 'reports-for-woocommerce' ),
				__( 'Date', 'reports-for-woocommerce' ),
				__( 'First name', 'reports-for-woocommerce' ),
				__( 'Last name', 'reports-for-woocommerce' ),
				__( 'Company', 'reports-for-woocommerce' ),
				__( 'Address', 'reports-for-woocommerce' ),
				__( 'City', 'reports-for-woocommerce' ),
				__( 'State', 'reports-for-woocommerce' ),
				__( 'ZIP code', 'reports-for-woocommerce' ),
				__( 'Country', 'reports-for-woocommerce' ),
			), $detailed_data_keys ),
			array( 'orders_report' => $this ) );
		$_detailed_data     = array();
		$_detailed_data[]   = $heading;
		foreach ( $detailed_data as $row ) {
			if ( isset( $row['raw_data'] ) ) {
				$raw_data = $row['raw_data'];
				unset( $row['raw_data'] );
				foreach ( $detailed_data_keys as $key ) {
					$row[] = ( isset( $raw_data[ $key ] ) ? $raw_data[ $key ] : '' );
				}
			}
			$_detailed_data[] = $row;
		}
		$detailed_data = $_detailed_data;

		// Sort data
		arsort( $data );

		// Return result
		return array( 'data' => $data, 'detailed_data' => $detailed_data );

	}

	/**
	 * prepare_data.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 *
	 * @todo    (dev) countries - replace with full country name
	 */
	function prepare_data( $data, $name ) {
		$is_continent = ( 'continent' === $this->get_menu()->get_data_type() );
		if ( $is_continent ) {
			$data_by_continent = array();
			foreach ( $data as $key => $value ) {
				$continent = alg_wc_reports()->core->countries->get_country_continent( $key );
				if ( ! isset( $data_by_continent[ $continent ] ) ) {
					$data_by_continent[ $continent ] = 0;
				}
				$data_by_continent[ $continent ] += $value;
			}
			$data = $data_by_continent;
		}
		if ( 'alg_wc_report_sales_by_customer_user' === $name ) {
			$modified_data = array();
			foreach ( $data as $key => $value ) {
				$name = $key;
				if ( $user_info = get_userdata( $key ) ) {
					$name = $user_info->user_login;
				}
				$modified_data[ $name ] = $value;
			}
			$data = $modified_data;
		}
		$chart_type = $this->get_menu()->get_chart_type();
		if ( 'map' === $chart_type || 'map_usa' === $chart_type ) {
			if ( 'map_usa' === $chart_type ) {
				$usa_states = alg_wc_reports()->core->countries->get_usa_states();
				foreach ( $data as $key => $value ) {
					if ( ! in_array( $key, $usa_states ) ) {
						unset( $data[ $key ] );
					}
				}
			}
		}
		return $data;
	}

}

endif;

return new Alg_WC_Report_Orders();
