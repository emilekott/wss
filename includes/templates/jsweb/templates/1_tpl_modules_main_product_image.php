<?php
/**
 * Module Template
 *
 * @package templateSystem
 * @copyright Copyright 2003-2005 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_modules_main_product_image.php 3208 2006-03-19 16:48:57Z birdbrain $
 */
?>
<?php require(DIR_WS_MODULES . zen_get_module_directory(FILENAME_MAIN_PRODUCT_IMAGE)); ?> 
<div id="productMainImage" class="centeredContent back">
     <div id="divmainimage" class="content_product_images_mainimage_zoom boxprview" style="width: 290px;">
          <a style="DISPLAY: block; CURSOR: crosshair; POSITION: relative; TEXT-DECORATION: none; outline-color: -moz-use-text-color; outline-style: none; outline-width: 0pt; moz-user-select: none" id="ancid1" class="MagicZoom" href="<?php echo $products_image_large; ?>" rel="zoom-position: inner; zoom-width:<?php echo MEDIUM_IMAGE_WIDTH; ?>px; zoom-height:<?php echo MEDIUM_IMAGE_HEIGHT; ?>px;drag-mode:false;" >
          	<img src="<?php echo $products_image_medium; ?>" width="<?php echo MEDIUM_IMAGE_WIDTH; ?>" height="<?php echo MEDIUM_IMAGE_HEIGHT; ?>" alt="<?php echo $products_name; ?>" />
          </a>
     </div>
</div>