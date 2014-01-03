<?php

//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit ();
	
if (is_multisite()) {
    global $wpdb;
    $blogs = $wpdb->get_results("SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A);
    if ($blogs) {
        foreach($blogs as $blog) {
            switch_to_blog($blog['blog_id']);
            rbhn_capabilities_clean_up();
            delete_option('help_note_option');
            delete_option('rbhn_update_request');
        }
        restore_current_blog();
    }
} else {
		rbhn_capabilities_clean_up();
		delete_option('help_note_option');
		delete_option('rbhn_update_request');
}

// remove capabilities on uninstall.
function rbhn_capabilities_clean_up() {

    global $wp_roles;
 
    if ( ! isset( $wp_roles ) )
        $wp_roles = new WP_Roles();
            
    $roles = $wp_roles->get_names();

    // loop through the roles to create the capability list that needs to be cleaned out
	foreach($roles as $role_key=>$role_name)  
    {
        rbhn_role_caps_uninstall( $role_key );
    }
}

// remove capabilities on uninstall.
function rbhn_role_caps_uninstall( $role_key ) {

    // collect the Help Note Post Type name sorted within the option.
    $settings_options = get_option('help_note_option');  
    if (  ! empty($settings_options ) ) {
	    foreach( $settings_options['help_note_post_types'] as $array) {
			
			foreach( $array as $active_role=>$active_posttype) {

				if ( $role_key == $active_role ) {
					$role = get_role( $active_role );
					$capability_type = $active_posttype;
				}
			}
		}
	}
	
	// if no post type found drop out.
	if ( empty($capability_type) )
		return;

	$delete_caps = array(
			"edit_{$capability_type}",
			"read_{$capability_type}",
			"delete_{$capability_type}",
			"edit_{$capability_type}s",
			"edit_others_{$capability_type}s",
			"publish_{$capability_type}s",
			"read_private_{$capability_type}s",
			"delete_{$capability_type}s",
			"delete_private_{$capability_type}s",
			"delete_published_{$capability_type}s",
			"delete_others_{$capability_type}s",
			"edit_private_{$capability_type}s",
			"edit_published_{$capability_type}s",
			"create_{$capability_type}s"
			);

    global $wp_roles;
 
    if ( ! isset( $wp_roles ) )
        $wp_roles = new WP_Roles();
		
	// loop through the capability list.
	foreach ($delete_caps as $cap) {
		// loop through all roles and clean capabilities.
		foreach (array_keys($wp_roles->roles) as $role) {
			$wp_roles->remove_cap($role, $cap);
		}
	}
}
?>