<?php
/**
 * @package    admin
 * @author     Conor Kerr <zen-cart.advshipper@dev.ceon.net>
 * @copyright  Copyright 2007-2009 Ceon
 * @copyright  Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright  Portions Copyright 2003 osCommerce
 * @link       http://dev.ceon.net/web/zen-cart/advshipper
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: advshipper.php 382 2009-06-22 18:49:29Z Bob $
 */

require('includes/application_top.php');

require(DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies();

$languages = zen_get_languages();

$current_time = time();

require_once(DIR_FS_ADMIN . DIR_WS_FUNCTIONS . 'advshipper.php');

if (defined('ADVSHIPPER_ZONES_SUPPORT') && ADVSHIPPER_ZONES_SUPPORT == 'Yes') {
	require_once(DIR_FS_ADMIN . DIR_WS_FUNCTIONS . 'advshipper_zones.php');
}

$advshipper_demo = isset($advshipper_demo) ? $advshipper_demo : false;

// Determine which configuration profile should be loaded
if (isset($_POST['advshipper_configuration_name'])) {
	// Use the selected configuration
} else {
	// Get the name of the configuration profile to use from the database
	$config_name = 'default';
}

// Output warning if database tables don't exist
$table_exists_query = 'SHOW TABLES LIKE "' . TABLE_ADVANCED_SHIPPER_CONFIGS . '";';
$table_exists_result = $db->Execute($table_exists_query);

if ($table_exists_result->EOF) {
	print '<strong><span style="color: red">Warning:</span><br />The Advanced Shipper Database Tables Do Not Exist!</strong><br /><br /><strong><span style="color: red">Please create the database tables, according to the installation instructions!</span></strong><br /><br /><br />';
	exit();
}

// Load the main configuration
$load_main_config_sql = "
	SELECT
		config_id,
		default_method,
		address_not_covered
	FROM
		" . TABLE_ADVANCED_SHIPPER_CONFIGS . "
	WHERE
		config_name = '" . $config_name . "';";

$load_main_config_result = $db->Execute($load_main_config_sql);

if ($load_main_config_result->EOF) {
	// Couldn't load selected config!
	if ($config_name == 'default') {
		// Create default config
		$create_first_config_sql = "
			INSERT INTO
				" . TABLE_ADVANCED_SHIPPER_CONFIGS . "
				(
				config_id,
				config_name
				)
			VALUES
				(
				'1',
				'default'
				);";
		
		$create_first_config_result = $db->Execute($create_first_config_sql);
	}
	$config_id = 1;
} else {
	$config_id = $load_main_config_result->fields['config_id'];
}


// Check if the database needs to be updated ///////////////////////////////////////////////////////

// Find out what the latest version number is
require_once(DIR_FS_CATALOG . DIR_WS_MODULES . 'shipping/advshipper.php');

if (!defined('MODULE_ADVANCED_SHIPPER_MADE_BY_CEON')) {
	print '<strong><span style="color: red">Warning:</span><br />The Advanced Shipper module has not been installed yet!</strong><br /><br /><strong><span style="color: red">Please go to Modules &gt; Shipping and install the module before trying to use the configuration utility!</span></strong><br /><br /><br />';
	exit();
} else if (MODULE_ADVANCED_SHIPPER_MADE_BY_CEON != MODULE_ADVANCED_SHIPPER_VERSION_NO) {
	@include_once('advshipper_auto_upgrade.php');
	
	if (!isset($advshipper_upgraded) || $advshipper_upgraded != true) {
		print '<strong><span style="color: red">Warning:</span><br />The Advanced Shipper ' .
			'Database is out of date - upgrade failed!</strong><br /><br />Was the ' .
			'&ldquo;advshipper_auto_upgrade.php&rdquo; script uploaded?';
		exit();
	} else {
		$messageStack->add(SUCCESS_DATABASE_UPDATED, 'success');
	}
}


$num_shipping_methods_query = "
	SELECT
		COUNT(*) AS num_shipping_methods
	FROM
		" . TABLE_ADVANCED_SHIPPER_METHOD_CONFIGS . "
	WHERE
		config_id = '" . $config_id . "';";

$num_shipping_methods_result = $db->Execute($num_shipping_methods_query);

$num_shipping_methods = $num_shipping_methods_result->fields['num_shipping_methods'];


// Have any buttons been pressed? //////////////////////////////////////////////////////////////////
$affected_method = null;
$action = 'display';
if (isset($_POST) && sizeof($_POST) != 0) {
	foreach ($_POST as $post_var => $post_var_value) {
		if (substr($post_var, 0, 7) == 'insert_' && is_numeric(substr($post_var, 7, 1))) {
			$action = 'insert';
			$affected_method = substr($post_var, 7, strlen($post_var) - 7);
			$affected_method = str_replace('_x', '', $affected_method);
			$affected_method = str_replace('_y', '', $affected_method);
			break;
		} else if (substr($post_var, 0, 5) == 'copy_' && is_numeric(substr($post_var, 5, 1))) {
			$action = 'copy';
			$affected_method = substr($post_var, 5, strlen($post_var) - 5);
			$affected_method = str_replace('_x', '', $affected_method);
			$affected_method = str_replace('_y', '', $affected_method);
			break;
		} else if (substr($post_var, 0, 7) == 'delete_' && is_numeric(substr($post_var, 7, 1))) {
			$action = 'delete';
			$affected_method = substr($post_var, 7, strlen($post_var) - 7);
			$affected_method = str_replace('_x', '', $affected_method);
			$affected_method = str_replace('_y', '', $affected_method);
			break;
		}
	}
	if (isset($_POST['page']) && ($_POST['page'] != $_GET['page'])) {
		$_GET['page'] = $_POST['page'];
	}
}

if ($advshipper_demo) {
	switch ($action) {
		case 'insert':
			$num_shipping_methods_to_insert =
				$_POST['insert_num_shipping_methods_' . $affected_method];
				
			if ($num_shipping_methods_to_insert == 1) {
				$messageStack->add(sprintf(SUCCESS_METHOD_INSERTED_DEMO, $config_name), 'success');
			} else {
				$messageStack->add(sprintf(SUCCESS_METHODS_INSERTED_DEMO,
					$num_shipping_methods_to_insert, $config_name), 'success');
			}
			break;
		case 'copy':
			$num_shipping_methods_to_insert =
				$_POST['copy_num_shipping_methods_' . $affected_method];
			$insert_after = $_POST['copy_to_' . $affected_method];
			
			if ($insert_after == '-1') {
				$insert_after = $affected_method;
			}
			
			if ($num_shipping_methods_to_insert != '-1' &&
					is_numeric($num_shipping_methods_to_insert) && is_numeric($insert_after)) {
				if ($num_shipping_methods_to_insert == 1) {
					$messageStack->add(sprintf(SUCCESS_METHOD_COPIED_ONCE_DEMO,
						$affected_method, $insert_after), 'success');
				} else {
					$messageStack->add(sprintf(SUCCESS_METHOD_COPIED_MULTIPLE_TIMES_DEMO,
						$affected_method, $num_shipping_methods_to_insert, $insert_after),
						'success');
				}
			}
			break;
		case 'delete':
			$messageStack->add(sprintf(SUCCESS_METHOD_DELETED_DEMO, $affected_method), 'success');
			break;
	}
	
	$action = 'display';
}

if ($action == 'insert') {
	// How many methods should be inserted? ////////////////////////////////////////////////////////
	$num_shipping_methods_to_insert = $_POST['insert_num_shipping_methods_' . $affected_method];
	
	if ($num_shipping_methods_to_insert != '-1' && is_numeric($num_shipping_methods_to_insert)) {
		// Re-number all following methods to allow space to insert the new method(s)
		for ($method_i = $num_shipping_methods; $method_i >= $affected_method; $method_i--) {
			$update_method_sql = "
				UPDATE
					" . TABLE_ADVANCED_SHIPPER_METHOD_CONFIGS . "
				SET
					method = '" . ($method_i + $num_shipping_methods_to_insert) . "'
				WHERE
					config_id = '" . $config_id . "'
				AND
					method = '" . $method_i . "';";
			
			$update_method_result = $db->Execute($update_method_sql);
			
			$update_method_sql = "
				UPDATE
					" . TABLE_ADVANCED_SHIPPER_METHOD_TITLES . "
				SET
					method = '" . ($method_i + $num_shipping_methods_to_insert) . "'
				WHERE
					config_id = '" . $config_id . "'
				AND
					method = '" . $method_i . "';";
			
			$update_method_result = $db->Execute($update_method_sql);
			
			$update_method_sql = "
				UPDATE
					" . TABLE_ADVANCED_SHIPPER_METHOD_ADMIN_TITLES . "
				SET
					method = '" . ($method_i + $num_shipping_methods_to_insert) . "'
				WHERE
					config_id = '" . $config_id . "'
				AND
					method = '" . $method_i . "';";
			
			$update_method_result = $db->Execute($update_method_sql);
			
			$update_method_sql = "
				UPDATE
					" . TABLE_ADVANCED_SHIPPER_METHOD_CATEGORIES . "
				SET
					method = '" . ($method_i + $num_shipping_methods_to_insert) . "'
				WHERE
					config_id = '" . $config_id . "'
				AND
					method = '" . $method_i . "';";
			
			$update_method_result = $db->Execute($update_method_sql);
			
			$update_method_sql = "
				UPDATE
					" . TABLE_ADVANCED_SHIPPER_METHOD_MANUFACTURERS . "
				SET
					method = '" . ($method_i + $num_shipping_methods_to_insert) . "'
				WHERE
					config_id = '" . $config_id . "'
				AND
					method = '" . $method_i . "';";
			
			$update_method_result = $db->Execute($update_method_sql);
			
			$update_method_sql = "
				UPDATE
					" . TABLE_ADVANCED_SHIPPER_METHOD_PRODUCTS . "
				SET
					method = '" . ($method_i + $num_shipping_methods_to_insert) . "'
				WHERE
					config_id = '" . $config_id . "'
				AND
					method = '" . $method_i . "';";
			
			$update_method_result = $db->Execute($update_method_sql);
			
			$update_method_sql = "
				UPDATE
					" . TABLE_ADVANCED_SHIPPER_REGION_CONFIGS . "
				SET
					method = '" . ($method_i + $num_shipping_methods_to_insert) . "'
				WHERE
					config_id = '" . $config_id . "'
				AND
					method = '" . $method_i . "';";
			
			$update_method_result = $db->Execute($update_method_sql);
			
			$update_method_sql = "
				UPDATE
					" . TABLE_ADVANCED_SHIPPER_REGION_ADMIN_TITLES . "
				SET
					method = '" . ($method_i + $num_shipping_methods_to_insert) . "'
				WHERE
					config_id = '" . $config_id . "'
				AND
					method = '" . $method_i . "';";
			
			$update_method_result = $db->Execute($update_method_sql);
			
			$update_method_sql = "
				UPDATE
					" . TABLE_ADVANCED_SHIPPER_REGION_TITLES . "
				SET
					method = '" . ($method_i + $num_shipping_methods_to_insert) . "'
				WHERE
					config_id = '" . $config_id . "'
				AND
					method = '" . $method_i . "';";
			
			$update_method_result = $db->Execute($update_method_sql);
			
			$update_method_sql = "
				UPDATE
					" . TABLE_ADVANCED_SHIPPER_REGION_SURCHARGE_TITLES . "
				SET
					method = '" . ($method_i + $num_shipping_methods_to_insert) . "'
				WHERE
					config_id = '" . $config_id . "'
				AND
					method = '" . $method_i . "';";
			
			$update_method_result = $db->Execute($update_method_sql);
			
			$update_method_sql = "
				UPDATE
					" . TABLE_ADVANCED_SHIPPER_REGION_UPS_CONFIGS . "
				SET
					method = '" . ($method_i + $num_shipping_methods_to_insert) . "'
				WHERE
					config_id = '" . $config_id . "'
				AND
					method = '" . $method_i . "';";
			
			$update_method_result = $db->Execute($update_method_sql);
			
			$update_method_sql = "
				UPDATE
					" . TABLE_ADVANCED_SHIPPER_REGION_USPS_CONFIGS . "
				SET
					method = '" . ($method_i + $num_shipping_methods_to_insert) . "'
				WHERE
					config_id = '" . $config_id . "'
				AND
					method = '" . $method_i . "';";
			
			$update_method_result = $db->Execute($update_method_sql);
		}
		
		// Now add the new methods
		for ($method_i = $affected_method; $method_i <
				($affected_method + $num_shipping_methods_to_insert); $method_i++) {
			$insert_method_sql = "
				INSERT INTO
					" . TABLE_ADVANCED_SHIPPER_METHOD_CONFIGS . "
					(
					config_id,
					method
					)
				VALUES
					(
					'" . $config_id . "',
					'" . $method_i . "'
					);";
			
			$insert_method_result = $db->Execute($insert_method_sql);
			
			for ($language_i = 0, $n = sizeof($languages); $language_i < $n; $language_i++) {
				$insert_method_title_sql = "
					INSERT INTO
						" . TABLE_ADVANCED_SHIPPER_METHOD_TITLES . "
						(
						config_id,
						method,
						language_id
						)
					VALUES
						(
						'" . $config_id . "',
						'" . $method_i . "',
						'" . $languages[$language_i]['id'] . "'
						);";
				
				$insert_method_title_result = $db->Execute($insert_method_title_sql);
			}
			
			for ($language_i = 0, $n = sizeof($languages); $language_i < $n; $language_i++) {
				$insert_method_admin_title_sql = "
					INSERT INTO
						" . TABLE_ADVANCED_SHIPPER_METHOD_ADMIN_TITLES . "
						(
						config_id,
						method,
						language_id
						)
					VALUES
						(
						'" . $config_id . "',
						'" . $method_i . "',
						'" . $languages[$language_i]['id'] . "'
						);";
				
				$insert_method_admin_title_result = $db->Execute($insert_method_admin_title_sql);
			}
		}
		
		$num_shipping_methods += $num_shipping_methods_to_insert;
		
		if ($num_shipping_methods_to_insert == 1) {
			$messageStack->add(sprintf(SUCCESS_METHOD_INSERTED, $config_name), 'success');
		} else {
			$messageStack->add(sprintf(SUCCESS_METHODS_INSERTED, $num_shipping_methods_to_insert,
				$config_name), 'success');
		}
	}
} else if ($action == 'copy') {
	// How many copies of the region should be inserted? ///////////////////////////////////////////
	$num_shipping_methods_to_insert = $_POST['copy_num_shipping_methods_' . $affected_method];
	$insert_after = $_POST['copy_to_' . $affected_method];
	
	if ($insert_after == '-1') {
		$insert_after = $affected_method;
	}
	
	if ($num_shipping_methods_to_insert != '-1' && is_numeric($num_shipping_methods_to_insert) &&
			is_numeric($insert_after)) {
		// Load the data for the selected method first so it can be copied
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
				method = '" . $affected_method . "';";
		
		$load_method_config_result = $db->Execute($load_method_config_sql);
		
		$method_titles = array();
		$load_method_titles_sql = "
			SELECT
				asmt.title,
				asmt.language_id
			FROM
				" . TABLE_ADVANCED_SHIPPER_METHOD_TITLES . " asmt
			WHERE
				asmt.config_id = '" . $config_id . "'
			AND
				asmt.method = '" . $affected_method . "';";
		
		$load_method_titles_result = $db->Execute($load_method_titles_sql);
		
		if ($load_method_titles_result->EOF) {
			
		} else {
			while (!$load_method_titles_result->EOF) {
				$method_titles[$load_method_titles_result->fields['language_id']] =
					$load_method_titles_result->fields['title'];
				
				$load_method_titles_result->MoveNext();
			}
		}
		
		$method_admin_titles = array();
		$load_method_admin_titles_sql = "
			SELECT
				asmat.title,
				asmat.language_id
			FROM
				" . TABLE_ADVANCED_SHIPPER_METHOD_ADMIN_TITLES . " asmat
			WHERE
				asmat.config_id = '" . $config_id . "'
			AND
				asmat.method = '" . $affected_method . "';";
		
		$load_method_admin_titles_result = $db->Execute($load_method_admin_titles_sql);
		
		if ($load_method_admin_titles_result->EOF) {
			
		} else {
			while (!$load_method_admin_titles_result->EOF) {
				$method_admin_titles[$load_method_admin_titles_result->fields['language_id']] =
					$load_method_admin_titles_result->fields['title'];
				
				$load_method_admin_titles_result->MoveNext();
			}
		}
		
		// Load any category selections for this method
		$categories = array();
		$load_categories_config_sql = "
			SELECT
				asmc.category_order,
				asmc.category_id
			FROM
				" . TABLE_ADVANCED_SHIPPER_METHOD_CATEGORIES . " asmc
			WHERE
				asmc.config_id = '" . $config_id . "'
			AND
				asmc.method = '" . $affected_method . "'
			ORDER BY
				asmc.category_order;";
		
		$load_categories_config_result = $db->Execute($load_categories_config_sql);
		
		if ($load_categories_config_result->EOF) {
			
		} else {
			while (!$load_categories_config_result->EOF) {
				$num_categories = sizeof($categories);
				foreach ($load_categories_config_result->fields as $key => $value) {
					$categories[$num_categories][$key] = $value;
				}
				
				$load_categories_config_result->MoveNext();
			}
		}
		
		// Load any manufacturer selections for this method
		$manufacturers = array();
		$load_manufacturers_config_sql = "
			SELECT
				asmm.manufacturer_order,
				asmm.manufacturer_id
			FROM
				" . TABLE_ADVANCED_SHIPPER_METHOD_MANUFACTURERS . " asmm
			WHERE
				asmm.config_id = '" . $config_id . "'
			AND
				asmm.method = '" . $affected_method . "'
			ORDER BY
				asmm.manufacturer_order;";
		
		$load_manufacturers_config_result = $db->Execute($load_manufacturers_config_sql);
		
		if ($load_manufacturers_config_result->EOF) {
			
		} else {
			while (!$load_manufacturers_config_result->EOF) {
				$num_manufacturers = sizeof($manufacturers);
				foreach ($load_manufacturers_config_result->fields as $key => $value) {
					$manufacturers[$num_manufacturers][$key] = $value;
				}
				
				$load_manufacturers_config_result->MoveNext();
			}
		}
		
		// Load any product selections for this method
		$products = array();
		$load_products_config_sql = "
			SELECT
				asmp.product_order,
				asmp.product_id,
				asmp.product_attributes_id
			FROM
				" . TABLE_ADVANCED_SHIPPER_METHOD_PRODUCTS . " asmp
			WHERE
				asmp.config_id = '" . $config_id . "'
			AND
				asmp.method = '" . $affected_method . "'
			ORDER BY
				asmp.product_order;";
		
		$load_products_config_result = $db->Execute($load_products_config_sql);
		
		if ($load_products_config_result->EOF) {
			
		} else {
			while (!$load_products_config_result->EOF) {
				$num_products = sizeof($products);
				foreach ($load_products_config_result->fields as $key => $value) {
					$products[$num_products][$key] = $value;
				}
				
				$load_products_config_result->MoveNext();
			}
		}
		
		// Load the regions configurations for this method
		$regions = array();
		$load_regions_config_sql = "
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
				asrc.method = '" . $affected_method . "'
			ORDER BY
				asrc.region;";
		
		$load_regions_config_result = $db->Execute($load_regions_config_sql);
		
		if ($load_regions_config_result->EOF) {
			
		} else {
			while (!$load_regions_config_result->EOF) {
				foreach ($load_regions_config_result->fields as $key => $value) {
					$regions[$load_regions_config_result->fields['region']][$key] = $value;
				}
				
				$load_regions_config_result->MoveNext();
			}
		}
		
		$region_admin_titles = array();
		$load_region_admin_titles_sql = "
			SELECT
				asrat.region,
				asrat.language_id,
				asrat.title
			FROM
				" . TABLE_ADVANCED_SHIPPER_REGION_ADMIN_TITLES . " asrat
			WHERE
				asrat.config_id = '" . $config_id . "'
			AND
				asrat.method = '" . $affected_method . "';";
		
		$load_region_admin_titles_result = $db->Execute($load_region_admin_titles_sql);
		
		if ($load_region_admin_titles_result->EOF) {
			
		} else {
			while (!$load_region_admin_titles_result->EOF) {
				$region_admin_titles[$load_region_admin_titles_result->fields['region']][$load_region_admin_titles_result->fields['language_id']] =
					$load_region_admin_titles_result->fields['title'];
				
				$load_region_admin_titles_result->MoveNext();
			}
		}
		
		$region_titles = array();
		$load_region_titles_sql = "
			SELECT
				asrt.region,
				asrt.language_id,
				asrt.title
			FROM
				" . TABLE_ADVANCED_SHIPPER_REGION_TITLES . " asrt
			WHERE
				asrt.config_id = '" . $config_id . "'
			AND
				asrt.method = '" . $affected_method . "';";
		
		$load_region_titles_result = $db->Execute($load_region_titles_sql);
		
		if ($load_region_titles_result->EOF) {
			
		} else {
			while (!$load_region_titles_result->EOF) {
				$region_titles[$load_region_titles_result->fields['region']][$load_region_titles_result->fields['language_id']] =
					$load_region_titles_result->fields['title'];
				
				$load_region_titles_result->MoveNext();
			}
		}
		
		$region_surcharge_titles = array();
		$load_region_surcharge_titles_sql = "
			SELECT
				asrst.region,
				asrst.language_id,
				asrst.title
			FROM
				" . TABLE_ADVANCED_SHIPPER_REGION_SURCHARGE_TITLES . " asrst
			WHERE
				asrst.config_id = '" . $config_id . "'
			AND
				asrst.method = '" . $affected_method . "';";
		
		$load_region_surcharge_titles_result = $db->Execute($load_region_surcharge_titles_sql);
		
		if ($load_region_surcharge_titles_result->EOF) {
			
		} else {
			while (!$load_region_surcharge_titles_result->EOF) {
				$region_surcharge_titles[$load_region_surcharge_titles_result->fields['region']][$load_region_surcharge_titles_result->fields['language_id']] =
					$load_region_surcharge_titles_result->fields['title'];
				
				$load_region_surcharge_titles_result->MoveNext();
			}
		}
		
		$region_ups_configs = array();
		$load_region_ups_configs_sql = "
			SELECT
				asruc.*
			FROM
				" . TABLE_ADVANCED_SHIPPER_REGION_UPS_CONFIGS . " asruc
			WHERE
				asruc.config_id = '" . $config_id . "'
			AND
				asruc.method = '" . $affected_method . "'
			ORDER BY
				asruc.region;";
		
		$load_region_ups_configs_result = $db->Execute($load_region_ups_configs_sql);
		
		if ($load_region_ups_configs_result->EOF) {
			
		} else {
			while (!$load_region_ups_configs_result->EOF) {
				foreach ($load_region_ups_configs_result->fields as $key => $value) {
					$region_ups_configs[$load_region_ups_configs_result->fields['region']][$key] =
						$value;
				}
				
				$load_region_ups_configs_result->MoveNext();
			}
		}
		
		$region_usps_configs = array();
		$load_region_usps_configs_sql = "
			SELECT
				asruc.*
			FROM
				" . TABLE_ADVANCED_SHIPPER_REGION_USPS_CONFIGS . " asruc
			WHERE
				asruc.config_id = '" . $config_id . "'
			AND
				asruc.method = '" . $affected_method . "'
			ORDER BY
				asruc.region;";
		
		$load_region_usps_configs_result = $db->Execute($load_region_usps_configs_sql);
		
		if ($load_region_usps_configs_result->EOF) {
			
		} else {
			while (!$load_region_usps_configs_result->EOF) {
				foreach ($load_region_usps_configs_result->fields as $key => $value) {
					$region_usps_configs[$load_region_usps_configs_result->fields['region']][$key] =
						$value;
				}
				
				$load_region_usps_configs_result->MoveNext();
			}
		}
		
		// Re-number all regions after the insertion method to allow space to insert the new
		// method(s)
		for ($method_i = $num_shipping_methods; $method_i > $insert_after; $method_i--) {
			$update_method_sql = "
				UPDATE
					" . TABLE_ADVANCED_SHIPPER_METHOD_CONFIGS . "
				SET
					method = '" . ($method_i + $num_shipping_methods_to_insert) . "'
				WHERE
					config_id = '" . $config_id . "'
				AND
					method = '" . $method_i . "';";
			
			$update_method_result = $db->Execute($update_method_sql);
			
			$update_method_sql = "
				UPDATE
					" . TABLE_ADVANCED_SHIPPER_METHOD_TITLES . "
				SET
					method = '" . ($method_i + $num_shipping_methods_to_insert) . "'
				WHERE
					config_id = '" . $config_id . "'
				AND
					method = '" . $method_i . "';";
			
			$update_method_result = $db->Execute($update_method_sql);
			
			$update_method_sql = "
				UPDATE
					" . TABLE_ADVANCED_SHIPPER_METHOD_ADMIN_TITLES . "
				SET
					method = '" . ($method_i + $num_shipping_methods_to_insert) . "'
				WHERE
					config_id = '" . $config_id . "'
				AND
					method = '" . $method_i . "';";
			
			$update_method_result = $db->Execute($update_method_sql);
			
			$update_method_sql = "
				UPDATE
					" . TABLE_ADVANCED_SHIPPER_METHOD_CATEGORIES . "
				SET
					method = '" . ($method_i + $num_shipping_methods_to_insert) . "'
				WHERE
					config_id = '" . $config_id . "'
				AND
					method = '" . $method_i . "';";
			
			$update_method_result = $db->Execute($update_method_sql);
			
			$update_method_sql = "
				UPDATE
					" . TABLE_ADVANCED_SHIPPER_METHOD_MANUFACTURERS . "
				SET
					method = '" . ($method_i + $num_shipping_methods_to_insert) . "'
				WHERE
					config_id = '" . $config_id . "'
				AND
					method = '" . $method_i . "';";
			
			$update_method_result = $db->Execute($update_method_sql);
			
			$update_method_sql = "
				UPDATE
					" . TABLE_ADVANCED_SHIPPER_METHOD_PRODUCTS . "
				SET
					method = '" . ($method_i + $num_shipping_methods_to_insert) . "'
				WHERE
					config_id = '" . $config_id . "'
				AND
					method = '" . $method_i . "';";
			
			$update_method_result = $db->Execute($update_method_sql);
			
			$update_method_sql = "
				UPDATE
					" . TABLE_ADVANCED_SHIPPER_REGION_CONFIGS . "
				SET
					method = '" . ($method_i + $num_shipping_methods_to_insert) . "'
				WHERE
					config_id = '" . $config_id . "'
				AND
					method = '" . $method_i . "';";
			
			$update_method_result = $db->Execute($update_method_sql);
			
			$update_method_sql = "
				UPDATE
					" . TABLE_ADVANCED_SHIPPER_REGION_ADMIN_TITLES . "
				SET
					method = '" . ($method_i + $num_shipping_methods_to_insert) . "'
				WHERE
					config_id = '" . $config_id . "'
				AND
					method = '" . $method_i . "';";
			
			$update_method_result = $db->Execute($update_method_sql);
			
			$update_method_sql = "
				UPDATE
					" . TABLE_ADVANCED_SHIPPER_REGION_TITLES . "
				SET
					method = '" . ($method_i + $num_shipping_methods_to_insert) . "'
				WHERE
					config_id = '" . $config_id . "'
				AND
					method = '" . $method_i . "';";
			
			$update_method_result = $db->Execute($update_method_sql);
			
			$update_method_sql = "
				UPDATE
					" . TABLE_ADVANCED_SHIPPER_REGION_SURCHARGE_TITLES . "
				SET
					method = '" . ($method_i + $num_shipping_methods_to_insert) . "'
				WHERE
					config_id = '" . $config_id . "'
				AND
					method = '" . $method_i . "';";
			
			$update_method_result = $db->Execute($update_method_sql);
			
			$update_method_sql = "
				UPDATE
					" . TABLE_ADVANCED_SHIPPER_REGION_UPS_CONFIGS . "
				SET
					method = '" . ($method_i + $num_shipping_methods_to_insert) . "'
				WHERE
					config_id = '" . $config_id . "'
				AND
					method = '" . $method_i . "';";
			
			$update_method_result = $db->Execute($update_method_sql);
			
			$update_method_sql = "
				UPDATE
					" . TABLE_ADVANCED_SHIPPER_REGION_USPS_CONFIGS . "
				SET
					method = '" . ($method_i + $num_shipping_methods_to_insert) . "'
				WHERE
					config_id = '" . $config_id . "'
				AND
					method = '" . $method_i . "';";
			
			$update_method_result = $db->Execute($update_method_sql);
		}
		
		// Now add the new method, copying the data loaded from the selected method ////////////////
		for ($method_i = $insert_after + 1; $method_i <
				($insert_after + 1 + $num_shipping_methods_to_insert); $method_i++) {
			$insert_method_sql = "
				INSERT INTO
					" . TABLE_ADVANCED_SHIPPER_METHOD_CONFIGS . "
					(
					config_id,
					method,
					select_products,
					availability_scheduling,
					once_only_start_datetime,
					once_only_end_datetime,
					availability_recurring_mode,
					availability_weekly_start_day,
					availability_weekly_start_time,
					availability_weekly_cutoff_day,
					availability_weekly_cutoff_time,
					usage_limit,
					once_only_shipping_datetime,
					availability_weekly_shipping_scheduling,
					availability_weekly_shipping_show_num_weeks,
					availability_weekly_shipping_regular_weekday_day,
					availability_weekly_shipping_regular_weekday_time
					)
				VALUES
					(
					'" . $config_id . "',
					'" . $method_i . "',
					'" . $db->prepare_input($load_method_config_result->fields['select_products']) . "',
					'" . $db->prepare_input($load_method_config_result->fields['availability_scheduling']) . "',
					" . (is_null($load_method_config_result->fields['once_only_start_datetime']) ? 'null' : "'" . $db->prepare_input($load_method_config_result->fields['once_only_start_datetime']) . "'") . ",
					" . (is_null($load_method_config_result->fields['once_only_end_datetime']) ? 'null' : "'" . $db->prepare_input($load_method_config_result->fields['once_only_end_datetime']) . "'") . ",
					'" . $db->prepare_input($load_method_config_result->fields['availability_recurring_mode']) . "',
					" . (is_null($load_method_config_result->fields['availability_weekly_start_day']) ? 'null' : "'" . $db->prepare_input($load_method_config_result->fields['availability_weekly_start_day']) . "'") . ",
					" . (is_null($load_method_config_result->fields['availability_weekly_start_time']) ? 'null' : "'" . $db->prepare_input($load_method_config_result->fields['availability_weekly_start_time']) . "'") . ",
					" . (is_null($load_method_config_result->fields['availability_weekly_cutoff_day']) ? 'null' : "'" . $db->prepare_input($load_method_config_result->fields['availability_weekly_cutoff_day']) . "'") . ",
					" . (is_null($load_method_config_result->fields['availability_weekly_cutoff_time']) ? 'null' : "'" . $db->prepare_input($load_method_config_result->fields['availability_weekly_cutoff_time']) . "'") . ",
					" . (is_null($load_method_config_result->fields['usage_limit']) ? 'null' :  "'" . $db->prepare_input($load_method_config_result->fields['availability_weekly_start_day']) . "'") . ",
					" . (is_null($load_method_config_result->fields['once_only_shipping_datetime']) ? 'null' : "'" . $db->prepare_input($load_method_config_result->fields['once_only_shipping_datetime']) . "'") . ",
					'" . $db->prepare_input($load_method_config_result->fields['availability_weekly_shipping_scheduling']) . "',
					" . (is_null($load_method_config_result->fields['availability_weekly_shipping_show_num_weeks']) ? 'null' : "'" . $db->prepare_input($load_method_config_result->fields['availability_weekly_shipping_show_num_weeks']) . "'") . ",
					" . (is_null($load_method_config_result->fields['availability_weekly_shipping_regular_weekday_day']) ? 'null' : "'" . $db->prepare_input($load_method_config_result->fields['availability_weekly_shipping_regular_weekday_day']) . "'") . ",
					" . (is_null($load_method_config_result->fields['availability_weekly_shipping_regular_weekday_time']) ? 'null' : "'" . $db->prepare_input($load_method_config_result->fields['availability_weekly_shipping_regular_weekday_time']) . "'") . "
					);";
			
			$insert_method_result = $db->Execute($insert_method_sql);
			
			for ($language_i = 0, $n = sizeof($languages); $language_i < $n; $language_i++) {
				$insert_method_title_sql = "
					INSERT INTO
						" . TABLE_ADVANCED_SHIPPER_METHOD_TITLES . "
						(
						config_id,
						method,
						language_id,
						title
						)
					VALUES
						(
						'" . $config_id . "',
						'" . $method_i . "',
						'" . $languages[$language_i]['id'] . "',
						'" . $db->prepare_input($method_titles[$languages[$language_i]['id']]) . "'
						);";
				
				$insert_method_title_result = $db->Execute($insert_method_title_sql);
			}
			
			for ($language_i = 0, $n = sizeof($languages); $language_i < $n; $language_i++) {
				$insert_method_admin_title_sql = "
					INSERT INTO
						" . TABLE_ADVANCED_SHIPPER_METHOD_ADMIN_TITLES . "
						(
						config_id,
						method,
						language_id,
						title
						)
					VALUES
						(
						'" . $config_id . "',
						'" . $method_i . "',
						'" . $languages[$language_i]['id'] . "',
						'" . $db->prepare_input($method_admin_titles[$languages[$language_i]['id']]) . "'
						);";
				
				$insert_method_admin_title_result = $db->Execute($insert_method_admin_title_sql);
			}
			
			// Copy any category selections for this method
			$num_categories = sizeof($categories);
			
			for ($category_i = 0; $category_i < $num_categories; $category_i++) {
				$insert_category_sql = "
					INSERT INTO
						" . TABLE_ADVANCED_SHIPPER_METHOD_CATEGORIES . "
						(
						config_id,
						method,
						category_order,
						category_id
						)
					VALUES
						(
						'" . $config_id . "',
						'" . $method_i . "',
						'" . $db->prepare_input($categories[$category_i]['category_order']) . "',
						'" . $db->prepare_input($categories[$category_i]['category_id']) . "'
						);";
				
				$insert_category_result = $db->Execute($insert_category_sql);
			}
			
			// Copy any manufacturer selections for this method
			$num_manufacturers = sizeof($manufacturers);
			
			for ($manufacturer_i = 0; $manufacturer_i < $num_manufacturers; $manufacturer_i++) {
				$insert_manufacturer_sql = "
					INSERT INTO
						" . TABLE_ADVANCED_SHIPPER_METHOD_MANUFACTURERS . "
						(
						config_id,
						method,
						manufacturer_order,
						manufacturer_id
						)
					VALUES
						(
						'" . $config_id . "',
						'" . $method_i . "',
						'" . $db->prepare_input($manufacturers[$manufacturer_i]['manufacturer_order']) . "',
						'" . $db->prepare_input($manufacturers[$manufacturer_i]['manufacturer_id']) . "'
						);";
				
				$insert_manufacturer_result = $db->Execute($insert_manufacturer_sql);
			}
			
			// Copy any product selections for this method
			$num_products = sizeof($products);
			
			for ($product_i = 0; $product_i < $num_products; $product_i++) {
				$insert_product_sql = "
					INSERT INTO
						" . TABLE_ADVANCED_SHIPPER_METHOD_PRODUCTS . "
						(
						config_id,
						method,
						product_order,
						product_id,
						product_attributes_id
						)
					VALUES
						(
						'" . $config_id . "',
						'" . $method_i . "',
						'" . $db->prepare_input($products[$product_i]['product_order']) . "',
						'" . $db->prepare_input($products[$product_i]['product_id']) . "',
						" . (is_null($products[$product_i]['product_attributes_id']) ? "'0'" : "'" . $db->prepare_input($products[$product_i]['product_attributes_id']) . "'") . "
						);";
				
				$insert_product_result = $db->Execute($insert_product_sql);
			}
			
			// Copy any region configurations for this method
			$num_regions = sizeof($regions);
			
			for ($region_i = 1; $region_i <= $num_regions; $region_i++) {
				$insert_region_sql = "
					INSERT INTO
						" . TABLE_ADVANCED_SHIPPER_REGION_CONFIGS . "
						(
						config_id,
						method,
						region,
						definition_method,
						countries_postcodes,
						countries_zones,
						countries_cities,
						countries_states,
						distance,
						tax_class,
						rates_include_tax,
						rate_limits_inc,
						total_up_price_inc_tax,
						table_of_rates,
						max_weight_per_package,
						packaging_weights,
						surcharge
						)
					VALUES
						(
						'" . $config_id . "',
						'" . $method_i . "',
						'" . $region_i . "',
						'" . $db->prepare_input($regions[$region_i]['definition_method']) . "',
						" . ((is_null($regions[$region_i]['countries_postcodes']) || strlen(trim($regions[$region_i]['countries_postcodes'])) == 0) ? 'null' : "'" . zen_db_prepare_input($regions[$region_i]['countries_postcodes']) . "'") . ",
						" . ((is_null($regions[$region_i]['countries_zones']) || strlen(trim($regions[$region_i]['countries_zones'])) == 0) ? 'null' : "'" . zen_db_prepare_input($regions[$region_i]['countries_zones']) . "'") . ",
						" . ((is_null($regions[$region_i]['countries_cities']) || strlen(trim($regions[$region_i]['countries_cities'])) == 0) ? 'null' : "'" . zen_db_prepare_input($regions[$region_i]['countries_cities']) . "'") . ",
						" . ((is_null($regions[$region_i]['countries_states']) || strlen(trim($regions[$region_i]['countries_states'])) == 0) ? 'null' : "'" . zen_db_prepare_input($regions[$region_i]['countries_states']) . "'") . ",
						" . ((is_null($regions[$region_i]['distance']) || strlen(trim($regions[$region_i]['distance'])) == 0) ? 'null' : "'" . zen_db_prepare_input($regions[$region_i]['distance']) . "'") . ",
						'" . $db->prepare_input($regions[$region_i]['tax_class']) . "',
						'" . $db->prepare_input($regions[$region_i]['rates_include_tax']) . "',
						'" . $db->prepare_input($regions[$region_i]['rate_limits_inc']) . "',
						'" . $db->prepare_input($regions[$region_i]['total_up_price_inc_tax']) . "',
						'" . $db->prepare_input($regions[$region_i]['table_of_rates']) . "',
						" . ((is_null($regions[$region_i]['max_weight_per_package']) || strlen(trim($regions[$region_i]['max_weight_per_package'])) == 0) ? 'null' : "'" . zen_db_prepare_input($regions[$region_i]['max_weight_per_package']) . "'") . ",
						" . ((is_null($regions[$region_i]['packaging_weights']) || strlen(trim($regions[$region_i]['packaging_weights'])) == 0) ? 'null' : "'" .  zen_db_prepare_input($regions[$region_i]['packaging_weights']) . "'") . ",
						" . ((is_null($regions[$region_i]['surcharge']) || strlen(trim($regions[$region_i]['surcharge'])) == 0) ? 'null' : "'" . zen_db_prepare_input($regions[$region_i]['surcharge']) . "'") . "
						);";
				
				$insert_region_result = $db->Execute($insert_region_sql);
				
				for ($language_i = 0, $n = sizeof($languages); $language_i < $n; $language_i++) {
					$insert_region_admin_title_sql = "
						INSERT INTO
							" . TABLE_ADVANCED_SHIPPER_REGION_ADMIN_TITLES . "
							(
							config_id,
							method,
							region,
							language_id,
							title
							)
						VALUES
							(
							'" . $config_id . "',
							'" . $method_i . "',
							'" . $region_i . "',
							'" . $languages[$language_i]['id'] . "',
							'" . $db->prepare_input($region_admin_titles[$region_i][$languages[$language_i]['id']]) . "'
							);";
					
					$insert_region_admin_title_result = $db->Execute($insert_region_admin_title_sql);
					
					$insert_region_title_sql = "
						INSERT INTO
							" . TABLE_ADVANCED_SHIPPER_REGION_TITLES . "
							(
							config_id,
							method,
							region,
							language_id,
							title
							)
						VALUES
							(
							'" . $config_id . "',
							'" . $method_i . "',
							'" . $region_i . "',
							'" . $languages[$language_i]['id'] . "',
							'" . $db->prepare_input($region_titles[$region_i][$languages[$language_i]['id']]) . "'
							);";
					
					$insert_region_title_result = $db->Execute($insert_region_title_sql);
					
					$insert_region_surcharge_title_sql = "
						INSERT INTO
							" . TABLE_ADVANCED_SHIPPER_REGION_SURCHARGE_TITLES . "
							(
							config_id,
							method,
							region,
							language_id,
							title
							)
						VALUES
							(
							'" . $config_id . "',
							'" . $method_i . "',
							'" . $region_i . "',
							'" . $languages[$language_i]['id'] . "',
							" . ((is_null($region_surcharge_titles[$region_i][$languages[$language_i]['id']]) || strlen(trim($region_surcharge_titles[$region_i][$languages[$language_i]['id']])) == 0) ? 'null' : "'" . $region_surcharge_titles[$region_i][$languages[$language_i]['id']] . "'") . "
							);";
					
					$insert_region_surcharge_title_result = $db->Execute($insert_region_surcharge_title_sql);
				}
				
				if (isset($region_ups_configs[$region_i])) {
					$region_ups_config_data_array = array(
						'config_id' => $config_id,
						'method' => $method_i,
						'region' => $region_i,
						'source_country' => zen_db_prepare_input($region_ups_configs[$region_i]['source_country']),
						'source_postcode' => zen_db_prepare_input($region_ups_configs[$region_i]['source_postcode']),
						'pickup_method' => zen_db_prepare_input($region_ups_configs[$region_i]['pickup_method']),
						'packaging' => zen_db_prepare_input($region_ups_configs[$region_i]['packaging']),
						'delivery_type' => zen_db_prepare_input($region_ups_configs[$region_i]['delivery_type']),
						'shipping_service_1dm' => zen_db_prepare_input($region_ups_configs[$region_i]['shipping_service_1dm']),
						'shipping_service_1dml' => zen_db_prepare_input($region_ups_configs[$region_i]['shipping_service_1dml']),
						'shipping_service_1da' => zen_db_prepare_input($region_ups_configs[$region_i]['shipping_service_1da']),
						'shipping_service_1dal' => zen_db_prepare_input($region_ups_configs[$region_i]['shipping_service_1dal']),
						'shipping_service_1dapi' => zen_db_prepare_input($region_ups_configs[$region_i]['shipping_service_1dapi']),
						'shipping_service_1dp' => zen_db_prepare_input($region_ups_configs[$region_i]['shipping_service_1dp']),
						'shipping_service_1dpl' => zen_db_prepare_input($region_ups_configs[$region_i]['shipping_service_1dpl']),
						'shipping_service_2dm' => zen_db_prepare_input($region_ups_configs[$region_i]['shipping_service_2dm']),
						'shipping_service_2dml' => zen_db_prepare_input($region_ups_configs[$region_i]['shipping_service_2dml']),
						'shipping_service_2da' => zen_db_prepare_input($region_ups_configs[$region_i]['shipping_service_2da']),
						'shipping_service_2dal' => zen_db_prepare_input($region_ups_configs[$region_i]['shipping_service_2dal']),
						'shipping_service_3ds' => zen_db_prepare_input($region_ups_configs[$region_i]['shipping_service_3ds']),
						'shipping_service_gnd' => zen_db_prepare_input($region_ups_configs[$region_i]['shipping_service_gnd']),
						'shipping_service_std' => zen_db_prepare_input($region_ups_configs[$region_i]['shipping_service_std']),
						'shipping_service_xpr' => zen_db_prepare_input($region_ups_configs[$region_i]['shipping_service_xpr']),
						'shipping_service_xprl' => zen_db_prepare_input($region_ups_configs[$region_i]['shipping_service_xprl']),
						'shipping_service_xdm' => zen_db_prepare_input($region_ups_configs[$region_i]['shipping_service_xdm']),
						'shipping_service_xdml' => zen_db_prepare_input($region_ups_configs[$region_i]['shipping_service_xdml']),
						'shipping_service_xpd' => zen_db_prepare_input($region_ups_configs[$region_i]['shipping_service_xpd']),
						'shipping_service_wxs' => zen_db_prepare_input($region_ups_configs[$region_i]['shipping_service_wxs'])
						);
					
					$region_ups_config_result = zen_db_perform(TABLE_ADVANCED_SHIPPER_REGION_UPS_CONFIGS, $region_ups_config_data_array);
				}
				
				// Save any USPS calculator settings
				if (isset($region_usps_configs[$region_i])) {
					$region_usps_config_data_array = array(
						'config_id' => $config_id,
						'method' => $method_i,
						'region' => $region_i,
						'user_id' => zen_db_prepare_input($region_usps_configs[$region_i]['user_id']),
						'server' => zen_db_prepare_input($region_usps_configs[$region_i]['server']),
						'source_country' => zen_db_prepare_input($region_usps_configs[$region_i]['source_country']),
						'source_postcode' => zen_db_prepare_input($region_usps_configs[$region_i]['source_postcode']),
						'machinable' => zen_db_prepare_input($region_usps_configs[$region_i]['machinable']),
						'display_transit_time' => zen_db_prepare_input($region_usps_configs[$region_i]['display_transit_time']),
						'domestic_express' => zen_db_prepare_input($region_usps_configs[$region_i]['domestic_express']),
						'domestic_priority' => zen_db_prepare_input($region_usps_configs[$region_i]['domestic_priority']),
						'domestic_first_class' => zen_db_prepare_input($region_usps_configs[$region_i]['domestic_first_class']),
						'domestic_parcel' => zen_db_prepare_input($region_usps_configs[$region_i]['domestic_parcel']),
						'domestic_media' => zen_db_prepare_input($region_usps_configs[$region_i]['domestic_media']),
						'domestic_bpm' => zen_db_prepare_input($region_usps_configs[$region_i]['domestic_bpm']),
						'domestic_library' => zen_db_prepare_input($region_usps_configs[$region_i]['domestic_library']),
						'international_ge' => zen_db_prepare_input($region_usps_configs[$region_i]['international_ge']),
						'international_gendr' => zen_db_prepare_input($region_usps_configs[$region_i]['international_gendr']),
						'international_gendnr' => zen_db_prepare_input($region_usps_configs[$region_i]['international_gendnr']),
						'international_emi' => zen_db_prepare_input($region_usps_configs[$region_i]['international_emi']),
						'international_emifre' => zen_db_prepare_input($region_usps_configs[$region_i]['international_emifre']),
						'international_pmi' => zen_db_prepare_input($region_usps_configs[$region_i]['international_pmi']),
						'international_pmifre' => zen_db_prepare_input($region_usps_configs[$region_i]['international_pmifre']),
						'international_pmifrb' => zen_db_prepare_input($region_usps_configs[$region_i]['international_pmifrb']),
						'international_fcmile' => zen_db_prepare_input($region_usps_configs[$region_i]['international_fcmile']),
						'international_fcmip' => zen_db_prepare_input($region_usps_configs[$region_i]['international_fcmip']),
						'international_fcmil' => zen_db_prepare_input($region_usps_configs[$region_i]['international_fcmil']),
						'international_fcmif' => zen_db_prepare_input($region_usps_configs[$region_i]['international_fcmif']),
						'international_fcmipar' => zen_db_prepare_input($region_usps_configs[$region_i]['international_fcmipar'])
						);
					
					$region_usps_config_result = zen_db_perform(TABLE_ADVANCED_SHIPPER_REGION_USPS_CONFIGS, $region_usps_config_data_array);
				}
			}
		}
		
		$num_shipping_methods += $num_shipping_methods_to_insert;
		
		if ($num_shipping_methods_to_insert == 1) {
			$messageStack->add(sprintf(SUCCESS_METHOD_COPIED_ONCE, $affected_method, $insert_after), 'success');
		} else {
			$messageStack->add(sprintf(SUCCESS_METHOD_COPIED_MULTIPLE_TIMES, $affected_method, $num_shipping_methods_to_insert, $insert_after), 'success');
		}
	}
} else if ($action == 'delete') {
	// Delete shipping method from database ////////////////////////////////////////////////////////
	$delete_method_sql = "
		DELETE FROM
			" . TABLE_ADVANCED_SHIPPER_METHOD_CONFIGS . "
		WHERE
			config_id = '" . $config_id . "'
		AND
			method = '" . $affected_method . "';";
	
	$delete_method_result = $db->Execute($delete_method_sql);
	
	$delete_method_sql = "
		DELETE FROM
			" . TABLE_ADVANCED_SHIPPER_METHOD_TITLES . "
		WHERE
			config_id = '" . $config_id . "'
		AND
			method = '" . $affected_method . "';";
	
	$delete_method_result = $db->Execute($delete_method_sql);
	
	$delete_method_sql = "
		DELETE FROM
			" . TABLE_ADVANCED_SHIPPER_METHOD_ADMIN_TITLES . "
		WHERE
			config_id = '" . $config_id . "'
		AND
			method = '" . $affected_method . "';";
	
	$delete_method_result = $db->Execute($delete_method_sql);
	
	$delete_method_sql = "
		DELETE FROM
			" . TABLE_ADVANCED_SHIPPER_METHOD_CATEGORIES . "
		WHERE
			config_id = '" . $config_id . "'
		AND
			method = '" . $affected_method . "';";
	
	$delete_method_result = $db->Execute($delete_method_sql);
	
	$delete_method_sql = "
		DELETE FROM
			" . TABLE_ADVANCED_SHIPPER_METHOD_MANUFACTURERS . "
		WHERE
			config_id = '" . $config_id . "'
		AND
			method = '" . $affected_method . "';";
	
	$delete_method_result = $db->Execute($delete_method_sql);
	
	$delete_method_sql = "
		DELETE FROM
			" . TABLE_ADVANCED_SHIPPER_METHOD_PRODUCTS . "
		WHERE
			config_id = '" . $config_id . "'
		AND
			method = '" . $affected_method . "';";
	
	$delete_method_result = $db->Execute($delete_method_sql);
	
	$delete_method_sql = "
		DELETE FROM
			" . TABLE_ADVANCED_SHIPPER_REGION_CONFIGS . "
		WHERE
			config_id = '" . $config_id . "'
		AND
			method = '" . $affected_method . "';";
	
	$delete_method_result = $db->Execute($delete_method_sql);
	
	$delete_method_sql = "
		DELETE FROM
			" . TABLE_ADVANCED_SHIPPER_REGION_ADMIN_TITLES . "
		WHERE
			config_id = '" . $config_id . "'
		AND
			method = '" . $affected_method . "';";
	
	$delete_method_result = $db->Execute($delete_method_sql);
	
	$delete_method_sql = "
		DELETE FROM
			" . TABLE_ADVANCED_SHIPPER_REGION_TITLES . "
		WHERE
			config_id = '" . $config_id . "'
		AND
			method = '" . $affected_method . "';";
	
	$delete_method_result = $db->Execute($delete_method_sql);
	
	$delete_method_sql = "
		DELETE FROM
			" . TABLE_ADVANCED_SHIPPER_REGION_SURCHARGE_TITLES . "
		WHERE
			config_id = '" . $config_id . "'
		AND
			method = '" . $affected_method . "';";
	
	$delete_method_result = $db->Execute($delete_method_sql);
	
	$delete_method_sql = "
		DELETE FROM
			" . TABLE_ADVANCED_SHIPPER_REGION_UPS_CONFIGS . "
		WHERE
			config_id = '" . $config_id . "'
		AND
			method = '" . $affected_method . "';";
	
	$delete_method_result = $db->Execute($delete_method_sql);
	
	$delete_method_sql = "
		DELETE FROM
			" . TABLE_ADVANCED_SHIPPER_REGION_USPS_CONFIGS . "
		WHERE
			config_id = '" . $config_id . "'
		AND
			method = '" . $affected_method . "';";
	
	$delete_method_result = $db->Execute($delete_method_sql);
	
	// Re-number all following methods
	for ($method_i = $affected_method; $method_i < $num_shipping_methods; $method_i++) {
		$update_method_sql = "
			UPDATE
				" . TABLE_ADVANCED_SHIPPER_METHOD_CONFIGS . "
			SET
				method = '" . $method_i . "'
			WHERE
				config_id = '" . $config_id . "'
			AND
				method = '" . ($method_i + 1) . "';";
		
		$update_method_result = $db->Execute($update_method_sql);
		
		$update_method_sql = "
			UPDATE
				" . TABLE_ADVANCED_SHIPPER_METHOD_TITLES . "
			SET
				method = '" . $method_i . "'
			WHERE
				config_id = '" . $config_id . "'
			AND
				method = '" . ($method_i + 1) . "';";
		
		$update_method_result = $db->Execute($update_method_sql);
		
		$update_method_sql = "
			UPDATE
				" . TABLE_ADVANCED_SHIPPER_METHOD_ADMIN_TITLES . "
			SET
				method = '" . $method_i . "'
			WHERE
				config_id = '" . $config_id . "'
			AND
				method = '" . ($method_i + 1) . "';";
		
		$update_method_result = $db->Execute($update_method_sql);
		
		$update_method_sql = "
			UPDATE
				" . TABLE_ADVANCED_SHIPPER_METHOD_CATEGORIES . "
			SET
				method = '" . $method_i . "'
			WHERE
				config_id = '" . $config_id . "'
			AND
				method = '" . ($method_i + 1) . "';";
		
		$update_method_result = $db->Execute($update_method_sql);
		
		$update_method_sql = "
			UPDATE
				" . TABLE_ADVANCED_SHIPPER_METHOD_MANUFACTURERS . "
			SET
				method = '" . $method_i . "'
			WHERE
				config_id = '" . $config_id . "'
			AND
				method = '" . ($method_i + 1) . "';";
		
		$update_method_result = $db->Execute($update_method_sql);
		
		$update_method_sql = "
			UPDATE
				" . TABLE_ADVANCED_SHIPPER_METHOD_PRODUCTS . "
			SET
				method = '" . $method_i . "'
			WHERE
				config_id = '" . $config_id . "'
			AND
				method = '" . ($method_i + 1) . "';";
		
		$update_method_result = $db->Execute($update_method_sql);
		
		$update_method_sql = "
			UPDATE
				" . TABLE_ADVANCED_SHIPPER_REGION_CONFIGS . "
			SET
				method = '" . $method_i . "'
			WHERE
				config_id = '" . $config_id . "'
			AND
				method = '" . ($method_i + 1) . "';";
		
		$update_method_result = $db->Execute($update_method_sql);
		
		$update_method_sql = "
			UPDATE
				" . TABLE_ADVANCED_SHIPPER_REGION_ADMIN_TITLES . "
			SET
				method = '" . $method_i . "'
			WHERE
				config_id = '" . $config_id . "'
			AND
				method = '" . ($method_i + 1) . "';";
		
		$update_method_result = $db->Execute($update_method_sql);
		
		$update_method_sql = "
			UPDATE
				" . TABLE_ADVANCED_SHIPPER_REGION_TITLES . "
			SET
				method = '" . $method_i . "'
			WHERE
				config_id = '" . $config_id . "'
			AND
				method = '" . ($method_i + 1) . "';";
		
		$update_method_result = $db->Execute($update_method_sql);
		
		$update_method_sql = "
			UPDATE
				" . TABLE_ADVANCED_SHIPPER_REGION_SURCHARGE_TITLES . "
			SET
				method = '" . $method_i . "'
			WHERE
				config_id = '" . $config_id . "'
			AND
				method = '" . ($method_i + 1) . "';";
		
		$update_method_result = $db->Execute($update_method_sql);
		
		$update_method_sql = "
			UPDATE
				" . TABLE_ADVANCED_SHIPPER_REGION_UPS_CONFIGS . "
			SET
				method = '" . $method_i . "'
			WHERE
				config_id = '" . $config_id . "'
			AND
				method = '" . ($method_i + 1) . "';";
		
		$update_method_result = $db->Execute($update_method_sql);
		
		$update_method_sql = "
			UPDATE
				" . TABLE_ADVANCED_SHIPPER_REGION_USPS_CONFIGS . "
			SET
				method = '" . $method_i . "'
			WHERE
				config_id = '" . $config_id . "'
			AND
				method = '" . ($method_i + 1) . "';";
		
		$update_method_result = $db->Execute($update_method_sql);
	}
	
	$num_shipping_methods--;
	
	$messageStack->add(sprintf(SUCCESS_METHOD_DELETED, $affected_method), 'success');
}


// Load the settings for the shipping methods for the loaded configuration
$load_methods_config_sql = "
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
	ORDER BY
		asmc.method";

// Split Page
// reset page when page is unknown
if (($_GET['page'] == '' || $_GET['page'] <= 1)) {
	$_GET['page'] = 1;
}

// Fix silly problem with Zen Cart's splitPageResults class
$load_methods_config_sql = strtolower(str_replace("\n", ' ', $load_methods_config_sql));
$load_methods_config_sql = str_replace("\r", ' ', $load_methods_config_sql);
$load_methods_config_sql = str_replace("\t", ' ', $load_methods_config_sql);

$num_of_rows = '';
$methods_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS_ORDERS, $load_methods_config_sql, $num_of_rows);
$methods = $db->Execute($load_methods_config_sql);

