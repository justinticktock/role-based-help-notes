<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * RBHN_Settings class.
 */
class RBHN_Settings {

	private static $settings = '';
	private $default_tab_key = 'rbhn_general';
	private $plugin_options_key = HELP_SETTINGS_PAGE;
	private $plugin_settings_tabs = array();
	
	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */	 
	function __construct() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'add_admin_menus' ) );
		add_action( 'do_settings_sections', array( $this, 'rbhn_hooks_section_callback' ) );
	}


	/**
	 * init_settings function.
	 *
	 * @access private
	 * @return void
	 */
	private static function init_settings() {
		self::$settings = apply_filters( 'rbhn_settings',
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
												'label' 	=> __( 'Contents Page', 'download_monitor' ),
												'desc'		=> __( 'If you wish to create a contents page add a new page and select it here so that the Help Note Contents are displayed.', 'role-based-help-notes-text-domain' ),
												'type'      => 'field_help_notes_contents_page_option',
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
						'description' 	=> __( 'These settings are optional.  Selection of any suggested plugin here will prompt you through the installation or you can go to the Menu ..[Plugins]..[Install Plugins].  The plugin will be forced active while this is selected; deselecting will not remove the plugin, you will need to manually uninstall.', 'role-based-help-notes-text-domain' ),					
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
	}
	

	/**
	 * Called during admin_menu, adds rendered using the plugin_options_page method.
	 *
	 * @access public
	 * @return void
	 */	 
	public function add_admin_menus() {
		add_options_page( __( 'Notes', 'role-based-help-notes-text-domain' ), __( 'Help Notes', 'role-based-help-notes-text-domain' ), 'manage_options', $this->plugin_options_key, array( &$this, 'plugin_options_page' ) );
	}

	
	/**
	 * register_settings function.
	 *
	 * @access public
	 * @return void
	 */
	public function register_settings() {

		self::init_settings();
		foreach ( self::$settings as $options_group => $section  ) {
			foreach ( $section['settings'] as $option ) {
				$this->current_section = $section;
				if ( isset( $option['std'] ) )
					add_option( $option['name'], $option['std'] );
				$this->plugin_settings_tabs[$options_group] = $section['title'];
				register_setting( $options_group, $option['name'] );
				add_settings_section( $options_group, $section['title'], array( $this, 'rbhn_hooks_section_callback' ), $options_group );
				add_settings_field( $option['name'].'_setting-id', $option['label'], array( $this, $option['type'] ), $options_group, $options_group, array( 'option' => $option )  );	
			}
		}
	}

	/**
	 * Settings page rendering it checks for active tab and replaces key with the related
	 * settings key. Uses the plugin_options_tabs method to render the tabs.
	 *
	 * @access public
	 * @return void
	 */		 
	public function plugin_options_page() {
		global $role_based_help_notes;
		$tab = isset( $_GET['tab'] ) ? sanitize_key($_GET['tab'] ) : $this->default_tab_key;
		if ( ! empty( $_GET['settings-updated'] )) {
			flush_rewrite_rules();
			$role_based_help_notes->help_do_on_activation();		// add the active capabilities
			RBHN_Capabilities::rbhn_clean_inactive_capabilties();	// remove the inactive role capabilities
		}
		?>
		<div class="wrap">
			<?php $this->plugin_options_tabs(); ?>
			<form method="post" action="options.php">
				<?php wp_nonce_field( 'update-options' ); ?>
				<?php settings_fields( $tab ); ?>
				<?php do_settings_sections( $tab ); ?>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Wordpress by default doesn't include a section callback to send any section descriptive text 
	 * to the form.  This function uses section description based on the current section id being processed.
	 *
	 * @param section_passed.
	 * @access public
	 * @return void
	 */	
	public function rbhn_hooks_section_callback($section_passed){
		foreach ( self::$settings as $options_group => $section  ) {
			if (( $section_passed['id'] == $options_group) && ( ! empty( $section['description'] ))) {
				echo esc_html( self::$settings[$options_group]['description'] );	
			}
		}
	 }
	
	/**
	 * field_checkbox_option 
	 *
	 * @param array of arguments to pass the option name to render the form field.
	 * @access public
	 * @return void
	 */
	public function field_checkbox_option( array $args  ) {
		$option   = $args['option'];
		$value = get_option( $option['name'] );
		?><label><input id="setting-<?php echo esc_html( $option['name'] ); ?>" name="<?php echo esc_html( $option['name'] ); ?>" type="checkbox" value="1" <?php checked( '1', $value ); ?> /> <?php echo esc_html( $option['cb_label'] ); ?></label><?php
		if ( ! empty( $option['desc'] ))
		echo ' <p class="description">' . esc_html( $option['desc'] ) . '</p>';
	}

	/**
	 * field_help_notes_contents_page_option 
	 *
	 * @param array of arguments to pass the option name to render the form field.
	 * @access public
	 * @return void
	 */
	public function field_help_notes_contents_page_option( array $args  ) {
		$option	= $args['option'];
		$value	= intval( get_option( $option['name'] ));
		?>
		<form action="<?php bloginfo('url'); ?>" method="get">
		<?php wp_dropdown_pages(array(
									'show_option_none' => __( "- None -", 'role-based-help-notes-text-domain' ), 
									'option_none_value' => '0', 
									'sort_order'   => 'ASC',
									'sort_column'  => 'post_title',
									'hierarchical'  => 0,
									'echo'          => 1,
									'selected'     => $value,
									'name'          => $option['name']
									)); ?>
		</form>

		<?php
		if ( ! empty( $option['desc'] ))
			echo ' <p class="description">' . esc_html( $option['desc'] ) . '</p>';		
	}

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

		asort($roles);
		foreach($roles as $role_key=>$role_name)
		{
			$id = sanitize_key( $role_key );
			
			$post_type_name = $role_based_help_notes->clean_post_type_name($role_key);
			$role_active = $this->rbhn_role_active( $role_key, (array) $value )

			// Render the output  
			?> 
			<input 
				type='checkbox'  
				id="<?php echo esc_html( "help_notes_{$id}" ) ; ?>" 
				name="<?php echo esc_html( $option['name'] ); ?>[][<?php echo esc_html( $role_key ) ; ?>]"
				value="<?php echo esc_html( $post_type_name )	; ?>"<?php checked( $role_active ); ?>
			</input>
			<?php echo esc_html( $role_name ) . " <br/>";			
		}								
									
		if ( ! empty( $option['desc'] ))
			echo ' <p class="description">' . esc_html( $option['desc'] ) . '</p>';		
	}
	
	/**
	 * field_plugin_checkbox_option 
	 *
	 * @param array of arguments to pass the option name to render the form field.
	 * @access public
	 * @return void
	 */
	public function field_plugin_checkbox_option( array $args  ) {
		$option   = $args['option'];
		$value = get_option( $option['name'] );
		?><label><input id="setting-<?php echo esc_html( $option['name'] ); ?>" name="<?php echo esc_html( $option['name'] ); ?>" type="checkbox" value="1" <?php checked( '1', $value ); ?> /> <?php 
		$plugin_main_file =  HELP_PLUGIN_DIR . $option['slug'] . '/' .  $option['slug'] . '.php' ;

		if ( ! file_exists( $plugin_main_file ) ) {
			echo esc_html__( 'Enable to prompt installation and force active.', 'role-based-help-notes-text-domain' ) . ' ( ';
			if ( $value ) echo '  <a href="' . TGM_Plugin_Activation::$instance->parent_menu_slug . '?page=install-required-plugins">' .  esc_html__( "Install", 'role-based-help-notes-text-domain' ) . " </a> | " ;
			
		} elseif ( is_plugin_active( $option['slug'] . '/' . $option['slug'] . '.php' ) ) {
			echo esc_html__(  'Force Active', 'role-based-help-notes-text-domain' ) . ' ( ';
			if ( ! $value ) echo '<a href="plugins.php?s=' . esc_html( $option['label'] )	 . '">' .  esc_html__( "Deactivate", 'role-based-help-notes-text-domain' ) . "</a> | " ;	
		} else {
			echo esc_html__(  'Force Active', 'role-based-help-notes-text-domain' ) . ' ( ';
		}
		echo ' <a href="http://wordpress.org/plugins/' . esc_html( $option['slug'] ) . '">' .  esc_html__( "wordpress.org", 'role-based-help-notes-text-domain' ) . " </a> )" ;		
		?></label><?php
		if ( ! empty( $option['desc'] ))
			echo ' <p class="description">' . esc_html( $option['desc'] ) . '</p>';
	}

	/**
	 * field_textarea_option 
	 *
	 * @param array of arguments to pass the option name to render the form field.
	 * @access public
	 * @return void
	 */
	public function field_textarea_option( array $args  ) {
		$option   = $args['option'];
		$value = get_option( $option['name'] );
		?><textarea id="setting-<?php echo esc_html( $option['name'] ); ?>" class="large-text" cols="50" rows="3" name="<?php echo esc_html( $option['name'] ); ?>" <?php echo esc_html( $placeholder ); ?>><?php echo esc_textarea( $value ); ?></textarea><?php

		if ( ! empty( $option['desc'] ))
			echo ' <p class="description">' . esc_html( $option['desc'] ) . '</p>';
	}

	/**
	 * field_select_option 
	 *
	 * @param array of arguments to pass the option name to render the form field.
	 * @access public
	 * @return void
	 */
	public function field_select_option( array $args  ) {
		$option   = $args['option'];
		$value = get_option( $option['name'] );
		?><select id="setting-<?php echo esc_html( $option['name'] ); ?>" class="regular-text" name="<?php echo esc_html( $option['name'] ); ?>"><?php
			foreach( $option['options'] as $key => $name )
				echo '<option value="' . esc_attr( $key ) . '" ' . selected( $value, $key, false ) . '>' . esc_html( $name ) . '</option>';
		?></select><?php

		if ( ! empty( $option['desc'] ))
			echo ' <p class="description">' . esc_html( $option['desc'] ) . '</p>';
	}
	


	/**
	 * field_default_option 
	 *
	 * @param array of arguments to pass the option name to render the form field.
	 * @access public
	 * @return void
	 */
	public function field_default_option( array $args  ) {
		$option   = $args['option'];
		$value = get_option( $option['name'] );
		?><input id="setting-<?php echo esc_html( $option['name'] ); ?>" class="regular-text" type="text" name="<?php echo esc_html( $option['name'] ); ?>" value="<?php esc_attr_e( $value ); ?>" <?php echo esc_html( $placeholder ); ?> /><?php

		if ( ! empty( $option['desc'] ))
			echo ' <p class="description">' . esc_html( $option['desc'] ) . '</p>';
	}
	

	
	
	/**
	 * Renders our tabs in the plugin options page,
	 * walks through the object's tabs array and prints
	 * them one by one.
	 *
	 * @access public
	 * @return void
	 */
	public function plugin_options_tabs() {
		$current_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : $this->default_tab_key;

		screen_icon();
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
			$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
			echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->plugin_options_key . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';	
		}
		echo '</h2>';
	}


	public function rbhn_role_active($role, $active_helpnote_roles) {

		foreach ($active_helpnote_roles as $active_role=>$active_posttype) {
				if (! empty($active_posttype["$role"])) {
					return true;
				}
		}
		return false;
	}


	/**
	 * install_plugins function.
	 *
	 * @access public
	 * @return array of plugins for installation via the TGM_Plugin_Activation class
	 */
	public static function install_plugins() {
		
		self::init_settings();
		$plugin_array = self::$settings['rbhn_plugin_extension']['settings'];
		$plugins = array();
		
		foreach ( $plugin_array as $plugin ) {
		
			if ( get_option( $plugin['name'] ) ) {
				// change the array element key name from 'label' to 'name' for use by TGM Activation
				$plugin['option-name'] = $plugin['name'];
				$plugin['name'] = $plugin['label'];
				unset($plugin['label']);
				$plugins[] = $plugin;
			}
		}
		return $plugins; 
	}
}

/** Create a new instance of the class */
new RBHN_Settings();

?>