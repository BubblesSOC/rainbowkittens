<?php
/**
 * The template for displaying search forms in Rainbow Kittens
 *
 * @package WordPress
 * @subpackage Rainbow Kittens
 */
?>
<form method="get" id="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<input type="text" name="s" id="s" />
	<input type="submit" name="submit" id="searchsubmit" value="Search" />
</form>
