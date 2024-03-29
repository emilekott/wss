<?php
/**
 * Page Template
 *
 * Loaded automatically by index.php?main_page=shopping_cart.<br />
 * Displays shopping-cart contents
 *
 * @package templateSystem
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_shopping_cart_default.php 5414 2006-12-27 07:51:03Z drbyte $
 */
?>
<div class="centerColumn" id="shoppingCartDefault">
<?php
  if ($flagHasCartContents) {
?>

<h1 id="cartDefaultHeading"><?php echo HEADING_TITLE; ?></h1>

<?php if ($messageStack->size('shopping_cart') > 0) echo $messageStack->output('shopping_cart'); ?>

<?php echo zen_draw_form('cart_quantity', zen_href_link(FILENAME_SHOPPING_CART, 'action=update_product')); ?>


<?php  if ($flagAnyOutOfStock) { ?>

<?php    if (STOCK_ALLOW_CHECKOUT == 'true') {  ?>

<div class="messageStackError"><?php echo OUT_OF_STOCK_CAN_CHECKOUT; ?></div>

<?php    } else { ?>
<div class="messageStackError"><?php echo OUT_OF_STOCK_CANT_CHECKOUT; ?></div>

<?php    } //endif STOCK_ALLOW_CHECKOUT ?>
<?php  } //endif flagAnyOutOfStock ?>

<table  border="0" width="100%" cellspacing="0" cellpadding="0" id="cartContentsDisplay">
     <tr class="tableHeading">
        <th scope="col" id="scQuantityHeading"><?php echo TABLE_HEADING_QUANTITY; ?></th>
        <th scope="col" id="scUpdateQuantity">Image</th>
        <th scope="col" id="scProductsHeading"><?php echo TABLE_HEADING_PRODUCTS; ?></th>
        <th scope="col" id="scUnitHeading"><?php echo TABLE_HEADING_PRICE; ?></th>
        <th scope="col" id="scTotalHeading"><?php echo TABLE_HEADING_TOTAL; ?></th>
        <th scope="col" id="scRemoveHeading">Remove</th>
     </tr>
         <!-- Loop through all products /-->
<?php
  foreach ($productArray as $product) {
?>
     <tr class="<?php echo $product['rowClass']; ?>">
       <td class="cartQuantity">
<?php
  echo zen_draw_hidden_field('products_id[]', $product['id']);
  if ($product['flagShowFixedQuantity']) {
    echo $product['showFixedQuantityAmount'] . '<br /><span class="alert bold">' . $product['flagStockCheck'] . '</span><br /><br />' . $product['showMinUnits'];
  } else {
    echo $product['quantityField'] . '<br /><span class="alert bold">' . $product['flagStockCheck'] . '</span><br /><br />' . $product['showMinUnits'];
  }
?>
       </td>
       <td class="cartProductImage">
	   <div id="cartImage"><a href="<?php echo $product['linkProductsName']; ?>"><?php echo $product['productsImage']; ?></a></div>
       </td>
       <td class="cartProductName">
		<div id="cartProdTitle"><a href="<?php echo $product['linkProductsName']; ?>"><?php echo $product['productsName'] . '<span class="alert bold">' . $product['flagStockCheck'] . '</span>'; ?></a></div>
<br class="clearBoth" />
<?php
  echo $product['attributeHiddenField'];
  if (isset($product['attributes']) && is_array($product['attributes'])) {
  echo '<div class="cartAttribsList">';
  echo '<ul>';
    reset($product['attributes']);
    foreach ($product['attributes'] as $option => $value) {
?>

<li><?php echo $value['products_options_name'] . TEXT_OPTION_DIVIDER . nl2br($value['products_options_values_name']); ?></li>

<?php
    }
  echo '</ul>';
  echo '</div>';
  }
?>
       </td>
       <td class="cartUnitDisplay"><?php echo $product['productsPriceEach']; ?></td>
       <td class="cartTotalDisplay"><?php echo $product['productsPrice']; ?></td>
       <td class="cartRemoveItemDisplay">
<?php
  if ($product['checkBoxDelete'] ) {
    echo zen_draw_checkbox_field('cart_delete[]', $product['id']);
  }
?>
</td>
</tr>

<?php
  } // end foreach ($productArray as $product)
?>
</table>
<!-- Finished loop through all products /-->
     
<br class="clearBoth" />
<div id="cartSubTotal"><?php echo SUB_TITLE_SUB_TOTAL; ?> <?php echo $cartShowTotal; ?></div>
<br class="clearBoth" />
<div id="checkoutButtons">
<!--bof shopping cart buttons-->
<BR><BR>
<div class="back">	 	
		<?php
		// show update cart button
		  if (SHOW_SHOPPING_CART_UPDATE == 2 or SHOW_SHOPPING_CART_UPDATE == 3) {
			echo zen_image_submit(ICON_IMAGE_UPDATE, ICON_UPDATE_ALT);
		  } 
		?>
</div>

<?php if (SHOW_SHIPPING_ESTIMATOR_BUTTON == '1'){ ?>
<div class="back scbutton01"><?php echo '<a href="javascript:popupWindow(\'' . zen_href_link(FILENAME_POPUP_SHIPPING_ESTIMATOR) . '\')">' .
 zen_image_button(BUTTON_IMAGE_SHIPPING_ESTIMATOR, BUTTON_SHIPPING_ESTIMATOR_ALT) . '</a>'; ?></div>
<? } ?>
<div class="forward scbutton01"><?php echo '<a href="' . zen_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL') . '">' . zen_image_button(BUTTON_IMAGE_CHECKOUT, BUTTON_CHECKOUT_ALT) . '</a>'; ?></div>
<div class="clearBoth"></div>
</div>

<!--eof shopping cart buttons-->
</form>

<br class="clearBoth" />
<?php if (SHOW_SHIPPING_ESTIMATOR_BUTTON == '2'){
/**
 * load the shipping estimator code if needed
 */
	require(DIR_WS_MODULES . zen_get_module_directory('shipping_estimator.php'));

   }

  }
