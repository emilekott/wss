<?php

/**
 * Ceon File System Function(s)
 *
 * This file contains a function necessary to determine if a file exists in the current include path
 * and isn't blocked by any open_basedir restrictions.
 *
 * @author     Conor Kerr <sage_pay_direct@dev.ceon.net>
 * @author     Aidan Lister <aidan@php.net>
 * @copyright  Copyright 2006-2009 Ceon
 * @copyright  Copyright 2004-2006 Aidan Lister
 * @link       http://dev.ceon.net/web/zen-cart/sage_pay_direct
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: ceon_file_system_functions.php 385 2009-06-23 11:11:45Z Bob $
 */

// {{{ Constants

/**
 * Constants to be used as return values
 */
define('CEON_FILE_EXISTS_IN_INCLUDE_PATH__EXISTS', 1);
define('CEON_FILE_EXISTS_IN_INCLUDE_PATH__DOESNT_EXIST', -1);
define('CEON_FILE_EXISTS_IN_INCLUDE_PATH__POSSIBLY_BLOCKED_BY_OPEN_BASEDIR', -2);

// }}}


// {{{ ceon_file_exists_in_include_path()

/**
 * Checks if a file exists in the include path and isn't blocked by any open_basedir restrictions.
 *
 * @version    4.0.0
 * @author     Conor Kerr <sage_pay_direct@dev.ceon.net>
 * @author     Aidan Lister <aidan@php.net>
 * @link       http://dev.ceon.net/web/zen-cart/sage_pay_direct
 * @link       http://aidanlister.com/repos/v/function.file_exists_incpath.php
 * @param      string     $file       Name of the file to look for.
 * @return     mixed      A postitive value if the file exists and can be accessed, otherwise a
 *                        negative integer indicating the problem accessing the file.
 */
function ceon_file_exists_in_include_path($file)
{
	$include_paths = explode(PATH_SEPARATOR, get_include_path());
	
	$open_basedir_restrictions = explode(PATH_SEPARATOR, @ini_get('open_basedir'));
	
	foreach ($include_paths as $include_path) {
		// Formulate the absolute path
		$fullpath = $include_path . DIRECTORY_SEPARATOR . $file;
		
		if (file_exists($fullpath)) {
			return CEON_FILE_EXISTS_IN_INCLUDE_PATH__EXISTS;
		}
	}
	
	if (sizeof($open_basedir_restrictions) > 0 &&
			strlen($open_basedir_restrictions[0]) > 0) {
		// File may be being blocked as an existing open_basedir restriction won't allow
		// access to that path!
		return CEON_FILE_EXISTS_IN_INCLUDE_PATH__POSSIBLY_BLOCKED_BY_OPEN_BASEDIR;
	}
	
	// File doesn't exist on the include_path
	return CEON_FILE_EXISTS_IN_INCLUDE_PATH__DOESNT_EXIST;
}

// }}}

?>