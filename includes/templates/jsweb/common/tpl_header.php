<?php
/**
 * Common Template - tpl_header.php
 *
 * this file can be copied to /templates/your_template_dir/pagename<br />
 * example: to override the privacy page<br />
 * make a directory /templates/my_template/privacy<br />
 * copy /templates/templates_defaults/common/tpl_footer.php to /templates/my_template/privacy/tpl_header.php<br />
 * to override the global settings and turn off the footer un-comment the following line:<br />
 * <br />
 * $flag_disable_header = true;<br />
 *
 * @package templateSystem
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_header.php 4813 2006-10-23 02:13:53Z drbyte $
 */
?>

<?php

function aasort (&$array, $key) {
    $sorter=array();
    $ret=array();
    reset($array);
    foreach ($array as $ii => $va) {
        $sorter[$ii]=$va[$key];
    }
    asort($sorter);
    foreach ($sorter as $ii => $va) {
        $ret[$ii]=$array[$ii];
    }
    $array=$ret;
}

if($_GET['main_page']=='index' and $_GET['cPath'] != '1') 
{
 $red1 = 'redbg01';
}
elseif($_GET['main_page']=='page' and $_GET['id'] == '1')
{
 $red2 = 'redbg01';
}
elseif($_GET['main_page']=='page' and $_GET['id'] == '4')
{
 $red3 = 'redbg01';
}
elseif($_GET['main_page']=='page' and $_GET['id'] == '5')
{
 $red4 = 'redbg01';
}
elseif($_GET['main_page']=='page' and $_GET['id'] == '6')
{
 $red5 = 'redbg01';
}
elseif($_GET['main_page']=='shippinginfo')
{
 $red6 = 'redbg01';
}

elseif($_GET['main_page']=='page' and $_GET['id'] == '7')
{
 $red8 = 'redbg01';
}
elseif($_GET['main_page']=='contact_us')
{
 $red9 = 'redbg01';
}
 else 
 {
 $red1  = '';
 }
?>


