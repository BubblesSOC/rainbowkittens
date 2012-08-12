<?php
/**
 * Display Recent Comments Excerpts
 *
 * @package WordPress
 * @subpackage Rainbow_Kittens
 */

function rk_recent_comments( $count = 3 ) {
  
  // Exclude comments on protected posts
  // Filter Hook:
  //   $pieces = array( 'fields', 'join', 'where', 'orderby', 'order', 'limits' );
	//   $clauses = apply_filters_ref_array( 'comments_clauses', array( compact( $pieces ), &$this ) );
	// Ref: WP_Comment_Query->query() : wp-includes/comment.php
	add_filter('comments_clauses', 'rk_exclude_protected_comments');
  
  $args = array(
    'status' => 'approve',
    'post_status' => 'publish',
    'number' => $count
  );
  $comments = get_comments($args);
	
  // Comment Formatting Filters
  // Ref: wp-includes/default-filters.php
  add_filter( 'comment_excerpt', 'rk_remove_dotdotdot', 1 );
  add_filter( 'comment_excerpt', 'make_clickable',      9 );
  add_filter( 'comment_excerpt', 'wptexturize',         9 );
  add_filter( 'comment_excerpt', 'convert_smilies'        );
  add_filter( 'comment_excerpt', 'rk_convert_heart'       );
  add_filter( 'comment_excerpt', 'force_balance_tags', 25 );
  
  foreach ($comments as $comment) {
    echo "\t<li>" . get_avatar( $comment, 32 ) . "<br>";
		comment_author_link( $comment->comment_ID );
		
		// Check to see if this comment is a reply
		$replied_to = " said:<br>";
		if ( $comment->comment_parent !== '0' ) {
		  $parent = get_comment( $comment->comment_parent );
		  if ( is_object($parent) )
		    $replied_to = ' replied to <a href="'. get_comment_link( $parent->comment_ID ) .'">'. get_comment_author( $parent->comment_ID ) .'</a>:<br>';
		}
		echo $replied_to;

    // Remove quoted text from comment
    // Filter Hook: $_comment = apply_filters('get_comment', $_comment);
    // Ref: get_comment() : wp-includes/comment.php
    add_filter( 'get_comment', 'rk_remove_quoted_text' );
		comment_excerpt( $comment->comment_ID );
		remove_filter( 'get_comment', 'rk_remove_quoted_text' );
		
		echo ' <a href="'. get_comment_link( $comment->comment_ID ) .'">&rarr;</a>';
		echo "</li>\n";
  }
  
  remove_filter('comments_clauses', 'rk_exclude_protected_comments');
  remove_filter('comment_excerpt', 'rk_remove_dotdotdot');
  remove_filter('comment_excerpt', 'make_clickable');
  remove_filter('comment_excerpt', 'wptexturize');
  remove_filter('comment_excerpt', 'convert_smilies');
  remove_filter('comment_excerpt', 'rk_convert_heart');
  remove_filter('comment_excerpt', 'force_balance_tags');
}

function rk_exclude_protected_comments( $clauses ) {
  global $wpdb;
  
  if ( isset( $_COOKIE['wp-postpass_' . COOKIEHASH] ) )
    $clauses['where'] .= " AND (". $wpdb->posts . ".post_password = '' OR ". $wpdb->posts . ".post_password = '". $_COOKIE['wp-postpass_' . COOKIEHASH] ."') ";
  else
    $clauses['where'] .= " AND ". $wpdb->posts . ".post_password = '' ";

  return $clauses;
}

function rk_remove_quoted_text( $_comment ) {
  if ( !is_object($_comment) )
    return $_comment;
  
  $comment_text = rk_fix_quotes( $_comment->comment_content );
  $substrings = preg_split('/(\[quote comment=[0-9]+\])/', $comment_text, -1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
  
  if ( count($substrings) < 2 )
    return $_comment;
    
  $converted_text = '';
  $open_qs = array();
  
  foreach ($substrings as $substring) {
    preg_match('/\[quote comment=[0-9]+\]/', $substring, $matches);
    
    if ( !empty($matches) ) {
      // Opening [quote comment=123]
      array_push($open_qs, $matches[0]);
    }
    elseif ( empty($open_qs) ) {
      // Text before any [quote]'s
      $converted_text .= $substring;
    }
    else {
      // Closing [/quote]
      $splits = preg_split('/(\[\/quote\])/', $substring, -1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
      foreach ($splits as $split) {
        if ( $split == "[/quote]" && !empty($open_qs) ) {
          array_pop($open_qs);
        }
        elseif ( empty($open_qs) ) {
          $converted_text .= $split;
        }
      }
    }
  }
  $_comment->comment_content = trim($converted_text);
  return $_comment;
}

function rk_remove_dotdotdot( $excerpt ) {
  return preg_replace('/\.\.\.$/s', '', $excerpt);
}
