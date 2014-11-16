<?php
/**
 * Plugin tabbed settings option class for WordPress themes.
 *
 * @package   class-tabbed-settings.php
 * @version   1.1.4
 * @author    Justin Fletcher <justin@justinandco.com>
 * @copyright Copyright ( c ) 2014, Justin Fletcher
 * @license   http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 *
 * The text domain must be manually replaced with the required plugin text domain.
 */

 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Everything is pulled into this Class to allow for extendibility with including
 * new callouts that are required on a plugin by plugin basis.
 */
if ( ! class_exists( 'Extendible_Tabbed_Settings' ) ) { 
	Class Extendible_Tabbed_Settings  {

		private $handlers = array( );
		
		public function registerHandler( $handler ) {
			$this->handlers[] = $handler;
		}

		public function __call( $method, $arguments ) {
			foreach ( $this->handlers as $handler ) {
				if ( method_exists( $handler, $method ) ) {
					return call_user_func_array(
						array( $handler, $method ),
						$arguments
					);
				}
			}
		}
		
	}
}

if ( ! class_exists( 'Tabbed_Settings' ) ) {

	/**
	 * Tabbed_Settings class.
	 */
	class Tabbed_Settings extends Extendible_Tabbed_Settings {

//		public static $instance;
//		public $settings = array( );
//		public $config = array( );


		// the following are configurable externally
        public $menu_access_capability = '';
        public $menu_parent = '';
        public $menu = '';
        public $menu_title = '';
        public $page_title = '';
		public $default_tab_key = '';

		/**
		 * __construct function.
		 *
		 * @access public
		 * @return void
		 */	 
		function __construct( $settings, $config ) {

			$this->settings = $settings;
			$this->register_config( $config );

			// hook priority = 9 to load settings before the class-tgm-plugin-activation.php runs with the same init hook at priority level 10
			add_action( 'init', array( $this, 'init' ), 9 );
			
			add_action( 'admin_init', array( $this, 'render_setting_page' ) );

			add_action( 'admin_menu', array( $this, 'add_admin_menus' ) );

			add_action( 'do_settings_sections', array( $this, 'hooks_section_callback' ) );

		}

		/**
		 * init function.
		 *
		 * @access private
		 * @return void
		 */
		public function init( ) {
		
			do_action( 'tabbed_settings_register' );
			// After this point, the settings should be registered and the configuration set.
			
		}

		/**
		 * Called during admin_menu, adds rendered using the plugin_options_page method.
		 *
		 * @access public
		 * @return void
		 */	 
		public function add_admin_menus( ) {

			add_submenu_page( $this->menu_parent, $this->page_title, $this->menu_title, $this->menu_access_capability, $this->menu, array( &$this, 'plugin_options_page' ) );
			
		}


        /**
         * Add individual tabbed_settings to our collection of settings.
         *
         * If the required keys are not set or the tabbed_settings has already
         * been registered, the plugin is not added.
         *
         * @since 2.0.0
         *
         * @param array $tabbed_settings Array of plugin arguments.
         */
        public function register_tabbed_settings( $settings ) {

			foreach ( $settings as $tab_name => $registered_setting_page ) {
				$this->settings[$tab_name] = $registered_setting_page;	
			}	
        }

        /**
         * Amend default configuration.  This function strips out the config array elements and stores them 
         * as a variable within the current Class object $this->"config-element" will be the means to return the 
         * current configuration stored.
         *
         * @param array $config Array of config options to pass as class properties.
		 * @return - sets up the CLASS object values $this->config.
         */
        public function register_config( $config ) {

            $keys = array( 
						'default_tab_key',
						'menu_parent',
						'menu_access_capability',
						'menu',
						'menu_title',
						'page_title',
					);

            foreach ( $keys as $key ) {
                if ( isset( $config[$key] ) ) {
                    if ( is_array( $config[$key] ) ) {
                        foreach ( $config[$key] as $subkey => $value ) {
                           $this->{$key}[$subkey] = $value;
                        }
                    } else {
                        $this->$key = $config[$key];
                    }
                }
            }
        }
		
		/**
		 * render_setting_page function.
		 *
		 * @access public
		 * @return void
		 */
		public function render_setting_page( ){

			foreach ( $this->settings as $options_group => $section  ) {

				if ( isset( $section['settings'] ) ) {
					foreach ( $section['settings'] as $option ) {
						
						if ( isset( $option['std'] ) )
							add_option( $option['name'], $option['std'] );
						
						$sanitize_callback = ( isset( $option['sanitize_callback'] ) ? $option['sanitize_callback'] : "" );
						register_setting( $options_group, $option['name'], $sanitize_callback );
						add_settings_section( $options_group, $section['title'], array( $this, 'hooks_section_callback' ), $options_group );
						
						$callback_type = ( isset( $option['type'] ) ? $option['type'] : "field_default_option" );
						add_settings_field( $option['name'].'_setting-id', $option['label'], array( $this, $callback_type ), $options_group, $options_group, array( 'option' => $option ) );	
					}
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
		public function plugin_options_page( ) {

			$tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : $this->default_tab_key;
			if ( isset( $_GET['settings-updated'] ) ) {		
				do_action( 'tabbed_settings_after_update' );
			}

			?>
			<div class="wrap">
				<?php $this->plugin_options_tabs( ); ?>
				<form method="post" action="options.php">
					<?php wp_nonce_field( 'update-options-nonce', 'update-options' ); ?>
					<?php settings_fields( $tab ); ?>
					<?php do_settings_sections( $tab ); ?>
					<?php submit_button( ); ?>
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
		public function hooks_section_callback( $section_passed ){
			foreach ( $this->settings as $options_group => $section  ) {
				if ( ( $section_passed['id'] == $options_group ) && ( ! empty( $section['description'] ) ) ) {	
					echo esc_html( $this->settings[$options_group]['description'] );	
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
			if ( ! empty( $option['desc'] ) )
			echo ' <p class="description">' .  $option['desc'] . '</p>';
		}

		/**
		 * field_page_select_list_option 
		 *
		 * @param array of arguments to pass the option name to render the form field.
		 * @access public
		 * @return void
		 */
		public function field_page_select_list_option( array $args  ) {
		
			$option	= $args['option'];
			
			?><label for="<?php echo $option['name']; ?>"><?php 
			wp_dropdown_pages( array( 
									'name' => $option['name'],
									'id'         	=> 'setting-' . $option['name'],
									'echo' => 1, 
									'hierarchical'  => 0,
									'sort_order'   	=> 'ASC',
									'sort_column'  	=> 'post_title',
									'show_option_none' => _x( "- None -", 'text for no selection', 'role-based-help-notes-text-domain' ), 
									'option_none_value' => '0', 
									'selected' => get_option( $option['name'] ) 
									) 
								); ?>
			</label>

			
			<?php
			if ( ! empty( $option['desc'] ) )
				echo ' <p class="description">' . $option['desc'] . '</p>';		
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
			$filename = ( isset( $option['filename'] ) ? $option['filename'] : $option['slug'] );
			$plugin_main_file =  trailingslashit( $option['plugin_dir']. $option['slug'] ) .  $filename . '.php' ;
			$value = get_option( $option['name'] );

			if ( is_plugin_active_for_network( $option['slug'] . '/' . $filename . '.php' ) ) {
				?><label><input id="setting-<?php echo esc_html( $option['name'] ); ?>" name="<?php echo esc_html( $option['name'] ); ?>" type="checkbox" disabled="disabled" checked="checked"/> <?php
			} else {
				?><label><input id="setting-<?php echo esc_html( $option['name'] ); ?>" name="<?php echo esc_html( $option['name'] ); ?>" type="checkbox" value="1" <?php checked( '1', $value ); ?> /> <?php 
			}

			if ( ! file_exists( $plugin_main_file ) ) {
				echo esc_html__( 'Enable to prompt installation and force active.', 'role-based-help-notes-text-domain' ) . ' ( ';
				if ( $value ) echo '  <a href="' . add_query_arg( 'page', TGM_Plugin_Activation::$instance->menu, admin_url( 'themes.php' ) ) . '">' .  _x( 'Install', 'Install the Plugin', 'role-based-help-notes-text-domain' ) . " </a> | " ;
				
			} elseif ( is_plugin_active( $option['slug'] . '/' . $option['slug'] . '.php' ) &&  ! is_plugin_active_for_network( $option['slug'] . '/' . $option['slug'] . '.php' ) ) {
				echo esc_html__( 'Force Active', 'role-based-help-notes-text-domain' ) . ' ( ';
				if ( ! $value ) echo '<a href="plugins.php?s=' . esc_html( $option['label'] )	 . '">' .  _x( 'Deactivate', 'deactivate the plugin', 'role-based-help-notes-text-domain' ) . "</a> | " ;	
			} else {
				echo esc_html__( 'Force Active', 'role-based-help-notes-text-domain' ) . ' ( ';
			}
			echo ' <a href="http://wordpress.org/plugins/' . esc_html( $option['slug'] ) . '">' .  esc_html__( "wordpress.org", 'role-based-help-notes-text-domain' ) . " </a> )" ;		
			?></label><?php
			if ( ! empty( $option['desc'] ) )
				echo ' <p class="description">' .  $option['desc']  . '</p>';
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
			$defaults = array( 	
						'columns' => "40",
						'rows' => "3",
						);
				
			$option = wp_parse_args( $option, $defaults );
			$value = get_option( $option['name'] );
			?><textarea id="setting-<?php echo esc_html( $option['name'] ); ?>" cols=<?php echo $option['columns']; ?> rows=<?php echo $option['rows']; ?> name="<?php echo esc_html( $option['name'] ); ?>" ><?php echo esc_textarea( $value ); ?></textarea><?php

			if ( ! empty( $option['desc'] ) )
				echo ' <p class="description">' . $option['desc'] . '</p>';
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

			if ( ! empty( $option['desc'] ) )
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
			?><input id="setting-<?php echo esc_html( $option['name'] ); ?>" class="regular-text" type="text" name="<?php echo esc_html( $option['name'] ); ?>" value="<?php esc_attr_e( $value ); ?>" /><?php

			if ( ! empty( $option['desc'] ) )
				echo ' <p class="description">' . $option['desc'] . '</p>';
		}
		
		/**
		 * Renders our tabs in the plugin options page,
		 * walks through the object's tabs array and prints
		 * them one by one.
		 *
		 * @access public
		 * @return void
		 */
		public function plugin_options_tabs( ) {
		
			$current_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : $this->default_tab_key;

			screen_icon( );
			echo '<h2 class="nav-tab-wrapper">';
			foreach ( $this->settings as $tab_key => $tab_options_array ) {
				$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
				echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->menu . '&tab=' . $tab_key . '">' . $tab_options_array['title'] . '</a>';	
			}
			echo '</h2>';
		}


		/**
		 * selected_plugins function.
		 *
		 * @access public
		 * @return array of plugins selected within the settings page for installation via the TGM_Plugin_Activation class
		 */
		public function selected_plugins( $plugin_extension_tab_name ) {

			$plugins = array( );

			if ( isset( $this->settings ) ) {

				$plugin_array = $this->settings[$plugin_extension_tab_name]['settings'];
				
				foreach ( $plugin_array as $plugin ) {

					if ( get_option( $plugin['name'] ) ) {
						// change the array element key name from 'label' to 'name' for use by TGM Activation
						$plugin['option-name'] = $plugin['name'];
						$plugin['name'] = $plugin['label'];
						unset( $plugin['label'] );
						$plugins[] = $plugin;
					}
				}

			}
			
			return $plugins;
		}
		
	}
	
}

?>