<?php

/**
 * sage_pay_direct 3D-Secure iFrame Redirection and Callback Handler
 *
 * @author     Conor Kerr <sage_pay_direct@dev.ceon.net>
 * @copyright  Copyright 2006-2009 Ceon
 * @copyright  Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright  Portions Copyright 2003 osCommerce
 * @link       http://dev.ceon.net/web/zen-cart/sage_pay_direct
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: sage_pay_direct_3d_secure_iframe.php 385 2009-06-23 11:11:45Z Bob $
 */

require('includes/application_top.php');

$_SESSION['navigation']->remove_current_page();

/**
 * Require language defines
 */
$sage_pay_direct_language_file_path = 'modules/payment/sage_pay_direct.php';
if (file_exists(DIR_WS_LANGUAGES . $_SESSION['language'] . '/' . $template_dir_select . $sage_pay_direct_language_file_path)) {
	require(DIR_WS_LANGUAGES . $_SESSION['language'] . '/' . $template_dir_select . $sage_pay_direct_language_file_path);
} else {
	require(DIR_WS_LANGUAGES . $_SESSION['language'] . '/' . $sage_pay_direct_language_file_path);
}

$current_page_base = 'sage_pay_direct_3d_secure_iframe';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Sage Pay Direct 3D-Secure Authorisation IFrame</title>
	<?php
	/**
	* Load all template-specific stylesheets, named like "style*.css", alphabetically
	*/
	$directory_array = $template->get_template_part($template->get_template_dir('.css', DIR_WS_TEMPLATE, $current_page_base, 'css'), '/^style/', '.css');
	while(list ($key, $value) = each($directory_array)) {
		echo '<link rel="stylesheet" type="text/css" href="' . $template->get_template_dir('.css', DIR_WS_TEMPLATE, $current_page_base, 'css') . '/' . $value . '" />' . "\n";
	}
	/**
	* Load stylesheets on a per-page basis. Concept by Juxi Zoza.
	*/
	$sheets_array = array('/' . $_SESSION['language'] . '_stylesheet', 
		'/' . $current_page_base, 
		'/' . $_SESSION['language'] . '_' . $current_page_base, 
		);
	while(list ($key, $value) = each($sheets_array)) {
		$perpagefile = $template->get_template_dir('.css', DIR_WS_TEMPLATE, $current_page_base, 'css') . $value . '.css';
		if (file_exists($perpagefile)) {
			echo '<link rel="stylesheet" type="text/css" href="' . $perpagefile .'" />'."\n";
		}
	}
	?>
</head>
<body>
<?php
// Don't display any messages/buttons if javascript is active within the current session
?>
<script language="Javascript">
<!--
document.write('<' + 'style type="text/css">');
document.write('.SagePayDirectJavascriptSuppressed { display: none; }');
document.write('<' + '/style>');
// -->
</script>

<?php
if (isset($_GET['ACSURL'])) {
	// Pass the information to the callback URL
?>
<h3 class="SagePayDirectJavascriptSuppressed"><?php echo MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_3D_SECURE_WHAT_NOW; ?></h3>
<p class="SagePayDirectJavascriptSuppressed"><?php echo MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_3D_SECURE_DISPLAYED_NEXT_1; ?></p>
<p class="SagePayDirectJavascriptSuppressed"><?php echo MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_3D_SECURE_DISPLAYED_NEXT_2; ?></p>
<p class="SagePayDirectJavascriptSuppressed"><?php echo MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_3D_SECURE_CLICK_TO_AUTHENTICATE; ?></p>

<form name="checkout_sage_pay_direct_3d_secure" action="<?php echo htmlspecialchars($_GET['ACSURL']); ?>" method="POST">
	<input type="hidden" name="PaReq" value="<?php echo str_replace(' ', '+', htmlspecialchars($_GET['PaReq'])); ?>" />
	<input type="hidden" name="TermUrl" value="<?php echo str_replace(' ', '+', zen_href_link(FILENAME_SAGE_PAY_DIRECT_3D_SECURE_IFRAME, 'made_by=ceon', 'SSL', true, true, true, true)); ?>" />
	<input type="hidden" name="MD" value="<?php echo str_replace(' ', '+', htmlspecialchars($_GET['MD'])); ?>" />

	<br clear="all" />
	<div class="buttonRow forward SagePayDirectJavascriptSuppressed"><?php echo zen_image_submit(BUTTON_IMAGE_CONTINUE_CHECKOUT, BUTTON_CONTINUE_ALT); ?></div>
	<div class="buttonRow back SagePayDirectJavascriptSuppressed"><?php echo TITLE_CONTINUE_CHECKOUT_PROCEDURE . '<br />' . TEXT_CONTINUE_CHECKOUT_PROCEDURE; ?></div>
</form>
<?php
// Output javascript necessary for auto-submission of form
?>
<script language="Javascript">
<!--
document.checkout_sage_pay_direct_3d_secure.submit();
// -->
</script>

<?php
} else if (isset($_POST['PaRes'])){
	// Grab the returned PaRes and post it to the checkout payment page
?>

<p class="SagePayDirectJavascriptSuppressed"><?php echo MODULE_PAYMENT_SAGE_PAY_DIRECT_TEXT_3D_SECURE_COMPLETED_CONTINUE; ?></p>
	
<form name="checkout_sage_pay_direct_3d_secure" action="<?php echo zen_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL'); ?>" method="POST" target="_parent">
	<input type="hidden" name="PaRes" value="<?php echo str_replace(' ', '+', htmlspecialchars($_POST['PaRes'])); ?>" />
	<input type="hidden" name="MD" value="<?php echo str_replace(' ', '+', htmlspecialchars($_POST['MD'])); ?>" />

	<br clear="all" />
	<div class="buttonRow forward SagePayDirectJavascriptSuppressed"><?php echo zen_image_submit(BUTTON_IMAGE_CONTINUE_CHECKOUT, BUTTON_CONTINUE_ALT); ?></div>
</form>
<?php
// Output javascript necessary for auto-submission of form
?>
<script language="Javascript">
<!--
document.checkout_sage_pay_direct_3d_secure.submit();
// -->
</script>

<?php	
} else {
	// ERROR!
	print "Sage Pay response failed?!";
}
?>
</body>
</html>


