<?php

/**
 * ceon_manual_card Language Definitions
 *
 * @author     Conor Kerr <conor.kerr_zen-cart@dev.ceon.net>
 * @copyright  Copyright 2006 Ceon
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: ceon_manual_card.php 180 2006-09-11 17:22:52Z conor $
 */


// Admin Configuration Items
define('MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_ADMIN_TITLE', 'Ceon Manual Card'); // Payment option title as displayed in the admin
define('MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_DESCRIPTION_BASE', '<strong>Test Card Info</strong><br/>A valid Credit/Debit Card Number must be used (e.g. 4111111111111111).<br /><br />Any future date can be used for the Expiration Date and any 3 or 4 (AMEX) digit number can be used for the CVV Code.<br /><br />Switch/Maestro and Solo can optionally use a Start Date and/or Issue Number. American Express cards always have and require a Start Date (although this module does not enforce its entry).');
if (file_exists_in_include_path('Crypt/Blowfish.php')) {
	define('MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_DESCRIPTION', MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_DESCRIPTION_BASE);
} else {
	define('MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_DESCRIPTION', MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_DESCRIPTION_BASE . '<br /><br /><strong><span style="color: red">Warning:</span><br />You do NOT have PEAR:Crypt_Blowfish installed on your server!</strong><br /><span style="color: red">Encryption will not be used for the Credit/Debit Card Details being stored in the Session.</span>');
}
define('MODULE_PAYMENT_CEON_MANUAL_CARD_ADMIN_TEXT_START_DATE', 'Start Date (If selected):');
define('MODULE_PAYMENT_CEON_MANUAL_CARD_ADMIN_TEXT_ISSUE_NUMBER', 'Issue Number (If entered):');

define('MODULE_PAYMENT_CEON_MANUAL_CARD_ADMIN_TEXT_TITLE', 'Extra Information provided by Ceon Manual Card module');
define('MODULE_PAYMENT_CEON_MANUAL_CARD_ADMIN_TEXT_EMAIL_NOTICE', '(The middle digits of the above Card Number and the CVV Number for the Card have been e-mailed to the address configured in the module.)');

// Catalog Items
define('MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CATALOG_TITLE', 'Credit/Debit Card');  // Payment option title as displayed to the customer
define('MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CARDS_ACCEPTED', 'Cards Accepted:');
define('MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CARD_TYPE', 'Card Type:');
define('MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CARD_OWNER', 'Card Owner:');
define('MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CARD_NUMBER', 'Card Number:');
define('MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CARD_EXPIRES', 'Card Expiry Date:');
//define('MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CVV', 'CVV Number (<a href="javascript:popupWindow(\'/popup_info?file=' . (FILENAME_POPUP_CVV_HELP) . '.html&amp;title=More+Info\')">' . 'More Info' . '</a>):');
define('MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CVV', 'CVV Number (<a href="javascript:popupWindow(\'' . zen_href_link(FILENAME_POPUP_CVV_HELP) . '\')">' . 'More Info' . '</a>):');
define('MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CVV_NO_LINK', 'CVV Number:');
define('MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CARD_START_DATE', 'Card Start Date (If on card):');
define('MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CARD_ISSUE', 'Card Issue No. (If on card):');
define('MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_JS_CARD_OWNER', '* The Card Owner\'s Name must be at least ' . CC_OWNER_MIN_LENGTH . ' characters.\n');
define('MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_JS_CARD_NUMBER', '* The Card Number must be at least ' . CC_NUMBER_MIN_LENGTH . ' characters.\n');
define('MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_JS_CARD_CVV', '* The 3 or 4 digit CVV Number must be entered from the back of the Card.\n');
define('MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_JS_CARD_TYPE', '* You must select the Type of Credit/Debit Card you are using.\n');
define('MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_JS_CARD_EXPIRY', '* You must select the Expiry Date of the Card you are using.\n');
define('MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_JS_CARD_START', '* You have selected an invalid Start Date for the Card\n--> Please select a valid Start Date or Reset to \"Month\" \"Year\" if your card does not have a Start Date.\n');
define('MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_ERROR', 'Card Error!');

define('MODULE_PAYMENT_CEON_MANUAL_CARD_SELECT_MONTH', 'Month');
define('MODULE_PAYMENT_CEON_MANUAL_CARD_SELECT_YEAR', 'Year');

define('MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_VISA', 'Visa');
define('MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_MC', 'MasterCard');
define('MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_DELTA', 'Visa Delta');
define('MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_SOLO', 'Solo');
define('MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_SWITCH_MAESTRO', 'Switch/Maestro');
define('MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_UKE', 'Visa Electron - UKE');
define('MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_AMEX', 'American Express');
define('MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_DC', 'Diners Club');
define('MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_JCB', 'JCB');

define('MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_EMAIL_SUBJECT', 'Extra Card Information for Order #');
define('MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_EMAIL' , "Here are the Middle Digits of the Card Number for Order #%s:\n\nMiddle Digits: %s\n\nAnd here is the CVV Number:\n\nCVV Number: %s\n\nYOU MUST NOT STORE THIS NUMBER... DELETE THIS E-MAIL ONCE YOU'VE CHARGED THE CARD!");

// Following messages can include HTML formatting by using |lt;| instead of < and |gt;| instead of >
// Rationale: Unfortunately Zen Cart, rather unintuitively, sanitizes all GET variables regardless
// of their content and therefore precludes the passing of HTML tags in error messages. Unformatted
// text isn't always appropriate as it can't always convey the necessary information as easily as
// formatted text so this hack has been created for the Ceon Manual Card error messages.
define('MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_NOT_CONFIGURED', 'You have enabled the Ceon Manual Card payment module but have not configured it to send Card information to you by email. As a result, you will not be able to process the Card number for orders placed using this method.  Please go to Admin->Modules->Payment->Ceon Manual Card->Edit and set the E-mail Address for sending Card information.');
define('MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_ERROR_NOT_CONFIGURED', '|lt;|h3 class="ErrorInfo"|gt;|' . str_replace('>', '&gt;', MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_NOT_CONFIGURED) . '|lt;|/h3|gt;|');


define('MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CARD_TYPE_ERROR', '|lt;|p class="ErrorInfo"|gt;|You must select the type of credit/debit card you are using.|lt;|/p|gt;|');
define('MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CARD_NUMBER_ERROR', '|lt;|p class="ErrorInfo"|gt;|The card number entered is invalid. Please check the number and try again.|lt;|/p|gt;|');
define('MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CARD_EXPIRY_ERROR', '|lt;|p class="ErrorInfo"|gt;|The expiration date entered for the card is invalid. Please check the date and try again.|lt;|/p|gt;|');
define('MODULE_PAYMENT_CEON_MANUAL_CARD_TEXT_CARD_UNKNOWN_ERROR', '|lt;|p class="ErrorInfo"|gt;|The first four digits of the number entered are: %s. If that number is correct, we do not accept that type of card. If it is wrong, please try again.|lt;|/p|gt;|');
?>