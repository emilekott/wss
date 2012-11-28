<?php
/**
 * new_products.php module
 *
 * @package modules
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: new_products.php 4629 2006-09-28 15:29:18Z ajeh $
 */
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}
// display limits
//$display_limit = zen_get_products_new_timelimit();
      //$display_limit = zen_get_new_date_range();
$display_limit = '';
if ( (!isset($new_products_category_id)) || ($new_products_category_id == '0') ) {
  $new_products_query = "select p.products_id, p.products_image, p.products_quantity, p.products_tax_class_id, p.products_price, p.products_date_added
                           from " . TABLE_PRODUCTS . " p
                           where p.products_status = 1 order by rand() " . $display_limit;
} else {
  $new_products_query = "select distinct p.products_id, p.products_image, p.products_quantity, p.products_tax_class_id, p.products_date_added,
                                           p.products_price
                           from " . TABLE_PRODUCTS . " p
                           left join " . TABLE_SPECIALS . " s
                           on p.products_id = s.products_id, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " .
  TABLE_CATEGORIES . " c
                           where p.products_id = p2c.products_id
                           and p2c.categories_id = c.categories_id
                           and c.parent_id = '" . (int)$new_products_category_id . "'
                           and p.products_status = 1 ORDER BY p.products_date_added DESC " . $display_limit;
}


$new_products = $db->Execute($new_products_query, MAX_DISPLAY_NEW_PRODUCTS);

$row = 0;
$col = 0;
$list_box_contents = array();
$title = '';

$num_products_count = $new_products->RecordCount();

// show only when 1 or more
if ($num_products_count > 0) {
  if ($num_products_count < SHOW_PRODUCT_INFO_COLUMNS_NEW_PRODUCTS || SHOW_PRODUCT_INFO_COLUMNS_NEW_PRODUCTS == 0 ) {
    $col_width = floor(100/$num_products_count)-0.5;
  } else {
    $col_width = floor(100/SHOW_PRODUCT_INFO_COLUMNS_NEW_PRODUCTS)-0.5;
  }

  while (!$new_products->EOF) {

    $products_price = zen_get_products_display_price($new_products->fields['products_id']);
$new_products->fields['products_name'] = zen_get_products_name($new_products->fields['products_id']);
    if($new_products->fields['products_quantity'] == 0) {
	    $buttonlist = '<img src="'.  DIR_WS_TEMPLATES . $template_dir .'/images/design/sold.jpg" border="0" alt="" />';
	    }
	    else {
	    $buttonlist = '<a href="' . zen_href_link($_GET['main_page'], zen_get_all_get_params(array('action')) . 'action=buy_now&products_id=' . $new_products->fields['products_id']) . '"><img src="'.  DIR_WS_TEMPLATES . $template_dir .'/images/design/add.jpg" border="0" alt="" /></a>';
	    }

    $list_box_contents[$row][$col] = array('params' => 'class="centerBoxContentsNew centeredContent back"' . ' ' . 'style="width:' . $col_width . '%;"',
	    'text' => '
	    <div class="prod_table">
	    <div class="prod_image"><a href="' . zen_href_link(zen_get_info_page($new_products->fields['products_id']), 'products_id=' . $new_products->fields['products_id']) . '">' . zen_image(DIR_WS_IMAGES . $new_products->fields['products_image'], $new_products->fields['products_name'], IMAGE_PRODUCT_NEW_WIDTH, IMAGE_PRODUCT_NEW_HEIGHT) . '</a></div>
	    <div class="prod_name"><a class="prod_namelink" href="' . zen_href_link(zen_get_info_page($new_products->fields['products_id']), 'products_id=' . $new_products->fields['products_id']) . '">' . $new_products->fields['products_name'] . '</a></div>
	    <div class="prod_price">'. zen_get_products_display_price($new_products->fields['products_id']) .'</div>
	    </div>
	    ');

    $col ++;
    if ($col > (SHOW_PRODUCT_INFO_COLUMNS_NEW_PRODUCTS - 1)) {
      $col = 0;
      $row ++;
    }
    $new_products->MoveNext();
  }

  if ($new_products->RecordCount() > 0) {
    if (isset($new_products_category_id) && $new_products_category_id != 0) {
      $category_title = zen_get_categories_name((int)$new_products_category_id);
      $title = '<h2 class="centerBoxHeading">' . sprintf(TABLE_HEADING_NEW_PRODUCTS, strftime('%B')) . ($category_title != '' ? ' - ' . $category_title : '' ) . '</h2>';
    } else {
      $title = '<h2 class="centerBoxHeading">' . sprintf(TABLE_HEADING_NEW_PRODUCTS, strftime('%B')) . '</h2>';
    }
    $zc_show_new_products = true;
  }
}
?>
