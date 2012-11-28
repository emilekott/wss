<?php
if (isset($ep_debug)) $sdltimer = ep_timer();
$epCategories =& new EpCategories();
$export_error = array();
$fieldmap = array();
switch ($_GET['export'])
{
case 'store':  
  $ep_file_types = array();
  $result = ep_query('SELECT type_id, type_name FROM ' . TABLE_PRODUCT_TYPES);
  for ($i = 0; $product_type_row = $result->getRow($i, ECLIPSE_DB_ASSOC); $i++)
  {
    $ep_file_types[$product_type_row['type_id']] = $product_type_row['type_name'];
  }
  $iii = 0;
  $filelayout = array(
                'v_products_id'    => $iii++,
                'v_products_model'    => $iii++,
                'v_products_type'    => $iii++,
                'v_products_image'    => $iii++,
                );
  foreach ($ep_languages as $key => $lang)
  {
    $filelayout  = array_merge($filelayout , array(
                  'v_products_name_' . $lang['code']    => $iii++,
                  'v_products_description_' . $lang['code'] => $iii++,
                  ));
    if (isset($ep_supported_mods['psd']))
    {
      $filelayout  = array_merge($filelayout , array(
                    'v_products_short_desc_' . $lang['code']  => $iii++,
                    ));
    }
    $filelayout  = array_merge($filelayout , array(
    'v_products_url_' . $lang['code'] => $iii++,
    ));
  }

  $header_array = array(
                  'v_specials_price'    => $iii++,
                  'v_specials_date_available'     => $iii++,
                  'v_specials_expires_date'     => $iii++,
                  'v_products_price'    => $iii++,
                  'v_products_weight'   => $iii++,
                  'v_products_date_available'      => $iii++,
                  'v_products_date_added'      => $iii++,
                  'v_products_quantity'   => $iii++,
                  );
  $header_array['v_manufacturers_name'] = $iii++;
  $filelayout = array_merge($filelayout, $header_array);
  $filelayout['ptc_categories_index_path'] = $iii++;
  $filelayout['ptc_categories_destination_path'] = $iii++;
  $filelayout['ptc_categories_linked_path'] = $iii++;
  $filelayout = array_merge($filelayout, array(
    'v_tax_class_title'   => $iii++,
    'v_products_status'      => $iii++,
    ));
  $filelayout_sql = 'SELECT
    p.products_id as v_products_id,
    p.products_type as v_products_type,
    p.products_model as v_products_model,
    p.products_image as v_products_image,
    p.products_price as v_products_price,
    p.products_weight as v_products_weight,
    p.products_date_available as v_products_date_available,
    p.products_date_added as v_products_date_added,
    p.products_tax_class_id as v_products_tax_class_id,
    p.products_quantity as v_products_quantity,
    p.manufacturers_id as v_manufacturers_id,
    p.master_categories_id as v_master_categories_id,
    c.categories_id as v_categories_id,
    p.products_status as v_products_status
    FROM
    (' . TABLE_PRODUCTS . ' as p LEFT JOIN 
    ' . TABLE_PRODUCTS_TO_CATEGORIES . ' as ptc ON p.products_id = ptc.products_id) LEFT JOIN 
    ' . TABLE_CATEGORIES . ' as c ON ptc.categories_id = c.categories_id';
  $dl_filename = EASYPOPULATE_EXPORT_PREFIX_STORE;
  break;
case 'extstore':
  $ep_file_types = array();
  $result = ep_query('SELECT type_id, type_name FROM ' . TABLE_PRODUCT_TYPES);
  for ($i = 0; $product_type_row = $result->getRow($i, ECLIPSE_DB_ASSOC); $i++)
  {
    $ep_file_types[$product_type_row['type_id']] = $product_type_row['type_name'];
  }
  $iii = 0;
  $filelayout = array(
                'v_products_id' => $iii++,
                'v_products_model' => $iii++,
                'v_products_type' => $iii++,
                'v_products_image' => $iii++
                );
  $filelayout['v_manufacturers_name'] = $iii++;
  foreach ($ep_languages as $key => $lang)
  {
    $filelayout['v_products_name_' . $lang['code']] = $iii++;
    $filelayout['v_products_description_' . $lang['code']] = $iii++;
    if (isset($ep_supported_mods['psd']))
    {
      $filelayout['v_products_short_desc_' . $lang['code']] = $iii++;
    }
    $filelayout['v_products_url_' . $lang['code']] = $iii++;
  }
$filelayout = array_merge($filelayout, array(
                'v_specials_price' => $iii++,
                'v_specials_date_available' => $iii++,
                'v_specials_expires_date' => $iii++,
                'v_products_price' => $iii++,
                'v_products_virtual' => $iii++,
                'v_products_weight' => $iii++,
                'v_products_date_available' => $iii++,
                'v_products_date_added' => $iii++,
                'v_products_quantity' => $iii++,
                'v_products_ordered' => $iii++,
                'v_products_quantity_order_min' => $iii++,
                'v_products_quantity_order_units' => $iii++,
                'v_products_priced_by_attribute' => $iii++,
                'v_product_is_free' => $iii++,
                'v_product_is_call' => $iii++,
                'v_products_quantity_mixed' => $iii++,
                'v_product_is_always_free_shipping' => $iii++,
                'v_products_qty_box_status' => $iii++,
                'v_products_quantity_order_max' => $iii++,
                'v_products_sort_order' => $iii++,
                'v_products_discount_type' => $iii++,
                'v_products_discount_type_from' => $iii++,
                'v_products_price_sorter' => $iii++,
                'v_master_categories_id' => $iii++,
                'v_products_mixed_discount_quantity' => $iii++,
                'v_metatags_title_status'    => $iii++,
                'v_metatags_products_name_status'    => $iii++,
                'v_metatags_model_status'    => $iii++,
                'v_metatags_price_status'    => $iii++,
                'v_metatags_title_tagline_status'    => $iii++
                ));
/*
  foreach ($ep_languages as $key => $lang)
  {
    $filelayout  = array_merge($filelayout, array(
                  'v_metatags_title_' . $lang['code']    => $iii++,
                  'v_metatags_keywords_' . $lang['code']    => $iii++,
                  'v_metatags_description_' . $lang['code']    => $iii++
                  ));
  }
*/
  $filelayout['ptc_categories_index_path'] = $iii++;
  $filelayout['ptc_categories_destination_path'] = $iii++;
  $filelayout['ptc_categories_linked_path'] = $iii++;
  $filelayout['v_tax_class_title'] = $iii++;
  $filelayout['v_products_status'] = $iii++;
  $filelayout_sql = 'SELECT
    p.products_id as v_products_id,
    p.products_type as v_products_type,
    p.products_quantity as v_products_quantity,
    p.products_model as v_products_model,
    p.products_image as v_products_image,
    p.products_price as v_products_price,
    p.products_virtual as v_products_virtual,
    p.products_date_added as v_products_date_added,
    p.products_date_available as v_products_date_available,
    p.products_weight as v_products_weight,
    p.products_status as v_products_status,
    p.products_tax_class_id as v_products_tax_class_id,
    p.manufacturers_id as v_manufacturers_id,
    p.products_ordered as v_products_ordered,
    p.products_quantity_order_min as v_products_quantity_order_min,
    p.products_quantity_order_units as v_products_quantity_order_units,
    p.products_priced_by_attribute as v_products_priced_by_attribute,
    p.product_is_free as v_product_is_free,
    p.product_is_call as v_product_is_call,
    p.products_quantity_mixed as v_products_quantity_mixed,
    p.product_is_always_free_shipping as v_product_is_always_free_shipping,
    p.products_qty_box_status as v_products_qty_box_status,
    p.products_quantity_order_max as v_products_quantity_order_max,
    p.products_sort_order as v_products_sort_order,
    p.products_discount_type as v_products_discount_type,
    p.products_discount_type_from as v_products_discount_type_from,
    p.products_price_sorter as v_products_price_sorter,
    p.master_categories_id as v_master_categories_id,
    p.products_mixed_discount_quantity as v_products_mixed_discount_quantity,
    p.metatags_title_status as v_metatags_title_status,
    p.metatags_products_name_status as v_metatags_products_name_status,
    p.metatags_model_status as v_metatags_model_status,
    p.metatags_price_status as v_metatags_price_status,
    p.metatags_title_tagline_status as v_metatags_title_tagline_status,
    c.categories_id as v_categories_id
    FROM
    (' . TABLE_PRODUCTS . ' as p LEFT JOIN 
    ' . TABLE_PRODUCTS_TO_CATEGORIES . ' as ptc ON p.products_id = ptc.products_id) LEFT JOIN 
    ' . TABLE_CATEGORIES . ' as c ON ptc.categories_id = c.categories_id';
  $dl_filename = EASYPOPULATE_EXPORT_PREFIX_EXTENDED_STORE;
  break;
case 'priceqty':
  $iii = 0;
  $filelayout = array(
    'v_products_id'    => $iii++,
    'v_products_model'    => $iii++,
    'v_specials_price'    => $iii++,
    'v_specials_date_available'     => $iii++,
    'v_specials_expires_date'     => $iii++,
    'v_products_price'    => $iii++,
    'v_products_quantity'   => $iii++
      );
  $filelayout_sql = "SELECT
    p.products_id as v_products_id,
    p.products_model as v_products_model,
    p.products_price as v_products_price,
    p.products_tax_class_id as v_products_tax_class_id,
    p.products_quantity as v_products_quantity
    FROM
    (".TABLE_PRODUCTS." as p)
    ";
  $dl_filename = EASYPOPULATE_EXPORT_PREFIX_PRICEQTY;
  break;
case 'products_categories':
  $iii = 0;
  $filelayout = array(
    'v_products_id'    => $iii++,
    'v_products_model'    => $iii++,
  );
  $filelayout['ptc_categories_index_path'] = $iii++;
  $filelayout['ptc_categories_destination_path'] = $iii++;
  $filelayout['ptc_categories_linked_path'] = $iii++;
  $filelayout_sql = 'SELECT
    p.products_id as v_products_id,
    p.products_model as v_products_model,
    p.master_categories_id as v_master_categories_id,
    c.categories_id as v_categories_id
    FROM
    (' . TABLE_PRODUCTS . ' as p LEFT JOIN 
    ' . TABLE_PRODUCTS_TO_CATEGORIES . ' as ptc ON p.products_id = ptc.products_id) LEFT JOIN 
    ' . TABLE_CATEGORIES . ' as c ON ptc.categories_id = c.categories_id';
  $dl_filename = EASYPOPULATE_EXPORT_PREFIX_PRODUCTS_CATEGORIES;
  break;
case 'googlebase':
  $ep_googlebase = array();
  include_once DIR_EP_INCLUDES . 'easypopulate_googlebase.php';
  if (count($ep_googlebase) >= 1)
  {
    $filelayout = array();
    $iii = 0;
    foreach ($ep_googlebase as $name => $val)
    {
      $filelayout[$name] = $iii++;
    }
  }
  else
  {
  }
  $google_not_null = array(
  'brand', 'manufacturer'
  );
  foreach ($google_not_null as $var)
  {
    if (isset($ep_googlebase[$var]))
      $ep_googlebase[$var] = '';
  }
  $filelayout_sql = 'SELECT
    p.products_id as id,
    p.products_id as p_products_id,
    p.products_model as model_number,
    p.products_model as manufacturer_id,
    p.products_image as p_products_image,
    p.products_price as p_products_price,
    p.products_weight as weight,
    p.products_date_added as p_products_date_added,
    p.products_date_available as p_products_date_available,
    p.products_tax_class_id as p_tax_class_id,
    p.products_quantity as quantity,
    p.manufacturers_id as p_manufacturers_id,
    p.master_categories_id as p_master_categories_id,
    m.manufacturers_name as manufacturer,
    m.manufacturers_name as brand,
    pd.products_name as title,
    pd.products_description as description,
    pd.products_url as pd_products_url,
    c.categories_id as c_categories_id,
    c.parent_id as c_parent_id,
    cd.categories_name as cd_categories_name
    FROM 
    ' . TABLE_PRODUCTS . ' as p
    LEFT JOIN ' . TABLE_CATEGORIES . ' as c 
    ON p.master_categories_id = c.categories_id
    LEFT JOIN ' . TABLE_CATEGORIES_DESCRIPTION . ' as cd
    ON p.master_categories_id = cd.categories_id AND
    cd.language_id = ' . (int)$language_id_default . '
    LEFT JOIN ' . TABLE_MANUFACTURERS . ' as m 
    ON p.manufacturers_id = m.manufacturers_id,
    ' . TABLE_PRODUCTS_DESCRIPTION . ' as pd
     WHERE
    p.products_id = pd.products_id AND
    pd.language_id = ' . (int)$language_id_default . ' AND
    p.products_status = 1
    ';
  $rh =& ep_query('SELECT configuration_value FROM ' . TABLE_CONFIGURATION . " WHERE configuration_key = 'DEFAULT_CURRENCY'");
  $ep_default_currency = $rh->getRow(0, ECLIPSE_DB_ASSOC);
  $dl_filename = EASYPOPULATE_EXPORT_PREFIX_GOOGLEBASE;
  break;
case 'attrib':
  if (isset($ep_debug)) $attrstart = ep_timer();
  $attribute_options_array = array();
  $attribute_options_values =& ep_query("select distinct products_options_id from (" . TABLE_PRODUCTS_OPTIONS . ") order by products_options_id");
  for ($i = 0; $attribute_options = $attribute_options_values->getRow($i, ECLIPSE_DB_ASSOC); $i++)
  {
    $attribute_options_array[] = array('products_options_id' => $attribute_options['products_options_id']);
  }
  if (isset($ep_debug)) ep_timer($attrstart,'Attributes');
  if (isset($ep_debug))
  {
    $attr3start = ep_timer();
  }
  $iii = 0;
  $filelayout = array(
                'v_products_id'    => $iii++,
                'v_products_model'    => $iii++
                );
  $header_array = array();
  $attribute_options_count = 1;
  foreach ($attribute_options_array as $attribute_options_values)
  {
    $key1 = 'v_attribute_options_id_' . $attribute_options_count;
    $header_array[$key1] = $iii++;
    for (reset($ep_languages); list($l_id, $arr) = each($ep_languages);)
    {
      $key2 = 'v_attribute_options_name_' . $attribute_options_count . '_' . $l_id;
      $header_array[$key2] = $iii++;
    }
    $attribute_values_values  =& ep_query("SELECT products_options_values_id  FROM (" . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . ") WHERE products_options_id = '" . (int)$attribute_options_values['products_options_id'] . "' ORDER BY products_options_values_id");
    $attribute_values_count = 1;
    for ($i = 0; $attribute_values = $attribute_values_values->getRow($i, ECLIPSE_DB_ASSOC); $i++)
    {
      $key3 = 'v_attribute_values_id_' . $attribute_options_count . '_' . $attribute_values_count;
      $header_array[$key3] = $iii++;
      $key4 = 'v_attribute_values_price_' . $attribute_options_count . '_' . $attribute_values_count;
      $header_array[$key4] = $iii++;
      for (reset($ep_languages); list($l_id, $arr) = each($ep_languages);)
      {
        $key5 = 'v_attribute_values_name_' . $attribute_options_count . '_' . $attribute_values_count . '_' . $l_id;
        $header_array[$key5] = $iii++;
      }
      $attribute_values_count++;
    }
    $attribute_options_count++;
  }
  $filelayout = array_merge($filelayout, $header_array);
  $filelayout_sql = 'SELECT
    products_id as v_products_id,
    products_model as v_products_model
    FROM
    (' . TABLE_PRODUCTS . ')
    ';
  if (isset($ep_debug))
  {
    ep_timer($attr3start,'Attributes 3');
  }
  $dl_filename = EASYPOPULATE_EXPORT_PREFIX_ATTRIBUTES;
  break;
case 'meta':
  $iii = 0;
  $filelayout = array(
    'v_products_id'    => $iii++,
    'v_products_model'    => $iii++,
    'v_products_name_' . $ep_languages[$language_id_default]['code']    => $iii++,
    'v_metatags_title_status'    => $iii++,
    'v_metatags_products_name_status'    => $iii++,
    'v_metatags_model_status'    => $iii++,
    'v_metatags_price_status'    => $iii++,
    'v_metatags_title_tagline_status'    => $iii++,
      );
  foreach ($ep_languages as $key => $lang)
  {
    $filelayout  = array_merge($filelayout , array(
                  'v_metatags_title_' . $lang['code']    => $iii++,
                  'v_metatags_keywords_' . $lang['code']    => $iii++,
                  'v_metatags_description_' . $lang['code']    => $iii++,
                  ));
  }
  $filelayout_sql = 'SELECT
    products_id as v_products_id,
    products_model as v_products_model,
    metatags_title_status as v_metatags_title_status,
    metatags_products_name_status as v_metatags_products_name_status,
    metatags_model_status as v_metatags_model_status,
    metatags_price_status as v_metatags_price_status,
    metatags_title_tagline_status as v_metatags_title_tagline_status
    FROM
    (' . TABLE_PRODUCTS . ')
    ';
  $dl_filename = EASYPOPULATE_EXPORT_PREFIX_META;
  break;
case 'categories':
  $iii = 0;
  $filelayout = array('c_categories_index_path' => $iii++,
                'c_categories_destination_path' => $iii++,
                'c_categories_image' => $iii++,
                'c_sort_order' => $iii++,
                'c_categories_status' => $iii++);
  foreach ($ep_languages as $key => $lang)
  {
    $filelayout['cd_categories_name_' . $lang['code']] = $iii++;
    $filelayout['cd_categories_description_' . $lang['code']] = $iii++;
  }
  $products_fields = ep_table_fields(TABLE_CATEGORIES);
  $filelayout_sql = 'SELECT ';
  foreach ($products_fields as $field_name)
  {
    $filelayout_sql .= $field_name . ' as c_' . $field_name . ', ';
  }
  $filelayout_sql = rtrim($filelayout_sql, ', ');
  $filelayout_sql .= ' FROM ' . TABLE_CATEGORIES . ' ORDER BY parent_id, sort_order';
  $dl_filename = EASYPOPULATE_EXPORT_PREFIX_CATEGORIES;
  break;
}
if (EASYPOPULATE_CONFIG_PRIMARY_INDEX == 'products_model')
{
  unset($filelayout['v_products_id']);
}
ep_flush('&nbsp;' . EASYPOPULATE_FLUSH_PROGRESS_START);
$filestring = "";
if ($_GET['type'] == 'template')
{
  $filelayout_sql .= ' LIMIT 1,1';
}
if (count($fileheaders) != 0 )
{
  $filelayout_header = $fileheaders;
}
else
{
  $filelayout_header = $filelayout;
}
for (reset($filelayout_header); list($key, $value) = each($filelayout_header);)
{
  $filestring .= $key . $separator;
}
$filestring = substr($filestring, 0, strlen($filestring)-1);
if ($_GET['export'] == 'googlebase')
{
  $endofrow = "\n";
  $separator = "\t";
}
else
{
  $endofrow = $separator . EASYPOPULATE_CONFIG_EXPLICIT_EOR . "\n";
}
$filestring .= $endofrow;
if ($_GET['export'] == 'categories')
{
  $error_detect = false;
  $cat_error_count = 0;
  $result  =& ep_query($filelayout_sql);
  for ($i = 0; $row = $result->getRow($i, ECLIPSE_DB_ASSOC); $i++)
  {
    $row['c_categories_index_path'] = $epCategories->catPaths[$row['c_categories_id']];
    $row['c_categories_destination_path'] = '';
    foreach ($ep_languages as $key => $lang)
    {
      $cat_desc_result =& ep_query('SELECT * FROM ' . TABLE_CATEGORIES_DESCRIPTION . ' WHERE categories_id = ' . (int)$row['c_categories_id'] . ' AND language_id = ' . (int)$lang['id']);
      if ($cat_desc_result->getRowCount() === 0 && $row['c_categories_index_path'] != '')
      {
        $cat_path_array = explode(EASYPOPULATE_CONFIG_CATEGORIES_PATH_SEPARATOR, $row['c_categories_index_path']);
        $cat_name = trim($cat_path_array[count($cat_path_array)-1]);
        ep_query('INSERT INTO ' . TABLE_CATEGORIES_DESCRIPTION . ' (categories_id, language_id, categories_name) VALUES (' . (int)$row['c_categories_id'] . ', ' . (int)$lang['id'] . ', ' . ep_db_input($cat_name) . ')');
        $cat_desc_result =& ep_query('SELECT * FROM ' . TABLE_CATEGORIES_DESCRIPTION . ' WHERE categories_id = ' . (int)$row['c_categories_id'] . ' AND language_id = ' . (int)$lang['id']);
        $error_detect = EASYPOPULATE_MSGSTACK_CATEGORIES_EXPORT_FIX;
        $cat_error_count++;
      }
      $cat_desc_row = $cat_desc_result->getRow(0, ECLIPSE_DB_ASSOC);
      if ($cat_desc_row['categories_name'] == '')
      {
        $error_detect = EASYPOPULATE_MSGSTACK_CATEGORIES_EXPORT_ALERT;
        $cat_error_count++;
      }
      $row['cd_categories_name_' . $lang['code']] = $cat_desc_row['categories_name'];
      $row['cd_categories_description_' . $lang['code']] = $cat_desc_row['categories_description'];
      unset($cat_path_array);
    }
    
    $row_export = '';
    for (reset($filelayout); list($key, $value) = each($filelayout);)
    {
      $row_export .= str_replace(array("\r", "\n", "\t"), " ", $row[$key]) . $separator;
    }
    $row_export = substr($row_export, 0, strlen($row_export) - 1) . $endofrow;
    $filestring .= $row_export;
    ep_flush();
  }
  if ($error_detect)
    $epMsgStack->add(sprintf($error_detect, $cat_error_count), 'caution');
  unset($error_detect);
}
else if ($_GET['export'] == 'googlebase')
{
  $export_error['googlebase'] = array();
  $result  =& ep_query($filelayout_sql);
  $attr4time = 0;
  for ($i = 0; $row = $result->getRow($i, ECLIPSE_DB_ASSOC); $i++)
  {
    foreach ($ep_googlebase as $name => $val)
    {
      if (!isset($row[$name]))
        $row[$name] = $val;
    }
    $ep_google_target = array("\"\"", "\t", "\r", "\n", "<br /><br />", "<br />", "</p>", "</h1>", "</li></ul>", "</li></ol>", "</li>", "<ul>");
    $ep_google_replace = array("\"", "", "", "", " ", " ", " ", " ", ". ", ". ", "; ", " ");
    if (isset($filelayout['title']))
    {
      if (!ep_empty($row['title']))
        $row['title'] = substr(html_entity_decode(strip_tags(str_replace($ep_google_target, $ep_google_replace, $row['title']))), 0, 65536);
    }
    if (isset($filelayout['description']))
    {
      if (!ep_empty($row['description']))
        $row['description'] = substr(html_entity_decode(strip_tags(str_replace($ep_google_target, $ep_google_replace, $row['description']))), 0, 65536);
    }
    if (isset($filelayout['image_link']))
    {
      /* image path code thanks to Tim Kroeger - www.breakmyzencart.com */
      $products_image = (($row['p_products_image'] == PRODUCTS_IMAGE_NO_IMAGE) ? '' : $row['p_products_image']);
      $products_image_extension = substr($products_image, strrpos($products_image, '.'));
      $products_image_base = ereg_replace($products_image_extension . '$', '', $products_image);
      $products_image_medium = $products_image_base . IMAGE_SUFFIX_MEDIUM . $products_image_extension;
      $products_image_large = $products_image_base . IMAGE_SUFFIX_LARGE . $products_image_extension;
      if (!file_exists(DIR_FS_CATALOG_IMAGES . 'large/' . $products_image_large))
      {
        if (!file_exists(DIR_FS_CATALOG_IMAGES . 'medium/' . $products_image_medium))
        {
         $row['image_link'] = (($products_image == '') ? '' : DIR_WS_CATALOG_IMAGES . $products_image);
        }
        else
        {
          $row['image_link'] = DIR_WS_CATALOG_IMAGES . 'medium/' . $products_image_medium;
        }
      }
      else
      {
        $row['image_link'] = DIR_WS_CATALOG_IMAGES . 'large/' . $products_image_large;
      }
    }
    if (isset($filelayout['currency']))
    {
      $row['currency'] = $ep_default_currency['configuration_value'];
    }
    if (isset($filelayout['price']))
    {
      $sql2 = "SELECT
          specials_new_products_price
        FROM
          (" . TABLE_SPECIALS . ")
        WHERE
          products_id = " . $row['p_products_id'] . " and
          status = 1 and
          expires_date < CURRENT_TIMESTAMP
          ORDER BY
          specials_id DESC"
        ;
      $result2  =& ep_query($sql2);
      if ($result2->getRowCount() > 0)
      {
        $row2 = $result2->getRow(0, ECLIPSE_DB_ASSOC);
        $row['p_products_price']  = $row2['specials_new_products_price'];
      }
      $row['price'] = round($row['p_products_price'] + ($googlebase_price_inc_tax * $row['p_products_price'] * ep_get_tax_class_rate($row['p_tax_class_id']) / 100),4);
    }
    if (isset($filelayout['link']))
    {
      $row['link'] = rtrim(HTTP_CATALOG_SERVER, '/') . '/index.php?main_page=' . FILENAME_PRODUCT_INFO . '&products_id=' . $row['p_products_id'];
    }
    if (isset($filelayout['label']))
    {
      if ($row['p_master_categories_id'] == '0')
      {
        if($row['c_categories_id'] = ep_update_cat_ids(array($row['p_products_id'])))
        {
          $row['p_master_categories_id'] = $row['c_categories_id'];
          $export_error['googlebase']['master_cat_id_missing'] = EASYPOPULATE_MSGSTACK_FILE_GOOGLEBASE_EXPORT_ERROR;
        }
        else
        {
        }
      }
      $cat_path_array = explode(EASYPOPULATE_CONFIG_CATEGORIES_PATH_SEPARATOR, $epCategories->catPaths[$row['c_categories_id']]);
      $row['label'] = implode(",", array_slice($cat_path_array, 0, 10));
    }
    if (isset($filelayout['product_type']))
    {
      if ($row['p_master_categories_id'] == '0' && ($ep_googlebase['product_type'] == '0' || $ep_googlebase['product_type'] == '1'))
      {
        $row['product_type'] = '';
      }
      else if ($ep_googlebase['product_type'] == '0')
      {
        $row['product_type'] = $cat_path_array[count($cat_path_array)-1];
      }
      else if ($ep_googlebase['product_type'] == '1')
      {
        $row['product_type'] = $cat_path_array[0];
      }
    }
    $row_export = '';
    for (reset($filelayout); list($key, $value) = each($filelayout);)
    {
      $row_export .= str_replace(array("\r", "\n", "\t"), " ", $row[$key]) . $separator;
    }
    $row_export = substr($row_export, 0, strlen($row_export) - 1) . $endofrow;
    $filestring .= $row_export;
    ep_flush();
  }
  if (count($export_error['googlebase']) > 0)
  {
    foreach ($export_error['googlebase'] as $error)
    {
      $epMsgStack->add($error, 'caution');
    }
  }
}
else
{
  $error_detect = false;
  $products_error_count = 0;
  $result  =& ep_query($filelayout_sql);
  $new_master_categories_id = array();
  $attr4time = 0;
  for ($i = 0; $row = $result->getRow($i, ECLIPSE_DB_ASSOC); $i++)
  {
    foreach ($ep_languages as $key => $lang)
    {
      $sql2 = 'SELECT * FROM (' . TABLE_PRODUCTS_DESCRIPTION . ') WHERE products_id = ' . $row['v_products_id'] . ' AND language_id = ' . (int)$lang['id'];
      $result2  =& ep_query($sql2);
      if ($row2 = $result2->getRow(0, ECLIPSE_DB_ASSOC))
      {
        $row['v_products_name_' . $lang['code']] = $row2['products_name'];
        $row['v_products_description_' . $lang['code']] = $row2['products_description'];
        if (isset($ep_supported_mods['psd']))
        {
          $row['v_products_short_desc_' . $lang['code']]   = $row2['products_short_desc'];
        }
        $row['v_products_url_' . $lang['code']]    = $row2['products_url'];
      }
      else
      {
        $default_desc_result =& ep_query('SELECT * FROM (' . TABLE_PRODUCTS_DESCRIPTION . ') WHERE products_id = ' . (int)$row['v_products_id'] . ' AND language_id = ' . (int)$language_id_default);
        if ($default_desc_row = $default_desc_result->getRow(0, ECLIPSE_DB_ASSOC))
        {
          ep_query('INSERT INTO ' . TABLE_PRODUCTS_DESCRIPTION . ' (products_id, language_id, products_name, products_description) VALUES (' . (int)$row['v_products_id'] . ', ' . (int)$lang['id'] . ', ' . ep_db_input($default_desc_row['products_name']) . ', ' . "''" . ')');
          $error_detect = EASYPOPULATE_MSGSTACK_PRODUCTS_EXPORT_FIX;
          $products_error_count++;
          $row['v_products_name_' . $lang['code']] = $default_desc_row['products_name'];
          $row['v_products_description_' . $lang['code']] = '';
          if (isset($ep_supported_mods['psd']))
          {
            $row['v_products_short_desc_' . $lang['code']]   = '';
          }
          $row['v_products_url_' . $lang['code']]    = '';
        }
        else
        {
        }
      }
      if (isset($filelayout['v_metatags_title_' . $lang['code']]))
      {
        $meta_results  =& ep_query('SELECT
              metatags_title,
              metatags_keywords,
              metatags_description
          FROM
              (' . TABLE_META_TAGS_PRODUCTS_DESCRIPTION . ')
          WHERE
          products_id = ' . $row['v_products_id'] . ' AND
          language_id = ' . (int)$lang['id']);
        if ($meta_results->getRowCount())
        {
          $ep_meta = $meta_results->getRow(0, ECLIPSE_DB_ASSOC);
          $row['v_metatags_title_' . $lang['code']] = $ep_meta['metatags_title'];
          $row['v_metatags_keywords_' . $lang['code']] = $ep_meta['metatags_keywords'];
          $row['v_metatags_description_' . $lang['code']] = $ep_meta['metatags_description'];
        }
        else
        {
          $row['v_metatags_title_' . $lang['code']] = '';
          $row['v_metatags_keywords_' . $lang['code']] = '';
          $row['v_metatags_description_' . $lang['code']] = '';
        }
      }
    }
    if (isset($filelayout['v_specials_price']))
    {
      $specials_result  =& ep_query('SELECT
            specials_new_products_price,
            specials_date_available,
            expires_date
        FROM
            (' . TABLE_SPECIALS . ')
        WHERE
        products_id = ' . $row['v_products_id']);
      if ($specials_result->getRowCount())
      {
        $ep_specials = $specials_result->getRow(0, ECLIPSE_DB_ASSOC);
        $row['v_specials_price'] = $ep_specials['specials_new_products_price'];
        $row['v_specials_date_available'] = $ep_specials['specials_date_available'];
        $row['v_specials_expires_date'] = $ep_specials['expires_date'];
      }
      else
      {
        $row['v_specials_price'] = '';
        $row['v_specials_date_available'] = '';
        $row['v_specials_expires_date'] = '';
      }
    }
    if (isset($filelayout['ptc_categories_index_path']))
    {
      if (isset($new_master_categories_id[$row['v_products_id']]))
        $row['v_master_categories_id'] = $new_master_categories_id[$row['v_products_id']];
      if (($row['v_master_categories_id'] == '' || $row['v_master_categories_id'] == '0')  && $row['v_categories_id'] == '')
      {
        if (!$ep_junk_cat_id = array_search('Junk Pile', $epCategories->catPaths))
        {
          $ep_junk_cat_id = 9999;
        }
        ep_query('UPDATE ' . TABLE_PRODUCTS . ' SET master_categories_id=' . (int)$ep_junk_cat_id . ' WHERE products_id=' . $row['v_products_id']);
        ep_query('INSERT INTO ' . TABLE_PRODUCTS_TO_CATEGORIES . ' (products_id, categories_id) VALUES (' . $row['v_products_id'] . ', ' . $ep_junk_cat_id . ')');
        $row['v_master_categories_id'] = $ep_junk_cat_id;
        $row['v_categories_id'] = $ep_junk_cat_id;
        $new_master_categories_id[$row['v_products_id']] = $ep_junk_cat_id;
        echo "missing cat on $row[v_products_model] - moved to junk";
      }
      else if ($row['v_master_categories_id'] == '' || $row['v_master_categories_id'] == '0')
      {
        ep_query('UPDATE ' . TABLE_PRODUCTS . ' SET master_categories_id=' . (int)$row['v_categories_id'] . ' WHERE products_id=' . $row['v_products_id']);
        $row['v_master_categories_id'] = $row['v_categories_id'];
        $new_master_categories_id[$row['v_products_id']] = $row['v_categories_id'];
        echo "updated master cat id on $row[v_products_model]";
      }
      $row['ptc_categories_index_path'] = $epCategories->catPaths[$row['v_categories_id']];
      $row['ptc_categories_destination_path'] = '';
      if ($row['v_master_categories_id'] == $row['v_categories_id'])
      {
        $row['ptc_categories_linked_path'] = '';
      }
      else if (isset($epCategories->catPaths[$row['v_master_categories_id']]))
      {
        $row['ptc_categories_linked_path'] = 'ISLINKED';
      }
      else
      {
        echo 'unknown master cat id';
      }
    }
    if (isset($filelayout['v_manufacturers_name']))
    {
      if ($row['v_manufacturers_id'] != '')
      {
        $sql2 = "SELECT manufacturers_name
          FROM (".TABLE_MANUFACTURERS.")
          WHERE
          manufacturers_id = " . $row['v_manufacturers_id']
          ;
        $result2  =& ep_query($sql2);
        $row2 = $result2->getRow(0, ECLIPSE_DB_ASSOC);
        $row['v_manufacturers_name'] = $row2['manufacturers_name'];
      }
    }
    if (isset($filelayout['v_attribute_options_id_1']))
    {
      if (isset($ep_debug)) $attr4start = ep_timer();
      $attribute_options_count = 1;
      for (reset($attribute_options_array); list($key, $attribute_options) = each($attribute_options_array);)
      {
        $row['v_attribute_options_id_' . $attribute_options_count]  = $attribute_options['products_options_id'];
        for (reset($ep_languages); list($l_id, $arr) = each($ep_languages);)
        {
          $attribute_options_languages_query = "select products_options_name from (" . TABLE_PRODUCTS_OPTIONS . ") where products_options_id = '" . (int)$attribute_options['products_options_id'] . "' and language_id = '" . (int)$lid . "'";
          $attribute_options_languages_values  =& ep_query($attribute_options_languages_query);
          $attribute_options_languages = $attribute_options_languages_values->getRow(0, ECLIPSE_DB_ASSOC);
          $row['v_attribute_options_name_' . $attribute_options_count . '_' . $lid] = $attribute_options_languages['products_options_name'];
        }
        $attribute_values_query = "select products_options_values_id from (" . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . ") where products_options_id = '" . (int)$attribute_options['products_options_id'] . "' order by products_options_values_id";
        $attribute_values_values  =& ep_query($attribute_values_query);
        $attribute_values_count = 1;
        for ($ii = 0; $attribute_values = $attribute_values_values->getRow($ii, ECLIPSE_DB_ASSOC); $ii++)
        {
          $row['v_attribute_values_id_' . $attribute_options_count . '_' . $attribute_values_count]   = $attribute_values['products_options_values_id'];
          $attribute_values_price_query = "select options_values_price, price_prefix from (" . TABLE_PRODUCTS_ATTRIBUTES . ") where products_id = '" . (int)$row['v_products_id'] . "' and options_id = '" . (int)$attribute_options['products_options_id'] . "' and options_values_id = '" . (int)$attribute_values['products_options_values_id'] . "'";
          $attribute_values_price_values  =& ep_query($attribute_values_price_query);
          $attribute_values_price = $attribute_values_price_values->getRow(0, ECLIPSE_DB_ASSOC);
          $row['v_attribute_values_price_' . $attribute_options_count . '_' . $attribute_values_count]  = $attribute_values_price['price_prefix'] . $attribute_values_price['options_values_price'];
          for (reset($ep_languages); list($lid, $arr) = each($ep_languages);)
          {
            $attribute_values_languages_query = "select products_options_values_name from (" . TABLE_PRODUCTS_OPTIONS_VALUES . ") where products_options_values_id = '" . (int)$attribute_values['products_options_values_id'] . "' and language_id = '" . (int)$lid . "'";
            $attribute_values_languages_values =& ep_query($attribute_values_languages_query);
            $attribute_values_languages = $attribute_values_languages_values->getRow(0, ECLIPSE_DB_ASSOC);
            $row['v_attribute_values_name_' . $attribute_options_count . '_' . $attribute_values_count . '_' . $lid] = $attribute_values_languages['products_options_values_name'];
          }
          $attribute_values_count++;
        }
        $attribute_options_count++;
        ep_flush();
      }
      if (isset($ep_debug))
      {
        $attr4end = ep_timer();
        $attr4time = $attr4time+$attr4end-$attr4start;
      }
    }
    if(isset($row['v_products_type']))
    {
      $row['v_products_type'] = $ep_file_types[$row['v_products_type']];
    }
    $row_tax_multiplier = ep_get_tax_class_rate($row['v_products_tax_class_id']);
    $row['v_tax_class_title'] = zen_get_tax_class_title($row['v_products_tax_class_id']);
    $row['v_products_price'] = round($row['v_products_price'] + ($price_with_tax * $row['v_products_price'] * $row_tax_multiplier / 100),4);
    $row_export = '';
    for (reset($filelayout); list($key, $value) = each($filelayout);)
    {
      $row_export .= str_replace(array("\r", "\n", "\t"), " ", $row[$key]) . $separator;
    }
    $row_export = substr($row_export, 0, strlen($row_export) - 1) . $endofrow;
    $filestring .= $row_export;
    ep_flush();
  }
  if ($error_detect)
    $epMsgStack->add(sprintf($error_detect, $products_error_count), 'caution');
  unset($error_detect);
}
if (isset($ep_debug)) ekko('Attributes 4 Time: ' . $attr4time);
$mezzo = strftime('%y%m%d_%H%M%S');
if ($_GET['type'] == 'template')
{
  $mezzo = EASYPOPULATE_EXPORT_NAME_TEMPLATE;
}
$dl_filename = $dl_filename . $mezzo . EASYPOPULATE_EXPORT_SUFFIX;
$tmpfpath = $tempdir . $dl_filename;
$fp = fopen( $tmpfpath, "w+");
fwrite($fp, $filestring);
fclose($fp);
$epMsgStack->add(sprintf(EASYPOPULATE_MSGSTACK_FILE_EXPORT_SUCCESS, $dl_filename, $tempdir), 'success');
if (isset($ep_debug)) ep_timer($sdltimer,'Download');
