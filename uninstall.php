<?php

//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit ( );
	
if ( is_multisite( ) ) {
    global $wpdb;
    $blogs = $wpdb->get_results( "SELECT blog_id FROM {$wpdb->blogs}", ARRAY_a );
    if ( $blogs ) {
        foreach( $blogs as $blor ) {
            switch_to_blog( $blog['blog_id'] );
            rbhn_capabilities_clean_up( );
			rbhn_clean_database( );
        }
        restore_current_blog( );
    }
} else {
		rbhn_capabilities_clean_up( );
		rbhn_clean_database( );
}
		
// remove all database entries for currently active blog on uninstall.
function rbhn_clean_database( ) {
		
		delete_option( 'rbhn_plugin_version' );
		delete_option( 'rbhn_caps_created' );
		delete_option( 'rbhn_general_enabled' );
		delete_option( 'rbhn_post_types' );
		delete_option( 'rbhn_contents_page' );
		delete_option( 'rbhn_widgets_enabled' );
		delete_option( 'rbhn_contents_page' );
		delete_option( 'rbhn_install_date' );
		delete_option( 'rbhn_make_clickable' );

		// plugin specific database entries
		delete_option( 'rbhn_user_role_editor' );
		delete_option( 'rbhn_menu_items_visibility_control' );
		delete_option( 'rbhn_user_switching' );
		delete_option( 'rbhn_simple_page_ordering' );
		delete_option( 'rbhn_simple_footnotes_plugin' );
		delete_option( 'rbhn_disable_comments_plugin' );
		delete_option( 'rbhn_email_post_changes_plugin' );
		delete_option( 'rbhn_post_type_switcher_plugin' );
		delete_option( 'rbhn_post_type_archive_in_menu_plugin' );
		
		delete_option( 'rbhn_deactivate_user-switching' );
		delete_option( 'rbhn_deactivate_simple-page-ordering' );
		delete_option( 'rbhn_deactivate_simple-footnotes' );
		delete_option( 'rbhn_deactivate_disable-comments' );
		delete_option( 'rbhn_deactivate_email-post-changes' );
		delete_option( 'rbhn_deactivate_post-type-switcher' );
		delete_option( 'rbhn_deactivate_post-type-archive-in-menu' );
		
		// user specific database entries
		delete_user_meta( get_current_user_id( ), 'rbhn_prompt_timeout', $meta_value );
		delete_user_meta( get_current_user_id( ), 'rbhn_start_date', $meta_value );
		delete_user_meta( get_current_user_id( ), 'rbhn_hide_notice', $meta_value );

}
		
// remove capabilities on uninstall.
function rbhn_capabilities_clean_up( ) {

    global $wp_roles;
 
    if ( ! isset( $wp_roles ) )
        $wp_roles = new WP_Roles( );
            
    $roles = $wp_roles->get_names( );

    // loop through the roles to create the capability list that needs to be cleaned out
	foreach( $roles as $role_key=>$role_name ) 
    {
        rbhn_role_caps_uninstall( $role_key );
    }
}

// remove capabilities on uninstall.
function rbhn_role_caps_uninstall( $role_key ) {

	$post_types_array = get_option( 'rbhn_post_types' );
	
    if ( ! empty( $post_types_array ) ) {
	    foreach( $post_types_array as $array ) {
			
			foreach( $array as $active_role=>$active_posttype ) {

				if ( $role_key == $active_role ) {
					$role = get_role( $active_role );
					$capability_type = $active_posttype;
				}
			}
		}
	}
	
	// if no post type found drop out.
	if ( empty( $capability_type ) )
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
			"create_{$capability_type}s",
			"manage_categories_{$capability_type}"		
			);

    global $wp_roles;
 
    if ( ! isset( $wp_roles ) ) {
        $wp_roles = new WP_Roles( );
	}
		
	$users = get_users( );
	$administrator      = get_role( 'administrator' );
	
	// loop through the capability list.
	foreach ( $delete_caps as $cap ) {

		// Clean-up Capability from WordPress Roles
		foreach ( array_keys( $wp_roles->roles ) as $role ) {
			$wp_roles->remove_cap( $role, $cap );
		}
		
		// Clean-up Capability from WordPress Users where explicitly allocated 
		foreach ( $users as $user ) {
			$user->remove_cap( $cap );
		}

		// Clean-up Capability from the Administrator Role
		$administrator->remove_cap( $cap );		
	}
	unset( $wp_roles );
}
	
?>