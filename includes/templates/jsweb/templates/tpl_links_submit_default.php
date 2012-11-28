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
//  Original contrib by Vijay Immanuel for osCommerce, converted to zen by dave@open-operations.com - http://www.open-operations.com
//  $Id: links_manager.php 2006-12-22 Clyde Jones
//
?>
<div class="centerColumn" id="linksSubmitDefault">
<?php echo HEADING_TITLE; ?>

<?php echo zen_draw_form('submit_link', zen_href_link(FILENAME_LINKS_SUBMIT, ''), 'post', 'onSubmit="return check_form(submit_link);"') . zen_draw_hidden_field('action', 'process'); ?>

<?php if ($messageStack->size('submit_link') > 0) echo $messageStack->output('submit_link'); ?>

<fieldset id="linksSubmitForm">
<legend><?php echo NAVBAR_TITLE_2; ?></legend>
<div class="alert forward"><?php echo FORM_REQUIRED_INFORMATION; ?></div>
<br class="clearBoth" />

<fieldset>
<legend><?php echo CATEGORY_WEBSITE; ?></legend>
<label class="inputLabel" for="links_title"><?php echo ENTRY_LINKS_TITLE; ?></label>
<?php echo zen_draw_input_field('links_title') . '&nbsp;' . (zen_not_null(ENTRY_LINKS_TITLE_TEXT) ? '<span class="alert">' . ENTRY_LINKS_TITLE_TEXT . '</span>': ''); ?>
<br class="clearBoth" />
<label class="inputLabel" for="links_url"><?php echo ENTRY_LINKS_URL; ?></label>
<?php echo zen_draw_input_field('links_url', 'http://') . '&nbsp;' . (zen_not_null(ENTRY_LINKS_URL_TEXT) ? '<span class="alert">' . ENTRY_LINKS_URL_TEXT . '</span>': ''); ?>
<br class="clearBoth" />
<?php
  //link category drop-down list
  $categories_array = array();
  $categories_values = $db->Execute("select lcd.link_categories_id, lcd.link_categories_name from " . TABLE_LINK_CATEGORIES_DESCRIPTION . " lcd where lcd.language_id = '" . (int)$_SESSION['languages_id'] . "' order by lcd.link_categories_name");
  while (!$categories_values->EOF) {
    $categories_array[] = array('id' => $categories_values->fields['link_categories_name'], 'text' => $categories_values->fields['link_categories_name']);
    $categories_values->MoveNext();
  }
  if (isset($HTTP_GET_VARS['lPath'])) {
    $current_categories_id = $HTTP_GET_VARS['lPath'];
    $categories = $db->Execute("select link_categories_name from " . TABLE_LINK_CATEGORIES_DESCRIPTION . " where link_categories_id ='" . (int)$current_categories_id . "' and language_id ='" . (int)$_SESSION['languages_id'] . "'");
    $default_category = $categories->fields['link_categories_name'];
  } else {
    $default_category = '';
  }
?>

<label class="inputLabel" for="links_category"><?php echo ENTRY_LINKS_CATEGORY; ?></label>
<?php echo zen_draw_pull_down_menu('links_category', $categories_array, $default_category) . '&nbsp;' . (zen_not_null(ENTRY_LINKS_CATEGORY_TEXT) ? '<span class="alert">' . ENTRY_LINKS_CATEGORY_TEXT . '</span>': '');?>
<br class="clearBoth" />
<label class="inputLabel" for="links_description"><?php echo ENTRY_LINKS_DESCRIPTION; ?></label>
<?php echo zen_draw_textarea_field('links_description', '20', '5') . '&nbsp;' . (zen_not_null(ENTRY_LINKS_DESCRIPTION_TEXT) ? '<span class="alert">' . ENTRY_LINKS_DESCRIPTION_TEXT . '</span>': '');?>
<br class="clearBoth" />
<label class="inputLabel" for="links_image"><?php echo ENTRY_LINKS_IMAGE; ?></label>
<?php echo zen_draw_input_field('links_image', 'http://') . '&nbsp;' . (zen_not_null(ENTRY_LINKS_IMAGE_TEXT) ? '<span class="alert">' . ENTRY_LINKS_IMAGE_TEXT . '</span>': ''); ?><?php echo '<a href="javascript:popupWindow(\'' . zen_href_link(FILENAME_POPUP_LINKS_HELP) . '\')">' . TEXT_LINKS_HELP_LINK . '</a>'; ?>
</fieldset>

<fieldset>
<legend><?php echo CATEGORY_CONTACT; ?></legend>
<label class="inputLabel" for="links_contact_name"><?php echo ENTRY_LINKS_CONTACT_NAME; ?></label>
<?php echo zen_draw_input_field('links_contact_name', $name) . '&nbsp;' . (zen_not_null(ENTRY_LINKS_CONTACT_NAME_TEXT) ? '<span class="alert">' . ENTRY_LINKS_CONTACT_NAME_TEXT . '</span>': ''); ?>
<br class="clearBoth" />
<label class="inputLabel" for="links_contact_email"><?php echo ENTRY_EMAIL_ADDRESS; ?></label>
<?php echo zen_draw_input_field('links_contact_email', $email) . '&nbsp;' . (zen_not_null(ENTRY_EMAIL_ADDRESS_TEXT) ? '<span class="alert">' . ENTRY_EMAIL_ADDRESS_TEXT . '</span>': ''); ?>
</fieldset>

<fieldset>
<?php if (SUBMIT_LINK_REQUIRE_RECIPROCAL == 'true') { ?>
<legend class="mmnleft"><?php echo CATEGORY_RECIPROCAL; ?></legend>
<label class="inputLabel" for="links_reciprocal_url"><?php echo ENTRY_LINKS_RECIPROCAL_URL; ?></label>
<?php echo zen_draw_input_field('links_reciprocal_url', 'http://') . '&nbsp;' . (zen_not_null(ENTRY_LINKS_RECIPROCAL_URL_TEXT) ? '<span class="alert">' . ENTRY_LINKS_RECIPROCAL_URL_TEXT . '</span>': ''); ?><?php echo '<a href="javascript:popupWindow(\'' . zen_href_link(FILENAME_POPUP_LINKS_HELP) . '\')">' . TEXT_LINKS_HELP_LINK . '</a>'; ?>
</fieldset>
<?php } ?>
</fieldset>
<?php
// BOF Captcha
if(is_object($captcha)) {
?>
<fieldset>
<legend><?php echo TITLE_CAPTCHA; ?></legend>
<?php echo $captcha->img(); ?>
<?php echo $captcha->redraw_button(BUTTON_IMAGE_CAPTCHA_REDRAW, BUTTON_IMAGE_CAPTCHA_REDRAW_ALT); ?>
<br class="clearBoth" />
<label for="captcha"><?php echo TITLE_CAPTCHA; ?></label>
<?php echo $captcha->input_field('captcha', 'id="captcha"') . '&nbsp;<span class="alert">' . TEXT_CAPTCHA . '</span>'; ?>
<br class="clearBoth" />
</fieldset>
<?php
}
// BOF Captcha
?>
<div class="buttonRow forward"><?php echo zen_image_submit(BUTTON_IMAGE_SUBMIT_LINK, BUTTON_SUBMIT_LINK_ALT); ?></div>
<div class="buttonRow back"><?php echo zen_back_link() . zen_image_button(BUTTON_IMAGE_BACK, BUTTON_BACK_ALT) . '</a>'; ?></div>
</form>

</div>
