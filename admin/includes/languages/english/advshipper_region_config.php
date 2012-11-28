<?php

/**
 * advshipper Region Config Admin Language Definitions
 *
 * @author     Conor Kerr <zen-cart.advshipper@dev.ceon.net>
 * @copyright  Copyright 2007-2009 Ceon
 * @link       http://dev.ceon.net/web/zen-cart/advshipper
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: advshipper_region_config.php 382 2009-06-22 18:49:29Z Bob $
 */

define('HEADING_TITLE', 'Set Rates for a Region');


define('TEXT_REGION_TITLE', 'Region Title');
define('TEXT_REGION', 'Region');
define('TEXT_LABEL_REGION_ADMIN_TITLE', 'Region Admin Title:');
define('TEXT_CONFIG_DESC_REGION_ADMIN_TITLE', 'Set or change the title to be used in the Admin to identify this region by entering the desired title for the language(s) below.');
define('TEXT_LABEL_REGION_TITLE', 'Region Title:');
define('TEXT_CONFIG_DESC_REGION_TITLE', 'Set or change the title for this region by entering the desired title for the language(s) below.');

define('TEXT_DEFINITION_METHOD', 'Definition Method');
define('TEXT_LABEL_DEFINITION_METHOD', 'Definition Method:');
define('TEXT_CONFIG_DESC_DEFINITION_METHOD', 'Should this region be determined by matching against one of the specified address ranges or should geolocation be used to determine the distance of the customer\'s address from the store?');
define('TEXT_DEFINITION_METHOD_ADDRESS_MATCHING', 'Address Matching');
define('TEXT_INTRO_DEFINITION_METHOD_ADDRESS_MATCHING_LOCALITY_AND_ZONE_SUPPORT', 'Both Locality and Zone support are available so any combination of one, two, three or even all four of the differing means of matching a region to a customer\'s address can be used, but a minimum of one must have value(s) to match specified. <br /><br /><strong>Duplication of information isn\'t necessary</strong>: For example, it is not necessary to add a City within a Country if that City is within a Postcode Range defined for the region, as the customer\'s address will be already have been matched by their Country and Postcode combination.');
define('TEXT_INTRO_DEFINITION_METHOD_ADDRESS_MATCHING_ZONE_SUPPORT', 'Zone support is available so either countries/postcodes, countries/zones or a combination of both methods of matching a region to a customer\'s address can be used. <br /><br /><strong>Duplication of information isn\'t necessary</strong>: For example, it is not necessary to select a Zones within a Country if that Zone is within a Postcode Range defined for the region, as the customer\'s address will be already have been matched by their Country and Postcode combination.');
define('TEXT_INTRO_DEFINITION_METHOD_ADDRESS_MATCHING_LOCALITY_SUPPORT', 'Locality support is available so any combination of one, two or even all three of the differing means of matching a region to a customer\'s address can be used, but a minimum of one must have value(s) to match specified. <br /><br /><strong>Duplication of information isn\'t necessary</strong>: For example, it is not necessary to add a City within a Country if that City is within a Postcode Range defined for the region, as the customer\'s address will be already have been matched by their Country and Postcode combination.');
define('TEXT_INTRO_DEFINITION_METHOD_ADDRESS_MATCHING_NO_LOCALITY_OR_ZONE_SUPPORT', 'Neither Locality support nor Zone support is installed so the only method of defining a region is Countries/Postcodes matching.');
define('TEXT_DEFINITION_METHOD_GEOLOCATION', 'Geolocation');

define('TEXT_ADDRESS_MATCHING', 'Address Matching');
define('TEXT_LABEL_COUNTRIES_POSTCODES', 'Countries/Postcodes:');
define('TEXT_CONFIG_DESC_COUNTRIES_POSTCODES', 'This is a comma-separated list of ISO Country Codes and Postcode Ranges which are included in this region. (E.g. GB:BT10-24,GB:LE or US:100-200,US:35003).');
define('TEXT_LABEL_COUNTRIES_CITIES', 'Countries/Cities:');
define('TEXT_CONFIG_DESC_COUNTRIES_CITIES', 'This is a list of Cities which are included in this region.');
define('TEXT_CURRENT_CITIES', 'Current Cities');
define('TEXT_LABEL_COUNTRIES_STATES', 'Countries/States:');
define('TEXT_CONFIG_DESC_COUNTRIES_STATES', 'This is a list of States/Localities which are included in this region.');
define('TEXT_CURRENT_STATES', 'Current States');
define('TEXT_LABEL_COUNTRIES_ZONES', 'Countries/Zones:');
define('TEXT_CONFIG_DESC_COUNTRIES_ZONES', 'This is a list of Zones which are included in this region.');
define('TEXT_CURRENT_ZONES', 'Current Zones');

