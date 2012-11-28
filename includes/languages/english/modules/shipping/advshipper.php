<?php

/**
 * advshipper Language Definitions
 *
 * @author     Conor Kerr <zen-cart.advshipper@dev.ceon.net>
 * @copyright  Copyright 2007-2009 Ceon
 * @link       http://dev.ceon.net/web/zen-cart/advshipper
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: advshipper.php 382 2009-06-22 18:49:29Z Bob $
 */


define('MODULE_ADVANCED_SHIPPER_TEXT_WEIGHT_UNIT_PLURAL', 'Kgs');
define('MODULE_ADVANCED_SHIPPER_TEXT_WEIGHT_UNIT_SINGULAR', 'Kg');

define('MODULE_ADVANCED_SHIPPER_TEXT_FREE', 'Free');
define('MODULE_ADVANCED_SHIPPER_TEXT_PER', ' per ');

/**
 * The default title for the shipping module.
 *
 * Please Note: The module MUST have a title defined or it will not appear as an option at checkout.
 *
 * If it is preferred not to display a title for the module, please take a look at the FAQ
 * "How can the overall title for the shipping be changed..."
 */
define('MODULE_ADVANCED_SHIPPER_DEFAULT_TITLE', 'Shipping'); // DO NOT LEAVE BLANK!

/**
 * Message to be displayed if the customer's address doesn't match any of the defined regions.
 */
define('MODULE_ADVANCED_SHIPPER_ERROR_INVALID_REGION', 'Sorry but we do not ship to your address by this shipping method!');

/**
 * Messages to be displayed if only the country has been selected in the shipping estimator and no
 * methods were matched (give the customer a chance to specify their region in case that's the
 * reason no quotes are available).
 */
define('MODULE_ADVANCED_SHIPPER_ERROR_SPECIFY_SHIPPING_ADDRESS', 'Please select your Country, enter/select your State/Province and enter your Post/Zip Code.');
define('MODULE_ADVANCED_SHIPPER_ERROR_SPECIFY_POSTCODE', 'Please enter your Post/Zip Code.');
define('MODULE_ADVANCED_SHIPPER_ERROR_SPECIFY_STATE', 'Please select your Country and enter/select your State/Province.');

/**
 * Message to be displayed if the customer's address is matched but no rates match the order.
 */
define('MODULE_ADVANCED_SHIPPER_ERROR_NO_RATES_MATCH', 'Sorry but there are no shipping options to your address at this time!');

/**
 * Message to be displayed if one or more products the customer has ordered required the customer
 * to contact the store to determine the shipping options.
 */
define('MODULE_ADVANCED_SHIPPER_TEXT_CONTACT_STORE', 'One or more of the product(s) you are ordering require personal shipping arrangements. Please <a href="' . zen_href_link(FILENAME_CONTACT_US) . '" target="_blank">Contact Us</a> to arrange shipping and complete your order.');

/**
 * Message to be displayed if one or more products the customer has ordered required the customer
 * to contact the store to determine the shipping options AFTER the order has been completed.
 */
define('MODULE_ADVANCED_SHIPPER_TEXT_CONTACT_STORE_AFTER_ORDER', '<strong>Contact Us</strong> - This method includes one or more products which require personal shipping arrangements. If you select this method, you <strong>must</strong> contact us to arrange shipping/finalise shipping costs after completing your order.');


/**
 * Message to be displayed if the customer's address is matched and a usable rate is found but the
 * method isn't available for use at the minute because of availability/shipping scheduling
 * settings.
 */
define('MODULE_ADVANCED_SHIPPER_ERROR_NO_RATES_AVAILABLE', 'Sorry but there are no shipping options available to your address at this time!');

/**
 * Message to be displayed if the no combination of shipping methods can be found to allocate each
 * product in the cart exactly one shipping method.
 */
define('MODULE_ADVANCED_SHIPPER_ERROR_NO_COMBINATIONS', 'Sorry but our shipping options don\'t cover the combination of products currently in the cart!');

