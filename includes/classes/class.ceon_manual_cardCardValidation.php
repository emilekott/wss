<?php

// {{{ ceon_manual_cardCardValidation

/**
 * ceon_manual_card Credit/Debit Card Validation Class
 *
 * @author     Conor Kerr <conor.kerr_zen-cart@dev.ceon.net>
 * @copyright  Copyright 2006 Ceon
 * @copyright  Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright  Portions Copyright 2003 osCommerce
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: class.ceon_manual_card_CardValidation.php 180 2006-08-08 00:09:22Z conor $
 */
class ceon_manual_cardCardValidation {
	var $card_type, $card_number, $card_expiry_month, $card_expiry_year;
	
	function validate($number, $expiry_m, $expiry_y) {
		$this->card_number = ereg_replace('[^0-9]', '', $number);
		
		if (is_numeric($expiry_m) && ($expiry_m > 0) && ($expiry_m < 13)) {
			$this->card_expiry_month = $expiry_m;
		} else {
			return -2;
		}
		
		$current_year = date('Y');
		$expiry_y = substr($current_year, 0, 2) . $expiry_y;
		if (is_numeric($expiry_y) && ($expiry_y >= $current_year) && ($expiry_y <= ($current_year + 10))) {
			$this->card_expiry_year = $expiry_y;
		} else {
			return -3;
		}
		
		if ($expiry_y == $current_year) {
			if ($expiry_m < date('n')) {
				return -4;
			}
		}
		
		return $this->is_valid();
	}
	
	function is_valid() {
		// Make sure a number was entered or tests on number's format will pass!
		if (strlen($this->card_number) == 0) {
			// No card number entered so obviously not valid!
			return false;
		}
		
		$cardNumber = strrev($this->card_number);
		$numSum = 0;
		
		for ($i=0; $i<strlen($cardNumber); $i++) {
			$currentNum = substr($cardNumber, $i, 1);
			
			// Double every second digit
			if ($i % 2 == 1) {
				$currentNum *= 2;
			}
			
			// Add digits of 2-digit numbers together
			if ($currentNum > 9) {
				$firstNum = $currentNum % 10;
				$secondNum = ($currentNum - $firstNum) / 10;
				$currentNum = $firstNum + $secondNum;
			}
			
			$numSum += $currentNum;
		}
		
		// If the total has no remainder it's OK
		return ($numSum % 10 == 0);
	}
}

// }}}
?>