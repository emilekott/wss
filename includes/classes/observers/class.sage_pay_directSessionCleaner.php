<?php

/**
 * sage_pay_direct Encrypted Card Details Session Cleaner
 *
 * @author     Conor Kerr <sage_pay_direct@dev.ceon.net>
 * @copyright  Copyright 2006-2009 Ceon
 * @link       http://dev.ceon.net/web/zen-cart/sage_pay_direct
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: class.sage_pay_directSessionCleaner.php 385 2009-06-23 11:11:45Z Bob $
 */

// {{{ class sage_pay_directSessionCleaner

/**
 * Determines if customer has arrived at one of the registered pages from a page outside of the
 * checkout process. If so, any previously entered Card information is cleared.
 * 
 * Rationale: Many people are uneasy when they see their card details entered automatically for
 * them, unless they are already in the checkout process, whereupon they prefer to not have to
 * re-type their details when moving between different parts of the process!
 *
 * @author     Conor Kerr <sage_pay_direct@dev.ceon.net>
 * @copyright  Copyright 2006-2009 Ceon
 * @link       http://dev.ceon.net/web/zen-cart/sage_pay_direct
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: class.sage_pay_directSessionCleaner.php 385 2009-06-23 11:11:45Z Bob $
 */
class sage_pay_directSessionCleaner extends base
{
	
	function sage_pay_directSessionCleaner()
	{
		global $zco_notifier;
		$zco_notifier->attach($this,
			array(
				'NOTIFY_HEADER_START_SHOPPING_CART',
				'NOTIFY_HEADER_START_CHECKOUT_SHIPPING',
				'NOTIFY_HEADER_START_CHECKOUT_SUCCESS'
				)
			);
	}
	
	function update(&$callingClass, $notifier, $paramsArray)
	{
		$referring_uri = getenv("HTTP_REFERER");
		
		if ($notifier == NOTIFY_HEADER_START_SHOPPING_CART) {
			if (strpos($referring_uri, 'main_page=checkout') === false
					&& strpos($referring_uri, 'main_page=shopping_cart') === false
					&& strpos($referring_uri, 'main_page=fec_confirmation') === false) {
				// Customer was not involved in any part of the checkout process before arriving at
				// this page
				if (isset($_SESSION['sage_pay_direct_data_entered'])) {
					// Previous card details exist. Remove them!
					unset($_SESSION['sage_pay_direct_data_entered']);
				}
			}
		} else if ($notifier == NOTIFY_HEADER_START_CHECKOUT_SHIPPING) {
			if (strpos($referring_uri, 'main_page=checkout') === false
					&& strpos($referring_uri, 'main_page=shopping_cart') === false
					&& strpos($referring_uri, 'main_page=fec_confirmation') === false) {
				// Customer was not involved in any part of the checkout process before arriving at
				// this page
				if (isset($_SESSION['sage_pay_direct_data_entered'])) {
					// Previous card details exist. Remove them!
					unset($_SESSION['sage_pay_direct_data_entered']);
				}
			}
		} else if ($notifier == NOTIFY_HEADER_START_CHECKOUT_SUCCESS) {
			// Customer has completed the checkout process so details shouldn't be stored any
			// longer
			if (isset($_SESSION['sage_pay_direct_data_entered'])) {
				// Previous card details exist. Remove them!
				unset($_SESSION['sage_pay_direct_data_entered']);
			}
		}
	}
}

// }}}
 
?>