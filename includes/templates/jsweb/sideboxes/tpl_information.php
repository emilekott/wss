<?php
/**
 * Side Box Template
 *
 * @package templateSystem
 * @copyright Copyright 2003-2005 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_information.php 2982 2006-02-07 07:56:41Z birdbrain $
 */
  $content = '';
  $content .= '<div id="' . str_replace('_', '-', $box_id . 'Content') . '" class="sideBoxContent">';
 // $content .= "\n" . '<ul style="margin: 0; padding: 0; list-style-type: none;">' . "\n";
  for ($i=0; $i<sizeof($information); $i++) {
  //  $content .= '<div class="cattab03"><img src="'. DIR_WS_TEMPLATES . $template_dir.'/images/design/arrows.jpg" border="0" alt="" />&nbsp;' . $information[$i] . '</div>' . "\n";
    $content .= '<div class="cattab03">' . $information[$i] . '</div>' . "\n";
  }
 // $content .= '</ul>' .  "\n";
  $content .= '</div>';
?>
