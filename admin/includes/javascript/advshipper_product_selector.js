<?php

/**
 * @author     Conor Kerr <zen-cart.advshipper@dev.ceon.net>
 * @copyright  Copyright 2007-2009 Ceon
 * @copyright  Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright  Portions Copyright 2003 osCommerce
 * @link       http://dev.ceon.net/web/zen-cart/advshipper
 * @version    $Id: advshipper_product_selector.js 382 2009-06-22 18:49:29Z Bob $
 */

?>

function advshipperSelectOptions()
{
	// Get the current selections
	try {
		all_options = document.getElementById('select_options_all_options').checked;
		select_options = document.getElementById('select_options_select_options').checked;
	} catch (e) {
		all_options = document.select_options_form.eval('select_options_all_options').checked;
		select_options = document.select_options_form.eval('select_options_select_options').checked;
	}
	
	select_options_panel = document.getElementById('select_options_panel');
	
	if (all_options) {
		// Hide the panel for option selections
		select_options_panel.style.display = 'none';
	} else {
		select_options_panel.style.display = '';
	}
}


function advshipperProductSelected(product_id_selected)
{
	var selected_product_and_options_string = product_id_selected;
	
	// Does the product have any options?
	var all_options = document.getElementById('select_options_all_options');
	
	if (all_options != undefined) {
		try {
			all_options = document.getElementById('select_options_all_options').checked;
			select_options = document.getElementById('select_options_select_options').checked;
		} catch (e) {
			all_options = document.select_options_form.eval('select_options_all_options').checked;
			select_options = document.select_options_form.eval('select_options_select_options').checked;
		}
		
		// Have any specific options been selected for this product?
		if (!all_options) {
			// Identify the selected options
			selected_product_and_options_string += advshipperSelectedOptions();
		}
	}
	
	window.opener.advshipperAddProduct(selected_product_and_options_string);
	
	window.close();
}
