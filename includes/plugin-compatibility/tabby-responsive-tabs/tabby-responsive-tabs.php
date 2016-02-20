<?php

/* tabby-responsive-tabs 
 * 
 * If tabby-responsive-tabs is installed and active and tabbed helpnotes are 
 * selected in the settings then the main rbhn class will provide a tabbed 
 * contents page.
 */




/* If tabby-responsive-tabs is installed and selected in settings to handle the Help Notes
 * contents page listing then we hook into the available fitlers here
 */

if (  get_option( 'rbhn_tabbed_contents_page' ) ) {
    //add_filter( 'rbhn_contents_page_before_listing', 'rbhn_tabby_contents_page_before_listing', 10 );
    add_filter( 'rbhn_contents_page_role_listing_title', 'rbhn_tabby_contents_page_role_listing_title', 10, 2 );
    add_filter( 'rbhn_contents_page_role_listing', 'rbhn_tabby_contents_page_role_listing', 10 );
    add_filter( 'rbhn_contents_page_role_final_listing', 'rbhn_tabby_contents_page_role_final_listing', 10 );
    add_action( 'wp_enqueue_scripts', 'rbhn_tabby_contents_page_scripts');        

    // add tabby_tab to URL arguments
    add_action( 'rbhn_create_content_section', 'rbhn_tabby_contents_section', 10, 3 );

        
}


function rbhn_tabby_contents_page_role_listing_title( $value, $posttype_Name  ) {
    $content = '[tabby title="' . $posttype_Name . '"]';
    return $content ;
}

function rbhn_tabby_contents_page_role_listing( $value  ) {
    $content = $value;
    return $content ;
}

function rbhn_tabby_contents_page_role_final_listing( $value  ) {
    $content = do_shortcode( $value . '[tabbyending]' );
    return $content ;
}

function rbhn_tabby_contents_section( $posttype_selected, $posttype_Name, $section_counter ) {
    
    // stop redirect looping
    if ( isset( $_GET['tabby_tab'] ) && ( $_GET['tabby_tab'] == 'tab' . $section_counter ) ) {
        return;
    }   

    if ( isset( $_GET['post_type'] ) ) {
        $query_post_type = $_GET['post_type'];
        $query_post_type = str_replace('MyPostType', '',  $query_post_type  ) ;  // a prefix was necessary here as the post_type on its own in the 
                                                                                 // URL arguments is a valid query parameter that WordPress would 
                                                                                 // interpret and use causing a 404 error
        if ( $posttype_selected == $query_post_type ) {
            // add tabby section id to the url arguments for the js goto to pickup
            remove_query_arg( 'tabby_tab' );
            wp_redirect( add_query_arg( array( 'tabby_tab' => 'tab' . $section_counter ) ) );
            exit;	
        }        
    }
    
}

function rbhn_tabby_contents_page_scripts() {

    //Add java content to the Contents Page to jump to the reference help note tab. 
    $contents_page_id = get_option( 'rbhn_contents_page' ) ;

    if ( is_page( $contents_page_id ) ) {

        if ( ! isset( $_GET['post_type'] ) ) {
                        return ;
        }

        $role_based_help_notes = RBHN_Role_Based_Help_Notes::get_instance( );
        
        // enqueue the java script to jump to the correct HelpNotes section on the contents page
        wp_enqueue_script(
                'contents-page-goto-tabby-tab', 
                plugins_url( 'js/contents-page-goto-tabby-tab.js' , __FILE__ ),
                array('jquery', 'tabby'), 
                $role_based_help_notes->plugin_get_version( ), 
                true
                );
    }
    
}