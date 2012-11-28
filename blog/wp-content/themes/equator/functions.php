<?php
define('SYS32_FUNCTION',TEMPLATEPATH.'/functions');
define('SYS32_INCLUDES',SYS32_FUNCTION.'/includes');
define('SYS32_SCRIPTS',SYS32_FUNCTION.'/scripts');
define('SYSTEM32_URL',get_bloginfo('template_url')); 
define('SYS32_URL',get_bloginfo('template_url') .'/functions'); 
require(SYS32_FUNCTION."/custom.php");
require(SYS32_FUNCTION."/adminoption.php");


function mytheme_add_admin() {
 

    global $themename, $shortname,$options;

    if ( $_GET['page'] == basename(__FILE__) ) {
    
        if ( 'save' == $_REQUEST['action'] ) {
$uploadpath = wp_upload_dir();
$uploadpath['baseurl'] = SYSTEM32_URL;

if ($_FILES["logourl"]["type"]){
     $directory = TEMPLATEPATH.'/';
     move_uploaded_file($_FILES["logourl"]["tmp_name"],
     $directory . $_FILES["logourl"]["name"]);

     update_option('imglogourl', $uploadpath['baseurl']. "/". $_FILES["logourl"]["name"]);
    }

update_option('colorpickerField1', $_REQUEST['colorpickerField1']);
                foreach ($options as $value) {
                    update_option( $value['id'], $_REQUEST[ $value['id'] ] ); }

                foreach ($options as $value) {
                    if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } else { delete_option( $value['id'] ); } }

                header("Location: themes.php?page=functions.php&saved=true");
                die;

        } else if( 'reset' == $_REQUEST['action'] ) {

            foreach ($options as $value) {
                delete_option( $value['id'] ); }

            header("Location: themes.php?page=functions.php&reset=true");
            die;

        }
    }

   


if(function_exists('add_object_page'))
    {

 add_object_page($themename, "".$themename, 'edit_themes', basename(__FILE__), 'mytheme_admin');
    }
    else
    {
         add_menu_page($themename, "".$themename, 'edit_themes', basename(__FILE__), 'mytheme_admin');
    }

}

function mytheme_admin() {

    global $themename, $shortname, $options;

    if ( $_REQUEST['saved'] ) $msgsetting='<div id="message" class="updated fade"><p><strong>'.$themename.' settings saved.</strong></p></div>';
    if ( $_REQUEST['reset'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings reset.</strong></p></div>';
  require(SYS32_SCRIPTS."/scripts.php");  
?>
<div class="wrap">
<p>&nbsp;</p>
<div id="container">
	<div class="qhead">
		<div style="float:right;"><h2><?php echo $themename; ?> Settings </h2><?php echo $msgsetting; ?></div>
		<img src="<?php echo bloginfo('template_url'); ?>/functions/img/adminlogo.png" width="185" height="62" alt="Qpanel" />
	</div>
	<div class="infohead">
	<a href="http://themeforest.net/user/system32">Author Info</a> - 	<a href="http://themeforest.net/user/system32/portfolio">Other Themes</a>
	</div>
	<div id="tabsAndContent">
	<ul id="tabsNav">
		<li><a href="#generalsetting"><img src="<?php echo bloginfo('template_url'); ?>/functions/img/group-icon.png" width="16" height="16" alt="General" /> General Options</a></li>
		<li><a href="#Colors"><img src="<?php echo bloginfo('template_url'); ?>/functions/img/colors-icon.png" width="16" height="16" alt="Colors" /> Color Options</a></li>
		<li><a href="#Navigation"><img src="<?php echo bloginfo('template_url'); ?>/functions/img/nav-icon.png" width="16" height="16" alt="Navigation" /> Navigation Options</a></li>
		<li><a href="#Slider"><img src="<?php echo bloginfo('template_url'); ?>/functions/img/timeline_marker.png" width="16" height="16" alt="Slider" /> Slider Options</a></li>
		<li><a href="#Sidebar"><img src="<?php echo bloginfo('template_url'); ?>/functions/img/sidebar-icon.png" width="15" height="16" alt="Sidebar" />Sidebar Options</a></li>
		<li><a href="#Galleryoption"><img src="<?php echo bloginfo('template_url'); ?>/functions/img/gallery-icon.png" width="16" height="16" alt="Gallery" /> Gallery Options</a></li>
		<li><a href="#Socialbookmark"><img src="<?php echo bloginfo('template_url'); ?>/functions/img/social-icon.png" width="16" height="16" alt="Socialbookmark" />Social Bookmarks</a></li>
		<li><a href="#Footer"><img src="<?php echo bloginfo('template_url'); ?>/functions/img/footer-icon.png" width="16" height="16" alt="Footer" />Footer Options</a></li>
		<li><a href="#Advertisement"><img src="<?php echo bloginfo('template_url'); ?>/functions/img/ads-icon.png" width="16" height="16" alt="Advertisement" />Advertisement</a></li>
	</ul>
<?php require(SYS32_FUNCTION.'/option_page.php'); ?>
<div style="clear:both;"></div>
<div class="foot">
<p class="submit">
<input name="save" class="button-secondary" type="submit" value="Save all changes" />    
<input type="hidden" name="action" value="save" />
</p></form>
</div>
<?php
}

add_action('admin_menu', 'mytheme_add_admin'); ?>