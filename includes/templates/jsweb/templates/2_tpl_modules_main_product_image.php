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
#block_zoom{float: left; clear: both;}

#inst, #zoom_imgx{float: left;}
#inst{width: 234px;}

#zoom_imgx{padding-left: 6px;}

#wrapperx, #imx{float: left; cursor: pointer;}

#wrapperx{margin-top: -300px;}

#productMainImage{border: none; padding-bottom: 0px; margin-bottom: 0px;}
#imx, #imxi{ border-bottom: 1px solid #ccc;}
</style>

<script type="text/javascript">

function changeZooming(l, m)
{
	document.getElementById('med_img').src = m;
	
	document.getElementById('imxi').src = m;
}

function showBigImage()
{
	document.getElementById('zoomplus').style.display = 'none';
	document.getElementById('zoomminus').style.display = 'block';
	
	document.getElementById('inst').innerHTML = '&nbsp;<img src="<?php echo DIR_WS_TEMPLATE;?>/images/clickndrag.gif" />';
	document.getElementById('imx').style.display = 'none';
	document.getElementById('wrapperx').style.display = 'block';
	

}

function hideBigImage()
{
	document.getElementById('zoomplus').style.display = 'block';
	document.getElementById('zoomminus').style.display = 'none';
	
	document.getElementById('inst').innerHTML = '&nbsp;';
	document.getElementById('wrapperx').style.display = 'none';
	document.getElementById('imx').style.display = 'block';
}



jQuery(document).ready(function() { 
   hideBigImage();
   jQuery('#wrapperx').css('margin-top', 0);
}); 

</script>

<?php require(DIR_WS_MODULES . zen_get_module_directory(FILENAME_MAIN_PRODUCT_IMAGE)); ?>
<?php
$first_image = $products_image_large;
?>
<div id="productMainImage" class="centeredContent back">
     <div id="divmainimage" class="content_product_images_mainimage_zoom boxprview" style="width: 290px;">
          <div id="wrapperx">
               <a id="ancid1" style="display: block; cursor: crosshair; position: relative; text-decoration: none;outline-style: none; outline-width: 0pt;" class="MagicZoom" href="<?php echo $products_image_large; ?>" rel="zoom-position:inner; zoom-width:<?php echo MEDIUM_IMAGE_WIDTH; ?>px; zoom-height:<?php echo MEDIUM_IMAGE_HEIGHT; ?>px;drag-mode:false;" >
                    <img id="imxi" src="<?php echo $products_image_large; ?>" width="<?php echo MEDIUM_IMAGE_WIDTH; ?>" height="<?php echo MEDIUM_IMAGE_HEIGHT; ?>" alt="<?php echo $products_name; ?>" />
               </a>
          </div>
          <div id="imx" onclick="showBigImage();">
          	<img id="med_img" src="<?php echo $products_image_medium; ?>" width="<?php echo MEDIUM_IMAGE_WIDTH; ?>" height="<?php echo MEDIUM_IMAGE_HEIGHT; ?>" alt="<?php echo $products_name; ?>" />
          </div>
     </div>
     
     
     
</div>

<div id="block_zoom">
     <div id="inst">
          &nbsp;
     </div>
     <div id="zoom_imgx">
          <a id="zoomplus" href="#" onclick="showBigImage(); return false;">
               <img src="<?php echo DIR_WS_TEMPLATE;?>/images/zoomplus.gif" />
          </a>
          <a id="zoomminus" style="display: none;" href="#" onclick="hideBigImage(); return false;">
               <img src="<?php echo DIR_WS_TEMPLATE;?>/images/zoomminus.gif" />
          </a>
     </div>
</div>