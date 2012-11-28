<?php
/**
 * @package    admin
 * @author     Conor Kerr <zen-cart.advshipper@dev.ceon.net>
 * @copyright  Copyright 2007-2009 Ceon
 * @copyright  Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright  Portions Copyright 2003 osCommerce
 * @link       http://dev.ceon.net/web/zen-cart/advshipper
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: advshipper_method_config.php 382 2009-06-22 18:49:29Z Bob $
 */

require('includes/application_top.php');

require(DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies();

$languages = zen_get_languages();
$num_languages = sizeof($languages);

$current_time = time();

require_once(DIR_FS_ADMIN . DIR_WS_FUNCTIONS . 'advshipper.php');

if (defined('ADVSHIPPER_ZONES_SUPPORT') && ADVSHIPPER_ZONES_SUPPORT == 'Yes') {
	require_once(DIR_FS_ADMIN . DIR_WS_FUNCTIONS . 'advshipper_zones.php');
}

$advshipper_demo = isset($advshipper_demo) ? $advshipper_demo : false;

/**
 * Variables hold values for the shipping method
 */
$method_admin_titles = array();

$method_titles = array();


$select_products = ADVSHIPPER_SELECT_PRODUCT_FALLOVER;

$categories = array();

$manufacturers = array();

$products = array();


$regions = array();


$method_availability_scheduling = ADVSHIPPER_AVAILABILITY_SCHEDULING_ALWAYS;

$method_once_only_start_date = null;

$method_once_only_start_time = '00:00';

$method_once_only_end_date = null;

$method_once_only_end_time = '00:00';

$method_availability_recurring_mode = ADVSHIPPER_AVAILABILITY_RECURRING_MODE_WEEKLY;

$method_availability_weekly_start_day = null;

$method_availability_weekly_start_time = '00:00';

$method_availability_weekly_cutoff_day = null;

$method_availability_weekly_cutoff_time = '00:00';

$method_usage_limit = null;

$method_once_only_shipping_date = null;

$method_once_only_shipping_time = '00:00';

$method_availability_weekly_shipping_scheduling =
	ADVSHIPPER_AVAILABILITY_WEEKLY_SHIPPING_SCHEDULING_NONE;

$method_availability_weekly_shipping_show_num_weeks = 1;

$method_availability_weekly_shipping_regular_weekday_day = null;

$method_availability_weekly_shipping_regular_weekday_time = '00:00';


$config_id = (int) $_GET['config_id'];
$method_num = (int) $_GET['method_num'];

$new_method = false;
if (isset($_GET['add'])) {
	$new_method = true;
}

// Main variable holds list of errors (if any) for current configuration
$errors = array();

// Does a configuration need to be loaded or is one currently being edited?
if (!isset($_POST['select_products']) && !$new_method) {
	// Load the configuration for the selected method
	$load_method_config_sql = "
		SELECT
			asmc.method,
			asmc.select_products,
			asmc.availability_scheduling,
			asmc.once_only_start_datetime,
			asmc.once_only_end_datetime,
			asmc.availability_recurring_mode,
			asmc.availability_weekly_start_day,
			asmc.availability_weekly_start_time,
			asmc.availability_weekly_cutoff_day,
			asmc.availability_weekly_cutoff_time,
			asmc.usage_limit,
			asmc.once_only_shipping_datetime,
			asmc.availability_weekly_shipping_scheduling,
			asmc.availability_weekly_shipping_show_num_weeks,
			asmc.availability_weekly_shipping_regular_weekday_day,
			asmc.availability_weekly_shipping_regular_weekday_time
		FROM
			" . TABLE_ADVANCED_SHIPPER_METHOD_CONFIGS . " asmc
		WHERE
			asmc.config_id = '" . $config_id . "'
		AND
			asmc.method = '" . $method_num . "';";
	
	$load_method_config_result = $db->Execute($load_method_config_sql);
	
	if ($load_method_config_result->EOF) {
		
	} else {
		$load_method_admin_titles_config_sql = "
			SELECT
				asmat.title,
				asmat.language_id
			FROM
				" . TABLE_ADVANCED_SHIPPER_METHOD_ADMIN_TITLES . " asmat
			WHERE
				asmat.config_id = '" . $config_id . "'
			AND
				asmat.method = '" . $method_num . "';";
		
		$load_method_admin_titles_config_result = $db->Execute($load_method_admin_titles_config_sql);
		
		if ($load_method_admin_titles_config_result->EOF) {
			
		} else {
			while (!$load_method_admin_titles_config_result->EOF) {
				$method_admin_titles[$load_method_admin_titles_config_result->fields['language_id']] = $load_method_admin_titles_config_result->fields['title'];
				
				$load_method_admin_titles_config_result->MoveNext();
			}
		}
		
		$load_method_titles_config_sql = "
			SELECT
				asmt.title,
				asmt.language_id
			FROM
				" . TABLE_ADVANCED_SHIPPER_METHOD_TITLES . " asmt
			WHERE
				asmt.config_id = '" . $config_id . "'
			AND
				asmt.method = '" . $method_num . "';";
		
		$load_method_titles_config_result = $db->Execute($load_method_titles_config_sql);
		
		if ($load_method_titles_config_result->EOF) {
			
		} else {
			while (!$load_method_titles_config_result->EOF) {
				$method_titles[$load_method_titles_config_result->fields['language_id']] = $load_method_titles_config_result->fields['title'];
				
				$load_method_titles_config_result->MoveNext();
			}
		}
		
		$select_products = $load_method_config_result->fields['select_products'];
		
		$method_availability_scheduling = $load_method_config_result->fields['availability_scheduling'];
		
		// Parse the date
		$date = $load_method_config_result->fields['once_only_start_datetime'];
		$year = substr($date, 0, 4);
		if ($year != '0000' && !is_null($date)) {
			$month = substr($date, 5, 2);
			$day = substr($date, 8, 2);
			$hour = substr($date, 11, 2);
			$minute = substr($date, 14, 2);
			$timestamp = mktime(0, 0, 0, $month, $day, $year);
			$method_once_only_start_date = date(DATE_FORMAT, $timestamp);
			$method_once_only_start_time = $hour . ':' . $minute;
		} else {
			$method_once_only_start_date = '';
			$method_once_only_start_time = '00:00';
		}
		
		
		// Parse the date
		$date = $load_method_config_result->fields['once_only_end_datetime'];
		$year = substr($date, 0, 4);
		if ($year != '0000' && !is_null($date)) {
			$month = substr($date, 5, 2);
			$day = substr($date, 8, 2);
			$hour = substr($date, 11, 2);
			$minute = substr($date, 14, 2);
			$timestamp = mktime(0, 0, 0, $month, $day, $year);
			$method_once_only_end_date = date(DATE_FORMAT, $timestamp);
			$method_once_only_end_time = $hour . ':' . $minute;
		} else {
			$method_once_only_end_date = '';
			$method_once_only_end_time = '00:00';
		}
		$method_availability_recurring_mode = $load_method_config_result->fields['availability_recurring_mode'];
		$method_availability_weekly_start_day = $load_method_config_result->fields['availability_weekly_start_day'];
		$method_availability_weekly_start_time = $load_method_config_result->fields['availability_weekly_start_time'];
		$method_availability_weekly_cutoff_day = $load_method_config_result->fields['availability_weekly_cutoff_day'];
		$method_availability_weekly_cutoff_time = $load_method_config_result->fields['availability_weekly_cutoff_time'];
		$method_usage_limit = $load_method_config_result->fields['usage_limit'];
		
		if (is_null($method_availability_weekly_start_time)) {
			$method_availability_weekly_start_time = '00:00';
		} else {
			$method_availability_weekly_start_time =
				substr($method_availability_weekly_start_time, 0, 5);
		}
		if (is_null($method_availability_weekly_cutoff_time)) {
			$method_availability_weekly_cutoff_time = '00:00';
		} else {
			$method_availability_weekly_cutoff_time =
				substr($method_availability_weekly_cutoff_time, 0, 5);
		}
		
		// Parse the date
		$date = $load_method_config_result->fields['once_only_end_datetime'];
		$year = substr($date, 0, 4);
		if ($year != '0000' && !is_null($date)) {
			$month = substr($date, 5, 2);
			$day = substr($date, 8, 2);
			$hour = substr($date, 11, 2);
			$minute = substr($date, 14, 2);
			$timestamp = mktime(0, 0, 0, $month, $day, $year);
			$method_once_only_shipping_date = date(DATE_FORMAT, $timestamp);
			$method_once_only_shipping_time = $hour . ':' . $minute;
		} else {
			$method_once_only_shipping_date = '';
			$method_once_only_shipping_time = '00:00';
		}
		$method_availability_weekly_shipping_scheduling = $load_method_config_result->fields['availability_weekly_shipping_scheduling'];
		$method_availability_weekly_shipping_show_num_weeks = $load_method_config_result->fields['availability_weekly_shipping_show_num_weeks'];
		$method_availability_weekly_shipping_regular_weekday_day = $load_method_config_result->fields['availability_weekly_shipping_regular_weekday_day'];
		$method_availability_weekly_shipping_regular_weekday_time = $load_method_config_result->fields['availability_weekly_shipping_regular_weekday_time'];
		
		if (is_null($method_availability_weekly_shipping_regular_weekday_time)) {
			$method_availability_weekly_shipping_regular_weekday_time = '00:00';
		} else {
			$method_availability_weekly_shipping_regular_weekday_time =
				substr($method_availability_weekly_shipping_regular_weekday_time, 0, 5);
		}
		
		// Load the categories to which this shipping method applies (if any) //////////////////
		$load_categories_sql = "
			SELECT
				asmc.category_id
			FROM
				" . TABLE_ADVANCED_SHIPPER_METHOD_CATEGORIES . " asmc
			WHERE
				asmc.config_id = '" . $config_id . "'
			AND
				asmc.method = '" . $method_num . "'
			ORDER BY
				asmc.category_order;";
		
		$load_categories_result = $db->Execute($load_categories_sql);
		
		if ($load_categories_result->EOF) {
			// No categories for this method
		} else {
			while (!$load_categories_result->EOF) {
				$category_id = $load_categories_result->fields['category_id'];
				
				$category_name = str_replace('(())', '', advshipper_get_generated_category_path($category_id));
				
				$category_name = str_replace('|', '/', $category_name);
				
				$categories[] = array(
					'id' => $category_id,
					'name' => $category_name
					);
				
				$load_categories_result->MoveNext();
			}
		}
		
		// Load the manufacturers to which this shipping method applies (if any) //////////////////
		$load_manufacturers_sql = "
			SELECT
				asmm.manufacturer_id
			FROM
				" . TABLE_ADVANCED_SHIPPER_METHOD_MANUFACTURERS . " asmm
			WHERE
				asmm.config_id = '" . $config_id . "'
			AND
				asmm.method = '" . $method_num . "'
			ORDER BY
				asmm.manufacturer_order;";
		
		$load_manufacturers_result = $db->Execute($load_manufacturers_sql);
		
		if ($load_manufacturers_result->EOF) {
			// No manufacturers for this method
		} else {
			while (!$load_manufacturers_result->EOF) {
				$manufacturer_id = $load_manufacturers_result->fields['manufacturer_id'];
				
				$manufacturer_name =
					str_replace('(())', '', advshipper_get_manufacturer_name($manufacturer_id));
				
				$manufacturer_name = str_replace('|', '/', $manufacturer_name);
				
				$manufacturers[] = array(
					'id' => $manufacturer_id,
					'name' => $manufacturer_name
					);
				
				$load_manufacturers_result->MoveNext();
			}
		}
		
		// Load the products to which this shipping method applies (if any) ////////////////////
		$load_products_sql = "
			SELECT
				DISTINCT asmp.product_order,
				asmp.product_id
			FROM
				" . TABLE_ADVANCED_SHIPPER_METHOD_PRODUCTS . " asmp
			WHERE
				asmp.config_id = '" . $config_id . "'
			AND
				asmp.method = '" . $method_num . "'
			ORDER BY
				asmp.product_order;";
		
		$load_products_result = $db->Execute($load_products_sql);
		
		if ($load_products_result->EOF) {
			// No products for this method
		} else {
			while (!$load_products_result->EOF) {
				$product_id = $load_products_result->fields['product_id'];
				$product_order = $load_products_result->fields['product_order'];
				
				$product_name = str_replace('(())', '', zen_get_products_name($product_id, $_SESSION['languages_id']));
				
				$product_has_attributes = zen_has_product_attributes($product_id);
				
				if ($product_has_attributes) {
					// Have any product options been selected for this product?
					$load_product_options_sql = "
						SELECT
							asmp.product_attributes_id
						FROM
							" . TABLE_ADVANCED_SHIPPER_METHOD_PRODUCTS . " asmp
						WHERE
							asmp.config_id = '" . $config_id . "'
						AND
							asmp.method = '" . $method_num . "'
						AND
							asmp.product_order = '" . $product_order . "';";
					
					$load_product_options_result = $db->Execute($load_product_options_sql);
					
					if (!$load_product_options_result->EOF) {
						while (!$load_product_options_result->EOF) {
							$product_attributes_id = $load_product_options_result->fields['product_attributes_id'];
							
							$product_id .= '-' . $product_attributes_id;
							
							// Is this a catch-all?
							if ($product_attributes_id == 0) {
								$product_name .= TEXT_ALL_PRODUCT_OPTIONS_SELECTED;
							} else {
								$option_name_sql = "
									SELECT
										po.products_options_name
									FROM
										" . TABLE_PRODUCTS_OPTIONS . " po
									LEFT JOIN
										" . TABLE_PRODUCTS_ATTRIBUTES . " pa
									ON
										po.products_options_id = pa.options_id
									WHERE
										pa.products_attributes_id = '" . $product_attributes_id . "'
									AND
										po.language_id = '" . (int) $_SESSION['languages_id'] . "';";
								
								$option_value_name_sql = "
									SELECT
										pov.products_options_values_name
									FROM
										" . TABLE_PRODUCTS_OPTIONS_VALUES . " pov
									LEFT JOIN
										" . TABLE_PRODUCTS_ATTRIBUTES . " pa
									ON
										pov.products_options_values_id = pa.options_values_id
									WHERE
										pa.products_attributes_id = '" . $product_attributes_id . "'
									AND
										pov.language_id = '" . (int) $_SESSION['languages_id'] . "';";
								
								$option_name_result = $db->Execute($option_name_sql);
								$option_value_result = $db->Execute($option_value_name_sql);
								
								$product_name .= ' // ' .
									$option_name_result->fields['products_options_name'] . ' -- ' .
									$option_value_result->fields['products_options_values_name'];
							}
							
							$load_product_options_result->MoveNext();;
						}
					}
				}
				
				$product_name = str_replace('|', '/', $product_name);
				
				while (strpos($product_name, ADVSHIPPER_PRODUCT_OPTIONS_SEPARATOR) !== false) {
					$product_name = str_replace(ADVSHIPPER_PRODUCT_OPTIONS_SEPARATOR, '---',
						$product_name);
				}
				
				$products[] = array(
					'id' => $product_id,
					'name' => $product_name
					);
				
				$load_products_result->MoveNext();
			}
		}
		
		// Load the settings for the regions for this shipping method
		$load_region_config_sql = "
			SELECT
				asrc.region,
				asrc.definition_method,
				asrc.countries_postcodes,
				asrc.countries_zones,
				asrc.countries_cities,
				asrc.countries_states,
				asrc.distance,
				asrc.tax_class,
				asrc.rates_include_tax,
				asrc.rate_limits_inc,
				asrc.total_up_price_inc_tax,
				asrc.table_of_rates,
				asrc.max_weight_per_package,
				asrc.packaging_weights,
				asrc.surcharge
			FROM
				" . TABLE_ADVANCED_SHIPPER_REGION_CONFIGS . " asrc
			WHERE
				asrc.config_id = '" . $config_id . "'
			AND
				asrc.method = '" . $method_num . "'
			ORDER BY
				asrc.region;";
		
		$load_region_config_result = $db->Execute($load_region_config_sql);
		
		if ($load_region_config_result->EOF) {
			
		} else {
			while (!$load_region_config_result->EOF) {
				$region_num = $load_region_config_result->fields['region'];
				
				$load_region_admin_titles_config_sql = "
					SELECT
						asrat.title,
						asrat.language_id
					FROM
						" . TABLE_ADVANCED_SHIPPER_REGION_ADMIN_TITLES . " asrat
					WHERE
						asrat.config_id = '" . $config_id . "'
					AND
						asrat.method = '" . $method_num . "'
					AND
						asrat.region = '" . $region_num . "';";
				
				$load_region_admin_titles_config_result = $db->Execute($load_region_admin_titles_config_sql);
				
				if ($load_region_admin_titles_config_result->EOF) {
					
				} else {
					while (!$load_region_admin_titles_config_result->EOF) {
						$regions[$region_num]['admin_titles'][$load_region_admin_titles_config_result->fields['language_id']] = $load_region_admin_titles_config_result->fields['title'];
						
						$load_region_admin_titles_config_result->MoveNext();
					}
				}
				
				$load_region_titles_config_sql = "
					SELECT
						asrt.title,
						asrt.language_id
					FROM
						" . TABLE_ADVANCED_SHIPPER_REGION_TITLES . " asrt
					WHERE
						asrt.config_id = '" . $config_id . "'
					AND
						asrt.method = '" . $method_num . "'
					AND
						asrt.region = '" . $region_num . "';";
				
				$load_region_titles_config_result = $db->Execute($load_region_titles_config_sql);
				
				if ($load_region_titles_config_result->EOF) {
					
				} else {
					while (!$load_region_titles_config_result->EOF) {
						$regions[$region_num]['titles'][$load_region_titles_config_result->fields['language_id']] = $load_region_titles_config_result->fields['title'];
						
						$load_region_titles_config_result->MoveNext();
					}
				}
				
				$load_region_surcharge_titles_config_sql = "
					SELECT
						asrst.title,
						asrst.language_id
					FROM
						" . TABLE_ADVANCED_SHIPPER_REGION_SURCHARGE_TITLES . " asrst
					WHERE
						asrst.config_id = '" . $config_id . "'
					AND
						asrst.method = '" . $method_num . "'
					AND
						asrst.region = '" . $region_num . "';";
				
				$load_region_surcharge_titles_config_result = $db->Execute($load_region_surcharge_titles_config_sql);
				
				if ($load_region_surcharge_titles_config_result->EOF) {
					
				} else {
					while (!$load_region_surcharge_titles_config_result->EOF) {
						$regions[$region_num]['surcharge_titles'][$load_region_surcharge_titles_config_result->fields['language_id']] = $load_region_surcharge_titles_config_result->fields['title'];
						
						$load_region_surcharge_titles_config_result->MoveNext();
					}
				}
				
				$regions[$region_num]['definition_method'] = $load_region_config_result->fields['definition_method'];
				$regions[$region_num]['countries_postcodes'] = $load_region_config_result->fields['countries_postcodes'];
				
				// Build the list of zones (if any)
				$countries_zones_string = $load_region_config_result->fields['countries_zones'];
				if (strlen($countries_zones_string) > 0) {
					$regions[$region_num]['countries_zones'] = $countries_zones_string;
				}
				
				// Build the list of cities (if any)
				$countries_cities_string = $load_region_config_result->fields['countries_cities'];
				if (strlen($countries_cities_string) > 0) {
					$regions[$region_num]['countries_cities'] = $countries_cities_string;
				}
				
				// Build the list of states/areas (if any)
				$countries_states_string = $load_region_config_result->fields['countries_states'];
				if (strlen($countries_states_string) > 0) {
					$regions[$region_num]['countries_states'] = $countries_states_string;
				}
				
				$regions[$region_num]['distance'] = $load_region_config_result->fields['distance'];
				$regions[$region_num]['tax_class'] = $load_region_config_result->fields['tax_class'];
				$regions[$region_num]['rates_include_tax'] = $load_region_config_result->fields['rates_include_tax'];
				$regions[$region_num]['rate_limits_inc'] = $load_region_config_result->fields['rate_limits_inc'];
				$regions[$region_num]['total_up_price_inc_tax'] = $load_region_config_result->fields['total_up_price_inc_tax'];
				$regions[$region_num]['table_of_rates'] = $load_region_config_result->fields['table_of_rates'];
				$regions[$region_num]['max_weight_per_package'] = $load_region_config_result->fields['max_weight_per_package'];
				$regions[$region_num]['packaging_weights'] = $load_region_config_result->fields['packaging_weights'];
				$regions[$region_num]['surcharge'] = $load_region_config_result->fields['surcharge'];
				
				$load_region_ups_config_sql = "
					SELECT
						*
					FROM
						" . TABLE_ADVANCED_SHIPPER_REGION_UPS_CONFIGS . " ruc
					WHERE
						ruc.config_id = '" . $config_id . "'
					AND
						ruc.method = '" . $method_num . "'
					AND
						ruc.region = '" . $region_num . "';";
				
				$load_region_ups_config_result = $db->Execute($load_region_ups_config_sql);
				
				if ($load_region_ups_config_result->EOF) {
					// No UPS settings for this region
					$regions[$region_num]['ups_config'] = null;
				} else {
					$regions[$region_num]['ups'][$load_region_ups_config_result->fields['language_id']] = $load_region_ups_config_result->fields['title'];
					
					$regions[$region_num]['ups_config']['source_country'] = $load_region_ups_config_result->fields['source_country'];
					$regions[$region_num]['ups_config']['source_postcode'] = $load_region_ups_config_result->fields['source_postcode'];
					$regions[$region_num]['ups_config']['pickup_method'] = $load_region_ups_config_result->fields['pickup_method'];
					$regions[$region_num]['ups_config']['packaging'] = $load_region_ups_config_result->fields['packaging'];
					$regions[$region_num]['ups_config']['delivery_type'] = $load_region_ups_config_result->fields['delivery_type'];
					$regions[$region_num]['ups_config']['shipping_service_1dm'] = $load_region_ups_config_result->fields['shipping_service_1dm'];
					$regions[$region_num]['ups_config']['shipping_service_1dml'] = $load_region_ups_config_result->fields['shipping_service_1dml'];
					$regions[$region_num]['ups_config']['shipping_service_1da'] = $load_region_ups_config_result->fields['shipping_service_1da'];
					$regions[$region_num]['ups_config']['shipping_service_1dal'] = $load_region_ups_config_result->fields['shipping_service_1dal'];
					$regions[$region_num]['ups_config']['shipping_service_1dapi'] = $load_region_ups_config_result->fields['shipping_service_1dapi'];
					$regions[$region_num]['ups_config']['shipping_service_1dp'] = $load_region_ups_config_result->fields['shipping_service_1dp'];
					$regions[$region_num]['ups_config']['shipping_service_1dpl'] = $load_region_ups_config_result->fields['shipping_service_1dpl'];
					$regions[$region_num]['ups_config']['shipping_service_2dm'] = $load_region_ups_config_result->fields['shipping_service_2dm'];
					$regions[$region_num]['ups_config']['shipping_service_2dml'] = $load_region_ups_config_result->fields['shipping_service_2dml'];
					$regions[$region_num]['ups_config']['shipping_service_2da'] = $load_region_ups_config_result->fields['shipping_service_2da'];
					$regions[$region_num]['ups_config']['shipping_service_2dal'] = $load_region_ups_config_result->fields['shipping_service_2dal'];
					$regions[$region_num]['ups_config']['shipping_service_3ds'] = $load_region_ups_config_result->fields['shipping_service_3ds'];
					$regions[$region_num]['ups_config']['shipping_service_gnd'] = $load_region_ups_config_result->fields['shipping_service_gnd'];
					$regions[$region_num]['ups_config']['shipping_service_std'] = $load_region_ups_config_result->fields['shipping_service_std'];
					$regions[$region_num]['ups_config']['shipping_service_xpr'] = $load_region_ups_config_result->fields['shipping_service_xpr'];
					$regions[$region_num]['ups_config']['shipping_service_xprl'] = $load_region_ups_config_result->fields['shipping_service_xprl'];
					$regions[$region_num]['ups_config']['shipping_service_xdm'] = $load_region_ups_config_result->fields['shipping_service_xdm'];
					$regions[$region_num]['ups_config']['shipping_service_xdml'] = $load_region_ups_config_result->fields['shipping_service_xdml'];
					$regions[$region_num]['ups_config']['shipping_service_xpd'] = $load_region_ups_config_result->fields['shipping_service_xpd'];
					$regions[$region_num]['ups_config']['shipping_service_wxs'] = $load_region_ups_config_result->fields['shipping_service_wxs'];
				}
				
				$load_region_usps_config_sql = "
					SELECT
						*
					FROM
						" . TABLE_ADVANCED_SHIPPER_REGION_USPS_CONFIGS . " asruc
					WHERE
						asruc.config_id = '" . $config_id . "'
					AND
						asruc.method = '" . $method_num . "'
					AND
						asruc.region = '" . $region_num . "';";
				
				$load_region_usps_config_result = $db->Execute($load_region_usps_config_sql);
				
				if ($load_region_usps_config_result->EOF) {
					// No USPS settings for this region
					$regions[$region_num]['usps_config'] = null;
				} else {
					$regions[$region_num]['usps'][$load_region_usps_config_result->fields['language_id']] = $load_region_usps_config_result->fields['title'];
					
					$regions[$region_num]['usps_config']['user_id'] = $load_region_usps_config_result->fields['user_id'];
					$regions[$region_num]['usps_config']['server'] = $load_region_usps_config_result->fields['server'];
					$regions[$region_num]['usps_config']['source_country'] = $load_region_usps_config_result->fields['source_country'];
					$regions[$region_num]['usps_config']['source_postcode'] = $load_region_usps_config_result->fields['source_postcode'];
					$regions[$region_num]['usps_config']['machinable'] = $load_region_usps_config_result->fields['machinable'];
					$regions[$region_num]['usps_config']['display_transit_time'] = $load_region_usps_config_result->fields['display_transit_time'];
					$regions[$region_num]['usps_config']['domestic_express'] = $load_region_usps_config_result->fields['domestic_express'];
					$regions[$region_num]['usps_config']['domestic_priority'] = $load_region_usps_config_result->fields['domestic_priority'];
					$regions[$region_num]['usps_config']['domestic_first_class'] = $load_region_usps_config_result->fields['domestic_first_class'];
					$regions[$region_num]['usps_config']['domestic_parcel'] = $load_region_usps_config_result->fields['domestic_parcel'];
					$regions[$region_num]['usps_config']['domestic_media'] = $load_region_usps_config_result->fields['domestic_media'];
					$regions[$region_num]['usps_config']['domestic_bpm'] = $load_region_usps_config_result->fields['domestic_bpm'];
					$regions[$region_num]['usps_config']['domestic_library'] = $load_region_usps_config_result->fields['domestic_library'];
					$regions[$region_num]['usps_config']['international_ge'] = $load_region_usps_config_result->fields['international_ge'];
					$regions[$region_num]['usps_config']['international_gendr'] = $load_region_usps_config_result->fields['international_gendr'];
					$regions[$region_num]['usps_config']['international_gendnr'] = $load_region_usps_config_result->fields['international_gendnr'];
					$regions[$region_num]['usps_config']['international_emi'] = $load_region_usps_config_result->fields['international_emi'];
					$regions[$region_num]['usps_config']['international_emifre'] = $load_region_usps_config_result->fields['international_emifre'];
					$regions[$region_num]['usps_config']['international_pmi'] = $load_region_usps_config_result->fields['international_pmi'];
					$regions[$region_num]['usps_config']['international_pmifre'] = $load_region_usps_config_result->fields['international_pmifre'];
					$regions[$region_num]['usps_config']['international_pmifrb'] = $load_region_usps_config_result->fields['international_pmifrb'];
					$regions[$region_num]['usps_config']['international_fcmile'] = $load_region_usps_config_result->fields['international_fcmile'];
					$regions[$region_num]['usps_config']['international_fcmip'] = $load_region_usps_config_result->fields['international_fcmip'];
					$regions[$region_num]['usps_config']['international_fcmil'] = $load_region_usps_config_result->fields['international_fcmil'];
					$regions[$region_num]['usps_config']['international_fcmif'] = $load_region_usps_config_result->fields['international_fcmif'];
					$regions[$region_num]['usps_config']['international_fcmipar'] = $load_region_usps_config_result->fields['international_fcmipar'];
				}
				
				$load_region_config_result->MoveNext();
			}
		}
	}
} else if (isset($_POST['select_products'])) {
	// Data has been submitted - check it	
	for ($language_i = 0; $language_i < $num_languages; $language_i++) {
		$method_admin_titles[$languages[$language_i]['id']] = $_POST['method_admin_titles'][$languages[$language_i]['id']];
		
		$method_titles[$languages[$language_i]['id']] = $_POST['method_titles'][$languages[$language_i]['id']];
	}
	
	// Make sure the title was specified
	for ($language_i = 0; $language_i < $num_languages; $language_i++) {
		if (strlen($method_titles[$languages[$language_i]['id']]) == 0) {
			if ($num_languages == 1) {
				$errors['method_title'][$languages[$language_i]['id']] =
					ERROR_TITLE_MISSING;
			} else {
				$errors['method_title'][$languages[$language_i]['id']] = ERROR_TITLE_FOR_LANGUAGE_MISSING;
			}
		}
	}
	
	
	$select_products = $_POST['select_products'];
	
	// Store any category selections ///////////////////////////////////////////////////////////////
	if (isset($_POST['categories'])) {
		$categories_info = $_POST['categories'];
		
		if (strlen($categories_info) > 0) {
			$categories_info_array = explode('||', $categories_info);
			
			$num_categories = sizeof($categories_info_array);
			
			for ($category_i = 0; $category_i < $num_categories; $category_i++) {
				$category_info = explode('|', $categories_info_array[$category_i]);
				
				$categories[$category_i]['id'] = $category_info[0];
				$categories[$category_i]['name'] = urldecode($category_info[1]);
			}
		}
	}
	
	
	// Store any manufacturer selections ///////////////////////////////////////////////////////////
	if (isset($_POST['manufacturers'])) {
		$manufacturers_info = $_POST['manufacturers'];
		
		if (strlen($manufacturers_info) > 0) {
			$manufacturers_info_array = explode('||', $manufacturers_info);
			
			$num_manufacturers = sizeof($manufacturers_info_array);
			
			for ($manufacturer_i = 0; $manufacturer_i < $num_manufacturers; $manufacturer_i++) {
				$manufacturer_info = explode('|', $manufacturers_info_array[$manufacturer_i]);
				
				$manufacturers[$manufacturer_i]['id'] = $manufacturer_info[0];
				$manufacturers[$manufacturer_i]['name'] = urldecode($manufacturer_info[1]);
			}
		}
	}
	
	
	// Store any product selections ////////////////////////////////////////////////////////////////
	if (isset($_POST['products'])) {
		$products_info = $_POST['products'];
		
		if (strlen($products_info) > 0) {
			$products_info_array = explode('||', $products_info);
			
			$num_products = sizeof($products_info_array);
			
			for ($product_i = 0; $product_i < $num_products; $product_i++) {
				$product_info = explode('|', $products_info_array[$product_i]);
				
				$products[$product_i]['id'] = $product_info[0];
				$products[$product_i]['name'] = urldecode($product_info[1]);
			}
		}
	}
	
	
	// Store any region configurations defined /////////////////////////////////////////////////////
	$regions_info = $_POST['regions_info'];
	
	if (strlen($regions_info) > 0) {
		$regions_info_array = explode('(())', $regions_info);
		
		$num_regions = sizeof($regions_info_array);
		
		for ($region_i = 0; $region_i < $num_regions; $region_i++) {
			$region_num = $region_i + 1;
			
			$region_info = explode('[[]]', $regions_info_array[$region_i]);
			
			// Decode admin titles
			$titles_encoded = urldecode($region_info[0]);
			
			$titles_decoded = explode('||', $titles_encoded);
			
			$num_titles = sizeof($titles_decoded);
			
			for ($title_i = 0; $title_i < $num_titles; $title_i++) {
				$current_title_info = explode('|', $titles_decoded[$title_i]);
				
				if ($current_title_info[1] == 'null') {
					$current_title_info[1] = '';
				}
				
				$current_title_info[1] = UTF8URLDecode($current_title_info[1]);
				
				$regions[$region_num]['admin_titles'][$current_title_info[0]] =
					str_replace('--plus--', '+', $current_title_info[1]);
			}
			
			// Decode titles
			$titles_encoded = urldecode($region_info[1]);
			
			$titles_decoded = explode('||', $titles_encoded);
			
			$num_titles = sizeof($titles_decoded);
			
			for ($title_i = 0; $title_i < $num_titles; $title_i++) {
				$current_title_info = explode('|', $titles_decoded[$title_i]);
				
				if ($current_title_info[1] == 'null') {
					$current_title_info[1] = '';
				}
				
				$current_title_info[1] = UTF8URLDecode($current_title_info[1]);
				
				$regions[$region_num]['titles'][$current_title_info[0]] =
					str_replace('--plus--', '+', $current_title_info[1]);
			}
			
			$regions[$region_num]['definition_method'] = $region_info[2];
			$regions[$region_num]['countries_postcodes'] = urldecode($region_info[3]);
			$regions[$region_num]['countries_zones'] = urldecode($region_info[4]);
			$regions[$region_num]['countries_states'] = urldecode($region_info[5]);
			$regions[$region_num]['countries_cities'] = urldecode($region_info[6]);
			$regions[$region_num]['distance'] = $region_info[7];
			$regions[$region_num]['tax_class'] = $region_info[8];
			$regions[$region_num]['rates_include_tax'] = $region_info[9];
			$regions[$region_num]['rate_limits_inc'] = $region_info[10];
			$regions[$region_num]['total_up_price_inc_tax'] = $region_info[11];
			$regions[$region_num]['table_of_rates'] =
				str_replace('--plus--', '+', urldecode($region_info[12]));
			$regions[$region_num]['max_weight_per_package'] = $region_info[13];
			$regions[$region_num]['packaging_weights'] =
				str_replace('--plus--', '+', urldecode($region_info[14]));
			$regions[$region_num]['surcharge'] =
				str_replace('--plus--', '+', urldecode($region_info[15]));
			
			// Decode surcharge titles
			$titles_encoded = urldecode($region_info[16]);
			
			$titles_decoded = explode('||', $titles_encoded);
			
			$num_titles = sizeof($titles_decoded);
			
			for ($title_i = 0; $title_i < $num_titles; $title_i++) {
				$current_title_info = explode('|', $titles_decoded[$title_i]);
				
				if ($current_title_info[1] == 'null') {
					$current_title_info[1] = '';
				}
				
				$current_title_info[1] = UTF8URLDecode($current_title_info[1]);
				
				$regions[$region_num]['surcharge_titles'][$current_title_info[0]] =
					str_replace('--plus--', '+', $current_title_info[1]);
			}
			
			// Parse the UPS settings (if any)
			$ups_calc_string = $region_info[17];
			
			if (!is_null($ups_calc_string) && strlen($ups_calc_string) > 0 &&
					$ups_calc_string != 'null') {
				$ups_config = explode('|', $ups_calc_string);
				
				$regions[$region_num]['ups_config']['source_country'] = $ups_config[0];
				$regions[$region_num]['ups_config']['source_postcode'] = $ups_config[1];
				$regions[$region_num]['ups_config']['pickup_method'] = $ups_config[2];
				$regions[$region_num]['ups_config']['packaging'] = $ups_config[3];
				$regions[$region_num]['ups_config']['delivery_type'] = $ups_config[4];
				$regions[$region_num]['ups_config']['shipping_service_1dm'] = $ups_config[5];
				$regions[$region_num]['ups_config']['shipping_service_1dml'] = $ups_config[6];
				$regions[$region_num]['ups_config']['shipping_service_1da'] = $ups_config[7];
				$regions[$region_num]['ups_config']['shipping_service_1dal'] = $ups_config[8];
				$regions[$region_num]['ups_config']['shipping_service_1dapi'] = $ups_config[9];
				$regions[$region_num]['ups_config']['shipping_service_1dp'] = $ups_config[10];
				$regions[$region_num]['ups_config']['shipping_service_1dpl'] = $ups_config[11];
				$regions[$region_num]['ups_config']['shipping_service_2dm'] = $ups_config[12];
				$regions[$region_num]['ups_config']['shipping_service_2dml'] = $ups_config[13];
				$regions[$region_num]['ups_config']['shipping_service_2da'] = $ups_config[14];
				$regions[$region_num]['ups_config']['shipping_service_2dal'] = $ups_config[15];
				$regions[$region_num]['ups_config']['shipping_service_3ds'] = $ups_config[16];
				$regions[$region_num]['ups_config']['shipping_service_gnd'] = $ups_config[17];
				$regions[$region_num]['ups_config']['shipping_service_std'] = $ups_config[18];
				$regions[$region_num]['ups_config']['shipping_service_xpr'] = $ups_config[19];
				$regions[$region_num]['ups_config']['shipping_service_xprl'] = $ups_config[20];
				$regions[$region_num]['ups_config']['shipping_service_xdm'] = $ups_config[21];
				$regions[$region_num]['ups_config']['shipping_service_xdml'] = $ups_config[22];
				$regions[$region_num]['ups_config']['shipping_service_xpd'] = $ups_config[23];
				$regions[$region_num]['ups_config']['shipping_service_wxs'] = $ups_config[24];
			} else {
				$regions[$region_num]['ups_config'] = null;
			}
			
			// Parse the USPS settings (if any)
			$usps_calc_string = $region_info[18];
			
			if (!is_null($usps_calc_string) && strlen($usps_calc_string) > 0 &&
					$usps_calc_string != 'null') {
				$usps_config = explode('|', $usps_calc_string);
				
				$regions[$region_num]['usps_config']['user_id'] = $usps_config[0];
				$regions[$region_num]['usps_config']['server'] = $usps_config[1];
				$regions[$region_num]['usps_config']['source_country'] = $usps_config[2];
				$regions[$region_num]['usps_config']['source_postcode'] = $usps_config[3];
				$regions[$region_num]['usps_config']['machinable'] = $usps_config[4];
				$regions[$region_num]['usps_config']['display_transit_time'] = $usps_config[5];
				$regions[$region_num]['usps_config']['domestic_express'] = $usps_config[6];
				$regions[$region_num]['usps_config']['domestic_priority'] = $usps_config[7];
				$regions[$region_num]['usps_config']['domestic_first_class'] = $usps_config[8];
				$regions[$region_num]['usps_config']['domestic_parcel'] = $usps_config[9];
				$regions[$region_num]['usps_config']['domestic_media'] = $usps_config[10];
				$regions[$region_num]['usps_config']['domestic_bpm'] = $usps_config[11];
				$regions[$region_num]['usps_config']['domestic_library'] = $usps_config[12];
				$regions[$region_num]['usps_config']['international_ge'] = $usps_config[13];
				$regions[$region_num]['usps_config']['international_gendr'] = $usps_config[14];
				$regions[$region_num]['usps_config']['international_gendnr'] = $usps_config[15];
				$regions[$region_num]['usps_config']['international_emi'] = $usps_config[16];
				$regions[$region_num]['usps_config']['international_emifre'] = $usps_config[17];
				$regions[$region_num]['usps_config']['international_pmi'] = $usps_config[18];
				$regions[$region_num]['usps_config']['international_pmifre'] = $usps_config[19];
				$regions[$region_num]['usps_config']['international_pmifrb'] = $usps_config[20];
				$regions[$region_num]['usps_config']['international_fcmile'] = $usps_config[21];
				$regions[$region_num]['usps_config']['international_fcmip'] = $usps_config[22];
				$regions[$region_num]['usps_config']['international_fcmil'] = $usps_config[23];
				$regions[$region_num]['usps_config']['international_fcmif'] = $usps_config[24];
				$regions[$region_num]['usps_config']['international_fcmipar'] = $usps_config[25];
			} else {
				$regions[$region_num]['usps_config'] = null;
			}
		}
	}
	
	$method_availability_scheduling = $_POST['method_availability_scheduling'];
	
	if (isset($_POST['method_once_only_start_date'])) {
		$method_once_only_start_date = $_POST['method_once_only_start_date'];
		if (strlen($method_once_only_start_date) == 0) {
			$method_once_only_start_date = null;
			$method_once_only_start_time = '00:00';
		} else {
			$method_once_only_start_time = $_POST['method_once_only_start_time'];
			// Perform error checks on value entered for time
			if (!advshipper_parse_time_string($method_once_only_start_time)) {
				$errors['method_once_only_start_time'] = ERROR_TIME_FORMAT;
			}
		}
	}
	
	if (isset($_POST['method_once_only_end_date'])) {
		$method_once_only_end_date = $_POST['method_once_only_end_date'];
		if (strlen($method_once_only_end_date) == 0) {
			$method_once_only_end_date = null;
			$method_once_only_end_time = '00:00';
		} else {
			$method_once_only_end_time = $_POST['method_once_only_end_time'];
			// Perform error checks on value entered for time
			if (!advshipper_parse_time_string($method_once_only_end_time)) {
				$errors['method_once_only_end_time'] = ERROR_TIME_FORMAT;
			}
		}
	}
	
	if (isset($_POST['method_availability_recurring_mode'])) {
		$method_availability_recurring_mode = $_POST['method_availability_recurring_mode'];
	}
	
	if (isset($_POST['method_availability_weekly_start_day'])) {
		$method_availability_weekly_start_day = $_POST['method_availability_weekly_start_day'];
		if ($method_availability_weekly_start_day == '-1') {
			$method_availability_weekly_start_day = null;
			$method_availability_weekly_start_time = '00:00';
		} else {
			$method_availability_weekly_start_time =
				$_POST['method_availability_weekly_start_time'];
			// Perform error checks on value entered for time
			if (!advshipper_parse_time_string($method_availability_weekly_start_time)) {
				$errors['method_availability_weekly_start_time'] = ERROR_TIME_FORMAT;
			}
		}
	}
	
	if (isset($_POST['method_availability_weekly_cutoff_day'])) {
		$method_availability_weekly_cutoff_day = $_POST['method_availability_weekly_cutoff_day'];
		if ($method_availability_weekly_cutoff_day == '-1') {
			$method_availability_weekly_cutoff_day = null;
			$method_availability_weekly_cutoff_time = '00:00';
		} else {
			$method_availability_weekly_cutoff_time =
				$_POST['method_availability_weekly_cutoff_time'];
			// Perform error checks on value entered for time
			if (!advshipper_parse_time_string($method_availability_weekly_cutoff_time)) {
				$errors['method_availability_weekly_cutoff_time'] = ERROR_TIME_FORMAT;
			}
		}
	}
	
	if (isset($_POST['method_usage_limit'])) {
		$method_usage_limit = $_POST['method_usage_limit'];
		if (strlen($method_usage_limit) == 0) {
			$method_usage_limit = null;
		}
	}
	
	
	if (isset($_POST['method_once_only_shipping_date'])) {
		$method_once_only_shipping_date = $_POST['method_once_only_shipping_date'];
		if (strlen($method_once_only_shipping_date) == 0) {
			$method_once_only_shipping_date = null;
			$method_once_only_shipping_time = '00:00';
		} else {
			$method_once_only_shipping_time = $_POST['method_once_only_shipping_time'];
			// Perform error checks on value entered for time
			if (!advshipper_parse_time_string($method_once_only_shipping_time)) {
				$errors['method_once_only_shipping_time'] = ERROR_TIME_FORMAT;
			}
		}
	}
	
	if (isset($_POST['method_availability_weekly_shipping_scheduling'])) {
		$method_availability_weekly_shipping_scheduling =
			$_POST['method_availability_weekly_shipping_scheduling'];
	}
	
	if (isset($_POST['method_availability_weekly_shipping_show_num_weeks'])) {
		$method_availability_weekly_shipping_show_num_weeks =
			$_POST['method_availability_weekly_shipping_show_num_weeks'];
		if (strlen($method_availability_weekly_shipping_show_num_weeks) == 0) {
			$method_availability_weekly_shipping_show_num_weeks = 2;
		} else if (!is_numeric($method_availability_weekly_shipping_show_num_weeks)) {
			$errors['method_availability_weekly_shipping_show_num_weeks'] =
				ERROR_SHOW_NUM_WEEKS_INVALID;
		}
	}
	
	if (isset($_POST['method_availability_weekly_shipping_regular_weekday_day'])) {
		$method_availability_weekly_shipping_regular_weekday_day =
			$_POST['method_availability_weekly_shipping_regular_weekday_day'];
		if ($method_availability_weekly_shipping_regular_weekday_day == '-1') {
			$method_availability_weekly_shipping_regular_weekday_day = null;
			$method_availability_weekly_shipping_regular_weekday_time = '00:00';
		} else {
			$method_availability_weekly_shipping_regular_weekday_time =
				$_POST['method_availability_weekly_shipping_regular_weekday_time'];
		
			// Perform error checks on value entered for time
			if (!advshipper_parse_time_string(
					$method_availability_weekly_shipping_regular_weekday_time
					)) {
				$errors['method_availability_weekly_shipping_regular_weekday_time'] =
					ERROR_TIME_FORMAT;
			}
		}
	}
	
	// Perform validation of date options selected
	if ($method_availability_scheduling == ADVSHIPPER_AVAILABILITY_SCHEDULING_RECURRING &&
			$method_availability_recurring_mode == ADVSHIPPER_AVAILABILITY_RECURRING_MODE_WEEKLY) {
		if (!is_null($method_availability_weekly_start_day) &&
				is_null($method_availability_weekly_cutoff_day)) {
			$errors['method_availability_weekly_cutoff_day'] = ERROR_WEEKLY_CUTOFF_NOT_SPECIFIED;
		} else if ($method_availability_weekly_shipping_scheduling !=
				ADVSHIPPER_AVAILABILITY_WEEKLY_SHIPPING_SCHEDULING_NONE) {
			if (is_null($method_availability_weekly_shipping_regular_weekday_day)) {
				$errors['method_availability_weekly_shipping_regular_weekday_day'] =
					ERROR_WEEKLY_SHIPPING_DAY_NOT_SPECIFIED;
			}
		}
	}
	
	// Save the configuration
	if (!$advshipper_demo && sizeof($errors) == 0) {
		// Save the method's configuration first ///////////////////////////////////////////////////
		
		// Format dates so they can be inserted into the database
		if (strtolower(DATE_FORMAT_SPIFFYCAL) == 'dd/mm/yyyy' ||
				strtolower(DATE_FORMAT_SPIFFYCAL) == 'dd.mm.yyyy') {
			if (strlen($method_once_only_start_date) == 10) {
				$method_once_only_start_day = substr($method_once_only_start_date, 0, 2);
				$method_once_only_start_month = substr($method_once_only_start_date, 3, 2);
				$method_once_only_start_year = substr($method_once_only_start_date, 6, 4);
				
				$method_once_only_start_datetime = $method_once_only_start_year . '-' .
					$method_once_only_start_month . '-' .
					$method_once_only_start_day . ' ' .
					$method_once_only_start_time;
			} else {
				$method_once_only_start_datetime = 'null';
			}
			
			if (strlen($method_once_only_end_date) == 10) {
				$method_once_only_end_day = substr($method_once_only_end_date, 0, 2);
				$method_once_only_end_month = substr($method_once_only_end_date, 3, 2);
				$method_once_only_end_year = substr($method_once_only_end_date, 6, 4);
				
				$method_once_only_end_datetime = $method_once_only_end_year . '-' .
					$method_once_only_end_month . '-' .
					$method_once_only_end_day . ' ' .
					$method_once_only_end_time;
			} else {
				$method_once_only_end_datetime = 'null';
			}
			
			if (strlen($method_once_only_shipping_date) == 10) {
				$method_once_only_shipping_day = substr($method_once_only_shipping_date, 0, 2);
				$method_once_only_shipping_month = substr($method_once_only_shipping_date, 3, 2);
				$method_once_only_shipping_year = substr($method_once_only_shipping_date, 6, 4);
				
				$method_once_only_shipping_datetime = $method_once_only_shipping_year . '-' .
					$method_once_only_shipping_month . '-' .
					$method_once_only_shipping_day . ' ' .
					$method_once_only_shipping_time;
			} else {
				$method_once_only_shipping_datetime = 'null';
			}
		} else if (strtolower(DATE_FORMAT_SPIFFYCAL) == 'mm/dd/yyyy') {
			if (strlen($method_once_only_start_date) == 10) {
				$method_once_only_start_day = substr($method_once_only_start_date, 3, 2);
				$method_once_only_start_month = substr($method_once_only_start_date, 0, 2);
				$method_once_only_start_year = substr($method_once_only_start_date, 6, 4);
				
				$method_once_only_start_datetime = $method_once_only_start_year . '-' .
					$method_once_only_start_month . '-' .
					$method_once_only_start_day . ' ' .
					$method_once_only_start_time;
			} else {
				$method_once_only_start_datetime = 'null';
			}
			
			if (strlen($method_once_only_end_date) == 10) {
				$method_once_only_end_day = substr($method_once_only_end_date, 3, 2);
				$method_once_only_end_month = substr($method_once_only_end_date, 0, 2);
				$method_once_only_end_year = substr($method_once_only_end_date, 6, 4);
				
				$method_once_only_end_datetime = $method_once_only_end_year . '-' .
					$method_once_only_end_month . '-' .
					$method_once_only_end_day . ' ' .
					$method_once_only_end_time;
			} else {
				$method_once_only_end_datetime = 'null';
			}
			
			if (strlen($method_once_only_shipping_date) == 10) {
				$method_once_only_shipping_day = substr($method_once_only_shipping_date, 3, 2);
				$method_once_only_shipping_month = substr($method_once_only_shipping_date, 0, 2);
				$method_once_only_shipping_year = substr($method_once_only_shipping_date, 6, 4);
				
				$method_once_only_shipping_datetime = $method_once_only_shipping_year . '-' .
					$method_once_only_shipping_month . '-' .
					$method_once_only_shipping_day . ' ' .
					$method_once_only_shipping_time;
			} else {
				$method_once_only_shipping_datetime = 'null';
			}
		} else {
			// Error!
			$messageStack->add_session(sprintf(ERROR_DATE_FORMAT, DATE_FORMAT_SPIFFYCAL), 'error');
			
			zen_redirect(zen_href_link(FILENAME_ADVANCED_SHIPPER_METHOD_CONFIG));
		}
		
		// Check if this is an insert or an update
		$insert = false;
		$check_exists_sql = "
			SELECT
				method
			FROM
				" . TABLE_ADVANCED_SHIPPER_METHOD_CONFIGS . "
			WHERE
				config_id = '" . $config_id . "'
			AND
				method = '" . $method_num . "';";
		
		$check_exists_result = $db->Execute($check_exists_sql);
		
		if ($check_exists_result->EOF) {
			$insert = true;
		}
		
		$method_config_data_array = array(
			'select_products' => $select_products,
			'availability_scheduling' => $method_availability_scheduling,
			'once_only_start_datetime' => $method_once_only_start_datetime,
			'once_only_end_datetime' => $method_once_only_end_datetime,
			'availability_recurring_mode' => $method_availability_recurring_mode,
			'availability_weekly_start_day' => (is_null($method_availability_weekly_start_day) ? 'null' : $method_availability_weekly_start_day),
			'availability_weekly_start_time' => (is_null($method_availability_weekly_start_time) ? 'null' : $method_availability_weekly_start_time),
			'availability_weekly_cutoff_day' => (is_null($method_availability_weekly_cutoff_day) ? 'null' : $method_availability_weekly_cutoff_day),
			'availability_weekly_cutoff_time' => (is_null($method_availability_weekly_cutoff_time) ? 'null' : $method_availability_weekly_cutoff_time),
			'usage_limit' => (is_null($method_usage_limit) ? 'null' : $method_usage_limit),
			'once_only_shipping_datetime' => (is_null($method_once_only_shipping_datetime) ? 'null' : $method_once_only_shipping_datetime),
			'availability_weekly_shipping_scheduling' => $method_availability_weekly_shipping_scheduling,
			'availability_weekly_shipping_show_num_weeks' => (is_null($method_availability_weekly_shipping_show_num_weeks) ? 'null' : $method_availability_weekly_shipping_show_num_weeks),
			'availability_weekly_shipping_regular_weekday_day' => (is_null($method_availability_weekly_shipping_regular_weekday_day) ? 'null' : $method_availability_weekly_shipping_regular_weekday_day),
			'availability_weekly_shipping_regular_weekday_time' => (is_null($method_availability_weekly_shipping_regular_weekday_time) ? 'null' : $method_availability_weekly_shipping_regular_weekday_time)
			);
		
		if ($insert) {
			$method_config_data_array['config_id'] = $config_id;
			$method_config_data_array['method'] = $method_num;
			$method_config_result = zen_db_perform(TABLE_ADVANCED_SHIPPER_METHOD_CONFIGS, $method_config_data_array);
		} else {
			$selection_sql = "
					config_id = '" . $config_id . "'
				AND
					method = '" . $method_num . "';";
			
			$method_config_result = zen_db_perform(TABLE_ADVANCED_SHIPPER_METHOD_CONFIGS, $method_config_data_array, 'update', $selection_sql);
		}
		
		// Save this method's title for each language currently installed //////////////////////////
		// Remove all the existing titles first in case the list of installed languages has
		// changed
		if (!$insert) {
			$remove_titles_sql = "
				DELETE FROM
					" . TABLE_ADVANCED_SHIPPER_METHOD_ADMIN_TITLES . "
				WHERE
					config_id = '" . $config_id . "'
				AND
					method = '" . $method_num . "';";
			
			$remove_titles_result = $db->Execute($remove_titles_sql);
		}
		
		for ($language_i = 0; $language_i < $num_languages; $language_i++) {
			$method_admin_titles_data_array = array(
				'config_id' => $config_id,
				'method' => $method_num,
				'language_id' => $languages[$language_i]['id'],
				'title' => $method_admin_titles[$languages[$language_i]['id']]
				);
			
			$method_admin_titles_result = zen_db_perform(TABLE_ADVANCED_SHIPPER_METHOD_ADMIN_TITLES, $method_admin_titles_data_array);
		}
		
		if (!$insert) {
			$remove_titles_sql = "
				DELETE FROM
					" . TABLE_ADVANCED_SHIPPER_METHOD_TITLES . "
				WHERE
					config_id = '" . $config_id . "'
				AND
					method = '" . $method_num . "';";
			
			$remove_titles_result = $db->Execute($remove_titles_sql);
		}
		
		for ($language_i = 0; $language_i < $num_languages; $language_i++) {
			$method_titles_data_array = array(
				'config_id' => $config_id,
				'method' => $method_num,
				'language_id' => $languages[$language_i]['id'],
				'title' => $method_titles[$languages[$language_i]['id']]
				);
			
			$method_titles_result = zen_db_perform(TABLE_ADVANCED_SHIPPER_METHOD_TITLES, $method_titles_data_array);
		}
		
		if (!$insert) {
			// Remove any existing categories in case there are none or the list of categories has
			// changed
			$remove_categories_sql = "
				DELETE FROM
					" . TABLE_ADVANCED_SHIPPER_METHOD_CATEGORIES . "
				WHERE
					config_id = '" . $config_id . "'
				AND
					method = '" . $method_num . "';";
			
			$remove_categories_result = $db->Execute($remove_categories_sql);
			
			// Remove any existing manufacturers in case there are none or the list of manufacturers
			// has changed
			$remove_manufacturers_sql = "
				DELETE FROM
					" . TABLE_ADVANCED_SHIPPER_METHOD_MANUFACTURERS . "
				WHERE
					config_id = '" . $config_id . "'
				AND
					method = '" . $method_num . "';";
			
			$remove_manufacturers_result = $db->Execute($remove_manufacturers_sql);
			
			// Remove any existing products in case there are none or the list of products has changed
			$remove_products_sql = "
				DELETE FROM
					" . TABLE_ADVANCED_SHIPPER_METHOD_PRODUCTS . "
				WHERE
					config_id = '" . $config_id . "'
				AND
					method = '" . $method_num . "';";
			
			$remove_products_result = $db->Execute($remove_products_sql);
		}
		
		// Save any category selections for this method ////////////////////////////////////////////
		$num_categories = sizeof($categories);
		
		for ($category_i = 0; $category_i < $num_categories; $category_i++) {
			$category_order = $category_i + 1;
			
			$category_config_data_array = array(
				'config_id' => $config_id,
				'method' => $method_num,
				'category_order' => $category_order,
				'category_id' => zen_db_prepare_input($categories[$category_i]['id'])
				);
			
			$category_config_result = zen_db_perform(TABLE_ADVANCED_SHIPPER_METHOD_CATEGORIES,
				$category_config_data_array);
		}
		
		// Save any manufacturer selections for this method ////////////////////////////////////////
		$num_manufacturers = sizeof($manufacturers);
		
		for ($manufacturer_i = 0; $manufacturer_i < $num_manufacturers; $manufacturer_i++) {
			$manufacturer_order = $manufacturer_i + 1;
			
			$manufacturer_config_data_array = array(
				'config_id' => $config_id,
				'method' => $method_num,
				'manufacturer_order' => $manufacturer_order,
				'manufacturer_id' => zen_db_prepare_input($manufacturers[$manufacturer_i]['id'])
				);
			
			$manufacturer_config_result =
				zen_db_perform(TABLE_ADVANCED_SHIPPER_METHOD_MANUFACTURERS,
				$manufacturer_config_data_array);
		}
		
		// Save any product selections for this method /////////////////////////////////////////////
		$num_products = sizeof($products);
		
		for ($product_i = 0; $product_i < $num_products; $product_i++) {
			$product_order = $product_i + 1;
			
			// Check if this product has options specified
			if (strpos($products[$product_i]['id'], '-') !== false) {
				$product_id_info = explode('-', $products[$product_i]['id']);
				
				$num_product_attributes = sizeof($product_id_info) - 1;
				
				for ($product_attribute_i = 1; $product_attribute_i <= $num_product_attributes; $product_attribute_i++) {
					$product_config_data_array = array(
						'config_id' => $config_id,
						'method' => $method_num,
						'product_order' => $product_order,
						'product_id' => zen_db_prepare_input($product_id_info[0]),
						'product_attributes_id' => (is_null($product_id_info[$product_attribute_i]) ? '0' : $db->prepare_input($product_id_info[$product_attribute_i]))
						);
					
					$product_config_result = zen_db_perform(TABLE_ADVANCED_SHIPPER_METHOD_PRODUCTS,
						$product_config_data_array);
				}
			} else {
				// Product doesn't have or isn't using any options
				$product_config_data_array = array(
					'config_id' => $config_id,
					'method' => $method_num,
					'product_order' => $product_order,
					'product_id' => zen_db_prepare_input($products[$product_i]['id']),
					'product_attributes_id' => '0'
					);
				
				$product_config_result = zen_db_perform(TABLE_ADVANCED_SHIPPER_METHOD_PRODUCTS,
					$product_config_data_array);
			}
		}
		
		// Save the region configurations for this method //////////////////////////////////////////
		// Remove all the existing regions first in case the list of regions has changed
		if (!$insert) {
			$remove_regions_sql = "
				DELETE FROM
					" . TABLE_ADVANCED_SHIPPER_REGION_CONFIGS . "
				WHERE
					config_id = '" . $config_id . "'
				AND
					method = '" . $method_num . "';";
			
			$remove_regions_result = $db->Execute($remove_regions_sql);
			
			$remove_titles_sql = "
				DELETE FROM
					" . TABLE_ADVANCED_SHIPPER_REGION_ADMIN_TITLES . "
				WHERE
					config_id = '" . $config_id . "'
				AND
					method = '" . $method_num . "';";
			
			$remove_titles_result = $db->Execute($remove_titles_sql);
			
			$remove_titles_sql = "
				DELETE FROM
					" . TABLE_ADVANCED_SHIPPER_REGION_TITLES . "
				WHERE
					config_id = '" . $config_id . "'
				AND
					method = '" . $method_num . "';";
			
			$remove_titles_result = $db->Execute($remove_titles_sql);
			
			$remove_titles_sql = "
				DELETE FROM
					" . TABLE_ADVANCED_SHIPPER_REGION_SURCHARGE_TITLES . "
				WHERE
					config_id = '" . $config_id . "'
				AND
					method = '" . $method_num . "';";
			
			$remove_titles_result = $db->Execute($remove_titles_sql);
			
			$remove_ups_configs_sql = "
				DELETE FROM
					" . TABLE_ADVANCED_SHIPPER_REGION_UPS_CONFIGS . "
				WHERE
					config_id = '" . $config_id . "'
				AND
					method = '" . $method_num . "';";
			
			$remove_ups_configs_result = $db->Execute($remove_ups_configs_sql);
			
			$remove_usps_configs_sql = "
				DELETE FROM
					" . TABLE_ADVANCED_SHIPPER_REGION_USPS_CONFIGS . "
				WHERE
					config_id = '" . $config_id . "'
				AND
					method = '" . $method_num . "';";
			
			$remove_usps_configs_result = $db->Execute($remove_usps_configs_sql);
		}
		
		$num_regions = sizeof($regions);
		
		for ($region_i = 0; $region_i < $num_regions; $region_i++) {
			$region_num = $region_i + 1;
			
			$region_config_data_array = array(
				'config_id' => $config_id,
				'method' => $method_num,
				'region' => $region_num,
				'definition_method' => zen_db_prepare_input($regions[$region_num]['definition_method']),
				'countries_postcodes' => ((is_null($regions[$region_num]['countries_postcodes']) || strlen(trim($regions[$region_num]['countries_postcodes'])) == 0) ? 'null' : zen_db_prepare_input($regions[$region_num]['countries_postcodes'])),
				'countries_zones' => ((is_null($regions[$region_num]['countries_zones']) || strlen(trim($regions[$region_num]['countries_zones'])) == 0) ? 'null' : zen_db_prepare_input($regions[$region_num]['countries_zones'])),
				'countries_cities' => ((is_null($regions[$region_num]['countries_cities']) || strlen(trim($regions[$region_num]['countries_cities'])) == 0) ? 'null' : zen_db_prepare_input($regions[$region_num]['countries_cities'])),
				'countries_states' => ((is_null($regions[$region_num]['countries_states']) || strlen(trim($regions[$region_num]['countries_states'])) == 0) ? 'null' : zen_db_prepare_input($regions[$region_num]['countries_states'])),
				'distance' => ((is_null($regions[$region_num]['distance']) || strlen(trim($regions[$region_num]['distance'])) == 0) ? 'null' : zen_db_prepare_input($regions[$region_num]['distance'])),
				'tax_class' => zen_db_prepare_input($regions[$region_num]['tax_class']),
				'rates_include_tax' => $regions[$region_num]['rates_include_tax'],
				'rate_limits_inc' => $regions[$region_num]['rate_limits_inc'],
				'total_up_price_inc_tax' => $regions[$region_num]['total_up_price_inc_tax'],
				'table_of_rates' => zen_db_prepare_input($regions[$region_num]['table_of_rates']),
				'max_weight_per_package' => ((is_null($regions[$region_num]['max_weight_per_package']) || strlen(trim($regions[$region_num]['max_weight_per_package'])) == 0) ? 'null' : zen_db_prepare_input($regions[$region_num]['max_weight_per_package'])),
				'packaging_weights' => ((is_null($regions[$region_num]['packaging_weights']) || strlen(trim($regions[$region_num]['packaging_weights'])) == 0) ? 'null' : zen_db_prepare_input($regions[$region_num]['packaging_weights'])),
				'surcharge' => ((is_null($regions[$region_num]['surcharge']) || strlen(trim($regions[$region_num]['surcharge'])) == 0) ? 'null' : zen_db_prepare_input($regions[$region_num]['surcharge']))
				);
			
			$region_config_result = zen_db_perform(TABLE_ADVANCED_SHIPPER_REGION_CONFIGS,
				$region_config_data_array);
			
			// Save this region's titles for each language currently installed //////////////////////			
			for ($language_i = 0; $language_i < $num_languages; $language_i++) {
				$region_admin_titles_data_array = array(
					'config_id' => $config_id,
					'method' => $method_num,
					'region' => $region_num,
					'language_id' => $languages[$language_i]['id'],
					'title' => (strlen($regions[$region_num]['admin_titles'][$languages[$language_i]['id']]) == 0 ? 'null' : zen_db_prepare_input($regions[$region_num]['admin_titles'][$languages[$language_i]['id']]))
					);
				
				$region_admin_titles_result = zen_db_perform(TABLE_ADVANCED_SHIPPER_REGION_ADMIN_TITLES,
					$region_admin_titles_data_array);
				
				$region_titles_data_array = array(
					'config_id' => $config_id,
					'method' => $method_num,
					'region' => $region_num,
					'language_id' => $languages[$language_i]['id'],
					'title' => (strlen($regions[$region_num]['titles'][$languages[$language_i]['id']]) == 0 ? 'null' : zen_db_prepare_input($regions[$region_num]['titles'][$languages[$language_i]['id']]))
					);
				
				$region_titles_result = zen_db_perform(TABLE_ADVANCED_SHIPPER_REGION_TITLES,
					$region_titles_data_array);
				
				$region_surcharge_titles_data_array = array(
					'config_id' => $config_id,
					'method' => $method_num,
					'region' => $region_num,
					'language_id' => $languages[$language_i]['id'],
					'title' => (strlen($regions[$region_num]['surcharge_titles'][$languages[$language_i]['id']]) == 0 ? 'null' : $regions[$region_num]['surcharge_titles'][$languages[$language_i]['id']])
					);
				
				$region_surcharge_titles_result =
					zen_db_perform(TABLE_ADVANCED_SHIPPER_REGION_SURCHARGE_TITLES,
					$region_surcharge_titles_data_array);
			}
			
			// Save any UPS calculator settings
			if (!is_null($regions[$region_num]['ups_config'])) {
				$region_ups_config_data_array = array(
					'config_id' => $config_id,
					'method' => $method_num,
					'region' => $region_num,
					'source_country' => zen_db_prepare_input($regions[$region_num]['ups_config']['source_country']),
					'source_postcode' => zen_db_prepare_input($regions[$region_num]['ups_config']['source_postcode']),
					'pickup_method' => zen_db_prepare_input($regions[$region_num]['ups_config']['pickup_method']),
					'packaging' => zen_db_prepare_input($regions[$region_num]['ups_config']['packaging']),
					'delivery_type' => zen_db_prepare_input($regions[$region_num]['ups_config']['delivery_type']),
					'shipping_service_1dm' => zen_db_prepare_input($regions[$region_num]['ups_config']['shipping_service_1dm']),
					'shipping_service_1dml' => zen_db_prepare_input($regions[$region_num]['ups_config']['shipping_service_1dml']),
					'shipping_service_1da' => zen_db_prepare_input($regions[$region_num]['ups_config']['shipping_service_1da']),
					'shipping_service_1dal' => zen_db_prepare_input($regions[$region_num]['ups_config']['shipping_service_1dal']),
					'shipping_service_1dapi' => zen_db_prepare_input($regions[$region_num]['ups_config']['shipping_service_1dapi']),
					'shipping_service_1dp' => zen_db_prepare_input($regions[$region_num]['ups_config']['shipping_service_1dp']),
					'shipping_service_1dpl' => zen_db_prepare_input($regions[$region_num]['ups_config']['shipping_service_1dpl']),
					'shipping_service_2dm' => zen_db_prepare_input($regions[$region_num]['ups_config']['shipping_service_2dm']),
					'shipping_service_2dml' => zen_db_prepare_input($regions[$region_num]['ups_config']['shipping_service_2dml']),
					'shipping_service_2da' => zen_db_prepare_input($regions[$region_num]['ups_config']['shipping_service_2da']),
					'shipping_service_2dal' => zen_db_prepare_input($regions[$region_num]['ups_config']['shipping_service_2dal']),
					'shipping_service_3ds' => zen_db_prepare_input($regions[$region_num]['ups_config']['shipping_service_3ds']),
					'shipping_service_gnd' => zen_db_prepare_input($regions[$region_num]['ups_config']['shipping_service_gnd']),
					'shipping_service_std' => zen_db_prepare_input($regions[$region_num]['ups_config']['shipping_service_std']),
					'shipping_service_xpr' => zen_db_prepare_input($regions[$region_num]['ups_config']['shipping_service_xpr']),
					'shipping_service_xprl' => zen_db_prepare_input($regions[$region_num]['ups_config']['shipping_service_xprl']),
					'shipping_service_xdm' => zen_db_prepare_input($regions[$region_num]['ups_config']['shipping_service_xdm']),
					'shipping_service_xdml' => zen_db_prepare_input($regions[$region_num]['ups_config']['shipping_service_xdml']),
					'shipping_service_xpd' => zen_db_prepare_input($regions[$region_num]['ups_config']['shipping_service_xpd']),
					'shipping_service_wxs' => zen_db_prepare_input($regions[$region_num]['ups_config']['shipping_service_wxs'])
					);
				
				$region_ups_config_result =
					zen_db_perform(TABLE_ADVANCED_SHIPPER_REGION_UPS_CONFIGS,
					$region_ups_config_data_array);
			}
			
			// Save any USPS calculator settings
			if (!is_null($regions[$region_num]['usps_config'])) {
				$region_usps_config_data_array = array(
					'config_id' => $config_id,
					'method' => $method_num,
					'region' => $region_num,
					'user_id' => zen_db_prepare_input($regions[$region_num]['usps_config']['user_id']),
					'server' => zen_db_prepare_input($regions[$region_num]['usps_config']['server']),
					'source_country' => zen_db_prepare_input($regions[$region_num]['usps_config']['source_country']),
					'source_postcode' => zen_db_prepare_input($regions[$region_num]['usps_config']['source_postcode']),
					'machinable' => zen_db_prepare_input($regions[$region_num]['usps_config']['machinable']),
					'display_transit_time' => zen_db_prepare_input($regions[$region_num]['usps_config']['display_transit_time']),
					'domestic_express' => zen_db_prepare_input($regions[$region_num]['usps_config']['domestic_express']),
					'domestic_priority' => zen_db_prepare_input($regions[$region_num]['usps_config']['domestic_priority']),
					'domestic_first_class' => zen_db_prepare_input($regions[$region_num]['usps_config']['domestic_first_class']),
					'domestic_parcel' => zen_db_prepare_input($regions[$region_num]['usps_config']['domestic_parcel']),
					'domestic_media' => zen_db_prepare_input($regions[$region_num]['usps_config']['domestic_media']),
					'domestic_bpm' => zen_db_prepare_input($regions[$region_num]['usps_config']['domestic_bpm']),
					'domestic_library' => zen_db_prepare_input($regions[$region_num]['usps_config']['domestic_library']),
					'international_ge' => zen_db_prepare_input($regions[$region_num]['usps_config']['international_ge']),
					'international_gendr' => zen_db_prepare_input($regions[$region_num]['usps_config']['international_gendr']),
					'international_gendnr' => zen_db_prepare_input($regions[$region_num]['usps_config']['international_gendnr']),
					'international_emi' => zen_db_prepare_input($regions[$region_num]['usps_config']['international_emi']),
					'international_emifre' => zen_db_prepare_input($regions[$region_num]['usps_config']['international_emifre']),
					'international_pmi' => zen_db_prepare_input($regions[$region_num]['usps_config']['international_pmi']),
					'international_pmifre' => zen_db_prepare_input($regions[$region_num]['usps_config']['international_pmifre']),
					'international_pmifrb' => zen_db_prepare_input($regions[$region_num]['usps_config']['international_pmifrb']),
					'international_fcmile' => zen_db_prepare_input($regions[$region_num]['usps_config']['international_fcmile']),
					'international_fcmip' => zen_db_prepare_input($regions[$region_num]['usps_config']['international_fcmip']),
					'international_fcmil' => zen_db_prepare_input($regions[$region_num]['usps_config']['international_fcmil']),
					'international_fcmif' => zen_db_prepare_input($regions[$region_num]['usps_config']['international_fcmif']),
					'international_fcmipar' => zen_db_prepare_input($regions[$region_num]['usps_config']['international_fcmipar'])
					);
				
				$region_usps_config_result =
					zen_db_perform(TABLE_ADVANCED_SHIPPER_REGION_USPS_CONFIGS,
					$region_usps_config_data_array);
			}
		}
	}
	
	if (sizeof($errors) == 0) {
		// Get the name of the config
		$config_name_sql = "
			SELECT
				config_name
			FROM
				" . TABLE_ADVANCED_SHIPPER_CONFIGS . "
			WHERE
				config_id = '" . $config_id . "';";
		
		$config_name_result = $db->Execute($config_name_sql);
		
		$config_name = $config_name_result->fields['config_name'];
		
		if ($advshipper_demo) {
			$messageStack->add_session(sprintf(SUCCESS_CONFIGURATION_SAVED_DEMO,
				$method_num, $config_name), 'success');
		} else {
			$messageStack->add_session(sprintf(SUCCESS_CONFIGURATION_SAVED,
				$method_num, $config_name), 'success');
		}
		
		zen_redirect(zen_href_link(FILENAME_ADVANCED_SHIPPER, zen_get_all_get_params(array('action', 'method_num')) ));
	}
}

?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
	<title><?php echo TITLE; ?></title>
	<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
	<link rel="stylesheet" type="text/css" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
	<script language="javascript" src="includes/menu.js"></script>
	<script language="javascript" src="includes/general.js"></script>
	<script language="javascript"  type="text/javascript">
	<!--
<?php require(DIR_WS_INCLUDES . 'javascript/advshipper_method_config.js'); ?>
	//-->
	</script>
	<link rel="stylesheet" type="text/css" href="includes/javascript/spiffyCal/spiffyCal_v2_1.css">
	<script language="JavaScript" src="includes/javascript/spiffyCal/spiffyCal_v2_1.js"></script>
	
	<script type="text/javascript">
		<!--
		function init()
		{
			cssjsmenu('navbar');
			if (document.getElementById)
			{
				var kill = document.getElementById('hoverJS');
				kill.disabled = true;
			}
		}
		// -->
	</script>
	<style type="text/css">
	.AdvancedShipperPageHeading { padding-bottom: 1.5em; }
	fieldset { padding: 0.8em 0.8em; margin-bottom: 2.5em; }
	fieldset fieldset { margin-bottom: 1em; }
	legend { font-weight: bold; font-size: 1.3em; }
	
	.AdvancedShipperMethodOdd {
		background-color: #d0d0d0;
	}
	.AdvancedShipperMethodEven {
		background-color: #f3f3f3;
	}
	.AdvancedShipperMethodOddTitle,
	.AdvancedShipperMethodOddCalculationMethod,
	.AdvancedShipperMethodOddAvailabilityScheduling,
	.AdvancedShipperMethodOddDeliveryScheduling {
		background-color: #d9d9d9;
	}
	.AdvancedShipperMethodEvenTitle,
	.AdvancedShipperMethodEvenCalculationMethod,
	.AdvancedShipperMethodEvenAvailabilityScheduling,
	.AdvancedShipperMethodEvenDeliveryScheduling {
		background-color: #fafafa;
	}
	
	.AdvancedShipperMethodOddCategoryProductSelection,
	.AdvancedShipperMethodOddRegionsConfiguration {
		background-color: #f3f3f3;
	}
	.AdvancedShipperMethodEvenCategoryProductSelection,
	.AdvancedShipperMethodEvenRegionsConfiguration {
		background-color: #eeffee;
	}
	
	.AdvancedShipperMethodOddRegionsConfigurationOdd {
		background-color: #fafafa;
	}
	.AdvancedShipperMethodOddRegionsConfigurationEven {
		background-color: #efefef;
	}
	.AdvancedShipperMethodEvenRegionsConfigurationOdd {
		background-color: #f5fff5;
	}
	.AdvancedShipperMethodEvenRegionsConfigurationEven {
		background-color: #e3ffe3;
	}
	
	.AdvancedShipperConfigLabel, .AdvancedShipperConfigField, .AdvancedShipperConfigDesc,
	.AdvancedShipperConfigButtonPanel {
		vertical-align: top;
	}
	.AdvancedShipperConfigLabel { font-weight: bold; padding-right: 1em; }
	.AdvancedShipperConfigLabel { width: 20%; }
	.AdvancedShipperConfigField { padding-bottom: 1.3em; }
	.AdvancedShipperConfigIntro { padding-top: 0.5em; padding-bottom:1.1em;  }
	.AdvancedShipperConfigButtonPanel { text-align: right; margin-bottom: 0.8em; width: 15em; }
	
	fieldset.AdvancedShipperAddressMatching { padding: 0.3em 0.8em; }
	fieldset.AdvancedShipperAddressMatching legend { font-size: 1em; }
	fieldset.AdvancedShipperAddressMatching p { padding: 0; margin: 0 0 0.5em 0; }
	
	.ErrorIntro { margin: 2em 0; color: #f00; }
	.FormError { font-weight: bold; color: #f00; }
	
	.Collapse { display:  none; }
	</style>
	<script language="JavaScript" src="<?php echo DIR_WS_INCLUDES . 'javascript/cba.js' ?>"></script>
</head>
<body onload="init()">
<div id="spiffycalendar" class="text"></div>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<?php echo zen_draw_form('advshipper', FILENAME_ADVANCED_SHIPPER_METHOD_CONFIG, zen_get_all_get_params(), 'post', 'onsubmit="" id="advshipper"', true);
echo zen_hide_session_id(); ?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
	<tr>
<!-- body_text //-->
		<td width="100%" valign="top">
			<table border="0" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td class="pageHeading AdvancedShipperPageHeading"><?php echo HEADING_TITLE; ?></td>
				</tr>
				<tr>
					<td>
<?php
$num_errors = sizeof($errors);
// Check if more than one language has not had a title entered for it 
if (isset($errors['method_title'])) {
	$num_errors += sizeof($errors['method_title']) - 1;
}

if ($num_errors == 1) {
	echo '<p class="ErrorIntro">' . TEXT_ERROR_IN_CONFIG;
} else if ($num_errors > 0 ) {
	printf('<p class="ErrorIntro">' . TEXT_ERRORS_IN_CONFIG, $num_errors);
} else {
	echo '<p>';
}
?>
						</p>	
					</td>
				</tr>
				<tr>
					<td>
<fieldset class="AdvancedShipperMethod<?php echo ((($method_num + 1) % 2 == 0) ? 'Odd' : 'Even'); ?>">
	<legend><?php
	echo TEXT_METHOD . ' ' . $method_num;
	if (strlen($method_admin_titles[$_SESSION['languages_id']]) > 0) {
		echo ' - &ldquo;' . htmlentities($method_admin_titles[$_SESSION['languages_id']], ENT_COMPAT, CHARSET) . '&rdquo;';
	}
?></legend>
	<table border="0" width="100%" cellpadding="0" cellspacing="0" id="config">
		<tr>
			<td colspan="2">
				<fieldset class="AdvancedShipperMethod<?php echo ((($method_num + 1) % 2 == 0) ? 'Odd' : 'Even') .
					'Title'; ?>">
					<legend><?php echo TEXT_METHOD_TITLES; ?></legend>
					<table border="0" width="100%" cellpadding="0" cellspacing="0">
						<tr>
							<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="<?php echo "method_admin_titles"; ?>"><?php echo TEXT_LABEL_METHOD_ADMIN_TITLE; ?></label></td>
							<td class="AdvancedShipperConfigDesc">
								<?php echo TEXT_CONFIG_DESC_METHOD_ADMIN_TITLE; ?>
							</td>
						</tr>
						<tr>
							<td class="AdvancedShipperConfigField">
				<?php
					for ($language_i = 0; $language_i < $num_languages; $language_i++) {
						if ($language_i != 0) {
							echo '<br />';
						}
						echo zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$language_i]['directory'] . '/images/' . $languages[$language_i]['image'], $languages[$language_i]['name']);
						echo ' ';
						echo zen_draw_input_field('method_admin_titles[' . $languages[$language_i]['id'] . ']', $method_admin_titles[$languages[$language_i]['id']], 'maxlength="255" size="45" onKeyPress="advshipperCheckEnterPressed(event)"');
						echo "\n";
					}
				?>
							</td>
						</tr>
						<tr>
							<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="<?php echo "method_titles"; ?>"><?php echo TEXT_LABEL_METHOD_TITLE; ?></label></td>
							<td class="AdvancedShipperConfigDesc">
								<?php echo TEXT_CONFIG_DESC_METHOD_TITLE; ?>
							</td>
						</tr>
						<tr>
							<td class="AdvancedShipperConfigField">
				<?php
					for ($language_i = 0; $language_i < $num_languages; $language_i++) {
						if ($language_i != 0) {
							echo '<br />';
						}
						echo zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$language_i]['directory'] . '/images/' . $languages[$language_i]['image'], $languages[$language_i]['name']);
						echo ' ';
						echo zen_draw_input_field('method_titles[' . $languages[$language_i]['id'] . ']', $method_titles[$languages[$language_i]['id']], 'maxlength="255" size="45" onKeyPress="advshipperCheckEnterPressed(event)"');
						echo "\n";
						if (isset($errors['method_title'][$languages[$language_i]['id']])) {
							// Display message about missing title
							echo '<p class="FormError">' . $errors['method_title'][$languages[$language_i]['id']] . ' </p>';
						}
					}
				?>
							</td>
						</tr>
					</table>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<fieldset class="AdvancedShipperMethod<?php echo ((($method_num + 1) % 2 == 0) ? 'Odd' : 'Even') .
					'CategoryProductSelection'; ?>">
					<legend><?php echo TEXT_CATEGORY_PRODUCT_SELECTION; ?></legend>
					<table border="0" width="100%" cellpadding="0" cellspacing="0">
						<tr>
							<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="<?php echo "select_products"; ?>"><?php echo TEXT_LABEL_PRODUCTS_CATEGORIES_MANUFACTURERS; ?></label></td>
							<td class="AdvancedShipperConfigDesc">
								<?php echo TEXT_CONFIG_DESC_PRODUCTS_CATEGORIES_MANUFACTURERS; ?>
							</td>
						</tr>
						<tr>
							<td class="AdvancedShipperConfigField">
								<?php
								$select_products_fallover_selected = (($select_products == ADVSHIPPER_SELECT_PRODUCT_FALLOVER) ? 'selected' : '');
								$select_products_specific_selected = (($select_products == ADVSHIPPER_SELECT_PRODUCT_SPECIFIC) ? 'selected' : '');
								
								if ($select_products_fallover_selected == '' &&
										$select_products_specific_selected == '') {
									$select_products_fallover_selected = 'selected';
								}
								
								echo '<p>' . advshipper_draw_radio_field('select_products', ADVSHIPPER_SELECT_PRODUCT_FALLOVER, $select_products_fallover_selected, '', 'id="select_products_fallover" onKeyPress="advshipperCheckEnterPressed(event)"') . '<label class="attribsRadioButton" for="select_products_fallover">' . TEXT_SELECT_FALLOVER_PRODUCTS . '</label>' . "\n";
								echo '<br />' . advshipper_draw_radio_field('select_products', ADVSHIPPER_SELECT_PRODUCT_SPECIFIC, $select_products_specific_selected, '', 'id="select_products_specific" onKeyPress="advshipperCheckEnterPressed(event)"') . '<label class="attribsRadioButton" for="select_products_specific">' . TEXT_SELECT_SPECIFIC_CATEGORIES_MANUFACTURERS_PRODUCTS . '</label>' . "</p>\n";
								
								?>
							</td>
						</tr>
					</table>
					<table border="0" width="100%" cellpadding="0" cellspacing="0" id="select_categories_products_panel">
						<tr>
							<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="<?php echo "current_categories"; ?>"><?php echo TEXT_LABEL_CATEGORIES; ?></label></td>
							<td class="AdvancedShipperConfigDesc">
								<?php echo TEXT_CONFIG_DESC_CATEGORIES; ?>
							</td>
						</tr>
						<tr>
							<td class="AdvancedShipperConfigField">
								<?php
								// List each category and add buttons to delete categories or add a
								// new category
								$categories_string = '';
								
								$num_categories = sizeof($categories);
								
								// Build the javascript necessary to set up the run-time storage of
								// data about the categories
								?>
