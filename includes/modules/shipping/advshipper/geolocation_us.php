<?php

/**
 * Advanced Shipper Geolocation Function for the United States (US)
 *
 * @author     Conor Kerr <zen-cart.advshipper@dev.ceon.net>
 * @copyright  Copyright 2009 Ceon
 * @copyright  Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright  Portions Copyright 2003 osCommerce
 * @link       http://dev.ceon.net/web/zen-cart/advshipper
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: geolocation_us.php 382 2009-06-22 18:49:29Z Bob $
 */


// {{{ advshipper_getDistanceUSUS()

/**
 * Function uses database to look up get the distance between two US zipcodes.
 *
 * @author     Conor Kerr <zen-cart.advshipper@dev.ceon.net>
 * @copyright  Copyright 2009 Ceon
 * @copyright  Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright  Portions Copyright 2003 osCommerce
 * @link       http://dev.ceon.net/web/zen-cart/advshipper
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @access     public
 * @param      string   $store_zipcode   The store's zipcode.
 * @param      string   $dest_zipcode    The destiniation's zipcode.
 * @return     string|integer   The distance (in miles) between the two zipcodes, an error message  
 *                              if an error occurred, the integer -1 if the customer's zipcode
 *                              is in an invalid format or the integer -2 if the customer's zipcode
 *                              could not be matched in the geolocation database.
 */
function advshipper_getDistanceUSUS($store_zipcode, $dest_zipcode)
{
	global $db;
	
	$store_zipcode = strtolower(preg_replace('/\s+/', '', $store_zipcode));
	
	// Check the zipcode is in the correct format
	// US zipcodes are 5 digits in length
	if (!preg_match('/(^[0-9][0-9][0-9][0-9][0-9])/', $store_zipcode)) {
		// Store's zipcode is invalid!
		return MODULE_ADVANCED_SHIPPER_ERROR_GEOLOCATION_STORE_POSTCODE_INVALID;
	}
	
	$dest_zipcode = strtolower(preg_replace('/\s+/', '', $dest_zipcode));
	
	// Check the zipcode is in the correct format
	// US zipcodes are 5 digits in length
	if (!preg_match('/(^[0-9][0-9][0-9][0-9][0-9])/', $dest_zipcode)) {
		// Postcode is not in the correct format
		return -1;
	}
	
	// Get the grid reference for the store
	$store_zipcode_query = "
		SELECT
			latitude, longitude
		FROM
			" . DB_PREFIX . "us_zipcodes 
		WHERE
			zipcode <= '" . $store_zipcode . "'
		ORDER BY
			zipcode DESC
		LIMIT 1;";
	
	$store_zipcode_result = $db->Execute($store_zipcode_query);
	
	if ($store_zipcode_result->EOF) {
		// Couldn't identify the store's zipcode
		return MODULE_ADVANCED_SHIPPER_ERROR_GEOLOCATION_STORE_POSTCODE_NOT_FOUND;
	}
	
	$source_lat = $store_zipcode_result->fields['latitude'];
	$source_lon = $store_zipcode_result->fields['longitude'];
	
	// Get the grid reference for the customer's address
	$dest_zipcode_query = "
		SELECT
			latitude, longitude
		FROM
			" . DB_PREFIX . "us_zipcodes 
		WHERE
			zipcode <= '" . $dest_zipcode . "'
		ORDER BY
			zipcode DESC
		LIMIT 1;";
	
	$dest_zipcode_result = $db->Execute($dest_zipcode_query);
	
	if ($dest_zipcode_result->EOF) {
		// Couldn't get the co-ordinates for the destination zipcode
		return -2;
	}
	
	// Work out the distance between the store's zipcode and the customer's zipcode
	$dest_lat = $dest_zipcode_result->fields['latitude'];
	$dest_lon = $dest_zipcode_result->fields['longitude'];
	
	$lon_difference = $source_lon - $dest_lon;
	$distance = (sin(deg2rad($source_lat)) * sin(deg2rad($dest_lat))) +
		(cos(deg2rad($source_lat)) * cos(deg2rad($dest_lat)) * cos(deg2rad($lon_difference)));
	$distance = acos($distance);
	$distance = rad2deg($distance);
	$miles_per_lat = 69.04; // Miles per degree latitude constant
	$distance = $distance * $miles_per_lat;
	
	return $distance;
}

// }}}

?>