$num_pages = ceil($num_of_rows / MAX_DISPLAY_SEARCH_RESULTS_ORDERS);

// Build the list of values for the insert region and copy regions selects
$insert_select_values = array();
$insert_select_values[] = array(
	'id' => '-1',
	'text' => TEXT_NUM_METHODS_TO_INSERT
	);
for ($i = 1; $i < 11; $i++) {
	$insert_select_values[] = array(
		'id' => $i,
		'text' => $i
		);
}

$num_copies_select_values = array();
$num_copies_select_values[] = array(
	'id' => '-1',
	'text' => TEXT_NUM_METHODS_TO_COPY
	);
for ($i = 1; $i < 11; $i++) {
	$num_copies_select_values[] = array(
		'id' => $i,
		'text' => $i
		);
}

$copy_to_select_values = array();
for ($i = 1; $i <= $num_shipping_methods; $i++) {
	$copy_to_select_values[] = array(
		'id' => $i,
		'text' => sprintf(TEXT_INSERT_AFTER_METHOD, $i)
		);
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
<?php require(DIR_WS_INCLUDES . 'javascript/advshipper.js'); ?>
	//-->
	</script>
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
	
	fieldset#advanced_shipper_general_config { background: #F7F6F0; }
	
	.AdvancedShipperMethodOdd {
		background-color: #d0d0d0;
	}
	.AdvancedShipperMethodEven {
		background-color: #f3f3f3;
	}
	.AdvancedShipperMethodSummaryOdd {
		background-color: #d9d9d9;
		padding-bottom: 0;
	}
	.AdvancedShipperMethodSummaryEven {
		background-color: #fafafa;
		padding-bottom: 0;
	}
	.AdvancedShipperMethodOddMethodOdd {
		background-color: #c6c6d6;
	}
	.AdvancedShipperMethodOddMethodEven {
		background-color: #d6d6e6;
	}
	.AdvancedShipperMethodEvenMethodOdd {
		background-color: #dae6da;
	}
	.AdvancedShipperMethodEvenMethodEven {
		background-color: #edfded;
	}
	
	.AdvancedShipperConfigLabel, .AdvancedShipperConfigField, .AdvancedShipperConfigDesc,
	.AdvancedShipperConfigButtonPanel {
		vertical-align: top;
	}
	.AdvancedShipperConfigLabel { font-weight: bold; padding-right: 1em; vertical-align: top; }
	.AdvancedShipperConfigLabel { width: 20%; }
	.AdvancedShipperConfigField { padding-bottom: 1.3em; vertical-align: top; }
	.AdvancedShipperConfigIntro { padding-top: 0.5em; padding-bottom:1.1em;  }
	.AdvancedShipperConfigButtonPanel { text-align: right; margin-bottom: 0.8em; vertical-align: top; }
	.AdvancedShipperConfigButtonPanel input { vertical-align: middle; }
	
	fieldset.AdvancedShipperMethodSummary, fieldset.AdvancedShipperMethodSummaryOdd,
	fieldset.AdvancedShipperMethodSummaryEven {
		padding: 0.4em 0.8em;
		margin: 0.3em 0 0.8em 0;
	}
	
	fieldset.AdvancedShipperMethodOddRegionEven {
		background-color: #e2e2e2;
	}
	
	fieldset.AdvancedShipperMethodEvenRegionEven {
		background-color: #fdfdfd;
	}
	
	fieldset.AdvancedShipperMethodSummaryOdd p, fieldset.AdvancedShipperMethodSummaryEven p {
		margin: 0 0 0.8em 0;
		padding: 0;
	}
	
	.NoRegionsDefined { font-weight: bold; }
	
	.Collapse { display:  none; }
	</style>
</head>
<body onload="init()">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
	<tr>
<!-- body_text //-->
		<td width="100%" valign="top">
			<table border="0" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td class="pageHeading AdvancedShipperPageHeading"><?php echo HEADING_TITLE; ?></td>
				</tr>
				<tr>
					<td style="padding-bottom: 1em;">
						<table cellpadding="0" cellspacing="0" border="0" width="100%">
							<tr>
								<td class="smallText" valign="top"><?php echo $methods_split->display_count($num_of_rows, MAX_DISPLAY_SEARCH_RESULTS_ORDERS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_METHODS); ?></td>
								<td class="smallText" align="right"><?php echo $methods_split->display_links($num_of_rows, MAX_DISPLAY_SEARCH_RESULTS_ORDERS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], zen_get_all_get_params(array('page', 'oID', 'action'))); ?></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo zen_draw_form('advshipper', FILENAME_ADVANCED_SHIPPER, 'page=' . $_GET['page'], 'post', 'onsubmit=""', true);
