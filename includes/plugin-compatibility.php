<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Additional Compatibility with Plugins
 * 
 */
  
/*
 * BuddyPress 
 * replace the author slug with the BP user public profile if the BP_ENABLE_ROOT_PROFILES has been defined.
 */
	if ( defined('BP_ENABLE_ROOT_PROFILES') ) 
		add_filter( 'rbhn_author_url', 'rbhn_bp_enable_root_profiles', 10, 2);

	/* 
	 * @uses bp_core_get_user_domain() Returns the domain for the passed user: e.g. http://domain.com/members/andy/
	 */
	function rbhn_bp_enable_root_profiles($val, $user_id ) {

		 return bp_core_get_user_domain( $user_id ) ;
		
	}
?>