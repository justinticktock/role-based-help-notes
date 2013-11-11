<?php
/**
Plugin Name: Role Based Help Notes
Plugin URI: http://justinandco.com
Description: The addition of Custom Post Type to cover site help notes
Version: 1.2.1
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
define( 'HELP_PLUGIN_URI', plugins_url('', __FILE__) );

//Includes...
require_once( HELP_MYPLUGINNAME_PATH . 'includes/capabilities.php' );   // <<< commented out for default meta_capabilities to take precedence.

// if selected install the plugings and force activation
require_once( HELP_MYPLUGINNAME_PATH . 'includes/install-plugins.php' );    

// Create a second level settings page
add_action('admin_menu', 'register_my_custom_submenu_page');

function register_my_custom_submenu_page() {
    add_submenu_page( 'options-general.php', 'Notes', 'Help Notes', 'manage_options', 'notes-settings', 'notes_settings_page_callback' ); 
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
		'help_note_option_group',  		// A settings group 
		'help_note_option'   ,  		 
		'sanitize_help_note_option'  	
	);  


    // General Settings..
    
     add_settings_section(
		'help_note_general',        			    
		'General',            	                  
		'help_note_general_section_callback',  	   
		'notes-settings'      					  
	);  
    
    add_settings_field(     
        'help_note_general_enabled',                         
		'General Help Notes:',             			         
		'settings_field_help_notes_general_type_enable',
		'notes-settings',   								 
		'help_note_general'  							     
	);       

    add_settings_field(   
		'help_note_contents_page',                 	
		'Contents Page:',             				
		'settings_field_help_notes_contents_page', 
		'notes-settings',   						
		'help_note_general'  						
	); 

    // Role Settings..
    
	 add_settings_section(
		'help_note_post_types',        			 
		'Role Based Settings',            		 
		'help_note_post_types_section_callback', 
		'notes-settings'      					 
	);  

	add_settings_field(   
		'help_note_post_types',                 
		'Help Note Post Types:',                
		'settings_field_help_notes_post_types', 
		'notes-settings',   					
		'help_note_post_types'  				
	);      
    
    // Extensions Settings..
	    
	 add_settings_section(
		'help_note_extensions',        			    
		'Plugin Extensions',            		    
		'help_note_extensions_section_callback',  	
		'notes-settings'      					    
	);  
    
    add_settings_field(   
		'help_note_menu_plugin',                 			
		'Post type archive in menu:',             			
		'settings_field_help_notes_install_menu_plugin', 	 
		'notes-settings',   								
		'help_note_extensions'  							
	);      

    add_settings_field(   
    	'help_note_simple_footnotes_plugin',                 
		'Simple Footnotes:',             			           
		'settings_field_help_notes_install_simple_footnotes',															   
		'notes-settings',   								   
		'help_note_extensions'  							   
	);       
    
    add_settings_field(   
    	'help_note_simple_page_ordering',                 	      
		'Simple Page Ordering:',             			          
		'settings_field_help_notes_install_simple_page_ordering', 
		'notes-settings',   								      
		'help_note_extensions'  							      
	);       
    

} // end help_note_plugin_intialize_options()

function help_note_general_section_callback() {  

	; 

} // end help_note_post_types_section_callback  


function help_note_post_types_section_callback() {  

	echo '<p>Select the Roles that you wish to create Help Notes for. </p>'; 

} // end help_note_post_types_section_callback  

function help_note_extensions_section_callback() {  
    
    ?><p>Select the extension plugins that you wish to use.  Selection of a plugin will prompt you through the installation and the plugin will be forced active while this is selected.
	To install follow the prompts or goto the [Plugins Menu]..[Install Plugins], (unselecting will not remove the plugin, you will need to manually uninstall Post-type-archive-in-menu).</p><?php

} // end help_note_extensions_section_callback  



/**
 * Renders settings field for Help Notes general type enable check box
 */
function settings_field_help_notes_general_type_enable() {
    // First, we read the option collection  
    $options = get_option('help_note_option');  

    // Render the output  
	?> 
	<input 
		type='checkbox' 
		name="help_note_option[help_note_general_enabled]" 
        id="help_note_general_enabled" 
		value="1"<?php checked( $options['help_note_general_enabled'], 1 ); ?>
        <p>&nbsp Select to enable the 'General' Help Notes post type.  (General Help Notes are global and not limited 
        to any one role, and follow the capabilities of the 'post' post type.)</p>
	</input>
    
	<?php
}


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
	  
	ksort($roles);
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
 * Renders settings field for Help Notes menu_plugin
 */
function settings_field_help_notes_install_menu_plugin() {
	// First, we read the option collection  
	$options = get_option('help_note_option');  

	// Render the output  
	?> 
	<input 
		type='checkbox' 
		name="help_note_option[help_note_menu_plugin]" 
        id="help_note_menu_plugin" 
		value="1"<?php checked( $options['help_note_menu_plugin'], 1 ); ?> 
        <p>&nbsp Once installed go to [Appearance]...[Menus] and locate the 'Archives' metabox for use in your theme menus.</p>
	</input>
    
	<?php
}


