<?php
/**
 * Functions and definitions
 *
 * @package WordPress
 * @subpackage Rainbow_Kittens
 */

require('functions/functions.recent-comments.php');
require('functions/functions.comment.php');
require('functions/functions.comment-form.php');
require('functions/functions.quoter.php');
require('classes/OpenGraphProtocol.php');
 
add_theme_support( 'automatic-feed-links' );
add_theme_support( 'post-formats', array('aside', 'link', 'image', 'quote', 'video', 'audio', 'chat') );
add_theme_support( 'post-thumbnails' );


function rk_scripts() {
  wp_enqueue_script('jquery');
  wp_enqueue_script('rk_modernizer_js', get_template_directory_uri() . '/js/modernizr-2.0.6.min.js');
  wp_register_script('rk_wordpress_js',  get_template_directory_uri() . '/js/wordpress.js', array('jquery'), false, true);
  wp_enqueue_script('rk_wordpress_js');
  wp_localize_script( 'rk_wordpress_js', 'wpAjax', array('url' => admin_url('admin-ajax.php')) );
  
  // Only include when there are comments or there is a comment form
  //if ( is_single() && !post_password_required() && ( have_comments() || comments_open() ) ) {
    wp_register_script( 'rk_wpcomments_js', get_template_directory_uri() . '/js/wordpress.comments.js', array('rk_wordpress_js', 'jquery'), false, true );
    wp_enqueue_script( 'rk_wpcomments_js' );
    // /stylesheets/jquery-ui-1.8.16.custom.css, jquery-ui-core, jquery-ui-dialog
  //}
  
  // wp_enqueue_script( 'rk_mint_js', site_url() . '/mint/?js', array(), false, true );
}
add_action( 'wp_enqueue_scripts', 'rk_scripts' );


// Custom Smilies
// Note: "word" smilies array in OpenGraphProtocol->_convertWordSmilies()
// Ref: smilies_init() : wp-includes/functions.php
$wpsmiliestrans = array(
	':D'		      => 'biggrin.gif',
	':-D'		      => 'biggrin.gif',
	                           
	':)'		      => 'smile.gif',  
	'=)'		      => 'smile.gif',  
	':-)'		      => 'smile.gif',  
	                           
	':('		      => 'sad.gif',    
	':-('		      => 'sad.gif',    
	                           
	':o'		      => 'shocked.gif',
	':-o'		      => 'shocked.gif',
	':shocked:'	  => 'shocked.gif',
	
	':|'		      => 'puzzled.gif',
	':?:'		      => 'puzzled.gif',
	        
	':P'		      => 'tongue.gif',
	':-P'		      => 'tongue.gif',
	        
	';)'		      => 'wink.gif',
	';-)'		      => 'wink.gif',
	
	':angry:'	    => 'angry.gif',
	'>:O'		      => 'angry.gif',
	
	':frown:'	    => 'frown.gif',
	'&gt;:('	    => 'frown.gif',
	
	':confused:'	=> 'confused.gif',
	':\\'		      => 'confused.gif',
	
	':cry:'		    => 'cry.gif',
	':&#8217;('	  => 'cry.gif',
	':\'('		    => 'cry.gif',
	
	'x_x'		      => 'dead.gif',
	':dead:'	    => 'dead.gif',
	
	':nod:'		    => 'nod.gif',
	'XD'		      => 'nod.gif',
	
	':stressed:'	=> 'stressed.gif',
	'&gt;_&lt;'	  => 'stressed.gif',
	
	':devil:'	    => 'devil.gif',
	':dizzy:'	    => 'dizzy.gif',
	':fish:'	    => 'fish.gif',
	':flip:'	    => 'flip.gif',
	':getsad:'	  => 'getsad.gif',
	':look:'	    => 'look.gif',
	':love:'	    => 'love.gif',
	':no:'		    => 'no.gif',
	':rain:'	    => 'rain.gif',
	':roll:'	    => 'roll.gif',
	':sleep:'	    => 'sleepy.gif',
	':speak:'	    => 'speak.gif',
	':spin:'	    => 'spin.gif',
	':study:'	    => 'study.gif',
	':tan:'		    => 'suntan.gif',
	':sweatdrop:'	=> 'sweatdrop.gif',
	':worried:'	  => 'worried.gif',
	':beat:'	    => 'heartbeat.gif',
	'O_o'		      => 'mixedup.gif',
	':9'		      => 'mmm.gif',
	':boil:'	    => 'boiling.gif',
	':heart:'	    => 'heart.gif',
	
	':lol:'		    => 'z-lol.gif',
	':grr:'		    => 'z-grr.gif',
	':duh:'		    => 'z-duh.gif',
	':rofl:'	    => 'z-rofl.gif',
	':woot:'	    => 'z-woot.gif',
	':hehe:'	    => 'z-hehe.gif',
);

