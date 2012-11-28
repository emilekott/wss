<?php

/**
 * advshipper Checkout Process Shipping Date Record Auto Loader
 *
 * @author     Conor Kerr <zen-cart.advshipper@dev.ceon.net>
 * @copyright  Copyright 2007-2009 Ceon
 * @link       http://dev.ceon.net/web/zen-cart/advshipper
 * @license    http://www.gnu.org/copyleft/gpl.html   GNU Public License V2.0
 * @version    $Id: config.advshipperCheckoutProcess.php 382 2009-06-22 18:49:29Z Bob $
 */

$autoLoadConfig[200][] = array('autoType' => 'class',
	'loadFile' => 'observers/class.advshipperCheckoutProcess.php');
	
$autoLoadConfig[200][] = array('autoType' => 'classInstantiate',
	'className' => 'advshipperCheckoutProcess',
	'objectName' => 'advshipper_checkout_process');

?>