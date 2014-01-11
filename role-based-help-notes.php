<?php
/*
Plugin Name: Role Based Help Notes
Plugin URI: http://justinandco.com/plugins/role-based-help-notes/
Description: The addition of Custom Post Type to cover site help notes
Version: 1.2.5
Author: Justin Fletcher
Author URI: http://justinandco.com
License: GPLv2 or later
Copyright 2013  (email : justin@justinandco.com)
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Define constants
define( 'HELP_MYPLUGINNAME_PATH', plugin_dir_path(__FILE__) );
define( 'HELP_MYPLUGINNAME_FULL_PATH', HELP_MYPLUGINNAME_PATH . 'role-based-help-notes.php' );
define( 'HELP_PLUGIN_URI', plugins_url('', __FILE__) );
define( 'HELP_SETTINGS_PAGE', 'notes-settings');



/* Includes... */

// plugin registration
require_once( HELP_MYPLUGINNAME_PATH . 'includes/register.php' );  

// settings 
require_once( HELP_MYPLUGINNAME_PATH . 'includes/settings.php' );  

// custom post type capabilities
require_once( HELP_MYPLUGINNAME_PATH . 'includes/capabilities.php' );  

// if selected install the plugings and force activation
require_once( HELP_MYPLUGINNAME_PATH . 'includes/install-plugins.php' );    

// Load the widgets functions file.
require_once( HELP_MYPLUGINNAME_PATH . 'includes/widgets.php' );

// Load code for better compatibility with other plugins.
require_once( HELP_MYPLUGINNAME_PATH . 'includes/plugin-compatibility.php' );

// A settings page to the admin acitve plugin listing
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'role_based_help_notes_action_links' );

function role_based_help_notes_action_links( $links ) {
    array_unshift( $links, '<a href="options-general.php?page=' . HELP_SETTINGS_PAGE . '">' . __( 'Settings' ) . "</a>" );
    return $links;
}

// Attached to admin_init. Loads the textdomain and the upgrade routine.
add_action( 'admin_init', 'rbhn_admin_init' );
			
