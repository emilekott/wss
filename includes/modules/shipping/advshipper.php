<?php

/**
 * advshipper
 *
 * @author     Conor Kerr <zen-cart.advshipper@dev.ceon.net>
 * @copyright  Copyright 2007-2009 Ceon
 * @copyright  Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright  Portions Copyright 2003 osCommerce
 * @link       http://dev.ceon.net/web/zen-cart/advshipper
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: advshipper.php 382 2009-06-22 18:49:29Z Bob $
 */

/**
 * Version definition, don't touch!
 */
define('MODULE_ADVANCED_SHIPPER_VERSION_NO', '3.8.1');

require_once(DIR_FS_CATALOG . DIR_WS_FUNCTIONS . 'advshipper.php');

if (file_exists(DIR_FS_CATALOG . DIR_WS_FUNCTIONS . 'advshipper_zones.php')) {
	require_once(DIR_FS_CATALOG . DIR_WS_FUNCTIONS . 'advshipper_zones.php');
}

// {{{ advshipper

/**
 * Shipping Module conforming to Zen Cart format which allows specifying a table of rates for any
 * number of regions for a particular shipping method, with the ability to limit the availability
 * of the shipping method based on category and/or manufacturer and/or product selections
 * and/or the current date/time.
 *
 * @author     Conor Kerr <zen-cart.advshipper@dev.ceon.net>
 * @copyright  Copyright 2007-2009 Ceon
 * @copyright  Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright  Portions Copyright 2003 osCommerce
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 */
class advshipper
{
	// {{{ properties
	
	/**
	 * The internal 'code' name used to designate "this" shipping module.
	 *
	 * @var     string
	 * @access  public
	 */
	var $code;
	
	/**
	 * The version of this module.
	 *
	 * @var     string
	 * @access  public
	 */
	var $version;
	
	/**
	 * The name displayed for this shipping method.
	 *
	 * @var string
	 * @access  public
	 */
	var $title;
	
	/**
	 * The description displayed for this shipping method.
	 *
	 * @var string
	 * @access  public
	 */
	var $description;
	
	/**
	 * Module status - set based on various config and zone criteria.
	 *
	 * @var     boolean
	 * @access  public
	 */
	var $enabled;
	
	/**
	 * The sort order of display for this module within the checkout's shipping method listing.
	 *
	 * @var     integer
	 * @access  public
	 */
	var $sort_order;
	
	/**
	 * The tax basis to be used for this shipping module.
	 *
	 * @var     integer
	 * @access  public
	 */
	var $tax_basis = 'Shipping';
	
	/**
	 * The tax class to be used for this shipping module.
	 *
	 * @var     integer
	 * @access  public
	 */
	var $tax_class = 0;
	
	/**
	 * The number of hours to adjust the server's time by (if any).
	 *
	 * @var     integer
	 * @access  protected
	 */
	var $_time_adjust;
	
	// }}}
	
	
	// {{{ Class Constructor
	
	/**
	 * Create a new instance of the advshipper class
	 */
	function advshipper()
	{
		global $db;
		
		$this->code = 'advshipper';
		$this->version = MODULE_ADVANCED_SHIPPER_VERSION_NO;
		
		// Perform error checking of module's configuration ////////////////////////////////////////
		
		// Variable holds status of configuration checks so that module can be disabled if it cannot
		// perform its function
		$critical_config_problem = false;
		
		$advshipper_config_messages = '';
		
		// Output warning if database tables don't exist
		$table_exists_query = 'SHOW TABLES LIKE "' . TABLE_ADVANCED_SHIPPER_CONFIGS . '";';
		$table_exists_result = $db->Execute($table_exists_query);
		
		if ($table_exists_result->EOF) {
			// Database tables don't exist
			$critical_config_problem = true;
			
			$advshipper_config_messages .= '<strong><span style="color: red">Warning:</span><br />The Advanced Shipper Database Tables Do Not Exist!</strong><br /><br /><strong><span style="color: red">Please create the database tables, according to the installation instructions!</span></strong><br /><br /><br />';
		}
		
		// Check the database version of the module against the version of the files
		$advshipper_config_messages .= '<fieldset style="background: #F7F6F0; margin-bottom: 1.5em"><legend style="font-size: 1.2em; font-weight: bold">Module Version Information</legend>';
		if (defined('MODULE_ADVANCED_SHIPPER_MADE_BY_CEON')) {
			$advshipper_database_version = MODULE_ADVANCED_SHIPPER_MADE_BY_CEON;
		} else {
			$advshipper_database_version = null;
		}
		if ($advshipper_database_version != MODULE_ADVANCED_SHIPPER_VERSION_NO
				&& (!is_null($advshipper_database_version))) {
			// Database version doesn't match expected version
			$critical_config_problem = true;
			
			$advshipper_config_messages .= '<strong><span style="color: red">Warning:</span><br />Database Must Be Upgraded!</strong><br /><br /><strong><span style="color: red">Please go to <em>Modules &gt; Advanced Shipper Config</em> in the Admin to have the database upgraded automatically!</span></strong><br />';
		}
		
		$advshipper_config_messages .= '<p>File Version: ' . $this->version;
		$advshipper_config_messages .= '<br />Database Version: ' .
			(is_null($advshipper_database_version) ? 'Module Not Installed' :
			$advshipper_database_version) . '</p>';
		$advshipper_config_messages .=
			'<p><a href="http://dev.ceon.net/web/zen-cart/advshipper/version_checker/' .
			$this->version . '" target="_blank">Check for updates</a></p></fieldset>';
		
		// Set the title and description based on the mode the module is in: Admin or Catalog
		if ((defined('IS_ADMIN_FLAG') && IS_ADMIN_FLAG === true) ||
				(!isset($_GET['main_page']) || $_GET['main_page'] == '')) {
			// In Admin mode
			$this->title = sprintf(MODULE_ADVANCED_SHIPPER_TEXT_ADMIN_TITLE, $this->version);
			$this->description = $advshipper_config_messages .
				MODULE_ADVANCED_SHIPPER_TEXT_DESCRIPTION_BASE;
		} else {
			// In Catalog mode
			$this->title = MODULE_ADVANCED_SHIPPER_TEXT_CATALOG_TITLE;
			$this->description = '';
		}
		
		// Disable the module if configured as such or a critical configuration error was found
		$this->enabled = ((MODULE_ADVANCED_SHIPPER_STATUS == 'Yes' &&
			$critical_config_problem == false) ? true : false);
		
		if ($this->enabled) {
			// Disable module if entire cart is free shipping or admin settings specify that the
			// module should be disabled if the user's address isn't covered by one of the defined
			// regions and this is the case for the user's address!
			if (!zen_get_shipping_enabled($this->code)) {
				$this->enabled = false;
			} else {
				// Load module's configuration
				$this->_loadConfiguration();
			}
		}
		
		$this->sort_order = MODULE_ADVANCED_SHIPPER_SORT_ORDER;
		$this->icon = '';
		
		// Unfortunately have to use a session kludge to store the tax class as Zen Cart attempts to
		// examine the tax class without requiring a quote to be constructed. Since this module
		// doesn't have the tax class set in its general configuration but on a per method/per
		// region basis, the previous tax class will be used in the hope that it is most likely to
		// be the correct class. Anytime the module builds a quote this is reset so the tax class
		// will only be stored after the successful building of a quote.
		if (isset($_SESSION['advshipper_tax_class'])) {
			$this->tax_class = $_SESSION['advshipper_tax_class'];
		}
	}
	
	// }}}
	
	
	// {{{ quote()
	
	/**
	 * Main runtime method, called when Zen Cart requires a list of quotes to be built or the
	 * details for the selected quote to be returned.
	 *
	 * @author Conor Kerr <zen-cart.advshipper@dev.ceon.net>
	 * @access public
	 * @param  string  $selected_method_combination  The ID of a method selected by the customer.
	 * @return array   The list of quotes for the customer, the details for the quote selected by
	 *                 the customer or error information.
	 */
	function quote($selected_method_combination = '')
	{
		global $order, $db;
		
		if (isset($_SESSION['advshipper_tax_class'])) {
			unset($_SESSION['advshipper_tax_class']);
			$this->tax_class = 0;
		}
		
		$this->quotes = array(
			'id' => $this->code,
			'methods' => array(),
			'module' => MODULE_ADVANCED_SHIPPER_DEFAULT_TITLE
			);
		
		// Variable holds information about shipping methods which can be used for this order
		$this->_methods = array();
		
		// Variable holds information about the products in the order
		$this->_products = $_SESSION['cart']->get_products();
		
		// Determine which method(s) (if any) apply for the contents of the cart and the customer's
		// shipping address. The information for any usable methods will be loaded and stored //////
		$usable_methods = $this->_getUsableMethodsInfo();
		
		if ($usable_methods === false) {
			// An error occurred or no methods are usable
			// Add dummy default method so shipping estimator will display error!
			$this->quotes['methods'][0] = array();
			
			if (!isset($this->quotes['error'])) {
				// If the shipping estimator is in use, destination postcode/state may not have been
				// specified. Let customer know they should try entering a postcode or selecting a
				// state.
				if ((is_null($order->delivery['postcode']) ||
						strlen($order->delivery['postcode']) == 0) &&
						(strlen($order->delivery['state']) == 0 &&
						(!isset($order->delivery['zone_id']) ||
						is_null($order->delivery['zone_id']) ||
						$order->delivery['zone_id'] == 0))) {
					$this->quotes['error'] = MODULE_ADVANCED_SHIPPER_ERROR_SPECIFY_SHIPPING_ADDRESS;
				} else if ((is_null($order->delivery['postcode']) ||
						strlen($order->delivery['postcode']) == 0) &&
						(strlen($order->delivery['state']) > 0 ||
						(isset($order->delivery['zone_id']) && $order->delivery['zone_id'] > 0))) {
					$this->quotes['error'] = MODULE_ADVANCED_SHIPPER_ERROR_SPECIFY_POSTCODE;
				} else if ((!is_null($order->delivery['postcode']) &&
						strlen($order->delivery['postcode']) > 0) &&
						(strlen($order->delivery['state']) == 0 &&
						(!isset($order->delivery['zone_id']) ||
						is_null($order->delivery['zone_id']) ||
						$order->delivery['zone_id'] == 0))) {
					$this->quotes['error'] = MODULE_ADVANCED_SHIPPER_ERROR_SPECIFY_STATE;
				} else {
					// Customer's address isn't covered by any of the regions defined
					$this->quotes['error'] = MODULE_ADVANCED_SHIPPER_ERROR_INVALID_REGION;
				}
			}
			
			return $this->quotes;
		}
		
		// Now that all the data has been collected for the methods that may possibly be used for
		// this order/items in the order, work out any rates that can be used //////////////////////
		$rates_calc_success = $this->_calcMethodRates();
		
		if ($rates_calc_success === false) {
			// An error occurred when calculating the rates which must be displayed to the customer
			
			// Add dummy default method so shipping estimator will display error!
			$this->quotes['methods'][0] = array();
			
			return $this->quotes;
		}
		
		// Must check to see if any products no longer have any usable methods due to a rate not
		// being able to be calculated for its usable method(s)
		$usable_methods = $this->_verifyAllProductsHaveUsableMethods();
		
		if ($usable_methods === false) {
			// At least one product has no usable methods
			$this->quotes['error'] = MODULE_ADVANCED_SHIPPER_ERROR_NO_RATES_MATCH;
			
			// Add dummy default method so shipping estimator will display error!
			$this->quotes['methods'][0] = array();
			
			return $this->quotes;
		}
		
		// Create the "instances" of the shipping methods. Non-dated methods will have 1 instance
		// only but dated instances could have several instances created as options for several
		// weeks in advance ////////////////////////////////////////////////////////////////////////
		$this->_createMethodInstances();
		
		// Must check to see if any products no longer have any usable methods due to the method(s)
		// not being available at this time
		$usable_methods = $this->_verifyAllProductsHaveUsableMethods();
		
		if ($usable_methods === false) {
			// At least one product has no usable methods
			$this->quotes['error'] = MODULE_ADVANCED_SHIPPER_ERROR_NO_RATES_AVAILABLE;
			
			// Add dummy default method so shipping estimator will display error!
			$this->quotes['methods'][0] = array();
			
			return $this->quotes;
		}
		
		// Build an array of all possible usable combinations for shipping methods for the products
		// in the cart. Each product must have a single shipping method, no more, no less!
		$method_combinations = $this->_getUsableCombinations();
		
		if ($method_combinations === false) {
			// There are no combinations of the usable shipping methods which will cover each
			// product in the cart once only
			$this->quotes['error'] = MODULE_ADVANCED_SHIPPER_ERROR_NO_COMBINATIONS;
			
			// Add dummy default method so shipping estimator will display error!
			$this->quotes['methods'][0] = array();
			
			return $this->quotes;
		}
		
		// Actually build the quotes! //////////////////////////////////////////////////////////////
		$this->_buildQuotes($method_combinations, $selected_method_combination);
		
		if (sizeof($this->quotes['methods']) == 1) {
			//$this->quotes['module'] = $this->quotes['methods'][0]['title'];
		}
		
		return $this->quotes;
	}
	
	// }}}
	
	
	// {{{ _getUsableMethodsInfo()
	
	/**
	 * Examines all products and builds a list of all shipping methods which have rates for this
	 * order/items in the order and the customer's shipping region.
	 *
	 * @author Conor Kerr <zen-cart.advshipper@dev.ceon.net>
	 * @access protected
	 * @return boolean    True if at least one method was indentified for each product in the cart
	 *                    or false if an error occurs (including if a product has no usable
	 *                    methods).
	 */
	function _getUsableMethodsInfo()
	{
		global $db;
		
		// Perform simple configuration lookups to speed up module when product selections and/or
		// category selections and/or manufacturer selections haven't been availed of for any
		// shipping method
		$use_product_selection_lookups = $this->_anyMethodsUseProductSelections();
		$use_category_selection_lookups = $this->_anyMethodsUseCategorySelections();
		$use_manufacturer_selection_lookups = $this->_anyMethodsUseManufacturerSelections();
		
		$fallover_methods = $this->_getMethodsUsingProductFallover();
		
		// Variable speeds up checks by recording list of shipping methods which have been
		// identified as not being applicable for the customer
		$unapplicable_methods = array();
		
		$num_products = sizeof($this->_products);
		
		for ($product_i = 0; $product_i < $num_products; $product_i++) {
			// Determine which shipping methods include this product ///////////////////////////////
			// Variable holds final list of methods which apply for this product
			$product_methods = array();
			
			$product_id = $this->_products[$product_i]['id'];
			if (strpos($product_id, ':') !== false) {
				$temp = explode(':', $product_id);
				$product_id = $temp[0];
			}
			
			// Mark and ignore virtual/free shipping products
			if (zen_get_products_virtual($product_id) ||
					zen_get_product_is_always_free_shipping($product_id)) {
				$this->_products[$product_i]['free_shipping'] = true;
				continue;
			} else {
				$this->_products[$product_i]['free_shipping'] = false;
			}
			
			$product_attributes = $this->_products[$product_i]['attributes'];
			
			// First, determine if this product is included in any specific shipping methods
			if ($use_product_selection_lookups) {
				$product_specific_methods =
					$this->_getProductSpecificShippingMethods($product_id, $product_attributes);
				
				$product_methods = $product_specific_methods;
			}
			
			// Determine if this product is included in any specific shipping methods by way of one
			// of its parent categories being included in a specific shipping method
			if ($use_category_selection_lookups) {
				$category_specific_methods =
					$this->_getCategorySpecificShippingMethods($product_id);
				
				for ($csm_i = 0, $num_csm = sizeof($category_specific_methods); $csm_i < $num_csm;
						$csm_i++) {
					if (!in_array($category_specific_methods[$csm_i], $product_methods)) {
						$product_methods[] = $category_specific_methods[$csm_i];
					}
				}
			}
			
			// Determine if this product is included in any specific shipping methods by way of its
			// manufacturer being included in a specific shipping method
			if ($use_manufacturer_selection_lookups) {
				$manufacturer_specific_methods =
					$this->_getManufacturerSpecificShippingMethods($product_id);
				
				for ($msm_i = 0, $num_msm = sizeof($manufacturer_specific_methods);
						$msm_i < $num_msm; $msm_i++) {
					if (!in_array($manufacturer_specific_methods[$msm_i], $product_methods)) {
						$product_methods[] = $manufacturer_specific_methods[$msm_i];
					}
				}
			}
			
			$num_product_specific_methods = sizeof($product_methods);
			
			if ($num_product_specific_methods == 0) {
				// Product not covered by any specific category/product selections so can only be
				// covered by a fallover
				$product_methods = $fallover_methods;
			}
			
			$num_product_methods = sizeof($product_methods);
			
			if ($num_product_methods == 1) {
				$this->_debug("\n<br />Product Index $product_i potentially covered by Method: " .
					$product_methods[0], true);
			} else if ($num_product_methods > 1) {
				$this->_debug("\n<br />Product Index $product_i potentially covered by Methods: " .
					implode(', ', $product_methods), true);
			}
			
			// Check each shipping method to check if it does NOT cover the customer's address /////
			$valid_product_methods = array();
			for ($method_i = 0; $method_i < $num_product_methods; $method_i++) {
				$method_num = $product_methods[$method_i];
				
				// Don't bother checking any shipping methods which have already been identified as
				// not covering the customer's address
				$num_unapplicable_methods = sizeof($unapplicable_methods);
				for ($unapp_i = 0; $unapp_i < $num_unapplicable_methods; $unapp_i++) {
					if ($method_num == $unapplicable_methods[$unapp_i]) {
						$method_applicable = false;
						continue 2; // Check next method
					}
				}
				
				$region_info = $this->_getRegionAndRates($method_num);
				
				// Check if an error occurred when attempting to get the region and rates 
				if (isset($this->quotes['error'])) {
					return false;
				}
				
				if ($region_info == false) {
					// Method can't be used for this order
					$unapplicable_methods[] = $method_num;
					
					$this->_debug("\nMethod $method_num excluded as no Region matches the " . 
						"customer's address.", false);
				} else {
					// Method may be able to be used if any of its rates match, store rates for use
					// later
					if (!isset($this->_methods[$method_num])) {
						$this->_methods[$method_num] = $region_info;
						$this->_methods[$method_num]['app_product_indexes'] = array($product_i);
					} else {
						// Product may be specifically selected as well as being part of a category,
						// avoid adding it twice!
						if (!in_array($product_i,
								$this->_methods[$method_num]['app_product_indexes'])) {
							$this->_methods[$method_num]['app_product_indexes'][] = $product_i;
						}
					}
					
					$valid_product_methods[] = $method_num;
				}
			}
			
			$num_valid_product_methods = sizeof($valid_product_methods);
			
			if ($num_valid_product_methods == 0) {
				$this->_debug("\n<br />Product Index $product_i NOT covered by ANY methods!", true);
			} else if ($num_valid_product_methods == 1) {
				$this->_debug("\n<br />Product Index $product_i covered by method: " .
					$valid_product_methods[0], true);
			} else if ($num_valid_product_methods > 1) {
				$this->_debug("\n<br />Product Index $product_i covered by methods: " .
					implode(', ', $valid_product_methods), true);
			}
			
			if ($num_valid_product_methods == 0) {
				// This product cannot be shipped as no methods match it, at least for the
				// customer's address - Can't use this module!
				
				if ($num_product_methods == 0) {
					// Let the user know that this product caused the problem so they can let the
					// store owner know... no store should have unshippable products!
					$this->quotes['error'] = sprintf(
						MODULE_ADVANCED_SHIPPER_ERROR_NO_METHODS_FOR_PRODUCT,
						$this->_products[$product_i]['name']
						);
				}
				
				return false;
			}
		}
		
		// Now that the region has been identified for each method and its info stored, must store
		// method info for each method
		$method_nums_string = '';
		foreach ($this->_methods as $method_num => $method_info) {
			$method_nums_string .= $method_num . ',';
		}
		$method_nums_string = substr($method_nums_string, 0, strlen($method_nums_string) - 1);
		
		$load_methods_info_sql = "
			SELECT
				asmc.method,
				asmc.select_products,
				asmc.availability_scheduling,
				asmc.once_only_start_datetime,
				asmc.once_only_end_datetime,
				asmc.availability_recurring_mode,
				asmc.availability_weekly_start_day,
				asmc.availability_weekly_start_time,
				asmc.availability_weekly_cutoff_day,
				asmc.availability_weekly_cutoff_time,
				asmc.usage_limit,
				asmc.once_only_shipping_datetime,
				asmc.availability_weekly_shipping_scheduling,
				asmc.availability_weekly_shipping_show_num_weeks,
				asmc.availability_weekly_shipping_regular_weekday_day,
				asmc.availability_weekly_shipping_regular_weekday_time
			FROM
				" . TABLE_ADVANCED_SHIPPER_METHOD_CONFIGS . " asmc
			WHERE
				asmc.config_id = '" . $this->_config_id . "'
			AND
				asmc.method IN (" . $method_nums_string . ");";
		
		$load_methods_info_result = $db->Execute($load_methods_info_sql);
		
		if ($load_methods_info_result->EOF) {
			return false;
		} else {
			while (!$load_methods_info_result->EOF) {
				$method_num = $load_methods_info_result->fields['method'];
				
				foreach ($load_methods_info_result->fields as $key => $value) {
					switch ($key) {
						case 'method':
							break;
						case 'once_only_start_datetime':
						case 'once_only_end_datetime':
						case 'once_only_shipping_datetime':
							$date = $value;
							$year = substr($date, 0, 4);
							if ($year != '0000' && !is_null($date)) {
								$month = substr($date, 5, 2);
								$day = substr($date, 8, 2);
								$hour = substr($date, 11, 2);
								$minute = substr($date, 14, 2);
								$timestamp = mktime($hour, $minute, 0, $month, $day, $year);
								switch ($key) {
									case 'once_only_start_datetime':
										$this->_methods[$method_num]['once_only_start_timestamp'] =
											$timestamp;
										break;
									case 'once_only_end_datetime':
										$this->_methods[$method_num]['once_only_end_timestamp'] =
											$timestamp;
										break;
									case 'once_only_shipping_datetime':
										$this->_methods[$method_num]['once_only_shipping_timestamp'] =
											$timestamp;
										break;
								}
								
							} else {
								$this->_methods[$method_num][$key] = null;
							}
							break;
						default:
							$this->_methods[$method_num][$key] = $value;
							break;
					}
				}
				
				$load_methods_info_result->MoveNext();
			}
			
			// Get the titles for this methods for the current language
			$load_methods_titles_sql = "
				SELECT
					asmt.method,
					asmt.title,
					asmt.language_id
				FROM
					" . TABLE_ADVANCED_SHIPPER_METHOD_TITLES . " asmt
				WHERE
					asmt.config_id = '" . $this->_config_id . "'
				AND
					asmt.method IN (" . $method_nums_string . ");";
			
			$load_methods_titles_result = $db->Execute($load_methods_titles_sql);
			
			if ($load_methods_titles_result->EOF) {
				
			} else {
				while (!$load_methods_titles_result->EOF) {
					$method_num = $load_methods_titles_result->fields['method'];
					
					if ($load_methods_titles_result->fields['language_id'] ==
							$_SESSION['languages_id']) {
						$this->_methods[$method_num]['title'] =
							$load_methods_titles_result->fields['title'];
					}
					
					$load_methods_titles_result->MoveNext();
				}
			}
		}
		
		return true;
	}
	
	// }}}
	
	
	// {{{ _anyMethodsUseProductSelections()
	
	/**
	 * Checks if any shipping methods apply for specific products.
	 *
	 * @author Conor Kerr <zen-cart.advshipper@dev.ceon.net>
	 * @access protected
	 * @return boolean   Whether or not category selections are used by any methods.
	 */
	function _anyMethodsUseProductSelections()
	{
		global $db;
		
		$check_methods_for_product_selections_sql = "
			SELECT
				asmp.product_id
			FROM
				" . TABLE_ADVANCED_SHIPPER_METHOD_PRODUCTS . " asmp
			WHERE
				asmp.config_id = '" . $this->_config_id . "'
			LIMIT 1;";
		
		$check_methods_for_product_selections_result =
			$db->Execute($check_methods_for_product_selections_sql);
			
		if ($check_methods_for_product_selections_result->EOF) {
			// No methods specifically cover any products
			return false;
		} else {
			return true;
		}
	}
	
	// }}}
	
	
	// {{{ _getProductSpecificShippingMethods()
	
	/**
	 * Checks the current configuration to see if any shipping methods specifically cover the
	 * specified product/attribute(s) combination. If so, the number for each of these shipping
	 * methods is included in the returned array.
	 *
	 * @author Conor Kerr <zen-cart.advshipper@dev.ceon.net>
	 * @access protected
	 * @param  integer    $product_id           The product's ID.
	 * @param  array      $product_attributes   The attributes for this product.
	 * @return array   An array containing the numbers of the shipping methods which specifically
	 *                 cover this product/attribute(s) combination.
	 */
	function _getProductSpecificShippingMethods($product_id, $product_attributes)
	{
		global $db;
		
		$product_specific_methods = array();
		
		// Check products with no attributes or products with a catchall for their attributes
		$check_methods_for_product_sql = "
			SELECT
				DISTINCT asmp.method
			FROM
				" . TABLE_ADVANCED_SHIPPER_METHOD_PRODUCTS . " asmp
			WHERE
				asmp.config_id = '" . $this->_config_id . "'
			AND
				asmp.product_id = '" . (int) $product_id . "'
			AND
				asmp.product_attributes_id = '0'
			ORDER BY
				asmp.method;";
		
		$check_methods_for_product_result = $db->Execute($check_methods_for_product_sql);
		
		if ($check_methods_for_product_result->EOF) {
			// No methods specifically cover this product
		} else {
			while (!$check_methods_for_product_result->EOF) {
				$product_specific_methods[] =
					$check_methods_for_product_result->fields['method'];
				$check_methods_for_product_result->MoveNext();
			}
		}
		
		if (is_array($product_attributes) && sizeof($product_attributes) != 0) {
			// Check if this product's specific attribute combination has been selected for a
			// shipping method
			// Look up the product attribute IDs for this product's attributes name/value pairs
			$product_attribute_ids = array();
			
			foreach ($product_attributes as $option_name_id => $option_value_id) {
				$products_attributes_id_sql = "
					SELECT
						pa.products_attributes_id
					FROM
						" . TABLE_PRODUCTS_ATTRIBUTES . " pa
					WHERE
						pa.products_id = '" . (int) $product_id . "'
					AND
						pa.options_id = '" . (int) $option_name_id . "'
					AND
						pa.options_values_id = '" . (int) $option_value_id . "';";
				
				$products_attributes_id_result = $db->Execute($products_attributes_id_sql);
				
				$product_attribute_ids[] =
					$products_attributes_id_result->fields['products_attributes_id'];
			}
			
			$num_product_attribute_ids = sizeof($product_attribute_ids);
			
			// Get the list of methods, and product selections within those methods, which could
			// possibly match the selected product attribute combination
			$check_methods_for_product_sql = "
				SELECT
					DISTINCT asmp.method,
					asmp.product_order
				FROM
					" . TABLE_ADVANCED_SHIPPER_METHOD_PRODUCTS . " asmp
				WHERE
					asmp.config_id = '" . $this->_config_id . "'
				AND
					asmp.product_id = '" . (int) $product_id . "'
				ORDER BY
					asmp.method,
					asmp.product_order;";
			
			$check_methods_for_product_result = $db->Execute($check_methods_for_product_sql);
			
			if ($check_methods_for_product_result->EOF) {
				// No methods specifically cover this product
			} else {
				// At least one method covers this product ID. Check if any also match the selected
				// product attribute combination
				while (!$check_methods_for_product_result->EOF) {
					$num_method_product_attribute_ids = 0;
					$num_matching_attribute_ids = 0;
					
					$method_product_attribute_ids_sql = "
						SELECT
							asmp.product_attributes_id
						FROM
							" . TABLE_ADVANCED_SHIPPER_METHOD_PRODUCTS . " asmp
						WHERE
							asmp.config_id = '" . $this->_config_id . "'
						AND
							asmp.method = '" .
							$check_methods_for_product_result->fields['method'] . "'
						AND
							asmp.product_id = '" . (int) $product_id . "'
						AND
							asmp.product_order = '" .
							$check_methods_for_product_result->fields['product_order'] . "';";
					
					$method_product_attribute_ids_result =
						$db->Execute($method_product_attribute_ids_sql);
					
					while (!$method_product_attribute_ids_result->EOF) {
						$num_method_product_attribute_ids++;
						
						// Does this attribute ID match one of the selected attribute IDs?
						if (in_array(
								$method_product_attribute_ids_result->fields['product_attributes_id'],
								$product_attribute_ids)) {
							$num_matching_attribute_ids++;
						}
						
						$method_product_attribute_ids_result->MoveNext();
					}
					
					if ($num_product_attribute_ids == $num_matching_attribute_ids &&
							$num_method_product_attribute_ids == $num_product_attribute_ids) {
						// This method specifies this product attribute combination!
						$product_specific_methods[] =
							$check_methods_for_product_result->fields['method'];
					}
					
					$check_methods_for_product_result->MoveNext();
				}
			}
		}
		
		return $product_specific_methods;
	}
	
