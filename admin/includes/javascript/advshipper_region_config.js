<?php

/**
 * @author     Conor Kerr <zen-cart.advshipper@dev.ceon.net>
 * @copyright  Copyright 2007-2009 Ceon
 * @copyright  Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright  Portions Copyright 2003 osCommerce
 * @link       http://dev.ceon.net/web/zen-cart/advshipper
 * @version    $Id: advshipper_region_config.js 382 2009-06-22 18:49:29Z Bob $
 */

?>

var num_regions = window.opener.regions.length;
var region = <?php echo $region_num; ?>;
var update_region = <?php echo ($update_region ? 'true' : 'false'); ?>;

if (region == '-1') {
	region = num_regions;
}

var submitting_form = false;

/**
 * Submit form if enter pressed.. set variable to prevent other buttons from processing their
 * onclick actions in firefox
 */
function advshipperCheckEnterPressed(e)
{
	characterCode = e.keyCode
	
	if (characterCode == 13) {
		submitting_form = true;
		advshipperSaveRegion(region, update_region);
		return true;
	}
}

function advshipperInitRegionConfig()
{
	// Get the info for the selected region
	if (region == num_regions || !update_region) {
		return;
	}
	
	var current_region_options = window.opener.regions[region];
	
	var num_region_titles = current_region_options.admin_titles.length;
	
	for (title_i = 0; title_i < num_region_titles; title_i++)
	{
		var language_id = current_region_options.admin_titles[title_i][0];
		var region_admin_title = current_region_options.admin_titles[title_i][1];
		
		var _language_el = document.getElementById('region_admin_title_' + language_id);
		_language_el.value = region_admin_title;
	}
	
	for (title_i = 0; title_i < num_region_titles; title_i++)
	{
		var language_id = current_region_options.titles[title_i][0];
		var region_title = current_region_options.titles[title_i][1];
		
		var _language_el = document.getElementById('region_title_' + language_id);
		_language_el.value = region_title;
	}
	
	if (current_region_options.definition_method == <?php echo ADVSHIPPER_DEFINITION_METHOD_ADDRESS_MATCHING; ?>) {
		document.getElementById('definition_method_address_matching').checked = true;
		
		var _countries_postcodes_el = document.getElementById('countries_postcodes');
		current_region_options.countries_postcodes =
			current_region_options.countries_postcodes.replace(/,([^\s]{1})/g, ', $1');
		current_region_options.countries_postcodes =
			current_region_options.countries_postcodes.replace(/  /g, ' ');
		_countries_postcodes_el.value = current_region_options.countries_postcodes;
		
		var _countries_zones_el = document.getElementById('countries_zones');
		if (_countries_zones_el != undefined) {
			advshipperBuildZoneList(current_region_options.countries_zones);
		}
		
		var _countries_states_el = document.getElementById('countries_states');
		if (_countries_states_el != undefined) {
			advshipperBuildStateList(current_region_options.countries_states);
		}
		
		var _countries_cities_el = document.getElementById('countries_cities');
		if (_countries_cities_el != undefined) {
			advshipperBuildCityList(current_region_options.countries_cities);
		}
	} else {
		document.getElementById('definition_method_geolocation').checked = true;
		
		var _distance_el = document.getElementById('distance');
		_distance_el.value = current_region_options.distance;
		
		advshipperDefinitionMethodSelected(<?php echo ADVSHIPPER_DEFINITION_METHOD_GEOLOCATION; ?>);
	}
	
	var _tax_class_el = document.getElementById('tax_class');
	
	var num_tax_classes = _tax_class_el.options.length;
	
	for (tax_class_i = 0; tax_class_i < num_tax_classes; tax_class_i++) {
		if (_tax_class_el.options[tax_class_i].value == current_region_options.tax_class) {
			_tax_class_el.selectedIndex = tax_class_i;
			break;
		}
	}
	
	if (current_region_options.rates_include_tax == <?php echo ADVSHIPPER_RATES_INC_TAX_INC; ?>) {
		var _rates_include_tax_inc_el = document.getElementById('rates_include_tax_inc');
		_rates_include_tax_inc_el.checked = true;
	} else {
		var _rates_include_tax_exc_el = document.getElementById('rates_include_tax_exc');
		_rates_include_tax_exc_el.checked = true;
	}
	
	if (current_region_options.rate_limits_inc == <?php echo ADVSHIPPER_RATE_LIMITS_INC_INC; ?>) {
		var _rate_limits_inc_inc_el = document.getElementById('rate_limits_inc_inc');
		_rate_limits_inc_inc_el.checked = true;
	} else {
		var _rate_limits_inc_exc_el = document.getElementById('rate_limits_inc_exc');
		_rate_limits_inc_exc_el.checked = true;
	}
	
	if (current_region_options.total_up_price_inc_tax == <?php echo ADVSHIPPER_TOTAL_UP_PRICE_INC_TAX_INC; ?>) {
		var _total_up_price_inc_tax_inc_el = document.getElementById('total_up_price_inc_tax_inc');
		_total_up_price_inc_tax_inc_el.checked = true;
	} else {
		var _total_up_price_inc_tax_exc_el = document.getElementById('total_up_price_inc_tax_exc');
		_total_up_price_inc_tax_exc_el.checked = true;
	}
	
	var _table_of_rates_el = document.getElementById('table_of_rates');
	current_region_options.table_of_rates =
		current_region_options.table_of_rates.replace(/,([^\s]{1})/g, ', $1');
	current_region_options.table_of_rates =
		current_region_options.table_of_rates.replace(/  /g, ' ');
	_table_of_rates_el.value = current_region_options.table_of_rates;
	
	var _max_weight_per_package_el = document.getElementById('max_weight_per_package');
	_max_weight_per_package_el.value = current_region_options.max_weight_per_package;
	
	var _packaging_weights_el = document.getElementById('packaging_weights');
	current_region_options.packaging_weights =
		current_region_options.packaging_weights.replace(/,([^\s]{1})/g, ', $1');
	_packaging_weights_el.value = current_region_options.packaging_weights;
	
	var _surcharge_el = document.getElementById('surcharge');
	_surcharge_el.value = current_region_options.surcharge;
	
	for (title_i = 0; title_i < num_region_titles; title_i++)
	{
		var language_id = current_region_options.surcharge_titles[title_i][0];
		var surcharge_title = current_region_options.surcharge_titles[title_i][1];
		
		var _language_el = document.getElementById('surcharge_title_' + language_id);
		_language_el.value = surcharge_title;
	}
	
	
	// Handle UPS settings
	if (current_region_options.ups_calc_string != null) {
		var ups_calc_settings = current_region_options.ups_calc_string.split('|');
	} else {
		var ups_calc_settings = new Array();
	}
	if (ups_calc_settings.length > 0) {
		_ups_calculator_enabled_enabled_el = document.getElementById('ups_calculator_enabled_enabled');
		_ups_calculator_enabled_enabled_el.checked = true;
		
		_ups_source_country_el = document.getElementById('ups_source_country');
		for (i = 0, n = _ups_source_country_el.options.length; i < n; i++) {
			if (_ups_source_country_el.options[i].value == ups_calc_settings[0]) {
				_ups_source_country_el.selectedIndex = i;
				break;
			}
		}
		
		_ups_source_postcode_el = document.getElementById('ups_source_postcode');
		_ups_source_postcode_el.value = ups_calc_settings[1];
		
		_ups_pickup_method_el = document.getElementById('ups_pickup_method');
		_ups_pickup_method_el.value = ups_calc_settings[2];
		
		_ups_packaging_el = document.getElementById('ups_packaging');
		_ups_packaging_el.value = ups_calc_settings[3];
		
		_ups_delivery_type_el = document.getElementById('ups_delivery_type');
		_ups_delivery_type_el.value = ups_calc_settings[4];
		
		_ups_shipping_service_1dm_el = document.getElementById('ups_shipping_service_1dm');
		if (ups_calc_settings[5] == 1) {
			_ups_shipping_service_1dm_el.checked = true;
		} else {
			_ups_shipping_service_1dm_el.checked = false;
		}
		
		_ups_shipping_service_1dml_el = document.getElementById('ups_shipping_service_1dml');
		if (ups_calc_settings[6] == 1) {
			_ups_shipping_service_1dml_el.checked = true;
		} else {
			_ups_shipping_service_1dml_el.checked = false;
		}
		
		_ups_shipping_service_1da_el = document.getElementById('ups_shipping_service_1da');
		if (ups_calc_settings[7] == 1) {
			_ups_shipping_service_1da_el.checked = true;
		} else {
			_ups_shipping_service_1da_el.checked = false;
		}
		
		_ups_shipping_service_1dal_el = document.getElementById('ups_shipping_service_1dal');
		if (ups_calc_settings[8] == 1) {
			_ups_shipping_service_1dal_el.checked = true;
		} else {
			_ups_shipping_service_1dal_el.checked = false;
		}
		
		_ups_shipping_service_1dapi_el = document.getElementById('ups_shipping_service_1dapi');
		if (ups_calc_settings[9] == 1) {
			_ups_shipping_service_1dapi_el.checked = true;
		} else {
			_ups_shipping_service_1dapi_el.checked = false;
		}
		
		_ups_shipping_service_1dp_el = document.getElementById('ups_shipping_service_1dp');
		if (ups_calc_settings[10] == 1) {
			_ups_shipping_service_1dp_el.checked = true;
		} else {
			_ups_shipping_service_1dp_el.checked = false;
		}
		
		_ups_shipping_service_1dpl_el = document.getElementById('ups_shipping_service_1dpl');
		if (ups_calc_settings[11] == 1) {
			_ups_shipping_service_1dpl_el.checked = true;
		} else {
			_ups_shipping_service_1dpl_el.checked = false;
		}
		
		_ups_shipping_service_2dm_el = document.getElementById('ups_shipping_service_2dm');
		if (ups_calc_settings[12] == 1) {
			_ups_shipping_service_2dm_el.checked = true;
		} else {
			_ups_shipping_service_2dm_el.checked = false;
		}
		
		_ups_shipping_service_2dml_el = document.getElementById('ups_shipping_service_2dml');
		if (ups_calc_settings[13] == 1) {
			_ups_shipping_service_2dml_el.checked = true;
		} else {
			_ups_shipping_service_2dml_el.checked = false;
		}
		
		_ups_shipping_service_2da_el = document.getElementById('ups_shipping_service_2da');
		if (ups_calc_settings[14] == 1) {
			_ups_shipping_service_2da_el.checked = true;
		} else {
			_ups_shipping_service_2da_el.checked = false;
		}
		
		_ups_shipping_service_2dal_el = document.getElementById('ups_shipping_service_2dal');
		if (ups_calc_settings[15] == 1) {
			_ups_shipping_service_2dal_el.checked = true;
		} else {
			_ups_shipping_service_2dal_el.checked = false;
		}
		
		_ups_shipping_service_3ds_el = document.getElementById('ups_shipping_service_3ds');
		if (ups_calc_settings[16] == 1) {
			_ups_shipping_service_3ds_el.checked = true;
		} else {
			_ups_shipping_service_3ds_el.checked = false;
		}
		
		_ups_shipping_service_gnd_el = document.getElementById('ups_shipping_service_gnd');
		if (ups_calc_settings[17] == 1) {
			_ups_shipping_service_gnd_el.checked = true;
		} else {
			_ups_shipping_service_gnd_el.checked = false;
		}
		
		_ups_shipping_service_std_el = document.getElementById('ups_shipping_service_std');
		if (ups_calc_settings[18] == 1) {
			_ups_shipping_service_std_el.checked = true;
		} else {
			_ups_shipping_service_std_el.checked = false;
		}
		
		_ups_shipping_service_xpr_el = document.getElementById('ups_shipping_service_xpr');
		if (ups_calc_settings[19] == 1) {
			_ups_shipping_service_xpr_el.checked = true;
		} else {
			_ups_shipping_service_xpr_el.checked = false;
		}
		
		_ups_shipping_service_xprl_el = document.getElementById('ups_shipping_service_xprl');
		if (ups_calc_settings[20] == 1) {
			_ups_shipping_service_xprl_el.checked = true;
		} else {
			_ups_shipping_service_xprl_el.checked = false;
		}
		
		_ups_shipping_service_xdm_el = document.getElementById('ups_shipping_service_xdm');
		if (ups_calc_settings[21] == 1) {
			_ups_shipping_service_xdm_el.checked = true;
		} else {
			_ups_shipping_service_xdm_el.checked = false;
		}
		
		_ups_shipping_service_xdml_el = document.getElementById('ups_shipping_service_xdml');
		if (ups_calc_settings[22] == 1) {
			_ups_shipping_service_xdml_el.checked = true;
		} else {
			_ups_shipping_service_xdml_el.checked = false;
		}
		
		_ups_shipping_service_xpd_el = document.getElementById('ups_shipping_service_xpd');
		if (ups_calc_settings[23] == 1) {
			_ups_shipping_service_xpd_el.checked = true;
		} else {
			_ups_shipping_service_xpd_el.checked = false;
		}
		
		_ups_shipping_service_wxs_el = document.getElementById('ups_shipping_service_wxs');
		if (ups_calc_settings[24] == 1) {
			_ups_shipping_service_wxs_el.checked = true;
		} else {
			_ups_shipping_service_wxs_el.checked = false;
		}
		
		advshipperUPSCalculatorSelection('enabled');
	}
	
	
	// Handle USPS settings
	if (current_region_options.usps_calc_string != null) {
		var usps_calc_settings = current_region_options.usps_calc_string.split('|');
	} else {
		var usps_calc_settings = new Array();
	}
	if (usps_calc_settings.length > 0) {
		_usps_calculator_enabled_enabled_el = document.getElementById('usps_calculator_enabled_enabled');
		_usps_calculator_enabled_enabled_el.checked = true;
		
		_usps_user_id_el = document.getElementById('usps_user_id');
		_usps_user_id_el.value = usps_calc_settings[0];
		
		_usps_server_test_el = document.getElementById('usps_server_test');
		_usps_server_production_el = document.getElementById('usps_server_production');
		if (usps_calc_settings[1] == 't') {
			_usps_server_test_el.checked = true;
			_usps_server_production_el.checked = false;
		} else {
			_usps_server_test_el.checked = false;
			_usps_server_production_el.checked = true;
		}
		
		_usps_source_country_el = document.getElementById('usps_source_country');
		for (i = 0, n = _usps_source_country_el.options.length; i < n; i++) {
			if (_usps_source_country_el.options[i].value == usps_calc_settings[2]) {
				_usps_source_country_el.selectedIndex = i;
				break;
			}
		}
		
		_usps_source_postcode_el = document.getElementById('usps_source_postcode');
		_usps_source_postcode_el.value = usps_calc_settings[3];
		
		_usps_machinable_true_el = document.getElementById('usps_machinable_true');
		_usps_machinable_false_el = document.getElementById('usps_machinable_false');
		if (usps_calc_settings[4] == 1) {
			_usps_machinable_true_el.checked = true;
			_usps_machinable_false_el.checked = false;
		} else {
			_usps_machinable_true_el.checked = false;
			_usps_machinable_false_el.checked = true;
		}
		
		_usps_display_transit_time_true_el = document.getElementById('usps_display_transit_time_true');
		_usps_display_transit_time_false_el = document.getElementById('usps_display_transit_time_false');
		if (usps_calc_settings[5] == 1) {
			_usps_display_transit_time_true_el.checked = true;
			_usps_display_transit_time_false_el.checked = false;
		} else {
			_usps_display_transit_time_true_el.checked = false;
			_usps_display_transit_time_false_el.checked = true;
		}
		
		_usps_domestic_express_el = document.getElementById('usps_domestic_express');
		if (usps_calc_settings[6] == 1) {
			_usps_domestic_express_el.checked = true;
		} else {
			_usps_domestic_express_el.checked = false;
		}
		
		_usps_domestic_priority_el = document.getElementById('usps_domestic_priority');
		if (usps_calc_settings[7] == 1) {
			_usps_domestic_priority_el.checked = true;
		} else {
			_usps_domestic_priority_el.checked = false;
		}
		
		_usps_domestic_first_class_el = document.getElementById('usps_domestic_first_class');
		if (usps_calc_settings[8] == 1) {
			_usps_domestic_first_class_el.checked = true;
		} else {
			_usps_domestic_first_class_el.checked = false;
		}
		
		_usps_domestic_parcel_el = document.getElementById('usps_domestic_parcel');
		if (usps_calc_settings[9] == 1) {
			_usps_domestic_parcel_el.checked = true;
		} else {
			_usps_domestic_parcel_el.checked = false;
		}
		
		_usps_domestic_media_el = document.getElementById('usps_domestic_media');
		if (usps_calc_settings[10] == 1) {
			_usps_domestic_media_el.checked = true;
		} else {
			_usps_domestic_media_el.checked = false;
		}
		
		_usps_domestic_bpm_el = document.getElementById('usps_domestic_bpm');
		if (usps_calc_settings[11] == 1) {
			_usps_domestic_bpm_el.checked = true;
		} else {
			_usps_domestic_bpm_el.checked = false;
		}
		
		_usps_domestic_library_el = document.getElementById('usps_domestic_library');
		if (usps_calc_settings[12] == 1) {
			_usps_domestic_library_el.checked = true;
		} else {
			_usps_domestic_library_el.checked = false;
		}
		
		_usps_international_ge_el = document.getElementById('usps_international_ge');
		if (usps_calc_settings[13] == 1) {
			_usps_international_ge_el.checked = true;
		} else {
			_usps_international_ge_el.checked = false;
		}
		
		_usps_international_gendr_el = document.getElementById('usps_international_gendr');
		if (usps_calc_settings[14] == 1) {
			_usps_international_gendr_el.checked = true;
		} else {
			_usps_international_gendr_el.checked = false;
		}
		
		_usps_international_gendnr_el = document.getElementById('usps_international_gendnr');
		if (usps_calc_settings[15] == 1) {
			_usps_international_gendnr_el.checked = true;
		} else {
			_usps_international_gendnr_el.checked = false;
		}
		
		_usps_international_emi_el = document.getElementById('usps_international_emi');
		if (usps_calc_settings[16] == 1) {
			_usps_international_emi_el.checked = true;
		} else {
			_usps_international_emi_el.checked = false;
		}
		
		_usps_international_emifre_el = document.getElementById('usps_international_emifre');
		if (usps_calc_settings[17] == 1) {
			_usps_international_emifre_el.checked = true;
		} else {
			_usps_international_emifre_el.checked = false;
		}
		
		_usps_international_pmi_el = document.getElementById('usps_international_pmi');
		if (usps_calc_settings[18] == 1) {
			_usps_international_pmi_el.checked = true;
		} else {
			_usps_international_pmi_el.checked = false;
		}
		
		_usps_international_pmifre_el = document.getElementById('usps_international_pmifre');
		if (usps_calc_settings[19] == 1) {
			_usps_international_pmifre_el.checked = true;
		} else {
			_usps_international_pmifre_el.checked = false;
		}
		
		_usps_international_pmifrb_el = document.getElementById('usps_international_pmifrb');
		if (usps_calc_settings[20] == 1) {
			_usps_international_pmifrb_el.checked = true;
		} else {
			_usps_international_pmifrb_el.checked = false;
		}
		
		_usps_international_fcmile_el = document.getElementById('usps_international_fcmile');
		if (usps_calc_settings[21] == 1) {
			_usps_international_fcmile_el.checked = true;
		} else {
			_usps_international_fcmile_el.checked = false;
		}
		
		_usps_international_fcmip_el = document.getElementById('usps_international_fcmip');
		if (usps_calc_settings[22] == 1) {
			_usps_international_fcmip_el.checked = true;
		} else {
			_usps_international_fcmip_el.checked = false;
		}
		
		_usps_international_fcmil_el = document.getElementById('usps_international_fcmil');
		if (usps_calc_settings[23] == 1) {
			_usps_international_fcmil_el.checked = true;
		} else {
			_usps_international_fcmil_el.checked = false;
		}
		
		_usps_international_fcmif_el = document.getElementById('usps_international_fcmif');
		if (usps_calc_settings[24] == 1) {
			_usps_international_fcmif_el.checked = true;
		} else {
			_usps_international_fcmif_el.checked = false;
		}
		
		_usps_international_fcmipar_el = document.getElementById('usps_international_fcmipar');
		if (usps_calc_settings[25] == 1) {
			_usps_international_fcmipar_el.checked = true;
		} else {
			_usps_international_fcmipar_el.checked = false;
		}
		
		advshipperUSPSCalculatorSelection('enabled');
	}
}


