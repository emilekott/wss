<?php
/**
 * column_left module
 *
 * @package templateStructure
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: column_left.php 4274 2006-08-26 03:16:53Z drbyte $
 */
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}
?>
<div id="search-right"><?php require(DIR_WS_MODULES . 'sideboxes/jsweb/search.php'); ?></div>
<?php if(isset($_GET['cPath'])&&$_GET['cPath']>0):
//get sub-categories
    error_reporting(E_ALL);
    $cPath_current = explode('_',$cPath);

    $check_if_sub_of_cat = $db->Execute("SELECT parent_id FROM zen_categories WHERE categories_id = ".zen_db_input(end($cPath_current)));
    if($check_if_sub_of_cat->fields['parent_id']):
          $hide_spotlight = true;
          $cPath_current =  array();
          $cPath_current[] = $check_if_sub_of_cat->fields['parent_id'];
    endif;

    $sub_cats_query = $db->Execute("SELECT categories_name,categories_id FROM (SELECT categories_id FROM zen_categories WHERE categories_status=1 AND parent_id = ".zen_db_input(end($cPath_current)).") cats LEFT JOIN zen_categories_description USING(categories_id)");
?>
    <?php if($sub_cats_query->RecordCount()>0): ?>
    <div id="rtop">
        Browse by category:
    </div>
    <div id="rspotlight-main" style="margin-bottom: 10px">
        <ul>
            <?php while(!$sub_cats_query->EOF): ?>
            <li <?php if(end(explode('_',$cPath))==$sub_cats_query->fields['categories_id']): echo 'id="curcat"'; endif; ?>><a href="<?=zen_href_link(FILENAME_DEFAULT, 'cPath='.$sub_cats_query->fields['categories_id'])?>"><?=$sub_cats_query->fields['categories_name']?></a></li>
            <?php $sub_cats_query->MoveNext(); endwhile; ?>
        </ul>
    </div>
    <?php else: ?>
    <div style="height: 10px"></div>
    <?php endif ?>
<?php else: ?>
    <div id="rtop">
        Spotlight Brand
    </div>
    <?php echo zen_display_banner('static','69') ?>
    <div id="rspotlight-main">
        <?php
            //random categories
            $random_cats_query = $db->Execute("SELECT manufacturers_id,manufacturers_name FROM zen_manufacturers ORDER BY RAND() LIMIT 5");
        ?>
        <ul>
            
            <?php while(!$random_cats_query->EOF): ?>
            <?php 
                $man_name = $random_cats_query->fields['manufacturers_name'];
                $short_name = (strlen($man_name) > 26) ? substr($man_name,0,23).'...' : $man_name;
              ?>
            <li><a href="<?=zen_href_link(FILENAME_DEFAULT, 'manufacturers_id='.$random_cats_query->fields['manufacturers_id'])?>"><?=$short_name?></a></li>
            <?php $random_cats_query->MoveNext(); endwhile; ?>
        </ul>
    </div>
    <div id="rspotlight-bot">
        <a href="<?=zen_href_link(FILENAME_EZPAGES,'id=12')?>">View all brands</a>
    </div>
<?php endif ?>


<?php
$column_box_default='tpl_box_default_left.php';
// Check if there are boxes for the column
$column_left_display= $db->Execute("select layout_box_name from " . TABLE_LAYOUT_BOXES . " where layout_box_location = 0 and layout_box_status= '1' and layout_template ='" . $template_dir . "'" . ' order by layout_box_sort_order');
// safety row stop
$box_cnt=0;
while (!$column_left_display->EOF and $box_cnt < 100) {
  $box_cnt++;

  if ( file_exists(DIR_WS_MODULES . 'sideboxes/' . $column_left_display->fields['layout_box_name']) or file_exists(DIR_WS_MODULES . 'sideboxes/' . $template_dir . '/' . $column_left_display->fields['layout_box_name']) ) {
?>
<?php
//$column_box_spacer = 'column_box_spacer_left';
$column_width = BOX_WIDTH_LEFT;
if ( file_exists(DIR_WS_MODULES . 'sideboxes/' . $template_dir . '/' . $column_left_display->fields['layout_box_name']) ) {
  $box_id = zen_get_box_id($column_left_display->fields['layout_box_name']);
  require(DIR_WS_MODULES . 'sideboxes/' . $template_dir . '/' . $column_left_display->fields['layout_box_name']);
} else {
  $box_id = zen_get_box_id($column_left_display->fields['layout_box_name']);
  require(DIR_WS_MODULES . 'sideboxes/' . $column_left_display->fields['layout_box_name']);
}
  } // file_exists
  $column_left_display->MoveNext();
} // while column_left
$box_id = '';

?>