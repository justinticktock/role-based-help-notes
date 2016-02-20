<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * RBHN_Email_Users_Settings class.
 */
class RBHN_Email_Users_Settings {

	// Refers to a single instance of this class.
    private static $instance = null;
	
    /**
     * __construct function.
     *
     * @access public
     * @return void
     */	 
    private function __construct() {

            // add the new extra settings to the option pages
            add_filter( 'rbhn_settings', array( $this, 'register_email_users_settings' ), 10, 1 );

            // hook to save the role settings into the WP capabilities for roles.
            add_action( 'admin_post_' . 'rbhn_enable_email_users_roles', array ( $this, 'field_roles_for_group_email_custom_save' ) );	
                  
    }

    /**
     * register_extra_settings function.
     *
     * @access public
     * @return void
     */
    public function register_email_users_settings( $settings ) {

            //$license 	= get_option( 'rbhne_license_key' );
            //$status 	= get_option( 'rbhne_license_status' );

            $rbhn_email_users_settings = 	array(
                                                    'rbhn_email_user_groups' => array( 
                                                            'access_capability' => 'promote_users',
                                                            'title' 		=> __( 'Email Roles', 'role-based-help-notes' ),
                                                            'description' 	=> __( 'Allow group email functionality, this uses the "Email Users" Plugin.', 'role-based-help-notes' ),
                                                            'form_action'       => admin_url( 'admin-post.php' ),
                                                            'settings' 		=> array(													
                                                                                        array(
                                                                                                'name' 		=> 'rbhn_enable_email_users_roles',
                                                                                                'std' 		=> array(),
                                                                                                'label' 	=> __( 'Add User Role(s)', 'role-based-help-notes' ),
                                                                                                'desc'		=> __( 'Enables the <strong>email_user_groups</strong> custom capability for individual roles, this will then be used by the <strong>email-users</strong> to enable group emailing.', 'role-based-help-notes' ),
                                                                                                'type'          => 'field_roles_for_group_email_checkbox',
                                                                                            ),					
                                                                                        ),		
                                                    ),
                                                    'rbhn_email_to_not_bcc' => array( 
                                                            'access_capability' => 'manage_options',
                                                            'title' 		=> __( 'Email Options', 'role-based-help-notes' ),
                                                            'description' 	=> __( 'Further options for the "Email Users" Plugin.', 'role-based-help-notes' ),
                                                            'settings' 		=> array(											
                                                                                        array(
                                                                                                'name' 		=> 'rbhn_disable_bcc',
                                                                                                'std' 		=> true,
                                                                                                'type'          => 'field_checkbox_option',
                                                                                                'label' 	=> __( '<strong>BCC</strong> > <strong>TO</strong>', 'role-based-help-notes' ),
                                                                                                'desc'		=> __( 'The <strong>email-users</strong> plugin by default places all group emails into <strong>BCC</strong>, enabling this option will move email addresses from  <strong>BCC</strong> > <strong>TO</strong>. This will allow the recipients of Help Note roles to reply all.', 'role-based-help-notes' ),
                                                                                            ),					
                                                                                        ),		
                                                    ),                
                                    );	

            $new_settings = array_merge ( (array) $settings, (array)$rbhn_email_users_settings );

            // Move plugin extensions tab to the end.
            $plugin_extension_tab = $new_settings['rbhn_plugin_extension'];
            unset($new_settings['rbhn_plugin_extension']);
            $plugin_extension_array = array();
            $plugin_extension_array['rbhn_plugin_extension'] = $plugin_extension_tab;		
            $final_settings = array_merge ( (array)$new_settings, (array)$plugin_extension_array );		
            return $final_settings;
    }


