<?php

/**
 * advshipper Admin Language Definitions
 *
 * @author     Conor Kerr <zen-cart.advshipper@dev.ceon.net>
 * @copyright  Copyright 2007-2009 Ceon
 * @link       http://dev.ceon.net/web/zen-cart/advshipper
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: advshipper_method_config.php 382 2009-06-22 18:49:29Z Bob $
 */

define('HEADING_TITLE', 'Ceon Advanced Shipper - Method Configuration');


define('TEXT_METHOD', 'Method');
define('TEXT_METHOD_TITLES', 'Method Titles');
define('TEXT_LABEL_METHOD_ADMIN_TITLE', 'Method Admin Title:');
define('TEXT_CONFIG_DESC_METHOD_ADMIN_TITLE', 'Set or change the title to be used in the Admin to identify this method by entering the desired title for the language(s) below.');
define('TEXT_LABEL_METHOD_TITLE', 'Method Title:');
define('TEXT_CONFIG_DESC_METHOD_TITLE', 'Set or change the title for this method that will be displayed to the customer by entering the desired title for the language(s) below. <br /><br />Placement markers can be used to add dynamic information (e.g. {method_total}, {rate_calc_desc}, {surcharge_info}, or {region_title} for the title of the region being used). It can also have time placement markers if the method uses shipping scheduling. See the docs for more info about placement markers.');

if (!defined('TEXT_YES')) { define('TEXT_YES', 'Yes'); }
if (!defined('TEXT_NO')) { define('TEXT_NO', 'No'); }

define('TEXT_CATEGORY_PRODUCT_SELECTION', 'Applicable Categories/Manufacturers/Products');
define('TEXT_LABEL_PRODUCTS_CATEGORIES_MANUFACTURERS', 'Categories/Manufacturers/Products:');
define('TEXT_CONFIG_DESC_PRODUCTS_CATEGORIES_MANUFACTURERS', 'This method can apply for all products which don\'t otherwise have a method defined (acting as a &ldquo;Fallover&rdquo;), or specific categories and/or manufacturers and/or products to which this method applies can be selected.');
define('TEXT_SELECT_FALLOVER_PRODUCTS', 'Apply this method for <strong>all</strong> products (and therefore categories/manufacturers) which don\'t otherwise have a method defined for them, as well as any specific categories/manufacturers/products selected below.');
define('TEXT_SELECT_SPECIFIC_CATEGORIES_MANUFACTURERS_PRODUCTS', 'Apply this method for the specific categories and/or manufacturers and/or products selected below <strong>only</strong>.');
define('TEXT_LABEL_CATEGORIES', 'Categories:');
define('TEXT_CONFIG_DESC_CATEGORIES', 'This is a list of specific Categories to which this method applies.');
define('TEXT_CURRENT_CATEGORIES', 'Current Categories');
define('JS_TEXT_CATEGORY_IN_LIST_SELECTED', 'The selected category is already in the list of categories \nto which this method applies!');
define('JS_TEXT_CATEGORY_IN_LIST_SINGLE', 'One of the selected categories is already in the list of categories \nto which this method applies:');
define('JS_TEXT_CATEGORIES_IN_LIST_ALL', 'The selected categories are already in the list of categories \nto which this method applies!');
define('JS_TEXT_CATEGORIES_IN_LIST', 'Some of the selected categories are already in the list of categories \nto which this method applies:');
define('TEXT_LABEL_MANUFACTURERS', 'Manufacturers:');
define('TEXT_CONFIG_DESC_MANUFACTURERS', 'This is a list of specific Manufacturers to which this method applies.');
define('TEXT_CURRENT_MANUFACTURERS', 'Current Manufacturers');
define('JS_TEXT_MANUFACTURER_IN_LIST_SELECTED', 'The selected manufacturer is already in the list of manufacturers \nto which this method applies!');
define('JS_TEXT_MANUFACTURER_IN_LIST_SINGLE', 'One of the selected manufacturers is already in the list of manufacturers \nto which this method applies:');
define('JS_TEXT_MANUFACTURERS_IN_LIST_ALL', 'The selected manufacturers are already in the list of manufacturers \nto which this method applies!');
define('JS_TEXT_MANUFACTURERS_IN_LIST', 'Some of the selected manufacturers are already in the list of manufacturers \nto which this method applies:');
define('TEXT_LABEL_PRODUCTS', 'Products:');
define('TEXT_CONFIG_DESC_PRODUCTS', 'This is a list of specific Products to which this method applies.');
define('TEXT_CURRENT_PRODUCTS', 'Current Products');
define('TEXT_ALL_PRODUCT_OPTIONS_SELECTED', ' -- All Options for Product');
define('JS_TEXT_PRODUCT_IN_LIST', 'The selected product is already in the list of products \nto which this method applies!');
define('JS_TEXT_PRODUCT_AND_ATTRIBUTES_IN_LIST', 'The selected product and options are already included in \nthe list of products to which this method applies!');

