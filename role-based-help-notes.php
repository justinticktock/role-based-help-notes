<?php
/**
Plugin Name: Role Based Help Notes
Plugin URI: http://justinandco.com
Description: The addition of Custom Post Type to cover site help notes
Version: 1.0
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

define( 'HELP_PLUGIN_VERSION', '1.0' );
define( 'HELP_MYPLUGINNAME_PATH', plugin_dir_path(__FILE__) );
define( 'HELP_PLUGIN_URI', plugins_url('', __FILE__) );

/*
Includes...
*/

require_once( HELP_MYPLUGINNAME_PATH . 'includes/capabilities.php' );   // <<< commented out for default meta_capabilities to take precedence.

// if selected install the plugings and force activation
$options = get_option('help_note_option'); 

if (isset($options['help_note_menu_plugin']) ) {

    // if option is ticked (true)
    if ( $options['help_note_menu_plugin'] ) {
        
        //include the TGM-Plugin-Activation CLASS
        require_once( HELP_MYPLUGINNAME_PATH . 'includes/class-tgm-plugin-activation.php');
        
        // install the plugins addition module
       require_once( HELP_MYPLUGINNAME_PATH . 'includes/install-plugins.php' );    

    }  
    
}

		
// Create the Help Top Level Menu Page
add_action( 'admin_menu', 'register_my_custom_help_menu_page' );

function register_my_custom_help_menu_page(){
    // give the help notes menu the same required access capability ('read') as used by the 'profile' and 'dashboard' menu items
    add_menu_page( 'Help menu title', 'Help Notes', 'read', 'helpmenu', '', plugins_url( 'role-based-help-notes/images/help.png' ), '5.9996' ); 
}

// Create a second level settings page
add_action('admin_menu', 'register_my_custom_submenu_page');

function register_my_custom_submenu_page() {
    add_submenu_page( 'helpmenu', 'Notes', 'Settings', 'manage_options', 'notes-settings', 'notes_settings_page_callback' ); 
}


function notes_settings_page_callback( $args = '' ) {
        extract( wp_parse_args( $args, array(
			'title'       => __( 'Help Notes Settings', 'help-notes' ),
            'options_group' => 'help_note_option_group',
            'options_key' => 'help_note_option'
		) ) );
		?>
		<div id="<?php echo $options_key; ?>" class="wrap">
			<?php screen_icon( 'options-general' ); ?>
			<h2><?php echo esc_html( $title ); ?></h2>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'help_note_option_group' );
					do_settings_sections( 'notes-settings' );
					submit_button();
				?>
			</form>
		</div>
		<?php
	}  // end help_note_settings_page_content  



/** 
 * Initializes the plugin's option by registering the Sections, 
 * Fields, and Settings. 
 * 
 * This function is registered with the 'admin_init' hook. 
 */   
 
add_action('admin_init', 'help_note_plugin_intialize_options' );  


function help_note_plugin_intialize_options() {  

    if ( get_option( 'rbhn_update_request' )) {
        
    	    update_option( 'rbhn_update_request', '' );
            help_do_on_activation();
            
    }


	
	register_setting(  
		'help_note_option_group',  		// A settings group name. Must exist prior to the register_setting call. 
										// This must match the group name in settings_fields()
		'help_note_option'   ,  		// The name of an option to sanitize and save. 
		'sanitize_help_note_option'  	// A callback function that sanitizes the option's value. 
	);  
				
	 add_settings_section(
		'help_note_post_types',        			// ID used to identify this section and with which to register option  
		'Help Note Post Types',            		// Title to be displayed on the administration page  
		'help_note_post_types_section_callback',  			// Callback used to render the description of the section  
		'notes-settings'      					// Page on which to add this section of option  
	);  

	add_settings_field(   
		'help_note_post_types',                 // String for use in the 'id' attribute of tags. 
		'Help Note Post Types:',               // Title of the field.   
		'settings_field_help_notes_post_types', // Function that fills the field with the desired inputs as part of the larger form. 
												// Passed a single argument, the $args array. Name and id of the input should match 
												// the $id given to this function. The function should echo its output. 
		'notes-settings',   					// The menu page on which to display this field. Should match $menu_slug
		'help_note_post_types'  				// The section of the settings page in which to show the box
	);  
	
	add_settings_field(   
		'help_note_menu_plugin',                 			// String for use in the 'id' attribute of tags. 
		'Install Menu Capability:',             			// Title of the field.   
		'settings_field_help_notes_install_menu_plugin', 	// Function that fills the field with the desired inputs as part of the larger form. 
															// Passed a single argument, the $args array. Name and id of the input should match 
															// the $id given to this function. The function should echo its output. 
		'notes-settings',   								// The menu page on which to display this field. Should match $menu_slug
		'help_note_post_types'  							// The section of the settings page in which to show the box
	);  

} // end help_note_plugin_intialize_options  



