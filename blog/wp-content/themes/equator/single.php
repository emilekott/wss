<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

get_header();
?>
<!-- begin:main_container-->
<div class="container_12 contentbg">
	<div class="grid_8">
		<!-- begin:main_container breadcrum-->
		<div class="breadcrumb-bar">
			<span class="breadcrumbs"> 
			<?php if(function_exists('bcn_display'))
			{
			bcn_display();
			}
			?>
			</span>
		</div>
		<!-- end:main_container breadcrum-->
		<!-- begin:main_container contentbox-->
		<div class="contentbox">
			<!-- begin:contentbox Full Post -->
			<div class="fullpost">
				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				<!-- begin:contentbox post-excerpt-->
				<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
					<div class="excerpt">
						<?php $postimageurl = get_post_meta($post->ID, 'post_image', true); if ($postimageurl) { 

$timthumboption = get_option("timthumboption"); 
if( $timthumboption == "0" ) {	?>
<a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><img src="<?php bloginfo( 'template_directory' ); ?>/timthumb.php?src=<?php echo get_post_meta( $post->ID, "post_image", true ); ?><?php echo $attributes; ?>&amp;w=200&amp;h=260&amp;zc=1" alt="<?php the_title(); ?>" class="post-img"/></a>
<?php } ?>

<?php if( $timthumboption =="1" ) {	?>

	<a href="<?php the_permalink(); ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>">
					<img src="<?php echo $postimageurl; ?>" alt="<?php the_title_attribute(); ?>" width="200" height="260" class="post-img" />
					</a>
<?php } ?> 
						<?php } ?>
						<h1><?php the_title(); ?></h1>
						<div class="postmetadata"><em><?php the_time('M jS'); ?>, in <?php the_category(', ') ?>, by <?php the_author_posts_link(); ?></em></div>
						<div class="short-text">
						<?php the_excerpt(); ?>
						</div>
					</div>
				</div>
				<!-- end:contentbox post-excerpt-->
				<!-- begin:contentbox Author Box-->
		<?php 
$aboutauthor=get_option("aboutauthor");
if( $aboutauthor == "0" ) { ?>	
	<div class="authorbox">
					<!-- begin:contentbox Author Info-->
					<div class="authorinfo">
						<?php echo get_avatar(get_the_author_email(), $size = '80', $default = 'http://www.themeflash.com/wp-content/themes/themeflash/images/default_avatar_visitor.gif' ); ?>
						<h3>Author : <?php the_author(); ?></h3>
						<span>
							<a href="<?php the_author_url(); ?>" target="_blank">Author's Website</a> |
					Articles from <?php the_author_posts_link(); ?>	
						</span>
						<p><?php the_author_description(); ?></p>
					</div>
					<!-- end:contentbox Author Info-->
				</div>
<?php } ?>
				<!-- end:contentbox Author Box-->
				<div class="post"><?php the_content(); ?></div>

				<!-- begin:contentbox Share It-->
				<div class="shareit-box">
					<h2>Like this post? Share it!</h2>
					<!-- begin:contentbox Share It Icons and Links-->
					<ul>
