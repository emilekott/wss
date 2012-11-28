<?php

/**
 * sage_pay_direct Language Definitions
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

// You should take a look at the following definitions and amend them as necessary ///////////////// 

/**
 * The following definitions are used as the message to be shown the user whenever it has been
 * determined that their card requires 3D-Secure authentication and the module is set to use a
 * simple submission form rather than the full template.
 *
 * HTML is allowed if/as desired.
 */
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_3D_SECURE_WHAT_NOW', 'What do I do now?');

define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_3D_SECURE_DISPLAYED_BELOW_1', 'Your Card Issuer\'s 3D-Secure Authorisation Page is displayed below. Please complete the form your Card Issuer presents you with so that your card can be securely authorised. Your order will then be created.');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_3D_SECURE_DISPLAYED_BELOW_2', 'If your card isn\'t registered for 3D-Secure your Card Issuer may include a link below to register your card now. Alternatively, they may even offer the facility to skip 3D-Secure authorisation at this time. If so, a means to do so will also be displayed below (not all Card Issuers offer this facility).');

define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_3D_SECURE_DISPLAYED_NEXT_1', 'Your Card Issuer\'s 3D-Secure Authorisation Page will be displayed on the next page. Please complete the form your Card Issuer presents you with so that your card can be securely authorised. Your order will then be created.');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_3D_SECURE_DISPLAYED_NEXT_2', 'If your card isn\'t registered for 3D-Secure your Card Issuer may include a link on the next page to register your card now. Alternatively, they may even offer the facility to skip 3D-Secure authorisation at this time. If so, a means to do so will also be displayed on the next page (not all Card Issuers offer this facility).');

define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_3D_SECURE_CLICK_TO_AUTHENTICATE', 'Please click the \'Continue\' button below to continue to your Card Issuer\'s 3D-Secure page...');

define('TITLE_CONTINUE_CHECKOUT_PROCEDURE', '<strong>Final Step</strong>');
define('TEXT_CONTINUE_CHECKOUT_PROCEDURE', '- continue to confirm your Card Details. Thank you!');

define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_3D_SECURE_COMPLETED_CONTINUE', 'Thank you! You have completed 3D-Secure Authorisation. Please click the \'Continue\' button below to complete your order...');


// HTML is allowed in the following message!
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_CUSTOM_SURCHARGES_DISCOUNTS_MESSAGE', '');


/**
 * Default (fall back) Definitions for information about card surcharges/discounts. The "SHORT"
 * version is added after the card's title in the Card Type selection gadget. The "LONG" version is
 * used as the title for the Order Total Summary Line in the ot_payment_surcharges_discounts Order
 * Total module.
 *
 * These are only used if no text was specified for a Card Type which is making use of the
 * surcharge/discount functionality.
 */
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_DISCOUNT_SHORT', 'Discount');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_DISCOUNT_LONG', 'Card Discount');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_SURCHARGE_SHORT', 'Surcharge');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_SURCHARGE_LONG', 'Card Surcharge');


// The remaining definitions should rarely require changing but feel free if you like! /////////////

/**
 * Payment option title as displayed to the customer, the one used depends on what logos have been
 * selected to be displayed
 */
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CATALOG_TITLE_SAGE_PAY_AND_PROTX', 'Credit/Debit Card (Secured by Sage Pay/Protx)');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CATALOG_TITLE_SAGE_PAY_ONLY', 'Credit/Debit Card (Secured by Sage Pay)');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CATALOG_TITLE_PROTX_ONLY', 'Credit/Debit Card (Secured by Protx)');

