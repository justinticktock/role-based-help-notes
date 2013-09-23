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

	foreach($roles as $role_key=>$role_name)
	{
        
    	// $role is used in the next foreach loop
		$role = get_role( $role_key );
		$capability_type = "help_{$role_key}_note";

        $role->remove_cap( "edit_{$capability_type}" );
		$role->remove_cap( "read_{$capability_type}" );
		$role->remove_cap( "delete_{$capability_type}" );
		$role->remove_cap( "edit_{$capability_type}s" );
		$role->remove_cap( "edit_others_{$capability_type}s" );
		$role->remove_cap( "publish_{$capability_type}s" );
		$role->remove_cap( "read_private_{$capability_type}s" );
        $role->remove_cap( "delete_{$capability_type}s" );
        $role->remove_cap( "delete_private_{$capability_type}s" );
        $role->remove_cap( "delete_published_{$capability_type}s" );
        $role->remove_cap( "delete_others_{$capability_type}s" );
        $role->remove_cap( "edit_private_{$capability_type}s" );
        $role->remove_cap( "edit_published_{$capability_type}s" );
        
    }

}

?>