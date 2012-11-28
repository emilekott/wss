<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
?>
<?php      

/* This code retrieves all our admin options. */
$rssfeed=get_option("rssfeed");
$twitterid=get_option("twitterid");
$emailupdate=get_option("emailupdate");
$advertise1=get_option("advertise1");
$advertise2=get_option("advertise2");
$advertise3=get_option("advertise3");
$communitystatus=get_option("communitystatus");
$communityfeeds=get_option("communityfeeds");
?>
<?php if($rssfeed) {
						$fburl="https://feedburner.google.com/api/awareness/1.0/GetFeedData?uri=$rssfeed";
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($ch, CURLOPT_URL, $fburl);
						$stored = curl_exec($ch);
						curl_close($ch);
						$grid = new SimpleXMLElement($stored);
						$rsscount = $grid->feed->entry['circulation'];
}
						?>

			<!-- begin:sidebar grid -->
			<div class="grid_4" id="sidebar">
				<!-- begin:sidebar Feed,twutter and Email Updates -->
				<div class="feeds"><?php if($rssfeed) { ?>
					<a href="http://feeds.feedburner.com/<?php echo $rssfeed; ?>" class="tooltip" title="<?php echo $rsscount;?> Subscribers"><img src="<?php echo bloginfo('template_url'); ?>/images/rss.png" alt="Rss Feeds" /></a>
 <?php } ?>&nbsp;<?php if($twitterid) { ?>

					<a href="http://twitter.com/<?php echo $twitterid; ?>"><img src="<?php echo bloginfo('template_url'); ?>/images/twitter.png" alt="Twitter Followers" /></a> <?php } ?>
<?php if($emailupdate) { ?>
					<a href="http://feedburner.google.com/fb/a/mailverify?uri=<?php echo $emailupdate; ?>"><img src="<?php echo bloginfo('template_url'); ?>/images/emailrss.png" alt="Email Updates" /></a><?php } ?>
				</div>
				<!-- end:sidebar Feed,twutter and Email Updates -->
				<!-- begin:sidebar Search Box -->
				<div class="search">
					<fieldset>
						<legend title="Search">Search</legend>
						<form id="searchform" method="get" action="<?php bloginfo('url'); ?>/">
							<label>	<input type="text" value="search site" name="s" id="s" size="25" onfocus="if(this.value=='search site'){this.value=''};" onblur="if(this.value==''){this.value='search site'};" tabindex="2" <?php if ($req) echo "aria-required='true'"; ?> />	</label>
							<input type="submit" class="buttonhide" name="h" value="Go" />
						</form>
					</fieldset>
				</div>
				<!-- end:sidebar Search Box -->
				<hr class="seperator" />
				<!-- begin:sidebar Advertise Box 1 -->
				<?php if($advertise1){ ?>
				<div class="ads">
				<?php echo stripslashes($advertise1); ?>
				</div>
				<hr class="seperator" />
				<?php } ?>
				<!-- end:sidebar Advertise Box 1 -->
				<!-- begin:sidebar Advertise Box 3 -->
				<?php if($advertise3){ ?>
				<div class="ads">
				<?php echo stripslashes($advertise3); ?>
				</div>
				<hr class="seperator" />
				<?php } ?>
				<!-- end:sidebar Advertise Box 3 -->
				<!-- begin:sidebar Categories -->
				<div class="sidebar-content clearfix">
					<div class="submenu">
					<?php /*Lets widgetize up in here!*/
					if(!function_exists('dynamic_sidebar') || !dynamic_sidebar()) :
					endif;?>
					</div>
					<!-- end:sidebar Categories -->
					<br clear="all" />

			</div>
					<hr class="seperator" />

					<?php if($advertise2){ ?>
					<!-- begin:sidebar Advertise Box 2 -->
					<div class="ads">
					<?php echo stripslashes($advertise2); ?>
					</div>
					<hr class="seperator" />
					<?php } ?>
					<!-- end:sidebar Advertise Box 2 -->
					<!-- begin:sidebar Community Feed -->
<?php if( $communitystatus == "0" ) { 	?>
					<div class="communityfeeds">
					<h2>Community Feeds</h2>
						<?php if (function_exists('fvCommunityNewsGetSubmissions'))
						    echo fvCommunityNewsGetSubmissions(10, '<li><h6><a href="%submission_url%" title="%submission_title%">%submission_title%</a></h6>%submission_description%</li>'); ?>
<div class="clear"></div>
					<a href="<?php echo get_permalink($communityfeeds); ?>#add" class="button">Submit</a> <a href="<?php echo get_permalink($communityfeeds); ?>" class="button">More</a>
					</div>
<?php } ?>
					<!-- end:sidebar Community Feed -->
				</div>
				<!-- end:sidebar grid -->

	

		</div>
	<!-- end:main_container-->