	// }}}
	
	
	// {{{ _anyMethodsUseCategorySelections()
	
	/**
	 * Checks if any shipping methods apply for specific categories.
	 *
	 * @author Conor Kerr <zen-cart.advshipper@dev.ceon.net>
	 * @access protected
	 * @return boolean   Whether or not category selections are used by any methods.
	 */
	function _anyMethodsUseCategorySelections()
	{
		global $db;
		
		$check_methods_for_category_selections_sql = "
			SELECT
				asmc.category_id
			FROM
				" . TABLE_ADVANCED_SHIPPER_METHOD_CATEGORIES . " asmc
			WHERE
				asmc.config_id = '" . $this->_config_id . "'
			LIMIT 1;";
		
		$check_methods_for_category_selections_result =
			$db->Execute($check_methods_for_category_selections_sql);
			
		if ($check_methods_for_category_selections_result->EOF) {
			// No methods specifically cover any categories
			return false;
		} else {
			return true;
		}
	}
	
	// }}}
	
	
	// {{{ _getCategorySpecificShippingMethods()
	
	/**
	 * Checks the current configuration to see if any shipping methods specifically cover categories
	 * which include the specified product. If so, the number for each of these shipping methods is
	 * included in the returned array.
	 *
	 * @author Conor Kerr <zen-cart.advshipper@dev.ceon.net>
	 * @access protected
	 * @param  integer    $product_id    The product's ID.
	 * @return array   An array containing the numbers of the shipping methods which specifically
	 *                 cover this product/attribute(s) combination.
	 */
	function _getCategorySpecificShippingMethods($product_id)
	{
		global $db;
		
		$category_specific_methods = array();
		
		// Get all categories this product is in
		$categories_for_product = array();
		$categories_for_product_query = "
			SELECT
				DISTINCT categories_id
			FROM
				" . TABLE_PRODUCTS_TO_CATEGORIES . "
			WHERE
				products_id = '" . (int) $product_id . "';";
		
		$categories_for_product_result = $db->Execute($categories_for_product_query);
		
		while (!$categories_for_product_result->EOF) {
			$categories_for_product[] = $categories_for_product_result->fields['categories_id'];
			
			$categories_for_product_result->MoveNext();
		}
		
		if (sizeof($categories_for_product) > 0) {
			// Must get all sub-categories for the categories found
			$parent_categories = array();
			foreach ($categories_for_product as $category_id) {
				zen_get_parent_categories($parent_categories, $category_id);
			}
			
			$categories_for_product = array_merge($categories_for_product, $parent_categories);
			
			$category_ids_string = implode(',', $categories_for_product);
			
			$check_methods_for_categories_sql = "
				SELECT
					DISTINCT asmc.method
				FROM
					" . TABLE_ADVANCED_SHIPPER_METHOD_CATEGORIES . " asmc
				WHERE
					asmc.config_id = '" . $this->_config_id . "'
				AND
					asmc.category_id IN (" . $category_ids_string . ")
				ORDER BY
					asmc.method;";
			
			$check_methods_for_categories_result = $db->Execute($check_methods_for_categories_sql);
			
			if ($check_methods_for_categories_result->EOF) {
				// No methods specifically cover these categories
			} else {
				while (!$check_methods_for_categories_result->EOF) {
					$category_specific_methods[] =
						$check_methods_for_categories_result->fields['method'];
						
					$check_methods_for_categories_result->MoveNext();
				}
			}
		}
		
		return $category_specific_methods;
	}
	
	// }}}
	
	
	// {{{ _anyMethodsUseManufacturerSelections()
	
	/**
	 * Checks if any shipping methods apply for specific manufacturers.
	 *
	 * @author Conor Kerr <zen-cart.advshipper@dev.ceon.net>
	 * @access protected
	 * @return boolean   Whether or not manufacturer selections are used by any methods.
	 */
	function _anyMethodsUseManufacturerSelections()
	{
		global $db;
		
		$check_methods_for_manufacturer_selections_sql = "
			SELECT
				asmm.manufacturer_id
			FROM
				" . TABLE_ADVANCED_SHIPPER_METHOD_MANUFACTURERS . " asmm
			WHERE
				asmm.config_id = '" . $this->_config_id . "'
			LIMIT 1;";
		
		$check_methods_for_manufacturer_selections_result =
			$db->Execute($check_methods_for_manufacturer_selections_sql);
			
		if ($check_methods_for_manufacturer_selections_result->EOF) {
			// No methods specifically cover any manufacturers
			return false;
		} else {
			return true;
		}
	}
	
	// }}}
	
	
	// {{{ _getManufacturerSpecificShippingMethods()
	
	/**
	 * Checks the current configuration to see if any shipping methods specifically cover
	 * manufacturers which include the specified product. If so, the number for each of these
	 * shipping methods is included in the returned array.
	 *
	 * @author Conor Kerr <zen-cart.advshipper@dev.ceon.net>
	 * @access protected
	 * @param  integer    $product_id    The product's ID.
	 * @return array   An array containing the numbers of the shipping methods which specifically
	 *                 cover this product/attribute(s) combination.
	 */
	function _getManufacturerSpecificShippingMethods($product_id)
	{
		global $db;
		
		$manufacturer_specific_methods = array();
		
		// Get the manufacturer for this product
		$manufacturer_for_product = null;
		$manufacturer_for_product_query = "
			SELECT
				manufacturers_id
			FROM
				" . TABLE_PRODUCTS . "
			WHERE
				products_id = '" . (int) $product_id . "';";
		
		$manufacturer_for_product_result = $db->Execute($manufacturer_for_product_query);
		
		if (!$manufacturer_for_product_result->EOF) {
			$manufacturer_for_product =
				$manufacturer_for_product_result->fields['manufacturers_id'];
		}
		
		if (!is_null($manufacturer_for_product)) {
			$check_methods_for_manufacturers_sql = "
				SELECT
					DISTINCT asmm.method
				FROM
					" . TABLE_ADVANCED_SHIPPER_METHOD_MANUFACTURERS . " asmm
				WHERE
					asmm.config_id = '" . $this->_config_id . "'
				AND
					asmm.manufacturer_id = " . $manufacturer_for_product . "
				ORDER BY
					asmm.method;";
			
			$check_methods_for_manufacturers_result =
				$db->Execute($check_methods_for_manufacturers_sql);
			
			if ($check_methods_for_manufacturers_result->EOF) {
				// No methods specifically cover the product's manufacturer
			} else {
				while (!$check_methods_for_manufacturers_result->EOF) {
					$manufacturer_specific_methods[] =
						$check_methods_for_manufacturers_result->fields['method'];
						
					$check_methods_for_manufacturers_result->MoveNext();
				}
			}
		}
		
		return $manufacturer_specific_methods;
	}
	
	// }}}
	
	
	// {{{ _getMethodsUsingProductFallover()
	
	/**
	 * Returns any shipping methods acting as a fallover for all products/categories which haven't
	 * been explicity selected for at least one shipping method.
	 *
	 * @author Conor Kerr <zen-cart.advshipper@dev.ceon.net>
	 * @access protected
	 * @return array      An array of the numbers for any methods which act as a fallover.
	 */
	function _getMethodsUsingProductFallover()
	{
		global $db;
		
		$fallover_methods = array();
		
		$get_fallover_methods_sql = "
			SELECT
				asmc.method
			FROM
				" . TABLE_ADVANCED_SHIPPER_METHOD_CONFIGS . " asmc
			WHERE
				asmc.config_id = '" . $this->_config_id . "'
			AND
				asmc.select_products = '" . (int) ADVSHIPPER_SELECT_PRODUCT_FALLOVER . "'";
		
		$get_fallover_methods_result =
			$db->Execute($get_fallover_methods_sql);
			
		if ($get_fallover_methods_result->EOF) {
			// No methods acting as a fallover
		} else {
			while (!$get_fallover_methods_result->EOF) {
				$fallover_methods[] = $get_fallover_methods_result->fields['method'];
				
				$get_fallover_methods_result->MoveNext();
			}
		}
		
		return $fallover_methods;
	}
	
	// }}}
	
	
	// {{{ _getRegionAndRates()
	
	/**
	 * Checks the specified method to see if it has a region defined which matches the customer's
	 * shipping address. If so, the rates information for that region is returned.
	 *
	 * @author Conor Kerr <zen-cart.advshipper@dev.ceon.net>
	 * @access protected
	 * @param  integer    $method_num     The number of the shipping method to be examined.
	 * @return array|boolean    The region and rates info for the region which matches the
	 *                          customer's address or false if none was identified or an error was
	 *                          encountered (which should be checked for and passed on to the user).
	 */
	function _getRegionAndRates($method_num)
	{
		global $db, $order;
		
		$region_rates = false;
		
		// Get the necessary information about the customer's address
		$dest_country = strtoupper($order->delivery['country']['iso_code_2']);
		$dest_postcode = strtolower(preg_replace('/\s+/', '', $order->delivery['postcode']));
		$dest_state = strtolower($order->delivery['state']);
		$dest_city = strtolower($order->delivery['city']);
		$dest_zone_id = $order->delivery['zone_id'];
		
		// Get the store's shipping country code
		$store_country_code_query = "
			SELECT
				countries_iso_code_2
			FROM
				" . TABLE_COUNTRIES . "
			WHERE
				countries_id = '" . (int) SHIPPING_ORIGIN_COUNTRY . "';";
		
		$store_country_code_value = $db->Execute($store_country_code_query);
		
		$store_country = strtoupper($store_country_code_value->fields['countries_iso_code_2']);
		
		$regions_info_sql = "
			SELECT
				asrc.region,
				asrc.definition_method,
				asrc.countries_postcodes,
				asrc.countries_zones,
				asrc.countries_cities,
				asrc.countries_states,
				asrc.distance,
				asrc.tax_class,
				asrc.rates_include_tax,
				asrc.rate_limits_inc,
				asrc.total_up_price_inc_tax,
				asrc.table_of_rates,
				asrc.max_weight_per_package,
				asrc.packaging_weights,
				asrc.surcharge,
				asrt.title,
				asrst.title AS surcharge_title
			FROM
				" . TABLE_ADVANCED_SHIPPER_REGION_CONFIGS . " asrc
			LEFT JOIN
				" . TABLE_ADVANCED_SHIPPER_REGION_TITLES . " asrt
			ON
				(
				asrt.config_id = asrc.config_id
			AND
				asrt.region = asrc.region
			AND
				asrt.method = asrc.method
			AND
				asrt.language_id = '" . (int) $_SESSION['languages_id'] . "'
				)
			LEFT JOIN
				" . TABLE_ADVANCED_SHIPPER_REGION_SURCHARGE_TITLES . " asrst
			ON
				(
				asrst.config_id = asrc.config_id
			AND
				asrst.region = asrc.region
			AND
				asrst.method = asrc.method
			AND
				asrst.language_id = '" . (int) $_SESSION['languages_id'] . "'
				)
			WHERE
				asrc.config_id = '" . $this->_config_id . "'
			AND
				asrc.method = '" . (int) $method_num . "'
			ORDER BY
				asrc.region;";
		
		$regions_info_result = $db->Execute($regions_info_sql);
		
		if ($regions_info_result->EOF) {
			// No regions for this method!
		} else {
			while (!$regions_info_result->EOF) {
				// Store info for this region in case it can be used
				$region_info = array(
					'region' => $regions_info_result->fields['region'],
					'tax_class' => $regions_info_result->fields['tax_class'],
					'rates_include_tax' => $regions_info_result->fields['rates_include_tax'],
					'rate_limits_inc' => $regions_info_result->fields['rate_limits_inc'],
					'total_up_price_inc_tax' => $regions_info_result->fields['total_up_price_inc_tax'],
					'table_of_rates' => $regions_info_result->fields['table_of_rates'],
					'max_weight_per_package' => $regions_info_result->fields['max_weight_per_package'],
					'packaging_weights' => $regions_info_result->fields['packaging_weights'],
					'surcharge' => $regions_info_result->fields['surcharge'],
					'region_title' => $regions_info_result->fields['title'],
					'surcharge_title' => $regions_info_result->fields['surcharge_title']
					);
				
				// Use geolocation to determine customer's distance from the store?
				if ($regions_info_result->fields['definition_method'] == 
						ADVSHIPPER_DEFINITION_METHOD_GEOLOCATION) {
					// Use geolocation if possible (Geolocation functions must be defined for the
					// store's country)
					
					if ($dest_postcode == '') {
						// Can't use geolocation when destination postcode is blank (as can happen
						// with shipping estimator)
						$regions_info_result->MoveNext();
						
						continue;
					}
					
					$geolocation_function_file = DIR_FS_CATALOG. DIR_WS_MODULES .
						'shipping/advshipper/geolocation_' . strtolower($store_country) . '.php';
					
					if (!file_exists($geolocation_function_file)) {
						// Could not load in geolocation functions!
						$this->quotes['error'] = sprintf(
							MODULE_ADVANCED_SHIPPER_ERROR_GEOLOCATION_FUNCTIONS_MISSING,
							$store_country);
						return false;
					} else {
						require_once($geolocation_function_file);
						
						// Get the distance between the customer's address and the store
						$geolocation_function = 'advshipper_getDistance' . $store_country .
							$dest_country;
						
						if (!function_exists($geolocation_function)) {
							// No geolocation function for determining the distance between
							// postcodes in the store's country and postcodes in the shipping
							// address' country has been defined, this region has not been matched
							$regions_info_result->MoveNext();
							
							continue;
						} else {
							$distance_to_customer = $geolocation_function(SHIPPING_ORIGIN_ZIP,
								$dest_postcode);
							
							$this->_debug("Distance to customer: " . $distance_to_customer . "\n\n", true);
							
							$region_distance_from_store = $regions_info_result->fields['distance'];
							
							$this->_debug("Distance from store defining this region: " . $region_distance_from_store . "\n\n", true);
							
							if (!is_numeric($distance_to_customer)) {
								// A problem has been encountered when trying to determine the
								// distance from the store has not been specified for this region
								$this->quotes['error'] = $distance_to_customer;
								return false;
							} else if ($distance_to_customer == -1) {
								// An error occurred when attempting to check the postcode
								// Alert the user to the fact that they've entered an invalid
								// postcode.
								
								// Check if specific error message for their country exists
								if (defined('MODULE_ADVANCED_SHIPPER_ERROR_CUSTOMER_POSTCODE_PARSE_' .
										$dest_country)) {
									$this->quotes['error'] = sprintf(constant(
										'MODULE_ADVANCED_SHIPPER_ERROR_CUSTOMER_POSTCODE_PARSE_' .
										$dest_country), $order->delivery['postcode']);
								} else {
									$this->quotes['error'] = sprintf(
										MODULE_ADVANCED_SHIPPER_ERROR_CUSTOMER_POSTCODE_PARSE,
										$order->delivery['postcode']);
								}
								return false;
							} else if ($distance_to_customer == -2) {
								// The customer's postcode could not be found in the geolocation
								// database, must skip geolocation
								$regions_info_result->MoveNext();
								
								continue;
							}
							
							if ($distance_to_customer < $region_distance_from_store) {
								// Customer's address is in this region!
								return $region_info;
							}
						}
					}
				} else {
					// Geolocation not being used, check customer's address against various address
					// ranges
					
					// Check against the customer's country and postcode ///////////////////////////
					
					$allowed_countries_postcodes = explode(",", preg_replace('/\s+/', '',
						$regions_info_result->fields['countries_postcodes']));
					
					// Check if destination address matches the country code and any postcode
					// specified
					for ($region_i2 = 0, $num_countries_postcodes =
							sizeof($allowed_countries_postcodes);
							$region_i2 < $num_countries_postcodes; $region_i2++) {
						$allowed_country_postcode = explode(':',
							$allowed_countries_postcodes[$region_i2]);
						
						$allowed_country = strtoupper($allowed_country_postcode[0]);
						if (sizeof($allowed_country_postcode) > 1) {
							// Check if this postcode (range) is to be disallowed rather than
							// allowed
							if (substr($allowed_country_postcode[1], 0, 1) == '!') {
								$allow_postcode = false;
								$postcode_range = strtolower(substr($allowed_country_postcode[1], 1,
									strlen($allowed_country_postcode[1]) - 1));
							} else {
								$allow_postcode = true;
								$postcode_range = strtolower($allowed_country_postcode[1]);
							}
						} else {
							$postcode_range = null;
							
							// Check if this country is to be disallowed rather than allowed
							if (substr($allowed_country, 0, 1) == '!') {
								$allow_country = false;
								$allowed_country = strtoupper(
									substr($allowed_country, 1, strlen($allowed_country) - 1)
									);
								if ($allowed_country == '*') {
									// Configuration error - ignored for minute
								}
							} else {
								$allow_country = true;
							}
						}
						
						if ($dest_country == $allowed_country || $allowed_country == '*') {
							// Check postcode if it has been specified for this region definition
							if (!is_null($postcode_range) && $postcode_range != '*') {
								// Must check postcode. Call custom method for country (if exists)
								$postcode_matches_range_method = '_regionMatchesRange' .
									$dest_country;
								
								if (!method_exists($this, $postcode_matches_range_method)) {
									$this->quotes['error'] = sprintf(
										MODULE_ADVANCED_SHIPPER_ERROR_RANGE_METHOD,
										$dest_country, $dest_country);
								} else {
									// (Match not possible if no destination code available, as
									// may occur when using shipping estimator).
									if ($dest_postcode == '') {
										$regions_info_result->MoveNext();
										
										continue 2;
									}
									$postcode_matches = $this->$postcode_matches_range_method(
										$dest_postcode, $postcode_range);
									
									if ($postcode_matches < 0) {
										// An error occurred when attempting to check the postcode
										// Alert the user to the fact that they've entered an
										// invalid postcode.
										
										// Check if specific error message for their country exists
										if (defined('MODULE_ADVANCED_SHIPPER_ERROR_CUSTOMER_POSTCODE_PARSE_' .
												$dest_country)) {
											$this->quotes['error'] = sprintf(constant(
												'MODULE_ADVANCED_SHIPPER_ERROR_CUSTOMER_POSTCODE_PARSE_' .
												$dest_country), $order->delivery['postcode']);
										} else {
											$this->quotes['error'] = sprintf(
												MODULE_ADVANCED_SHIPPER_ERROR_CUSTOMER_POSTCODE_PARSE,
												$order->delivery['postcode']);
										}
										return false;
									} else {
										if ($allow_postcode && $postcode_matches) {
											// Have matched a valid postcode for this region
											return $region_info;
										} else if (!$allow_postcode && $postcode_matches) {
											// Don't bother checking any other postcodes, this
											// postcode isn't allowed for this region!
											$regions_info_result->MoveNext();
											
											continue 2;
										}
									}
								}
							} else {
								if ($allow_country)	{
									// Not checking against postcode and country matches
									return $region_info;
								} else {
									// Not checking against postcode, country matches but isn't
									// allowed for this region
									$regions_info_result->MoveNext();
									
									continue 2;
								}
							}
						}
					}
					
					// Only attempt to use zones module if it is installed!
					if (function_exists('advshipper_zones_get_ids_for_zones_string')) {
						// Check against the customer's country and zone ///////////////////////////
						$zone_ids = advshipper_zones_get_ids_for_zones_string(
							$regions_info_result->fields['countries_zones']
							);
						
						foreach ($zone_ids as $zone_id) {
							if (is_numeric($zone_id)) {
								if ($dest_zone_id != 0 && $dest_zone_id == $zone_id) {
									return $region_info;
								}
							} else if ($zone_id == '*') {
								// All countries, all zones
								return $region_info;
							} else {
								// Country code specified (all zones for a country)
								if (strtoupper($zone_id) == $dest_country) {
									return $region_info;
								}
							}
						}
					}
					
					// Only attempt to use locality module if it is installed!
					if (function_exists('localities_parse_identifier_string')) {
						// Check against the customer's country and state //////////////////////////
						$dest_state_info = localities_parse_identifier_string($dest_state);
						
						if ($dest_state_info !== false) {
							$dest_state_id = $dest_state_info['level_2_id'];
							
							$countries_states_info = localities_parse_identifiers_string(
								$regions_info_result->fields['countries_states']);
							
							$num_states = sizeof($countries_states_info);
							
							if ($num_states > 0) {
								$level_2_ids = array();
								foreach ($countries_states_info as $country_state) {
									$level_2_ids[] = $country_state['level_2_id'];
								}
								
								if (in_array($dest_state_id, $level_2_ids)) {
									return $region_info;
								}
								
								// If any of the states which define this region use the default
								// level 2 locality for their level 1 locality then must check all
								// other level 2 localities for main level 1 locality
								$level_2_ids_string = implode(',', $level_2_ids);
								
								$level_1_localities_query = "
									SELECT
										DISTINCT level_1_id
									FROM
										" . TABLE_LOCALITIES_LEVEL_2 . "
									WHERE
										id IN (" . $level_2_ids_string . ")
									AND
										name = '--default--';";
								
								$level_1_localities_result =
									$db->Execute($level_1_localities_query);
								
								$level_1_ids = array();
								
								while (!$level_1_localities_result->EOF) {
									$level_1_ids[] = $level_1_localities_result->fields['level_1_id'];
									
									$level_1_localities_result->MoveNext();
								}
								
								if (sizeof($level_1_ids) > 0) {
									$level_1_ids_string = implode(',', $level_1_ids);
									
									$level_2_localities_query = "
										SELECT
											id
										FROM
											" . TABLE_LOCALITIES_LEVEL_2 . "
										WHERE
											level_1_id IN (" . $level_1_ids_string . ");";
									
									$level_2_localities_result =
										$db->Execute($level_2_localities_query);
									
									$level_2_ids = array();
									
									while (!$level_2_localities_result->EOF) {
										$level_2_ids[] = $level_2_localities_result->fields['id'];
										
										$level_2_localities_result->MoveNext();
									}
									
									if (in_array($dest_state_id, $level_2_ids)) {
										return $region_info;
									}
								}
							}
						}
						
						// Check against the customer's country and city ///////////////////////////
						$dest_city_info = localities_parse_identifier_string($dest_city);
						
						if ($dest_city_info !== false) {
							$dest_city_id = $dest_city_info['level_3_id'];
							
							$countries_cities_info = localities_parse_identifiers_string(
								$regions_info_result->fields['countries_cities']);
							
							$num_cities = sizeof($countries_cities_info);
							
							if ($num_cities > 0) {
								foreach ($countries_cities_info as $country_city) {
									if ($country_city['level_3_id'] == $dest_city_id) {
										return $region_info;
									}
								}
							}
						}
					}
				}
				
				$regions_info_result->MoveNext();
			}
		}
		
		return $region_rates;
	}
	
	// }}}
	
	
	// {{{ _calcMethodRates()
	