// Filter Hook: $srcurl = apply_filters('smilies_src', includes_url("images/smilies/$img"), $img, site_url());
// Ref: translate_smiley() : wp-includes/formatting.php
function my_smilies_src_filter($includes_url, $img, $site_url) {
	return get_template_directory_uri() . "/images/smilies/" . $img;
}
add_filter('smilies_src', 'my_smilies_src_filter', 10, 3);


// Convert <3 to &hearts;
function rk_convert_heart($content) {
	return preg_replace('/&lt;3/', '&hearts;', $content);
}
add_filter('the_content', 'rk_convert_heart');
add_filter('comment_text', 'rk_convert_heart');


// Blogspot-Style Archives
// Ref: wp_get_archives() : wp-includes/general-template.php
function rk_get_archives() {
	
	// Remove whitespace before each link
	// Filter Hook: $link_html = apply_filters( 'get_archives_link', $link_html );
	// Ref: get_archives_link() : wp-includes/general-template.php
	add_filter('get_archives_link', 'ltrim');
	
  $args = array(
    'type' => 'yearly',
    'format' => 'custom',
    'echo' => 0
  );
  $years = explode( "\n", rtrim(wp_get_archives($args)) );
  
  $args['type'] = 'monthly';
  $months = explode("\n", rtrim(wp_get_archives($args)) );
  
  foreach ($years as $year) {
    $y = preg_replace('/<a(.*?)>([0-9]{4})<\/a>/i', "$2", $year);
    echo '<li class="year'. ($y == date('Y') ? ' active' : '') .'">';
    echo "\n\t" . $year . "\n\t";
    echo '<ol class="months">' . "\n";
    array_walk($months, "rk_get_archives_callback", $y);
    echo "\t</ol>\n</li>\n";
  }
  
  remove_filter('get_archives_link', 'ltrim');
}

function rk_get_archives_callback($item, $index, $year) {
  $y = preg_replace('/<a(.*?)>([a-z]+) ([0-9]{4})<\/a>/i', "$3", $item);
  if ($y == $year)
    echo "\t\t" . '<li>'. $item .'</li>' . "\n";
}


// Post archives page navigation
function rk_post_nav() {
	$previous = get_previous_posts_link( "&laquo; Newer Posts" );
	$next = get_next_posts_link( "Older Posts &raquo;" );
	
	echo "<ol>\n";
	if ($previous != null)
		echo "\t<li>$previous</li>\n";
	if ($next != null)
		echo "\t<li>$next</li>\n";
	echo "</ol>\n";
}


// Post Password Form
// Filter Hook: return apply_filters('the_password_form', $output);
// Ref: get_the_password_form() : wp-includes/post-template.php
function rk_the_password_form( $output ) {
	return $output;
}
add_filter( 'the_password_form', 'rk_the_password_form' );


// Protected Title Format
// Filter Hook: $protected_title_format = apply_filters('protected_title_format', __('Protected: %s'));
// Ref: get_the_title() : wp-includes/post-template.php
function rk_protected_title_format( $format ) {
	return '%s';
}
add_filter( 'protected_title_format', 'rk_protected_title_format' );
