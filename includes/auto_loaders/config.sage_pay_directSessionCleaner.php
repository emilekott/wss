<?php

/**
 * sage_pay_direct Encrypted Card Details Session Cleaner Auto Loader
 *
 * @author     Conor Kerr <sage_pay_direct@dev.ceon.net>
 * @copyright  Copyright 2006-2009 Ceon
 * @link       http://dev.ceon.net/web/zen-cart/sage_pay_direct
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: config.sage_pay_directSessionCleaner.php 385 2009-06-23 11:11:45Z Bob $
 */

$autoLoadConfig[200][] = array('autoType' => 'class',
	'loadFile' => 'observers/class.sage_pay_directSessionCleaner.php');
	
$autoLoadConfig[200][] = array('autoType' => 'classInstantiate',
	'className' => 'sage_pay_directSessionCleaner',
	'objectName' => 'sage_pay_direct_session_cleaner');

?>