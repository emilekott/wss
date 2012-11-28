<?php

/**
 * ceon_manual_card File System Function(s)
 *
 * This file contains a function necessary to determine if a file exists in the current include path
 *
 * @author     Conor Kerr <conor.kerr_zen-cart@dev.ceon.net>
 * @author     Aidan Lister <aidan@php.net>
 * @copyright  Copyright 2006 Ceon
 * @copyright  Copyright 2004-2006 Aidan Lister
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: ceon_manual_card_functions_file_system.php 180 2006-09-11 17:22:52Z conor $
 */

// {{{ file_exists_in_include_path()

/**
 * Check if a file exists in the include path
 *
 * @version     1.2.1
 * @author      Aidan Lister <aidan@php.net>
 * @link        http://aidanlister.com/repos/v/function.file_exists_incpath.php
 * @param       string     $file       Name of the file to look for
 * @return      mixed      The full path if file exists, FALSE if it does not
 */
if (!function_exists('file_exists_in_include_path')) {
	function file_exists_in_include_path($file)
	{
		$paths = explode(PATH_SEPARATOR, get_include_path());
		
		foreach ($paths as $path) {
			// Formulate the absolute path
			$fullpath = $path . DIRECTORY_SEPARATOR . $file;
			
			// Check it
			if (file_exists($fullpath)) {
				return $fullpath;
			}
		}
		
		return false;
	}
}

// }}}

?>