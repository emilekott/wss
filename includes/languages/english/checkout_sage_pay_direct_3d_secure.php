<?php

/**
 * sage_pay_direct 3D Secure Checkout Redirection Page Language Definitions
 *
 * @author     Conor Kerr <sage_pay_direct@dev.ceon.net>
 * @copyright  Copyright 2006-2009 Ceon
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: checkout_sage_pay_direct_3d_secure.php 385 2009-06-23 11:11:45Z Bob $
 */

define('NAVBAR_TITLE_1', 'Checkout');
define('NAVBAR_TITLE_2', '3D-Secure Verification');

define('HEADING_TITLE', 'Step 3 of 3 - Part 2 - 3D-Secure Verification');

define('CHECKOUT_SAGE_PAY_DIRECT_3D_SECURE_TEXT_NECESSARY', 'Your Card Issuer has indicated that your card may require 3D-Secure authorisation.');

define('CHECKOUT_SAGE_PAY_DIRECT_3D_SECURE_TEXT_3D_SECURE_WHAT_IS_3D_SECURE', 'What is 3D-Secure?');
define('CHECKOUT_SAGE_PAY_DIRECT_3D_SECURE_TEXT_3D_SECURE_ADDITIONAL_INFO', '3D-Secure is also known as \'Verified by Visa\' or \'MasterCard SecureCode\' and is an additional layer of security provided by your Card Issuer to help protect your details.');

define('CHECKOUT_SAGE_PAY_DIRECT_3D_SECURE_TEXT_NO_IFRAME_SUPPORT_1', 'Unfortunately your browser doesn\'t support a technology known as &ldquo;IFrames&rdquo;, so the next page you will see won\'t look like our site but will instead be styled by your Card Issuer.');
define('CHECKOUT_SAGE_PAY_DIRECT_3D_SECURE_TEXT_NO_IFRAME_SUPPORT_2', 'This is nothing to worry about! The next page is hosted by your Card Issuer so is completely secure. It should already be familiar to you if you have used this card on a 3D-Secure website before or if you have been sent 3D-Secure information by your Card Issuer (in which case you\'ll see that the next page matches the information they sent you).');
define('CHECKOUT_SAGE_PAY_DIRECT_3D_SECURE_TEXT_NO_IFRAME_SUPPORT_3', 'Please fill in the form your Card Issuer presents you with so that your card can be securely authorised. You will then be returned to our site and your order will be created.');
define('CHECKOUT_SAGE_PAY_DIRECT_3D_SECURE_TEXT_NO_IFRAME_SUPPORT_4', 'If you are still uneasy about the differences between the next page and the rest of our store\'s checkout process then please use the back button and use a different (non 3D-Secure) card or contact us for assistance.');

/**
 * The following line shouldn't need to be modified.
 */
define('CHECKOUT_SAGE_PAY_DIRECT_3D_SECURE_ERROR_GET_PARAMS', '<h3 class="ErrorInfo">Unfortunately an error has occurred when attempting 3D-Secure verification of your Card Details! Please <a href="index.php?main_page=contact_us">contact us</a> immediately so that we can get this resolved for you. Thank you!</p>');

?>