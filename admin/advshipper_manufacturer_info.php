<?php
/**
 * Advanced Shipper manufacturer Info Script - Looks up the name for a manufacturer.
 * 
 * @package    admin
 * @author     Conor Kerr <zen-cart.advshipper@dev.ceon.net>
 * @copyright  Copyright 2007-2009 Ceon
 * @copyright  Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright  Portions Copyright 2003 osCommerce
 * @link       http://dev.ceon.net/web/zen-cart/advshipper
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: advshipper_manufacturer_info.php 382 2009-06-22 18:49:29Z Bob $
 */

require('includes/application_top.php');

$languages = zen_get_languages();

require_once(DIR_FS_ADMIN . DIR_WS_FUNCTIONS . 'advshipper.php');

// Check and parse the input variable
$manufacturer_ids_string = isset($_GET['manufacturer_ids_string']) ?
	$_GET['manufacturer_ids_string'] : null;

// Initialise the response variable
$answer = '';

$manufacturer_ids = explode('_', $manufacturer_ids_string);

if (is_null($manufacturer_ids_string) || sizeof($manufacturer_ids) == 0) {
	// Necessary details missing!
	$answer = '-1';
} else {
	for ($i = 0, $n = sizeof($manufacturer_ids); $i < $n; $i++) {
		// Get the details for the current manufacturer
		$manufacturer_id = (int) $manufacturer_ids[$i];
		$manufacturer_name = advshipper_get_manufacturer_name($manufacturer_id);
		
		if ($manufacturer_name != '') {
			$manufacturer_name = str_replace('(())', '', $manufacturer_name);
			
			$manufacturer_name = str_replace('|', '/', $manufacturer_name);
			
			// Build return string
			// Format: ID of manufacturer
			//         Name of manufacturer
			$answer .= $manufacturer_id . '|' . $manufacturer_name . '||';
		} else {
			// Problem occurred looking up manufacturer's details!
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