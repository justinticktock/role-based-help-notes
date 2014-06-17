<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

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
 * @since 1.2.1
 */
function rbhn_register_widgets() {

    // option collection  
	$option = get_option('rbhn_user_widget_enabled');  

    /* If the user widget is enabled. */    
    if ( isset( $option ) && !empty( $option ) ) {

		/* Load the user widget file. */
		require_once( HELP_MYPLUGINNAME_PATH . 'includes/class-users-widget.php' );

		/* Register the user widget. */
		register_widget( 'rbhn_users_widget' );
	}
}

?>