echo zen_hide_session_id(); ?>
<?php

// Build controls for each shipping method and a summary of products and regions included //////////
while (!$methods->EOF) {
	$method_num = $methods->fields['method'];
	
	// Get the admin title for this method
	$method_admin_titles = array();
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
			$method_admin_titles[$method_num][$load_method_admin_titles_config_result->fields['language_id']] = $load_method_admin_titles_config_result->fields['title'];
			
			$load_method_admin_titles_config_result->MoveNext();
		}
	}
	
	// Get the catalog-side title for this method
	$method_titles = array();
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
			$method_titles[$method_num][$load_method_titles_config_result->fields['language_id']] =
				$load_method_titles_config_result->fields['title'];
			
			$load_method_titles_config_result->MoveNext();
		}
	}
	
	if ($methods->fields['select_products'] == ADVSHIPPER_SELECT_PRODUCT_FALLOVER) {
		$method_uses_fallover = true;
	} else {
		$method_uses_fallover = false;
	}
	
	// Load the categories to which this shipping method applies (if any) //////////////////////
	$categories = array();
	
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
	
	// Load the manufacturers to which this shipping method applies (if any) //////////////////////
	$manufacturers = array();
	
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
	
	// Load the products to which this shipping method applies (if any) ////////////////////////
	$products = array();
	
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
			
			$product_name = str_replace('(())', '', zen_get_products_name(
				$product_id,
				$_SESSION['languages_id']
				));
			
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
						$product_attributes_id =
							$load_product_options_result->fields['product_attributes_id'];
						
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
							
							$product_name .= '<br />// ' .
								$option_name_result->fields['products_options_name'] . ' -- ' .
								$option_value_result->fields['products_options_values_name'];
						}
						
						$load_product_options_result->MoveNext();;
					}
				}
			}
			
			$products[] = array(
				'id' => $product_id,
				'name' => $product_name
				);
			
			$load_products_result->MoveNext();
		}
	}
		
	// Get the admin titles of the regions this method applies for
	$region_admin_titles = array();
	$load_region_admin_titles_config_sql = "
		SELECT
			asrat.region,
			asrat.language_id,
			asrat.title
		FROM
			" . TABLE_ADVANCED_SHIPPER_REGION_ADMIN_TITLES . " asrat
		WHERE
			asrat.config_id = '" . $config_id . "'
		AND
			asrat.method = '" . $method_num . "'
		ORDER BY
			asrat.region;";
	
	$load_region_admin_titles_config_result = $db->Execute($load_region_admin_titles_config_sql);
	
	if ($load_region_admin_titles_config_result->EOF) {
		
	} else {
		while (!$load_region_admin_titles_config_result->EOF) {
			$region_admin_titles[$load_region_admin_titles_config_result->fields['region']][$load_region_admin_titles_config_result->fields['language_id']] =
				$load_region_admin_titles_config_result->fields['title'];
			
			$load_region_admin_titles_config_result->MoveNext();
		}
	}
	
	// Get the information about the regions this method applies for
	$region_info = array();
	$load_region_info_config_sql = "
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
			asrc.surcharge
		FROM
			" . TABLE_ADVANCED_SHIPPER_REGION_CONFIGS . " asrc
		WHERE
			asrc.config_id = '" . $config_id . "'
		AND
			asrc.method = '" . $method_num . "'
		ORDER BY
			asrc.region;";
	
	$load_region_info_config_result = $db->Execute($load_region_info_config_sql);
	
	if ($load_region_info_config_result->EOF) {
		$num_regions = 0;
	} else {
		while (!$load_region_info_config_result->EOF) {
			$region_info[] = $load_region_info_config_result->fields;
			
			$load_region_info_config_result->MoveNext();
		}
		
		$num_regions = sizeof($region_info);
	}
	
	?>