function advshipperDefinitionMethodSelected(value)
{
	address_matching_panel_el = window.document.getElementById('address_matching_panel');
	geolocation_panel_el = window.document.getElementById('geolocation_panel');
	
	if (value == <?php echo ADVSHIPPER_DEFINITION_METHOD_ADDRESS_MATCHING; ?>)
	{
		geolocation_panel_el.style.display = 'none';
		address_matching_panel_el.style.display = '';
	} else {
		geolocation_panel_el.style.display = '';
		address_matching_panel_el.style.display = 'none';
	}
}


function advshipperUPSCalculatorSelection(value)
{
	ups_source_country_header_el = window.document.getElementById('ups_source_country_header');
	ups_source_country_field_el = window.document.getElementById('ups_source_country_field');
	
	ups_source_postcode_header_el = window.document.getElementById('ups_source_postcode_header');
	ups_source_postcode_field_el = window.document.getElementById('ups_source_postcode_field');
	
	ups_pickup_method_header_el = window.document.getElementById('ups_pickup_method_header');
	ups_pickup_method_field_el = window.document.getElementById('ups_pickup_method_field');
	
	ups_packaging_header_el = window.document.getElementById('ups_packaging_header');
	ups_packaging_field_el = window.document.getElementById('ups_packaging_field');
	
	ups_delivery_type_header_el = window.document.getElementById('ups_delivery_type_header');
	ups_delivery_type_field_el = window.document.getElementById('ups_delivery_type_field');
	
	ups_shipping_services_header_el = window.document.getElementById('ups_shipping_services_header');
	ups_shipping_services_field_el = window.document.getElementById('ups_shipping_services_field');
	
	if (value == 'enabled')
	{
		ups_source_country_header_el.style.display = '';
		ups_source_country_field_el.style.display = '';
		
		ups_source_postcode_header_el.style.display = '';
		ups_source_postcode_field_el.style.display = '';
		
		ups_pickup_method_header_el.style.display = '';
		ups_pickup_method_field_el.style.display = '';
		
		ups_packaging_header_el.style.display = '';
		ups_packaging_field_el.style.display = '';
		
		ups_delivery_type_header_el.style.display = '';
		ups_delivery_type_field_el.style.display = '';
		
		ups_shipping_services_header_el.style.display = '';
		ups_shipping_services_field_el.style.display = '';
	} else {
		ups_source_country_header_el.style.display = 'none';
		ups_source_country_field_el.style.display = 'none';
		
		ups_source_postcode_header_el.style.display = 'none';
		ups_source_postcode_field_el.style.display = 'none';
		
		ups_pickup_method_header_el.style.display = 'none';
		ups_pickup_method_field_el.style.display = 'none';
		
		ups_packaging_header_el.style.display = 'none';
		ups_packaging_field_el.style.display = 'none';
		
		ups_delivery_type_header_el.style.display = 'none';
		ups_delivery_type_field_el.style.display = 'none';
		
		ups_shipping_services_header_el.style.display = 'none';
		ups_shipping_services_field_el.style.display = 'none';
	}
}


