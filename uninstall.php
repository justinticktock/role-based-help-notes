<?php

//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit ();
	
if (is_multisite()) {
    global $wpdb;
    $blogs = $wpdb->get_results("SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A);
    if ($blogs) {
        foreach($blogs as $blog) {
            switch_to_blog($blog['blog_id']);
            delete_option('help_note_option');
            rbhn_capability_clean_up();
        }
        restore_current_blog();
    }
} else {
    do_action( 'rbhn_remove_caps');  // clear out capabilities
    rbhn_capability_clean_up();
}

?>