<fieldset class="AdvancedShipperMethod<?php echo (($method_num % 2 == 0) ? 'Even' : 'Odd'); ?>">
	<legend><?php
	echo TEXT_METHOD . ' ' . $method_num;
	if (strlen($method_admin_titles[$method_num][$_SESSION['languages_id']]) > 0) {
		echo ' - &ldquo;' . htmlentities($method_admin_titles[$method_num][$_SESSION['languages_id']], ENT_COMPAT, CHARSET) . '&rdquo;';
	}
?></legend>
<?php
	// Don't collapse this method's display if errors must be displayed!
	$collapse = true;
?>
	<table border="0" width="100%" cellpadding="0" cellspacing="0" id="method_<?php echo $method_num; ?>_config" <?php echo ($collapse == true ? 'class="Collapse"' : ''); ?>>
		<tr>
			<td valign="top" width="60%">
				<table border="0" width="100%" cellpadding="0" cellspacing="0">
					<tr>
						<td>
							<fieldset width="100%" class="AdvancedShipperMethodSummary<?php echo (($method_num % 2 == 0) ? 'Even' : 'Odd'); ?>">
								<legend>
								<?php
								echo TEXT_METHOD_TITLE;
								?>
								</legend>
								<table border="0" width="100%" cellpadding="0" cellspacing="0">
									<tr>
										<td class="AdvancedShipperConfigLabel"><label for="<?php echo "method_title_" . $method_num; ?>"><?php echo TEXT_LABEL_METHOD_TITLE; ?></label></td>
										<td class="AdvancedShipperConfigField">
											<?php
											echo htmlentities($method_titles[$method_num][$_SESSION['languages_id']], ENT_COMPAT, CHARSET);
											?>
										</td>
									</tr>
								</table>
							</fieldset>
						</td>
					</tr>
					<tr>
						<td>
							<fieldset width="100%" class="AdvancedShipperMethodSummary<?php echo (($method_num % 2 == 0) ? 'Even' : 'Odd'); ?>">
								<legend>
								<?php
								echo TEXT_CATEGORIES_MANUFACTURERS_PRODUCTS_SELECTION;
								?>
								</legend>
								<?php
								if ($method_uses_fallover) {
									echo '<p>' . TEXT_FALLOVER_PRODUCTS . '</p>';
								}
								
								$num_categories = sizeof($categories);
								
								if ($num_categories > 0) {
								?>
								<table border="0" width="100%" cellpadding="0" cellspacing="0">
									<tr>
										<td class="AdvancedShipperConfigLabel"><label for="categories"><?php echo TEXT_LABEL_CATEGORIES; ?></label></td>
										<td class="AdvancedShipperConfigField">
											<?php
											for ($category_i = 0; $category_i < $num_categories; $category_i++) {
												echo '<p>' . $categories[$category_i]['name'] . '</p>';
											}
											?>
										</td>
									</tr>
								</table>
								<?php }
								
								$num_manufacturers = sizeof($manufacturers);
								
								if ($num_manufacturers > 0) {
								?>
								<table border="0" width="100%" cellpadding="0" cellspacing="0">
									<tr>
										<td class="AdvancedShipperConfigLabel"><label for="manufacturers"><?php echo TEXT_LABEL_MANUFACTURERS; ?></label></td>
										<td class="AdvancedShipperConfigField">
											<?php
											for ($manufacturer_i = 0; $manufacturer_i < $num_manufacturers; $manufacturer_i++) {
												echo '<p>' . $manufacturers[$manufacturer_i]['name'] . '</p>';
											}
											?>
										</td>
									</tr>
								</table>
								<?php }
								
								$num_products = sizeof($products);
								
								if ($num_products > 0) {
								?>
								<table border="0" width="100%" cellpadding="0" cellspacing="0">
									<tr>
										<td class="AdvancedShipperConfigLabel"><label for="products"><?php echo TEXT_LABEL_PRODUCTS; ?></label></td>
										<td class="AdvancedShipperConfigField">
											<?php
											for ($product_i = 0; $product_i < $num_products; $product_i++) {
												echo '<p>' . $products[$product_i]['name'] . '</p>';
											}
											?>
										</td>
									</tr>
								</table>
								<?php }
								if (!$method_uses_fallover && $num_categories == 0 &&
										$num_products == 0 && $num_manufacturers == 0) {
									echo '<p>' . htmlentities(TEXT_NO_CATEGORIES_MANUFACTURERS_PRODUCTS_SELECTIONS, ENT_COMPAT, CHARSET) . '</p>';
								}
								?>
							</fieldset>
						</td>
					</tr>
					<tr>
						<td colspan="2">
						<?php
						// Build the summary of the shipping regions for this method ///////////////
						for ($region_i = 0; $region_i < $num_regions; $region_i++) {
							$region_num = $region_i + 1;
						?>
							<fieldset width="100%" class="AdvancedShipperMethodSummary<?php echo (($method_num % 2 == 0) ? 'Even' : 'Odd'); ?> AdvancedShipperMethod<?php echo (($method_num % 2 == 0) ? 'Even' : 'Odd') .
								'Region' . ((($region_i + 1) % 2 == 0) ? 'Odd' : 'Even'); ?>">
								<legend>
								<?php
								echo TEXT_REGION . ' ' . $region_num;
								?>
								</legend>
								<table border="0" width="100%" cellpadding="0" cellspacing="0">
							<?php
							if (strlen($region_admin_titles[$region_num][$_SESSION['languages_id']]) > 0) { ?>
									<tr>
										<td class="AdvancedShipperConfigLabel"><label for="<?php echo "region_admin_titles[$region_num]"; ?>"><?php echo TEXT_LABEL_REGION_ADMIN_TITLE; ?></label></td>
										<td class="AdvancedShipperConfigField">
								<?php
									echo '&ldquo;'  . htmlentities($region_admin_titles[$region_num][$_SESSION['languages_id']], ENT_COMPAT, CHARSET) . '&rdquo;';
								?>
										</td>
									</tr>
							<?php
							}
							?>
							<?php
							if (strlen($region_titles[$region_num][$_SESSION['languages_id']]) > 0) { ?>
									<tr>
										<td class="AdvancedShipperConfigLabel"><label for="<?php echo "region_titles[$region_num]"; ?>"><?php echo TEXT_LABEL_REGION_TITLE; ?></label></td>
										<td class="AdvancedShipperConfigField">
								<?php
									echo '&ldquo;'  . htmlentities($region_titles[$region_num][$_SESSION['languages_id']], ENT_COMPAT, CHARSET) . '&rdquo;';
								?>
										</td>
									</tr>
							<?php
							}
							?>
									<?php
									if($region_info[$region_i]['definition_method'] == ADVSHIPPER_DEFINITION_METHOD_ADDRESS_MATCHING
										&& strlen($region_info[$region_i]['countries_postcodes']) > 0) {
									?>
									<tr>
										<td class="AdvancedShipperConfigLabel"><label for="<?php echo "countries_postcodes[$region_i]"; ?>"><?php echo TEXT_LABEL_COUNTRIES_POSTCODES; ?></label></td>
										<td class="AdvancedShipperConfigField">
											<?php
											// Replace commas with a comma and a space to prevent
											// long lines
											echo '<p>' . str_replace(',', ', ', $region_info[$region_i]['countries_postcodes']) . '</p>';
											?>
										</td>
									</tr>
									<?php
									}
									if($region_info[$region_i]['definition_method'] == ADVSHIPPER_DEFINITION_METHOD_ADDRESS_MATCHING
										&& strlen($region_info[$region_i]['countries_zones']) > 0) {
									?>
									<tr>
										<td class="AdvancedShipperConfigLabel"><label for="<?php echo "countries_zones[$region_i]"; ?>"><?php echo TEXT_LABEL_COUNTRIES_ZONES; ?></label></td>
										<td class="AdvancedShipperConfigField">
											<?php
											$countries_zones_info = advshipper_zones_get_ids_and_names_for_zones_string($region_info[$region_i]['countries_zones']);
											
											$num_zones = sizeof($countries_zones_info);
											
											if ($num_zones > 0) {
												foreach ($countries_zones_info as $country_zone) {
													echo '<p>' . htmlentities($country_zone['name'], ENT_COMPAT, CHARSET) . '</p>';
												}
											}
											?>
										</td>
									</tr>
									<?php
									}
									if($region_info[$region_i]['definition_method'] == ADVSHIPPER_DEFINITION_METHOD_ADDRESS_MATCHING
										&& strlen($region_info[$region_i]['countries_states']) > 0) {
									?>
									<tr>
										<td class="AdvancedShipperConfigLabel"><label for="<?php echo "countries_states[$region_i]"; ?>"><?php echo TEXT_LABEL_COUNTRIES_STATES; ?></label></td>
										<td class="AdvancedShipperConfigField">
											<?php
											$countries_states_info = localities_parse_identifiers_string($region_info[$region_i]['countries_states']);
											
											$num_states = sizeof($countries_states_info);
											
											if ($num_states > 0) {
												foreach ($countries_states_info as $country_state) {
													echo '<p>' . htmlentities(localities_get_level_2_locality_name($country_state['level_2_id'], null, true), ENT_COMPAT, CHARSET) . '</p>';
												}
											}
											?>
										</td>
									</tr>
									<?php
									}
									if($region_info[$region_i]['definition_method'] == ADVSHIPPER_DEFINITION_METHOD_ADDRESS_MATCHING
										&& strlen($region_info[$region_i]['countries_cities']) > 0) {
									?>
									<tr>
										<td class="AdvancedShipperConfigLabel"><label for="<?php echo "countries_cities[$region_i]"; ?>"><?php echo TEXT_LABEL_COUNTRIES_CITIES; ?></label></td>
										<td class="AdvancedShipperConfigField">
											<?php
											$countries_cities_info = localities_parse_identifiers_string($region_info[$region_i]['countries_cities']);
											
											$num_cities = sizeof($countries_cities_info);
											
											if ($num_cities > 0) {
												foreach ($countries_cities_info as $country_city) {
													echo '<p>' . htmlentities(localities_get_level_3_locality_name($country_city['level_3_id'], null, true), ENT_COMPAT, CHARSET) . '</p>';
												}
											}
											?>
										</td>
									</tr>
									<?php
									}
									if($region_info[$region_i]['definition_method'] == ADVSHIPPER_DEFINITION_METHOD_GEOLOCATION) {
									?>
									<tr>
										<td class="AdvancedShipperConfigLabel"><label for="<?php echo "distance[$region_i]"; ?>"><?php echo TEXT_LABEL_DISTANCE; ?></label></td>
										<td class="AdvancedShipperConfigField">
											<?php
											echo '<p>' . htmlentities($region_info[$region_i]['distance'], ENT_COMPAT, CHARSET) . '</p>';
											?>
										</td>
									</tr>
									<?php
									}
									?>
									<tr>
										<td class="AdvancedShipperConfigLabel"><label for="<?php echo "table_of_rates[$region_i]"; ?>"><?php echo TEXT_LABEL_TABLE_OF_RATES; ?></label></td>
										<td class="AdvancedShipperConfigField">
											<?php
											// Replace commas with a comma and a space to prevent
											// long lines
											if (strlen(trim($region_info[$region_i]['table_of_rates'])) == 0) {
												echo '<p>' . TEXT_REGION_HAS_NO_RATES . '</p>';
											} else {
												echo '<p>' . htmlentities(str_replace(',', ', ', $region_info[$region_i]['table_of_rates']), ENT_COMPAT, CHARSET) . '</p>';
											}
											?>
										</td>
									</tr>
									<tr>
										<td class="AdvancedShipperConfigLabel"><label for="<?php echo "surcharge[$region_i]"; ?>"><?php echo TEXT_LABEL_SURCHARGE; ?></label></td>
										<td class="AdvancedShipperConfigField">
											<?php
											// Replace commas with a comma and a space to prevent
											// long lines
											if (strlen(trim($region_info[$region_i]['surcharge'])) == 0) {
												echo '<p>' . TEXT_REGION_HAS_NO_SURCHARGE . '</p>';
											} else {
												echo '<p>' . htmlentities(str_replace(',', ', ', $region_info[$region_i]['surcharge']), ENT_COMPAT, CHARSET) . '</p>';
											}
											?>
										</td>
									</tr>
								</table>
							</fieldset>
						<?php
						}
						if ($num_regions == 0) {
						?>
							<fieldset width="100%" class="AdvancedShipperMethodSummary<?php echo (($method_num % 2 == 0) ? 'Even' : 'Odd'); ?>">
								<legend>
								<?php
								echo TEXT_REGIONS;
								?>
								</legend>
								<p class="NoRegionsDefined"><?php echo htmlentities(TEXT_NO_REGIONS_DEFINED, ENT_COMPAT, CHARSET); ?></p>
							</fieldset>
						<?php
						}
						?>
						</td>
					</tr>
				</table>
			</td>
			<td class="AdvancedShipperConfigButtonPanel" style="padding-left: 1.5em; padding-bottom: 1em;">
				<fieldset width="100%" class="AdvancedShipperMethodSummary<?php echo (($method_num % 2 == 0) ? 'Even' : 'Odd'); ?>">
					<legend>
					<?php
					echo TEXT_INSERT_METHODS_TITLE;
					?>
					</legend>
					<?php
					$insert_select = zen_draw_pull_down_menu('insert_num_shipping_methods_' . $method_num, $insert_select_values);
					echo '<p>' . TEXT_INSERT_METHODS . '</p>';
					echo '<p>' . $insert_select . ' ' . zen_image_submit('button_insert_before.gif', TEXT_INSERT_METHOD, 'name="insert_' . $method_num . '" value="' . $method_num . '"') . '</p>';
					?>
				</fieldset>
				<fieldset width="100%" class="AdvancedShipperMethodSummary<?php echo (($method_num % 2 == 0) ? 'Even' : 'Odd'); ?>">
					<legend>
					<?php
					echo TEXT_COPY_METHOD_TITLE;
					?>
					</legend>
					<?php
					$num_copies_select = zen_draw_pull_down_menu('copy_num_shipping_methods_' . $method_num, $num_copies_select_values);
					$copy_to_select = zen_draw_pull_down_menu('copy_to_' . $method_num, $copy_to_select_values, $method_num);
					echo '<p>' . TEXT_COPY_METHODS . '</p>';
					echo '<p>' . $num_copies_select  . ' ' . $copy_to_select. ' ' . zen_image_submit('button_copy.gif', TEXT_COPY_METHOD, 'name="copy_' . $method_num . '" value="' . $method_num . '"') . '</p>';
					?>
				</fieldset>
				<fieldset width="100%" class="AdvancedShipperMethodSummary<?php echo (($method_num % 2 == 0) ? 'Even' : 'Odd'); ?>">
					<legend>
					<?php
					echo TEXT_DELETE_METHOD_TITLE;
					?>
					</legend>
					<?php
					echo '<p>' . zen_image_submit('button_delete.gif', TEXT_DELETE_METHOD, 'name="delete_' . $method_num . '" value="' . $method_num . '" onClick="javascript:return advshipperConfirmDeletion();"') . '</p>';
					?>
				</fieldset>
			</td>
		</tr>
	</table>
	<table border="0" width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td>
