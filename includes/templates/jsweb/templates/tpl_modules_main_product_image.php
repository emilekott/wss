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

<style type="text/css">
#block_zoom{float: left; clear: both; width: 305px; margin-right: 8px;}

#inst, #zoom_imgx{float: left;}
#inst{width: 234px;}

#zoom_imgx{padding-left: 6px;}

#wrapperx, #imx{float: left; cursor: pointer;}

#wrapperx{margin-top: -300px;}

#productMainImage{border: none; padding-bottom: 0px; margin-bottom: 0px;}
#imx, #imxi{ border-bottom: 1px solid #cccccc;}

#productMainImage, #block_zoom{float: right; clear: both;}
#productAdditionalImages{clear: both;}
</style>

<script type="text/javascript" src="/includes/templates/jsweb/jscript/fancybox/jquery.fancybox-1.3.1.pack.js"></script>
<link rel="stylesheet" type="text/css" href="/includes/templates/jsweb/jscript/fancybox/jquery.fancybox-1.3.1.css" media="screen" />

<script type="text/javascript">
jQuery(document).ready(function() {
    jQuery("a.imgbig").fancybox({'titleShow':false});
    jQuery('a.open-prodpopup').fancybox();
});
function changeZooming(path){
    jQuery('#imx img').attr('src',path);
    jQuery('.imgbig').attr('href',path);
    return false
}

</script>

<?php require(DIR_WS_MODULES . zen_get_module_directory(FILENAME_MAIN_PRODUCT_IMAGE)); ?>
<?php
$first_image = $products_image_large;
?>
<div id="productMainImage" class="centeredContent back">
     <div id="divmainimage" class="content_product_images_mainimage_zoom boxprview" style="width: 430px;">

          <div id="imx" onclick="">
              <a class="imgbig" href="<?php echo $products_image_medium; ?>"><img id="med_img" src="<?php echo $products_image_medium; ?>" width="<?php echo MEDIUM_IMAGE_WIDTH; ?>" height="<?php echo MEDIUM_IMAGE_HEIGHT; ?>" alt="<?php echo $products_name; ?>" /></a>
          </div>
     </div>
     
     
     
</div>

<div id="block_zoom">
     <div id="inst">
          &nbsp;
     </div>
     <div id="zoom_imgx">
          <a class="imgbig" href="<?php echo $products_image_medium; ?>">
               <img src="<?php echo DIR_WS_TEMPLATE;?>/images/zoomplus.gif" />
          </a>
     </div>
</div>