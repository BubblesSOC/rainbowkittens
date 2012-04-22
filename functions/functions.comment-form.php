<?php
/**
 * Custom Comment Form Functions
 *
 * @package WordPress
 * @subpackage Rainbow_Kittens
 */

// Ref: comment_form(): wp-includes/comment-template.php
// Ref: http://codex.wordpress.org/Function_Reference/comment_form
function rk_comment_form() {
	$commenter = wp_get_current_commenter();
	
	$args = array(
		'fields' => rk_comment_form_fields( $commenter ),
		'comment_field' => rk_comment_field(),
		'logged_in_as' => '',
		'comment_notes_before' => '',
		'comment_notes_after' => rk_comment_notes_after(),
		'title_reply' => '',
		'title_reply_to' => 'You are replying to %s',
		'cancel_reply_link' => 'Cancel Reply'
	);
	
  add_action( 'comment_form_before', 'rk_comment_form_before' );  // Add heading, Quicktags dialogs
	add_action( 'comment_form_top', 'rk_comment_form_top' );  // Add <fieldset>, <ol>
	add_action( 'comment_form_logged_in_after', 'rk_comment_form_logged_in_after', 10, 2 ); // Add fields even if user is logged in
	
	comment_form($args);
}


// Comment Form $args Callbacks
function rk_comment_form_fields( $commenter ) {
  return array(
		'author' =>	rk_comment_form_field( 'author', true, 'Name', esc_attr( $commenter['comment_author'] ) ),
		'email'  =>	rk_comment_form_field( 'email', true, 'Email', esc_attr( $commenter['comment_author_email'] ) ),
		'url'    =>	rk_comment_form_field( 'url', false, 'Website', esc_attr( $commenter['comment_author_url'] ) )
	);
}

function rk_comment_form_field( $field, $req, $label, $value ) {
  global $wpdb;
  
	$aria_req = '';
	if ($req) {
		$aria_req = 'aria-required="true"';
	}
		
	// Ref: /wp-comments-post.php
  $user = wp_get_current_user();
  $readonly = '';
  if ( $user->ID ) {
    $readonly = 'readonly ';
    switch ($field) {
      case 'author':
        $value = $wpdb->escape($user->display_name);
        break;
      case 'email':
        $value = $wpdb->escape($user->user_email);
        break;
      case 'url':
        $value = $wpdb->escape($user->user_url);
        break;
    }
  }
	
	$form_field = <<<EOD
<li class="comment-form-field" id="comment-form-$field">
	<label for="$field">$label</label>
	<input id="$field" name="$field" type="text" value="$value" size="30" $aria_req $readonly/>
	<span class="error" id="$field-error"></span>
</li>
EOD;
	return $form_field;
}

function rk_comment_field() {
	$comment_field = <<<EOD
<li class="comment-form-field" id="comment-form-comment">
	<label for="comment">Comment</label>
	<textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea>
	<span class="error" id="comment-error"></span>
</li>
EOD;
	return $comment_field;
}

function rk_comment_notes_after() {
	$notes_after = <<<EOD
</ol>
</fieldset>
<p id="comment-notes">Name, email, and comment required.  Email never displayed.  Upload a <a href="http://www.gravatar.com" title="Gravatar">Gravatar</a> to be displayed with your comment.  Comments containing links will be moderated for spam prevention.</p>
EOD;
	return $notes_after;
}


// Comment Form Action Callbacks
function rk_comment_form_before() {
	$before = <<<EOD
<hr />
<h2>Leave a Reply</h2>	
EOD;
	echo $before;
	
	// Add dialogs for QuickTags
  add_action('wp_footer', 'rk_quicktags_dialogs');
}

function rk_comment_form_top() {
	$before = <<<EOD
<fieldset>
<ol>
EOD;
  echo $before;
}

function rk_quicktags_dialogs() {
  $dialogs = <<<EOD
<div class="hidden" id="quicktag-link-dialog">
  <label for="quicktag-link-url">Enter the <acronym title="Uniform Resource Locator">URL</acronym>:</label>
  <input type="text" name="quicktag-link-url" id="quicktag-link-url" value="http://" />
  <p class="hidden" id="quicktag-link-error">Please enter a valid <acronym title="Uniform Resource Locator">URL</acronym>.</p>
</div>
<div class="hidden" id="quicktag-code-dialog">
  <label for="quicktag-link-url">Enter your code:</label>
  <textarea name="quicktag-code-box" id="quicktag-code-box" cols="25" rows="5"></textarea>
  <div><small>Code will be automatically escaped.</small></div>
</div>
EOD;
  echo $dialogs;
}

function rk_comment_form_logged_in_after($commenter, $user_identity) {
  // Action Hook: do_action( 'comment_form_logged_in_after', $commenter, $user_identity );
  do_action( 'comment_form_before_fields' );
  $fields = rk_comment_form_fields( $commenter );
  
  // Snippet from comment_form():
  foreach ( (array) $fields as $name => $field ) {
		echo apply_filters( "comment_form_field_{$name}", $field ) . "\n";
	}
	do_action( 'comment_form_after_fields' );
}