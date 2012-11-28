<?php
/**
 * Module Template
 *
 * @package templateSystem
 * @copyright Copyright 2003-2005 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_modules_product_listing.php 3241 2006-03-22 04:27:27Z ajeh $
 * UPDATED TO WORK WITH COLUMNAR PRODUCT LISTING 04/04/2006
 */

 include(DIR_WS_MODULES . zen_get_module_directory(FILENAME_PRODUCT_LISTING));
?>
<div id="productListing">
<?php 
// only show when there is something to submit and enabled
    if ($show_top_submit_button == true) {
?>
<div class="buttonRow forward"><?php echo zen_image_submit(BUTTON_IMAGE_ADD_PRODUCTS_TO_CART, BUTTON_ADD_PRODUCTS_TO_CART_ALT, 'id="submit1" name="submit1"'); ?></div>
<br class="clearBoth" />
<?php
    } // show top submit
?>

<?php if ( ($listing_split->number_of_rows > 0) && ( (PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3') ) ) {
?>
<div id="productsListingTopNumber" class="navSplitPagesResult back"><?php echo $listing_split->display_count(TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></div>
<div id="productsListingListingTopLinks" class="navSplitPagesLinks forward"><?php echo TEXT_RESULT_PAGE . ' ' . $listing_split->display_links(MAX_DISPLAY_PAGE_LINKS, zen_get_all_get_params(array('page', 'info', 'x', 'y', 'main_page'))); ?></div>
<br class="clearBoth" />
<?php
}
?>

<?php
/**
 * load the list_box_content template to display the products
 */
if (PRODUCT_LISTING_LAYOUT_STYLE == 'columns') {
  require($template->get_template_dir('tpl_columnar_display.php',DIR_WS_TEMPLATE, $current_page_base,'common'). '/tpl_columnar_display.php');
} else {// (PRODUCT_LISTING_LAYOUT_STYLE == 'rows')
  require($template->get_template_dir('tpl_tabular_display.php',DIR_WS_TEMPLATE, $current_page_base,'common'). '/tpl_tabular_display.php');
}

?>

<?php if ( ($listing_split->number_of_rows > 0) && ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3')) ) {
?>

<form name="productsort2" action="<?php echo zen_href_link(FILENAME_DEFAULT,zen_get_all_get_params(array('sort'))) ?>" method="get">
    <div class="product-listing-multibar">
        <div class="listing-pagination"><?=$listing_split->display_links(MAX_DISPLAY_PAGE_LINKS, zen_get_all_get_params(array('page', 'info', 'x', 'y')));?>
            
        </div>

        <?php echo $listing_split->display_count(TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?>

        <select name="sort" onChange="document.productsort2.submit()">
            <option value="<?=PRODUCT_LIST_NAME?>a" <?=PRODUCT_LIST_NAME.'a'==$_GET['sort'] ? 'selected="selected"' : ''?>>Product name</option>
            <option value="<?=PRODUCT_LIST_PRICE?>a" <?=PRODUCT_LIST_PRICE.'a'==$_GET['sort'] ? 'selected="selected"' : ''?>>Price - Low to High</option>
            <option value="<?=PRODUCT_LIST_PRICE?>d" <?=PRODUCT_LIST_PRICE.'d'==$_GET['sort'] ? 'selected="selected"' : ''?>>Price - High to Low</option>
        </select>
        <?php
            $param_temp = zen_get_all_get_params(array('sort','cPath'));
            $param_temp = explode('&',$param_temp);
            if(count($param_temp)):
                foreach($param_temp as $par):
                    $namevals = explode('=',$par);
                    if(strlen($namevals[0])>0&&strlen($namevals[1])>0):
                        echo '<input type="hidden" name="'.$namevals[0].'" value="'.$namevals[1].'" />';
                    endif;
                endforeach;
            endif;
        ?>
    </div>
</form>
<?php
  }
?>

<?php 
// only show when there is something to submit and enabled
    if ($show_bottom_submit_button == true) {
?>
<div class="buttonRow forward"><?php echo zen_image_submit(BUTTON_IMAGE_ADD_PRODUCTS_TO_CART, BUTTON_ADD_PRODUCTS_TO_CART_ALT, 'id="submit2" name="submit1"'); ?></div>
<br class="clearBoth" />
<?php
    } // show_bottom_submit_button
?>
</div>

<?php
// if ($show_top_submit_button == true or $show_bottom_submit_button == true or (PRODUCT_LISTING_MULTIPLE_ADD_TO_CART != 0 and $show_submit == true and $listing_split->number_of_rows > 0)) {
  if ($show_top_submit_button == true or $show_bottom_submit_button == true) {
?>
</form>
<?php } ?>
