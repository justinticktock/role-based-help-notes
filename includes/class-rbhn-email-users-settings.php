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
                                                            'title' 		=> __( 'Email Groups', 'role-based-help-notes-text-domain' ),
                                                            'description' 	=> __( 'Enable the roles to allow group email functionality.', 'role-based-help-notes-text-domain' ),
                                                            'form_action'       => admin_url( 'admin-post.php' ),
                                                            'settings' 		=> array(														
                                                                                        array(
                                                                                                'name' 		=> 'rbhn_enable_email_users_roles',
                                                                                                'std' 		=> array(),
                                                                                                'label' 	=> __( 'Add User Role(s)', 'role-based-help-notes-text-domain' ),
                                                                                                'desc'		=> __( 'Enable <strong>email_user_groups</strong> custom capability for individual roles, this will then be used by the <strong>email-users</strong> to enable group emailing.', 'role-based-help-notes-text-domain' ),
                                                                                                'type'      => 'field_roles_for_group_email_checkbox',
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
            $new_settings = array_merge ( (array)$new_settings, (array)$plugin_extension_array );		
            return 	$new_settings;
    }


    public function field_roles_for_group_email_custom_save( ) {

        // authenticate
        $_nonce = isset( $_POST['rbhn_email_user_groups_nonce'] ) ? $_POST['rbhn_email_user_groups_nonce'] : '';

        if ( ! wp_verify_nonce( $_nonce , 'rbhn_email_user_groups' ) ) { 
           wp_die( __( 'You do not have permission.', 'role-based-help-notes-text-domain' ) );
        }

        $option_name = 'rbhn_enable_email_users_roles';

        if ( isset ( $_POST[ $option_name ] ) ) {
            update_option( $option_name, $_POST[ $option_name ] );
            $msg = 'updated';
        } else {
            delete_option( $option_name );
                        //wp_die( $_POST[ $option_name ] );
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
        foreach ( $roles as $role_key=>$_rolename ) {
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
				$value = ( array ) get_option( $option['name'] );

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
			if ( ! empty( $option['desc'] ) )
				echo ' <p class="description">' . $option['desc'] . '</p>';		
		}
	/**
	 * @param array of arguments to pass the option name to render the form field.
	 * @access public
	 * @return void
	 */
	public function field_editable_roles_checkbox( array $args  ) {

        $redirect = urlencode( remove_query_arg( 'msg', $_SERVER['REQUEST_URI'] ) );
        $redirect = urlencode( $_SERVER['REQUEST_URI'] );
	 
		$option   = $args['option'];
		$roles = get_editable_roles( );

		if ( isset( $_GET['user_id'] ) ) {
			$user_id = $_GET['user_id'];
			$user = new WP_User( $user_id );
			$current_user_roles = (array) $user->roles;
			
			?><H2><?php

			?><H2</br	><?php
			esc_html_e( sprintf( __( 'Roles for %1$s :', 'role-based-help-notes-text-domain' ), $user->display_name ) );
			?> </H2><?php
			
			?><ul><?php 
			asort( $roles );

			?>
			
			  
				<input type="hidden" name="action" value="<?php echo $option['name']; ?>">
		
				<?php 
					 
				foreach( $roles as $role_key=>$_rolename )
				{
					$id = sanitize_key( $role_key );
					$value = get_option( $option['name'] );

					// Render the output  
					?> 
				

					<input type='checkbox'  
						id="<?php echo esc_html( "user_role_{$id}" ) ; ?>" 
						name="<?php echo esc_html( $option['name'] ); ?>[]"
						value="<?php echo esc_attr( $role_key )	; ?>"<?php checked( in_array( $role_key, $current_user_roles ) ); ?>
					>
					<input type="hidden" name="user_id" value="<?php echo $user_id; ?>">					
					
					<?php echo esc_html( $_rolename['name'] ); ?></label>	
					<br/></li>
					<?php 
				}?></ul>
				 <?php // submit_button( 'Send' ); ?>
	 
			<?php
			
		
		if ( ! empty( $option['desc'] ) )
			echo ' <p class="description">' . $option['desc'] . '</p>';			
			
		} else {  // no user_id
		
		echo '<a href="' . admin_url( 'users.php' ) . '">' . __( 'Select a users [Roles] option under their name.', 'role-based-help-notes-text-domain' ) . "</a>";
		
		}
		

	}
		

		
}



		
?>