function advshipperUSPSCalculatorSelection(value)
{
	usps_user_id_header_el = window.document.getElementById('usps_user_id_header');
	usps_user_id_field_el = window.document.getElementById('usps_user_id_field');
	
	usps_server_header_el = window.document.getElementById('usps_server_header');
	usps_server_field_el = window.document.getElementById('usps_server_field');
	
	usps_source_country_header_el = window.document.getElementById('usps_source_country_header');
	usps_source_country_field_el = window.document.getElementById('usps_source_country_field');
	
	usps_source_postcode_header_el = window.document.getElementById('usps_source_postcode_header');
	usps_source_postcode_field_el = window.document.getElementById('usps_source_postcode_field');
	
	usps_machinable_header_el = window.document.getElementById('usps_machinable_header');
	usps_machinable_field_el = window.document.getElementById('usps_machinable_field');
	
	usps_display_transit_time_header_el = window.document.getElementById('usps_display_transit_time_header');
	usps_display_transit_time_field_el = window.document.getElementById('usps_display_transit_time_field');
	
	usps_domestic_services_header_el = window.document.getElementById('usps_domestic_services_header');
	usps_domestic_services_field_el = window.document.getElementById('usps_domestic_services_field');
	
	usps_international_services_header_el = window.document.getElementById('usps_international_services_header');
	usps_international_services_field_el = window.document.getElementById('usps_international_services_field');
	
	if (value == 'enabled')
	{
		usps_user_id_header_el.style.display = '';
		usps_user_id_field_el.style.display = '';
		
		usps_server_header_el.style.display = '';
		usps_server_field_el.style.display = '';
		
		usps_source_country_header_el.style.display = '';
		usps_source_country_field_el.style.display = '';
		
		usps_source_postcode_header_el.style.display = '';
		usps_source_postcode_field_el.style.display = '';
		
		usps_machinable_header_el.style.display = '';
		usps_machinable_field_el.style.display = '';
		
		usps_display_transit_time_header_el.style.display = '';
		usps_display_transit_time_field_el.style.display = '';
		
		usps_domestic_services_header_el.style.display = '';
		usps_domestic_services_field_el.style.display = '';
		
		usps_international_services_header_el.style.display = '';
		usps_international_services_field_el.style.display = '';
	} else {
		usps_user_id_header_el.style.display = 'none';
		usps_user_id_field_el.style.display = 'none';
		
		usps_server_header_el.style.display = 'none';
		usps_server_field_el.style.display = 'none';
		
		usps_source_country_header_el.style.display = 'none';
		usps_source_country_field_el.style.display = 'none';
		
		usps_source_postcode_header_el.style.display = 'none';
		usps_source_postcode_field_el.style.display = 'none';
		
		usps_machinable_header_el.style.display = 'none';
		usps_machinable_field_el.style.display = 'none';
		
		usps_display_transit_time_header_el.style.display = 'none';
		usps_display_transit_time_field_el.style.display = 'none';
		
		usps_domestic_services_header_el.style.display = 'none';
		usps_domestic_services_field_el.style.display = 'none';
		
		usps_international_services_header_el.style.display = 'none';
		usps_international_services_field_el.style.display = 'none';
	}
}