define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CARDS_ACCEPTED', 'Cards Accepted: ');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CARD_TYPE', 'Card Type: ');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CARD_OWNER', 'Card Owner: ');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CARD_NUMBER', 'Card Number: ');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CARD_EXPIRES', 'Card Expiry Date: ');
//define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CVV', 'CVV Number (<a href="javascript:popupWindow(\'/popup_info?file=' . (FILENAME_POPUP_CVV_HELP) . '.html&amp;title=More+Info\')">' . 'More Info' . '</a>):');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CVV', 'CVV Number (<a href="javascript:popupWindow(\'' . zen_href_link(FILENAME_POPUP_CVV_HELP) . '\')">' . 'More Info' . '</a>): ');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CARD_START_DATE', 'Card Start Date (If on card): ');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CARD_ISSUE', 'Card Issue No. (If on card): ');

define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_JS_CARD_OWNER', '* The Card Owner\'s Name must be at least ' . CC_OWNER_MIN_LENGTH . ' characters.\n');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_JS_CARD_NUMBER', '* The Card Number must be at least ' . CC_NUMBER_MIN_LENGTH . ' characters.\n');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_JS_CARD_CVV', '* The 3 or 4 digit CVV Number must be entered from the back of the Card.\n');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_JS_CARD_TYPE', '* You must select the Type of Credit/Debit Card you are using.\n');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_JS_CARD_EXPIRY', '* You must select the Expiry Date of the Card you are using.\n');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_JS_CARD_START', '* You have selected an invalid Start Date for the Card\n--> Please select a valid Start Date or Reset to \"Month\" \"Year\" if your card does not have a Start Date.\n');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_ERROR', 'Card Error!');

define('MODULE_PAYMENT_SAGE_PAY_DIRECT_SELECT_MONTH', 'Month');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_SELECT_YEAR', 'Year');

define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_VISA', 'Visa');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_MC', 'MasterCard');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_VISA_DEBIT', 'Visa Debit');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_SOLO', 'Solo');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_MAESTRO', 'Maestro');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_VISA_ELECTRON', 'Visa Electron (UKE)');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_AMEX', 'American Express');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_DC', 'Diners Club');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_JCB', 'JCB');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_LASER', 'Laser');

define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_3D_SECURE_VERIFIED_BY_VISA', 'Verified By Visa');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_3D_SECURE_MASTERCARD_SECURECODE', 'MasterCard SecureCode');

define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEST_MODE_CVV_MESSAGE', ' (As the module is in test mode only "123" will result in a successful match.)');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEST_MODE_BILLING_ADDRESS_MESSAGE', '<fieldset><legend>Test Mode In Use</legend><p>As the module is in test mode, only specific values for the Billing Address and Postcode will result in a status of "MATCHED" for AVS verification if AVS checks are enabled on your account. To use these default values please leave the following checkbox checked, otherwise uncheck it to test the AVS functionality with your Billing Address details as specified above. If unchecked, a status of "NOT MATCHED" will be returned for the AVS verification if your billing address details do not match the test server\'s defaults.</p><h4>Some Test Card Details</h4><p>For your convenience, a selection of test card numbers is listed below. The full list of test numbers is available in the admin...</p><p>Visa#: 4929000000006<br />MasterCard#: 5404000000000001<br />Visa Debit#: 4462000000000003<br />Solo#: 6334900000000005 - Issue #: 1<br />UK Maestro#: 5641820000000005 - Issue #: 01</p></fieldset>');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_BILLING_ADDRESS', 'Use Test Billing Address: ');

// The following messages can include HTML. (Unformatted text isn't always appropriate as it can't
// always convey the necessary information as easily as formatted text).
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CURL_PROBLEM', '<p class="ErrorInfo">Sorry but a technical problem has occurred when attempting to contact the Payment Gateway. Please <a href="index.php?main_page=contact_us">contact us</a> immediately so that we can get this resolved for you. Thank you!</p>');