	/**
	 * Calculates the applicable rates for the methods, based on their table of rates and the
	 * products they apply to. If the applicable products for a method have no rate defined then the
	 * method will be removed from the list of usable methods for this order.
	 *
	 * @author Conor Kerr <zen-cart.advshipper@dev.ceon.net>
	 * @access protected
	 * @return boolean  True if everything went okay, false if an error occurred which must be shown
	 *                  to the customer.
	 */
	function _calcMethodRates()
	{
		global $order, $currencies;
		
		// Record any methods which have no rates so they can be removed from the list of usable
		// methods
		$methods_with_no_rates = array();
		
		foreach ($this->_methods as $method_num => $method_info) {
			$rate_limits_inc = ($method_info['rate_limits_inc'] == ADVSHIPPER_RATE_LIMITS_INC_INC);
			
			$this->_debug("Region " . $method_info['region'] . " being used" .
				(strlen($method_info['region_title']) > 0 ?
				': ' . $method_info['region_title'] : ''));
			
			$this->_debug("Table of Rates for Region " . $method_info['region'] . ":\n\n" .
				htmlentities(str_replace(',', ', ', $method_info['table_of_rates'])) . "\n", true);
			
			$method_table_of_rates = preg_replace('/\s+/', '', $method_info['table_of_rates']);
			
			// Get the total price, number of items and weight this method applies to
			$method_products_info = array();
			foreach ($method_info['app_product_indexes'] as $product_i) {
				$method_products_info[] = $this->_products[$product_i];
				
				$this->_debug("\n<br />Product for Method $method_num: " .
					"Product Index: $product_i: " .  $this->_products[$product_i]['name'], true);
			}
			
			
			$products_total_price = 0;
			foreach ($method_products_info as $product_info) {
				// Should the total price of the applicable products include the tax?
				if ($method_info['total_up_price_inc_tax'] == ADVSHIPPER_TOTAL_UP_PRICE_INC_TAX_INC) {
					$product_tax_rate = 1.0;
					
					if (STORE_PRODUCT_TAX_BASIS == 'Shipping') {
						$product_tax_rate = zen_get_tax_rate($product_info['tax_class_id'],
							$order->delivery['country']['id'],
							$order->delivery['zone_id']
							);
					} else {
						$product_tax_rate = zen_get_tax_rate($product_info['tax_class_id'],
							$order->billing['country']['id'],
							$order->billing['zone_id']
							);
					}
					
					$product_price = ($product_info['quantity'] *
						$product_info['final_price']) + $product_info['onetime_charges'];
					
					$product_price += zen_calculate_tax($product_price, $product_tax_rate);
					
					$products_total_price += $product_price;
				} else {
					$products_total_price += ($product_info['quantity'] *
						$product_info['final_price']) + $product_info['onetime_charges'];
				}
			}
			
			$this->_debug("\n<br />Total price of " . sizeof($method_products_info) .
				" applicable product(s) for method " . $method_num . ": " .
				$products_total_price, false);
			
			
			$products_num_items = 0;
			foreach ($method_products_info as $product_info) {
				$products_num_items += $product_info['quantity'];
			}
			
			$this->_debug("\n<br />Total number of items of " . sizeof($method_products_info) .
				" applicable product(s) for method " . $method_num . ": " .
				$products_num_items, false);
			
			
			$products_total_weight = 0;
			foreach ($method_products_info as $product_info) {
				$products_total_weight += ($product_info['quantity'] * $product_info['weight']);
			}
			
			$this->_debug("\n<br />Total weight of " . sizeof($method_products_info) .
				" applicable product(s) for method " . $method_num . ": " .
				$products_total_weight, false);
			
			// Calculate the packaging weight (if specified)
			if (strlen($method_info['packaging_weights']) > 0) {
				$packaging_weight = $this->_parseCalcPackagingWeight(
					$method_info['packaging_weights'], $products_total_weight,
					$rate_limits_inc);
				
				if ($packaging_weight === false) {
					$this->_debug("\n<br />Error occurred when trying to parse/calculate " .
						" packaging weight for " . sizeof($method_products_info) .
						" applicable product(s) in package for method " . $method_num .
						": " . $method_info['packaging_weights'], false);
				} else {
					$products_total_weight += $packaging_weight;
					
					$this->_debug("\n<br />Total weight of " . sizeof($method_products_info) .
						" applicable product(s) in package for method " . $method_num .
						" including packaging weight of " . $packaging_weight . ": " .
						$products_total_weight, false);
				}
			}
			
			// Get the total price of *all* products in the order, not just the applicable products
			$order_total_price = 0;
			foreach ($this->_products as $product_info) {
				// Should the total price of all the products include their tax?
				if ($method_info['total_up_price_inc_tax'] == ADVSHIPPER_TOTAL_UP_PRICE_INC_TAX_INC) {
					$product_tax_rate = 1.0;
					
					if (STORE_PRODUCT_TAX_BASIS == 'Shipping') {
						$product_tax_rate = zen_get_tax_rate($product_info['tax_class_id'],
							$order->delivery['country']['id'],
							$order->delivery['zone_id']
							);
					} else {
						$product_tax_rate = zen_get_tax_rate($product_info['tax_class_id'],
							$order->billing['country']['id'],
							$order->billing['zone_id']
							);
					}
					
					$product_price = ($product_info['quantity'] *
						$product_info['final_price']) + $product_info['onetime_charges'];
					
					$product_price += zen_calculate_tax($product_price, $product_tax_rate);
					
					$order_total_price += $product_price;
				} else {
					$order_total_price += ($product_info['quantity'] *
						$product_info['final_price']) + $product_info['onetime_charges'];
				}
			}
			
			$this->_debug("\n<br />Total price of all " . sizeof($this->_products) .
				" product(s) for the order, according to the tax settings for method " .
				$method_num . ": " . $order_total_price, false);
			
			
			$package_weights = array();
			
			// Is there a maximum weight for the applicable product(s) which can be shipped in a
			// single package?
			if (!is_null($method_info['max_weight_per_package']) &&
					is_numeric($method_info['max_weight_per_package'])) {
				$this->_debug("\n<br />Maximum package weight: " .
					$method_info['max_weight_per_package'], false);
				
				if ($products_total_weight > $method_info['max_weight_per_package']) {
					// Applicable products can't be shipped in a single package, work out how many
					// packages would be needed and what the weight of each package would be
					$package_weights = $this->_calcPackageWeights($method_products_info,
						$method_info['max_weight_per_package'], $method_info['packaging_weights'],
						$rate_limits_inc);
					
					if ($package_weights == false) {
						// At least one product is too heavy to be shipped via this method!
						$methods_with_no_rates[] = $method_num;
						
						$this->_debug("\nAt least one product is too heavy to be included in any " .
							"package for method " . $method_num, false);
						
						continue;
					}
				}
			}
			
			if (sizeof($package_weights) == 0) {
				// Only one package is necessary for the applicable products
				$package_weights[] = $products_total_weight;
			}
			
			$num_packages = sizeof($package_weights);
			
			$this->_debug("\n<br />Num of packages required: " . $num_packages, false);
			
			// Calculate the rate for each package
			$rate_info = array();
			
			$total_weight_of_packages = 0;
			
			foreach ($package_weights as $current_package_weight) {
				$this->_debug("\n<br />Package weight: " . $current_package_weight, false);
				
				$total_weight_of_packages += $current_package_weight;
				
				$current_package_rate_info = $this->_calcRate($method_table_of_rates,
					$current_package_weight, $products_total_price, $products_num_items,
					$order_total_price, $rate_limits_inc, $method_num, $method_info['region']);
				
				if ($current_package_rate_info === false) {
					if (isset($this->quotes['error'])) {
						return false;
					}
				} else {
					// Make sure an error hasn't been returned
					if (isset($current_package_rate_info['error'])) {
						$this->quotes['error'] = $current_package_rate_info['error'];
						
						return false;
					}
					
					$this->_debug("\n<br />Package rate: " . $current_package_rate_info[0]['rate'], false);
					
					$num_rates = sizeof($rate_info);
					
					if ($num_rates == 0) {
						// Either only 1 package is being used or this is the first package
						$rate_info = $current_package_rate_info;
					} else {
						// Add the rate(s) for this package onto the current rate(s)
						for ($rate_i = 0; $rate_i < $num_rates; $rate_i++) {
							if (isset($current_package_rate_info[0]['rate_extra_title'])) {
								// This rate is the result of a quote
								
								// Must match up the services used for this package with those used
								// for the other packages. It may be that one or more packages
								// aren't covered by the same services as the others due to their
								// weight being too high or too low
								// @TODO In future maybe different combinations of the carrier's
								// services could be used if a service doesn't cover all the package
								// weights in the order
								$num_package_rates = sizeof($current_package_rate_info);
								
								$rate_matched = false;
								
								for ($current_package_rate_i = 0;
										$current_package_rate_i < $num_package_rates;
										$current_package_rate_i++) {
									if ($rate_info[$rate_i]['rate_extra_title'] ==
											$current_package_rate_info[$current_package_rate_i]['rate_extra_title']) {
										
										$rate_matched = true;
										
										$rate_info[$rate_i]['rate'] +=
											$current_package_rate_info[$current_package_rate_i]['rate'];
										
										$rate_info[$rate_i]['rate_components_info'] = array_merge(
											$rate_info[$rate_i]['rate_components_info'],
											$current_package_rate_info[$current_package_rate_i]['rate_components_info']);
										
										break;
									}
								}
								
								if (!$rate_matched && (!isset($rate_info[$rate_i]['usable']) || $rate_info[$rate_i]['usable'] != false)) {
									// This method can't be used as it doesn't cover all the
									// packages
									$rate_info[$rate_i]['usable'] = false;
									
									$this->_debug("\n<br />Method not usable as no rate returned for at least one of the packages: " . $rate_info[$rate_i]['rate_extra_title'], false);
								}
							} else {
								// This rate is not the result of a quote but instead uses one of
								// the simple calculation methods
								$rate_info[$rate_i]['rate'] +=
									$current_package_rate_info[0]['rate'];
								
								$rate_info[$rate_i]['rate_components_info'] = array_merge(
									$rate_info[$rate_i]['rate_components_info'],
									$current_package_rate_info[0]['rate_components_info']);
							}
						}
					}
				}
			}
			
			$num_rates = sizeof($rate_info);
			
			// Remove any unusable rates
			$usable_rates_info = array();
			for ($rate_i = 0; $rate_i < $num_rates; $rate_i++) {
				if (!isset($rate_info[$rate_i]['usable']) ||
						$rate_info[$rate_i]['usable'] != false) {
					$usable_rates_info[] = $rate_info[$rate_i];
				}
			}
			
			$rate_info = $usable_rates_info;
			unset($usable_rates_info);
			
			$num_rates = sizeof($rate_info);
			
			if ($num_rates > 0) {
				$this->_methods[$method_num]['rates'] = array();
				$this->_methods[$method_num]['package_weights'] = $package_weights;
				
				$tax_rate = zen_get_tax_rate(
					$method_info['tax_class'],
					$order->delivery['country']['id'],
					$order->delivery['zone_id']
					);
				
				$tax_multiplier = 1.0;
				if ($method_info['rates_include_tax'] == ADVSHIPPER_RATES_INC_TAX_INC) {
					// If tax needs to be included, subtract the amount from the shipping quote so
					// that rates can be entered including tax
					if ($method_info['tax_class'] > 0) {
						$tax_multiplier = (100.0 / ($tax_rate + 100.0));
					}
				}
				
				for ($rate_i = 0; $rate_i < $num_rates; $rate_i++) {
					$rate = $rate_info[$rate_i]['rate'];
					$rate_components_info = (isset($rate_info[$rate_i]['rate_components_info']) ?
						$rate_info[$rate_i]['rate_components_info'] : null);
					$rate_extra_title = (isset($rate_info[$rate_i]['rate_extra_title']) ?
						$rate_info[$rate_i]['rate_extra_title'] : null);
					
					if (is_string($rate) && $rate == 'contact') {
						// Shopping basket contains at least one product that requires the customer
						// to contact the store about shipping options before ordering
						$this->_debug(
							"Method $method_num requires that the customer contact the store!",
							true);
						
						$this->quotes['error'] = MODULE_ADVANCED_SHIPPER_TEXT_CONTACT_STORE;
						
						return false;
					} else if (is_string($rate) && $rate == 'contact_after_order') {
						$this->_methods[$method_num]['rates'][$rate_i]['contact_after_order'] =
							true;
						$this->_methods[$method_num]['rates'][$rate_i]['rate'] = 0;
						$this->_methods[$method_num]['rates'][$rate_i]['display_rate'] =
							$currencies->format(round(0, 2));
						$this->_methods[$method_num]['rates'][$rate_i]['rate_calc_desc'] = '';
						$this->_methods[$method_num]['rates'][$rate_i]['display_surcharge'] = '';
						$this->_methods[$method_num]['rates'][$rate_i]['rate_extra_title'] = '';
						
						break;
					}
					
					$this->_debug("Rate identified for Method $method_num: $rate",
						true);
					
					// Adjust the rate for tax if necessary (no change will be made if this isn't
					// necessary as tax multiplier will be 1!)
					$rate = $rate * $tax_multiplier;
					
					$num_rate_components_info = sizeof($rate_components_info);
					
					// Build the information about the rate being used
					$rate_calc_desc = '';
					
					for ($rate_info_i = 0; $rate_info_i < $num_rate_components_info;
							$rate_info_i++) {
						$individual_rate = $rate_components_info[$rate_info_i]['individual_value'];
						
						$num_individual_rates =
							$rate_components_info[$rate_info_i]['num_individual_values'];
						
						if (strpos($num_individual_rates, '.') !== false) {
							// Limit the decimal places for any component to 5 and then remove any
							// zeros from the end
							$num_individual_rates = round($num_individual_rates, 5);
							
							$num_individual_rates =
								preg_replace('/[0]+$/', '', $num_individual_rates);
						}
						
						if (is_null($individual_rate)) {
							if ($rate_components_info[$rate_info_i]['calc_method'] ==
									ADVSHIPPER_CALC_METHOD_WEIGHT ||
									$rate_components_info[$rate_info_i]['calc_method'] ==
									ADVSHIPPER_CALC_METHOD_UPS ||
									$rate_components_info[$rate_info_i]['calc_method'] ==
									ADVSHIPPER_CALC_METHOD_USPS) {	
								$rate_calc_desc .= $num_individual_rates;
								
								if ($num_individual_rates == 1) {
									$rate_calc_desc .=
										MODULE_ADVANCED_SHIPPER_TEXT_WEIGHT_UNIT_SINGULAR;
								} else {
									$rate_calc_desc .=
										MODULE_ADVANCED_SHIPPER_TEXT_WEIGHT_UNIT_PLURAL;
								}
								
								if ($num_packages > 1 || $num_rate_components_info > 1) {
									$rate_calc_desc .= ': ';
								}
							}
							
							if ($num_rate_components_info > 1 ||
									(($rate_components_info[$rate_info_i]['calc_method'] ==
									ADVSHIPPER_CALC_METHOD_WEIGHT ||
									$rate_components_info[$rate_info_i]['calc_method'] ==
									ADVSHIPPER_CALC_METHOD_UPS ||
									$rate_components_info[$rate_info_i]['calc_method'] ==
									ADVSHIPPER_CALC_METHOD_USPS) &&
									$num_packages > 1)) {
								$rate_band_flat_rate =
									$rate_components_info[$rate_info_i]['value_band_total'];
								
								if ($rate_band_flat_rate == 0) {
									$rate_calc_desc .= MODULE_ADVANCED_SHIPPER_TEXT_FREE;
								} else {
									// Adjust the display rate for tax
									$rate_band_flat_rate = $rate_band_flat_rate * $tax_multiplier;
									
									$rate_band_flat_rate =
										zen_add_tax($rate_band_flat_rate, $tax_rate);
									
									$rate_calc_desc .= $currencies->format($rate_band_flat_rate);
								}
							}
						} else {
							if (isset($rate_components_info[$rate_info_i]['block_size'])) {
								if ($rate_components_info[$rate_info_i]['calc_method'] ==
										ADVSHIPPER_CALC_METHOD_WEIGHT ||
										$rate_components_info[$rate_info_i]['calc_method'] ==
										ADVSHIPPER_CALC_METHOD_UPS ||
										$rate_components_info[$rate_info_i]['calc_method'] ==
										ADVSHIPPER_CALC_METHOD_USPS) {
									$rate_calc_desc .=
										$rate_components_info[$rate_info_i]['applicable_value'];
									
									if ($rate_components_info[$rate_info_i]['applicable_value'] == 1) {
										$rate_calc_desc .=
											MODULE_ADVANCED_SHIPPER_TEXT_WEIGHT_UNIT_SINGULAR;
									} else {
										$rate_calc_desc .=
											MODULE_ADVANCED_SHIPPER_TEXT_WEIGHT_UNIT_PLURAL;
									}
								} else if ($rate_components_info[$rate_info_i]['calc_method'] ==
										ADVSHIPPER_CALC_METHOD_PRICE) {
									$rate_calc_desc .= $currencies->format(zen_add_tax(
										$rate_components_info[$rate_info_i]['applicable_value'],
										$tax_rate));
								} else {
									$rate_calc_desc .=
										$rate_components_info[$rate_info_i]['applicable_value'];
								}
								
								$rate_calc_desc .= ': ';
								
								$rate_calc_desc .= $num_individual_rates;
								
								$rate_calc_desc .= ' x ';
								
								if ($individual_rate == 0) {
									$rate_calc_desc .= 
										MODULE_ADVANCED_SHIPPER_TEXT_FREE;
								} else {
									// Adjust the display rate for tax
									$individual_rate = $individual_rate * $tax_multiplier;
									
									$individual_rate = zen_add_tax($individual_rate, $tax_rate);
									
									$rate_calc_desc .= $currencies->format($individual_rate);
								}
								
								$rate_calc_desc .= MODULE_ADVANCED_SHIPPER_TEXT_PER;
								
								if ($rate_components_info[$rate_info_i]['calc_method'] ==
										ADVSHIPPER_CALC_METHOD_WEIGHT) {
									$rate_calc_desc .=
										$rate_components_info[$rate_info_i]['block_size'];
									
									if ($rate_components_info[$rate_info_i]['block_size'] == 1) {
										$rate_calc_desc .=
											MODULE_ADVANCED_SHIPPER_TEXT_WEIGHT_UNIT_SINGULAR;
									} else {
										$rate_calc_desc .=
											MODULE_ADVANCED_SHIPPER_TEXT_WEIGHT_UNIT_PLURAL;
									}
								} else if ($rate_components_info[$rate_info_i]['calc_method'] ==
										ADVSHIPPER_CALC_METHOD_PRICE) {
									$rate_calc_desc .= $currencies->format(
										$rate_components_info[$rate_info_i]['block_size']);
								} else {
									$rate_calc_desc .=
										$rate_components_info[$rate_info_i]['block_size'];
								}
							} else if ($num_individual_rates > 0) {
								$rate_calc_desc .= $num_individual_rates;
								
								if ($rate_components_info[$rate_info_i]['calc_method'] ==
										ADVSHIPPER_CALC_METHOD_WEIGHT) {	
									if ($num_individual_rates == 1) {
										$rate_calc_desc .=
											MODULE_ADVANCED_SHIPPER_TEXT_WEIGHT_UNIT_SINGULAR;
									} else {
										$rate_calc_desc .=
											MODULE_ADVANCED_SHIPPER_TEXT_WEIGHT_UNIT_PLURAL;
									}
								}
								
								$rate_calc_desc .= ' x ';
								
								if ($individual_rate == 0) {
									$rate_calc_desc .= 
										MODULE_ADVANCED_SHIPPER_TEXT_FREE;
								} else {
									// Adjust the display rate for tax
									$individual_rate = $individual_rate * $tax_multiplier;
									
									$individual_rate = zen_add_tax($individual_rate, $tax_rate);
									
									$rate_calc_desc .= $currencies->format($individual_rate);
								}
								
								if ($rate_components_info[$rate_info_i]['calc_method'] ==
										ADVSHIPPER_CALC_METHOD_WEIGHT) {	
									$rate_calc_desc .= '/' .
										MODULE_ADVANCED_SHIPPER_TEXT_WEIGHT_UNIT_SINGULAR;
								}
							}
							
							$additional_charge =
								$rate_components_info[$rate_info_i]['additional_value'];
							
							if ($additional_charge > 0) {
								$additional_charge = $additional_charge * $tax_multiplier;
								
								$additional_charge = zen_add_tax($additional_charge, $tax_rate);
								
								if ($num_individual_rates > 0) {
									$rate_calc_desc .= ' + ';
								}
								$rate_calc_desc .= $currencies->format($additional_charge);
							}
							
							if ($num_individual_rates == 0 && $additional_charge == 0) {
								// This rate band doesn't affect the total, remove any previous
								// joining string (' + ');
								if (strlen($rate_calc_desc) > 0) {
									$rate_calc_desc = substr($rate_calc_desc, 0,
										strlen($rate_calc_desc) - 3);
								}
							}
						}
						
						if ($rate_info_i < $num_rate_components_info - 1) {
							$rate_calc_desc .= ' + ';
						}
					}
					
					$display_surcharge = '';
					
					if (strlen($method_info['surcharge']) > 0) {
						$surcharge_rate_string = $method_info['surcharge'];
						
						$this->_debug("Surcharge rate string being used for method " .
							$method_num . ": $surcharge_rate_string", true);
						
						$surcharge = $this->_calcSurcharge($method_info['surcharge'],
							$total_weight_of_packages, $products_total_price, $products_num_items,
							$order_total_price, $rate, $num_packages, $rate_limits_inc);
						
						if ($surcharge === false) {
							$surcharge = 0;
						}
						
						if ($surcharge > 0) {
							$surcharge = $surcharge * $tax_multiplier;
							
							$rate += $surcharge;
							
							$display_surcharge = zen_add_tax($surcharge, $tax_rate);
							
							$display_surcharge = $currencies->format($display_surcharge);
						}
					}
					
					$display_rate = zen_add_tax($rate, $tax_rate);
					
					$display_rate = $currencies->format($display_rate);
					
					$this->_methods[$method_num]['rates'][$rate_i]['rate'] = $rate;
					$this->_methods[$method_num]['rates'][$rate_i]['display_rate'] =
						$display_rate;
					$this->_methods[$method_num]['rates'][$rate_i]['rate_calc_desc'] =
						$rate_calc_desc;
					$this->_methods[$method_num]['rates'][$rate_i]['display_surcharge'] =
						$display_surcharge;
					$this->_methods[$method_num]['rates'][$rate_i]['rate_extra_title'] =
						$rate_extra_title;
					$this->_methods[$method_num]['rates'][$rate_i]['package_weights_desc'] =
						$package_weights_desc;
				}
			} else {
				// Method can't be used for this order
				$methods_with_no_rates[] = $method_num;
				
				$this->_debug("\nNo rates matched by method " . $method_num, false);
			}
		}
		
		// Remove any methods which have no rates for the list of usable methods
		if (sizeof($methods_with_no_rates) > 0) {
			foreach($methods_with_no_rates as $method_with_no_rates) {
				unset($this->_methods[$method_with_no_rates]);
			}
		}
		
		return true;
	}
	
	// }}}
	
	
	// {{{ _calcRate()

	/**
	 * Examines a table of rates to see if any of the limits for the calculation method(s) within
	 * match the weight/price/number of items for the applicable products. If so, the rate is
	 * calculated and returned.
	 *
	 * @author Conor Kerr <zen-cart.advshipper@dev.ceon.net>
	 * @access protected
	 * @param  string     $table_of_rates  The table of rates.
	 * @param  float      $package_weight  The weight of the applicable products in the package.
	 * @param  float      $products_total_price    The price of the applicable products.
	 * @param  integer    $products_num_items      The number of applicable products.
	 * @param  float      $order_total_price    The total price of all products in the order, not
	 *                                          just the applicable products for a method.
	 * @param  boolean    $rate_limits_inc      Whether or not the limits are inclusive.
	 * @param  integer    $method_num   The number of the method the rate is for.
	 * @param  integer    $region_num   The number of the region of the method the rate is for.
	 * @return array|boolean   An array of arrays containing the rate(s) and any extra info about
	 *                         individual components of the rate(s) and how they were calculated or
	 *                         false if no limits matched and rate(s) calculated.
	 */
	function _calcRate($table_of_rates, $package_weight, $products_total_price, 
		$products_num_items, $order_total_price, $rate_limits_inc, $method_num, $region_num)
	{
		$rate_info = false;
		
		// Get the calculation method for this table of rates
		$pattern = '|^\<([^\>]+)\>|iU';
		
		$calc_method = '';
		
		if (preg_match($pattern, $table_of_rates, $matches)) {
			$calc_method = $matches[1];
		}
		
		if ($calc_method != ADVSHIPPER_CALC_METHOD_WEIGHT &&
				$calc_method != ADVSHIPPER_CALC_METHOD_PRICE &&
				$calc_method != ADVSHIPPER_CALC_METHOD_NUM_ITEMS &&
				$calc_method != ADVSHIPPER_CALC_METHOD_TOTAL_ORDER_PRICE) {
			// Couldn't determine calculation method!
			$this->_debug("Couldn't determine calculation method! " . $table_of_rates, true);
			return false;
		}
		
		$this->_debug("Calculation method being tried: " . $calc_method, true);
		
		// Remove the calculation method tags
		$table_of_rates = preg_replace($pattern, '', $table_of_rates);
		
		$pattern = '|\<\/' . $calc_method . '\>$|iU';
		$table_of_rates = preg_replace($pattern, '', $table_of_rates);
		
		$rate_string = null;
		
		do {
			$this->_debug("Remaining table of rates: " . htmlentities($table_of_rates), true);
			
			// Get the limits
			$limit_string = '';
			
			$limit_rate_divider_pos = strpos($table_of_rates, ':');
			
			if ($limit_rate_divider_pos !== false) {
				$limit_string = substr($table_of_rates, 0, $limit_rate_divider_pos);
				$rate_string = substr($table_of_rates, ($limit_rate_divider_pos + 1),
					strlen($table_of_rates) - $limit_rate_divider_pos);
			} else {
				// Improper format specified for limit/rate
				$this->_debug("Couldn't parse limits/rates: " . $table_of_rates, true);
				return false;
			}
			
			$this->_debug("Parsing limits string: " . $limit_string, true);
			
			// Has a limit range (minimum as well as maximum values) been specified?
			$limits = $this->_parseLimits($limit_string);
			
			if ($limits === false) {
				// Improper format specified for limits
				$this->_debug("Couldn't parse limits: " . $limit_string, true);
				return false;
			}
			
			$minimum_limit = $limits[0];
			$maximum_limit = $limits[1];
			
			// Set value to be compared aginst the limits based on the calculation method
			switch ($calc_method) {
				case ADVSHIPPER_CALC_METHOD_WEIGHT:
					$calc_method_value = $package_weight;
					break;
				case ADVSHIPPER_CALC_METHOD_PRICE:
					$calc_method_value = $products_total_price;
					break;
				case ADVSHIPPER_CALC_METHOD_NUM_ITEMS:
					$calc_method_value = $products_num_items;
					break;
				case ADVSHIPPER_CALC_METHOD_TOTAL_ORDER_PRICE:
					$calc_method_value = $order_total_price;
					break;
			}
			
			$this->_debug("Calculation value being tested against: " . $calc_method_value, true);
			
			if (($maximum_limit == '*' && $calc_method_value < $minimum_limit) ||
					($maximum_limit != '*' &&
					(
					($rate_limits_inc == true &&
					($calc_method_value < $minimum_limit ||
					$calc_method_value > $maximum_limit)) ||
					($rate_limits_inc == false &&
					($calc_method_value < $minimum_limit ||
					$calc_method_value >= $maximum_limit))
					)
					)) {
				// Calculation method value doesn't fall within limits, rate not applicable
				// Move on to next set of limits
				// If rate is an embedded table of rates, must skip right past it
				if (substr($rate_string, 0, 1) == '<') {
					$pattern = '|^\<([^\>]+)\>|iU';
					
					if (!preg_match($pattern, $rate_string, $matches)) {
						// Couldn't parse the rate string!
						$this->_debug("Couldn't parse rate string: " . $rate_string, true);
						return false;
					}
					
					// Extract the table of rates
					$embedded_table_of_rates = $this->_extractElement($rate_string, 0, $matches[1]);
					
					// Remove this embedded table of rates, thereby skipping past it!
					$table_of_rates = substr($table_of_rates, strlen($embedded_table_of_rates));
				}
				
				// Next set of limits will come after the first comma
				$next_rate_comma_pos = strpos($table_of_rates, ',');
				
				if ($next_rate_comma_pos === false) {
					// No more limits/rates to match against
					return false;
				}
				
				// Remove the current limits and rate from the table of rates
				$table_of_rates = substr($table_of_rates, $next_rate_comma_pos + 1,
					strlen($table_of_rates) - 1);
				
				// Attempt to parse the next set of limits & rate
			} else {
				// Limits match
				break;
			}
		} while (1);
		
		// Limits have been matched so attempt to parse and calculate rate /////////////////////////
		
		// First off, check if this "rate" is itself a table of rates
		if (substr($rate_string, 0, 1) == '<') {
			$pattern = '|^\<([^\>]+)\>|iU';
			
			if (!preg_match($pattern, $rate_string, $matches)) {
				// Couldn't parse the rate string!
				$this->_debug("Couldn't parse rate string: " . $rate_string, true);
				return false;
			}
			
			// Extract the table of rates
			$table_of_rates = $this->_extractElement($rate_string, 0, $matches[1]);
			
			// Examine this embedded table of rates
			return $this->_calcRate($table_of_rates, $package_weight,
				$products_total_price, $products_num_items, $order_total_price, $rate_limits_inc,
				$method_num, $region_num);
		}
		
		// Strip any following limits/rates (which will come after a comma)
		$next_rate_comma_pos = strpos($rate_string, ',');
		
		if ($next_rate_comma_pos !== false) {
			$rate_string = substr($rate_string, 0, $next_rate_comma_pos);
		}
		
		$this->_debug("Rate string being used: " . $rate_string, true);
		
		// Rate is a single rate, not a table of rates
		
		// Take a record of and remove any min/max limits on the rate before passing it for
		// calculation
		$min_max = $this->_parseMinMaxLimitsForValueFormat($rate_string);
		
		if (is_array($min_max)) {
			// Min and/or max values have been extracted from the rate format, must use updated
			// string for subsequent calculations
			$rate_string = $min_max['value_format'];
		}
		
		switch (strtolower($rate_string)) {
			case 'contact':
				// Single rate to be returned
				$rate_info[0] = array(
					'rate' => 'contact'
					);
				break;
			case 'contact_after_order':
				// Single rate to be returned
				$rate_info[0] = array(
					'rate' => 'contact_after_order'
					);
				break;
			case ADVSHIPPER_CALC_METHOD_UPS:
				// Multiple rates could be returned if multiple methods available
				$rate_info = $this->_calcUPSRate($package_weight, $method_num, $region_num,
					$min_max);
				break;
			case ADVSHIPPER_CALC_METHOD_USPS:
				// Multiple rates could be returned if multiple methods available
				$rate_info = $this->_calcUSPSRate($package_weight, $products_total_price,
					$method_num, $region_num, $min_max);
				break;
			default:
				// Single rate to be calculated
				switch ($calc_method) {
					case ADVSHIPPER_CALC_METHOD_WEIGHT:
						$rate_info[0] = $this->_getRateForWeight(
							$calc_method_value,
							$rate_string,
							$rate_limits_inc,
							$min_max
							);
						break;
					case ADVSHIPPER_CALC_METHOD_PRICE:
					case ADVSHIPPER_CALC_METHOD_TOTAL_ORDER_PRICE:
						$rate_info[0] = $this->_getRateForPrice(
							$calc_method_value,
							$rate_string,
							$rate_limits_inc,
							$min_max
							);
						break;
					case ADVSHIPPER_CALC_METHOD_NUM_ITEMS:
						$rate_info[0] = $this->_getRateForNumItems(
							$calc_method_value,
							$rate_string,
							$rate_limits_inc,
							$min_max
							);
						break;
				}
		}
		
		return $rate_info;
	}
	
