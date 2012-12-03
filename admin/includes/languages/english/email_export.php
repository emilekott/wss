<?php
//
// +----------------------------------------------------------------------+
// |zen-cart Open Source E-commerce                                       |
// +----------------------------------------------------------------------+
// | Copyright (c) 2003 The zen-cart developers                           |
// |                                                                      |
// | http://www.zen-cart.com/index.php                                    |
// |                                                                      |
// | Portions Copyright (c) 2003 osCommerce                               |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the GPL license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available through the world-wide-web at the following url:           |
// | http://www.zen-cart.com/license/2_0.txt.                             |
// | If you did not receive a copy of the zen-cart license and are unable |
// | to obtain it through the world-wide-web, please send a note to       |
// | license@zen-cart.com so we can mail you a copy immediately.          |
// +----------------------------------------------------------------------+
//  $Id: email_export.php  2011-08-09  swifty8078$
//

define('HEADING_TITLE', 'Email Address Export');
define('TEXT_EMAIL_EXPORT_FORMAT', 'Export File Format:');
define('TEXT_PLEASE_SELECT_AUDIENCE', 'Please choose the desired recipient list:');
define('TEXT_EMAIL_EXPORT_FILENAME', 'Export Filename:');
define('TEXT_EMAIL_EXPORT_SAVETOFILE','Save to file on server? (otherwise will stream for download directly from this window)');
define('TEXT_EMAIL_EXPORT_DEST','Destination: ');
define('ERROR_PLEASE_SELECT_AUDIENCE','Error: Please select an audience list to export');
define('TEXT_INSTRUCTIONS','<u>INSTRUCTIONS</u><br />You can use this page to export your Zen Cart newsletter subscribers (and others)<br />
list to a CSV or TXT file for easy import into an email program\'s address book.<br />Thus, you can use a 3rd-party emailing tool for 
sending your <br />advertising newsletters, etc.<br /><br />
1. Choose your export format.<br />
2. Choose the snapshot of customer info (recipient list).<br />
3. Enter a filename.  Careful about your choice of file extension.<br />
&nbsp;&nbsp;&nbsp;&nbsp;If you use .TXT, you can save it or open it in a Text Editor directly.<br />
&nbsp;&nbsp;&nbsp;&nbsp;If you use .CSV, you can save it or open it in a spreadsheet program directly.<br />
4. Click Save to proceed.<br />
5. Choose whether to save or open the file, depending on what your browser offers.');
?>