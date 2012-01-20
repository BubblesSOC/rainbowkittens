<?php
/**
 * The template for displaying Comments.
 *
 * The area of the page that contains both current comments
 * and the comment form. The actual display of comments is
 * handled by a callback to rk_comment() which is
 * located in the functions.php file.
 *
 * @package WordPress
 * @subpackage Rainbow_Kittens
 */
if (  post_password_required() ) : ?>
		
		<hr />
		
		<!-- <section> -->
		<div class="section" id="comments">
			<!-- <header> -->
				<h2>Password Required</h2>
			<!-- </header> -->
			<p>This post is password protected. Enter the password to view any comments.</p>
		</div>
		<!-- </section> -->
	
	</div>
	<!-- </article> -->

<?php
		/* Stop the rest of comments.php from being processed,
		 * but don't kill the script entirely -- we still have
		 * to fully load the template.
		 */
		return;
endif;

if ( have_comments() ) : ?>
		
		<hr />
		
		<!-- <section> -->
		<div class="section" id="comments">
			<!-- <header> -->
				<h2>Comments</h2>
<?php 	if ( comments_open() ) : ?>
				<p><?php comments_number('No one has', 'Only one person has', '% people have' );?> commented on the post &#8220;<?php the_title(); ?>.&#8221; Why don't you <a href="#respond">leave a comment</a>?</p>
<?php	else : ?>
				<p><?php comments_number('No one', 'Only one person', '% people' );?> commented on the post &#8220;<?php the_title(); ?>.&#8221;</p>
<?php	endif; ?>
			<!-- </header> -->

			<ol>
				<?php wp_list_comments( array( 'callback' => 'rk_comment', 'type' => 'comment' ) ); ?>
			</ol>
		</div>
		<!-- </section> -->
	
<?php endif; ?>
		
	</div>
	<!-- </article> -->
	
	<?php rk_comment_form(); ?>
