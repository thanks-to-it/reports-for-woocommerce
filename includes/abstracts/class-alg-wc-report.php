<?php
/**
 * Reports for WooCommerce - Report Abstract Class
 *
 * @version 2.0.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Report' ) ) :

abstract class Alg_WC_Report {

	/**
	 * id.
	 *
	 * @version 1.7.0
	 * @since   1.7.0
	 */
	public $id;

	/**
	 * title.
	 *
	 * @version 1.7.0
	 * @since   1.7.0
	 */
	public $title;

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	abstract function __construct();

	/**
	 * get_reports.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	abstract function get_reports();

	/**
	 * get_data.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	abstract function get_data( $name );

	/**
	 * export_report.
	 *
	 * @version 2.0.0
	 * @since   1.2.0
	 *
	 * @todo    (feature) customizable separator
	 * @todo    (dev) wrap in quotes?
	 * @todo    (dev) better filename?
	 * @todo    (dev) `header( 'Content-Length: ' . strlen( $csv ) );`?
	 */
	function export_report( $name, $data_type ) {
		$csv  = '';
		$sep  = apply_filters( 'alg_wc_reports_export_sep', ',' );
		$data = $this->get_data( $name );
		if ( 'detailed' === $data_type ) {
			if ( ! empty( $data['detailed_data'] ) ) {
				foreach ( $data['detailed_data'] as $row ) {
					$csv .= '"' . implode( '"' . $sep . '"', array_map( 'strip_tags', $row ) ) . '"' . PHP_EOL;
				}
			}
		} else { // 'main'
			if ( ! empty( $data['data'] ) ) {
				foreach ( $data['data'] as $key => $value ) {
					$csv .= '"' . $key . '"' . $sep . '"' . $value . '"' . PHP_EOL;
				}
			}
		}
		header( 'Content-Description: File Transfer' );
		header( 'Content-Type: application/octet-stream' );
		header( 'Content-Disposition: attachment; filename=report-' . $name . '-' . $data_type . '.csv' );
		header( 'Content-Transfer-Encoding: binary' );
		header( 'Expires: 0' );
		header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		header( 'Pragma: public' );
		echo wp_kses_post( $csv );
		die();
	}

	/**
	 * output_report.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 *
	 * @todo    (dev) `$saved_menu_selections`: save per `$name`?
	 * @todo    (dev) add `output_detailed_data_table()` function
	 * @todo    (dev) `$saved_menu_selections`: optional?
	 */
	function output_report( $name ) {

		// Save user menu selections
		$current_menu_selections = array();
		foreach ( array( 'item_data_type', 'product_type', 'order_status', 'data_scale', 'data_type', 'chart_type', 'product_cat', 'product_cats' ) as $key ) {
			if ( isset( $_GET[ $key ] ) ) {
				$current_menu_selections[ $key ] = wc_clean( wp_unslash( $_GET[ $key ] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			} elseif ( 'product_cats' === $key && isset( $_GET[ $key . '_submit' ] ) ) {
				$current_menu_selections[ $key ] = array();
			}
		}
		$saved_menu_selections = get_user_meta( get_current_user_id(), '_alg_wc_reports_menu_selections', true );
		$menu_selections       = ( ! empty( $saved_menu_selections ) ? array_replace( $saved_menu_selections, $current_menu_selections ) : $current_menu_selections );
		update_user_meta( get_current_user_id(), '_alg_wc_reports_menu_selections', $menu_selections );

		// Report
		$data = $this->get_data( $name );
		$this->output_header( $data['data'], $name );
		if ( empty( $data['data'] ) ) {
			echo '<div id="message" class="updated inline"><p>' . esc_html__( 'No data.', 'reports-for-woocommerce' ) . '</p></div>';
		} else {
			if ( 'yes' === get_option( 'alg_wc_reports_show_chart', 'yes' ) ) {
				$this->output_chart( $data['data'] );
			}
			if ( 'yes' === get_option( 'alg_wc_reports_show_data_as_table', 'yes' ) ) {
				$this->output_data_table( $data['data'], $name );
			}
			if ( 'yes' === get_option( 'alg_wc_reports_show_detailed_data', 'no' ) ) {
				if ( ! empty( $data['detailed_data'] ) ) {
					echo '<h3>' . esc_html__( 'Detailed Data', 'reports-for-woocommerce' ) . '</h3>' . ' ' . wp_kses_post( $this->get_export_link( $name, 'detailed' ) );
					echo '<table class="widefat striped">';
					foreach ( $data['detailed_data'] as $row ) {
						echo wp_kses_post( '<tr><td>' . implode( '</td><td>', $row ) . '</td></tr>' );
					}
					echo '</table>';
				}
			}
		}
		$this->output_footer();

	}

	/**
	 * output_header.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function output_header( $data, $name ) {
		echo $this->get_menu()->get_menus( $data, $name, $this->id );
	}

	/**
	 * output_chart.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 */
	function output_chart( $data ) {
		echo '<h3>' . esc_html__( 'Report', 'reports-for-woocommerce' ) . '</h3>';
		$chart_type = $this->get_menu()->get_chart_type();
		if ( in_array( $chart_type, array( 'map', 'map_usa' ) ) ) {
			$is_continent = ( 'continent' === $this->get_menu()->get_data_type() );
			alg_wc_reports()->core->draw->map(
				$data,
				( 'map_usa' === $chart_type ? 'usa' : 'world' ),
				$is_continent
			);
		} else {
			$label = (
				'line_total' === $this->get_menu()->get_item_data_type() ?
				__( 'Sum', 'reports-for-woocommerce' ) :
				sprintf(
					/* Translators: %s: Orders/Products/Taxes. */
					__( 'Number of %s', 'reports-for-woocommerce' ),
					$this->title
				)
			);
			alg_wc_reports()->core->draw->chart(
				$chart_type,
				$data,
				$label
			);
		}
	}

	/**
	 * get_export_link.
	 *
	 * @version 1.2.0
	 * @since   1.2.0
	 *
	 * @todo    (dev) rethink `alg_wc_reports_export_type`
	 */
	function get_export_link( $name, $data_type ) {
		return '[<a href="' . add_query_arg( array(
				'alg_wc_reports_export_type'      => strtolower( str_replace( 'Alg_WC_Report_', '', get_class( $this ) ) ),
				'alg_wc_reports_export_name'      => $name,
				'alg_wc_reports_export_data_type' => $data_type,
			) ) . '">' . __( 'Export', 'reports-for-woocommerce' ) . '</a>]';
	}

	/**
	 * output_data_table.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 */
	function output_data_table( $data, $name ) {
		echo (
			'<h3>' . esc_html__( 'Data', 'reports-for-woocommerce' ) . '</h3>' . ' ' .
			wp_kses_post( $this->get_export_link( $name, 'main' ) ) .
			wp_kses_post( $this->format_as_table( $data ) )
		);
	}

	/**
	 * format_as_table.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function format_as_table( $data ) {
		$table_html = '';
		$table_html .= '<table class="widefat striped">';
		foreach ( $data as $key => $value ) {
			$table_html .= "<tr><th>{$key}</th><td>{$value}</td></tr>";
		}
		$table_html .= '</table>';
		return $table_html;
	}

	/**
	 * output_footer.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 */
	function output_footer() {
		echo '<p><em>' .
			sprintf(
				/* Translators: %s: Report settings link. */
				esc_html__( 'Reports settings: %s', 'reports-for-woocommerce' ),
				'<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=alg_wc_reports' ) ) . '">' .
					esc_html__( 'WooCommerce > Settings > Reports', 'reports-for-woocommerce' ) .
				'</a>'
			) .
		'</em></p>';
		if ( in_array( $this->id, array( 'orders', 'taxes' ) ) ) {
			echo '<p><em>' .
				sprintf(
					/* Translators: %s: Report date. */
					esc_html__( 'Report dates: %s', 'reports-for-woocommerce' ),
					'<code>' . wp_kses_post( str_replace( '...', ' > ', $this->get_menu()->get_report_date() ) ) . '</code>'
				) .
			'</em></p>';
		}
	}

	/**
	 * get_enabled_reports.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function get_enabled_reports() {
		$enabled_reports = get_option( 'alg_wc_reports_enabled_reports', array() );
		$enabled_reports = ( isset( $enabled_reports[ $this->id ] ) ? $enabled_reports[ $this->id ] : array() );
		if ( empty( $enabled_reports ) ) {
			return $this->get_reports();
		} else {
			$reports = array();
			foreach ( $this->get_reports() as $report_id => $report_title ) {
				if ( ! in_array( $report_id, $enabled_reports ) ) {
					continue;
				}
				$reports[ $report_id ] = $report_title;
			}
			return $reports;
		}
	}

	/**
	 * get_menu.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function get_menu() {
		return alg_wc_reports()->core->menu;
	}

}

endif;
