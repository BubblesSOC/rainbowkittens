<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage Rainbow_Kittens
 */

get_header(); ?>

<!-- <section> -->
<div class="section hfeed" role="main">

<?php while ( have_posts() ) : the_post(); ?>

	<!-- <article> -->
	<div id="post-<?php the_ID(); ?>" <?php post_class("article"); ?> role="article">
		<!-- <header> -->
			<h1 class="entry-title"><a href="<?php the_permalink(); ?>" title="Permalink to <?php the_title_attribute(); ?>" rel="bookmark" class="entry-permalink"><?php the_title(); ?></a></h1>
			<p>Posted <abbr class="published" title="<?php the_time("c"); ?>"><?php time_since( get_the_time('U') ); ?></abbr></p>
		<!-- </header> -->
		
		<div class="entry-content">
			<?php the_content('Read More'); ?>
		</div>
		
		<!-- <footer> -->
			<ul class="post-info">
				<li class="vcard author"><a class="url fn nickname" href="http://bubblessoc.net">Bubs</a></li>
				<li>Categories: <?php the_category(', '); ?></li>
				<li><?php the_tags(); ?></li>
				<li>Share:
          <?php do_action('bsp_share_buttons', get_permalink(), wp_get_shortlink()); ?>  
  		  </li>
			</ul>
		<!-- </footer> -->
		
		<?php comments_template(); /* Includes article </div> */ ?>
	
	<!-- <nav> -->
	<!--<div class="nav" role="navigation">
		<?php previous_post_link(); ?>
		<?php next_post_link(); ?>
	</div>-->
	<!-- </nav> -->

<?php endwhile; ?>

<?php get_sidebar(); ?>
	
</div>
<!-- </section> -->
	
<?php get_footer(); ?>
