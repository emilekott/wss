<?php
/**
 * Side Box Template
 *
 * @package templateSystem
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_manufacturers_select.php 4771 2006-10-17 05:32:42Z ajeh $
 */
  $content = '<div id="menulink"><a  href="#" onclick="showpopup(\'showmanu\'); return false"><img src="'. DIR_WS_TEMPLATES . $template_dir .'/images/design/brand.jpg" border="0" alt="" onmouseover="this.src=\''. DIR_WS_TEMPLATES . $template_dir .'/images/design/brand_hover.jpg\'" onmouseout="this.src=\''. DIR_WS_TEMPLATES . $template_dir .'/images/design/brand.jpg\'" /></a></div>';
  $content .= '<div id="' . str_replace('_', '-', $box_id . 'Content') . '" class="sideBoxContent2 centeredContent2"><div id="showmanu">';
  $content.= zen_draw_form('manufacturers_form', zen_href_link(FILENAME_DEFAULT, '', 'NONSSL', false), 'get');
  $content .= zen_draw_hidden_field('main_page', FILENAME_DEFAULT);
  $content .= zen_draw_pull_down_menu('manufacturers_id', $manufacturer_sidebox_array, (isset($_GET['manufacturers_id']) ? $_GET['manufacturers_id'] : ''), 'onchange="this.form.submit();" size="' . MAX_MANUFACTURERS_LIST . '" style="width: 100%; margin: auto; background-color: #000; color:#fff;"') . zen_hide_session_id();
  $content .= '</form></div><div id="viewallbr"><a id="viewallbrlink" href="'. zen_href_link(FILENAME_EZPAGES, 'id=12', 'NONSSL').'">View all Brands</a></div>';
   $content .= '</form></div><div id="newbr"><a id="newbr" href="http://www.witteringsurfshop.com/sale-everything-reduced-c-15.html">Summer Sale</a></div>';
      $content .= '</form></div><div id="newbr"><a id="newbr" href="http://www.witteringsurfshop.com/page.html?id=15">New Hardware</a></div>';
	  
	  $content .= '</form></div><div id="newbr"><a id="newbr" href="http://www.witteringsurfshop.com/surfboards-surfboard-packages-c-5_37.html">Package Deals</a></div>';
  
  $content .= '</div>';
?>
