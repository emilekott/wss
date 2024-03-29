<?php
/**
 * shopping_cart sidebox - displays contents of customer's shopping cart.  Also shows GV balance, if any.
 *
 * @package templateSystem
 * @copyright Copyright 2003-2005 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: shopping_cart.php 3505 2006-04-24 04:00:05Z drbyte $
 */

  switch (true) {
    case (SHOW_SHOPPING_CART_BOX_STATUS == '0'):
      $show_shopping_cart_box = true;
      break;
    case (SHOW_SHOPPING_CART_BOX_STATUS == '1'):
      if ($_SESSION['cart']->count_contents() > 0 || (isset($_SESSION['customer_id']) && zen_user_has_gv_account($_SESSION['customer_id']) > 0)) {
        $show_shopping_cart_box = true;
      } else {
        $show_shopping_cart_box = false;
      }
      break;
    case (SHOW_SHOPPING_CART_BOX_STATUS == '2'):
      if ( ( ($_SESSION['cart']->count_contents() > 0) || (isset($_SESSION['customer_id']) && zen_user_has_gv_account($_SESSION['customer_id']) > 0) ) && ($_GET['main_page'] != FILENAME_SHOPPING_CART) ) {
        $show_shopping_cart_box = true;
      } else {
        $show_shopping_cart_box = false;
      }
      break;
    }


  if ($show_shopping_cart_box == true) {
    require($template->get_template_dir('tpl_shopping_cart.php',DIR_WS_TEMPLATE, $current_page_base,'sideboxes'). '/tpl_shopping_cart.php');
    $title =  BOX_HEADING_SHOPPING_CART;
    $title_link = false;
    $title_link = FILENAME_SHOPPING_CART;

    require($template->get_template_dir('tpl_box_header.php',DIR_WS_TEMPLATE, $current_page_base,'common'). '/tpl_box_header.php');
  }
?>
