<?php
/**
 * Module Template:
 * Loaded by product-type template to display additional product images.
 *
 * @package templateSystem
 * @copyright Copyright 2003-2005 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_modules_additional_images.php 3215 2006-03-20 06:05:55Z birdbrain $
 */

  require(DIR_WS_MODULES . zen_get_module_directory('additional_images.php'));
 ?>
 <?php
 if ($flag_show_product_info_additional_images != 0 && $num_images > 0) {
  ?>
<div id="productAdditionalImages">
<?php
echo '<div' . ' class="additionalImages centeredContent back" style="width: 100px;"' . '>' . '<a href="'.$first_image.'" rev="'.$first_image.'" rel="zoom-id:ancid1;" onclick="changeZooming(\''.$first_image.'\', \''.$first_image.'\'); return false;">'.'<img height="'.$SMALL_IMAGE_HEIGHT_x.'" width="'.$SMALL_IMAGE_WIDTH_x.'" src="'.$first_image.'" />'.'</a>' .  '</div>' . "\n";
?>
<?php
	 require($template->get_template_dir('tpl_columnar_display.php',DIR_WS_TEMPLATE, $current_page_base,'common'). '/tpl_columnar_display.php'); ?>

</div>
<?php 
  }
?>
