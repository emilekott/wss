<?php

#The following software is distributed free of charge using the BSD Licence:
#
#
# Copyright (c) 2008, Corra Communications
# All rights reserved.
#
# Redistribution and use in source and binary forms, with or without
# modification, are permitted provided that the following conditions are met:
#     * Redistributions of source code must retain the above copyright
#       notice, this list of conditions and the following disclaimer.
#     * Redistributions in binary form must reproduce the above copyright
#       notice, this list of conditions and the following disclaimer in the
#       documentation and/or other materials provided with the distribution.
#     * Neither the name of the organization nor the
#       names of its contributors may be used to endorse or promote products
#       derived from this software without specific prior written permission.
#
# THIS SOFTWARE IS PROVIDED BY CORRA COMMUNICATIONS ``AS IS'' AND ANY
# EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
# WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
# DISCLAIMED. IN NO EVENT SHALL CORRA COMMUNICATIONS BE LIABLE FOR ANY
# DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
# (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
# LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
# ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
# (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
# SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.


/**
 * Advanced Shipper Geolocation Function for Australia (AU)
 *
 * @author     Conor Kerr <zen-cart.advshipper@dev.ceon.net>
 * @copyright  Copyright 2007-2009 Ceon
 * @copyright  Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright  Portions Copyright 2003 osCommerce
 * @link       http://dev.ceon.net/web/zen-cart/advshipper
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: geolocation_au.php 382 2009-06-22 18:49:29Z Bob $
 */


// {{{ advshipper_getDistanceAUAU()

/**
 * Function uses database to look up get the distance between two Australian postcodes.
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
 * @return     string|integer   The distance (in km) between the two postcodes, an error message if 
 *                              an error occurred, the integer -1 if the customer's postcode
 *                              is in an invalid format or the integer -2 if the customer's postcode
 *                              could not be matched in the geolocation database.
 */
function advshipper_getDistanceAUAU($store_postcode, $dest_postcode)
{
	global $db;
	
	$store_postcode = strtolower(preg_replace('/\s+/', '', $store_postcode));
	
	// Check the postcode is in the correct format
	// Australian postcodes are 4 digits in length in general but some are only 3 digits
	if (!preg_match('/(^[0-9][0-9][0-9][0-9])|(^[8-9][0-9][0-9])|(^[0-2][0-2][0-9])/',
			$store_postcode)) {
		// Store's postcode is invalid!
		return MODULE_ADVANCED_SHIPPER_ERROR_GEOLOCATION_STORE_POSTCODE_INVALID;
	}
	
	$dest_postcode = strtolower(preg_replace('/\s+/', '', $dest_postcode));
	
	// Check the postcode is in the correct format
	// Australian postcodes are 4 digits in length in general but some are only 3 digits
	if (!preg_match('/(^[0-9][0-9][0-9][0-9])|(^[8-9][0-9][0-9])|(^[0-2][0-2][0-9])/',
			$dest_postcode)) {
		// Postcode is not in the correct format
		return -1;
	}
	
	// Get the grid reference for the store
	$store_postcode_query = "
		SELECT
			lat, lon
		FROM
			" . DB_PREFIX . "au_postcodes 
		WHERE
			postcode = '" . $store_postcode . "';";
	
	$store_postcode_result = $db->Execute($store_postcode_query);
	
	if ($store_postcode_result->EOF) {
		// Couldn't identify the store's postcode
		return MODULE_ADVANCED_SHIPPER_ERROR_GEOLOCATION_STORE_POSTCODE_NOT_FOUND;
	}
	
	$source_lat = $store_postcode_result->fields['lat'];
	$source_lon = $store_postcode_result->fields['lon'];
	
	// Get the grid reference for the customer's address
	$dest_postcode_query = "
		SELECT
			lat, lon
		FROM
			" . DB_PREFIX . "au_postcodes 
		WHERE
			postcode = '" . $dest_postcode . "';";
	
	$dest_postcode_result = $db->Execute($dest_postcode_query);
	
	if ($dest_postcode_result->EOF) {
		// Couldn't get the co-ordinates for the destination postcode
		return -2;
	}
	
	// Work out the distance between the store's postcode and the customer's postcode
	$dest_lat = $dest_postcode_result->fields['lat'];
	$dest_lon = $dest_postcode_result->fields['lon'];
	
	$lon_difference = $source_lon - $dest_lon;
	$distance = (sin(deg2rad($source_lat)) * sin(deg2rad($dest_lat))) +
		(cos(deg2rad($source_lat)) * cos(deg2rad($dest_lat)) * cos(deg2rad($lon_difference)));
	$distance = acos($distance);
	$distance = rad2deg($distance);
	$km_per_lat = 111.325; // Kilometers per degree latitude constant
	$distance = $distance * $km_per_lat;
	
	return $distance;
}

// }}}

?>