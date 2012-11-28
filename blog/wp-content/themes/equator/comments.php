<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

// Do not delete these lines
	if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');

	if ( post_password_required() ) { ?>
		<p class="nocomments">This post is password protected. Enter the password to view comments.</p>
	<?php
		return;
	}
?>

<!-- You can start editing here. -->

<? if ($post->ID != 2535) {  ?>

<?php if ( have_comments() ) : ?>

			<div id="comments"><!--start:comments -->
				<h1>User Comments</h1>
				<ol class="commentlist">
					<?php wp_list_comments('type=comment&callback=mytheme_comment'); ?>
				</ol>
			</div><!--end:comments -->
			<div class="navigation"><!--start:navigation -->
				<div class="alignleft"><?php previous_comments_link() ?></div>
				<div class="alignright"><?php next_comments_link() ?></div>
			</div><!--end:navigation -->
			 <?php else : // this is displayed if there are no comments so far ?>

			<?php if ('open' == $post->comment_status) : ?>
				<!-- If comments are open, but there are no comments. -->
		
			 <?php else : // comments are closed ?>	
				<!-- If comments are closed. -->
				<p class="nocomments">Comments are closed.</p>
		
			<?php endif; ?>
		<?php endif; ?>
	
	
		<?php if ('open' == $post->comment_status) : ?>
			<!--start:repond -->
			<div id="respond">
				<div class="cancel-comment-reply fr">
					<small><?php cancel_comment_reply_link(); ?></small>
				</div>
				<h3><?php comment_form_title( 'Leave a Reply', 'Leave a Reply to %s' ); ?></h3>
				<?php if ( get_option('comment_registration') && !$user_ID ) : ?>
				<p>You must be <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php echo urlencode(get_permalink()); ?>">logged in</a> to post a comment.</p>
				</div>
				<?php else : ?>
				<!--start:form -->
				<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">
				<?php if ( $user_ID ) : ?>
					<p>Logged in as <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. <a href="<?php echo wp_logout_url(get_permalink()); ?>" title="Log out of this account">Log out &raquo;</a></p>
				<?php else : ?>
					<p><input type="text" name="author" id="author" value="Name" onfocus="if(this.value=='Name'){this.value=''};" onblur="if(this.value==''){this.value='Name'};"	 tabindex="1" <?php if ($req) echo "aria-required='true'"; ?> /></p>
					<p><input type="text" name="email" id="email" value="E-mail" onfocus="if(this.value=='E-mail'){this.value=''};" 	onblur="if(this.value==''){this.value='E-mail'};" tabindex="2" <?php if ($req) echo "aria-required='true'"; ?> /></p>
					<p><input type="text" name="url" id="url" value="URL (optional)" onfocus="if(this.value=='URL (optional)'){this.value=''};" onblur="if(this.value==''){this.value='URL (optional)'};" tabindex="2" /></p>
					<?php endif; ?>
					<!--<p><small><strong>XHTML:</strong> You can use these tags: <code><?php echo allowed_tags(); ?></code></small></p>-->
					<p><textarea name="comment" id="comment" cols="30" rows="10" Value="Type your message here" onfocus="if(this.value=='Type your message 	here'){this.value=''};" onblur="if(this.value==''){this.value='Type your message here'};" tabindex="2"></textarea></p>
					<p><input name="submit" type="submit" class="button" id="submit" tabindex="2" value="Submit" /><?php comment_id_fields(); ?></p>
					<?php do_action('comment_form', $post->ID); ?>
				</form>
				<!--end:form -->
			</div>
			<!--end:repond-->
<?php endif; // If registration required and not logged in ?>
<?php endif; // if you delete this the sky will fall on your head ?>
		
<?php
} else { ?> 

			<h2 style="margin-top:30px;">Previous User Submissions</h2>
			<a href="#add" class="floated_link">Submit a Link</a>
		
			<?php if ($comments) : ?>
				<ol>
					<?php foreach ($comments as $comment) : ?>
						<?php if (get_comment_type() == "comment"){ ?>					
							<li id="comment-<?php comment_ID() ?>" >
								<?php comment_author_link(); ?> <br />
								<?php comment_text(); ?>
							</li>
						<?php } ?>
					<?php endforeach; /* end for each comment */ ?>
				</ol>
			<?php endif; ?>
			
			
			<div style="clear:both"></div>				
			<a name="add"></a>
			<h2 style="margin-top:30px;">Submit a Link</h2>
	
			<div class="formcontainer">	
				<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">
					<p><input type="text" name="author" id="author" value="" size="22" tabindex="1" />
					<label for="author"><small>Link Title <?php if ($req) echo "(required)"; ?></small></label></p>
					<input type="hidden" name="email" id="email" value="mohdabdulwajidkhan@gmail.com" size="22" tabindex="2" />
					<p><input type="text" name="url" id="url" value="" size="22" tabindex="3" />
					<label for="url"><small>Link URL</small></label></p>
					<p><input type="text" name="comment" id="comment" value="" size="22" tabindex="3" />
					<label for="url"><small>Link Description (Max 20 Word)</small></label></p>
					<p><input name="submit" type="image" src="<?php bloginfo('template_directory'); ?>/images/submit.gif" id="submit" tabindex="5" value="Submit Comment"  class="button" />
					<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
					</p>
					<?php do_action('comment_form', $post->ID); ?>
				</form>
			</div>					
			<div style="clear:both"></div>	

<? } ?>