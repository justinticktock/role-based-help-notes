<?php
/**
 * This function removes the bbc default functionality of email-users
 * it forces the sent emails to use the TO and not the BCC email header 
 * so that recpients of the email can reply-all.
 *
 */
function mailusers_rbhn_headers($to, $headers, $bcc)
{
    //  Copy the BCC headers to the TO header without the "Bcc:" prefix
    $to = preg_replace('/^Bcc:\s+/', '', $bcc) ;

    //  Empty out the BCC header
    $bcc = array() ;

    return array($to, $headers, $bcc) ;
}

$role_based_help_notes = RBHN_Role_Based_Help_Notes::get_instance( );
$help_note_roles = $role_based_help_notes->help_notes_role( );
        
// add conditionals for the filter moving email addresses from  BCC > TO
$disable_bcc = get_option( 'rbhn_disable_bcc' );
if ( $disable_bcc ) {
    if ( isset($_POST['send_targets']) && is_array($_POST['send_targets']) && count($_POST['send_targets']) == 1 ) {    // limit to only where one group is selected on the group email page
                                                                                                                        // and the email is "To" only one group.          
        $selected_email_users_group = $_POST['send_targets'];
        $send_2_role = preg_replace('/role-/', '', $selected_email_users_group);
        $send_2_role = array_values( $send_2_role );
        $send_2_role = array_shift( $send_2_role );
         if ( in_array( $send_2_role, $help_note_roles ) ) {  // and if the group/role has help notes enabled
            add_filter('mailusers_manipulate_headers', 'mailusers_rbhn_headers', 10, 3) ;  
        }     
    }
}


/**
 * Remove the standard email-users meta box on the help notes edit admin screen
 * this is because Help Notes are to be private and not immediately presented
 * with the option to email others without the role.
 *
 */
function rbhn_email_users_remove_metabox( $current_screen ) {
    
    $role_based_help_notes = RBHN_Role_Based_Help_Notes::get_instance( );
    if ( in_array( $current_screen->post_type, $role_based_help_notes->site_help_notes( ) ) 
            && ( 'post' == $current_screen->base  ) 
        ) {
        remove_action( 'submitpost_box', 'mailusers_post_relatedlink' );
    }
}
add_action( 'current_screen', 'rbhn_email_users_remove_metabox' );