define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_INVALID_MESSAGE', '<h3 class="ErrorInfo">Your card could not be authorised! Please correct any details below and try again, try another card or <a href="index.php?main_page=contact_us">contact us</a> for further assistance.</h3><p class="ExtraErrorInfo">(%s)</p>');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CARD_TYPE_DOES_NOT_MATCH', '<h3 class="ErrorInfo">The Card Type selected doesn&rsquo;t match the Card Number you have entered! Please correct any details below and try again, try another card or <a href="index.php?main_page=contact_us">contact us</a> for further assistance.</h3><!--(%s)-->');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_NOTAUTHED_MESSAGE', '<h3 class="ErrorInfo">Your card could not be authorised! Please correct any details below and try again, try another card or <a href="index.php?main_page=contact_us">contact us</a> for further assistance.</h3><p class="ExtraErrorInfo">(%s)</p>');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_REJECTED_MESSAGE', '<h3 class="ErrorInfo">Your card could not be authorised! Please correct any details below and try again, try another card or <a href="index.php?main_page=contact_us">contact us</a> for further assistance.</h3><p class="ExtraErrorInfo">(%s)</p>');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_ERROR_MESSAGE', '<h3 class="ErrorInfo">Unable to continue! A problem has occurred with our systems. Please <strong><a href="index.php?main_page=contact_us">contact us immediately</a></strong> for assistance.</h3><p class="ExtraErrorInfo">(%s)</p>');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_DECLINED_MESSAGE', '<h3 class="ErrorInfo">Your card could not be authorised! Please correct any details below and try again or <a href="index.php?main_page=contact_us">contact us</a> for further assistance.</h3><p class="ExtraErrorInfo">(%s)</p>');

define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_3D_SECURE_NOTAUTHED_MESSAGE', '<h3 class="ErrorInfo">Your card could not be authorised! Did you enter the correct 3D-Secure password? If not, please try again, otherwise please try another card. If you are still having difficulties please <a href="index.php?main_page=contact_us">contact us</a> for further assistance.</h3><p class="ExtraErrorInfo">(%s)</p>');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_3D_SECURE_REJECTED_MESSAGE', '<h3 class="ErrorInfo">Your card could not be authorised! Did you enter the correct 3D-Secure password? If not, please try again, otherwise please try another card. If you are still having difficulties please <a href="index.php?main_page=contact_us">contact us</a> for further assistance.</h3><p class="ExtraErrorInfo">(%s)</p>');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_3D_SECURE_DECLINED_MESSAGE', '<h3 class="ErrorInfo">Your card could not be authorised! Did you enter the correct 3D-Secure password? If not, please try again, otherwise please try another card. If you are still having difficulties please  <a href="index.php?main_page=contact_us">contact us</a> for further assistance.</h3><p class="ExtraErrorInfo">(%s)</p>');

define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_BILLING_STATE_PROBLEM', '<p class="ErrorInfo">You are ordering from the US but you have not entered/selected a valid US State in the billing address. The value you entered/selected was: &ldquo;%s&rdquo;. Please correct the address and try again or <a href="index.php?main_page=contact_us">contact us</a> for further assistance.</p>');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_DELIVERY_STATE_PROBLEM', '<p class="ErrorInfo">You are ordering from the US but you have not entered/selected a valid US State in the delivery address. The value you entered/selected was: &ldquo;%s&rdquo;. Please go back to the shipping page, correct the address and try again or <a href="index.php?main_page=contact_us">contact us</a> for further assistance.</p>');

