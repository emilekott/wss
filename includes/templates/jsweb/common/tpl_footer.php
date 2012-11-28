<?php
/**
 * Common Template - tpl_footer.php
 *
 * this file can be copied to /templates/your_template_dir/pagename<br />
 * example: to override the privacy page<br />
 * make a directory /templates/my_template/privacy<br />
 * copy /templates/templates_defaults/common/tpl_footer.php to /templates/my_template/privacy/tpl_footer.php<br />
 * to override the global settings and turn off the footer un-comment the following line:<br />
 * <br />
 * $flag_disable_footer = true;<br />
 *
 * @package templateSystem
 * @copyright Copyright 2003-2005 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_footer.php 4821 2006-10-23 10:54:15Z drbyte $
 */
require(DIR_WS_MODULES . zen_get_module_directory('footer.php'));
?>
<?php
if (!isset($flag_disable_footer) || !$flag_disable_footer) {
?>
<!--bof-navigation display -->
<div class="footer01">
<div class="footer02">
		<ul>
		<li><a class="footerlinks01" href="<?php echo zen_href_link(FILENAME_DEFAULT, '', 'NONSSL'); ?>"><?php echo HEADER_TITLE_CATALOG; ?></a></li><span class="footerpad">&nbsp;|&nbsp;</span>
		<li><a class="footerlinks01" href="<?php echo zen_href_link(FILENAME_EZPAGES, 'id=1', 'NONSSL'); ?>"><?php echo HEADER_TITLE_ABOUT; ?></a></li><span class="footerpad">&nbsp;|&nbsp;</span>
		<li><a class="footerlinks01" href="<?php echo zen_href_link(FILENAME_CONTACT_US, '', 'NONSSL'); ?>"><?php echo HEADER_TITLE_CONTACT; ?></a></li><span class="footerpad">&nbsp;|&nbsp;</span>
        <li><a class="footerlinks01" href="<?php echo zen_href_link(FILENAME_PRIVACY, '', 'NONSSL'); ?>"><?php echo BOX_INFORMATION_PRIVACY; ?></a></li><span class="footerpad">&nbsp;|&nbsp;</span>
         <li><a class="footerlinks01" href="<?php echo zen_href_link(FILENAME_CONDITIONS, '', 'NONSSL'); ?>"><?php echo BOX_INFORMATION_CONDITIONS; ?></a></li><span class="footerpad">&nbsp;|&nbsp;</span>
		<li><a class="footerlinks01" href="<?php echo zen_href_link(FILENAME_ACCOUNT, '', 'NONSSL'); ?>"><?php echo HEADER_TITLE_MY_ACCOUNT; ?></a></li><span class="footerpad">&nbsp;|&nbsp;</span>
<?php if (isset($_SESSION['customer_id'])) { ?>
		<li><a class="footerlinks01" href="<?php echo zen_href_link(FILENAME_LOGOFF, '', 'NONSSL'); ?>"><?php echo HEADER_TITLE_LOGOFF; ?></a></li><span class="footerpad">&nbsp;|&nbsp;</span>
<?php
      } else {
        if (STORE_STATUS == '0') {
?>
		<li><a class="footerlinks01" href="<?php echo zen_href_link(FILENAME_LOGIN, '', 'SSL'); ?>"><?php echo HEADER_TITLE_LOGIN; ?></a></li><span class="footerpad">&nbsp;|&nbsp;</span>
<?php } } ?>
		<li><a class="footerlinks01" href="<?php echo zen_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'NONSSL'); ?>"><?php echo HEADER_TITLE_CHECKOUT; ?></a></li>		
		</ul>
</div>
<div class="centeredContent" style="width:998px; margin: 0px auto 0px auto; position: relative"><img  src="<?php echo DIR_WS_TEMPLATES . $template_dir; ?>/images/design/cards2.gif" border="0" alt="Secure Payments" />

<div id="socialfooter">
    <a href="http://www.facebook.com/WitteringSurfShop" target="_blank"><img src="<?php echo DIR_WS_TEMPLATES . $template_dir; ?>/images/design/facebook_icon.png" align="right"  border="0" alt="View our Facebook Fan Page" /></a>
    <img src="<?php echo DIR_WS_TEMPLATES . $template_dir; ?>/images/design/blank.png" align="right"  border="0" alt="" />
    <a href="http://www.twitter.com/WittSurfShop" target="_blank"> <img src="<?php echo DIR_WS_TEMPLATES . $template_dir; ?>/images/design/twitter_icon.png" align="right" border="0" alt="Follow us on Twitter" /></a>
</div>

</div>
<!--bof-ip address display -->
<?php
if (SHOW_FOOTER_IP == '1') {
?>
<div id="siteinfoIP"><?php echo TEXT_YOUR_IP_ADDRESS . '  ' . $_SERVER['REMOTE_ADDR']; ?></div>
<?php
}
?>
<!--eof-ip address display -->

<!--bof-banner #5 display -->
<?php
  if (SHOW_BANNERS_GROUP_SET5 != '' && $banner = zen_banner_exists('dynamic', SHOW_BANNERS_GROUP_SET5)) {
    if ($banner->RecordCount() > 0) {
?>
<div id="bannerFive" class="banners"><?php echo zen_display_banner('static', $banner); ?></div>
<?php
    }
  }
?>
<!--eof-banner #5 display -->

<!--bof- site copyright display -->
<div id="siteinfoLegal" class="legalCopyright"><?php echo FOOTER_TEXT_BODY; ?></div>

<!--eof- site copyright display -->
</div>
<?php
} // flag_disable_footer
?>
