<?php
/**
 * Page Template
 *
 * Main index page<br />
 * Displays greetings, welcome text (define-page content), and various centerboxes depending on switch settings in Admin<br />
 * Centerboxes are called as necessary
 *
 * @package templateSystem
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_index_default.php 3464 2006-04-19 00:07:26Z ajeh $
 */
?>
<div class="centerColumn2" id="indexDefault">


<?php if (SHOW_CUSTOMER_GREETING == 1) { ?>
<h2 class="greeting"><?php echo zen_customer_greeting(); ?></h2>
<?php } ?>

<!-- deprecated - to use uncomment this section
<?php if (TEXT_MAIN) { ?>
<div id="" class="content"><?php echo TEXT_MAIN; ?></div>
<?php } ?>-->

<!-- deprecated - to use uncomment this section
<?php if (TEXT_INFORMATION) { ?>
<div id="" class="content"><?php echo TEXT_INFORMATION; ?></div>
<?php } ?>-->

<?php if (DEFINE_MAIN_PAGE_STATUS >= 1 and DEFINE_MAIN_PAGE_STATUS <= 2) { ?>
<?php
/**
 * get the Define Main Page Text
 */
?>
<div class="back" style="padding-bottom:10px;">
<style type="text/css">
.nav { padding: 5px; height:22px; padding-top:10px; background-image:url(http://www.witteringsurfshop.com/images/slidebg.png); background-repeat:repeat-x; background-color:#e3e3e3; }
#nav a, #s7 strong {  width:25px; margin:5px; color:#fff; padding-right:8px; padding-left:6px; padding-bottom:2px; padding-top:3px; background-image:url(http://www.witteringsurfshop.com/images/navbg_off.png); background-repeat:no-repeat; text-decoration: none; color:#fff; }
#nav a.activeSlide {margin:5px; background-image:url(http://www.witteringsurfshop.com/images/navbg_on.png); background-repeat:no-repeat; width:25px; color:#fff; padding-top:3px; padding-bottom:2px; padding-right:8px; padding-left:6px;}
#nav a:focus { outline: none; color:#fff;}
#output { text-align: left;  }

#nav { text-align: left; width: 766px; }
</style>
<script type="text/javascript" src="js/jquery-1.js"></script>
<script type="text/javascript" src="js/chili-1.js"></script>
<script type="text/javascript" src="js/jquery_002.js"></script>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript">
$(function() {
    $('#s4').after('<div id="nav" class="nav">').cycle({
        fx:     'turnRight',
        speed:  'fast',
        timeout: 6000,
        pager:  '#nav',
        after: function() { if (window.console) console.log(this.src); }
    });
});
</script>

<div id="s4" class="pics">
<!--bof- banner #6 display -->
<?php
echo zen_display_banner_main('slideshow');
?>
<!--eof- banner #6 display -->
</div>

</div>

<div id="indexDefaultMainContent" class="content" style="padding-bottom:2px;">
<?php require($define_page); ?></div>

<?php } ?>


<?php
  $show_display_category = $db->Execute(SQL_SHOW_PRODUCT_INFO_MAIN);
  while (!$show_display_category->EOF) {
?>

<?php if ($show_display_category->fields['configuration_key'] == 'SHOW_PRODUCT_INFO_MAIN_FEATURED_PRODUCTS') { ?>
<?php
/**
 * display the Featured Products Center Box
 */
?>
<?php require($template->get_template_dir('tpl_modules_featured_products.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_featured_products.php'); ?>
<?php } ?>

<?php if ($show_display_category->fields['configuration_key'] == 'SHOW_PRODUCT_INFO_MAIN_SPECIALS_PRODUCTS') { ?>
<?php
/**
 * display the Special Products Center Box
 */
?>
<?php require($template->get_template_dir('tpl_modules_specials_default.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_specials_default.php'); ?>
<?php } ?>



<?php if ($show_display_category->fields['configuration_key'] == 'SHOW_PRODUCT_INFO_MAIN_UPCOMING') { ?>
<?php
/**
 * display the Upcoming Products Center Box
 */
?>
<?php include(DIR_WS_MODULES . zen_get_module_directory(FILENAME_UPCOMING_PRODUCTS)); ?><?php } ?>

<?php
  if ('center02' != '' && $banner = zen_banner_exists('dynamic', 'center02')) {
    if ($banner->RecordCount() > 0) {
?>
<div class="back" style="padding-bottom:10px;"><?php echo zen_display_banner('static', $banner); ?></div>
<?php
    }
  }
?>
<?php
  if ('center03' != '' && $banner = zen_banner_exists('dynamic', 'center03')) {
    if ($banner->RecordCount() > 0) {
?>
<div class="forward" style="padding-bottom:10px;"><?php echo zen_display_banner('static', $banner); ?></div>
<?php
    }
  }
?>

<?php if ($show_display_category->fields['configuration_key'] == 'SHOW_PRODUCT_INFO_MAIN_NEW_PRODUCTS') { ?>
<?php
/**
 * display the New Products Center Box
 */
?>
<?php //require($template->get_template_dir('tpl_modules_whats_new_2.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_whats_new_2.php'); ?>
<?php } ?>
<br class="clearBoth" />
<?php
  $show_display_category->MoveNext();
} // !EOF
?>
<?php if ($show_display_category->fields['configuration_key'] == 'SHOW_PRODUCT_INFO_MAIN_NEW_PRODUCTS') {
/**
 * display the New Products Center Box
 */
    //echo $template->get_template_dir('tpl_modules_whats_new.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_whats_new.php';
?>

<?php require($template->get_template_dir('tpl_modules_whats_new.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_whats_new.php'); ?>
<?php } ?>
<h1>Online Surf Shop selling a great range of Surfboards, Wetsuits, Clothing and Hardware</h1>
<p>We specialise in <a href="http://www.witteringsurfshop.com/surfboards-c-5.html">surfing</a>, <a href="http://www.witteringsurfshop.com/hardware-c-48.html">surf hardware</a> and <a href="http://www.witteringsurfshop.com/clothing-footwear-c-57.html">surf fashion</a> and we make sure that we know our stuff.<br />

<br />We know that we can't sell you that new style of board if we don't know a thing about the construction or advise you of the benefits of a <a href="http://www.witteringsurfshop.com/wetsuits-c-47.html">wetsuit</a> with a front zip if we have never worn one... so, we swot up in our own time to make sure that we can give you the best possible advice, information and after sales service.</p>
<br />
<p>We are advocates for the products we stock. We surf the boards, we wear the clothes, we try on the wetsuits. The idea is that we fill the shop with gear that we like and that we think you'll like too.</p>
<br />
<p> We love quality brands, a few of our favourites are <a href="http://www.witteringsurfshop.com/xcel-wetsuits-m-1.html">Xcel Wetsuits</a>, <a href="http://www.witteringsurfshop.com/freewaters-sandals-house-shoes-m-62.html">Freewaters</a>, <a href="http://www.witteringsurfshop.com/old-guys-rule-m-49.html">Old Guys Rule</a>, <a href="http://www.witteringsurfshop.com/firewire-surfboards-m-19.html">Firewire Surfboards</a>, <a href="http://www.witteringsurfshop.com/jp-surfboards-m-16.html">JP Surfboards</a>, <a href="http://www.witteringsurfshop.com/lost-surfboards-m-73.html">Lost Surfboards</a>, <a href="http://www.witteringsurfshop.com/ocean-and-earth-m-6.html">Ocean / Earth</a>, <a href="http://www.witteringsurfshop.com/black-white-surfboards-m-15.html">Black &amp; White Surfboards</a>, <a href="http://www.witteringsurfshop.com/fluid-juice-surfboards-m-27.html">Fluid Juice Surfboards</a>, <a href="http://www.witteringsurfshop.com/nalu-beads-m-50.html">Nalu Beads</a> &amp; <a href="http://www.witteringsurfshop.com/page.html?id=12">Many More...</a></p>
</div>
