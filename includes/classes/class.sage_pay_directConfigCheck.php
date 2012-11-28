<?php

/**
 * sage_pay_direct Config Check Warning
 *
 * @author     Conor Kerr <sage_pay_direct@dev.ceon.net>
 * @copyright  Copyright 2006-2009 Ceon
 * @link       http://dev.ceon.net/web/zen-cart/sage_pay_direct
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: class.sage_pay_directConfigCheck.php 385 2009-06-23 11:11:45Z Bob $
 */

// {{{ class sage_pay_directConfigCheck

/**
 * Checks if the Sage Pay Direct module is installed and in test/debug mode so that the user can be
 * alerted! Also checks if storage of details in the session is enabled but encryption is not
 * possible as the PEAR:Crypt_Blowfish package can't be accessed, alerting the user to relevant
 * information and possible solutions.
 *
 * @author     Conor Kerr <sage_pay_direct@dev.ceon.net>
 * @copyright  Copyright 2006-2009 Ceon
 * @link       http://dev.ceon.net/web/zen-cart/sage_pay_direct
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: class.sage_pay_directConfigCheck.php 385 2009-06-23 11:11:45Z Bob $
 */
class sage_pay_directConfigCheck extends base {
	
	function sage_pay_directConfigCheck()
	{
		global $messageStack;
		
		if ((defined('MODULE_PAYMENT_SAGE_PAY_DIRECT_DEBUGGING_ENABLED') &&
				MODULE_PAYMENT_SAGE_PAY_DIRECT_DEBUGGING_ENABLED == 'Yes') &&
				(defined('MODULE_PAYMENT_SAGE_PAY_DIRECT_STATUS') &&
				MODULE_PAYMENT_SAGE_PAY_DIRECT_STATUS == 'Yes')) {
			$messageStack->add('header', 'SAGE PAY DIRECT IS IN TESTING (DEBUG) MODE', 'warning');
		} else if ((defined('MODULE_PAYMENT_SAGE_PAY_DIRECT_TRANSACTION_MODE') &&
				(MODULE_PAYMENT_SAGE_PAY_DIRECT_TRANSACTION_MODE == 'Test' ||
				MODULE_PAYMENT_SAGE_PAY_DIRECT_TRANSACTION_MODE == 'VSP Simulator')) &&
				(defined('MODULE_PAYMENT_SAGE_PAY_DIRECT_STATUS') &&
				MODULE_PAYMENT_SAGE_PAY_DIRECT_STATUS == 'Yes')) {
			$messageStack->add('header', 'SAGE PAY DIRECT IS USING A TEST TRANSACTION MODE', 'warning');
		}
		
		if (defined('MODULE_PAYMENT_SAGE_PAY_DIRECT_DEBUGGING_ENABLED') &&
				MODULE_PAYMENT_SAGE_PAY_DIRECT_STORE_DETAILS_IN_SESSION == 'Yes') {
			if (MODULE_PAYMENT_SAGE_PAY_DIRECT_USE_BLOWFISH == 'Yes') {
				$pear_available = ceon_file_exists_in_include_path('PEAR.php');
				if ($pear_available != CEON_FILE_EXISTS_IN_INCLUDE_PATH__EXISTS) {
					$error_message = "&ldquo;Store entered details temporarily in session&rdquo; and &ldquo;Use Blowfish Encryption&rdquo; is enabled but this server's PHP installation does NOT have access to PEAR on this server!<br /><br />Either consult the documentation (especially the FAQs) to see how to get Blowfish encryption working on this server, change &ldquo;Store entered details temporarily in session&rdquo; to &ldquo;No&rdquo;, or disable Blowfish encryption.<br /><br />\n";
					$error_message .= 'It is not recommended to disable Blowfish encryption if there\'s a chance someone can see the contents of the &ldquo;sessions&rdquo; folder on this server (which may be possible if this is a shared server) - this is left up to the store owner\'s own descretion.<br /><br />';
					$error_message .= 'The current values for the various settings on this server follow. These are, <strong>without a doubt</strong>, the values currently set for these settings, available to PHP on the store\'s side, <strong>no matter what</strong> the admin, the server\'s control panel, the Server/Version Info or phpinfo() etc. may say!<br /><br />';
					$error_message .= "<strong>Info to help enable Blowfish support...</strong><br /><br />\n";
					
					if ($pear_available == CEON_FILE_EXISTS_IN_INCLUDE_PATH__DOESNT_EXIST) {
						if (ini_get('safe_mode') != 1) {
							$error_message .= "<strong>It would appear that the PEAR library is not on this server's PHP installation's include path. The path to the PEAR library on this server should be added to the PHP installation's include path configuration setting.</strong><br /><br />\n";
						} else {
							$error_message .= "<strong>It would appear that the PEAR library is not on this server's PHP installation's include path or is blocked by a safe mode setting. The path to the server's PEAR library should be checked to make sure it is in the PHP installation's include path configuration setting and/or that a safe mode setting is not interfering with PHP's ability to access the path to the PEAR library.</strong><br /><br />\n";
						}
					} else if ($pear_available ==
							CEON_FILE_EXISTS_IN_INCLUDE_PATH__POSSIBLY_BLOCKED_BY_OPEN_BASEDIR) {
						if (ini_get('safe_mode') != 1) {
							$error_message .= "<strong>It would appear that the PEAR library is not on this server's PHP installation's include path or is being blocked by open_basedir restrictions. The path to the server's PEAR library should be checked to make sure it is in the PHP installation's include path configuration setting and that the path to the PEAR: library is also in the PHP installation's open_basedir configuration setting.</strong><br /><br />\n";
						} else {
							$error_message .= "<strong>It would appear that the PEAR library is not on this server's PHP installation's include path, is being blocked by open_basedir restrictions or is being blocked by a safe mode setting. The path to this server's PEAR library should be checked to make sure it is in the PHP installation's include path configuration setting, that the path to the PEAR library is also in the PHP installation's open_basedir configuration setting, and that a safe mode setting is not interfering with PHP's ability to access the path to the PEAR library.</strong><br /><br />\n";
						}
					}
					
					$error_message .= "Alternatively, <strong>this server may have more than one PEAR installation</strong> - please check that the correct PEAR library is being used if there is more than one on this server.<br /><br />\n";
					
					$extra_debug_info = $this->_getExtraDebugInfo();
					
					$messageStack->add('header', 'SAGE PAY DIRECT: ' . $error_message .
						$extra_debug_info, 'warning');
				} else {
					$blowfish_available = ceon_file_exists_in_include_path('Crypt/Blowfish.php');
					if ($blowfish_available != CEON_FILE_EXISTS_IN_INCLUDE_PATH__EXISTS) {
						$error_message = "&ldquo;Store entered details temporarily in session&rdquo; and &ldquo;Use Blowfish Encryption&rdquo; is enabled but this server's PHP installation does NOT have access to PEAR:Crypt_Blowfish on this server!<br /><br />Either consult the documentation (especially the FAQs) to see how to get Blowfish encryption working on this server, change &ldquo;Store entered details temporarily in session&rdquo; to &ldquo;No&rdquo;, or disable Blowfish encryption.<br /><br />\n";
						$error_message .= 'It is not recommended to disable Blowfish encryption if there\'s a chance someone can see the contents of the &ldquo;sessions&rdquo; folder on this server (which may be possible if this is a shared server) - this is left up to the store owner\'s own descretion.<br /><br />';
						$error_message .= 'The current values for the various settings on this server follow. These are, <strong>without a doubt</strong>, the values currently set for these settings, available to PHP on the store\'s side, <strong>no matter what</strong> the admin, the server\'s control panel, the Server/Version Info or phpinfo() etc. may say!<br /><br />';
						$error_message .= "<strong>Info to help enable Blowfish support...</strong><br /><br />\n";
						
						if ($blowfish_available == CEON_FILE_EXISTS_IN_INCLUDE_PATH__DOESNT_EXIST) {
							if (ini_get('safe_mode') != 1) {
								$error_message .= "<strong>It would appear that the PEAR:Crypt_Blowfish package is not on this server's PHP installation's include path. Please check that Crypt_Blowfish is installed in this server's PEAR library.</strong><br /><br />\n";
							} else {
								$error_message .= "<strong>It would appear that the PEAR:Crypt_Blowfish package is not on this server's PHP installation's include path or is blocked by a safe mode setting. Please check that Crypt_Blowfish is installed in this server's PEAR library and that a safe mode setting is not interfering with PHP's ability to access the path to the Crypt_Blowfish files.</strong><br /><br />\n";
							}
						} else if ($blowfish_available ==
								CEON_FILE_EXISTS_IN_INCLUDE_PATH__POSSIBLY_BLOCKED_BY_OPEN_BASEDIR) {
							if (ini_get('safe_mode') != 1) {
								$error_message .= "<strong>It would appear that the PEAR:Crypt_Blowfish package is not on this server's PHP installation's include path or is being blocked by open_basedir restrictions. Please check that Crypt_Blowfish is installed in this server's PEAR library and that the path to the PEAR:Crypt_Blowfish package is also in the PHP installation's open_basedir configuration setting.</strong><br /><br />\n";
							} else {
								$error_message .= "<strong>It would appear that the PEAR:Crypt_Blowfish package is not on this server's PHP installation's include path, is being blocked by open_basedir restrictions or is being blocked by a safe mode setting. Please check that the PEAR:Crypt_Blowfish package is installed in this server's PEAR library, that the path to the Crypt_Blowfish files is also in the PHP installation's open_basedir configuration setting, and that a safe mode setting is is not interfering with PHP's ability to access the path to the Crypt_Blowfish files.</strong><br /><br />\n";
							}
						}
						
						$error_message .= "Alternatively, <strong>this server may have more than one PEAR installation and the PEAR:Crypt_Blowfish package may not have been installed in the &ldquo;correct&rdquo; PEAR library folder</strong> - please check that the correct PEAR library is being used if there is more than one on this server.<br /><br />\n";
						
						$extra_debug_info = $this->_getExtraDebugInfo();
						
						$messageStack->add('header', 'SAGE PAY DIRECT: ' . $error_message .
							$extra_debug_info, 'warning');
					}
				}
			}
		}
	}
	
	function _getExtraDebugInfo()
	{
		$extra_debug_info = "Current Include Path: " . get_include_path();
		
		$extra_debug_info .=  "\n<br /><br />Safe mode in use?: " .
			(ini_get('safe_mode') == 1 ? 'yes' : 'no');
		
		$extra_debug_info .= "\n<br /><br />open_basedir restricted directories: " .
			ini_get('open_basedir');
		
		return $extra_debug_info;
	}
}

// }}}
 
?>