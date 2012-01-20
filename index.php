<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Rainbow_Kittens
 */

get_header();
if ( have_posts() ) : ?>

<!-- <section> -->
<div class="section hfeed" role="main">

<?php	while ( have_posts() ) : the_post(); ?>

	<!-- <article> -->
	<div id="post-<?php the_ID(); ?>" <?php post_class("article"); ?> role="article">
		<!-- <header> -->
			<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="Permalink to <?php the_title_attribute(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
			<p>Posted <abbr class="published" title="<?php the_time("c"); ?>"><?php rk_time_since( get_the_time('U') ); ?></abbr></p>
		<!-- </header> -->
		
		<div class="entry-content">
			<?php the_content('Read More'); ?>
		</div>
		
		<!-- <footer> -->
			<ul class="post-info">
				<li class="vcard author"><a class="url fn nickname" href="http://bubblessoc.net">Bubs</a></li>
<?php 		if ( !post_password_required() ) : ?>
				<li><?php comments_popup_link('0 Comments', '1 Comment', '% Comments'); ?></li>
<?php 		endif; ?>
				<li>Categories: <?php the_category(', '); ?></li>
				<li><?php the_tags(); ?></li>
			</ul>
		<!-- </footer> -->
	</div>
	<!-- </article> -->
	
<?php 	endwhile; ?>
	<hr />
	
	<!-- <nav> -->
	<div class="nav" role="navigation">
		<?php rk_post_nav(); ?>
	</div>
	<!-- </nav> -->
	
<?php else : ?>

<!-- <section> -->
<div class="section" role="main">
	<!-- <header> -->
		<h2>Nothing Found</h2>
	<!-- </header> -->
	<p>Apologies, but no results were found for the requested archive. Perhaps searching will help find a related post.</p>

<?php endif; ?>
<?php get_sidebar(); ?>
	
</div>
<!-- </section> -->
	
<?php get_footer(); ?>