<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>
<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>
<?php wp_head(); ?>
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/grid.css" type="text/css" />
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/comments.css" type="text/css" />
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />

<?php $style=get_option("perfectcolors"); if($style){ ?>
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory'); ?>/colors/<?php echo $style; ?>" media="screen" />
	<?php  }else{ ?>
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/colors/blue.css" type="text/css" media="screen" />
<?php } ?>

<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<!-- start:jquery scripts -->
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/jquery.easing.min.js"></script>
<!-- end:jquery scripts -->

<!-- start:Slider script and css -->
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/s3Slider.js"></script>
<link href="<?php bloginfo('template_directory'); ?>/css/slider.css" rel="stylesheet" type="text/css" />

<!-- end: slider jquery scripts -->

<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/main.js"></script>

<!-- start:jquery superfish -->
<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory'); ?>/css/superfish.css" media="screen" />
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/hoverIntent.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/superfish.js"></script>
<script type="text/javascript">
		// initialise plugins
		jQuery(function(){
			jQuery('ul.sf-menu').superfish();
			jQuery('ul.sf-menup').superfish();
		});

</script>
<!-- end:jquery superfish -->

<!--[if lt IE 7]>
        <script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/unitpngfix.js"></script>
	<![endif]-->

</head>
<style type="text/css">
<!--

<?php 
$extracss=get_option("extracss");

echo $extracss;
?>

-->
</style>
<body>

    <div id="locate">

<!-- begin:header_container-->
<div class="container_12">
<?php      

/* This code retrieves all our admin options. */

$pagestopnavigation=get_option("pagestopnavigation");
$headerbanner=get_option("headerbanner");
$categorytopnavigation=get_option("categorytopnavigation");
?>
	<!-- begin:header_top-menubar-->
	<div class="grid_12 top-bar">
		<ul class="sf-menup">
                    <li><a href="http://www.witteringsurfshop.com/" title="Back to Main Surf Shop Site">&lt; Back to Main Surf Shop Site</a></li>
                    <li><a href="http://twitter.com/WittSurfShop" title="Follow us on Twitter">Follow us on Twitter</a></li>
                    <li><a href="http://www.facebook.com/WitteringSurfShop" title="Become a fan on Facebook">Become a fan on Facebook</a></li>
                    <li><a href="http://feeds.feedburner.com/WitteringSurfShopBlog?format=xml" title="Subscribe via RSS">Subscribe via RSS</a></li>
                    <li><a href="http://www.witteringsurfshop.com/page.html?id=14" title="Subscribe via RSS">Sign up to our E-Newsletter</a></li>
                    <li><a class="finalitem" href="http://www.witteringsurfshop.com/contact_us.html" title="Contact the team">Contact the team</a></li>
                    <!--
			<li><a href="<?php echo get_option('home'); ?>" title="<?php bloginfo('name'); ?>">Home</a></li>

<?php
 if (get_option('pagestopnavigation') <> ''){
 $navpages = implode(",", get_option('pagestopnavigation'));
}
		 if($navpages){
			 wp_list_pages('title_li=&include='.$navpages);
			}else{
			 wp_list_pages('title_li=');  } 
?>
			<li><a href="<?php echo get_option('home'); ?>/wp-login.php?action=register" title="<?php bloginfo('name'); ?>">Register</a></li>
                    -->
		</ul>
	</div>
	<!-- end:header_top-menubar-->
	<div class="clear">&nbsp;</div>
	<!-- begin:header_Logo And Banner468 -->
	<div class="header">
		<!-- begin:header_Logo -->
		<div class="grid_4">
                    <a href="http://www.witteringsurfshop.com/">
                    <img src="<?php bloginfo('stylesheet_directory') ?>/images/logo.jpg" alt="<?php bloginfo('title') ?>" />
                    </a>
		</div>
		<!-- end:header_Logo -->
		<!-- begin:header_Banner468 -->
		<div class="grid_8">
		<div class="banner468"><?php //echo stripslashes($headerbanner); ?>
                    <div id="hdr-call-us">
                        <a href="http://www.witteringsurfshop.com/contact_us.html">
                        <img src="<?php bloginfo('stylesheet_directory') ?>/images/callus.gif" alt="Call us on 01243 672292 - Expert Advice 7 Days a week 9AM-5PM" />
                        </a>
                    </div>
                    <div id="hdr-cart">
                        <a href="http://www.witteringsurfshop.com/">
                            <img src="<?php bloginfo('stylesheet_directory') ?>/images/cart.gif" alt="Your Shopping Cart" />
                        </a>
                    </div>
                </div>
		</div>
		<!-- end:header_Banner468 -->
                <div class="clear">&nbsp;</div>
                <div class="menu-bar">
		<ul style="font-weight:<?php echo get_option('weightfont'); ?>;text-transform:<?php echo get_option('texttransform'); ?>;" class="sf-menu">
			<li><a href="<?php echo get_option('home'); ?>" title="<?php bloginfo('name'); ?>">Home</a></li>
                <?php
                 if (get_option('categorytopnavigation') <> ''){
                 $navcats = implode(",", get_option('categorytopnavigation'));
                }
                if($navcats) {
                wp_list_categories('title_li=&include='.$navcats);
                }else{
                  } ?>
                </ul>
                </div>

                <div class="pagebreak"></div>

	</div>
	<div class="clear">&nbsp;</div>
	<!-- end:header_Logo And Banner468 -->

        

</div>
<!-- end:header_container-->
<div class="clear">&nbsp;</div>
<!-- begin:header_container menu -->
<div class="container_12">
	<!-- begin:header_container menu-bar -->
	
<!-- end:header_container menu-bar -->
</div>
<!-- end:header_container menu -->
<div class="clear">&nbsp;</div>
