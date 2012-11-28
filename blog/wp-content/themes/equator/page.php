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
			?>/ All Posts
			</span>
		</div>
		<!-- end:main_container breadcrum-->
		<!-- begin:main_container contentbox-->
		<div class="contentbox">
			<div id="content" class="narrowcolumn" role="main">
				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				<div class="post" id="post-<?php the_ID(); ?>">
					<h2><?php the_title(); ?></h2>
					<!-- begin:main_container contentbox page-->
					<div class="entry">
						<?php the_content('<p class="serif">Read the rest of this page &raquo;</p>'); ?>
						<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
					</div>
					<!-- end:main_container contentbox page-->
				</div>
				<?php endwhile; endif; ?>
				<?php edit_post_link('Edit this entry.', '<p>', '</p>'); ?>
			</div>
		</div>
		<!-- end:main_container contentbox-->
	</div>
<?php get_sidebar(); ?>

<?php get_footer(); ?>
