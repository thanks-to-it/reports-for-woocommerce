<?php
/**
 * Reports for WooCommerce - Countries Class
 *
 * @version 1.2.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Reports_Countries' ) ) :

class Alg_WC_Reports_Countries {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 *
	 * @todo    (dev) `get_canada_provinces() { return array( 'AB', 'BC', 'MB', 'NB', 'NL', 'NT', 'NS', 'NU', 'ON', 'PE', 'QC', 'SK', 'YT' ); }` (https://www.ups.com/worldshiphelp/WS16/ENU/AppHelp/Codes/State_Province_Codes.htm)
	 */
	function __construct() {
		return true;
	}

	/**
	 * get_usa_states.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 *
	 * @see     https://www.ups.com/worldshiphelp/WS16/ENU/AppHelp/Codes/State_Province_Codes.htm
	 */
	function get_usa_states() {
		return array( 'AL', 'AK', 'AZ', 'AR', 'AA', 'AE', 'AP', 'CA', 'CO', 'CT', 'DE', 'DC', 'FL', 'GA',
			'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME', 'MD', 'MA', 'MI', 'MN', 'MS', 'MO', 'MT',
			'NE', 'NV', 'NH', 'NJ', 'NM', 'NY', 'NC', 'ND', 'OH', 'OK', 'OR', 'PA', 'RI', 'SC', 'SD', 'TN',
			'TX', 'UT', 'VT', 'VA', 'WA', 'WV', 'WI', 'WY' );
	}

	/**
	 * get_continent_center_country.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function get_continent_center_country( $continent ) {
		$data = array(
			__( 'Africa', 'reports-for-woocommerce' )              => 'CF',
			__( 'Asia', 'reports-for-woocommerce' )                => 'CN',
			__( 'Australia & Oceania', 'reports-for-woocommerce' ) => 'AU',
			__( 'Europe', 'reports-for-woocommerce' )              => 'CZ',
			__( 'Central America', 'reports-for-woocommerce' )     => 'BM',
			__( 'North America', 'reports-for-woocommerce' )       => 'US',
			__( 'South America', 'reports-for-woocommerce' )       => 'BR',
		);
		return ( isset( $data[ $continent ] ) ? $data[ $continent ] : '' );
	}

	/**
	 * get_country_continent.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function get_country_continent( $country ) {
		$data = array(
			__( 'North America', 'reports-for-woocommerce' )       => array( 'CA', 'MX', 'US' ),
			__( 'Europe', 'reports-for-woocommerce' )              => array( 'AD', 'AL', 'AT', 'AX', 'BA', 'BE', 'BG', 'BY', 'CH', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI',
				'FO', 'FR', 'FX', 'GB', 'GG', 'GI', 'GR', 'HR', 'HU', 'IE', 'IM', 'IS', 'IT', 'JE', 'LI', 'LT', 'LU', 'LV', 'MC', 'MD', 'ME', 'MK', 'MT', 'NL', 'NO',
				'PL', 'PT', 'RO', 'RS', 'RU', 'SE', 'SI', 'SJ', 'SK', 'SM', 'TR', 'UA', 'VA' ),
			__( 'Asia', 'reports-for-woocommerce' )                => array( 'AE', 'AF', 'AM', 'AP', 'AZ', 'BD', 'BH', 'BN', 'BT', 'CC', 'CY', 'CN', 'CX', 'GE', 'HK',
				'ID', 'IL', 'IN', 'IO', 'IQ', 'IR', 'YE', 'JO', 'JP', 'KG', 'KH', 'KP', 'KR', 'KW', 'KZ', 'LA', 'LB', 'LK', 'MY', 'MM', 'MN', 'MO', 'MV', 'NP', 'OM',
				'PH', 'PK', 'PS', 'QA', 'SA', 'SG', 'SY', 'TH', 'TJ', 'TL', 'TM', 'TW', 'UZ', 'VN' ),
			__( 'Australia & Oceania', 'reports-for-woocommerce' ) => array( 'AS', 'AU', 'CK', 'FJ', 'FM', 'GU', 'KI', 'MH', 'MP', 'NC', 'NF', 'NR', 'NU', 'NZ', 'PF',
				'PG', 'PN', 'PW', 'SB', 'TK', 'TO', 'TV', 'UM', 'VU', 'WF', 'WS' ),
			__( 'Central America', 'reports-for-woocommerce' )     => array( 'AG', 'AI', 'AN', 'AW', 'BB', 'BL', 'BM', 'BS', 'BZ', 'CR', 'CU', 'DM', 'DO', 'GD', 'GL',
				'GP', 'GT', 'HN', 'HT', 'JM', 'KY', 'KN', 'LC', 'MF', 'MQ', 'MS', 'NI', 'PA', 'PM', 'PR', 'SV', 'TC', 'TT', 'VC', 'VG', 'VI' ),
			__( 'South America', 'reports-for-woocommerce' )       => array( 'AR', 'BO', 'BR', 'CL', 'CO', 'EC', 'FK', 'GF', 'GY', 'GY', 'PE', 'PY', 'SR', 'UY', 'VE' ),
			__( 'Africa', 'reports-for-woocommerce' )              => array( 'AO', 'BF', 'BI', 'BJ', 'BW', 'CD', 'CF', 'CG', 'CI', 'CM', 'CV', 'DJ', 'DZ', 'EG', 'EH',
				'ER', 'ET', 'GA', 'GH', 'GM', 'GN', 'GQ', 'GW', 'YT', 'KE', 'KM', 'LY', 'LR', 'LS', 'MA', 'MG', 'ML', 'MR', 'MU', 'MW', 'MZ', 'NA', 'NE', 'NG', 'RE',
				'RW', 'SC', 'SD', 'SH', 'SL', 'SN', 'SO', 'ST', 'SZ', 'TD', 'TG', 'TN', 'TZ', 'UG', 'ZA', 'ZM', 'ZW' ),
		);
		foreach ( $data as $continent => $countries ) {
			if ( in_array( $country, $countries ) ) {
				return $continent;
			}
		}
		return '';
	}

	/**
	 * country_code_alpha2_to_alpha3.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function country_code_alpha2_to_alpha3( $alpha2 ) {
		$data = array(
			'AF' => 'AFG',
			'AX' => 'ALA',
			'AL' => 'ALB',
			'DZ' => 'DZA',
			'AS' => 'ASM',
			'AD' => 'AND',
			'AO' => 'AGO',
			'AI' => 'AIA',
			'AQ' => 'ATA',
			'AG' => 'ATG',
			'AR' => 'ARG',
			'AM' => 'ARM',
			'AW' => 'ABW',
			'AU' => 'AUS',
			'AT' => 'AUT',
			'AZ' => 'AZE',
			'BS' => 'BHS',
			'BH' => 'BHR',
			'BD' => 'BGD',
			'BB' => 'BRB',
			'BY' => 'BLR',
			'BE' => 'BEL',
			'BZ' => 'BLZ',
			'BJ' => 'BEN',
			'BM' => 'BMU',
			'BT' => 'BTN',
			'BO' => 'BOL',
			'BA' => 'BIH',
			'BW' => 'BWA',
			'BV' => 'BVT',
			'BR' => 'BRA',
			'VG' => 'VGB',
			'IO' => 'IOT',
			'BN' => 'BRN',
			'BG' => 'BGR',
			'BF' => 'BFA',
			'BI' => 'BDI',
			'KH' => 'KHM',
			'CM' => 'CMR',
			'CA' => 'CAN',
			'CV' => 'CPV',
			'KY' => 'CYM',
			'CF' => 'CAF',
			'TD' => 'TCD',
			'CL' => 'CHL',
			'CN' => 'CHN',
			'HK' => 'HKG',
			'MO' => 'MAC',
			'CX' => 'CXR',
			'CC' => 'CCK',
			'CO' => 'COL',
			'KM' => 'COM',
			'CG' => 'COG',
			'CD' => 'COD',
			'CK' => 'COK',
			'CR' => 'CRI',
			'CI' => 'CIV',
			'HR' => 'HRV',
			'CU' => 'CUB',
			'CY' => 'CYP',
			'CZ' => 'CZE',
			'DK' => 'DNK',
			'DJ' => 'DJI',
			'DM' => 'DMA',
			'DO' => 'DOM',
			'EC' => 'ECU',
			'EG' => 'EGY',
			'SV' => 'SLV',
			'GQ' => 'GNQ',
			'ER' => 'ERI',
			'EE' => 'EST',
			'ET' => 'ETH',
			'FK' => 'FLK',
			'FO' => 'FRO',
			'FJ' => 'FJI',
			'FI' => 'FIN',
			'FR' => 'FRA',
			'GF' => 'GUF',
			'PF' => 'PYF',
			'TF' => 'ATF',
			'GA' => 'GAB',
			'GM' => 'GMB',
			'GE' => 'GEO',
			'DE' => 'DEU',
			'GH' => 'GHA',
			'GI' => 'GIB',
			'GR' => 'GRC',
			'GL' => 'GRL',
			'GD' => 'GRD',
			'GP' => 'GLP',
			'GU' => 'GUM',
			'GT' => 'GTM',
			'GG' => 'GGY',
			'GN' => 'GIN',
			'GW' => 'GNB',
			'GY' => 'GUY',
			'HT' => 'HTI',
			'HM' => 'HMD',
			'VA' => 'VAT',
			'HN' => 'HND',
			'HU' => 'HUN',
			'IS' => 'ISL',
			'IN' => 'IND',
			'ID' => 'IDN',
			'IR' => 'IRN',
			'IQ' => 'IRQ',
			'IE' => 'IRL',
			'IM' => 'IMN',
			'IL' => 'ISR',
			'IT' => 'ITA',
			'JM' => 'JAM',
			'JP' => 'JPN',
			'JE' => 'JEY',
			'JO' => 'JOR',
			'KZ' => 'KAZ',
			'KE' => 'KEN',
			'KI' => 'KIR',
			'KP' => 'PRK',
			'KR' => 'KOR',
			'KW' => 'KWT',
			'KG' => 'KGZ',
			'LA' => 'LAO',
			'LV' => 'LVA',
			'LB' => 'LBN',
			'LS' => 'LSO',
			'LR' => 'LBR',
			'LY' => 'LBY',
			'LI' => 'LIE',
			'LT' => 'LTU',
			'LU' => 'LUX',
			'MK' => 'MKD',
			'MG' => 'MDG',
			'MW' => 'MWI',
			'MY' => 'MYS',
			'MV' => 'MDV',
			'ML' => 'MLI',
			'MT' => 'MLT',
			'MH' => 'MHL',
			'MQ' => 'MTQ',
			'MR' => 'MRT',
			'MU' => 'MUS',
			'YT' => 'MYT',
			'MX' => 'MEX',
			'FM' => 'FSM',
			'MD' => 'MDA',
			'MC' => 'MCO',
			'MN' => 'MNG',
			'ME' => 'MNE',
			'MS' => 'MSR',
			'MA' => 'MAR',
			'MZ' => 'MOZ',
			'MM' => 'MMR',
			'NA' => 'NAM',
			'NR' => 'NRU',
			'NP' => 'NPL',
			'NL' => 'NLD',
			'AN' => 'ANT',
			'NC' => 'NCL',
			'NZ' => 'NZL',
			'NI' => 'NIC',
			'NE' => 'NER',
			'NG' => 'NGA',
			'NU' => 'NIU',
			'NF' => 'NFK',
			'MP' => 'MNP',
			'NO' => 'NOR',
			'OM' => 'OMN',
			'PK' => 'PAK',
			'PW' => 'PLW',
			'PS' => 'PSE',
			'PA' => 'PAN',
			'PG' => 'PNG',
			'PY' => 'PRY',
			'PE' => 'PER',
			'PH' => 'PHL',
			'PN' => 'PCN',
			'PL' => 'POL',
			'PT' => 'PRT',
			'PR' => 'PRI',
			'QA' => 'QAT',
			'RE' => 'REU',
			'RO' => 'ROU',
			'RU' => 'RUS',
			'RW' => 'RWA',
			'BL' => 'BLM',
			'SH' => 'SHN',
			'KN' => 'KNA',
			'LC' => 'LCA',
			'MF' => 'MAF',
			'PM' => 'SPM',
			'VC' => 'VCT',
			'WS' => 'WSM',
			'SM' => 'SMR',
			'ST' => 'STP',
			'SA' => 'SAU',
			'SN' => 'SEN',
			'RS' => 'SRB',
			'SC' => 'SYC',
			'SL' => 'SLE',
			'SG' => 'SGP',
			'SK' => 'SVK',
			'SI' => 'SVN',
			'SB' => 'SLB',
			'SO' => 'SOM',
			'ZA' => 'ZAF',
			'GS' => 'SGS',
			'SS' => 'SSD',
			'ES' => 'ESP',
			'LK' => 'LKA',
			'SD' => 'SDN',
			'SR' => 'SUR',
			'SJ' => 'SJM',
			'SZ' => 'SWZ',
			'SE' => 'SWE',
			'CH' => 'CHE',
			'SY' => 'SYR',
			'TW' => 'TWN',
			'TJ' => 'TJK',
			'TZ' => 'TZA',
			'TH' => 'THA',
			'TL' => 'TLS',
			'TG' => 'TGO',
			'TK' => 'TKL',
			'TO' => 'TON',
			'TT' => 'TTO',
			'TN' => 'TUN',
			'TR' => 'TUR',
			'TM' => 'TKM',
			'TC' => 'TCA',
			'TV' => 'TUV',
			'UG' => 'UGA',
			'UA' => 'UKR',
			'AE' => 'ARE',
			'GB' => 'GBR',
			'US' => 'USA',
			'UM' => 'UMI',
			'UY' => 'URY',
			'UZ' => 'UZB',
			'VU' => 'VUT',
			'VE' => 'VEN',
			'VN' => 'VNM',
			'VI' => 'VIR',
			'WF' => 'WLF',
			'EH' => 'ESH',
			'YE' => 'YEM',
			'ZM' => 'ZMB',
			'ZW' => 'ZWE',
		);
		return ( isset( $data[ $alpha2 ] ) ? $data[ $alpha2 ] : '' );
	}

}

endif;

return new Alg_WC_Reports_Countries();
