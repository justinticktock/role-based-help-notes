<?php


// Add New Capabilities ..
function my_help_add_role_caps() {

    
    global $wp_roles;

    // Load Roles if not set
    if ( ! isset( $wp_roles ) )
    $wp_roles = new WP_Roles();

    $roles              = $wp_roles->get_names();
    $administrator      = get_role('administrator');
    
    // option collection  
    $settings_options = get_option('help_note_option');  
    
    if (  ! empty($settings_options ) ) {

		foreach( $settings_options['help_note_post_types'] as $selected_key=>$role_selected)
        {
			$post_type_name = clean_post_type_name($role_selected);

    		// gets the author role
    		$role = get_role( $role_selected );
    		$capability_type = sanitize_key($post_type_name);

			
    		$role->add_cap( "edit_{$capability_type}s" );
    		$role->add_cap( "edit_others_{$capability_type}s" );
    		$role->add_cap( "publish_{$capability_type}s" );
    		$role->add_cap( "read_private_{$capability_type}s" );
            $role->add_cap( "delete_{$capability_type}s" );
            $role->add_cap( "delete_private_{$capability_type}s" );
            $role->add_cap( "delete_published_{$capability_type}s" );
            $role->add_cap( "delete_others_{$capability_type}s" );
            $role->add_cap( "edit_private_{$capability_type}s" );
            $role->add_cap( "edit_published_{$capability_type}s" );     
            $role->add_cap( "create_{$capability_type}s" );  
                    
            // add administrator roles
            // don't allocate any of the three primitive capabilities to a users role
            $administrator->add_cap( "edit_{$capability_type}" );
            $administrator->add_cap( "read_{$capability_type}" );
            $administrator->add_cap( "delete_{$capability_type}" );
            $administrator->add_cap( "create_{$capability_type}s" );
			
			// add administrator roles
    		$administrator->add_cap( "edit_{$capability_type}s" );
    		$administrator->add_cap( "edit_others_{$capability_type}s" );
    		$administrator->add_cap( "publish_{$capability_type}s" );
    		$administrator->add_cap( "read_private_{$capability_type}s" );
            $administrator->add_cap( "delete_{$capability_type}s" );
            $administrator->add_cap( "delete_private_{$capability_type}s" );
            $administrator->add_cap( "delete_published_{$capability_type}s" );
            $administrator->add_cap( "delete_others_{$capability_type}s" );
            $administrator->add_cap( "edit_private_{$capability_type}s" );
            $administrator->add_cap( "edit_published_{$capability_type}s" );
    	}
  
	}    
}


// Add Meta Capability Handling 
add_filter( 'map_meta_cap', 'rbhn_map_meta_cap', 10, 4 );


function rbhn_map_meta_cap( $caps, $cap, $user_id, $args ) {

    // drop out if not a specific post
    if ( empty($args[0]) ) 
        return $caps;    
    
    // option collection to collect active Help Note roles.  
	$settings_options = get_option('help_note_option');  
    
    if (  ! empty( $settings_options ) ) { 

        $help_note_found = false;
        
		foreach( $settings_options['help_note_post_types'] as $selected_key=>$role_selected) {
          
			$capability_type = clean_post_type_name($role_selected);

			if ( "edit_{$capability_type}" == $cap || "delete_{$capability_type}" == $cap || "read_{$capability_type}" == $cap  ) {
					

            	$post = get_post( $args[0] );
                $post_type = $capability_type;


				/* Set an empty array for the caps. */
				$caps = array();
                
                $help_note_found = true;
                break;
                
			}
    	}
        
		/* If editing a help note, assign the required capability. */
		if (  $help_note_found && ("edit_{$capability_type}" == $cap ) ) {

			if( $user_id == $post->post_author )
				$caps[] = $post_type->cap->edit_posts;
			else
				$caps[] = $post_type->cap->edit_others_posts;	
                
            echo "edit_{$capability_type} = " . var_dump($caps);
		}
				
		/* If deleting a help note, assign the required capability. */
		elseif( $help_note_found && ("delete_{$capability_type}" == $cap ) ) {
			
			if( isset($post->post_author ) && $user_id == $post->post_author  && isset($post_type->cap->delete_posts) )
				$caps[] = $post_type->cap->delete_posts;
			elseif (isset($post_type->cap->delete_others_posts))
				$caps[] = $post_type->cap->delete_others_posts;		
                
            echo "delete_{$capability_type} = " . var_dump($caps);
		}

		/* If reading a private help note, assign the required capability. */
		elseif( $help_note_found && ("read_{$capability_type}" == $cap ) ) {

			if( 'private' != $post->post_status )
				$caps[] = 'read';
			elseif ( $user_id == $post->post_author )
				$caps[] = 'read';
			else
				$caps[] = $post_type->cap->read_private_posts;
                
            echo "read_{$capability_type} = " . var_dump($caps);
		}

	}
	
	/* Return the capabilities required by the user. */
	return $caps;	
}
?>
