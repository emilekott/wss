<?php
/**
 * @package    admin
 * @author     Conor Kerr <zen-cart.advshipper@dev.ceon.net>
 * @copyright  Copyright 2007-2009 Ceon
 * @copyright  Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright  Portions Copyright 2003 osCommerce
 * @link       http://dev.ceon.net/web/zen-cart/advshipper
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: advshipper_region_config.php 382 2009-06-22 18:49:29Z Bob $
 */

require('includes/application_top.php');

require(DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies();

$languages = zen_get_languages();

require_once(DIR_FS_ADMIN . DIR_WS_FUNCTIONS . 'advshipper.php');


$method_num = isset($_GET['method']) ? (int) $_GET['method'] : null;

$region_num = isset($_GET['region']) ? (int) $_GET['region'] : null;

$update_region = isset($_GET['update_region']) ? ($_GET['update_region'] == 'true' ? true : false) : false;

/**
 * Variables hold values for the region
 */
$region_admin_titles = array();

$region_titles = array();
	
$definition_method = ADVSHIPPER_DEFINITION_METHOD_ADDRESS_MATCHING;

$countries_postcodes = null;

$countries_cities = null;

$countries_states = null;

$distance = null;

$tax_class = null;

$rates_include_tax = ADVSHIPPER_RATES_INC_TAX_INC;

$rate_limits_inc = ADVSHIPPER_RATE_LIMITS_INC_INC;

$total_up_price_inc_tax = ADVSHIPPER_TOTAL_UP_PRICE_INC_TAX_EXC;

$table_of_rates = null;

$max_weight_per_package = null;

$packaging_weights = null;

$surcharge = null;

$surcharge_titles = array();


$ups_calculator_enabled = false;

$ups_source_country = SHIPPING_ORIGIN_COUNTRY;

$ups_source_postcode = SHIPPING_ORIGIN_ZIP;

$ups_pickup_method = 'CC';

$ups_packaging = 'CP';

$ups_delivery_type = 'RES';

$ups_shipping_service_1dm = 1;
$ups_shipping_service_1dml = 1;
$ups_shipping_service_1da = 1;
$ups_shipping_service_1dal = 1;
$ups_shipping_service_1dapi = 1;
$ups_shipping_service_1dp = 1;
$ups_shipping_service_1dpl = 1;
$ups_shipping_service_2dm = 1;
$ups_shipping_service_2dml = 1;
$ups_shipping_service_2da = 1;
$ups_shipping_service_2dal = 1;
$ups_shipping_service_3ds = 1;
$ups_shipping_service_gnd = 1;
$ups_shipping_service_std = 1;
$ups_shipping_service_xpr = 1;
$ups_shipping_service_xprl = 1;
$ups_shipping_service_xdm = 1;
$ups_shipping_service_xdml = 1;
$ups_shipping_service_xpd = 1;
$ups_shipping_service_wxs = 1;


$usps_calculator_enabled = false;

$usps_user_id = 'NONE';

$usps_server = 't';

$usps_source_country = SHIPPING_ORIGIN_COUNTRY;

$usps_source_postcode = SHIPPING_ORIGIN_ZIP;

$usps_machinable = 0;

$usps_display_transit_time = 1;

$usps_domestic_express = 1;
$usps_domestic_priority = 1;
$usps_domestic_first_class = 1;
$usps_domestic_parcel = 1;
$usps_domestic_media = 1;
$usps_domestic_bpm = 1;
$usps_domestic_library = 1;

$usps_international_ge = 1;
$usps_international_gendr = 1;
$usps_international_gendnr = 1;
$usps_international_emi = 1;
$usps_international_emifre = 1;
$usps_international_pmi = 1;
$usps_international_pmifre = 1;
$usps_international_pmifrb = 1;
$usps_international_fcmile = 1;
$usps_international_fcmip = 1;
$usps_international_fcmil = 1;
$usps_international_fcmif = 1;
$usps_international_fcmipar = 1;

?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
	<title><?php echo HEADING_TITLE; ?></title>
	<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
	<link rel="stylesheet" type="text/css" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
	<script language="javascript" src="includes/menu.js"></script>
	<script language="javascript" src="includes/general.js"></script>
	<script language="JavaScript" type="text/javascript">
	<!--
	var language_ids = new Array();
		<?php
			// Record the language IDs so the javascript functions can access these fields
			for ($language_i = 0, $n = sizeof($languages); $language_i < $n; $language_i++) {
				echo "\t" . 'language_ids[' . $language_i . '] = ' . $languages[$language_i]['id'] . ";\n";
			}
		?>
	// -->
	</script>
	<script language="javascript"  type="text/javascript">
	<!--
<?php require(DIR_WS_INCLUDES . 'javascript/advshipper_region_config.js'); ?>
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
	
	.AdvancedShipperRegionOdd {
		background-color: #d0d0d0;
	}
	.AdvancedShipperRegionEven {
		background-color: #f3f3f3;
	}
	.AdvancedShipperRegionOddConfigPanel{
		background-color: #d9d9d9;
	}
	.AdvancedShipperRegionEvenConfigPanel {
		background-color: #fafafa;
	}
	
	.AdvancedShipperConfigLabel, .AdvancedShipperConfigField, .AdvancedShipperConfigDesc,
	.AdvancedShipperConfigButtonPanel {
		vertical-align: top;
	}
	.AdvancedShipperConfigLabel { font-weight: bold; padding-right: 1em; }
	.AdvancedShipperConfigLabel { width: 22.5%; }
	.AdvancedShipperConfigField { padding-top: 0.5em; padding-bottom: 1.3em; }
	.AdvancedShipperConfigIntro { padding-top: 0.5em; padding-bottom:1.1em;  }
	.AdvancedShipperConfigButtonPanel { text-align: right; margin-bottom: 0.8em; }
	
	fieldset.AdvancedShipperAddressMatching { padding: 0.3em 0.8em; }
	fieldset.AdvancedShipperAddressMatching legend { font-size: 1em; }
	fieldset.AdvancedShipperAddressMatching p { padding: 0; margin: 0 0 0.5em 0; }
	
	.ErrorIntro { margin: 2em 0; color: #f00; }
	.FormError { font-weight: bold; color: #f00; }
	
	.Collapse { display:  none; }
	</style>
	<script language="JavaScript" src="<?php echo DIR_WS_INCLUDES . 'javascript/cba.js' ?>"></script>
</head>
<body onload="advshipperInitRegionConfig();">
<?php if (defined('ADVSHIPPER_ZONES_SUPPORT') && ADVSHIPPER_ZONES_SUPPORT == 'Yes') { ?>
<script language="javascript"  type="text/javascript">
<!--
<?php require(DIR_WS_INCLUDES . 'javascript/advshipper_region_config_zones.js'); ?>
//-->
</script>
<?php } ?>
<?php if (defined('ADVSHIPPER_LOCALITY_SUPPORT') && ADVSHIPPER_LOCALITY_SUPPORT == 'Yes') { ?>
<script language="javascript"  type="text/javascript">
<!--
<?php require(DIR_WS_INCLUDES . 'javascript/advshipper_region_config_localities.js'); ?>
//-->
</script>
<?php } ?>

<!-- body //-->
<?php echo zen_draw_form('advshipper', FILENAME_ADVANCED_SHIPPER_REGION_CONFIG, zen_get_all_get_params(), 'post', 'onSubmit="return advshipperSaveRegion(region, \'' . $update_region . '\');"', true);
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
if (sizeof($errors) == 1) {
	echo '<p class="ErrorIntro">' . TEXT_ERROR_IN_CONFIG;
} else if (sizeof($errors) > 0 ) {
	printf('<p class="ErrorIntro">' . TEXT_ERRORS_IN_CONFIG, sizeof($errors));
} else {
	echo '<p>';
}
?>
						</p>	
					</td>
				</tr>	
				<tr>
					<td>
<?php

// Build fields for the region's configuration /////////////////////////////////////////////////////
?>
<fieldset class="AdvancedShipperRegion<?php echo ((($method_num) % 2 == 0) ? 'Even' : 'Odd'); ?>">
	<legend><?php echo TEXT_REGION . ' '; ?>
<script language="JavaScript" type="text/javascript">
<!--
document.write(region + 1);
// -->
</script>
	<?php
	if (strlen($region_titles[$_SESSION['languages_id']]) > 0) {
		echo ' - &ldquo;' . htmlentities($region_titles[$_SESSION['languages_id']]) . '&rdquo;';
	}
?></legend>
	<fieldset class="AdvancedShipperRegion<?php echo ((($method_num) % 2 == 0) ? 'Even' : 'Odd'); ?>ConfigPanel">
		<legend><?php echo TEXT_REGION_TITLE; ?></legend>
		<table cellpadding="0" cellspacing="0" width="100%" border="0">
			<tr>
				<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="region_admin_titles"><?php echo TEXT_LABEL_REGION_ADMIN_TITLE; ?></label></td>
				<td class="AdvancedShipperConfigDesc">
					<?php echo TEXT_CONFIG_DESC_REGION_ADMIN_TITLE; ?>
				</td>
			</tr>
			<tr>
				<td class="AdvancedShipperConfigField">
	<?php
		for ($language_i = 0, $n = sizeof($languages); $language_i < $n; $language_i++) {
			if ($language_i != 0) {
				echo '<br />';
			}
			echo zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$language_i]['directory'] . '/images/' . $languages[$language_i]['image'], $languages[$language_i]['name']);
			echo ' ';
			echo zen_draw_input_field('region_admin_titles[' . $languages[$language_i]['id'] . ']', $region_admin_titles[$languages[$language_i]['id']], 'maxlength="255" size="45" id="region_admin_title_' . $languages[$language_i]['id'] . '" onKeyPress="advshipperCheckEnterPressed(event)"');
			echo "\n";
		}
	?>
				</td>
			</tr>
			<tr>
				<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="region_titles"><?php echo TEXT_LABEL_REGION_TITLE; ?></label></td>
				<td class="AdvancedShipperConfigDesc">
					<?php echo TEXT_CONFIG_DESC_REGION_TITLE; ?>
				</td>
			</tr>
			<tr>
				<td class="AdvancedShipperConfigField">
	<?php
		for ($language_i = 0, $n = sizeof($languages); $language_i < $n; $language_i++) {
			if ($language_i != 0) {
				echo '<br />';
			}
			echo zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$language_i]['directory'] . '/images/' . $languages[$language_i]['image'], $languages[$language_i]['name']);
			echo ' ';
			echo zen_draw_input_field('region_titles[' . $languages[$language_i]['id'] . ']', $region_titles[$languages[$language_i]['id']], 'maxlength="255" size="45" id="region_title_' . $languages[$language_i]['id'] . '" onKeyPress="advshipperCheckEnterPressed(event)"');
			echo "\n";
		}
	?>
				</td>
			</tr>
		</table>
	</fieldset>
	
	<fieldset class="AdvancedShipperRegion<?php echo ((($method_num) % 2 == 0) ? 'Even' : 'Odd'); ?>ConfigPanel">
		<legend><?php echo TEXT_DEFINITION_METHOD; ?></legend>
		<table cellpadding="0" cellspacing="0" width="100%" border="0">
			<tr>
				<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="definition_method"><?php echo TEXT_LABEL_DEFINITION_METHOD; ?></label></td>
				<td class="AdvancedShipperConfigDesc">
					<?php echo TEXT_CONFIG_DESC_DEFINITION_METHOD; ?>
				</td>
			</tr>
			<tr>
				<td class="AdvancedShipperConfigField">
					<?php echo advshipper_draw_radio_field('definition_method', ADVSHIPPER_DEFINITION_METHOD_ADDRESS_MATCHING, $definition_method == ADVSHIPPER_DEFINITION_METHOD_ADDRESS_MATCHING, null, 'id="definition_method_address_matching" onclick="advshipperDefinitionMethodSelected(' . ADVSHIPPER_DEFINITION_METHOD_ADDRESS_MATCHING . ');" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_DEFINITION_METHOD_ADDRESS_MATCHING; ?>
					<br /><?php echo advshipper_draw_radio_field('definition_method', ADVSHIPPER_DEFINITION_METHOD_GEOLOCATION, $definition_method == ADVSHIPPER_DEFINITION_METHOD_GEOLOCATION, null, 'id="definition_method_geolocation" onclick="advshipperDefinitionMethodSelected(' . ADVSHIPPER_DEFINITION_METHOD_GEOLOCATION . ');" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_DEFINITION_METHOD_GEOLOCATION; ?>
				</td>
			</tr>
		</table>
	</fieldset>
		
	<fieldset class="AdvancedShipperRegion<?php echo ((($method_num) % 2 == 0) ? 'Even' : 'Odd'); ?>ConfigPanel" <?php echo ($definition_method == ADVSHIPPER_DEFINITION_METHOD_ADDRESS_MATCHING ? '' : 'style="display: none;"'); ?> id="address_matching_panel">
		<legend><?php echo TEXT_ADDRESS_MATCHING; ?></legend>
		<table cellpadding="0" cellspacing="0" width="100%" border="0">
			<tr <?php echo ($definition_method == ADVSHIPPER_DEFINITION_METHOD_ADDRESS_MATCHING ? '' : 'style="display: none;"'); ?> id="address_matching_intro_header">
				<td>&nbsp;</td>
				<td class="AdvancedShipperConfigIntro">
				<?php if (defined('ADVSHIPPER_ZONES_SUPPORT') &&
						ADVSHIPPER_ZONES_SUPPORT == 'Yes' &&
						defined('ADVSHIPPER_LOCALITY_SUPPORT') &&
						ADVSHIPPER_LOCALITY_SUPPORT == 'Yes') {
					echo TEXT_INTRO_DEFINITION_METHOD_ADDRESS_MATCHING_LOCALITY_AND_ZONE_SUPPORT;
				} else if ((defined('ADVSHIPPER_ZONES_SUPPORT') &&
						ADVSHIPPER_ZONES_SUPPORT == 'Yes') &&
						(!defined('ADVSHIPPER_LOCALITY_SUPPORT') ||
						ADVSHIPPER_LOCALITY_SUPPORT != 'Yes')) {
					echo TEXT_INTRO_DEFINITION_METHOD_ADDRESS_MATCHING_ZONE_SUPPORT;
				} else if ((!defined('ADVSHIPPER_ZONES_SUPPORT') ||
						ADVSHIPPER_ZONES_SUPPORT != 'Yes') &&
						(defined('ADVSHIPPER_LOCALITY_SUPPORT') &&
						ADVSHIPPER_LOCALITY_SUPPORT == 'Yes')) {
					echo TEXT_INTRO_DEFINITION_METHOD_ADDRESS_MATCHING_LOCALITY_SUPPORT;
				} else {
					echo TEXT_INTRO_DEFINITION_METHOD_ADDRESS_MATCHING_NO_LOCALITY_OR_ZONE_SUPPORT;
				} ?></td>
			</tr>
			<tr <?php echo ($definition_method == ADVSHIPPER_DEFINITION_METHOD_ADDRESS_MATCHING ? '' : 'style="display: none;"'); ?> id="countries_postcodes_header">
				<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="countries_postcodes"><?php echo TEXT_LABEL_COUNTRIES_POSTCODES; ?></label></td>
				<td class="AdvancedShipperConfigDesc">
					<?php echo TEXT_CONFIG_DESC_COUNTRIES_POSTCODES; ?>
				</td>
			</tr>
			<tr <?php echo ($definition_method == ADVSHIPPER_DEFINITION_METHOD_ADDRESS_MATCHING ? '' : 'style="display: none;"'); ?> id="countries_postcodes_field">
				<td class="AdvancedShipperConfigField">
					<?php echo zen_draw_textarea_field('countries_postcodes', 'virtual', 50, 6, $countries_postcodes, 'id="countries_postcodes"'); ?>
				</td>
			</tr>
			<?php if (defined('ADVSHIPPER_ZONES_SUPPORT') && ADVSHIPPER_ZONES_SUPPORT == 'Yes') {
				require(DIR_FS_ADMIN . DIR_WS_MODULES . FILENAME_ADVANCED_SHIPPER_REGION_CONFIG_ZONE_SELECTION);
			} ?>
			<?php if (defined('ADVSHIPPER_LOCALITY_SUPPORT') && ADVSHIPPER_LOCALITY_SUPPORT == 'Yes') {
				require(DIR_FS_ADMIN . DIR_WS_MODULES . FILENAME_ADVANCED_SHIPPER_REGION_CONFIG_LOCALITY_SELECTION);
			} ?>
		</table>
	</fieldset>
	
	<fieldset class="AdvancedShipperRegion<?php echo ((($method_num) % 2 == 0) ? 'Even' : 'Odd'); ?>ConfigPanel" <?php echo ($definition_method == ADVSHIPPER_DEFINITION_METHOD_GEOLOCATION ? '' : 'style="display: none;"'); ?> id="geolocation_panel">
		<legend><?php echo TEXT_GEOLOCATION; ?></legend>
		<table cellpadding="0" cellspacing="0" width="100%" border="0">
			<tr id="distance_header">
				<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="distance"><?php echo TEXT_LABEL_DISTANCE; ?></label></td>
				<td class="AdvancedShipperConfigDesc">
					<?php echo TEXT_CONFIG_DESC_DISTANCE; ?>
				</td>
			</tr>
			<tr id="distance_field">
				<td class="AdvancedShipperConfigField">
					<?php echo zen_draw_input_field('distance', $distance, 'maxlength="6" size="6" id="distance" onKeyPress="advshipperCheckEnterPressed(event)"'); ?>
				</td>
			</tr>
		</table>
	</fieldset>
	
	<fieldset class="AdvancedShipperRegion<?php echo ((($method_num) % 2 == 0) ? 'Even' : 'Odd'); ?>ConfigPanel">
		<legend><?php echo TEXT_RATES_CONFIG; ?></legend>
		<table cellpadding="0" cellspacing="0" width="100%" border="0">
			<tr>
				<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="tax_class"><?php echo TEXT_LABEL_TAX_CLASS; ?></label></td>
				<td class="AdvancedShipperConfigDesc">
					<?php echo TEXT_CONFIG_DESC_TAX_CLASS; ?>
				</td>
			</tr>
			<tr>
				<td class="AdvancedShipperConfigField">
					<?php echo advshipper_cfg_pull_down_tax_classes('tax_class', $tax_class, 'id="tax_class"'); ?>
				</td>
			</tr>
			<tr>
				<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="rates_include_tax"><?php echo TEXT_LABEL_RATES_INCLUDE_TAX; ?></label></td>
				<td class="AdvancedShipperConfigDesc">
					<?php echo TEXT_CONFIG_DESC_RATES_INCLUDE_TAX; ?>
				</td>
			</tr>
			<tr>
				<td class="AdvancedShipperConfigField">
					<?php echo advshipper_draw_radio_field('rates_include_tax', ADVSHIPPER_RATES_INC_TAX_INC, $rates_include_tax == ADVSHIPPER_RATES_INC_TAX_INC, null, 'id="rates_include_tax_inc" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_RATES_INCLUDE_TAX; ?>
					<br /><?php echo advshipper_draw_radio_field('rates_include_tax', ADVSHIPPER_RATES_INC_TAX_EXC, $rates_include_tax == ADVSHIPPER_RATES_INC_TAX_EXC, null, 'id="rates_include_tax_exc" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_RATES_DO_NOT_INCLUDE_TAX; ?>
				</td>
			</tr>
			<tr>
				<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="rate_limits_inc"><?php echo TEXT_LABEL_RATE_LIMITS_INC; ?></label></td>
				<td class="AdvancedShipperConfigDesc">
					<?php echo TEXT_CONFIG_DESC_RATE_LIMITS_INC; ?>
				</td>
			</tr>
			<tr>
				<td class="AdvancedShipperConfigField">
					<?php echo advshipper_draw_radio_field('rate_limits_inc', ADVSHIPPER_RATE_LIMITS_INC_INC, $rate_limits_inc == ADVSHIPPER_RATE_LIMITS_INC_INC, null, 'id="rate_limits_inc_inc" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_RATE_LIMITS_INC_INC; ?>
					<br /><?php echo advshipper_draw_radio_field('rate_limits_inc', ADVSHIPPER_RATE_LIMITS_INC_EXC, $rate_limits_inc == ADVSHIPPER_RATE_LIMITS_INC_EXC, null, 'id="rate_limits_inc_exc" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_RATE_LIMITS_INC_EXC; ?>
				</td>
			</tr>
			<tr>
				<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="total_up_price_inc_tax"><?php echo TEXT_LABEL_TOTAL_UP_PRICE_INC_TAX; ?></label></td>
				<td class="AdvancedShipperConfigDesc">
					<?php echo TEXT_CONFIG_DESC_TOTAL_UP_PRICE_INC_TAX; ?>
				</td>
			</tr>
			<tr>
				<td class="AdvancedShipperConfigField">
					<?php echo advshipper_draw_radio_field('total_up_price_inc_tax', ADVSHIPPER_TOTAL_UP_PRICE_INC_TAX_INC, $total_up_price_inc_tax == ADVSHIPPER_TOTAL_UP_PRICE_INC_TAX_INC, null, 'id="total_up_price_inc_tax_inc" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_TOTAL_UP_PRICE_INC_TAX_INC; ?>
					<br /><?php echo advshipper_draw_radio_field('total_up_price_inc_tax', ADVSHIPPER_TOTAL_UP_PRICE_INC_TAX_EXC, $total_up_price_inc_tax == ADVSHIPPER_TOTAL_UP_PRICE_INC_TAX_EXC, null, 'id="total_up_price_inc_tax_exc" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_TOTAL_UP_PRICE_INC_TAX_EXC; ?>
				</td>
			</tr>
			<tr>
				<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="table_of_rates"><?php echo TEXT_LABEL_TABLE_OF_RATES; ?></label></td>
				<td class="AdvancedShipperConfigDesc">
					<?php echo TEXT_CONFIG_DESC_TABLE_OF_RATES; ?>
				</td>
			</tr>
			<tr>
				<td class="AdvancedShipperConfigField">
					<?php echo zen_draw_textarea_field('table_of_rates', 'virtual', 50, 6, $table_of_rates, 'id="table_of_rates"'); ?>
				</td>
			</tr>
			<tr>
				<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="packaging_weights"><?php echo TEXT_LABEL_MAX_WEIGHT_PER_PACKAGE; ?></label></td>
				<td class="AdvancedShipperConfigDesc">
					<?php echo TEXT_CONFIG_DESC_MAX_WEIGHT_PER_PACKAGE; ?>
				</td>
			</tr>
			<tr>
				<td class="AdvancedShipperConfigField">
					<?php echo zen_draw_input_field('max_weight_per_package', $max_weight_per_package, 'size="10" id="max_weight_per_package" onKeyPress="advshipperCheckEnterPressed(event)"'); ?>
				</td>
			</tr>
			<tr>
				<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="packaging_weights"><?php echo TEXT_LABEL_PACKAGING_WEIGHTS; ?></label></td>
				<td class="AdvancedShipperConfigDesc">
					<?php echo TEXT_CONFIG_DESC_PACKAGING_WEIGHTS; ?>
				</td>
			</tr>
			<tr>
				<td class="AdvancedShipperConfigField">
					<?php echo zen_draw_input_field('packaging_weights', $packaging_weights, 'size="45" id="packaging_weights" onKeyPress="advshipperCheckEnterPressed(event)"'); ?>
				</td>
			</tr>
			<tr>
				<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="surcharge"><?php echo TEXT_LABEL_SURCHARGE; ?></label></td>
				<td class="AdvancedShipperConfigDesc">
					<?php echo TEXT_CONFIG_DESC_SURCHARGE; ?>
				</td>
			</tr>
			<tr>
				<td class="AdvancedShipperConfigField">
					<?php echo zen_draw_textarea_field('surcharge', 'virtual', 50, 2, $surcharge, 'id="surcharge"'); ?>
				</td>
			</tr>
			<tr>
				<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="surcharge_titles"><?php echo TEXT_LABEL_SURCHARGE_TITLE; ?></label></td>
				<td class="AdvancedShipperConfigDesc">
					<?php echo TEXT_CONFIG_DESC_SURCHARGE_TITLE; ?>
				</td>
			</tr>
			<tr>
				<td class="AdvancedShipperConfigField">
	<?php
		for ($language_i = 0, $n = sizeof($languages); $language_i < $n; $language_i++) {
			if ($language_i != 0) {
				echo '<br />';
			}
			echo zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$language_i]['directory'] . '/images/' . $languages[$language_i]['image'], $languages[$language_i]['name']);
			echo ' ';
			echo zen_draw_input_field('surcharge_titles[' . $languages[$language_i]['id'] . ']', $surcharge_titles[$languages[$language_i]['id']], 'maxlength="255" size="45" id="surcharge_title_' . $languages[$language_i]['id'] . '" onKeyPress="advshipperCheckEnterPressed(event)"');
			echo "\n";
		}
	?>
				</td>
			</tr>
		</table>
	</fieldset>

	<fieldset class="AdvancedShipperRegion<?php echo
		((($method_num) % 2 == 0) ? 'Even' : 'Odd') .	'ConfigPanel'; ?>">
		<legend><?php echo TEXT_UPS_CALC_SETTINGS; ?></legend>
		<table border="0" width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td class="AdvancedShipperConfigLabel"><label for="ups_calculator_enabled"><?php echo TEXT_LABEL_UPS_ENABLED; ?></label></td>
				<td class="AdvancedShipperConfigField">
					<?php echo advshipper_draw_radio_field('ups_calculator_enabled', 'disabled', ($ups_calculator_enabled == false), null, 'onclick="advshipperUPSCalculatorSelection(\'disable\');" id="ups_calculator_enabled_disabled" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_UPS_CALC_NOT_BEING_USED; ?>
					<br /><?php echo advshipper_draw_radio_field('ups_calculator_enabled', 'enabled', ($ups_calculator_enabled == true), null, 'onclick="advshipperUPSCalculatorSelection(\'enabled\');" id="ups_calculator_enabled_enabled" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_UPS_CALC_BEING_USED; ?>
				</td>
			</tr>
			<tr id="ups_source_country_header" style="display: none;">
				<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="ups_source_country"><?php echo TEXT_LABEL_UPS_SOURCE_COUNTRY; ?></label></td>
				<td class="AdvancedShipperConfigDesc">
					<?php echo TEXT_CONFIG_DESC_UPS_SOURCE_COUNTRY; ?>
				</td>
			</tr>
			<tr id="ups_source_country_field" style="display: none;">
				<td class="AdvancedShipperConfigField">
					<?php echo $country_select = zen_draw_pull_down_menu('ups_source_country',
						zen_get_countries(),
						$ups_source_country,
						'id="ups_source_country" onKeyPress="advshipperCheckEnterPressed(event)"'
						); ?>
				</td>
			</tr>
			<tr id="ups_source_postcode_header" style="display: none;">
				<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="ups_source_postcode"><?php echo TEXT_LABEL_UPS_SOURCE_POSTCODE; ?></label></td>
				<td class="AdvancedShipperConfigDesc">
					<?php echo TEXT_CONFIG_DESC_UPS_SOURCE_POSTCODE; ?>
				</td>
			</tr>
			<tr id="ups_source_postcode_field" style="display: none;">
				<td class="AdvancedShipperConfigField">
					<?php echo zen_draw_input_field('ups_source_postcode', $ups_source_postcode, 'size="9" id="ups_source_postcode" onKeyPress="advshipperCheckEnterPressed(event)"'); ?>
				</td>
			</tr>
			<tr id="ups_pickup_method_header" style="display: none;">
				<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="ups_pickup_method"><?php echo TEXT_LABEL_UPS_PICKUP_METHOD; ?></label></td>
				<td class="AdvancedShipperConfigDesc">
					<?php echo TEXT_CONFIG_DESC_UPS_PICKUP_METHOD; ?>
				</td>
			</tr>
			<tr id="ups_pickup_method_field" style="display: none;">
				<td class="AdvancedShipperConfigField">
					<?php echo zen_draw_input_field('ups_pickup_method', $ups_pickup_method, 'maxlength="4" size="4" id="ups_pickup_method" onKeyPress="advshipperCheckEnterPressed(event)"'); ?>
				</td>
			</tr>
			<tr id="ups_packaging_header" style="display: none;">
				<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="ups_packaging"><?php echo TEXT_LABEL_UPS_PACKAGING; ?></label></td>
				<td class="AdvancedShipperConfigDesc">
					<?php echo TEXT_CONFIG_DESC_UPS_PACKAGING; ?>
				</td>
			</tr>
			<tr id="ups_packaging_field" style="display: none;">
				<td class="AdvancedShipperConfigField">
					<?php echo zen_draw_input_field('ups_packaging', $ups_packaging, 'maxlength="4" size="4" id="ups_packaging" onKeyPress="advshipperCheckEnterPressed(event)"'); ?>
				</td>
			</tr>
			<tr id="ups_delivery_type_header" style="display: none;">
				<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="ups_delivery_type"><?php echo TEXT_LABEL_UPS_DELIVERY_TYPE; ?></label></td>
				<td class="AdvancedShipperConfigDesc">
					<?php echo TEXT_CONFIG_DESC_UPS_DELIVERY_TYPE; ?>
				</td>
			</tr>
			<tr id="ups_delivery_type_field" style="display: none;">
				<td class="AdvancedShipperConfigField">
					<?php echo zen_draw_input_field('ups_delivery_type', $ups_delivery_type, 'maxlength="3" size="3" id="ups_delivery_type" onKeyPress="advshipperCheckEnterPressed(event)"'); ?>
				</td>
			</tr>
			<tr id="ups_shipping_services_header" style="display: none;">
				<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="ups_shipping_service_1dm"><?php echo TEXT_LABEL_UPS_SHIPPING_SERVICES; ?></label></td>
				<td class="AdvancedShipperConfigDesc">
					<?php echo TEXT_CONFIG_DESC_UPS_SHIPPING_SERVICES; ?>
				</td>
			</tr>
			<tr id="ups_shipping_services_field" style="display: none;">
				<td class="AdvancedShipperConfigField">
					<?php echo advshipper_draw_checkbox_field('ups_shipping_service_1dm', 1, ($ups_shipping_service_1dm == 1), null,  'id="ups_shipping_service_1dm" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_UPS_SHIPPING_SERVICE_1DM; ?>
					<br /><?php echo advshipper_draw_checkbox_field('ups_shipping_service_1dml', 1, ($ups_shipping_service_1dml == 1), null,  'id="ups_shipping_service_1dml" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_UPS_SHIPPING_SERVICE_1DML; ?>
					<br /><?php echo advshipper_draw_checkbox_field('ups_shipping_service_1da', 1, ($ups_shipping_service_1da == 1), null,  'id="ups_shipping_service_1da" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_UPS_SHIPPING_SERVICE_1DA; ?>
					<br /><?php echo advshipper_draw_checkbox_field('ups_shipping_service_1dal', 1, ($ups_shipping_service_1dal == 1), null,  'id="ups_shipping_service_1dal" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_UPS_SHIPPING_SERVICE_1DAL; ?>
					<br /><?php echo advshipper_draw_checkbox_field('ups_shipping_service_1dapi', 1, ($ups_shipping_service_1dapi == 1), null,  'id="ups_shipping_service_1dapi" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_UPS_SHIPPING_SERVICE_1DAPI; ?>
					<br /><?php echo advshipper_draw_checkbox_field('ups_shipping_service_1dp', 1, ($ups_shipping_service_1dp == 1), null,  'id="ups_shipping_service_1dp" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_UPS_SHIPPING_SERVICE_1DP; ?>
					<br /><?php echo advshipper_draw_checkbox_field('ups_shipping_service_1dpl', 1, ($ups_shipping_service_1dpl == 1), null,  'id="ups_shipping_service_1dpl" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_UPS_SHIPPING_SERVICE_1DPL; ?>
					<br /><?php echo advshipper_draw_checkbox_field('ups_shipping_service_2dm', 1, ($ups_shipping_service_2dm == 1), null,  'id="ups_shipping_service_2dm" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_UPS_SHIPPING_SERVICE_2DM; ?>
					<br /><?php echo advshipper_draw_checkbox_field('ups_shipping_service_2dml', 1, ($ups_shipping_service_2dml == 1), null,  'id="ups_shipping_service_2dml" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_UPS_SHIPPING_SERVICE_2DML; ?>
					<br /><?php echo advshipper_draw_checkbox_field('ups_shipping_service_2da', 1, ($ups_shipping_service_2da == 1), null,  'id="ups_shipping_service_2da" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_UPS_SHIPPING_SERVICE_2DA; ?>
					<br /><?php echo advshipper_draw_checkbox_field('ups_shipping_service_2dal', 1, ($ups_shipping_service_2dal == 1), null,  'id="ups_shipping_service_2dal" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_UPS_SHIPPING_SERVICE_2DAL; ?>
					<br /><?php echo advshipper_draw_checkbox_field('ups_shipping_service_3ds', 1, ($ups_shipping_service_3ds == 1), null,  'id="ups_shipping_service_3ds" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_UPS_SHIPPING_SERVICE_3DS; ?>
					<br /><?php echo advshipper_draw_checkbox_field('ups_shipping_service_gnd', 1, ($ups_shipping_service_gnd == 1), null,  'id="ups_shipping_service_gnd" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_UPS_SHIPPING_SERVICE_GND; ?>
					<br /><?php echo advshipper_draw_checkbox_field('ups_shipping_service_std', 1, ($ups_shipping_service_std == 1), null,  'id="ups_shipping_service_std" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_UPS_SHIPPING_SERVICE_STD; ?>
					<br /><?php echo advshipper_draw_checkbox_field('ups_shipping_service_xpr', 1, ($ups_shipping_service_xpr == 1), null,  'id="ups_shipping_service_xpr" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_UPS_SHIPPING_SERVICE_XPR; ?>
					<br /><?php echo advshipper_draw_checkbox_field('ups_shipping_service_xprl', 1, ($ups_shipping_service_xprl == 1), null,  'id="ups_shipping_service_xprl" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_UPS_SHIPPING_SERVICE_XPRL; ?>
					<br /><?php echo advshipper_draw_checkbox_field('ups_shipping_service_xdm', 1, ($ups_shipping_service_xdm == 1), null,  'id="ups_shipping_service_xdm" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_UPS_SHIPPING_SERVICE_XDM; ?>
					<br /><?php echo advshipper_draw_checkbox_field('ups_shipping_service_xdml', 1, ($ups_shipping_service_xdml == 1), null,  'id="ups_shipping_service_xdml" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_UPS_SHIPPING_SERVICE_XDML; ?>
					<br /><?php echo advshipper_draw_checkbox_field('ups_shipping_service_xpd', 1, ($ups_shipping_service_xpd == 1), null,  'id="ups_shipping_service_xpd" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_UPS_SHIPPING_SERVICE_XPD; ?>
					<br /><?php echo advshipper_draw_checkbox_field('ups_shipping_service_wxs', 1, ($ups_shipping_service_wxs == 1), null,  'id="ups_shipping_service_wxs" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_UPS_SHIPPING_SERVICE_WXS; ?>
				</td>
			</tr>
		</table>
	</fieldset>
	
	<fieldset class="AdvancedShipperRegion<?php echo
		((($method_num) % 2 == 0) ? 'Even' : 'Odd') .	'ConfigPanel'; ?>">
		<legend><?php echo TEXT_USPS_CALC_SETTINGS; ?></legend>
		<table border="0" width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td class="AdvancedShipperConfigLabel"><label for="usps_calculator_enabled"><?php echo TEXT_LABEL_USPS_ENABLED; ?></label></td>
				<td class="AdvancedShipperConfigField">
					<?php echo advshipper_draw_radio_field('usps_calculator_enabled', 'disabled', ($usps_calculator_enabled == false), null, 'onclick="advshipperUSPSCalculatorSelection(\'disable\');" id="usps_calculator_enabled_disabled" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_USPS_CALC_NOT_BEING_USED; ?>
					<br /><?php echo advshipper_draw_radio_field('usps_calculator_enabled', 'enabled', ($usps_calculator_enabled == true), null, 'onclick="advshipperUSPSCalculatorSelection(\'enabled\');" id="usps_calculator_enabled_enabled" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_USPS_CALC_BEING_USED; ?>
				</td>
			</tr>
			<tr id="usps_user_id_header" style="display: none;">
				<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="usps_user_id"><?php echo TEXT_LABEL_USPS_USER_ID; ?></label></td>
				<td class="AdvancedShipperConfigDesc">
					<?php echo TEXT_CONFIG_DESC_USPS_USER_ID; ?>
				</td>
			</tr>
			<tr id="usps_user_id_field" style="display: none;">
				<td class="AdvancedShipperConfigField">
					<?php echo zen_draw_input_field('usps_user_id', $usps_user_id, 'size="20" id="usps_user_id" onKeyPress="advshipperCheckEnterPressed(event)"'); ?>
				</td>
			</tr>
			<tr id="usps_server_header" style="display: none;">
				<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="usps_server"><?php echo TEXT_LABEL_USPS_SERVER; ?></label></td>
				<td class="AdvancedShipperConfigDesc">
					<?php echo TEXT_CONFIG_DESC_USPS_SERVER; ?>
				</td>
			</tr>
			<tr id="usps_server_field" style="display: none;">
				<td class="AdvancedShipperConfigField">
					<?php echo advshipper_draw_radio_field('usps_server', 't', ($usps_server == 't'), null, 'id="usps_server_test" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_USPS_SERVER_TEST; ?>
					<br /><?php echo advshipper_draw_radio_field('usps_server', 'p', ($usps_server == 'p'), null, 'id="usps_server_production" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_USPS_SERVER_PRODUCTION; ?>
				</td>
			</tr>
			<tr id="usps_source_country_header" style="display: none;">
				<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="usps_source_country"><?php echo TEXT_LABEL_USPS_SOURCE_COUNTRY; ?></label></td>
				<td class="AdvancedShipperConfigDesc">
					<?php echo TEXT_CONFIG_DESC_USPS_SOURCE_COUNTRY; ?>
				</td>
			</tr>
			<tr id="usps_source_country_field" style="display: none;">
				<td class="AdvancedShipperConfigField">
					<?php echo $country_select = zen_draw_pull_down_menu('usps_source_country',
						zen_get_countries(),
						$usps_source_country,
						'id="usps_source_country" onKeyPress="advshipperCheckEnterPressed(event)"'
						); ?>
				</td>
			</tr>
			<tr id="usps_source_postcode_header" style="display: none;">
				<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="usps_source_postcode"><?php echo TEXT_LABEL_USPS_SOURCE_POSTCODE; ?></label></td>
				<td class="AdvancedShipperConfigDesc">
					<?php echo TEXT_CONFIG_DESC_USPS_SOURCE_POSTCODE; ?>
				</td>
			</tr>
			<tr id="usps_source_postcode_field" style="display: none;">
				<td class="AdvancedShipperConfigField">
					<?php echo zen_draw_input_field('usps_source_postcode', $usps_source_postcode, 'size="9" id="usps_source_postcode" onKeyPress="advshipperCheckEnterPressed(event)"'); ?>
				</td>
			</tr>
			<tr id="usps_machinable_header" style="display: none;">
				<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="usps_machinable"><?php echo TEXT_LABEL_USPS_MACHINABLE; ?></label></td>
				<td class="AdvancedShipperConfigDesc">
					<?php echo TEXT_CONFIG_DESC_USPS_MACHINABLE; ?>
				</td>
			</tr>
			<tr id="usps_machinable_field" style="display: none;">
				<td class="AdvancedShipperConfigField">
					<?php echo advshipper_draw_radio_field('usps_machinable', '1', ($usps_machinable == 1), null, 'id="usps_machinable_true" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_USPS_MACHINABLE_TRUE; ?>
					<br /><?php echo advshipper_draw_radio_field('usps_machinable', '0', ($usps_machinable == 0), null, 'id="usps_machinable_false" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_USPS_MACHINABLE_FALSE; ?>
				</td>
			</tr>
			<tr id="usps_display_transit_time_header" style="display: none;">
				<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="usps_display_transit_time"><?php echo TEXT_LABEL_USPS_DISPLAY_TRANSIT_TIME; ?></label></td>
				<td class="AdvancedShipperConfigDesc">
					<?php echo TEXT_CONFIG_DESC_USPS_DISPLAY_TRANSIT_TIME; ?>
				</td>
			</tr>
			<tr id="usps_display_transit_time_field" style="display: none;">
				<td class="AdvancedShipperConfigField">
					<?php echo advshipper_draw_radio_field('usps_display_transit_time', '1', ($usps_display_transit_time == 1), null, 'id="usps_display_transit_time_true" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_USPS_DISPLAY_TRANSIT_TIME_TRUE; ?>
					<br /><?php echo advshipper_draw_radio_field('usps_display_transit_time', '0', ($usps_display_transit_time == 0), null, 'id="usps_display_transit_time_false" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_USPS_DISPLAY_TRANSIT_TIME_FALSE; ?>
				</td>
			</tr>
			<tr id="usps_domestic_services_header" style="display: none;">
				<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="usps_domestic_express"><?php echo TEXT_LABEL_USPS_DOMESTIC_SERVICES; ?></label></td>
				<td class="AdvancedShipperConfigDesc">
					<?php echo TEXT_CONFIG_DESC_USPS_DOMESTIC_SERVICES; ?>
				</td>
			</tr>
			<tr id="usps_domestic_services_field" style="display: none;">
				<td class="AdvancedShipperConfigField">
					<?php echo advshipper_draw_checkbox_field('usps_domestic_express', 1, ($usps_domestic_express == 1), null,  'id="usps_domestic_express" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_USPS_DOMESTIC_EXPRESS; ?>
					<br /><?php echo advshipper_draw_checkbox_field('usps_domestic_priority', 1, ($usps_domestic_priority == 1), null,  'id="usps_domestic_priority" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_USPS_DOMESTIC_PRIORITY; ?>
					<br /><?php echo advshipper_draw_checkbox_field('usps_domestic_first_class', 1, ($usps_domestic_first_class == 1), null,  'id="usps_domestic_first_class" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_USPS_DOMESTIC_FIRST_CLASS; ?>
					<br /><?php echo advshipper_draw_checkbox_field('usps_domestic_parcel', 1, ($usps_domestic_parcel == 1), null,  'id="usps_domestic_parcel" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_USPS_DOMESTIC_PARCEL; ?>
					<br /><?php echo advshipper_draw_checkbox_field('usps_domestic_media', 1, ($usps_domestic_media == 1), null,  'id="usps_domestic_media" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_USPS_DOMESTIC_MEDIA; ?>
					<br /><?php echo advshipper_draw_checkbox_field('usps_domestic_bpm', 1, ($usps_domestic_bpm == 1), null,  'id="usps_domestic_bpm" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_USPS_DOMESTIC_BPM; ?>
					<br /><?php echo advshipper_draw_checkbox_field('usps_domestic_library', 1, ($usps_domestic_library == 1), null,  'id="usps_domestic_library" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_USPS_DOMESTIC_LIBRARY; ?>
				</td>
			</tr>
			<tr id="usps_international_services_header" style="display: none;">
				<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="usps_international_ge"><?php echo TEXT_LABEL_USPS_INTERNATIONAL_SERVICES; ?></label></td>
				<td class="AdvancedShipperConfigDesc">
					<?php echo TEXT_CONFIG_DESC_USPS_INTERNATIONAL_SERVICES; ?>
				</td>
			</tr>
			<tr id="usps_international_services_field" style="display: none;">
				<td class="AdvancedShipperConfigField">
					<br /><?php echo advshipper_draw_checkbox_field('usps_international_ge', 1, ($usps_international_ge == 1), null,  'id="usps_international_ge" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_USPS_INTERNATIONAL_GE; ?>
					<br /><?php echo advshipper_draw_checkbox_field('usps_international_gendr', 1, ($usps_international_gendr == 1), null,  'id="usps_international_gendr" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_USPS_INTERNATIONAL_GENDR; ?>
					<br /><?php echo advshipper_draw_checkbox_field('usps_international_gendnr', 1, ($usps_international_gendnr == 1), null,  'id="usps_international_gendnr" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_USPS_INTERNATIONAL_GENDNR; ?>
					<br /><?php echo advshipper_draw_checkbox_field('usps_international_emi', 1, ($usps_international_emi == 1), null,  'id="usps_international_emi" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_USPS_INTERNATIONAL_EMI; ?>
					<br /><?php echo advshipper_draw_checkbox_field('usps_international_emifre', 1, ($usps_international_emifre == 1), null,  'id="usps_international_emifre" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_USPS_INTERNATIONAL_EMIFRE; ?>
					<br /><?php echo advshipper_draw_checkbox_field('usps_international_pmi', 1, ($usps_international_pmi == 1), null,  'id="usps_international_pmi" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_USPS_INTERNATIONAL_PMI; ?>
					<br /><?php echo advshipper_draw_checkbox_field('usps_international_pmifre', 1, ($usps_international_pmifre == 1), null,  'id="usps_international_pmifre" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_USPS_INTERNATIONAL_PMIFRE; ?>
					<br /><?php echo advshipper_draw_checkbox_field('usps_international_pmifrb', 1, ($usps_international_pmifrb == 1), null,  'id="usps_international_pmifrb" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_USPS_INTERNATIONAL_PMIFRB; ?>
					<br /><?php echo advshipper_draw_checkbox_field('usps_international_fcmile', 1, ($usps_international_fcmile == 1), null,  'id="usps_international_fcmile" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_USPS_INTERNATIONAL_FCMILE; ?>
					<br /><?php echo advshipper_draw_checkbox_field('usps_international_fcmip', 1, ($usps_international_fcmip == 1), null,  'id="usps_international_fcmip" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_USPS_INTERNATIONAL_FCMIP; ?>
					<br /><?php echo advshipper_draw_checkbox_field('usps_international_fcmil', 1, ($usps_international_fcmil == 1), null,  'id="usps_international_fcmil" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_USPS_INTERNATIONAL_FCMIL; ?>
					<br /><?php echo advshipper_draw_checkbox_field('usps_international_fcmif', 1, ($usps_international_fcmif == 1), null,  'id="usps_international_fcmif" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_USPS_INTERNATIONAL_FCMIF; ?>
					<br /><?php echo advshipper_draw_checkbox_field('usps_international_fcmipar', 1, ($usps_international_fcmipar == 1), null,  'id="usps_international_fcmipar" onKeyPress="advshipperCheckEnterPressed(event)"'); ?> <?php echo TEXT_USPS_INTERNATIONAL_FCMIPAR; ?>
				</td>
			</tr>
		</table>
	</fieldset>
</fieldset>
					</td>
				</tr>
				<tr>
					<td align="right" style="padding-bottom: 1.5em">
						<?php
						if ($update_region == false) {
							echo zen_draw_input_field('region_insert_submit', IMAGE_INSERT, 'id="region_insert_submit" onclick="javascript:advshipperSaveRegion(region, \'' . $update_region . '\'); return false;"', false, 'submit');
						} else {
							echo zen_draw_input_field('region_update_submit', IMAGE_UPDATE, 'id="region_insert_submit" onclick="javascript:advshipperSaveRegion(region, \'' . $update_region . '\'); return false;"', false, 'submit');
						}
						echo ' ' . zen_draw_input_field('region_insert_cancel_submit', IMAGE_CANCEL, 'id="region_insert_cancel_submit" onclick="javascript:advshipperCancelRegionConfig(); return false;"', false, 'submit');
						?>
					</td>
				</tr>
			</table>
		</td>
<!-- body_text_eof //-->
	</tr>
</table>
</form>
<!-- body_eof //-->

</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>