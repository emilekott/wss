<?php

/**
 * ceon_manual_card
 *
 * @author     Conor Kerr <conor.kerr_zen-cart@dev.ceon.net>
 * @copyright  Copyright 2006 Ceon
 * @copyright  Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright  Portions Copyright 2003 osCommerce
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: ceon_manual_card.php 180 2006-09-11 17:22:52Z conor $
 */


// {{{ ceon_manual_card

/**
 * Payment Module conforming to Zen Cart format. This module is used for MANUAL processing of card
 * data collected from customers.
 * 
 * It should ONLY be used if no other gateway is suitable, AND you must have SSL active on your
 * server for your own protection.
 *
 * Retains all Card Details entered throughout the checkout process, making use of PEAR Crypt
 * Blowfish, if possible, to encrypt the details and therefore hopefully comply with any applicable
 * Data Protection Laws.
 *
 * 
 * @author     Conor Kerr <conor.kerr_zen-cart@dev.ceon.net>
 * @copyright  Copyright 2006 Ceon
 * @copyright  Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright  Portions Copyright 2003 osCommerce
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 */
class ceon_manual_card extends base
{
	
	/**
	 * $code determines the internal 'code' name used to designate "this" payment module
	 *
	 * @var string
	 */
	var $code;
	
	/**
	 * $title is the displayed name for this payment method
	 *
	 * @var string
	 */
	var $title;
	
	/**
	 * $description is a soft name for this payment method
	 *
	 * @var string
	 */
	var $description;
	
	/**
	 * $enabled determines whether this module shows or not... in catalog.
	 *
	 * @var boolean
	 */
	var $enabled;
	
	
	// {{{ Class Constructor
	
