<?php
/**
 * Side Box Template
 *
 * @package templateSystem
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_search.php 4142 2006-08-15 04:32:54Z drbyte $
 */
  $content = "";
  $content .= '<div id="' . str_replace('_', '-', $box_id . 'Content') . '">';
  $content = '<div id="search01" class="back">';
  $content .= zen_draw_form('quick_find', zen_href_link(FILENAME_ADVANCED_SEARCH_RESULT, '', 'NONSSL', false), 'get');
  $content .= zen_draw_hidden_field('main_page',FILENAME_ADVANCED_SEARCH_RESULT);
  $content .= zen_draw_hidden_field('search_in_description', '1') . zen_hide_session_id();

  if (strtolower(IMAGE_USE_CSS_BUTTONS) == 'yes') {
  $content .= zen_draw_input_field('keyword', '', 'size="18" maxlength="50" style="width:130px; height:19px; padding-top:6px;border:0px;margin-left:4px; margin-top:1px; font-size:10px; color:#838383;" value="' . HEADER_SEARCH_DEFAULT_TEXT . '" onfocus="if (this.value == \'' . HEADER_SEARCH_DEFAULT_TEXT . '\') this.value = \'\';" onblur="if (this.value == \'\') this.value = \'' . HEADER_SEARCH_DEFAULT_TEXT . '\';"') . '</div><div id="search02" class="back"><input type="image" src="includes/templates/jsweb/buttons/english/button_search01.png" alt="' . HEADER_SEARCH_BUTTON . '"/>';
  } else {
    $content .= zen_draw_input_field('keyword', '', 'size="18" maxlength="50" style="width:130px; height:15px;border:0px;padding-top:6px; margin-left:4px; margin-top:1px; font-size:10px; color:#838383;" value="' . HEADER_SEARCH_DEFAULT_TEXT . '" onfocus="if (this.value == \'' . HEADER_SEARCH_DEFAULT_TEXT . '\') this.value = \'\';" onblur="if (this.value == \'\') this.value = \'' . HEADER_SEARCH_DEFAULT_TEXT . '\';"') . '</div><div id="search02" class="back"><input type="image" src="includes/templates/jsweb/buttons/english/button_search01.png" alt="' . HEADER_SEARCH_BUTTON . '"/>';
  }
  $content .= '</form></div><br class="clearBoth" /></div>';


?>