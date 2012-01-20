<?php
/**
 * Functions and definitions
 *
 * Sets up the theme and provides some helper functions. Some helper functions
 * are used in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * The first function, twentyeleven_setup(), sets up the theme by registering support
 * for various features in WordPress, such as post thumbnails, navigation menus, and the like.
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are instead attached
 * to a filter or action hook. The hook can be removed by using remove_action() or
 * remove_filter() and you can attach your own function to the hook.
 *
 * We can remove the parent theme's hook only after it is attached, which means we need to
 * wait until setting up the child theme:
 *
 * <code>
 * add_action( 'after_setup_theme', 'my_child_theme_setup' );
 * function my_child_theme_setup() {
 *     // We are providing our own filter for excerpt_length (or using the unfiltered value)
 *     remove_filter( 'excerpt_length', 'twentyeleven_excerpt_length' );
 *     ...
 * }
 * </code>
 *
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 *
 * @package WordPress
 * @subpackage Rainbow_Kittens
 */
 
add_theme_support( 'automatic-feed-links' );
add_theme_support( 'post-formats' );

// Custom Smilies
// Note: "word" smilies array in rk_og_description()
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

add_filter('smilies_src', 'my_smilies_src_filter',10,3);


// Convert <3 to &hearts;
function rk_convert_heart($content) {
	$content = preg_replace('/&lt;3/', '&hearts;', $content);
	return $content;
}

add_filter('the_content', 'rk_convert_heart');
add_filter('comment_text', 'rk_convert_heart');


function rk_get_archives_callback($item, $index, $currYear) {
	global $wp_locale;
	
	if ( $item['year'] == $currYear ) {
		$url = get_month_link( $item['year'], $item['month'] );
		// translators: 1: month name, 2: 4-digit year
		$text = sprintf(__('%1$s %2$d'), $wp_locale->get_month($item['month']), $item['year']);
		echo get_archives_link($url, $text);
	}
}

function rk_get_archives() {
	// Ref: wp_get_archives() : wp-includes/general-template.php
	global $wpdb;
	
	$query = "SELECT YEAR(post_date) AS `year` FROM $wpdb->posts WHERE `post_type` = 'post' AND `post_status` = 'publish' GROUP BY `year` ORDER BY `year` DESC";
	$arcresults = $wpdb->get_results($query);
	$years = array();
	
	if ($arcresults) {
		foreach ( (array)$arcresults as $arcresult ) {
			array_push($years, $arcresult->year);
		}
	}
	
	$query = "SELECT YEAR(post_date) as `year`, MONTH(post_date) as `month` FROM $wpdb->posts WHERE `post_type` = 'post' AND `post_status` = 'publish' GROUP BY `year`, `month` ORDER BY `year` DESC, `month` ASC";
	$arcresults = $wpdb->get_results($query, ARRAY_A);
	$months = array();
	
	if ( $arcresults ) {
		foreach ($years as $year) {
			echo "\t<li class=\"year". ($year == date("Y") ? " active" : "") ."\">\n";
			echo "\t\t<a href=\"". get_year_link($year) ."\" title=\"$year\">$year</a>\n";
			echo "\t\t<ol class=\"months\">\n";
			array_walk($arcresults, "rk_get_archives_callback", $year);
			echo "\t\t</ol>\n\t</li>\n";
		}
	}
}

function rk_recent_comments() {
	$comments = get_comments('status=approve&number=3');
	
	foreach ($comments as $comment) {
		echo "\t<li>" . get_avatar( $comment, 32 ) . "<br>";
		comment_author_link( $comment->comment_ID );
		
		// Check to see if this comment is a reply
		$parent = get_comment( $comment->comment_parent );
		if ( is_object($parent) ) {
			echo " replied to <a href=\"" . get_permalink( $parent->comment_post_ID ) . "#comment-". $parent->comment_ID ."\">" . get_comment_author( $parent->comment_ID ) . "</a>:<br>";
		}
		else {
			echo " said:<br>";
		}
		
		$comment_text = strip_tags( get_comment_text( $comment->comment_ID ) );
		
		// Remove Quoted Text
		$comment_text = preg_replace('/\[quote comment="[0-9]+"\]([^\[]|\[(?!\/quote\]))*\[\/quote\]/', '', $comment_text);
		
		// Ref: get_comment_excerpt() : wp-includes/comment-template.php
		$blah = explode(' ', $comment_text);
		if (count($blah) > 20) {
			$k = 20;
			$use_dotdotdot = 1;
		} else {
			$k = count($blah);
			$use_dotdotdot = 0;
		}
		$excerpt = '';
		for ($i=0; $i<$k; $i++) {
			$excerpt .= $blah[$i] . ' ';
		}
		//$excerpt .= ($use_dotdotdot) ? '...' : '';
		
		// Comment Filters
		// Ref: wp-includes/default-filters.php
		add_filter( 'my_recent_comment_excerpt', 'wptexturize' );
		add_filter( 'my_recent_comment_excerpt', 'convert_chars' );
		add_filter( 'my_recent_comment_excerpt', 'convert_smilies' );
		add_filter( 'my_recent_comment_excerpt', 'rk_convert_heart' );
		
		echo apply_filters('my_recent_comment_excerpt', $excerpt);
		echo '<a href="'. get_permalink( $comment->comment_post_ID ) .'#comment-'. $comment->comment_ID . '">&rarr;</a>';
		echo "</li>\n";
	}
}

