<?php



add_action('admin_init', 'my_debug', 12 );      // add to admin
if (!is_admin()) {
	add_action('init', 'my_debug', 12 );    	// add to frontend and hook after helpnotes has executed.
}	

function my_debug(){


	//print_r ('h_general....<br />');
	//print_r( get_post_type_object('h_general') );

	
	print '</pre>';
	//Tons of information about the   review    post type (change review to your post type)
	//Paste anywhere on a theme template file outside the loop.


	global $wp_post_types;
	print '<pre>';
	
	print_r ('$wp_post_types....<br />');
	
	print_r( $wp_post_types );
	print '</pre>';
	// Tons of information about all the different post types on the site
	//Paste anywhere on a theme template file outside the loop.


	echo '<br /><br /><h3>Roles</h3>';
	
	global $wp_roles;
	
	//if ( ! isset( $wp_roles ) )
	//$wp_roles = new WP_Roles();

	
	//echo var_dump($wp_roles->role_names );
		foreach ( $wp_roles->role_names as $role => $name ) :
			echo '<br /> <br />';
			echo '<pre> Role displayed in Admin as ' . $name ;
			echo  '     Database entry: '  . $role . '</pre>';

			echo '<h5> Capabilities assigned to the role of ' . $name. '</h5>';
			// print_r( $caps);
			echo '<pre>';
			$rolename = get_role($role);
			$caps = $rolename->capabilities;
				foreach ($caps as $capability => $value):
					echo  $capability . ' '.  $value . "\n" ;
				endforeach;
			echo '</pre>';
		endforeach;

	//Show the role display name and the name used in the database for each role on the site.
	//Show the capabilities assigned to that role
	//Paste anywhere on a theme template file outside the loop.
	//Thanks to Greenshady: http://wordpress.org/support/topic/get-a-users-role-by-user-id
	// Thanks to:  http://sillybean.net/wordpress/creating-roles/

}



?>