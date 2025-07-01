<?php
/**
 * Reports for WooCommerce - Draw Class
 *
 * @version 2.0.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Reports_Draw' ) ) :

class Alg_WC_Reports_Draw {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function __construct() {
		return true;
	}

	/**
	 * map.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 *
	 * @see     http://datamaps.github.io/
	 */
	function map( $data, $scope, $is_continent ) {

		$country_data = array();
		$total        = array_sum( $data );
		foreach ( $data as $key => $value ) {
			$value_percent = round( $value / $total * 100 );
			$country_data[] = array(
				'name'     => $key . ' - ' . $value . ' - ' . $value_percent . '%',
				'radius'   => $value_percent,
				'centered' => ( 'map_usa' === $scope ?
					$key :
					alg_wc_reports()->core->countries->country_code_alpha2_to_alpha3(
						(
							$is_continent ?
							alg_wc_reports()->core->countries->get_continent_center_country( $key ) :
							$key
						)
					)
				),
				'fillKey'  => 'red',
			);
		}

		?><div id="alg-wc-reports-bubbles" style="width:1024px;height:600px;background-color:white;"></div><?php

		ob_start();
		?>
		var alg_wc_reports_bubble_map = new Datamap( {
			element: document.getElementById( 'alg-wc-reports-bubbles' ),
			scope: '<?php echo esc_js( $scope ); ?>',
			geographyConfig: {
				popupOnHover:     true,
				highlightOnHover: true,
			},
			fills: {
				defaultFill: '#ABDDA4',
				red:         'red',
			},
			bubblesConfig: {
				borderColor: 'red',
			},
		} );
		alg_wc_reports_bubble_map.bubbles( <?php echo json_encode( $country_data ); ?>, {
			popupTemplate: function( geo, data ) {
				return '<div class="hoverinfo">' + data.name + '</div>';
			}
		} );
		<?php
		wp_add_inline_script( 'alg-wc-reports-datamaps-all', ob_get_clean() );

	}

	/**
	 * get_chart_colors.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function get_chart_colors( $count ) {
		$default_colors = array(
			array( 'rgba(54, 162, 235, 0.2)',  'rgba(54, 162, 235, 1)'  ),
			array( 'rgba(255, 99, 132, 0.2)',  'rgba(255, 99, 132, 1)'    ),
			array( 'rgba(255, 206, 86, 0.2)',  'rgba(255, 206, 86, 1)'  ),
			array( 'rgba(75, 192, 192, 0.2)',  'rgba(75, 192, 192, 1)'  ),
			array( 'rgba(153, 102, 255, 0.2)', 'rgba(153, 102, 255, 1)' ),
			array( 'rgba(255, 159, 64, 0.2)',  'rgba(255, 159, 64, 1)'  ),
		);
		$colors = array(
			'background' => array(),
			'border'     => array(),
		);
		for ( $i = 0; $i < $count; $i++ ) {
			$z = $i % count( $default_colors );
			$colors['background'][] = $default_colors[ $z ][0];
			$colors['border'][]     = $default_colors[ $z ][1];
		}
		return $colors;
	}

	/**
	 * chart.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 *
	 * @see     http://www.chartjs.org
	 */
	function chart( $type, $data, $label ) {

		$colors = $this->get_chart_colors( count( $data ) );

		?>
		<div class="chart-container" style="position:relative;width:50vw;background-color:white;">
			<canvas id="alg-wc-reports-chart"></canvas>
		</div>
		<?php

		ob_start();
		?>
		var ctx = document.getElementById( 'alg-wc-reports-chart' ).getContext( '2d' );
		var alg_wc_reports_chart = new Chart( ctx, {
			type: '<?php echo esc_js( $type ); ?>',
			data: {
				labels: <?php echo json_encode( array_keys( $data ) ); ?>,
				datasets: [ {
					label:           '<?php echo esc_js( $label ); ?>',
					data:            <?php echo json_encode( array_values( $data ) ); ?>,
					backgroundColor: <?php echo json_encode( $colors['background'] ); ?>,
					borderColor:     <?php echo json_encode( $colors['border'] ); ?>,
					borderWidth:     1,
				} ]
			},
			options: {
				scales: {
					yAxes: [ {
						ticks: {
							beginAtZero:true
						}
					} ]
				}
			},
		} );
		<?php
		wp_add_inline_script( 'alg-wc-reports-chart', ob_get_clean() );

	}

}

endif;

return new Alg_WC_Reports_Draw();
