<?php
/**
 * Common Template - tpl_main_page.php
 *
 * Governs the overall layout of an entire page<br />
 * Normally consisting of a header, left side column. center column. right side column and footer<br />
 * For customizing, this file can be copied to /templates/your_template_dir/pagename<br />
 * example: to override the privacy page<br />
 * - make a directory /templates/my_template/privacy<br />
 * - copy /templates/templates_defaults/common/tpl_main_page.php to /templates/my_template/privacy/tpl_main_page.php<br />
 * <br />
 * to override the global settings and turn off columns un-comment the lines below for the correct column to turn off<br />
 * to turn off the header and/or footer uncomment the lines below<br />
 * Note: header can be disabled in the tpl_header.php<br />
 * Note: footer can be disabled in the tpl_footer.php<br />
 * <br />
 * $flag_disable_header = true;<br />
 * $flag_disable_left = true;<br />
 * $flag_disable_right = true;<br />
 * $flag_disable_footer = true;<br />
 * <br />
 * // example to not display right column on main page when Always Show Categories is OFF<br />
 * <br />
 * if ($current_page_base == 'index' and $cPath == '') {<br />
 *  $flag_disable_right = true;<br />
 * }<br />
 * <br />
 * example to not display right column on main page when Always Show Categories is ON and set to categories_id 3<br />
 * <br />
 * if ($current_page_base == 'index' and $cPath == '' or $cPath == '3') {<br />
 *  $flag_disable_right = true;<br />
 * }<br />
 *
 * @package templateSystem
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_main_page.php 3721 2006-06-07 03:19:12Z birdbrain $
 */

// the following IF statement can be duplicated/modified as needed to set additional flags
  if (in_array($current_page_base,explode(",",'list_pages_to_skip_all_right_sideboxes_on_here,separated_by_commas,and_no_spaces')) ) {
    $flag_disable_right = true;
  }

  if($current_page_base=='product_info'):
    $flag_disable_left = true;
  endif;

  $header_template = 'tpl_header.php';
  $footer_template = 'tpl_footer.php';
  $left_column_file = 'column_left.php';
  $right_column_file = 'column_right.php';
  $body_id = str_replace('_', '', $_GET['main_page']);
?>

<body id="<?php echo $body_id . 'Body'; ?>"<?php echo ' xonload="correctPNG(); '.$zv_onload.'"'; ?>>
<?php /*if($zv_onload !='') echo ' onload="'.$zv_onload.'"'; ?>>*/?>
<?php
  if (SHOW_BANNERS_GROUP_SET1 != '' && $banner = zen_banner_exists('dynamic', SHOW_BANNERS_GROUP_SET1)) {
    if ($banner->RecordCount() > 0) {
?>
<div id="bannerOne" class="banners"><?php echo zen_display_banner('static', $banner); ?></div>
<?php
    }
  }
?>
<div class="headerbg01">
<?php
 /**
  * prepares and displays header output
  *
  */
  require($template->get_template_dir('tpl_header.php',DIR_WS_TEMPLATE, $current_page_base,'common'). '/tpl_header.php');?>

</div>

<div id="mainWrapper2">
<div id="mainWrapper">

<table width="100%" border="0" cellspacing="0" cellpadding="0" id="contentMainWrapper">
  <tr>

    <td valign="top" <?php if(!$flag_disable_left): echo 'id="maincontent"'; endif ?>>
        <?php if($flag_disable_left): ?>
           <div id="search-right" class="prod-page-search"><?php require(DIR_WS_MODULES . 'sideboxes/jsweb/search.php'); ?></div>
        <?php endif ?>
        <div class="header03">
            
            <ul>
                <li><a class="headerlinks01" href="<?php echo zen_href_link(FILENAME_DEFAULT, '', 'NONSSL'); ?>"><?php echo HEADER_TITLE_CATALOG; ?></a></li>
                <li><a class="headerlinks01" href="<?php echo zen_href_link(FILENAME_EZPAGES, 'id=1', 'NONSSL'); ?>"><?php echo HEADER_TITLE_ABOUT; ?></a></li>
                <li><a class="headerlinks01" href="<?php echo zen_href_link(FILENAME_EZPAGES, 'id=4', 'NONSSL'); ?>">THE SHOP</a></li>
                <li><a class="headerlinks01" href="<?php echo zen_href_link(FILENAME_EZPAGES, 'id=5', 'NONSSL'); ?>">SURF CAFE</a></li>
                <li><a class="headerlinks01" href="<?php echo zen_href_link(FILENAME_SURF_FORECAST, '', 'NONSSL'); ?>">SURF FORECAST</a></li>
                <li><a class="headerlinks01" href="<?php echo zen_href_link(FILENAME_EZPAGES, 'id=18', 'NONSSL'); ?>">LESSONS</a></li>
                <li><a class="headerlinks01" href="<?php echo zen_href_link(FILENAME_EZPAGES, 'id=19', 'NONSSL'); ?>">HIRE</a></li>
                <li><a class="headerlinks01" href="<?php echo zen_href_link(FILENAME_SHIPPING, '', 'NONSSL'); ?>">DELIVERY & RETURNS</a></li>
                <li><a class="headerlinks01" href="http://blog.witteringsurfshop.com">BLOG</a></li>
                <li><a class="headerlinks01" href="<?php echo zen_href_link(FILENAME_CONTACT_US, '', 'NONSSL'); ?>"><?php echo HEADER_TITLE_CONTACT; ?></a></li>
                
            </ul>
        </div>