/**
 * Shipping method output templates - can be used to customise the format of the output on the
 * checkout page etc. HTML is allowed.
 *
 * Individual tags are used to place the content at the appropriate points.
 *
 * There are two alternative layouts.. if an option contains a combination of methods, the
 * information about the applicable products is displayed for each method.
 *
 * If there is only one method, information about the applicable products isn't displayed (as all
 * are obviously applicable!)
 *
 *
 *
 * Layout 1) For a combination of methods, the templates are nested as follows:
 *
 * THe overall method combination is "wrapped" using the following template:
 *
 * MODULE_ADVANCED_SHIPPER_TEMPLATE_SHIPPING_METHOD_COMB
 *
 * Each shipping method within the method combination is wrapped using the following template, with
 * the following method being appended to the previous:
 *
 * MODULE_ADVANCED_SHIPPER_TEMPLATE_SHIPPING_METHOD_WITH_PRODUCT_INFO
 *
 * Each product within a method is wrapped using the following template, with the following product
 * being appended to the previous:
 *
 * MODULE_ADVANCED_SHIPPER_TEMPLATE_PRODUCT_INFO
 *
 * If a product has attributes, they make use of the following template. Like the method and product
 * templates, if there is more than one attribute, each following attribute is appended to the
 * previous attribute:
 *
 * MODULE_ADVANCED_SHIPPER_TEMPLATE_PRODUCT_INFO_ATTRIBUTE_INFO
 *
 *
 *
 * Layout 2) For a single method, only one template is necessary:
 * 
 * MODULE_ADVANCED_SHIPPER_TEMPLATE_SHIPPING_METHOD_NO_PRODUCT_INFO
 * 
 */
define('MODULE_ADVANCED_SHIPPER_TEMPLATE_SHIPPING_METHOD_COMB', '<div class="AdvancedShipperShippingMethodCombination">{method_comb}</div>');
define('MODULE_ADVANCED_SHIPPER_TEMPLATE_SHIPPING_METHOD_WITH_PRODUCT_INFO', '<p class="AdvancedShipperShippingMethod">{method_title}{product_info}</p>');
define('MODULE_ADVANCED_SHIPPER_TEMPLATE_PRODUCT_INFO', '<br />{quantity} x {name}{attribute_info}');
define('MODULE_ADVANCED_SHIPPER_TEMPLATE_PRODUCT_INFO_ATTRIBUTE_INFO', '<br />// {name} -- {value}');

define('MODULE_ADVANCED_SHIPPER_TEMPLATE_SHIPPING_METHOD_NO_PRODUCT_INFO', '{method_title}');

/**
 * Fallback/default output templates for method total, rate calculation description and handling fee
 * used if placement tag not used in title. These are appended to the method's title if they are
 * being used.
 */
define('MODULE_ADVANCED_SHIPPER_TEMPLATE_METHOD_TOTAL', ' ({method_total})');
define('MODULE_ADVANCED_SHIPPER_TEMPLATE_RATE_CALC_DESC', ' ({num_packages_desc}{rate_calc_desc})');
define('MODULE_ADVANCED_SHIPPER_TEMPLATE_SURCHARGE', ' (Inc Handling Fee: {surcharge_amount})');

/**
 * Default templates for information about number of packages which would have to be used for a
 * method - to be used alongside the rate_calc_desc.
 */
define('MODULE_ADVANCED_SHIPPER_TEXT_NUM_PACKAGES_SINGLE', '');
define('MODULE_ADVANCED_SHIPPER_TEXT_NUM_PACKAGES_MULTIPLE', '{num_packages} Packages: ');

/**
 * Default templates for information about the weights of the package(s) which would have to be used
 * for a method - to be used instead of the rate_calc_desc, NOT alongside it.
 */
define('MODULE_ADVANCED_SHIPPER_TEXT_PACKAGE_WEIGHTS_DESC_SINGLE', '{package_weight}');
define('MODULE_ADVANCED_SHIPPER_TEXT_PACKAGE_WEIGHTS_DESC_MULTIPLE', '{num_packages} Packages: {package_weights}');

/**
 * Message to be displayed if no title was found for the method for the current language (should
 * only happen if store's language settings have changed since method was last saved)
 */
define('MODULE_ADVANCED_SHIPPER_METHOD_TITLE_MISSING', 'Standard Shipping - TITLE NEEDS UPDATING');

/**
 * Titles for UPS shipping methods
 */
define('TEXT_UPS_TITLE_PREFIX', ' - UPS: ');
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


/**
 * Titles for USPS shipping methods
 */
