<?php

// Create a second level settings page
add_action('admin_menu', 'register_role_based_help_notes_settings_page');

function register_role_based_help_notes_settings_page() {
    add_submenu_page( 'options-general.php', 'Notes', 'Help Notes', 'manage_options', HELP_SETTINGS_PAGE, 'notes_settings_page_callback' ); 
}

function notes_settings_page_callback( $args = '' ) {

        extract( wp_parse_args( $args, array(
            'title'       => __( 'Help Notes Settings', 'role-based-help-notes-text-domain' ),
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
					do_settings_sections( HELP_SETTINGS_PAGE );
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

	// do a one shot during save of options
	if ( get_option( 'rbhn_update_request' )) {

			update_option( 'rbhn_update_request', '' );

			help_do_on_activation();   			// add the active capabilities

			rbhn_clean_inactive_capabilties();	// remove the inactive role capabilities

    }

	register_setting(  
		'help_note_option_group',  		// A settings group 
		'help_note_option',
		'sanitize_help_note_option'
	);  

	// General Settings..

	add_settings_section(
		'help_note_general',
		__( 'General', 'role-based-help-notes-text-domain' ),
		'help_note_general_section_callback',
		HELP_SETTINGS_PAGE
	);  

	add_settings_field(     
		'help_note_general_enabled',
		__( 'General Help Notes:', 'role-based-help-notes-text-domain' ),
		'settings_field_help_notes_general_type_enable',
		HELP_SETTINGS_PAGE,
		'help_note_general'
	);       

	add_settings_field(     
		'user_widget_enabled',
		__( 'Widget:', 'role-based-help-notes-text-domain' ),
		'settings_field_user_widget_enable',
		HELP_SETTINGS_PAGE,
		'help_note_general'
	);       

	add_settings_field(   
		'help_note_contents_page',
		__( 'Contents Page:', 'role-based-help-notes-text-domain' ),
		'settings_field_help_notes_contents_page', 
		HELP_SETTINGS_PAGE,
		'help_note_general'
	);

	// Role Settings..

	 add_settings_section(
		'help_note_post_types',
		__( 'Role Based Settings:', 'role-based-help-notes-text-domain' ),
		'help_note_post_types_section_callback', 
		HELP_SETTINGS_PAGE
	);

	add_settings_field(   
		'help_note_post_types',
		__( 'Help Note Post Types:', 'role-based-help-notes-text-domain' ),
		'settings_field_help_notes_post_types', 
		HELP_SETTINGS_PAGE,
		'help_note_post_types'
	);

	// Extensions Settings..
	    
	 add_settings_section(
		'help_note_extensions',
		__( 'Plugin Extensions', 'role-based-help-notes-text-domain' ),
		'help_note_extensions_section_callback',
		HELP_SETTINGS_PAGE
	);

	add_settings_field(   
		'help_note_simple_page_ordering',
		'Simple Page Ordering:',
		'settings_field_help_notes_install_simple_page_ordering',
		HELP_SETTINGS_PAGE,
		'help_note_extensions'
	);

	add_settings_field(   
		'help_note_simple_footnotes_plugin',
		'Simple Footnotes:',
		'settings_field_help_notes_install_simple_footnotes',	
		HELP_SETTINGS_PAGE,
		'help_note_extensions'
	);
	
	add_settings_field(   
		'help_note_email_post_changes_plugin',
		'Email Post Changes:',
		'settings_field_help_notes_install_email_post_changes',
		HELP_SETTINGS_PAGE, 
		'help_note_extensions'
	);

	add_settings_field(
		'help_note_post_tpye_switcher_plugin',
		'Post Type Switcher:',
		'settings_field_help_notes_install_post_type_switcher',
		HELP_SETTINGS_PAGE,
		'help_note_extensions'
	);

	add_settings_field(   
		'help_note_menu_plugin',
		'Post type archive in menu:',
		'settings_field_help_notes_install_menu_plugin',
		HELP_SETTINGS_PAGE,
		'help_note_extensions'
	);

} // end help_note_plugin_intialize_options()

function help_note_general_section_callback() {  

	; 

}


function help_note_post_types_section_callback() {  
	
	_e( 'Select the Roles that you wish to create Help Notes for. ', 'role-based-help-notes-text-domain' );

}

function help_note_extensions_section_callback() {  

    $string = "<p>Select the extension plugins that you wish to use.  Selection of a plugin will prompt you through the installation or go to the Menu ..[Plugins]..[Install Plugins], </Br>
	The plugin will be forced active while this is selected; deselecting will not remove the plugin, you will need to manually uninstall .</p>";
	
	_e( $string, 'role-based-help-notes-text-domain' );

}
	

/**
 * Renders settings field for Help Notes general type enable check box
 */
function settings_field_help_notes_general_type_enable() {

    // First, we read the option collection  
    $options = get_option('help_note_option');  

    // Render the output  
	
	 $string = "Enabling the 'General' option gives you global Help Notes, which are not limited to any one role, these will be accessible to all and follow the capabilities of the normal wordpress 'post' post type.";
	 
	?> 
	<input 
		type='checkbox' 
		name="help_note_option[help_note_general_enabled]" 
        id="help_note_general_enabled" 
		value="1"<?php checked( $options['help_note_general_enabled'], 1 ); ?>
        </Br><p><?php _e( $string, 'role-based-help-notes-text-domain' );?></p>
	</input>
    
	<?php
}


/**
 * Renders settings field for User Widget enable check box
 */
function settings_field_user_widget_enable() {

    // First, we read the option collection  
    $options = get_option('help_note_option');  

    // Render the output  
	
	 $string = "Enabling the 'User Widget' will allow you to place the Help Notes user widget into your sidebars.  The widget lists all users that have access to the Help Notes for a particular role and it is only shown on individual Help Note posts.";
	 	
    ?> 
	<input 
		type='checkbox' 
		name="help_note_option[user_widget_enabled]" 
        id="user_widget_enabled" 
		value="1"<?php checked( $options['user_widget_enabled'], 1 ); ?>
        </BR><p><?php _e( $string, 'role-based-help-notes-text-domain' );?></p>
	</input>
    
	<?php
}

function rbhn_role_active($role, $active_helpnote_roles) {

    foreach ($active_helpnote_roles as $active_role=>$active_posttype) {
			if (! empty($active_posttype["$role"])) {
				return true;
			}
    }
    return false;
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
		$post_type_name = clean_post_type_name($role_key);
		$role_active = rbhn_role_active( $role_key, (array) $options['help_note_post_types'])
		
		// Render the output  
		?> 
		<input 
			type='checkbox'  
			id="<?php echo "help_notes_{$id}" ; ?>" 
			name="help_note_option[help_note_post_types][][<?php echo $role_key ; ?>]"
			value="<?php echo $post_type_name	; ?>"<?php checked( $role_active ); ?>
		</input>
		<?php echo " $role_name<br/>";			

	}
}

/**
 * Renders settings field for Help Notes menu_plugin
 */
function settings_field_help_notes_install_menu_plugin() {

	// First, we read the option collection  
	$options = get_option('help_note_option');  

	// Render the output  
	
	 $string = "Once installed go to [Appearance]...[Menus] and locate the 'Archives' metabox for use in your theme menus.";
		
	?> 
	<input 
		type='checkbox' 
		name="help_note_option[help_note_menu_plugin]" 
        id="help_note_menu_plugin" 
		value="1"<?php checked( $options['help_note_menu_plugin'], 1 ); ?> 
        </Br><p><?php _e( $string, 'role-based-help-notes-text-domain' );?></p>
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
	
	 $string = "Once installed go you can use the 'ref' shortcode for example... [ref]Add footnote text here[/ref] within your posts.";
			
	?> 
	<input 
		type='checkbox' 
		name="help_note_option[help_note_simple_footnotes_plugin]" 
        id="help_note_simple_footnotes_plugin" 
		value="1"<?php checked( $options['help_note_simple_footnotes_plugin'], 1 ); ?>
        </Br><p><?php _e( $string, 'role-based-help-notes-text-domain' );?></p>
	</input>
    
	<?php
}

/**
 * Renders settings field for Help Notes simple_footnotes_plugin
 */
function settings_field_help_notes_install_email_post_changes() {

    // First, we read the option collection  
	$options = get_option('help_note_option');  

	// Render the output  
	
	 $string = "Once installed go to [Settings]...[Email Post Changes] to use the plugin and notify specific users of changes to Help Notes by email.";
			
	?> 
	<input 
		type='checkbox' 
		name="help_note_option[help_note_email_post_changes_plugin]" 
        id="help_note_email_post_changes_plugin" 
		value="1"<?php checked( $options['help_note_email_post_changes_plugin'], 1 ); ?>
        </Br><p><?php _e( $string, 'role-based-help-notes-text-domain' );?></p>
	</input>
    
	<?php
}


/**
 * Renders settings field for Help Notes simple_footnotes_plugin
 */
function settings_field_help_notes_install_post_type_switcher() {

    // First, we read the option collection  
	$options = get_option('help_note_option');  

	// Render the output  
	
	 $string = "This plugin will allow you to change the role which has the help note.  Once installed within you will find a new selection/edit option in the 'Publish' area.";
		
	?> 
	<input 
		type='checkbox' 
		name="help_note_option[help_note_post_type_switcher_plugin]" 
        id="help_note_post_type_switcher_plugin" 
		value="1"<?php checked( $options['help_note_post_type_switcher_plugin'], 1 ); ?>
        </Br><p><?php _e( $string, 'role-based-help-notes-text-domain' );?></p>
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
	
	 $string = "Once installed go you can drag pages up/down within the admin side to re-order Help Notes.";
	 
	?> 
	<input 
		type='checkbox' 
		name="help_note_option[help_note_simple_page_ordering]" 
        id="help_note_simple_page_ordering" 
		value="1"<?php checked( $options['help_note_simple_page_ordering'], 1 ); ?>
        </Br><p><?php _e( $string, 'role-based-help-notes-text-domain' );?></p>
	</input>
    
	<?php
}

function settings_field_help_notes_contents_page() {

	// First, we read the option collection  
	$options = get_option('help_note_option');  

	// Render the output  
	
	 $string = "If you wish to create a contents page add a new page and select it here so that the Help Note Contents are displayed.";
	  
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
    </Br> <?php _e( $string, 'role-based-help-notes-text-domain' );?>
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

?>