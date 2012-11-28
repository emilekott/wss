<?php
/*

Template Name: Gallery Page

*/
get_header();?>
<link rel="stylesheet" href="<?php echo bloginfo('template_url'); ?>/css/prettyPhoto.css" type="text/css" media="screen" title="prettyPhoto main stylesheet" />
<script src="<?php echo bloginfo('template_url'); ?>/js/jquery.prettyPhoto.js" type="text/javascript"></script>
	<script type="text/javascript" charset="utf-8">
		$(document).ready(function(){
			$(".gallery a[rel^='prettyPhoto']").prettyPhoto({theme:'light_square'});
		});
		</script>
<!-- end:PrettyPhoto Scripts-->

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
	
				<?php the_content(''); ?>

				<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>

		
		<?php endwhile; endif; ?>

<?php
 if (get_option('gallerypage') <> ''){
$gallery_cats= implode(",", get_option('gallerypage'));
}

$posts_per_page=get_option('galleryitems');
query_posts("posts_per_page=$posts_per_page&cat=$gallery_cats.&paged=$paged");
$timthumboption = get_option("timthumboption");
?>

<ul class="gallery clearfix">
			<!-- start: port Box  -->
			<?php if(have_posts()) : ?>

			<?php while(have_posts()) : the_post(); $img = get_post_meta($post->ID, "gallery_thumb", TRUE);?>
			<?php $fullimg = get_post_meta($post->ID, "post_fullimg", TRUE); ?>
			<li>
			<?php if( $timthumboption == "1" ) {	?>
			<?php if( $img && $fullimg)  {?>
			<a rel="prettyPhoto[mixed]" title="<?php the_title(); ?>" href="<?php echo get_post_meta( $post->ID, "post_fullimg", true ); ?>">
			<img src="<?php echo get_post_meta( $post->ID, "gallery_thumb", true ); ?>" width="180" height="180" alt="<?php the_title(); ?>" /></a>
			<?php } ?>

			<?php if($img && $fullimg=="") {?>
			<a rel="prettyPhoto[mixed]" title="<?php the_title(); ?>" href="<?php echo get_post_meta( $post->ID, "post_image", true ); ?>">
			<img src="<?php echo get_post_meta( $post->ID, "gallery_thumb", true ); ?>" width="180" height="180" alt="<?php the_title(); ?>" /></a>
			<?php } ?>

			<?php if($img=="" && $fullimg) {?>
			<a rel="prettyPhoto[mixed]" title="<?php the_title(); ?>" href="<?php echo get_post_meta( $post->ID, "post_fullimg", true ); ?>">
			<img src="<?php echo get_post_meta( $post->ID, "post_fullimg", true ); ?>" width="180" height="180" alt="<?php the_title(); ?>" /></a>
			<?php } }?>	
		


			<?php if( $timthumboption == "0" ) {	?>
			<?php if( $img && $fullimg)  {?>
			<a rel="prettyPhoto[mixed]" title="<?php the_title(); ?>" href="<?php echo get_post_meta( $post->ID, "post_fullimg", true ); ?>">
			<img src="<?php bloginfo( 'template_directory' ); ?>/timthumb.php?src=<?php echo get_post_meta( $post->ID, "gallery_thumb", true ); ?>&amp;w=180&amp;h=180" alt="<?php the_title(); ?>" /></a>
			<?php } ?>

			<?php if($img && $fullimg=="") {?>
			<a rel="prettyPhoto[mixed]" title="<?php the_title(); ?>" href="<?php echo get_post_meta( $post->ID, "post_image", true ); ?>">
			<img src="<?php bloginfo( 'template_directory' ); ?>/timthumb.php?src=<?php echo get_post_meta( $post->ID, "gallery_thumb", true ); ?>&amp;w=180&amp;h=180" alt="<?php the_title(); ?>" /></a>
			<?php } ?>

			<?php if($img=="" && $fullimg) {?>
			<a rel="prettyPhoto[mixed]" title="<?php the_title(); ?>" href="<?php echo get_post_meta( $post->ID, "post_fullimg", true ); ?>">
			<img src="<?php bloginfo( 'template_directory' ); ?>/timthumb.php?src=<?php echo get_post_meta( $post->ID, "post_fullimg", true ); ?>&amp;w=180&amp;h=180" alt="<?php the_title(); ?>" /></a>
			<?php } } ?>
</li>


  			<!-- end: port Box  -->
			<?php endwhile; ?>
</ul>
			<div class="clear"></div>

			<?php else :?>
			<h2>Sorry but we could not find what you were looking for. But don't give up, keep at it!</h2>
			<?php endif; ?>

			<div class="pagination wp-pagenavi">
			<?php if(function_exists('wp_pagenavi')) { wp_pagenavi(); } ?>
			</div>
			</div>
	</div>
	<!-- end:main_container contentbox-->
</div>
<?php get_sidebar(); ?>

<?php get_footer(); ?>
