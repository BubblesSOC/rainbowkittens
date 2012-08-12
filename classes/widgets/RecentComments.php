<?php
/**
 * Recent Comments Widget
 *
 * @package WordPress
 * @subpackage Rainbow_Kittens
 */
 
class RK_Recent_Comments_Widget extends WP_Widget {
  
  /**
   * Register widget with WordPress.
   */
  function __construct() {
    parent::__construct(
      'rk-recent-comments',
      "Bubs' Recent Comments"
    );
  }
  
  /**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
  function form( $instance ) {
    $title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : 'Recent Comments';
		$number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 3;
?>
<p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label> 
	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
</p>
<p>
	<label for="<?php echo $this->get_field_id( 'number' ); ?>">Number of comments to show:</label> 
	<input size="3" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" />
</p>
<?php
  }
  
  /**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
  function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['number'] = absint( $new_instance['number'] );
    return $instance;
  }
  
  /**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
  function widget( $args, $instance ) {
    extract( $args );
    $title = apply_filters( 'widget_title', empty($instance['title']) ? 'Recent Comments' : $instance['title'] );
    if ( empty($instance['number']) || !$number = absint($instance['number']) )
 			$number = 3;
 			
    echo str_replace( $widget_id, 'recent-comments', $before_widget ) . "\n";
    echo $before_title . $title . $after_title . "\n";
    echo "<ol>\n";
    rk_recent_comments( $number );
    echo "</ol>\n";
    echo $after_widget . "\n\n";
  }
}
?>