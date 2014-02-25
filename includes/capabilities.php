<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * RBHN_Settings class.
 */
class RBHN_Capabilities {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
	
		// Add Meta Capability Handling 
		add_filter( 'map_meta_cap', array( $this, 'rbhn_map_meta_cap' ), 10, 4);
	}
		
	/**
	 * rbhn_add_role_caps function.
	 *
	 * @access public
	 * @return void
	 */
	public function rbhn_add_role_caps() {

		$administrator      = get_role('administrator');
		
		// option collection  
		$post_types_array = get_option('rbhn_post_types');		
		$caps_options = get_option('rbhn_caps_created');  

		if ( ! empty( $post_types_array ) ) {
		
			foreach( $post_types_array as $help_note_post_types_array) {

				foreach( $help_note_post_types_array as $active_role=>$active_posttype) {

					//echo "checking active role..." . $active_role 		. "</br>";	
					if ( in_array( $active_role, $caps_options ) )
						break ; // if capabilities are already created drop out
					//echo "creating capabilities for $active_role</br></br>";	
					
					// add active role to option to stop re-creating its capabilities
					$caps_options[] = $active_role;
					update_option('rbhn_caps_created', $caps_options); 

					// gets the new Help Note active role
					$role = get_role( $active_role );
					$capability_type = $active_posttype;
					
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
	}

	/**
	 * rbhn_role_caps_cleanup function.
	 *
	 * @access public
	 * @param mixed $role_key
	 * @return void
	 */
	public function rbhn_role_caps_cleanup( $role_key ) {

		// Since the clean up is for roles not stored within the options array we need to regenerate the post-type/capability-type that would have existed
		// if the help note for a role was enabled.  Once we know this we can then clean up the capabilities if they still exist.
		$capability_type = clean_post_type_name($role_key);
			
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
			
		$users = get_users();
		$administrator      = get_role('administrator');
		
		foreach ($delete_caps as $cap) {
			
			// Clean-up Capability from WordPress Roles
			foreach (array_keys($wp_roles->roles) as $role) {
				$wp_roles->remove_cap($role, $cap);
			}
			
			// Clean-up Capability from WordPress Users where explicitly allocated 
			foreach ($users as $user) {
				$user->remove_cap($cap);
			}		

			// Clean-up Capability from the Administrator Role
			$administrator->remove_cap($cap);
			
		}
	}

	/**
	 * rbhn_clean_inactive_capabilties function.
	 *
	 * @access public
	 * @return void
	 */
	public function rbhn_clean_inactive_capabilties() {
	
		// collect an array of all inactive Help Note Post Types an remove capabilities
		$post_types_array = get_option('rbhn_post_types');  
		
		if (  ! empty($post_types_array ) ) {
		
			$active_roles = array();
			
			foreach( $post_types_array as $array) {
				foreach( $array as $active_role=>$active_posttype) {
					// add the Help Note active role in an array
					$active_roles[] = $active_role;
				}
			}

			// Find capabilities already built.
			$caps_options = get_option('rbhn_caps_created');  
			foreach( $caps_options as $cap_built) {

				// capabilities have been built so stop further re-builds.
				if ( $cap_built && ! in_array( $cap_built, $active_roles ) ) {
					//echo "removing  $cap_built</BR></BR>";		

					// clean up the capabilities 
					rbhn_role_caps_cleanup($cap_built);
					
					// remove the removed $cap_built from the built capabilities array 
					$caps_options = array_diff( $caps_options, array( $cap_built ) );
					update_option( 'rbhn_caps_created', $caps_options ); 
				}	
			}
		}
	}


	/**
	 * rbhn_map_meta_cap function to add Meta Capability Handling.
	 *
	 * @access public
	 * @param mixed $caps, $cap, $user_id, $args
	 * @return void
	 */
	public function rbhn_map_meta_cap( $caps, $cap, $user_id, $args ) {

		// drop out if not a specific post
		if ( empty($args[0]) ) 
			return $caps;    
		
		// option collection to collect active Help Note roles.  
		$post_types_array = get_option('rbhn_post_types');
		
		if (  ! empty( $post_types_array ) ) { 

			$help_note_found = false;
			
			foreach( $post_types_array as $active_role=>$active_posttype) {

				$capability_type = $active_posttype;
				
				if ( 'edit_' . array_shift($capability_type) == $cap || 'delete_' . array_shift($capability_type) == $cap || 'read_' . array_shift($capability_type) == $cap  ) {
						

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
			}
					
			/* If deleting a help note, assign the required capability. */
			elseif( $help_note_found && ("delete_{$capability_type}" == $cap ) ) {
				
				if( isset($post->post_author ) && $user_id == $post->post_author  && isset($post_type->cap->delete_posts) )
					$caps[] = $post_type->cap->delete_posts;
				elseif (isset($post_type->cap->delete_others_posts))
					$caps[] = $post_type->cap->delete_others_posts;		
			}

			/* If reading a private help note, assign the required capability. */
			elseif( $help_note_found && ("read_{$capability_type}" == $cap ) ) {

				if( 'private' != $post->post_status )
					$caps[] = 'read';
				elseif ( $user_id == $post->post_author )
					$caps[] = 'read';
				else
					$caps[] = $post_type->cap->read_private_posts;
			}
		}
		
		/* Return the capabilities required by the user. */
		return $caps;	
	}
}

new RBHN_Capabilities();

?>