<?php
	echo '<a href="#" onclick="return advshipperToggleMethod(' . $method_num . ', this, \'' . TEXT_HIDE_METHOD . '\', \'' . TEXT_SHOW_METHOD . '\');">';
	if ($collapse) {
		// Display control to show hidden content
		echo TEXT_SHOW_METHOD . '</a>';
	} else {
		// Display control to hide content
		echo TEXT_HIDE_METHOD . '</a>';
	}
?>
			</td>
			<td class="AdvancedShipperConfigButtonPanel">
				<p><a href="<?php echo zen_href_link(FILENAME_ADVANCED_SHIPPER_METHOD_CONFIG, 'config_id=' . $config_id . '&method_num=' . $method_num . '&page=' . $_GET['page']); ?>"><?php echo zen_image_button('button_edit.gif', TEXT_EDIT_METHOD); ?></a></p>
			</td>
		</tr>
	</table>
</fieldset>
<?php
	$methods->MoveNext();
}
// Only display add method button if on the last page
if ($num_pages == $_GET['page']) {
?>
	<p style="text-align: right; padding: 0.4em 1em; margin-top: -0.8em;"><a href="<?php echo zen_href_link(FILENAME_ADVANCED_SHIPPER_METHOD_CONFIG, 'config_id=' . $config_id . '&method_num=' . ($num_shipping_methods + 1) . '&add=true&page=' . $_GET['page']); ?>"><?php echo zen_image_button('button_insert.gif', TEXT_ADD_METHOD); ?></a></p>
<?php
}
?>
						</form>
					</td>
				</tr>
				<tr>
					<td>
						<table cellpadding="0" cellspacing="0" border="0" width="100%">
							<tr>
								<td class="smallText" valign="top"><?php echo $methods_split->display_count($num_of_rows, MAX_DISPLAY_SEARCH_RESULTS_ORDERS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_METHODS); ?></td>
								<td class="smallText" align="right"><?php echo $methods_split->display_links($num_of_rows, MAX_DISPLAY_SEARCH_RESULTS_ORDERS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], zen_get_all_get_params(array('page', 'oID', 'action'))); ?></td>
							</tr>
						</table>
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
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>