define('TEXT_USPS_TITLE_PREFIX', ' - USPS: ');
define('TEXT_USPS_DOMESTIC_EXPRESS', 'Express Mail');
define('TEXT_USPS_DOMESTIC_PRIORITY', 'Priority Mail');
define('TEXT_USPS_DOMESTIC_FIRST_CLASS', 'First Class Mail');
define('TEXT_USPS_DOMESTIC_PARCEL', 'Parcel Post');
define('TEXT_USPS_DOMESTIC_MEDIA', 'Media Mail');
define('TEXT_USPS_DOMESTIC_BPM', 'Bound Printed Material');
define('TEXT_USPS_DOMESTIC_LIBRARY', 'Library');
define('TEXT_USPS_INTERNATIONAL_GE', 'Global Express Guaranteed');
define('TEXT_USPS_INTERNATIONAL_GENDR', 'Global Express Guaranteed Non-Document Rectangular');
define('TEXT_USPS_INTERNATIONAL_GENDNR', 'Global Express Guaranteed Non-Document Non-Rectangular');
define('TEXT_USPS_INTERNATIONAL_GEE', 'USPS GXG Envelopes');
define('TEXT_USPS_INTERNATIONAL_EMI', 'Express Mail International (EMS)');
define('TEXT_USPS_INTERNATIONAL_EMIFRE', 'Express Mail International (EMS) Flat-Rate Envelope');
define('TEXT_USPS_INTERNATIONAL_PMI', 'Priority Mail International');
define('TEXT_USPS_INTERNATIONAL_PMIFRE', 'Priority Mail International Flat-Rate Envelope');
define('TEXT_USPS_INTERNATIONAL_PMIFRB', 'Priority Mail International Flat-Rate Box');
define('TEXT_USPS_INTERNATIONAL_PMILFRB', 'Priority Mail International Large Flat Rate Box');
define('TEXT_USPS_INTERNATIONAL_FCMILE', 'First Class Mail International Large Envelope');
define('TEXT_USPS_INTERNATIONAL_FCMIP', 'First Class Mail International Package');
define('TEXT_USPS_INTERNATIONAL_FCMIL', 'First Class Mail International Letters');
define('TEXT_USPS_INTERNATIONAL_FCMIF', 'First Class Mail International Flats');
define('TEXT_USPS_INTERNATIONAL_FCMIPAR', 'First Class Mail International Parcels');

define('TEXT_USPS_DAY', 'day');
define('TEXT_USPS_DAYS', 'days');
define('TEXT_USPS_WEEKS', 'weeks');

define('MODULE_ADVANCED_SHIPPER_ERROR_USPS_SERVER', 'An error occurred when attempting to obtain USPS shipping quotes.<br />If you prefer to use USPS as your shipping method, please <a href="index.php?main_page=contact_us">contact us</a> for  assistance. The error was: ');
define('MODULE_ADVANCED_SHIPPER_USPS_TEST_MODE_NOTICE', '<br /><br /><span class="alert">The USPS account is in TEST MODE. Usable rate quotes may not be displayed until the USPS account is moved to the production server (1-800-344-7779) and the USPS calculation settings havbe been set to production mode in the admin.</span>');



/**
 * The default fallback error message for invalid postcode formats.
 */
define('MODULE_ADVANCED_SHIPPER_ERROR_CUSTOMER_POSTCODE_PARSE', 'Sorry! It is not possible to calculate the shipping rate to the selected address as we are unable to verify the postcode. <br /><br />Please check that the postcode entered for this shipping address, &ldquo;%s&rdquo;, is correct. <br /><br />If not, please go to &ldquo;View or Change Entries in my Address Book&rdquo; in your account and update this postcode so that the shipping rate can be determined for your order!');

/**
 * Specific error messages for invalid postcode formats for each country. 
 */
