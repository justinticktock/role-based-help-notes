<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * RBHN_Settings class.
 */
class RBHN_Settings {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		/**
		 * Include the Tabbed_Settings class.
		 */
		
		require_once(dirname( __FILE__ ) . '/class-tabbed-settings.php');

		add_action( 'ticktock_settings_register', array( $this, 'rbhn_settings_register' ) );
		
		// register the plugin specific settings callout functions
		add_action( 'init', array( $this, 'init' ));
	}
	
	/**
	 * Registers all new setting callout functions 
	 *
	 * @access public	 
	 * @return void
	 */
	public function init() {
		
		/**
		 * Init Tabbed_Settings class
		 */
		 
		if ( class_exists( 'RBHN_Settings_Additional_Methods' )) {
			Tabbed_Settings::$instance->registerHandler( new RBHN_Settings_Additional_Methods() );
		}
	}
	
	
	public function rbhn_settings_register() {
	
		$theme_text_domain = 'role-based-help-notes-text-domain';

		$config = array(
						'default_tab_key' => 'rbhn_general',		// Default settings tab, opened on first settings page open.
						'menu' => 'notes-settings',       			// menu options page slug name.
						);
	
	
		$tabbed_settings = 	apply_filters( 'rbhn_settings', 
								array(								
									'rbhn_general' => array(
										'title' 		=> __( 'General', 'role-based-help-notes-text-domain' ),
										'description' 	=> __( 'Settings for general purpose.', 'role-based-help-notes-text-domain' ),
										'settings' 		=> array(		
																array(
																	'name' 		=> 'rbhn_general_enabled',
																	'std' 		=> false,
																	'label' 	=> __( 'General Help Notes', 'role-based-help-notes-text-domain' ),
																	'cb_label'  => __( 'Enable', 'role-based-help-notes-text-domain' ),
																	'desc'		=> __( "Enabling the 'General' option gives you global Help Notes, which are not limited to any one role, these will be accessible to all and follow the capabilities of the normal wordpress 'post' post type.", 'role-based-help-notes-text-domain' ),
																	'type'      => 'field_checkbox_option'
																	),
																array(
																	'name' 		=> 'rbhn_user_widget_enabled',
																	'std' 		=> false,
																	'label' 	=> __( 'Widget', 'role-based-help-notes-text-domain' ),
																	'cb_label'  => __( 'Enable', 'role-based-help-notes-text-domain' ),
																	'desc'		=> __( "Enabling the 'User Widget' will allow you to place the Help Notes user widget into your sidebars.  The widget lists all users that have access to the Help Notes for a particular role and it is only shown on individual Help Note posts.", 'role-based-help-notes-text-domain' ),
																	'type'      => 'field_checkbox_option'
																	),
																array(
																	'name' 		=> 'rbhn_contents_page',
																	'std' 		=> '0',
																	'label' 	=> __( 'Contents Page', 'contents_page' ),
																	'desc'		=> __( 'If you wish to create a contents page add a new page and select it here so that the Help Note Contents are displayed.', 'role-based-help-notes-text-domain' ),
																	'type'      => 'field_page_select_list_option',
																	),
															),
									),
									'rbhn_roles' => array(
										'title' 		=> __( 'Roles', 'role-based-help-notes-text-domain' ),
										'description' 	=> __( 'Select the Roles that you wish to create Help Notes for.', 'role-based-help-notes-text-domain' ),
										'settings' 		=> array(					
																array(
																	'name' 		=> 'rbhn_post_types',
																	'std' 		=> array(),
																	'label' 	=> __( 'Help Note Post Types', 'role-based-help-notes-text-domain' ),
																	'desc'		=> '',
																	'type'      => 'field_help_notes_post_types_option'
																	),					
																),
									),
									'rbhn_plugin_extension' => array(
											'title' 		=> __( 'Plugin Extensions', 'role-based-help-notes-text-domain' ),
											'description' 	=> __( 'These settings are optional.  Selection of any suggested plugin here will prompt you through the installation.  The plugin will be forced active while this is selected; deselecting will not remove the plugin, you will need to manually uninstall.', 'role-based-help-notes-text-domain' ),					
											'settings' 		=> array(
																	array(
																		'name' 		=> 'rbhn_user_switching',
																		'std' 		=> false,
																		'label' 	=> 'User Switching',
																		'cb_label'  => __( 'Enable', 'role-based-help-notes-text-domain' ),
																		'desc'		=> __( 'This is a useful plugin for Administrators to test the accessibility of users with different roles, you can simply switch to their account to check how the Help Notes appear for them.', 'role-based-help-notes-text-domain' ),
																		'type'      => 'field_plugin_checkbox_option',
																		// the following are for tgmpa_register activation of the plugin
																		'slug'      			=> 'user-switching', 
																		'required'              => false,
																		'force_deactivation' 	=> false,
																		'force_activation'      => true,												
																		),						
																	array(
																		'name' 		=> 'rbhn_simple_page_ordering',
																		'std' 		=> false,
																		'label' 	=> 'Simple Page Ordering',
																		'cb_label'  => __( 'Enable', 'role-based-help-notes-text-domain' ),
																		'desc'		=> __( 'Once installed go you can drag pages up/down within the admin side to re-order Help Notes.', 'role-based-help-notes-text-domain' ),
																		'type'      => 'field_plugin_checkbox_option',
																		// the following are for tgmpa_register activation of the plugin
																		'slug'      			=> 'simple-page-ordering', 
																		'required'              => false,
																		'force_deactivation' 	=> false,
																		'force_activation'      => true,												
																		),					
																	array(
																		'name' 		=> 'rbhn_simple_footnotes_plugin',
																		'std' 		=> false,
																		'label' 	=> 'Simple Footnotes',
																		'cb_label'  => __( 'Enable', 'role-based-help-notes-text-domain' ),
																		'desc'		=> __( "Once installed go you can use the 'ref' shortcode for example... [ref]Add footnote text here[/ref] within your posts.", 'role-based-help-notes-text-domain' ),
																		'type'      => 'field_plugin_checkbox_option',
																		// the following are for tgmpa_register activation of the plugin
																		'slug'      			=> 'simple-footnotes',
																		'required'              => false,
																		'force_deactivation' 	=> false,
																		'force_activation'      => true,		
																		),					
																	array(
																		'name' 		=> 'rbhn_disable_comments_plugin',
																		'std' 		=> false,
																		'label' 	=> 'Disable Comments',
																		'cb_label'  => __( 'Enable', 'role-based-help-notes-text-domain' ),
																		'desc'		=> __( 'Comments are of less value for Help Notes and this plugin will allow you to easily remove comments from use.', 'role-based-help-notes-text-domain' ),
																		'type'      => 'field_plugin_checkbox_option',
																		// the following are for tgmpa_register activation of the plugin
																		'slug'      			=> 'disable-comments',
																		'required'              => false,
																		'force_deactivation' 	=> false,
																		'force_activation'      => true,		
																		),					
																	array(
																		'name' 		=> 'rbhn_email_post_changes_plugin',
																		'std' 		=> false,
																		'label' 	=> 'Email Post Changes',
																		'cb_label'  => __( 'Enable', 'role-based-help-notes-text-domain' ),
																		'desc'		=> __( 'Once installed go to [Settings]...[Email Post Changes] to use the plugin and notify specific users of changes to Help Notes by email.', 'role-based-help-notes-text-domain' ),
																		'type'      => 'field_plugin_checkbox_option',
																		// the following are for tgmpa_register activation of the plugin
																		'slug'      			=> 'email-post-changes',
																		'required'              => false,
																		'force_deactivation' 	=> false,
																		'force_activation'      => true,		
																		),					
																	array(
																		'name' 		=> 'rbhn_post_type_switcher_plugin',
																		'std' 		=> false,
																		'label' 	=> 'Post Type Switcher',
																		'cb_label'  => __( 'Enable', 'role-based-help-notes-text-domain' ),
																		'desc'		=> __( "This plugin will allow users with two or more roles the ability to change the role assigned to a help note.  Once installed you will find a new selection/edit option in the 'Publish' area.", 'role-based-help-notes-text-domain' ),
																		'type'      => 'field_plugin_checkbox_option',
																		// the following are for tgmpa_register activation of the plugin
																		'slug'      			=> 'post-type-switcher', 
																		'required'              => false,
																		'force_deactivation' 	=> false,
																		'force_activation'      => true,		
																		),					
																	array(
																		'name' 		=> 'rbhn_post_type_archive_in_menu_plugin',
																		'std' 		=> false,
																		'label' 	=> 'Post type archive in menu',
																		'cb_label'  => __( 'Enable', 'role-based-help-notes-text-domain' ),
																		'desc'		=> __( "Once installed go to [Appearance]...[Menus] and locate the 'Archives' metabox for use in your theme menus.", 'role-based-help-notes-text-domain' ),
																		'type'      => 'field_plugin_checkbox_option',
																		// the following are for tgmpa_register activation of the plugin
																		'slug'      			=> 'post-type-archive-in-menu', 
																		'required'              => false,
																		'force_deactivation' 	=> false,
																		'force_activation'      => true,		
																		),
																	),
									)				
								)
							);


		ticktock_settings( $tabbed_settings, $config );

		}
}