<script language="JavaScript" type="text/javascript">
<!--
	var new_category_i = 0;
								<?php
								for ($category_i = 0; $category_i < $num_categories; $category_i++) {
								?>
	new_category_i = categories.length;
	categories[new_category_i] = new Object();
	categories[new_category_i].category_id = '<?php echo addslashes($categories[$category_i]['id']); ?>';
	categories[new_category_i].name = '<?php echo addslashes($categories[$category_i]['name']); ?>';
								<?php
								}
								?>
// -->
</script>
								<fieldset class="AdvancedShipperAddressMatching" id="current_categories" <?php if ($num_categories == 0) { echo ' style="display: none;"'; } ?>>
									<legend>
								<?php
								echo TEXT_CURRENT_CATEGORIES;
								?>
									</legend>
								<?php
								for ($category_i = 0; $category_i < $num_categories; $category_i++) {
									if ($categories_string != '') {
										$categories_string .= '||';
									}
									$categories_string .= $categories[$category_i]['id'] . '|' . $categories[$category_i]['name'];
									
									echo '<p id="category_name_' . $categories[$category_i]['id'] . '">' . htmlentities($categories[$category_i]['name'], ENT_COMPAT, CHARSET);
									echo ' ' . zen_draw_input_field('category_delete_' . $categories[$category_i]['id'], IMAGE_DELETE, 'id="category_delete_' . $categories[$category_i]['id'] . '" onclick="javascript:advshipperDeleteCategory(\'' . $categories[$category_i]['id'] . '\');return false;"', false, 'submit') . '</p>';
								}
								?>
								</fieldset>
								<?php
								echo '<p>' . zen_draw_input_field('category_add', IMAGE_INSERT, 'id="category_add" onclick="javascript:advshipperCategorySelection(\'' . zen_href_link(FILENAME_ADVANCED_SHIPPER_CATEGORY_SELECTOR) . '\');return false;"', false, 'submit') . '</p>';
								echo zen_draw_hidden_field('categories', $categories_string, 'id="categories"');
								?>
							</td>
						</tr>
						<tr>
							<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="<?php echo "current_manufacturers"; ?>"><?php echo TEXT_LABEL_MANUFACTURERS; ?></label></td>
							<td class="AdvancedShipperConfigDesc">
								<?php echo TEXT_CONFIG_DESC_MANUFACTURERS; ?>
							</td>
						</tr>
						<tr>
							<td class="AdvancedShipperConfigField">
								<?php
								// List each manufacturer and add buttons to delete manufacturers or
								// add a new manufacturer
								$manufacturers_string = '';
								
								$num_manufacturers = sizeof($manufacturers);
								
								// Build the javascript necessary to set up the run-time storage of
								// data about the manufacturers
								?>
