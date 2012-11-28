<?php

  function zen_display_banner_main($action) {
    global $db, $request_type;


$banner_query = "SELECT * from ". TABLE_BANNERS ."
             WHERE banners_group = '$action' and status = 1";
$banner = $db->Execute($banner_query);
$banner_string ='';
while (!$banner->EOF) { 
    if (zen_not_null($banner->fields['banners_html_text'])) {
      $banner_string = '';
    } else {
      if ($banner->fields['banners_url'] == '') {
        $banner_string .= zen_image(DIR_WS_IMAGES . $banner->fields['banners_image'], $banner->fields['banners_title']);
      } else {
        if ($banner->fields['banners_open_new_windows'] == '1') {
          $banner_string .= '<a href="' . zen_href_link(FILENAME_REDIRECT, 'action=banner&goto=' . $banner->fields['banners_id']) . '" target="_blank">' . zen_image(DIR_WS_IMAGES . $banner->fields['banners_image'], $banner->fields['banners_title']) . '</a>'. "\n";
        } else {
          $banner_string .= '<a href="' . zen_href_link(FILENAME_REDIRECT, 'action=banner&goto=' . $banner->fields['banners_id']) . '">' . zen_image(DIR_WS_IMAGES . $banner->fields['banners_image'], $banner->fields['banners_title']) . '</a>' . "\n";
        }
      }
    }

   $banner->MoveNext();
   }

    return $banner_string;
  }
  function zen_get_manufacturers_desc($product_id) {
    global $db;

    $product_query = "select manufacturers_description
                      from " . TABLE_MANUFACTURERS . "
                      where manufacturers_id = '" . (int)$product_id . "'";

    $product =$db->Execute($product_query);

    return ($product->RecordCount() > 0) ? '<div class="manu_desc">' . $product->fields['manufacturers_description'] . '</div>' : "";
  }
?>