function help_note_post_types_section_callback() {  

	echo '<p>Select the Roles that you wish to create Help Notes for. </p>'; 

} // end help_note_post_types_section_callback  

/**
 * Renders settings field for Help Notes Post Types
 */
function settings_field_help_notes_post_types() {

	//  loop through the site roles and create a custom post for each
	global $wp_roles;
	
	if ( ! isset( $wp_roles ) )
	$wp_roles = new WP_Roles();
	
	$roles = $wp_roles->get_names();

	// First, we read the option collection  
	$options = get_option('help_note_option');  
	  
	//ksort($roles);
	foreach($roles as $role_key=>$role_name)
	{
			$id = sanitize_key( $role_key );
		
		// Render the output  
		?> 
		<input 
			type='checkbox'  
			id="<?php echo "help_notes_{$id}" ; ?>" 
			name="help_note_option[help_note_post_types][]"  
			value="<?php echo $role_key; ?>"<?php checked( in_array( $role_key, (array) $options['help_note_post_types']) ); ?>
		</input>
				
		<?php echo " $role_name<br />";		

	}
}

/**
 * Renders settings field for Help Notes Post Types
 */
function settings_field_help_notes_install_menu_plugin() {
	// First, we read the option collection  
	$options = get_option('help_note_option');  

	// Render the output  
	?> 
	<input 
		type='checkbox' 
		name="help_note_option[help_note_menu_plugin]" 
        id="help_note_option_select_menu_plugin]" 
		value="1"<?php checked( $options['help_note_menu_plugin'], 1 ); ?> </Br>
		</Br> Selecting this will prompt you through the installation of plugin 'Post type archive in menu'.  The plugin will be forced active while this is selected.
		</Br> To install follow the prompts or goto the [Plugins Menu]..[Install Plugins], once installed go to [Appearance]...[Menus] and locate the "Archives" metabox for use in your theme's menus.
		</Br> (unselecting will not remove the plugin, you will need to manually uninstall 'Post type archive in menu').
	</input>
	<?php
}

function sanitize_help_note_option( $settings ) {  

	// set the flag to flush the Permalink rules on save of the settings.
	update_option( 'rbhn_update_request', '1' );

    
	// option must be safe
	$settings['help_note_post_types'] = isset( $settings['help_note_post_types'] ) ? (array) $settings['help_note_post_types'] : array();

	return $settings;
	
}
// set_current_user
add_action( 'init', 'help_register_multiple_posttypes' );

function help_register_multiple_posttypes() {

	//  loop through the site roles and create a custom post for each
	global $wp_roles;
    
    // Load roles if not set
	if ( ! isset( $wp_roles ) )
		$wp_roles = new WP_Roles();

	$roles = $wp_roles->get_names();

	call_user_func_array( 'help_register_posttype', array("general", "General") );  // generate a genetic help note post type
	
	// option collection  
	$settings_options = get_option('help_note_option');  
	
	if (  ! empty($settings_options ) ) {	
		foreach( $settings_options['help_note_post_types'] as $selected_key=>$role_selected)
		{
			if (array_key_exists ($role_selected, $roles)) {
				call_user_func_array( 'help_register_posttype', array($role_selected, $roles[$role_selected]) ); 
			} 
		}
	}
}

