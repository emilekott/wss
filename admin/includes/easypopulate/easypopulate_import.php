<?php

ep_flush('&nbsp;' . EASYPOPULATE_FLUSH_PROGRESS_START);
$epCategories =& new EpCategories();
$epCategories->CatPathsFlip();

$ep_file_types = array();
$result = ep_query('SELECT type_id, type_name FROM ' . TABLE_PRODUCT_TYPES);
for ($i = 0; $product_type_row = $result->getRow($i, ECLIPSE_DB_ASSOC); $i++)
{
  $ep_file_types[$product_type_row['type_name']] = $product_type_row['type_id'];
}

if(!isset($ep_overrides['text_delimiters']))
  $ep_overrides['text_delimiters'] = array("'" => '', "\"" => '');
if(!isset($ep_overrides['linebreak']))
  $ep_overrides['linebreak'] = "\n";
$fh = fopen($tempdir . $file['name'], "r");
$header_row = trim(ep_getline($fh, $ep_overrides['linebreak']));
$headers_array = explode($separator, $header_row);
if (substr($headers_array[0], -1) === substr($headers_array[0], 0, 1) && isset($ep_overrides['text_delimiters'][substr($headers_array[0], -1)]))
{
  $text_delimiter = substr($headers_array[0], -1);
}
else
{
  $text_delimiter = '';
}

$lll = 0;
$filelayout = array();
foreach ($headers_array as $header)
{
  $cleanheader = trim($header, $text_delimiter);
  $filelayout[$cleanheader] = $lll++;
}

if (strpos($header_row, EASYPOPULATE_CONFIG_EXPLICIT_EOR))
{
  $explicit_eor = $separator . $text_delimiter . EASYPOPULATE_CONFIG_EXPLICIT_EOR . $text_delimiter;
  unset($filelayout[EASYPOPULATE_CONFIG_EXPLICIT_EOR]);
}

$file_contents = array();
while (!feof($fh))
{
   $file_contents[] = ep_getline($fh, $ep_overrides['linebreak']);
}
fclose($fh);
if (isset($explicit_eor))
  $file_contents = explode($explicit_eor, implode("", $file_contents));
