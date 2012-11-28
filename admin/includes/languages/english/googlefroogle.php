<?php
/**
 * googlefroogle.php
 *
 * @package google froogle
 * @copyright Copyright 2007 Numinix Technology http://www.numinix.com
 * @copyright Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: googlefroogle.php 8 2008-09-19 17:19:39Z numinix $
 */

define('HEADING_TITLE', 'Google Base Feeder');
define('TEXT_GOOGLE_FROOGLE_OVERVIEW_HEAD', '<p><strong>OVERVIEW:</strong></p>');
define('TEXT_GOOGLE_FROOGLE_OVERVIEW_TEXT', '<p>This module automatically generates product feed for your Zen-Cart store and upload it to Google Base ftp.</p>');
define('TEXT_GOOGLE_FROOGLE_INSTRUCTIONS_HEAD', '<p><strong>INSTRUCTIONS: </strong></p>');

define('TEXT_GOOGLE_FROOGLE_INSTRUCTIONS_STEP1a', '<p><strong><font color="#FF0000">Create Product Feed:</font></strong> Click <a href=%s><strong>[HERE]</strong></a> to create / update your products feed. </p>');
define('TEXT_GOOGLE_FROOGLE_INSTRUCTIONS_STEP1b', '<p><strong><font color="#FF0000">Create Documents Feed:</font></strong> Click <a href=%s><strong>[HERE]</strong></a> to create / update your documents feed. </p>');
define('TEXT_GOOGLE_FROOGLE_INSTRUCTIONS_STEP1c', '<p><strong><font color="#FF0000">Create News Feed:</font></strong> Click <a href=%s><strong>[HERE]</strong></a> to create / update your news feed. </p>');

define('TEXT_GOOGLE_FROOGLE_INSTRUCTIONS_STEP1a_NOTE', '<p>NOTE: You may <a href="' . HTTP_SERVER . DIR_WS_CATALOG . GOOGLE_FROOGLE_DIRECTORY . GOOGLE_FROOGLE_OUTPUT_FILENAME . "_products.xml" . '" target="_blank" class="splitPageLink"><strong>view</strong></a> your product feed file before proceeding to step 2. </p>');
define('TEXT_GOOGLE_FROOGLE_INSTRUCTIONS_STEP1b_NOTE', '<p>NOTE: You may <a href="' . HTTP_SERVER . DIR_WS_CATALOG . GOOGLE_FROOGLE_DIRECTORY . GOOGLE_FROOGLE_OUTPUT_FILENAME . "_documents.xml" . '" target="_blank" class="splitPageLink"><strong>view</strong></a> your document feed file before proceeding to step 2. </p>');
define('TEXT_GOOGLE_FROOGLE_INSTRUCTIONS_STEP1c_NOTE', '<p>NOTE: You may <a href="' . HTTP_SERVER . DIR_WS_CATALOG . GOOGLE_FROOGLE_DIRECTORY . GOOGLE_FROOGLE_OUTPUT_FILENAME . "_news.xml" . '" target="_blank" class="splitPageLink"><strong>view</strong></a> your news feed file before proceeding to step 2. </p>');  

define('TEXT_GOOGLE_FROOGLE_INSTRUCTIONS_STEP2_NOTE', '<p>NOTE: You may <a href="%s"><strong>view</strong></a> Google Base FTP content.</p>');
define('TEXT_GOOGLE_FROOGLE_FTP_FILES', 'Google Base FTP content:');

define('TEXT_GOOGLE_FROOGLE_INSTRUCTIONS_STEP2a', '<p><strong><font color="#FF0000">Upload Product Feed:</font></strong> Click <a href=%s><strong>[HERE]</strong></a> to upload products feed to Google Base.</p>');
define('TEXT_GOOGLE_FROOGLE_INSTRUCTIONS_STEP2b', '<p><strong><font color="#FF0000">Upload Documents Feed:</font></strong> Click <a href=%s><strong>[HERE]</strong></a> to upload documents feed to Google Base.</p>');
define('TEXT_GOOGLE_FROOGLE_INSTRUCTIONS_STEP2c', '<p><strong><font color="#FF0000">Upload News Feed:</font></strong> Click <a href=%s><strong>[HERE]</strong></a> to upload news feed to Google Base.</p>'); 

define('TEXT_GOOGLE_FROOGLE_LAST_UPLOAD', 'Last uploaded at ');
define('TEXT_GOOGLE_FROOGLE_INSTRUCTIONS_TIPS', '<p><strong><font color="#FF0000">Tips:</font></strong> To have this update and automatically upload product feed to Google Base, you will need to set up a Cron job via your host\'s control panel.<br />
To run it as a cron job (i.e. at 5:00 am), put something in your crontab like the 
following:<br /><br />
0 5 * * * GET ' . HTTP_CATALOG_SERVER . '/googlefroogle.php?feed=fy_uy_tp (other types: td for documents, tn for news)<br />
or<br />
0 5 * * * wget ' . HTTP_CATALOG_SERVER . '/googlefroogle.php?feed=fy_uy_tp
</p>');
define('TEXT_GOOGLE_FROOGLE_LOGIN_HEAD', '<strong>What is a product feed?</strong>');
define('TEXT_GOOGLE_FROOGLE_LOGIN', '<p>A product feed is a file containing information about the products listed on your site.</p><p>By sending this product feed regularly (once a month, once a day, or somewhere in between), you can make sure Google Base is displaying the latest pricing, promotional, or other information for your products.</p><p>For more information you can visit a <a href="http://base.google.com/support/" target="_blank" class="splitPageLink"><strong>Froogle Merchants Help Center</strong></a>.</p><p>To register or login to your Google account, click <a href="http://www.google.com/accounts/ManageAccount" target="_blank" class="splitPageLink"><strong>[HERE]</strong></a>.</p>');

define('FTP_CONNECTION_FAILED', 'Connection failed:');
define('FTP_CONNECTION_OK', 'Connected to:');
define('FTP_LOGIN_FAILED', 'Login failed:');
define('FTP_LOGIN_OK', 'Login ok:');
define('FTP_CURRENT_DIRECTORY', 'Current Directory Is:');
define('FTP_CANT_CHANGE_DIRECTORY', 'Can not change directory on:');
define('FTP_UPLOAD_FAILED', 'Upload Failed');
define('FTP_UPLOAD_SUCCESS', 'Uploaded Successfully');
define('FTP_SERVER_NAME', ' Server Name: ');
define('FTP_USERNAME', ' Username: ');
define('FTP_PASSWORD', ' Password: ');
?>