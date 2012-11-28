<?php

/**
 * @author     Conor Kerr <zen-cart.advshipper@dev.ceon.net>
 * @copyright  Copyright 2007-2009 Ceon
 * @copyright  Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright  Portions Copyright 2003 osCommerce
 * @link       http://dev.ceon.net/web/zen-cart/advshipper
 * @version    $Id: advshipper.js 382 2009-06-22 18:49:29Z Bob $
 */

?>

function advshipperToggleMethod(method, link_obj, link_hide_text, link_show_text)
{
	method_config_el = document.getElementById('method_' + method + '_config');
	
	if (method_config_el.style.display == 'block') {
		method_config_el.style.display = 'none';
		link_obj.innerHTML = link_show_text;
	} else {
		method_config_el.style.display = 'block';
		link_obj.innerHTML = link_hide_text;
	}
	
	return false;
}

/**
 * Prevent the accidential deletion of a method!
 */
function advshipperConfirmDeletion()
{
	var perform_deletion = confirm('<?php echo addslashes(TEXT_JS_DELETE_CONFIRMATION); ?>');
	if (perform_deletion) {
		return true;
	} else {
		return false;
	}
}