	// }}}
	
	
	// {{{ _calcSurcharge()

	/**
	 * Examines a table of rates to see if any of the limits for the calculation method(s) within
	 * match the weight/price/number of items/shipping rate/number of packages for a method. If so,
	 * the surcharge rate is calculated and returned.
	 *
	 * @author Conor Kerr <zen-cart.advshipper@dev.ceon.net>
	 * @access protected
	 * @param  string     $table_of_rates            The table of rates.
	 * @param  float      $total_weight_of_packages  The total weight of the method's package(s).
	 * @param  float      $products_total_price      The total price of the method's products.
	 * @param  integer    $product_num_items         The number of items in the method.
	 * @param  float      $order_total_price    The total price of all products in the order, not
	 *                                          just the applicable products for a method.
	 * @param  float      $shipping_rate             The calculated shipping rate for the method.
	 * @param  integer    $num_packages              The number of packages being shipped.
	 * @param  boolean    $rate_limits_inc      Whether or not the limits are inclusive.
	 * @return float|false   The surcharge rate or false if an error occured parsing the table of
	 *                       rates.
	 */
	function _calcSurcharge($table_of_rates, $total_weight_of_packages, $products_total_price, 
		$products_num_items, $order_total_price, $shipping_rate, $num_packages, $rate_limits_inc)
	{
		$surcharge = 0;
		
		// Get the calculation method for this table of rates
		$pattern = '|^\<([^\>]+)\>|iU';
		
		$calc_method = '';
		
		if (!preg_match($pattern, $table_of_rates, $matches)) {
			// Simple flat rate being used for surcharge
			$surcharge = (float) $table_of_rates;
			
			$this->_debug("Flat rate being used for surcharge: $surcharge", true);
			
			return $surcharge;
		} else {
			$calc_method = $matches[1];
		}
		
		if ($calc_method != ADVSHIPPER_CALC_METHOD_WEIGHT &&
				$calc_method != ADVSHIPPER_CALC_METHOD_PRICE &&
				$calc_method != ADVSHIPPER_CALC_METHOD_NUM_ITEMS &&
				$calc_method != ADVSHIPPER_CALC_METHOD_TOTAL_ORDER_PRICE &&
				$calc_method != ADVSHIPPER_CALC_METHOD_SHIPPING_RATE &&
				$calc_method != ADVSHIPPER_CALC_METHOD_NUM_PACKAGES) {
			// Couldn't determine calculation method!
			$this->_debug("Couldn't determine calculation method! " . $table_of_rates, true);
			return false;
		}
		
		$this->_debug("Calculation method being tried: " . $calc_method, true);
		
		// Remove the calculation method tags
		$table_of_rates = preg_replace($pattern, '', $table_of_rates);
		
		$pattern = '|\<\/' . $calc_method . '\>$|iU';
		$table_of_rates = preg_replace($pattern, '', $table_of_rates);
		
		$rate_string = null;
		
		do {
			$this->_debug("Remaining table of rates for surcharge: " .
				htmlentities($table_of_rates), true);
			
			// Get the limits
			$limit_string = '';
			
			$limit_rate_divider_pos = strpos($table_of_rates, ':');
			
			if ($limit_rate_divider_pos !== false) {
				$limit_string = substr($table_of_rates, 0, $limit_rate_divider_pos);
				$rate_string = substr($table_of_rates, ($limit_rate_divider_pos + 1),
					strlen($table_of_rates) - $limit_rate_divider_pos);
			} else {
				// Improper format specified for limit/rate
				$this->_debug("Couldn't parse limits/rates: " . $table_of_rates, true);
				return false;
			}
			
			$this->_debug("Parsing limits string: " . $limit_string, true);
			
			// Has a limit range (minimum as well as maximum values) been specified?
			$limits = $this->_parseLimits($limit_string);
			
			if ($limits === false) {
				// Improper format specified for limits
				$this->_debug("Couldn't parse limits: " . $limit_string, true);
				return false;
			}
			
			$minimum_limit = $limits[0];
			$maximum_limit = $limits[1];
			
			// Set value to be compared aginst the limits based on the calculation method
			switch ($calc_method) {
				case ADVSHIPPER_CALC_METHOD_WEIGHT:
					$calc_method_value = $total_weight_of_packages;
					break;
				case ADVSHIPPER_CALC_METHOD_PRICE:
					$calc_method_value = $products_total_price;
					break;
				case ADVSHIPPER_CALC_METHOD_NUM_ITEMS:
					$calc_method_value = $products_num_items;
					break;
				case ADVSHIPPER_CALC_METHOD_TOTAL_ORDER_PRICE:
					$calc_method_value = $order_total_price;
					break;
				case ADVSHIPPER_CALC_METHOD_SHIPPING_RATE:
					$calc_method_value = $shipping_rate;
					break;
				case ADVSHIPPER_CALC_METHOD_NUM_PACKAGES:
					$calc_method_value = $num_packages;
					break;
			}
			
			$this->_debug("Calculation value being tested against: " . $calc_method_value, true);
			
			if (($maximum_limit == '*' && $calc_method_value < $minimum_limit) ||
					($maximum_limit != '*' &&
					(
					($rate_limits_inc == true &&
					($calc_method_value < $minimum_limit ||
					$calc_method_value > $maximum_limit)) ||
					($rate_limits_inc == false &&
					($calc_method_value < $minimum_limit ||
					$calc_method_value >= $maximum_limit))
					)
					)) {
				// Calculation method value doesn't fall within limits, rate not applicable
				// Move on to next set of limits
				// If rate is an embedded table of rates, must skip right past it
				if (substr($rate_string, 0, 1) == '<') {
					$pattern = '|^\<([^\>]+)\>|iU';
					
					if (!preg_match($pattern, $rate_string, $matches)) {
						// Couldn't parse the rate string!
						$this->_debug("Couldn't parse rate string: " . $rate_string, true);
						return false;
					}
					
					// Extract the table of rates
					$embedded_table_of_rates = $this->_extractElement($rate_string, 0, $matches[1]);
					
					// Remove this embedded table of rates, thereby skipping past it!
					$table_of_rates = substr($table_of_rates, strlen($embedded_table_of_rates));
				}
				
				// Next set of limits will come after the first comma
				$next_rate_comma_pos = strpos($table_of_rates, ',');
				
				if ($next_rate_comma_pos === false) {
					// No more limits/rates to match against
					return false;
				}
				
				// Remove the current limits and rate from the table of rates
				$table_of_rates = substr($table_of_rates, $next_rate_comma_pos + 1,
					strlen($table_of_rates) - 1);
				
				// Attempt to parse the next set of limits & rate
			} else {
				// Limits match
				break;
			}
		} while (1);
		
		// Limits have been matched so attempt to parse and calculate rate /////////////////////////
		
		// First off, check if this "rate" is itself a table of rates
		if (substr($rate_string, 0, 1) == '<') {
			$pattern = '|^\<([^\>]+)\>|iU';
			
			if (!preg_match($pattern, $rate_string, $matches)) {
				// Couldn't parse the rate string!
				$this->_debug("Couldn't parse rate string: " . $rate_string, true);
				return false;
			}
			
			// Extract the table of rates
			$table_of_rates = $this->_extractElement($rate_string, 0, $matches[1]);
			
			// Examine this embedded table of rates
			return $this->_calcSurcharge($table_of_rates, $total_weight_of_packages,
				$order_total_price, $order_num_items, $order_total_price, $shipping_rate,
				$num_packages, $rate_limits_inc);
		}
		
		// Strip any following limits/rates (which will come after a comma)
		$next_rate_comma_pos = strpos($rate_string, ',');
		
		if ($next_rate_comma_pos !== false) {
			$rate_string = substr($rate_string, 0, $next_rate_comma_pos);
		}
		
		$this->_debug("Rate string being used: " . $rate_string, true);
		
		// Single rate to be calculated
		
		// Take a record of and remove any min/max limits on the rate before passing it for
		// calculation
		$min_max = $this->_parseMinMaxLimitsForValueFormat($rate_string);
		
		if (is_array($min_max)) {
			// Min and/or max values have been extracted from the rate format, must use updated
			// string for subsequent calculations
			$rate_string = $min_max['value_format'];
		}
		
		switch ($calc_method) {
			case ADVSHIPPER_CALC_METHOD_WEIGHT:
				$surcharge_rate_info = $this->_getRateForWeight(
					$calc_method_value,
					$rate_string,
					$rate_limits_inc,
					$min_max
					);
				break;
			case ADVSHIPPER_CALC_METHOD_PRICE:
			case ADVSHIPPER_CALC_METHOD_TOTAL_ORDER_PRICE:
			case ADVSHIPPER_CALC_METHOD_SHIPPING_RATE:
				$surcharge_rate_info = $this->_getRateForPrice(
					$calc_method_value,
					$rate_string,
					$rate_limits_inc,
					$min_max
					);
				break;
			case ADVSHIPPER_CALC_METHOD_NUM_ITEMS:
			case ADVSHIPPER_CALC_METHOD_NUM_PACKAGES:
				$surcharge_rate_info = $this->_getRateForNumItems(
					$calc_method_value,
					$rate_string,
					$rate_limits_inc,
					$min_max
					);
				break;
		}
		
		$surcharge = $surcharge_rate_info['rate'];
		
		return $surcharge;
	}
	
	// }}}
	
	
	// {{{ _verifyAllProductsHaveUsableMethods()
	
	/**
	 * Checks if every product has at least one usable shipping method.
	 *
	 * @author Conor Kerr <zen-cart.advshipper@dev.ceon.net>
	 * @access protected
	 * @return boolean   True if every product has at least one usable shipping method, false if
	 *                   not.
	 */
	function _verifyAllProductsHaveUsableMethods()
	{
		$num_products = sizeof($this->_products);
		
		$products_usable_method_status = array();
		
		for ($product_i = 0; $product_i < $num_products; $product_i++) {
			if ($this->_products[$product_i]['free_shipping'] == true) {
				$products_usable_method_status[$product_i] = true;
			} else {
				$products_usable_method_status[$product_i] = false;
			}
		}
		
		// Examine the list of applicable products for each usable method
		foreach ($this->_methods as $method_num => $method_info) {
			foreach ($method_info['app_product_indexes'] as $app_product_index) {
				$products_usable_method_status[$app_product_index] = true;
			}
		}
		
		foreach ($products_usable_method_status as $product_i => $product_has_usable_method) {
			if (!$product_has_usable_method) {
				return false;
			}
		}
		
		return true;
		
	}
	
	// }}}
	
	
	// {{{ _createMethodInstances()
	
	/**
	 * Checks that each method is available for use at the current time and creates a single
	 * instance for any non-dated method or as many instances as are required for any dated methods
	 * which can be shown for several weeks in advance.
	 *
	 * @author Conor Kerr <zen-cart.advshipper@dev.ceon.net>
	 * @access protected
	 * @return none
	 */
	function _createMethodInstances()
	{
		global $db;
		
		// Record any methods which aren't available at the current time so they can be removed from
		// the list of usable methods
		$methods_unavailable = array();
		
		$current_timestamp = time();
		
		// Adjust the timestamp to correspond to the store's time, rather than the server's time
		$current_timestamp += ($this->_time_adjust * 3600);
		
		$current_day_of_week = date('w', $current_timestamp);
		
		foreach ($this->_methods as $method_num => $method_info) {
			// Should this method have more than one instance generated?
			$num_method_instances = 1;
			
			// Check if this method is available
			$availability_scheduling = $method_info['availability_scheduling'];
			
			if ($availability_scheduling == ADVSHIPPER_AVAILABILITY_SCHEDULING_ONCE_ONLY) {
				if ($current_timestamp < $method_info['once_only_start_timestamp'] ||
					$current_timestamp > $method_info['once_only_end_timestamp']) {
					// The once only period for this method has either not yet started or is over
					$methods_unavailable[] = $method_num;
					continue;
				}
			}
			
			if ($availability_scheduling == ADVSHIPPER_AVAILABILITY_SCHEDULING_RECURRING) {
				$recurring_mode = $method_info['availability_recurring_mode'];
				$weekly_shipping_scheduling =
					$method_info['availability_weekly_shipping_scheduling'];
				
				if ($recurring_mode == ADVSHIPPER_AVAILABILITY_RECURRING_MODE_WEEKLY) {
					// Has a start date/time been specified? If so, check it!
					
					// Convert the current week's start day of the week and time into a
					// timestamp
					$start_day_of_week = $method_info['availability_weekly_start_day'];
					$start_time = $method_info['availability_weekly_start_time'];
					
					if (!is_null($start_day_of_week)) {
						$start_timestamp = $this->_calcDayOfWeekAndTimeTimestamp(
							$start_day_of_week,
							$start_time
							);
					}
					
					// Convert the current week's cutoff day of the week and time into a
					// timestamp
					$cutoff_day_of_week = $method_info['availability_weekly_cutoff_day'];
					$cutoff_time = $method_info['availability_weekly_cutoff_time'];
					
					$current_week_cutoff_timestamp = $this->_calcDayOfWeekAndTimeTimestamp(
						$cutoff_day_of_week,
						$cutoff_time
						);
					
					if (!is_null($start_day_of_week)) {
						if ($current_week_cutoff_timestamp < $start_timestamp) {
							if ($current_week_cutoff_timestamp < $current_timestamp) {
								$current_week_cutoff_timestamp += 7 * 24 * 3600;
							} else if ($current_week_cutoff_timestamp > $current_timestamp) {
								$start_timestamp -= 7 * 24 * 3600;
							}
						}
					}
					
					if ($current_timestamp < $start_timestamp) {
						// Method has scheduled start date/time which hasn't been reached yet
						$methods_unavailable[] = $method_num;	
						continue;
					}
					
					if (is_null($start_day_of_week) && $weekly_shipping_scheduling !=
							ADVSHIPPER_AVAILABILITY_WEEKLY_SHIPPING_SCHEDULING_NONE) {
						$num_method_instances =
							$method_info['availability_weekly_shipping_show_num_weeks'];
					}
				}
			}
			
			$this->_methods[$method_num]['instances'] = array();
			
			for ($method_instance_i = 0; $method_instance_i < $num_method_instances; $method_instance_i++) {
				// Check if this method instance falls within an active recurring time period
				if ($availability_scheduling == ADVSHIPPER_AVAILABILITY_SCHEDULING_RECURRING) {
					if ($recurring_mode == ADVSHIPPER_AVAILABILITY_RECURRING_MODE_WEEKLY) {
						// Get the timestamp for the current instance's cutoff
						$cutoff_timestamp = $current_week_cutoff_timestamp +
							($method_instance_i * (7 * 24 * 3600));
						
						if ($cutoff_timestamp < $current_timestamp) {
							// The instance for the current week is no longer valid
							continue;
						}
						
						if ($weekly_shipping_scheduling != 'none') {
							$shipping_day_of_week =
								$method_info['availability_weekly_shipping_regular_weekday_day'];
							$shipping_time =
								$method_info['availability_weekly_shipping_regular_weekday_time'];
							
							$shipping_timestamp = $this->_calcDayOfWeekAndTimeTimestamp(
								$shipping_day_of_week,
								$shipping_time
								);
							
							// Get the timestamp for the current instance's shipping
							// date/time
							$shipping_timestamp = $shipping_timestamp +
								($method_instance_i * (7 * 24 * 3600));
							
							while ($shipping_timestamp < $cutoff_timestamp) {
								$shipping_timestamp += 7 * 24 * 3600;
							}
						}
					}
				}
				
				// If this method instance has a shipping date, record information about the date
				// so the method instances can be sorted chronologically and any order using this
				// method instance can have its shipping date recorded.
				$shipping_ts = null;
				if ($availability_scheduling == ADVSHIPPER_AVAILABILITY_SCHEDULING_ONCE_ONLY &&
						!is_null($method_info['once_only_shipping_datetime'])) {
					$shipping_ts = $method_info['once_only_shipping_datetime'];
				}
				
				if ($availability_scheduling == ADVSHIPPER_AVAILABILITY_SCHEDULING_RECURRING &&
						$recurring_mode == ADVSHIPPER_AVAILABILITY_RECURRING_MODE_WEEKLY &&
						$weekly_shipping_scheduling !=
							ADVSHIPPER_AVAILABILITY_WEEKLY_SHIPPING_SCHEDULING_NONE) {
					$shipping_ts = $shipping_timestamp;
				}
					
				if (!is_null($shipping_ts)) {
					// Check if any limit for this method has been reached
					$usage_limit = $method_info['usage_limit'];
					
					if (!is_null($usage_limit) && is_numeric($usage_limit) && $usage_limit > 0) {
						$check_usage_limit_query = "
							SELECT
								count(*) AS usage_count
							FROM
								" . TABLE_ADVANCED_SHIPPER_ORDERS . "
							WHERE
								shipping_ts = '" . date('Y-m-d H:i:00', $shipping_ts) . "';";
						
						$check_usage_limit_result = $db->Execute($check_usage_limit_query);
						
						if (!$check_usage_limit_result->EOF) {
							$usage_count = $check_usage_limit_result->fields['usage_count'];
							
							if ($usage_count >= $usage_limit) {
								// Limit for this method reached
								continue;
							}
						}
					}
				}
				
				$this->_methods[$method_num]['instances'][] = array(
					'timestamp' => $shipping_ts
					);
			}
			
			if (sizeof($this->_methods[$method_num]['instances']) == 0) {
				$this->_debug("\n<br />Method " . $method_num . " not available at this time! ",
					false);
				
				$methods_unavailable[] = $method_num;	
				continue;
			}
		}
		
		// Remove any methods which aren't available at the current time
		if (sizeof($methods_unavailable) > 0) {
			foreach($methods_unavailable as $method_unavailable) {
				unset($this->_methods[$method_unavailable]);
			}
		}
		
		ksort($this->_methods);
		
		$this->_debug(''); // Space out debug info
		
		foreach ($this->_methods as $method_num => $method_info) {
			$method_info_string = '';
			
			foreach ($this->_methods[$method_num]['app_product_indexes'] as $product_i) {
				$method_info_string .=  $product_i . ',';
			}
			$method_info_string = substr($method_info_string, 0,
				strlen($method_info_string) - 1);
			
			$this->_debug("Applicable Product Index(es) for Method $method_num... " .
				$method_info_string);
		}
	}
	
	// }}}
	
	
	// {{{ _getUsableCombinations()
	
	/**
	 * Builds every possible combination of the usable methods and each method's instances which
	 * will satisfy the delivery options for the products in the cart.
	 *
	 * @author Conor Kerr <zen-cart.advshipper@dev.ceon.net>
	 * @access protected
	 * @return array|boolean  An array of all possible method and method instance combinations or
	 *                        false if there are no usable combinations.
	 */
	function _getUsableCombinations()
	{
		// First, must take note of which shipping methods each product can use
		$num_products = sizeof($this->_products);
		
		$products_methods = array();
		
		for ($product_i = 0; $product_i < $num_products; $product_i++) {
			$products_methods[$product_i] = array();
		}
		
		foreach ($this->_methods as $method_num => $method_info) {
			foreach ($method_info['app_product_indexes'] as $product_i) {
				$products_methods[$product_i][] = $method_num;
			}
		}
		
		$this->_debug(''); // Space out debug info
		
		for ($product_i = 0; $product_i < $num_products; $product_i++) {
			$product_methods_string = '';
			
			foreach ($products_methods[$product_i] as $product_method) {
				$product_methods_string .=  $product_method . ', ';
			}
			$product_methods_string = substr($product_methods_string, 0,
				strlen($product_methods_string) - 2);
			
			$this->_debug("Usable Methods for Product Index $product_i... $product_methods_string");
		}
		
		// Build every possible combination of the usable methods, but ensure that each combination
		// provides exactly one shipping method for each product in the cart
		$method_combinations = $this->_getProductMethodCombinations($products_methods);
		
		$this->_debug("\n<br/>Usable methods combinations...");
		$this->_debug($method_combinations);
		
		if ($method_combinations !== false) {
			// Build the instances for each method within each combination
			$method_combinations =
				$this->_getMethodCombinationsWithRateInstancesAndMethodInstances($method_combinations);
		}
		
		return $method_combinations;
	}
	
	// }}}
	
	
	// {{{ _getProductMethodCombinations()
	
	/**
	 * Builds every possible combination of the usable methods, ensuring that each combination
	 * provides exactly one shipping method for each product in the cart.
	 *
	 * @author Conor Kerr <zen-cart.advshipper@dev.ceon.net>
	 * @access protected
	 * @param  array   $products_methods  An array of information about what methods each product
	 *                                    is covered by.
	 * @param  array   $method_combinations  An array of methods which can be combined for all
	 *                                       products previously examined.
	 * @param  array   $products_assigned_methods  An array of the product indexes which have
	 *                                             already been assigned a shipping method.
	 * @param  integer $product_i  The product index to be examined.
	 * @return array|boolean  An array of all possible method combinations for the specified product
	 *                        and all products after it.
	 */
	function _getProductMethodCombinations($products_methods, $method_combinations = array(),
		$products_assigned_methods = array(), $product_i = 0)
	{
		$num_products = sizeof($this->_products);
		
		$additional_method_combinations = array();
		$current_combination_i = 0;
		
		$this->_debug("<br/>Examining methods for Product Index $product_i...");
		$this->_debug($method_combinations, true);
		
		if ($product_i < $num_products) {
			$combination_possible = false;
			
			if ($this->_products[$product_i]['free_shipping'] == true) {
				// Skip free shipping products
				$following_method_combinations = $this->_getProductMethodCombinations(
					$products_methods,
					$method_combinations,
					$products_assigned_methods,
					$product_i + 1
					);
				
				if ($following_method_combinations !== false) {
					$combination_possible = true;
					$additional_method_combinations = $following_method_combinations;
				}
			} else {
				// Is this product already covered by a shipping method?
				$num_used_methods_product_covered_by = 0;
				if (in_array($product_i, $products_assigned_methods)) {
					// Must ensure that product cannot use more than one of the methods already
					// being used
					foreach ($products_methods[$product_i] as $product_method) {
						if (in_array($product_method, $method_combinations)) {
							$num_used_methods_product_covered_by++;
						}
					}
				}
				if ($num_used_methods_product_covered_by > 1) {
					// This product is covered by more than one method currently being used
					// Shipping combination therefore isn't valid!
					$combination_possible = false;
				} else {
					// Product not yet covered by any methods or covered only by one, must check all
					// following product combinations with that generated thus far
					$num_curr_prod_methods = sizeof($products_methods[$product_i]);
					
					for ($curr_prod_method_i = 0; $curr_prod_method_i < $num_curr_prod_methods;
							$curr_prod_method_i++) {
						
						$current_method = $products_methods[$product_i][$curr_prod_method_i];
						
						$new_method_combinations = $method_combinations;
						$new_products_assigned_methods = $products_assigned_methods;
						
						// Is this method already being used?
						$method_being_used = false;
						
						foreach ($method_combinations as $method_combination) {
							if ($method_combination == $current_method) {
								$method_being_used = true;
								break;
							}
						}
						
						$this->_debug("Product Index $product_i -- " .
							"Product Method Index $curr_prod_method_i -- " .
							"Method $current_method: " .
							($method_being_used ? "already being used." :
							"not already being used."));
						
						if (!$method_being_used) {
							// Check if this method covers any products which are already covered
							foreach ($this->_methods[$current_method]['app_product_indexes'] as 
									$app_product_index) {
								if (in_array($app_product_index, $products_assigned_methods)) {
									// Method can't be used in this combination as at least one
									// product would then be using two methods!	
									continue 2;
								}
							}
							
							$new_method_combinations[] = $current_method;
							
							// Mark off the products that this method covers
							foreach ($this->_methods[$current_method]['app_product_indexes'] as 
									$app_product_index) {
								$new_products_assigned_methods[$app_product_index] = $app_product_index;
							}
							
							$this->_debug("<br />Current list of method combinations...");
							$this->_debug($new_method_combinations);
						}
						
						$following_method_combinations = $this->_getProductMethodCombinations(
							$products_methods,
							$new_method_combinations,
							$new_products_assigned_methods,
							($product_i + 1));
						
						if ($following_method_combinations === false) {
							// The combination of all previous methods, this method and the
							// following methods isn't valid!
							continue;
						} else {
							// Combination of all previous methods, this method and the following
							// methods are valid so record the combination's details
							$combination_possible = true;
							
							$num_following = sizeof($following_method_combinations);
							
							if ($num_following > 0) {
								foreach ($following_method_combinations as
										$following_method_combination) {
									$additional_method_combinations[$current_combination_i] = array();
									
									if (!$method_being_used) {
										$additional_method_combinations[$current_combination_i][] =
											$current_method;
									}
									
									$additional_method_combinations[$current_combination_i] =
										array_merge(
											$additional_method_combinations[$current_combination_i],
											$following_method_combination
											);
									
									$current_combination_i++;
								}
							} else {
								if (!$method_being_used) {
									$additional_method_combinations[$current_combination_i] = array();
									
									$additional_method_combinations[$current_combination_i][] =
										$current_method;
									
									$current_combination_i++;
								}
							}
						}
					}
				}
			}
			if ($combination_possible === false) {
				return false;
			}
		}
		
		return $additional_method_combinations;
	}
	