<script language="JavaScript" type="text/javascript">
<!--
	var new_manufacturer_i = 0;
								<?php
								for ($manufacturer_i = 0; $manufacturer_i < $num_manufacturers; $manufacturer_i++) {
								?>
	new_manufacturer_i = manufacturers.length;
	manufacturers[new_manufacturer_i] = new Object();
	manufacturers[new_manufacturer_i].manufacturer_id = '<?php echo addslashes($manufacturers[$manufacturer_i]['id']); ?>';
	manufacturers[new_manufacturer_i].name = '<?php echo addslashes($manufacturers[$manufacturer_i]['name']); ?>';
								<?php
								}
								?>
// -->
</script>
								<fieldset class="AdvancedShipperAddressMatching" id="current_manufacturers" <?php if ($num_manufacturers == 0) { echo ' style="display: none;"'; } ?>>
									<legend>
								<?php
								echo TEXT_CURRENT_MANUFACTURERS;
								?>
									</legend>
								<?php
								for ($manufacturer_i = 0; $manufacturer_i < $num_manufacturers; $manufacturer_i++) {
									if ($manufacturers_string != '') {
										$manufacturers_string .= '||';
									}
									$manufacturers_string .= $manufacturers[$manufacturer_i]['id'] . '|' . $manufacturers[$manufacturer_i]['name'];
									
									echo '<p id="manufacturer_name_' . $manufacturers[$manufacturer_i]['id'] . '">' . htmlentities($manufacturers[$manufacturer_i]['name'], ENT_COMPAT, CHARSET);
									echo ' ' . zen_draw_input_field('manufacturer_delete_' . $manufacturers[$manufacturer_i]['id'], IMAGE_DELETE, 'id="manufacturer_delete_' . $manufacturers[$manufacturer_i]['id'] . '" onclick="javascript:advshipperDeleteManufacturer(\'' . $manufacturers[$manufacturer_i]['id'] . '\');return false;"', false, 'submit') . '</p>';
								}
								?>
								</fieldset>
								<?php
								echo '<p>' . zen_draw_input_field('manufacturer_add', IMAGE_INSERT, 'id="manufacturer_add" onclick="javascript:advshipperManufacturerSelection(\'' . zen_href_link(FILENAME_ADVANCED_SHIPPER_MANUFACTURER_SELECTOR) . '\');return false;"', false, 'submit') . '</p>';
								echo zen_draw_hidden_field('manufacturers', $manufacturers_string, 'id="manufacturers"');
								?>
							</td>
						</tr>
						<tr>
							<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="<?php echo "current_products"; ?>"><?php echo TEXT_LABEL_PRODUCTS; ?></label></td>
							<td class="AdvancedShipperConfigDesc">
								<?php echo TEXT_CONFIG_DESC_PRODUCTS; ?>
							</td>
						</tr>
						<tr>
							<td class="AdvancedShipperConfigField">
								<?php
								// List each product and add buttons to delete products or add a
								// new product
								$products_string = '';
								
								$num_products = sizeof($products);
								
								// Build the javascript necessary to set up the run-time storage of
								// data about the products
								?>
