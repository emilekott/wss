<?php
/**
 * Side Box Template
 *
 * Displays icons for Card Types accepted by this store through the Sage Pay Direct payment module.
 *
 * @package    templateSystem
 * @author     Conor Kerr <sage_pay_direct@dev.ceon.net>
 * @copyright  Copyright 2006-2009 Ceon
 * @copyright  Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright  Portions Copyright 2003 osCommerce
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version $Id: tpl_sage_pay_direct_cards_accepted.php 385 2009-06-23 11:11:45Z Bob $
 */

$content = '';
$content .= '<div id="' . str_replace('_', '-', $box_id . 'Content') . '" class="sideBoxContent centeredContent">';
$content .= "\n";
if (MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_VISA == 'Yes') {
	$content .= zen_image(DIR_WS_TEMPLATE_IMAGES  . 'card_icons/visa.png', MODULE_PAYMENT_SAGE_PAY_DIRECT_CARDS_ACCEPTED_SIDEBOX_VISA, '', '', 'class="SagePayDirectCardsAcceptedSideboxCardIcon"');
}
if (MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_MC == 'Yes') {
	$content .= zen_image(DIR_WS_TEMPLATE_IMAGES  . 'card_icons/mc.png', MODULE_PAYMENT_SAGE_PAY_DIRECT_CARDS_ACCEPTED_SIDEBOX_MC, '', '', 'class="SagePayDirectCardsAcceptedSideboxCardIcon"');
}
if (MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_VISA_DEBIT == 'Yes') {
	$content .= zen_image(DIR_WS_TEMPLATE_IMAGES  . 'card_icons/visa_debit.png', MODULE_PAYMENT_SAGE_PAY_DIRECT_CARDS_ACCEPTED_SIDEBOX_VISA_DEBIT, '', '', 'class="SagePayDirectCardsAcceptedSideboxCardIcon"');
}
if (MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_SOLO == 'Yes') {
	$content .= zen_image(DIR_WS_TEMPLATE_IMAGES  . 'card_icons/solo.png', MODULE_PAYMENT_SAGE_PAY_DIRECT_CARDS_ACCEPTED_SIDEBOX_SOLO, '', '', 'class="SagePayDirectCardsAcceptedSideboxCardIcon"');
}
if (MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_MAESTRO == 'Yes') {
	$content .= zen_image(DIR_WS_TEMPLATE_IMAGES  . 'card_icons/maestro.png', MODULE_PAYMENT_SAGE_PAY_DIRECT_CARDS_ACCEPTED_SIDEBOX_MAESTRO, '', '', 'class="SagePayDirectCardsAcceptedSideboxCardIcon"');
}
if (MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_VISA_ELECTRON == 'Yes') {
	$content .= zen_image(DIR_WS_TEMPLATE_IMAGES  . 'card_icons/visa_electron.png', MODULE_PAYMENT_SAGE_PAY_DIRECT_CARDS_ACCEPTED_SIDEBOX_VISA_ELECTRON, '', '', 'class="SagePayDirectCardsAcceptedSideboxCardIcon"');
}
if (MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_AMEX == 'Yes') {
	$content .= zen_image(DIR_WS_TEMPLATE_IMAGES  . 'card_icons/amex.png', MODULE_PAYMENT_SAGE_PAY_DIRECT_CARDS_ACCEPTED_SIDEBOX_AMEX, '', '', 'class="SagePayDirectCardsAcceptedSideboxCardIcon"');
}
if (MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_DC == 'Yes') {
	$content .= zen_image(DIR_WS_TEMPLATE_IMAGES  . 'card_icons/dc.png', MODULE_PAYMENT_SAGE_PAY_DIRECT_CARDS_ACCEPTED_SIDEBOX_DC, '', '', 'class="SagePayDirectCardsAcceptedSideboxCardIcon"');
}
if (MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_JCB == 'Yes') {
	$content .= zen_image(DIR_WS_TEMPLATE_IMAGES  . 'card_icons/jcb.png', MODULE_PAYMENT_SAGE_PAY_DIRECT_CARDS_ACCEPTED_SIDEBOX_JCB, '', '', 'class="SagePayDirectCardsAcceptedSideboxCardIcon"');
}
if (MODULE_PAYMENT_SAGE_PAY_DIRECT_ACCEPT_LASER == 'Yes') {
	$content .= zen_image(DIR_WS_TEMPLATE_IMAGES  . 'card_icons/laser.png', MODULE_PAYMENT_SAGE_PAY_DIRECT_CARDS_ACCEPTED_SIDEBOX_LASER, '', '', 'class="SagePayDirectCardsAcceptedSideboxCardIcon"');
}
$content .= '<div style="clear: left;">&nbsp;</div>' . "\n";
$content .= zen_image(DIR_WS_TEMPLATE_IMAGES  . 'card_icons/verified_by_visa_small.png', MODULE_PAYMENT_SAGE_PAY_DIRECT_CARDS_ACCEPTED_SIDEBOX_VERIFIED_BY_VISA, '', '', 'class="SagePayDirectCardsAcceptedSidebox3DSecureIcon"') . "\n";
$content .= zen_image(DIR_WS_TEMPLATE_IMAGES  . 'card_icons/mastercard_securecode_small.png', MODULE_PAYMENT_SAGE_PAY_DIRECT_CARDS_ACCEPTED_SIDEBOX_MASTERCARD_SECURECODE, '', '', 'class="SagePayDirectCardsAcceptedSidebox3DSecureIcon"') . "\n";
if (MODULE_PAYMENT_SAGE_PAY_DIRECT_CARDS_ACCEPTED_SIDEBOX_SHOW_SAGE_PAY_LOGO == 'Yes') {
	$content .= '<div style="clear: left;">&nbsp;</div>' . "\n";
	$content .= zen_image(DIR_WS_TEMPLATE_IMAGES  . 'card_icons/sage_pay_secured.png', MODULE_PAYMENT_SAGE_PAY_DIRECT_CARDS_ACCEPTED_SIDEBOX_SAGE_PAY_SECURED, '', '', 'class="SagePayDirectCardsAcceptedSideboxSagePayIcon"') . "\n";
	$content .= "\n";
}
if (MODULE_PAYMENT_SAGE_PAY_DIRECT_CARDS_ACCEPTED_SIDEBOX_SHOW_PROTX_LOGO == 'Yes') {
	$content .= '<div style="clear: left;">&nbsp;</div>' . "\n";
	$content .= zen_image(DIR_WS_TEMPLATE_IMAGES  . 'card_icons/protx_secured.png', MODULE_PAYMENT_SAGE_PAY_DIRECT_CARDS_ACCEPTED_SIDEBOX_PROTX_SECURED, '', '', 'class="SagePayDirectCardsAcceptedSideboxProtxIcon"') . "\n";
	$content .= "\n";
}
$content .= '</div>';

?>