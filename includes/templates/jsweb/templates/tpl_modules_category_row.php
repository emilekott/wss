<?php
/**
 * index category_row.php
 *
 * changed by EK 2/12/12
 * 
 * Couldn't be bothered to look around in files so built this which shows a list of all categories
 * followed by list of all products in those sub categories.
 * This means top level categories act more like a filter and it decreases the clicks
 * 
 */
if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}
$title = '';
$num_categories = $categories->RecordCount();

if ($num_categories > 0) {
 
    while (!$categories->EOF) {
        $cPath_new = zen_get_path($categories->fields['categories_id']);
        // strip out 0_ from top level cats
        $cPath_new = str_replace('=0_', '=', $cPath_new);
        $sub_cats[$categories->fields['categories_id']] = array(
            'link' => zen_href_link(FILENAME_DEFAULT, $cPath_new),
            'title' => $categories->fields['categories_name'],
        );
        $categories->MoveNext();
    }
}

?>
<ul id ="subcategory-list">
    <?php foreach($sub_cats as $key=>$category){ ?>
    <li class="cat-<?php echo($key); ?>"><a href="<?php print $category['link']; ?>"><?php print $category['title']; ?></a></li>
    <?php
    $cats_list[] = $key;
        } 
     ?>
</ul><div class="clear"></div>
<!--
<form action="../">
<label for="category">Category:</label>
<select name="category" onchange="window.open(this.options[this.selectedIndex].value,'_top')">
    <option value = "0" selected = "selected">-- All --</option>
    <?php foreach($sub_cats as $key=>$category){ ?>
    <option value="<?php echo $category['link']; ?>"><?php echo $category['title']; ?></option>
    <?php } ?>
</select>
</form>
-->

<?php 
    $subcat_list = implode(',',$cats_list);
    $sorter = $_GET['sort'];
    if ($sorter){
        switch ($sorter){
            case "2a":
                //product name
                $listing_sql = "select p.products_image, pd.products_name, p.products_quantity, p.products_id, p.products_type, p.master_categories_id, p.manufacturers_id, p.products_price, p.products_tax_class_id, pd.products_description, IF(s.status = 1, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status =1, s.specials_new_products_price, p.products_price) as final_price, p.products_sort_order, p.product_is_call, p.product_is_always_free_shipping, p.products_qty_box_status from zen_products_description pd, zen_products p left join zen_manufacturers m on p.manufacturers_id = m.manufacturers_id, zen_products_to_categories p2c left join zen_specials s on p2c.products_id = s.products_id where p.products_status = 1 and p.products_id = p2c.products_id and pd.products_id = p2c.products_id and pd.language_id = '1' and p2c.categories_id IN ($subcat_list) order by pd.products_name ASC";
                break;
            case "3a":
                //price asc
                $listing_sql = "select p.products_image, pd.products_name, p.products_quantity, p.products_id, p.products_type, p.master_categories_id, p.manufacturers_id, p.products_price, p.products_tax_class_id, pd.products_description, IF(s.status = 1, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status =1, s.specials_new_products_price, p.products_price) as final_price, p.products_sort_order, p.product_is_call, p.product_is_always_free_shipping, p.products_qty_box_status from zen_products_description pd, zen_products p left join zen_manufacturers m on p.manufacturers_id = m.manufacturers_id, zen_products_to_categories p2c left join zen_specials s on p2c.products_id = s.products_id where p.products_status = 1 and p.products_id = p2c.products_id and pd.products_id = p2c.products_id and pd.language_id = '1' and p2c.categories_id IN ($subcat_list) order by p.products_price ASC";
                break;
            case "3d":
                //price desc
                $listing_sql = "select p.products_image, pd.products_name, p.products_quantity, p.products_id, p.products_type, p.master_categories_id, p.manufacturers_id, p.products_price, p.products_tax_class_id, pd.products_description, IF(s.status = 1, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status =1, s.specials_new_products_price, p.products_price) as final_price, p.products_sort_order, p.product_is_call, p.product_is_always_free_shipping, p.products_qty_box_status from zen_products_description pd, zen_products p left join zen_manufacturers m on p.manufacturers_id = m.manufacturers_id, zen_products_to_categories p2c left join zen_specials s on p2c.products_id = s.products_id where p.products_status = 1 and p.products_id = p2c.products_id and pd.products_id = p2c.products_id and pd.language_id = '1' and p2c.categories_id IN ($subcat_list) order by p.products_price DESC";
                break;
        }
        
    }
    else{
        $listing_sql = "select p.products_image, pd.products_name, p.products_quantity, p.products_id, p.products_type, p.master_categories_id, p.manufacturers_id, p.products_price, p.products_tax_class_id, pd.products_description, IF(s.status = 1, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status =1, s.specials_new_products_price, p.products_price) as final_price, p.products_sort_order, p.product_is_call, p.product_is_always_free_shipping, p.products_qty_box_status from zen_products_description pd, zen_products p left join zen_manufacturers m on p.manufacturers_id = m.manufacturers_id, zen_products_to_categories p2c left join zen_specials s on p2c.products_id = s.products_id where p.products_status = 1 and p.products_id = p2c.products_id and pd.products_id = p2c.products_id and pd.language_id = '1' and p2c.categories_id IN ($subcat_list) order by p.products_sort_order, pd.products_name";
    }
    
    require($template->get_template_dir('tpl_modules_product_listing.php', DIR_WS_TEMPLATE, $current_page_base,'templates'). '/' . 'tpl_modules_product_listing.php');

?>
