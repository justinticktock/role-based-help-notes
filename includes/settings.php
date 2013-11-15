<?php


// Create a second level settings page
add_action('admin_menu', 'register_my_custom_submenu_page');

function register_my_custom_submenu_page() {
    add_submenu_page( 'options-general.php', 'Notes', 'Help Notes', 'manage_options', HELP_SETTINGS_PAGE, 'notes_settings_page_callback' ); 
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
		HELP_SETTINGS_PAGE      					  
	);  
    
    add_settings_field(     
        'help_note_general_enabled',                         
		'General Help Notes:',             			         
		'settings_field_help_notes_general_type_enable',
		HELP_SETTINGS_PAGE,   								 
		'help_note_general'  							     
	);       

    add_settings_field(   
		'help_note_contents_page',                 	
		'Contents Page:',             				
		'settings_field_help_notes_contents_page', 
		HELP_SETTINGS_PAGE,   						
		'help_note_general'  						
	); 

    // Role Settings..
    
	 add_settings_section(
		'help_note_post_types',        			 
		'Role Based Settings',            		 
		'help_note_post_types_section_callback', 
		HELP_SETTINGS_PAGE      					 
	);  

	add_settings_field(   
		'help_note_post_types',                 
		'Help Note Post Types:',                
		'settings_field_help_notes_post_types', 
		HELP_SETTINGS_PAGE,   					
		'help_note_post_types'  				
	);      
    
    // Extensions Settings..
	    
	 add_settings_section(
		'help_note_extensions',        			    
		'Plugin Extensions',            		    
		'help_note_extensions_section_callback',  	
		HELP_SETTINGS_PAGE      					    
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
		HELP_SETTINGS_PAGE,   								   
		'help_note_extensions'  							   
	);       
    
    add_settings_field(   
    	'help_note_simple_page_ordering',                 	      
		'Simple Page Ordering:',             			          
		'settings_field_help_notes_install_simple_page_ordering', 
		HELP_SETTINGS_PAGE,   								      
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


?>
