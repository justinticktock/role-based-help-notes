<?php

/**
 * Additional Compatibility with Plugins
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}



/**
 * Function provided to check if the current visitor has available Help Notes
 * @return False if no help notes are available for the current user, otherwise an array of help_note post types available.
 * Can be used also in other plugins - like 'Menu Items Visibility Control' plugin.
 */
if ( !function_exists( 'help_notes_available' ) ) {
	function help_notes_available( ) {
		
		$helpnote_post_types = array( );
		
		$role_based_help_notes = RBHN_Role_Based_Help_Notes::get_instance( ); 
			
		// check if no help notes are selected.
		$help_note_post_types =  get_option( 'rbhn_post_types' );

		if ( ! array_filter( ( array ) $help_note_post_types ) && ! get_option( 'rbhn_general_enabled' ) ) {
			return false;
                }

		// if the current user has the role of an active writeable Help Note.
		if ( array_filter( ( array ) $help_note_post_types ) ) {	
			foreach( $help_note_post_types as $array ) {
				foreach( $array as $active_role=>$active_posttype ) {
					if ( $role_based_help_notes->help_notes_current_user_has_role( $active_role ) ) {
						$helpnote_post_types[] = $active_posttype;
					}				
				}
			}	
		}   

		// General Help Notes 
                // we need to check here is there are actually any general help notes that exist
                // as they can be enabled but nothing writen yet and in this case having a menu
                // option for the user to help notes is not appicable.
                if ( get_option('rbhn_general_enabled)') ) {
                    $my_query = new WP_Query( array(
                            'post_type'     => array( 'h_general' ),
                            ) );

                    if ( $my_query->have_posts( ) ) {
                            $helpnote_post_types[] = 'h_general';
                            }

                    wp_reset_postdata( );
                }
		

		$final_helpnote_post_types = array_filter( $helpnote_post_types );
                
		if ( ! empty( $final_helpnote_post_types ) ) { 
			return $final_helpnote_post_types;
		} else {
			return false;
		}
	}
}
/* Role Based Help Notes Extra email-diff and SSL Support 
 *  
 * Load the entension plugin settings
 */

if ( is_admin() ) {
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    if ( is_plugin_active( 'role-based-help-notes-extra/role-based-help-notes-extra.php' ) || is_plugin_active_for_network( 'role-based-help-notes-extra/role-based-help-notes-extra.php' ) ) {
        require_once( RBHNE_PATH . 'includes/class-rbhne-settings.php' );
    }
}





/* tabby-responsive-tabs 
 * 
 * If tabby-responsive-tabs is installed and active and tabbed helpnotes are 
 * selected in the settings then the main rbhn class will provide a tabbed 
 * contents page once selected with the settings.
 */

/* load the is_plugin_active() method for use on the front of site as its only
 * available on the admin side by default
 */
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );


/* If tabby-responsive-tabs is installed and selected in settings to handle the Help Notes
 * contents page listing then we hook into the available fitlers here
 */

if ( ( is_plugin_active( 'tabby-responsive-tabs/tabby-responsive-tabs.php' ) || 
      is_plugin_active_for_network( 'tabby-responsive-tabs/tabby-responsive-tabs.php' ) ) 
    ) {

	/* Load the email users widget file. */
	require_once( HELP_MYPLUGINNAME_PATH . 'includes/plugin-compatibility/tabby-responsive-tabs/tabby-responsive-tabs.php' );
	
} else {
    add_filter( 'rbhn_settings', 'tabby_responsive_tabs_settings', 10, 1 );
}


function tabby_responsive_tabs_settings( $settings ) {
    
    foreach ( $settings['rbhn_general']['settings'] as $setting=>$options ) {
        //var_dump( $options );

        if ( isset( $options['name'] ) && ( $options['name'] == 'rbhn_tabbed_contents_page' ) ) {
            unset($settings['rbhn_general']['settings'][$setting]);
            break 1;
        }

    }
    return $settings;
}

// Plugin De-activation
function rbhn_tabby_tabs_deactivate( ) {
    update_option( 'rbhn_tabbed_contents_page', false );    
}

register_deactivation_hook( trailingslashit( HELP_PLUGIN_DIR . 'tabby-responsive-tabs' ) .  'tabby-responsive-tabs.php', 'rbhn_tabby_tabs_deactivate' );



/* buddypress 
 * 
 * If BuddyPress is installed and active then re-route the user page
 * to the BuddyPress profile page instead of the user archive of posts
 */
if ( defined( 'BP_ENABLE_ROOT_PROFILES' ) ) {
    // Load class for compatibilty with email-users plugin
    require_once( HELP_MYPLUGINNAME_PATH . 'includes/plugin-compatibility/buddypress/buddypress.php' );
}        

/* user-emails 
 * 
 * Load code to limit availalable/editable roles to limit email users groups availability.
 */

if ( is_plugin_active( 'email-users/email-users.php' ) || is_plugin_active_for_network( 'email-users/email-users.php' ) ) {
    
    require_once( HELP_MYPLUGINNAME_PATH . 'includes/plugin-compatibility/email-users/class-rbhn-email-users-group-settings.php' );
    require_once( HELP_MYPLUGINNAME_PATH . 'includes/plugin-compatibility/email-users/email-users-custom.php' );
    
    // option collection  
    $option = get_option( 'rbhn_widgets_enabled' );  

    /* If the widgets are enabled. */    
    if ( isset( $option ) && !empty( $option ) ) {

        /* Load the email users widget file. */
        require_once( HELP_MYPLUGINNAME_PATH . 'includes/plugin-compatibility/email-users/class-rbhn-email-users-widget.php' );
        
        /* Register the widget. */
        register_widget( 'rbhn_email_users_widget' );

    }

}