define('MODULE_ADVANCED_SHIPPER_ERROR_CUSTOMER_POSTCODE_PARSE_AU', 'Sorry! It is not possible to calculate the shipping rate to the selected address as the postcode, &ldquo;%s&rdquo;, is not in the correct format. <br /><br />Please go to &ldquo;View or Change Entries in my Address Book&rdquo; in your account and update this postcode so that the shipping rate can be determined for your order! <br /><br />Postcodes for Australia should be 4 digits in length. An example is &ldquo;2000&rdquo;.');
define('MODULE_ADVANCED_SHIPPER_ERROR_CUSTOMER_POSTCODE_PARSE_CA', 'Sorry! It is not possible to calculate the shipping rate to the selected address as the postcode, &ldquo;%s&rdquo;, is not in the correct format. <br /><br />Please go to &ldquo;View or Change Entries in my Address Book&rdquo; in your account and update this postcode so that the shipping rate can be determined for your order! <br /><br />Postcodes for Canada should be in the format: &ldquo;X9X 9X9&rdquo;, where X is a letter and 9 is a digit. An example is &ldquo;K1A 0B1&rdquo;.');
define('MODULE_ADVANCED_SHIPPER_ERROR_CUSTOMER_POSTCODE_PARSE_CZ', 'Sorry! It is not possible to calculate the shipping rate to the selected address as the postcode, &ldquo;%s&rdquo;, is not in the correct format. <br /><br />Please go to &ldquo;View or Change Entries in my Address Book&rdquo; in your account and update this postcode so that the shipping rate can be determined for your order! <br /><br />Postcodes for the Czech Republic should be 5 digits in length. An example is &ldquo;364 97&rdquo;.');
define('MODULE_ADVANCED_SHIPPER_ERROR_CUSTOMER_POSTCODE_PARSE_ES', 'Sorry! It is not possible to calculate the shipping rate to the selected address as the postcode, &ldquo;%s&rdquo;, is not in the correct format. <br /><br />Please go to &ldquo;View or Change Entries in my Address Book&rdquo; in your account and update this postcode so that the shipping rate can be determined for your order! <br /><br />Postcodes for Spain should be 5 digits in length. An example is &ldquo;46025&rdquo;.');
define('MODULE_ADVANCED_SHIPPER_ERROR_CUSTOMER_POSTCODE_PARSE_FR', 'Sorry! It is not possible to calculate the shipping rate to the selected address as the postcode, &ldquo;%s&rdquo;, is not in the correct format. <br /><br />Please go to &ldquo;View or Change Entries in my Address Book&rdquo; in your account and update this postcode so that the shipping rate can be determined for your order! <br /><br />Postcodes for France should be 5 digits in length. An example is &ldquo;75008&rdquo;.');
define('MODULE_ADVANCED_SHIPPER_ERROR_CUSTOMER_POSTCODE_PARSE_GB', 'Sorry! It is not possible to calculate the shipping rate to the selected address as the postcode, &ldquo;%s&rdquo;, is not in the correct format. <br /><br />Please go to &ldquo;View or Change Entries in my Address Book&rdquo; in your account and update this postcode so that the shipping rate can be determined for your order! <br /><br />It should be in one of the following formats: &ldquo;X9 9XX&rdquo;, &ldquo;X99 9XX&rdquo;, &ldquo;X9X 9XX&rdquo;, &ldquo;XX9 9XX&rdquo;, &ldquo;XX99 9XX&rdquo; or &ldquo;XX9X 9XX&rdquo;, where X is a letter and 9 is a digit. An example is &ldquo;BT10 0JX&rdquo;.');
define('MODULE_ADVANCED_SHIPPER_ERROR_CUSTOMER_POSTCODE_PARSE_IN', 'Sorry! It is not possible to calculate the shipping rate to the selected address as the postcode, &ldquo;%s&rdquo;, is not in the correct format. <br /><br />Please go to &ldquo;View or Change Entries in my Address Book&rdquo; in your account and update this postcode so that the shipping rate can be determined for your order! <br /><br />Postcodes for India should be 6 digits in length. An example is &ldquo;625002&rdquo;.');
define('MODULE_ADVANCED_SHIPPER_ERROR_CUSTOMER_POSTCODE_PARSE_IT', 'Sorry! It is not possible to calculate the shipping rate to the selected address as the postcode, &ldquo;%s&rdquo;, is not in the correct format. <br /><br />Please go to &ldquo;View or Change Entries in my Address Book&rdquo; in your account and update this postcode so that the shipping rate can be determined for your order! <br /><br />Postcodes for Italy should be 5 digits in length, with an optional two letter prefix. Examples are &ldquo;50121&rdquo; and &ldquo;FI-50121&rdquo;.');
define('MODULE_ADVANCED_SHIPPER_ERROR_CUSTOMER_POSTCODE_PARSE_MY', 'Sorry! It is not possible to calculate the shipping rate to the selected address as the postcode, &ldquo;%s&rdquo;, is not in the correct format. <br /><br />Please go to &ldquo;View or Change Entries in my Address Book&rdquo; in your account and update this postcode so that the shipping rate can be determined for your order! <br /><br />Postcodes for Malaysia should be 5 digits in length. An example is &ldquo;50101&rdquo;.');
define('MODULE_ADVANCED_SHIPPER_ERROR_CUSTOMER_POSTCODE_PARSE_PL', 'Sorry! It is not possible to calculate the shipping rate to the selected address as the postcode, &ldquo;%s&rdquo;, is not in the correct format. <br /><br />Please go to &ldquo;View or Change Entries in my Address Book&rdquo; in your account and update this postcode so that the shipping rate can be determined for your order! <br /><br />Postcodes for Poland should be 5 digits in length, normally with a dash/hyphen separating the second and third digits. An example is &ldquo;31-962&rdquo;.');
define('MODULE_ADVANCED_SHIPPER_ERROR_CUSTOMER_POSTCODE_PARSE_PT', 'Sorry! It is not possible to calculate the shipping rate to the selected address as the postcode, &ldquo;%s&rdquo;, is not in the correct format. <br /><br />Please go to &ldquo;View or Change Entries in my Address Book&rdquo; in your account and update this postcode so that the shipping rate can be determined for your order! <br /><br />Postcodes for Portugal should have 4 digits at the beginning (the rest of the postcode is not necessary). An example is &ldquo;9101&rdquo;.');
define('MODULE_ADVANCED_SHIPPER_ERROR_CUSTOMER_POSTCODE_PARSE_SM', 'Sorry! It is not possible to calculate the shipping rate to the selected address as the postcode, &ldquo;%s&rdquo;, is not in the correct format. <br /><br />Please go to &ldquo;View or Change Entries in my Address Book&rdquo; in your account and update this postcode so that the shipping rate can be determined for your order! <br /><br />Postcodes for San Marino should be 5 digits in length. An example is &ldquo;50121&rdquo;.');
define('MODULE_ADVANCED_SHIPPER_ERROR_CUSTOMER_POSTCODE_PARSE_US', 'Sorry! It is not possible to calculate the shipping rate to the selected address as the ZIP code entered, &ldquo;%s&rdquo;, is not in the correct format. <br /><br />Please go to &ldquo;View or Change Entries in my Address Book&rdquo; in your account and update this ZIP code so that the shipping rate can be determined for your order! <br /><br />ZIP codes should be at least 5 digits long. An example is &ldquo;98102&rdquo;.');
define('MODULE_ADVANCED_SHIPPER_ERROR_CUSTOMER_POSTCODE_PARSE_VA', 'Sorry! It is not possible to calculate the shipping rate to the selected address as the postcode, &ldquo;%s&rdquo;, is not in the correct format. <br /><br />Please go to &ldquo;View or Change Entries in my Address Book&rdquo; in your account and update this postcode so that the shipping rate can be determined for your order! <br /><br />Postcodes for Vatican City should be 5 digits in length. An example is &ldquo;50121&rdquo;.');