// end   if ($flagHasCartContents) 
  else {
?>

<h2 id="cartEmptyText"><?php echo TEXT_CART_EMPTY; ?></h2>

<?php
$show_display_shopping_cart_empty = $db->Execute(SQL_SHOW_SHOPPING_CART_EMPTY);

while (!$show_display_shopping_cart_empty->EOF) {
?>

<?php
  if ($show_display_shopping_cart_empty->fields['configuration_key'] == 'SHOW_SHOPPING_CART_EMPTY_FEATURED_PRODUCTS') { ?>
<?php
/**
 * display the Featured Products Center Box
 */
?>
<?php require($template->get_template_dir('tpl_modules_featured_products.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_featured_products.php'); ?>
<?php } ?>

<?php
  if ($show_display_shopping_cart_empty->fields['configuration_key'] == 'SHOW_SHOPPING_CART_EMPTY_SPECIALS_PRODUCTS') { ?>
<?php
/**
 * display the Special Products Center Box
 */
?>
<?php require($template->get_template_dir('tpl_modules_specials_default.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_specials_default.php'); ?>
<?php } ?>

<?php
  if ($show_display_shopping_cart_empty->fields['configuration_key'] == 'SHOW_SHOPPING_CART_EMPTY_NEW_PRODUCTS') { ?>
<?php
/**
 * display the New Products Center Box
 */
?>
<?php require($template->get_template_dir('tpl_modules_whats_new.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_whats_new.php'); ?>
<?php } ?>

<?php
  if ($show_display_shopping_cart_empty->fields['configuration_key'] == 'SHOW_SHOPPING_CART_EMPTY_UPCOMING') {
    include(DIR_WS_MODULES . zen_get_module_directory(FILENAME_UPCOMING_PRODUCTS));
  }
?>
<?php
  $show_display_shopping_cart_empty->MoveNext();
} // !EOF
?>

<?php
  }
?>
<!-- ** BEGIN PAYPAL EXPRESS CHECKOUT ** -->
  <?php  // only display EC option if cart contents >0 and value >0
if (defined('MODULE_PAYMENT_PAYPALWPP_STATUS') && MODULE_PAYMENT_PAYPALWPP_STATUS == 'True' && $_SESSION['cart']->count_contents() > 0 && $_SESSION['cart']->total > 0) {
  include(DIR_FS_CATALOG . DIR_WS_MODULES . 'payment/paypal/tpl_ec_button.php');
}
?>
<!-- ** END PAYPAL EXPRESS CHECKOUT ** -->

</div>
