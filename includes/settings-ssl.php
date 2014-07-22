<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SSL_Settings class.
 */
class RBHN_SSL_Settings {
    
	// Refers to a single instance of this class.
    private static $instance = null;
	
	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */	 
	private function __construct() {
	
		// check for active plugin to cater for the SSL
		if ( ! class_exists('ITSEC_SSL') )
			return;
			
		// add the new extra settings to the option pages
		add_filter( 'rbhn_settings', array( $this, 'register_rbhn_ssl_settings' ), 10, 1 );

		add_action( 'template_redirect', array( $this, 'rbhn_ssl_template_redirect' ), 9 );

	}
			
	/**
	 * register_extra_settings function.
	 *
	 * @access public
	 * @return void
	 */
	public function register_rbhn_ssl_settings( $settings ) {

		$rbhn_ssl_settings = array();

		$rbhn_ssl_settings = 	array(
								'rbhn_ssl' => array(	
									'title' 		=> __( 'SSL', 'role-based-help-notes-text-domain' ),
									'description' 	=> __( "The website front-end can be forced to use SSL (https://) for the Help Notes.", 'role-based-help-notes-extra-text-domain' ),
									'settings' 		=> array(		
															array(
																'name' 		=> 'rbhn_force_ssl',
																'label' 	=> __( 'Force SSL', 'role-based-help-notes-text-domain' ),
																'cb_label'  => __( 'Enable', 'role-based-help-notes-text-domain' ),
																'desc'		=> __( "The help note singular pages will be forced to use SSL (https://), this doesn't include the general notes type.", 'role-based-help-notes-extra-text-domain' ),
																'type'      => 'field_checkbox_option',
																),															
														),
								),						
							);	


		return 	array_merge ( (array)$settings, (array)$rbhn_ssl_settings );
	}

	/**
	 * create the redirect for help note pages.
	 *
	 * @access public
	 * @return void
	 */
	public function rbhn_ssl_template_redirect( ) {
			
		global $post;
		
		$role_based_help_notes = RBHN_Role_Based_Help_Notes::get_instance();
		
		if ( ! $role_based_help_notes->is_single_help_note() )
			return
		
		$secure_help_notes = false;

		if ( get_option('rbhn_force_ssl') ) {
			$secure_help_notes = true;
		}

		update_post_meta( $post->ID, 'itsec_enable_ssl', $secure_help_notes );
		
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
RBHN_SSL_Settings::get_instance();


?>