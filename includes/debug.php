<?php





add_action( 'contextual_help', 'rbhn_screen_help', 10, 3 );
function rbhn_screen_help( $contextual_help, $screen_id, $screen ) {
 
    // The add_help_tab function for screen was introduced in WordPress 3.3.
    if ( ! method_exists( $screen, 'add_help_tab' ) )
        return $contextual_help;
 
 
    // Append global $hook_suffix to the hook stems
    $hooks = array(
        "load-$hook_suffix",
        "admin_print_styles-$hook_suffix",
        "admin_print_scripts-$hook_suffix",
        "admin_head-$hook_suffix",
        "admin_footer-$hook_suffix"
    );
 
    $help_content = my_debug();
 
    // Add help panel
    $screen->add_help_tab( array(
        'id'      => 'role-based-help-notes-help',
        'title'   => 'Capabilty Debug Information',
        'content' => $help_content,
    ));
 
    return $contextual_help;
}




function my_debug(){

	//Tons of information about the   review    post type (change review to your post type)
	//Paste anywhere on a theme template file outside the loop.

    
	global $wp_post_types;
	
    $debug_text = "<br /><br /><h3>Post Types</h3>";
    $debug_text .= "<br /> <br />";
    $debug_text .= "<pre>" . $wp_post_types ."</pre>" ;
    //$debug_text .=  '<li>' . implode( '</li><li>', $wp_post_types ) . '</li>' ;


	// Tons of information about all the different post types on the site
	//Paste anywhere on a theme template file outside the loop.
    $debug_text .= "<br /><br /><h3>Roles</h3>";
	global $wp_roles;
	

	foreach ( $wp_roles->role_names as $role => $name ) :
		$debug_text .= "<br /> <br />";
		$debug_text .= "<pre> Role displayed in Admin as " . $name ;
		$debug_text .= "     Database entry: "  . $role . "</pre>";

		$debug_text .= "<h5> Capabilities assigned to the role of " . $name. "</h5>";
		$debug_text .= "<pre>";
		$rolename = get_role($role);
		$caps = $rolename->capabilities;
			foreach ($caps as $capability => $value):
				$debug_text .= "$capability  $value\n" ;
			endforeach;
		$debug_text .= "</pre>";
	endforeach;

    return $debug_text;

}



?>