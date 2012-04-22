<?php
/**
 * @package WordPress
 * @subpackage Rainbow_Kittens
 */
?>
	<hr />
	
	<!-- <aside> -->
	<div class="aside" id="archives" role="complementary">
		<h2>Archives</h2>
		<ol>
			<?php rk_get_archives(); ?>
		</ol>
	</div>
	<!-- </aside> -->

	<!-- <aside> -->
	<div class="aside" id="categories" role="complementary">
		<h2>Categories</h2>
		<ul>
			<?php wp_list_categories('title_li='); ?>
		</ul>
	</div>
	<!-- </aside> -->
	
	<!-- <aside> -->
	<div class="aside" id="search" role="search">
		<?php get_search_form(); ?>
	</div>
	<!-- </aside> -->

	<!-- <aside> -->
	<div class="aside" id="recent-comments" role="complementary">
		<h2>Recent Comments</h2>
		<ul>
			<?php rk_recent_comments(); ?>
		</ul>
	</div>
	<!-- </aside> -->

	<!-- <aside> -->
	<div class="aside" id="raptr" role="complementary">
		<h2>Raptr</h2>
		<ul>
			<li>Loading...</li>
		</ul>
	</div>
	<!-- </aside> -->

	<!-- <aside> -->
	<div class="aside" id="twitter" role="complementary">
		<h2>Twitter</h2>
		<ul>
			<li>Loading...</li>
		</ul>
	</div>
	<!-- </aside> -->

	<!-- <aside> -->
	<div class="aside" id="lastfm" role="complementary">
		<h2>Last.fm</h2>
		<ul>
			<li>Loading...</li>
		</ul>
	</div>
	<!-- </aside> -->

	<!-- <aside> -->
	<div class="aside" id="flickr" role="complementary">
		<h2>Flickr</h2>
		<ul>
			<li>Loading...</li>
		</ul>
	</div>
	<!-- </aside> -->
	
	<!-- <aside> -->
	<div class="aside" id="github" role="complementary">
		<h2>Github</h2>
		<ul>
			<li>Loading...</li>
		</ul>
	</div>
	<!-- </aside> -->
	