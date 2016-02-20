<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Adds Help Note Tag Cloud widget.
 */

class RBHN_Tag_Cloud_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct( ) {
		parent::__construct(
			'rbhn_tag_cloud_widget', // Base ID
			__( 'Help Note Tag Cloud', 'role-based-help-notes' ), // Name
			array( 'description' => __( 'A Tag Cloud Widget', 'role-based-help-notes' ), ) // Args
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
       $exclude_help_notes = array( 'h_general' );
       $show_widget_help_notes = array_diff( $role_based_help_notes->active_help_notes( ), $exclude_help_notes );
       
        if ( ! in_array( get_post_type( ),  $show_widget_help_notes ) ) {
            return; 
        }

		$post_type = get_post_type( );
		$help_note_object = get_post_type_object( $post_type );
		$help_note_name = $help_note_object->labels->menu_name;
		
		if ( empty( $instance['title'] ) ) {
			$title = sprintf( _x( '%1$s Topics', 'the topics tag cloud for a single role' , 'role-based-help-notes' ), $help_note_name );
		} else {
			$title = $instance['title'];
		}
		
		echo $args['before_widget'];
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
                }
 /*       
		if ( $title )
			echo $before_title . $title . $after_title;
*/
		echo '<div class="tagcloud">';

		wp_tag_cloud( apply_filters( 'rbhn_tag_cloud_widget_args', array( 'taxonomy' => $post_type . 'topics' ) ) );

		echo "</div>";

		echo $args['after_widget'];
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
		<?php echo __( 'Leave Blank for a dynamic cloud title which includes the Role.', 'role-based-help-notes' ); ?></p>
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

}