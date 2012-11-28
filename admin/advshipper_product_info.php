<?php
/**
 * Advanced Shipper Add Product Script - Adds a product to a shipping method's list of products.
 * 
 * @package    admin
 * @author     Conor Kerr <zen-cart.advshipper@dev.ceon.net>
 * @copyright  Copyright 2007-2009 Ceon
 * @copyright  Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright  Portions Copyright 2003 osCommerce
 * @link       http://dev.ceon.net/web/zen-cart/advshipper
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: advshipper_product_info.php 382 2009-06-22 18:49:29Z Bob $
 */

require('includes/application_top.php');

$languages = zen_get_languages();

require_once(DIR_FS_ADMIN . DIR_WS_FUNCTIONS . 'advshipper.php');

// Check and parse the input variables
$product_id_string = (isset($_GET['product_id_string']) &&
	strlen($_GET['product_id_string']) > 0) ? $_GET['product_id_string'] : null;

// Initialise the response variable
$answer = '-1';


if (is_null($product_id_string)) {
	// Necessary details missing!
	$answer = '-1';
} else {
	// Get the product's ID
	$product_attribute_options = explode(ADVSHIPPER_PRODUCT_OPTIONS_SEPARATOR, $product_id_string);
	$product_id = $product_attribute_options[0];
	
	$new_product_id_string = $product_id;
	
	$product_has_attributes = zen_has_product_attributes($product_id);
	
	// Get the details for the specified product
	$product_name = str_replace('(())', '', zen_get_products_name($product_id,
		$_SESSION['languages_id']));
	
	// If this product has attributes, get the details for its attributes
	if ($product_has_attributes) {
		if (strpos($product_id_string, ADVSHIPPER_PRODUCT_OPTIONS_SEPARATOR) === false) {
			// No attributes selected, apply for all options for this product
			$product_name .= ADVSHIPPER_ADD_PRODUCT_TEXT_ALL_OPTIONS_SELECTED;
		} else {
			// Attribute options selected, get their details
			$num_attribute_options = sizeof($product_attribute_options) - 1;
			
			for ($i = 1; $i <= $num_attribute_options; $i++) {
				// Options name/value pair are stored separated by a dash
				$option_name_value_pair = explode('-', $product_attribute_options[$i]);
				
				// Get the product attributes which matches this combination of option name and
				// value so its ID can be added to the main product ID
				$products_attributes_id_sql = "
					SELECT
						pa.products_attributes_id
					FROM
						" . TABLE_PRODUCTS_ATTRIBUTES . " pa
					WHERE
						pa.products_id = '" . (int) $product_id . "'
					AND
						pa.options_id = '" . (int) $option_name_value_pair[0] . "'
					AND
						pa.options_values_id = '" . (int) $option_name_value_pair[1] . "';";
				
				$products_attributes_id_result = $db->Execute($products_attributes_id_sql);
				
				$products_attributes_id =
					$products_attributes_id_result->fields['products_attributes_id'];
				
				$new_product_id_string .= '-' . $products_attributes_id;
				
				// Get the description for this option combination
				$option_name_sql = "
					SELECT
						po.products_options_name
					FROM
						" . TABLE_PRODUCTS_OPTIONS . " po
					WHERE
						po.products_options_id = '" . (int) $option_name_value_pair[0] . "'
					AND
						po.language_id = '" . (int) $_SESSION['languages_id'] . "';";
				
				$option_value_name_sql = "
					SELECT
						pov.products_options_values_name
					FROM
						" . TABLE_PRODUCTS_OPTIONS_VALUES . " pov
					WHERE
						pov.products_options_values_id = '" . (int) $option_name_value_pair[1] . "'
					AND
						pov.language_id = '" . (int) $_SESSION['languages_id'] . "';";
				
				$option_name_result = $db->Execute($option_name_sql);
				$option_value_result = $db->Execute($option_value_name_sql);
				
				$product_name .= ' // ' . $option_name_result->fields['products_options_name'] .
					' -- ' . $option_value_result->fields['products_options_values_name'];
			}
		}
	}
	
	$product_name = str_replace('|', '/', $product_name);
	
	while (strpos($product_name, ADVSHIPPER_PRODUCT_OPTIONS_SEPARATOR) !== false) {
		$product_name = str_replace(ADVSHIPPER_PRODUCT_OPTIONS_SEPARATOR, '---', $product_name);
	}
	
	// Build return string
	// Format: ID of product
	//         Name of product
	$answer = $new_product_id_string . '|' . $product_name;
}

?>
_cba.ready (
	<?php  echo $_GET['_cba_request_id'];?>,
	"<?php echo addslashes($answer);?>"
);
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>