<?php
/**
 * Comment Functions
 *
 * @package WordPress
 * @subpackage Rainbow_Kittens
 */

// Comment Template
// Used as a callback by wp_list_comments() for displaying the comments.
function rk_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
?>

<!-- <article> -->
<li <?php comment_class("article"); ?> id="comment-<?php comment_ID(); ?>">
	<!-- <header> -->
		<p><a href="<?php comment_link(); ?>" id="comment-permalink-<?php comment_ID(); ?>" class="comment-permalink">Posted <abbr class="published" title="<?php comment_time("c"); ?>"><?php time_since( get_comment_time('U') ); ?></abbr></a></p>
	<!-- </header> -->
	
	<div class="comment-content">
		<?php if ($comment->comment_approved == '0') : ?>
		<p><em>Your comment is awaiting moderation.</em></p>
		<?php endif; ?>
		<?php comment_text() ?>
	</div>
	
	<!-- <footer> -->
		<div class="vcard">
			<?php echo get_avatar( $comment, 48, '', get_comment_author() . "'s Gravatar" ); ?>
			<cite class="fn nickname"><?php comment_author_link(); ?></cite> <?php rk_parent_comment_link(); ?>
		</div>
    <ul>
      <?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'], 'before' => '<li>', 'after' => '</li>' ) ) ); ?>
      <?php rk_comment_quote_link(); ?>
      <?php edit_comment_link('Edit', '<li>', '</li>'); ?>
    </ul>
	<!-- </footer> -->

<?php
}

// Display a link to the parent comment of a reply (if applicable)
function rk_parent_comment_link() {
	$parentId = $GLOBALS['comment']->comment_parent;
	
	if (0 == $parentId)
		return;
	
	$parent = get_comment( $parentId );
	echo 'replied to <a href="#comment-' . $parent->comment_ID . '">' . get_comment_author( $parent->comment_ID ) . '</a>';
}

// Don't print <ul>'s if there are no <li>'s
// UNFINISHED
function rk_comment_meta( $comment, $args, $depth ) {
  $has_reply = false;
  if ( true ) {
    // Ref: get_comment_reply_link() : wp-includes/comment-template.php
    $has_reply = true;
  }
  
  $has_quote = false;
  if ( comments_open($GLOBALS['post']->ID) ) {
    $has_quote = true;
  }
  
  $has_edit = false;
  if ( current_user_can('edit_comment', $comment->comment_ID) ) {
    $has_edit = true;
  }
  
  if ( !$has_reply && !$has_quote && !$has_edit ) {
    return;
  }
?>
    <ul>
      <?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'], 'before' => '<li>', 'after' => '</li>' ) ) ); ?>
      <?php rk_comment_quote_link(); ?>
      <?php edit_comment_link('Edit', '<li>', '</li>'); ?>
    </ul>
<?php
}

// Notify parent commenter on reply
// Action Hook: do_action('comment_post', $comment_ID, $commentdata['comment_approved']);
// Ref: wp_notify_postauthor() : wp-includes/pluggable.php
// function rk_notify_parent($commentId, $status) {
//  
//  /* If comment is spam or moderated */
//  if ($status == "spam" || $status == 0)
//    return;
//  
//  $comment = get_comment($commentId);
//  
//  /* If comment is a trackback/pingback or parent */
//  if ($comment->comment_type != '' || $comment->comment_parent == 0)
//    return;
//  
//  $parent = get_comment($comment->comment_parent);
//  
//  if ('' == $parent->comment_author_email)
//    return;
//  
//  $post = get_post($comment->comment_post_ID);
//  
//  $notify_message = "Hi ".$parent->comment_author.".  ".$comment->comment_author." has responded to your comment on the post \"".$post->post_title."\" at ".get_option('blogname').":\r\n\r\n";
//  $notify_message .= "Author : ".$comment->comment_author."\r\n";
//  //$notify_message .= "E-mail : ".$comment->comment_author_email."\r\n";
//  $notify_message .= "URL    : ".$comment->comment_author_url."\r\n";
//  $notify_message .= "Comment:\r\n".$comment->comment_content."\r\n\r\n";
//  $notify_message .= "You can view your original comment here:\r\n";
//  $notify_message .= get_permalink($post->ID) . "#comment-".$parent->comment_ID."\r\n";
//  
//  $subject = "Re: [".get_option('blogname')."] Comment: \"".$post->post_title."\"";
//  
//  $from = "From: \"$comment->comment_author\" <$comment->comment_author_email>";
//  $reply_to = "Reply-To: \"$comment->comment_author_email\" <$comment->comment_author_email>";
//  
//  $message_headers = "$from\n" . "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"\n" . $reply_to . "\n";
//      
//  @wp_mail($parent->comment_author_email, $subject, $notify_message, $message_headers);
// }
// 
// add_action('comment_post', 'rk_notify_parent', 10, 2);