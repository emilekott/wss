<?php
/**
 * @package    admin
 * @author     Conor Kerr <zen-cart.advshipper@dev.ceon.net>
 * @copyright  Copyright 2007-2009 Ceon
 * @copyright  Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright  Portions Copyright 2003 osCommerce
 * @link       http://dev.ceon.net/web/zen-cart/advshipper
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: advshipper_product_selector.php 382 2009-06-22 18:49:29Z Bob $
 */

require('includes/application_top.php');

require(DIR_FS_ADMIN . DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies();

$languages = zen_get_languages();

require_once(DIR_FS_ADMIN . DIR_WS_FUNCTIONS . 'advshipper.php');

if (isset($_GET['action'])) {
	$action = $_GET['action'];
} else {
	$action = (isset($_POST['action']) ? $_POST['action'] : '');
}

$products_filter = (isset($_GET['products_filter']) ? $_GET['products_filter'] : $products_filter);

$current_category_id = (isset($_GET['current_category_id']) ? $_GET['current_category_id'] : $current_category_id);

if ($action == 'new_cat') {
	$current_category_id = (isset($_GET['current_category_id']) ? $_GET['current_category_id'] : $current_category_id);
	$new_product_query = $db->Execute("select ptc.* from " . TABLE_PRODUCTS_TO_CATEGORIES . " ptc	left join " . TABLE_PRODUCTS_DESCRIPTION . " pd on ptc.products_id = pd.products_id and pd.language_id = '" . (int)$_SESSION['languages_id'] . "' where ptc.categories_id='" . $current_category_id . "' order by pd.products_name");
	$products_filter = $new_product_query->fields['products_id'];
	zen_redirect(zen_href_link(FILENAME_ADVANCED_SHIPPER_PRODUCT_SELECTOR, 'products_filter=' . $products_filter . '&current_category_id=' . $current_category_id));
}

// set categories and products if not set
if ($products_filter == '' and $current_category_id != '') {
	$new_product_query = $db->Execute("select ptc.* from " . TABLE_PRODUCTS_TO_CATEGORIES . " ptc	left join " . TABLE_PRODUCTS_DESCRIPTION . " pd on ptc.products_id = pd.products_id and pd.language_id = '" . (int)$_SESSION['languages_id'] . "' where ptc.categories_id='" . $current_category_id . "' order by pd.products_name");
	$products_filter = $new_product_query->fields['products_id'];
	if ($products_filter != '') {
		zen_redirect(zen_href_link(FILENAME_ADVANCED_SHIPPER_PRODUCT_SELECTOR, 'products_filter=' . $products_filter . '&current_category_id=' . $current_category_id));
	}
} else {
	if ($products_filter == '' and $current_category_id == '') {
		$reset_categories_id = zen_get_category_tree('', '', '0', '', '', true);
		$current_category_id = $reset_categories_id[0]['id'];
		$new_product_query = $db->Execute("select ptc.* from " . TABLE_PRODUCTS_TO_CATEGORIES . " ptc	left join " . TABLE_PRODUCTS_DESCRIPTION . " pd on ptc.products_id = pd.products_id and pd.language_id = '" . (int)$_SESSION['languages_id'] . "' where ptc.categories_id='" . $current_category_id . "' order by pd.products_name");
		$products_filter = $new_product_query->fields['products_id'];
		$_GET['products_filter'] = $products_filter;
	}
}

require(DIR_FS_ADMIN . DIR_WS_MODULES . FILENAME_PREV_NEXT);

if (zen_not_null($action)) {
	switch ($action) {
		case 'set_products_filter':
			$_GET['products_filter'] = $_POST['products_filter'];
			$action='';
			zen_redirect(zen_href_link(FILENAME_ADVANCED_SHIPPER_PRODUCT_SELECTOR, 'products_filter=' . $_GET['products_filter'] . '&current_category_id=' . $_POST['current_category_id']));
			break;
	}
}

?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
	<title><?php echo HEADING_TITLE; ?></title>
	<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
	<link rel="stylesheet" type="text/css" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
	<script language="javascript" src="includes/menu.js"></script>
	<script language="javascript" src="includes/general.js"></script>
	<script language="javascript"  type="text/javascript">
	<!--
<?php require(DIR_WS_INCLUDES . 'javascript/advshipper_product_selector.js'); ?>
	//-->
	</script>
	<style type="text/css">
	.AdvancedShipperPageHeading { padding-bottom: 1.5em; }
	fieldset { padding: 0.8em 0.8em; margin-bottom: 2.5em; }
	fieldset fieldset { margin-bottom: 1em; }
	legend { font-weight: bold; font-size: 1.3em; }
	
	fieldset { background: #F7F6F0; }
	
	.AdvancedShipperConfigLabel, .AdvancedShipperConfigField, .AdvancedShipperConfigDesc {
		vertical-align: top;
	}
	.AdvancedShipperConfigLabel { padding-top: 0.5em; font-weight: bold; padding-right: 1em; }
	.AdvancedShipperConfigLabel { width: 25%; }
	.AdvancedShipperConfigField { padding-top: 0.5em; padding-bottom: 1.3em; }
	.AdvancedShipperConfigIntro { padding-top: 0.5em; padding-bottom: 1.1em; }
	
	#product_selection strong { display: block; margin-bottom: 0.8em; }
	</style>
</head>
<body>

<script language="javascript"  type="text/javascript">
<!--
function ProductSelectionCancelled()
{
	window.close();
}
//-->
</script>

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
	<tr>
<!-- body_text //-->
		<td width="100%" valign="top">
			<table border="0" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td class="pageHeading AdvancedShipperPageHeading"><?php echo HEADING_TITLE; ?></td>
				</tr>
				<tr>
					<td>
						<!--<fieldset id="product_search">
							<legend><?php echo TEXT_PRODUCT_SEARCH_TITLE; ?></legend>
							<table border="0" width="100%" cellpadding="0" cellspacing="0">
								<tr>
									<td>
										<?php echo zen_draw_form('search', FILENAME_ADVANCED_SHIPPER_PRODUCT_SELECTOR, '', 'get');
										// show reset search
										if (isset($_GET['search']) && zen_not_null($_GET['search'])) {
											echo '<a href="' . zen_href_link(FILENAME_ADVANCED_SHIPPER_PRODUCT_SELECTOR) . '">' . zen_image_button('button_reset.gif', IMAGE_RESET) . '</a>&nbsp;&nbsp;';
										}
										echo HEADING_TITLE_SEARCH_DETAIL . ' ' . zen_draw_input_field('search') . zen_hide_session_id();
										if (isset($_GET['search']) && zen_not_null($_GET['search'])) {
											$keywords = zen_db_input(zen_db_prepare_input($_GET['search']));
											echo '<br/ >' . TEXT_INFO_SEARCH_DETAIL_FILTER . $keywords;
										}
										?>
										</form>
									</td>
								</tr>
							</table>
						</fieldset>-->
						<fieldset id="product_selection">
							<legend><?php echo TEXT_PRODUCT_SELECTION_TITLE; ?></legend>
							<table border="0" width="100%" cellpadding="0" cellspacing="0">
								<tr>
									<td>
										<table>
											<?php
											$curr_page = FILENAME_ADVANCED_SHIPPER_PRODUCT_SELECTOR;
											
											require(DIR_FS_ADMIN . DIR_WS_MODULES . FILENAME_PREV_NEXT_DISPLAY); ?>
										</table>
									</td>
								</tr>
								<tr>
									<td>
										<?php
										if ($_GET['products_filter'] != '') {
										?>
											<tr>
												<td>
													<form name="set_products_filter_id" <?php echo 'action="' . zen_href_link(FILENAME_ADVANCED_SHIPPER_PRODUCT_SELECTOR, 'action=set_products_filter') . '"'; ?> method="post"><?php echo zen_draw_hidden_field('products_filter', $_GET['products_filter']); ?><?php echo zen_draw_hidden_field('current_category_id', $_GET['current_category_id']); ?>
													<table border="0" cellspacing="0" cellpadding="2">
														<tr>
															<td colspan="2" class="main" style="padding-top: 1.5em"><?php echo TEXT_PRODUCT_TO_VIEW; ?></td>
														</tr>
														<tr>
															<td class="attributes-even" align="center"><?php echo zen_draw_products_pull_down('products_filter', 'size="10" onchange="this.form.submit()"', '', true, $_GET['products_filter'], true, true); ?></td>
															<td class="main" align="right" valign="top"><noscript><?php echo zen_image_submit('button_display.gif', IMAGE_DISPLAY); ?></noscript></td>
														</tr>
													</table>
													</form>
												</td>	
											</tr>
										<?php
										}
										?>
									</td>
								</tr>
							</table>
						</fieldset>
						<?php
						if ($_GET['products_filter'] != '') {
						?>
						<fieldset id="product_selection">
							<legend><?php echo TEXT_PRODUCT_OPTIONS_TITLE; ?></legend>
							<table border="0" width="100%" cellpadding="0" cellspacing="0">
								<tr>
									<td>
										<form name="select_options_form" <?php echo 'action="' . zen_href_link(FILENAME_ADVANCED_SHIPPER_PRODUCT_SELECTOR, 'products_filter=' . $_GET['products_filter'] . '&current_category_id=' . $_GET['current_category_id']); ?>" method="post"><?php echo zen_draw_hidden_field('products_filter', $_GET['products_filter']); ?><?php echo zen_draw_hidden_field('current_category_id', $_GET['current_category_id']);
										// A product has been selected, check if it has any 
										// attributes that should be selected
										$show_onetime_charges_description = 'false';
										$show_attributes_qty_prices_description = 'false';
										
										// limit to 1 for performance when processing larger tables
										$sql = "select count(*) as total
											from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_ATTRIBUTES . " patrib
											where    patrib.products_id='" . (int) $_GET['products_filter'] . "'
											and      patrib.options_id = popt.products_options_id
											and      popt.language_id = '" . (int) $_SESSION['languages_id'] . "'" .
											" limit 1";
										
										$pr_attr = $db->Execute($sql);
										
										if ($pr_attr->fields['total'] > 0) {
											// Variable holds the javascript necessary to determine
											// which option(s) have been selected
											$options_selected_js = '';
											
											// Variable holds the names for the various options, to
											// be passed back for display to the customer
											$options_display_names = array();
											
											if (PRODUCTS_OPTIONS_SORT_ORDER=='0') {
												$options_order_by= ' order by LPAD(popt.products_options_sort_order,11,"0")';
											} else {
												$options_order_by= ' order by popt.products_options_name';
											}
											
											$sql = "select distinct popt.products_options_id, popt.products_options_name, popt.products_options_sort_order,
												popt.products_options_type, popt.products_options_length, popt.products_options_comment,
												popt.products_options_size,
												popt.products_options_images_per_row,
												popt.products_options_images_style,
												popt.products_options_rows
												from        " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_ATTRIBUTES . " patrib
												where           patrib.products_id='" . (int) $_GET['products_filter'] . "'
												and             patrib.options_id = popt.products_options_id
												and             popt.language_id = '" . (int) $_SESSION['languages_id'] . "' " .
											$options_order_by;
											
											$products_options_names = $db->Execute($sql);
											
											if (PRODUCTS_OPTIONS_SORT_BY_PRICE == '1') {
												$order_by= ' order by LPAD(pa.products_options_sort_order,11,"0"), pov.products_options_values_name';
											} else {
												$order_by= ' order by LPAD(pa.products_options_sort_order,11,"0"), pa.options_values_price';
											}
											
											$discount_type = zen_get_products_sale_discount_type((int) $_GET['products_filter']);
											$discount_amount = zen_get_discount_calc((int) $_GET['products_filter']);
											
											$zv_display_select_option = 0;
											
											while (!$products_options_names->EOF) {
												$products_options_array = array();
												
												$sql = "select    pov.products_options_values_id,
													pov.products_options_values_name,
													pa.*
													from      " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov
													where     pa.products_id = '" . (int)$_GET['products_filter'] . "'
													and       pa.options_id = '" . (int)$products_options_names->fields['products_options_id'] . "'
													and       pa.options_values_id = pov.products_options_values_id
													and       pov.language_id = '" . (int)$_SESSION['languages_id'] . "' " .
													$order_by;
												
												$products_options = $db->Execute($sql);
												
												$products_options_value_id = '';
												$products_options_details = '';
												$products_options_details_noname = '';
												$tmp_radio = '';
												$tmp_checkbox = '';
												$selected_attribute = false;
												
												$show_attributes_qty_prices_icon = 'false';
												
												while (!$products_options->EOF) {
													$products_options_display_price ='';
													$new_attributes_price = '';
													$price_onetime = '';
													
													$products_options_array[] = array(
														'id' => $products_options->fields['products_options_values_id'],
														'text' => $products_options->fields['products_options_values_name']);
													
													// collect price information if it exists
													if ($products_options->fields['attributes_discounted'] == 1) {
														// apply product discount to attributes if discount is on
														//              $new_attributes_price = $products_options->fields['options_values_price'];
														$new_attributes_price = zen_get_attributes_price_final($products_options->fields["products_attributes_id"], 1, '', 'false');
														$new_attributes_price = zen_get_discount_calc((int)$_GET['products_filter'], true, $new_attributes_price);
													} else {
														// discount is off do not apply
														$new_attributes_price = $products_options->fields['options_values_price'];
													}
													
													// reverse negative values for display
													if ($new_attributes_price < 0) {
														$new_attributes_price = -$new_attributes_price;
													}
													
													if ($products_options->fields['attributes_price_onetime'] != 0 or $products_options->fields['attributes_price_factor_onetime'] != 0) {
														$show_onetime_charges_description = 'true';
														$new_onetime_charges = zen_get_attributes_price_final_onetime($products_options->fields["products_attributes_id"], 1, '');
														$price_onetime = TEXT_ONETIME_CHARGE_SYMBOL . $currencies->display_price($new_onetime_charges, zen_get_tax_rate($product_info->fields['products_tax_class_id']));
													} else {
														$price_onetime = '';
													}
													
													if ($products_options->fields['attributes_qty_prices'] != '' or $products_options->fields['attributes_qty_prices_onetime'] != '') {
														$show_attributes_qty_prices_description = 'true';
														$show_attributes_qty_prices_icon = 'true';
													}
													
													if ($products_options->fields['options_values_price'] != '0' and ($products_options->fields['product_attribute_is_free'] != '1' and $product_info->fields['product_is_free'] != '1')) {
													// show sale maker discount if a percentage
														$products_options_display_price= ' (' . $products_options->fields['price_prefix'] .
														$currencies->display_price($new_attributes_price, zen_get_tax_rate($product_info->fields['products_tax_class_id'])) . ') ';
													} else {
														// if product_is_free and product_attribute_is_free
														if ($products_options->fields['product_attribute_is_free'] == '1' and $product_info->fields['product_is_free'] == '1') {
															$products_options_display_price= TEXT_ATTRIBUTES_PRICE_WAS . $products_options->fields['price_prefix'] .
															$currencies->display_price($new_attributes_price, zen_get_tax_rate($product_info->fields['products_tax_class_id'])) . TEXT_ATTRIBUTE_IS_FREE;
														} else {
															// normal price
															if ($new_attributes_price == 0) {
																$products_options_display_price= '';
															} else {
																$products_options_display_price= ' (' . $products_options->fields['price_prefix'] .
																$currencies->display_price($new_attributes_price, zen_get_tax_rate($product_info->fields['products_tax_class_id'])) . ') ';
															}
														}
													}
													
													$products_options_display_price .= $price_onetime;
													
													$products_options_array[sizeof($products_options_array)-1]['text'] .= $products_options_display_price;
													
													// collect weight information if it exists
													if (($flag_show_weight_attrib_for_this_prod_type=='1' and $products_options->fields['products_attributes_weight'] != '0')) {
														$products_options_display_weight = ' (' . $products_options->fields['products_attributes_weight_prefix'] . round($products_options->fields['products_attributes_weight'],2) . TEXT_PRODUCT_WEIGHT_UNIT . ')';
														$products_options_array[sizeof($products_options_array)-1]['text'] .= $products_options_display_weight;
													} else {
														// reset
														$products_options_display_weight='';
													}
													
													// prepare product options details
													$prod_id = $_GET['products_filter'];
													if ($products_options_names->fields['products_options_type'] == PRODUCTS_OPTIONS_TYPE_FILE or $products_options_names->fields['products_options_type'] == PRODUCTS_OPTIONS_TYPE_TEXT or $products_options_names->fields['products_options_type'] == PRODUCTS_OPTIONS_TYPE_CHECKBOX or $products_options_names->fields['products_options_type'] == PRODUCTS_OPTIONS_TYPE_RADIO or $products_options->RecordCount() == 1 or $products_options_names->fields['products_options_type'] == PRODUCTS_OPTIONS_TYPE_READONLY) {
														$products_options_value_id = $products_options->fields['products_options_values_id'];
														if ($products_options_names->fields['products_options_type'] != PRODUCTS_OPTIONS_TYPE_TEXT and $products_options_names->fields['products_options_type'] != PRODUCTS_OPTIONS_TYPE_FILE) {
															$products_options_details = $products_options->fields['products_options_values_name'];
														} else {
															// don't show option value name on TEXT or filename
															$products_options_details = '';
														}
														if ($products_options_names->fields['products_options_images_style'] >= 3) {
															$products_options_details .= $products_options_display_price . ($products_options->fields['products_attributes_weight'] != 0 ? '<br />' . $products_options_display_weight : '');
															$products_options_details_noname = $products_options_display_price . ($products_options->fields['products_attributes_weight'] != 0 ? '<br />' . $products_options_display_weight : '');
														} else {
															$products_options_details .= $products_options_display_price . ($products_options->fields['products_attributes_weight'] != 0 ? '  ' . $products_options_display_weight : '');
															$products_options_details_noname = $products_options_display_price . ($products_options->fields['products_attributes_weight'] != 0 ? '  ' . $products_options_display_weight : '');
														}
													}
													
													// radio buttons
													if ($products_options_names->fields['products_options_type'] == PRODUCTS_OPTIONS_TYPE_RADIO) {
														// if an error, set to customer setting
														if ($_POST['id'] !='') {
															$selected_attribute= false;
															reset($_POST['id']);
															foreach ($_POST['id'] as $key => $value) {
																if (($key == $products_options_names->fields['products_options_id'] and $value == $products_options->fields['products_options_values_id'])) {
																	$selected_attribute = true;
																	break;
																}
															}
														} else {
															// select default but do NOT auto select single radio buttons
															//                        $selected_attribute = ($products_options->fields['attributes_default']=='1' ? true : false);
															// select default radio button or auto select single radio buttons
															$selected_attribute = ($products_options->fields['attributes_default']=='1' ? true : ($products_options->RecordCount() == 1 ? true : false));
														}
														
														$tmp_radio .= advshipper_draw_radio_field('id[' . $products_options_names->fields['products_options_id'] . ']', $products_options_value_id, $selected_attribute, '', 'id="' . 'attrib-' . $products_options_names->fields['products_options_id'] . '-' . $products_options_value_id . '"') . '<label class="attribsRadioButton zero" for="' . 'attrib-' . $products_options_names->fields['products_options_id'] . '-' . $products_options_value_id . '">' . $products_options_details . '</label><br />' . "\n";
														
														$options_selected_js .= "
	current_option = false;
	try {
		current_option = document.getElementById('attrib-" . $products_options_names->fields['products_options_id'] . '-' . $products_options_value_id . "').checked;
	} catch (e) {
		current_option = document.select_options_form.eval('attrib-" . $products_options_names->fields['products_options_id'] . '-' . $products_options_value_id . "').checked;
	}
	if (current_option) {
		selected_options_string += '" . ADVSHIPPER_PRODUCT_OPTIONS_SEPARATOR . $products_options_names->fields['products_options_id'] . '-' . $products_options_value_id . "'
	}";
													}
													
													// checkboxes
													if ($products_options_names->fields['products_options_type'] == PRODUCTS_OPTIONS_TYPE_CHECKBOX) {
														$string = $products_options_names->fields['products_options_id'].'_chk'.$products_options->fields['products_options_values_id'];
														// if an error, set to customer setting
														if ($_POST['id'] !='') {
															$selected_attribute= false;
															reset($_POST['id']);
															foreach ($_POST['id'] as $key => $value) {
																if (is_array($value)) {
																	foreach ($value as $kkey => $vvalue) {
																		if (($key == $products_options_names->fields['products_options_id'] and $vvalue == $products_options->fields['products_options_values_id'])) {
																			$selected_attribute = true;
																			break;
																		}
																	}
																} else {
																	if (($key == $products_options_names->fields['products_options_id'] and $value == $products_options->fields['products_options_values_id'])) {
																		$selected_attribute = true;
																		break;
																	}
																}
															}
														} else {
															$selected_attribute = ($products_options->fields['attributes_default']=='1' ? true : false);
														}
														
														$tmp_checkbox .= zen_draw_checkbox_field('id[' . $products_options_names->fields['products_options_id'] . ']['.$products_options_value_id.']', $products_options_value_id, $selected_attribute, '', 'id="' . 'attrib-' . $products_options_names->fields['products_options_id'] . '-' . $products_options_value_id . '"') . '<label class="attribsCheckbox" for="' . 'attrib-' . $products_options_names->fields['products_options_id'] . '-' . $products_options_value_id . '">' . $products_options_details . '</label><br />' . "\n";
														
														$options_selected_js .= "
	current_option = false;
	try {
		current_option = document.getElementById('attrib-" . $products_options_names->fields['products_options_id'] . '-' . $products_options_value_id . "').checked;
	} catch (e) {
		current_option = document.select_options_form.eval('attrib-" . $products_options_names->fields['products_options_id'] . '-' . $products_options_value_id . "').checked;
	}
	if (current_option) {
		selected_options_string += '" . ADVSHIPPER_PRODUCT_OPTIONS_SEPARATOR . $products_options_names->fields['products_options_id'] . '-' . $products_options_value_id . "'
	}";
													}
													
													// default
													// find default attribute if set to for default dropdown
													if ($products_options->fields['attributes_default']=='1') {
														$selected_attribute = $products_options->fields['products_options_values_id'];
													}
													
													$products_options->MoveNext();
												}
												
												// Option Name Type Display
												switch (true) {
													// checkbox
													case ($products_options_names->fields['products_options_type'] == PRODUCTS_OPTIONS_TYPE_CHECKBOX):
														if ($show_attributes_qty_prices_icon == 'true') {
															$options_name[] = ATTRIBUTES_QTY_PRICE_SYMBOL . $products_options_names->fields['products_options_name'];
														} else {
															$options_name[] = $products_options_names->fields['products_options_name'];
														}
														$options_menu[] = $tmp_checkbox . "\n";
														$options_comment[] = $products_options_names->fields['products_options_comment'];
														$options_comment_position[] = ($products_options_names->fields['products_options_comment_position'] == '1' ? '1' : '0');
														
														break;
													// radio buttons
													case ($products_options_names->fields['products_options_type'] == PRODUCTS_OPTIONS_TYPE_RADIO):
														if ($show_attributes_qty_prices_icon == 'true') {
															$options_name[] = ATTRIBUTES_QTY_PRICE_SYMBOL . $products_options_names->fields['products_options_name'];
														} else {
															$options_name[] = $products_options_names->fields['products_options_name'];
														}
														$options_menu[] = $tmp_radio . "\n";
														$options_comment[] = $products_options_names->fields['products_options_comment'];
														$options_comment_position[] = ($products_options_names->fields['products_options_comment_position'] == '1' ? '1' : '0');
														
														$options_selected_js .= '';
														
														break;
													// dropdown menu auto switch to selected radio button display
													case ($products_options->RecordCount() == 1):
														if ($show_attributes_qty_prices_icon == 'true') {
															$options_name[] = '<label class="switchedLabel ONE" for="' . 'attrib-' . $products_options_names->fields['products_options_id'] . '-' . $products_options_value_id . '">' . ATTRIBUTES_QTY_PRICE_SYMBOL . $products_options_names->fields['products_options_name'] . '</label>';
														} else {
															$options_name[] = $products_options_names->fields['products_options_name'];
														}
														$options_menu[] = advshipper_draw_radio_field('id[' . $products_options_names->fields['products_options_id'] . ']', $products_options_value_id, 'selected', '', 'id="' . 'attrib-' . $products_options_names->fields['products_options_id'] . '-' . $products_options_value_id . '"') . '<label class="attribsRadioButton" for="' . 'attrib-' . $products_options_names->fields['products_options_id'] . '-' . $products_options_value_id . '">' . $products_options_details . '</label>' . "\n";
														$options_comment[] = $products_options_names->fields['products_options_comment'];
														$options_comment_position[] = ($products_options_names->fields['products_options_comment_position'] == '1' ? '1' : '0');
														
														$options_selected_js .= "
	current_option = false;
	try {
		current_option = document.getElementById('attrib-" . $products_options_names->fields['products_options_id'] . '-' . $products_options_value_id . "').checked;
	} catch (e) {
		current_option = document.select_options_form.eval('attrib-" . $products_options_names->fields['products_options_id'] . '-' . $products_options_value_id . "').checked;
	}
	if (current_option) {
		selected_options_string += '" . ADVSHIPPER_PRODUCT_OPTIONS_SEPARATOR . $products_options_names->fields['products_options_id'] . '-' . $products_options_value_id . "'
	}";
														break;
													default:
														// normal dropdown menu display
														// use customer-selected values
														if ($_POST['id'] !='') {
															reset($_POST['id']);
															foreach ($_POST['id'] as $key => $value) {
																if ($key == $products_options_names->fields['products_options_id']) {
																	$selected_attribute = $value;
																	break;
																}
															}
														} else {
															// use default selected set above
														}
														
														if ($show_attributes_qty_prices_icon == 'true') {
															$options_name[] = ATTRIBUTES_QTY_PRICE_SYMBOL . $products_options_names->fields['products_options_name'];
														} else {
															$options_name[] = '<label class="attribsSelect" for="' . 'attrib-' . $products_options_names->fields['products_options_id'] . '">' . $products_options_names->fields['products_options_name'] . '</label>';
														}
														
														
														$options_menu[] = zen_draw_pull_down_menu('id[' . $products_options_names->fields['products_options_id'] . ']', $products_options_array, $selected_attribute, 'id="' . 'attrib-' . $products_options_names->fields['products_options_id'] . '"') . "\n";
														$options_comment[] = $products_options_names->fields['products_options_comment'];
														$options_comment_position[] = ($products_options_names->fields['products_options_comment_position'] == '1' ? '1' : '0');
														
														$options_selected_js .= "
	option_select_el_id_" . $products_options_names->fields['products_options_id'] . " = document.getElementById('attrib-" . $products_options_names->fields['products_options_id'] . "');
	selected_option = option_select_el_id_" . $products_options_names->fields['products_options_id'] . ".options[option_select_el_id_" . $products_options_names->fields['products_options_id'] . ".selectedIndex].value;
	selected_options_string += '" . ADVSHIPPER_PRODUCT_OPTIONS_SEPARATOR . $products_options_names->fields['products_options_id'] . "-' + selected_option;";
														
														break;
												}
												
												foreach ($products_options_array as $product_option) {
													$options_display_names[(int)$products_options_names->fields['products_options_id'] . '-' . $product_option['id']] = '// ' . $products_options_names->fields['products_options_name'] . ' -- ' . $product_option['text'];
												}
												
												$products_options_names->MoveNext();
											}
											
											// Build a JavaScript associative array containing the
											// names for every possible option and value
											$options_names_js = '';
											foreach($options_display_names as $option_id => $option_name) {
												$options_names_js .= 'option_names[' . $option_id . '] = "' . addslashes($option_name) . '"' . ";\n";
											}
											
											echo "\n" . '<script language="JavaScript" type="text/javascript">' . "\n<!--\n";
											echo "var option_names = new Array();\n\n";
											echo $options_names_js;
											echo "\nfunction advshipperSelectedOptions()
{
	var selected_options_string = '';
	
	" . $options_selected_js . "
	
	return selected_options_string;
}
// -->\n</script>\n";
											
											$select_options_all_options_selected = ((isset($_POST['select_options']) && $_POST['select_options'] == 'all_options') ? 'selected' : '');
											$select_options_select_options_selected = ((isset($_POST['select_options']) && $_POST['select_options'] == 'select_options') ? 'selected' : '');
											
											if ($select_options_all_options_selected == '' &&
													$select_options_select_options_selected == '') {
												$select_options_all_options_selected = 'selected';
											}
											
											echo '<p>' . advshipper_draw_radio_field('select_options', 'all_options', $select_options_all_options_selected, '', 'id="select_options_all_options" onclick="javascript:advshipperSelectOptions()"') . '<label class="attribsRadioButton" for="select_options_all_options">' . TEXT_ALL_OPTIONS . '</label>' . "\n";
											echo '<br />' . advshipper_draw_radio_field('select_options', 'select_options', $select_options_select_options_selected, '', 'id="select_options_select_options" onclick="javascript:advshipperSelectOptions()"') . '<label class="attribsRadioButton" for="select_options_select_options">' . TEXT_SELECT_OPTIONS . '</label>' . "</p>\n";
										?>
											<fieldset id="select_options_panel" <?php echo ($select_options_all_options_selected == 'selected' ? 'style="display: none"' : ''); ?>>
												<legend><?php echo TEXT_SELECT_OPTIONS; ?></legend>
										<?php
											$num_options = sizeof($options_menu);
											
											for ($i = 0; $i < $num_options; $i++) {
												if ($options_comment[$i] != '' and $options_comment_position[$i] == '0') {
												?>
												<h3 class="attributesComments"><?php echo $options_comment[$i]; ?></h3>
												<?php
												}
												?>
												
												<div class="wrapperAttribsOptions">
												<h4 class="optionName back"><?php echo $options_name[$i]; ?></h4>
												<div class="back"><?php echo "\n" . $options_menu[$i]; ?></div>
												<br class="clearBoth" />
												</div>
												
												
												<?php if ($options_comment[$i] != '' and $options_comment_position[$i] == '1') { ?>
												<div class="ProductInfoComments"><?php echo $options_comment[$i]; ?></div>
												<?php }
											}
										?>
											</fieldset>
										<?php
										} else {
											// Product doesn't have any attributes
											echo '<p>' . TEXT_NO_ATTRIBUTES . '</p>';
										}
										echo zen_draw_input_field('product_select_submit', IMAGE_SELECT, 'id="product_select_submit" onclick="javascript:advshipperProductSelected(' . (int) $_GET['products_filter'] . '); return false;"', false, 'submit');
										echo ' ' . zen_draw_input_field('product_cancel_submit', IMAGE_CANCEL, 'id="product_cancel_submit" onclick="javascript:ProductSelectionCancelled(); return false;"', false, 'submit');
										?>
										</form>
									</td>
								</tr>
							</table>
						</fieldset>
						<?php
						}
						?>
					</td>
				</tr>
			</table>
		</td>
<!-- body_text_eof //-->
	</tr>
</table>
<!-- body_eof //-->

</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>