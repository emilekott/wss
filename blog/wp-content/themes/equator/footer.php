<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
?>
<div class="clear">&nbsp;</div>
<!-- begin:Footer -->
<div class="contentbottom">
    <div class="pagebreak"></div>
</div>
<div class="ftr-mnu-bar">
    <ul>
        <li><a href="/">Home</a></li>
        <li><a href="http://www.witteringsurfshop.com/page.html?id=1">About Us</a></li>
        <li><a href="http://www.witteringsurfshop.com/contact_us.html">Contact Us</a></li>
        <li><a href="http://www.witteringsurfshop.com/index.php?main_page=account">My Account</a></li>
        <li><a href="http://www.witteringsurfshop.com/index.php?main_page=account">Log In</a></li>
        <li><a href="http://www.witteringsurfshop.com/index.php?main_page=checkout_shipping" class="lastitem">Checkout</a></li>
    </ul>
    <div class="clear"></div>
</div>
<div class="footer">
	<!-- begin:Container Footer -->
	<div class="container_12">
		<!-- begin:Footer Copyrights -->
		<div class="grid_4">
			<h2>Copyright Terms</h2>
			<p><?php $googleanalytics=get_option("googleanalytics"); 
                      $recentcoments=get_option("recentcoments");
                     $copyright=get_option("copyright"); echo stripslashes($copyright); ?></p>
		</div>
		<!-- end:Footer Copyrights -->
		<!-- begin:Footer Menu -->
		<div class="grid_4">
<?php wp_list_bookmarks('categorize=1&before=<li>&title_before=<h2>&title_after=</h2>&category_before=</n>&category_before=</n>&after=</li>&orderby=url'); ?>	
		</div>
		<!-- end:Footer Menu -->
		<!-- begin:Footer Recent Comments -->
		<div class="grid_4">
			<h2>Recent Comments</h2>
			<?php
			global $wpdb;
			$sql = "SELECT DISTINCT ID, post_title, post_password, comment_ID,
			comment_post_ID, comment_author, comment_date_gmt, comment_approved,
			comment_type,comment_author_url,
			SUBSTRING(comment_content,1,100) AS com_excerpt
			FROM $wpdb->comments
			LEFT OUTER JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID =
			$wpdb->posts.ID)
			WHERE comment_approved = '1' AND comment_type = '' AND
			post_password = ''
			ORDER BY comment_date_gmt DESC
			LIMIT $recentcoments";
			$comments = $wpdb->get_results($sql);
			$output = $pre_HTML;
			$output .= "\n<ul>";
			foreach ($comments as $comment) { ?>
<ul class="recentcomment">
<li>			
<?php echo "<a href=\"" . get_permalink($comment->ID) .
"#comment-" . $comment->comment_ID . "\" title=\"on " .
$comment->post_title . "\">" . strip_tags($comment->com_excerpt)
."</a>" ?>
<span>by <?php echo strip_tags($comment->comment_author); ?></span>
</li>
</ul>
<?php } ?>
		</div>
	</div>
</div>
	<!-- begin:Container Footer -->

        

</div> <!-- end #locate -->
<?php echo stripslashes($googleanalytics); ?>
</body>
</html>