/**
 * Renders settings field for Help Notes simple_footnotes_plugin
 */
function settings_field_help_notes_install_simple_footnotes() {
    // First, we read the option collection  
	$options = get_option('help_note_option');  

	// Render the output  
	?> 
	<input 
		type='checkbox' 
		name="help_note_option[help_note_simple_footnotes_plugin]" 
        id="help_note_simple_footnotes_plugin" 
		value="1"<?php checked( $options['help_note_simple_footnotes_plugin'], 1 ); ?>
        <p>&nbsp Once installed go you can use the 'ref' shortcode for example... [ref]Add footnote text here[/ref] within your posts.</p>
	</input>
    
	<?php
}

/**
 * Renders settings field for Help Notes simple_page_ordering
 */
function settings_field_help_notes_install_simple_page_ordering() {
    // First, we read the option collection  
	$options = get_option('help_note_option');  

	// Render the output  
	?> 
	<input 
		type='checkbox' 
		name="help_note_option[help_note_simple_page_ordering]" 
        id="help_note_simple_page_ordering" 
		value="1"<?php checked( $options['help_note_simple_page_ordering'], 1 ); ?>
        <p>&nbsp Once installed go you can drag pages up/down within the admin side to re-order Help Notes.</p>
	</input>
    
	<?php
}




function settings_field_help_notes_contents_page() {
	// First, we read the option collection  
	$options = get_option('help_note_option');  

	// Render the output  
	?> 
    
    <form action="<?php bloginfo('url'); ?>" method="get">
	<?php wp_dropdown_pages(array( 
                                'show_option_none' => __( "- None -" ), 
                                'option_none_value' => '0', 
                                'sort_order'   => 'ASC',
                				'sort_column'  => 'post_title',
                                'hierarchical'  => 0,
                                'echo'          => 1,
            					'selected'     => $options['help_note_contents_page'],
            					'name'          => 'help_note_option[help_note_contents_page]'
            				    )); ?>
    </Br> If you wish to create a contents page add a new page and select it here so that the Help Note Contents are displayed.
    </form>
    
	<?php
}

function sanitize_help_note_option( $settings ) {  

	// set the flag to flush the Permalink rules on save of the settings.
	update_option( 'rbhn_update_request', '1' );

    
	// option must be safe
	$settings['help_note_post_types'] = isset( $settings['help_note_post_types'] ) ? (array) $settings['help_note_post_types'] : array();


	return $settings;
	
}


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
		foreach( $settings_options['help_note_post_types'] as $selected_key=>$role_selected)
		{
			if (array_key_exists ($role_selected, $roles)) {
                $post_type_name = clean_post_type_name($role_selected);
                if (current_user_can( $role_selected )) {
				    $active_posttypes[] = $post_type_name; 
                }
			} 
		}
	}
    
    return $active_posttypes;
}



// register post types
add_action( 'init', 'help_register_multiple_posttypes' );

function help_register_multiple_posttypes() {
    
    // option collection  
	$settings_options = get_option('help_note_option'); 

	if ( isset( $settings_options['help_note_general_enabled'] ) && ! empty( $settings_options['help_note_general_enabled'] ) ) {
		call_user_func_array( 'help_register_posttype', array("general", "General") );  // generate a genetic help note post type
	}
	 
    
	//  loop through the site roles and create a custom post for each
	global $wp_roles;
    
    // Load roles if not set
	if ( ! isset( $wp_roles ) )
		$wp_roles = new WP_Roles();

	$roles = $wp_roles->get_names();

	
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

    $post_type_name = clean_post_type_name($role_key);
	
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
	
	if ($role_key == "general" ) {
		$help_capabilitytype    = 'post';	
	} else {
		$help_capabilitytype    = $post_type_name;
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
		'menu_icon'			  => HELP_PLUGIN_URI . '/images/help.png' ,
	);

	register_post_type( $post_type_name, $help_args );

}


// Add Contents Details to the Contents page if delclared in settings ..
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
                    	'title_li'     => __("$posttype_Name"),
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
			$post_type_name = clean_post_type_name($role_selected);

    		// gets the author role
    		$role = get_role( $role_selected );
    		$capability_type = sanitize_key($post_type_name);

			
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
      'help_note_post_types'                => array(),
      'help_note_menu_plugin'               => false,
      'help_note_simple_footnotes_plugin'   => false,
      'help_note_simple_page_ordering'   	=> false,
      'help_note_contents_page'             => '0',
      'help_note_general_enabled'           => false,
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


function clean_post_type_name($post_type_name) {
    // limit to 20 characters length for the WP limitation of custom post type names
    $post_type_name = sanitize_key('h_' . substr($post_type_name , -18)); 
    return $post_type_name;
}


?>