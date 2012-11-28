<?php
/**
 * googlefroogle.php
 *
 * @package google base feeder
 * @copyright Copyright 2007-2008 Numinix Technology http://www.numinix.com
 * @copyright Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: googlefroogle.php 30 2009-03-14 23:23:16Z numinix $
 * @author Numinix Technology
 */
/*
Google Base - Attribute List - http://base.google.co.uk/base/tab_instructions.html
Google Base - Products - Creating your bulk upload - http://base.google.com/base/products.html
*/
  require('includes/application_top.php');
  require(DIR_WS_CLASSES . 'google_base.php');
  $google_base = new google_base();  
  
  // include shipping class
  if (GOOGLE_BASE_SHIPPING_RATE_METHOD == 'percategory') { 
    include(DIR_WS_MODULES . 'shipping/percategory.php');
    $percategory = new percategory();
  } elseif (GOOGLE_BASE_SHIPPING_RATE_METHOD == 'free rules shipping') {
    include(DIR_WS_MODULES . 'shipping/freerules.php');
    $freerules = new freerules();
  }
  
  @define('GOOGLE_FROOGLE_EXPIRATION_DAYS', 30);
  @define('GOOGLE_FROOGLE_EXPIRATION_BASE', 'now'); // now/product
  @define('GOOGLE_FROOGLE_OFFER_ID', 'id'); // id/model/false
  @define('GOOGLE_FROOGLE_DIRECTORY', 'feed/');
  @define('GOOGLE_FROOGLE_OUTPUT_BUFFER_MAXSIZE', 1024*1024);
  $anti_timeout_counter = 0; //for timeout issues as well as counting number of products processed
  $google_base_start_counter = 0; //for counting all products regardless of inclusion
  $starting_point = GOOGLE_BASE_START_PRODUCTS;
  $max_limit = false;
  @define('GOOGLE_FROOGLE_USE_CPATH', 'false');
  @define('NL', "<br />\n");
  
  // process parameters
  $parameters = split('_', $_GET['feed']); // ?feed=fy_uy_tp
  $feed_parameter = $parameters[0];
  $feed = $google_base->get_feed($feed_parameter);
  $upload_parameter = $parameters[1];
  $upload = $google_base->get_upload($upload_parameter);
  $type_parameter = $parameters[2];
  $type = $google_base->get_type($type_parameter);
  $outfile = DIR_FS_CATALOG . GOOGLE_FROOGLE_DIRECTORY . GOOGLE_FROOGLE_OUTPUT_FILENAME . "_" . $type . ".xml"; //example domain_products.xml
  
  if (GOOGLE_BASE_MAGIC_SEO_URLS == 'true') {
    require_once(DIR_WS_CLASSES . 'msu_ao.php');
    include(DIR_WS_INCLUDES . 'modules/msu_ao_1.php');
  }  
  
  require(zen_get_file_directory(DIR_WS_LANGUAGES . strtolower(GOOGLE_FROOGLE_LANGUAGE) .'/', 'googlefroogle.php', 'false'));
  $language = ucwords(strtolower(GOOGLE_FROOGLE_LANGUAGE));
  $languages = $db->execute("select code, languages_id from " . TABLE_LANGUAGES . " where name='" . $language . "' limit 1");
  $product_url_add = (GOOGLE_FROOGLE_LANGUAGE_DISPLAY == 'true' ? "&language=" . $languages->fields['code'] : '') . (GOOGLE_FROOGLE_CURRENCY_DISPLAY == 'true' ? "&currency=" . GOOGLE_FROOGLE_CURRENCY : '');

  echo sprintf(TEXT_GOOGLE_FROOGLE_STARTED, $google_base->google_base_version()) . NL;
  echo TEXT_GOOGLE_FROOGLE_FILE_LOCATION . $outfile . NL;
  echo "Processing: Feed - " . (isset($feed) && $feed == "yes" ? "Yes" : "No") . ", Upload - " . (isset($upload) && $upload == "yes" ? "Yes" : "No") . NL;

  if (isset($feed) && $feed == "yes") {
    if (is_dir(DIR_FS_CATALOG . GOOGLE_FROOGLE_DIRECTORY)) {
      if (!is_writeable(DIR_FS_CATALOG . GOOGLE_FROOGLE_DIRECTORY)) {
        echo ERROR_GOOGLE_FROOGLE_DIRECTORY_NOT_WRITEABLE . NL;
        die;
      }
    } else {
      echo ERROR_GOOGLE_FROOGLE_DIRECTORY_DOES_NOT_EXIST . NL;
      die;
    }

    $stimer_feed = $google_base->microtime_float();
    if (!get_cfg_var('safe_mode') && function_exists('safe_mode')) {
      set_time_limit(0);
    }

    $output_buffer = "";


    if (file_exists($outfile)) {
      chmod($outfile, 0777);
    } else {
      fopen($outfile, "w");
    }
    if (is_writeable($outfile)) {
      
      $content = array();
      
      $content["xml"] = '<?xml version="1.0" encoding="UTF-8" ?>';
      $content["rss"] = '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">';
      $content["channel"]= '<channel>';
      $content["title"] = '<title>' . $google_base->google_base_xml_sanitizer(STORE_NAME, true) . '</title>';
      $content["link"] = '<link>' . GOOGLE_BASE_ADDRESS . '</link>';
      $content["channel_description"] = '<description>' . $google_base->google_base_xml_sanitizer(GOOGLE_BASE_DESCRIPTION, true) . '</description>';
      $google_base->google_base_fwrite($content, "wb", $type);
      
      
      $categories_array = $google_base->google_base_category_tree();
      
      $additional_attributes = '';
      $additional_tables = '';
      if (GOOGLE_BASE_ASA == 'true') {
        $additional_attributes .= ", p.products_upc, p.products_isbn, p.products_condition, p.products_category";
      }
      
      if (GOOGLE_BASE_MAP_PRICING == 'true') {
        $additional_attributes .= ", p.map_price, p.map_enabled";
        $gb_map_enabled = true;
      }
      
      if (GOOGLE_BASE_META_TITLE == 'true') {
        $additional_attributes .= ", mtpd.metatags_title";
        $additional_tables .= " LEFT JOIN " . TABLE_META_TAGS_PRODUCTS_DESCRIPTION . " mtpd ON (p.products_id = mtpd.products_id) ";
      }
      
      switch($type) {
        case "products":
          $products_query = "SELECT distinct(p.products_id), p.products_model, pd.products_name, pd.products_description, p.products_image, p.products_tax_class_id, p.products_price_sorter, p.products_priced_by_attribute, p.products_type, GREATEST(p.products_date_added, IFNULL(p.products_last_modified, 0), IFNULL(p.products_date_available, 0)) AS base_date, m.manufacturers_name, p.products_quantity, pt.type_handler, p.products_weight" . $additional_attributes . "
                             FROM " . TABLE_PRODUCTS . " p
                               LEFT JOIN " . TABLE_MANUFACTURERS . " m ON (p.manufacturers_id = m.manufacturers_id)
                               LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON (p.products_id = pd.products_id)
                               LEFT JOIN " . TABLE_PRODUCT_TYPES . " pt ON (p.products_type=pt.type_id)"
                             . $additional_tables . 
                             "WHERE p.products_status = 1
                                AND p.products_type <> 3
                                AND p.product_is_call <> 1
                               AND p.product_is_free <> 1
                               AND pd.language_id = " . (int)$languages->fields['languages_id'] ."
                             GROUP BY p.products_id
                             ORDER BY p.products_last_modified DESC";

          $products = $db->Execute($products_query);
            while (!$products->EOF && !$max_limit) { // run until end of file or until maximum number of products reached
              $google_base_start_counter++;
              // reset tax array
              $tax_rate = array();
              list($categories_list, $cPath) = $google_base->google_base_get_category($products->fields['products_id']);
              //if ($google_base->numinix_categories_check((GOOGLE_BASE_POS_CATEGORIES), $categories_list, 1) == true && $google_base->numinix_categories_check((GOOGLE_BASE_NEG_CATEGORIES), $categories_list, 2) == false && ($google_base_start_counter >= $starting_point)) { // check to see if category limits are set.  If so, only process for those categories.
              if ($google_base->numinix_categories_check(GOOGLE_BASE_POS_CATEGORIES, $products->fields['products_id'], 1) == true && $google_base->numinix_categories_check(GOOGLE_BASE_NEG_CATEGORIES, $products->fields['products_id'], 2) == false && ($google_base_start_counter >= $starting_point)) { // check to see if category limits are set.  If so, only process for those categories.  
                if ($gb_map_enabled && $products->fields['map_enabled'] == '1') {
                  $price = $products->fields['map_price'];
                } else {
                  $price = $google_base->google_get_products_actual_price($products->fields['products_id']);
                }
                //BEGIN ZERO QUANTITY CHECK
                if (GOOGLE_BASE_ZERO_QUANTITY == 'false') {
                  if ($products->fields['products_quantity'] > 0) {
                    $zero_quantity = false;
                  } else {
                    $zero_quantity = true;
                  }
                } else {
                  $zero_quantity = false;
                }
                
                $products_description = $products->fields['products_description'];
                if (GOOGLE_BASE_DEBUG == 'true') {
                  $success = false;
                  echo 'id: ' . $products->fields['products_id'] . ', price: ' . round($price, 2) . ', description length: ' . strlen($products_description) . ' ';
                  if ( ($price <= 0) || (strlen($products_description) < 15) ) {
                    echo '- skipped, price below zero or description length less than 15 chars.';
                  } else {
                    if ($zero_quantity == false) {
                      echo '- including';
                    } else {
                      echo '- skipped, zero quantity product.  turn on include zero quantity to include.';
                    }
                  }
                }
                
                if (($price > 0) && ($zero_quantity == false) && (strlen($products_description) >= 15)) {
                  if (GOOGLE_BASE_DEBUG == 'true') {
                    $success = true;
                  }
                  $anti_timeout_counter++;
                  if ($anti_timeout_counter == GOOGLE_FROOGLE_MAX_PRODUCTS && GOOGLE_FROOGLE_MAX_PRODUCTS != 0) { // if counter is greater than or equal to maximum products
                    $max_limit = true; // then max products reached
                  } else {
                    $max_limit = false; // otherwise, max products not reached
                  }

                  
                  $tax_rate = zen_get_tax_rate($products->fields['products_tax_class_id']);
                  
                  // calculate tax for tax amount
                  //$tax_amount = zen_calculate_tax($price, $tax_rate);
                  // the following will only add the tax if DISPLAY_PRICE_WITH_TAX is set to true in the Zen Cart admin
                  $price = zen_add_tax($price, $tax_rate);
                  
                  // modify price to match defined currency
                  $price = $currencies->value($price, true, GOOGLE_FROOGLE_CURRENCY, $currencies->get_value(GOOGLE_FROOGLE_CURRENCY));
                  
                  if (GOOGLE_BASE_MAGIC_SEO_URLS == 'true') {
                    include(DIR_WS_INCLUDES . 'modules/msu_ao_2.php');
                  } else { // default
                    $link = ($products->fields['type_handler'] ? $products->fields['type_handler'] : 'product') . '_info';
                    $cPath_href = (GOOGLE_FROOGLE_USE_CPATH == 'true' ? 'cPath=' . $cPath . '&' : '');
                    $link = zen_href_link($link, $cPath_href . 'products_id=' . (int)$products->fields['products_id'] . $product_url_add, 'NONSSL', false);
                  }
                  $product_type = $google_base->google_base_get_category($products->fields['products_id']);
                  array_pop($product_type); // removes category number from end
                  $product_type = explode(',', $product_type[0]);
                  
                  if (defined('GOOGLE_BASE_PAYMENT_METHODS') && GOOGLE_BASE_PAYMENT_METHODS != '') {
                    $payments_accepted = split(',', GOOGLE_BASE_PAYMENT_METHODS);
                  }
                  
                  $content = array();
                  $content["item_start"] = "\n" . '<item>';
                  if ( (GOOGLE_BASE_META_TITLE == 'true') && ($products->fields['metatags_title'] != '') ) {
                    $content["title"] = '<title>' . $google_base->google_base_xml_sanitizer($products->fields['metatags_title'], true) . '</title>';
                  } else {
                    $content["title"] = '<title>' . $google_base->google_base_xml_sanitizer($products->fields['products_name'], true) . '</title>'; 
                  }
                
                  if ($products->fields['manufacturers_name'] != '') {
                    $content["brand"] = '<g:brand>' . $google_base->google_base_xml_sanitizer($products->fields['manufacturers_name'], true) . '</g:brand>';
                  } else {
                    $content["brand"] = '<g:brand>' . $google_base->google_base_xml_sanitizer(STORE_NAME, true) . '</g:brand>';
                  }
                  if (GOOGLE_BASE_ASA == 'true' && $products->fields['products_condition'] != '') {
                    $content["condition"] = '<g:condition>' . $products->fields['products_condition'] . '</g:condition>';
                  } else {
                    $content["condition"] = '<g:condition>' . GOOGLE_FROOGLE_CONDITION . '</g:condition>';
                  }     
                  if (GOOGLE_BASE_PRODUCT_TYPE == 'top') {
                    $top_level = array_shift($product_type);
                    $content["product_type"] = (GOOGLE_BASE_ASA == 'true' ? ($products->fields['products_category'] != '' ? '<g:product_type>' . $google_base->google_base_xml_sanitizer($products->fields['products_category'], true) . '</g:product_type>' : '<g:product_type>' . $google_base->google_base_xml_sanitizer($top_level, true) . '</g:product_type>') : '<g:product_type>' . $google_base->google_base_xml_sanitizer($top_level, true) . '</g:product_type>');
                  } elseif (GOOGLE_BASE_PRODUCT_TYPE == 'bottom') {
                    $bottom_level = array_pop($product_type); // sets last category in array as bottom-level
                    $bottom_level = htmlentities($bottom_level);
                    $content["product_type"] = (GOOGLE_BASE_ASA == 'true' ? ($products->fields['products_category'] != '' ? '<g:product_type>' . $google_base->google_base_xml_sanitizer($products->fields['products_category'], true) . '</g:product_type>' : '<g:product_type>' . $google_base->google_base_xml_sanitizer($bottom_level, true) . '</g:product_type>') : '<g:product_type>' . $google_base->google_base_xml_sanitizer($bottom_level, true) . '</g:product_type>');
                  } elseif (GOOGLE_BASE_PRODUCT_TYPE == 'full') {
                    $full_path = implode(",", $product_type);
                    $full_path = htmlentities($full_path);
                    $content["product_type"] = (GOOGLE_BASE_ASA == 'true' ? ($products->fields['products_category'] != '' ? '<g:product_type>' . $google_base->google_base_xml_sanitizer($products->fields['products_category'], true) . '</g:product_type>' : '<g:product_type>' . $google_base->google_base_xml_sanitizer($full_path, true) . '</g:product_type>') : '<g:product_type>' . $google_base->google_base_xml_sanitizer($full_path, true) . '</g:product_type>');
                  }
                                
                  $content["expiration_date"] = '<g:expiration_date>' . $google_base->google_base_expiration_date($products->fields['base_date']) . '</g:expiration_date>';
                  
                  if (GOOGLE_FROOGLE_OFFER_ID != 'false') {
                    if (GOOGLE_FROOGLE_OFFER_ID == 'id') {
                      $content["id"] = '<g:id>' . $products->fields['products_id'] . '</g:id>';
                    } else if (GOOGLE_FROOGLE_OFFER_ID == 'model') {
                      $content["id"] = '<g:id>' . $google_base->google_base_sanita($products->fields['products_model'], true) . '</g:id>';
                    } else if (GOOGLE_FROOGLE_OFFER_ID == 'UPC' && GOOGLE_BASE_ASA == 'true') {
                      $content["id"] = '<g:id>' . $google_base->google_base_sanita($products->fields['products_upc']) . '</g:id>';
                    } else if (GOOGLE_FROOGLE_OFFER_ID == 'ISBN' && GOOGLE_BASE_ASA == 'true') {
                      $content["id"] = '<g:id>' . $google_base->google_base_sanita($products->fields['products_isbn']) . '</g:id>';
                    }
                  }
                  
                  //$content["guid"] = '<guid isPermaLink="false">' . $products->fields['products_id'] . '</guid>';
                  if ($products->fields['products_image'] != '') {
                    $content["image_link"] = '<g:image_link>' . $google_base->google_base_image_url($google_base->google_base_xml_sanitizer($products->fields['products_image'])) . '</g:image_link>';
                  } elseif (PRODUCTS_IMAGE_NO_IMAGE_STATUS == '1') {
                    $content["image_link"] = '<g:image_link>' . $google_base->google_base_xml_sanitizer(PRODUCTS_IMAGE_NO_IMAGE) . '</g:image_link>';
                  }
                  $content["link"] = '<link>' . $link . '</link>';
                  $content["price"] = '<g:price>' . number_format($price, 2, '.', '') . '</g:price>';
                  if (GOOGLE_FROOGLE_TAX_DISPLAY == 'true') {
                    $content["tax"] = '<g:tax>';
                    $content["tax"] .= '<g:rate>' . $tax_rate . '</g:rate>';
                    $content["tax"] .= '</g:tax>';
                  }
                  if ($products->fields['products_model'] != '') {
                    $content["mpn"] = '<g:mpn>' . $google_base->google_base_sanita($products->fields['products_model'], true) . '</g:mpn>';
                  }
                  if (GOOGLE_BASE_ASA == 'true') {
                    if ($products->fields['products_upc'] != '') {
                      $content["upc"] = '<g:upc>' . $google_base->google_base_sanita($products->fields['products_upc'], true) . '</g:upc>';
                    } elseif ($products->fields['products_isbn'] != '') {
                      $content["isbn"] = '<g:isbn>' . $google_base->google_base_sanita($products->fields['products_isbn'], true) . '</g:isbn>';
                    }
                  }
                  if (GOOGLE_FROOGLE_IN_STOCK == 'true') {
                    if ($products->fields['products_quantity'] > 0) {
                      $content["quantity"] = '<g:quantity>' . $products->fields['products_quantity'] . '</g:quantity>';
                    } else {
                      $content["quantity"] = '<g:quantity>' . (int)GOOGLE_BASE_DEFAULT_QUANTITY . '</g:quantity>';
                    }
                  }
                  if (GOOGLE_FROOGLE_LANGUAGE_DISPLAY == 'true') {
                    $content["language"] = '<g:language>' . $languages->fields['code'] . '</g:language>';  
                  }
                  if (GOOGLE_FROOGLE_CURRENCY_DISPLAY == 'true') {
                    $content["currency"] = '<g:currency>' . GOOGLE_FROOGLE_CURRENCY . '</g:currency>';
                  }
                  if(GOOGLE_FROOGLE_PICKUP != 'do not display') {
                    $content["pickup"] = '<g:pickup>' . GOOGLE_FROOGLE_PICKUP . '</g:pickup>';
                  }
                  if(GOOGLE_BASE_WEIGHT == 'true' && $products->fields['products_weight'] != '') {
                    $content["weight"] = '<g:weight>' . $products->fields['products_weight'] . ' ' . GOOGLE_BASE_UNITS . '</g:weight>';
                  }
                  if (defined('GOOGLE_BASE_PAYMENT_METHODS') && GOOGLE_BASE_PAYMENT_METHODS != '') { 
                    foreach($payments_accepted as $payment_accepted) {
                      $content[$payment_accepted] = '<g:payment_accepted>' . trim($payment_accepted) . '</g:payment_accepted>';
                    }
                  }
                  if (defined('GOOGLE_BASE_PAYMENT_NOTES') && GOOGLE_BASE_PAYMENT_NOTES != '') {
                    $content["payment_notes"] = '<g:payment_notes>' . trim(GOOGLE_BASE_PAYMENT_NOTES) . '</g:payment_notes>';
                  }
                  
                  if (defined('GOOGLE_BASE_SHIPPING_METHOD') && (GOOGLE_BASE_SHIPPING_METHOD != '') && (GOOGLE_BASE_SHIPPING_METHOD != 'none')) {
                    
                    $shipping_rate = $google_base->shipping_rate(GOOGLE_BASE_SHIPPING_METHOD, $percategory, $freerules, GOOGLE_BASE_RATE_ZONE, $products->fields['products_weight'], $price, $products->fields['products_id']);
                    
                    if ((float)$shipping_rate >= 0) {
                      $content["shipping"] = '<g:shipping>';
                      if (GOOGLE_BASE_SHIPPING_COUNTRY != '') {
                        $content["shipping"] .= '  <g:country>' . $google_base->get_countries_iso_code_2(GOOGLE_BASE_SHIPPING_COUNTRY) . '</g:country>';
                      }
                      
                      if (GOOGLE_BASE_SHIPPING_REGION != '') {
                        $content["shipping"] .= '  <g:region>' . GOOGLE_BASE_SHIPPING_REGION . '</g:region>';
                      }
                      if (GOOGLE_BASE_SHIPPING_SERVICE != '') {
                        $content["shipping"] .= '  <g:service>' . GOOGLE_BASE_SHIPPING_SERVICE . '</g:service>';
                      }
                      $content["shipping"] .= '  <g:price>' . $shipping_rate . '</g:price>';
                      $content["shipping"] .= '</g:shipping>';
                    }
                  }
                  if ($products->fields['products_description'] != '') {
                    $content["description"] = '<description>' . $google_base->google_base_xml_sanitizer($products_description, true) . '</description>';
                  }
                  $content["item_end"] = '</item>';
                  $google_base->google_base_fwrite($content, "a", $type);
                }
                if (GOOGLE_BASE_DEBUG == 'true') {
                  if ($success) {
                    echo ' - success';
                  } else {
                    echo ' - failed';
                  }
                  echo '<br />';
                }
              }
              $products->MoveNext();
            }
            $content = array();
            $content["channel"] = "\n" . '</channel>';
            $content["rss"] = '</rss>';
            $google_base->google_base_fwrite($content, "a", $type);
            chmod($outfile, 0655);
          break;
        
        case "documents":
          $products_query = "SELECT distinct(p.products_id), p.products_model, pd.products_name, pd.products_description, p.products_image, p.products_type, GREATEST(p.products_date_added, IFNULL(p.products_last_modified, 0), IFNULL(p.products_date_available, 0)) AS base_date, m.manufacturers_name, pt.type_handler
                           FROM " . TABLE_PRODUCTS . " p
                             LEFT JOIN " . TABLE_MANUFACTURERS . " m ON (p.manufacturers_id = m.manufacturers_id)
                             LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON (p.products_id = pd.products_id)
                             LEFT JOIN " . TABLE_PRODUCT_TYPES . " pt ON (p.products_type=pt.type_id)
                           WHERE p.products_status = 1
                              AND p.products_type = 3
                             AND p.product_is_call <> 1
                             AND p.product_is_free <> 1
                             AND pd.language_id = " . $languages->fields['languages_id'] ."
                           GROUP BY p.products_id
                           ORDER BY p.products_last_modified DESC";
          
          $products = $db->Execute($products_query);
          $tax_rate = array();
            while (!$products->EOF && !$max_limit) { // run until end of file or until maximum number of products reached
              list($categories_list, $cPath) = $google_base->google_base_get_category($products->fields['products_id']);
              //if ($google_base->numinix_categories_check((GOOGLE_BASE_POS_CATEGORIES), $categories_list, 1) == true && $google_base->numinix_categories_check((GOOGLE_BASE_NEG_CATEGORIES), $categories_list, 2) == false && ($google_base_start_counter >= GOOGLE_BASE_START_PRODUCTS)) { // check to see if category limits are set.  If so, only process for those categories.
              if ($google_base->numinix_categories_check(GOOGLE_BASE_POS_CATEGORIES, $products->fields['products_id'], 1) == true && $google_base->numinix_categories_check(GOOGLE_BASE_NEG_CATEGORIES, $products->fields['products_id'], 2) == false && ($google_base_start_counter >= $starting_point)) { // check to see if category limits are set.  If so, only process for those categories.  
                $anti_timeout_counter++;
                if ($anti_timeout_counter == GOOGLE_FROOGLE_MAX_PRODUCTS && GOOGLE_FROOGLE_MAX_PRODUCTS != 0) { // if counter is greater than or equal to maximum products
                  $max_limit = true; // then max products reached
                } else {
                  $max_limit = false; // otherwise, max products not reached
                }              
                              
                if (GOOGLE_BASE_MAGIC_SEO_URLS == 'true') {
                  include(DIR_WS_INCLUDES . 'modules/msu_ao_2.php');
                } else { // default
                  $link = ($products->fields['type_handler'] ? $products->fields['type_handler'] : 'product') . '_info';
                  $cPath_href = (GOOGLE_FROOGLE_USE_CPATH == 'true' ? 'cPath=' . $cPath . '&' : '');
                  $link = zen_href_link($link, $cPath_href . 'products_id=' . (int)$products->fields['products_id'] . $product_url_add, 'NONSSL', false);
                }
                            
                              
                $content = array();
                $content["item_start"] = "\n" . '<item>';
                $content["title"] = '<title>' . $google_base->google_base_xml_sanitizer($products->fields['products_name'], true) . '</title>'; 
                $content["author"] = '<g:author>' . $google_base->google_base_xml_sanitizer($products->fields['manufacturers_name']) . '</g:author>';
                $content["expiration_date"] = '<g:expiration_date>' . $google_base->google_base_expiration_date($products->fields['base_date']) . '</g:expiration_date>';

                if (GOOGLE_FROOGLE_OFFER_ID != 'false') {
                  if (GOOGLE_FROOGLE_OFFER_ID == 'id') {
                    $content["id"] = '<g:id>' . $products->fields['products_id'] . '</g:id>';
                  } else if (GOOGLE_FROOGLE_OFFER_ID == 'model') {
                    $content["id"] = '<g:id>' . $google_base->google_base_sanita($products->fields['products_model']) . '</g:id>';
                  }
                }
    
                //$content["guid"] = '<guid isPermaLink="false">' . $products->fields['products_id'] . '</guid>';
                if ($products->fields['products_image'] != '') {
                  $content["image_link"] = '<g:image_link>' . $google_base->google_base_image_url($google_base->google_base_xml_sanitizer($products->fields['products_image'])) . '</g:image_link>';
                } elseif (PRODUCTS_IMAGE_NO_IMAGE_STATUS == '1') {
                  $content["image_link"] = '<g:image_link>' . $google_base->google_base_xml_sanitizer(HTTP_SERVER . DIR_WS_IMAGES . PRODUCTS_IMAGE_NO_IMAGE) . '</g:image_link>';
                }
                $content["link"] = '<link>' . $link . '</link>';
                if (GOOGLE_FROOGLE_LANGUAGE_DISPLAY == 'true') {
                  $content["language"] = '<g:language>' . $languages->fields['code'] . '</g:language>';  
                }
                $content["publication_name"] = '<g:publication_name>The ' . STORE_NAME . ' Website</g:publication_name>';
                $content["publish_date"] = '<g:publish_date>' . $products->fields['products_date_added'] . '</g:publish_date>'; 
                $content["description"] = '<description>' . $google_base->google_base_xml_sanitizer($products->fields['products_description'], true) . '</description>';
                $content["item_end"] = '</item>';
                $google_base->google_base_fwrite($content, "a", $type);
              }
              $products->MoveNext();
            }
            $content = array();
            $content["channel"] = "\n" . '</channel>';
            $content["rss"] = '</rss>';
            $google_base->google_base_fwrite($content, "a", $type);
            chmod($outfile, 0655);
          break;
        case 'news':
          if (TABLE_NEWS_ARTICLES == 'TABLE_NEWS_ARTICLES') {
            echo '<br/>Error: News and Article Module not Installed!';
            die();
          }
          $news_query = "SELECT na.article_id, na.news_image, na.news_date_published,
                                nat.news_article_name, nat.news_article_text,
                                nau.author_name
                         FROM " . TABLE_NEWS_ARTICLES . " na
                         LEFT JOIN " . TABLE_NEWS_ARTICLES_TEXT . " nat ON (na.article_id = nat.article_id)
                         LEFT JOIN " . TABLE_NEWS_AUTHORS . " nau ON (na.authors_id = nau.authors_id)
                         WHERE na.news_status = 1
                         AND nat.language_id = " . (int)$languages->fields['languages_id'] . "
                         ORDER BY na.news_date_published DESC";
          $news = $db->Execute($news_query);
          while (!$news->EOF && !$max_limit) { // run until end of file or until maximum number of products reached   
            $anti_timeout_counter++;
            if ($anti_timeout_counter == GOOGLE_FROOGLE_MAX_PRODUCTS && GOOGLE_FROOGLE_MAX_PRODUCTS != 0) { // if counter is greater than or equal to maximum products
              $max_limit = true; // then max products reached
            } else {
              $max_limit = false; // otherwise, max products not reached
            }
            $date_published = substr($news->fields['news_date_published'], 0, 10);
            $content = array();
            $content["item_start"] = "\n" . '<item>';
            $content["title"] = '<title>' . $google_base->google_base_xml_sanitizer($news->fields['news_article_name'], true) . '</title>'; 
            if ($news->fields['author_name'] != '') { 
              $content["author"] = '<g:author>' . $google_base->google_base_xml_sanitizer($news->fields['author_name'], true) . '</g:author>';
            }
            $content["id"] = '<g:id>' . $news->fields['article_id'] . '</g:id>';
            if ($news->fields['news_image'] != '') {
              $content["image_link"] = '<g:image_link>' . $google_base->google_base_image_url($google_base->google_base_xml_sanitizer($news->fields['news_image'])) . '</g:image_link>';
            }
            $content["link"] = '<link>' . $google_base->google_base_news_link($news->fields['article_id']) . '</link>';
            $content["publish_date"] = '<g:publish_date>' . $date_published . '</g:publish_date>'; 
            $content["description"] = '<description>' . $google_base->google_base_xml_sanitizer($news->fields['news_article_text'], true) . '</description>';
            $content["news_source"] = '<g:news_source>' . HTTP_SERVER . DIR_WS_CATALOG . '</g:news_source>';
            $content["item_end"] = '</item>';
            $google_base->google_base_fwrite($content, "a", $type);
            $news->MoveNext();
          }
            $content = array();
            $content["channel"] = "\n" . '</channel>';
            $content["rss"] = '</rss>';
            $google_base->google_base_fwrite($content, "a", $type);
            chmod($outfile, 0655);
          break;
      }
    } else {
      echo ERROR_GOOGLE_FROOGLE_OPEN_FILE . NL;
      die;
    }
    
    $timer_feed = $google_base->microtime_float()-$stimer_feed;
    
    echo TEXT_GOOGLE_FROOGLE_FEED_COMPLETE . ' ' . GOOGLE_FROOGLE_TIME_TAKEN . ' ' . sprintf("%f " . TEXT_GOOGLE_FROOGLE_FEED_SECONDS, number_format($timer_feed, 6) ) . ' ' . $anti_timeout_counter . TEXT_GOOGLE_FROOGLE_FEED_RECORDS . NL;  
  }

  if (isset($upload) && $upload == "yes") {
    echo TEXT_GOOGLE_FROOGLE_UPLOAD_STARTED . NL;
    if($google_base->ftp_file_upload(GOOGLE_FROOGLE_SERVER, GOOGLE_FROOGLE_USERNAME, GOOGLE_FROOGLE_PASSWORD, $outfile)) {
      echo TEXT_GOOGLE_FROOGLE_UPLOAD_OK . NL;
      $db->execute("update " . TABLE_CONFIGURATION . " set configuration_value = '" . date("Y/m/d H:i:s") . "' where configuration_key='GOOGLE_FROOGLE_UPLOADED_DATE'");
    } else {
      echo TEXT_GOOGLE_FROOGLE_UPLOAD_FAILED . NL;
    }
  }
?>