/**
 * Module configuration error messages. These will only be displayed if the store owner has made a
 * mistake when configuring the module.
 */
define('MODULE_ADVANCED_SHIPPER_ERROR_NO_METHODS_FOR_PRODUCT', 'Module configuration error: Product in cart is not covered by any of the store\'s shipping rates: %s');
define('MODULE_ADVANCED_SHIPPER_ERROR_NO_RATES_DEFINED', 'Module configuration error: No rates have been defined for region %s!');
define('MODULE_ADVANCED_SHIPPER_ERROR_RANGE_METHOD', 'Module configuration error: Method for determining matching postcode range for country "%s" has not been defined! (Please create the method "_getRegionMatchingRange%s()")');
define('MODULE_ADVANCED_SHIPPER_ERROR_POSTCODE_PARSE', 'Module configuration error: Could not parse the postcode range "%s".');
define('MODULE_ADVANCED_SHIPPER_ERROR_GEOLOCATION_FUNCTIONS_MISSING', 'Module configuration error: No Geolocation functions found for country code "%s".');
define('MODULE_ADVANCED_SHIPPER_ERROR_GEOLOCATION_STORE_POSTCODE_INVALID', 'Module configuration error: The Store\'s Postcode is not valid!');
define('MODULE_ADVANCED_SHIPPER_ERROR_GEOLOCATION_STORE_POSTCODE_NOT_FOUND', 'Module configuration error: The Store\'s Postcode was not found in the Geolocation database!');
define('MODULE_ADVANCED_SHIPPER_ERROR_NO_UPS_CONFIG', 'Module configuration error: The UPS configuration has not been specified for method %s, region %s.');
define('MODULE_ADVANCED_SHIPPER_ERROR_NO_USPS_CONFIG', 'Module configuration error: The USPS configuration has not been specified for method %s, region %s.');

define('MODULE_ADVANCED_SHIPPER_TEXT_CATALOG_TITLE', 'Advanced Shipper');

// Admin text definitions
define('MODULE_ADVANCED_SHIPPER_TEXT_ADMIN_TITLE', 'Advanced Shipper v%s');
define('MODULE_ADVANCED_SHIPPER_TEXT_DESCRIPTION_BASE', '<fieldset style="background: #F7F6F0; margin-bottom: 1.5em; color: #000;">
	<legend style="font-size: 1.2em; font-weight: bold">About This Module</legend>
	<p>The Advanced Shipper shipping module allows you to create individual Shipping Methods for a selection of Products and/or Categories, each of which can have a table of rates specific to individual Regions. The rate for the region to be used is determined by the details for the applicable products; their total Weight, total Price or the Number of Items.</p>
</fieldset>');

?>
