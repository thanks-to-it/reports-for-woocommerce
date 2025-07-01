<?php
/**
 * Reports for WooCommerce - Core Class
 *
 * @version 2.0.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Reports_Core' ) ) :

class Alg_WC_Reports_Core {

	/**
	 * countries.
	 *
	 * @version 1.7.0
	 * @since   1.7.0
	 */
	public $countries;

	/**
	 * draw.
	 *
	 * @version 1.7.0
	 * @since   1.7.0
	 */
	public $draw;

	/**
	 * menu.
	 *
	 * @version 1.7.0
	 * @since   1.7.0
	 */
	public $menu;

	/**
	 * report_types.
	 *
	 * @version 1.7.0
	 * @since   1.7.0
	 */
	public $report_types = array();

	/**
	 * reports.
	 *
	 * @version 1.7.0
	 * @since   1.7.0
	 */
	public $reports;

	/**
	 * Constructor.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 *
	 * @todo    (dev) Advanced - "Save results in transients"
	 * @todo    (dev) reports caching (e.g., crons)
	 * @todo    (dev) add option to output only data table (i.e., no chart)
	 */
	function __construct() {
		if ( ! is_admin() ) {
			return;
		}
		add_action( 'init', array( $this, 'init' ), 1 );
		add_filter( 'woocommerce_admin_reports', array( $this, 'add_reports' ), 1 );
		add_action( 'admin_init', array( $this, 'export_reports' ), 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'add_scripts' ) );
	}

	/**
	 * init.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 *
	 * @todo    (dev) move `$this->countries` to `Alg_WC_Report_Orders`
	 */
	function init() {

		// Properties
		$this->countries = require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-reports-countries.php';
		$this->draw      = require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-reports-draw.php';
		$this->menu      = require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-reports-menu.php';

		// Reports
		require_once plugin_dir_path( __FILE__ ) . 'abstracts/class-alg-wc-report.php';
		$this->report_types = array( 'orders', 'taxes', 'products' );
		foreach ( $this->report_types as $type ) {
			$this->reports[ $type ] = require_once plugin_dir_path( __FILE__ ) . 'reports/class-alg-wc-report-' . $type . '.php';
		}

	}

	/**
	 * add_scripts.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 *
	 * @todo    (dev) load only needed by `$this->menu->get_chart_type()`
	 * @todo    (dev) `jquery` in deps
	 */
	function add_scripts() {
		if ( isset( $_GET['page'] ) && 'wc-reports' === $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$version = alg_wc_reports()->version;
			$url     = alg_wc_reports()->plugin_url() . '/includes/lib/';

			// Chart
			wp_enqueue_script(
				'alg-wc-reports-chart',
				$url . 'chartjs/Chart.min.js',
				array(),
				$version,
				true
			);

			// Datamaps
			wp_enqueue_script(
				'alg-wc-reports-d3',
				$url . 'datamaps/d3.min.js',
				array(),
				$version,
				true
			);
			wp_enqueue_script(
				'alg-wc-reports-topojson',
				$url . 'datamaps/topojson.min.js',
				array(),
				$version,
				true
			);
			wp_enqueue_script(
				'alg-wc-reports-datamaps-all',
				$url . 'datamaps/datamaps.all.min.js',
				array(),
				$version,
				true
			);

		}
	}

	/**
	 * export_reports.
	 *
	 * E.g.: http://example.com/wp-admin/?alg_wc_reports_export_type=orders&alg_wc_reports_export_name=alg_wc_report_sales_by_product_cat&alg_wc_reports_export_data_type=detailed
	 *
	 * @version 2.0.0
	 * @since   1.2.0
	 *
	 * @todo    (dev) validate `$name`
	 * @todo    (dev) validate `$data_type`
	 */
	function export_reports() {
		if (
			isset(
				$_GET['alg_wc_reports_export_type'],
				$_GET['alg_wc_reports_export_name'],
				$_GET['alg_wc_reports_export_data_type'],
				$_GET['_alg_wc_reports_export_nonce']
			)
		) {
			if (
				! wp_verify_nonce(
					sanitize_text_field( wp_unslash( $_GET['_alg_wc_reports_export_nonce'] ) ),
					'alg_wc_reports_export',
				)
			) {
				wp_die( esc_html__( 'Link expired.', 'reports-for-woocommerce' ) );
			}
			if ( current_user_can( 'manage_woocommerce' ) ) {
				$type      = wc_clean( wp_unslash( $_GET['alg_wc_reports_export_type'] ) );      // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				$name      = wc_clean( wp_unslash( $_GET['alg_wc_reports_export_name'] ) );      // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				$data_type = wc_clean( wp_unslash( $_GET['alg_wc_reports_export_data_type'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				if ( isset( $this->reports[ $type ] ) ) {
					$this->reports[ $type ]->export_report( $name, $data_type );
				}
			}
		}
	}

	/**
	 * add_reports.
	 *
	 * @version 1.7.0
	 * @since   1.0.0
	 *
	 * @todo    (dev) `$reports['stock']['title'] = __( 'Stock & Products', 'reports-for-woocommerce' );`
	 */
	function add_reports( $reports ) {

		foreach ( $this->report_types as $type ) {

			$is_enabled = get_option( 'alg_wc_reports_enabled_section', array() );
			if ( isset( $is_enabled[ $type ] ) && 'no' === $is_enabled[ $type ] ) {
				continue;
			}

			$enabled_reports       = $this->reports[ $type ]->get_enabled_reports();
			$total_enabled_reports = count( $enabled_reports );

			foreach ( $enabled_reports as $report_id => $report_title ) {

				if ( ! isset( $reports[ $type ]['title'] ) ) {
					switch ( $type ) {
						case 'products':
							$reports[ $type ]['title'] = __( 'Products', 'reports-for-woocommerce' );
							break;
						case 'taxes':
							$reports[ $type ]['title'] = __( 'Taxes', 'reports-for-woocommerce' );
							break;
						default:
							$reports[ $type ]['title'] = $type;
					}
				}

				$reports[ $type ]['reports'][ $report_id ] = array(
					'title'       => $report_title,
					'description' => '',
					'hide_title'  => ( $total_enabled_reports > 1 ),
					'callback'    => array( $this->reports[ $type ], 'output_report' ),
				);

			}

		}

		return $reports;

	}

}

endif;

return new Alg_WC_Reports_Core();