    public function field_roles_for_group_email_custom_save( ) {

        // authenticate
        $_nonce = isset( $_POST['rbhn_email_user_groups_nonce'] ) ? $_POST['rbhn_email_user_groups_nonce'] : '';

        if ( ! wp_verify_nonce( $_nonce , 'rbhn_email_user_groups' ) ) { 
           wp_die( __( 'You do not have permission.', 'role-based-help-notes' ) );
        }

        $option_name = 'rbhn_enable_email_users_roles';

        if ( isset ( $_POST[ $option_name ] ) ) {
            update_option( $option_name, $_POST[ $option_name ] );
            $msg = 'updated';
        } else {
            delete_option( $option_name );
            $msg = 'deleted';
        }


        $url = add_query_arg( 'msg', $msg, urldecode( $_POST['_wp_http_referer'] ) );


        if ( ! defined( 'get_editable_roles' ) ) {
                require_once( ABSPATH.'wp-admin/includes/user.php' );
        }

        // Collect all WP roles
        global $wp_roles;
 
        if ( ! isset( $wp_roles ) ) {
            $wp_roles = new WP_Roles( );
        }
        
        // collect an array of all roles names
        $roles = $wp_roles->get_names( );
        
        // collect our option for the roles to include the email_user_groups custom capability 
        $new_roles = get_option( $option_name );

        // set the capabilities
        foreach ( array_keys( $roles ) as $role_key ) {
            if ( in_array( $role_key, $new_roles ) ) {
                $role = get_role( $role_key );
                $role->add_cap( 'email_user_groups' );
            } else {
                $wp_roles->remove_cap( $role_key, 'email_user_groups' );
            }
        }

        wp_safe_redirect( $url );
        exit;

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

// Create new tabbed settings object for this plugin..
// and Include additional functions that are required.
RBHN_Email_Users_Settings::get_instance();


/**
 * RBHN_Email_Users_Settings_Additional_Methods class.
 */
class RBHN_Email_Users_Settings_Additional_Methods {

	
		/**
		 * field_roles_checkbox 
		 *
		 * @param array of arguments to pass the option name to render the form field.
		 * @return void
		 */
		public function field_roles_for_group_email_checkbox( array $args  ) {

			$option   = $args['option'];

			//  loop through the site roles and create a custom post for each
			global $wp_roles;
			
			if ( ! isset( $wp_roles ) ) {
				$wp_roles = new WP_Roles( );
			}

			$roles = $wp_roles->get_names( );
			unset( $wp_roles );
			
			?><ul><?php 
			asort( $roles );
                        
                        // this is necessary for the admin-post.php hook to find the element
                        ?><input type="hidden" name="action" value="<?php echo $option['name']; ?>"><?php
                                                            
			foreach( $roles as $role_key=>$role_name )
			{
                            $role = get_role( $role_key );
                            $id = sanitize_key( $role_key );

                            // Render the output  
                            ?> 
                            <li><label>
                            <input type='checkbox'  
                                    id="<?php echo esc_html( "exclude_enabled_{$id}" ) ; ?>" 
                                    name="<?php echo esc_html( $option['name'] ); ?>[]"
                                    value="<?php echo esc_attr( $role_key )	; ?>"<?php checked( $role->has_cap( 'email_user_groups' ) ) ;?>

                            >
                            <?php echo esc_html( $role_name ) . " <br/>"; ?>	
                            </label></li>
                            <?php 
			}?></ul><?php 
			if ( ! empty( $option['desc'] ) ) {
				echo ' <p class="description">' . $option['desc'] . '</p>';
                        }
		}
	
}



/**
 * RE_EXCLUDER class.
 */
class RBHN_EMAIL_GROUPS {

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
    
    
    /**
     * if on the admin page "mailusers-send-to-group-page" that the email-users
     * plugin provides this function will return a filtered $editable_roles 
     * based on the current users roles and "email_user_groups" capability 
     *
     * @return   A single instance of this class.
     */
    public function exclude_role_from_user( $editable_roles ) {        

        // drop out if not on the group emails page.
         if ( ! is_admin() || ! ( isset( $_GET['page'] ) && ( $_GET['page'] == 'mailusers-send-to-group-page' ) ) )  {
             return $editable_roles;
         }	
         

        // if url has passed the role use this
        // 
        // when the users.php admin page is shown with url argument 'optout' call function 'get_disabled_email_user_list'
        if ( isset( $_GET['helpnotetype'] ) && $_GET['helpnotetype']  ) {

            $help_note_type_passed = sanitize_key( $_GET['helpnotetype'] );
            // collect the active help note post types
            $role_based_help_notes = RBHN_Role_Based_Help_Notes::get_instance();
            $passed_role = $role_based_help_notes->help_notes_role( $help_note_type_passed );
                
        }	
       

        global $wp_roles;    
        
        if ( ! isset( $wp_roles ) ) {
                $wp_roles = new WP_Roles();
        }

        
        $roles_with_cap_email_user_groups = array();
        $current_user_assigned_roles = array( );
        
        
        /* Loop through each role object because we need to get the caps. */
        foreach ( $wp_roles->role_objects as $key => $role ) {
            
            /* build up the allowed roles for the current user */
            if ( $this->rbhn_current_user_has_role( $key ) && ( ( ! isset( $passed_role )) || ( isset( $passed_role ) && $passed_role == $key ) ) ) {
                $current_user_assigned_roles[] = $key;
                
                /* Roles without capabilities will cause an error, so we need to check if $role->capabilities is an array. */
                if ( is_array( $role->capabilities ) ) {

                    /* Loop through the current users role's and there capabilities to find roles with the 'email_user_groups' capabiltiy set. */
                    foreach ( $role->capabilities as $cap => $grant ) {
                        if ( ( $cap == 'email_user_groups' ) && $grant ) {
                             $roles_with_cap_email_user_groups[] = $key;
                             break;
                        }
                    }
                }
            }
            
        }        

         // now we have gathered all roles that are still allowed so now we will find the 
         // inverse to get an array of roles to be excluded
         $roles_all = array_keys( $wp_roles->get_names( ) );
         
         if ( $roles_all != $roles_with_cap_email_user_groups ) {

             // find roles not allowed for the current user
             $excluded_roles = array_diff( $roles_all, $roles_with_cap_email_user_groups );

             // exclude roles from $editable_roles
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
 
RBHN_EMAIL_GROUPS::get_instance();