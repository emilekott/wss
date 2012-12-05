<?php
/**
 * Page Template
 *
 * Loaded automatically by index.php?main_page=product_info.<br />
 * Displays details of a typical product
 *
 * @package templateSystem
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_product_info_display.php 5369 2006-12-23 10:55:52Z drbyte $
 */
 //require(DIR_WS_MODULES . '/debug_blocks/product_info_prices.php');
?>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
<script type="text/javascript" src="./js/jquery.form.js"></script>
<script language="javascript">
jQuery.noConflict();
<!--
// prepare the form when the DOM is ready 
jQuery(document).ready(function() { 
    var options = { 
        target:        '#content-text',   // target element(s) to be updated with server response
	url:'add_to_cart.php',         // override for form's 'action' attribute 
        type:'POST',        // 'get' or 'post', override for form's 'method' attribute 
        beforeSubmit:  showRequest,  // pre-submit callback 
        success:       showResponse,  // post-submit callback
        error: function() { alert('boo'); }
        // other available options: 
        //dataType:  null        // 'xml', 'script', or 'json' (expected server response type) 
        //clearForm: true        // clear all form fields after successful submit 
        //resetForm: true        // reset the form after successful submit 
 
        // $.ajax options can be used here too, for example: 
        //timeout:   3000 
    };  
    // bind to the form's submit event 
    jQuery('#cart_quantity').submit(function() { 
        jQuery(this).ajaxSubmit(options); 
        return false; 
    }); 
}); 
 
// pre-submit callback 
function showRequest(formData, jqForm, options) { 
    var queryString = jQuery.param(formData); 
   document.getElementById('load1').innerHTML = '<img src="<?php echo DIR_WS_TEMPLATES . $template_dir; ?>/images/loading.gif" border="0">';
    return true; 
} 
 
// post-submit callback 
function showResponse(responseText, statusText)  { 
   document.getElementById('load1').innerHTML = '';
   document.getElementById('button_cart').innerHTML = '<a href="<?php echo zen_href_link(FILENAME_SHOPPING_CART, '', 'NONSSL');?>"><img src="<?php echo DIR_WS_TEMPLATES . $template_dir;?>/images/design/checkoutnow.jpg" border="0" alt="" /></a>';
   document.getElementById('button_cartadded').innerHTML = '<input type="image" src="includes/templates/jsweb/buttons/english/added.jpg" alt="Add to Cart" title=" Add to Cart " />';
} 

		-->
