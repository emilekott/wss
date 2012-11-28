<?php
/**
 * sage_pay_direct 3D-Secure Page Template
 *
 * Loaded automatically by index.php?main_page=checkout_sage_pay_direct_3d_secure<br />
 * Displays the message about the transaction requiring 3D-Secure authentication.
 *
 * @package    templateSystem
 * @author     Conor Kerr <sage_pay_direct@dev.ceon.net>
 * @copyright  Copyright 2006-2009 Ceon
 * @copyright  Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright  Portions Copyright 2003 osCommerce
 * @link       http://dev.ceon.net/web/zen-cart/sage_pay_direct
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: tpl_checkout_sage_pay_direct_3d_secure_default.php 385 2009-06-23 11:11:45Z Bob $
 */
?>
<div class="centerColumn" id="checkoutPayment">
	<h1 id="checkoutPaymentHeading"><?php echo HEADING_TITLE; ?></h1>
	<p><?php echo zen_image(DIR_WS_TEMPLATE_IMAGES  . 'card_icons/verified_by_visa.png', MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_3D_SECURE_VERIFIED_BY_VISA, '', '', 'class="SagePayDirect3DSecureIcon"') .
		zen_image(DIR_WS_TEMPLATE_IMAGES  . 'card_icons/mastercard_securecode.png', MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_3D_SECURE_MASTERCARD_SECURECODE, '', '', 'class="SagePayDirect3DSecureIcon"'); ?>
	<?php echo CHECKOUT_SAGE_PAY_DIRECT_3D_SECURE_TEXT_NECESSARY; ?></p>
	<h3><?php echo CHECKOUT_SAGE_PAY_DIRECT_3D_SECURE_TEXT_3D_SECURE_WHAT_IS_3D_SECURE; ?></h3>
	<p><?php echo CHECKOUT_SAGE_PAY_DIRECT_3D_SECURE_TEXT_3D_SECURE_ADDITIONAL_INFO; ?></p>
	<?php
	// Display overview of 3D-Secure process if javascript is active within the current session
	?>
	<script language="Javascript">
	<!--
	document.write('<' + 'h3>' + '<?php echo addslashes(MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_3D_SECURE_WHAT_NOW); ?>' + '<' + '/h3>');
	document.write('<' + 'p>' + '<?php echo addslashes(MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_3D_SECURE_DISPLAYED_BELOW_1); ?>' + '<' + '/p>');
	document.write('<' + 'p>' + '<?php echo addslashes(MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_3D_SECURE_DISPLAYED_BELOW_2); ?>' + '<' + '/p>');
	// -->
	</script>
	<style type="text/css">
	iframe#sage_pay_direct_3d_secure_iframe {
		border: none;
		width: 100%;
		height: 450px;
	}
	</style>
	<iframe frameborder="0" src="<?php echo zen_href_link(FILENAME_SAGE_PAY_DIRECT_3D_SECURE_IFRAME, 'ACSURL=' . urlencode($acs_url) . '&PaReq=' . urlencode($pa_req) . '&MD=' . urlencode($md), 'SSL', true, true, true, true); ?>" id="sage_pay_direct_3d_secure_iframe">
		<!-- Build form for any browser that doesn't support iFrames -->
		<p><?php echo zen_image(DIR_WS_TEMPLATE_IMAGES  . 'card_icons/verified_by_visa.png', MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_3D_SECURE_VERIFIED_BY_VISA, '', '', 'class="SagePayDirect3DSecureIcon"') .
			zen_image(DIR_WS_TEMPLATE_IMAGES  . 'card_icons/mastercard_securecode.png', MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_3D_SECURE_MASTERCARD_SECURECODE, '', '', 'class="SagePayDirect3DSecureIcon"'); ?>
		<?php echo CHECKOUT_SAGE_PAY_DIRECT_3D_SECURE_TEXT_NO_IFRAME_SUPPORT_1; ?></p>
		<p><?php echo CHECKOUT_SAGE_PAY_DIRECT_3D_SECURE_TEXT_NO_IFRAME_SUPPORT_2; ?></p>
		<p><?php echo CHECKOUT_SAGE_PAY_DIRECT_3D_SECURE_TEXT_NO_IFRAME_SUPPORT_3; ?></p>
		<p><?php echo CHECKOUT_SAGE_PAY_DIRECT_3D_SECURE_TEXT_NO_IFRAME_SUPPORT_4; ?></p>
		<form name="form" action="<?php echo htmlspecialchars($acs_url); ?>" method="POST">
			<input type="hidden" name="PaReq" value="<?php echo str_replace(' ', '+', htmlspecialchars($pa_req)); ?>" />
			<input type="hidden" name="TermUrl" value="<?php echo str_replace(' ', '+', zen_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL')); ?>" />
			<input type="hidden" name="MD" value="<?php echo str_replace(' ', '+', htmlspecialchars($md)); ?>" />
			
			<br clear="all" />
			<div class="buttonRow forward"><?php echo zen_image_submit(BUTTON_IMAGE_CONTINUE_CHECKOUT, BUTTON_CONTINUE_ALT); ?></div>
			<div class="buttonRow back"><?php echo TITLE_CONTINUE_CHECKOUT_PROCEDURE . '<br />' . TEXT_CONTINUE_CHECKOUT_PROCEDURE; ?></div>
		</form>
	</iframe>
</div>
