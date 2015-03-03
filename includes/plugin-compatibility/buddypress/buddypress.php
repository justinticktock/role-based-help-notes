<?php

/*
 * BuddyPress 
 * replace the author slug with the BP user public profile if the BP_ENABLE_ROOT_PROFILES has been defined.
  * @return bp_core_get_user_domain( ) for the current user
 */



/* 
 * @uses bp_core_get_user_domain( ) Returns the domain for the passed user: e.g. http://domain.com/members/andy/
 */
add_filter( 'rbhn_author_url', 'rbhn_bp_enable_root_profiles', 10, 2);

function rbhn_bp_enable_root_profiles( $val, $user_id ) {

	 return bp_core_get_user_domain( $user_id ) ;
	
}

?>