	// }}}
	
	
	// {{{ _getMethodCombinationsWithRateInstancesAndMethodInstances()
	
	/**
	 * Builds every possible combination of the usable methods, with an individual method
	 * combination for each combination of a method's rates and instances. I.e. if method has
	 * several rates, it is effectively split into several methods, one for each rate. In the same
	 * manner, if a method has several shipping timestamps, each of those timestamps is used to
	 * separate the method into several methods, with there being one instance of each for *each*
	 * rate!
	 *
	 * @author Conor Kerr <zen-cart.advshipper@dev.ceon.net>
	 * @access protected
	 * @param  array   $method_combinations  An array of methods which can be combined for all
	 *                                       products previously examined.
	 * @return array  An array of all possible method combinations for all instances of the methods 
	 *                within each overall method combination.
	 */
	function _getMethodCombinationsWithRateInstancesAndMethodInstances($method_combinations)
	{
		$method_and_instance_combinations = array();
		
		$method_comb_i = 0;
		
		foreach ($method_combinations as $method_combination) {
			$num_methods = sizeof($method_combination);
			
			$method_instance_combs =
				$this->_getRateAndMethodInstanceCombinations($method_combination);
			
			foreach ($method_instance_combs as $method_instance_comb) {
				// Build the id string which identifies this combination
				$id_string = '';
				foreach ($method_instance_comb as $method_instance) {
					$id_string .= $method_instance['method'] . '-' . 
						$method_instance['rate_i'] . '-' .
						$method_instance['instance_i'] . '-';
				}
				$id_string = substr($id_string, 0, strlen($id_string) - 1);
				
				$method_and_instance_combinations[$id_string] = $method_instance_comb;
			}
		}
		
		$this->_debug("<br/>Usable method, rate and instance combinations...");
		$this->_debug($method_and_instance_combinations);
		
		return $method_and_instance_combinations;
	}
	
	// }}}
	
	
	// {{{ _getRateAndMethodInstanceCombinations()
	
	/**
	 * Builds every possible combination of the method instances for a method and all following
	 * methods.
	 *
	 * @author Conor Kerr <zen-cart.advshipper@dev.ceon.net>
	 * @access protected
	 * @param  array   $method_nums   An array with the numbers of the methods.
	 * @param  integer $method_i      The method number currently having its combinations built.
	 * @return array  An array of all possible method instance combinations the methods.
	 */
	function _getRateAndMethodInstanceCombinations($method_nums, $method_i = 0)
	{
		$method_instance_combs = array();
		
		$num_methods = sizeof($method_nums);
		$method_num = $method_nums[$method_i];
		
		if ($method_i < ($num_methods - 1)) {
			$following_combs =
				$this->_getRateAndMethodInstanceCombinations($method_nums, $method_i + 1);
			
			$num_rates = sizeof($this->_methods[$method_num]['rates']);
			
			$num_instances = sizeof($this->_methods[$method_num]['instances']);
			
			for ($rate_i = 0; $rate_i < $num_rates; $rate_i++) {
				for ($instance_i = 0; $instance_i < $num_instances; $instance_i++) {
					foreach ($following_combs as $following_comb) {
						$method_instance_comb = array();
						
						$method_instance_comb[] = array(
								'method' => $method_num,
								'rate_i' => $rate_i,
								'instance_i' => $instance_i,
								'timestamp' =>
									$this->_methods[$method_num]['instances'][$instance_i]['timestamp']
							);
						
						foreach ($following_comb as $current_method_instance) {
							$method_instance_comb[] = $current_method_instance;
						}
						
						$method_instance_combs[] = $method_instance_comb;
					}
				}
			}
		} else {
			$num_rates = sizeof($this->_methods[$method_num]['rates']);
			
			$num_instances = sizeof($this->_methods[$method_num]['instances']);
			
			for ($rate_i = 0; $rate_i < $num_rates; $rate_i++) {
				for ($instance_i = 0; $instance_i < $num_instances; $instance_i++) {
					$method_instance_combs[] = array(
						array(
							'method' => $method_num,
							'rate_i' => $rate_i,
							'instance_i' => $instance_i,
							'timestamp' =>
								$this->_methods[$method_num]['instances'][$instance_i]['timestamp']
							)
						);
				}
			}
		}
		
		return $method_instance_combs;
	}
	
	// }}}
	
	
	// {{{ _parseLimits()

	/**
	 * Analyses a limit string to see if a range has been specified and returns the minimum and
	 * maximum values for the limits.
	 *
	 * @author Conor Kerr <zen-cart.advshipper@dev.ceon.net>
	 * @access protected
	 * @param  string  $limit_string   The string defining the limit(s).
	 * @return array|boolean   An array containing the minimum and maximum limits or false if a
	 *                         parsing error occurred.
	 */
	function _parseLimits($limit_string)
	{
		$minimum_limit = 0;
		$maximum_limit = 0;
		
		if (strpos($limit_string, '-') !== false) {
			// Get the minimum and maximum limits
			if (preg_match('/^([0-9\.]+)[\-]([0-9\.]+)/', $limit_string, $limits_array)) {
				$minimum_limit = $limits_array[1];
				$maximum_limit = $limits_array[2];
			} else if (preg_match('/^([0-9\.]+)[\-]\*/', $limit_string, $limits_array)) {
				$minimum_limit = $limits_array[1];
				$maximum_limit = '*';
			} else {
				// Limit(s) not specified properly!
				return false;
			}
		} else {
			// Limit is taken as a maximum limit
			$maximum_limit = $limit_string;
		}
		
		return array($minimum_limit, $maximum_limit);
	}
	
	// }}}
	
	
	// {{{ _calcPackageWeights()

	/**
	 * Works out the minimum number of packages needed to package the applicable products, recording
	 * the weight for each package.
	 *
	 * @author Conor Kerr <zen-cart.advshipper@dev.ceon.net>
	 * @access protected
	 * @param  array    $method_products_info     The information about the applicable products.
	 * @param  integer  $max_weight_per_package   The maximum weight a package can have.
	 * @param  string   $packaging_weight_string  The definition string to be used to calculate the
	 *                                            weight of the packaging for each package.
	 * @param  boolean  $rate_limits_inc          Whether or not the limits are inclusive.
	 * @return array|false    An array of the weights of the packages or false if a product is too
	 *                        heavy to include in any package or an error occurs.
	 */
	function _calcPackageWeights(&$method_products_info, $max_weight_per_package,
		$packaging_weight_string, $rate_limits_inc)
	{
		$package_weights = array();
		
		// Get the weight information for the products
		$product_weights = array();
		foreach ($method_products_info as $product_info) {
			for ($i = 0; $i < $product_info['quantity']; $i++) {
				$product_weights[] = $product_info['weight'];
			}
		}
		
		// Sort the weights in reverse order so heaviest products can be allocated to a package
		// first
		rsort($product_weights);
		
		// Check that no product is too heavy to be included in any package
		$heaviest_product_weight = $product_weights[0];
		
		// Calculate and add on the packaging weight (if specified)
		if (strlen($packaging_weight_string) > 0) {
			$packaging_weight = $this->_parseCalcPackagingWeight($packaging_weight_string,
				$heaviest_product_weight, $rate_limits_inc);
			
			if ($packaging_weight === false) {
				$this->_debug("\n<br />Error occurred when trying to parse/calculate " .
					" packaging weight for heaviest product in package : " .
					$packaging_weight_string, false);
				
				return false;
			} else {
				$heaviest_product_weight += $packaging_weight;
			}
		}
		
		if ($heaviest_product_weight > $max_weight_per_package) {
			// At least one product is too heavy to be included in any package!
			return false;
		}
		
		while (sizeof($product_weights) > 0) {
			$num_package_weights = sizeof($package_weights);
			
			// Add the heaviest product first
			$package_weights[$num_package_weights] = $product_weights[0];
			
			array_splice($product_weights, 0, 1);
			
			// Attempt to add as many other products as possible for this package
			while (sizeof($product_weights) > 0) {
				$num_product_weights = sizeof($product_weights);
				
				for ($i = 0; $i < $num_product_weights; $i++) {
					$current_package_weight_attempt = $package_weights[$num_package_weights] +
						$product_weights[$i];
					
					$this->_debug("\n<br />Attempting to add product weighing " .
						$product_weights[$i] . " to package " . ($num_package_weights + 1) .
						" whose contents currently weigh " .
						$package_weights[$num_package_weights], true);
					
					// Calculate and add on the packaging weight (if specified)
					if (strlen($packaging_weight_string) > 0) {
						$packaging_weight = $this->_parseCalcPackagingWeight(
							$packaging_weight_string, $current_package_weight_attempt,
							$rate_limits_inc);
						
						if ($packaging_weight === false) {
							// Should never get here as error in packaging weight string would have
							// been detected before this point
							return false;
						} else {
							$current_package_weight_attempt += $packaging_weight;
							
							$this->_debug("\n<br />Total weight of product(s) in package " .
								($num_package_weights + 1) . " including packaging weight of " .
								$packaging_weight . ": " . $current_package_weight_attempt, true);
						}
					}
					
					if ($current_package_weight_attempt < $max_weight_per_package) {
						// This product can be included in the package
						$package_weights[$num_package_weights] += $product_weights[$i];
						
						array_splice($product_weights, $i, 1);
						
						break;
					}
					
					if ($i == ($num_product_weights - 1)) {
						// No more products can be included in this package, they're all too heavy
						$this->_debug("\n<br />Can't include any more products in package " .
							($num_package_weights + 1), true);
						
						break 2;
					}
				}
			}
		}
		
		rsort($package_weights);
		
		// Finally, apply any packaging weights to each package
		if (strlen($packaging_weight_string) > 0) {
			for ($i = 0, $n = sizeof($package_weights); $i < $n; $i++) {
				$packaging_weight = $this->_parseCalcPackagingWeight($packaging_weight_string,
					$package_weights[$i], $rate_limits_inc);
				
				if ($packaging_weight === false) {
					// Should never get here as error in packaging weight string would have
					// been detected before this point
					return false;
				} else {
					$package_weights[$i] += $packaging_weight;
				}
			}
		}
		
		return $package_weights;
	}
	
	// }}}
	
	
	// {{{ _parseCalcPackagingWeight()

	/**
	 * Parses a combination rate and calculates the total rate applicable for the order according
	 * to the specified combination.
	 *
	 * @author Conor Kerr <zen-cart.advshipper@dev.ceon.net>
	 * @access protected
	 * @param  string   $packaging_weight_string  The string defining the limit(s) and weight(s) to 
	 *                                            examined.
	 * @param  float    $products_total_weight    The total weight of the applicable products to be
	 *                                            used in calculating the packaging weight.
	 * @param  boolean  $rate_limits_inc          Whether or not the limits are inclusive.
	 * @return float    The calculated weight of the packaging.
	 */
	function _parseCalcPackagingWeight($packaging_weight_string, $products_total_weight,
		$rate_limits_inc)
	{
		$packaging_weight = 0.0;
		
		$packaging_weights_info = split(',', $packaging_weight_string);
		
		$weight_string = null;
		
		for ($i = 0, $num_pwi = sizeof($packaging_weights_info); $i < $num_pwi; $i++) {
			// Get the limits
			$limit_string = '';
			
			$limit_rate_divider_pos = strpos($packaging_weights_info[$i], ':');
			
			if ($limit_rate_divider_pos !== false) {
				$limit_string = substr($packaging_weights_info[$i], 0, $limit_rate_divider_pos);
				$weight_string = substr($packaging_weights_info[$i], ($limit_rate_divider_pos + 1),
					strlen($packaging_weights_info[$i]) - $limit_rate_divider_pos);
			} else {
				// Improper format specified for limit/weight
				$this->_debug("Couldn't parse limits/weights: " . $packaging_weights_info[$i],
					true);
				return false;
			}
			
			$this->_debug("Parsing limits string: " . $limit_string, true);
			
			$limits = $this->_parseLimits($limit_string);
			
			if ($limits === false) {
				// Improper format specified for limits
				$this->_debug("Couldn't parse limits: " . $limit_string, true);
				return false;
			}
			
			$minimum_limit = $limits[0];
			$maximum_limit = $limits[1];
			
			if (($maximum_limit == '*' && $products_total_weight < $minimum_limit) ||
					($maximum_limit != '*' &&
					(
					($rate_limits_inc == true &&
					($products_total_weight < $minimum_limit ||
					$products_total_weight > $maximum_limit)) ||
					($rate_limits_inc == false &&
					($products_total_weight < $minimum_limit ||
					$products_total_weight >= $maximum_limit))
					)
					)) {
				// Limits not matched so can't use this weight string
				$weight_string = null;
			} else {
				// Limit/weight string identified
				break;
			}
		}
		
		if (!is_null($weight_string)) {
			$packaging_weight = $this->_getPackagingWeightForWeight($products_total_weight,
				$weight_string, $rate_limits_inc);
			
			if ($packaging_weight === false) {
				return false;
			}
		}
		
		return $packaging_weight;
	}
	
	// }}}
	
	
	// {{{ _getPackagingWeightForWeight()

	/**
	 * Calculates the packaging weight based on the weight and packaging weight format string
	 * passed.
	 *
	 * @author Conor Kerr <zen-cart.advshipper@dev.ceon.net>
	 * @access protected
	 * @param  float     $weight     The weight for which the packaging weight should be calculated.
	 * @param  string    $packaging_weight_format   The string defining the packaging weight format.
	 * @param  boolean   $limits_inc   Whether any limits for combination values are inclusive or
	 *                                 not.
	 * @return float|false    The packaging weight or false if the value couldn't be calculated.
	 */
	function _getPackagingWeightForWeight($weight, $packaging_weight_format, $limits_inc)
	{
		$packaging_weight = 0.0;
		
		// Take a record of and remove any min/max limits on the weight before passing it for
		// calculation
		$min_max = $this->_parseMinMaxLimitsForValueFormat($packaging_weight_format);
		
		if (is_array($min_max)) {
			// Min and/or max values have been extracted from the weight format, must use updated
			// string for subsequent calculations
			$packaging_weight_format = $min_max['value_format'];
		}
		
		// Check if a combination value has been specified
		// Example format: (1-2:3.00)(3-*:2.00)
		if (substr($packaging_weight_format, 0, 1) == '(') {
			// Get the list of combination values and their limits
			$combination_weights_info =
				$this->_parseCalcCombinationValue($packaging_weight_format, $weight, $limits_inc);
			
			if ($combination_weights_info === false) {
				// Couldn't parse the value properly!
				return false;
			}
			
			$packaging_weight = $combination_weights_info['value_total'];
		} else if (strpos($packaging_weight_format, '[') !== false) {
			// Weight is a block value
			$block_weight_info = $this->_parseCalcBlockValue($packaging_weight_format, $weight);
			
			if ($block_weight_info === false) {
				// Couldn't parse the value properly!
				return false;
			}
			
			$packaging_weight = $block_weight_info['value'];
		} else if (strpos($packaging_weight_format, '%') !== false) {
			// Weight is a percentage
			$percentage_value = $this->_parseCalcPercentageValue($packaging_weight_format,
				$weight);
			
			if ($percentage_value === false) {
				// Couldn't parse the value properly!
				return false;
			}
			
			$packaging_weight = $percentage_value['value'] +
				$percentage_value['additional_value'];
		} else {
			$packaging_weight = $packaging_weight_format;
		}
		
		if ($min_max != false) {
			// Apply the limit(s) to the packaging weight
			$packaging_weight_limited = $this->calcMinMaxValue($packaging_weight, $min_max['min'],
				$min_max['max']);
			
			if ($packaging_weight_limited != $packaging_weight) {
				$packaging_weight = $packaging_weight_limited;
			}
		}
		
		return $packaging_weight;
	}
	
	// }}}
	
	
	// {{{ _getRateForWeight()

	/**
	 * Calculates a rate based on the weight and rate format string passed.
	 *
	 * @author Conor Kerr <zen-cart.advshipper@dev.ceon.net>
	 * @access protected
	 * @param  float      $weight        The weight for which the rate should be calculated.
	 * @param  string     $rate_format   The string defining the rate.
	 * @param  boolean    $limits_inc    Whether any limits for combination rates are inclusive or
	 *                                   not.
	 * @param  array      $min_max       Any minimum/maximum limits which should be applied to
	 *                                   the final rate calculated.
	 * @return array|boolean   An array containing the rate and any extra info about individual
	 *                         parts of the rate and how they were calculated or false if there was
	 *                         a problem parsing the rate format.
	 */
	function _getRateForWeight($weight, $rate_format, $limits_inc, $min_max)
	{
		$rate = 0;
		$rates_info = array();
		
		// Check if a combination rate has been specified
		// Example format: (1-2:3.00)(3-*:2.00)
		if (substr($rate_format, 0, 1) == '(') {
			// Get the list of combination rates and their limits
			$combination_rates_info =
				$this->_parseCalcCombinationValue($rate_format, $weight, $limits_inc);
			
			if ($combination_rates_info === false) {
				// Couldn't parse the rate properly!
				return false;
			}
			
			$rate = $combination_rates_info['value_total'];
			
			$rates_info = $combination_rates_info['values_info'];
			
			// Attribute the calculation method to the rate
			for ($i = 0, $n = sizeof($rates_info); $i < $n; $i++) {
				$rates_info[$i]['calc_method'] = ADVSHIPPER_CALC_METHOD_WEIGHT;
			}
		} else if (strpos($rate_format, '[') !== false) {
			// Value is a block rate, based on weight
			$block_value_info = $this->_parseCalcBlockValue($rate_format, $weight);
			
			if ($block_value_info === false) {
				// Couldn't parse the value properly!
				return false;
			}
			
			$rate_band_rate = $block_value_info['value'];
			
			$block_value = $block_value_info['block_value'];
			$num_blocks = $block_value_info['num_blocks'];
			$block_size = $block_value_info['block_size'];
			
			$rates_info[] = array(
				'value_band_total' => $rate_band_rate,
				'individual_value' => $block_value,
				'num_individual_values' => $num_blocks,
				'block_size' => $block_size,
				'applicable_value' => $weight,
				'additional_value' => null,
				'calc_method' => ADVSHIPPER_CALC_METHOD_WEIGHT
				);
			
			$rate = $rate_band_rate;
		} else if (strpos($rate_format, '%') !== false) {
			// Rate is a percentage based on weight
			$percentage_value = $this->_parseCalcPercentageValue($rate_format, $weight);
			
			if ($percentage_value === false) {
				// Couldn't parse the rate properly!
				return false;
			}
			
			$rate_band_rate = $percentage_value['value'];
			$additional_charge = $percentage_value['additional_value'];
			
			$rates_info[] = array(
				'value_band_total' => $rate_band_rate + $additional_charge,
				'individual_value' => ($weight > 0 ? ($rate_band_rate / $weight) : 0),
				'num_individual_values' => $weight,
				'additional_value' => $additional_charge,
				'calc_method' => ADVSHIPPER_CALC_METHOD_WEIGHT
				);
			
			$rate = $rate_band_rate + $additional_charge;
		} else {
			$rate = $rate_format;
			
			$rates_info[] = array(
				'value_band_total' => $rate,
				'individual_value' => null,
				'num_individual_values' => $weight,
				'additional_value' => null,
				'calc_method' => ADVSHIPPER_CALC_METHOD_WEIGHT
				);
		}
		
		if ($min_max != false) {
			// Apply the limit(s) to the rate
			$rate_limited = $this->calcMinMaxValue($rate, $min_max['min'], $min_max['max']);
			
			if ($rate_limited != $rate) {
				$rate = $rate_limited;
				
				$rates_info = array();
				
				$rates_info[] = array(
					'value_band_total' => $rate,
					'individual_value' => null,
					'num_individual_values' => $weight,
					'additional_value' => null,
					'calc_method' => ADVSHIPPER_CALC_METHOD_WEIGHT
					);
			}
		}
		
		$rate_info = array(
			'rate' => $rate,
			'rate_components_info' => $rates_info
			);
		
		return $rate_info;
	}
	
	// }}}
	
	
	// {{{ _getRateForPrice()

	/**
	 * Calculates a rate based on the price and rate format string passed.
	 *
	 * @author Conor Kerr <zen-cart.advshipper@dev.ceon.net>
	 * @access protected
	 * @param  float      $price         The price for which the rate should be calculated.
	 * @param  string     $rate_format   The string defining the rate.
	 * @param  boolean    $limits_inc    Whether any limits for combination rates are inclusive or
	 *                                   not.
	 * @param  array      $min_max       Any minimum/maximum limits which should be applied to
	 *                                   the final rate calculated.
	 * @return array|boolean   An array containing the rate and any extra info about individual
	 *                         parts of the rate and how they were calculated or false if there was
	 *                         a problem parsing the rate format.
	 */
	function _getRateForPrice($price, $rate_format, $limits_inc, $min_max)
	{
		$rates_info = array();
		
		// Check if a combination rate has been specified
		// Example format: (1-2:3.00)(3-*:2.00)
		if (substr($rate_format, 0, 1) == '(') {
			// Get the list of combination rates and their limits
			$combination_rates_info =
				$this->_parseCalcCombinationValue($rate_format, $price, $limits_inc);
			
			if ($combination_rates_info === false) {
				// Couldn't parse the rate properly!
				return false;
			}
			
			$rate = $combination_rates_info['value_total'];
			
			$rates_info = $combination_rates_info['values_info'];
			
			// Attribute the calculation method to the rate
			for ($i = 0, $n = sizeof($rates_info); $i < $n; $i++) {
				$rates_info[$i]['calc_method'] = ADVSHIPPER_CALC_METHOD_PRICE;
			}
		} if (strpos($rate_format, '[') !== false) {
			// Value is a block rate, based on the price
			$block_value_info = $this->_parseCalcBlockValue($rate_format, $price);
			
			if ($block_value_info === false) {
				// Couldn't parse the value properly!
				return false;
			}
			
			$rate_band_rate = $block_value_info['value'];
			
			$block_value = $block_value_info['block_value'];
			$num_blocks = $block_value_info['num_blocks'];
			$block_size = $block_value_info['block_size'];
			
			$rates_info[] = array(
				'value_band_total' => $rate_band_rate,
				'individual_value' => $block_value,
				'num_individual_values' => $num_blocks,
				'block_size' => $block_size,
				'applicable_value' => $price,
				'additional_value' => null,
				'calc_method' => ADVSHIPPER_CALC_METHOD_PRICE
				);
			
			$rate = $rate_band_rate;
		} else if (strpos($rate_format, '%') !== false) {
			// Rate is a percentage of the order total
			$percentage_value = $this->_parseCalcPercentageValue($rate_format, $price);
			
			if ($percentage_value === false) {
				// Couldn't parse the rate properly!
				return false;
			}
			
			$rate_band_rate = $percentage_value['value'];
			$additional_charge = $percentage_value['additional_value'];
			
			$rates_info[] = array(
				'value_band_total' => $rate_band_rate + $additional_charge,
				'individual_value' => ($price > 0 ? ($rate_band_rate / $price) : 0),
				'num_individual_values' => $price,
				'additional_value' => $additional_charge,
				'calc_method' => ADVSHIPPER_CALC_METHOD_PRICE
				);
			
			$rate = $rate_band_rate + $additional_charge;
		} else {
			$rate = $rate_format;
			
			$rates_info[] = array(
				'value_band_total' => $rate,
				'individual_value' => null,
				'num_individual_values' => $price,
				'additional_value' => null,
				'calc_method' => ADVSHIPPER_CALC_METHOD_PRICE
				);
		}
		
		if ($min_max != false) {
			// Apply the limit(s) to the rate
			$rate_limited = $this->calcMinMaxValue($rate, $min_max['min'], $min_max['max']);
			
			if ($rate_limited != $rate) {
				$rate = $rate_limited;
				
				$rates_info = array();
				
				$rates_info[] = array(
					'value_band_total' => $rate,
					'individual_value' => null,
					'num_individual_values' => $price,
					'additional_value' => null,
					'calc_method' => ADVSHIPPER_CALC_METHOD_PRICE
					);
			}
		}
		
		$rate_info = array(
			'rate' => $rate,
			'rate_components_info' => $rates_info
			);
		
		return $rate_info;
	}
	
	// }}}
	
	
	// {{{ _getRateForNumItems()

	/**
	 * Calculates a rate based on the number of items and rate format string passed.
	 *
	 * @author Conor Kerr <zen-cart.advshipper@dev.ceon.net>
	 * @access protected
	 * @param  float      $num_items    The num of items for which the rate should be calculated.
	 * @param  string     $rate_format  The string defining the rate.
	 * @param  boolean    $limits_inc   Whether any limits for combination rates are inclusive or
	 *                                  not.
	 * @param  array      $min_max       Any minimum/maximum limits which should be applied to
	 *                                   the final rate calculated.
	 * @return array|boolean   An array containing the rate and any extra info about individual
	 *                         parts of the rate and how they were calculated or false if there was
	 *                         a problem parsing the rate format.
	 */
	function _getRateForNumItems($num_items, $rate_format, $limits_inc, $min_max)
	{
		$rate = 0;
		$rates_info = array();
		
		// Check if a combination rate has been specified
		// Example format: (1-2:3.00)(3-*:2.00)
		if (substr($rate_format, 0, 1) == '(') {
			// Get the list of combination rates and their limits
			$combination_rates_info =
				$this->_parseCalcCombinationValue($rate_format, $num_items, $limits_inc);
			
			if ($combination_rates_info === false) {
				// Couldn't parse the rate properly!
				return false;
			}
			
			$rate = $combination_rates_info['value_total'];
			
			$rates_info = $combination_rates_info['values_info'];
			
			// Attribute the calculation method to the rate
			for ($i = 0, $n = sizeof($rates_info); $i < $n; $i++) {
				$rates_info[$i]['calc_method'] = ADVSHIPPER_CALC_METHOD_NUM_ITEMS;
			}
		} else if (strpos($rate_format, '[') !== false) {
			// Value is a block rate, based on the number of items
			$block_value_info = $this->_parseCalcBlockValue($rate_format, $num_items);
			
			if ($block_value_info === false) {
				// Couldn't parse the value properly!
				return false;
			}
			
			$rate_band_rate = $block_value_info['value'];
			
			$block_value = $block_value_info['block_value'];
			$num_blocks = $block_value_info['num_blocks'];
			$block_size = $block_value_info['block_size'];
			
			$rates_info[] = array(
				'value_band_total' => $rate_band_rate,
				'individual_value' => $block_value,
				'num_individual_values' => $num_blocks,
				'block_size' => $block_size,
				'applicable_value' => $num_items,
				'additional_value' => null,
				'calc_method' => ADVSHIPPER_CALC_METHOD_NUM_ITEMS
				);
			
			$rate = $rate_band_rate;
		} else if (strpos($rate_format, '%') !== false) {
			// Rate is a percentage of the number of items
			$percentage_value = $this->_parseCalcPercentageValue($rate_format, $num_items);
			
			if ($percentage_value === false) {
				// Couldn't parse the rate properly!
				return false;
			}
			
			$rate_band_rate = $percentage_value['value'];
			$additional_charge = $percentage_value['additional_value'];
			
			$rates_info[] = array(
				'value_band_total' => $rate_band_rate + $additional_charge,
				'individual_value' => ($rate_band_rate / $num_items),
				'num_individual_values' => $num_items,
				'additional_value' => $additional_charge,
				'calc_method' => ADVSHIPPER_CALC_METHOD_NUM_ITEMS
				);
			
			$rate = $rate_band_rate + $additional_charge;
		} else {
			$rate = $rate_format;
			
			$rates_info[] = array(
				'value_band_total' => $rate,
				'individual_value' => null,
				'num_individual_values' => $num_items,
				'additional_value' => null,
				'calc_method' => ADVSHIPPER_CALC_METHOD_NUM_ITEMS
				);
		}
		
		if ($min_max != false) {
			// Apply the limit(s) to the rate
			$rate_limited = $this->calcMinMaxValue($rate, $min_max['min'], $min_max['max']);
			
			if ($rate_limited != $rate) {
				$rate = $rate_limited;
				
				$rates_info = array();
				
				$rates_info[] = array(
					'value_band_total' => $rate,
					'individual_value' => null,
					'num_individual_values' => $num_items,
					'additional_value' => null,
					'calc_method' => ADVSHIPPER_CALC_METHOD_NUM_ITEMS
					);
			}
		}
		
		$rate_info = array(
			'rate' => $rate,
			'rate_components_info' => $rates_info
			);
		
		return $rate_info;
	}
	
