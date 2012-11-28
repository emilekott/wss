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
			<!-- begin:contentbox Archive -->
			<div class="s-result">
				<?php if (have_posts()) : ?>
		 	  <?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
		 	  <?php /* If this is a category archive */ if (is_category()) { ?>
				<h2 class="pagetitle">Archive for the &#8216;<?php single_cat_title(); ?>&#8217; Category</h2>
		 	  <?php /* If this is a tag archive */ } elseif( is_tag() ) { ?>
				<h2 class="pagetitle">Posts Tagged &#8216;<?php single_tag_title(); ?>&#8217;</h2>
		 	  <?php /* If this is a daily archive */ } elseif (is_day()) { ?>
				<h2 class="pagetitle">Archive for <?php the_time('F jS, Y'); ?></h2>
		 	  <?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
				<h2 class="pagetitle">Archive for <?php the_time('F, Y'); ?></h2>
		 	  <?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
				<h2 class="pagetitle">Archive for <?php the_time('Y'); ?></h2>
			  <?php /* If this is an author archive */ } elseif (is_author()) { ?>
				<h2 class="pagetitle">Author Archive</h2>
		 	  <?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
				<h2 class="pagetitle">Blog Archives</h2>
		 	  <?php } ?>
			</div>
			<!-- end:contentbox Archive -->
			<?php while (have_posts()) : the_post(); ?>
			<!-- begin:contentbox post-excerpt-->
			<div <?php post_class() ?>>
				<div class="excerpt">
					<span class="comments"><a href="<?php the_permalink(); ?>#comments" title="View Comments"><b><?php comments_number(0, 1, '%'); ?></b></a></span>
					<?php $postimageurl = get_post_meta($post->ID, 'post_image', true); if ($postimageurl) { 
$timthumboption = get_option("timthumboption"); 
if( $timthumboption == "0" ) {	?>
<a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><img src="<?php bloginfo( 'template_directory' ); ?>/timthumb.php?src=<?php echo get_post_meta( $post->ID, "post_image", true ); ?><?php echo $attributes; ?>&amp;w=200&amp;h=260&amp;zc=1" alt="<?php the_title(); ?>" class="post-img"/></a>
<?php } ?>

<?php if( $timthumboption =="1" ) {	?>

	<a href="<?php the_permalink(); ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>">
					<img src="<?php echo $postimageurl; ?>" alt="<?php the_title_attribute(); ?>" width="200"  height="260" class="post-img" />
					</a>
<?php } ?> 
					<?php } ?>
					<h1><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
					<div class="postmetadata"><em><?php the_time('M j'); ?>, in <?php the_category(', ') ?>, by <?php the_author_posts_link(); ?></em></div>
					<div class="short-text">
						<?php the_excerpt(); ?>
						<br />
						<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">Continue Reading &raquo;</a>
					</div>
				</div>
			</div>
			<!-- end:contentbox post-excerpt-->
			<?php endwhile; ?>
<?php if(function_exists('wp_pagenavi')) { wp_pagenavi(); } ?>  
	<?php else :
		if ( is_category() ) { // If this is a category archive
		printf("<h2 class='center'>Sorry, but there aren't any posts in the %s category yet.</h2>", single_cat_title('',false));
		} else if ( is_date() ) { // If this is a date archive
		echo("<h2>Sorry, but there aren't any posts with this date.</h2>");
		} else if ( is_author() ) { // If this is a category archive
		$userdata = get_userdatabylogin(get_query_var('author_name'));
		printf("<h2 class='center'>Sorry, but there aren't any posts by %s yet.</h2>", $userdata->display_name);
		} else {
		echo("<h2 class='center'>No posts found.</h2>");
		}
		get_search_form();

	endif;
?>
		</div>
		<!-- end:main_container contentbox-->
	</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