define('TEXT_REGIONS_CONFIGURATION', 'Regions &amp; Rates');
define('TEXT_LABEL_REGIONS_AND_RATES', 'Regions &amp; Rates:');
define('TEXT_NO_REGIONS_DEFINED', 'No Regions have been defined for this shipping method.');
define('TEXT_LABEL_REGIONS', 'Regions:');
define('TEXT_CURRENT_REGIONS', 'Current Regions &amp; Rates');
define('TEXT_REGION', 'Region');
define('TEXT_TABLE_OF_RATES', 'Table Of Rates');
define('TEXT_REGION_HAS_NO_RATES', 'This region has no rates, so if it matches a customer\'s address, it will mean that this shipping method will not apply for that address.');
define('TEXT_SURCHARGE', 'Surcharge');
define('TEXT_DEFINITION_METHOD', 'Definition Method');
define('TEXT_ADDRESS_MATCHING', 'Address Matching');
define('TEXT_LABEL_COUNTRIES_POSTCODES', 'Countries/Postcodes:');
define('TEXT_LABEL_COUNTRIES_ZONES', 'Countries/Zones:');
define('TEXT_ALL_COUNTRIES', 'All Countries');
define('TEXT_ALL_ZONES', 'All Zones');
define('TEXT_LABEL_COUNTRIES_STATES', 'Countries/States:');
define('TEXT_LABEL_COUNTRIES_CITIES', 'Countries/Cities:');
define('TEXT_GEOLOCATION', 'Geolocation');
define('TEXT_LABEL_DISTANCE', 'Distance From Store:');
define('TEXT_EDIT_REGION', 'Edit Region');
define('TEXT_INSERT_REGION', 'Insert Region');
define('TEXT_DELETE_REGION', 'Delete Region');
define('TEXT_JS_DELETE_CONFIRMATION', 'Are you sure you want to delete this region?');
define('TEXT_REGION_ORDERING', 'Region Ordering');
define('TEXT_MOVE_REGION_UP', 'Move Up');
define('TEXT_MOVE_REGION_DOWN', 'Move Down');

define('TEXT_METHOD_AVAILABILITY_SCHEDULING', 'Method Availability Scheduling');
define('TEXT_LABEL_METHOD_AVAILABILITY_SCHEDULING', 'Method Availability Scheduling:');
define('TEXT_CONFIG_DESC_METHOD_AVAILABILITY_SCHEDULING', 'Should this method always be available as a shipping option or should it be available for a particular time period only?');
define('TEXT_METHOD_AVAILABILITY_SCHEDULING_ALWAYS', 'Always Available');
define('TEXT_METHOD_AVAILABILITY_SCHEDULING_ONCE_ONLY', 'Once Only (Available for a certain time period)');
define('TEXT_METHOD_AVAILABILITY_SCHEDULING_RECURRING', 'Recurring (Available for regular time periods, e.g. Mon-Fri only)');

define('TEXT_LABEL_METHOD_ONCE_ONLY_START_DATE', 'Once Only Start Date/Time:');
define('TEXT_CONFIG_DESC_METHOD_ONCE_ONLY_START_DATE', 'This is the date/time at which this method will become available as a shipping option. If not entered, the method will be available immediately.');
define('TEXT_DATE_FORMAT', '(Format: ' . strtoupper(DATE_FORMAT_SPIFFYCAL) . ')');
define('TEXT_TIME_FORMAT', '(Format: HH:MM - 24 Hour)');
define('TEXT_LABEL_METHOD_ONCE_ONLY_END_DATE', 'Once Only End Date/Time:');
define('TEXT_CONFIG_DESC_METHOD_ONCE_ONLY_END_DATE', 'This is the date/time at which this method will stop being available as a shipping option.');

define('TEXT_LABEL_METHOD_AVAILABILITY_RECURRING_MODE', 'Recurring Mode:');
define('TEXT_CONFIG_DESC_METHOD_AVAILABILITY_RECURRING_MODE', 'This is the manner in which the availablity of the method will recur.');
define('TEXT_METHOD_AVAILABILITY_RECURRING_MODE_WEEKLY', 'Weekly');
define('TEXT_LABEL_METHOD_AVAILABILITY_WEEKLY_START_DAY_AND_TIME', 'Weekly Start Day/Time:');
define('TEXT_CONFIG_DESC_METHOD_AVAILABILITY_WEEKLY_START_DAY_AND_TIME', 'If this method should only be available from a particular day and time each week, select the day and enter the time here. If no day and time is specified here then once the cut off day/time has been reached for the current week, this method will be repeated as an option for the following week.');
define('TEXT_LABEL_METHOD_AVAILABILITY_WEEKLY_CUTOFF_DAY_AND_TIME', 'Weekly Cut-off Day/Time:');
define('TEXT_CONFIG_DESC_METHOD_AVAILABILITY_WEEKLY_CUTOFF_DAY_AND_TIME', 'This is the day of the week and the time on that day that this method will no longer be an option for the customer upon checkout. If no start day and time is specified above then this shipping method will recur at this day and time (for the next week).');

