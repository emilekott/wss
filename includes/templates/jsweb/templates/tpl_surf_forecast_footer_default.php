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
<!-- bof tpl_surf_forecast_footer_default.php -->
	<div class='centerColumn' id='surf_forecast_footer'>
		<h1 id='surf_forecast_footer-heading'><?php echo HEADING_TITLE; ?></h1>
		<div id='surf_forecast_footer-content' class='content'>
		<?php
		/**
		* require the html_define for the surf_forecast_footer page
		*/
		require($define_page);
		?>
		</div>
	</div>
<!-- eof tpl_surf_forecast_footer_default.php -->
