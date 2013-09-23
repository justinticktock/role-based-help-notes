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
            delete_option('help_note_option');
            rbhn_capability_clean_up();
        }
        restore_current_blog();
    }
} else {
    do_action( 'rbhn_remove_caps');  // clear out capabilities
    rbhn_capability_clean_up();
}



// remove capabilities on uninstall.
function rbhn_capability_clean_up() {

    global $wp_roles;
 
    if ( ! isset( $wp_roles ) )
        $wp_roles = new WP_Roles();
            
    $roles = $wp_roles->get_names();

    // loop through the roles to create the capabillity list that needs to be cleaned out
	foreach($roles as $role_key=>$role_name)  
    {
        
        $role = get_role( $role_key );
        $caps = $role->capabilities;
        
        $capability_type = "help_{$role_key}_note";
    
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
                "edit_published_{$capability_type}s"
                );


        // loop through the capablity list.
        foreach ($delete_caps as $cap) {
            // loop through all roles and clean capabilties.
            foreach (array_keys($wp_roles->roles) as $role) {
                $wp_roles->remove_cap($role, $cap);
            }
        }

        
    }

}

?>