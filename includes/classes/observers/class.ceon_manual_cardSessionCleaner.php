<?php

// {{{ class ceon_manual_cardSessionCleaner

/**
 * Determines if customer has arrived at one of the registered pages from a page outside of the
 * checkout process. If so, any previously entered Card information is cleared.
 * 
 * Rationale: Many people are uneasy when they see their card details entered automatically for
 * them, unless they are already in the checkout process, whereupon they prefer to not have to
 * re-type their details when moving between different parts of the process!
 */
class ceon_manual_cardSessionCleaner extends base {
	
	function ceon_manual_cardSessionCleaner() {
		global $zco_notifier;
		$zco_notifier->attach($this, array('NOTIFY_HEADER_START_SHOPPING_CART', 'NOTIFY_HEADER_START_CHECKOUT_SHIPPING'));
	}
	
	function update(&$callingClass, $notifier, $paramsArray) {
		$referring_url = getenv("HTTP_REFERER");

		if ($notifier == NOTIFY_HEADER_START_SHOPPING_CART) {
			if (strpos($referring_url, 'main_page=checkout') === false) {
				// Customer was not involved in any part of the checkout process before arriving at this page
				if (isset($_SESSION['ceon_manual_card_data_entered'])) {
					// Previous Card details exist. Remove them!
					unset($_SESSION['ceon_manual_card_data_entered']);
				}
			}
		} else if ($notifier == NOTIFY_HEADER_START_CHECKOUT_SHIPPING) {
			if (strpos($referring_url, 'main_page=checkout') === false && strpos($referring_url, 'main_page=shopping_cart') === false) {
				// Customer was not involved in any part of the checkout process before arriving at this page
				if (isset($_SESSION['ceon_manual_card_data_entered'])) {
					// Previous Card details exist. Remove them!
					unset($_SESSION['ceon_manual_card_data_entered']);
				}
			}
		}
	}
}

// }}}
 
?>