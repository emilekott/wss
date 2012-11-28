<?php
/**
* product_filter.php
*
*Zen Cart product filter module
  *Johnny Ye, Oct 2007
  */
     require($template->get_template_dir('tpl_product_filter.php',DIR_WS_TEMPLATE, $current_page_base,'sideboxes'). '/tpl_product_filter.php');

  	$title =  BOX_HEADING_FILTER;
  	$title_link = false;

    require($template->get_template_dir($column_box_default, DIR_WS_TEMPLATE, $current_page_base,'common') . '/' . $column_box_default);
 ?>
