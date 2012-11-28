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

  require('includes/application_top.php');

	function ftp_get_rawlist($url, $login, $password, $ftp_dir='', $ssl=false, $port=21, $timeout=30) {
		$out = '';
		$out .= FTP_CONNECTION_OK . ' ' . $url . '<br />';
		if($ssl)
			$cd = @ftp_ssl_connect($url);
		else
			$cd = @ftp_connect($url, $port, $timeout);
		if (!$cd) {
			return $out . FTP_CONNECTION_FAILED . ' ' . $url . '<br />';
		}
		ftp_set_option($cd, FTP_TIMEOUT_SEC, $timeout);
		$login_result = @ftp_login($cd, $login, $password);
		if (!$login_result) {
			ftp_close($cd);
			return $out . FTP_LOGIN_FAILED . FTP_USERNAME . ' ' . $login . FTP_PASSWORD . ' ' . $password . '<br />';
		}
		if ($ftp_dir != "") {
			if (!@ftp_chdir($cd, $ftp_dir)) {
				ftp_close($cd);
				return $out . FTP_CANT_CHANGE_DIRECTORY . '&nbsp;' . $url . '<br />';
			}
		}
		$out .= ftp_pwd($cd) . '<br />';
		$raw = ftp_rawlist($cd, $ftp_file, true);
		for($i=0,$n=sizeof($raw);$i<$n;$i++){
			$out .= $raw[$i] . '<br />';
		}
		ftp_close($cd);
		return $out;
	}
?>
<?php
if(isset($_GET['action']) && $_GET['action'] == 'ftpdir') {
	ob_start();
	echo TEXT_GOOGLE_FROOGLE_FTP_FILES . '<br />';
	echo ftp_get_rawlist(GOOGLE_FROOGLE_SERVER, GOOGLE_FROOGLE_USERNAME, GOOGLE_FROOGLE_PASSWORD);
	$out = ob_get_contents();
	ob_end_clean();
	echo '<pre>';
	echo $out;
	exit();
}
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
<script language="javascript" src="includes/menu.js"></script>
<script language="javascript" src="includes/general.js"></script>
<script type="text/javascript">
  <!--
  function init()
  {
    cssjsmenu('navbar');
    if (document.getElementById)
    {
      var kill = document.getElementById('hoverJS');
      kill.disabled = true;
    }
  }
  // -->
</script>
<script language="javascript"><!--
function getObject(name) {
   var ns4 = (document.layers) ? true : false;
   var w3c = (document.getElementById) ? true : false;
   var ie4 = (document.all) ? true : false;

   if (ns4) return eval('document.' + name);
   if (w3c) return document.getElementById(name);
   if (ie4) return eval('document.all.' + name);
   return false;
}
//--></script>
<script language="javascript"><!--

var req, name;

function loadFroogleXMLDoc(request,field, loading) {

   name = field;
   var url="<?php echo HTTP_SERVER . DIR_WS_CATALOG . FILENAME_GOOGLEFROOGLE . ".php?" ?>"+request;
   // Internet Explorer
   try { req = new ActiveXObject("Msxml2.XMLHTTP"); }
   catch(e) {
      try { req = new ActiveXObject("Microsoft.XMLHTTP"); }
      catch(oc) { req = null; }
   }

   // Mozailla/Safari
   if (!req && typeof XMLHttpRequest != "undefined") { req = new XMLHttpRequest(); }

   // Call the processChange() function when the page has loaded
   if (req != null) {
      processLoading(loading);
      req.onreadystatechange = processChange;
      req.open("GET", url, true);
      req.send(null);
   }
}

function processChange() {
   if (req.readyState == 4 && req.status == 200)
      getObject(name).innerHTML = req.responseText;
}

function processLoading(text) {
  getObject(name).innerHTML = text;
}
//--></script>
</head>
<body onload="init()">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><img src="images/googlebase.gif" width="110" height="48"></td>
          </tr>
        </table></td>
      </tr>
      <tr>
      	<td width="100%" valign="top">
          <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="main">
            <tr>
              <td width="78%" align="left" valign="top">
