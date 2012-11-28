<?php
/**
 * Side Box Template
 *
 * @package templateSystem
 * @copyright Copyright 2003-2005 Zen Cartt Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_shopping_cart.php 3056 2006-02-21 06:41:36Z birdbrain $
 */
  $content ="";
  
  $content .= '';
//    $products = $_SESSION['cart']->get_products();
//    for ($i=0, $n=sizeof($products); $i<$n; $i++) {
//      $content .= '';

//      if (($_SESSION['new_products_id_in_cart']) && ($_SESSION['new_products_id_in_cart'] == $products[$i]['id'])) {
//        $content .= '<span class="cartNewItem">';
//      } else {
//        $content .= '<span class="cartOldItem">';
//      }

//      $content .= $products[$i]['quantity'] . BOX_SHOPPING_CART_DIVIDER . '</span><a href="' . zen_href_link(zen_get_info_page($products[$i]['id']), 'products_id=' . $products[$i]['id']) . '">';

//      if (($_SESSION['new_products_id_in_cart']) && ($_SESSION['new_products_id_in_cart'] == $products[$i]['id'])) {
//        $content .= '<span class="cartNewItem">';
//      } else {
//        $content .= '<span class="cartOldItem">';
//      }

//      $content .= $products[$i]['name'] . '</span></a>' . "";

//      if (($_SESSION['new_products_id_in_cart']) && ($_SESSION['new_products_id_in_cart'] == $products[$i]['id'])) {
//        $_SESSION['new_products_id_in_cart'] = '';
//      }
//    }
$content .= '<span class="color01">'. $_SESSION['cart']->count_contents() .'</span>&nbsp;Items&nbsp;<span class="color01">'. $currencies->format($_SESSION['cart']->show_total()) .'</span>&nbsp;&nbsp;<a class="sclink" href="' . zen_href_link(FILENAME_SHOPPING_CART, '', 'NONSSL') . '">View Basket</a>&nbsp;|&nbsp;<a class="sclink" href="' . zen_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'NONSSL') . '">Checkout Now</a>';

//  if ($_SESSION['cart']->count_contents() > 0) {
//    $content .= '<hr>';
//    $content .= '' . $currencies->format($_SESSION['cart']->show_total()) . '';
//    $content .= '<br>';
//  }

//  if (isset($_SESSION['customer_id'])) {
//    $gv_query = "select amount
//                 from " . TABLE_COUPON_GV_CUSTOMER . "
//                 where customer_id = '" . $_SESSION['customer_id'] . "'";
//   $gv_result = $db->Execute($gv_query);

//    if ($gv_result->fields['amount'] > 0 ) {
//      $content .= '<a href="' . zen_href_link(FILENAME_GV_SEND, '', 'SSL') . '">' . zen_image_button(BUTTON_IMAGE_SEND_A_GIFT_CERT , BUTTON_SEND_A_GIFT_CERT_ALT) . '</a>'; 
//      $content .= '' . VOUCHER_BALANCE . $currencies->format($gv_result->fields['amount']) . '';
//    }
//  $content .= '';
//  }
?>