// Plugin Name: Dunstan's Time Since
// Plugin URI: http://binarybonsai.com/wordpress/timesince
// Description: Tells the time between the entry being posted and the comment being made.
function rk_time_since($older_date, $newer_date = false) {
	
	// array of time period chunks
	$chunks = array(
	array(60 * 60 * 24 * 365 , 'year'),
	array(60 * 60 * 24 * 30 , 'month'),
	array(60 * 60 * 24 * 7, 'week'),
	array(60 * 60 * 24 , 'day'),
	array(60 * 60 , 'hour'),
	array(60 , 'minute'),
	);
	
	// $newer_date will equal false if we want to know the time elapsed between a date and the current time
	// $newer_date will have a value if we want to work out time elapsed between two known dates
	$newer_date = ($newer_date == false) ? (time()+(60*60*get_option("gmt_offset"))) : $newer_date;
	
	// difference in seconds
	$since = $newer_date - $older_date;
	
	// we only want to output two chunks of time here, eg:
	// x years, xx months
	// x days, xx hours
	// so there's only two bits of calculation below:

	// step one: the first chunk
	for ($i = 0, $j = count($chunks); $i < $j; $i++)
		{
		$seconds = $chunks[$i][0];
		$name = $chunks[$i][1];

		// finding the biggest chunk (if the chunk fits, break)
		if (($count = floor($since / $seconds)) != 0)
			{
			break;
			}
		}

	// set output var
	$output = ($count == 1) ? '1 '.$name : "$count {$name}s";

	// step two: the second chunk
	if ($i + 1 < $j)
		{
		$seconds2 = $chunks[$i + 1][0];
		$name2 = $chunks[$i + 1][1];
		
		if (($count2 = floor(($since - ($seconds * $count)) / $seconds2)) != 0)
			{
			// add to output var
			$output .= ($count2 == 1) ? ', 1 '.$name2 : ", $count2 {$name2}s";
			}
		}
	
	echo $output . " ago";
}

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

// OPEN GRAPH PROTOCOL FUNCTIONS
// Setup OGP image cache
// add_option() does nothing if option already exists
add_option('og_image_cache', array());
$og_image_cache = get_option('og_image_cache');

function rk_og_update_cache( $data_arr ) {
	global $og_image_cache;
	
	array_push( $og_image_cache, $data_arr );
	update_option('og_image_cache', $og_image_cache);
}

function rk_og_image_cached($post_id) {
	// Search cache for the $post_id
	// If $post_id is cached => return image url
	// Otherwise, return FALSE
	global $og_image_cache;
	
	if ( $post_id == 0 || empty($og_image_cache) )
		return false;
	
	foreach ($og_image_cache as $image) {
		if ( $post_id == $image['post_id'] )
			// Should validate $image['url'] here, but I'm not gonna
			return $image['url'];
	}
	
	return false;
}

function rk_og_image_valid($img_url) {
	// If $img_url is a valid image => return TRUE
	// Otherwise, return FALSE
	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_URL, $img_url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
	curl_setopt($ch, CURLOPT_FAILONERROR, true);
	
	$data = curl_exec($ch);
	curl_close($ch);
	
	if ($data === false)
		return false;
	
	if ( @imagecreatefromstring($data) === false )
		return false;
	
	return true;
}