	// }}}
	
	
	// {{{ _parseCalcCombinationValue()

	/**
	 * Parses a combination value and calculates the total value according to the specified
	 * combination.
	 *
	 * @author Conor Kerr <zen-cart.advshipper@dev.ceon.net>
	 * @access protected
	 * @param  string   $combination_value_string   The string defining the limit(s) and value(s) to 
	 *                                              be combined.
	 * @param  float    $limit_num    The number to be used in calculating the value.
	 * @param  boolean  $limits_inc   Whether any limits for combination rates are inclusive or not.
	 * @return array|boolean   An array containing the total rate, and array of rates used (and the
	 *                         number of times each rate is used) or false if a parsing error
	 *                         occurred.
	 */
	function _parseCalcCombinationValue($combination_value_string, $limit_num, $limits_inc)
	{
		$combination_values = array();
		
		$combination_value_string = str_replace(')(', '|', $combination_value_string);
		$combination_value_string = substr($combination_value_string, 1,
			strlen($combination_value_string) - 2);
		
		$combination_values_info = explode('|', $combination_value_string);
		
		$prev_max_limit = 0;
		
		for ($i = 0, $num_cri = sizeof($combination_values_info); $i < $num_cri; $i++) {
			$combination_value_divider_pos = strpos($combination_values_info[$i], ':');
			
			if ($combination_value_divider_pos === false ||
					($combination_value_divider_pos + 1) == strlen($combination_values_info[$i])) {
				// Improper format specified for limit(s)/value
				return false;
			}
			
			$limit_string = substr($combination_values_info[$i], 0, $combination_value_divider_pos);
			
			$current_value_string = substr($combination_values_info[$i],
				$combination_value_divider_pos + 1,
				strlen($combination_values_info[$i]) - ($combination_value_divider_pos + 1));
			
			$limits = $this->_parseLimits($limit_string);
			
			if ($limits === false) {
				// Improper format specified for limits
				$this->_debug("Improper format specified for limits: " . $limit_string);
				
				return false;
			}
			
			$minimum_limit = $limits[0];
			$maximum_limit = $limits[1];
			
			if ($minimum_limit < $prev_max_limit) {
				$minimum_limit = $prev_max_limit;
			}
			
			if ($limit_num < $minimum_limit) {
				// No limits match the number to be used to calcuate the value
				break;
			}
			
			// Determine how many times the current value should be added on
			if ($maximum_limit == '*') {
				$maximum_limit = $limit_num;
			} else if ($limit_num < $maximum_limit) {
				$maximum_limit = $limit_num;
			}
			
			if ($limit_num - $minimum_limit == 0) {
				if ($limits_inc && $minimum_limit == $prev_max_limit) {
					// Limits are inclusive so num within limits of 0 isn't included in this band,
					// as it is part of the previous band.
					$this->_debug("Current number for calculation has a value of zero and ".
						"inclusive limits are being used, with the previous band using its maximum " .
						"value of " . $prev_max_limit . ", therefore no match made for current " .
						"band (" . $limit_string . ").", true);
					
					break;
				} else if (!$limits_inc) {
					$this->_debug("Current number for calculation has a value of zero exactly and ".
						"exclusive limits are being used, with the previous band therefore not " .
						"using its exact maximum value of " . $prev_max_limit . ", therefore match ".
						"made for current band (" . $limit_string . "), with a value of zero!",
						true);
				}
			}
			
			$num_fall_within_limit = $maximum_limit - $prev_max_limit;
			
			if (strpos($current_value_string, '[') !== false) {
				// Value is a block rate, based on the limit number
				$block_value_info = $this->_parseCalcBlockValue($current_value_string,
					$num_fall_within_limit);
				
				if ($block_value_info === false) {
					// Couldn't parse the value properly!
					return false;
				}
				
				$current_combination_value = $block_value_info['value'];
				
				$block_value = $block_value_info['block_value'];
				$num_blocks = $block_value_info['num_blocks'];
				$block_size = $block_value_info['block_size'];
				
				$combination_values['values_info'][] = array(
					'value_band_total' => $current_combination_value,
					'individual_value' => $block_value,
					'num_individual_values' => $num_blocks,
					'block_size' => $block_size,
					'applicable_value' => $num_fall_within_limit,
					'additional_value' => null
					);
			} else if (strpos($current_value_string, '%') !== false) {
				// Value is a percentage of the limit number
				$percentage_value_info = $this->_parseCalcPercentageValue($current_value_string,
					$num_fall_within_limit);
				
				if ($percentage_value_info === false) {
					// Couldn't parse the value properly!
					return false;
				}
				
				$current_combination_value = $percentage_value_info['value'];
				$current_combination_additional_charge =
					$percentage_value_info['additional_value'];
				
				if ($num_fall_within_limit > 0) {
					$individual_value = ($current_combination_value / $num_fall_within_limit);
				} else {
					$individual_value = 0;
				}
				
				$combination_values['values_info'][] = array(
					'value_band_total' => $current_combination_value +
						$current_combination_additional_charge,
					'individual_value' => $individual_value,
					'num_individual_values' => $num_fall_within_limit,
					'additional_value' => $current_combination_additional_charge
					);
			} else {
				$current_combination_value = $current_value_string;
				
				$combination_values['values_info'][] = array(
					'value_band_total' => $current_combination_value,
					'individual_value' => null,
					'num_individual_values' => $num_fall_within_limit,
					'additional_value' => null
					);
			}
			
			$prev_max_limit = $maximum_limit;
		}
		
		if (sizeof($combination_values) == 0) {
			return false;
		}
		
		// Calculate the total value
		$combination_values['value_total'] = 0;
		foreach ($combination_values['values_info'] as $combination_value) {
			$combination_values['value_total'] += $combination_value['value_band_total'];
		}
		
		return $combination_values;
	}
	
	// }}}
	
	
	// {{{ _calcDayOfWeekAndTimeTimestamp()

	/**
	 * Calculates a timestamp for a day of the week and time in either the forthcoming or past
	 * week.
	 *
	 * @author Conor Kerr <zen-cart.advshipper@dev.ceon.net>
	 * @access protected
	 * @param  integer  $day_of_week   The day of the current week.
	 * @param  integer  $time_of_day   The time of day.
	 * @return integer  The UNIX timestamp.
	 */
	function _calcDayOfWeekAndTimeTimestamp($day_of_week, $time_of_day)
	{
		// Get the timestamp for the start of this week
		$current_ts = time();
		
		$current_day = date('w', $current_ts);
		$current_hour = date('G', $current_ts);
		$current_minute = date('i', $current_ts);
		
		$start_of_week_ts = $current_ts - (($current_hour + 1) * 3600) - ($current_minute * 60) -
			($current_day * 24 * 3600);
		
		if (is_string($time_of_day) && preg_match('/^[0-9][0-9]:[0-9][0-9]/', $time_of_day)) {
			$hour = (int) substr($time_of_day, 0, 2);
			$minute = (int) substr($time_of_day, 3, 2);
		} else {
			$hour = 0;
			$minute = 0;
		}
		
		$ts = $start_of_week_ts + ($day_of_week * 24 * 3600) + (($hour + 1) * 3600) +
			($minute * 60);
		
		return $ts;
	}
	
	// }}}
	
	
	// {{{ _parseCalcPercentageValue()

	/**
	 * Calculates a value according to the specified format, based on the base value specified. The
	 * calculated value is a percentage of the base value plus an optional flat rate.
	 *
	 * @author Conor Kerr <zen-cart.advshipper@dev.ceon.net>
	 * @access protected
	 * @param  string   $value_format  A string containing the format of the value to be calculated.
	 * @param  integer  $base_value    The base value to which the value calculation should be
	 *                                 applied.
	 * @return array|boolean    The calculated value and any additional charge, or false if an error
	 *                          occurred.
	 */
	function _parseCalcPercentageValue($value_format, $base_value)
	{
		$percentage_value = 0.0;
		$percentage_value_pos = strpos($value_format, '%');
		
		// Does the percentage value have an additional set charge? (E.g. 3.4% + 0.20)
		$additional_flat_rate_charge = 0.0;
		$additional_flat_rate_charge_pos = strpos($value_format, '+');
		
		if ($additional_flat_rate_charge_pos !== false) {
			if ($additional_flat_rate_charge_pos < $percentage_value_pos) {
				// Percentage value must follow additional flat charge (I.e. 0.20+3.4%)
				// Get the value of the additional set charge
				$additional_flat_rate_charge = substr($value_format, 0,
					$additional_flat_rate_charge_pos);
				
				// Get the percentage value
				$percentage_value = substr($value_format, ($additional_flat_rate_charge_pos + 1),
					$percentage_value_pos - ($additional_flat_rate_charge_pos + 1));
			} else {
				// Percentage value must precede additional flat charge (I.e. 3.4%+0.20)
				// Get the value of the additional set charge
				$additional_flat_rate_charge = substr($value_format,
					($additional_flat_rate_charge_pos + 1), strlen($value_format) -
					($additional_flat_rate_charge_pos + 1));
				
				// Get the percentage value
				$percentage_value = substr($value_format, 0, $percentage_value_pos);
			}
		} else {
			// Get the percentage value
			$percentage_value = substr($value_format, 0, $percentage_value_pos);
		}
		
		if (!is_numeric($additional_flat_rate_charge) || !is_numeric($percentage_value)) {
			// The value format hasn't been specified properly!
			return false;
		}
		
		$value = ($base_value * ($percentage_value / 100));
		
		return array(
			'value' => (float) $value,
			'additional_value' => (float) $additional_flat_rate_charge
			);
	}
	
	// }}}
	
	
	// {{{ _parseCalcBlockValue()

	/**
	 * Calculates a value according to the specified format, based on the base value specified. The
	 * calculated value is a cumulative addition of a value specified in the format, totalled up
	 * according to the number of value "blocks" required to cover the base value.
	 *
	 * @author Conor Kerr <zen-cart.advshipper@dev.ceon.net>
	 * @access protected
	 * @param  string   $value_format  A string containing the format of the value to be calculated.
	 *                                 Expected format: [block_size:block_value]
	 * @param  integer  $base_value    The base value to which the value calculation should be
	 *                                 applied.
	 * @return array|boolean    The calculated value, or false if an error occurred.
	 */
	function _parseCalcBlockValue($value_format, $base_value)
	{
		// Remove the wrapping brackets ([])
		$value_format = substr($value_format, 1, strlen($value_format) - 2);
		
		// Parse the block size and block value
		$block_value_divider_pos = strpos($value_format, ':');
		
		if ($block_value_divider_pos === false ||
				($block_value_divider_pos + 1) == strlen($value_format)) {
			// Improper format specified for block size/value
			return false;
		}
		
		$block_size = substr($value_format, 0, $block_value_divider_pos);
		
		$block_value = substr($value_format, $block_value_divider_pos + 1,
			strlen($value_format) - ($block_value_divider_pos + 1));
		
		// Get the number of "blocks" necessary to cover the base value
		$num_blocks = ceil($base_value / $block_size);
		
		$total_value = (float) $num_blocks * $block_value;
		
		return array(
			'value' => $total_value,
			'num_blocks' => $num_blocks,
			'block_value' => $block_value,
			'block_size' => $block_size
			);
	}
	
	// }}}
	
	
	// {{{ _parseMinMaxLimitsForValueFormat()

	/**
	 * Extracts any minimum or maxiumum limit specifications from a value specification string.
	 *
	 * @author Conor Kerr <zen-cart.advshipper@dev.ceon.net>
	 * @access protected
	 * @param  string   $value_format   A string containing the format of the value to be examined.
	 * @return array|false      An array of the extracted limit(s) or false if the format string 
	 *                          contains no limits.
	 */
	function _parseMinMaxLimitsForValueFormat($value_format)
	{
		$min = null;
		$max = null;
		
		$value_format = preg_replace('|\s|', '', $value_format);
		
		if (preg_match('/.*(min([0-9\.]+)).*/i', $value_format, $match_array)) {
			$min = $match_array[2];
			
			$value_format = str_replace($match_array[1], '', $value_format);
		}
		
		if (preg_match('/.*(max([0-9\.]+)).*/i', $value_format, $match_array)) {
			$max = $match_array[2];
			
			$value_format = str_replace($match_array[1], '', $value_format);
		}
		
		if (!is_null($min) || !is_null($max)) {
			return array(
				'value_format' => $value_format,
				'min' => $min,
				'max' => $max
				);
		}
		
		return false;
	}
	
	// }}}
	
	
	// {{{ calcMinMaxValue()

	/**
	 * Applies minimum and/or maxiumum limits to a value.
	 *
	 * @author Conor Kerr <zen-cart.advshipper@dev.ceon.net>
	 * @access public
	 * @param  float     $value   The value to which the limit(s) should be applied.
	 * @param  float     $min     The min limit.
	 * @param  float     $max     The max limit.
	 * @return float     The calculated value.
	 */
	function calcMinMaxValue($value, $min, $max)
	{
		if (!is_null($min) && $value < $min) {
			return $min;
		} else if (!is_null($max) && $value > $max) {
			return $max;
		}
		
		return $value;
	}
	
	// }}}
	
	
	/// {{{ _buildQuotes()
	
	/* Builds the quotes for this module, using the list of usable method combinations.
	 *
	 * @author  Conor Kerr <zen-cart.advshipper@dev.ceon.net>
	 * @access  public
	 * @param   array  $method_combinations   The information about which method combinations to be
	 *                                        used.
	 * @param   string $selected_method_combination  The ID of the method combination a quote should
	 *                                               be generated for (if any)
	 * @return  boolean     True if first array item comes after second, false otherwise.
	 */
	function _buildQuotes($method_combinations, $selected_method_combination)
	{
		global $order, $currencies;
		
		$num_method_combinations = sizeof($method_combinations);
		
		// Since the quotes can be built, can get the information about any products' attributes
		$this->_storeInfoForProductsAttributes();
		
		foreach ($method_combinations as $method_comb_id => $method_comb) {
			// Has no method been selected by the customer yet or is this the selected method
			// combination?
			if ($selected_method_combination == '' || (zen_not_null($selected_method_combination) &&
					$selected_method_combination == $method_comb_id)) {
				
				$method_comb_title = '';
				$method_comb_cost = 0;
				$shipping_ts = null;
				
				$num_methods_in_comb = sizeof($method_comb);
				
				foreach ($method_comb as $method_instance) {
					$method_num = $method_instance['method'];
					
					$rate_i = $method_instance['rate_i'];
					
					$method_comb_cost += $this->_methods[$method_num]['rates'][$rate_i]['rate'];
					
					if (isset($this->_methods[$method_num]['rates'][$rate_i]['contact_after_order'])) {
						// Handle special case of asking customer to contact shop after completing
						// their order
						$method_comb_title .=
							MODULE_ADVANCED_SHIPPER_TEXT_CONTACT_STORE_AFTER_ORDER;
						
						continue;
					}
					
					// Check if the tax classes for all methods in the combination are the same
					
					if (!is_null($this->_methods[$method_num]['tax_class']) &&
							$this->_methods[$method_num]['tax_class'] != 0) {
						$this->tax_class = $this->_methods[$method_num]['tax_class'];
					}
					
					$current_method_title = $this->_methods[$method_num]['title'];
					
					if (is_null($current_method_title) || strlen($current_method_title) == 0) {
						// Method should always have a title! A new language must have been added
						// since the method was first added
						$current_method_title = MODULE_ADVANCED_SHIPPER_METHOD_TITLE_MISSING;
					}
					
					// Add any information specified about a shipping date/time
					if (!is_null($method_instance['timestamp'])) {
						$current_method_title = strftime($current_method_title,
							$method_instance['timestamp']);
						
						// Use earliest timestamp from all methods in combination as the timestamp
						// for the overall combination
						if (is_null($shipping_ts)) {
							$shipping_ts = $method_instance['timestamp'];
						} else if ($method_instance['timestamp'] < $shipping_ts) {
							$shipping_ts = $method_instance['timestamp'];
						}
					}
					
					// Check if any surcharge title has been specified or if should fall back to
					// default
					if (!is_null($this->_methods[$method_num]['surcharge_title']) &&
							strlen($this->_methods[$method_num]['surcharge_title']) > 0)  {
						$surcharge_desc = $this->_methods[$method_num]['surcharge_title'];
					} else {
						$surcharge_desc = MODULE_ADVANCED_SHIPPER_TEMPLATE_SURCHARGE;
					}
					
					if ($num_methods_in_comb > 1) {
						$shipping_method_title =
							MODULE_ADVANCED_SHIPPER_TEMPLATE_SHIPPING_METHOD_WITH_PRODUCT_INFO;
					} else {
						$shipping_method_title =
							MODULE_ADVANCED_SHIPPER_TEMPLATE_SHIPPING_METHOD_NO_PRODUCT_INFO;
					}
					
					// Check if there are placement markers in the title
					$current_title_has_placement_markers = false;
					if (strpos($current_method_title, '{method_total}') !== false ||
							strpos($current_method_title, '{rate_calc_desc}') !== false ||
							strpos($current_method_title, '{surcharge_info}') !== false ||
							strpos($current_method_title, '{package_weights_desc}') !== false ||
							strpos($current_method_title, '{method_extra_title}') !== false ||
							strpos($shipping_method_title, '{method_total}') !== false ||
							strpos($shipping_method_title, '{rate_calc_desc}') !== false ||
							strpos($shipping_method_title, '{surcharge_info}') !== false ||
							strpos($shipping_method_title, '{package_weights_desc}') !== false ||
							strpos($shipping_method_title, '{method_extra_title}') !== false) {
						// Placement markers found
						$current_title_has_placement_markers = true;
					}
					
					// Should a default title template be constructed?
					if (!$current_title_has_placement_markers) {
						// Does this method have an additional title?
						if (strlen($this->_methods[$method_num]['rates'][$rate_i]['rate_extra_title']) > 0)  {
							$current_method_title .= '{method_extra_title}';
						}
						
						$current_method_title .= 
							MODULE_ADVANCED_SHIPPER_TEMPLATE_METHOD_TOTAL;
						
						if (strlen($this->_methods[$method_num]['rates'][$rate_i]['rate_calc_desc']) > 0)  {
							$current_method_title .= 
								MODULE_ADVANCED_SHIPPER_TEMPLATE_RATE_CALC_DESC;
						}
						
						if (strlen($this->_methods[$method_num]['rates'][$rate_i]['display_surcharge']) > 0)  {
							$current_method_title = trim($current_method_title) . ' ' .
								trim($surcharge_desc);
						}
					}
					
					$shipping_method_title = str_replace('{method_title}',
						$current_method_title, $shipping_method_title);
					
					if ($current_title_has_placement_markers) {
						if (strlen($this->_methods[$method_num]['rates'][$rate_i]['display_surcharge']) > 0) {
							// Add surcharge desc and placement to the title
							$shipping_method_title = str_replace('{surcharge_info}',
								$surcharge_desc, $shipping_method_title);
							}
					}
					
					// Add information to method's title
					$shipping_method_title = str_replace('{method_total}',
						$this->_methods[$method_num]['rates'][$rate_i]['display_rate'],
						$shipping_method_title);
					$shipping_method_title = str_replace('{rate_calc_desc}',
						$this->_methods[$method_num]['rates'][$rate_i]['rate_calc_desc'],
						$shipping_method_title);
					$shipping_method_title = str_replace('{surcharge_amount}',
						$this->_methods[$method_num]['rates'][$rate_i]['display_surcharge'],
						$shipping_method_title);
					$shipping_method_title = str_replace('{region_title}',
						$this->_methods[$method_num]['region_title'],
						$shipping_method_title);
					
					// Does this method have an additional title?
					if (strlen($this->_methods[$method_num]['rates'][$rate_i]['rate_extra_title']) > 0)  {
						if (strpos($shipping_method_title, '{method_extra_title}') !== false) {
							$shipping_method_title = str_replace('{method_extra_title}',
								$this->_methods[$method_num]['rates'][$rate_i]['rate_extra_title'],
								$shipping_method_title);
						} else {
							$shipping_method_title .= 
								$this->_methods[$method_num]['rates'][$rate_i]['rate_extra_title'];
						}
					}
					
					// Build information about the weights of package(s) to be shipped
					$num_packages = sizeof($this->_methods[$method_num]['package_weights']);
					
					$package_weights_desc = '';
					for ($package_weight_i = 0; $package_weight_i < $num_packages;
							$package_weight_i++) {
						$package_weights_desc .=
							$this->_methods[$method_num]['package_weights'][$package_weight_i];
						
						if ($this->_methods[$method_num]['package_weights'][$package_weight_i] == 1) {
							$package_weights_desc .=
								MODULE_ADVANCED_SHIPPER_TEXT_WEIGHT_UNIT_SINGULAR;
						} else {
							$package_weights_desc .=
								MODULE_ADVANCED_SHIPPER_TEXT_WEIGHT_UNIT_PLURAL;
						}
						
						if ($package_weight_i < ($num_packages - 1)) {
							$package_weights_desc .= ' + ';
						}
					}
					
					if ($num_packages == 1) {
						// Add number of packages (to be used alongside rate calc desc )
						$shipping_method_title = str_replace('{num_packages_desc}',
							MODULE_ADVANCED_SHIPPER_TEXT_NUM_PACKAGES_SINGLE,
							$shipping_method_title);
						
						// Build description of package weights (to be used instead of rate calc
						// desc)
						$package_weights_desc = str_replace('{package_weight}',
							$package_weights_desc,
							MODULE_ADVANCED_SHIPPER_TEXT_PACKAGE_WEIGHTS_DESC_SINGLE);
						
						$shipping_method_title = str_replace('{package_weights_desc}',
							$package_weights_desc, $shipping_method_title);
					} else {
						// Using more than one package
						// Add number of packages (to be used alongside rate calc desc )
						$num_packages_desc = str_replace('{num_packages}', $num_packages,
							MODULE_ADVANCED_SHIPPER_TEXT_NUM_PACKAGES_MULTIPLE);
						
						$shipping_method_title = str_replace('{num_packages_desc}',
							$num_packages_desc, $shipping_method_title);
						
						// Build description of package weights (to be used instead of rate calc
						// desc)
						$package_weights_desc = str_replace('{package_weights}',
							$package_weights_desc,
							MODULE_ADVANCED_SHIPPER_TEXT_PACKAGE_WEIGHTS_DESC_MULTIPLE);
						
						$package_weights_desc = str_replace('{num_packages}', $num_packages,
							$package_weights_desc);
						
						$shipping_method_title = str_replace('{package_weights_desc}',
							$package_weights_desc, $shipping_method_title);
					}
					
					if ($num_methods_in_comb > 1) {
						// Add information about the products included in this method
						foreach ($this->_methods[$method_num]['app_product_indexes'] as
								$product_i) {
							$current_product_info = MODULE_ADVANCED_SHIPPER_TEMPLATE_PRODUCT_INFO;
							
							$current_product_info = str_replace('{quantity}',
								$this->_products[$product_i]['quantity'], $current_product_info);
							
							$current_product_info = str_replace('{name}',
								$this->_products[$product_i]['name'], $current_product_info);
							
							// Get information about attributes
							if (isset($this->_products[$product_i]['attribute_names'])) {
								foreach ($this->_products[$product_i]['attribute_names'] as
										$attribute_name) {
									
									$current_attribute_info =
										MODULE_ADVANCED_SHIPPER_TEMPLATE_PRODUCT_INFO_ATTRIBUTE_INFO;
									
									$current_attribute_info = str_replace('{name}',
										$attribute_name[0], $current_attribute_info);
									
									$current_attribute_info = str_replace('{value}',
										$attribute_name[1], $current_attribute_info);
									
									// Add tag for next attribute (if any)
									$current_attribute_info .= '{attribute_info}';
									
									$current_product_info = str_replace('{attribute_info}',
										$current_attribute_info, $current_product_info);
								}
							} else {
								$current_product_info = str_replace('{attribute_info}', '',
									$current_product_info);
							}
							
							// Add tag for next product (if any)
							$current_product_info .= '{product_info}';
							
							$shipping_method_title = str_replace('{product_info}',
								$current_product_info, $shipping_method_title);
						}
					}
					
					// Add spaces before any <br /> or <p> tags so that, when stripped for the
					// plain text title, two lines won't join.
					$pattern = '|\<br[^\>]+\>|iU';
					$replace_pattern = ' \\0';
					$shipping_method_title = preg_replace($pattern, $replace_pattern,
						$shipping_method_title);
					
					$pattern = '|\<p[^\>]+\>|iU';
					$replace_pattern = ' \\0';
					$shipping_method_title = preg_replace($pattern, $replace_pattern,
						$shipping_method_title);
					
					// Remove any left-over placement tags
					$pattern = '|{[^}]+}|iU';
					$shipping_method_title = preg_replace($pattern, '', $shipping_method_title);
					
					$method_comb_title .= $shipping_method_title;
				}
				
				// Remove any trailing newline(s) so the wrapping bracket isn't forced onto a new 
				// line in the plain text version of the title
				$method_comb_title = trim($method_comb_title);
				
				if ($num_methods_in_comb > 1) {
					// Wrap method combination
					$overall_comb_title = MODULE_ADVANCED_SHIPPER_TEMPLATE_SHIPPING_METHOD_COMB;
					$overall_comb_title = str_replace('{method_comb}',
						$method_comb_title, $overall_comb_title);
				} else {
					$overall_comb_title = $method_comb_title;
				}
				
				$this->quotes['methods'][] = array(
						'id' => $method_comb_id,
						'title' => $overall_comb_title,
						'cost' => $method_comb_cost,
						'icon' => '',
						'shipping_ts' => $shipping_ts,
						'quote_i' => sizeof($this->quotes['methods'])
					);
				
				if (zen_not_null($selected_method_combination)) {
					// The selected shipping method has had its quote built, no need to
					// check any further quotes!
					break;
				}
			}
		}
		
		if ($this->tax_class > 0) {
			$this->quotes['tax'] = zen_get_tax_rate(
				$this->tax_class,
				$order->delivery['country']['id'],
				$order->delivery['zone_id']
				);
		}
		
		// Store tax class for use when no quote is generated.. see explanation in constructor
		$_SESSION['advshipper_tax_class'] = $this->tax_class;
		
		if (sizeof($this->quotes["methods"]) > 0) {
			usort($this->quotes["methods"], array($this, "_orderMethods"));
		}
	}
	
