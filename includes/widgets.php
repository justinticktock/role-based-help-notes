<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Loads and enables the widgets for the plugin.
 *
 * @package Role Based Help Notes
 */

/* Hook widget registration to the 'widgets_init' hook. */
add_action( 'widgets_init', 'rbhn_register_widgets' );

/**
 * Registers widgets for the plugin.
 *
 */
function rbhn_register_widgets( ) {

    // option collection  
	$option = get_option( 'rbhn_widgets_enabled' );  

    /* If the user widget are enabled. */    
    if ( isset( $option ) && !empty( $option ) ) {

		/* Load the user widget file. */
		require_once( HELP_MYPLUGINNAME_PATH . 'includes/class-rbhn-users-widget.php' );
		
		/* Load the tag cloud widget file. */
		require_once( HELP_MYPLUGINNAME_PATH . 'includes/class-rbhn-tag-cloud-widget.php' );
		
		/* Register the widgets. */
		register_widget( 'rbhn_users_widget' );
		register_widget( 'rbhn_tag_cloud_widget' );
	}
	
	
}

?>