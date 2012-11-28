<script type="text/javascript">
    $(document).ready(function() {
        $('#slider1').s3Slider({
            timeOut:3000 
        });
    });
</script>
<?php 
$featuredarticle=get_option("featuredarticle");
$featuredarticlelimit=get_option("featuredarticlelimit");
?>
<div id="slider1"><div class="featured"></div>
        <ul id="slider1Content">
<?php 

if($featuredarticle=="Select Category"){
$my_query = new WP_Query("showposts=$featuredarticlelimit");
}else{
$my_query = new WP_Query("cat=$featuredarticle&showposts=$featuredarticlelimit");
}
while ($my_query->have_posts()) : $my_query->the_post();$do_not_duplicate = $post->ID; ?>

<?php
 	$timthumboption = get_option("timthumboption"); 
if( $timthumboption == "0" ) {	?>
<li class="slider1Image">
<a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>">
<img src="<?php bloginfo( 'template_directory' ); ?>/timthumb.php?src=<?php echo get_post_meta( $post->ID, "featured_image", true ); ?>&amp;w=600&amp;h=280&amp;zc=1" alt="<?php the_title(); ?>"/></a>
<span class="bottom"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></span>
</li>
<?php } ?>

<?php if( $timthumboption =="1" ) {	?>

 <li class="slider1Image"> <a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><img width="600" height="280" src="<?php echo get_post_meta( $post->ID, "featured_image", true ); ?>" alt="<?php the_title(); ?>"/></a>
  <span class="bottom"><strong><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></strong></span></li>

<?php } ?> 
                   <?php endwhile; ?>
            
    <div class="clear slider1Image"></div>             
</ul>
</div>
