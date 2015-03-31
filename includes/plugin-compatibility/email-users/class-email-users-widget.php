<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Adds Users_Widget widget.
 */
class RBHN_Email_Users_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'rbhn_email_users_widget', // Base ID
			__('Help Note Email Users', 'role-based-help-notes-text-domain'), // Name
			array( 'description' => __( 'Add user group email shortcut to sidebar', 'role-based-help-notes-text-domain' ), ) // Args
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
	
            $role_based_help_notes = RBHN_Role_Based_Help_Notes::get_instance();

            // drop out if not a single Help Note page or Help Hote Archive page.
            // or the General Help Note Type
            // or the group email functionality isn't enabled via the capabilties and the email-users plugin
            $show_widget_help_notes = $role_based_help_notes->active_help_notes();
            $exclude_help_notes = array('h_general');
            $show_widget_help_notes = array_diff($show_widget_help_notes, $exclude_help_notes);

             if ( ! in_array( get_post_type(),  $show_widget_help_notes ) || is_archive()  )
                 return; 

            $post_type = get_post_type();
            $help_note_object = get_post_type_object( $post_type );
            $help_note_name = $help_note_object->labels->menu_name;


            // Find the users of the role based on the post type in use
            $post_type = get_post_type();
            $help_note_role = $role_based_help_notes->help_notes_role( $post_type );
            
            /* only continue if the role has group email enabled */
            if ( ! isset( $wp_roles ) ) {
                    $wp_roles = new WP_Roles();
            }   
            
            /* Loop through each role object because we need to get the caps. */
            $roles_with_cap_email_user_groups = array();
            foreach ( $wp_roles->role_objects as $key => $role ) {

                /* build up the allowed roles for the current user */
                if ( $key == $help_note_role ) {
                    /* Roles without capabilities will cause an error, so we need to check if $role->capabilities is an array. */
                    if ( is_array( $role->capabilities ) ) {

                        /* Loop through the role's capabilities to find roles with the 'email_user_groups' capabiltiy set. */
                        foreach ( $role->capabilities as $cap => $grant )
                            if ( ( $cap == 'email_user_groups' ) && $grant ) {
                                 $roles_with_cap_email_user_groups[] = $key;
                                 break 2;
                            }
                    }                    
                }
            }   


           if ( in_array( $help_note_role, $roles_with_cap_email_user_groups ) ) {
                echo $args['before_widget'];
                if ( empty ( $instance['title'] ) ) {
                    $instance['title'] = esc_html__("Group Email", 'role-based-help-notes' );
                }
                        
                echo $args['before_title'] . $instance['title'] . "" . $args['after_title'];
               // echo '<ul><a href="' . admin_url( 'admin.php?page=mailusers-send-to-group-page' ) . '">' .   sprintf( __( 'Email everyone with the %1$s role.', 'role-based-help-notes-text-domain' ), '<strong>' . $help_note_name .'</strong>') . " </a></ul>";
                echo '<button class="readmorebtn" onclick="' . esc_attr('window.location="' . admin_url( 'admin.php?page=mailusers-send-to-group-page' ) . '"') . '">' . sprintf( __( 'Email the %1$s group.', 'role-based-help-notes-text-domain' ), '<strong>' . $help_note_name .'</strong>') . '</button></BR></BR>';
                echo $args['after_widget'];
           }
           
          
            
         
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
			$title = '';
		}

		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
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

} // class RBHN_Email_Users_Widget

?>