define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CARD_TYPE_ERROR', '<p class="ErrorInfo">You must select the type of credit/debit card you are using.</p>');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CARD_NUMBER_ERROR', '<p class="ErrorInfo">The card number entered is invalid. Please check the number and try again, try another card or <a href="index.php?main_page=contact_us">contact us</a> for further assistance.</p>');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CARD_EXPIRY_ERROR', '<p class="ErrorInfo">The expiration date entered for the card is invalid. Please check the date and try again, try another card or <a href="index.php?main_page=contact_us">contact us</a> for further assistance.</p>');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CARD_EXPIRED_ERROR', '<p class="ErrorInfo">Your card has expired! Please try another card or <a href="index.php?main_page=contact_us">contact us</a> for further assistance.</p>');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CARD_ISSUE_NUMBER_LENGTH_ERROR', '<p class="ErrorInfo">The issue number is invalid! Please correct the number and try again, try another card or <a href="index.php?main_page=contact_us">contact us</a> for further assistance.</p>');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CARD_HOLDER_LENGTH_ERROR', '<p class="ErrorInfo">The length of the card holder name is invalid! Please check the card holder\'s name and try again, try another card or <a href="index.php?main_page=contact_us">contact us</a> for further assistance.</p>');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CVV_MISSING_ERROR', '<p class="ErrorInfo">A CVV number has not been entered. Please enter the number and try again, try another card or <a href="index.php?main_page=contact_us">contact us</a> for further assistance.</p>');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CVV_NUMBER_ERROR', '<p class="ErrorInfo">The CVV number entered is invalid. Please check the number and try again, try another card or <a href="index.php?main_page=contact_us">contact us</a> for further assistance.</p>');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CARD_START_ERROR', '<p class="ErrorInfo">The start date entered for the card is invalid. Please check the date and try again, try another card or <a href="index.php?main_page=contact_us">contact us</a> for further assistance.</p>');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_CARD_UNKNOWN_ERROR', '<p class="ErrorInfo">The card number entered doesn\'t match any known card types. Please check the number entered. If it is correct, we do not accept that type of card. If it is wrong, please try again, try another card or <a href="index.php?main_page=contact_us">contact us</a> for further assistance.</p>');

// Admin text definitions
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_ADMIN_TITLE', 'Sage Pay Direct v%s');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_DESCRIPTION_BASE', '<fieldset style="background: #F7F6F0; margin-bottom: 1.5em"><legend style="font-size: 1.2em; font-weight: bold">Test Card Details</legend>Visa#: 4929000000006<br />MasterCard#: 5404000000000001<br />Visa Debit#: 4462000000000003<br />Solo#: 6334900000000005 - Issue #: 1<br />UK Maestro#: 5641820000000005 - Issue #: 01<br />International Maestro#: 3000000000000004<br />Visa Electron (UKE)#: 4917300000000008<br />AMEX#: 374200000000004<br />JCB#: 3569990000000009<br />Diners Club#: 36000000000008<br />Laser#: 6304990000000000044<p>Any future date can be used for the expiration date.</p><p>The only CVV Code which will return a match is 123.</p><p>The AVS Verification will only return a match if the following Billing Address details are used: <br /><br />Billing Address: 88<br />Billing Postcode: 412</p><p>These are the default billing address details which will be submitted by the module in test mode if the &ldquo;Use Test Billing Address&rdquo; checkbox is ticked.</fieldset><fieldset style="background: #F7F6F0; margin-bottom: 1.5em"><legend style="font-size: 1.2em; font-weight: bold">Admin Links</legend><a target="_blank" href="https://live.sagepay.com/mysagepay">My Sage Pay Live Account Admin</a><br /><br /><a target="_blank" href="https://test.sagepay.com/mysagepay">My Sage Pay Test Account Admin</a><br /><br /><a target="_blank" href="https://test.sagepay.com/simulator">Simulator Admin</a></fieldset>');

define('MODULE_PAYMENT_SAGE_PAY_DIRECT_SAGE_PAY_TRANSACTION_ID', 'Sage Pay Transaction ID:');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_VENDOR_TRANSACTION_CODE', 'Vendor Transaction Code (Unique ID):');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_STATUS', 'Status:');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_STATUS_DETAIL', 'Status Message:');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_SECURITY_KEY', 'MD5 Security Key for Transaction:');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_TX_AUTH_NO', 'Sage Pay Authorisation Code:');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_AVSCV2', 'AVS and CV2 Response:');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_ADDRESS_RESULT', 'Specific Address Result:');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_POSTCODE_RESULT', 'Specific Postcode Result:');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_CV2_RESULT', 'Specific CV2 Result:');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_3D_SECURE_STATUS', '3D Secure Status:');
define('MODULE_PAYMENT_SAGE_PAY_DIRECT_CAVV_RESULT', 'Specific CAVV Result:');

?>