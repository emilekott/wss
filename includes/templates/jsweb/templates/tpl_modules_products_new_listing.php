<?php
/**
 * Module Template
 *
 * Loaded automatically by index.php?main_page=products_new.<br />
 * Displays listing of New Products
 *
 * @package templateSystem
 * @copyright Copyright 2003-2005 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_modules_products_new_listing.php 2951 2006-02-03 07:02:51Z birdbrain $
 */
?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
          <tr>
            <td colspan="3"></td>
          </tr>
<?php
  $group_id = zen_get_configuration_key_value('PRODUCT_NEW_LIST_GROUP_ID');

  if ($products_new_split->number_of_rows > 0) {
    $products_new = $db->Execute($products_new_split->sql_query);
    $i='0';
    while (!$products_new->EOF) {

      if (PRODUCT_NEW_LIST_IMAGE != '0') {
        $display_products_image = '<a href="' . zen_href_link(zen_get_info_page($products_new->fields['products_id']), 'products_id=' . $products_new->fields['products_id']) . '">' . zen_image(DIR_WS_IMAGES . $products_new->fields['products_image'], $products_new->fields['products_name'], IMAGE_PRODUCT_NEW_LISTING_WIDTH, IMAGE_PRODUCT_NEW_LISTING_HEIGHT) . '</a>' . str_repeat('<br clear="all" />', substr(PRODUCT_NEW_LIST_IMAGE, 3, 1));
      } else {
        $display_products_image = '';
      }

      if (PRODUCT_NEW_LIST_NAME != '0') {
        $display_products_name = '<a href="' . zen_href_link(zen_get_info_page($products_new->fields['products_id']), 'products_id=' . $products_new->fields['products_id']) . '"><strong>' . $products_new->fields['products_name'] . '</strong></a>' . str_repeat('<br clear="all" />', substr(PRODUCT_NEW_LIST_NAME, 3, 1));
      } else {
        $display_products_name = '';
      }

      if (PRODUCT_NEW_LIST_MODEL != '0' and zen_get_show_product_switch($products_new->fields['products_id'], 'model')) {
        $display_products_model = TEXT_PRODUCTS_MODEL . $products_new->fields['products_model'] . str_repeat('<br clear="all" />', substr(PRODUCT_NEW_LIST_MODEL, 3, 1));
      } else {
        $display_products_model = '';
      }

      if (PRODUCT_NEW_LIST_WEIGHT != '0' and zen_get_show_product_switch($products_new->fields['products_id'], 'weight')) {
        $display_products_weight = '<br />' . TEXT_PRODUCTS_WEIGHT . $products_new->fields['products_weight'] . TEXT_SHIPPING_WEIGHT . str_repeat('<br clear="all" />', substr(PRODUCT_NEW_LIST_WEIGHT, 3, 1));
      } else {
        $display_products_weight = '';
      }

      if (PRODUCT_NEW_LIST_QUANTITY != '0' and zen_get_show_product_switch($products_new->fields['products_id'], 'quantity')) {
        if ($products_new->fields['products_quantity'] <= 0) {
          $display_products_quantity = TEXT_OUT_OF_STOCK . str_repeat('<br clear="all" />', substr(PRODUCT_NEW_LIST_QUANTITY, 3, 1));
        } else {
          $display_products_quantity = TEXT_PRODUCTS_QUANTITY . $products_new->fields['products_quantity'] . str_repeat('<br clear="all" />', substr(PRODUCT_NEW_LIST_QUANTITY, 3, 1));
        }
      } else {
        $display_products_quantity = '';
      }

      if (PRODUCT_NEW_LIST_DATE_ADDED != '0' and zen_get_show_product_switch($products_new->fields['products_id'], 'date_added')) {
        $display_products_date_added = TEXT_DATE_ADDED . ' ' . zen_date_long($products_new->fields['products_date_added']) . str_repeat('<br clear="all" />', substr(PRODUCT_NEW_LIST_DATE_ADDED, 3, 1));
      } else {
        $display_products_date_added = '';
      }

      if (PRODUCT_NEW_LIST_MANUFACTURER != '0' and zen_get_show_product_switch($products_new->fields['products_id'], 'manufacturer')) {
        $display_products_manufacturers_name = ($products_new->fields['manufacturers_name'] != '' ? TEXT_MANUFACTURER . ' ' . $products_new->fields['manufacturers_name'] . str_repeat('<br clear="all" />', substr(PRODUCT_NEW_LIST_MANUFACTURER, 3, 1)) : '');
      } else {
        $display_products_manufacturers_name = '';
      }

      if ((PRODUCT_NEW_LIST_PRICE != '0' and zen_get_products_allow_add_to_cart($products_new->fields['products_id']) == 'Y') and zen_check_show_prices() == true) {
        $products_price = zen_get_products_display_price($products_new->fields['products_id']);
        $display_products_price = TEXT_PRICE . ' ' . $products_price . str_repeat('<br clear="all" />', substr(PRODUCT_NEW_LIST_PRICE, 3, 1)) . (zen_get_show_product_switch($products_new->fields['products_id'], 'ALWAYS_FREE_SHIPPING_IMAGE_SWITCH') ? (zen_get_product_is_always_free_shipping($products_new->fields['products_id']) ? TEXT_PRODUCT_FREE_SHIPPING_ICON . '<br />' : '') : '');
      } else {
        $display_products_price = '';
      }

// more info in place of buy now
      if (PRODUCT_NEW_BUY_NOW != '0' and zen_get_products_allow_add_to_cart($products_new->fields['products_id']) == 'Y') {
        if (zen_has_product_attributes($products_new->fields['products_id'])) {
          $link = '<a href="' . zen_href_link(zen_get_info_page($products_new->fields['products_id']), 'products_id=' . $products_new->fields['products_id']) . '">' . MORE_INFO_TEXT . '</a>';
        } else {
//          $link= '<a href="' . zen_href_link(FILENAME_PRODUCTS_NEW, zen_get_all_get_params(array('action')) . 'action=buy_now&products_id=' . $products_new->fields['products_id']) . '">' . zen_image_button(BUTTON_IMAGE_IN_CART, BUTTON_IN_CART_ALT) . '</a>';
          if (PRODUCT_NEW_LISTING_MULTIPLE_ADD_TO_CART > 0) {
//            $how_many++;
            $link = TEXT_PRODUCT_NEW_LISTING_MULTIPLE_ADD_TO_CART . "<input type=\"text\" name=\"products_id[" . $products_new->fields['products_id'] . "]\" value=\"0\" size=\"4\" />";
          } else {
            $link = '<a href="' . zen_href_link(FILENAME_PRODUCTS_NEW, zen_get_all_get_params(array('action')) . 'action=buy_now&products_id=' . $products_new->fields['products_id']) . '">' . zen_image_button(BUTTON_IMAGE_BUY_NOW, BUTTON_BUY_NOW_ALT) . '</a>&nbsp;';
          }
        }

        $the_button = $link;
        $products_link = '<a href="' . zen_href_link(zen_get_info_page($products_new->fields['products_id']), 'products_id=' . $products_new->fields['products_id']) . '">' . MORE_INFO_TEXT . '</a>';
        $display_products_button = zen_get_buy_now_button($products_new->fields['products_id'], $the_button, $products_link) . '<br />' . zen_get_products_quantity_min_units_display($products_new->fields['products_id']) . str_repeat('<br clear="all" />', substr(PRODUCT_NEW_BUY_NOW, 3, 1));
      } else {
        $link = '<a href="' . zen_href_link(zen_get_info_page($products_new->fields['products_id']), 'products_id=' . $products_new->fields['products_id']) . '">' . MORE_INFO_TEXT . '</a>';
        $the_button = $link;
        $products_link = '<a href="' . zen_href_link(zen_get_info_page($products_new->fields['products_id']), 'products_id=' . $products_new->fields['products_id']) . '">' . MORE_INFO_TEXT . '</a>';
        $display_products_button = zen_get_buy_now_button($products_new->fields['products_id'], $the_button, $products_link) . '<br />' . zen_get_products_quantity_min_units_display($products_new->fields['products_id']) . str_repeat('<br clear="all" />', substr(PRODUCT_NEW_BUY_NOW, 3, 1));
      }

      if (PRODUCT_NEW_LIST_DESCRIPTION != '0') {
        $disp_text = zen_get_products_description($products_new->fields['products_id']);
        $disp_text = zen_clean_html($disp_text);

        $display_products_description = stripslashes(zen_trunc_string($disp_text, 150, '<a href="' . zen_href_link(zen_get_info_page($products_new->fields['products_id']), 'products_id=' . $products_new->fields['products_id']) . '"> ' . MORE_INFO_TEXT . '</a>'));
      } else {
        $display_products_description = '';
      }


if($i=='0')
{
?>
<TR>
<?
}
?>
	    <?php
if($products_new->fields['products_quantity'] == 0) {
	    $buttonlist = '<img src="'.  DIR_WS_TEMPLATES . $template_dir .'/images/design/sold.jpg" border="0" alt="" />';
	    }
	    else {
	    $buttonlist = '<a href="' . zen_href_link($_GET['main_page'], zen_get_all_get_params(array('action')) . 'action=buy_now&products_id=' . $products_new->fields['products_id']) . '"><img src="'.  DIR_WS_TEMPLATES . $template_dir .'/images/design/add.jpg" border="0" alt="" /></a>';
	    }
echo $tabela = '<td align="center">
	    <div class="prod_table">
	    <div class="prod_image"><a href="' . zen_href_link(zen_get_info_page($products_new->fields['products_id']), 'products_id=' . $products_new->fields['products_id']) . '">' . zen_image(DIR_WS_IMAGES . $products_new->fields['products_image'], $products_new->fields['products_name'], IMAGE_PRODUCT_NEW_WIDTH, IMAGE_PRODUCT_NEW_HEIGHT) . '</a></div>
	    <div class="prod_name"><a class="prod_namelink" href="' . zen_href_link(zen_get_info_page($products_new->fields['products_id']), 'products_id=' . $products_new->fields['products_id']) . '">' . $products_new->fields['products_name'] . '</a></div>
	    <div class="prod_price">'. zen_get_products_display_price($products_new->fields['products_id']) .'</div>
	    </div>
</td>';
//                    echo $display_products_image;
//                    echo $display_products_quantity;
//                    echo $display_products_button;
//                    echo $display_products_name;
//                    echo $display_products_model;
//                    echo $display_products_manufacturers_name;
//                    echo $display_products_price;
//                    echo $display_products_weight;
//                    echo $display_products_date_added;
$i++;
              ?>

<?
if($i=='4')
{
?>
</TR>
<?
$i='0';
}
?>
<?php
      $products_new->MoveNext();
    }
if($i=='1' or $i=='2' or $i=='3' )
{
?>
</TR>
<?
}
  } else {
?>
          <tr>
            <td class="main" colspan="2"><?php echo TEXT_NO_NEW_PRODUCTS; ?></td>
          </tr>
<?php
  }
?>
</table>
