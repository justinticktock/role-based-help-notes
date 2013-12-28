<?php

/* Add the Help Note Custom Post Type to the author post listing */ 
function rbhn_custom_post_author_archive( $query ) {
    
	if( !is_admin() && $query->is_main_query() && empty( $query->query_vars['suppress_filters'] ) ) {

		// For author queries add Help Note post types
		if ($query->is_author) {
			$include_post_types = rbhn_active_posttypes() ;
			$include_post_types[] = 'post';
			$query->set( 'post_type', $include_post_types);
		}

		// remove the filter after running, run only once!
		remove_action( 'pre_get_posts', 'rbhn_custom_post_author_archive' ); 

	}
}    

add_filter( 'pre_get_posts', 'rbhn_custom_post_author_archive' );


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

       // drop out if not a single Help Note page or Help Hote Archive page.
       // or the General Help Note Type
       $show_widget_help_notes = rbhn_active_posttypes();
       $exclude_help_notes = array('h_general');
       $show_widget_help_notes = array_diff($show_widget_help_notes, $exclude_help_notes);
       
        if ( ! in_array( get_post_type(),  $show_widget_help_notes ) )
            return; 

		$post_type = get_post_type();
		$help_note_object = get_post_type_object( $post_type );
		$help_note_name = $help_note_object->labels->menu_name;
		$title = __( $help_note_name . " Users", 'role-based-help-notes' );
		
		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];
        
		// Find the role based on the post type.
		$post_type = get_post_type();
		$settings_options = get_option('help_note_option');  
		if (  ! empty($settings_options ) ) {
			foreach( $settings_options['help_note_post_types'] as $array) {
				foreach( $array as $active_role=>$active_posttype) {
					if ($post_type == $active_posttype) {
						$help_note_role =  $active_role;
						break 2;
					}
				}
			}		
		}

        $users = get_users( Array('role' => $help_note_role) );

		/* If users were found. */
		if ( !empty( $users ) ) {
            
			echo '<ul class="xoxo users">';

			/* Loop through each available user, creating a list item with a link to the user's archive. */
			foreach ( $users as $user ) {

				$user_id = $user->ID;
				$url = apply_filters( 'rbhn_author_url', get_author_posts_url( $user_id, $user->user_nicename ) , $user_id);	

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
		if ( isset( $instance[ 'title' ] ) && ( $instance[ 'title' ] != "" )) {
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