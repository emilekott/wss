<?php
/**
 * Specials
 *
 * @package page
 * @copyright Copyright 2003-2007 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: main_template_vars.php 6912 2007-09-02 02:23:45Z drbyte $
 */

if (MAX_DISPLAY_SPECIAL_PRODUCTS > 0 ) {
  $specials_query_raw = "SELECT p.products_id, p.products_image, p.products_quantity, pd.products_name,
                          p.master_categories_id
                         FROM (" . TABLE_PRODUCTS . " p
                         LEFT JOIN " . TABLE_SPECIALS . " s on p.products_id = s.products_id
                         LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd on p.products_id = pd.products_id )
                         WHERE p.products_id = s.products_id and p.products_id = pd.products_id and p.products_status = '1'
                         AND s.status = 1
                         AND pd.language_id = :languagesID
                         ORDER BY s.specials_date_added DESC";

  $specials_query_raw = $db->bindVars($specials_query_raw, ':languagesID', $_SESSION['languages_id'], 'integer');
  $specials_split = new splitPageResults($specials_query_raw, MAX_DISPLAY_SPECIAL_PRODUCTS);
  $specials = $db->Execute($specials_split->sql_query);
  $row = 0;
  $col = 0;
  $list_box_contents = array();
  $title = '';

  $num_products_count = $specials->RecordCount();
  if ($num_products_count) {
    if ($num_products_count < SHOW_PRODUCT_INFO_COLUMNS_SPECIALS_PRODUCTS || SHOW_PRODUCT_INFO_COLUMNS_SPECIALS_PRODUCTS==0 ) {
      $col_width = floor(100/$num_products_count);
    } else {
      $col_width = floor(100/SHOW_PRODUCT_INFO_COLUMNS_SPECIALS_PRODUCTS);
    }

    $list_box_contents = array();
    while (!$specials->EOF) {

	    $products_price = zen_get_products_display_price($specials->fields['products_id']);
	     if($specials->fields['products_quantity'] == 0) {
	    $buttonlist = '<img src="'.  DIR_WS_TEMPLATES . $template_dir .'/images/design/sold.jpg" border="0" alt="" />';
	    }
	    else {
	    $buttonlist = '<a href="' . zen_href_link($_GET['main_page'], zen_get_all_get_params(array('action')) . 'action=buy_now&products_id=' . $specials->fields['products_id']) . '"><img src="'.  DIR_WS_TEMPLATES . $template_dir .'/images/design/add.jpg" border="0" alt="" /></a>';
	    }
      $specials->fields['products_name'] = zen_get_products_name($specials->fields['products_id']);
      $list_box_contents[$row][$col] = array('params' => 'class="specialsListBoxContents"' . ' ' . 'style="width:' . $col_width . '%;"',
	      'text' => '
	    <div class="prod_table">
	    <div class="prod_image"><a href="' . zen_href_link(zen_get_info_page($specials->fields['products_id']), 'products_id=' . $specials->fields['products_id']) . '">' . zen_image(DIR_WS_IMAGES . $specials->fields['products_image'], $specials->fields['products_name'], IMAGE_PRODUCT_NEW_WIDTH, IMAGE_PRODUCT_NEW_HEIGHT) . '</a></div>
	    <div class="prod_name"><a class="prod_namelink" href="' . zen_href_link(zen_get_info_page($specials->fields['products_id']), 'products_id=' . $specials->fields['products_id']) . '">' . $specials->fields['products_name'] . '</a></div>
	    <div class="prod_price">'. zen_get_products_display_price($specials->fields['products_id']) .'</div>
	    </div>
');
      $col ++;
      if ($col > (SHOW_PRODUCT_INFO_COLUMNS_SPECIALS_PRODUCTS - 1)) {
        $col = 0;
        $row ++;
      }
      $specials->MoveNext();
    }
    require($template->get_template_dir('tpl_specials_default.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_specials_default.php');
  }
}
?>
