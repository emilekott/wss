<?php
//
// +----------------------------------------------------------------------+
// |zen-cart Open Source E-commerce                                       |
// +----------------------------------------------------------------------+
// | Copyright (c) 2006 The zen-cart developers                           |
// |                                                                      |
// | http://www.zen-cart.com/index.php                                    |
// |                                                                      |
// | Portions Copyright (c) 2003 osCommerce                               |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the GPL license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available through the world-wide-web at the following url:           |
// | http://www.zen-cart.com/license/2_0.txt.                             |
// | If you did not receive a copy of the zen-cart license and are unable |
// | to obtain it through the world-wide-web, please send a note to       |
// | license@zen-cart.com so we can mail you a copy immediately.          |
// +----------------------------------------------------------------------+
// $Id: css_buttons.php
// 2006/10/30 by Paul Mathot (NL)

function css_button ($image = '', $text, $type, $parameters = '') {
  global $css_button_text;
  // the secondary class allows styling of individual buttons
  // (id's won't work well, because buttons may be shown multiple times on a page)
  $name = basename($image, '.gif');
  $sec_class = ' ' . $name;
  $mouse_out_class = 'cssButton' . $sec_class;
  
  if(!empty($parameters))$parameters = ' ' . $parameters;
  
  if (CSSBUTTONS_JS_HOVER_ENABLE == 'false'){
  // disable javascript
    $css_button_js = '';
  }else{
  // javascript to set different classes on mouseover and mouseout: enables hover effect on the buttons
  // (pure css hovers on non link elements do work work in < IE7) 
    $mouse_over_class = 'cssButtonHover' . $sec_class . $sec_class . 'Hover';
    $css_button_js .=  'onmouseover="this.className=\''. $mouse_over_class . '\'" onmouseout="this.className=\'' . $mouse_out_class . '\'"';
  }

  if (strpos($parameters, 'class="') === FALSE){
  // only add class if no class is passed through $parameters
    $class = ' class="' . $mouse_out_class . '" ' . $css_button_js;
  }
  
  if ($type == 'submit'){
  // form input button
    // bof hack to simulate image button (replace parameter by _x parameter)
    // needed for gv name="edit" button (other named buttons are name="btn_submit" and name="submit1" and ...??)
    if (strpos($parameters, 'name')!== false){
    // replace by regular expression
      $s=strpos($parameters, '"', strpos($parameters, 'name'))+1;
      $e=strpos($parameters, '"', $s);
      $name = substr($parameters, $s, $e-$s);
      // replaces values of both name and value ()
      //$parameters = str_replace($name, $name . '_x', $parameters);
      // replaces the value of name only
      $parameters = substr_replace($parameters, $name . '_x', $s, $e-$s);
    }
    // eof hack to simulate image button  
    $css_button = '<input' . $class . ' type="submit" value="' .$text . '"' . $parameters . ' />';
  }

  if ($type == 'button'){
  // link button
    $css_button = '<span'. $class . $parameters . '>' . $text . '</span>'; // add $parameters ???
  }
  
  // bof *experimental* CSS popup code
  //$css_button_text['button_prev'] = 'Testing button previous popup';
  if (CSSBUTTONS_CATALOG_POPUPS_ENABLE == 'true'){    
    if(CSSBUTTONS_CATALOG_POPUPS_SHOW_BUTTON_NAMES == 'true'){
      $popuptext = $name;
    }elseif(!empty($css_button_text[$name])){
      $popuptext = $css_button_text[$name];
    }else{
      $popuptext = '';
    }
    
    if($popuptext != ''){
      if ($type == 'submit'){
      // link button    
       $css_button = '<span class="cssButtonSubmitPopup">' . $css_button . '<strong>' . zen_output_string_protected($popuptext) . '</strong></span>';
      }  
      
     if ($type == 'button'){
      // link button    
       //$css_button .= '<span class="cssButtonPopup">' . $css_button_text[$name] . '</span>';
       $css_button = '<span class="cssButtonLinkPopup">' . $css_button . '<strong>' . zen_output_string_protected($popuptext) . '</strong></span>';
      }
    }  
  }
  // eof *experimental* CSS popup code

  return $css_button;
}
?>