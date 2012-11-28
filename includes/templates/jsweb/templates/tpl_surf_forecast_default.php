<?php
/**
* @package page template
* @copyright Copyright 2003-2006 Zen Cart Development Team
* @copyright Portions Copyright 2003 osCommerce
* @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
* @version $Id: Define Generator v0.1 $
*/

// THIS FILE IS SAFE TO EDIT! This is the template page for your new page 

?>
<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="js/jquery.dimensions.js"></script>
<script type="text/javascript" src="js/jquery.tooltip.js"></script>
<style type="text/css">

#tooltip {
	position: absolute;
	z-index: 3000;
	border: 1px solid #111;
	background-color: #eee;
	padding: 5px;
	opacity: 1;
}
#tooltip h3, #tooltip div { margin: 0; }
</style>
<script type="text/javascript">

$(function() {
jQuery.noConflict
	jQuery('#Jbaner').tooltip({
	track: true, 
	delay: 0,
	showURL: false,
	bodyHandler: function() {
		return jQuery("<img/>").attr("src", this.src);
	}
});
})
</script>
<!-- bof tpl_surf_forecast_default.php -->
	<div class='centerColumn2' id='surf_forecast'>

		<div id='surf_forecast-content' class='content'>
<?php 
$define_pagetk2 = zen_get_file_directory(DIR_WS_LANGUAGES . $_SESSION['language'] . '/html_includes/', 'define_surf_forecast_header.php', 'false'); 
require($define_pagetk2);
?>

		

                <img src="/images/forecast_cam.jpg" style="margin-top: 5px;" alt="LIVE Streaming Surf Cam" border="0" /><br />
          <!-- Webcam -->
<!--
                <img src="/images/surfcam-soon.jpg" alt="Surf Cam Being Updated" border="0" /><br />
-->

<iframe src="http://magicseaweed.com/syndicate/client/webcams/eastwitterings.html" width="775px" height="550px" frameborder="0" > </iframe>


        <!-- Webcam -->
        <br clear="all" />


		
<?php
		/**
		* require the html_define for the surf_forecast page
		*/

		require($define_page);
?>
	<br class="clearBoth" />
	<?php
        require($template->get_template_dir('tpl_modules_whats_new.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_whats_new.php');
        ?>
	 
        
		<br class="clearBoth" />
<?php 
$define_pagetk = zen_get_file_directory(DIR_WS_LANGUAGES . $_SESSION['language'] . '/html_includes/', 'define_surf_forecast_footer.php', 'false'); 
require($define_pagetk);
?>
		</div>
	</div>
<!-- eof tpl_surf_forecast_default.php -->
