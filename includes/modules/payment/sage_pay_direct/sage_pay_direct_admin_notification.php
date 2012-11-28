<?php

/**
 * sage_pay_direct Admin Notification
 *
 * Displays the Sage Pay transaction information recorded for an order in the order admin.
 *
 * @author     Conor Kerr <sage_pay_direct@dev.ceon.net>
 * @author     Jason LeBaron <jason@networkdad.com>
 * @copyright  Copyright 2006-2009 Ceon
 * @copyright  Portions Copyright 2004-2006 Jason LeBaron
 * @copyright  Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright  Portions Copyright 2003 osCommerce
 * @link       http://dev.ceon.net/web/zen-cart/sage_pay_direct
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: sage_pay_direct_admin_notification.php 385 2009-06-23 11:11:45Z Bob $
 */

// Only output the Sage Pay Transaction information if it was recorded for this order!
if (isset($sage_pay_direct_transaction_info->fields)) {

	// strip slashes in case they were added to handle apostrophes:
	foreach ($sage_pay_direct_transaction_info->fields as $key => $value){
		$sage_pay_direct_transaction_info->fields[$key] = stripslashes($value);
	}
	
	// display all Sage Pay Direct status fields (in admin Orders page):
	$output = '<td><table>' . "\n";
	$output .= '<tr style="background-color : #cccccc; border-style : dotted;">' . "\n";
	$output .= '<td valign="top"><table>' . "\n";
	
	$output .= '<tr><td class="main">' . "\n";
	$output .= MODULE_PAYMENT_SAGE_PAY_DIRECT_SAGE_PAY_TRANSACTION_ID . "\n";
	$output .= '</td><td class="main">' . "\n";
	$output .= $sage_pay_direct_transaction_info->fields['vpstxid'] . "\n";
	$output .= '</td></tr>' . "\n";
	
	$output .= '<tr><td class="main">' . "\n";
	$output .= MODULE_PAYMENT_SAGE_PAY_DIRECT_VENDOR_TRANSACTION_CODE . "\n";
	$output .= '</td><td class="main">' . "\n";
	$output .= $sage_pay_direct_transaction_info->fields['vendor_tx_code'] . "\n";
	$output .= '</td></tr>' . "\n";
	
	$output .= '<tr><td class="main">' . "\n";
	$output .= MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_STATUS . "\n";
	$output .= '</td><td class="main">' . "\n";
	$output .= $sage_pay_direct_transaction_info->fields['status'] . "\n";
	$output .= '</td></tr>' . "\n";
	
	$output .= '<tr><td class="main">' . "\n";
	$output .= MODULE_PAYMENT_SAGE_PAY_DIRECT_STATUS_DETAIL . "\n";
	$output .= '</td><td class="main">' . "\n";
	$output .= $sage_pay_direct_transaction_info->fields['status_detail'] . "\n";
	$output .= '</td></tr>' . "\n";
	
	$output .= '<tr><td class="main">' . "\n";
	$output .= MODULE_PAYMENT_SAGE_PAY_DIRECT_SECURITY_KEY . "\n";
	$output .= '</td><td class="main">' . "\n";
	$output .= $sage_pay_direct_transaction_info->fields['security_key'] . "\n";
	$output .= '</td></tr>' . "\n";
	
	$output .= '<tr><td class="main">' . "\n";
	$output .= MODULE_PAYMENT_SAGE_PAY_DIRECT_TX_AUTH_NO . "\n";
	$output .= '</td><td class="main">' . "\n";
	$output .= $sage_pay_direct_transaction_info->fields['tx_auth_no'] . "\n";
	$output .= '</td></tr>' . "\n";
	
	$output .= '<tr><td class="main">' . "\n";
	$output .= MODULE_PAYMENT_SAGE_PAY_DIRECT_AVSCV2 . "\n";
	$output .= '</td><td class="main">' . "\n";
	$output .= $sage_pay_direct_transaction_info->fields['avs_cv2'] . "\n";
	$output .= '</td></tr>' . "\n";
	
	$output .= '<tr><td class="main">' . "\n";
	$output .= MODULE_PAYMENT_SAGE_PAY_DIRECT_ADDRESS_RESULT . "\n";
	$output .= '</td><td class="main">' . "\n";
	$output .= $sage_pay_direct_transaction_info->fields['address_result'] . "\n";
	$output .= '</td></tr>' . "\n";
	
	$output .= '<tr><td class="main">' . "\n";
	$output .= MODULE_PAYMENT_SAGE_PAY_DIRECT_POSTCODE_RESULT . "\n";
	$output .= '</td><td class="main">' . "\n";
	$output .= $sage_pay_direct_transaction_info->fields['postcode_result'] . "\n";
	$output .= '</td></tr>' . "\n";
	
	$output .= '<tr><td class="main">' . "\n";
	$output .= MODULE_PAYMENT_SAGE_PAY_DIRECT_CV2_RESULT . "\n";
	$output .= '</td><td class="main">' . "\n";
	$output .= $sage_pay_direct_transaction_info->fields['cv2_result'] . "\n";
	$output .= '</td></tr>' . "\n";
	
	$output .= '<tr><td class="main">' . "\n";
	$output .= MODULE_PAYMENT_SAGE_PAY_DIRECT_3D_SECURE_STATUS . "\n";
	$output .= '</td><td class="main">' . "\n";
	$output .= $sage_pay_direct_transaction_info->fields['threed_secure_status'] . "\n";
	$output .= '</td></tr>' . "\n";
	
	$output .= '<tr><td class="main">' . "\n";
	$output .= MODULE_PAYMENT_SAGE_PAY_DIRECT_CAVV_RESULT . "\n";
	$output .= '</td><td class="main">' . "\n";
	$output .= $sage_pay_direct_transaction_info->fields['cavv_result'] . "\n";
	$output .= '</td></tr>' . "\n";
	
	$output .= '</table></td>' . "\n";
	
	$output .= '</tr>' . "\n";
	$output .= '</table></td>' . "\n";
}
?>