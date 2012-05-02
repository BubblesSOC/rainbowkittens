<?php
/**
 * Open Graph Protocol
 *
 * @package WordPress
 * @subpackage Rainbow_Kittens
 * @link http://ogp.me OGP Documentation
 */

class Open_Graph_Protocol {
  private $_defaultImage;
  private $_title;
  private $_type;
  private $_url;
  private $_description;
  private $_thumbnail;
  private $_images;
  
  function __construct() {
    // Uploaded to theme's images?
    $this->_defaultImage = "http://bubblessoc.net/images/avatars/octopusAv.gif";
    
    // Initialize the meta data when the conditional tags become available
    add_action('wp', array($this, 'initMetadata'));
  }
  
  function initMetadata() {
    // Also needs to work for pages
    if ( is_single() ) {
      $this->_title = get_the_title();
      $this->_type = 'article';
      $this->_url = get_permalink();
      $this->_description = $this->_getExcerpt();
      $this->_thumbnail = $this->_getPostThumbnail();
      $this->_images = $this->_getPostImages();
    }
    else {
      $this->_title = get_bloginfo('name');
      $this->_type = 'website';
      $this->_url = site_url();
      $this->_description = get_bloginfo('description');
      $this->_thumbnail = null;
      $this->_images = null;
    }
  }
  
  private function _getPostThumbnail() {
    global $post;
    
    // Ref: http://codex.wordpress.org/Post_Thumbnails
    if ( has_post_thumbnail($post->ID) ) {
      $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full' );
      return $thumb[0];
    }
    
    return null;
  }
  
  private function _getPostImages() {
    global $post;
    
    if ( empty($post->post_password) ) {
      $pattern = "/<img.*?(?=src=(?:\"|')([^\s]+?\.(?:jpg|jpeg|gif|png))(?:\"|'))[^>]*>/i";
    	if ( preg_match_all($pattern, $post->post_content, $matches) > 0 ) {
    	  $images = array();
  	  
    	  foreach ($matches[1] as $img_src) {
    	    // Handle case: /images/blog/blah.jpg
    	    $img_src = preg_replace('/(^\/[^\s]+)/', "http://bubblessoc.net$1", $img_src);
  	    
    	    // Check image size
          if ( $this->_checkImageSize($img_src) )
    	      array_push($images, $img_src);
    	  }
  	  
      	if ( !empty($images) )
      	  return $images;
    	}
    }
  	
  	return null;
  }
  
  private function _checkImageSize( $img_src ) {
    $tmp = get_template_directory() . '/images/tmp/' . time();
    $ch = curl_init($img_src);
    $fp = fopen($tmp, 'wb');
    $options = array(
     CURLOPT_FILE => $fp,
     CURLOPT_HEADER => false,
     CURLOPT_CONNECTTIMEOUT => 1
    );
    curl_setopt_array($ch, $options);
    
    $is_valid_img = false;
    if ( curl_exec($ch) ) {
      $size = getimagesize($tmp);
      if ($size[0] >= 50 && $size[1] >= 50)
        $is_valid_img = true;
    }

    curl_close($ch);
    fclose($fp);
    unlink($tmp);
    return $is_valid_img;
  }
  
  private function _getExcerpt() {
    // Needs work!
    // Ref: get_the_content() : wp-includes/post-template.php
    global $post;
    
    if ( !empty($post->post_password) )
      return "This post is password protected.";
    
    // Strip the <!--more-->
    $content = preg_replace('/<!--more(.*?)?-->/', '', $post->post_content);
 
    // Ref: wp_trim_excerpt() : wp-includes/formatting.php
    $content = strip_shortcodes($content);
    $content = $this->_convertWordSmilies($content);
    
    // Excerpt RSS Filters
  	// Ref: wp-includes/default-filters.php
    $content = convert_chars($content);
    $content = ent2ncr($content);
    
    $content = str_replace(']]>', ']]&gt;', $content);
    $content = wp_trim_words( $content, 55, '...' );
    return $content;
  }
  
  private function _convertWordSmilies( $text ) {
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
  	return $text;
  }
  
  private function _getImageMeta() {
    if ( !is_null($this->_thumbnail) ) {
      return '<meta property="og:image" content="'. $this->_thumbnail .'" />';
    }
    elseif ( is_array($this->_images) ) {
      $img_meta = '';
      foreach ( $this->_images as $image ) {
        $img_meta .= '<meta property="og:image" content="'. $image .'" />' . "\n";
      }
      return trim($img_meta);
    }
    else {
      return '<meta property="og:image" content="'. $this->_defaultImage .'" />';
    }
  }
  
  function getNamespace() {
    switch ( $this->_type ) {
      case 'article':
        return 'http://ogp.me/ns/article#';
        break;
      case 'website':
        return 'http://ogp.me/ns/website#';
        break;
      default:
        return 'http://ogp.me/ns#';
        break;
    }
  }

  function getMetadata() {
    $site_name = get_bloginfo('name');
    $img_meta = $this->_getImageMeta();
    $metadata = <<<EOD
<meta property="og:site_name" content="{$site_name}" />
<meta property="og:title" content="{$this->_title}" />
<meta property="og:description" content="{$this->_description}" />
<meta property="og:type" content="{$this->_type}" />
<meta property="og:url" content="{$this->_url}" />
$img_meta
EOD;
    return apply_filters( 'bsp_facebook_metatag', $metadata . "\n" );
  }
  
  function getAttrArray() {
    return array(
      'default_image' => $this->_defaultImage,
      'title' => $this->_title,
      'type' => $this->_type,
      'url' => $this->_url,
      'description' => $this->_description,
      'thumbnail' => $this->_thumbnail,
      'images' => $this->_images
    );
  }
}


function ogp_namespace() {
  global $ogp;
  echo $ogp->getNamespace();
}

function ogp_metadata() {
  global $ogp;
  echo $ogp->getMetadata();
}

function ogp_get_attributes() {
  global $ogp;
  return $ogp->getAttrArray();
}

function ogp_description() {
  $attrs = ogp_get_attributes();
  echo $attrs['description'];
}

$ogp = new Open_Graph_Protocol();
?>