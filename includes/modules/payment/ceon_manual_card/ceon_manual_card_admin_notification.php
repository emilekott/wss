<?php

/**
 * ceon_manual_card
 *
 * @author     Conor Kerr <conor.kerr_zen-cart@dev.ceon.net>
 * @copyright  Copyright 2006 Ceon
 * @copyright  Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright  Portions Copyright 2003 osCommerce
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: ceon_manual_card_admin_notification.php 180 2006-09-11 17:22:52Z conor $
 */

// Only output the Additional transaction information if it was recorded for this order!
if (isset($ceon_manual_card_result->fields)) {

	// Strip slashes in case they were added to handle apostrophes:
	foreach ($ceon_manual_card_result->fields as $key=>$value){
		$ceon_manual_card_result->fields[$key] = stripslashes($value);
	}
	
	// Display additional transaction information (in Admin Orders page):
	$output .= '<td><strong>';
	$output .= MODULE_PAYMENT_CEON_MANUAL_CARD_ADMIN_TEXT_TITLE;
	$output .= '</strong></td></tr>'."\n";
	
	$output .= '<tr><td><table border="0" cellspacing="0" cellpadding="2">'."\n";
	
	$output .= '<tr><td class="main">'."\n";
	$output .= MODULE_PAYMENT_CEON_MANUAL_CARD_ADMIN_TEXT_START_DATE."\n";
	$output .= '</td><td class="main">';
	$output .= $ceon_manual_card_result->fields['cc_start']."\n";
	$output .= '</td></tr>'."\n";
	
	$output .= '<tr><td class="main">'."\n";
	$output .= MODULE_PAYMENT_CEON_MANUAL_CARD_ADMIN_TEXT_ISSUE_NUMBER."\n";
	$output .= '</td><td class="main">';
	$output .= $ceon_manual_card_result->fields['cc_issue']."\n";
	$output .= '</td></tr>'."\n";
	
	$output .='</table></td>'."\n";
	
	$output .= '</tr><tr><td>' . MODULE_PAYMENT_CEON_MANUAL_CARD_ADMIN_TEXT_EMAIL_NOTICE . '</td>' . "\n";
}
?>