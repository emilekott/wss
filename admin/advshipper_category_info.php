<?php
/**
 * Advanced Shipper Category Info Script - Looks up the name for a category (uses full category
 * path).
 * 
 * @package    admin
 * @author     Conor Kerr <zen-cart.advshipper@dev.ceon.net>
 * @copyright  Copyright 2007-2009 Ceon
 * @copyright  Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright  Portions Copyright 2003 osCommerce
 * @link       http://dev.ceon.net/web/zen-cart/advshipper
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: advshipper_category_info.php 382 2009-06-22 18:49:29Z Bob $
 */

require('includes/application_top.php');

$languages = zen_get_languages();

require_once(DIR_FS_ADMIN . DIR_WS_FUNCTIONS . 'advshipper.php');

// Check and parse the input variable
$category_ids_string = isset($_GET['category_ids_string']) ? $_GET['category_ids_string'] : null;

// Initialise the response variable
$answer = '';

$category_ids = explode('_', $category_ids_string);

if (is_null($category_ids_string) || sizeof($category_ids) == 0) {
	// Necessary details missing!
	$answer = '-1';
} else {
	for ($i = 0, $n = sizeof($category_ids); $i < $n; $i++) {
		// Get the details for the current category
		$category_id = (int) $category_ids[$i];
		$category_name = advshipper_get_generated_category_path($category_id);
		
		if ($category_name != '') {
			$category_name = str_replace('(())', '', $category_name);
			
			$category_name = str_replace('|', '/', $category_name);
			
			// Build return string
			// Format: ID of category
			//         Name of category
			$answer .= $category_id . '|' . $category_name . '||';
		} else {
			// Problem occurred looking up category's details!
			$answer = '-1';
			
			break;
		}
	}
	if ($answer != '-1') {
		$answer = substr($answer, 0, strlen($answer) - 2);
	}
}

?>
_cba.ready (
	<?php  echo $_GET['_cba_request_id'];?>,
	"<?php echo addslashes($answer);?>"
);
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>