<script language="JavaScript" type="text/javascript">
<!--
	var new_product_i = 0;
								<?php
								for ($product_i = 0; $product_i < $num_products; $product_i++) {
									if ($products_string != '') {
										$products_string .= '||';
									}
									$products_string .= $products[$product_i]['id'] . '|' . $products[$product_i]['name'];
								?>
	new_product_i = products.length;
	products[new_product_i] = new Object();
	products[new_product_i].product_id = '<?php echo addslashes($products[$product_i]['id']); ?>';
	products[new_product_i].name = '<?php echo addslashes($products[$product_i]['name']); ?>';
								<?php
								}
								?>
// -->
</script>
								<fieldset class="AdvancedShipperAddressMatching" id="current_products" <?php if ($num_products == 0) { echo ' style="display: none;"'; } ?>>
									<legend>
								<?php
								echo TEXT_CURRENT_PRODUCTS;
								?>
									</legend>
								<?php
								for ($product_i = 0; $product_i < $num_products; $product_i++) {
									echo '<p id="product_name_' . $products[$product_i]['id'] . '">' . htmlentities($products[$product_i]['name'], ENT_COMPAT, CHARSET);
									echo ' ' . zen_draw_input_field('product_delete_' . $products[$product_i]['id'], IMAGE_DELETE, 'id="product_delete_' . $products[$product_i]['id'] . '" onClick="javascript:advshipperDeleteProduct(\'' . $products[$product_i]['id'] . '\');return false;"', false, 'submit') . '</p>';
								}
								?>
								</fieldset>
								<?php
								echo '<p>' . zen_draw_input_field('product_add', IMAGE_INSERT, 'id="product_add" onclick="javascript:advshipperProductSelection(\'' . zen_href_link(FILENAME_ADVANCED_SHIPPER_PRODUCT_SELECTOR) . '\');return false;"', false, 'submit') . '</p>';
								echo zen_draw_hidden_field('products', htmlspecialchars($products_string), 'id="products"');
								?>
							</td>
						</tr>
					</table>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<fieldset class="AdvancedShipperMethod<?php echo ((($method_num + 1) % 2 == 0) ? 'Odd' : 'Even') .
					'RegionsConfiguration'; ?>">
					<legend><?php echo TEXT_REGIONS_CONFIGURATION; ?></legend>
					<table border="0" width="100%" cellpadding="0" cellspacing="0">
						<tr>
							<td class="AdvancedShipperConfigLabel"><label for="<?php echo "regions_configuration"; ?>"><?php echo TEXT_LABEL_REGIONS_AND_RATES; ?></label></td>
							<td class="AdvancedShipperConfigField" id="regions_panel">
								<?php
								// List each region configuration and add buttons to delete region
								// configurations or add a new one
								echo zen_draw_hidden_field('regions_info', '', 'id="regions_info"');
								
								$num_regions = sizeof($regions);
								?>
