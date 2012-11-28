<?php
/**
 * @package    admin
 * @author     Conor Kerr <zen-cart.advshipper@dev.ceon.net>
 * @copyright  Copyright 2007-2009 Ceon
 * @copyright  Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright  Portions Copyright 2003 osCommerce
 * @link       http://dev.ceon.net/web/zen-cart/advshipper
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: advshipper_category_selector.php 382 2009-06-22 18:49:29Z Bob $
 */

require('includes/application_top.php');

$languages = zen_get_languages();

function advshipper_get_category_tree($parent_id = '0', $spacing = '', $exclude = '', $category_tree_array = '', $include_itself = false, $category_has_products = false, $limit = false)
{
	global $db;
	
	if ($limit) {
		$limit_count = " limit 1";
	} else {
		$limit_count = '';
	}
	
	if (!is_array($category_tree_array)) {
		$category_tree_array = array();
	}
	if ((sizeof($category_tree_array) < 1) && ($exclude != '0')) {
		$category_tree_array[] = array('id' => '0', 'text' => TEXT_TOP);
	}
	
	if ($include_itself) {
		$category = $db->Execute("select cd.categories_name
			from " . TABLE_CATEGORIES_DESCRIPTION . " cd
			where cd.language_id = '" . (int) $_SESSION['languages_id'] . "'
			and cd.categories_id = '" . (int) $parent_id . "'");
		
		$category_tree_array[] = array(
			'id' => $parent_id,
			'text' => $category->fields['categories_name']
			);
	}
	
	$categories = $db->Execute("select c.categories_id, cd.categories_name, c.parent_id
		from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd
		where c.categories_id = cd.categories_id
		and cd.language_id = '" . (int) $_SESSION['languages_id'] . "'
		and c.parent_id = '" . (int) $parent_id . "'
		order by c.sort_order, cd.categories_name");
	
	while (!$categories->EOF) {
		/*if ($category_has_products == true && zen_products_in_category_count($categories->fields['categories_id'], '', false, true) >= 1) {
			$mark = '*';
		} else {
			$mark = '&nbsp;&nbsp;';
		}*/
		$mark = '';
		if ($exclude != $categories->fields['categories_id']) {
			$category_tree_array[] = array(
				'id' => $categories->fields['categories_id'],
				'text' => $spacing . $categories->fields['categories_name'] . $mark
				);
		}
		$category_tree_array = advshipper_get_category_tree($categories->fields['categories_id'], $spacing . '&nbsp;&nbsp;&nbsp;', $exclude, $category_tree_array, '', $category_has_products);
		$categories->MoveNext();
	}
	
	return $category_tree_array;
}

$categories = advshipper_get_category_tree('', '', '0', '', '', true);

$num_categories = sizeof($categories);

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
	.AdvancedShipperConfigIntro { padding-top: 0.5em; padding-bottom:1.1em;  }
	</style>
</head>
<body>


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
						<?php
						echo zen_draw_form('category_form', FILENAME_ADVANCED_SHIPPER_CATEGORY_SELECTOR, '', 'post');
						?>
						<script language="javascript"  type="text/javascript">
						<!--
function CategoriesSelected()
{
	var category_ids_el = document.getElementById('category_ids');
	if (category_ids_el == undefined) {
		return;
	}
	var category_ids_selected = new Array();
	
	for (var i = 0; i < category_ids_el.options.length; i++) {
		if (category_ids_el.options[i].selected) {
			category_ids_selected.push(category_ids_el.options[i].value);
		}
	}
	
	var category_ids_selected_string = category_ids_selected.join('_');
	
	window.opener.advshipperAddCategories(category_ids_selected_string);
	
	window.close();
}
function CategoriesSelectionCancelled()
{
	window.close();
}
						//-->
						</script>
						<fieldset id="category_selection">
							<legend><?php echo TEXT_CATEGORIES_SELECTION_TITLE; ?></legend>
							<table border="0" width="100%" cellpadding="0" cellspacing="0">
								<tr>
									<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="country"><?php echo TEXT_LABEL_SELECT_CATEGORIES; ?>:</label></td>
								</tr>
								<tr>
									<td class="AdvancedShipperConfigField">
										<?php if ($num_categories > 0) {
											$select_size = 15;
											
											if ($num_categories < $select_size) {
												$select_size = $num_categories;
											}
											
											echo zen_draw_pull_down_menu('category_ids',
												$categories, '',
												'id="category_ids" multiple="multiple" size="' .
												$select_size . '"');
											
											echo '<p>' . TEXT_SELECT_MULTIPLE_CATEGORIES . '</p>';
										} else {
											echo TEXT_NO_CATEGORIES;
										}?>
									</td>
								</tr>
							</table>
						</fieldset>
						<?php if (sizeof($categories) > 0) {
							echo zen_draw_input_field('categories_select_submit', IMAGE_SELECT, 'id="categories_select_submit" onclick="javascript:CategoriesSelected(); return false;"', false, 'submit');
						}
						echo ' ' . zen_draw_input_field('categories_cancel_submit', IMAGE_CANCEL, 'id="categories_cancel_submit" onclick="javascript:CategoriesSelectionCancelled(); return false;"', false, 'submit');
						?>
						</form>
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