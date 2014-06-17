<?php

/**
 * Additional Compatibility with Plugins
 * 
 */
  
/*
 * BuddyPress 
 * replace the author slug with the BP user public profile if the BP_ENABLE_ROOT_PROFILES has been defined.
  * @return bp_core_get_user_domain() for the current user
 */
if ( defined('BP_ENABLE_ROOT_PROFILES') ) 
	add_filter( 'rbhn_author_url', 'rbhn_bp_enable_root_profiles', 10, 2);

/* 
 * @uses bp_core_get_user_domain() Returns the domain for the passed user: e.g. http://domain.com/members/andy/
 */
function rbhn_bp_enable_root_profiles($val, $user_id ) {

	 return bp_core_get_user_domain( $user_id ) ;
	
}

/**
 * conditional function provided to checks if the current visitor has available Help Notes
 * @return bool True if Help Notes are available for the site front-end, false if not logged or no Help Notes are available.
 * Used also in other plugins - like “Menu Items Visibility Control” plugin.
 */
if ( !function_exists('help_notes_available') ) {
	function help_notes_available() {
	
		// check if no help notes are selected.
		$help_note_post_types =  get_option('rbhn_post_types');
	
		if ( ! array_filter( (array) $help_note_post_types ) && ! get_option('rbhn_general_enabled') )
			return false;

	   
		global $role_based_help_notes; 
		// option collection  
		$post_types_array 		= get_option('rbhn_post_types');
	
		//if the current user has the role of an active Help Note.
		if (  array_filter( (array) $post_types_array )) {	
			foreach( $post_types_array as $array) {
				foreach( $array as $active_role=>$active_posttype) {
					if ($role_based_help_notes->help_notes_current_user_has_role( $active_role )) {
						return true;
					}				
				}
			}	
		}   

		// General Help Notes
		$my_query = new WP_Query( array(
			'post_type'     => array( 'h_general' ),
			));

		if ( $my_query->have_posts() ) {
			wp_reset_postdata();
			return true;
			}
			
		wp_reset_postdata();
		return false;
	}
}
?>