</script>
<div id="productGeneral2">
<!--bof Form start-->
<form name="cart_quantity" action="" id="cart_quantity">  
<?php //echo zen_draw_form('cart_quantity', zen_href_link(zen_get_info_page($_GET['products_id']), zen_get_all_get_params(array('action')) . 'action=add_product'), 'post', 'enctype="multipart/form-data"') . "\n"; ?>
<!--eof Form start-->
<?php if ($messageStack->size('product_info') > 0) echo $messageStack->output('product_info'); ?>
<div class="newbr">
<div class="newbr01 back">
<!--bof Category Icon -->
<?php if ($module_show_categories != 0) {?>
<?php
/**
 * display the category icons
 */
require($template->get_template_dir('/tpl_modules_category_icon_display.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_category_icon_display.php'); ?>
<?php } ?>
<!--eof Category Icon -->
<div class="newbr02"><?php echo $breadcrumb->trail(BREAD_CRUMBS_SEPARATOR); ?></div>
</div>
<div class="newbr03 forward">
<!--bof Prev/Next top position -->
<?php if (PRODUCT_INFO_PREVIOUS_NEXT == 1 or PRODUCT_INFO_PREVIOUS_NEXT == 3) { ?>
<?php
/**
 * display the product previous/next helper
 */
require($template->get_template_dir('/tpl_products_next_previous.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_products_next_previous.php'); ?>
<?php } ?>
<!--eof Prev/Next top position-->
</div>
<br class="clearBoth" />
</div>


<div class="prodinfo02 forward">
<!--bof Product Name-->
<div id="productName" class="color01 back"><?php echo $products_name; ?></div>
<!--eof Product Name-->
<?php
//calculate delivery date
//get day. if mon - thurs before 12, day+1
//if fri before 12, day + 3
$prod_quantity = ($products_quantity == 1)? '1 item in stock.' : $products_quantity.' items in stock.';
if(zen_get_products_manufacturers_image((int)$_GET['products_id'])){

	echo '<div class="manu1 forward"><a href="'. zen_href_link(FILENAME_DEFAULT, 'manufacturers_id='. $product_info->fields['manufacturers_id'] .'', 'NONSSL') .'">' . zen_image(DIR_WS_IMAGES . zen_get_products_manufacturers_image((int)$_GET['products_id']), 'View all products from this manufacturer', '100', 100, 'class="listingProductImage"') . '</a></div>';
}
if($flag_show_product_info_quantity == 1){
    echo '<div class="back stockq">'.$prod_quantity.'<br /><span class="del-est">'.get_estimated_delivery_date().'</span></div>';
}

?>
<br class="clearBoth" />
<br class="clearBoth" />
<!--bof Product Price block -->
<h2 id="productPrices" class="productGeneraloff back"><span class="ourpr">Our Price:</span> 
<?php
// base price
  if ($show_onetime_charges_description == 'true') {
    $one_time = '<span >' . TEXT_ONETIME_CHARGE_SYMBOL . TEXT_ONETIME_CHARGE_DESCRIPTION . '</span><br />';
  } else {
    $one_time = '';
  }
  echo $one_time . ((zen_has_product_attributes_values((int)$_GET['products_id']) and $flag_show_product_info_starting_at == 1) ? TEXT_BASE_PRICE : '') . zen_get_products_display_price((int)$_GET['products_id']);
?></h2>
<!--eof Product Price block -->
<br class="clearBoth" />
<br class="clearBoth" />
<!--bof Attributes Module -->
<?php
  if ($pr_attr->fields['total'] > 0) {
?>
<?php
/**
 * display the product atributes
 */
  require($template->get_template_dir('/tpl_modules_attributes.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_attributes.php'); ?>

<?php
  }
?>
<!--eof Attributes Module -->

<!--bof Add to Cart Box -->
<?php
if (CUSTOMERS_APPROVAL == 3 and TEXT_LOGIN_FOR_PRICE_BUTTON_REPLACE_SHOWROOM == '') {
  // do nothing
} else {
?>
            <?php
    $display_qty = (($flag_show_product_info_in_cart_qty == 1 and $_SESSION['cart']->in_cart($_GET['products_id'])) ? '<p>' . PRODUCTS_ORDER_QTY_TEXT_IN_CART . $_SESSION['cart']->get_quantity($_GET['products_id']) . '</p>' : '');
            if ($products_qty_box_status == 0 or $products_quantity_order_max== 1) {
              // hide the quantity box and default to 1
              $the_button = '<input type="hidden" name="cart_quantity" value="1" />' . zen_draw_hidden_field('products_id', (int)$_GET['products_id']) . zen_image_submit(BUTTON_IMAGE_IN_CART , BUTTON_IN_CART_ALT);
            } else {
              // show the quantity box
    $the_button = '<div class="back iewidth01ff qbg"><b>'.PRODUCTS_ORDER_QTY_TEXT . '</b><input class="roundin" style="padding:0px; margin:0px;" type="text" name="cart_quantity" value="' . (zen_get_buy_now_qty($_GET['products_id'])) . '" maxlength="6" size="4" /><br />' . zen_get_products_quantity_min_units_display((int)$_GET['products_id']) . '</div><div class="back iewidth01ff">' . zen_draw_hidden_field('products_id', (int)$_GET['products_id']) . '<span id="button_cartadded">'. zen_image_submit('add2.jpg' , BUTTON_IN_CART_ALT).'</span></div><div class="back" id="button_cart" style="padding-left:6px;"></div><br class="clearBoth" />';
            }
    $display_button = zen_get_buy_now_button($_GET['products_id'], $the_button);
  ?>
  <?php if ($display_qty != '' or $display_button != '') { ?>
	<div id="cartAdd">
	<?php echo $display_button . '&nbsp;<span id="load1"></span>';?>

	</div>
<?php } // display qty and button ?>
<?php } // CUSTOMERS_APPROVAL == 3 ?>
<!--eof Add to Cart Box-->

<!--bof Product description -->
<?php if ($products_description != '') { ?>
<div class="product-page-section">
    <div class="prodvid">Product Description:</div>
    <div id="productDescription" class="productGeneraloff">
    <?php echo stripslashes($products_description); ?></div>
</div>
<?php } ?>
<!--eof Product description -->
<div class="product-page-section" id="product-info-social">

<!-- AddThis Button BEGIN -->
<div class="addthis_toolbox addthis_default_style">
<a href="http://www.addthis.com/bookmark.php?v=250&amp;username=witteringsurf" class="addthis_button_compact">Share this product</a>
<span class="addthis_separator">|</span>
<a class="addthis_button_facebook"></a>
<a class="addthis_button_myspace"></a>
<a class="addthis_button_google"></a>
<a class="addthis_button_twitter"></a>
</div>
<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username=witteringsurf"></script>
<!-- AddThis Button END -->
</div>
<!--eof Product details list -->

<!--bof Tell a Friend button -->
<?php
  if ($flag_show_product_info_tell_a_friend == 1) { ?>
<div id="productTellFriendLink" style="text-align:right; padding-top:32px;" class="forward"><?php echo ($flag_show_product_info_tell_a_friend == 1 ? '<a href="' . zen_href_link(FILENAME_TELL_A_FRIEND, 'products_id=' . $_GET['products_id']) . '">' . zen_image_button('tell.gif', BUTTON_TELLAFRIEND_ALT) . '</B></a>' : ''); ?></div><br class="clearBoth" />
<?php
  }
?>
<!--eof Tell a Friend button -->

<!--bof Quantity Discounts table -->
<?php
  if ($products_discount_type != 0) { ?>
<?php
/**
 * display the products quantity discount
 */
 require($template->get_template_dir('/tpl_modules_products_quantity_discounts.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_products_quantity_discounts.php'); ?>
<?php
  }
?>
<!--eof Quantity Discounts table -->

<!--bof free ship icon  -->
<?php if(zen_get_product_is_always_free_shipping($products_id_current) && $flag_show_product_info_free_shipping) { ?>
<div id="freeShippingIcon"><?php echo TEXT_PRODUCT_FREE_SHIPPING_ICON; ?></div>
<?php } ?>
<!--eof free ship icon  -->
</div>

<div class="prodinfo01 back">

<br />
<!--bof Main Product Image -->
<?php
  if (zen_not_null($products_image)) {
  ?>
<?php
/**
 * display the main product image
 */
   require($template->get_template_dir('/tpl_modules_main_product_image.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_main_product_image.php'); ?>
<?php
  }
?>
<!--eof Main Product Image-->
<!--bof Additional Product Images -->
<?php
/**
 * display the products additional images
 */
  require($template->get_template_dir('/tpl_modules_additional_images.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_additional_images.php'); ?>
<!--eof Additional Product Images -->
<div style="clear:both"></div>
<div  style="width: 400px; overflow:hidden; float: left;">
<?php
  if ('mov_'.$products_id_current != '' && $banner = zen_banner_exists('dynamic', 'mov_'.$products_id_current)) {
    if ($banner->RecordCount() > 0) {
?>
<div>

<object width="400" height="325">
<?php echo zen_display_banner('static', $banner); ?></div>
</object>
<?php
    }
  }
?>
</div>
<div style="clear:both"></div>
</div>

<br class="clearBoth" />



<!--bof Prev/Next bottom position -->
<?php if (PRODUCT_INFO_PREVIOUS_NEXT == 2 or PRODUCT_INFO_PREVIOUS_NEXT == 3) { ?>
<?php
/**
 * display the product previous/next helper
 */
 require($template->get_template_dir('/tpl_products_next_previous.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_products_next_previous.php'); ?>
<?php } ?>
<!--eof Prev/Next bottom position -->



<!--bof Reviews button and count-->
<?php
  if ($flag_show_product_info_reviews == 1) {
    // if more than 0 reviews, then show reviews button; otherwise, show the "write review" button
    if ($reviews->fields['count'] > 0 ) { ?>
<div id="productReviewLink" class="buttonRow back"><?php echo '<a href="' . zen_href_link(FILENAME_PRODUCT_REVIEWS, zen_get_all_get_params()) . '">' . zen_image_button(BUTTON_IMAGE_REVIEWS, BUTTON_REVIEWS_ALT) . '</a>'; ?></div>
<br class="clearBoth" />
<p class="reviewCount"><?php echo ($flag_show_product_info_reviews_count == 1 ? TEXT_CURRENT_REVIEWS . ' ' . $reviews->fields['count'] : ''); ?></p>
<?php } else { ?>
<div id="productReviewLink" class="buttonRow back"><?php echo '<a href="' . zen_href_link(FILENAME_PRODUCT_REVIEWS_WRITE, zen_get_all_get_params(array())) . '">' . zen_image_button(BUTTON_IMAGE_WRITE_REVIEW, BUTTON_WRITE_REVIEW_ALT) . '</a>'; ?></div>
<br class="clearBoth" />
<?php
  }
}
?>
<!--eof Reviews button and count -->


<!--bof Product date added/available-->
<?php
  if ($products_date_available > date('Y-m-d H:i:s')) {
    if ($flag_show_product_info_date_available == 1) {
?>
  <p id="productDateAvailable" class="productGeneraloff centeredContent"><?php echo sprintf(TEXT_DATE_AVAILABLE, zen_date_long($products_date_available)); ?></p>
<?php
    }
  } else {
    if ($flag_show_product_info_date_added == 1) {
?>
      <p id="productDateAdded" class="productGeneraloff centeredContent"><?php echo sprintf(TEXT_DATE_ADDED, zen_date_long($products_date_added)); ?></p>
<?php
    } // $flag_show_product_info_date_added
  }
?>
<!--eof Product date added/available -->

<!--bof Product URL -->
<?php
  if (zen_not_null($products_url)) {
    if ($flag_show_product_info_url == 1) {
?>
    <p id="productInfoLink" class="productGeneraloff centeredContent"><?php echo sprintf(TEXT_MORE_INFORMATION, zen_href_link(FILENAME_REDIRECT, 'action=url&goto=' . urlencode($products_url), 'NONSSL', true, false)); ?></p>
<?php
    } // $flag_show_product_info_url
  }
?>
<!--eof Product URL -->
<br class="clearBoth" />
<!-- BOF: Cross-Sell information -->
<?php
// THIS CODE WOULD BE ADDED INTO YOUR TPL_PRODUCT_INFO_DISPLAY.PHP WHEREVER YOU WANT TO DISPLAY THE CROSS_SELL BOX:
  require($template->get_template_dir('tpl_modules_xsell_products.php', DIR_WS_TEMPLATE, $current_page_base,'templates'). '/' . 'tpl_modules_xsell_products.php');
?>
<!-- EOF: Cross-Sell information -->
<br class="clearBoth" />
<!--bof also purchased products module-->
<?php require($template->get_template_dir('tpl_modules_also_purchased_products.php', DIR_WS_TEMPLATE, $current_page_base,'templates'). '/' . 'tpl_modules_also_purchased_products.php');?>
<!--eof also purchased products module-->

<!--bof Form close-->
</form>
<!--bof Form close-->
</div>

<?php 
function get_estimated_delivery_date(){
    $day_today = date('N');
    $hour_today = date('G');
    
    //day 1,2,3,4 with hour < 12 = date+1
    //day 1,2,3,4 with hour > 12 = date+2
    //day 5 with hour < 12 = date+3
    //day 5 with hour > 12 = date+4
    //day 6 = date+3
    //day 7 = date+2
    $todayDate = date("d-m-Y");
    if ($day_today<=4 && $hour_today <12){
        $estimate = strtotime(date("d-m-Y", strtotime($todayDate)) . "+1 day");
    }elseif($day_today<=4 && $hour_today >12){
        $estimate = strtotime(date("d-m-Y", strtotime($todayDate)) . "+2 day");
    }elseif($day_today==5 && $hour_today < 12){
        $estimate = strtotime(date("d-m-Y", strtotime($todayDate)) . "+3 day");
    }elseif($day_today==5 && $hour_today > 12){
        $estimate = strtotime(date("d-m-Y", strtotime($todayDate)) . "+4 day");
    }elseif($day_today==6){
        $estimate = strtotime(date("d-m-Y", strtotime($todayDate)) . "+3 day");
    }elseif($day_today==7){
        $estimate = strtotime(date("d-m-Y", strtotime($todayDate)) . "+2 day");
    }
    
    return "UK Mainland delivery by ".date("D jS M", $estimate);
}

?>
