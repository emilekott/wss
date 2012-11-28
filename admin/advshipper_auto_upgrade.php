<?php
/**
 * Advanced Shipper Upgrade Script - Upgrades database from version 2.x onwards
 *
 * @package    admin
 * @author     Conor Kerr <zen-cart.advshipper@dev.ceon.net>
 * @copyright  Copyright 2007-2009 Ceon
 * @copyright  Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright  Portions Copyright 2003 osCommerce
 * @link       http://dev.ceon.net/web/zen-cart/advshipper
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: advshipper_auto_upgrade.php 382 2009-06-22 18:49:29Z Bob $
 */

// Find out what the latest version number is
require_once(DIR_FS_CATALOG . DIR_WS_MODULES . 'shipping/advshipper.php');


// {{{ advshipper_update_surcharge_column()
/*
 * Update the surcharge column of the region configs table and update the format of any existing
 * surcharges.
 */
function advshipper_update_surcharge_column()
{
	global $db;
	
	$update_surcharge_column_sql = "
		ALTER TABLE
			" . TABLE_ADVANCED_SHIPPER_REGION_CONFIGS . "
		CHANGE
			`surcharge` `surcharge` TEXT NULL;";
	
	$update_surcharge_column_result = $db->Execute($update_surcharge_column_sql);
	
	// Now modify all existing surcharges to use the new format
	$surcharges_query = "
		SELECT
			config_id,
			method,
			region,
			surcharge
		FROM
			" . TABLE_ADVANCED_SHIPPER_REGION_CONFIGS . "
		WHERE
			surcharge IS NOT NULL;";
	
	$surcharges_result = $db->Execute($surcharges_query);
	
	while (!$surcharges_result->EOF) {
		$surcharge = $surcharges_result->fields['surcharge'];
		
		$pattern = '|^(\<[^\>]+\>)|iU';
		
		if (preg_match($pattern, $surcharge, $matches)) {
			$opening_tag = $matches[1];
			
			$surcharge = str_replace($opening_tag, $opening_tag . '*: ', $surcharge);
			
			$update_record_format_query = "
				UPDATE
					" . TABLE_ADVANCED_SHIPPER_REGION_CONFIGS . "
				SET
					surcharge = '" . zen_db_input($surcharge) . "'
				WHERE
					config_id = '" . $surcharges_result->fields['config_id'] . "'
				AND
					method = '" . $surcharges_result->fields['method'] . "'
				AND
					region = '" . $surcharges_result->fields['region'] . "';";
			
			$update_record_format_result = $db->Execute($update_record_format_query);
		}
		
		$surcharges_result->MoveNext();
	}
}

// }}}


