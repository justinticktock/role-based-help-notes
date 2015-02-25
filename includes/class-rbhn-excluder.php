<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * RE_EXCLUDER class.
 */
class RBHN_EXCLUDER {

	// Refers to a single instance of this class.
    private static $instance = null;

	
    /**
    * __construct function.
    *
    * @access public
    * @return void
    */
    public function __construct() {

           // block standard WP roles from the primary selections.
           add_filter( 'editable_roles', array( $this, 'exclude_role_from_user' ) );

    }

    public function exclude_role_from_user( $editable_roles ) {
           global $wp_roles;
           
           // drop out if not on the group emails page.
            if	( ! is_admin() || ! ( isset( $_GET['page'] ) && ( $_GET['page'] == 'mailusers-send-to-group-page' ) ) )  {
                return $editable_roles;
            }	
                        
           if ( ! isset( $wp_roles ) ) {
                   $wp_roles = new WP_Roles();
           }

           $roles_all = array_keys( $wp_roles->get_names( ) );

           $roles_allowed = array( );
           // loop through each role that is excluded
           foreach ( $roles_all as $role ) {

                   if ( $this->rbhn_current_user_has_role( $role ) ) {

                       //build up the allows roles array
                       // use the $excluded_roles array as a mask on $roles_allowed for this foreach loop (settings-tab)
                       $roles_allowed[] = $role;

                   }
           }

           // now we have gathered all roles that are still allowed so now we will find the 
           // inverse to get an array of roles to be excluded
           if ( $roles_all != $roles_allowed ) {

                   $excluded_roles = array_diff( $roles_all, $roles_allowed );
                   // loop through each role that is excluded
                   // and remove excluded roles 
                   foreach ( $excluded_roles as $role_key_exclude ) {
                       unset ( $editable_roles[$role_key_exclude] );
                   }
           }
           return $editable_roles;
    }


    /**
    * Checks if a particular user has a role. 
    * Returns true if a match was found.
    *
    * @param string $role Role name.
    * @param int $user_id (Optional ) The ID of a user. Defaults to the current user.
    * @return bool
    */
    public function rbhn_current_user_has_role( $role, $user_id = null ) {

           if ( is_numeric( $user_id ) ) {
                   $user = get_userdata( $user_id );
           } else {
                   $user = wp_get_current_user( );
           }
           if ( empty( $user ) ) {
                   return false;
           }
           return in_array( $role, ( array ) $user->roles );
    }
	
	
	/**
     * Creates or returns an instance of this class.
     *
     * @return   A single instance of this class.
     */
    public static function get_instance() {
 
        if ( null == self::$instance ) {
            self::$instance = new self;
        }
 
        return self::$instance;
 
    }		
}

/**
 * Init URE_OVERRIDE class
 */
 
RBHN_EXCLUDER::get_instance();


?>