unset($file_contents[(count($file_contents)-1)]);
if (isset($filelayout['c_categories_index_path']))
{
  if (!isset($filelayout['c_categories_status']) && !isset($filelayout['c_categories_destination_path']))
  {
    $epMsgStack->add(EASYPOPULATE_MSGSTACK_CATEGORIES_INDEX_ACTION_MISSING, 'warning');
  }
  else
  {

    $display['EASYPOPULATE_DISPLAY_RESULT_CATEGORIES'] = '';
    $data = array();
    $row_counter = 1;
    $filelayout_count = count($filelayout);

    for (reset($file_contents); list($key, $file_row) = each($file_contents);)
    {
      $row_counter++;
      $file_row = trim($file_row, " \n\r\0\x0B");
      $items = explode($separator, $file_row);
      if ($filelayout_count != count($items))
      {
        $display['EASYPOPULATE_DISPLAY_RESULT_PRODUCT'] .= sprintf(EASYPOPULATE_DISPLAY_RESULT_COLUMN_COUNT_MISMATCH, $row_counter);
        $ep_counter['errors']++;
        continue;
      }
      foreach ($filelayout as $column_heading => $column_number)
      {
        if (!ep_empty(trim($items[$column_number])))
        {
          if (substr($items[$column_number], -1) === substr($items[$column_number], 0, 1) && isset($ep_overrides['text_delimiters'][substr($items[$column_number], -1)]))
            $items[$column_number] = substr($items[$column_number], 1, strlen($items[$column_number]) - 2);
          $items[$column_number] = str_replace('\"\"', '"', $items[$column_number]);
        }
      }

      if ($items[$filelayout['c_categories_index_path']] == '')
      {
        $display['EASYPOPULATE_DISPLAY_RESULT_CATEGORIES'] .= sprintf(EASYPOPULATE_DISPLAY_RESULT_CATEGORIES_NO_INDEX, $row_counter);
        $ep_counter['errors']++;
        continue;
      }
      else if (!$cat_id = $epCategories->catPaths[$items[$filelayout['c_categories_index_path']]])
      {

        /*
        if ($items[$filelayout['c_categories_index_path']] == $items[$filelayout['c_categories_destination_path']])
        {
        */

          $new_cat_path = explode(EASYPOPULATE_CONFIG_CATEGORIES_PATH_SEPARATOR, $items[$filelayout['c_categories_index_path']]);
          if (count($new_cat_path) === 1)
          {
            $parent_id = '0';
          }
          else if (!$parent_id = $epCategories->catPaths[implode(EASYPOPULATE_CONFIG_CATEGORIES_PATH_SEPARATOR, array_slice($new_cat_path, 0, count($new_cat_path) - 1))])
          {
            $display['EASYPOPULATE_DISPLAY_RESULT_CATEGORIES'] .= sprintf(EASYPOPULATE_DISPLAY_RESULT_CATEGORIES_NO_PARENT, $row_counter);
            $ep_counter['errors']++;
            continue;
          }

          $cat_fields = array();
          foreach (array('sort_order', 'categories_image', 'categories_status') as $field)
          {
            if(isset($items[$filelayout['c_' . $field]]))
            {
              $cat_fields[$field] = $items[$filelayout['c_' . $field]];
            }
          }
          $cat_desc_fields = array();
    	    foreach ($ep_languages as $lang)
    	    {
            if(isset($items[$filelayout['cd_categories_name_' . $lang['code']]]))
            {
              if (!ep_empty($items[$filelayout['cd_categories_name_' . $lang['code']]]))
                $cat_desc_fields[$lid]['categories_name'] = $items[$filelayout['cd_categories_name_' . $lang['code']]];
            }
            if(isset($items[$filelayout['cd_categories_description_' . $lang['code']]]))
            {
              if (!ep_empty($items[$filelayout['cd_categories_description_' . $lang['code']]]))
                $cat_desc_fields[$lid]['categories_description'] = $items[$filelayout['cd_categories_description_' . $lang['code']]];
            }
    	    }
          if ($new_cat_id = $epCategories->CreatNewCat($parent_id, $items[$filelayout['cd_categories_name_' . $ep_languages[$language_id_default]['code']]], $cat_fields, $cat_desc_fields))
          {
            $epCategories->catPaths[$items[$filelayout['c_categories_index_path']]] = $new_cat_id;
            $ep_counter['added']++;
          }
          else
          {
            $display['EASYPOPULATE_DISPLAY_RESULT_CATEGORIES'] .= sprintf(EASYPOPULATE_DISPLAY_RESULT_CATEGORIES_NO_NAME, $row_counter);
            $ep_counter['errors']++;
          }

        /*
        }
        else
        {
          $display['EASYPOPULATE_DISPLAY_RESULT_CATEGORIES'] .= sprintf(EASYPOPULATE_DISPLAY_RESULT_CATEGORIES_NAME_MISMATCH, $row_counter);
          $ep_counter['errors']++;
          continue;
        }
        */
      }
      else
      {

        $display['EASYPOPULATE_DISPLAY_RESULT_CATEGORIES'] .= sprintf(EASYPOPULATE_DISPLAY_RESULT_CATEGORIES_NOT_IMPLEMENTED, $row_counter);
        $ep_counter['errors']++;
        continue;
        if (!ep_empty($items[$filelayout['c_categories_status']]))
        {
          switch ($items[$filelayout['c_categories_status']])
          {
            case '99':

            case '9':

            break;
            case '0':
            case '1':

            break;
          }
        }
        else
        {
          echo '<br />no action requested on status';
        }
      }



    }
  }
}
else if (!isset($filelayout[$v_primary_index]))
{
  $epMsgStack->add(sprintf(EASYPOPULATE_MSGSTACK_FILE_INDEX_MISSING, $v_primary_index), 'warning');
}
else if (isset($filelayout['v_products_id']) && EASYPOPULATE_CONFIG_PRIMARY_INDEX == 'products_model')
{
  $epMsgStack->add(EASYPOPULATE_MSGSTACK_FILE_ADDITIONAL_INDEX, 'warning');
}
else
{
  $display['EASYPOPULATE_DISPLAY_RESULT_PRODUCT'] = '';//EASYPOPULATE_DISPLAY_HEADING;
  $display['EASYPOPULATE_DISPLAY_RESULT_SPECIALS'] = '';
  $display['EASYPOPULATE_DISPLAY_RESULT_META'] = '';
  $filelayout_count = count($filelayout);
  $row_counter = 1;

  for (reset($file_contents); list($key, $file_row) = each($file_contents);)
  {
    $row_counter++;
    $file_row = trim($file_row, " \n\r\0\x0B");
    $items = explode($separator, $file_row);
    if ($filelayout_count != count($items))
    {
      $display['EASYPOPULATE_DISPLAY_RESULT_PRODUCT'] .= sprintf(EASYPOPULATE_DISPLAY_RESULT_COLUMN_COUNT_MISMATCH, $row_counter);
      $ep_counter['errors']++;
      continue;
    }
    foreach ($filelayout as $key => $value)
    {
      $i = $filelayout[$key];
      $items[$i] = trim($items[$i]);
      if (!ep_empty($items[$i]))
      {
        if (substr($items[$i], -1) === substr($items[$i], 0, 1) && isset($ep_overrides['text_delimiters'][substr($items[$i], -1)]))
          $items[$i] = substr($items[$i], 1, strlen($items[$i]) - 2);
        $items[$i] = str_replace('\"\"','"',$items[$i]);
      }
    }



    $products_fields = ep_table_fields(TABLE_PRODUCTS);
    $default_these = array();
    foreach ($products_fields as $field_name)
    {
      $default_these[] = 'v_' . $field_name;
    }
    $default_these = array_merge($default_these, array(
      'v_tax_class_title',
      'v_manufacturers_name',
      'v_categories_id'
    ));
    $sql = 'SELECT ';
    foreach ($products_fields as $field_name)
    {
      $sql .= 'p.' . $field_name . ' as v_' . $field_name . ', ';
    }
    $sql .= 'c.categories_id as v_categories_id
    FROM
    (' . TABLE_PRODUCTS . ' as p LEFT JOIN 
    ' . TABLE_PRODUCTS_TO_CATEGORIES . ' as ptc ON p.products_id = ptc.products_id) LEFT JOIN 
    ' . TABLE_CATEGORIES . ' as c ON ptc.categories_id = c.categories_id 
    WHERE 
    p.' . EASYPOPULATE_CONFIG_PRIMARY_INDEX . ' = ' . ep_db_input($items[$filelayout[$v_primary_index]]);
    $result =& ep_query($sql);
    $product_is_new = true;
    $ep_price_sorter = false;
    for ($i = 0; $row = $result->getRow($i, ECLIPSE_DB_ASSOC); $i++)
    {

      $product_is_new = false;
      if ($items[$filelayout['v_products_status']] == 9 || $items[$filelayout['v_products_status']] == 99)
      {
        if ($row['v_master_categories_id'] == '0')
        {
          if (!$row['v_master_categories_id'] = ep_update_cat_ids(array($row['v_products_id'])))
          {
          }
        }
        $product_delete = true;
        if (isset($items[$filelayout['ptc_categories_index_path']]))
        {
          if (!$cat_path_id = $epCategories->catPaths[$items[$filelayout['ptc_categories_index_path']]])
          {
            $product_delete = false;
            continue 2;
          }
          else if ($row['v_master_categories_id'] != $cat_path_id)
          {
            $product_delete = false;
            $ptc_check_exist_result =& ep_query('SELECT * FROM ' . TABLE_PRODUCTS_TO_CATEGORIES . ' WHERE products_id = ' . $row['v_products_id'] . ' AND categories_id = ' . (int)$cat_path_id);
            if ($ptc_check_exist_result->getRowCount() > 0)
            {
              ep_query('DELETE FROM ' . TABLE_PRODUCTS_TO_CATEGORIES . ' WHERE products_id = ' . $row['v_products_id'] . ' AND categories_id = ' . (int)$cat_path_id);
              echo $items[$filelayout['ptc_categories_index_path']] . ' - deleted linked product<br />';
              $ep_counter['deleted']++;
            }
            else
            {

            }
            continue 2;
          }
          else
          {
            $product_delete = true;
          }
        }
        if ($product_delete)
        {
          $image_delete = false;
          if ($items[$filelayout['v_products_status']] == 99)
            $image_delete = true;
          if (!ep_remove_product($items[$filelayout[$v_primary_index]], $image_delete))
          {
            $display['EASYPOPULATE_DISPLAY_RESULT_PRODUCT'] .= sprintf(EASYPOPULATE_DELETE_IMAGE_FAIL, $items[$filelayout[$v_primary_index]]);
            $ep_counter['partial_errors']++;
          }
          $ep_counter['deleted']++;
          continue 2;
        }

      }

      foreach ($ep_languages as $lang)
      {
        $sql2 = 'SELECT *
          FROM (' . TABLE_PRODUCTS_DESCRIPTION . ')
          WHERE
            products_id = ' . (int)$row['v_products_id'] . ' AND
            language_id = ' . (int)$lang['id'];
        $result2  =& ep_query($sql2);
        $row2 = $result2->getRow(0, ECLIPSE_DB_ASSOC);
        $row['v_products_name_' . $lang['code']] = $row2['products_name'];
        $row['v_products_description_' . $lang['code']] = $row2['products_description'];
        if (isset($ep_supported_mods['psd']))
        {
          $row['v_products_short_desc_' . $lang['code']] = $row2['products_short_desc'];
        }
        $row['v_products_url_' . $lang['code']] = $row2['products_url'];
      }

      if ($row['v_manufacturers_id'] != '')
      {
        $sql2 = 'SELECT manufacturers_name FROM (' . TABLE_MANUFACTURERS . ') WHERE manufacturers_id = ' . (int)$row['v_manufacturers_id'];
        $result2  =& ep_query($sql2);
        $row2 = $result2->getRow(0, ECLIPSE_DB_ASSOC);
        $row['v_manufacturers_name'] = $row2['manufacturers_name'];
      }


      $row_tax_multiplier = ep_get_tax_class_rate($row['v_products_tax_class_id']);
      $row['v_tax_class_title'] = zen_get_tax_class_title($row['v_products_tax_class_id']);
      if ($price_with_tax)
      {
        $row['v_products_price'] = round($row['v_products_price'] + ($row['v_products_price'] * $row_tax_multiplier / 100), 4);
      }
      if ($row['v_products_price'] != round($items[$filelayout['v_products_price']], 4))
      {
        $ep_price_sorter = true;
      }
      foreach ($default_these as $var)
      {
        $$var = $row[$var];
      }
    }
    


    if ($items[$filelayout['v_products_status']] == 9 || $items[$filelayout['v_products_status']] == 99)
    {
      $ep_counter['errors']++;
      $display['EASYPOPULATE_DISPLAY_RESULT_PRODUCT'] .= sprintf(EASYPOPULATE_DISPLAY_RESULT_DELETE_NOT_FOUND, $items[$filelayout[$v_primary_index]]);
      continue;
    }
    if ($product_is_new)
    {
      if (!$cat_path_id = $epCategories->catPaths[$items[$filelayout['ptc_categories_index_path']]])
      {
        $ep_counter['errors']++;
        $display['EASYPOPULATE_DISPLAY_RESULT_PRODUCT'] .= sprintf(EASYPOPULATE_DISPLAY_RESULT_CATEGORY_NOT_FOUND, $items[$filelayout[$v_primary_index]], ' new');
        continue;
      }
      /*
      if (!$dest_path_id = $epCategories->catPaths[$items[$filelayout['ptc_categories_destination_path']]])
      {
        $ep_counter['errors']++;
        $display['EASYPOPULATE_DISPLAY_RESULT_PRODUCT'] .= sprintf(EASYPOPULATE_DISPLAY_RESULT_DEST_CATEGORY_NOT_FOUND, $items[$filelayout[$v_primary_index]], ' new');
        continue;
      }
      if ($dest_path_id != $cat_path_id)
      {
        $ep_counter['errors']++;
        $display['EASYPOPULATE_DISPLAY_RESULT_PRODUCT'] .= sprintf(EASYPOPULATE_DISPLAY_RESULT_NEW_CATEGORY_MISMATCH, $items[$filelayout[$v_primary_index]]);
        continue;
      }
      */
      $v_master_categories_id = $cat_path_id;
    }
    else
    {
      if (isset($items[$filelayout['ptc_categories_index_path']]))
      {
        if ($cat_path_id = $epCategories->catPaths[$items[$filelayout['ptc_categories_index_path']]])
        {
          $ptc_check_exist_result =& ep_query('SELECT * FROM ' . TABLE_PRODUCTS_TO_CATEGORIES . ' WHERE products_id = ' . (int)$v_products_id . ' AND categories_id = ' . (int)$cat_path_id);
          if ($ptc_check_exist_result->getRowCount() > 0)
          {
            if ($v_master_categories_id != $cat_path_id)
            {

              if ($dest_path_id = $epCategories->catPaths[$items[$filelayout['ptc_categories_destination_path']]])
              {
                if ($dest_path_id != $cat_path_id)
                {

                  ep_query('UPDATE ' . TABLE_PRODUCTS_TO_CATEGORIES . ' SET categories_id = ' . $dest_path_id . ' WHERE products_id = ' . (int)$v_products_id . ' AND categories_id = ' . (int)$cat_path_id);
                  $ep_counter['moved']++;
                  continue;
                }
              }
              else if ($link_path_id = $epCategories->catPaths[$items[$filelayout['ptc_categories_linked_path']]])
              {
                $ep_counter['errors']++;
                $display['EASYPOPULATE_DISPLAY_RESULT_PRODUCT'] .= sprintf(EASYPOPULATE_DISPLAY_RESULT_SKIP_LINK_ON_LINKED_PRODUCT, $items[$filelayout[$v_primary_index]], $items[$filelayout['ptc_categories_index_path']]);
                continue;
              }
              else
              {
                $ep_counter['errors']++;
                $display['EASYPOPULATE_DISPLAY_RESULT_PRODUCT'] .= sprintf(EASYPOPULATE_DISPLAY_RESULT_SKIP_LINKED_PRODUCT, $items[$filelayout[$v_primary_index]], $items[$filelayout['ptc_categories_index_path']]);
                continue;
              }
            }
            else
            {

              if ($link_path_id = $epCategories->catPaths[$items[$filelayout['ptc_categories_linked_path']]])
              {
                $ptc_check_exist_result =& ep_query('SELECT * FROM ' . TABLE_PRODUCTS_TO_CATEGORIES . ' WHERE products_id = ' . (int)$v_products_id . ' AND categories_id = ' . (int)$link_path_id);
                if ($ptc_check_exist_result->getRowCount() === 0)
                {
                  ep_query('INSERT INTO ' . TABLE_PRODUCTS_TO_CATEGORIES . ' (products_id, categories_id) VALUES (' . (int)$v_products_id . ', ' . (int)$link_path_id . ')');
                }

              }
              if ($dest_path_id = $epCategories->catPaths[$items[$filelayout['ptc_categories_destination_path']]])
              {
                if ($dest_path_id != $v_master_categories_id)
                {
                  $ptc_categories_result =& ep_query('SELECT * FROM ' . TABLE_PRODUCTS_TO_CATEGORIES . ' WHERE products_id = ' . (int)$v_products_id . ' AND categories_id = ' . (int)$cat_path_id);
                  $ptc_categories_array = array();
                  for ($i = 0; $ptc_categories = $ptc_categories_result->getRow($i, ECLIPSE_DB_ASSOC); $i++)
                  {
                    $ptc_categories_array[$ptc_categories['categories_id']] = $ptc_categories['categories_id'];
                  }
                  unset($ptc_categories_array[$v_master_categories_id]);
                  unset($ptc_categories_array[$dest_path_id]);
                  array_unshift($ptc_categories_array, $dest_path_id);
                  ep_query('DELETE FROM ' . TABLE_PRODUCTS_TO_CATEGORIES . ' WHERE products_id = ' . (int)$v_products_id);
                  foreach ($ptc_categories_array as $ptc_categories)
                  {
                    ep_query('INSERT INTO ' . TABLE_PRODUCTS_TO_CATEGORIES . ' (products_id, categories_id) VALUES (' . (int)$v_products_id . ', ' . (int)$ptc_categories . ')');
                  }
                  ep_query('UPDATE ' . TABLE_PRODUCTS . ' SET master_categories_id = ' . (int)$dest_path_id . ' WHERE products_id = ' . (int)$v_products_id);
                  $items[$filelayout['ptc_categories_index_path']] = $items[$filelayout['ptc_categories_destination_path']];
                  $v_master_categories_id = $v_categories_id = $dest_path_id;
                  $ep_counter['moved']++;
                }
                else
                {
                  $ep_counter['partial_errors']++;
                  $display['EASYPOPULATE_DISPLAY_RESULT_PRODUCT'] .= sprintf(EASYPOPULATE_DISPLAY_RESULT_NEW_MASTER_IS_MASTER, $items[$filelayout[$v_primary_index]], $items[$filelayout['ptc_categories_destination_path']]);
                }
              }
            }
          }
          else
          {
            $ep_counter['errors']++;
            $display['EASYPOPULATE_DISPLAY_RESULT_PRODUCT'] .= sprintf(EASYPOPULATE_DISPLAY_RESULT_NO_INDEX_CATEGORY, $items[$filelayout[$v_primary_index]], $items[$filelayout['ptc_categories_index_path']]);
            continue;
          }
        }
        else
        {
          $ep_counter['errors']++;
          $display['EASYPOPULATE_DISPLAY_RESULT_PRODUCT'] .= sprintf(EASYPOPULATE_DISPLAY_RESULT_INVALID_CATEGORY, $items[$filelayout['ptc_categories_index_path']]);
          continue;
        }
      }
    }



    foreach ($filelayout as $key => $value)
    {
      $$key = $items[$value];
    }

    if ($product_is_new)
    {

      if (!isset($v_products_date_added) || ep_empty($v_products_date_added))
      {
        $v_products_date_added = 'CURRENT_TIMESTAMP';
      }
      else
      {
        if (!$v_products_date_added = ep_datoriser($v_products_date_added))
        {
          $v_products_date_added = 'CURRENT_TIMESTAMP';
        }
      }
      if (!isset($v_products_date_available) || ep_empty($v_products_date_available))
      {
        $v_products_date_available = 'NULL';
      }
      else
      {
        if (!$v_products_date_available = ep_datoriser($v_products_date_available))
        {
          $v_products_date_available = 'NULL';
        }
      }
    }
    else
    {

      if (!$v_products_date_added = ep_datoriser($v_products_date_added))
      {
        if ($row['v_products_date_added'] == '0000-00-00 00:00:00')
        {
          $v_products_date_added = 'CURRENT_TIMESTAMP';
        }
        else
        {
          $v_products_date_added = $row['v_products_date_added'];
        }
      }
      if (!$v_products_date_available = ep_datoriser($v_products_date_available))
      {

        $v_products_date_available = $row['v_products_date_available'];
      }
      $v_products_last_modified = 'CURRENT_TIMESTAMP';
    }

    foreach ($ep_languages as $lang)
    {
      if (isset($filelayout['v_products_name_' . $lang['code']]))
      {
        $v_products_name[$lang['id']] = ep_smart_tags($items[$filelayout['v_products_name_' . $lang['code']]], 'false');
        $v_products_description[$lang['id']] = ep_smart_tags($items[$filelayout['v_products_description_' . $lang['code'] ]]);

        if (isset($ep_supported_mods['psd']))
        {
          $v_products_short_desc[$lang['id']] = ep_smart_tags($items[$filelayout['v_products_short_desc_' . $lang['code']]]);
        }
        $v_products_url[$lang['id']] = ep_smart_tags($items[$filelayout['v_products_url_' . $lang['code'] ]], 'false');
      }
    }
    if(!isset($ep_overrides['currency_symbols']))
    {
      $ep_overrides['currency_symbols'] = ' $£¥€';
    }
    if (isset($filelayout['v_products_price']))
    {
      $v_products_price = (float)trim($v_products_price, $ep_overrides['currency_symbols']);
    }
    else if (!isset($v_products_price))
    {
      $v_products_price = 0;
      $v_products_status = 0;
      $ep_counter['partial_errors']++;
      $display['EASYPOPULATE_DISPLAY_RESULT_PRODUCT'] .= sprintf(EASYPOPULATE_DISPLAY_RESULT_NO_PRICE_ON_NEW_PRODUCT, $$v_primary_index);
    }

    if (isset($filelayout['v_tax_class_title']))
    {
      if (!$v_products_tax_class_id = ep_get_tax_title_class_id($v_tax_class_title))
        $v_products_tax_class_id = '0';

      $row_tax_multiplier = ep_get_tax_class_rate($v_products_tax_class_id);

      if ($price_with_tax)
      {
        $v_products_price = round($v_products_price / (1 + ( $row_tax_multiplier * $price_with_tax / 100)), 4);
      }
    }

    if (ep_empty($v_products_quantity))
      $v_products_quantity = 0;
    if ($v_products_status === 0 || (EASYPOPULATE_CONFIG_ZERO_QTY_INACTIVE == 'true' && $v_products_quantity === 0))
    {
      $v_products_status = 0;
    }
    else
    {
      $v_products_status = 1;
    }
    if (ep_empty($v_manufacturers_id))
    {
      $v_manufacturers_id = 'NULL';
    }
    if (ep_empty($v_products_image))
    {
      $v_products_image = PRODUCTS_IMAGE_NO_IMAGE;
    }
    if (isset($filelayout['v_products_model']) && strlen($v_products_model) > $modelsize)
    {
      $ep_counter['errors']++;
      $display['EASYPOPULATE_DISPLAY_RESULT_PRODUCT'] .= sprintf(EASYPOPULATE_DISPLAY_RESULT_MODEL_NAME_LONG, $$v_primary_index);
      continue;
    }
    if (isset($filelayout['v_manufacturers_name']) && $v_manufacturers_name != '')
    {
      $sql = 'SELECT manufacturers_id
          FROM (' . TABLE_MANUFACTURERS . ')
          WHERE
          manufacturers_name = ' . ep_db_input($v_manufacturers_name);
      $result  =& ep_query($sql);
      if ($result->getRowCount() > 0)
      {
        $row = $result->getRow(0, ECLIPSE_DB_ASSOC);
        $v_manufacturers_id = $row['manufacturers_id'];
      }
      else
      {
        $max_mfg_id = ep_get_next_id(TABLE_MANUFACTURERS);
        $sql = 'INSERT INTO ' . TABLE_MANUFACTURERS . ' (
          manufacturers_id,
          manufacturers_name,
          date_added,
          last_modified
          ) VALUES ('
          . $max_mfg_id . ',
          ' . ep_db_input($v_manufacturers_name) . ",
          CURRENT_TIMESTAMP,
          CURRENT_TIMESTAMP
          )";
        ep_query($sql);
        $v_manufacturers_id = $max_mfg_id;
      }
    }

    if (isset($filelayout['v_products_type']))
    {

      $v_products_type = str_replace("\DC3\Sp", '-', $v_products_type);
      if (!isset($ep_file_types[$v_products_type]))
      {

        if (ep_empty($v_products_type))
        {
          $v_products_type = EASYPOPULATE_CONFIG_PRODUCT_TYPE_DEFAULT;
        }
        else if (!in_array($v_products_type, $ep_file_types))
        {
          $display['EASYPOPULATE_DISPLAY_RESULT_PRODUCT'] .= sprintf(EASYPOPULATE_DISPLAY_RESULT_PRODUCT_PRODUCTS_TYPE_FAIL, $v_products_type);
          $ep_counter['partial_errors']++;
          $v_products_type = EASYPOPULATE_CONFIG_PRODUCT_TYPE_DEFAULT;
        }
      }
      else
      {
        $v_products_type = $ep_file_types[$v_products_type];
      }
    }
    else
    {
      $v_products_type = EASYPOPULATE_CONFIG_PRODUCT_TYPE_DEFAULT;
    }

    /**
    * PRODUCT ADDING/UPDATING
    * ALL PRODUCT PROCESSING IS DONE AFTER HERE ONLY
    */

    $fail_row = false;
    if (ep_empty($$v_primary_index))
    {
      if (EASYPOPULATE_CONFIG_PRIMARY_INDEX == 'products_model')
      {
        $fail_row = true;
      }
      else if (EASYPOPULATE_CONFIG_NEW_ON_NULL_INDEX == 'false')
      {
        $fail_row = true;
      }
    }
    else
    {
      if (EASYPOPULATE_CONFIG_PRIMARY_INDEX == 'products_id')
      {
        if (EASYPOPULATE_CONFIG_NEW_ON_NULL_INDEX == 'true' && $product_is_new)
        {
          $fail_row = true;
        }
        if (!is_numeric($v_products_id))
        {
          $fail_row = true;
        }
      }
    }
    if ($fail_row)
    {
      $ep_counter['errors']++;
      $string = '';
      foreach ($items as $col => $langer)
      {
        if ($col == $filelayout[$v_primary_index]) continue;
        $string .= print_el($langer);
      }
      $display['EASYPOPULATE_DISPLAY_RESULT_PRODUCT'] .= sprintf(EASYPOPULATE_DISPLAY_RESULT_INDEX_ERROR, EASYPOPULATE_CONFIG_PRIMARY_INDEX, $string);
    }
    else
    {
      if ($product_is_new)
      {
        $v_products_id = ep_get_next_id(TABLE_PRODUCTS);

        $v_products_last_modified = 'CURRENT_TIMESTAMP';        

        $sql = 'INSERT INTO ' . TABLE_PRODUCTS . ' (';
        $sql_fields = '';
        $sql_values = '';
        foreach ($products_fields as $field_name)
        {
          $sql_fields .= "\n $field_name,";
          $value = ${'v_' . $field_name};
          $sql_values .= "\n " . ep_db_input($value) . ',';
        }
        $sql_fields = rtrim($sql_fields, ',');
        $sql_values = rtrim($sql_values, ',');
        $sql .= $sql_fields . ')  VALUES (' . $sql_values . ')';
        $result =& ep_query($sql);
        if ($result->isSuccess())
        {

          $ep_counter['added']++;
          ep_query('INSERT INTO ' . TABLE_PRODUCTS_TO_CATEGORIES . ' (products_id, categories_id) VALUES (' . (int)$v_products_id . ', ' . (int)$v_master_categories_id . ')');
        }
        else
        {
          $display['EASYPOPULATE_DISPLAY_RESULT_PRODUCT'] .= sprintf(EASYPOPULATE_DISPLAY_RESULT_NEW_PRODUCT_FAIL, $$v_primary_index);
          $ep_counter['errors']++;

          foreach ($items as $col => $langer)
          {
            if ($col == $filelayout['v_products_model']) continue;
            $display['EASYPOPULATE_DISPLAY_RESULT_PRODUCT'] .= print_el($langer);
          }
          $display['EASYPOPULATE_DISPLAY_RESULT_PRODUCT'] .= '<br />';
          continue;
        }
      }
      else
      {

        $sql = 'UPDATE ' . TABLE_PRODUCTS . ' SET ';
        foreach ($products_fields as $field_name)
        {
          $value = ${'v_' . $field_name};
          $sql .= "\n $field_name = " . ep_db_input($value) . ',';
        }
        $sql = rtrim($sql, ',');
        $sql .= ' WHERE (products_id = ' . (int)$v_products_id . ')';
        $result =& ep_query($sql);
        if ($result->isSuccess())
        {
          $ep_counter['updated']++;
        }
        else
        {
          $ep_counter['errors']++;
          $display['EASYPOPULATE_DISPLAY_RESULT_PRODUCT'] .= sprintf(EASYPOPULATE_DISPLAY_RESULT_UPDATE_PRODUCT_FAIL, $$v_primary_index);
          continue;
        }
      }

      if (isset($v_products_name))
      {
        foreach ($v_products_name as $key => $name)
        {
          if ($name != '')
          {
            $sql = 'SELECT * FROM (' . TABLE_PRODUCTS_DESCRIPTION . ') WHERE
                products_id = ' . (int)$v_products_id . ' AND
                language_id = ' . $key;
            $result  =& ep_query($sql);
            if($result->isSuccess() && $result->getRowCount() > 0)
            {
              $sql =
                'UPDATE ' . TABLE_PRODUCTS_DESCRIPTION . ' SET
                  products_name = ' . ep_db_input($name) . ',
                  products_description = ' . ep_db_input($v_products_description[$key]) . ',
                  ';
              if (isset($ep_supported_mods['psd']))
              {
                $sql .= '
                    products_short_desc = ' . ep_db_input($v_products_short_desc[$key]) . ',
                    ';
              }
              $sql .= '
                  products_url = ' . ep_db_input($v_products_url[$key]) . '
                WHERE
                  products_id = ' . (int)$v_products_id . ' AND
                  language_id = ' . (int)$key;

              $result  =& ep_query($sql);
            }
            else
            {
              $sql =
                'INSERT INTO ' . TABLE_PRODUCTS_DESCRIPTION . '
                  (products_id,
                  language_id,
                  products_name,
                  products_description,';
              if (isset($ep_supported_mods['psd']))
              {
                $sql .= '
                  products_short_desc, ';
              }
              $sql .= '
                  products_url)
                  VALUES (
                    ' . (int)$v_products_id . ',
                    ' . $key . ',
                    ' . ep_db_input($name) . ',
                    ' . ep_db_input($v_products_description[$key]) . ',
                    ';
              if (isset($ep_supported_mods['psd']))
              {
                $sql .= ep_db_input($v_products_short_desc[$key]) . ', ';
              }
              $sql .= ep_db_input($v_products_url[$key]) . '
                    )';
              $result  =& ep_query($sql);
            }
          }
        }
      }

      if (isset($v_attribute_options_id_1))
      {
        $has_attributes = true;
        $attribute_rows = 1;
        $attribute_options_count = 1;
        $v_attribute_options_id_var = 'v_attribute_options_id_' . $attribute_options_count;
        while (isset($$v_attribute_options_id_var) && $$v_attribute_options_id_var != '')
        {


          $attributes_clean_query = 'DELETE FROM ' . TABLE_PRODUCTS_ATTRIBUTES . ' WHERE products_id = ' . (int)$v_products_id . ' AND options_id = ' . (int)$$v_attribute_options_id_var;
          ep_query($attributes_clean_query);
          $attribute_options_query = 'SELECT products_options_name FROM (' . TABLE_PRODUCTS_OPTIONS . ') WHERE products_options_id = ' . (int)$$v_attribute_options_id_var;
          $attribute_options_values  =& ep_query($attribute_options_query);

          if ($attribute_rows == 1)
          {
            if (!$attribute_options_values->isSuccess() || $attribute_options_values->getRowCount() == 0)
            {
              foreach ($ep_languages as $lang)
              {
               $v_attribute_options_name_var = 'v_attribute_options_name_' . $attribute_options_count . '_' . $lang['id'];
                if (isset($$v_attribute_options_name_var))
                {
                  $attribute_options_insert_query = 'INSERT INTO ' . TABLE_PRODUCTS_OPTIONS . '
                  (products_options_id, language_id, products_options_name)
                  VALUES (' . (int)$$v_attribute_options_id_var . ', ' . (int)$lang['id'] . ', ' . ep_db_input($$v_attribute_options_name_var) . ')';
                  $attribute_options_insert =& ep_query($attribute_options_insert_query);
                }
              }
            }
            else
            {
              foreach ($ep_languages as $lang)
              {
                $v_attribute_options_name_var = 'v_attribute_options_name_' . $attribute_options_count . '_' . $lang['id'];
                if (isset($$v_attribute_options_name_var))
                {
                  $attribute_options_update_lang_query = 'SELECT products_options_name FROM (' . TABLE_PRODUCTS_OPTIONS . ') where products_options_id = ' . (int)$$v_attribute_options_id_var . ' AND language_id = ' . (int)$lang['id'];
                  $attribute_options_update_lang_values  =& ep_query($attribute_options_update_lang_query);
                  if (!$attribute_options_update_lang_values->isSuccess() || $attribute_options_update_lang_values->getRowCount() == 0)
                  {
                    $attribute_options_lang_insert_query = 'INSERT INTO ' . TABLE_PRODUCTS_OPTIONS . '
                    (products_options_id, language_id, products_options_name)
                    VALUES (' . (int)$$v_attribute_options_id_var . ',
                    ' . (int)$lang['id'] . ', ' . ep_db_input($$v_attribute_options_name_var) . ')';
                    ep_query($attribute_options_lang_insert_query);
                  }
                  else
                  {
                    $attribute_options_update_query = 'UPDATE ' . TABLE_PRODUCTS_OPTIONS .
                    ' SET products_options_name = ' . ep_db_input($$v_attribute_options_name_var) .
                    ' WHERE products_options_id = ' . (int)$$v_attribute_options_id_var .
                    ' AND language_id = ' . (int)$lang['id'];
                    ep_query($attribute_options_update_query);
                  }
                }
              }
            }
          }

          $attribute_values_count = 1;
          $v_attribute_values_id_var = 'v_attribute_values_id_' . $attribute_options_count . '_' . $attribute_values_count;

          while (isset($$v_attribute_values_id_var) && $$v_attribute_values_id_var != '')
          {
            $attribute_values_query = 'SELECT products_options_values_name FROM (' . TABLE_PRODUCTS_OPTIONS_VALUES . ')
            WHERE products_options_values_id = ' . (int)$$v_attribute_values_id_var;
            $attribute_values_values  =& ep_query($attribute_values_query);

            if ($attribute_rows)
            {
              if (!$attribute_values_values->isSuccess() || $attribute_values_values->getRowCount() == 0)
              {
                foreach ($ep_languages as $lang)
                {
                  $v_attribute_values_name_var = 'v_attribute_values_name_' . $attribute_options_count . '_' . $attribute_values_count . '_' . $lang['id'];
                  if (isset($$v_attribute_values_name_var))
                  {
                    $attribute_values_insert_query = 'INSERT INTO ' . TABLE_PRODUCTS_OPTIONS_VALUES . ' (
                    products_options_values_id,
                    language_id,
                    products_options_values_name
                    ) values (
                    ' . (int)$$v_attribute_values_id_var . ',
                    ' . (int)$lang['id'] . ',
                    ' . ep_db_input($$v_attribute_values_name_var) . '
                    )';
                    ep_query($attribute_values_insert_query);
                  }
                }
                $attribute_values_pov2po_query = 'INSERT INTO ' . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . '(
                products_options_id,
                products_options_values_id
                ) values (
                ' . (int)$$v_attribute_options_id_var . ',
                ' . (int)$$v_attribute_values_id_var . '
                )';
                ep_query($attribute_values_pov2po_query);
              }
              else
              {
                foreach ($ep_languages as $lang)
                {
                  $v_attribute_values_name_var = 'v_attribute_values_name_' . $attribute_options_count . '_' . $attribute_values_count . '_' . $lang['id'];
                  if (isset($$v_attribute_values_name_var))
                  {
                    $attribute_values_update_lang_query = 'SELECT products_options_values_name FROM (' . TABLE_PRODUCTS_OPTIONS_VALUES . ')
                    WHERE products_options_values_id =' . (int)$$v_attribute_values_id_var . '
                    AND language_id =' . (int)$lang['id'];
                    $attribute_values_update_lang_values  =& ep_query($attribute_values_update_lang_query);

                    if (!$attribute_values_update_lang_values->isSuccess() || $attribute_values_update_lang_values->getRowCount() == 0)
                    {
                      $attribute_values_lang_insert_query = 'INSERT INTO ' . TABLE_PRODUCTS_OPTIONS_VALUES . '(
                      products_options_values_id,
                      language_id,
                      products_options_values_name
                      ) values (
                      ' . (int)$$v_attribute_values_id_var . ',
                      ' . (int)$lang['id'] . ',
                      ' . ep_db_input($$v_attribute_values_name_var) . '
                      )';
                      ep_query($attribute_values_lang_insert_query);
                    }
                    else
                    {
                      $attribute_values_update_query = 'UPDATE ' . TABLE_PRODUCTS_OPTIONS_VALUES . '
                      SET products_options_values_name = ' . ep_db_input($$v_attribute_values_name_var) . '
                      WHERE products_options_values_id = ' . (int)$$v_attribute_values_id_var . '
                      AND language_id = ' . (int)$lang['id'];
                      ep_query($attribute_values_update_query);
                    }
                  }
                }
              }
            }

            $v_attribute_values_price_var = 'v_attribute_values_price_' . $attribute_options_count . '_' . $attribute_values_count;
            if (isset($$v_attribute_values_price_var) && ($$v_attribute_values_price_var != ''))
            {
              $attribute_prices_query = 'SELECT options_values_price, price_prefix FROM (' . TABLE_PRODUCTS_ATTRIBUTES . ')
              WHERE products_id = ' . (int)$v_products_id . '
              AND options_id = ' . (int)$$v_attribute_options_id_var . '
              AND options_values_id = ' . (int)$$v_attribute_values_id_var;
              $attribute_prices_values  =& ep_query($attribute_prices_query);
              $attribute_values_price_prefix = ($$v_attribute_values_price_var < 0) ? '-' : '+';

              if (!$attribute_prices_values->isSuccess() || $attribute_prices_values->getRowCount() == 0)
              {
                $attribute_prices_insert_query = 'INSERT INTO ' . TABLE_PRODUCTS_ATTRIBUTES . '
                (products_id,
                options_id,
                options_values_id,
                options_values_price,
                price_prefix
                ) values (
                ' . (int)$v_products_id . ',
                ' . (int)$$v_attribute_options_id_var . ',
                ' . (int)$$v_attribute_values_id_var . ',
                ' . (float)$$v_attribute_values_price_var . ',
                ' . ep_db_input($attribute_values_price_prefix) . '
                 )';
                ep_query($attribute_prices_insert_query);
              }
              else
              {
                $attribute_prices_update_query = 'UPDATE ' . TABLE_PRODUCTS_ATTRIBUTES . '
                SET options_values_price = ' . (float)$$v_attribute_values_price_var . ',
                price_prefix = ' . ep_db_input($attribute_values_price_prefix) . '
                WHERE products_id = ' . (int)$v_products_id . '
                AND options_id = ' . (int)$$v_attribute_options_id_var . '
                AND options_values_id = ' . (int)$$v_attribute_values_id_var;
                ep_query($attribute_prices_update_query);
              }
            }
            $attribute_values_count++;
            $v_attribute_values_id_var = 'v_attribute_values_id_' . $attribute_options_count . '_' . $attribute_values_count;
          }
          $attribute_options_count++;
          $v_attribute_options_id_var = 'v_attribute_options_id_' . $attribute_options_count;
        }
        $attribute_rows++;
      }

      if (isset($v_specials_price) && !ep_empty($v_specials_price))
      {
        if ($v_specials_price >= $v_products_price)
        {
          $display['EASYPOPULATE_DISPLAY_RESULT_SPECIALS'] .= sprintf(EASYPOPULATE_SPECIALS_PRICE_FAIL, $$v_primary_index, substr(strip_tags($v_products_name[$language_id_default]), 0, 10));
          $ep_counter['partial_errors']++;

          continue;
        }

        $has_specials = true;
        $v_specials_date_available = !ep_empty($v_specials_date_available) ? ep_datoriser($v_specials_date_available) : '0001-01-01';
        $v_specials_expires_date = !ep_empty($v_specials_expires_date) ? ep_datoriser($v_specials_expires_date) : '0001-01-01';

        $special  =& ep_query('SELECT products_id
                               FROM (' . TABLE_SPECIALS . ')
                               WHERE products_id = ' . (int)$v_products_id);
        if (!$special->getRowCount())
        {
          if ($v_specials_price == '0')
          {
            $display['EASYPOPULATE_DISPLAY_RESULT_SPECIALS'] .= sprintf(EASYPOPULATE_SPECIALS_DELETE_FAIL, $$v_primary_index, substr($v_products_name[$language_id_default], 0, 10));
            $ep_counter['partial_errors']++;
          }
          else
          {
            $ep_price_sorter = true;
            $sql =  'INSERT INTO ' . TABLE_SPECIALS . '
                    (products_id,
                    specials_new_products_price,
                    specials_date_added,
                    specials_date_available,
                    expires_date,
                    status)
                    VALUES (
                        ' . (int)$v_products_id . ',
                        ' . ep_db_input($v_specials_price) . ",
                        now(),
                        " . ep_db_input($v_specials_date_available) . ',
                        ' . ep_db_input($v_specials_expires_date) . ",
                        '1')";
            $result  =& ep_query($sql);
          }
        }
        else
        {
          if ($v_specials_price == '0')
          {
            ep_query('DELETE FROM' . TABLE_SPECIALS . '
                   WHERE products_id = ' . (int)$v_products_id);
          }
          else
          {
            $ep_price_sorter = true;
            $sql =  'UPDATE ' . TABLE_SPECIALS . '
                    SET
                    specials_new_products_price = ' . ep_db_input($v_specials_price) . ",
                    specials_last_modified = now(),
                    specials_date_available = " . ep_db_input($v_specials_date_available) . ',
                    expires_date = ' . ep_db_input($v_specials_expires_date) . ",
                    status = '1'
                    WHERE products_id = " . (int)$v_products_id;
            ep_query($sql);
          }
        }
      }

      if (isset($filelayout['v_metatags_title_status']) || isset($filelayout['v_metatags_products_name_status']) || isset($filelayout['v_metatags_model_status']) || isset($filelayout['v_metatags_price_status']) || isset($filelayout['v_metatags_title_tagline_status']))
      {
        ep_query('UPDATE ' . TABLE_PRODUCTS .
        ' SET metatags_title_status = ' . $v_metatags_title_status .
        ', metatags_products_name_status = ' . $v_metatags_products_name_status .
        ', metatags_model_status = ' . $v_metatags_model_status .
        ', metatags_price_status = ' . $v_metatags_price_status .
        ', metatags_title_tagline_status = ' . $v_metatags_title_tagline_status .
        ' WHERE products_id = ' . (int)$v_products_id);

        $allow_delete_meta = false;
        if ($v_metatags_title_status == 0 && $v_metatags_products_name_status == 0 && $v_metatags_model_status == 0 && $v_metatags_price_status == 0 && $v_metatags_title_tagline_status == 0)
        {
          $allow_delete_meta = true;
        }
        $meta_result  =& ep_query('SELECT language_id, metatags_title, metatags_keywords, metatags_description
                        FROM (' . TABLE_META_TAGS_PRODUCTS_DESCRIPTION . ')
                        WHERE products_id = ' . (int)$v_products_id);
        $meta_data = array();
        $meta_data_new = array();
        $meta_data_update = array();
        for ($i = 0; $meta_row = $meta_result->getRow($i, ECLIPSE_DB_ASSOC); $i++)
        {
          $meta_data[$meta_row['language_id']]['title'] = $meta_row['metatags_title'];
          $meta_data[$meta_row['language_id']]['keywords'] = $meta_row['metatags_keywords'];
          $meta_data[$meta_row['language_id']]['description'] = $meta_row['metatags_description'];
        }
        $meta_row_count = $i;
        
        $meta_string = '';
        $meta_sql = array();
        foreach ($ep_languages as $lang)
        {
          if (!isset($meta_data[$lang['id']]) && $meta_row_count > 0)
          {
            if (isset($meta_data[$language_id_default]))
            {
              $meta_data_new[$lang['id']]['title'] = $meta_data[$language_id_default]['title'];
              $meta_data_new[$lang['id']]['keywords'] = $meta_data[$language_id_default]['keywords'];
              $meta_data_new[$lang['id']]['description'] = $meta_data[$language_id_default]['description'];
            }
            else
            {
              $meta_data_new[$lang['id']]['title'] = '';
              $meta_data_new[$lang['id']]['keywords'] = '';
              $meta_data_new[$lang['id']]['description'] = '';
            }
          }
          foreach (array('title', 'keywords', 'description') as $meta_type)
          {
            if (isset($filelayout['v_metatags_' . $meta_type . '_' . $lang['code']]))
            {
              if (!isset($meta_data[$lang['id']][$meta_type]))
              {
                $meta_data_new[$lang['id']][$meta_type] = $items[$filelayout['v_metatags_' . $meta_type . '_' . $lang['code']]];
                $meta_string .= $meta_data_new[$lang['id']][$meta_type];
              }
              else
              {
                $meta_data_update[$lang['id']][$meta_type] = $items[$filelayout['v_metatags_' . $meta_type . '_' . $lang['code']]];
                $meta_string .= $meta_data_update[$lang['id']][$meta_type];
              }
            }
          }
        }

        if (strlen($meta_string) === 0)
        {
          if ($meta_row_count > 0)
          {
            if (isset($filelayout['v_metatags_title_' . $ep_languages[$language_id_default]['code']]) && isset($filelayout['v_metatags_keywords_' . $ep_languages[$language_id_default]['code']]) & $allow_delete_meta == true)
            {
              ep_query('DELETE FROM ' . TABLE_META_TAGS_PRODUCTS_DESCRIPTION . ' WHERE products_id = ' . (int)$v_products_id);
              $ep_counter['meta_deleted']++;
            }
            else
            {
              $display['EASYPOPULATE_DISPLAY_RESULT_META'] .= sprintf(EASYPOPULATE_META_DELETE_FAIL, $$v_primary_index);
              $ep_counter['meta_delete_failed']++;
            }
          }
        }
        else
        {
          foreach ($meta_data_new as $lid => $data)
          {
            ep_query('INSERT INTO ' . TABLE_META_TAGS_PRODUCTS_DESCRIPTION . ' (products_id, language_id, metatags_title, metatags_keywords, metatags_description) VALUES (' . (int)$v_products_id . ',' . (int)$lid . ',' . ep_db_input($data['title']) . ',' . ep_db_input($data['keywords']) . ',' . ep_db_input($data['description']) . ')');
            if ($meta_row_count > 0)
            {
              $ep_counter['meta_repaired']++;
            }
            else
            {
              $ep_counter['meta_added']++;
            }
          }
          foreach ($meta_data_update as $lid => $data)
          {
            ep_query('UPDATE ' . TABLE_META_TAGS_PRODUCTS_DESCRIPTION . ' SET metatags_title = ' . ep_db_input($data['title']) . ', metatags_keywords = ' . ep_db_input($data['keywords']) . ', metatags_description = ' . ep_db_input($data['description']) . ' WHERE products_id = ' . (int)$v_products_id . ' AND language_id = ' . (int)$lid);
            $ep_counter['meta_updated']++;
          }
        }
        unset($meta_data); unset($meta_data_new); unset($meta_data_update);
      }


      if ($has_attributes)
      {

        zen_update_attributes_products_option_values_sort_order($v_products_id);
      }
      if($ep_price_sorter)
      {
        zen_update_products_price_sorter($v_products_id);
      }
    }
    ep_flush();
  }

  if ($has_specials)
  {

    zen_expire_specials();
  }
}
