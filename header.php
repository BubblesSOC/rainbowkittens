<?php
/**
 * @package WordPress
 * @subpackage Rainbow_Kittens
 */
?><!DOCTYPE html>
<html class="no-js" lang="en" prefix="og: http://ogp.me/ns#">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="author" content="Sidney Collins" />
<?php rk_metadata(); ?>
<?php ogp_metadata(); ?>
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
<link rel="Shortcut Icon" href="http://bubblessoc.net/favicon.ico" />
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="profile" href="http://microformats.org/profile/hcard" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<link rel="stylesheet" type="text/css" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="fb-root"></div>

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
