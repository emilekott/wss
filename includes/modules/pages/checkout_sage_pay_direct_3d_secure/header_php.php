<?php

/**
 * sage_pay_direct 3D Secure Redirection Page
 *
 * @author     Conor Kerr <sage_pay_direct@dev.ceon.net>
 * @copyright  Copyright 2006-2009 Ceon
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: header_php.php 385 2009-06-23 11:11:45Z Bob $
 */

require(DIR_FS_CATALOG . DIR_WS_MODULES . 'require_languages.php');

if (!isset($_GET['ACSURL']) || !isset($_GET['PaReq']) || !isset($_GET['MD'])) {
	// Necessary config details missing! Redirect back to payment page and display error message
	$error_message = str_replace('<', 'ceonltceon',
		CHECKOUT_SAGE_PAY_DIRECT_3D_SECURE_ERROR_GET_PARAMS);
	$error_message = str_replace('>', 'ceongtceon', $error_message);
	
	$error_message = str_replace('"', 'ceonquotceon', $error_message);
	
	$payment_error_return = 'payment_error=sage_pay_direct&error=' . urlencode($error_message);
	
	zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
}

// Avoid hack attempts during the checkout procedure by checking the internal cartID
if ($_SESSION['cart']->cartID != $_SESSION['cartID']) {
	// User has attempted to access this page after its initial display and navigating to another
	// page. Can't continue in case they have modified their cart contents
	zen_redirect(zen_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
}

/**
 * Load language defines from main Sage Pay Direct languages file... saves duplication of definitions!
 */
$sage_pay_direct_language_file_path = 'modules/payment/sage_pay_direct.php';
if (file_exists(DIR_WS_LANGUAGES . $_SESSION['language'] . '/' . $template_dir_select . $sage_pay_direct_language_file_path)) {
	require(DIR_WS_LANGUAGES . $_SESSION['language'] . '/' . $template_dir_select . $sage_pay_direct_language_file_path);
} else {
	require(DIR_WS_LANGUAGES . $_SESSION['language'] . '/' . $sage_pay_direct_language_file_path);
}

$acs_url = $_GET['ACSURL'];
$pa_req = $_GET['PaReq'];
$md = $_GET['MD'];

$breadcrumb->add(NAVBAR_TITLE_1, zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
$breadcrumb->add(NAVBAR_TITLE_2);

?>