function rbhn_admin_init() {

	$settings_options = get_option('help_note_option');

	if ( empty($settings_options) || ! isset( $settings_options['help_notes_version'] ) || $settings_options['help_notes_version'] < plugin_get_version() ) {
	
		$current_plugin_version = isset( $settings_options['help_notes_version'] ) ? $settings_options['help_notes_version'] : 0;

		rbhn_upgrade( $current_plugin_version );

		// set default options if not already set..
		help_do_on_activation();
		
		// Collect option again after rbhn_upgrade() changes and set the current plugin revision
		$settings_options = get_option('help_note_option');
		$settings_options['help_notes_version']  = plugin_get_version();
		update_option('help_note_option', $settings_options); 
	}
		
	load_plugin_textdomain('role-based-help-notes-text-domain', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}



function rbhn_upgrade( $current_plugin_version ) {

	if ( $current_plugin_version < '1.2.4' ) {
		$settings_options = get_option('help_note_option');  	

		if (  ! empty( $settings_options['help_note_post_types'] ) ) {
			$new_help_note_post_types = array();
			foreach( $settings_options['help_note_post_types'] as $selected_key=>$role_selected) {
				$new_entry = array();
				$new_entry[$role_selected] = clean_post_type_name($role_selected);
				$new_help_note_post_types[] = $new_entry;
			}
		}
		
		// convert option format
		$settings_options['help_note_post_types'] = $new_help_note_post_types;
		update_option('help_note_option', $settings_options); 
	}

	if ( $current_plugin_version < '1.2.5' ) {
		$widget_options = get_option('widget_users_widget');
		$widget_options[2]['title'] = '';
		update_option('widget_users_widget', $widget_options); 
	}		
}
	
// Returns the selected-active Help Note Custom Post Types

function rbhn_active_posttypes() {

    $active_posttypes = array();

    //  loop through the site roles and create a custom post for each
	global $wp_roles;
    
    // Load roles if not set
	if ( ! isset( $wp_roles ) )
		$wp_roles = new WP_Roles();

	$roles = $wp_roles->get_names();


	// option collection  
	$settings_options = get_option('help_note_option');  
    	
   if ( isset( $settings_options['help_note_general_enabled'] ) && !empty( $settings_options['help_note_general_enabled'] ) ) {
    
		$active_posttypes[] = "h_general"; 
	}
	
	if (  ! empty($settings_options ) ) {	
		foreach( $settings_options['help_note_post_types'] as $array) {
			foreach( $array as $active_role=>$active_posttype) {
                if (current_user_can( $active_role )) {
				    $active_posttypes[] = $active_posttype;
                }				
			}
		}	
	}
    
    return $active_posttypes;
}



// register the selected-active Help Note post types

add_action( 'init', 'help_register_multiple_posttypes' );

function help_register_multiple_posttypes() {
    
    // option collection  
	$settings_options = get_option('help_note_option'); 

	if ( isset( $settings_options['help_note_general_enabled'] ) && ! empty( $settings_options['help_note_general_enabled'] ) ) {
		// generate a genetic help note post type
        call_user_func_array( 'help_register_posttype', array("general", "General", "h_general") );  
	}
	 
    
	//  loop through the site roles and create a custom post for each
	global $wp_roles;
    
    // Load roles if not set
	if ( ! isset( $wp_roles ) )
		$wp_roles = new WP_Roles();

	$roles = $wp_roles->get_names();

	if (  ! empty($settings_options ) ) {
		foreach( $settings_options['help_note_post_types'] as $array) {	
			foreach( $array as $active_role=>$active_posttype) {
				if (array_key_exists ($active_role, $roles)) {
					call_user_func_array( 'help_register_posttype', array($active_role, $roles[$active_role], $active_posttype) ); 
				} 
			}
		}
	}
}

// Adds custom post type for a single Help Note

function help_register_posttype($role_key, $role_name, $post_type_name) {
	
    $role_name = (strcasecmp("note", $role_name) ?  $role_name . ' ' : '' );

	$help_labels = array(

		'name'               => sprintf( __( '%1$s Notes', 'role-based-help-notes-text-domain'), $role_name) ,
		'singular_name'      => sprintf( __( '%1$s Note', 'role-based-help-notes-text-domain'), $role_name) ,
		'add_new'            => __( 'Add New', 'role-based-help-notes-text-domain'),
		'add_new_item'       => sprintf( __( 'Add New %1$s Note', 'role-based-help-notes-text-domain'), $role_name) ,
		'edit_item'          => sprintf( __( 'Edit %1$s Note', 'role-based-help-notes-text-domain'), $role_name) ,
		'new_item'           => sprintf( __( 'New %1$s Note', 'role-based-help-notes-text-domain'), $role_name) ,
		'view_item'          => sprintf( __( 'View %1$s Note', 'role-based-help-notes-text-domain'), $role_name) ,
		'search_items'       => sprintf( __( 'Search %1$s Notes', 'role-based-help-notes-text-domain'), $role_name) ,
		'not_found'          => sprintf( __( 'No %1$s Notes found', 'role-based-help-notes-text-domain'), $role_name) ,
		'not_found_in_trash' => sprintf( __( 'No %1$s Notes found in Trash', 'role-based-help-notes-text-domain'), $role_name) ,
		'parent_item_colon'  => '',
		'menu_name'          =>  $role_name,
	);
	
	if ($role_key == "general" ) {
		$help_capabilitytype    = 'post';	
	} else {
		$help_capabilitytype    = $post_type_name;
	};
    
    global $wp_version;
    if (version_compare($wp_version, '3.8', '>=')) {  
        //if version 3.8 or high we have dashicon support.
		$help_menu_icon    = apply_filters( 'rbhn_dashicon', 'dashicons-format-aside') ;	
	} else {
		$help_menu_icon    = HELP_PLUGIN_URI . '/images/help.png' ;
	};

	$help_public = true;
	        
	$help_args = array(

		'labels'              => $help_labels,
		'public'              => $help_public,  // true implies the members 'content permissions'
										        // meta box is available.
		'publicly_queryable'  => $help_public,
		'exclude_from_search' => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
        'show_in_admin_bar'   => true,
		'capability_type'     => $help_capabilitytype,
		'map_meta_cap'        => true,
		'hierarchical'        => true,
		'supports'            => array( 'title', 'editor', 'comments', 'thumbnail', 'page-attributes' , 'revisions', 'author' ),
		'has_archive'         => true,
		'rewrite'             => true,
		'query_var'           => true,
		'can_export'          => true,
		'show_in_nav_menus'   => false,
    	'menu_icon'			  => $help_menu_icon,
        
	);

	register_post_type( $post_type_name, $help_args );

}

// Add Contents Details to the Contents page if declared in settings ..
add_filter('the_content', 'rbhn_add_post_content');

function rbhn_add_post_content($content) {
    
    global $post;
    
    // option collection  
    $settings_options = get_option('help_note_option');  
    
    //http://pippinsplugins.com/playing-nice-with-the-content-filter/
    if ( ($settings_options['help_note_contents_page'] != "0") && is_page($settings_options['help_note_contents_page'])  && is_main_query() ) {
        
        $active_role_notes = rbhn_active_posttypes();
        
        foreach( $active_role_notes as $posttype_selected) {
            
            $posttype = get_post_type_object( $posttype_selected );
            $posttype_Name = $posttype->labels->name; 
        
           
            $args = array(
                        'depth'        => 0,
                        'show_date'    => '',
                    	'date_format'  => get_option('date_format'),
                    	'child_of'     => 0,
                    	'exclude'      => '',
                    	'include'      => '',
                    	'title_li'     =>  "$posttype_Name",
                    	'echo'         => 0,
                    	'authors'      => '',
                    	'sort_column'  => 'menu_order, post_title',
                    	'link_before'  => '',
                    	'link_after'   => '',
                    	'walker'       => '',
                    	'post_type'    => "$posttype_selected",
                        'post_status'  => 'publish' 
                    );
                    
            $content =  $content . '<p>' . wp_list_pages( $args ) . '</p>';
        }
	}
	return $content;
}

function clean_post_type_name($role_key) {
    // limit to 20 characters length for the WP limitation of custom post type names
    $post_type_name = sanitize_key('h_' . substr($role_key , -18)); 
    return $post_type_name;
}

/**
* Returns current plugin version.
*
* @return string Plugin version
*/
function plugin_get_version() {
	$plugin_data = get_plugin_data( HELP_MYPLUGINNAME_FULL_PATH );
	$plugin_version = $plugin_data['Version'];
	return $plugin_version;
}

?>