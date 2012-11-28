<?php
/**
 * captcha_img.php generate CAPTCHA image
 *
 * @package captcha
 * @copyright Copyright 2004-2007 AndrewBerezin
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: captcha_img.php v 2.5 23.03.2007 13:11 AndrewBerezin $
 */

//define('STRICT_ERROR_REPORTING', true);

require('includes/application_top.php');

@ini_set('display_errors', '1');
error_reporting(E_ALL);
error_reporting(E_ALL & ~E_NOTICE);

require(DIR_WS_CLASSES . 'captcha.php');
$captcha = new captcha();

$captcha->debug = true;

if (headers_sent($filename, $linenum)) {
  echo "Headers already sent in $filename on line $linenum\n";
}

$captcha->generateCaptcha();

echo '<pre>';
var_export($captcha);
echo "\n\n\n";
echo '</pre>';
?>