// Handle latest releases //////////////////////////////////////////////////////////////////////////
if (substr(MODULE_ADVANCED_SHIPPER_MADE_BY_CEON, 0, 3) == '3.0') {
	// Update the surcharge column of the region configs table /////////////////////////////////////
	advshipper_update_surcharge_column();
	
	// Add the Manufacturer Selections table ///////////////////////////////////////////////////////
	$add_method_manufacturers_table_sql = "
		CREATE TABLE IF NOT EXISTS
			`" . TABLE_ADVANCED_SHIPPER_METHOD_MANUFACTURERS . "`
		(
			`config_id` INT UNSIGNED NOT NULL,
			`method` INT UNSIGNED NOT NULL,
			`manufacturer_order` INT UNSIGNED NOT NULL,
			`manufacturer_id` INT UNSIGNED NOT NULL,
			PRIMARY KEY ( 
				`config_id`,
				`method`,
				`manufacturer_order`,
				`manufacturer_id`
			)
		);";
	
	$add_method_manufacturers_table_result = $db->Execute($add_method_manufacturers_table_sql);
	
	// Add the new maximum weight per package column to the region configs table
	$add_column_sql = "
		ALTER TABLE
			" . TABLE_ADVANCED_SHIPPER_REGION_CONFIGS . "
		ADD
			`max_weight_per_package` FLOAT UNSIGNED NULL
		AFTER
			`table_of_rates`;";
	
	$add_column_result = $db->Execute($add_column_sql);
	
	// Add the new packaging weights column to the region configs table
	$add_column_sql = "
		ALTER TABLE
			" . TABLE_ADVANCED_SHIPPER_REGION_CONFIGS . "
		ADD
			`packaging_weights` TEXT NULL
		AFTER
			`max_weight_per_package`;";
	
	$add_column_result = $db->Execute($add_column_sql);
	
	$update_module_copyright_info_sql = "
		UPDATE
			" . TABLE_CONFIGURATION . "
		SET
			configuration_title = '</b></fieldset><img src=\"" . DIR_WS_ADMIN . "/images/ceon_button_logo.png\" alt=\"Made by Ceon. &copy; 2007-2009 Ceon\" align=\"right\" style=\"margin: 1em 0.2em;\"/><br />Module &copy; 2007-2009 Ceon<p style=\"display: none\">'
		WHERE
			configuration_key = 'MODULE_ADVANCED_SHIPPER_MADE_BY_CEON';";
			
	$update_module_copyright_info_result = $db->Execute($update_module_copyright_info_sql);
	
	// Add the new total up price inc tax column to the region configs table
	$add_column_sql = "
		ALTER TABLE
			" . TABLE_ADVANCED_SHIPPER_REGION_CONFIGS . "
		ADD
			`total_up_price_inc_tax` TINYINT NOT NULL DEFAULT '2'
		AFTER
			`rate_limits_inc`;";
	
	$add_column_result = $db->Execute($add_column_sql);
	
} else if (substr(MODULE_ADVANCED_SHIPPER_MADE_BY_CEON, 0, 3) == '3.2') {
	// Update the surcharge column of the region configs table /////////////////////////////////////
	advshipper_update_surcharge_column();
	
	// Add the new maximum weight per package column to the region configs table
	$add_column_sql = "
		ALTER TABLE
			" . TABLE_ADVANCED_SHIPPER_REGION_CONFIGS . "
		ADD
			`max_weight_per_package` FLOAT UNSIGNED NULL
		AFTER
			`table_of_rates`;";
	
	$add_column_result = $db->Execute($add_column_sql);
	
	// Add the new packaging weights column to the region configs table
	$add_column_sql = "
		ALTER TABLE
			" . TABLE_ADVANCED_SHIPPER_REGION_CONFIGS . "
		ADD
			`packaging_weights` TEXT NULL
		AFTER
			`max_weight_per_package`;";
	
	$add_column_result = $db->Execute($add_column_sql);
	
	$update_module_copyright_info_sql = "
		UPDATE
			" . TABLE_CONFIGURATION . "
		SET
			configuration_title = '</b></fieldset><img src=\"" . DIR_WS_ADMIN . "/images/ceon_button_logo.png\" alt=\"Made by Ceon. &copy; 2007-2009 Ceon\" align=\"right\" style=\"margin: 1em 0.2em;\"/><br />Module &copy; 2007-2009 Ceon<p style=\"display: none\">'
		WHERE
			configuration_key = 'MODULE_ADVANCED_SHIPPER_MADE_BY_CEON';";
			
	$update_module_copyright_info_result = $db->Execute($update_module_copyright_info_sql);
	
	// Add the new total up price inc tax column to the region configs table
	$add_column_sql = "
		ALTER TABLE
			" . TABLE_ADVANCED_SHIPPER_REGION_CONFIGS . "
		ADD
			`total_up_price_inc_tax` TINYINT NOT NULL DEFAULT '2'
		AFTER
			`rate_limits_inc`;";
	
	$add_column_result = $db->Execute($add_column_sql);
	
} else if (substr(MODULE_ADVANCED_SHIPPER_MADE_BY_CEON, 0, 3) == '3.4') {
	// Update the surcharge column of the region configs table /////////////////////////////////////
	advshipper_update_surcharge_column();
	
	// Add the new maximum weight per package column to the region configs table
	$add_column_sql = "
		ALTER TABLE
			" . TABLE_ADVANCED_SHIPPER_REGION_CONFIGS . "
		ADD
			`max_weight_per_package` FLOAT UNSIGNED NULL
		AFTER
			`table_of_rates`;";
	
	$add_column_result = $db->Execute($add_column_sql);
	
	// Must ensure that packaging weights column has the correct null setting
	$update_packaging_weights_column_sql = "
		ALTER TABLE
			" . TABLE_ADVANCED_SHIPPER_REGION_CONFIGS . "
		CHANGE
			`packaging_weights` `packaging_weights` TEXT NULL;";
	
	$update_packaging_weights_column_result = $db->Execute($update_packaging_weights_column_sql);
	
	$update_module_copyright_info_sql = "
		UPDATE
			" . TABLE_CONFIGURATION . "
		SET
			configuration_title = '</b></fieldset><img src=\"" . DIR_WS_ADMIN . "/images/ceon_button_logo.png\" alt=\"Made by Ceon. &copy; 2007-2009 Ceon\" align=\"right\" style=\"margin: 1em 0.2em;\"/><br />Module &copy; 2007-2009 Ceon<p style=\"display: none\">'
		WHERE
			configuration_key = 'MODULE_ADVANCED_SHIPPER_MADE_BY_CEON';";
			
	$update_module_copyright_info_result = $db->Execute($update_module_copyright_info_sql);
	
	// Add the new total up price inc tax column to the region configs table
	$add_column_sql = "
		ALTER TABLE
			" . TABLE_ADVANCED_SHIPPER_REGION_CONFIGS . "
		ADD
			`total_up_price_inc_tax` TINYINT NOT NULL DEFAULT '2'
		AFTER
			`rate_limits_inc`;";
	
	$add_column_result = $db->Execute($add_column_sql);
	
} else if (MODULE_ADVANCED_SHIPPER_MADE_BY_CEON == '3.6.0') {
	// Update the surcharge column of the region configs table /////////////////////////////////////
	advshipper_update_surcharge_column();
	
	// Must ensure that packaging weights column has the correct null setting
	$update_packaging_weights_column_sql = "
		ALTER TABLE
			" . TABLE_ADVANCED_SHIPPER_REGION_CONFIGS . "
		CHANGE
			`packaging_weights` `packaging_weights` TEXT NULL;";
	
	$update_packaging_weights_column_result = $db->Execute($update_packaging_weights_column_sql);
	
	// Add the new total up price inc tax column to the region configs table
	$add_column_sql = "
		ALTER TABLE
			" . TABLE_ADVANCED_SHIPPER_REGION_CONFIGS . "
		ADD
			`total_up_price_inc_tax` TINYINT NOT NULL DEFAULT '2'
		AFTER
			`rate_limits_inc`;";
	
	$add_column_result = $db->Execute($add_column_sql);
	
} else if (MODULE_ADVANCED_SHIPPER_MADE_BY_CEON == '3.6.1' ||
		MODULE_ADVANCED_SHIPPER_MADE_BY_CEON == '3.6.2') {
	// Update the surcharge column of the region configs table /////////////////////////////////////
	advshipper_update_surcharge_column();
	
	// Add the new total up price inc tax column to the region configs table
	$add_column_sql = "
		ALTER TABLE
			" . TABLE_ADVANCED_SHIPPER_REGION_CONFIGS . "
		ADD
			`total_up_price_inc_tax` TINYINT NOT NULL DEFAULT '2'
		AFTER
			`rate_limits_inc`;";
	
	$add_column_result = $db->Execute($add_column_sql);

} else if (substr(MODULE_ADVANCED_SHIPPER_MADE_BY_CEON, 0, 3) == '3.6') {
	// Update the surcharge column of the region configs table /////////////////////////////////////
	advshipper_update_surcharge_column();
	
} else if (substr(MODULE_ADVANCED_SHIPPER_MADE_BY_CEON, 0, 3) == '3.8') {
	// Up to date!
	
} else {
	// Check if the database should be upgraded from 2.0.x to 2.2.6 first //////////////////////////
	$columns_exist_query = 'SHOW COLUMNS FROM ' . TABLE_ADVANCED_SHIPPER_REGION_CONFIGS . ';';
	$columns_exist_result = $db->Execute($columns_exist_query);
	
	$columns = array();
	while (!$columns_exist_result->EOF) {
		$columns[] = $columns_exist_result->fields['Field'];
		$columns_exist_result->MoveNext();
	}
	
	if (!in_array('countries_zones', $columns)) {
		$add_column_sql = "
			ALTER TABLE
				" . TABLE_ADVANCED_SHIPPER_REGION_CONFIGS . "
			ADD
				`countries_zones` TEXT NULL
			AFTER
				`countries_postcodes`;";
		
		$add_column_result = $db->Execute($add_column_sql);
	}
	
	
	// Update the format of all the tables of rates ////////////////////////////////////////////////
	$load_method_config_sql = "
		SELECT
			asmc.method,
			asmc.calc_method
		FROM
			" . TABLE_ADVANCED_SHIPPER_METHOD_CONFIGS . " asmc
		WHERE
			asmc.config_id = '" . $config_id . "'
		ORDER BY
			asmc.method;";
	
	$load_method_config_result = $db->Execute($load_method_config_sql);
	
	while (!$load_method_config_result->EOF) {
		
		$method = $load_method_config_result->fields['method'];
		$calc_method = $load_method_config_result->fields['calc_method'];
		
		switch ($calc_method) {
			case 1:
				$calc_method_string = ADVSHIPPER_CALC_METHOD_WEIGHT;
				break;
			case 2:
				$calc_method_string = ADVSHIPPER_CALC_METHOD_PRICE;
				break;
			case 3:
				$calc_method_string = ADVSHIPPER_CALC_METHOD_NUM_ITEMS;
				break;
		}
		
		$load_region_config_sql = "
			SELECT
				asrc.region,
				asrc.table_of_rates
			FROM
				" . TABLE_ADVANCED_SHIPPER_REGION_CONFIGS . " asrc
			WHERE
				asrc.config_id = '" . $config_id . "'
			AND
				asrc.method = '" . $method . "'
			ORDER BY
				asrc.region;";
		
		$load_region_config_result = $db->Execute($load_region_config_sql);
		
		while (!$load_region_config_result->EOF) {
			
			$region = $load_region_config_result->fields['region'];
			$table_of_rates = $load_region_config_result->fields['table_of_rates'];
			
			// Wrap table of rates with calculation method tags
			$table_of_rates = '<' . $calc_method_string . '>' . $table_of_rates . '</' .
				$calc_method_string . '>';
			
			$update_table_of_rates_sql = "
				UPDATE
					" . TABLE_ADVANCED_SHIPPER_REGION_CONFIGS . "
				SET
					table_of_rates = '" . $table_of_rates . "'
				WHERE
					config_id = '" . $config_id . "'
				AND
					method = '" . $method . "'
				AND
					region = '" . $region . "';";
			
			$update_table_of_rates_result = $db->Execute($update_table_of_rates_sql);
			
			$load_region_config_result->MoveNext();
		}
		
		$load_method_config_result->MoveNext();
	}
	
	
	// Remove deprecated calculation method column /////////////////////////////////////////////////
	$remove_calc_method_column_sql = "
		ALTER TABLE
			" . TABLE_ADVANCED_SHIPPER_METHOD_CONFIGS . "
		DROP
			calc_method";
	
	$remove_calc_method_column_result = $db->Execute($remove_calc_method_column_sql);
	
	
	
	// Add region admin titles table ///////////////////////////////////////////////////////////////
	$add_region_admin_titles_table_sql = "
		CREATE TABLE
			`" . TABLE_ADVANCED_SHIPPER_REGION_ADMIN_TITLES . "`
			(
			`config_id` INT UNSIGNED NOT NULL,
			`method` INT UNSIGNED NOT NULL,
			`region` INT UNSIGNED NOT NULL,
			`language_id` INT UNSIGNED NOT NULL,
			`title` TEXT DEFAULT NULL,
			PRIMARY KEY ( 
				`config_id`, 
				`method`,
				`region`,
				`language_id` 
			)
		);";
	
	$add_region_admin_titles_table_result = $db->Execute($add_region_admin_titles_table_sql);
	
	
	// Copy existing region titles to region admin titles table ////////////////////////////////////
	$load_region_titles_sql = "
		SELECT
			asrt.config_id,
			asrt.method,
			asrt.region,
			asrt.language_id,
			asrt.title
		FROM
			" . TABLE_ADVANCED_SHIPPER_REGION_TITLES . " asrt;";
	
	$load_region_titles_result = $db->Execute($load_region_titles_sql);
	
	while (!$load_region_titles_result->EOF) {
	
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
				'" . $load_region_titles_result->fields['config_id'] . "',
				'" . $load_region_titles_result->fields['method'] . "',
				'" . $load_region_titles_result->fields['region'] . "',
				'" . $load_region_titles_result->fields['language_id'] . "',
				'" . $db->prepare_input($load_region_titles_result->fields['title']) . "'
				);";
		
		$insert_region_admin_title_result = $db->Execute($insert_region_admin_title_sql);
		
		$load_region_titles_result->MoveNext();
	}
	
	
	// Add the UPS config table ////////////////////////////////////////////////////////////////////
	$add_region_ups_configs_table_sql = "
		CREATE TABLE IF NOT EXISTS
			`" . TABLE_ADVANCED_SHIPPER_REGION_UPS_CONFIGS . "`
			(
			`config_id` INT UNSIGNED NOT NULL,
			`method` INT UNSIGNED NOT NULL,
			`region` INT UNSIGNED NOT NULL,
			`source_country` INT UNSIGNED NOT NULL,
			`source_postcode` VARCHAR(15) NOT NULL,
			`pickup_method` VARCHAR(4) NOT NULL,
			`packaging` VARCHAR(4) NOT NULL,
			`delivery_type` VARCHAR(3) NOT NULL,
			`shipping_service_1dm` TINYINT NOT NULL DEFAULT '1',
			`shipping_service_1dml` TINYINT NOT NULL DEFAULT '1',
			`shipping_service_1da` TINYINT NOT NULL DEFAULT '1',
			`shipping_service_1dal` TINYINT NOT NULL DEFAULT '1',
			`shipping_service_1dapi` TINYINT NOT NULL DEFAULT '1',
			`shipping_service_1dp` TINYINT NOT NULL DEFAULT '1',
			`shipping_service_1dpl` TINYINT NOT NULL DEFAULT '1',
			`shipping_service_2dm` TINYINT NOT NULL DEFAULT '1',
			`shipping_service_2dml` TINYINT NOT NULL DEFAULT '1',
			`shipping_service_2da` TINYINT NOT NULL DEFAULT '1',
			`shipping_service_2dal` TINYINT NOT NULL DEFAULT '1',
			`shipping_service_3ds` TINYINT NOT NULL DEFAULT '1',
			`shipping_service_gnd` TINYINT NOT NULL DEFAULT '1',
			`shipping_service_std` TINYINT NOT NULL DEFAULT '1',
			`shipping_service_xpr` TINYINT NOT NULL DEFAULT '1',
			`shipping_service_xprl` TINYINT NOT NULL DEFAULT '1',
			`shipping_service_xdm` TINYINT NOT NULL DEFAULT '1',
			`shipping_service_xdml` TINYINT NOT NULL DEFAULT '1',
			`shipping_service_xpd` TINYINT NOT NULL DEFAULT '1',
			`shipping_service_wxs` TINYINT NOT NULL DEFAULT '1',
			PRIMARY KEY ( 
				`config_id`,
				`method`,
				`region`
			)
		);";
	
	$add_region_ups_configs_table_result = $db->Execute($add_region_ups_configs_table_sql);
	
	
	// Add the USPS config table ///////////////////////////////////////////////////////////////////
	$add_region_usps_configs_table_sql = "
		CREATE TABLE IF NOT EXISTS
			`" . TABLE_ADVANCED_SHIPPER_REGION_USPS_CONFIGS . "`
			(
			`config_id` INT UNSIGNED NOT NULL,
			`method` INT UNSIGNED NOT NULL,
			`region` INT UNSIGNED NOT NULL,
			`user_id` VARCHAR(20) NOT NULL,
			`server` VARCHAR(1) NOT NULL DEFAULT 't',
			`source_country` INT UNSIGNED NOT NULL,
			`source_postcode` VARCHAR(15) NOT NULL,
			`machinable` TINYINT NOT NULL DEFAULT '1',
			`display_transit_time` TINYINT NOT NULL DEFAULT '1',
			`domestic_express` TINYINT NOT NULL DEFAULT '1',
			`domestic_priority` TINYINT NOT NULL DEFAULT '1',
			`domestic_first_class` TINYINT NOT NULL DEFAULT '1',
			`domestic_parcel` TINYINT NOT NULL DEFAULT '1',
			`domestic_media` TINYINT NOT NULL DEFAULT '1',
			`domestic_bpm` TINYINT NOT NULL DEFAULT '1',
			`domestic_library` TINYINT NOT NULL DEFAULT '1',
			`international_ge` TINYINT NOT NULL DEFAULT '1',
			`international_gendr` TINYINT NOT NULL DEFAULT '1',
			`international_gendnr` TINYINT NOT NULL DEFAULT '1',
			`international_emi` TINYINT NOT NULL DEFAULT '1',
			`international_emifre` TINYINT NOT NULL DEFAULT '1',
			`international_pmi` TINYINT NOT NULL DEFAULT '1',
			`international_pmifre` TINYINT NOT NULL DEFAULT '1',
			`international_pmifrb` TINYINT NOT NULL DEFAULT '1',
			`international_fcmile` TINYINT NOT NULL DEFAULT '1',
			`international_fcmip` TINYINT NOT NULL DEFAULT '1',
			`international_fcmil` TINYINT NOT NULL DEFAULT '1',
			`international_fcmif` TINYINT NOT NULL DEFAULT '1',
			`international_fcmipar` TINYINT NOT NULL DEFAULT '1',
			PRIMARY KEY ( 
				`config_id`,
				`method`,
				`region`
			)
		);";
	
	$add_region_usps_configs_table_result = $db->Execute($add_region_usps_configs_table_sql);
	
	
	// Update the handling fee column of the region configs table //////////////////////////////////
	$update_handling_fee_column_sql = "
		ALTER TABLE
			" . TABLE_ADVANCED_SHIPPER_REGION_CONFIGS . "
		CHANGE
			`handling_fee` `surcharge` TEXT NULL;";
	
	$update_handling_fee_column_result = $db->Execute($update_handling_fee_column_sql);
	
	
	// Remove the show rate calculations column of the method configs table ////////////////////////
	$drop_show_rate_calc_column_sql = "
		ALTER TABLE
			" . TABLE_ADVANCED_SHIPPER_METHOD_CONFIGS . "
		DROP 
			`show_rate_calc`;";
	
	$drop_show_rate_calc_column_result = $db->Execute($drop_show_rate_calc_column_sql);
	
	
	// Add region surcharge titles table ///////////////////////////////////////////////////////////
	$add_region_surcharge_titles_table_sql = "
		CREATE TABLE
			`" . TABLE_ADVANCED_SHIPPER_REGION_SURCHARGE_TITLES . "`
			(
			`config_id` INT UNSIGNED NOT NULL,
			`method` INT UNSIGNED NOT NULL,
			`region` INT UNSIGNED NOT NULL,
			`language_id` INT UNSIGNED NOT NULL,
			`title` TEXT DEFAULT NULL,
			PRIMARY KEY ( 
				`config_id`, 
				`method`,
				`region`,
				`language_id` 
			)
		);";
	
	$add_region_surcharge_titles_table_result = $db->Execute($add_region_surcharge_titles_table_sql);
	
	
	// Create default surcharge titles /////////////////////////////////////////////////////////////
	$load_regions_sql = "
		SELECT
			asrt.config_id,
			asrt.method,
			asrt.region,
			asrt.language_id,
			asrt.title
		FROM
			" . TABLE_ADVANCED_SHIPPER_REGION_TITLES . " asrt;";
	
	$load_regions_result = $db->Execute($load_regions_sql);
	
	while (!$load_regions_result->EOF) {
	
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
				'" . $load_regions_result->fields['config_id'] . "',
				'" . $load_regions_result->fields['method'] . "',
				'" . $load_regions_result->fields['region'] . "',
				'" . $load_regions_result->fields['language_id'] . "',
				NULL
				);";
		
		$insert_region_surcharge_title_result = $db->Execute($insert_region_surcharge_title_sql);
		
		$load_regions_result->MoveNext();
	}
	
	
	// Add option for method sorting ///////////////////////////////////////////////////////////////
	$add_method_sorting_setting_sql = "
		INSERT INTO
			" . TABLE_CONFIGURATION . "
			(
			configuration_title,
			configuration_key,
			configuration_value,
			configuration_description,
			configuration_group_id,
			sort_order,
			set_function,
			date_added
			)
		VALUES
			(
			'Method Sorting',
			'MODULE_ADVANCED_SHIPPER_METHOD_SORT_ORDER',
			'Admin method order',
			'For non-dated methods, how should the methods be sorted - according to the order they have been set up in the admin or according to their total cost?',
			'6',
			'0',
			'zen_cfg_select_option(array(\'Admin method order\', \'Cost - lowest to highest\', \'Cost - highest to lowest\'), ',
			now()
			);";
	
	$add_method_sorting_setting_result = $db->Execute($add_method_sorting_setting_sql);
	
	// Add the Manufacturer Selections table ///////////////////////////////////////////////////////
	$add_method_manufacturers_table_sql = "
		CREATE TABLE IF NOT EXISTS
			`" . TABLE_ADVANCED_SHIPPER_METHOD_MANUFACTURERS . "`
		(
			`config_id` INT UNSIGNED NOT NULL,
			`method` INT UNSIGNED NOT NULL,
			`manufacturer_order` INT UNSIGNED NOT NULL,
			`manufacturer_id` INT UNSIGNED NOT NULL,
			PRIMARY KEY ( 
				`config_id`,
				`method`,
				`manufacturer_order`,
				`manufacturer_id`
			)
		);";
	
	$add_method_manufacturers_table_result = $db->Execute($add_method_manufacturers_table_sql);
	
	// Add the new maximum weight per package column to the region configs table
	$add_column_sql = "
		ALTER TABLE
			" . TABLE_ADVANCED_SHIPPER_REGION_CONFIGS . "
		ADD
			`max_weight_per_package` FLOAT UNSIGNED NULL
		AFTER
			`table_of_rates`;";
	
	$add_column_result = $db->Execute($add_column_sql);
	
	// Add the new packaging weights column to the region configs table
	$add_column_sql = "
		ALTER TABLE
			" . TABLE_ADVANCED_SHIPPER_REGION_CONFIGS . "
		ADD
			`packaging_weights` TEXT NULL
		AFTER
			`max_weight_per_package`;";
	
	$add_column_result = $db->Execute($add_column_sql);
	
	$update_module_copyright_info_sql = "
		UPDATE
			" . TABLE_CONFIGURATION . "
		SET
			configuration_title = '</b></fieldset><img src=\"" . DIR_WS_ADMIN . "/images/ceon_button_logo.png\" alt=\"Made by Ceon. &copy; 2007-2009 Ceon\" align=\"right\" style=\"margin: 1em 0.2em;\"/><br />Module &copy; 2007-2009 Ceon<p style=\"display: none\">'
		WHERE
			configuration_key = 'MODULE_ADVANCED_SHIPPER_MADE_BY_CEON';";
			
	$update_module_copyright_info_result = $db->Execute($update_module_copyright_info_sql);
	
	// Add the new total up price inc tax column to the region configs table
	$add_column_sql = "
		ALTER TABLE
			" . TABLE_ADVANCED_SHIPPER_REGION_CONFIGS . "
		ADD
			`total_up_price_inc_tax` TINYINT NOT NULL DEFAULT '2'
		AFTER
			`rate_limits_inc`;";
	
	$add_column_result = $db->Execute($add_column_sql);
}


// Update version number of database tables/////////////////////////////////////////////////////////
$update_db_version_number_sql = "
	UPDATE
		" . TABLE_CONFIGURATION . "
	SET
		configuration_value = '" . MODULE_ADVANCED_SHIPPER_VERSION_NO . "'
	WHERE
		configuration_key = 'MODULE_ADVANCED_SHIPPER_MADE_BY_CEON';";

$update_db_version_number_result = $db->Execute($update_db_version_number_sql);

// Finished!
$advshipper_upgraded = true;

?>