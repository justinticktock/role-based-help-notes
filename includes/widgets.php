<?php
/**
 * Loads and enables the widgets for the plugin.
 *
 * @package Members
 * @subpackage Functions
 */

/* Hook widget registration to the 'widgets_init' hook. */
add_action( 'widgets_init', 'rbhn_register_widgets' );

/**
 * Registers widgets for the plugin.
 *
 * @since 1.3.0
 */
function rbhn_register_widgets() {

    /* If the login form widget is enabled. */
	if ( true ) {

		/* Load the user listing widget file. */
		require_once( HELP_MYPLUGINNAME_PATH . 'includes/widget-users.php' );

		/* Register the login form widget. */
		register_widget( 'users_widget' );
	}

}

?>
