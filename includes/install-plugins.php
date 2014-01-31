<?php

/**
 * This file represents an example of the code that themes would use to register
 * the required plugins.
 *
 * It is expected that theme authors would copy and paste this code into their
 * functions.php file, and amend to suit.
 *
 * @package       TGM-Plugin-Activation
 * @subpackage Example
 * @version	   2.3.6
 * @author	   Thomas Griffin <thomas@thomasgriffinmedia.com>
 * @author	   Gary Jones <gamajo@gamajo.com>
 * @copyright  Copyright (c) 2012, Thomas Griffin
 * @license	   http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 * @link       https://github.com/thomasgriffin/TGM-Plugin-Activation
 */

/**
 * Include the TGM_Plugin_Activation class.
 */
 
require_once(dirname( __FILE__ ) . '/class-tgm-plugin-activation.php');


function help_note_register_required_plugins() {
    
    $plugins = array();
    
    // First, we read the option collection  
	$options = get_option('help_note_option'); 
	
    if ( isset($options['help_note_simple_page_ordering']) && $options['help_note_simple_page_ordering'] ) {
        $plugins[] = array(
                        'name'              	=> 'Simple Page Ordering',
                        'slug'      			=> 'simple-page-ordering',
                        'required'              => false, // If false, the plugin is only 'recommended' instead of required
            			'force_deactivation' 	=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
                        'force_activation'      => true, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
                        );
	}   
       
    if ( isset($options['help_note_simple_footnotes_plugin']) && $options['help_note_simple_footnotes_plugin'] ) {
        $plugins[] = array(
                        'name'              	=> 'Simple Footnotes',
                        'slug'      			=> 'simple-footnotes',
                        'required'              => false, // If false, the plugin is only 'recommended' instead of required
            			'force_deactivation' 	=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
                        'force_activation'      => true, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
                        );
	}   
       
    if ( isset($options['help_note_disable_comments_plugin']) && $options['help_note_disable_comments_plugin'] ) {
        $plugins[] = array(
                        'name'              	=> 'Disable Comments',
                        'slug'      			=> 'disable-comments',
                        'required'              => false, // If false, the plugin is only 'recommended' instead of required
            			'force_deactivation' 	=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
                        'force_activation'      => true, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
                        );
	}   

    if ( isset($options['help_note_email_post_changes_plugin']) && $options['help_note_email_post_changes_plugin'] ) {
        $plugins[] = array(
                        'name'          		=> 'Email Post Changes',
                        'slug'      			=> 'email-post-changes',
                        'required'              => false, // If false, the plugin is only 'recommended' instead of required
            			'force_deactivation' 	=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
                        'force_activation'      => true, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
                        );
	}                            

    if ( isset($options['help_note_post_type_switcher_plugin']) && $options['help_note_post_type_switcher_plugin'] ) {
        $plugins[] = array(
                        'name'          		=> 'Post Type Switcher',
                        'slug'      			=> 'post-type-switcher',
                        'required'              => false, // If false, the plugin is only 'recommended' instead of required
            			'force_deactivation' 	=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
                        'force_activation'      => true, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
                        );
	} 			 
			 
    if ( isset($options['help_note_menu_plugin']) && $options['help_note_menu_plugin'] ) {
        $plugins[] = array(
                        'name'          		=> 'Post type archive in menu',
                        'slug'      			=> 'post-type-archive-in-menu',
                        'required'              => false, // If false, the plugin is only 'recommended' instead of required
            			'force_deactivation' 	=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
                        'force_activation'      => true, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
                        );
	}                            
                    
    return $plugins;
}


add_action( 'tgmpa_register', 'help_note_tgmpa_register' );

function help_note_tgmpa_register() {

	/**
	 * Array of plugin arrays. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 */
	$plugins = help_note_register_required_plugins();


	// Change this to your theme text domain, used for internationalising strings
	$theme_text_domain = 'role-based-help-notes-text-domain';

	/**
	 * Array of configuration settings. Amend each line as needed.
	 * If you want the default strings to be available under your own theme domain,
	 * leave the strings uncommented.
	 * Some of the strings are added into a sprintf, so see the comments at the
	 * end of each line for what each argument will be.
	 */
	$config = array(
		'domain'       		=> $theme_text_domain,         	// Text domain - likely want to be the same as your theme.
		'default_path' 		=> '',                         	// Default absolute path to pre-packaged plugins
		'parent_menu_slug' 	=> 'plugins.php', 				// Default parent menu slug
		'parent_url_slug' 	=> 'plugins.php', 				// Default parent URL slug
		'menu'         		=> 'install-required-plugins', 	// Menu slug
		'has_notices'      	=> true,                       	// Show admin notices or not
		'is_automatic'    	=> false,					   	// Automatically activate plugins after installation or not
		'message' 			=> '',							// Message to output right before the plugins table
		'strings'      		=> array(
			'page_title'                       			=> __( 'Install Required Plugins', $theme_text_domain ),
			'menu_title'                       			=> __( 'Install Plugins', $theme_text_domain ),
			'installing'                       			=> __( 'Installing Plugin: %s', $theme_text_domain ), // %1$s = plugin name
			'oops'                             			=> __( 'Something went wrong with the plugin API.', $theme_text_domain ),
			'notice_can_install_required'     			=> _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.' ), // %1$s = plugin name(s)
			'notice_can_install_recommended'			=> _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.' ), // %1$s = plugin name(s)
			'notice_cannot_install'  					=> _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ), // %1$s = plugin name(s)
			'notice_can_activate_required'    			=> _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
			'notice_can_activate_recommended'			=> _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
			'notice_cannot_activate' 					=> _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ), // %1$s = plugin name(s)
			'notice_ask_to_update' 						=> _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.' ), // %1$s = plugin name(s)
			'notice_cannot_update' 						=> _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ), // %1$s = plugin name(s)
			'install_link' 					  			=> _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
			'activate_link' 				  			=> _n_noop( 'Activate installed plugin', 'Activate installed plugins' ),
			'return'                           			=> __( 'Return to Required Plugins Installer', $theme_text_domain ),
			'plugin_activated'                 			=> __( 'Plugin activated successfully.', $theme_text_domain ),
			'complete' 									=> __( 'All plugins installed and activated successfully. %s', $theme_text_domain ), // %1$s = dashboard link
			'nag_type'									=> 'updated' // Determines admin notice type - can only be 'updated' or 'error'
		)
	);
	
	tgmpa( $plugins, $config );
	
    // remove the action in-case other plugins/themes have also used tgmpa_register
    remove_action( 'tgmpa_register', 'help_note_tgmpa_register' );
}