/**
 * Gets the values for the configuration and sends them to method config region management.
 */
function advshipperSaveRegion(region_num, update_region)
{
	var _admin_titles_string = null;
	var _titles_string = null;
	var _definition_method = null;
	var _countries_postcodes = null;
	var _countries_zones_string = null;
	var _countries_states_string = null;
	var _countries_cities_string = null;
	var _distance = null;
	var _tax_class = null;
	var _rates_include_tax = null;
	var _rate_limits_inc = null;
	var _total_up_price_inc_tax = null;
	var _table_of_rates = null;
	var _max_weight_per_package = null;
	var _packaging_weights = null;
	var _surcharge = null;
	var _surcharge_titles_string = null;
	
	var _ups_calc_string = null;
	var _usps_calc_string = null;
	
	// Get the titles entered for each language and encode for passing back (IE fails when passing
	// back arrays - argh! >:( )
	var _admin_titles = new Array();
	var _titles = new Array();
	var _surcharge_titles = new Array();
	for (i = 0; i < language_ids.length; i++) {
		var _region_admin_title_el = document.getElementById('region_admin_title_' + language_ids[i]);
		
		var current_region_admin_title = _region_admin_title_el.value;
		
		// Remove any illegal characters from the text for the title
		if (current_region_admin_title.length > 0) {
			current_region_admin_title = current_region_admin_title.replace(/\(\(\)\)/g, '');
			current_region_admin_title = current_region_admin_title.replace(/\[\[\]\]/g, '');
			current_region_admin_title = current_region_admin_title.replace(/\|/g, ' ');
			// Remove leading/trailing whitespace
			current_region_admin_title = current_region_admin_title.replace(/^\s+|\s+$/g, '');
		}
		
		_admin_titles[i] = language_ids[i] + '|' + current_region_admin_title;
		
		
		var _region_title_el = document.getElementById('region_title_' + language_ids[i]);
		
		var current_region_title = _region_title_el.value;
		
		// Remove any illegal characters from the text for the title
		if (current_region_title.length > 0) {
			current_region_title = current_region_title.replace(/\(\(\)\)/g, '');
			current_region_title = current_region_title.replace(/\[\[\]\]/g, '');
			current_region_title = current_region_title.replace(/\|/g, ' ');
			// Remove leading/trailing whitespace
			current_region_title = current_region_title.replace(/^\s+|\s+$/g, '');
		}
		
		_titles[i] = language_ids[i] + '|' + current_region_title;
		
		
		var _surcharge_title_el = document.getElementById('surcharge_title_' + language_ids[i]);
		
		var current_surcharge_title = _surcharge_title_el.value;
		
		// Remove any illegal characters from the text for the title
		if (current_surcharge_title.length > 0) {
			current_surcharge_title = current_surcharge_title.replace(/\(\(\)\)/g, '');
			current_surcharge_title = current_surcharge_title.replace(/\[\[\]\]/g, '');
			current_surcharge_title = current_surcharge_title.replace(/\|/g, ' ');
		}
		
		_surcharge_titles[i] = language_ids[i] + '|' + current_surcharge_title;
	}
	
	_admin_titles_string = _admin_titles.join('||');
	_titles_string = _titles.join('||');
	_surcharge_titles_string = _surcharge_titles.join('||');

	
	var _definition_method_address_matching_el = document.getElementById('definition_method_address_matching');
	
	if (_definition_method_address_matching_el.checked) {
		_definition_method = <?php echo ADVSHIPPER_DEFINITION_METHOD_ADDRESS_MATCHING; ?>;
	} else {
		_definition_method = <?php echo ADVSHIPPER_DEFINITION_METHOD_GEOLOCATION; ?>;
	}
	
	
	if (_definition_method == <?php echo ADVSHIPPER_DEFINITION_METHOD_ADDRESS_MATCHING; ?>) {
		_countries_postcodes_el = document.getElementById('countries_postcodes');
		_countries_postcodes = _countries_postcodes_el.value;
		
		_countries_zones_el = document.getElementById('countries_zones');
		if (_countries_zones_el != undefined) {
			// Encode zones array due to IE bug
			_num_countries_zones = zones.length;
			
			if (_num_countries_zones > 0) {
				_countries_zones_info = new Array();
				
				for (zone_i = 0; zone_i < _num_countries_zones; zone_i++) {
					_countries_zones_info[zone_i] = zones[zone_i].zone_id + '|' +
						zones[zone_i].name.replace(/\|/g, '');
				}
				
				_countries_zones_string = _countries_zones_info.join('||');
			}
		}
		
		_countries_states_el = document.getElementById('countries_states');
		if (_countries_states_el != undefined) {
			// Encode states array due to IE bug
			_num_countries_states = states.length;
			
			if (_num_countries_states > 0) {
				_countries_states_info = new Array();
				
				for (state_i = 0; state_i < _num_countries_states; state_i++) {
					_countries_states_info[state_i] = states[state_i].locality_id + '|' +
						states[state_i].name.replace(/\|/g, '');
				}
				
				_countries_states_string = _countries_states_info.join('||');
			}
		}
		
		_countries_cities_el = document.getElementById('countries_cities');
		if (_countries_cities_el != undefined) {
			// Encode cities array due to IE bug
			_num_countries_cities = cities.length;
			
			if (_num_countries_cities > 0) {
				_countries_cities_info = new Array();
				
				for (city_i = 0; city_i < _num_countries_cities; city_i++) {
					_countries_cities_info[city_i] = cities[city_i].locality_id + '|' +
						cities[city_i].name.replace(/\|/g, '');
				}
				
				_countries_cities_string = _countries_cities_info.join('||');
			}
		}
	} else {
		_distance_el = document.getElementById('distance');
		_distance = _distance_el.value;
	}
	
	
	var _tax_class_el = document.getElementById('tax_class');
	
	_tax_class = _tax_class_el.options[_tax_class_el.selectedIndex].value;
	
	
	var _rates_include_tax_inc_el = document.getElementById('rates_include_tax_inc');
	
	if (_rates_include_tax_inc_el.checked) {
		_rates_include_tax = <?php echo ADVSHIPPER_RATES_INC_TAX_INC; ?>;
	} else {
		_rates_include_tax = <?php echo ADVSHIPPER_RATES_INC_TAX_EXC; ?>;
	}
	
	
	var _rate_limits_inc_inc_el = document.getElementById('rate_limits_inc_inc');
	
	if (_rate_limits_inc_inc_el.checked) {
		_rate_limits_inc = <?php echo ADVSHIPPER_RATE_LIMITS_INC_INC; ?>;
	} else {
		_rate_limits_inc = <?php echo ADVSHIPPER_RATE_LIMITS_INC_EXC; ?>;
	}
	
	
	var _total_up_price_inc_tax_inc_el = document.getElementById('total_up_price_inc_tax_inc');
	
	if (_total_up_price_inc_tax_inc_el.checked) {
		_total_up_price_inc_tax = <?php echo ADVSHIPPER_TOTAL_UP_PRICE_INC_TAX_INC; ?>;
	} else {
		_total_up_price_inc_tax = <?php echo ADVSHIPPER_TOTAL_UP_PRICE_INC_TAX_EXC; ?>;
	}
	
	
	_table_of_rates_el = document.getElementById('table_of_rates');
	_table_of_rates = _table_of_rates_el.value;
	_table_of_rates = _table_of_rates.replace(/\(\(\)\)/g, '');
	_table_of_rates = _table_of_rates.replace(/\[\[\]\]/g, '');
	
	_max_weight_per_package_el = document.getElementById('max_weight_per_package');
	_max_weight_per_package = _max_weight_per_package_el.value;
	_max_weight_per_package = _max_weight_per_package.replace(/\s/g, '');
	_max_weight_per_package = _max_weight_per_package.replace(/\(\(\)\)/g, '');
	_max_weight_per_package = _max_weight_per_package.replace(/\[\[\]\]/g, '');
	
	_packaging_weights_el = document.getElementById('packaging_weights');
	_packaging_weights = _packaging_weights_el.value;
	_packaging_weights = _packaging_weights.replace(/\s/g, '');
	_packaging_weights = _packaging_weights.replace(/\(\(\)\)/g, '');
	_packaging_weights = _packaging_weights.replace(/\[\[\]\]/g, '');
	
	_surcharge_el = document.getElementById('surcharge');
	_surcharge = _surcharge_el.value;
	_surcharge = _surcharge.replace(/\s/g, '');
	_surcharge = _surcharge.replace(/\(\(\)\)/g, '');
	_surcharge = _surcharge.replace(/\[\[\]\]/g, '');
	
	// Handle UPS settings
	_ups_calculator_enabled_enabled_el = document.getElementById('ups_calculator_enabled_enabled');
	if (_ups_calculator_enabled_enabled_el.checked) {
		// Using UPS settings!
		_ups_source_country_el = document.getElementById('ups_source_country');
		_ups_calc_string = _ups_source_country_el.options[_ups_source_country_el.selectedIndex].value;
		
		_ups_source_postcode_el = document.getElementById('ups_source_postcode');
		_ups_calc_string += '|' + _ups_source_postcode_el.value.replace(/\|/g, '');
		
		_ups_pickup_method_el = document.getElementById('ups_pickup_method');
		_ups_calc_string += '|' + _ups_pickup_method_el.value.replace(/\|/g, '');
		
		_ups_packaging_el = document.getElementById('ups_packaging');
		_ups_calc_string += '|' + _ups_packaging_el.value.replace(/\|/g, '');
		
		_ups_delivery_type_el = document.getElementById('ups_delivery_type');
		_ups_calc_string += '|' + _ups_delivery_type_el.value.replace(/\|/g, '');
		
		_ups_shipping_service_1dm_el = document.getElementById('ups_shipping_service_1dm');
		_ups_calc_string += '|' + (_ups_shipping_service_1dm_el.checked ? 1 : 0);
		
		_ups_shipping_service_1dml_el = document.getElementById('ups_shipping_service_1dml');
		_ups_calc_string += '|' + (_ups_shipping_service_1dml_el.checked ? 1 : 0);
		
		_ups_shipping_service_1da_el = document.getElementById('ups_shipping_service_1da');
		_ups_calc_string += '|' + (_ups_shipping_service_1da_el.checked ? 1 : 0);
		
		_ups_shipping_service_1dal_el = document.getElementById('ups_shipping_service_1dal');
		_ups_calc_string += '|' + (_ups_shipping_service_1dal_el.checked ? 1 : 0);
		
		_ups_shipping_service_1dapi_el = document.getElementById('ups_shipping_service_1dapi');
		_ups_calc_string += '|' + (_ups_shipping_service_1dapi_el.checked ? 1 : 0);
		
		_ups_shipping_service_1dp_el = document.getElementById('ups_shipping_service_1dp');
		_ups_calc_string += '|' + (_ups_shipping_service_1dp_el.checked ? 1 : 0);
		
		_ups_shipping_service_1dpl_el = document.getElementById('ups_shipping_service_1dpl');
		_ups_calc_string += '|' + (_ups_shipping_service_1dpl_el.checked ? 1 : 0);
		
		_ups_shipping_service_2dm_el = document.getElementById('ups_shipping_service_2dm');
		_ups_calc_string += '|' + (_ups_shipping_service_2dm_el.checked ? 1 : 0);
		
		_ups_shipping_service_2dml_el = document.getElementById('ups_shipping_service_2dml');
		_ups_calc_string += '|' + (_ups_shipping_service_2dml_el.checked ? 1 : 0);
		
		_ups_shipping_service_2da_el = document.getElementById('ups_shipping_service_2da');
		_ups_calc_string += '|' + (_ups_shipping_service_2da_el.checked ? 1 : 0);
		
		_ups_shipping_service_2dal_el = document.getElementById('ups_shipping_service_2dal');
		_ups_calc_string += '|' + (_ups_shipping_service_2dal_el.checked ? 1 : 0);
		
		_ups_shipping_service_3ds_el = document.getElementById('ups_shipping_service_3ds');
		_ups_calc_string += '|' + (_ups_shipping_service_3ds_el.checked ? 1 : 0);
		
		_ups_shipping_service_gnd_el = document.getElementById('ups_shipping_service_gnd');
		_ups_calc_string += '|' + (_ups_shipping_service_gnd_el.checked ? 1 : 0);
		
		_ups_shipping_service_std_el = document.getElementById('ups_shipping_service_std');
		_ups_calc_string += '|' + (_ups_shipping_service_std_el.checked ? 1 : 0);
		
		_ups_shipping_service_xpr_el = document.getElementById('ups_shipping_service_xpr');
		_ups_calc_string += '|' + (_ups_shipping_service_xpr_el.checked ? 1 : 0);
		
		_ups_shipping_service_xprl_el = document.getElementById('ups_shipping_service_xprl');
		_ups_calc_string += '|' + (_ups_shipping_service_xprl_el.checked ? 1 : 0);
		
		_ups_shipping_service_xdm_el = document.getElementById('ups_shipping_service_xdm');
		_ups_calc_string += '|' + (_ups_shipping_service_xdm_el.checked ? 1 : 0);
		
		_ups_shipping_service_xdml_el = document.getElementById('ups_shipping_service_xdml');
		_ups_calc_string += '|' + (_ups_shipping_service_xdml_el.checked ? 1 : 0);
		
		_ups_shipping_service_xpd_el = document.getElementById('ups_shipping_service_xpd');
		_ups_calc_string += '|' + (_ups_shipping_service_xpd_el.checked ? 1 : 0);
		
		_ups_shipping_service_wxs_el = document.getElementById('ups_shipping_service_wxs');
		_ups_calc_string += '|' + (_ups_shipping_service_wxs_el.checked ? 1 : 0);
	}
	
	// Handle USPS settings
	_usps_calculator_enabled_enabled_el = document.getElementById('usps_calculator_enabled_enabled');
	if (_usps_calculator_enabled_enabled_el.checked) {
		// Using USPS settings!
		_usps_user_id_el = document.getElementById('usps_user_id');
		_usps_calc_string = _usps_user_id_el.value.replace(/\|/g, '');
		
		_usps_server_test_el = document.getElementById('usps_server_test');
		_usps_calc_string += '|' + (_usps_server_test_el.checked ? 't' : 'p');
		
		_usps_source_country_el = document.getElementById('usps_source_country');
		_usps_calc_string += '|' + _usps_source_country_el.options[_usps_source_country_el.selectedIndex].value;
		
		_usps_source_postcode_el = document.getElementById('usps_source_postcode');
		_usps_calc_string += '|' + _usps_source_postcode_el.value.replace(/\|/g, '');
		
		_usps_machinable_true_el = document.getElementById('usps_machinable_true');
		_usps_calc_string += '|' + (_usps_machinable_true_el.checked ? 1 : 0);
		
		_usps_display_transit_time_true_el = document.getElementById('usps_display_transit_time_true');
		_usps_calc_string += '|' + (_usps_display_transit_time_true_el.checked ? 1 : 0);
		
		_usps_domestic_express_el = document.getElementById('usps_domestic_express');
		_usps_calc_string += '|' + (_usps_domestic_express_el.checked ? 1 : 0);
		
		_usps_domestic_priority_el = document.getElementById('usps_domestic_priority');
		_usps_calc_string += '|' + (_usps_domestic_priority_el.checked ? 1 : 0);
		
		_usps_domestic_first_class_el = document.getElementById('usps_domestic_first_class');
		_usps_calc_string += '|' + (_usps_domestic_first_class_el.checked ? 1 : 0);
		
		_usps_domestic_parcel_el = document.getElementById('usps_domestic_parcel');
		_usps_calc_string += '|' + (_usps_domestic_parcel_el.checked ? 1 : 0);
		
		_usps_domestic_media_el = document.getElementById('usps_domestic_media');
		_usps_calc_string += '|' + (_usps_domestic_media_el.checked ? 1 : 0);
		
		_usps_domestic_bpm_el = document.getElementById('usps_domestic_bpm');
		_usps_calc_string += '|' + (_usps_domestic_bpm_el.checked ? 1 : 0);
		
		_usps_domestic_library_el = document.getElementById('usps_domestic_library');
		_usps_calc_string += '|' + (_usps_domestic_library_el.checked ? 1 : 0);
		
		_usps_international_ge_el = document.getElementById('usps_international_ge');
		_usps_calc_string += '|' + (_usps_international_ge_el.checked ? 1 : 0);
		
		_usps_international_gendr_el = document.getElementById('usps_international_gendr');
		_usps_calc_string += '|' + (_usps_international_gendr_el.checked ? 1 : 0);
		
		_usps_international_gendnr_el = document.getElementById('usps_international_gendnr');
		_usps_calc_string += '|' + (_usps_international_gendnr_el.checked ? 1 : 0);
		
		_usps_international_emi_el = document.getElementById('usps_international_emi');
		_usps_calc_string += '|' + (_usps_international_emi_el.checked ? 1 : 0);
		
		_usps_international_emifre_el = document.getElementById('usps_international_emifre');
		_usps_calc_string += '|' + (_usps_international_emifre_el.checked ? 1 : 0);
		
		_usps_international_pmi_el = document.getElementById('usps_international_pmi');
		_usps_calc_string += '|' + (_usps_international_pmi_el.checked ? 1 : 0);
		
		_usps_international_pmifre_el = document.getElementById('usps_international_pmifre');
		_usps_calc_string += '|' + (_usps_international_pmifre_el.checked ? 1 : 0);
		
		_usps_international_pmifrb_el = document.getElementById('usps_international_pmifrb');
		_usps_calc_string += '|' + (_usps_international_pmifrb_el.checked ? 1 : 0);
		
		_usps_international_fcmile_el = document.getElementById('usps_international_fcmile');
		_usps_calc_string += '|' + (_usps_international_fcmile_el.checked ? 1 : 0);
		
		_usps_international_fcmip_el = document.getElementById('usps_international_fcmip');
		_usps_calc_string += '|' + (_usps_international_fcmip_el.checked ? 1 : 0);
		
		_usps_international_fcmil_el = document.getElementById('usps_international_fcmil');
		_usps_calc_string += '|' + (_usps_international_fcmil_el.checked ? 1 : 0);
		
		_usps_international_fcmif_el = document.getElementById('usps_international_fcmif');
		_usps_calc_string += '|' + (_usps_international_fcmif_el.checked ? 1 : 0);
		
		_usps_international_fcmipar_el = document.getElementById('usps_international_fcmipar');
		_usps_calc_string += '|' + (_usps_international_fcmipar_el.checked ? 1 : 0);
	}
	
	// Validate data entered
	if (!advshipperCheckRegionConfiguration(_definition_method, _countries_postcodes, _countries_zones_string, _countries_states_string, _countries_cities_string, _distance, _table_of_rates, _surcharge)) {
		return false;
	}
	
	if (!update_region) {
		window.opener.advshipperInsertRegion(region_num, _admin_titles_string, _titles_string, _definition_method, _countries_postcodes, _countries_zones_string, _countries_states_string, _countries_cities_string, _distance, _tax_class, _rates_include_tax, _rate_limits_inc, _total_up_price_inc_tax, _table_of_rates, _max_weight_per_package, _packaging_weights, _surcharge, _surcharge_titles_string, _ups_calc_string, _usps_calc_string);
	} else {
		window.opener.advshipperUpdateRegion(region_num, _admin_titles_string, _titles_string, _definition_method, _countries_postcodes, _countries_zones_string, _countries_states_string, _countries_cities_string, _distance, _tax_class, _rates_include_tax, _rate_limits_inc, _total_up_price_inc_tax, _table_of_rates, _max_weight_per_package, _packaging_weights, _surcharge, _surcharge_titles_string, _ups_calc_string, _usps_calc_string);
	}
	
	window.opener.advshipperRebuildRegionsPanel();
	
	window.close();
	
	return true;
}