define('TEXT_GEOLOCATION', 'Geolocation');
define('TEXT_LABEL_DISTANCE', 'Distance From Store:');
define('TEXT_CONFIG_DESC_DISTANCE', 'This is the distance from the store that defines this region.');

define('TEXT_RATES_CONFIG', 'Rates Configuration');
define('TEXT_LABEL_TAX_CLASS', 'Tax Class:');
define('TEXT_CONFIG_DESC_TAX_CLASS', 'This is the tax class to be used for the shipping rates for this region.');
define('TEXT_LABEL_RATES_INCLUDE_TAX', 'Rates Include Tax:');
define('TEXT_CONFIG_DESC_RATES_INCLUDE_TAX', 'Do the Rates/Fee entered below include tax? (This setting is used only if a Tax Class is selected for this region).');
define('TEXT_RATES_INCLUDE_TAX', 'Rates Include Tax');
define('TEXT_RATES_DO_NOT_INCLUDE_TAX', 'Rates Do Not Include Tax');
define('TEXT_LABEL_RATE_LIMITS_INC', 'Rate Limits Inclusivity:');
define('TEXT_CONFIG_DESC_RATE_LIMITS_INC', 'Do the Limits for the Rates entered in the Tables of Rates include or exclude the <strong>upper</strong> limit? (E.g. For 100-200, is 200 <em>included</em> in the range, or is it <em>excluded</em> - with the last whole number in the range being 199 and the range effectively being 100.0000 - 199.9999?) ');
define('TEXT_RATE_LIMITS_INC_INC', 'Rate Limits Are Inclusive');
define('TEXT_RATE_LIMITS_INC_EXC', 'Rate Limits Are Exclusive');
define('TEXT_LABEL_TOTAL_UP_PRICE_INC_TAX', 'Total Up Price of Applicable/All Products Including Their Tax:');
define('TEXT_CONFIG_DESC_TOTAL_UP_PRICE_INC_TAX', 'When comparing the total <strong>price</strong> of the applicable products or the <strong>totalorderprice</strong> of all the products in the order against limits in the table of rates, should the price of the products include or exclude their tax?');
define('TEXT_TOTAL_UP_PRICE_INC_TAX_INC', 'Include Tax When Totalling Up Price Of Products For Comparison Against Rate Limits');
define('TEXT_TOTAL_UP_PRICE_INC_TAX_EXC', 'Exclude Tax When Totalling Up Price Of Products For Comparison Against Rate Limits');
define('TEXT_LABEL_TABLE_OF_RATES', 'Table of Rates:');
define('TEXT_CONFIG_DESC_TABLE_OF_RATES', 'Table of Shipping rates to destinations in this region, based the <strong>weight</strong>, <strong>price</strong> or the <strong>numitems</strong> of the applicable products. Example: &lt;price&gt;50:2.95, 100:3.50&lt;/price&gt; means price less than or equal to 50 would cost 2.95 for destinations in this region, 50-100 would be 3.50. See docs for full explanation of format.');
define('TEXT_LABEL_MAX_WEIGHT_PER_PACKAGE', 'Max Weight Per Package:');
define('TEXT_CONFIG_DESC_MAX_WEIGHT_PER_PACKAGE', 'If there is a maximum weight of applicable products which can be sent in a single package, and the ability to split the order into multiple packages is available, enter the weight at which the products for this method will be split into another package. <br /><br />All packages will use the <strong>same</strong> table of rates and packaging weight settings, but rates will be calculated using the weight of each individual package.');
define('TEXT_LABEL_PACKAGING_WEIGHTS', 'Packaging Weights:');
define('TEXT_CONFIG_DESC_PACKAGING_WEIGHTS', 'A table of the packaging weights to be added on to the weight of the applicable products - before the number of packages required to ship the applicable products for this method is worked out or the rate for the <strong>weight</strong> is calculated  - can be entered here. E.g. &ldquo;0-5:0.03, *:0.2+1%&rdquo;.');
define('TEXT_LABEL_SURCHARGE', 'Surcharge:');
define('TEXT_CONFIG_DESC_SURCHARGE', 'This is the surcharge for orders shipped to this region (if any). It can be a flat rate, e.g. 1.50.
<br/><br/>Or it can be a table of surcharge rates, based on the total <strong>price</strong> or the <strong>numitems</strong> of the applicable products, the <strong>shipping</strong> rate calculated for the method, the total <strong>weight</strong> of all the packages calculated for the method (all the applicable products plus the packaging) or the <strong>num</strong>ber of <strong>packages</strong> being used to ship the applicable products by this method.
<br /><br />E.g. &lt;price&gt;0-200:[100:1], 1000:[100:0.75], *:7.50&lt;/price&gt;, &lt;shipping&gt;*:1%+0.50&lt;/shipping&gt;, &lt;weight&gt;*:10%&lt;/weight&gt; or &lt;numpackages&gt;*:200%&lt;/numpackages&gt;.');
define('TEXT_LABEL_SURCHARGE_TITLE', 'Surcharge Text:');
define('TEXT_CONFIG_DESC_SURCHARGE_TITLE', 'Set or change the surcharge text for this region by entering the desired text for the language(s) below. The placement tag {surcharge_amount} must be present somewhere in this text as that is where the amount will be placed. If nothing is entered here, the default text from the language file will be used. <br /><br />This entire text will then replace the placement tag {surcharge_info} in the method\'s title (if placement tags are used in the method title, otherwise it will be appended to the title).');

define('TEXT_UPS_CALC_SETTINGS', 'UPS Calculator Settings');
define('TEXT_LABEL_UPS_ENABLED', 'UPS Calculator Enabled:');
define('TEXT_UPS_CALC_NOT_BEING_USED', 'UPS calculator isn\'t being used in the Table of Rates so no settings are necessary.');
define('TEXT_UPS_CALC_BEING_USED', 'UPS calculator is being used in the Table of Rates. UPS Calculator must be configured below.');
define('TEXT_LABEL_UPS_SOURCE_COUNTRY', 'Shipping Source Country:');
define('TEXT_CONFIG_DESC_UPS_SOURCE_COUNTRY', 'This is the country from which products using this method will be shipped. Defaults to store\'s country.');
define('TEXT_LABEL_UPS_SOURCE_POSTCODE', 'Shipping Source Postcode:');
define('TEXT_CONFIG_DESC_UPS_SOURCE_POSTCODE', 'This is the postcode/zip of the place from which products using this method will be shipped. Defaults to store\'s postcode.');
define('TEXT_LABEL_UPS_PICKUP_METHOD', 'UPS Pickup Method:');
define('TEXT_CONFIG_DESC_UPS_PICKUP_METHOD', 'How will the packages be given to UPS? CC - Customer Counter, RDP - Daily Pickup, OTP - One Time Pickup, LC - Letter Center, OCA - On Call Air');
define('TEXT_LABEL_UPS_PACKAGING', 'UPS Packaging:');
define('TEXT_CONFIG_DESC_UPS_PACKAGING', 'What packaging will be used? CP - Your Packaging, ULE - UPS Letter, UT - UPS Tube, UBE - UPS Express Box');
define('TEXT_LABEL_UPS_DELIVERY_TYPE', 'Delivery type:');
define('TEXT_CONFIG_DESC_UPS_DELIVERY_TYPE', 'Is the quote to be classed as being for residential (RES) or commerical (COM) delivery?');
define('TEXT_LABEL_UPS_SHIPPING_SERVICES', 'UPS Shipping Services:');
define('TEXT_CONFIG_DESC_UPS_SHIPPING_SERVICES', 'Select the UPS Services to be offered.');

define('TEXT_UPS_SHIPPING_SERVICE_1DM', 'Next Day Air Early AM');
define('TEXT_UPS_SHIPPING_SERVICE_1DML', 'Next Day Air Early AM Letter');
define('TEXT_UPS_SHIPPING_SERVICE_1DA', 'Next Day Air');
define('TEXT_UPS_SHIPPING_SERVICE_1DAL', 'Next Day Air Letter');
define('TEXT_UPS_SHIPPING_SERVICE_1DAPI', 'Next Day Air Intra (Puerto Rico)');
define('TEXT_UPS_SHIPPING_SERVICE_1DP', 'Next Day Air Saver');
define('TEXT_UPS_SHIPPING_SERVICE_1DPL', 'Next Day Air Saver Letter');
define('TEXT_UPS_SHIPPING_SERVICE_2DM', '2nd Day Air AM');
define('TEXT_UPS_SHIPPING_SERVICE_2DML', '2nd Day Air AM Letter');
define('TEXT_UPS_SHIPPING_SERVICE_2DA', '2nd Day Air');
define('TEXT_UPS_SHIPPING_SERVICE_2DAL', '2nd Day Air Letter');
define('TEXT_UPS_SHIPPING_SERVICE_3DS', '3 Day Select');
define('TEXT_UPS_SHIPPING_SERVICE_GND', 'Ground');
define('TEXT_UPS_SHIPPING_SERVICE_GNCRES', 'Ground Residential');
define('TEXT_UPS_SHIPPING_SERVICE_GNDCOM', 'Ground Commercial');
define('TEXT_UPS_SHIPPING_SERVICE_STD', 'Canada Standard');
define('TEXT_UPS_SHIPPING_SERVICE_XPR', 'Worldwide Express');
define('TEXT_UPS_SHIPPING_SERVICE_XPRL', 'Worldwide Express Letter');
define('TEXT_UPS_SHIPPING_SERVICE_XDM', 'Worldwide Express Plus');
define('TEXT_UPS_SHIPPING_SERVICE_XDML', 'Worldwide Express Plus Letter');
define('TEXT_UPS_SHIPPING_SERVICE_XPD', 'Worldwide Expedited');
define('TEXT_UPS_SHIPPING_SERVICE_WXS', 'Worldwide Saver');

define('TEXT_USPS_CALC_SETTINGS', 'USPS Calculator Settings');
define('TEXT_LABEL_USPS_ENABLED', 'USPS Calculator Enabled:');
define('TEXT_USPS_CALC_NOT_BEING_USED', 'USPS calculator isn\'t being used in the Table of Rates so no settings are necessary.');
define('TEXT_USPS_CALC_BEING_USED', 'USPS calculator is being used in the Table of Rates. USPS Calculator must be configured below.');
define('TEXT_LABEL_USPS_USER_ID', 'USPS Web Tools User ID:');
define('TEXT_CONFIG_DESC_USPS_USER_ID', 'This is the USPS USERID assigned for Rate Quotes/ShippingAPI.');
define('TEXT_LABEL_USPS_SERVER', 'Server:');
define('TEXT_CONFIG_DESC_USPS_SERVER', 'This is the USPS server to be used for quotes. An account at USPS is needed to use the Production server.');
define('TEXT_USPS_SERVER_TEST', 'Test');
define('TEXT_USPS_SERVER_PRODUCTION', 'Production');
define('TEXT_LABEL_USPS_SOURCE_COUNTRY', 'Shipping Source Country:');
define('TEXT_CONFIG_DESC_USPS_SOURCE_COUNTRY', 'This is the country from which products using this method will be shipped. Defaults to store\'s country.');
define('TEXT_LABEL_USPS_SOURCE_POSTCODE', 'Shipping Source Postcode:');
define('TEXT_CONFIG_DESC_USPS_SOURCE_POSTCODE', 'This is the postcode/zip of the place from which products using this method will be shipped. Defaults to store\'s postcode.');
define('TEXT_LABEL_USPS_MACHINABLE', 'All Packages are Machinable:');
define('TEXT_CONFIG_DESC_USPS_MACHINABLE', 'Are all products shipped machinable based on C700 Package Services 2.0 Nonmachinable PARCEL POST USPS Rules and Regulations?<br /><br /><strong>Note: Nonmachinable packages will usually result in a higher Parcel Post Rate Charge.<br /><br />Packages 35lbs or more, or less than 6 ounces (.375), will be overridden and set to False</strong>');
define('TEXT_USPS_MACHINABLE_TRUE', 'True');
define('TEXT_USPS_MACHINABLE_FALSE', 'False');
define('TEXT_LABEL_USPS_DISPLAY_TRANSIT_TIME', 'Display transit time:');
define('TEXT_CONFIG_DESC_USPS_DISPLAY_TRANSIT_TIME', 'Should the transit time be appended to the method\'s title (if possible)?');
define('TEXT_USPS_DISPLAY_TRANSIT_TIME_TRUE', 'True');
define('TEXT_USPS_DISPLAY_TRANSIT_TIME_FALSE', 'False');
define('TEXT_LABEL_USPS_DOMESTIC_SERVICES', 'Domestic Services:');
define('TEXT_CONFIG_DESC_USPS_DOMESTIC_SERVICES', 'Select the USPS Domestic Services to be offered.');

define('TEXT_USPS_DOMESTIC_EXPRESS', 'Express Mail');
define('TEXT_USPS_DOMESTIC_PRIORITY', 'Priority Mail');
define('TEXT_USPS_DOMESTIC_FIRST_CLASS', 'First Class Mail');
define('TEXT_USPS_DOMESTIC_PARCEL', 'Parcel Post');
define('TEXT_USPS_DOMESTIC_MEDIA', 'Media Mail');
define('TEXT_USPS_DOMESTIC_BPM', 'Bound Printed Material');
define('TEXT_USPS_DOMESTIC_LIBRARY', 'Library');

define('TEXT_LABEL_USPS_INTERNATIONAL_SERVICES', 'International Services:');
define('TEXT_CONFIG_DESC_USPS_INTERNATIONAL_SERVICES', 'Select the USPS International Services to be offered.');

define('TEXT_USPS_INTERNATIONAL_GE', 'Global Express Guaranteed');
define('TEXT_USPS_INTERNATIONAL_GENDR', 'Global Express Guaranteed Non-Document Rectangular');
define('TEXT_USPS_INTERNATIONAL_GENDNR', 'Global Express Guaranteed Non-Document Non-Rectangular');
//define('TEXT_USPS_INTERNATIONAL_GEE', 'USPS GXG Envelopes');
define('TEXT_USPS_INTERNATIONAL_EMI', 'Express Mail International (EMS)');
define('TEXT_USPS_INTERNATIONAL_EMIFRE', 'Express Mail International (EMS) Flat-Rate Envelope');
define('TEXT_USPS_INTERNATIONAL_PMI', 'Priority Mail International');
define('TEXT_USPS_INTERNATIONAL_PMIFRE', 'Priority Mail International Flat-Rate Envelope');
define('TEXT_USPS_INTERNATIONAL_PMIFRB', 'Priority Mail International Flat-Rate Box');
//define('TEXT_USPS_INTERNATIONAL_PMILFRB', 'Priority Mail International Large Flat Rate Box');
define('TEXT_USPS_INTERNATIONAL_FCMILE', 'First Class Mail International Large Envelope');
define('TEXT_USPS_INTERNATIONAL_FCMIP', 'First Class Mail International Package');
define('TEXT_USPS_INTERNATIONAL_FCMIL', 'First Class Mail International Letters');
define('TEXT_USPS_INTERNATIONAL_FCMIF', 'First Class Mail International Flats');
define('TEXT_USPS_INTERNATIONAL_FCMIPAR', 'First Class Mail International Parcels');

define('JS_TEXT_ZONE_IN_LIST', 'The selected zone is already in the list of zone \nwhich define this region!');
define('JS_TEXT_STATE_IN_LIST', 'The selected state is already in the list of states \nwhich define this region!');
define('JS_TEXT_CITY_IN_LIST', 'The selected city is already in the list of cities \nwhich define this region!');

define('TEXT_ERROR_IN_CONFIG', 'A problem was found with the configuration, please fix the error highlighted below then try again to save the changes.');
define('TEXT_ERRORS_IN_CONFIG', '%s problems were found with the configuration, please fix the errors highlighted below then try again to save the changes.');

define('ADVSHIPPER_JS_ERROR_NO_ADDRESS_DEFINED', '* An address to match has not been defined!\n');
define('ADVSHIPPER_JS_ERROR_DISTANCE_NOT_SPECIFIED', '* The distance from the store has not been specified!\n');
define('ADVSHIPPER_JS_ERROR_TABLE_OF_RATES_NOT_SPECIFIED', '* The table of rates has not been specified!\n');
define('ADVSHIPPER_JS_ERROR_TABLE_OF_RATES_FORMAT', '* The table of rates have not been specified properly\n- the calculation method tags which should wrap the table are missing or incorrect!\n');
define('ADVSHIPPER_JS_ERROR_TABLE_OF_RATES_LIMITS_FORMAT', '* The limits for a rate are not valid: ');
define('ADVSHIPPER_JS_ERROR_SURCHARGE_FORMAT', '* The surcharge is not valid: ');

define('SUCCESS_CONFIGURATION_UPDATED', 'Region %s configuration for &ldquo;%s&rdquo; was successfully updated!');
define('SUCCESS_CONFIGURATION_UPDATED_STATE_DELETED', '&ldquo;%s&rdquo; deleted from shipping method for region %s in configuration &ldquo;%s&rdquo;!');
define('SUCCESS_CONFIGURATION_UPDATED_CITY_DELETED', '&ldquo;%s&rdquo; deleted from shipping method for region %s in configuration &ldquo;%s&rdquo;!');

?>