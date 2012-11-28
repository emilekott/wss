<?php
if ( function_exists('register_sidebar') ) {
   register_sidebar(array(
       'before_widget' => '',
       'after_widget' => '<br clear="all" />',
       'before_title' => '<h2>',
       'after_title' => '</h2>',
   ));
}
?>
<?php
function string_getInsertedString($long_string,$short_string,$is_html=false){
  if($short_string>=strlen($long_string))return false;
  $insertion_length=strlen($long_string)-strlen($short_string);
  for($i=0;$i<strlen($short_string);++$i){
    if($long_string[$i]!=$short_string[$i])break;
  }
  $inserted_string=substr($long_string,$i,$insertion_length);
  if($is_html && $inserted_string[$insertion_length-1]=='<'){
    $inserted_string='<'.substr($inserted_string,0,$insertion_length-1);
  }
  return $inserted_string;
}

function DOMElement_getOuterHTML($document,$element){
  $html=$document->saveHTML();
  $element->parentNode->removeChild($element);
  $html2=$document->saveHTML();
  return string_getInsertedString($html,$html2,true);
}

function getFollowers($username){
  $x = file_get_contents("http://twitter.com/".$username);
  $doc = new DomDocument;
  @$doc->loadHTML($x);
  $ele = $doc->getElementById('follower_count');
  $innerHTML=preg_replace('/^<[^>]*>(.*)<[^>]*>$/',"\\1",DOMElement_getOuterHTML($doc,$ele));
  return $innerHTML;
}
?><?php

/* Custom Write Panel
/* ----------------------------------------------*/

$meta_boxes =
	array(
		"image" => array(
			"name" => "post",
			"type" => "text",
			"std" => "",
			"title" => "Image",
	"description" => "Using the \"<em>Add an Image</em>\" button, Upload an image and paste the URL here. Images will be resized. This is the Article's main image and will automatically be sized.") 	
	);

function meta_boxes() {
	global $post, $meta_boxes;
	
	echo'
		<table  cellspacing="0" id="inactive-plugins-table">
		
			<tbody class="plugins">';
	
			foreach($meta_boxes as $meta_box) {
				$meta_box_value = get_post_meta($post->ID, $pre.'_image', true);
				
				if($meta_box_value == "")
					$meta_box_value = $meta_box['std'];
				
				echo'<tr>
						<td width="100" align="center">';		
							echo'<input type="hidden" name="'.$meta_box['name'].'_noncename" id="'.$meta_box['name'].'_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
							echo'<h2>'.$meta_box['title'].'</h2>';
				echo'	</td>
						<td>';
							echo'<input type="text" name="'.$meta_box['name'].'_image" value="'.get_post_meta($post->ID, $meta_box['name'].'_image', true).'" size="100%" /><br />';
							echo'<p><label for="'.$meta_box['name'].'_image">'.$meta_box['description'].'.</label></p>';
				echo'	</td>
					</tr>';
			}
	
	echo'
			</tbody>
		</table>';		
}

function create_meta_box() {
	global $theme_name;
	if ( function_exists('add_meta_box') ) {
		add_meta_box( 'new-meta-boxes', 'eQuator Post Image', 'meta_boxes', 'post', 'normal', 'high' );
	}
}

function save_postdata( $post_id ) {
	global $post, $meta_boxes;
	
	foreach($meta_boxes as $meta_box) {
		// Verify
		if ( !wp_verify_nonce( $_POST[$meta_box['name'].'_noncename'], plugin_basename(__FILE__) )) {
			return $post_id;
		}
	
		if ( 'page' == $_POST['post_type'] ) {
			if ( !current_user_can( 'edit_page', $post_id ))
				return $post_id;
		} else {
			if ( !current_user_can( 'edit_post', $post_id ))
				return $post_id;
		}
	
		$data = $_POST[$meta_box['name'].'_image'];
		
		if(get_post_meta($post_id, $meta_box['name'].'_image') == "")
			add_post_meta($post_id, $meta_box['name'].'_image', $data, true);
		elseif($data != get_post_meta($post_id, $pre.'_image', true))
			update_post_meta($post_id, $meta_box['name'].'_image', $data);
		elseif($data == "")
			delete_post_meta($post_id, $meta_box['name'].'_image', get_post_meta($post_id, $meta_box['name'].'_image', true));
	}
}

add_action('admin_menu', 'create_meta_box');
add_action('save_post', 'save_postdata');
?>
<?php
function mytheme_comment($comment, $args, $depth) {
$GLOBALS['comment'] = $comment; ?>
<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
		<div id="comment-<?php comment_ID(); ?>" class="comment_wrap">
			<div class="comment-author">
				<div class="vcard">
					<?php echo get_avatar($comment,$size='80',$default='http://www.themeflash.com/wp-content/themes/themeflash/images/default_avatar_visitor.gif' ); ?>
					<br />
					<?php printf(__('<cite class="fn">%s</cite>'), get_comment_author_link()) ?>
					<div class="comment-meta">
						<?php printf(__('%1$s'), get_comment_date())?>
						<br />
						<?php edit_comment_link(__('(Edit)'),'  ','') ?>
					</div>
				</div>
			</div>
			<div class="single_comment">
				<?php 
				 if ( $depth == 1 ) { ?>
				<img src="<?php bloginfo('template_directory'); ?>/images/comment_arrow.png" class="comment-arrow" />
				<?php } ?>
				<?php if ($comment->comment_approved == '0') : ?>
				<div class="moderation"><em><?php _e('Your comment is awaiting moderation.') ?></em></div>
				<?php endif; ?>	
				<br />
				<?php comment_text() ?>
				<span class="reply">
				<?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
				</span>
			</div>
			<br clear="all" />
		</div>
<?php } ?>