<?php
/**
 * Quoter Functionality
 *
 * Based on the discontinued plugin by Daniele Mancino
 * @link http://www.damagedgoods.it/wp-plugins/quoter/
 *
 * @package WordPress
 * @subpackage Rainbow_Kittens
 */

// Display the 'quote' link
function rk_comment_quote_link() {
  $post = $GLOBALS['post'];
  
  if ( !comments_open($post->ID) )
		return false;
		
	$comment_text = rk_fix_quotes( get_comment_text() );
		
	echo '<li><a href="#" class="comment-quote-link" id="comment-quote-link-' . get_comment_ID() . '" data-comment="' . esc_js( str_replace('\\', '\\\\', $comment_text) ) . '">Quote</a></li>';
	echo "\n";
}

function rk_fix_quotes($comment_text) {
  // Convert [/quote]'s to lowercase
  $comment_text = str_ireplace('[/quote]', '[/quote]', $comment_text);
  
  // Remove quotation marks (i.e. [quote comment="123"] to [quote comment=123])
  $comment_text = preg_replace('/\[quote comment="([0-9]+)"\]/i', '[quote comment=$1]', $comment_text);
  
  // Convert [quote comment=123] to lowercase
  return preg_replace('/\[quote comment=([0-9]+)\]/i', '[quote comment=$1]', $comment_text);
}
add_filter('comment_text', 'rk_fix_quotes', 1);


function rk_convert_quotes($comment_text) {
  $substrings = preg_split('/(\[quote comment=[0-9]+\])/', $comment_text, -1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
  
  if ( count($substrings) < 2 )
    return $comment_text;
  
  // For debugging:
  // echo "<!-- \n";
  // echo "$comment_text\n\n";
  // print_r($substrings);
  // echo "-->";
  
  $converted_text = '';
  $open_qs = array();
  
  foreach ($substrings as $substring) {
    preg_match('/\[quote comment=([0-9]+)\]/', $substring, $matches);
    
    if ( !empty($matches) ) {
      // Opening [quote comment=123]
      array_push($open_qs, $matches[0]);
      $quoted_comment = get_comment( $matches[1] );
      if ( is_object($quoted_comment) ) {
        $quoted_comment_link = get_comment_link( $quoted_comment->comment_ID );
        $converted_text .= '<p class="comment-quote-info"><a href="' . $quoted_comment_link . '"><small><em>' . get_comment_author( $quoted_comment->comment_ID ) . '</em> wrote:</small></a></p>';
        $converted_text .= '<blockquote cite="' . $quoted_comment_link . '">';
      }
      else {
        $converted_text .= '<blockquote>';
      }
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
          $converted_text .= '</blockquote>';
        }
        else {
          $converted_text .= $split;
        }
      }
    }
  }
  
  // Balance [quote]'s
  foreach ($open_qs as $open_q) {
    $converted_text .= '</blockquote>';
  }
  
  return $converted_text;
}
add_filter('comment_text', 'rk_convert_quotes', 1);