	// }}}
	
	
	// {{{ _storeInfoForProductsAttributes()
	
	/**
	 * Stores the option names and value names for any products in the cart which have specific
	 * attribute options selected.
	 *
	 * @author     Conor Kerr <zen-cart.advshipper@dev.ceon.net>
	 * @access     protected
	 * @return     none
	 */
	function _storeInfoForProductsAttributes()
	{
		global $db;
		
		foreach ($this->_products as $product_i => $product) {
			// Get information about attributes
			if (is_array($this->_products[$product_i]['attributes'])) {
				$this->_products[$product_i]['attribute_names'] = array();
				
				foreach ($this->_products[$product_i]['attributes'] as $option_name_id =>
						$option_value_id) {
					
					$option_name_sql = "
						SELECT
							po.products_options_name
						FROM
							" . TABLE_PRODUCTS_OPTIONS . " po
						WHERE
							po.products_options_id = '" . (int) $option_name_id . "'
						AND
							po.language_id = '" . (int) $_SESSION['languages_id'] . "';";
					
					$option_value_name_sql = "
						SELECT
							pov.products_options_values_name
						FROM
							" . TABLE_PRODUCTS_OPTIONS_VALUES . " pov
						WHERE
							pov.products_options_values_id = '" . (int) $option_value_id . "'
						AND
							pov.language_id = '" . (int) $_SESSION['languages_id'] . "';";
					
					$option_name_result = $db->Execute($option_name_sql);
					$option_value_result = $db->Execute($option_value_name_sql);
					
					$this->_products[$product_i]['attribute_names'][] = array(
						$option_name_result->fields['products_options_name'],
						$option_value_result->fields['products_options_values_name']
						);
				}
			}
		}
	}
	
	// }}}
	
	
	// {{{ _orderMethods()
	
	/**
	 * Orders shipping methods according to their timestamp, if they have one, otherwise by the
	 * specified sorting method in the admin.
	 *
	 * @author     Conor Kerr <zen-cart.advshipper@dev.ceon.net>
	 * @access     public
	 * @param      array  $a   The first method to be compared.
	 * @param      array  $b   The second method to be compared.
	 * @return     boolean     True if first array item comes after second, false otherwise.
	 */
	function _orderMethods($a, $b)
	{
		$a_shipping_ts = $a['shipping_ts'];
		$b_shipping_ts = $b['shipping_ts'];
		
		// Sort non-dated (null) methods to bottom of list
		if (is_null($a_shipping_ts) && !is_null($b_shipping_ts)) {
			return true;
		}
		if (is_null($b_shipping_ts) && !is_null($a_shipping_ts)) {
			return false;
		}
		if (is_null($a_shipping_ts) && is_null($b_shipping_ts)) {
			// If both items are non-dated, sort by method order or cost
			if (MODULE_ADVANCED_SHIPPER_METHOD_SORT_ORDER == 'Admin method order') {
				return $a['quote_i'] > $b['quote_i'];
			} else if (MODULE_ADVANCED_SHIPPER_METHOD_SORT_ORDER == 'Cost - lowest to highest') {
				return $a['cost'] > $b['cost'];
			} else {
				return $a['cost'] <= $b['cost'];
			}
		}
		
		return ($a_shipping_ts > $b_shipping_ts);
	}
	
	// }}}
	
	
	// {{{ _calcUPSRate()
	
	/**
	 * Contacts UPS to get a quote for shipping the applicable products from the source to the
	 * customer's shipping address.
	 *
	 * @author     Conor Kerr <zen-cart.advshipper@dev.ceon.net>
	 * @access     public
	 * @param      float   $weight      The weight of the applicable products.
	 * @param      integer $method_num  The number of the method containing the region with the
	 *                                  UPS configuration.
	 * @param      integer $region_num  The number of the region with the UPS configuration.
	 * @param      array   $min_max     Any minimum/maximum limits which should be applied to the 
	 *                                  final rate calculated.
	 * @return     array   An array of rates and method titles or an array containing an error
	 *                     message.
	 */
	function _calcUPSRate($weight, $method_num, $region_num, $min_max)
	{
		global $db;
		
		// Get the configuration for the UPS calculator
		$load_ups_config_sql = "
			SELECT
				*
			FROM
				" . TABLE_ADVANCED_SHIPPER_REGION_UPS_CONFIGS . "
			WHERE
				config_id = '" . $this->_config_id . "'
			AND
				method = '" . $method_num . "'
			AND
				region = '" . $region_num . "';";
		
		$load_ups_config_result = $db->Execute($load_ups_config_sql);
		
		if ($load_ups_config_result->EOF) {
			// Couldn't load config!
			return array(
				'error' => sprintf(MODULE_ADVANCED_SHIPPER_ERROR_NO_UPS_CONFIG, $method_num,
					$region_num)
				);
		} else {
			$ups_config = $load_ups_config_result->fields;
		}
		
		require_once (DIR_FS_CATALOG . DIR_WS_MODULES .
			'shipping/advshipper/class.AdvancedShipperUPSCalculator.php');
		
		$ups_calc = new AdvancedShipperUPSCalculator($ups_config);
		
		return $ups_calc->quote($weight, $min_max);
	}
	
	// }}}
	
	
	// {{{ _calcUSPSRate()
	
	/**
	 * Contacts USPS to get a quote for shipping the applicable products from the source to the
	 * customer's shipping address.
	 *
	 * @author     Conor Kerr <zen-cart.advshipper@dev.ceon.net>
	 * @access     public
	 * @param      float   $weight      The weight of the applicable products.
	 * @param      float   $price       The price of the applicable products.
	 * @param      integer $method_num  The number of the method containing the region with the
	 *                                  USPS configuration.
	 * @param      integer $region_num  The number of the region with the USPS configuration.
	 * @param      array   $min_max     Any minimum/maximum limits which should be applied to the 
	 *                                  final rate calculated.
	 * @return     array   An array of rates and method titles or an array containing an error
	 *                     message.
	 */
	function _calcUSPSRate($weight, $price, $method_num, $region_num, $min_max)
	{
		global $db;
		
		// Get the configuration for the USPS calculator
		$load_ups_config_sql = "
			SELECT
				*
			FROM
				" . TABLE_ADVANCED_SHIPPER_REGION_USPS_CONFIGS . "
			WHERE
				config_id = '" . $this->_config_id . "'
			AND
				method = '" . $method_num . "'
			AND
				region = '" . $region_num . "';";
		
		$load_ups_config_result = $db->Execute($load_ups_config_sql);
		
		if ($load_ups_config_result->EOF) {
			// Couldn't load config!
			return array(
				'error' => sprintf(MODULE_ADVANCED_SHIPPER_ERROR_NO_USPS_CONFIG, $method_num,
					$region_num)
				);
		} else {
			$ups_config = $load_ups_config_result->fields;
		}
		
		require_once (DIR_FS_CATALOG . DIR_WS_MODULES .
			'shipping/advshipper/class.AdvancedShipperUSPSCalculator.php');
		
		$ups_calc = new AdvancedShipperUSPSCalculator($ups_config);
		
		return $ups_calc->quote($weight, $price, $min_max);
	}
	
	// }}}
	
	
	// {{{ _loadConfiguration()

	/**
	 * Loads the module's configuration.
	 *
	 * @author Conor Kerr <zen-cart.advshipper@dev.ceon.net>
	 * @access protected
	 * @return none
	 */
	function _loadConfiguration()
	{
		global $db;
		
		// Load the time adjust value from the general configuration
		$this->_time_adjust = MODULE_ADVANCED_SHIPPER_TIME_ADJUST;
		
		$config_name = 'default';
		
		// Load the main configuration
		$load_main_config_sql = "
			SELECT
				config_id,
				default_method,
				address_not_covered
			FROM
				" . TABLE_ADVANCED_SHIPPER_CONFIGS . "
			WHERE
				config_name = '" . $config_name . "';";
		
		$load_main_config_result = $db->Execute($load_main_config_sql);
		
		if ($load_main_config_result->EOF) {
			// Couldn't load selected config!
			return false;
		} else {
			$this->_config_id = $load_main_config_result->fields['config_id'];
			$this->_default_method = $load_main_config_result->fields['default_method'];
		}
		
		return true;
	}
	
	// }}}
	
	
	// {{{ _debug()

	/**
	 * Outputs debug information to the browser if debugging is enabled. Static method to be used
	 * from other classes.
	 *
	 * @access  public
	 * @static
	 * @param   mixed    $message    The debug information to be displayed or a variable to be
	 *                               dumped.
	 * @param   boolean  $extended   Whether or not this information is "extended" debug info.
	 * @return  none
	 */
	function debug($message, $extended = false)
	{
		advshipper::_debug($message, $extended);
	}
	
	// }}}
	
	// {{{ _debug()

	/**
	 * Outputs debug information to the browser if debugging is enabled.
	 *
	 * @access  private
	 * @param   mixed    $message    The debug information to be displayed or a variable to be
	 *                               dumped.
	 * @param   boolean  $extended   Whether or not this information is "extended" debug info.
	 * @return  none
	 */
	function _debug($message, $extended = false)
	{
		if ((MODULE_ADVANCED_SHIPPER_DEBUG_LEVEL == 'Basic' && !$extended) ||
				MODULE_ADVANCED_SHIPPER_DEBUG_LEVEL == 'Extended') {
			
			if (is_string($message)) {
				echo $message . "<br/>\n";
			} else {
				ob_start();
				var_dump($message);
				$debug_string = '<pre>' . ob_get_clean() . '</pre>';
				echo nl2br($debug_string);
			}
		}
	}
	
	// }}}
	
	
	// {{{ _regionMatchesRangeAU()
	
	/**
	 * Check Australian postcode
	 * 
	 * @param  string  $dest_postcode   The postcode to be checked (Whitespace should already have
	 *                                  been stripped).
	 * @param  string  $postcode_range  A postcode or range of postcodes to check against.
	 * @return boolean|integer          The boolean status of whether or not the postcode was
	 *                                  matched or -1 if an error occurred (Customer's postcode is
	 *                                  in incorrect format to be tested by this method).
	 */
	function _regionMatchesRangeAU($dest_postcode, $postcode_range)
	{
		// Check the postcode is in the correct format
		// Australian postcodes are 4 digits in length in general but some are only 3 digits
		if (!preg_match('/(^[0-9][0-9][0-9][0-9])|(^[8-9][0-9][0-9])|(^[0-2][0-2][0-9])/',
				$dest_postcode)) {
			// Postcode is not in the correct format
			return -1;
		}
		
		// Has a range of postcodes been specified?
		if (strpos($postcode_range, '-') !== false) {
			// Check against the range of postcodes specified
			// Format xxxx-xxxx where x = 0-9, with first digit being significant
			if (preg_match('/^([0-9]+)[\-]([0-9]+)/', $postcode_range, $postcode_range_array)) {
				$start_range = $postcode_range_array[1];
				$end_range = $postcode_range_array[2];
				
				if (substr($dest_postcode, 0, strlen($start_range)) >= $start_range
					&& substr($dest_postcode, 0, strlen($end_range)) <= $end_range) {
					// Postcode matches one of the codes covered by this range!
					return true;
				}
			} else {
				$this->quotes['error'] = sprintf(MODULE_ADVANCED_SHIPPER_ERROR_POSTCODE_PARSE,
					$postcode_range);
			}
		} else {
			// Check against a single postcode
			if (substr($dest_postcode, 0, strlen($postcode_range)) == $postcode_range) {
				// Have matched the postcode
				return true;
			}
		}
		
		return false;
	}
	
	// }}}
	
	
	// {{{ _regionMatchesRangeBE()
	
	/**
	 * Check Belgian postcode
	 * 
	 * @param  string  $dest_postcode   The postcode to be checked (Whitespace should already have
	 *                                  been stripped).
	 * @param  string  $postcode_range  A postcode or range of postcodes to check against.
	 * @return boolean|integer          The boolean status of whether or not the postcode was
	 *                                  matched or -1 if an error occurred (Customer's postcode is
	 *                                  in incorrect format to be tested by this method).
	 */
	function _regionMatchesRangeBE($dest_postcode, $postcode_range)
	{
		// Check the postcode is in the correct format
		// Belgian postcodes are 4 digits in length
		if (!preg_match('/(^[0-9][0-9][0-9][0-9]$)/', $dest_postcode)) {
			// Postcode is not in the correct format
			return -1;
		}
		
		// Has a range of postcodes been specified?
		if (strpos($postcode_range, '-') !== false) {
			// Check against the range of postcodes specified
			// Format xxxx-xxxx where x = 0-9, with first digit being significant
			if (preg_match('/^([0-9]+)[\-]([0-9]+)/', $postcode_range, $postcode_range_array)) {
				$start_range = $postcode_range_array[1];
				$end_range = $postcode_range_array[2];
				
				if (substr($dest_postcode, 0, strlen($start_range)) >= $start_range
					&& substr($dest_postcode, 0, strlen($end_range)) <= $end_range) {
					// Postcode matches one of the codes covered by this range!
					return true;
				}
			} else {
				$this->quotes['error'] = sprintf(MODULE_ADVANCED_SHIPPER_ERROR_POSTCODE_PARSE,
					$postcode_range);
			}
		} else {
			// Check against a single postcode
			if (substr($dest_postcode, 0, strlen($postcode_range)) == $postcode_range) {
				// Have matched the postcode
				return true;
			}
		}
		
		return false;
	}
	
	// }}}
	
	
	// {{{ _regionMatchesRangeCA()
	
	/**
	 * Check Canadian postcode
	 * 
	 * @param  string  $dest_postcode   The postcode to be checked (Whitespace should already have
	 *                                  been stripped).
	 * @param  string  $postcode_range  A postcode or range of postcodes to check against.
	 * @return boolean|integer          The boolean status of whether or not the postcode was
	 *                                  matched or -1 if an error occurred (Customer's postcode is
	 *                                  in incorrect format to be tested by this method).
	 */
	function _regionMatchesRangeCA($dest_postcode, $postcode_range)
	{
		// Check the postcode is in the correct format
		// Canadian postcodes must be of the format X9X 9X9 (whitespace should already have been
		// stripped before calling this method)
		if (!preg_match('/^[a-z][0-9][a-z][0-9][a-z][0-9]$/', $dest_postcode)) {
			// Postcode is not in the correct format
			return -1;
		}
		
		// Has a range of postcodes been specified?
		if (strpos($postcode_range, '-') !== false) {
			// Check against the range of postcodes specified
			// Check if the range is numerical
			if (preg_match('/([a-z])([0-9])[\-]([0-9])/i', $postcode_range, $postcode_range_array)) {
				// Numerical range found (X9-9)
				if (substr($dest_postcode, 0, 1) == $postcode_range_array[1]) {
					$start_range = $postcode_range_array[2];
					$end_range = $postcode_range_array[3];
					
					// Get the number to be compared against the specified numerical range
					$dest_postcode_number = $dest_postcode[1];
					
					if ($dest_postcode_number >= $start_range && $dest_postcode_number <= $end_range) {
						// Postcode matches one of the codes covered by this range!
						return true;
					}
				}
			} else if (preg_match('/([a-z][0-9])([a-z])[\-]([a-z])/i', $postcode_range,
					$postcode_range_array)) {
				// Alphabetic range found (X9X-X)
				// Check if part of code before range matches
				if (substr($dest_postcode, 0, 2) == $postcode_range_array[1]) {
					$start_range = $postcode_range_array[2];
					$end_range = $postcode_range_array[3];
					
					// Get the character to be compared against the specified alphabetic range
					$dest_postcode_character = $dest_postcode[2];
					
					// Get the ASCII code for this character
					$dest_postcode_character_ascii = ord($dest_postcode_character);
					
					if ($dest_postcode_character_ascii >= ord($start_range) &&
							$dest_postcode_character_ascii <= ord($end_range)) {
						// Postcode matches one of the codes covered by this range!
						return true;
					}
				}
			} else {
				$this->quotes['error'] = sprintf(MODULE_ADVANCED_SHIPPER_ERROR_POSTCODE_PARSE,
					$postcode_range);
			}
		} else {
			// Check against a single postcode
			if (substr($dest_postcode, 0, strlen($postcode_range)) == $postcode_range) {
				// Have matched the postcode
				return true;
			}
		}
		
		return false;
	}
	
	// }}}
	
	
	// {{{ _regionMatchesRangeCZ()
	
	/**
	 * Check Czech Republic postcode
	 * 
	 * @param  string  $dest_postcode   The postcode to be checked (Whitespace should already have
	 *                                  been stripped).
	 * @param  string  $postcode_range  A postcode or range of postcodes to check against.
	 * @return boolean|integer          The boolean status of whether or not the postcode was
	 *                                  matched or -1 if an error occurred (Customer's postcode is
	 *                                  in incorrect format to be tested by this method).
	 */
	function _regionMatchesRangeCZ($dest_postcode, $postcode_range)
	{
		// Check the postcode is in the correct format
		// Czech Republic postcodes must be 5 digits in length (normally having a space in the
		// middle but this should ahve been stripped before passing the postcode to this method).
		if (!preg_match('/^[0-9][0-9][0-9][0-9][0-9]$/', $dest_postcode)) {
			// Postcode is not in the correct format
			return -1;
		}
		
		// Has a range of postcodes been specified?
		if (strpos($postcode_range, '-') !== false) {
			// Check against the range of postcodes specified
			// Format xxxxx-xxxxx where x = 0-9, with first digit(s) being significant
			if (preg_match('/^([0-9]+)[\-]([0-9]+)/', $postcode_range, $postcode_range_array)) {
				$start_range = $postcode_range_array[1];
				$end_range = $postcode_range_array[2];
				
				if (substr($dest_postcode, 0, strlen($start_range)) >= $start_range
					&& substr($dest_postcode, 0, strlen($end_range)) <= $end_range) {
					// Postcode matches one of the codes covered by this range!
					return true;
				}
			} else {
				$this->quotes['error'] = sprintf(MODULE_ADVANCED_SHIPPER_ERROR_POSTCODE_PARSE,
					$postcode_range);
			}
		} else {
			// Check against a single postcode
			if (substr($dest_postcode, 0, strlen($postcode_range)) == $postcode_range) {
				// Have matched the postcode
				return true;
			}
		}
		
		return false;
	}
	
	// }}}
	
	
	// {{{ _regionMatchesRangeES()
	
	/**
	 * Check Spanish postcode
	 * 
	 * @param  string  $dest_postcode   The postcode to be checked (Whitespace should already have
	 *                                  been stripped).
	 * @param  string  $postcode_range  A postcode or range of postcodes to check against.
	 * @return boolean|integer          The boolean status of whether or not the postcode was
	 *                                  matched or -1 if an error occurred (Customer's postcode is
	 *                                  in incorrect format to be tested by this method).
	 */
	function _regionMatchesRangeES($dest_postcode, $postcode_range)
	{
		// Check the postcode is in the correct format
		// Spanish postcodes must be 5 digits in length
		if (!preg_match('/^[0-9][0-9][0-9][0-9][0-9]$/', $dest_postcode)) {
			// Postcode is not in the correct format
			return -1;
		}
		
		// Has a range of postcodes been specified?
		if (strpos($postcode_range, '-') !== false) {
			// Check against the range of postcodes specified
			// Format xxxxx-xxxxx where x = 0-9, with first digit(s) being significant
			if (preg_match('/^([0-9]+)[\-]([0-9]+)/', $postcode_range, $postcode_range_array)) {
				$start_range = $postcode_range_array[1];
				$end_range = $postcode_range_array[2];
				
				if (substr($dest_postcode, 0, strlen($start_range)) >= $start_range
					&& substr($dest_postcode, 0, strlen($end_range)) <= $end_range) {
					// Postcode matches one of the codes covered by this range!
					return true;
				}
			} else {
				$this->quotes['error'] = sprintf(MODULE_ADVANCED_SHIPPER_ERROR_POSTCODE_PARSE,
					$postcode_range);
			}
		} else {
			// Check against a single postcode
			if (substr($dest_postcode, 0, strlen($postcode_range)) == $postcode_range) {
				// Have matched the postcode
				return true;
			}
		}
		
		return false;
	}
	
	// }}}
	
	
	// {{{ _regionMatchesRangeFR()
	
	/**
	 * Check French postcode
	 * 
	 * @param  string  $dest_postcode   The postcode to be checked (Whitespace should already have
	 *                                  been stripped).
	 * @param  string  $postcode_range  A postcode or range of postcodes to check against.
	 * @return boolean|integer          The boolean status of whether or not the postcode was
	 *                                  matched or -1 if an error occurred (Customer's postcode is
	 *                                  in incorrect format to be tested by this method).
	 */
	function _regionMatchesRangeFR($dest_postcode, $postcode_range)
	{
		// Check the postcode is in the correct format
		// French postcodes must be 5 digits in length
		if (!preg_match('/^[0-9][0-9][0-9][0-9][0-9]$/', $dest_postcode)) {
			// Postcode is not in the correct format
			return -1;
		}
		
		// Has a range of postcodes been specified?
		if (strpos($postcode_range, '-') !== false) {
			// Check against the range of postcodes specified
			// Format xxxxx-xxxxx where x = 0-9, with first digit(s) being significant
			if (preg_match('/^([0-9]+)[\-]([0-9]+)/', $postcode_range, $postcode_range_array)) {
				$start_range = $postcode_range_array[1];
				$end_range = $postcode_range_array[2];
				
				if (substr($dest_postcode, 0, strlen($start_range)) >= $start_range
					&& substr($dest_postcode, 0, strlen($end_range)) <= $end_range) {
					// Postcode matches one of the codes covered by this range!
					return true;
				}
			} else {
				$this->quotes['error'] = sprintf(MODULE_ADVANCED_SHIPPER_ERROR_POSTCODE_PARSE,
					$postcode_range);
			}
		} else {
			// Check against a single postcode
			if (substr($dest_postcode, 0, strlen($postcode_range)) == $postcode_range) {
				// Have matched the postcode
				return true;
			}
		}
		
		return false;
	}
	
	// }}}
	
	
	// {{{ _regionMatchesRangeGB()
	
	/**
	 * Check UK postcode
	 *
	 * @param  string  $dest_postcode   The postcode to be checked (Whitespace should already have
	 *                                  been stripped).
	 * @param  string  $postcode_range  A postcode or range of postcodes to check against.
	 * @return boolean|integer          The boolean status of whether or not the postcode was
	 *                                  matched or -1 if an error occurred (Customer's postcode is
	 *                                  in incorrect format to be tested by this method).
	 */
	function _regionMatchesRangeGB($dest_postcode, $postcode_range)
	{
		// Check the postcode is in the correct format
		// UK postcodes must be of the format X9 9XX, X99 9XX, X9X 9XX, XX9 9XX, XX99 9XX, XX9X 9XX
		// or BFPO (whitespace should already have been stripped before calling this method)
		if (!preg_match('/^[a-z][0-9][0-9][a-z][a-z]$/', $dest_postcode) &&
			!preg_match('/^[a-z][0-9][0-9][0-9][a-z][a-z]$/', $dest_postcode) &&
			!preg_match('/^[a-z][0-9][a-z][0-9][a-z][a-z]$/', $dest_postcode) &&
			!preg_match('/^[a-z][a-z][0-9][0-9][a-z][a-z]$/', $dest_postcode) &&
			!preg_match('/^[a-z][a-z][0-9][0-9][0-9][a-z][a-z]$/', $dest_postcode) &&
			!preg_match('/^[a-z][a-z][0-9][a-z][0-9][a-z][a-z]$/', $dest_postcode) &&
			!preg_match('/^bfpo[0-9]{1,4}$/', $dest_postcode)) {
			// Postcode is not in the correct format
			return -1;
		}
		
		if (substr($dest_postcode, 0, 4) == 'bfpo') {
			$dest_outbound_code = 'bfpo';
		} else {
			$dest_outbound_code = substr($dest_postcode, 0, strlen($dest_postcode) - 3);
		}
		
		// Has a range of postcodes been specified?
		if (strpos($postcode_range, '-') !== false) {
			// Check against the range of postcodes specified
			// Check if the range is numerical
			if (preg_match('/^([a-z]+)([0-9]+)[\-]([0-9]+)/i', $postcode_range,
					$postcode_range_array)) {
				// Numerical range found (Matches X9 9XX, X99 9XX, XX9 9XX, XX99 9XX or BFPO XXXX)
				
				// Get the beginning alphanumeric part of the customer's postcode
				preg_match('/^([a-z]+)[0-9]+/', $dest_outbound_code, $dest_postcode_array);
				
				if ($dest_outbound_code == 'bfpo' ||
						$dest_postcode_array[1] == $postcode_range_array[1]) {
					$start_range = $postcode_range_array[2];
					$end_range = $postcode_range_array[3];
					
					// Get the significant digit(s) to be compared against the specified range
					// (Make sure that the beginning of the second part of the postcode doesn't
					// get taken as the end of the first! BT2 3JJ shouldn't be mistaken for
					// BT23 3JJ)!
					if ($dest_outbound_code == 'bfpo') {
						$dest_postcode_numerical_part = substr($dest_postcode, 4,
							strlen($dest_postcode) - 4);
					} else if (strlen($dest_postcode) == 5) {
						$dest_postcode_numerical_part = substr($dest_postcode, 1, 1);
					} else if (strlen($dest_postcode) == 6) {
						if (preg_match('/^[a-z][0-9]/i', $dest_postcode)) {
							$dest_postcode_numerical_part = substr($dest_postcode, 1, 2);
						} else {
							$dest_postcode_numerical_part = substr($dest_postcode, 2, 1);
						}
					} else if (strlen($dest_postcode) == 7) {
						$dest_postcode_numerical_part = substr($dest_postcode, 2, 2);
					}
					
					if ($dest_postcode_numerical_part >= $start_range &&
							$dest_postcode_numerical_part <= $end_range) {
						// Postcode matches one of the codes covered by this range!
						return true;
					}
				}
			} else if (preg_match('/^([a-z]+[0-9])([a-z])[\-]([0-9])/i', $postcode_range,
					$postcode_range_array)) {
				// Alphabetic range found (Matches X9X 9XX or XX9X 9XX)
				// Check if part of code before range matches
				if (substr($dest_postcode, 0, strlen($postcode_range_array[1])) ==
						$postcode_range_array[1]) {
					$start_range = $postcode_range_array[2];
					$end_range = $postcode_range_array[3];
					
					// Get the character to be compared against the specified alphabetic range
					$dest_postcode_character = $dest_postcode[strlen($postcode_range_array[1])];
					
					// Get the ASCII code for this character
					$dest_postcode_character_ascii = ord($dest_postcode_character);
					
					if ($dest_postcode_character_ascii >= ord($start_range) &&
							$dest_postcode_character_ascii <= ord($end_range)) {
						// Postcode matches one of the codes covered by this range!
						return true;
					}
				}
			} else {
				$this->quotes['error'] = sprintf(MODULE_ADVANCED_SHIPPER_ERROR_POSTCODE_PARSE,
					$postcode_range);
			}
		} else {
			// Check against a single postcode
			if ($dest_outbound_code == 'bfpo') {
				if ($dest_postcode == $postcode_range) {
					return true;
				}
			} else if (strlen($dest_outbound_code) == strlen($postcode_range)) {
				if ($dest_outbound_code == $postcode_range) {
					// Have matched the postcode
					return true;
				}
			} else {
				if (strlen($postcode_range) == 1) {
					if (strlen($dest_outbound_code) == 2) {
						// Matched postcode of format X9 9XX
						if (preg_match('/^[a-z][0-9]/i', $dest_outbound_code)) {
							if (substr($dest_outbound_code, 0, 1) == $postcode_range) {
								return true;
							}
						}
					} else if (strlen($dest_outbound_code) == 3) {
						// Matched postcode of format X99 9XX or X9X 9XX
						if (preg_match('/^[a-z][0-9][0-9]/i', $dest_outbound_code)
								|| preg_match('/^[a-z][0-9][a-z]/i', $dest_outbound_code)) {
							if (substr($dest_outbound_code, 0, 1) == $postcode_range) {
								return true;
							}
						}
					}
				} else if (strlen($postcode_range) == 2) {
					if (strlen($dest_outbound_code) == 3) {
						// Matched postcode of format X9X 9XX or XX9 9XX
						if (preg_match('/^[a-z][0-9][a-z]/i', $dest_outbound_code)
								|| preg_match('/^[a-z][a-z][0-9]/i', $dest_outbound_code)) {
							if (substr($dest_outbound_code, 0, 2) == $postcode_range) {
								return true;
							}
						}
					} else if (strlen($dest_outbound_code) == 4) {
						// Matched postcode of format XX99 9XX or XX9X 9XX
						if (substr($dest_outbound_code, 0, 2) == $postcode_range) {
							return true;
						}
					}
				} else if (strlen($postcode_range) == 3) {
					if (strlen($dest_outbound_code) == 4) {
						// Matched postcode of format XX9X 9XX
						if (preg_match('/^[a-z][a-z][0-9][a-z]/i', $dest_outbound_code)) {
							if (substr($dest_outbound_code, 0, 3) == $postcode_range) {
								return true;
							}
						}
					}
				}
			}
		}
		
		return false;
	}
	