<script language="JavaScript" type="text/javascript">
<!--
	var admin_titles = new Array();
	var titles = new Array();
	var countries_zones = new Array();
	var countries_states = new Array();
	var countries_cities = new Array();
	var new_title_i = 0;
	var new_zone_i = 0;
	var new_state_i = 0;
	var new_city_i = 0;
	var surcharge_titles = new Array();
	<?php
	// Populate the region array
	for ($region_i = 1; $region_i <= $num_regions; $region_i++) {
		// Get the titles entered for each language
		$current_region_admin_titles = array();
		$current_region_titles = array();
?>
var _admin_titles_string = null;
	var _titles_string = null;
	var _countries_zones_string = null;
	var _countries_states_string = null;
	var _countries_cities_string = null;
	var _surcharge_titles_string = null;
	var _ups_calc_string = null;
	var _usps_calc_string = null;
	
	admin_titles = new Array();
	titles = new Array();
	surcharge_titles = new Array();
<?php
		for ($language_i = 0; $language_i < $num_languages; $language_i++) {
			?>
	new_title_i = admin_titles.length;
	admin_titles[new_title_i] = '<?php echo $languages[$language_i]['id']; ?>|<?php echo (isset($regions[$region_i]['admin_titles'][$languages[$language_i]['id']]) && strlen($regions[$region_i]['admin_titles'][$languages[$language_i]['id']]) > 0 ? addslashes($regions[$region_i]['admin_titles'][$languages[$language_i]['id']]) : 'null'); ?>';
	
	titles[new_title_i] = '<?php echo $languages[$language_i]['id']; ?>|<?php echo (isset($regions[$region_i]['titles'][$languages[$language_i]['id']]) && strlen($regions[$region_i]['titles'][$languages[$language_i]['id']]) > 0 ? addslashes($regions[$region_i]['titles'][$languages[$language_i]['id']]) : 'null'); ?>';
	
	surcharge_titles[new_title_i] = '<?php echo $languages[$language_i]['id']; ?>|<?php echo (isset($regions[$region_i]['surcharge_titles'][$languages[$language_i]['id']]) && strlen($regions[$region_i]['surcharge_titles'][$languages[$language_i]['id']]) > 0 ? addslashes($regions[$region_i]['surcharge_titles'][$languages[$language_i]['id']]) : 'null'); ?>';
			<?php
		}
		?>
	_admin_titles_string = admin_titles.join('||');
	_titles_string = titles.join('||');
	_surcharge_titles_string = surcharge_titles.join('||');
		<?php
		// Build the list of zones for this region
		if (function_exists('advshipper_zones_get_ids_and_names_for_zones_string')) {
			$countries_zones_info = advshipper_zones_get_ids_and_names_for_zones_string($regions[$region_i]['countries_zones']);
			
			$num_zones = sizeof($countries_zones_info);
		} else {
			$num_zones = 0;
		}
		
		if ($num_zones > 0) {
?>
	countries_zones = new Array();
<?php
			for ($zone_i = 0; $zone_i < $num_zones; $zone_i++) {
		
?>	new_zone_i = countries_zones.length;
	countries_zones[new_zone_i] = '<?php echo addslashes($countries_zones_info[$zone_i]['id']); ?>|<?php echo addslashes($countries_zones_info[$zone_i]['name']); ?>';
		<?php
			}
		?>
		_countries_zones_string = countries_zones.join('||');
		<?php
		}
		
		// Build the list of states for this region
		if (function_exists('localities_parse_identifiers_string')) {
			$countries_states_info = localities_parse_identifiers_string($regions[$region_i]['countries_states']);
			
			$num_states = sizeof($countries_states_info);
		} else {
			$num_states = 0;
		}
		
		if ($num_states > 0) {
?>
	countries_states = new Array();
<?php
			for ($state_i = 0; $state_i < $num_states; $state_i++) {
		
?>	new_state_i = countries_states.length;
	countries_states[new_state_i] = '<?php echo addslashes(localities_encode_identifier_string($countries_states_info[$state_i]['level_2_id'], null)); ?>|<?php echo addslashes(localities_get_level_2_locality_name($countries_states_info[$state_i]['level_2_id'], null, true)); ?>';
		<?php
			}
		?>
		_countries_states_string = countries_states.join('||');
		<?php
		}
		
		// Build the list of cities for this region
		if (function_exists('localities_parse_identifiers_string')) {
			$countries_cities_info = localities_parse_identifiers_string($regions[$region_i]['countries_cities']);
			
			$num_cities = sizeof($countries_cities_info);
		} else {
			$num_cities = 0;
		}
		
		if ($num_cities > 0) {
?>
	countries_cities = new Array();
<?php
			for ($city_i = 0; $city_i < $num_cities; $city_i++) {
		?>
	new_city_i = countries_cities.length;
	countries_cities[new_city_i] = '<?php echo addslashes(localities_encode_identifier_string($countries_cities_info[$city_i]['level_2_id'], $countries_cities_info[$city_i]['level_3_id'])); ?>|<?php echo addslashes(localities_get_level_3_locality_name($countries_cities_info[$city_i]['level_3_id'], null, true)); ?>';
		<?php
			}
		?>
		_countries_cities_string = countries_cities.join('||');
		<?php
		}
		
		if (is_array($regions[$region_i]['ups_config'])) {
			$ups_calc_string = implode('|', $regions[$region_i]['ups_config']);
			
			echo "\t_ups_calc_string='" . $ups_calc_string . "';\n";
		}
		
		if (is_array($regions[$region_i]['usps_config'])) {
			$usps_calc_string = implode('|', $regions[$region_i]['usps_config']);
			
			echo "\t_usps_calc_string='" . $usps_calc_string . "';\n";
		}
		
		echo "\tadvshipperInsertRegion('" . ($region_i - 1) . "', _admin_titles_string, _titles_string, '" . $regions[$region_i]['definition_method'] . "', '" . str_replace("\n", '\n', str_replace("\r", '\n', addslashes($regions[$region_i]['countries_postcodes']))) . "', _countries_zones_string, _countries_states_string, _countries_cities_string, '" . $regions[$region_i]['distance'] . "', '" . $regions[$region_i]['tax_class'] . "', '" . $regions[$region_i]['rates_include_tax'] . "', '" . $regions[$region_i]['rate_limits_inc'] . "', '" . $regions[$region_i]['total_up_price_inc_tax'] . "', '" . str_replace("\n", '\n', str_replace("\r", '\n', addslashes($regions[$region_i]['table_of_rates']))) . "', '" . addslashes($regions[$region_i]['max_weight_per_package']) . "', '" . addslashes($regions[$region_i]['packaging_weights']) . "', '" . addslashes($regions[$region_i]['surcharge']) . "', _surcharge_titles_string, _ups_calc_string, _usps_calc_string);\n";
		}
	?>