<?php      
$twitter=get_option("twitter");
$facebook=get_option("facebook");
$digg=get_option("digg");
$delicious=get_option("delicious");
$reddit=get_option("reddit");
$stumbleupon=get_option("stumbleupon");
$designfloat=get_option("designfloat");
$linkedin=get_option("linkedin");
$myspace=get_option("myspace");
$sponsors=get_option("sponsors");
?>
						<?php if($twitter == "0") {  ?>
						<li><a href="http://twitter.com/home?status=<?php the_title(); ?> - <?php the_permalink(); ?>"><img src="<?php echo bloginfo('template_url'); ?>/images/social/twitter.png" alt="Tweet" /></a></li>
						<?php } ?>
						<?php if($facebook == "0") {  ?>
						<li><a href="http://www.facebook.com/share.php?u=<?php the_permalink(); ?>&t=<?php the_title(); ?>"><img src="<?php echo bloginfo('template_url'); ?>/images/social/facebook.png" alt="Facebook" /></a></li>
						<?php } ?>
						<?php if($digg == "0") {  ?>
						<li><a href="http://digg.com/submit?phase=2&url=<?php the_permalink(); ?>&title=<?php the_title(); ?>&bodytext=<?php the_excerpt(); ?>"><img src="<?php echo bloginfo('template_url'); ?>/images/social/digg.png" alt="Diggit" /></a></li>
						<?php } ?>
						<?php if($delicious == "0") {  ?>
						<li><a href="http://delicious.com/post?url=<?php the_permalink(); ?>&title=<?php the_title(); ?>&notes=<?php the_excerpt(); ?>"><img src="<?php echo bloginfo('template_url'); ?>/images/social/delicious.png" alt="Delicious" /></a></li>
						<?php } ?>
						<?php if($reddit == "0") {  ?>
						<li><a href="http://reddit.com/submit?url=<?php the_permalink(); ?>&title=<?php the_title(); ?>"><img src="<?php echo bloginfo('template_url'); ?>/images/social/reddit.png" alt="Diggit" /></a></li>
						<?php } ?>
						<?php if($stumbleupon == "0") {  ?>
						<li><a href="http://www.stumbleupon.com/submit?url=<?php the_permalink(); ?>&title=<?php the_title(); ?>"><img src="<?php echo bloginfo('template_url'); ?>/images/social/stumbleupon.png" alt="Diggit" /></a></li>
						<?php } ?>
						<?php if($designfloat == "0") {  ?>
						<li><a href="http://www.designfloat.com/submit.php?url=<?php the_permalink(); ?>&title=<?php the_title(); ?>"><img src="<?php echo bloginfo('template_url'); ?>/images/social/designfloat.png" alt="Diggit" /></a></li>
						<?php } ?>
						<?php if($linkedin == "0") {  ?>
						<li><a href="http://www.linkedin.com/shareArticle?mini=true&url=<?php the_permalink(); ?>&title=<?php the_title(); ?>&summary=<?php the_excerpt(); ?>"></a><img src="<?php echo bloginfo('template_url'); ?>/images/social/linkedin.png" alt="Diggit" /></a></li>
						<?php } ?>
						<?php if($myspace == "0") {  ?>
						<li><a href="http://www.myspace.com/Modules/PostTo/Pages/?u=<?php the_permalink(); ?>&t=<?php the_title(); ?>"></a><img src="<?php echo bloginfo('template_url'); ?>/images/social/myspace.png" alt="Diggit" /></a></li>
						<?php } ?>

					</ul>
					<!-- end:contentbox Share It Icons and Links-->
				</div>
				<!-- end:contentbox Share It-->
				<br />
				<!-- begin:contentbox Sponser Ads and Related Post-->
				<div class="post-bottom-box">
					<!-- begin:contentbox Sponser Ads-->
					<div class="adbox">
					<?php echo stripslashes($sponsors); ?>
					</div>
					<!-- end:contentbox Sponser Ads-->
				<!-- begin:contentbox Related Post-->
					<div class="related-post">
						<h2>Related Posts</h2>
						<?php if (function_exists('related_posts')){ ?>  
						<ul class="related-posts">
						<?php related_posts();?>  
						</ul>
						<?php }?> 
					</div>
					<!-- end:contentbox Related Post-->	
				</div>
				<!-- begin:contentbox Sponser Ads and Related Post-->
				<br />
				<!-- begin:contentbox Comment Block-->
				<div id="commentblock">
				<?php comments_template(); ?>
				</div>
				<!-- end:contentbox Comment Block-->
				<?php endwhile; else: ?>
				<p>Sorry, no posts matched your criteria.</p>
				<?php endif; ?>
			</div>
			<!-- end:contentbox Full Post -->
		</div>
		<!-- end:main_container contentbox-->
	</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>