/**
 * Valdiates the region's configuration.
 *
 * @returns {boolean} Whether configuration is valid or not.
 */
function advshipperCheckRegionConfiguration(definition_method, countries_postcodes, countries_zones_string, countries_states_string, countries_cities_string, distance, table_of_rates, surcharge)
{
	var errors = '';
	
	if (countries_postcodes != null) {
		countries_postcodes = countries_postcodes.replace(/\s/g, '');
	}
	if (countries_states_string != null) {
		countries_states_string = countries_states_string.replace(/\s/g, '');
	}
	if (countries_cities_string != null) {
		countries_cities_string = countries_cities_string.replace(/\s/g, '');
	}
	if (distance != null) {
		distance = distance.replace(/\s/g, '');
	}
	if (table_of_rates != null) {
		table_of_rates = table_of_rates.replace(/\s/g, '');
	}
	
	if (definition_method == <?php echo ADVSHIPPER_DEFINITION_METHOD_ADDRESS_MATCHING; ?>) {
		if ((countries_postcodes == null || countries_postcodes.length == 0) &&
			(countries_zones_string == null || countries_zones_string.length == 0) &&
			(countries_states_string == null || countries_states_string.length == 0) &&
			(countries_cities_string == null || countries_cities_string.length == 0)) {
			errors += '<?php echo ADVSHIPPER_JS_ERROR_NO_ADDRESS_DEFINED; ?>';
		}
	} else {
		if (distance == null || distance.length == 0) {
			errors += '<?php echo ADVSHIPPER_JS_ERROR_DISTANCE_NOT_SPECIFIED; ?>';
		}
	}
	
	if (table_of_rates == null || table_of_rates.length == 0) {
		errors += '<?php echo ADVSHIPPER_JS_ERROR_TABLE_OF_RATES_NOT_SPECIFIED; ?>';
	} else {
		if (!table_of_rates.match(/^\s*\<(<?php echo ADVSHIPPER_CALC_METHOD_WEIGHT; ?>|<?php echo ADVSHIPPER_CALC_METHOD_PRICE; ?>|<?php echo ADVSHIPPER_CALC_METHOD_NUM_ITEMS; ?>|<?php echo ADVSHIPPER_CALC_METHOD_TOTAL_ORDER_PRICE; ?>|<?php echo ADVSHIPPER_CALC_METHOD_SHIPPING_RATE; ?>)\>.*\<\/\1\>\s*$/)) {			errors += '<?php echo ADVSHIPPER_JS_ERROR_TABLE_OF_RATES_FORMAT; ?>';
		}
		
		/*var rates = table_of_rates.split(',');
		var num_rates = rates.length;
		
		for (var rate_i = 0; rate_i < num_rates; rate_i++) {
			var limit_rate_divider_pos = rates[rate_i].indexOf(':');
			
			if (limit_rate_divider_pos == -1) {
				errors += '<?php echo ADVSHIPPER_JS_ERROR_TABLE_OF_RATES_FORMAT; ?>';
				break;
			} else {
				var limit_string = rates[rate_i].substr(0, limit_rate_divider_pos);
				var rate_string = rates[rate_i].substr((limit_rate_divider_pos + 1),
					rates[rate_i].length - limit_rate_divider_pos);
				
				if (!limit_string.match(/^(([0-9]+(\.[0-9]+)?)\-)?(([0-9]+(\.[0-9]+)?)|\*)$/)) {
					errors += '<?php echo ADVSHIPPER_JS_ERROR_TABLE_OF_RATES_LIMITS_FORMAT; ?>' +
						'"' + limit_string + '"\n';
					break;
				}
			}
		}*/
	}
	
	if (surcharge != null && surcharge.length != 0 &&
			!surcharge.match(/^[0-9]+(\.[0-9]+)?$/) &&
			(!surcharge.match(/^\s*\<(<?php echo ADVSHIPPER_CALC_METHOD_WEIGHT; ?>|<?php echo ADVSHIPPER_CALC_METHOD_PRICE; ?>|<?php echo ADVSHIPPER_CALC_METHOD_NUM_ITEMS; ?>|<?php echo ADVSHIPPER_CALC_METHOD_TOTAL_ORDER_PRICE; ?>|<?php echo ADVSHIPPER_CALC_METHOD_SHIPPING_RATE; ?>|<?php echo ADVSHIPPER_CALC_METHOD_NUM_PACKAGES; ?>)\>.+\<\/\1\>\s*$/) ||
			surcharge.match(/[^\<\>\/a-z0-9\.%\+\s,:\(\)\-\]\[\*]/))) {
		errors += '<?php echo ADVSHIPPER_JS_ERROR_SURCHARGE_FORMAT; ?>' +
			'"' + surcharge + '"\n';
	}
	
	if (errors != '') {
		alert(errors);
		return false;
	}
	
	return true;
}

function advshipperCancelRegionConfig()
{
	window.close();
}