	// }}}
	
	
	// {{{ _regionMatchesRangeIE()
	
	/**
	 * Check Irish postcode
	 * 
	 * @param  string  $dest_postcode   The postcode to be checked (Whitespace should already have
	 *                                  been stripped).
	 * @param  string  $postcode_range  A postcode or range of postcodes to check against.
	 * @return boolean|integer          The boolean status of whether or not the postcode was
	 *                                  matched or -1 if an error occurred (Customer's postcode is
	 *                                  in incorrect format to be tested by this method).
	 */
	function _regionMatchesRangeIE($dest_postcode, $postcode_range)
	{
		// Check the postcode is in the correct format
		// Irish postcodes are plain text, possibly with a number after them
		
		// Check against a single postcode
		if (substr($dest_postcode, 0, strlen($postcode_range)) == $postcode_range) {
			// Have matched the postcode
			return true;
		}
		
		return false;
	}
	
	// }}}
	
	
	// {{{ _regionMatchesRangeIN()
	
	/**
	 * Check Indian postcode
	 * 
	 * @param  string  $dest_postcode   The postcode to be checked (Whitespace should already have
	 *                                  been stripped).
	 * @param  string  $postcode_range  A postcode or range of postcodes to check against.
	 * @return boolean|integer          The boolean status of whether or not the postcode was
	 *                                  matched or -1 if an error occurred (Customer's postcode is
	 *                                  in incorrect format to be tested by this method).
	 */
	function _regionMatchesRangeIN($dest_postcode, $postcode_range)
	{
		// Check the postcode is in the correct format
		// Indian postcodes must be 6 digits in length
		if (!preg_match('/^[0-9][0-9][0-9][0-9][0-9][0-9]$/', $dest_postcode)) {
			// Postcode is not in the correct format
			return -1;
		}
		
		// Has a range of postcodes been specified?
		if (strpos($postcode_range, '-') !== false) {
			// Check against the range of postcodes specified
			// Format xxxx-xxxx where x = 0-9, with first digit being significant
			if (preg_match('/^([0-9]+)[\-]([0-9]+)/', $postcode_range, $postcode_range_array)) {
				$start_range = $postcode_range_array[1];
				$end_range = $postcode_range_array[2];
				
				if (substr($dest_postcode, 0, strlen($start_range)) >= $start_range
					&& substr($dest_postcode, 0, strlen($end_range)) <= $end_range) {
					// Postcode matches one of the codes covered by this range!
					return true;
				}
			} else {
				$this->quotes['error'] = sprintf(MODULE_ADVANCED_SHIPPER_ERROR_POSTCODE_PARSE,
					$postcode_range);
			}
		} else {
			// Check against a single postcode
			if (substr($dest_postcode, 0, strlen($postcode_range)) == $postcode_range) {
				// Have matched the postcode
				return true;
			}
		}
		
		return false;
	}
	
	// }}}
	
	
	// {{{ _regionMatchesRangeIT()
	
	/**
	 * Check Italian postcode
	 * 
	 * @param  string  $dest_postcode   The postcode to be checked (Whitespace should already have
	 *                                  been stripped).
	 * @param  string  $postcode_range  A postcode or range of postcodes to check against.
	 * @return boolean|integer          The boolean status of whether or not the postcode was
	 *                                  matched or -1 if an error occurred (Customer's postcode is
	 *                                  in incorrect format to be tested by this method).
	 */
	function _regionMatchesRangeIT($dest_postcode, $postcode_range)
	{
		// Check the postcode is in the correct format
		// Italian postcodes are 5 digits in length, with an optional two character prefix
		if (!preg_match('/^[a-z]?[a-z]?\-?([0-9][0-9][0-9][0-9][0-9])$/', $dest_postcode,
				$postcode_parts)) {
			// Postcode is not in the correct format
			return -1;
		}
		
		// Ignore any prefix
		$dest_postcode = $postcode_parts[1];
		
		// Has a range of postcodes been specified?
		if (strpos($postcode_range, '-') !== false) {
			// Check against the range of postcodes specified
			// Format xxxx-xxxx where x = 0-9, with first digit being significant
			if (preg_match('/^([0-9]+)[\-]([0-9]+)/', $postcode_range, $postcode_range_array)) {
				$start_range = $postcode_range_array[1];
				$end_range = $postcode_range_array[2];
				
				if (substr($dest_postcode, 0, strlen($start_range)) >= $start_range
					&& substr($dest_postcode, 0, strlen($end_range)) <= $end_range) {
					// Postcode matches one of the codes covered by this range!
					return true;
				}
			} else {
				$this->quotes['error'] = sprintf(MODULE_ADVANCED_SHIPPER_ERROR_POSTCODE_PARSE,
					$postcode_range);
			}
		} else {
			// Check against a single postcode
			if (substr($dest_postcode, 0, strlen($postcode_range)) == $postcode_range) {
				// Have matched the postcode
				return true;
			}
		}
		
		return false;
	}
	
	// }}}
	
	
	// {{{ _regionMatchesRangeMY()
	
	/**
	 * Check Malaysian postcode
	 * 
	 * @param  string  $dest_postcode   The postcode to be checked (Whitespace should already have
	 *                                  been stripped).
	 * @param  string  $postcode_range  A postcode or range of postcodes to check against.
	 * @return boolean|integer          The boolean status of whether or not the postcode was
	 *                                  matched or -1 if an error occurred (Customer's postcode is
	 *                                  in incorrect format to be tested by this method).
	 */
	function _regionMatchesRangeMY($dest_postcode, $postcode_range)
	{
		// Check the postcode is in the correct format
		// Malaysian postcodes must be 5 digits in length
		if (!preg_match('/^[0-9][0-9][0-9][0-9][0-9]$/', $dest_postcode)) {
			// Postcode is not in the correct format
			return -1;
		}
		
		// Has a range of postcodes been specified?
		if (strpos($postcode_range, '-') !== false) {
			// Check against the range of postcodes specified
			// Format xxxxx-xxxxx where x = 0-9, with first digit(s) being significant
			if (preg_match('/^([0-9]+)[\-]([0-9]+)/', $postcode_range, $postcode_range_array)) {
				$start_range = $postcode_range_array[1];
				$end_range = $postcode_range_array[2];
				
				if (substr($dest_postcode, 0, strlen($start_range)) >= $start_range
					&& substr($dest_postcode, 0, strlen($end_range)) <= $end_range) {
					// Postcode matches one of the codes covered by this range!
					return true;
				}
			} else {
				$this->quotes['error'] = sprintf(MODULE_ADVANCED_SHIPPER_ERROR_POSTCODE_PARSE,
					$postcode_range);
			}
		} else {
			// Check against a single postcode
			if (substr($dest_postcode, 0, strlen($postcode_range)) == $postcode_range) {
				// Have matched the postcode
				return true;
			}
		}
		
		return false;
	}
	
	// }}}
	
	
	// {{{ _regionMatchesRangeNZ()
	
	/**
	 * Check New Zealand postcode
	 * 
	 * @param  string  $dest_postcode   The postcode to be checked (Whitespace should already have
	 *                                  been stripped).
	 * @param  string  $postcode_range  A postcode or range of postcodes to check against.
	 * @return boolean|integer          The boolean status of whether or not the postcode was
	 *                                  matched or -1 if an error occurred (Customer's postcode is
	 *                                  in incorrect format to be tested by this method).
	 */
	function _regionMatchesRangeNZ($dest_postcode, $postcode_range)
	{
		// Check the postcode is in the correct format
		// New Zealand postcodes are 4 digits in length
		if (!preg_match('/^[0-9][0-9][0-9][0-9]$/',
				$dest_postcode)) {
			// Postcode is not in the correct format
			return -1;
		}
		
		// Has a range of postcodes been specified?
		if (strpos($postcode_range, '-') !== false) {
			// Check against the range of postcodes specified
			// Format xxxx-xxxx where x = 0-9, with first digit being significant
			if (preg_match('/^([0-9]+)[\-]([0-9]+)/', $postcode_range, $postcode_range_array)) {
				$start_range = $postcode_range_array[1];
				$end_range = $postcode_range_array[2];
				
				if (substr($dest_postcode, 0, strlen($start_range)) >= $start_range
					&& substr($dest_postcode, 0, strlen($end_range)) <= $end_range) {
					// Postcode matches one of the codes covered by this range!
					return true;
				}
			} else {
				$this->quotes['error'] = sprintf(MODULE_ADVANCED_SHIPPER_ERROR_POSTCODE_PARSE,
					$postcode_range);
			}
		} else {
			// Check against a single postcode
			if (substr($dest_postcode, 0, strlen($postcode_range)) == $postcode_range) {
				// Have matched the postcode
				return true;
			}
		}
		
		return false;
	}
	
	// }}}
	
	
	// {{{ _regionMatchesRangePL()
	
	/**
	 * Check Polish postcode
	 * 
	 * @param  string  $dest_postcode   The postcode to be checked (Whitespace should already have
	 *                                  been stripped).
	 * @param  string  $postcode_range  A postcode or range of postcodes to check against.
	 * @return boolean|integer          The boolean status of whether or not the postcode was
	 *                                  matched or -1 if an error occurred (Customer's postcode is
	 *                                  in incorrect format to be tested by this method).
	 */
	function _regionMatchesRangePL($dest_postcode, $postcode_range)
	{
		// Check the postcode is in the correct format
		// Polish postcodes are 5 digits in length, with a hyphen after the first two digits
		// Remove any hyphen
		$dest_postcode = str_replace('-', '', $dest_postcode);
		
		if (!preg_match('/^[0-9][0-9][0-9][0-9][0-9]$/', $dest_postcode)) {
			// Postcode is not in the correct format
			return -1;
		}
		
		// Has a range of postcodes been specified?
		if (strpos($postcode_range, '-') !== false) {
			// Check against the range of postcodes specified
			// Format xxxx-xxxx where x = 0-9, with first digit being significant
			if (preg_match('/^([0-9]+)[\-]([0-9]+)/', $postcode_range, $postcode_range_array)) {
				$start_range = $postcode_range_array[1];
				$end_range = $postcode_range_array[2];
				
				if (substr($dest_postcode, 0, strlen($start_range)) >= $start_range
					&& substr($dest_postcode, 0, strlen($end_range)) <= $end_range) {
					// Postcode matches one of the codes covered by this range!
					return true;
				}
			} else {
				$this->quotes['error'] = sprintf(MODULE_ADVANCED_SHIPPER_ERROR_POSTCODE_PARSE,
					$postcode_range);
			}
		} else {
			// Check against a single postcode
			if (substr($dest_postcode, 0, strlen($postcode_range)) == $postcode_range) {
				// Have matched the postcode
				return true;
			}
		}
		
		return false;
	}
	
	// }}}
	
	
	// {{{ _regionMatchesRangePT()
	
	/**
	 * Check Portuguese postcode
	 * 
	 * @param  string  $dest_postcode   The postcode to be checked (Whitespace should already have
	 *                                  been stripped).
	 * @param  string  $postcode_range  A postcode or range of postcodes to check against.
	 * @return boolean|integer          The boolean status of whether or not the postcode was
	 *                                  matched or -1 if an error occurred (Customer's postcode is
	 *                                  in incorrect format to be tested by this method).
	 */
	function _regionMatchesRangePT($dest_postcode, $postcode_range)
	{
		// Check the postcode is in the correct format
		// Portuguese postcodes must be at least 4 digits in length (can ignore rest of code)
		if (!preg_match('/^[0-9][0-9][0-9][0-9]/', $dest_postcode)) {
			// Postcode is not in the correct format
			return -1;
		}
		
		// Has a range of postcodes been specified?
		if (strpos($postcode_range, '-') !== false) {
			// Check against the range of postcodes specified
			// Format xxxx-xxxx where x = 0-9, with first digit being significant
			if (preg_match('/^([0-9]+)[\-]([0-9]+)/', $postcode_range, $postcode_range_array)) {
				$start_range = $postcode_range_array[1];
				$end_range = $postcode_range_array[2];
				
				if (substr($dest_postcode, 0, strlen($start_range)) >= $start_range
					&& substr($dest_postcode, 0, strlen($end_range)) <= $end_range) {
					// Postcode matches one of the codes covered by this range!
					return true;
				}
			} else {
				$this->quotes['error'] = sprintf(MODULE_ADVANCED_SHIPPER_ERROR_POSTCODE_PARSE,
					$postcode_range);
			}
		} else {
			// Check against a single postcode
			if (substr($dest_postcode, 0, strlen($postcode_range)) == $postcode_range) {
				// Have matched the postcode
				return true;
			}
		}
		
		return false;
	}
	
	// }}}
	
	
	// {{{ _regionMatchesRangeSM()
	
	/**
	 * Check San Marino postcode
	 * 
	 * @param  string  $dest_postcode   The postcode to be checked (Whitespace should already have
	 *                                  been stripped).
	 * @param  string  $postcode_range  A postcode or range of postcodes to check against.
	 * @return boolean|integer          The boolean status of whether or not the postcode was
	 *                                  matched or -1 if an error occurred (Customer's postcode is
	 *                                  in incorrect format to be tested by this method).
	 */
	function _regionMatchesRangeSM($dest_postcode, $postcode_range)
	{
		// Check the postcode is in the correct format
		// San Marino postcodes are 5 digits in length
		if (!preg_match('/^([0-9][0-9][0-9][0-9][0-9])$/', $dest_postcode,
				$postcode_parts)) {
			// Postcode is not in the correct format
			return -1;
		}
		
		// Has a range of postcodes been specified?
		if (strpos($postcode_range, '-') !== false) {
			// Check against the range of postcodes specified
			// Format xxxx-xxxx where x = 0-9, with first digit being significant
			if (preg_match('/^([0-9]+)[\-]([0-9]+)/', $postcode_range, $postcode_range_array)) {
				$start_range = $postcode_range_array[1];
				$end_range = $postcode_range_array[2];
				
				if (substr($dest_postcode, 0, strlen($start_range)) >= $start_range
					&& substr($dest_postcode, 0, strlen($end_range)) <= $end_range) {
					// Postcode matches one of the codes covered by this range!
					return true;
				}
			} else {
				$this->quotes['error'] = sprintf(MODULE_ADVANCED_SHIPPER_ERROR_POSTCODE_PARSE,
					$postcode_range);
			}
		} else {
			// Check against a single postcode
			if (substr($dest_postcode, 0, strlen($postcode_range)) == $postcode_range) {
				// Have matched the postcode
				return true;
			}
		}
		
		return false;
	}
	
	// }}}
	
	
	// {{{ _regionMatchesRangeUS()
	
	/**
	 * Check US postcode
	 * 
	 * @param  string  $dest_postcode   The postcode to be checked (Whitespace should already have
	 *                                  been stripped).
	 * @param  string  $postcode_range  A postcode or range of postcodes to check against.
	 * @return boolean|integer          The boolean status of whether or not the postcode was
	 *                                  matched or -1 if an error occurred (Customer's postcode is
	 *                                  in incorrect format to be tested by this method).
	 */
	function _regionMatchesRangeUS($dest_postcode, $postcode_range)
	{
		// Check the postcode is in the correct format
		// US ZIP codes must be at least 5 digits in length (can ignore extended digits)
		if (!preg_match('/^[0-9][0-9][0-9][0-9][0-9]/', $dest_postcode)) {
			// Postcode is not in the correct format
			return -1;
		}
		
		// Has a range of ZIP codes been specified?
		if (strpos($postcode_range, '-') !== false) {
			// Check against the range of ZIP codes specified
			// Format xxxxx-xxxxx where x = 0-9, with first digit being significant
			if (preg_match('/^([0-9]+)[\-]([0-9]+)/', $postcode_range, $postcode_range_array)) {
				$start_range = $postcode_range_array[1];
				$end_range = $postcode_range_array[2];
				
				if (substr($dest_postcode, 0, strlen($start_range)) >= $start_range
					&& substr($dest_postcode, 0, strlen($end_range)) <= $end_range) {
					// ZIP Code matches one of the codes covered by this range!
					return true;
				}
			} else {
				$this->quotes['error'] = sprintf(MODULE_ADVANCED_SHIPPER_ERROR_POSTCODE_PARSE,
					$postcode_range);
			}
		} else {
			// Check against a single ZIP code
			if (substr($dest_postcode, 0, strlen($postcode_range)) == $postcode_range) {
				// Have matched the ZIP code
				return true;
			}
		}
		
		return false;
	}
	
	// }}}
	
	
	// {{{ _regionMatchesRangeVA()
	
	/**
	 * Check Vatican City postcode
	 * 
	 * @param  string  $dest_postcode   The postcode to be checked (Whitespace should already have
	 *                                  been stripped).
	 * @param  string  $postcode_range  A postcode or range of postcodes to check against.
	 * @return boolean|integer          The boolean status of whether or not the postcode was
	 *                                  matched or -1 if an error occurred (Customer's postcode is
	 *                                  in incorrect format to be tested by this method).
	 */
	function _regionMatchesRangeVA($dest_postcode, $postcode_range)
	{
		// Check the postcode is in the correct format
		// Vatican City postcodes are 5 digits in length
		if (!preg_match('/^([0-9][0-9][0-9][0-9][0-9])$/', $dest_postcode,
				$postcode_parts)) {
			// Postcode is not in the correct format
			return -1;
		}
		
		// Has a range of postcodes been specified?
		if (strpos($postcode_range, '-') !== false) {
			// Check against the range of postcodes specified
			// Format xxxx-xxxx where x = 0-9, with first digit being significant
			if (preg_match('/^([0-9]+)[\-]([0-9]+)/', $postcode_range, $postcode_range_array)) {
				$start_range = $postcode_range_array[1];
				$end_range = $postcode_range_array[2];
				
				if (substr($dest_postcode, 0, strlen($start_range)) >= $start_range
					&& substr($dest_postcode, 0, strlen($end_range)) <= $end_range) {
					// Postcode matches one of the codes covered by this range!
					return true;
				}
			} else {
				$this->quotes['error'] = sprintf(MODULE_ADVANCED_SHIPPER_ERROR_POSTCODE_PARSE,
					$postcode_range);
			}
		} else {
			// Check against a single postcode
			if (substr($dest_postcode, 0, strlen($postcode_range)) == $postcode_range) {
				// Have matched the postcode
				return true;
			}
		}
		
		return false;
	}
	
	// }}}
	
	
	// {{{ _extractElement()
	
	/**
	 * Extracts the source for an element. Takes encapsulated elements of the same type into
	 * consideration so that they form part of the source also.
	 *
	 * @access  protected
	 * @param   reference(string)    &$source  The source in which to look for the element
	 * @param   integer $start_pos   The position within the source to begin looking for the element
	 * @param   string  $tag_name    The name of the tag for this element
	 * @param   string  $attributes  An optional attribute string which wmay be used to identify an
	 *                               element uniquely (rather than simply searching for the element
	 *                               type)
	 * @param   boolean  $match_brackets   Whether the elements use '<>' or '{}' (true for brackets)
	 * @return  string|boolean       The source of the element or false if not found.
	 */
	function _extractElement(&$source, $start_pos, $tag_name, $attributes = '', $match_brackets = false)
	{
		// Build the first test for the start tag
		if (!$match_brackets) {
			$start_tag_with_attributes = '<' . $tag_name . $attributes;
			
			$start_tag = '<' . $tag_name;
			
			$end_tag = '</' . $tag_name . '>';
		} else {
			$start_tag_with_attributes = '{' . $tag_name . $attributes;
			
			$start_tag = '{' . $tag_name;
			
			$end_tag = '{/' . $tag_name . '}';
		}
		
		$start_tag_pos = strpos($source, $start_tag_with_attributes, $start_pos);
		
		if ($start_tag_pos === false) {
			// No matching tag found
			return false;
		}
		
		// Find ending tag for this element
		$tag_source = substr($source, $start_tag_pos, (strlen($source) - $start_tag_pos));
			
		
		$num_open_elements = 1;
		$current_start_pos = $start_tag_pos + 1; // Add 1 to ensure this tag isn't matched again
		
		do {
			$end_tag_pos = strpos($source, $end_tag, $current_start_pos);
			if ($end_tag_pos === false) {
				// Starting tag not closed in source - error, can't extract the element!
				return false;
			} else {
				$num_open_elements--;
			}
			
			// Check if any starting tags for similar elements exist within this element. If they do
			// then closing tag just found may belong to another element.
			do {
				$current_start_pos = strpos($source, $start_tag, $current_start_pos);
				
				if ($current_start_pos !== false && $current_start_pos < $end_tag_pos) {
					// Another element of the same type exists within this element. Must ensure its
					// closing tag is not taken as the closing tag for this element.
					$num_open_elements++;
					$current_start_pos++; // Add 1 to ensure this tag isn't matched again
				} else {
					// No (more) encapsulated elements found
					break;
				}
			} while (1);
			
			if ($num_open_elements == 0) {
				// No open encapsulated tags found so the source found IS the source for the element
				break;
			}
			
			// There are still some open encapsulated tags, need to move past their closing tags to
			// find the closing tag for the element we are interested in
			$current_start_pos = $end_tag_pos + 1;
		} while (1);
		
		$tag_source =
			substr($source, $start_tag_pos, ($end_tag_pos - $start_tag_pos) + strlen($end_tag));
		
		return $tag_source;
	}
	
	// }}}
	

	function check()
	{
		global $db;
		
		if (!isset($this->_check)) {
			$check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ADVANCED_SHIPPER_STATUS'");
			$this->_check = $check_query->RecordCount();
		}
		if (!$this->_check) {
			return false;
		}
		
		return true;
	}

	function install()
	{
		global $db;
		
		// General configuration values ////////////////////////////////////////////////////////////
		$background_colour = '#d0d0d0';
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('</b><fieldset style=\"background: " . $background_colour . "; margin-bottom: 1.5em;\"><legend style=\"font-size: 1.4em; font-weight: bold\">General Config</legend><b>Enable Advanced Shipper Method', 'MODULE_ADVANCED_SHIPPER_STATUS', 'Yes', 'Do you want to offer Advanced Shipper shipping?', '6', '0', 'zen_cfg_select_option(array(\'Yes\', \'No\'), ', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Compensate For Server Time', 'MODULE_ADVANCED_SHIPPER_TIME_ADJUST', '0', 'Specify the number of hours to adjust the server\'s time by so that it matches the store\'s shipping times. (Use a minus symbol, &lsquo;-&rsquo;, for negative adjustment).', '6', '0', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Method Sorting', 'MODULE_ADVANCED_SHIPPER_METHOD_SORT_ORDER', 'Admin method order', 'For non-dated methods, how should the methods be sorted - according to the order they have been set up in the admin or according to their total cost?', '6', '0', 'zen_cfg_select_option(array(\'Admin method order\', \'Cost - lowest to highest\', \'Cost - highest to lowest\'), ', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order of Display', 'MODULE_ADVANCED_SHIPPER_SORT_ORDER', '0', 'The Sort Order of Display determines what order the installed shipping modules are displayed in. The module with the lowest Sort Order is displayed first (towards the top).', '6', '0', now())");
		
		// Miscellaneous options ///////////////////////////////////////////////////////////////////
		$background_colour = '#d0d0d0';
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('</b></fieldset><fieldset style=\"background: " . $background_colour . "; margin-bottom: 1.5em;\"><legend style=\"font-size: 1.4em; font-weight: bold\">Misc Config</legend><b>Enable Debugging Output', 'MODULE_ADVANCED_SHIPPER_DEBUG_LEVEL', 'None', 'The debugging output can be used to check how/if any methods cover the current content of the shopping cart.', '6', '0', 'zen_cfg_select_option(array(\'None\', \'Basic\', \'Extended\'), ', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('</b></fieldset><img src=\"" . DIR_WS_ADMIN . "/images/ceon_button_logo.png\" alt=\"Made by Ceon. &copy; 2007-2009 Ceon\" align=\"right\" style=\"margin: 1em 0.2em;\"/><br />Module &copy; 2007-2009 Ceon<p style=\"display: none\">', 'MODULE_ADVANCED_SHIPPER_MADE_BY_CEON', '" . $this->version . "', '', '6', '0', 'zen_draw_hidden_field(\'made_by_ceon\' . ', now())");
		
	}
	
	
	function remove()
	{
		global $db;
		$db->Execute("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
	}
	
	function keys()
	{
		$keys = array(
			'MODULE_ADVANCED_SHIPPER_STATUS',
			'MODULE_ADVANCED_SHIPPER_TIME_ADJUST',
			'MODULE_ADVANCED_SHIPPER_METHOD_SORT_ORDER',
			'MODULE_ADVANCED_SHIPPER_SORT_ORDER',
			'MODULE_ADVANCED_SHIPPER_DEBUG_LEVEL',
			'MODULE_ADVANCED_SHIPPER_MADE_BY_CEON'
			);
		
		return $keys;
	}
}
?>