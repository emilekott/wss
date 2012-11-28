<?php
$themename="Equator";
$cats_array = get_categories('hide_empty=0');
$pages_array = get_pages('hide_empty=0');
$dynamic_pages = array();
$dynamic_cats = array();
global $themename, $shortname, $options;

foreach ($pages_array as $pagg) {
	$dynamic_pages[$pagg->ID] = $pagg->post_title;
	$pages_ids[] = $pagg->ID;
	}
foreach ($cats_array as $categs) {
	$dynamic_cats[$categs->cat_ID] = $categs->cat_name;
	$cats_ids[] = $categs->cat_ID;
	}

// get color stylesheet
$colors=array();
if(is_dir(TEMPLATEPATH . "/colors/")) {
	if($style_dirs = opendir(TEMPLATEPATH . "/colors/")) {
		while(($color = readdir($style_dirs)) !== false) {
			if(stristr($color, ".css") !== false) {
				$colors[] = $color;
			}
		}
	}
}
$colors_select = $colors;

$options = array (

##################################################################
# General
##################################################################

array(	"name" => "generalsetting",
		"type" => "title"),

	array(	"type" => "open"),

	array(	"name" => "About Author",
			"desc" => "Check this disable if you would like to DISABLE the Single page About Author Info Box.",
			"id" => $shortname."aboutauthor",
			"type" => "radio",
			"options" => array("enable","disable"),
			"std" => "0"),

	array(	"name" => "Upload Logo",
			"desc" => "Upload your own Logo here from your desktop locally and click save all changes to effect.",
			"id" => $shortname."logourl",
			"std" => "",
			"type" => "logo"),	

	array(	"name" => "Homepage Posts Categories",
			"desc" => "Select the categories you not want to show in the index page. If you are using gallery usually you do not want to show the gallery posts on index page so check all categories and uncheck the gallery category so that it will not show in the index",
			"id" => $shortname."blogpage_cats",
			"std" => "",  "options" => $dynamic_cats,
	        "type" => "checkbox1"),
	
	array(	"name" => "Timthumb Resizeing",
			"type" => "radio",
			"id" => $shortname."timthumboption",
			"options" => array("enable","disable"),
			"std" => "0",
			"desc" => " If your server does not support timthumb then check Disable Timthumb and save changes"),


    array(	"type" => "close"),

##################################################################
# Colors
##################################################################

array(	"name" => "Colors",
		"type" => "title"),

	array(	"type" => "open"),

	array(	"name" => "Colors",
			"desc" => "Select the stylesheet you want to use for the Theme",
			"id" => $shortname."perfectcolors",
			"std" => "", "options" => $colors_select,
	        "type" => "select"),

	array(	"name" => "Extra CSS",
			"desc" => "Enter the Extra CSS you want to use for this theme",
			"id" => $shortname."extracss",
			"std" => "", 
	        "type" => "textarea"),

	array(	"type" => "close"),

##################################################################
# Navigation
##################################################################

array(	"name" => "Navigation",
		"type" => "title"),

	array(	"type" => "open"),

	array(	"name" => "Menu Pages",
			"desc" => "The selected pages only will show up in the menu. If you exclude a page with sub pages both will be excluded from the menu.",
			"id" => $shortname."pagestopnavigation",
			"std" => "", "options" => $dynamic_pages,
			"type" => "checkbox1"),

	array(	"name" => "Menu Categories",
			"desc" => "The selected categories only will show up in the menu. If you exclude a category with sub categories both will be excluded from the menu.",
			"id" => $shortname."categorytopnavigation",
			"std" => "", "options" => $dynamic_cats,
			"type" => "checkbox1"),

   	array(	"name" => __("Select Menu Font Style ", "weight of a font"),
			"desc" => __('A list to select Specific the weight of a font.', 'Specifies the weight of a font'),
			"id" => $shortname."weightfont",
			"options" => array('normal','bold','lighter','inherit'),
			"std" => "normal",
			"type" => "select"),

	array(	"name" => __("Select Menu Text Transform ", "Text transform"),
			"desc" => __('A list to select text-transform Property.', 'text-transform Property'),
			"id" => $shortname."texttransform",
			"options" => array('capitalize','uppercase','lowercase'),
			"std" => "capitalize",
			"type" => "select"),

    array(	"type" => "close"),

##################################################################
# Slider
##################################################################
		
array(	"name" => "Slider",
		"type" => "title"),

	array(	"type" => "open"),

	array(	"name" => "Select Featured Article Category",
			"desc" => "Select the category you want to assign for the featured slider on homepage.",
			"id" => $shortname."featuredarticle",
			"std" => "",
			"options"=>$dynamic_cats,
			"type" => "multifull"),
	
	array(	"name" => "Featured Article Limit",
			"desc" => "Enter the number of slides you want to display in homepage featured slider .",
			"id" => $shortname."featuredarticlelimit",
			"std" => "3",
			"type" => "text"),

	array(	"name" => "Homepage Slider",
			"desc" => "Check disable button if you would like to DISABLE the Homepage Featured Article Slider.",
			"id" => $shortname."homepageslider",
			"type" => "radio",
			"options" => array("enable","disable"),
			"std" => "0"),	

	array(	"type" => "close"),



##################################################################
# Sidebar Setting
##################################################################

	array(	"name" => "Sidebar",
			"type" => "title"),
			array(	"type" => "open"),
		
	array(	"name" => "Rss Feed",
			"desc" => "Enter your RSS Feed ID only example 'themeflash' <strong>(Note: Do not write full URL for the google feedburner)</strong><br />.",
			"id" => $shortname."rssfeed",
			"std" => "",
			"type" => "text"),

	array(	"name" => "Twitter ID",
			"desc" => "Enter your twitter ID example 'themeflash' <strong>(Note: Do not write full URL for the twitter)</strong><br /> ",
			"id" => $shortname."twitterid",
			"std" => "",
			"type" => "text"),

	array(	"name" => "Email RSS Feeds",
			"desc" => "Enter your Email RSS Feeds ID<br /> example 'themeflash'<strong>(Note: Do not write full URL for the google feedburner)</strong>",
			"id" => $shortname."emailupdate",
			"std" => "",
			"type" => "text"),

	array(	"name" => " Dissable Community Feeds",
			"type" => "radio",
			"id" => $shortname."communitystatus",
			"options" => array("enable","disable"),
			"std" => "0",
			"desc" => "Check disable button if you would like to DISABLE the Community Feeds in sidebar"), 

	array(	"name" => "Community Feeds Page ID",
			"desc" => "Enter the page ID of the communityfeeds Page you created<br /> ",
			"id" => $shortname."communityfeeds",
			"std" => "",
			"type" => "text"),

	array(	"type" => "close"),



##################################################################
# Gallery
##################################################################

array(	"name" => "Galleryoption",
		"type" => "title"),

	array(	"type" => "open"),

	array(	"name" => "Gallery Categories",
			"desc" => "The selected categories only will show up in the gallery. If you exclude a category with sub categories both will be excluded from the page.",
			"id" => $shortname."gallerypage",
			"std" => "", "options" => $dynamic_cats,
			"type" => "checkbox1"),

	array(	"name" => "No. of Items in Gallery",
			"desc" => "Enter the number of items to display in gallery page",
			"id" => $shortname."galleryitems",
			"std" => "",  
            "type" => "text"),

    array(	"type" => "close"),


##################################################################
# Socialbookmark Setting
##################################################################

	array(	"name" => "Socialbookmark",
			"type" => "title"),
			array(	"type" => "open"),
		
	array(  "name" => "Twitter",
	        "desc" => "Check disable button if you would like to DISABLE the twitter.",
	        "id" => $shortname."twitter",
			"type" => "radio",
			"options" => array("enable","disable"),
	        "std" => "0"),
	
	array(  "name" => "Facebook",
	        "desc" => "Check disable button if you would like to DISABLE the facebook.",
	        "id" => $shortname."facebook",	
			"type" => "radio",
			"options" => array("enable","disable"),
	        "std" => "0"),

	array(  "name" => "Digg",
	        "desc" => "Check disable button if you would like to DISABLE the digg.",
	        "id" => $shortname."digg",
			"type" => "radio",
			"options" => array("enable","disable"),
	        "std" => "0"),
	
	array(  "name" => "Delicious",
	        "desc" => "Check disable button if you would like to DISABLE the delicious.",
	        "id" => $shortname."delicious",
			"type" => "radio",
			"options" => array("enable","disable"),
	        "std" => "0"),
	
	array(  "name" => "Reddit",
	        "desc" => "Check disable button if you would like to DISABLE the reddit.",
	        "id" => $shortname."reddit",
			"type" => "radio",
			"options" => array("enable","disable"),
	        "std" => "0"),
	array(  "name" => "Stumbleupon",
	        "desc" => "Check disable button if you would like to DISABLE the stumbleupon.",
	        "id" => $shortname."stumbleupon",
			"type" => "radio",
			"options" => array("enable","disable"),
	        "std" => "0"),
	array(  "name" => "Designfloat",
	        "desc" => "Check disable button if you would like to DISABLE the designfloat.",
	        "id" => $shortname."designfloat",
			"type" => "radio",
			"options" => array("enable","disable"),
	        "std" => "0"),
	array(  "name" => "Linkedin",
	        "desc" => "Check disable button if you would like to DISABLE the linkedin.",
	        "id" => $shortname."linkedin",
			"type" => "radio",
			"options" => array("enable","disable"),
	        "std" => "0"),
		
	array(  "name" => "Myspace",
	        "desc" => "Check disable button if you would like to DISABLE the myspace.",
	        "id" => $shortname."myspace",
			"type" => "radio",
			"options" => array("enable","disable"),
	        "std" => "0"),

	array(	"type" => "close"),


##################################################################
# Advertisment
##################################################################

	array(	"name" => "Advertisement",
			"type" => "title"),

			array(	"type" => "open"),

	array(	"name" => "Header Banner",
			"desc" => "Enter the advertisement code of Header Banner which will appear beside Logo",
			"id" => $shortname."headerbanner",
			"std" => "",
			"type" => "textarea"),

	array(	"name" => "Sponsors Ad ",
			"desc" => "Enter the Advertise Code here. This ad will appear right beside the related post block in the post single page.",
			"id" => $shortname."sponsors",
			"std" => "",
			"type" => "textarea"),


	array(	"name" => "Sidebar Top Ad1",
			"desc" => "Enter the Advertise Code of the Banner.",
			"id" => $shortname."advertise1",
			"std" => "",
			"type" => "textarea"),

	array(	"name" => "Sidebar Top Ad2",
			"desc" => "Enter the Advertise Code of the Banner.",
			"id" => $shortname."advertise2",
			"std" => "",
			"type" => "textarea"),

	array(	"name" => "Sidebar Top Ad3",
			"desc" => "Enter the Advertise Code of the Banner.",
			"id" => $shortname."advertise3",
			"std" => "",
			"type" => "textarea"),


			array(	"type" => "close"),

##################################################################
# Footer Setting
##################################################################

	array(	"name" => "Footer",
			"type" => "title"),
			array(	"type" => "open"),
		

	array(	"name" => "Google Analytics",
			"desc" => "Enter the Google Analytics Script Code Here",
			"id" => $shortname."googleanalytics",
			"std" => "",
			"type" => "textarea"),


	array(	"name" => "Footer Copyright",
			"desc" => "Enter the content of the Footer Copyright Notice<br /> .",
			"id" => $shortname."copyright",
			"std" => "",
			"type" => "textarea"),


	array(	"name" => "Footer Recent Comments",
			"desc" => "Enter the number of Comments you want to display in footer<br /> ",
			"id" => $shortname."recentcoments",
			"std" => "",
			"type" => "text"),

	array(	"type" => "close")
);
?>