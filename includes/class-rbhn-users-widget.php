<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Adds Users_Widget widget.
 */
class RBHN_Users_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct( ) {
		parent::__construct(
			'rbhn_users_widget', // Base ID
			__( 'Help Note Users', 'role-based-help-notes-text-domain' ), // Name
			array( 'description' => __( 'A Users Widget', 'role-based-help-notes-text-domain' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget( )
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
	
		$role_based_help_notes = RBHN_Role_Based_Help_Notes::get_instance( );

       // drop out if not a single Help Note page or Help Hote Archive page.
       // or the General Help Note Type
       $show_widget_help_notes = $role_based_help_notes->active_help_notes( );
       $exclude_help_notes = array( 'h_general' );
       $show_widget_help_notes = array_diff( $show_widget_help_notes, $exclude_help_notes );
       
        if ( ! in_array( get_post_type( ),  $show_widget_help_notes ) || is_archive( ) )
            return; 

		$post_type = get_post_type( );
		$help_note_object = get_post_type_object( $post_type );
		$help_note_name = $help_note_object->labels->menu_name;
		
		if ( empty( $instance['title'] ) ) {
			$title = sprintf( __( '%1$s Line-up', 'role-based-help-notes-text-domain' ), $help_note_name );
		} else {
			$title = $instance['title'];
		}
		
		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];
        
		// Find the users of the role based on the post type in use
		$post_type = get_post_type( );
    	$help_note_role = $role_based_help_notes->help_notes_role( $post_type );
        $users = get_users( Array( 'role' => $help_note_role ) );

		/* If users were found. */
		if ( !empty( $users ) ) {
            
			echo '<ul class="xoxo users">';

			/* Loop through each available user, creating a list item with a link to the user's archive. */
			foreach ( $users as $user ) {

				$url_found = '';
				$user_id = $user->ID;

                $my_query = new WP_Query( array(
                    'author'        => $user_id,
                    'post_type'     => get_post_types( array( 'public' => true ) ),
                    'post_status'   => 'publish',
					) );

				if ( $my_query->have_posts( ) ) {
					$url_found = get_author_posts_url( $user_id, $user->user_nicename );
				}

				$url = apply_filters( 'rbhn_author_url', $url_found , $user_id );	

				$class = "user-{$user->ID}";
				if ( is_author( $user->ID ) )
					$class .= ' current-user';

				if ( ! empty( $url ) ) {
					echo "<li class='{$class}'><a href='{$url}' title='" . esc_attr( $user->display_name ) . "'>{$user->display_name}</a></li>\n";
				} else {
					echo "<li class='{$class}'>{$user->display_name}</li>\n";		
				}
			}

			echo '</ul>';
		}

		echo $args['after_widget'];
		wp_reset_postdata( );
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form( )
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) && ( $instance[ 'title' ] != "" ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = '';
		}

		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		<?php echo __( 'Leave Blank for a dynamic title which includes the Role.', 'role-based-help-notes-text-domain' ); ?></p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update( )
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array( );
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}

} // class Users_Widget

?>