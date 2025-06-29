=== Reports for WooCommerce ===
Contributors: algoritmika, thankstoit, anbinder, karzin
Tags: woocommerce, report, reports, ecommerce
Requires at least: 4.4
Tested up to: 6.8
Stable tag: 1.7.2
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Advanced WooCommerce reports.

== Description ==

**Reports for WooCommerce** plugin lets you add advanced reports to your WooCommerce store.

### 🚀 Advanced Reporting for WooCommerce ###

Elevate your WooCommerce store to new heights with the Reports for WooCommerce plugin, designed to bring advanced reporting capabilities right at your fingertips.

Delve deep into your sales and product data with an array of detailed reports that break down every aspect of your business, helping you make informed decisions to steer your store in the right direction.

This plugin affords you a comprehensive view of your sales metrics, broken down by various criteria such as payment gateways, billing and shipping details, order currency, and customer details.

Additionally, it offers a precise analysis of product details categorized by aspects including price, dimensions, review count, and classification into categories and tags.

Enrich your reporting experience with a variety of visualization options to depict your data. Choose from pie charts for a segmented view, bar charts for a comparative analysis, or even a world map to geographically represent your sales data, offering a vibrant and intuitive way to interpret the figures.

Reports for WooCommerce plugin is not just a tool; it's a lens through which you can view your business, comprehending every nuance to foster growth and success.

### ✅ Reports ###

* Sales by payment gateways
* Sales by billing country
* Sales by billing state
* Sales by billing city
* Sales by shipping country
* Sales by order currency
* Sales by customer
* Sales by product
* Sales by product category
* Products by price
* Products by weight
* Products by length
* Products by width
* Products by height
* Products by review count
* Products by category
* Products by tag

### 🗘 Feedback ###

* We are open to your suggestions and feedback. Thank you for using or trying out one of our plugins!
* Head to the plugin [GitHub Repository](https://github.com/thanks-to-it/reports-for-woocommerce) to find out how you can pitch in.

### ℹ More ###

* The plugin is **"High-Performance Order Storage (HPOS)"** compatible.

== Installation ==

1. Upload the entire plugin folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Start by visiting plugin settings at "WooCommerce > Settings > Reports".

== Changelog ==

= 2.0.0 - 26/06/2025 =
* Fix - Translation loading fixed.
* Dev - Security - Output escaped.
* Dev - Security - Input sanitized.
* Dev - Code refactoring.
* Dev - Coding standards improved.
* WC tested up to: 9.9.
* Tested up to: 6.8.

= 1.7.2 - 11/04/2024 =
* Dev - Developers - `alg_wc_reports_taxes_get_data_taxes_by_order_args` filter added.
* Dev - Developers - `alg_wc_reports_taxes_detailed_data_row` filter added.
* Dev - Developers - `alg_wc_reports_taxes_detailed_data_heading` filter added.
* WC tested up to: 9.8.
* Tested up to: 6.7.

= 1.7.1 - 08/09/2024 =
* Dev - Export - Wrapping CSV values in double quotes now.
* WC tested up to: 9.2.
* Tested up to: 6.6.
* WooCommerce added to the "Requires Plugins" (plugin header).

= 1.7.0 - 05/01/2024 =
* Fix - Possible backend "Undefined array key `title`" notice fixed.
* Dev – "High-Performance Order Storage (HPOS)" compatibility.
* Dev - PHP 8.2 compatibility - "Creation of dynamic property is deprecated" notice fixed.
* WC tested up to: 8.4.
* Tested up to: 6.4.

= 1.6.2 - 19/06/2023 =
* WC tested up to: 7.8.
* Tested up to: 6.2.

= 1.6.1 - 25/01/2023 =
* Fix - Footer - Report dates - Now showing only if applicable, i.e., for `orders` and `taxes` reports only.
* Dev - Developers - `alg_wc_reports_orders_data` filter added.
* Dev - Developers - `alg_wc_reports_orders_is_custom_data` filter added.
* Dev - Developers - `alg_wc_reports_settings_custom` filter added.
* WC tested up to: 7.3.

= 1.6.0 - 27/11/2022 =
* Fix - Menu - Product categories - Possible issues fixed (`get_hidden_inputs()`).
* Dev - Developers - `alg_wc_reports_orders_get_data_sales_by_meta_args` filter added.
* Dev - Developers - `alg_wc_reports_menu_get_html` filter added.
* Dev - Code refactoring.
* Tested up to: 6.1.
* WC tested up to: 7.1.

= 1.5.0 - 25/10/2022 =
* Dev - Reports - Taxes - "Taxes by order" report added.
* Dev - Menu - Period - "Custom date range" period added.
* Dev - Menu - "Data as chart" option added (defaults to `yes`).
* Dev - Developers - `alg_wc_reports_orders_detailed_data_keys` filter added.
* Dev - Developers - `alg_wc_reports_orders_get_data_sales_by_meta_order` filter added.
* Dev - Developers - `alg_wc_reports_menu_order_status` filter added.
* Dev - Developers - `alg_wc_reports_export_sep` filter added.
* Tested up to: 6.0.
* WC tested up to: 7.0.
* Deploy script added.
* Readme.txt updated.

= 1.4.0 - 15/04/2022 =
* Dev - Reports - Orders - Sales by product category - "Product categories" menu added.
* Tested up to: 5.9.
* WC tested up to: 6.4.

= 1.3.1 - 10/08/2021 =
* Dev - Reports - Detailed data - "Customer" column split into "First name", "Last name" and "Company".

= 1.3.0 - 07/08/2021 =
* Fix - Reports - Period - Including last day now.
* Dev - Reports - Detailed data - Replacing commas with spaces in "Customer" column now.
* Dev - Reports - Detailed data - "Address", "City", "State", "ZIP code", "Country" columns added.
* Dev - Reports - Period - "Report dates: ..." added to the footer.
* Dev - Developers - Reports - Orders - `alg_wc_reports_orders_item_value`, `alg_wc_reports_orders_detailed_data_row`, `alg_wc_reports_orders_detailed_data_heading` filters added.
* Dev - Code refactoring.
* Tested up to: 5.8.
* WC tested up to: 5.5.

= 1.2.0 - 30/06/2021 =
* Dev - Reports - Orders - "Sales by product category" report added.
* Dev - Reports - Orders - Sales by product/product category - "Item data" menu added.
* Dev - Advanced - "Detailed data" option added (defaults to `no`). Detailed data added for all "Orders" reports.
* Dev - Advanced - "Orders date range" option added (defaults to "Date created").
* Dev - Advanced - "Orders sorting" options added (defaults to "Date" and "Descending").
* Dev - Reports - "Export" options added (to "Data" and "Detailed data" tables).
* Dev - User menu selections are now saved.
* Dev - Plugin is initialized on the `plugins_loaded` action now.
* Dev - Localisation - `load_plugin_textdomain()` moved to the `init` action.
* Dev - Code refactoring.
* WC tested up to: 5.4.
* Tested up to: 5.7.

= 1.1.1 - 08/10/2020 =
* Dev - Advanced - "Skip zero orders" option added.
* Dev - Admin settings descriptions updated.
* WC tested up to: 4.5.
* Tested up to: 5.5.

= 1.1.0 - 11/02/2020 =
* Dev - Code refactoring and clean up.
* Dev - Admin settings descriptions updated.
* POT file uploaded.
* Plugin URI updated.
* Tested up to: 5.3.
* WC tested up to: 3.9.

= 1.0.0 - 11/05/2018 =
* Initial Release.

== Upgrade Notice ==

= 1.0.0 =
This is the first release of the plugin.
