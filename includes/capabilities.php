<?php

add_filter( 'map_meta_cap', 'rbhn_map_meta_cap', 10, 4 );


function rbhn_map_meta_cap( $caps, $cap, $user_id, $args ) {
    // uncommenting this will show what access protection (capabilityes) are required for areas of the screen.
    /*
    if (is_admin() ) { 
       echo var_dump(func_get_args()) . "</BR></BR></BR></BR>";
       return $caps;
        }
    */
    
    global $wp_roles;

    // Load Roles if not set
    if ( ! isset( $wp_roles ) )
	$wp_roles = new WP_Roles();

	$roles = $wp_roles->get_names();

    // option collection  
	$settings_options = get_option('help_note_option');  
    
    if (  ! empty($settings_options ) ) {

		foreach( $settings_options['help_note_post_types'] as $selected_key=>$role_selected)
        {
          
    		$role = get_role( $role_selected );
    		$capability_type = "help_{$role_selected}_note";

            
            switch ( $cap ) {

        		case "delete_{$capability_type}":
                    
                    /* If deleting a helpnote, assign the required capability. */

					$post = get_post( $args[0] );
					$post_type = get_post_type_object( $post->post_type );

					/* Set an empty array for the caps. */
					$caps = array();

                	if( $user_id == $post->post_author )
            			$caps[] = $post_type->cap->delete_posts;
            		else
            			$caps[] = $post_type->cap->delete_others_posts;
        			
        			break;

        		case "edit_{$capability_type}":

                    /* If editing a helpnote, assign the required capability. */

					$post = get_post( $args[0] );
					$post_type = get_post_type_object( $post->post_type );

					/* Set an empty array for the caps. */
					$caps = array();
					
            		if( $user_id == $post->post_author )
            			$caps[] = $post_type->cap->edit_posts;
            		else
            			$caps[] = $post_type->cap->edit_others_posts;
            
        			break;
        
        		case "read_{$capability_type}":	

                    /* If reading a private helpnote, assign the required capability. */
					
					$post = get_post( $args[0] );
					$post_type = get_post_type_object( $post->post_type );

					/* Set an empty array for the caps. */
					$caps = array();

            		if( 'private' != $post->post_status )
            			$caps[] = 'read';
            		elseif ( $user_id == $post->post_author )
            			$caps[] = 'read';
            		else
            			$caps[] = $post_type->cap->read_private_posts;

        			break;
        			
        		}	
    	}
	}    


	/* Return the capabilities required by the user. */
	return $caps;


}

?>