function rk_og_post_image($post_id) {
	// Retrieve all image urls from $post_id
	
	// Don't need to bother db if $post_id == 0
	if ($post_id == 0)
		return false;
	
	$post = get_post($post_id);
	
	// $post_id not found => return FALSE
	if ( is_null($post) )
		return false;
	
	// When the first valid image is found => return the url
	// If no valid images are found => return FALSE
	$pattern = "/<img.*?(?=src=(?:\"|')(http:\/\/[^\s]+?\.(?:jpg|jpeg|gif|png))(?:\"|'))[^>]*>/i";
	$match_count = preg_match_all($pattern, $post->post_content, $matches);
	
	foreach ($matches[1] as $img_url) {
		if ( rk_og_image_valid($img_url) )
			return $img_url;
	}
	
	return false;
}

function rk_og_image_fetch($post_id) {
	// CHECK THE CACHE
	$cached_img_url = rk_og_image_cached($post_id);
	
	// Image cached => display and exit
	if ( $cached_img_url !== false ) {
		return $cached_img_url . "#cached";
	}
	
	// NO IMAGE CACHED
	// Search $post_id for the first valid image
	// Valid image found => display, cache, and exit
	$img_url = rk_og_post_image($post_id);
	
	if ( $img_url !== false ) {
		// Cache it
		rk_og_update_cache( array("post_id" => $post_id, "url" => $img_url) );
		return $img_url;
	}
	
	return false;
}

function rk_og_image($post_id = 0) {
	// Get an image url for OGP
	$img_url = rk_og_image_fetch($post_id);
	
	if ( $img_url !== false ) {
		echo $img_url;
		return;
	}
	
	// NO VALID IMAGE FOUND
	// Either a post wasn't specified or the post didn't contain an image
	// Starting with the latest post published, find the first valid image
	$posts = get_posts("numberposts=10");
	
	foreach ($posts as $post) {
		$img_url = rk_og_image_fetch($post->ID);
		
		if ($img_url !== false) {
			echo $img_url;
			return;
		}
	}
	
	// Looks like no valid images were found...
	// Use a default image?
	echo "DEFAULT IMAGE";
}

function rk_og_metadata() {
	if ( is_single() ) :
?>
<meta property="og:title" content="<?php the_title(); ?>" />
<meta property="og:type" content="article" />
<meta property="og:url" content="<?php the_permalink(); ?>" />
<meta property="og:image" content="<?php rk_og_image( get_the_ID() ); ?>" />
<meta property="og:description" content="<?php rk_og_description(); ?>" />
<meta property="og:site_name" content="BubblesSOC" />
<?php	else : ?>
<meta property="og:title" content="BubblesSOC" />
<meta property="og:type" content="website" />
<meta property="og:url" content="http://bubblessoc.net" />
<meta property="og:image" content="<?php rk_og_image(); ?>" />
<meta property="og:description" content="<?php bloginfo("description"); ?>" />
<?php	endif;
}

function rk_og_description() {
	// Ref: wp_trim_excerpt() : wp-includes/formatting.php
	global $post;
	
	$text = strip_tags( $post->post_content );
	$excerpt_length = 55;
	$excerpt_more = "...";
	
	$words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
	if ( count($words) > $excerpt_length ) {
		array_pop($words);
		$text = implode(' ', $words);
		$text = $text . $excerpt_more;
	} else {
		$text = implode(' ', $words);
	}
	
	// Convert "word" smilies
	$smilies = array(
		':shocked:'	  => ':o',
		':?:'		      => ':|',
		':angry:'	    => '',
		':frown:'	    => ':(',
		':confused:'	=> ':\\',
		':cry:'		    => ':\'(',
		':dead:'	    => 'x_x',
		':nod:'		    => 'XD',
		':stressed:'	=> '',
		':devil:'	    => '',
		':dizzy:'	    => '@_@',
		':fish:'	    => '',
		':flip:'	    => '',
		':getsad:'	  => ':(',
		':look:'	    => ':)',
		':love:'	    => '&hearts;',
		':no:'		    => '-_-',
		':rain:'	    => 'D:',
		':roll:'	    => ':D',
		':sleep:'	    => '',
		':speak:'	    => '',
		':spin:'	    => ':D',
		':study:'	    => '',
		':tan:'		    => '',
		':sweatdrop:'	=> '^^;',
		':worried:'	  => '',
		':beat:'	    => '&hearts;',
		':boil:'	    => '',
		':heart:'	    => '&hearts;',
		':lol:'		    => 'LOL',
		':grr:'		    => 'GRR',
		':duh:'		    => '',
		':rofl:'	    => 'ROFL',
		':woot:'	    => 'w00t',
		':hehe:'	    => 'hehe',
		
		// Added
		'&lt;3'		    => '&hearts;',
	);
	
	foreach ($smilies as $search => $replace) {
		$text = str_replace($search, $replace, $text);
	}
	
	// Excerpt RSS Filters
	// Ref: wp-includes/default-filters.php
  add_filter( 'my_post_content_excerpt', 'convert_chars' );
  echo apply_filters('my_recent_comment_excerpt', $text);
}

