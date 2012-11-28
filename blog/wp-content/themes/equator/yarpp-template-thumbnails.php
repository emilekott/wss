
<?php if ($related_query->have_posts()):?>
	
	
	
		<?php while ($related_query->have_posts()) : $related_query->the_post(); ?>
			<li>
				<a href="<?php the_permalink() ?>" rel="bookmark">
				<?php if ($related_thumbnail != "") : ?>
					<img src="<?php echo $related_thumbnail; ?>" alt="<?php the_title(); ?>" />
				<?php else : ?>
					<?php endif; ?>
				
				<?php the_title(); ?></a>
			</li>
				
		<?php endwhile; ?>
	
	<div class="clear">&nbsp;</div>
	
<?php else: ?>
	
	<p>No related posts found</p>

<?php endif; ?>
