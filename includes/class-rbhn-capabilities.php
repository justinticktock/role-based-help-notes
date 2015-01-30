<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * RBHN_Capabilities class.
 */
class RBHN_Capabilities {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct( ) {
	
		// Add Meta Capability Handling 
		add_filter( 'map_meta_cap', array( $this, 'rbhn_map_meta_cap' ), 10, 4);
	}
		
	/**
	 * rbhn_add_role_caps function.
	 *
	 * @access public
	 * @return void
	 */
	public static function rbhn_add_role_caps( ) {

		// option collection  
		$post_types_array 	= get_option( 'rbhn_post_types' );		
		$caps_options 		= ( array ) get_option( 'rbhn_caps_created' );  

		if ( ! empty( $post_types_array ) ) {

			foreach( $post_types_array as $help_note_post_types_array ) {

				foreach( $help_note_post_types_array as $active_role=>$active_posttype ) {


					if ( in_array( $active_role, $caps_options ) )
						break ; // if capabilities are already created drop out

					// add active role to option to stop re-creating its capabilities
					$caps_options[] = $active_role;
					update_option( 'rbhn_caps_created', $caps_options ); 

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
					$role->add_cap( "manage_categories_{$capability_type}" );
					
					
					// As of 12/11/2014 help notes are created all the time so Admins are no-longer given access by default 
					// they must be in a role to get the Help Notes.  This is for the flush of permalinks to save correctly
					// For Super-Admins on a multi-site Network installation all Help Notes will be available.
					// For Administrators Help Notes will only be available if the role is given.


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
	public static function rbhn_role_caps_cleanup( $role_key ) {

		$role_based_help_notes = RBHN_Role_Based_Help_Notes::get_instance( );
		
		// Since the clean up is for roles not stored within the options array we need to regenerate the post-type/capability-type that would have existed
		// if the help note for a role was enabled.  Once we know this we can then clean up the capabilities if they still exist.
		$capability_type = $role_based_help_notes->clean_post_type_name( $role_key );
			
		$delete_caps = 	array(
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
			
		$users 			= get_users( );
		$administrator	= get_role( 'administrator' );
		
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

	/**
	 * rbhn_clean_inactive_capabilties function.
	 *
	 * @access public
	 * @return void
	 */
	public static function rbhn_clean_inactive_capabilties( ) {

		// collect an array of all inactive Help Note Post Types an remove capabilities
		$post_types_array = get_option( 'rbhn_post_types' );  
		
		$role_based_help_notes = RBHN_Role_Based_Help_Notes::get_instance( );
		$active_roles = $role_based_help_notes->help_notes_role( );

		// Find capabilities already built.
		$caps_options = get_option( 'rbhn_caps_created' );
		if ( ! empty( $caps_options ) ) {
			foreach( $caps_options as $cap_built ) {

				// capabilities have been built so stop further re-builds.
				if ( $cap_built && ! in_array( $cap_built, $active_roles ) ) {

					// clean up the capabilities 
					self::rbhn_role_caps_cleanup( $cap_built );
					
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
		
		// option collection to collect active Help Note roles.  
		$post_types_array = ( array ) get_option( 'rbhn_post_types' );	// collect available roles
		$post_types_array = array_filter( $post_types_array );			// remove empty entries

		// if get_option( 'rbhn_post_types' ) not empty
		if ( ! empty( $post_types_array ) ) {
		
			$help_note_found = false;

	
			foreach( $post_types_array as $active_role=>$active_posttype ) {
	
				$active_posttype_values =  array_values ( ( array ) $active_posttype );
				$capability_type = array_shift( $active_posttype_values );
 
				if ( 'edit_' . $capability_type == $cap || 'delete_' . $capability_type == $cap || 'read_' . $capability_type == $cap  ) {


					$post = get_post( $args[0] );
					$post_type = get_post_type_object( $post->post_type );

					/* Set an empty array for the caps. */
					$caps = array( );
					
					$help_note_found = true;
					break;
					
				}
			}

			
			/* If editing a help note, assign the required capability. */
			if ( $help_note_found && ( "edit_{$capability_type}" == $cap ) ) {

				if( $user_id == $post->post_author )
					$caps[] = $post_type->cap->edit_posts;
				else
					$caps[] = $post_type->cap->edit_others_posts;	

			}
					
			/* If deleting a help note, assign the required capability. */
			elseif( $help_note_found && ( "delete_{$capability_type}" == $cap ) ) {
				
				if( isset( $post->post_author ) && $user_id == $post->post_author  && isset( $post_type->cap->delete_posts ) )
					$caps[] = $post_type->cap->delete_posts;
				elseif ( isset( $post_type->cap->delete_others_posts ) )
					$caps[] = $post_type->cap->delete_others_posts;		
			}

			/* If reading a private help note, assign the required capability. */
			elseif( $help_note_found && ( "read_{$capability_type}" == $cap ) ) {

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

new RBHN_Capabilities( );

?>