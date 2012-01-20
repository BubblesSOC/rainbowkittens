<?php
/**
 * @package WordPress
 * @subpackage Rainbow_Kittens
 */
?><!DOCTYPE html>
<html class="no-js" lang="en" xmlns:og="http://ogp.me/ns#">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="author" content="Bubs" />
<meta name="description" content="<?php bloginfo("description"); ?>" />
<?php rk_og_metadata(); ?>
<title><?php
	/*
	 * TO DO
	 * Print the <title> tag based on what is being viewed.
	 */
	global $page, $paged;

	wp_title( '|', true, 'right' );

	// Add the blog name.
	bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		echo " | $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf( __( 'Page %s', 'twentyeleven' ), max( $paged, $page ) );

	?></title>
<link rel="Shortcut Icon" href="http://www.bubblessoc.net/favicon.ico" />
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="profile" href="http://microformats.org/profile/hcard" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<link rel="stylesheet" type="text/css" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
<?php if ( is_single() ) : ?>
<link rel="stylesheet" type="text/css" href="<?php bloginfo( 'template_directory' ); ?>/stylesheets/jquery-ui-1.8.16.custom.css" />
<?php endif; ?>
<?php
  wp_enqueue_script("jquery");
  wp_enqueue_script("jquery-ui-core");
  wp_enqueue_script("jquery-ui-dialog");
  wp_head(); 
?>
<script type="text/javascript" src="<?php bloginfo( 'template_directory' ); ?>/js/modernizr-2.0.6.min.js"></script>
<script type="text/javascript" src="<?php bloginfo( 'template_directory' ); ?>/js/wordpress.js"></script>
<?php
  # Only include when there are comments or there is a comment form
  if ( is_single() && !post_password_required() && ( have_comments() || comments_open() ) ) : 
?>
<script type="text/javascript" src="<?php bloginfo( 'template_directory' ); ?>/js/wordpress.comments.js"></script>
<?php endif; ?>
</head>

<body <?php body_class(); ?>>

<!-- <header> -->
<div class="header" role="banner">
<?php if ( is_single() ) : ?>
	<a href="<?php bloginfo("url"); ?>" title="BubblesSOC">BubblesSOC</a>
<?php else: ?>
	<h1><a href="<?php bloginfo("url"); ?>" title="BubblesSOC">BubblesSOC</a></h1>
<?php endif; ?>

	<!-- <nav> -->
	<div class="nav" role="nav">
	</div>
	<!-- </nav> -->
</div>
<!-- </header> -->