function rk_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	
	// *Cuteness theme comment functions need to be implemented: notify_parent(), quoter plugin, edit link
?>

				<!-- <article> -->
				<li <?php comment_class("article"); ?> id="comment-<?php comment_ID(); ?>">
					<!-- <header> -->
						<p><a href="<?php comment_link(); ?>" id="comment-permalink-<?php comment_ID(); ?>" class="comment-permalink">Posted <abbr class="published" title="<?php comment_time("c"); ?>"><?php rk_time_since( get_comment_time('U') ); ?></abbr></a></p>
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

function rk_parent_comment_link() {
	$parentId = $GLOBALS['comment']->comment_parent;
	
	if (0 == $parentId)
		return;
	
	$parent = get_comment( $parentId );
	echo 'replied to <a href="#comment-' . $parent->comment_ID . '">' . get_comment_author( $parent->comment_ID ) . '</a>';
}

function rk_comment_quote_link() {
  $post = $GLOBALS['post'];
  
  if ( !comments_open($post->ID) )
		return false;
		
	$comment_text = preg_replace('/\[quote comment="([0-9]+)"\]/i', '[quote comment=$1]', get_comment_text());
		
	echo '<li><a href="#" class="comment-quote-link" id="comment-quote-link-' . get_comment_ID() . '" data-comment="' . esc_js( str_replace('\\', '\\\\', $comment_text) ) . '">Quote</a></li>';
	echo "\n";
}

function rk_comment_form() {
	// Ref: comment_form(): wp-includes/comment-template.php
	// Ref: http://codex.wordpress.org/Function_Reference/comment_form
	$commenter = wp_get_current_commenter();
	
	$fields = array(
		'author' =>	rk_comment_form_field( 'author', true, 'Name', esc_attr( $commenter['comment_author'] ) ),
		'email'  =>	rk_comment_form_field( 'email', true, 'Email', esc_attr( $commenter['comment_author_email'] ) ),
		'url'    =>	rk_comment_form_field( 'url', false, 'Website', esc_attr( $commenter['comment_author_url'] ) )
	);
	
	$args = array(
		'fields'		=> $fields,
		'comment_field'		=> rk_comment_form_comment_field(),
		'comment_notes_before'	=> '',
		'comment_notes_after'	=> rk_comment_form_notes_after(),
		'title_reply' => '',
		'title_reply_to' => 'You are replying to %s',
		'cancel_reply_link' => 'Cancel Reply'
	);
	
	// Annonymous functions not available locally
	add_action( 'comment_form_before', 'rk_comment_form_before' );
	add_action( 'comment_form_before_fields', 'rk_comment_form_before_fields' );
	add_action( 'comment_form_logged_in_after', 'rk_comment_form_before_fields' );
	
	comment_form($args);
}

function rk_comment_form_field( $field, $req, $label, $value ) {
	$aria_req = '';
	
	if ($req)
		$aria_req = 'aria-required="true" ';
	
	$form_field = <<<EOD
<li class="comment-form-$field">
	<label for="$field">$label</label>
	<input id="$field" name="$field" type="text" value="$value" size="30" $aria_req/>
</li>
EOD;
	return $form_field;
}

function rk_comment_form_comment_field() {
	$comment_field = <<<EOD
<li class="comment-form-comment">
	<label for="comment">Comment</label>
	<textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea>
</li>
EOD;
	return $comment_field;
}

function rk_comment_form_notes_after() {
	$notes_after = <<<EOD
</ol>
</fieldset>
<p class="comment-notes">Name, email, and comment required.  Email never displayed.  Upload a <a href="http://www.gravatar.com" title="Gravatar">Gravatar</a> to be displayed with your comment.  Comments containing links will be moderated for spam prevention.</p>
EOD;
	return $notes_after;
}

// Annonymous function replacements
function rk_comment_form_before() {
	$before = <<<EOD
<hr />
<h2>Leave a Reply</h2>	
EOD;
	echo $before;
	
	// Add dialogs for QuickTags
  add_action('wp_footer', 'rk_quicktags_dialogs');
}

function rk_comment_form_before_fields() {
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
