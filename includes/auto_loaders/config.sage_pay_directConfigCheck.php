<?php

/**
 * sage_pay_direct Config Check Auto Loader
 *
 * @author     Conor Kerr <sage_pay_direct@dev.ceon.net>
 * @copyright  Copyright 2006-2009 Ceon
 * @link       http://dev.ceon.net/web/zen-cart/sage_pay_direct
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: config.sage_pay_directConfigCheck.php 385 2009-06-23 11:11:45Z Bob $
 */

$autoLoadConfig[175][] = array('autoType' => 'class',
	'loadFile' => 'class.sage_pay_directConfigCheck.php');
	
$autoLoadConfig[175][] = array('autoType' => 'classInstantiate',
	'className' => 'sage_pay_directConfigCheck',
	'objectName' => 'sage_pay_directConfigCheck');

?>