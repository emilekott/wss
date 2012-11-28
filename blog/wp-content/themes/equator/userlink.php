<?php
/**
 Template Name: User Link
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
		<!-- begin:main_container contentbox Community Submit Page-->
		<div class="contentbox">
			<h2>Community Link Feed</h2>
			<br />
			<?php if (function_exists('fvCommunityNewsGetSubmissions'))
			echo fvCommunityNewsGetSubmissions(10, '<li><h5><a href="%submission_url%" title="%submission_title%">%submission_title%</a></h5>%submission_description%</li>'); ?>
			</ul>
			<br />
			<h3>* Required fields</h3>
			<?php if (function_exists('fvCommunityNewsForm'))
			fvCommunityNewsForm(); ?>
			<br />
		</div>
		<!-- end:main_container contentbox Community Submit Page-->
	</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>