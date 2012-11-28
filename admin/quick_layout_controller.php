<?php
//
// +----------------------------------------------------------------------+
// |zen-cart Open Source E-commerce                                       |
// +----------------------------------------------------------------------+
// | Copyright (c) 2003 The zen-cart developers                           |
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
//  $Id: layout_controller.php 2981 2006-02-07 04:59:30Z ajeh $
//


  require('includes/application_top.php');

// Check all exisiting boxes are in the main /sideboxes
  $boxes_directory = DIR_FS_CATALOG_MODULES . 'sideboxes/';

  $file_extension = substr($PHP_SELF, strrpos($PHP_SELF, '.'));
  $directory_array = array();
  if ($dir = @dir($boxes_directory)) {
    while ($file = $dir->read()) {
      if (!is_dir($boxes_directory . $file)) {
        if (substr($file, strrpos($file, '.')) == $file_extension) {
          if ($file != 'empty.txt') {
            $directory_array[] = $file;
          }
        }
      }
    }
    if (sizeof($directory_array)) {
      sort($directory_array);
    }
    $dir->close();
  }

// Check all exisiting boxes are in the current template /sideboxes/template_dir
  $dir_check= $directory_array;
  $boxes_directory = DIR_FS_CATALOG_MODULES . 'sideboxes/' . $template_dir . '/';

  $file_extension = substr($PHP_SELF, strrpos($PHP_SELF, '.'));

  if ($dir = @dir($boxes_directory)) {
    while ($file = $dir->read()) {
      if (!is_dir($boxes_directory . $file)) {
          if (in_array($file, $dir_check, TRUE)) {
            // skip name exists
          } else {
            if ($file != 'empty.txt') {
              $directory_array[] = $file;
            }
          }
      }
    }
    sort($directory_array);
    $dir->close();
  }

  $warning_new_box='';
  $installed_boxes = array();
  for ($i = 0, $n = sizeof($directory_array); $i < $n; $i++) {
    $file = $directory_array[$i];

// Verify Definitions
    $definitions = $db->Execute("select layout_box_name from " . TABLE_LAYOUT_BOXES . " where layout_box_name='" . $file . "' and layout_template='" . $template_dir . "'");
    if ($definitions->EOF) {
      if (!strstr($file, 'ezpages_bar')) {
        $warning_new_box .= $file . ' ';
      } else {
        // skip ezpage sideboxes
//        $warning_new_box .= $file . ' - HIDDEN ';
      }
      $db->Execute("insert into " . TABLE_LAYOUT_BOXES . "
                  (layout_template, layout_box_name, layout_box_status, layout_box_location, layout_box_sort_order, layout_box_sort_order_single, layout_box_status_single)
                  values ('" . $template_dir  . "', '" . $file . "', 0, 0, 0, 0, 0)");
    }
  }

////////////////////////////////////
  if ($_GET['action']) {
    switch ($_GET['action']) {
      case 'insert':
        $layout_box_name = zen_db_prepare_input($_POST['layout_box_name']);
        $layout_box_status = zen_db_prepare_input($_POST['layout_box_status']);
        $layout_box_location = zen_db_prepare_input($_POST['layout_box_location']);
        $layout_box_sort_order = zen_db_prepare_input($_POST['layout_box_sort_order']);
        $layout_box_sort_order_single = zen_db_prepare_input($_POST['layout_box_sort_order_single']);
        $layout_box_status_single = zen_db_prepare_input($_POST['layout_box_status_single']);

        $db->Execute("insert into " . TABLE_LAYOUT_BOXES . "
                    (layout_box_name, layout_box_status, layout_box_location, layout_box_sort_order, layout_box_sort_order_single, layout_box_status_single)
                    values ('" . zen_db_input($layout_box_name) . "',
                            '" . zen_db_input($layout_box_status) . "',
                            '" . zen_db_input($layout_box_location) . "',
                            '" . zen_db_input($layout_box_sort_order) . "',
                            '" . zen_db_input($layout_box_sort_order_single) . "',
                            '" . zen_db_input($layout_box_status_single) . "')");

        $messageStack->add_session(SUCCESS_BOX_ADDED . $_GET['layout_box_name'], 'success');
        zen_redirect(zen_href_link(FILENAME_QUICK_LAYOUT_CONTROLLER));
        break;
      case 'quick_update':
      	$field = 'layout_box_status';
        foreach($_POST[$field] as $layout_id => $value) {
        	if(!isset($value['new'])) $value['new']='0';
        	if($value['old'] != $value['new']) $new_layout[$layout_id][$field] = (int)$value['new'];
        }
        $field = 'layout_box_location';
        foreach($_POST[$field] as $layout_id => $value) {
        	if(!isset($value['new'])) $value['new']='0';
        	if($value['old'] != $value['new']) $new_layout[$layout_id][$field] = (int)$value['new'];
        }
        $field = 'layout_box_sort_order';
        foreach($_POST[$field] as $layout_id => $value) {
        	if(!isset($value['new'])) $value['new']='0';
        	if($value['old'] != $value['new']) $new_layout[$layout_id][$field] = (int)$value['new'];
        }
        $field = 'layout_box_sort_order_single';
        foreach($_POST[$field] as $layout_id => $value) {
        	if(!isset($value['new'])) $value['new']='0';
        	if($value['old'] != $value['new']) $new_layout[$layout_id][$field] = (int)$value['new'];
        }
        $field = 'layout_box_status_single';
        foreach($_POST[$field] as $layout_id => $value) {
        	if(!isset($value['new'])) $value['new']='0';
        	if($value['old'] != $value['new']) $new_layout[$layout_id][$field] = (int)$value['new'];
        }        
//        echo '<pre>';var_dump($_POST["layout_box_name"]);echo '</pre>';
        foreach($new_layout as $layout_id => $value) {
//        	echo '<pre>';var_dump($_POST['layout_box_name'][$layout_id], $layout_id, $value);echo '</pre>';
        	zen_db_perform(TABLE_LAYOUT_BOXES, $value, 'update', 'layout_id=' . (int)$layout_id);
        	$messageStack->add_session(SUCCESS_BOX_UPDATED . $_POST['layout_box_name'][$layout_id], 'success');
        }
        zen_redirect(zen_href_link(FILENAME_QUICK_LAYOUT_CONTROLLER, 'page=' . $_GET['page'] . '&cID=' . $box_id));
        break;
      case 'reset_defaults':
        $reset_boxes = $db->Execute("select * from " . TABLE_LAYOUT_BOXES . " where layout_template= 'default_template_settings'");
        while (!$reset_boxes->EOF) {
          $db->Execute("update " . TABLE_LAYOUT_BOXES . " set layout_box_status= '" . $reset_boxes->fields['layout_box_status'] . "', layout_box_location= '" . $reset_boxes->fields['layout_box_location'] . "', layout_box_sort_order='" . $reset_boxes->fields['layout_box_sort_order'] . "', layout_box_sort_order_single='" . $reset_boxes->fields['layout_box_sort_order_single'] . "', layout_box_status_single='" . $reset_boxes->fields['layout_box_status_single'] . "' where layout_box_name='" . $reset_boxes->fields['layout_box_name'] . "' and layout_template='" . $template_dir . "'");
          $reset_boxes->MoveNext();
        }

        $messageStack->add_session(SUCCESS_BOX_RESET . $template_dir, 'success');
        zen_redirect(zen_href_link(FILENAME_QUICK_LAYOUT_CONTROLLER, 'page=' . $_GET['page']));
        break;
    }
  }

?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
<script language="javascript" src="includes/menu.js"></script>
<script language="javascript" src="includes/general.js"></script>
<script type="text/javascript">
  <!--
  function init()
  {
    cssjsmenu('navbar');
    if (document.getElementById)
    {
      var kill = document.getElementById('hoverJS');
      kill.disabled = true;
    }
  }
  // -->
</script>
</head>
<body onload="init()">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">

      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
<?php
if ($warning_new_box) {
?>
        <tr class="messageStackError">
          <td colspan="2" class="messageStackError">
<?php echo 'WARNING: New boxes found: ' . $warning_new_box; ?>
          </td>
        </tr>
<?php
}
?>
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE . ' ' . $template_dir; ?></td>
            <td class="pageHeading" align="right"><?php echo zen_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>

      <tr>
        <td>            	<?php echo zen_draw_form('quick_update', FILENAME_QUICK_LAYOUT_CONTROLLER, 'page=' . $_GET['page'] . '&action=quick_update', 'post'); ?>
        	<table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="main" align="left"><strong>Boxes Path: </strong><?php echo DIR_FS_CATALOG_MODULES . ' ... ' . '<br />&nbsp;'; ?></td>
          </tr>
          <tr>
            <td valign="top">
              <table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr valign="top"><td  colspan="6"class="dataTableHeadingContent" align="right"><?php echo zen_image_submit('button_update.gif', IMAGE_UPDATE, 'align="right"'); ?></td></tr>
              <tr valign="top"><td valign="top" colspan="6" ><?php echo zen_draw_separator('pixel_trans.gif', '75%', '10'); ?></td></tr>
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent" align="left" width="200"><?php echo TABLE_HEADING_LAYOUT_BOX_NAME; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_LAYOUT_BOX_STATUS; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_LAYOUT_BOX_LOCATION; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_LAYOUT_BOX_SORT_ORDER; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_LAYOUT_BOX_SORT_ORDER_SINGLE; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_LAYOUT_BOX_STATUS_SINGLE; ?></td>
              </tr>

<?php
  $boxes_directory = DIR_FS_CATALOG_MODULES . 'sideboxes' . '/';
  $boxes_directory_template = DIR_FS_CATALOG_MODULES . 'sideboxes/' . $template_dir . '/';

  $column_controller = $db->Execute("select layout_id, layout_box_name, layout_box_status, layout_box_location, layout_box_sort_order, layout_box_sort_order_single, layout_box_status_single from " . TABLE_LAYOUT_BOXES . " where (layout_template='" . $template_dir . "' and layout_box_name NOT LIKE '%ezpages_bar%') order by  layout_box_location, layout_box_sort_order");
  while (!$column_controller->EOF) {
  	$td_class = (file_exists($boxes_directory . $column_controller->fields['layout_box_name']) or file_exists($boxes_directory_template . $column_controller->fields['layout_box_name'])) ? "dataTableContent" : "messageStackError";
?>
              <tr class="dataTableRow" onmouseover="this.className='dataTableRowOver';" onmouseout="this.className='dataTableRow';">
                <td class="dataTableContent" width="100"><?php echo (file_exists($boxes_directory_template . $column_controller->fields['layout_box_name']) ? '<span class="alert">' . ereg_replace(DIR_FS_CATALOG_MODULES, '', $boxes_directory_template) . '</span>' . $column_controller->fields['layout_box_name'] : ereg_replace(DIR_FS_CATALOG_MODULES, '', $boxes_directory) . $column_controller->fields['layout_box_name']); ?>
                <?php echo zen_draw_hidden_field('layout_box_name[' . $column_controller->fields['layout_id'] . ']', $column_controller->fields['layout_box_name']) ?>
                </td>
                <td class="<?php echo $td_class; ?>" align="center">
                	<?php echo zen_draw_hidden_field('layout_box_status[' . $column_controller->fields['layout_id'] . '][old]', $column_controller->fields['layout_box_status']) .
                	zen_draw_checkbox_field('layout_box_status[' . $column_controller->fields['layout_id'] . '][new]', 1, false, $column_controller->fields['layout_box_status']); ?>
                </td>
                <td class="<?php echo $td_class; ?>" align="center">
                	<?php echo zen_draw_hidden_field('layout_box_location[' . $column_controller->fields['layout_id'] . '][old]', $column_controller->fields['layout_box_location']) .
                	zen_draw_radio_field('layout_box_location[' . $column_controller->fields['layout_id'] . '][new]', $value = '0', $checked = false, $column_controller->fields['layout_box_location'], '') .
                	zen_draw_radio_field('layout_box_location[' . $column_controller->fields['layout_id'] . '][new]', $value = '1', $checked = false, $column_controller->fields['layout_box_location'], '')
                	; ?>
                </td>
                <td class="<?php echo $td_class; ?>" align="center">
                	<?php echo zen_draw_hidden_field('layout_box_sort_order[' . $column_controller->fields['layout_id'] . '][old]', $column_controller->fields['layout_box_sort_order']) . zen_draw_input_field('layout_box_sort_order[' . $column_controller->fields['layout_id'] . '][new]', $column_controller->fields['layout_box_sort_order'], 'size="3" style="text-align: right;"'); ?>
                </td>
                <td class="<?php echo $td_class; ?>" align="center">
                	<?php echo zen_draw_hidden_field('layout_box_sort_order_single[' . $column_controller->fields['layout_id'] . '][old]', $column_controller->fields['layout_box_sort_order_single']) . zen_draw_input_field('layout_box_sort_order_single[' . $column_controller->fields['layout_id'] . '][new]', $column_controller->fields['layout_box_sort_order_single'], 'size="3" style="text-align: right;"'); ?>
                </td>
                <td class="<?php echo $td_class; ?>" align="center">
                	<?php echo zen_draw_hidden_field('layout_box_status_single[' . $column_controller->fields['layout_id'] . '][old]', $column_controller->fields['layout_box_status_single']) .
                  zen_draw_checkbox_field('layout_box_status_single[' . $column_controller->fields['layout_id'] . '][new]', 1, false, $column_controller->fields['layout_box_status_single']); ?>
                </td>
              </tr>

<?php
    $last_box_column = $column_controller->fields['layout_box_location'];
    $column_controller->MoveNext();
    if (($column_controller->fields['layout_box_location'] != $last_box_column) and !$column_controller->EOF) {
?>
              <tr valign="top">
                <td colspan="6" height="20" align="center" valign="middle"><?php echo zen_draw_separator('pixel_black.gif', '90%', '3'); ?></td>
              </tr>
<?php
    }
  }
?>

              <tr valign="top">
                <td valign="top" colspan="6" ><?php echo zen_draw_separator('pixel_trans.gif', '75%', '10'); ?></td>
              </tr>
            <tr><td  colspan="6"class="dataTableHeadingContent" align="right"><?php echo zen_image_submit('button_update.gif', IMAGE_UPDATE, 'align="right"'); ?></td></tr>
            </table>
            </form>
            </td>

          </tr>

          <tr>
            <td><table align="center">
              <tr>
                <td class="main" align="left">
                  <?php echo '<br />' . TEXT_INFO_RESET_TEMPLATE_SORT_ORDER . '<strong>' . $template_dir . '<strong>'; ?>
                </td>
              </tr>
              <tr>
                <td class="main" align="center">
                  <?php echo TEXT_INFO_RESET_TEMPLATE_SORT_ORDER_NOTE; ?>
                </td>
              </tr>
              <tr>
                <td class="main" align="center">
                  <?php echo '<br /><a href="' . zen_href_link(FILENAME_QUICK_LAYOUT_CONTROLLER, 'page=' . $_GET['page'] . '&action=reset_defaults') . '">' . zen_image_button('button_reset.gif', IMAGE_RESET) . '</a>'; ?>
                </td>
              </tr>
            </table></td>
          </tr>
          <tr valign="top">
            <td valign="top"><?php echo zen_draw_separator('pixel_trans.gif', '1', '100'); ?></td>
          </tr>

<!-- end of display -->

        </table></td>
      </tr>
    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br />
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>