	/**
	 * Create a new instance of the ceon_manual_card class
	 */
	function ceon_manual_card()
	{
		global $order;
		
		$this->code = 'ceon_manual_card';
		if ($_GET['main_page'] != '') {
			$this->title = MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CATALOG_TITLE; // Payment module title in Catalog
		} else {
			$this->title = MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_ADMIN_TITLE; // Payment module title in Admin
		}
		$this->description = MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_DESCRIPTION; // Descriptive Info about module in Admin
		$this->enabled = ((MODULE_PAYMENT_CEON_MANUAL_CARD_STATUS == 'True') ? true : false); // Whether the module is installed or not
		$this->sort_order = MODULE_PAYMENT_CEON_MANUAL_CARD_SORT_ORDER; // Sort Order of this payment option on the customer payment page
		
		if ((int) MODULE_PAYMENT_CEON_MANUAL_CARD_ORDER_STATUS_ID > 0) {
			$this->order_status = MODULE_PAYMENT_CEON_MANUAL_CARD_ORDER_STATUS_ID;
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
		
		if (($this->enabled == true) && ((int) MODULE_PAYMENT_CEON_MANUAL_CARD_ZONE > 0)) {
			$check_flag = false;
			$check = $db->Execute("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_CEON_MANUAL_CARD_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
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
			'    var ceon_manual_card_error_class = "CeonManualCardFormGadgetError";' . "\n" .
			'    var ceon_manual_card_card_owner_gadget = document.checkout_payment.ceon_manual_card_card_owner;' . "\n" .
			'    var ceon_manual_card_card_number_gadget = document.checkout_payment.ceon_manual_card_card_number;' . "\n" .
			'    var ceon_manual_card_card_type_gadget = document.checkout_payment.ceon_manual_card_card_type;' . "\n" .
			'    var ceon_manual_card_card_type_gadget_value = ceon_manual_card_card_type_gadget.options[ceon_manual_card_card_type_gadget.selectedIndex].value;' . "\n" .
			'    var ceon_manual_card_card_expires_month_gadget = document.checkout_payment.ceon_manual_card_card_expires_month;' . "\n" .
			'    var ceon_manual_card_card_expires_month_gadget_value = ceon_manual_card_card_expires_month_gadget.options[ceon_manual_card_card_expires_month_gadget.selectedIndex].value;' . "\n" .
			'    var ceon_manual_card_card_expires_year_gadget = document.checkout_payment.ceon_manual_card_card_expires_year;' . "\n" .
			'    var ceon_manual_card_card_expires_year_gadget_value = ceon_manual_card_card_expires_year_gadget.options[ceon_manual_card_card_expires_year_gadget.selectedIndex].value;' . "\n";
		
		if (MODULE_PAYMENT_CEON_MANUAL_CARD_USE_CVV == 'True')  {
			$js .= '    var ceon_manual_card_card_cvv_gadget = document.checkout_payment.ceon_manual_card_card_cvv;' . "\n";
		}
		
		if ($this->_useStartDate()) {
			$js .= '    var ceon_manual_card_card_start_month_gadget = document.checkout_payment.ceon_manual_card_card_start_month;' . "\n" .
			'    var ceon_manual_card_card_start_month_gadget_value = ceon_manual_card_card_start_month_gadget.options[ceon_manual_card_card_start_month_gadget.selectedIndex].value;' . "\n" .
			'    var ceon_manual_card_card_start_year_gadget = document.checkout_payment.ceon_manual_card_card_start_year;' . "\n" .
			'    var ceon_manual_card_card_start_year_gadget_value = ceon_manual_card_card_start_year_gadget.options[ceon_manual_card_card_start_year_gadget.selectedIndex].value;' . "\n";
		}
		
		$js .= '    if (ceon_manual_card_card_owner_gadget.value == "" || ceon_manual_card_card_owner_gadget.value.length < ' . CC_OWNER_MIN_LENGTH . ') {' . "\n" .
			'      error_message = error_message + "' . MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_JS_CARD_OWNER . '";' . "\n" .
			'      error = 1;' . "\n" .
			'      // Update the form gadget\'s class to give visual feedback to user' . "\n" .
			'      if (ceon_manual_card_card_owner_gadget.className.indexOf(ceon_manual_card_error_class) == -1) {' . "\n" .
			'        ceon_manual_card_card_owner_gadget.className =  ceon_manual_card_card_owner_gadget.className + " " + ceon_manual_card_error_class;' . "\n" .
			'      }' . "\n" .
			'    } else {' . "\n" .
			'      // Reset error status if necessary' . "\n" .
			'      ceon_manual_card_card_owner_gadget.className = ceon_manual_card_card_owner_gadget.className.replace(ceon_manual_card_error_class, "");' . "\n" .
			'    }' . "\n" .
			'    if (ceon_manual_card_card_type_gadget_value == "xxx") {' . "\n" .
			'      error_message = error_message + "' . MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_JS_CARD_TYPE . '";' . "\n" .
			'      error = 1;' . "\n" .
			'      // Update the form gadget\'s class to give visual feedback to user' . "\n" .
			'      if (ceon_manual_card_card_type_gadget.className.indexOf(ceon_manual_card_error_class) == -1) {' . "\n" .
			'        ceon_manual_card_card_type_gadget.className =  ceon_manual_card_card_type_gadget.className + " " + ceon_manual_card_error_class;' . "\n" .
			'      }' . "\n" .
			'    } else {' . "\n" .
			'      // Reset error status if necessary' . "\n" .
			'      ceon_manual_card_card_type_gadget.className = ceon_manual_card_card_type_gadget.className.replace(ceon_manual_card_error_class, "");' . "\n" .
			'    }' . "\n" .
			'    if (ceon_manual_card_card_number_gadget.value == "" || ceon_manual_card_card_number_gadget.value.length < ' . CC_NUMBER_MIN_LENGTH . ') {' . "\n" .
			'      error_message = error_message + "' . MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_JS_CARD_NUMBER . '";' . "\n" .
			'      error = 1;' . "\n" .
			'      // Update the form gadget\'s class to give visual feedback to user' . "\n" .
			'      if (ceon_manual_card_card_number_gadget.className.indexOf(ceon_manual_card_error_class) == -1) {' . "\n" .
			'        ceon_manual_card_card_number_gadget.className =  ceon_manual_card_card_number_gadget.className + " " + ceon_manual_card_error_class;' . "\n" .
			'      }' . "\n" .
			'    } else {' . "\n" .
			'      // Reset error status if necessary' . "\n" .
			'      ceon_manual_card_card_number_gadget.className = ceon_manual_card_card_number_gadget.className.replace(ceon_manual_card_error_class, "");' . "\n" .
			'    }' . "\n" .
			'    if (ceon_manual_card_card_expires_month_gadget_value == "" || ceon_manual_card_card_expires_year_gadget_value == "") {' . "\n" .
			'      error_message = error_message + "' . MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_JS_CARD_EXPIRY . '";' . "\n" .
			'      error = 1;' . "\n" .
			'    }' . "\n" .
			'    if (ceon_manual_card_card_expires_month_gadget_value == "") {' . "\n" .
			'      // Update the form gadget\'s class to give visual feedback to user' . "\n" .
			'      if (ceon_manual_card_card_expires_month_gadget.className.indexOf(ceon_manual_card_error_class) == -1) {' . "\n" .
			'        ceon_manual_card_card_expires_month_gadget.className =  ceon_manual_card_card_expires_month_gadget.className + " " + ceon_manual_card_error_class;' . "\n" .
			'      }' . "\n" .
			'    } else {' . "\n" .
			'      // Reset error status if necessary' . "\n" .
			'      ceon_manual_card_card_expires_month_gadget.className = ceon_manual_card_card_expires_month_gadget.className.replace(ceon_manual_card_error_class, "");' . "\n" .
			'    }' . "\n" .
			'    if (ceon_manual_card_card_expires_year_gadget_value == "") {' . "\n" .
			'      // Update the form gadget\'s class to give visual feedback to user' . "\n" .
			'      if (ceon_manual_card_card_expires_year_gadget.className.indexOf(ceon_manual_card_error_class) == -1) {' . "\n" .
			'        ceon_manual_card_card_expires_year_gadget.className =  ceon_manual_card_card_expires_year_gadget.className + " " + ceon_manual_card_error_class;' . "\n" .
			'      }' . "\n" .
			'    } else {' . "\n" .
			'      // Reset error status if necessary' . "\n" .
			'      ceon_manual_card_card_expires_year_gadget.className = ceon_manual_card_card_expires_year_gadget.className.replace(ceon_manual_card_error_class, "");' . "\n" .
			'    }' . "\n";
		
		if (MODULE_PAYMENT_CEON_MANUAL_CARD_USE_CVV == 'True')  {
			$js .= '    if (ceon_manual_card_card_cvv_gadget.value == "" || ceon_manual_card_card_cvv_gadget.value.length < "3" || ceon_manual_card_card_cvv_gadget.value.length > "4") {' . "\n".
				'      error_message = error_message + "' . MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_JS_CARD_CVV . '";' . "\n" .
				'      error = 1;' . "\n" .
				'      // Update the form gadget\'s class to give visual feedback to user' . "\n" .
				'      if (ceon_manual_card_card_cvv_gadget.className.indexOf(ceon_manual_card_error_class) == -1) {' . "\n" .
				'        ceon_manual_card_card_cvv_gadget.className =  ceon_manual_card_card_cvv_gadget.className + " " + ceon_manual_card_error_class;' . "\n" .
				'      }' . "\n" .
				'    } else {' . "\n" .
				'      // Reset error status if necessary' . "\n" .
				'      ceon_manual_card_card_cvv_gadget.className = ceon_manual_card_card_cvv_gadget.className.replace(ceon_manual_card_error_class, "");' . "\n" .
				'    }' . "\n";
		}
		
		if ($this->_useStartDate()) {
			$js .=
			'    if ((ceon_manual_card_card_start_month_gadget_value == "" && ceon_manual_card_card_start_year_gadget_value != "")' . "\n" .
			'       || (ceon_manual_card_card_start_month_gadget_value != "" && ceon_manual_card_card_start_year_gadget_value == "")) {' . "\n" .
			'        error_message = error_message + "' . MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_JS_CARD_START . '";' . "\n" .
			'        error = 1;' . "\n" .
			'        if (ceon_manual_card_card_start_month_gadget_value == "") {' . "\n" .
			'          // Update the form gadget\'s class to give visual feedback to user' . "\n" .
			'          if (ceon_manual_card_card_start_month_gadget.className.indexOf(ceon_manual_card_error_class) == -1) {' . "\n" .
			'            ceon_manual_card_card_start_month_gadget.className =  ceon_manual_card_card_start_month_gadget.className + " " + ceon_manual_card_error_class;' . "\n" .
			'          }' . "\n" .
			'        } else {' . "\n" .
			'          // Reset error status if necessary' . "\n" .
			'          ceon_manual_card_card_start_month_gadget.className = ceon_manual_card_card_start_month_gadget.className.replace(ceon_manual_card_error_class, "");' . "\n" .
			'        }' . "\n" .
			'        if (ceon_manual_card_card_start_year_gadget_value == "") {' . "\n" .
			'          // Update the form gadget\'s class to give visual feedback to user' . "\n" .
			'          if (ceon_manual_card_card_start_year_gadget.className.indexOf(ceon_manual_card_error_class) == -1) {' . "\n" .
			'            ceon_manual_card_card_start_year_gadget.className =  ceon_manual_card_card_start_year_gadget.className + " " + ceon_manual_card_error_class;' . "\n" .
			'          }' . "\n" .
			'        } else {' . "\n" .
			'          // Reset error status if necessary' . "\n" .
			'          ceon_manual_card_card_start_year_gadget.className = ceon_manual_card_card_start_year_gadget.className.replace(ceon_manual_card_error_class, "");' . "\n" .
			'        }' . "\n" .
			'    } else {' . "\n" .
			'        // Make sure that, if user hasn\'t used either start date field, they aren\'t marked as having an error' . "\n" .
			'        ceon_manual_card_card_start_month_gadget.className = ceon_manual_card_card_start_month_gadget.className.replace(ceon_manual_card_error_class, "");' . "\n" .
			'        ceon_manual_card_card_start_year_gadget.className = ceon_manual_card_card_start_year_gadget.className.replace(ceon_manual_card_error_class, "");' . "\n" .
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
		global $order, $_POST;
		
		// Build the options for the Expiry and Start Date Select Gadgets //////////////////////////
		$expires_month[] = array('id' => '', 'text' => MODULE_PAYMENT_CEON_MANUAL_CARD_SELECT_MONTH);
		for ($i = 1; $i < 13; $i++) {
			$expires_month[] = array('id' => sprintf('%02d', $i), 'text' => strftime('%B', mktime(0, 0, 0, $i, 1, 2000)));
		}
		
		// The Expiry Year options include the next ten years and this year
		$today = getdate();
		$expires_year[] = array('id' => '', 'text' => MODULE_PAYMENT_CEON_MANUAL_CARD_SELECT_YEAR);
		for ($i = $today['year']; $i < $today['year'] + 10; $i++) {
			$expires_year[] = array('id' => strftime('%y',mktime(0, 0, 0, 1, 1, $i)), 'text' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)));
		}
		
		$start_month[] = array('id' => '', 'text' => MODULE_PAYMENT_CEON_MANUAL_CARD_SELECT_MONTH);
		for ($i = 1; $i < 13; $i++) {
			$start_month[] = array('id' => sprintf('%02d', $i), 'text' => strftime('%B', mktime(0,0,0,$i,1,2000)));
		}
		
		// The Start Year options include the past four years and this year
		$start_year[] = array('id' => '', 'text' => MODULE_PAYMENT_CEON_MANUAL_CARD_SELECT_YEAR);
		for ($i = $today['year'] - 4; $i <= $today['year']; $i++) {
			$start_year[] = array('id' => strftime('%y',mktime(0, 0, 0, 1, 1, $i)), 'text' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)));
		}
		
		// Build the options for the Card Type /////////////////////////////////////////////////////
		// Automatic detection based on card number is not used as there are clashes with some
		// Switch/Maestro and Solo numbers. Don't ask why, seems stupid, but it happens!
		$credit_card_type[] = array('id' => 'xxx', 'text' => 'Select Card Type');
		if (MODULE_PAYMENT_CEON_MANUAL_CARD_ACCEPT_VISA == 'True') {
			$credit_card_type[] = array('id' => 'VISA', 'text' => MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_VISA);
		}
		if (MODULE_PAYMENT_CEON_MANUAL_CARD_ACCEPT_MC == 'True') {
			$credit_card_type[] = array('id' => 'MC', 'text' => MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_MC);
		}
		if (MODULE_PAYMENT_CEON_MANUAL_CARD_ACCEPT_DELTA == 'True') {
			$credit_card_type[] = array('id' => 'DELTA', 'text' => MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_DELTA);
		}
		if (MODULE_PAYMENT_CEON_MANUAL_CARD_ACCEPT_SOLO == 'True') {
			$credit_card_type[] = array('id' => 'SOLO', 'text' => MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_SOLO);
		}
		if (MODULE_PAYMENT_CEON_MANUAL_CARD_ACCEPT_SWITCH_MAESTRO == 'True') {
			$credit_card_type[] = array('id' => 'SWITCH_MAESTRO', 'text' => MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_SWITCH_MAESTRO);
		}
		if (MODULE_PAYMENT_CEON_MANUAL_CARD_ACCEPT_UKE == 'True') {
			$credit_card_type[] = array('id' => 'UKE', 'text' => MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_UKE);
		}
		if (MODULE_PAYMENT_CEON_MANUAL_CARD_ACCEPT_AMEX == 'True') {
			$credit_card_type[] = array('id' => 'AMEX', 'text' => MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_AMEX);
		}
		if (MODULE_PAYMENT_CEON_MANUAL_CARD_ACCEPT_DC == 'True') {
			$credit_card_type[] = array('id' => 'DC', 'text' => MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_DC);
		}
		if (MODULE_PAYMENT_CEON_MANUAL_CARD_ACCEPT_JCB == 'True') {
			$credit_card_type[] = array('id' => 'JCB', 'text' => MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_JCB);
		}
		
		// Initialise the default data to be used in the input form ////////////////////////////////
		$ceon_manual_card_card_owner = $order->billing['firstname'] . ' ' . $order->billing['lastname'];
		$ceon_manual_card_card_type = 'xxx';
		$ceon_manual_card_card_number = '';
		$ceon_manual_card_card_cvv = '';
		$ceon_manual_card_card_expires_month = '';
		$ceon_manual_card_card_expires_year = '';
		$ceon_manual_card_card_issue = '';
		$ceon_manual_card_card_start_month = '';
		$ceon_manual_card_card_start_year = '';
		
		
		// Check if the user has already entered their data. If so, use it to populate the form
		if (isset($_SESSION['ceon_manual_card_data_entered'])) {
			// Make sure that the user has been directly involved with the checkout process in the
			// previous step, otherwise this data should be considered expired
			$referring_url = getenv("HTTP_REFERER");
			
			if (strpos($referring_url, 'main_page=checkout') === false && strpos($referring_url, 'main_page=shopping_cart') === false) {
				// Have not arrived here from another part of the checkout process, data should
				// be considered invalid! Remove it from the session.
				unset($_SESSION['ceon_manual_card_data_entered']);
			} else {
				// Have arrived here from another part of the checkout process
				// Restore the data previously entered by the user
				if (file_exists_in_include_path('Crypt/Blowfish.php')) {
					// The PEAR Crypt Blowfish package can be used, use it to decrypt the Credit
					// Card Details. See pre_confirmation_check for encryption information.
					require_once('Crypt/Blowfish.php');
					
					$bf = new Crypt_Blowfish(MODULE_PAYMENT_CEON_MANUAL_CARD_ENCRYPTION_KEYPHRASE);
					
					$plaintext = $bf->decrypt($_SESSION['ceon_manual_card_data_entered']);
					
					$data_entered = unserialize(base64_decode($plaintext));
				} else {
					// Card Details were stored unencrypted in the session...
					// COULD BE A SECURITY RISK, it is HIGHLY ADVISED that the PEAR Crypt Blowfish
					// Package is installed!
					$data_entered = unserialize(base64_decode($_SESSION['ceon_manual_card_data_entered']));
				}
				
				$ceon_manual_card_card_owner = $data_entered['ceon_manual_card_card_owner'];
				$ceon_manual_card_card_type = $data_entered['ceon_manual_card_card_type'];
				$ceon_manual_card_card_number = $data_entered['ceon_manual_card_card_number'];
				$ceon_manual_card_card_cvv = $data_entered['ceon_manual_card_card_cvv'];
				$ceon_manual_card_card_expires_month = $data_entered['ceon_manual_card_card_expires_month'];
				$ceon_manual_card_card_expires_year = $data_entered['ceon_manual_card_card_expires_year'];
				
				if (isset($data_entered['ceon_manual_card_card_start_month'])) {
					$ceon_manual_card_card_start_month = $data_entered['ceon_manual_card_card_start_month'];
					$ceon_manual_card_card_start_year = $data_entered['ceon_manual_card_card_start_year'];
				}
				
				if (isset($data_entered['ceon_manual_card_card_issue'])) {
					$ceon_manual_card_card_issue = $data_entered['ceon_manual_card_card_issue'];
				}
			}
		}
	
		$selection = array(
			'id' => $this->code,
			'module' => MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CATALOG_TITLE
			);

		// Display icons for the list of cards accepted?
		if (MODULE_PAYMENT_CEON_MANUAL_CARD_SHOW_CARDS_ACCEPTED == 'True') {
			// Build the list of cards accepted
			$cards_accepted_images_source = '';
			if (MODULE_PAYMENT_CEON_MANUAL_CARD_ACCEPT_VISA == 'True') {
				$cards_accepted_images_source .= zen_image(DIR_WS_TEMPLATE_IMAGES  . 'card_icons/visa.png', MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_VISA, '', '', 'class="CeonManualCardCardIcon"');
			}
			if (MODULE_PAYMENT_CEON_MANUAL_CARD_ACCEPT_MC == 'True') {
				$cards_accepted_images_source .= zen_image(DIR_WS_TEMPLATE_IMAGES  . 'card_icons/mc.png', MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_MC, '', '', 'class="CeonManualCardCardIcon"');
			}
			if (MODULE_PAYMENT_CEON_MANUAL_CARD_ACCEPT_DELTA == 'True') {
				$cards_accepted_images_source .= zen_image(DIR_WS_TEMPLATE_IMAGES  . 'card_icons/visa_delta.png', MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_DELTA, '', '', 'class="CeonManualCardCardIcon"');
			}
			if (MODULE_PAYMENT_CEON_MANUAL_CARD_ACCEPT_SOLO == 'True') {
				$cards_accepted_images_source .= zen_image(DIR_WS_TEMPLATE_IMAGES  . 'card_icons/solo.png', MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_SOLO, '', '', 'class="CeonManualCardCardIcon"');
			}
			if (MODULE_PAYMENT_CEON_MANUAL_CARD_ACCEPT_SWITCH_MAESTRO == 'True') {
				$cards_accepted_images_source .= zen_image(DIR_WS_TEMPLATE_IMAGES  . 'card_icons/switch.png', MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_SWITCH_MAESTRO, '', '', 'class="CeonManualCardCardIcon"');
				$cards_accepted_images_source .= zen_image(DIR_WS_TEMPLATE_IMAGES  . 'card_icons/maestro.png', MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_SWITCH_MAESTRO, '', '', 'class="CeonManualCardCardIcon"');
			}
			if (MODULE_PAYMENT_CEON_MANUAL_CARD_ACCEPT_UKE == 'True') {
				$cards_accepted_images_source .= zen_image(DIR_WS_TEMPLATE_IMAGES  . 'card_icons/visa_electron.png', MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_UKE, '', '', 'class="CeonManualCardCardIcon"');
			}
			if (MODULE_PAYMENT_CEON_MANUAL_CARD_ACCEPT_AMEX == 'True') {
				$cards_accepted_images_source .= zen_image(DIR_WS_TEMPLATE_IMAGES  . 'card_icons/amex.png', MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_AMEX, '', '', 'class="CeonManualCardCardIcon"');
			}
			if (MODULE_PAYMENT_CEON_MANUAL_CARD_ACCEPT_DC == 'True') {
				$cards_accepted_images_source .= zen_image(DIR_WS_TEMPLATE_IMAGES  . 'card_icons/dc.png', MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_DC, '', '', 'class="CeonManualCardCardIcon"');
			}
			if (MODULE_PAYMENT_CEON_MANUAL_CARD_ACCEPT_JCB == 'True') {
				$cards_accepted_images_source .= zen_image(DIR_WS_TEMPLATE_IMAGES  . 'card_icons/jcb.png', MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_JCB, '', '', 'class="CeonManualCardCardIcon"');
			}
			$selection['fields'][] = array(
				'title' => MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CARDS_ACCEPTED,
				'field' => $cards_accepted_images_source
				);
		}
		
		$selection['fields'][] = array(
			'title' => MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CARD_OWNER,
			'field' => zen_draw_input_field('ceon_manual_card_card_owner', $ceon_manual_card_card_owner)
			);
		
		$selection['fields'][] = array(
			'title' => MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CARD_TYPE,
			'field' => zen_draw_pull_down_menu('ceon_manual_card_card_type', $credit_card_type, $ceon_manual_card_card_type)
			);
		
		if (MODULE_PAYMENT_CEON_MANUAL_CARD_DISABLE_CARD_NUMBER_AUTOCOMPLETE == 'True') {
			$selection['fields'][] = array(
				'title' => MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CARD_NUMBER,
				'field' => zen_draw_input_field('ceon_manual_card_card_number', $ceon_manual_card_card_number, 'autocomplete="off"')
				);
		} else {
			$selection['fields'][] = array(
				'title' => MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CARD_NUMBER,
				'field' => zen_draw_input_field('ceon_manual_card_card_number', $ceon_manual_card_card_number)
				);
			
		}
		
		$selection['fields'][] = array(
			'title' => MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CARD_EXPIRES,
			'field' => zen_draw_pull_down_menu('ceon_manual_card_card_expires_month', $expires_month, $ceon_manual_card_card_expires_month) . '&nbsp;' . zen_draw_pull_down_menu('ceon_manual_card_card_expires_year', $expires_year, $ceon_manual_card_card_expires_year)
			);
		
		
		if (MODULE_PAYMENT_CEON_MANUAL_CARD_USE_CVV == 'True') {
			if (MODULE_PAYMENT_CEON_MANUAL_CARD_DISABLE_CVV_AUTOCOMPLETE == 'True') {
				$selection['fields'][] = array(
					'title' => MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CVV,
					'field' => zen_draw_input_field('ceon_manual_card_card_cvv', $ceon_manual_card_card_cvv, 'size="4" maxlength="4" autocomplete="off"')
					);
			} else {
				$selection['fields'][] = array(
					'title' => MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CVV,
					'field' => zen_draw_input_field('ceon_manual_card_card_cvv', $ceon_manual_card_card_cvv, 'size="4" maxlength="4"')
					);
				
			}
		}
		
		if ($this->_useStartDate()) {
			$selection['fields'][] = array(
				'title' => MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CARD_START_DATE,
				'field' => zen_draw_pull_down_menu('ceon_manual_card_card_start_month', $start_month, $ceon_manual_card_card_start_month) . '&nbsp;' . zen_draw_pull_down_menu('ceon_manual_card_card_start_year', $start_year, $ceon_manual_card_card_start_year)
				);
		}
		if ($this->_useIssueNumber()) {
			$selection['fields'][] = array(
				'title' => MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CARD_ISSUE,
				'field' => zen_draw_input_field('ceon_manual_card_card_issue', $ceon_manual_card_card_issue, 'size="2" maxlength="2"')
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
		global $_POST;
		
		// Store the data entered so far so that user is not required to enter everything again if
		// anything is wrong or if they come back to the payment page to change some detail(s) /////
		$data_entered = array();
		$data_entered['ceon_manual_card_card_owner'] = $_POST['ceon_manual_card_card_owner'];
		$data_entered['ceon_manual_card_card_type'] = $_POST['ceon_manual_card_card_type'];
		$data_entered['ceon_manual_card_card_number'] = $_POST['ceon_manual_card_card_number'];
		
		$data_entered['ceon_manual_card_card_expires_month'] = $_POST['ceon_manual_card_card_expires_month'];
		$data_entered['ceon_manual_card_card_expires_year'] = $_POST['ceon_manual_card_card_expires_year'];
		
		if (isset($_POST['ceon_manual_card_card_cvv'])) {
			$data_entered['ceon_manual_card_card_cvv'] = $_POST['ceon_manual_card_card_cvv'];
		} else {
			$data_entered['ceon_manual_card_card_cvv'] = '';
		}
		
		if (isset($_POST['ceon_manual_card_card_start_year'])) {
			$data_entered['ceon_manual_card_card_start_month'] = $_POST['ceon_manual_card_card_start_month'];
			$data_entered['ceon_manual_card_card_start_year'] = $_POST['ceon_manual_card_card_start_year'];
		}
		
		if (isset($_POST['ceon_manual_card_card_issue'])) {
			$data_entered['ceon_manual_card_card_issue'] = $_POST['ceon_manual_card_card_issue'];
		}
		
		// Data entered is stored in the session as an base64 encoded, serialised array, with
		// optional encryption. However it is HIGHLY RECOMMENDED that encryption is used as it
		// prevents other users on your server from possibly obtaining Card Details from
		// the session file. As far as we are aware it is illegal to disregard this possibility but
		// we can take no responsibility for this information.. YOU MUST CHECK THIS OUT YOURSELF!
		$plaintext = base64_encode(serialize($data_entered));
		
		if (file_exists_in_include_path('Crypt/Blowfish.php')) {
			// The PEAR Crypt Blowfish package can be used, use it to encrypt the Credit
			// Card Details. This should provide reliable security for the protection of Credit
			// Card Details stored within the session, especially given that the session is a
			// temporary entity which expires when the user logs out or doesn't use the site for a
			// certain period of time.
			// REMEMBER TO SET THE KEYPHRASE THROUGH THE ADMIN!! DON'T ALLOW THE DEFAULT TO BE USED!
			require_once('Crypt/Blowfish.php');
			
			$bf = new Crypt_Blowfish(MODULE_PAYMENT_CEON_MANUAL_CARD_ENCRYPTION_KEYPHRASE);
			
			$encrypted = $bf->encrypt($plaintext);
			
			$_SESSION['ceon_manual_card_data_entered'] = $encrypted;
		} else {
			// Card Details are being stored unencrypted in the session...
			// COULD BE A SECURITY RISK, it is HIGHLY ADVISED that the PEAR Crypt Blowfish
			// Package is installed and used! See above!
			$_SESSION['ceon_manual_card_data_entered'] = $plaintext;
		}
		
		include(DIR_FS_CATALOG . DIR_WS_CLASSES . 'class.ceon_manual_cardCardValidation.php');
		
		$ceon_manual_card_card_validation = new ceon_manual_cardCardValidation();
		$result = $ceon_manual_card_card_validation->validate($_POST['ceon_manual_card_card_number'], $_POST['ceon_manual_card_card_expires_month'], $_POST['ceon_manual_card_card_expires_year'], $_POST['ceon_manual_card_card_cvv']);
		$error = '';
		switch ($result) {
			case -1:
				$error = sprintf(MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CARD_UNKNOWN_ERROR, substr($ceon_manual_card_card_validation->card_number, 0, 4));
				break;
			case -2:
			case -3:
			case -4:
				$error = MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CARD_EXPIRY_ERROR;
				break;
			case false:
				$error = MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CARD_NUMBER_ERROR;
				break;
		}
		
		if ($_POST['ceon_manual_card_card_type'] == 'xxx') {
			// Type of card not selected!
			$result = false;
			$error = MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CARD_TYPE_ERROR;
		}
		
		if (($result == false) || ($result < 1)) {
			// The user has not entered valid Card Details, redirect back to the input form
			
			// Encode the error message and redirect
			$payment_error_return = 'payment_error=' . $this->code . '&error=' . urlencode($error);
			
			zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
		}
		
		// Card seems to be valid, store the details found
		$this->card_owner = $_POST['ceon_manual_card_card_owner'];
		$this->card_type = $_POST['ceon_manual_card_card_type'];
		$this->card_number = $ceon_manual_card_card_validation->card_number;
		$this->card_expiry_month = $ceon_manual_card_card_validation->card_expiry_month;
		$this->card_expiry_year = $ceon_manual_card_card_validation->card_expiry_year;
		
		if (MODULE_PAYMENT_CEON_MANUAL_CARD_USE_CVV != 'True' ) {
			$this->card_cvv = '000';
		} else {
			$this->card_cvv = $_POST['ceon_manual_card_card_cvv'];
		}
		
		if ($this->_useStartDate()) {
			$this->card_start = $_POST['ceon_manual_card_card_start_month'] . $_POST['ceon_manual_card_card_start_year'];
		}
		
		if ($this->_useIssueNumber()) {
			$this->card_issue = $_POST['ceon_manual_card_card_issue'];
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
		global $_POST;

		// To reorder the output just adjust the order that the fields are added to the array
		// E.g. Below, "Expires" can be reordered below "Start" by moving its section appropriately
		
		// Get the name for the card type selected as defined in the language definitions file
		$card_type_name = $this->_getCardTypeNameForCode($_POST['ceon_manual_card_card_type']);
		
		$confirmation = array(
			//'title' => MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CATALOG_TITLE, // Redundant
			'fields' => array(
				array(
					'title' => MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CARD_TYPE,
					'field' => $card_type_name
					),
				array(
					'title' => MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CARD_OWNER,
					'field' => $_POST['ceon_manual_card_card_owner']
					),
				array(
					'title' => MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CARD_NUMBER,
					'field' => substr($this->card_number, 0, 4) . str_repeat('X', (strlen($this->card_number) - 8)) . substr($this->card_number, -4)
					)
				)
			);
		$confirmation['fields'][] = array(
			'title' => MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CARD_EXPIRES,
			'field' => strftime('%B, %Y', mktime(0, 0, 0, $_POST['ceon_manual_card_card_expires_month'], 1, '20' . $_POST['ceon_manual_card_card_expires_year']))
			);
		if (MODULE_PAYMENT_CEON_MANUAL_CARD_USE_CVV == 'True') {
			$confirmation['fields'][] = array(
				'title' => MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CVV,
				'field' => $_POST['ceon_manual_card_card_cvv']
				);
		}
		if ($this->_useStartDate() && $_POST['ceon_manual_card_card_start_year'] != '') {										
			$confirmation['fields'][] = array(
				'title' => MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CARD_START_DATE,
				'field' => strftime('%B, %Y', mktime(0, 0, 0, $_POST['ceon_manual_card_card_start_month'], 1, $_POST['ceon_manual_card_card_start_year']))
				);
		}
		if ($this->_useIssueNumber() && $_POST['ceon_manual_card_card_issue'] != '') {
			$confirmation['fields'][] = array(
				'title' => MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CARD_ISSUE,
				'field' => $_POST['ceon_manual_card_card_issue']
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
		global $_POST;
		
		// These are hidden fields on the checkout confirmation page
		$process_button_string = zen_draw_hidden_field('card_owner', $this->card_owner) .
			zen_draw_hidden_field('card_expires', $this->card_expiry_month . substr($this->card_expiry_year, -2)) .
			zen_draw_hidden_field('card_type', $this->card_type) .
			zen_draw_hidden_field('card_number', $this->card_number);
		
		if (MODULE_PAYMENT_CEON_MANUAL_CARD_USE_CVV == 'True') {
			$process_button_string .= zen_draw_hidden_field('card_cvv', $this->card_cvv);
		}
		
		if ($this->_useStartDate()) {
			$process_button_string .= zen_draw_hidden_field('card_start', $this->card_start);
		}
		
		if ($this->_useIssueNumber()) {
			$process_button_string .= zen_draw_hidden_field('card_issue', $this->card_issue);
		}
		
		$process_button_string .= zen_draw_hidden_field(zen_session_name(), zen_session_id());
		
		return $process_button_string;
	}
	
	// }}}
	
	
	// {{{ before_process()

	/**
	 * The main guts of the payment module as it were, this method formats the data appropriately
	 * for sending to the store owner. If a problem occurs this will redirect back to the Card
	 * Details entry page so that an error message can be displayed.
	 *
	 * @access  public
	 * @param   none
	 * @return  none
	 */
	function before_process()
	{
		global $_POST, $order;
		
		// Check if this module has been configured properly (it's useless to continue otherwise!)
		$module_configured_properly = false;
		if ((defined('MODULE_PAYMENT_CEON_MANUAL_CARD_EMAIL')) && (zen_validate_email(MODULE_PAYMENT_CEON_MANUAL_CARD_EMAIL))) {
			$module_configured_properly = true;
		}
		
		if ($module_configured_properly) {
			// Store the card details for this order ///////////////////////////////////////////////
			$order->info['cc_expires'] = $_POST['card_expires'];
			$order->info['cc_type'] = substr($this->_getCardTypeNameForCode($_POST['card_type']), 0, 20);
			$order->info['cc_owner'] = $_POST['card_owner'];
			
			if (MODULE_PAYMENT_CEON_MANUAL_CARD_USE_CVV == 'True') {
				$this->cc_cvv = $_POST['card_cvv'];
			}
			
			if ($this->_useStartDate()) {
				$order->info['cc_start'] = $_POST['card_start'];
			}
			if ($this->_useIssueNumber()) {
				$order->info['cc_issue'] = $_POST['card_issue'];
			}
			
			$len = strlen($_POST['card_number']);
			$this->cc_middle = substr($_POST['card_number'], 4, ($len - 8));
			$order->info['cc_number'] = substr($_POST['card_number'], 0, 4) . str_repeat('X', (strlen($_POST['card_number']) - 8)) . substr($_POST['card_number'], -4);
		}
		
		// Debugging output
		if (MODULE_PAYMENT_CEON_MANUAL_CARD_DEBUGGING_ENABLED == 'True') {
			echo "<html><head><title>Ceon Manual Card Debug Output</title></head><body>\n";
			echo "<pre>\n\n";
			echo "----------------------\n";
			echo "Card Details Received:\n";
			echo "----------------------\n";
			echo MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CARD_NUMBER . ' ' . $_POST['card_number'] . "\n";
			echo MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CARD_TYPE . ' ' . $this->_getCardTypeNameForCode($_POST['card_type']) . "\n";
			echo MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CARD_OWNER . ' ' . $_POST['card_owner'] . "\n";
			echo MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CARD_EXPIRES . ' ' . $_POST['card_expires'] . "\n";
			if (MODULE_PAYMENT_CEON_MANUAL_CARD_USE_CVV == 'True') {
				echo MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CVV_NO_LINK . ' ' . $_POST['card_cvv'] . "\n";
			}
			if ($this->_useStartDate()) {
				echo MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CARD_START_DATE . ' ' . $_POST['card_start'] . "\n";
			}
			if ($this->_useIssueNumber()) {
				echo MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CARD_ISSUE . ' ' . $_POST['card_issue'] . "\n";
			}
			echo "\n------------------------------------------\n";
			echo "Card Number Able to be Sent Successfully?:\n";
			echo "------------------------------------------\n";
			if ($module_configured_properly) {
				echo "Yes... would have been emailed to: " . MODULE_PAYMENT_CEON_MANUAL_CARD_EMAIL . "\n";
			} else {
				echo "NO!" . "\n\n"  . MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_NOT_CONFIGURED . "\n";
			}
			echo "------------------------------------------\n";
			
			if (file_exists_in_include_path('Crypt/Blowfish.php')) {
				echo "-----------------------------\n";
				echo "Blowfish Encryption was used.\n";
				echo "-----------------------------\n";
			} else {
				echo "--------------------\n";
				echo "Encryption NOT USED!\n";
				echo "--------------------\n\n";
				
				// Output the include path details
				echo "------------------------------\n";
				echo "Include paths for this system:\n";
				echo "------------------------------\n\n";
				echo get_include_path() . "\n\n";
				
				echo "----------------------\n";
				echo "Safe mode in use?: " . (ini_get('safe_mode') == 1 ? 'yes' : 'no') . "\n";
				echo "----------------------\n\n";
				if (ini_get('safe_mode') == 1) {
					echo "------------------------------------\n";
					echo "open_basedir restricted directories:\n";
					echo "------------------------------------\n\n";
					echo ini_get('open_basedir') . "\n\n";
				}
			}
			echo "\n\n</pre>\n</body>\n</html>";
			
			exit;
		}
		
		// If the module hasn't been configured properly then redirect back to the payment page with
		// the appropriate error message
		if (!$module_configured_properly) {

			$payment_error_return = 'payment_error=' . $this->code . '&error=' . urlencode(MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_ERROR_NOT_CONFIGURED);
			
			zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
		}
	}
	
	// }}}
	
	
	// {{{ after_process()

	/**
	 * Send the collected information via email to the store owner, storing outer digits and
	 * e-mailing middle digits
	 *
	 * @access  public
	 * @param   none
	 * @return  none
	 */
	function after_process()
	{
		global $insert_id;
		
		$message = sprintf(MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_EMAIL, $insert_id, $this->cc_middle, $this->cc_cvv);
		$html_msg['EMAIL_MESSAGE_HTML'] = str_replace("\n\n", '<br />', $message);
		
		zen_mail(MODULE_PAYMENT_CEON_MANUAL_CARD_EMAIL, MODULE_PAYMENT_CEON_MANUAL_CARD_EMAIL, MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_EMAIL_SUBJECT . $insert_id, $message, STORE_NAME, EMAIL_FROM, $html_msg, 'cc_middle_digs');
	}
	
	// }}}
	
	
	// {{{ after_order_create()

	/**
	 * Saves the additional credit card information for this transaction (that which is not normally
	 * stored in the Orders Table - Start Date and Issue Number)
	 *
	 * @access  public
	 * @param   int     $zf_order_id   The order id associated with this completed transaction.
	 * @return  none
	 */
	function after_order_create($zf_order_id)
	{
		global $db, $order;
		
		// Store the start date and issue number (if specified)
		$start_and_issue = array(
			'order_id' => $zf_order_id,
			'cc_start' => (isset($order->info['cc_start']) ? $order->info['cc_start'] : ''),
			'cc_issue' => (isset($order->info['cc_issue']) ? $order->info['cc_issue'] : '')
			);
		zen_db_perform(TABLE_CEON_MANUAL_CARD, $start_and_issue);
	}
	
	// }}}
	
	
	// {{{ admin_notification()

	/**
	 * Displays the additional transaction information in the order details screen in the Admin
	 *
	 * @access  public
	 * @param   int  $zf_order_id  The id of the order for which details should be generated
	 * @return  string  A HTML table detailing the additional transaction information stored by this
	 *                  payment module
	 */
	function admin_notification($zf_order_id)
	{
		global $db;
		
		$sql = "
			SELECT
				*
			FROM
				" . TABLE_CEON_MANUAL_CARD . "
			WHERE
				order_id = '" . $zf_order_id . "'";
				
		$ceon_manual_card_result = $db->Execute($sql);
	
		require(DIR_FS_CATALOG. DIR_WS_MODULES . 'payment/ceon_manual_card/ceon_manual_card_admin_notification.php');
		
		return $output;
	}
	
	// }}}
	
	
	// {{{ get_error()

	/**
	 * Gets the current error message from the URL and returns it for addition to the Message Stack
	 *
	 * @access  public
	 * @param   none
	 * @return  array  The title and message parts of the error message are returned in a hash.
	 */
	function get_error()
	{
		global $_GET;
		
		// Translate Coded Version of HTML error message back into HTML
		// Necessary to get round Zen Cart's interference (sanitization) of GET Variables.
		$_GET['error'] = str_replace('|lt;|', '<', $_GET['error']);
		$_GET['error'] = str_replace('|gt;|', '>', $_GET['error']);
		
		$error = array(
			'title' => MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_ERROR,
			'error' => stripslashes(urldecode($_GET['error']))
			);
		
		return $error;
	}
	
	// }}}


	// {{{ _useStartDate()

	/**
	 * Examines the list of cards accepted and determines whether at least one of them may need a
	 * start date to be supplied for card processing to take place.
	 *
	 * @access  private
	 * @param   none
	 * @return  bool  Whether at least one of the cards accepted may need a start date to be
	 *                supplied for card processing to take place (boolean true for yes!)
	 */
	function _useStartDate()
	{

		if (MODULE_PAYMENT_CEON_MANUAL_CARD_ACCEPT_SOLO == 'True') {
			return true;
		}
		if (MODULE_PAYMENT_CEON_MANUAL_CARD_ACCEPT_SWITCH_MAESTRO == 'True') {
			return true;
		}
		if (MODULE_PAYMENT_CEON_MANUAL_CARD_ACCEPT_AMEX == 'True') {
			return true;
		}
		
		return false;
	}
	
	// }}}
	
	
	// {{{ _useIssueNumber()

	/**
	 * Examines the list of cards accepted and determines whether at least one of them may need an
	 * issue number to be supplied for card processing to take place.
	 *
	 * @access  private
	 * @param   none
	 * @return  bool  Whether at least one of the cards accepted may need an issue number to be
	 *                supplied for card processing to take place (boolean true for yes!)
	 */
	function _useIssueNumber()
	{

		if (MODULE_PAYMENT_CEON_MANUAL_CARD_ACCEPT_SOLO == 'True') {
			return true;
		}
		if (MODULE_PAYMENT_CEON_MANUAL_CARD_ACCEPT_SWITCH_MAESTRO == 'True') {
			return true;
		}
		
		return false;
	}
	
	// }}}
	
	
	// {{{ _getCardTypeNameForCode()

	/**
	 * Lookup function simply returns the Name of the Card Type for the given Card Type Code.
	 *
	 * @access  private
	 * @param   string  $card_type_code   The code of the card Type for which the Name should be
	 *                                    returned.
	 * @return  string  The Name of the Card Type.
	 */
	function _getCardTypeNameForCode($card_type_code)
	{
		$card_type_name = '';
		
		switch ($card_type_code) {
			case 'VISA':
				$card_type_name = MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_VISA;
				break;
			case 'MC':
				$card_type_name = MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_MC;
				break;
			case 'DELTA':
				$card_type_name = MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_DELTA;
				break;
			case 'SOLO':
				$card_type_name = MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_SOLO;
				break;
			case 'SWITCH_MAESTRO':
				$card_type_name = MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_SWITCH_MAESTRO;
				break;
			case 'UKE':
				$card_type_name = MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_UKE;
				break;
			case 'AMEX':
				$card_type_name = MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_AMEX;
				break;
			case 'DC':
				$card_type_name = MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_DC;
				break;
			case 'JCB':
				$card_type_name = MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_JCB;
				break;
			default:
				break;
		}
		
		return $card_type_name;
	}
	
	// }}}

	function check()
	{
		global $db;
		if (!isset($this->_check)) {
			$check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_CEON_MANUAL_CARD_STATUS'");
			$this->_check = $check_query->RecordCount();
		}
		return $this->_check;
	}

	function install()
	{
		global $db;
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Ceon Manual Card Module', 'MODULE_PAYMENT_CEON_MANUAL_CARD_STATUS', 'True', 'Do you want to accept Manual Card payments via the Ceon Manual Card module?', '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('E-mail Address', 'MODULE_PAYMENT_CEON_MANUAL_CARD_EMAIL', '', 'The E-mail Address to which the Middle Digits of the Card Number should be sent.<br /><br />THIS IS ESSENTIAL!', '6', '0', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Encryption Keyphrase', 'MODULE_PAYMENT_CEON_MANUAL_CARD_ENCRYPTION_KEYPHRASE', 'Enter your encryption keyphrase here!', 'The keyphrase to be used to encrypt the Card details for storing in the session.<br /><br />THIS IS VERY IMPORTANT!<br /><br />Consult the documentation if you aren\'t sure what this is for.', '6', '0', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Request CVV Number', 'MODULE_PAYMENT_CEON_MANUAL_CARD_USE_CVV', 'True', 'Do you want to ask the customer for the card\'s CVV number?', '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Visa Card Payments', 'MODULE_PAYMENT_CEON_MANUAL_CARD_ACCEPT_VISA', 'True', 'Do you want to Accept Visa Card Payments?', '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('MasterCard Card Payments', 'MODULE_PAYMENT_CEON_MANUAL_CARD_ACCEPT_MC', 'True', 'Do you want to Accept MasterCard Card Payments?', '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Delta Card Payments', 'MODULE_PAYMENT_CEON_MANUAL_CARD_ACCEPT_DELTA', 'True', 'Do you want to Accept Visa Delta Card Payments?', '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Solo Card Payments', 'MODULE_PAYMENT_CEON_MANUAL_CARD_ACCEPT_SOLO', 'True', 'Do you want to Accept Solo Card Payments?', '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Switch/Maestro Card Payments', 'MODULE_PAYMENT_CEON_MANUAL_CARD_ACCEPT_SWITCH_MAESTRO', 'True', 'Do you want to Accept Switch/Maestro Card Payments?', '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Visa Electron - UKE - Card Payments', 'MODULE_PAYMENT_CEON_MANUAL_CARD_ACCEPT_UKE', 'True', 'Do you want to Accept Visa Electron Card Payments?', '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('American Express Card Payments', 'MODULE_PAYMENT_CEON_MANUAL_CARD_ACCEPT_AMEX', 'False', 'Do you want to Accept American Express Card Payments?', '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Diners Club Card Payments', 'MODULE_PAYMENT_CEON_MANUAL_CARD_ACCEPT_DC', 'False', 'Do you want to Accept Diners Club Card Payments?', '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('JCB Card Payments', 'MODULE_PAYMENT_CEON_MANUAL_CARD_ACCEPT_JCB', 'False', 'Do you want to Accept JCB Card Payments?', '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Show icons of Cards Accepted', 'MODULE_PAYMENT_CEON_MANUAL_CARD_SHOW_CARDS_ACCEPTED', 'True', 'Do you want to show icons for each Credit/Debit Card accepted?', '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Disable Autocomplete for Card Number field', 'MODULE_PAYMENT_CEON_MANUAL_CARD_DISABLE_CARD_NUMBER_AUTOCOMPLETE', 'True', 'Do you want to disable the autocomplete functionality of certain browsers for the Card Number field? (This prevents the browser from automatically entering the user\'s Card Number).', '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Disable Autocomplete for CVV field (if CVV enabled!)', 'MODULE_PAYMENT_CEON_MANUAL_CARD_DISABLE_CVV_AUTOCOMPLETE', 'True', 'Do you want to disable the autocomplete functionality of certain browsers for the CVV field? (This prevents the browser from automatically entering the user\'s CVV Number).', '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Debugging Output', 'MODULE_PAYMENT_CEON_MANUAL_CARD_DEBUGGING_ENABLED', 'False', 'When enabled, this option will cause the Cart to stop after the user has submitted their Card Details. The debug information will be outputted instead of the Checkout Success or Failure (Payment) page.<br /><br />DON\'T ENABLE UNLESS YOU KNOW WHAT YOU ARE DOING!', '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display.', 'MODULE_PAYMENT_CEON_MANUAL_CARD_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment Zone', 'MODULE_PAYMENT_CEON_MANUAL_CARD_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '2', 'zen_get_zone_class_title', 'zen_cfg_pull_down_zone_classes(', now())");
		$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Set Order Status', 'MODULE_PAYMENT_CEON_MANUAL_CARD_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value.', '6', '0', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");
	}

	function remove()
	{
		global $db;
		$db->Execute("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
	}

	function keys()
	{
		return array(
			'MODULE_PAYMENT_CEON_MANUAL_CARD_STATUS',
			'MODULE_PAYMENT_CEON_MANUAL_CARD_EMAIL',
			'MODULE_PAYMENT_CEON_MANUAL_CARD_ENCRYPTION_KEYPHRASE',
			'MODULE_PAYMENT_CEON_MANUAL_CARD_USE_CVV',
			'MODULE_PAYMENT_CEON_MANUAL_CARD_ACCEPT_VISA',
			'MODULE_PAYMENT_CEON_MANUAL_CARD_ACCEPT_MC',
			'MODULE_PAYMENT_CEON_MANUAL_CARD_ACCEPT_DELTA',
			'MODULE_PAYMENT_CEON_MANUAL_CARD_ACCEPT_SOLO',
			'MODULE_PAYMENT_CEON_MANUAL_CARD_ACCEPT_SWITCH_MAESTRO',
			'MODULE_PAYMENT_CEON_MANUAL_CARD_ACCEPT_UKE',
			'MODULE_PAYMENT_CEON_MANUAL_CARD_ACCEPT_AMEX',
			'MODULE_PAYMENT_CEON_MANUAL_CARD_ACCEPT_DC',
			'MODULE_PAYMENT_CEON_MANUAL_CARD_ACCEPT_JCB',
			'MODULE_PAYMENT_CEON_MANUAL_CARD_SHOW_CARDS_ACCEPTED',
			'MODULE_PAYMENT_CEON_MANUAL_CARD_DISABLE_CARD_NUMBER_AUTOCOMPLETE',
			'MODULE_PAYMENT_CEON_MANUAL_CARD_DISABLE_CVV_AUTOCOMPLETE',
			'MODULE_PAYMENT_CEON_MANUAL_CARD_DEBUGGING_ENABLED',
			'MODULE_PAYMENT_CEON_MANUAL_CARD_SORT_ORDER',
			'MODULE_PAYMENT_CEON_MANUAL_CARD_ZONE',
			'MODULE_PAYMENT_CEON_MANUAL_CARD_ORDER_STATUS_ID'
			);
	}
}

// }}}

?>
