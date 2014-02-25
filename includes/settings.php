<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * RBHN_Settings class.
 */
class RBHN_Settings {

	private static $settings = '';
	
	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
	
		// Create a second level settings page
		add_action('admin_menu', array( $this, 'admin_menu' ));
		
		// Initialize the plugin option by registering the Sections,
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		
		///////register_activation_hook( HELP_MYPLUGINNAME_PATH.'role-based-help-notes.php', array( $this, 'help_do_on_activation' ) );

	}

	/**
	 * admin_menu function.
	 *
	 * @access public
	 * @return void
	 */
	public function admin_menu() {
		add_submenu_page( 'options-general.php', __( 'Notes', 'role-based-help-notes-text-domain' ), __( 'Help Notes', 'role-based-help-notes-text-domain' ), 'manage_options', HELP_SETTINGS_PAGE, array( $this, 'settings_page' ) ); 
	}
	
	/**
	 * init_settings function.
	 *
	 * @access private
	 * @return void
	 */
	private static function init_settings() {
		self::$settings = apply_filters( 'role_based_help_notes_settings',
			array(
				'general' => array(
					'title' 		=> __( 'General', 'role-based-help-notes-text-domain' ),
					'description' 	=> __( 'Settings for general purpose.', 'role-based-help-notes-text-domain' ),
					'settings' 		=> array(		
											array(
												'name' 		=> 'rbhn_general_enabled',
												'std' 		=> false,
												'label' 	=> __( 'General Help Notes', 'role-based-help-notes-text-domain' ),
												'cb_label'  => __( 'Enable', 'role-based-help-notes-text-domain' ),
												'desc'		=> __( "Enabling the 'General' option gives you global Help Notes, which are not limited to any one role, these will be accessible to all and follow the capabilities of the normal wordpress 'post' post type.", 'role-based-help-notes-text-domain' ),
												'type'      => 'checkbox'
												),
											array(
												'name' 		=> 'rbhn_user_widget_enabled',
												'std' 		=> false,
												'label' 	=> __( 'Widget', 'role-based-help-notes-text-domain' ),
												'cb_label'  => __( 'Enable', 'role-based-help-notes-text-domain' ),
												'desc'		=> __( "Enabling the 'User Widget' will allow you to place the Help Notes user widget into your sidebars.  The widget lists all users that have access to the Help Notes for a particular role and it is only shown on individual Help Note posts.", 'role-based-help-notes-text-domain' ),
												'type'      => 'checkbox'
												),
											array(
												'name' 		=> 'rbhn_contents_page',
												'std' 		=> '0',
												'label' 	=> __( 'Contents Page', 'download_monitor' ),
												'desc'		=> __( 'If you wish to create a contents page add a new page and select it here so that the Help Note Contents are displayed.', 'role-based-help-notes-text-domain' ),
												'type'      => 'settings_field_help_notes_contents_page',
												),						
										),
				),
				'roles' => array(
					'title' 		=> __( 'Roles', 'role-based-help-notes-text-domain' ),
					'settings' 		=> array(					
											array(
												'name' 		=> 'rbhn_post_types',
												'std' 		=> array(),
												'label' 	=> __( 'Help Note Post Types', 'role-based-help-notes-text-domain' ),
												'desc'		=> __( 'Select the Roles that you wish to create Help Notes for. ', 'role-based-help-notes-text-domain' ),
												'type'      => 'settings_field_help_notes_post_types'
												),					
											),
				),
				'plugin_extension' => array(
						'title' 		=> __( 'Plugin Extensions', 'role-based-help-notes-text-domain' ),
						'description' 	=> __( 'These settings are optional.  Selection of any suggested plugin here will prompt you through the installation or you can go to the Menu ..[Plugins]..[Install Plugins].  The plugin will be forced active while this is selected; deselecting will not remove the plugin, you will need to manually uninstall.', 'role-based-help-notes-text-domain' ),					
						'settings' 		=> array(
												array(
													'name' 		=> 'rbhn_user_switching',
													'std' 		=> false,
													'label' 	=> 'User Switching',
													'cb_label'  => __( 'Enable', 'role-based-help-notes-text-domain' ),
													'desc'		=> __( 'This is a useful plugin for Administrators to test the accessibility of users with different roles, you can simply switch to their account to check how the Help Notes appear for them.', 'role-based-help-notes-text-domain' ),
													'type'      => 'install_plugin_checkbox',
													// for tgmpa_register activation of the plugin
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
													'type'      => 'install_plugin_checkbox',
													// for tgmpa_register activation of the plugin
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
													'type'      => 'install_plugin_checkbox',
													// for tgmpa_register activation of the plugin
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
													'desc'		=> __( 'Comments are of less value for Help Notes and this plugin will allow you to easily remove their comments from use.', 'role-based-help-notes-text-domain' ),
													'type'      => 'install_plugin_checkbox',
													// for tgmpa_register activation of the plugin
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
													'type'      => 'install_plugin_checkbox',
													// for tgmpa_register activation of the plugin
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
													'desc'		=> __( "This plugin will allow users with two or more roles capability to change the role assigned to a help note.  Once installed within you will find a new selection/edit option in the 'Publish' area.", 'role-based-help-notes-text-domain' ),
													'type'      => 'install_plugin_checkbox',
													// for tgmpa_register activation of the plugin
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
													'type'      => 'install_plugin_checkbox',
													// for tgmpa_register activation of the plugin
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
	 * register_settings function.
	 *
	 * @access public
	 * @return void
	 */
	public function register_settings() {

		self::init_settings();
		foreach ( self::$settings as $section ) {
			foreach ( $section['settings'] as $option ) {
				if ( isset( $option['std'] ) )
					add_option( $option['name'], $option['std'] );
				register_setting( 'role_based_help_notes', $option['name'] );
			}
		}
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
	 * settings_page function.
	 *
	 * @access public
	 * @return void
	 */
	public function settings_page() {
	
		global $role_based_help_notes;

		// do a one shot during save of options
		if ( get_option( 'rbhn_update_request' ) ) {

				update_option( 'rbhn_update_request', '' );
				//help_do_on_activation();	// add the active capabilities
				rbhn_clean_inactive_capabilties();						// remove the inactive role capabilities

		}

		self::init_settings();
		?>
		<div class="wrap">
			<form method="post" action="options.php">

				<?php settings_fields( 'role_based_help_notes' ); ?>
				<?php screen_icon( 'options-general' ); ?>

			    <h2 class="nav-tab-wrapper">
			    	<?php
			    		foreach ( self::$settings as $key => $section ) {
			    			echo '<a href="#settings-' . sanitize_title( $key ) . '" class="nav-tab">' . esc_html( $section['title'] ) . '</a>';
			    		}
			    	?>
			    </h2><br/>

				<?php
					if ( ! empty( $_GET['settings-updated'] ) ) {
						flush_rewrite_rules();
					}
			
					foreach ( self::$settings as $key => $section ) {

						echo '<div id="settings-' . sanitize_title( $key ) . '" class="settings_panel">';
						if ( isset( $section['description'] ) )
							echo ' <p>' . $section['description']  . '</p>';	
						echo '<table class="form-table">';

						foreach ( $section['settings'] as $option ) {

							$placeholder = ( ! empty( $option['placeholder'] ) ) ? 'placeholder="' . $option['placeholder'] . '"' : '';

							echo '<tr valign="top"><th scope="row"><label for="setting-' . $option['name'] . '">' . $option['label'] . '</a></th><td>';

							if ( ! isset( $option['type'] ) ) $option['type'] = '';

							$value = get_option( $option['name'] );

							switch ( $option['type'] ) {
							
								// Generate Help Note Role Form Elements
							
								case "settings_field_help_notes_post_types" :
								
									//  loop through the site roles and create a custom post for each
									global $wp_roles;
									global $role_based_help_notes;
									
									if ( ! isset( $wp_roles ) )
									$wp_roles = new WP_Roles();
									
									$roles = $wp_roles->get_names();

									ksort($roles);
									foreach($roles as $role_key=>$role_name)
									{
										$id = sanitize_key( $role_key );
										
										$post_type_name = $role_based_help_notes->clean_post_type_name($role_key);
										$role_active = $this->rbhn_role_active( $role_key, (array) $value )

										// Render the output  
										?> 
										<input 
											type='checkbox'  
											id="<?php echo "help_notes_{$id}" ; ?>" 
											name="<?php echo $option['name']; ?>[][<?php echo $role_key ; ?>]"
											value="<?php echo $post_type_name	; ?>"<?php checked( $role_active ); ?>
										</input>
										<?php echo " $role_name<br/>";			
									}								
																
									if ( $option['desc'] )
										echo ' <p class="description">' . $option['desc'] . '</p>';		

								break;
							
								// Generate Help Note Content Page Form Element
								
								case "settings_field_help_notes_contents_page" :
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
	
									if ( $option['desc'] )
										echo ' <p class="description">' . $option['desc'] . '</p>';		
										
								break;
								
								// Generic Form items..
								
								case "checkbox" :

									?><label><input id="setting-<?php echo $option['name']; ?>" name="<?php echo $option['name']; ?>" type="checkbox" value="1" <?php checked( '1', $value ); ?> /> <?php echo $option['cb_label']; ?></label><?php

									if ( $option['desc'] )
										echo ' <p class="description">' . $option['desc'] . '</p>';

								break;
								
								
								case "install_plugin_checkbox" :

									?><label><input id="setting-<?php echo $option['name']; ?>" name="<?php echo $option['name']; ?>" type="checkbox" value="1" <?php checked( '1', $value ); ?> /> <?php 
									$plugin_main_file =  HELP_PLUGIN_DIR . $option['slug'] . '/' .  $option['slug'] . '.php' ;

									//echo $plugin_main_file ; 
									if ( ! file_exists( $plugin_main_file ) ) {
										echo __( 'Enable to prompt installation', 'role-based-help-notes-text-domain' ) ;									
									} elseif ( is_plugin_active( $option['slug'] . '/' . $option['slug'] . '.php' ) ) {
										echo __(  'Force Active', 'role-based-help-notes-text-domain' ) .  '  ( <a href="plugins.php?s=' . $option['slug'] . '">' .  __( "Deactivate", 'role-based-help-notes-text-domain' ) . " </a> )" ;
									} else {
										echo __(  'Force Active', 'role-based-help-notes-text-domain' ) .  '  ( <a href="plugins.php?s=' . $option['slug'] . '">' .  __( "Active", 'role-based-help-notes-text-domain' ) . " </a> )" ;
									}
									?></label><?php
									if ( $option['desc'] )
										echo ' <p class="description">' . $option['desc'] . '</p>';

								break;
									
								case "textarea" :

									?><textarea id="setting-<?php echo $option['name']; ?>" class="large-text" cols="50" rows="3" name="<?php echo $option['name']; ?>" <?php echo $placeholder; ?>><?php echo esc_textarea( $value ); ?></textarea><?php

									if ( $option['desc'] )
										echo ' <p class="description">' . $option['desc'] . '</p>';

								break;
								case "select" :

									?><select id="setting-<?php echo $option['name']; ?>" class="regular-text" name="<?php echo $option['name']; ?>"><?php
										foreach( $option['options'] as $key => $name )
											echo '<option value="' . esc_attr( $key ) . '" ' . selected( $value, $key, false ) . '>' . esc_html( $name ) . '</option>';
									?></select><?php

									if ( $option['desc'] )
										echo ' <p class="description">' . $option['desc'] . '</p>';

								break;
								default :

									?><input id="setting-<?php echo $option['name']; ?>" class="regular-text" type="text" name="<?php echo $option['name']; ?>" value="<?php esc_attr_e( $value ); ?>" <?php echo $placeholder; ?> /><?php

									if ( $option['desc'] )
										echo ' <p class="description">' . $option['desc'] . '</p>';

								break;

							}

							echo '</td></tr>';
						}

						echo '</table></div>';
					}

				?>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'role_based_help_notes' ); ?>" />
				</p>
		    </form>
		</div>
		<?php

		$role_based_help_notes->add_inline_js("
			jQuery('.nav-tab-wrapper a').click(function() {
				jQuery('.settings_panel').hide();
				jQuery('.nav-tab-active').removeClass('nav-tab-active');
				jQuery( jQuery(this).attr('href') ).show();
				jQuery(this).addClass('nav-tab-active');
				return false;
			});
			jQuery('#setting-dlm_default_template').change(function(){
				if ( jQuery(this).val() == 'custom' ) {
					jQuery('#setting-dlm_custom_template').closest('tr').show();
				} else {
					jQuery('#setting-dlm_custom_template').closest('tr').hide();
				}
			}).change();

			jQuery('.nav-tab-wrapper a:first').click();
		");
	}
	
	/**
	 * install_plugins function.
	 *
	 * @access public
	 * @return array of plugins for installation via the TGM_Plugin_Activation class
	 */
	public static function install_plugins() {
		
		self::init_settings();
		$plugin_array = self::$settings['plugin_extension']['settings'];
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