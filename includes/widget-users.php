<?php




/* Remove Widget if not a Help Note Archive page or Single page */ 
/* base on Plugin Name:    Widget Logic; Version:        0.56 */ 
function rbhn_widget_logic_filter_sidebars_widgets($sidebars_widgets) {    

    global $wp_reset_query_is_done;

    // loop through every widget in every sidebar (barring 'wp_inactive_widgets') checking WL for each one
    foreach($sidebars_widgets as $widget_area => $widget_list)
    {    if ($widget_area=='wp_inactive_widgets' || empty($widget_list)) continue;

        foreach($widget_list as $pos => $widget_id)
        {   

            if (false)
                unset($sidebars_widgets[$widget_area][$pos]);
        }
    }
    return $sidebars_widgets;
}

add_filter( 'sidebars_widgets', 'rbhn_widget_logic_filter_sidebars_widgets', 10);




/* Add the Help Note Custom Post Type to the author post listing */ 
function custom_post_author_archive( $query ) {
    

  if( ($query->is_author) && empty( $query->query_vars['suppress_filters'] ) ) {
      
    // include the possible author writen help notes based on the author roles h_*
    // !!!!!!!!!! todo...
    $include_post_types = array('post', 'h_ccc_member');
    
    // exclude the above if the current user can't access these with their allocated roles.
    // !!!!!!!!!! todo...
    $include_post_types = array('post', 'h_ccc_member');
    
    
    $query->set( 'post_type', $include_post_types);
    remove_action( 'pre_get_posts', 'custom_post_author_archive' ); // run once!
    //return $query;
      
    }
}    

add_filter( 'pre_get_posts', 'custom_post_author_archive' );





/**
 * Adds Users_Widget widget.
 */
class Users_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'users_widget', // Base ID
			__('Help Note Users', 'text_domain'), // Name
			array( 'description' => __( 'A Users Widget', 'text_domain' ), ) // Args
		);
	}



	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
    
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];
        
        $post_type = get_post_type();
        $help_note_role =  substr($post_type,2);
        $users = get_users( Array('role' => $help_note_role) );

		/* If users were found. */
		if ( !empty( $users ) ) {
            
			echo '<ul class="xoxo users">';

			/* Loop through each available user, creating a list item with a link to the user's archive. */
			foreach ( $users as $user ) {
				$url = get_author_posts_url( $user->ID, $user->user_nicename );

				$class = "user-{$user->ID}";
				if ( is_author( $user->ID ) )
					$class .= ' current-user';

				echo "<li class='{$class}'><a href='{$url}' title='" . esc_attr( $user->display_name ) . "'>{$user->display_name}</a></li>\n";
			}

			echo '</ul>';
		}
        
        
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Help Note Users', 'text_domain' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
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
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}

} // class Users_Widget

?>
