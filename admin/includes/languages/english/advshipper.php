<?php

/**
 * advshipper Admin Language Definitions
 *
 * @author     Conor Kerr <zen-cart.advshipper@dev.ceon.net>
 * @copyright  Copyright 2007-2009 Ceon
 * @link       http://dev.ceon.net/web/zen-cart/advshipper
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: advshipper.php 382 2009-06-22 18:49:29Z Bob $
 */

define('HEADING_TITLE', 'Ceon Advanced Shipper');

define('TEXT_METHOD', 'Method');

define('TEXT_SHOW_METHOD', 'Show Method Summary & Other Controls');
define('TEXT_HIDE_METHOD', 'Hide Method Summary & Other Controls');
define('TEXT_EDIT_METHOD', 'Configure this method, its products/categories/manufacturers and regions.');
define('TEXT_ADD_METHOD', 'Add a new method after all other methods.');
define('TEXT_INSERT_METHODS_TITLE', 'Insert Method(s)');
define('TEXT_INSERT_METHODS', 'Insert methods before this method:');
define('TEXT_NUM_METHODS_TO_INSERT', 'Num methods to insert');
define('TEXT_INSERT_METHOD', 'Insert new method(s) immediately before this one.');
define('TEXT_COPY_METHOD_TITLE', 'Copy Method');
define('TEXT_COPY_METHODS', 'Copy this method\'s config, creating new method(s):');
define('TEXT_COPY_METHOD', 'Copy this method\'s config and create new method(s) with the same config.');
define('TEXT_NUM_METHODS_TO_COPY', 'Num copies to create');
define('TEXT_INSERT_AFTER_METHOD', 'Insert after method %s');
define('TEXT_DELETE_METHOD_TITLE', 'Delete Method');
define('TEXT_DELETE_METHOD', 'Delete this method and all its shipping methods.');
define('TEXT_JS_DELETE_CONFIRMATION', 'Are you sure you want to delete this method?');

define('TEXT_METHOD_TITLE', 'Method Title');
define('TEXT_LABEL_METHOD_TITLE', 'Method Title');

define('TEXT_CATEGORIES_MANUFACTURERS_PRODUCTS_SELECTION', 'Applicable Categories/Manufacturers/Products');
define('TEXT_FALLOVER_PRODUCTS', 'This method will be applied for <strong>all</strong> products (and therefore categories/manufacturers) which don\'t otherwise have a method defined for them (acting as as a &ldquo;fallover&rdquo;), as well as any specific categories/manufacturers/products indicated below.');
define('TEXT_LABEL_CATEGORIES', 'Categories:');
define('TEXT_LABEL_MANUFACTURERS', 'Manufacturers:');
define('TEXT_LABEL_PRODUCTS', 'Products:');
define('TEXT_ALL_PRODUCT_OPTIONS_SELECTED', ' -- All Options for Product');
define('TEXT_NO_CATEGORIES_MANUFACTURERS_PRODUCTS_SELECTIONS', 'No categories/manufacturers/products have been selected for this shipping method yet!');

define('TEXT_REGION', 'Region');
define('TEXT_REGIONS', 'Regions');
define('TEXT_LABEL_REGION_ADMIN_TITLE', 'Region Admin Title:');
define('TEXT_LABEL_REGION_TITLE', 'Region Title:');
define('TEXT_LABEL_TABLE_OF_RATES', 'Table of Rates:');
define('TEXT_REGION_HAS_NO_RATES', 'This region has no rates, so if it matches a customer\'s address, it will mean that <strong>this shipping method will not apply</strong> for that address.');
define('TEXT_NO_REGIONS_DEFINED', 'No regions have been defined for this shipping method yet!');

define('TEXT_LABEL_SURCHARGE', 'Surcharge:');
define('TEXT_REGION_HAS_NO_SURCHARGE', 'No surcharge');

define('TEXT_LABEL_COUNTRIES_POSTCODES', 'Countries/Postcodes:');
define('TEXT_LABEL_COUNTRIES_ZONES', 'Countries/Zones:');
define('TEXT_ALL_COUNTRIES', 'All Countries');
define('TEXT_ALL_ZONES', 'All Zones');
define('TEXT_LABEL_COUNTRIES_CITIES', 'Countries/Cities:');
define('TEXT_LABEL_COUNTRIES_STATES', 'Countries/States:');
define('TEXT_LABEL_DISTANCE', 'Distance From Store:');

define('TEXT_DISPLAY_NUMBER_OF_METHODS', 'Displaying <strong>%s</strong> to <strong>%s</strong> (of <strong>%s</strong> shipping methods)');

define('SUCCESS_CONFIGURATION_ADDED', 'Configuration for &ldquo;%s&rdquo; was added successfully!');
define('SUCCESS_METHOD_INSERTED', 'New shipping method added and configuration saved for &ldquo;%s&rdquo;!');
define('SUCCESS_METHODS_INSERTED', '%s new shipping methods added and configuration saved for &ldquo;%s&rdquo;!');
define('SUCCESS_METHOD_COPIED_ONCE', 'Shipping Method %s has been copied! The copy has been inserted after shipping method %s.');
define('SUCCESS_METHOD_COPIED_MULTIPLE_TIMES', 'Shipping Method %s has been copied %s times! The copies have been inserted after shipping method %s.');
define('SUCCESS_METHOD_DELETED', 'Shipping Method %s deleted!');

define('SUCCESS_METHOD_INSERTED_DEMO', '[DEMO MODE] New shipping method would have been added and configuration saved for &ldquo;%s&rdquo; if module was not in demo mode!');
define('SUCCESS_METHODS_INSERTED_DEMO', '[DEMO MODE] %s new shipping methods would have been added and configuration saved for &ldquo;%s&rdquo; if module was not in demo mode!');
define('SUCCESS_METHOD_COPIED_ONCE_DEMO', '[DEMO MODE] Shipping Method %s would have been copied if module was not in demo mode! The copy would have been inserted after shipping method %s.');
define('SUCCESS_METHOD_COPIED_MULTIPLE_TIMES_DEMO', '[DEMO MODE] Shipping Method %s would have been copied %s times if module was not in demo mode! The copies would have been inserted after shipping method %s.');
define('SUCCESS_METHOD_DELETED_DEMO', '[DEMO MODE] Shipping Method %s would have been deleted if module was not in demo mode!');

define('SUCCESS_DATABASE_UPDATED', 'Database was updated successfully!');

?>