// -->
</script>
<script language="JavaScript" type="text/javascript">
<!--
advshipperBuildRegionsPanel();
// -->
</script>
								<?php
								echo '<p id="region_add_holder">' . zen_draw_input_field('region_add', IMAGE_INSERT, 'id="region_add" onclick="javascript:advshipperRegionConfig(\'' . zen_href_link(FILENAME_ADVANCED_SHIPPER_REGION_CONFIG, 'region=-1') . '\');return false;"', false, 'submit') . '</p>';
								?></td>
						</tr>
					</table>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<fieldset class="AdvancedShipperMethod<?php echo ((($method_num + 1) % 2 == 0) ? 'Odd' : 'Even') .
					'AvailabilityScheduling'; ?>">
					<legend><?php echo TEXT_METHOD_AVAILABILITY_SCHEDULING; ?></legend>
					<table border="0" width="100%" cellpadding="0" cellspacing="0">
						<tr>
							<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="<?php echo "method_availability_scheduling"; ?>"><?php echo TEXT_LABEL_METHOD_AVAILABILITY_SCHEDULING; ?></label></td>
							<td class="AdvancedShipperConfigDesc">
								<?php echo TEXT_CONFIG_DESC_METHOD_AVAILABILITY_SCHEDULING; ?>
							</td>
						</tr>
						<tr>
							<td class="AdvancedShipperConfigField">
								<?php echo advshipper_draw_radio_field('method_availability_scheduling', ADVSHIPPER_AVAILABILITY_SCHEDULING_ALWAYS, $method_availability_scheduling == ADVSHIPPER_AVAILABILITY_SCHEDULING_ALWAYS, null, 'onclick="advshipperMethodAvailabilitySchedulingSelected(' . ADVSHIPPER_AVAILABILITY_SCHEDULING_ALWAYS . ');" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_METHOD_AVAILABILITY_SCHEDULING_ALWAYS; ?>
								<br /><?php echo advshipper_draw_radio_field('method_availability_scheduling', ADVSHIPPER_AVAILABILITY_SCHEDULING_ONCE_ONLY, $method_availability_scheduling == ADVSHIPPER_AVAILABILITY_SCHEDULING_ONCE_ONLY, null, 'onclick="advshipperMethodAvailabilitySchedulingSelected(' . ADVSHIPPER_AVAILABILITY_SCHEDULING_ONCE_ONLY . ');" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_METHOD_AVAILABILITY_SCHEDULING_ONCE_ONLY; ?>
								<br /><?php echo advshipper_draw_radio_field('method_availability_scheduling', ADVSHIPPER_AVAILABILITY_SCHEDULING_RECURRING, $method_availability_scheduling == ADVSHIPPER_AVAILABILITY_SCHEDULING_RECURRING, null, 'onclick="advshipperMethodAvailabilitySchedulingSelected(' . ADVSHIPPER_AVAILABILITY_SCHEDULING_RECURRING . ');" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_METHOD_AVAILABILITY_SCHEDULING_RECURRING; ?>
							</td>
						</tr>
						<tr <?php echo ($method_availability_scheduling == ADVSHIPPER_AVAILABILITY_SCHEDULING_ONCE_ONLY ? '' : 'style="display: none;"'); ?> id="method_once_only_start_date_header">
							<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="<?php echo 'method_once_only_start_date'; ?>"><?php echo TEXT_LABEL_METHOD_ONCE_ONLY_START_DATE; ?></label></td>
							<td class="AdvancedShipperConfigDesc">
								<?php echo TEXT_CONFIG_DESC_METHOD_ONCE_ONLY_START_DATE; ?>
							</td>
						</tr>
						<tr <?php echo ($method_availability_scheduling == ADVSHIPPER_AVAILABILITY_SCHEDULING_ONCE_ONLY ? '' : 'style="display: none;"'); ?> id="method_once_only_start_date_field">
							<td class="AdvancedShipperConfigField">
								<script language="javascript">
									var <?php echo 'method_once_only_start_date'; ?> = new ctlSpiffyCalendarBox("<?php echo 'method_once_only_start_date'; ?>", "advshipper", "<?php echo 'method_once_only_start_date' . ''; ?>", "<?php echo 'method_once_only_start_date' . '_button'; ?>", "<?php echo $method_once_only_start_date; ?>", scBTNMODE_CUSTOMBLUE);
								</script>
								<script language="javascript">
									<?php echo 'method_once_only_start_date'; ?>.writeControl();
									<?php echo 'method_once_only_start_date'; ?>.dateFormat="<?php echo DATE_FORMAT_SPIFFYCAL; ?>";
								</script>
								<?php echo  ' ' . TEXT_DATE_FORMAT; ?>
								<br /><?php echo zen_draw_input_field('method_once_only_start_time', $method_once_only_start_time, 'maxlength="5" size="5" id="method_once_only_start_time' . '" onKeyPress="advshipperCheckEnterPressed(event)"') . ' ' . TEXT_TIME_FORMAT;
								if (isset($errors['method_once_only_start_time'])) {
									echo '<p class="FormError">' . $errors['method_once_only_start_time'] . ' </p>';
								}
								?>
							</td>
						</tr>
						<tr <?php echo ($method_availability_scheduling == ADVSHIPPER_AVAILABILITY_SCHEDULING_ONCE_ONLY ? '' : 'style="display: none;"'); ?> id="method_once_only_end_date_header">
							<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="<?php echo 'method_once_only_end_date'; ?>"><?php echo TEXT_LABEL_METHOD_ONCE_ONLY_END_DATE; ?></label></td>
							<td class="AdvancedShipperConfigDesc">
								<?php echo TEXT_CONFIG_DESC_METHOD_ONCE_ONLY_END_DATE; ?>
							</td>
						</tr>
						<tr <?php echo ($method_availability_scheduling == ADVSHIPPER_AVAILABILITY_SCHEDULING_ONCE_ONLY ? '' : 'style="display: none;"'); ?> id="method_once_only_end_date_field">
							<td class="AdvancedShipperConfigField">
								<script language="javascript">
									var <?php echo 'method_once_only_end_date'; ?> = new ctlSpiffyCalendarBox("<?php echo 'method_once_only_end_date'; ?>", "advshipper", "<?php echo 'method_once_only_end_date' . ''; ?>", "<?php echo 'method_once_only_end_date' . '_button'; ?>", "<?php echo $method_once_only_end_date; ?>", scBTNMODE_CUSTOMBLUE);
								</script>
								<script language="javascript">
									<?php echo 'method_once_only_end_date'; ?>.writeControl();
									<?php echo 'method_once_only_end_date'; ?>.dateFormat="<?php echo DATE_FORMAT_SPIFFYCAL; ?>";
								</script>
								<?php echo  ' ' . TEXT_DATE_FORMAT; ?>
								<br /><?php echo zen_draw_input_field('method_once_only_end_time', $method_once_only_end_time, 'maxlength="5" size="5" id="method_once_only_end_time' . '" onKeyPress="advshipperCheckEnterPressed(event)"') . ' ' . TEXT_TIME_FORMAT;
								if (isset($errors['method_once_only_end_time'])) {
									echo '<p class="FormError">' . $errors['method_once_only_end_time'] . ' </p>';
								}
								?>
							</td>
						</tr>
						<tr <?php echo ($method_availability_scheduling == ADVSHIPPER_AVAILABILITY_SCHEDULING_RECURRING ? '' : 'style="display: none;"'); ?> id="method_availability_recurring_mode_header">
							<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="<?php echo "method_availability_recurring_mode"; ?>"><?php echo TEXT_LABEL_METHOD_AVAILABILITY_RECURRING_MODE; ?></label></td>
							<td class="AdvancedShipperConfigDesc">
								<?php echo TEXT_CONFIG_DESC_METHOD_AVAILABILITY_RECURRING_MODE; ?>
							</td>
						</tr>
						<tr <?php echo ($method_availability_scheduling == ADVSHIPPER_AVAILABILITY_SCHEDULING_RECURRING ? '' : 'style="display: none;"'); ?> id="method_availability_recurring_mode_field">
							<td class="AdvancedShipperConfigField">
								<?php echo advshipper_draw_radio_field('method_availability_recurring_mode', ADVSHIPPER_AVAILABILITY_RECURRING_MODE_WEEKLY, $method_availability_recurring_mode == ADVSHIPPER_AVAILABILITY_RECURRING_MODE_WEEKLY, '', 'onKeyPress="advshipperCheckEnterPressed(event)"'); //, null, 'onclick="advshipperMethodAvailabilityRecurringModeSelected(' . ADVSHIPPER_AVAILABILITY_RECURRING_MODE_WEEKLY . ');"'); ?> <?php echo TEXT_METHOD_AVAILABILITY_RECURRING_MODE_WEEKLY; ?>
							</td>
						</tr>
						<tr <?php echo (($method_availability_scheduling == ADVSHIPPER_AVAILABILITY_SCHEDULING_RECURRING && $method_availability_recurring_mode == ADVSHIPPER_AVAILABILITY_RECURRING_MODE_WEEKLY) ? '' : 'style="display: none;"'); ?> id="method_availability_weekly_start_day_and_time_header">
							<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="<?php echo "method_availability_weekly_start_day"; ?>"><?php echo TEXT_LABEL_METHOD_AVAILABILITY_WEEKLY_START_DAY_AND_TIME; ?></label></td>
							<td class="AdvancedShipperConfigDesc">
								<?php echo TEXT_CONFIG_DESC_METHOD_AVAILABILITY_WEEKLY_START_DAY_AND_TIME; ?>
							</td>
						</tr>
						<tr <?php echo (($method_availability_scheduling == ADVSHIPPER_AVAILABILITY_SCHEDULING_RECURRING && $method_availability_recurring_mode == ADVSHIPPER_AVAILABILITY_RECURRING_MODE_WEEKLY) ? '' : 'style="display: none;"'); ?> id="method_availability_weekly_start_day_and_time_field">
							<td class="AdvancedShipperConfigField">
								<?php echo advshipper_cfg_pull_down_day_of_week('method_availability_weekly_start_day', $method_availability_weekly_start_day, 'id="method_availability_weekly_start_day' . '" onchange="javascript:advshipperAvailabilityWeeklyStartDay(' . ')"'); ?>
								<br /><?php echo zen_draw_input_field('method_availability_weekly_start_time', $method_availability_weekly_start_time, 'maxlength="5" size="5" id="method_availability_weekly_start_time' . '" onKeyPress="advshipperCheckEnterPressed(event)"') . ' ' . TEXT_TIME_FORMAT;
								if (isset($errors['method_availability_weekly_start_time'])) {
									echo '<p class="FormError">' . $errors['method_availability_weekly_start_time'] . ' </p>';
								}
								?>
							</td>
						</tr>
						<tr <?php echo (($method_availability_scheduling == ADVSHIPPER_AVAILABILITY_SCHEDULING_RECURRING && $method_availability_recurring_mode == ADVSHIPPER_AVAILABILITY_RECURRING_MODE_WEEKLY) ? '' : 'style="display: none;"'); ?> id="method_availability_weekly_cutoff_day_and_time_header">
							<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="<?php echo "method_availability_weekly_cutoff_day"; ?>"><?php echo TEXT_LABEL_METHOD_AVAILABILITY_WEEKLY_CUTOFF_DAY_AND_TIME; ?></label></td>
							<td class="AdvancedShipperConfigDesc">
								<?php echo TEXT_CONFIG_DESC_METHOD_AVAILABILITY_WEEKLY_CUTOFF_DAY_AND_TIME; ?>
							</td>
						</tr>
						<tr <?php echo (($method_availability_scheduling == ADVSHIPPER_AVAILABILITY_SCHEDULING_RECURRING && $method_availability_recurring_mode == ADVSHIPPER_AVAILABILITY_RECURRING_MODE_WEEKLY) ? '' : 'style="display: none;"'); ?> id="method_availability_weekly_cutoff_day_and_time_field">
							<td class="AdvancedShipperConfigField">
								<?php echo advshipper_cfg_pull_down_day_of_week('method_availability_weekly_cutoff_day', $method_availability_weekly_cutoff_day, 'id="method_availability_weekly_cutoff_day' . '" onchange="javascript:advshipperAvailabilityWeeklyCutoffDay()"'); ?>
								<br /><?php echo zen_draw_input_field('method_availability_weekly_cutoff_time', $method_availability_weekly_cutoff_time, 'maxlength="5" size="5" id="method_availability_weekly_cutoff_time' . '" onKeyPress="advshipperCheckEnterPressed(event)"') . ' ' . TEXT_TIME_FORMAT;
								if (isset($errors['method_availability_weekly_cutoff_day'])) {
									echo '<p class="FormError">' . $errors['method_availability_weekly_cutoff_day'] . ' </p>';
								}
								if (isset($errors['method_availability_weekly_cutoff_time'])) {
									echo '<p class="FormError">' . $errors['method_availability_weekly_cutoff_time'] . ' </p>';
								}
								?>
							</td>
						</tr>
						<tr <?php echo ($method_availability_scheduling == ADVSHIPPER_AVAILABILITY_SCHEDULING_ALWAYS ? 'style="display: none;"' : ''); ?> id="method_usage_limit_header">
							<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="<?php echo "method_usage_limit"; ?>"><?php echo TEXT_LABEL_METHOD_USAGE_LIMIT; ?></label></td>
							<td class="AdvancedShipperConfigDesc">
								<?php echo TEXT_CONFIG_DESC_METHOD_USAGE_LIMIT; ?>
							</td>
						</tr>
						<tr <?php echo ($method_availability_scheduling == ADVSHIPPER_AVAILABILITY_SCHEDULING_ALWAYS ? 'style="display: none;"' : ''); ?> id="method_usage_limit_field">
							<td class="AdvancedShipperConfigField">
								<?php echo zen_draw_input_field('method_usage_limit', $method_usage_limit, 'maxlength="7" size="7" id="method_usage_limit' . '"'); ?>
							</td>
						</tr>
					</table>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<fieldset class="AdvancedShipperMethod<?php echo ((($method_num + 1) % 2 == 0) ? 'Odd' : 'Even') .
					'DeliveryScheduling'; ?>">
					<legend><?php echo TEXT_METHOD_SHIPPING_SCHEDULING; ?></legend>
					<table border="0" width="100%" cellpadding="0" cellspacing="0">
						<tr <?php echo ($method_availability_scheduling == ADVSHIPPER_AVAILABILITY_SCHEDULING_ONCE_ONLY ? '' : 'style="display: none;"'); ?> id="method_once_only_shipping_date_header">
							<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="<?php echo 'method_once_only_shipping_date'; ?>"><?php echo TEXT_LABEL_METHOD_ONCE_ONLY_SHIPPING_DATE; ?></label></td>
							<td class="AdvancedShipperConfigDesc">
								<?php echo TEXT_CONFIG_DESC_METHOD_ONCE_ONLY_SHIPPING_DATE; ?>
							</td>
						</tr>
						<tr <?php echo ($method_availability_scheduling == ADVSHIPPER_AVAILABILITY_SCHEDULING_ONCE_ONLY ? '' : 'style="display: none;"'); ?> id="method_once_only_shipping_date_field">
							<td class="AdvancedShipperConfigField">
								<script language="javascript">
									var <?php echo 'method_once_only_shipping_date'; ?> = new ctlSpiffyCalendarBox("<?php echo 'method_once_only_shipping_date'; ?>", "advshipper", "<?php echo 'method_once_only_shipping_date' . ''; ?>", "<?php echo 'method_once_only_shipping_date' . '_button'; ?>", "<?php echo $method_once_only_shipping_date; ?>", scBTNMODE_CUSTOMBLUE);
								</script>
								<script language="javascript">
									<?php echo 'method_once_only_shipping_date'; ?>.writeControl();
									<?php echo 'method_once_only_shipping_date'; ?>.dateFormat="<?php echo DATE_FORMAT_SPIFFYCAL; ?>";
								</script>
								<?php echo  ' ' . TEXT_DATE_FORMAT; ?>
								<br /><?php echo zen_draw_input_field('method_once_only_shipping_time', $method_once_only_shipping_time, 'maxlength="5" size="5" id="method_once_only_shipping_time' . '" onKeyPress="advshipperCheckEnterPressed(event)"') . ' ' . TEXT_TIME_FORMAT;
								if (isset($errors['method_once_only_shipping_time'])) {
									echo '<p class="FormError">' . $errors['method_once_only_shipping_time'] . ' </p>';
								}
								?>
							</td>
						</tr>
						<tr <?php echo ($method_availability_scheduling == ADVSHIPPER_AVAILABILITY_SCHEDULING_RECURRING ? '' : 'style="display: none;"'); ?> id="method_availability_weekly_shipping_scheduling_header">
							<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="<?php echo 'method_availability_weekly_shipping_scheduling'; ?>"><?php echo TEXT_LABEL_METHOD_AVAILABILITY_WEEKLY_SHIPPING_SCHEDULING; ?></label></td>
							<td class="AdvancedShipperConfigDesc">
								<?php echo TEXT_CONFIG_DESC_METHOD_AVAILABILITY_WEEKLY_SHIPPING_SCHEDULING; ?>
							</td>
						</tr>
						<tr <?php echo ($method_availability_scheduling == ADVSHIPPER_AVAILABILITY_SCHEDULING_RECURRING ? '' : 'style="display: none;"'); ?> id="method_availability_weekly_shipping_scheduling_field">
							<td class="AdvancedShipperConfigField">
								<?php echo advshipper_draw_radio_field('method_availability_weekly_shipping_scheduling', ADVSHIPPER_AVAILABILITY_WEEKLY_SHIPPING_SCHEDULING_NONE, $method_availability_weekly_shipping_scheduling == ADVSHIPPER_AVAILABILITY_WEEKLY_SHIPPING_SCHEDULING_NONE, null, 'onclick="advshipperMethodAvailabilityWeeklyDeliverySchedulingSelected(' . ADVSHIPPER_AVAILABILITY_WEEKLY_SHIPPING_SCHEDULING_NONE . ');" id="method_availability_weekly_shipping_scheduling' . '_' . 'none" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_METHOD_AVAILABILITY_WEEKLY_SHIPPING_SCHEDULING_NONE; ?>
								<br /><div id="method_availability_weekly_shipping_scheduling_regular_weekday_div" <?php echo (is_null($method_availability_weekly_cutoff_day) ? 'style="display: none;"' : ''); ?>><?php echo advshipper_draw_radio_field('method_availability_weekly_shipping_scheduling', ADVSHIPPER_AVAILABILITY_WEEKLY_SHIPPING_SCHEDULING_REGULAR_WEEKDAY, $method_availability_weekly_shipping_scheduling == ADVSHIPPER_AVAILABILITY_WEEKLY_SHIPPING_SCHEDULING_REGULAR_WEEKDAY, null, 'onclick="advshipperMethodAvailabilityWeeklyDeliverySchedulingSelected(' . ADVSHIPPER_AVAILABILITY_WEEKLY_SHIPPING_SCHEDULING_REGULAR_WEEKDAY . ');" id="method_availability_weekly_shipping_scheduling' . '_' . 'regular_weekday" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_METHOD_AVAILABILITY_WEEKLY_SHIPPING_SCHEDULING_REGULAR_WEEKDAY; ?></div>
							</td>
						</tr>
						<tr <?php echo ($method_availability_scheduling == ADVSHIPPER_AVAILABILITY_SCHEDULING_RECURRING &&
								$method_availability_weekly_shipping_scheduling != ADVSHIPPER_AVAILABILITY_WEEKLY_SHIPPING_SCHEDULING_NONE &&
								($method_availability_weekly_start_day == '0' ||
								is_null($method_availability_weekly_start_day)) ? '' : 'style="display: none;"'); ?> id="method_availability_weekly_shipping_show_num_weeks_header">
							<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="<?php echo "method_availability_weekly_shipping_show_num_weeks"; ?>"><?php echo TEXT_LABEL_METHOD_AVAILABILITY_WEEKLY_SHIPPING_SHOW_NUM_WEEKS; ?></label></td>
							<td class="AdvancedShipperConfigDesc">
								<?php echo TEXT_CONFIG_DESC_METHOD_AVAILABILITY_WEEKLY_SHIPPING_SHOW_NUM_WEEKS; ?>
							</td>
						</tr>
						<tr <?php echo ($method_availability_scheduling == ADVSHIPPER_AVAILABILITY_SCHEDULING_RECURRING &&
								$method_availability_weekly_shipping_scheduling != ADVSHIPPER_AVAILABILITY_WEEKLY_SHIPPING_SCHEDULING_NONE &&
								($method_availability_weekly_start_day == '0' ||
								is_null($method_availability_weekly_start_day)) ? '' : 'style="display: none;"'); ?> id="method_availability_weekly_shipping_show_num_weeks_field">
							<td class="AdvancedShipperConfigField">
								<?php echo zen_draw_input_field('method_availability_weekly_shipping_show_num_weeks', $method_availability_weekly_shipping_show_num_weeks, 'maxlength="2" size="2" id="method_availability_weekly_shipping_show_num_weeks' . '" onKeyPress="advshipperCheckEnterPressed(event)"'); 
								if (isset($errors['method_availability_weekly_shipping_show_num_weeks'])) {
									echo '<p class="FormError">' . $errors['method_availability_weekly_shipping_show_num_weeks'] . ' </p>';
								}
								?>
							</td>
						</tr>
						<tr <?php echo (($method_availability_scheduling == ADVSHIPPER_AVAILABILITY_SCHEDULING_RECURRING &&
										$method_availability_weekly_shipping_scheduling != ADVSHIPPER_AVAILABILITY_WEEKLY_SHIPPING_SCHEDULING_NONE &&
										$method_availability_recurring_mode == ADVSHIPPER_AVAILABILITY_RECURRING_MODE_WEEKLY) ? '' : 'style="display: none;"'); ?> id="method_availability_weekly_shipping_regular_weekday_day_and_time_header">
							<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="<?php echo "method_availability_weekly_shipping_regular_weekday_day"; ?>"><?php echo TEXT_LABEL_METHOD_AVAILABILITY_WEEKLY_SHIPPING_REGULAR_WEEKDAY_DAY_AND_TIME; ?></label></td>
							<td class="AdvancedShipperConfigDesc">
								<?php echo TEXT_CONFIG_DESC_METHOD_AVAILABILITY_WEEKLY_SHIPPING_REGULAR_WEEKDAY_DAY_AND_TIME; ?>
							</td>
						</tr>
						<tr <?php echo (($method_availability_scheduling == ADVSHIPPER_AVAILABILITY_SCHEDULING_RECURRING &&
										$method_availability_weekly_shipping_scheduling != ADVSHIPPER_AVAILABILITY_WEEKLY_SHIPPING_SCHEDULING_NONE &&
										$method_availability_recurring_mode == ADVSHIPPER_AVAILABILITY_RECURRING_MODE_WEEKLY) ? '' : 'style="display: none;"'); ?> id="method_availability_weekly_shipping_regular_weekday_day_and_time_field">
							<td class="AdvancedShipperConfigField">
								<?php echo advshipper_cfg_pull_down_day_of_week('method_availability_weekly_shipping_regular_weekday_day', $method_availability_weekly_shipping_regular_weekday_day, 'id="method_availability_weekly_shipping_regular_weekday_day' . '"'); ?>
								<br /><?php echo zen_draw_input_field('method_availability_weekly_shipping_regular_weekday_time', $method_availability_weekly_shipping_regular_weekday_time, 'maxlength="5" size="5" id="method_availability_weekly_shipping_regular_weekday_time' . '" onKeyPress="advshipperCheckEnterPressed(event)"') . ' ' . TEXT_TIME_FORMAT;
								if (isset($errors['method_availability_weekly_shipping_regular_weekday_day'])) {
									echo '<p class="FormError">' . $errors['method_availability_weekly_shipping_regular_weekday_day'] . ' </p>';
								}
								if (isset($errors['method_availability_weekly_shipping_regular_weekday_time'])) {
									echo '<p class="FormError">' . $errors['method_availability_weekly_shipping_regular_weekday_time'] . ' </p>';
								}
								?>
							</td>
						</tr>
					</table>
				</fieldset>
			</td>
		</tr>
	</table>
</fieldset>
					</td>
				</tr>
				<tr>
					<td align="right">
						<?php
						echo zen_image_submit('button_save.gif', IMAGE_SAVE, 'name="save" value="save"');
						echo '&nbsp;<a href="' . zen_href_link(FILENAME_ADVANCED_SHIPPER, zen_get_all_get_params(array('action', 'region_num')), 'NONSSL') .'">' . zen_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>';
						?>
					</td>
				</tr>
				<tr>
					<td style="padding-top: 5px";><?php echo zen_draw_separator('pixel_black.gif', '100%', '2'); ?></td>
				</tr>
			</table>
		</td>
<!-- body_text_eof //-->
	</tr>
</table>
</form>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>