<?php 
echo TEXT_GOOGLE_FROOGLE_OVERVIEW_HEAD; 
echo TEXT_GOOGLE_FROOGLE_OVERVIEW_TEXT; 
echo TEXT_GOOGLE_FROOGLE_INSTRUCTIONS_HEAD; 
printf(TEXT_GOOGLE_FROOGLE_INSTRUCTIONS_STEP1a, "\"javascript:(void 0)\" class=\"splitPageLink\" onClick=\"window.open('" . HTTP_SERVER . DIR_WS_CATALOG . FILENAME_GOOGLEFROOGLE . ".php?feed=fy_un_tp', 'googlefrooglefeed', 'resizable=1, statusbar=5, width=600, height=400, top=0, left=50, scrollbars=yes')\""); 
echo TEXT_GOOGLE_FROOGLE_INSTRUCTIONS_STEP1a_NOTE;
printf(TEXT_GOOGLE_FROOGLE_INSTRUCTIONS_STEP1b, "\"javascript:(void 0)\" class=\"splitPageLink\" onClick=\"window.open('" . HTTP_SERVER . DIR_WS_CATALOG . FILENAME_GOOGLEFROOGLE . ".php?feed=fy_un_td', 'googlefrooglefeed', 'resizable=1, statusbar=5, width=600, height=400, top=0, left=50, scrollbars=yes')\""); 
echo TEXT_GOOGLE_FROOGLE_INSTRUCTIONS_STEP1b_NOTE;
printf(TEXT_GOOGLE_FROOGLE_INSTRUCTIONS_STEP1c, "\"javascript:(void 0)\" class=\"splitPageLink\" onClick=\"window.open('" . HTTP_SERVER . DIR_WS_CATALOG . FILENAME_GOOGLEFROOGLE . ".php?feed=fy_un_tn', 'googlefrooglefeed', 'resizable=1, statusbar=5, width=600, height=400, top=0, left=50, scrollbars=yes')\""); 
echo TEXT_GOOGLE_FROOGLE_INSTRUCTIONS_STEP1c_NOTE;
?>
              	<div id="FroogleFeed" style="display: block; margin: 5px; width:96%; float: left; background-color:#CCCCCC;"></div>
<?php 
printf(TEXT_GOOGLE_FROOGLE_INSTRUCTIONS_STEP2a, "\"javascript:(void 0)\" class=\"splitPageLink\" onClick=\"window.open('" . HTTP_SERVER . DIR_WS_CATALOG . FILENAME_GOOGLEFROOGLE . ".php?feed=fn_uy_tp', 'googlefroogleupload', 'resizable=1, statusbar=5, width=600, height=400, top=0, left=50, scrollbars=yes')\""); 
printf(TEXT_GOOGLE_FROOGLE_INSTRUCTIONS_STEP2b, "\"javascript:(void 0)\" class=\"splitPageLink\" onClick=\"window.open('" . HTTP_SERVER . DIR_WS_CATALOG . FILENAME_GOOGLEFROOGLE . ".php?feed=fn_uy_td', 'googlefroogleupload', 'resizable=1, statusbar=5, width=600, height=400, top=0, left=50, scrollbars=yes')\"");
printf(TEXT_GOOGLE_FROOGLE_INSTRUCTIONS_STEP2c, "\"javascript:(void 0)\" class=\"splitPageLink\" onClick=\"window.open('" . HTTP_SERVER . DIR_WS_CATALOG . FILENAME_GOOGLEFROOGLE . ".php?feed=fn_uy_tn', 'googlefroogleupload', 'resizable=1, statusbar=5, width=600, height=400, top=0, left=50, scrollbars=yes')\""); 
if(GOOGLE_FROOGLE_UPLOADED_DATE != '') echo TEXT_GOOGLE_FROOGLE_LAST_UPLOAD . GOOGLE_FROOGLE_UPLOADED_DATE; 
printf(TEXT_GOOGLE_FROOGLE_INSTRUCTIONS_STEP2_NOTE, "\"javascript:(void 0)\" class=\"splitPageLink\" onClick=\"window.open('" . zen_href_link(FILENAME_GOOGLEFROOGLE, 'action=ftpdir') . "', 'googlefroogleftp', 'resizable=1, statusbar=5, width=600, height=400, top=0, left=50, scrollbars=yes')\""); 
 ?>
                <div id="FroogleFTP" style="display: block; margin: 5px; width:96%; float: left; background-color:#CCCCCC;"></div>
                <div id="FroogleUpload" style="display: block; margin: 5px; width:96%; float: left; background-color:#CCCCCC;"></div>
                <?php echo TEXT_GOOGLE_FROOGLE_INSTRUCTIONS_TIPS; ?>
              </td>
              <td width="22%" align="right" valign="top">
              	<table width="98%"  border="0" cellpadding="1" cellspacing="0" bgcolor="#E1EEFF">
                  <tr>
                    <td align="center" class="smallText"><?php echo TEXT_GOOGLE_FROOGLE_LOGIN_HEAD; ?> </td>
                  </tr>
                  <tr>
                    <td class="smallText">
                    	<table width="100%"  border="0" cellpadding="4" cellspacing="0" bgcolor="#F0F8FF">
                        <tr>
                          <td align="left" valign="top" class="smallText"><?php echo TEXT_GOOGLE_FROOGLE_LOGIN; ?></td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>
<!-- body_text_eof //-->
    </table>
    </td>
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>