<!--bof-header logo and navigation display-->
<?php
if (!isset($flag_disable_header) || !$flag_disable_header) {

    //instead of shite hard-coding, how about just grabbing top-level categories???
    //get all parent cats
    //$top_cats_query = "SELECT categories_id,categories_name, sort_order FROM zen_categories LEFT JOIN zen_categories_description USING(categories_id) WHERE categories_id IN($instring) AND categories_status=1 ORDER BY  find_in_set(categories_id, '$instring');";
    $top_cats_query = "SELECT categories_id,categories_name, sort_order FROM zen_categories LEFT JOIN zen_categories_description USING(categories_id) WHERE parent_id=0 AND categories_status=1 ORDER BY  find_in_set(categories_id, '$instring');";
    $top_cats_query = $db->Execute($top_cats_query);
    while(!$top_cats_query->EOF):
        $topcats[$top_cats_query->fields['categories_id']] = $top_cats_query->fields;
        $top_cats_query->MoveNext();
    endwhile;

    //get all sub-cats
    foreach($topcats as $parent_cat_id => $junk):
        $get_sub_cats = "SELECT categories_id,categories_name, sort_order FROM zen_categories LEFT JOIN zen_categories_description USING(categories_id) WHERE parent_id = $parent_cat_id ORDER BY categories_name";
        $get_sub_cats = $db->Execute($get_sub_cats);
        while(!$get_sub_cats->EOF):
            $subcats[$parent_cat_id][] = $get_sub_cats->fields;
            $get_sub_cats->MoveNext();
        endwhile;
    endforeach;
?>
<script>
function showSubMenu(parentCatId){
   document.getElementById('submnu-'+parentCatId).style.display = 'block';
}
function hideSubMenu(parentCatId){
   document.getElementById('submnu-'+parentCatId).style.display = 'none';
}
</script>
<?php
                $current_hour = date('G');
                switch(true):
                    case $current_hour<12:
                        $time_greeting = 'Morning';
                    break;
                    case $current_hour<17:
                        $time_greeting = 'Afternoon';
                    break;
                    case $current_hour<=23:
                        $time_greeting = 'Evening';
                    break;
                endswitch;
 if(!$_SESSION['customer_id']): ?>
    <div id="loggedout-bar">
            
        Good <?=$time_greeting?>, Been here before? <a href="<?=zen_href_link('login')?>">Login</a> or <a href="<?=zen_href_link('create_account')?>">Register</a>
    </div>
<?php else:
    $get_cust_name = $db->Execute("SELECT customers_firstname FROM zen_customers WHERE customers_id = ".zen_db_input($_SESSION['customer_id']));
?>
    <div id="loggedout-bar">
        Good <?=$time_greeting?> <?=$get_cust_name->fields['customers_firstname']?>, <a href="<?=zen_href_link('account')?>">Click here</a> to view your account. Not <?=$get_cust_name->fields['customers_firstname']?>? <a href="<?=zen_href_link('logoff')?>">Log Out</a>
    </div>
<?php endif ?>
<div id="header-main">
    <div id="header-main-locate">
        <div id="header-main-right">
            <img src="images/header/call.gif" alt="Call us: 01243 672292 - Expert advice 7 days a week" />
            <div id="header-main-basket">
                <div id="content-text">
                    <?php require(DIR_WS_MODULES . 'sideboxes/jsweb/shopping_cart.php'); ?>
                </div>
            </div>
        </div>
        <div id="header-main-logo">
            <a href="<?=zen_href_link('index')?>"><img src="images/logotop.png" alt="Wittering Surf Shop" /></a>
        </div>
        <div id="header-mnu">
            <ul>
             
                <?php 
                //print_r($topcats);
                aasort($topcats, "sort_order");
                foreach($topcats as $cat_id => $cat): ?>
                
                    <li<?php if(isset($subcats[$cat_id])&&is_array($subcats[$cat_id])&&count($subcats[$cat_id])>0): ?> onmouseover="showSubMenu(<?=$cat_id?>)" onmouseout="hideSubMenu(<?=$cat_id?>)"<?php endif ?>>
                        <a href="<?=zen_href_link(FILENAME_DEFAULT,'cPath='.$cat['categories_id'])?>" id="menuitem-<?=$cat['categories_id']?>"><?=$cat['categories_name']?></a>
                        <?php if(isset($subcats[$cat_id])&&is_array($subcats[$cat_id])&&count($subcats[$cat_id])>0): ?>
                        <div class="submnu<?=$cat_id==40 ? ' submnu-right' : '' ?>" id="submnu-<?=$cat_id?>">
                            <div class="submnu-main">
                                <ul>
                                    <?php 
                                        aasort($subcats[$cat_id], "sort_order");
                                        foreach($subcats[$cat_id] as $sub): ?>
                                    <li><a href="<?=zen_href_link(FILENAME_DEFAULT,'cPath='.$sub['categories_id'])?>"><?=$sub['categories_name']?></a></li>
                                    <?php endforeach ?>
                                </ul>
                                <div class="clear"></div>
                            </div>
                            <div class="submnu-bot"></div>
                        </div>
                        <?php endif ?>
                    </li>
                <?php endforeach ?>
                    <li>
                        <a href="index.php?main_page=surf_forecast">SURF FORECAST</a>
                    </li>
                    <li onmouseover="showSubMenu(200)" onmouseout="hideSubMenu(200)">
                        <a href="index.php?main_page=page&id=1">ABOUT</a>
                        <div class="submnu" id="submnu-200">
                            <div class="submnu-main">
                                <ul>
                                    <li><a href="index.php?main_page=page&id=1">THE TEAM</a></li>
                                    <li><a href="index.php?main_page=page&id=4">THE SHOP</a></li>
                                    <li><a href="index.php?main_page=page&id=5">THE CAFE</a></li>
                                    <li><a href="index.php?main_page=page&id=19">HIRE AND REPAIR</a></li>
                                </ul>
                                <div class="clear"></div>
                            </div>
                            <div class="submnu-bot"></div>
                        </div>
                    </li>
                    <li>
                        <a href="http://blog.witteringsurfshop.com/">BLOG</a>
                    </li>
                    <li>
                        <a href="index.php?main_page=contact_us">CONTACT</a>
                    </li>
                   
            </ul>
        </div>
    </div>
</div>
<!--<div class="message-main">
<div class="message-home" ><img src="http://localhost:8888/witteringsurfshop/includes/templates/jsweb/css/images/offer-icon.jpg" /> <a href="http://www.witteringsurfshop.com/kayaks-c-91.html">NEW Osprey Kayaks In Store Now!</a> | <a href="http://www.witteringsurfshop.com/shippinginfo.html">FREE UK Delivery for orders over &pound;50</a></div>
<div class="message-fb" ><img src="http://localhost:8888/witteringsurfshop/includes/templates/jsweb/css/images/fb-icon.jpg" /> <a href="http://www.facebook.com/witteringsurfshop" target="_blank">Like us on Facebook for exclusive offers, competitions, news &amp; deals!</a></div>
</div>-->
<div style="clear:both;"></div><br />



















<div id="headerWrapper">
<!--bof-navigation display-->



<!--eof-header logo and navigation display-->
<?php
  // Display all header alerts via messageStack:
  if ($messageStack->size('header') > 0) {
    echo $messageStack->output('header');
  }
  if (isset($_GET['error_message']) && zen_not_null($_GET['error_message'])) {
  echo htmlspecialchars(urldecode($_GET['error_message']));
  }
  if (isset($_GET['info_message']) && zen_not_null($_GET['info_message'])) {
   echo htmlspecialchars($_GET['info_message']);
} else {

} ?>
</div>
<?php } ?>
