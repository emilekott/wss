<?php
/**
 * Displays a list of the manufacturers for the site so one can be selected to be added to the
 * list of manufacturers to which a shipping method applies.
 *
 * @package    admin
 * @author     Conor Kerr <zen-cart.advshipper@dev.ceon.net>
 * @copyright  Copyright 2007-2009 Ceon
 * @copyright  Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright  Portions Copyright 2003 osCommerce
 * @link       http://dev.ceon.net/web/zen-cart/advshipper
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: advshipper_manufacturer_selector.php 382 2009-06-22 18:49:29Z Bob $
 */

require('includes/application_top.php');

$languages = zen_get_languages();

require_once(DIR_FS_ADMIN . DIR_WS_FUNCTIONS . 'advshipper.php');

$manufacturers = advshipper_get_manufacturers();

$num_manufacturers = sizeof($manufacturers);

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
						echo zen_draw_form('manufacturer_form', FILENAME_ADVANCED_SHIPPER_MANUFACTURER_SELECTOR, '', 'post');
						?>
						<script language="javascript"  type="text/javascript">
						<!--
function ManufacturersSelected()
{
	manufacturer_ids_el = document.getElementById('manufacturer_ids');
	if (manufacturer_ids_el == undefined) {
		return;
	}
	var manufacturer_ids_selected = new Array();
	
	for (var i = 0; i < manufacturer_ids_el.options.length; i++) {
		if (manufacturer_ids_el.options[i].selected) {
			manufacturer_ids_selected.push(manufacturer_ids_el.options[i].value);
		}
	}
	
	var manufacturer_ids_selected_string = manufacturer_ids_selected.join('_');
	
	window.opener.advshipperAddManufacturers(manufacturer_ids_selected_string);
	
	window.close();
}
function ManufacturersSelectionCancelled()
{
	window.close();
}
						//-->
						</script>
						<fieldset id="manufacturer_selection">
							<legend><?php echo TEXT_MANUFACTURERS_SELECTION_TITLE; ?></legend>
							<table border="0" width="100%" cellpadding="0" cellspacing="0">
								<tr>
									<td rowspan="2" class="AdvancedShipperConfigLabel"><label for="country"><?php echo TEXT_LABEL_SELECT_MANUFACTURERS; ?>:</label></td>
								</tr>
								<tr>
									<td class="AdvancedShipperConfigField">
										<?php if ($num_manufacturers > 0) {
											$select_size = 15;
											
											if ($num_manufacturers < $select_size) {
												$select_size = $num_manufacturers;
											}
											
											echo zen_draw_pull_down_menu('manufacturer_ids',
												$manufacturers, '',
												'id="manufacturer_ids" multiple="multiple" size="' .
												$select_size . '"');
											
											
											echo '<p>' . TEXT_SELECT_MULTIPLE_MANUFACTURERS . '</p>';
										} else {
											echo TEXT_NO_MANUFACTURERS;
										}?>
									</td>
								</tr>
							</table>
						</fieldset>
						<?php if (sizeof($manufacturers) > 0) {
							echo zen_draw_input_field('manufacturers_select_submit', IMAGE_SELECT, 'id="manufacturers_select_submit" onclick="javascript:ManufacturersSelected(); return false;"', false, 'submit');
						}
						echo ' ' . zen_draw_input_field('manufacturers_cancel_submit', IMAGE_CANCEL, 'id="manufacturers_cancel_submit" onclick="javascript:ManufacturersSelectionCancelled(); return false;"', false, 'submit');
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