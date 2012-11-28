<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv=Content-Type content="text/html; charset=ISO-8859-1">
<title>Contact</title>
<meta name="viewport" content="user-scalable = no">
<meta name="format-detection" content="telephone=no">
<meta name="generator" content="Freeway 5 Pro 5.6.4">
<meta name="viewport" content="user-scalable=no,width=1024">
<style type="text/css">
<!-- 
body { margin:0px; background-color:#ccc; height:100% }
html { height:100% }
form { margin:0px }
img { margin:0px; border-style:none }
button { margin:0px; border-style:none; padding:0px; background-color:transparent; vertical-align:top }
p:first-child { margin-top:0px }
table { empty-cells:hide }
.f-sp { font-size:1px; visibility:hidden }
.f-lp { margin-bottom:0px }
.f-fp { margin-top:0px }
em { font-style:italic }
h1 { font-weight:bold; font-size:18px }
h1:first-child { margin-top:0px }
h2 { font-weight:bold; font-size:16px }
h2:first-child { margin-top:0px }
h3 { font-weight:bold; font-size:14px }
h3:first-child { margin-top:0px }
strong { font-weight:bold }
.style16 { color:#24252d; font-family:Verdana,Arial,Helvetica,sans-serif; font-size:18px }
.style34 { color:#fff; font-family:Verdana,Arial,Helvetica,sans-serif; text-align:center }
.style14 { color:#fff; font-family:Verdana,Arial,Helvetica,sans-serif }
#name { font-family:Verdana,sans-serif; font-size:16px; text-align:left; color:#FFFFFF; background-color:#24252D; border-color:#3E3F4D; height:24px; width:300px;}
#email { font-family:Verdana,sans-serif; font-size:16px; text-align:left; color:#FFFFFF; background-color:#24252D; border-color:#3E3F4D; height:24px; width:300px;}
#telephone { font-family:Verdana,sans-serif; font-size:16px; text-align:left; color:#FFFFFF; background-color:#24252D; border-color:#3E3F4D; height:24px; width:300px;}
#message { font-family:Verdana,sans-serif; font-size:16px; text-align:left; color:#FFFFFF; background-color:#24252D; border-color:#3E3F4D; height:120px; width:312px;}
-->
</style>
<script type="text/javascript">
document.ontouchmove = function(e) {
	e.preventDefault();
}

function myBackButton() {
	location.replace("#top");
	history.back();
}
</script>

<script src="Resources/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="ValidateForm.js" type="text/javascript"></script>

</head>
<body onload="document.contact.name.focus();">
<a name="top"></a>

<div id="PageDiv" style="position:relative; min-height:100%; margin:auto; width:1024px">
	<form action="" name="contact">
	<div id="surfbackground" style="position:fixed; left:0px; top:0px; width:1024px; height:768px; z-index:1">
		<img src="Resources/surfbackground.jpeg" border=0 width=1024 height=768 alt="surfbackground" style="float:left">
	</div>
	<div id="TopBlock" style="position:fixed; left:0px; top:0px; width:1024px; height:180px; z-index:2">
		<img src="Resources/topblocke.png" border=0 width=1024 height=180 alt="" usemap="#map1" onclick="myBackButton();" style="float:left">
	</div>
	<div id="background" style="position:absolute; left:185px; top:222px; width:500px; height:300px; z-index:3">
		<img src="Resources/background.png" border=0 width=500 height=300 alt="background" style="float:left">
	</div>
	<div id="name" style="position:absolute; left:319px; top:253px; width:300px; height:24px; z-index:4">
		<input name="name" size=24 type="text" id="name"></div>
	<div id="email" style="position:absolute; left:319px; top:293px; width:300px; height:24px; z-index:5">
		<input name="email" size=24 type="email" id="email"></div>
	<div id="telephone" style="position:absolute; left:319px; top:333px; width:300px; height:24px; z-index:6">
		<input name="telephone" size=24 type="tel" id="telephone"></div>
	<div id="message" style="position:absolute; left:319px; top:373px; width:312px; height:120px; z-index:7">
		<textarea cols=25 rows=7 name="message" style="resize:none;" width="312px;" id="message"></textarea></div>
	<div id="SubmitButton" style="position:absolute; left:390px; top:558px; width:142px; height:42px; z-index:8">
		<input type=image src="Resources/submitbutton.png" name="submit" value="SubmitButton">
	</div>
	<div id="PHPcode" style="position:absolute; left:600px; top:600px; width:100px; height:100px; z-index:9">
		<?php
include 'lookup.php';

// Set error reporting level
ini_set('display_errors', 1);
error_reporting(E_ALL & ~(E_STRICT|E_NOTICE));

$referrer = $_GET["referrer"];
if (empty($referrer)) {
?>
	<script type="text/javascript">
	alert("Internal Error - no referring page!");
	history.back();
	</script>
<?php
	exit();
}

$contactTitle = '';
lookupReferrer($referrer);
?>
</div>
	<div id="EnquireAbout" style="position:absolute; left:190px; top:180px; width:500px; height:40px; z-index:10">
		<p class="f-lp"><span class="style16"><strong>Contact <?php echo $contactTitle ?></strong></span></p>
	</div>
	<div id="Referrer-Hidden" style="position:absolute; left:750px; top:600px; width:175px; height:31px; z-index:11">
		<input name="referrer2" value="<?php echo $referrer ?>" size=14 type="hidden"></div>
	<div id="ErrorFade" style="position:absolute; left:0px; top:0px; width:1024px; height:768px; z-index:12; display:none">
		<img src="Resources/errorfade.png" border=0 width=1024 height=768 alt="ErrorFade" style="float:left">
	</div>
	<div id="ErrorMessage1" style="position:absolute; left:636px; top:248px; width:222px; height:80px; z-index:13; display:none">
		<div id="ArrowPoint1" style="position:absolute; left:1px; top:-1px; width:30px; height:30px; z-index:1">
			<img src="Resources/arrowpoint1.gif" border=0 width=44 height=44 alt="ArrowPoint1" style="float:left">
		</div>
		<div id="Box1" style="position:absolute; left:22px; top:0px; width:200px; height:70px; z-index:2">
			<img src="Resources/box1.gif" border=0 width=200 height=70 alt="Box1" style="float:left">
		</div>
		<div id="Text1" style="position:absolute; left:45px; top:13px; width:160px; height:45px; z-index:3">
			<p class="style34 f-lp">You didn't enter your name !</p>
		</div>
	</div>
	<div id="ErrorMessage2" style="position:absolute; left:636px; top:288px; width:222px; height:80px; z-index:14; display:none">
		<div id="ArrowPoint2" style="position:absolute; left:1px; top:-1px; width:30px; height:30px; z-index:1">
			<img src="Resources/arrowpoint2.gif" border=0 width=44 height=44 alt="ArrowPoint2" style="float:left">
		</div>
		<div id="Box2" style="position:absolute; left:22px; top:0px; width:200px; height:70px; z-index:2">
			<img src="Resources/box2.gif" border=0 width=200 height=70 alt="Box2" style="float:left">
		</div>
		<div id="Text2" style="position:absolute; left:30px; top:13px; width:180px; height:45px; z-index:3">
			<p class="style34 f-lp">You didn't enter your email address !</p>
		</div>
	</div>
	<div id="ErrorMessage3" style="position:absolute; left:636px; top:328px; width:222px; height:80px; z-index:15; display:none">
		<div id="ArrowPoint3" style="position:absolute; left:1px; top:-1px; width:30px; height:30px; z-index:1">
			<img src="Resources/arrowpoint3.gif" border=0 width=44 height=44 alt="ArrowPoint3" style="float:left">
		</div>
		<div id="Box3" style="position:absolute; left:22px; top:0px; width:200px; height:70px; z-index:2">
			<img src="Resources/box3.gif" border=0 width=200 height=70 alt="Box3" style="float:left">
		</div>
		<div id="Text3" style="position:absolute; left:45px; top:13px; width:160px; height:45px; z-index:3">
			<p class="style34 f-lp">You didn't enter your telephone !</p>
		</div>
	</div>
	<div id="ErrorMessage4" style="position:absolute; left:636px; top:380px; width:222px; height:80px; z-index:16; display:none">
		<div id="ArrowPoint4" style="position:absolute; left:1px; top:-1px; width:30px; height:30px; z-index:1">
			<img src="Resources/arrowpoint4.gif" border=0 width=44 height=44 alt="ArrowPoint4" style="float:left">
		</div>
		<div id="Box4" style="position:absolute; left:22px; top:0px; width:200px; height:70px; z-index:2">
			<img src="Resources/box4.gif" border=0 width=200 height=70 alt="Box4" style="float:left">
		</div>
		<div id="Text4" style="position:absolute; left:45px; top:13px; width:160px; height:45px; z-index:3">
			<p class="style34 f-lp">You didn't enter your message !</p>
		</div>
	</div>
	<div id="ErrorMessage5" style="position:absolute; left:636px; top:390px; width:222px; height:150px; z-index:17; display:none">
		<div id="ArrowPoint5" style="position:absolute; left:1px; top:-1px; width:30px; height:30px; z-index:1">
			<img src="Resources/arrowpoint5.gif" border=0 width=44 height=44 alt="ArrowPoint5" style="float:left">
		</div>
		<div id="Box5" style="position:absolute; left:22px; top:0px; width:200px; height:150px; z-index:2">
			<img src="Resources/box5.gif" border=0 width=200 height=150 alt="Box5" style="float:left">
		</div>
		<div id="Text5" style="position:absolute; left:45px; top:13px; width:160px; height:120px; z-index:3">
			<p class="style34">Sorry, you have used more than 800 characters in your message.</p>
			<p class="style34 f-lp">Please shorten it !</p>
		</div>
	</div>
	<div id="ErrorMessage6" style="position:absolute; left:636px; top:288px; width:222px; height:80px; z-index:18; display:none">
		<div id="ArrowPoint6" style="position:absolute; left:1px; top:-1px; width:30px; height:30px; z-index:1">
			<img src="Resources/arrowpoint6.gif" border=0 width=44 height=44 alt="ArrowPoint6" style="float:left">
		</div>
		<div id="Box6" style="position:absolute; left:22px; top:0px; width:200px; height:70px; z-index:2">
			<img src="Resources/box6.gif" border=0 width=200 height=70 alt="Box6" style="float:left">
		</div>
		<div id="Text6" style="position:absolute; left:45px; top:13px; width:160px; height:45px; z-index:3">
			<p class="style34 f-lp"><span class="style14">That is an invalid</span> email address !</p>
		</div>
	</div>
	<div id="ErrorMessage7" style="position:absolute; left:636px; top:288px; width:222px; height:80px; z-index:19; display:none">
		<div id="ArrowPoint7" style="position:absolute; left:1px; top:-1px; width:30px; height:30px; z-index:1">
			<img src="Resources/arrowpoint7.gif" border=0 width=44 height=44 alt="ArrowPoint7" style="float:left">
		</div>
		<div id="Box7" style="position:absolute; left:22px; top:0px; width:200px; height:70px; z-index:2">
			<img src="Resources/box7.gif" border=0 width=200 height=70 alt="Box7" style="float:left">
		</div>
		<div id="Text7" style="position:absolute; left:30px; top:13px; width:186px; height:45px; z-index:3">
			<p class="style34 f-lp"><span class="style14">That </span>email address doesn't go anywhere !</p>
		</div>
	</div>
	</form>
	<map name="map1">
	<area alt="Home" coords="60,26,330,126" href="index.html">
	</map>
</div>
</body>
</html>