// Adds custom post type for help
function help_register_posttype($role_key, $role_name) {

    $role_name = (strcasecmp("note", $role_name) ?  $role_name . ' ' : '' );

	$help_labels = array(

		'name'               => $role_name . 'Notes',
		'singular_name'      => $role_name . 'Note',
		'add_new'            => 'Add New',
		'add_new_item'       => 'Add New ' . $role_name . 'Note',
		'edit_item'          => 'Edit ' . $role_name . 'Note',
		'new_item'           => 'New ' . $role_name . 'Note',
		'view_item'          => 'View ' . $role_name . 'Note',
		'search_items'       => 'Search ' . $role_name . 'Notes',
		'not_found'          => 'No ' . $role_name . 'Notes found',
		'not_found_in_trash' => 'No ' . $role_name . 'Notes found in Trash',
		'parent_item_colon'  => '',
		'menu_name'          =>  $role_name,

	);
	
	if ($role_key != "general" ) {
	
		$help_capabilitytype    = "help_{$role_key}_note";
        $help_mapmetacap        = true;
        		
	} else {
	
		$help_capabilitytype    = 'post';
        $help_mapmetacap        = true;

	};
    
    $capability = array(
            'edit_post'		        => 'edit_{$help_capabilitytype}',
            'read_post'		        => 'read_{$help_capabilitytype}',
            'delete_post'		    => 'delete_{$help_capabilitytype}',
            'edit_posts'		    => 'edit_{$help_capabilitytype}s',
            'edit_others_posts'	    => 'edit_others_{$help_capabilitytype}s',
            'publish_posts'		     => 'publish_{$help_capabilitytype}s',
            'read_private_posts'	 => 'read_private_{$help_capabilitytype}s',
            'delete_posts'           => 'delete_{$help_capabilitytype}s',
            'delete_private_posts'   => 'delete_private_{$help_capabilitytype}s',
            'delete_published_posts' => 'delete_published_{$help_capabilitytype}s',
            'delete_others_posts'    => 'delete_others_{$help_capabilitytype}s',
            'edit_private_posts'     => 'edit_private_{$help_capabilitytype}s',
            'edit_published_posts'   => 'edit_published_{$help_capabilitytype}s',
            );
            
    
	$help_public = true;
	        
	$help_args = array(

		'labels'              => $help_labels,
		'public'              => $help_public,  // true implies the members 'content permissions'
										        // meta box is available.
		'publicly_queryable'  => $help_public,
		'exclude_from_search' => false,
		'show_ui'             => true,
		'show_in_menu'        => 'helpmenu', // toplevel_page_helpmenu',
        'show_in_admin_bar'   => true,
    //    'capabililty'         => $capability,
		'capability_type'     => $help_capabilitytype,
		'map_meta_cap'        => $help_mapmetacap,
		'hierarchical'        => true,
		'supports'            => array( 'title', 'editor', 'comments', 'thumbnail', 'page-attributes' , 'revisions', 'author' ),
		'has_archive'         => true,
		'rewrite'             => true,
		'query_var'           => true,
		'can_export'          => true,
		'show_in_nav_menus'   => false
	
	);

	// limit to 20 characters length for the WP limitation of custom post type names
	$post_type_name = 'h_' . substr($role_key , -18);

	register_post_type( $post_type_name, $help_args );

}


// Add New Capabilities ..
function my_help_add_role_caps() {

    
	global $wp_roles;

    // Load Roles if not set
	if ( ! isset( $wp_roles ) )
	$wp_roles = new WP_Roles();

	$roles              = $wp_roles->get_names();
    $administrator      = get_role('administrator');
    
    // option collection  
	$settings_options = get_option('help_note_option');  
    
    if (  ! empty($settings_options ) ) {

		foreach( $settings_options['help_note_post_types'] as $selected_key=>$role_selected)
        {

    		// gets the author role
    		$role = get_role( $role_selected );
    		$capability_type = "help_{$role_selected}_note";

			
    		$role->add_cap( "edit_{$capability_type}s" );
    		$role->add_cap( "edit_others_{$capability_type}s" );
    		$role->add_cap( "publish_{$capability_type}s" );
    		$role->add_cap( "read_private_{$capability_type}s" );
            $role->add_cap( "delete_{$capability_type}s" );
            $role->add_cap( "delete_private_{$capability_type}s" );
            $role->add_cap( "delete_published_{$capability_type}s" );
            $role->add_cap( "delete_others_{$capability_type}s" );
            $role->add_cap( "edit_private_{$capability_type}s" );
            $role->add_cap( "edit_published_{$capability_type}s" );     
            $role->add_cap( "create_{$capability_type}s" );  
                    
            // add admininstrator roles
            // don't allocate any of the three primitive capabilities to a users role
            $administrator->add_cap( "edit_{$capability_type}" );
            $administrator->add_cap( "read_{$capability_type}" );
            $administrator->add_cap( "delete_{$capability_type}" );
            $administrator->add_cap( "create_{$capability_type}s" );
			
			// add admininstrator roles
    		$administrator->add_cap( "edit_{$capability_type}s" );
    		$administrator->add_cap( "edit_others_{$capability_type}s" );
    		$administrator->add_cap( "publish_{$capability_type}s" );
    		$administrator->add_cap( "read_private_{$capability_type}s" );
            $administrator->add_cap( "delete_{$capability_type}s" );
            $administrator->add_cap( "delete_private_{$capability_type}s" );
            $administrator->add_cap( "delete_published_{$capability_type}s" );
            $administrator->add_cap( "delete_others_{$capability_type}s" );
            $administrator->add_cap( "edit_private_{$capability_type}s" );
            $administrator->add_cap( "edit_published_{$capability_type}s" );
    	}
  
	}    
}
		
/* Add capabilities and Flush your rewrite rules for plugin activation */
function help_do_on_activation() {

    $defaults = array(
      'help_note_post_types' => array(),
      'help_note_menu_plugin' => false,
    );
    
    $options = wp_parse_args(get_option('help_note_option'), $defaults);
    
	// create the option on plugin intialisation 
    update_option('help_note_option', $options); 


    //Add the selected role capabilities for use with the role help notes
	my_help_add_role_caps();
    
	// ATTENTION: This is *only* done during plugin activation hook in this example!
	// You should *NEVER EVER* do this on every page load!!
	flush_rewrite_rules();
    

}
register_activation_hook( HELP_MYPLUGINNAME_PATH.'role-based-help-notes.php', 'help_do_on_activation' );

?>