<?php
/**
 * featured_products module - prepares content for display
 *
 * @package modules
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: featured_products.php 4629 2006-09-28 15:29:18Z ajeh $
 */
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}
if ( (!isset($featured_products_category_id)) || ($featured_products_category_id == '0') ) {
  $featured_products_query = "select distinct p.products_id, p.products_image, p.products_quantity, pd.products_name
                           from (" . TABLE_PRODUCTS . " p
                           left join " . TABLE_FEATURED . " f on p.products_id = f.products_id
                           left join " . TABLE_PRODUCTS_DESCRIPTION . " pd on p.products_id = pd.products_id )
                           where p.products_id = f.products_id and p.products_id = pd.products_id and p.products_status = 1 and f.status = 1 and pd.language_id = '" . (int)$_SESSION['languages_id'] . "'";
} else {
  $featured_products_query = "select distinct p.products_id, p.products_image, p.products_quantity, pd.products_name
                           from (" . TABLE_PRODUCTS . " p
                           left join " . TABLE_FEATURED . " f on p.products_id = f.products_id
                           left join " . TABLE_PRODUCTS_DESCRIPTION . " pd on p.products_id = pd.products_id), " .
  TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " .
  TABLE_CATEGORIES . " c
                           where p.products_id = p2c.products_id
                           and p2c.categories_id = c.categories_id
                           and c.parent_id = '" . (int)$featured_products_category_id . "'
                           and p.products_id = f.products_id and p.products_id = pd.products_id and p.products_status = 1 and f.status = 1 and pd.language_id = '" . (int)$_SESSION['languages_id'] . "'";

}
$featured_products = $db->ExecuteRandomMulti($featured_products_query, MAX_DISPLAY_SEARCH_RESULTS_FEATURED);

$row = 0;
$col = 0;
$list_box_contents = array();
$title = '';

$num_products_count = $featured_products->RecordCount();

// show only when 1 or more
if ($num_products_count > 0) {
  if ($num_products_count < SHOW_PRODUCT_INFO_COLUMNS_FEATURED_PRODUCTS || SHOW_PRODUCT_INFO_COLUMNS_FEATURED_PRODUCTS == 0) {
    $col_width = floor(100/$num_products_count);
  } else {
    $col_width = floor(100/SHOW_PRODUCT_INFO_COLUMNS_FEATURED_PRODUCTS);
  }
  while (!$featured_products->EOF) {

    $products_price = zen_get_products_display_price($featured_products->fields['products_id']);
 if($featured_products->fields['products_quantity'] == 0) {
	    $buttonlist = '<img src="'.  DIR_WS_TEMPLATES . $template_dir .'/images/design/sold.jpg" border="0" alt="" />';
	    }
	    else {
	    $buttonlist = '<a href="' . zen_href_link($_GET['main_page'], zen_get_all_get_params(array('action')) . 'action=buy_now&products_id=' . $featured_products->fields['products_id']) . '"><img src="'.  DIR_WS_TEMPLATES . $template_dir .'/images/design/add.jpg" border="0" alt="" /></a>';
	    }
    $list_box_contents[$row][$col] = array('params' =>'class="centerBoxContentsFeatured centeredContent back"' . ' ' . 'style="width:' . $col_width . '%;"',
	    'text' => '
	    <div class="prod_table">
	    <div class="prod_image"><a href="' . zen_href_link(zen_get_info_page($featured_products->fields['products_id']), 'products_id=' . $featured_products->fields['products_id']) . '">' . zen_image(DIR_WS_IMAGES . $featured_products->fields['products_image'], $featured_products->fields['products_name'], IMAGE_FEATURED_PRODUCTS_LISTING_WIDTH, IMAGE_FEATURED_PRODUCTS_LISTING_HEIGHT) . '</a></div>
	    <div class="prod_name"><a class="prod_namelink" href="' . zen_href_link(zen_get_info_page($featured_products->fields['products_id']), 'products_id=' . $featured_products->fields['products_id']) . '">' . $featured_products->fields['products_name'] . '</a></div>
	    <div class="prod_price">'. zen_get_products_display_price($featured_products->fields['products_id']) .'</div>
	    </div>
	    ');

    $col ++;
    if ($col > (SHOW_PRODUCT_INFO_COLUMNS_FEATURED_PRODUCTS - 1)) {
      $col = 0;
      $row ++;
    }
    $featured_products->MoveNextRandom();
  }

  if ($featured_products->RecordCount() > 0) {
    if (isset($featured_products_category_id) && $featured_products_category_id !=0) {
      $category_title = zen_get_categories_name((int)$featured_products_category_id);
      $title = '<h2 class="centerBoxHeading">' . TABLE_HEADING_FEATURED_PRODUCTS . ($category_title != '' ? ' - ' . $category_title : '') . '</h2>';
    } else {
      $title = '<h2 class="centerBoxHeading">' . TABLE_HEADING_FEATURED_PRODUCTS . '</h2>';
    }
    $zc_show_featured = true;
  }
}
?>