/** Create a new instance of the class */
new RBHN_Settings();



/**
 * RBHN_Settings_Additional_Methods class.
 */
class RBHN_Settings_Additional_Methods {


	/**
	 * field_help_notes_post_types_option 
	 *
	 * @param array of arguments to pass the option name to render the form field.
	 * @access public
	 * @return void
	 */
	public function field_help_notes_post_types_option( array $args  ) {
		$option   = $args['option'];
		
		//  loop through the site roles and create a custom post for each
		global $wp_roles;
		global $role_based_help_notes;
		$value = get_option( $option['name'] );
		
		if ( ! isset( $wp_roles ) )
		$wp_roles = new WP_Roles();

		$roles = $wp_roles->get_names(); 
		?><ul><?php 
		asort( $roles );
		foreach( $roles as $role_key=>$role_name )
		{
			$id = sanitize_key( $role_key );
			
			$post_type_name = $role_based_help_notes->clean_post_type_name( $role_key );
			$role_active = $this->rbhn_role_active( $role_key, (array) $value )

			// Render the output  
			?> 
			<li><label><input 
				type='checkbox'  
				id="<?php echo esc_html( "help_notes_{$id}" ) ; ?>" 
				name="<?php echo esc_html( $option['name'] ); ?>[][<?php echo esc_html( $role_key ) ; ?>]"
				value="<?php echo esc_attr( $post_type_name )	; ?>"<?php checked( $role_active ); ?>
			</input>
			<?php echo esc_html( $role_name ) . " <br/>"; ?>	
			</label></li>
			<?php 
		}?></ul><?php 
		if ( ! empty( $option['desc'] ))
			echo ' <p class="description">' . esc_html( $option['desc'] ) . '</p>';		
	}
	

	/**
	 * rbhn_role_active 
	 *
	 * @param $role current role and $active_helpnote_roles array of active help notes.
	 * @access public
	 * @return void
	 */
	public function rbhn_role_active($role, $active_helpnote_roles) {

		foreach ($active_helpnote_roles as $active_role=>$active_posttype) {
				if (! empty($active_posttype["$role"])) {
					return true;
				}
		}
		return false;
	}
}


?>