define('TEXT_LABEL_METHOD_USAGE_LIMIT', 'Method Usage Limit:');
define('TEXT_CONFIG_DESC_METHOD_USAGE_LIMIT', 'If this method should be disabled once a maximum number of orders have been made using it in the given time period, enter the limit here.');

define('TEXT_METHOD_SHIPPING_SCHEDULING', 'Method Shipping Scheduling');
define('TEXT_LABEL_METHOD_SHIPPING_SCHEDULING', 'Method Shipping Scheduling:');
define('TEXT_LABEL_METHOD_ONCE_ONLY_SHIPPING_DATE', 'Shipping Date/Time:');
define('TEXT_CONFIG_DESC_METHOD_ONCE_ONLY_SHIPPING_DATE', 'This is the date/time when shipping for this method is expected to take place. <br /><br />If entered, the shipping date will be recorded alongside the standard details for the order and information about the shipping date can be displayed in the method\'s title using the PHP function strftime\'s standard conversion specifiers (E.g. <code>%a Shipping (%d %b from %H:%M)</code> ). If not entered, information about the shipping date cannot be made available to the customer (which is perfectly fine).');

define('TEXT_LABEL_METHOD_AVAILABILITY_WEEKLY_SHIPPING_SCHEDULING', 'Method Shipping Scheduling:');
define('TEXT_CONFIG_DESC_METHOD_AVAILABILITY_WEEKLY_SHIPPING_SCHEDULING', 'This setting can be used to select a day/time when the customer\'s order will be shipped if they use this shipping method. It can also be used to enable customers to be able to select an instance of this shipping method in the future and therefore select from a range of shipping dates.<br /><br />If used, the shipping date will be recorded alongside the standard details for the order and information about the shipping date can be displayed in the method\'s title using the PHP function strftime\'s standard conversion specifiers (E.g. <code>%a Shipping (%d %b from %H:%M)</code> ). If not used, information about the shipping date cannot be made available to the customer (which is perfectly fine).<br /><br />A Cut-off Day/Time must be specified above for an option to become available!');
define('TEXT_METHOD_AVAILABILITY_WEEKLY_SHIPPING_SCHEDULING_NONE', 'No Shipping Scheduling');
define('TEXT_METHOD_AVAILABILITY_WEEKLY_SHIPPING_SCHEDULING_REGULAR_WEEKDAY', 'Shipping On A Regular Weekday');
define('TEXT_LABEL_METHOD_AVAILABILITY_WEEKLY_SHIPPING_SHOW_NUM_WEEKS', 'Number of Weeks to Show:');
define('TEXT_CONFIG_DESC_METHOD_AVAILABILITY_WEEKLY_SHIPPING_SHOW_NUM_WEEKS', 'This is the number of weeks in advance to show this method as being available for. Showing several weeks in advance lets customers select a shipping date further than a week in the future, if so desired.');
define('TEXT_LABEL_METHOD_AVAILABILITY_WEEKLY_SHIPPING_REGULAR_WEEKDAY_DAY_AND_TIME', 'Weekly Shipping Day/Time:');
define('TEXT_CONFIG_DESC_METHOD_AVAILABILITY_WEEKLY_SHIPPING_REGULAR_WEEKDAY_DAY_AND_TIME', 'This is the day of the week and optional time on that day that the customer\'s order will be shipped when using this method.');


define('TEXT_MONDAY', 'Monday');
define('TEXT_TUESDAY', 'Tuesday');
define('TEXT_WEDNESDAY', 'Wednesday');
define('TEXT_THURSDAY', 'Thursday');
define('TEXT_FRIDAY', 'Friday');
define('TEXT_SATURDAY', 'Saturday');
define('TEXT_SUNDAY', 'Sunday');

define('TEXT_ERROR_IN_CONFIG', 'A problem was found with the configuration, please fix the error highlighted below then try again to save the changes.');
define('TEXT_ERRORS_IN_CONFIG', '%s problems were found with the configuration, please fix the errors highlighted below then try again to save the changes.');

define('ERROR_TITLE_MISSING', 'The title for this method must be entered!');
define('ERROR_TITLE_FOR_LANGUAGE_MISSING', 'The title for this method must be entered for this language!');
define('ERROR_DATE_FORMAT', 'Can\'t use calendar\'s date format (%s)!');
define('ERROR_TIME_FORMAT', 'The time entered is invalid!');
define('ERROR_WEEKLY_CUTOFF_NOT_SPECIFIED', 'A weekly start date/time has been specified so a weekly cutoff must also be specified!');
define('ERROR_SHOW_NUM_WEEKS_INVALID', 'The number of weeks entered is invalid!');
define('ERROR_WEEKLY_SHIPPING_DAY_NOT_SPECIFIED', 'Shipping on a regular weekday has been selected but the day/time has not been specified!');

define('SUCCESS_CONFIGURATION_SAVED', 'Method %s configuration for &ldquo;%s&rdquo; was successfully saved!');
define('SUCCESS_CONFIGURATION_SAVED_DEMO', '[DEMO MODE] Method %s configuration for &ldquo;%s&rdquo; would have been saved if module was not in demo mode!');

?>