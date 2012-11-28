<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

get_header();?>
<!-- begin:main_container-->
<div class="container_12 contentbg">
	<div class="grid_8">
		<div class="breadcrumb-bar">
			<span class="breadcrumbs"> 
			<?php if(function_exists('bcn_display'))
			{
			bcn_display();
			}
			?>
			</span>
		</div>
		<div class="contentbox">
			<div id="content" class="narrowcolumn">
			<h2 class="center">Error 404 - Not Found</h2>
			</div>
		</div>
	</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>