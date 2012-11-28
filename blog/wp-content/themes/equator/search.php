<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

get_header(); ?>
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
			<div id="content" class="narrowcolumn" role="main">
				<?php if (have_posts()) : ?>
				<!-- begin:contentbox search result-->
				<div class="s-result">
				<h2>Search Results for : <?php echo $s;?> </h2>
				</div>
				<!-- end:contentbox search result-->
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
					<img src="<?php echo $postimageurl; ?>" alt="<?php the_title_attribute(); ?>" width="200" height="260" class="post-img" />
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
				<?php else : ?>
				<h2 class="center">No posts found. Try a different search?</h2>
				<?php get_search_form(); ?>
				<?php endif; ?>
			</div>
		</div>
		<!-- end:main_container contentbox-->
	</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
