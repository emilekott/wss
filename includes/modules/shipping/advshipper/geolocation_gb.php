<?php

/**
 * Advanced Shipper Geolocation Function for UK (GB)
 *
 * @author     Conor Kerr <zen-cart.advshipper@dev.ceon.net>
 * @copyright  Copyright 2007-2009 Ceon
 * @copyright  Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright  Portions Copyright 2003 osCommerce
 * @link       http://dev.ceon.net/web/zen-cart/advshipper
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: geolocation_gb.php 382 2009-06-22 18:49:29Z Bob $
 */


// {{{ advshipper_getDistanceGBGB()

/**
 * Function uses database to look up get the distance between two UK postcodes, based on the
 * Outbound part of the postcodes..
 *
 * @author     Conor Kerr <zen-cart.advshipper@dev.ceon.net>
 * @copyright  Copyright 2007-2009 Ceon
 * @copyright  Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright  Portions Copyright 2003 osCommerce
 * @link       http://dev.ceon.net/web/zen-cart/advshipper
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @access     public
 * @param      string   $store_postcode   The store's postcode.
 * @param      string   $dest_postcode    The destiniation's postcode.
 * @return     string|integer   The distance (in miles) between the two postcodes, an error message  
 *                              if an error occurred, the integer -1 if the customer's postcode
 *                              is in an invalid format or the integer -2 if the customer's postcode
 *                              could not be matched in the geolocation database.
 */
function advshipper_getDistanceGBGB($store_postcode, $dest_postcode)
{
	global $db;
	
	$store_postcode = strtolower(preg_replace('/\s+/', '', $store_postcode));
	
	// Check the postcode is in the correct format
	// UK postcodes must be of the format X9 9XX, X99 9XX, X9X 9XX, XX9 9XX, XX99 9XX or
	// XX9X 9XX (whitespace should already have been stripped)
	if (!ereg('^[a-z][0-9][0-9][a-z][a-z]$', $store_postcode) &&
		!ereg('^[a-z][0-9][0-9][0-9][a-z][a-z]$', $store_postcode) &&
		!ereg('^[a-z][0-9][a-z][0-9][a-z][a-z]$', $store_postcode) &&
		!ereg('^[a-z][a-z][0-9][0-9][a-z][a-z]$', $store_postcode) &&
		!ereg('^[a-z][a-z][0-9][0-9][0-9][a-z][a-z]$', $store_postcode) &&
		!ereg('^[a-z][a-z][0-9][a-z][0-9][a-z][a-z]$', $store_postcode)) {
		// Store's postcode is invalid!
		return MODULE_ADVANCED_SHIPPER_ERROR_GEOLOCATION_STORE_POSTCODE_INVALID;
	}
	// Get the outbound part of the code and convert it to uppercase (to match the db's format)
	$store_postcode = strtoupper(substr($store_postcode, 0, strlen($store_postcode) - 3));
	

	$dest_postcode = strtolower(preg_replace('/\s+/', '', $dest_postcode));
	
	// Check the postcode is in the correct format
	// UK postcodes must be of the format X9 9XX, X99 9XX, X9X 9XX, XX9 9XX, XX99 9XX or
	// XX9X 9XX (whitespace should already have been stripped)
	if (!ereg('^[a-z][0-9][0-9][a-z][a-z]$', $dest_postcode) &&
		!ereg('^[a-z][0-9][0-9][0-9][a-z][a-z]$', $dest_postcode) &&
		!ereg('^[a-z][0-9][a-z][0-9][a-z][a-z]$', $dest_postcode) &&
		!ereg('^[a-z][a-z][0-9][0-9][a-z][a-z]$', $dest_postcode) &&
		!ereg('^[a-z][a-z][0-9][0-9][0-9][a-z][a-z]$', $dest_postcode) &&
		!ereg('^[a-z][a-z][0-9][a-z][0-9][a-z][a-z]$', $dest_postcode)) {
		// Customer's postcode is invalid!
		return -1;
	}
	// Get the outbound part of the code and convert it to uppercase (to match the db's format)
	$dest_postcode = strtoupper(substr($dest_postcode, 0, strlen($dest_postcode) - 3));
	
	// Get the grid reference for the store
	$store_postcode_query = "
		SELECT
			grid_e, grid_n
		FROM
			" . DB_PREFIX . "uk_postcodes 
		WHERE
			postcode = '" . $store_postcode . "';";
	
	$store_postcode_result = $db->Execute($store_postcode_query);
	
	if ($store_postcode_result->EOF) {
		// Couldn't identify the store's postcode
		return MODULE_ADVANCED_SHIPPER_ERROR_GEOLOCATION_STORE_POSTCODE_NOT_FOUND;
	}
	
	$source_grid_e = $store_postcode_result->fields['grid_e'];
	$source_grid_n = $store_postcode_result->fields['grid_n'];
	
	// Get the grid reference for the customer's address
	$dest_postcode_query = "
		SELECT
			grid_e, grid_n
		FROM
			" . DB_PREFIX . "uk_postcodes 
		WHERE
			postcode = '" . $dest_postcode . "';";
	
	$dest_postcode_result = $db->Execute($dest_postcode_query);
	
	if ($dest_postcode_result->EOF) {
		// Couldn't get the co-ordinates for the destination postcode
		return -2;
	}
	
	// Work out the distance between the store's postcode and the customer's postcode
	$dest_grid_e = $dest_postcode_result->fields['grid_e'];
	$dest_grid_n = $dest_postcode_result->fields['grid_n'];
	
	$distance_e = $source_grid_e - $dest_grid_e;
	if ($distance_e < 0) {
		$distance_e = $distance_e * -1;
	}
	
	$distance_n = $source_grid_n - $dest_grid_n;
	if ($distance_n < 0) {
		$distance_n = $distance_n * -1;
	}
	
	$distance = sqrt(($distance_e * $distance_e) + ($distance_n * $distance_n));
	
	return $distance / 1609;
}

// }}}

?>