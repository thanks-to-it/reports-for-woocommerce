<?php
/**
 * Reports for WooCommerce - Report - Products
 *
 * @version 2.0.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Report_Products' ) ) :

class Alg_WC_Report_Products extends Alg_WC_Report {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function __construct() {
		$this->id    = 'products';
		$this->title = __( 'Products', 'reports-for-woocommerce' );
	}

	/**
	 * get_reports.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 *
	 * @todo    (dev) reports by predefined meta (more) (i.e., `_wc_average_rating` etc.)
	 * @todo    (dev) reports by custom taxonomy
	 * @todo    (dev) reports by custom meta
	 * @todo    (dev) reports by custom attribute
	 */
	function get_reports() {
		return array(
			'alg_wc_report_products_by_price'           => __( 'Products by price', 'reports-for-woocommerce' ),
			'alg_wc_report_products_by_weight'          => __( 'Products by weight', 'reports-for-woocommerce' ),
			'alg_wc_report_products_by_length'          => __( 'Products by length', 'reports-for-woocommerce' ),
			'alg_wc_report_products_by_width'           => __( 'Products by width', 'reports-for-woocommerce' ),
			'alg_wc_report_products_by_height'          => __( 'Products by height', 'reports-for-woocommerce' ),
			'alg_wc_report_products_by_wc_review_count' => __( 'Products by review count', 'reports-for-woocommerce' ),
			'alg_wc_report_products_by_category'        => __( 'Products by category', 'reports-for-woocommerce' ),
			'alg_wc_report_products_by_tag'             => __( 'Products by tag', 'reports-for-woocommerce' ),
		);
	}

	/**
	 * get_data.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 */
	function get_data( $name ) {
		switch ( $name ) {

			case 'alg_wc_report_products_by_price':
				$data = $this->get_data_products_by_price();
				break;

			case 'alg_wc_report_products_by_category':
				$data = $this->get_data_products_by_term( 'product_cat' );
				break;

			case 'alg_wc_report_products_by_tag':
				$data = $this->get_data_products_by_term( 'product_tag' );
				break;

			default:
				$data = $this->get_data_products_by_meta(
					str_replace( 'alg_wc_report_products_by', '', $name )
				);

		}
		return array( 'data' => $data, 'detailed_data' => array() );
	}

	/**
	 * get_products.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function get_products() {
		$type = $this->get_menu()->get_product_type();
		return wc_get_products( array(
			'type'   => (
				'any' === $type ?
				array_merge( array( 'variation' ), array_keys( wc_get_product_types() ) ) :
				$type
			),
			'limit'  => -1,
			'return' => 'ids',
		) );
	}

	/**
	 * get_data_products_by_term.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function get_data_products_by_term( $taxonomy ) {
		$data = array();
		foreach ( $this->get_products() as $product_id ) {
			$terms = get_the_terms( $product_id, $taxonomy );
			if ( ! empty( $terms ) ) {
				foreach( $terms as $term ) {
					$value = $term->name;
					if ( ! isset( $data[ $value ] ) ) {
						$data[ $value ] = 0;
					}
					$data[ $value ]++;
				}
			}
		}
		arsort( $data );
		return $data;
	}

	/**
	 * get_data_products_by_price.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function get_data_products_by_price() {

		$scale = $this->get_menu()->get_data_scale();
		$data  = array();
		foreach ( $this->get_products() as $product_id ) {
			$product = wc_get_product( $product_id );
			$value   = $product->get_price();
			if ( '' !== $value ) {
				$value = intval( ceil( $value / $scale ) );
			}
			if ( ! isset( $data[ $value ] ) ) {
				$data[ $value ] = 0;
			}
			$data[ $value ]++;
		}
		ksort( $data );

		$modified_data = array();
		foreach ( $data as $key => $value ) {
			if ( 0 === $key ) {
				$new_key = __( 'free', 'reports-for-woocommerce' );
			} elseif ( '' === $key ) {
				$new_key = __( 'empty', 'reports-for-woocommerce' );
			} else {
				$new_key = sprintf(
					'%s-%s %s',
					( ( $key - 1 ) * $scale ),
					( $key * $scale ),
					get_woocommerce_currency()
				);
			}
			$modified_data[ $new_key ] = $value;
		}
		$data = $modified_data;

		return $data;
	}

	/**
	 * get_data_products_by_meta.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function get_data_products_by_meta( $meta_key ) {

		$scale = $this->get_menu()->get_data_scale();
		$data  = array();
		foreach ( $this->get_products() as $product_id ) {
			$value = get_post_meta( $product_id, $meta_key, true );
			if ( '' !== $value ) {
				$value = intval( ceil( $value / $scale ) );
			}
			if ( ! isset( $data[ $value ] ) ) {
				$data[ $value ] = 0;
			}
			$data[ $value ]++;
		}
		ksort( $data );

		$modified_data = array();
		foreach ( $data as $key => $value ) {
			if ( 0 === $key ) {
				$new_key = $key;
			} elseif ( '' === $key ) {
				$new_key = __( 'empty', 'reports-for-woocommerce' );
			} else {
				$new_key = sprintf(
					'%s - %s',
					( ( $key - 1 ) * $scale ),
					( $key * $scale )
				);
			}
			$modified_data[ $new_key ] = $value;
		}
		$data = $modified_data;

		return $data;
	}

}

endif;

return new Alg_WC_Report_Products();
