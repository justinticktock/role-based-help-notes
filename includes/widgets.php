<?php
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
	$settings_options = get_option('help_note_option');  

    /* If the user widget is enabled. */    
    if ( isset( $settings_options['user_widget_enabled'] ) && !empty( $settings_options['user_widget_enabled'] ) ) {

		/* Load the user widget file. */
		require_once( HELP_MYPLUGINNAME_PATH . 'includes/widget-users.php' );

		/* Register the user widget. */
		register_widget( 'users_widget' );
	}

}

?>