<!-- bof  breadcrumb -->
<?php if (DEFINE_BREADCRUMB_STATUS == '1') { ?>
    <div id="navBreadCrumb"><?php echo $breadcrumb->trail(BREAD_CRUMBS_SEPARATOR); ?></div>
<?php } ?>
<!-- eof breadcrumb -->

<?php
  if (SHOW_BANNERS_GROUP_SET3 != '' && $banner = zen_banner_exists('dynamic', SHOW_BANNERS_GROUP_SET3)) {
    if ($banner->RecordCount() > 0) {
?>
<div id="bannerThree" class="banners"><?php echo zen_display_banner('static', $banner); ?></div>
<?php
    }
  }
?>

<?php
 /**
  * prepares and displays center column
  *
  */
//echo '=='.$body_code;
 require($body_code); ?>

<?php
  if (SHOW_BANNERS_GROUP_SET4 != '' && $banner = zen_banner_exists('dynamic', SHOW_BANNERS_GROUP_SET4)) {
    if ($banner->RecordCount() > 0) {
?>
<div id="bannerFour" class="banners"><?php echo zen_display_banner('static', $banner); ?></div>
<?php
    }
  }
?></td>

<?php
if (COLUMN_RIGHT_STATUS == 0 or (CUSTOMERS_APPROVAL == '1' and $_SESSION['customer_id'] == '')) {
  // global disable of column_right
  $flag_disable_right = true;
}
if (!isset($flag_disable_right) || !$flag_disable_right) {
if($_GET['main_page']=='login'){
}
else
{
?>
<td id="navColumnTwo" class="columnRight" style="width: <?php echo COLUMN_WIDTH_RIGHT; ?>">
<?php
 /**
  * prepares and displays right column sideboxes
  *
  */
?>
<div id="navColumnTwoWrapper" style="width: <?php echo BOX_WIDTH_RIGHT; ?>"><?php require(DIR_WS_MODULES . zen_get_module_directory('column_right.php')); ?></div></td>
<?php
}
}
?>
<?php
if (COLUMN_LEFT_STATUS == 0 or (CUSTOMERS_APPROVAL == '1' and $_SESSION['customer_id'] == '')) {
  // global disable of column_left
  $flag_disable_left = true;
}
if (!isset($flag_disable_left) || !$flag_disable_left) {
?>

 <td id="navColumnOne" class="columnLeft" style="width: <?php echo COLUMN_WIDTH_LEFT; ?>">
<?php
 /**
  * prepares and displays left column sideboxes
  *
  */
?>
<?php require(DIR_WS_MODULES . zen_get_module_directory('column_left.php')); ?>
<div class="sideBoxContent centeredContent"><a href="http://www.witteringsurfshop.com/page.html?id=14"><img src="<?php echo DIR_WS_TEMPLATES . $template_dir; ?>/images/signup_banner.jpg" border="0" alt="" /></a></div>
<?php
if($_GET['main_page']=='surf_forecast'):
    $forecast_banners = $db->Execute("SELECT banners_title,banners_url,banners_image  FROM zen_banners WHERE banners_group = 'forecastsidebar'");
    while(!$forecast_banners->EOF):
        echo '<div class="sideBoxContent centeredContent"><a href="'.$forecast_banners->fields['banners_url'].'"><img src="/images/'.$forecast_banners->fields['banners_image'].'" alt="'.$forecast_banners->fields['banners_title'].'" /></a></div>';
    $forecast_banners->MoveNext();
    endwhile;
endif;
?>
</td>
<?php
}
?>
  </tr>
</table>
</div>
</div>
<div class="footbg01">
<div class="footbg02">
<?php
 /**
  * prepares and displays footer output
  *
  */
  require($template->get_template_dir('tpl_footer.php',DIR_WS_TEMPLATE, $current_page_base,'common'). '/tpl_footer.php');?>
</div>
</div>
<!--bof- parse time display -->
<?php
  if (DISPLAY_PAGE_PARSE_TIME == 'true') {
?>
<div class="smallText center">Parse Time: <?php echo $parse_time; ?> - Number of Queries: <?php echo $db->queryCount(); ?> - Query Time: <?php echo $db->queryTime(); ?></div>
<?php
  }
?>
<!--eof- parse time display -->
<!--bof- banner #6 display -->
<?php
  if (SHOW_BANNERS_GROUP_SET6 != '' && $banner = zen_banner_exists('dynamic', SHOW_BANNERS_GROUP_SET6)) {
    if ($banner->RecordCount() > 0) {
?>
<div id="bannerSix" class="banners"><?php echo zen_display_banner('static', $banner); ?></div>
<?php
    }
  }
?>
<!--eof- banner #6 display -->
<?php
require($template->get_template_dir('.php',DIR_WS_TEMPLATE, $current_page_base,'google_analytics') . '/google_analytics.php');
?>
</body>
