<?php

add_filter( 'map_meta_cap', 'rbhn_map_meta_cap', 10, 4 );

function rbhn_map_meta_cap( $caps, $cap, $user_id, $args ) {
    
	// add loop break out for the right cpt
	/* Set an empty array for the caps. */
    global $wp_roles;

    // Load Roles if not set
    if ( ! isset( $wp_roles ) )
	$wp_roles = new WP_Roles();

	$roles = $wp_roles->get_names();

    // option collection  
	$settings_options = get_option('help_note_option');  
    
    if (  ! empty($settings_options ) ) {

		$help_note_found = false;
		
		foreach( $settings_options['help_note_post_types'] as $selected_key=>$role_selected)
        {
          
			// limit to 20 characters length for the WP limitation of custom post type names
			$post_type_name = 'h_' . substr($role_selected , -18);
			$capability_type    = sanitize_key($post_type_name);
            
			if ( "edit_{$capability_type}" == $cap || "delete_{$capability_type}" == $cap || "read_{$capability_type}" == $cap ) {

					$post_type = get_post_type_object( $post->post_type );

					/* Set an empty array for the caps. */
					$caps = array();
				$help_note_found = true;
				break;					
			}
		}

		if (! $help_note_found ) {
        			
			return $caps;

		}	
	}


	/* If editing a testimonial, assign the required capability. */
	if ( "edit_{$capability_type}" == $cap ) {
					
            		if( $user_id == $post->post_author )
            			$caps[] = $post_type->cap->edit_posts;
            		else
            			$caps[] = $post_type->cap->edit_others_posts;
	}
            
	/* If deleting a testimonial, assign the required capability. */
	else if( "delete_{$capability_type}" == $cap ) {
        
		if( isset($post->post_author ) && $user_id == $post->post_author  && isset($post_type->cap->delete_posts) )
			$caps[] = $post_type->cap->delete_posts;
		elseif (isset($post_type->cap->delete_others_posts))
			$caps[] = $post_type->cap->delete_others_posts;
	}

	/* If reading a private testimonial, assign the required capability. */
	elseif( "read_{$capability_type}" == $cap ) {

            		if( 'private' != $post->post_status )
            			$caps[] = 'read';
            		elseif ( $user_id == $post->post_author )
            			$caps[] = 'read';
            		else
            			$caps[] = $post_type->cap->read_private_posts;
        		}	

	/* Return the capabilities required by the user. */
	return $caps;

}
?>
