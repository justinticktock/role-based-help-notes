<?php
/*
Plugin Name: Role Based Help Notes
Plugin URI: http://justinandco.com/plugins/role-based-help-notes/
Description: The addition of Custom Post Type to cover site help notes
Version: 1.3.1
Author: Justin Fletcher
Author URI: http://justinandco.com
Text Domain: role-based-help-notes-text-domain
Domain Path: /languages/
License: GPLv2 or later
*/


if ( ! defined( 'ABSPATH' )) {
	exit; // Exit if accessed directly
}


/**
 * RBHN_Role_Based_Help_Notes class.
 *
 * Main Class which inits the CPTs and plugin
 */
class RBHN_Role_Based_Help_Notes {
	
	// Refers to a single instance of this class.
    private static $instance = null;
	
    public	 $plugin_full_path;
	public   $plugin_file = 'role-based-help-notes/role-based-help-notes.php';
	
	// Settings page slug	
    public	 $menu = 'notes-settings';
	
	// Settings Admin Menu Title
    public	 $menu_title = 'Help Notes';
	
	// Settings Page Title
    public	 $page_title = 'Help Notes';
	
	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	private function __construct() {
	
		$this->plugin_full_path = plugin_dir_path(__FILE__) . 'role-based-help-notes.php' ;
		
		// Set the constants needed by the plugin.
		add_action( 'plugins_loaded', array( $this, 'constants' ), 1 );
		
		/* Load the functions files. */
		add_action( 'plugins_loaded', array( $this, 'includes' ), 2 );
			
		/* Hooks... */
		
		// Attached to set_current_user. Loads the plugin installer CLASS after themes are set-up to stop duplication of the CLASS.
		add_action( 'set_current_user', array( $this, 'set_current_user' ));
		
		// register admin side - Loads the textdomain, upgrade routine and menu item.
		add_action( 'admin_init', array( $this, 'admin_init' ));
        add_action( 'admin_menu', array( $this, 'admin_menu' ), 9 );  // increase priority to stop cpt's overwriting the menu.
		
		// register the selected-active Help Note post types
		add_action( 'init', array( $this, 'init' ));

		// Load admin error messages	
		add_action( 'admin_init', array( $this, 'deactivation_notice' ));
		add_action( 'admin_notices', array( $this, 'action_admin_notices' ));
	
		// Add Contents Details to the Contents page if declared in settings ..
		add_filter( 'the_content', array( $this, 'rbhn_add_post_content' ), 12 );
		
		// Add the Help Note Custom Post Types to the author post listing
		add_filter( 'pre_get_posts', array( $this, 'rbhn_custom_post_author_archive' ));

	}
	

	/**
	 * Defines constants used by the plugin.
	 *
	 * @return void
	 */
	function constants() {

		// Define constants
		define( 'HELP_MYPLUGINNAME_PATH', plugin_dir_path(__FILE__));
		define( 'HELP_PLUGIN_DIR', trailingslashit( plugin_dir_path( HELP_MYPLUGINNAME_PATH )));
		define( 'HELP_MENU_PAGE', 'notes.php' );
		
		// admin prompt constants
		define( 'PROMPT_DELAY_IN_DAYS', 30);
		define( 'PROMPT_ARGUMENT', 'rbhn_hide_notice' );

	}

	/**
	 * Loads the initial files needed by the plugin.
	 *
	 * @return void
	 */
	function includes() {

		// settings 
		require_once( HELP_MYPLUGINNAME_PATH . 'includes/settings.php' );
		//if ( help_notes_available()) {
			require_once( HELP_MYPLUGINNAME_PATH . 'includes/class-rbhn-taxonomy.php' ); 		
		//}
		 
		// custom post type capabilities
		require_once( HELP_MYPLUGINNAME_PATH . 'includes/class-rbhn-capabilities.php' );  

		// Load the widgets functions file.
		require_once( HELP_MYPLUGINNAME_PATH . 'includes/widgets.php' );

		// Load code for better compatibility with other plugins.
		require_once( HELP_MYPLUGINNAME_PATH . 'includes/plugin-compatibility.php' );
		
	}


	/**
	 * Initialise the plugin installs
	 *
	 * @return void
	 */
	public function set_current_user() {

		// install the plugins and force activation if they are selected within the plugin settings
		require_once( HELP_MYPLUGINNAME_PATH . 'includes/plugin-install.php' );
		
	}
        
    /**
	 * Initialise the plugin menu. 
	 *
	 * @return void
	 */
	public function admin_menu() {
		
		if ( ! help_notes_available())
			return; 
		
		// check if no help notes are selected before adding the menu ...
		//   Roles are selected in the settings.
		//   OR the general Help Notes is selected in the settings.
		//   OR there are help note posts created and viewable.
		if ( array_filter(( array )  get_option( 'rbhn_post_types' )) || get_option( 'rbhn_general_enabled' ) || help_notes_available()) {
				add_menu_page( _x( 'Help Notes', 'the help notes text to be displayed in the title tags of the page when the menu is selected', 'role-based-help-notes-text-domain' ), __( 'Help Notes', 'the help notes title in the admin menu',  'role-based-help-notes-text-domain' ), 'read', HELP_MENU_PAGE, array( &$this, 'menu_page' ), 'dashicons-format-aside', '5.123123123' );
		}	
	}
    
	/**
	 * menu_page: 
	 *
	 * @return content for the top menu page
	 */
	public function menu_page() {
		// This is used as the first Help Note custom post type top level menu:
		
		$welcome_page_id = get_option( 'rbhn_welcome_page' ) ;
		
		if ( isset( $welcome_page_id ) && ( $welcome_page_id <> 0 )) {
		
			$welcome_post = get_post( $welcome_page_id ) ;
			$welcome_content = $welcome_post->post_content ;
			
			echo "<h1>" . $welcome_post->post_title . "</h1>";
			
			echo $welcome_content;
		} else {
			echo "<h1>" . __( 'Help Notes', 'role-based-help-notes-text-domain' ) . "</h1>";
			
		}
		
	}	
		
	/**
	 * Initialise the plugin by handling upgrades and loading the text domain. 
	 *
	 * @return void
	 */
	public function admin_init() {

		$plugin_current_version = get_option( 'rbhn_plugin_version' );
		$plugin_new_version =  $this->plugin_get_version();
		
		// Admin notice hide prompt notice catch
		$this->catch_hide_notice();

		if ( empty( $plugin_current_version ) || $plugin_current_version < $plugin_new_version ) {
		
			$plugin_current_version = isset( $plugin_current_version ) ? $plugin_current_version : 0;

			$this->upgrade( $plugin_current_version );

			// set default options if not already set..
			$this->plugin_do_on_activation();
			
			// Update the option again after upgrade() changes and set the current plugin revision	
			update_option( 'rbhn_plugin_version', $plugin_new_version ); 
		}
			
		load_plugin_textdomain( 'role-based-help-notes-text-domain', false, dirname( plugin_basename( __FILE__ )) . '/languages/' );
		
		
		// De-register help note post types for the email_post_changes plugin
		if ( isset( $_GET['page'] ) && ( $_GET['page'] == 'email_post_changes' )) {
			$this->deregister_helpnote_for_settings_page_of_email_post_changes_plugin();
		}		
		
				
	}

	/**
	 * Provides an upgrade path for older versions of the plugin
	 *
	 * @param float $current_plugin_version the local plugin version prior to an update 
	 * @return void
	 */
	public function upgrade( $plugin_version ) {
		
		// Upgrade path..
		if ( $plugin_version < '1.3.1' ) {

			// rename the widget setting as it appls to multiple widgets now.
			$value = get_option( 'rbhn_user_widget_enabled' );
			update_option( 'rbhn_widgets_enabled', $value ); 
			delete_option( 'rbhn_user_widget_enabled' );
			
			// remove the Help Notes from the email-post-changes settings, due to 
			// security reasons of non-members of a role getting the Help note changes
			// email this is now only catered for by the role-base-help-notes-extra plugin
			// ref www.justinandco.com/plugins/user-upgrade-capability-extra/
			
			// collect the email-post-changes options for the active post_types
			$email_post_types_options = ( array ) get_option( 'email_post_changes' );
			if ( $email_post_types_options ) {
				$email_post_changes_post_types = (( array ) $email_post_types_options['post_types'] );
				$rbhn_post_changes_post_types = array_values( $this->site_help_notes());
				$new_email_post_changes_post_types = array_diff( $email_post_changes_post_types, $rbhn_post_changes_post_types );
				$email_post_types_options['post_types'] = $new_email_post_changes_post_types;
				update_option( 'email_post_changes', $email_post_types_options );
			}			
		}
	}
	
	/**
	 * Checks if a particular user has a role. 
	 * Returns true if a match was found.
	 *
	 * @param string $role Role name.
	 * @param int $user_id (Optional ) The ID of a user. Defaults to the current user.
	 * @return bool
	 */
	public function help_notes_current_user_has_role( $role, $user_id = null ) {
	 
		if ( is_numeric( $user_id ))
		$user = get_userdata( $user_id );
		else
			$user = wp_get_current_user();
	 
		if ( empty( $user ))
		return false;
	 
		return in_array( $role, ( array ) $user->roles );
	}

	
	/**
	 * Returns array listing all roles with the Help Note Custom Post Types
	 *
	 * @access public
	 * @return array of all site Help Notes post types
	 */		
	public function site_help_notes() {	
	
		//  loop through the site roles and create a custom post for each
		global $wp_roles;
		
		$general_help_enabled 	= get_option( 'rbhn_general_enabled' );
		
		if ( ! isset( $wp_roles ))
		$wp_roles = new WP_Roles();

		$roles = $wp_roles->get_names(); 
		asort( $roles );
		
		$site_help_notes = array();
		
		foreach( $roles as $role_key=>$role_name )
		{
			$id = sanitize_key( $role_key );
			
			$post_type_name = $this->clean_post_type_name( $role_key );
			$site_help_notes[$role_key] = $post_type_name;
		}
		
	   if ( isset( $general_help_enabled ) && ! empty( $general_help_enabled )) {

			$site_help_notes[] = "h_general"; 
		}

		return $site_help_notes;

	}
		
		
	/**
	 * Returns full list the selected and enabled Help Note Custom Post Types
	 *
	 * @access public
	 * @return array of active Help Notes post types
	 */		
	public function enabled_help_notes() {

		// option collection  
		$general_help_enabled 	= get_option( 'rbhn_general_enabled' );
		$post_types_array_set 	= get_option( 'rbhn_post_types' );

		$enabled_posttypes		= array();
		//$post_types_array 		= array_values( $post_types_array );
		
		foreach( $post_types_array_set as $array=>$h_posttype ) {
		
				$enabled_posttypes = array_merge( $enabled_posttypes, array_values( $h_posttype ));				
		}



		
	   if ( isset( $general_help_enabled ) && ! empty( $general_help_enabled )) {

			$enabled_posttypes[] = "h_general"; 
		}

		return $enabled_posttypes;
	}

	/**
	 * Returns the selected-active and active Help Note Custom Post Types
	 * for the current user. If the $user_id argument is passed then this will provide an array of post_types
	 * for that user.
	 *
	 * @access public
	 * @param integer $user_id User ID or null	 
	 * @return array of active Help Notes post types
	 */		
	public function active_help_notes( $user_id = null  ) {

		$active_posttypes = array();

		//  loop through the site roles and create a custom post for each
		global $wp_roles;
		
		// Load roles if not set
		if ( ! isset( $wp_roles ))
			$wp_roles = new WP_Roles();

		$roles = $wp_roles->get_names();


		// option collection  
		$general_help_enabled 	= get_option( 'rbhn_general_enabled' );
		$post_types_array 		= get_option( 'rbhn_post_types' );

	    if ( isset( $general_help_enabled ) && ! empty( $general_help_enabled )) {
			$active_posttypes[] = "h_general"; 
		}

		if (  ! empty( $post_types_array )) {	
			foreach( $post_types_array as $array ) {
				foreach( $array as $active_role=>$h_posttype ) {
					if ( $this->help_notes_current_user_has_role( $active_role , $user_id )) {
						$active_posttypes[] = $h_posttype;
					}				
				}
			}	
		}

		return $active_posttypes;
	}
	
	public function is_single_help_note() {
	
       // drop out if not a single Help Note page or Help Hote Archive page.
       // or the General Help Note Type
       $ssl_help_notes = $this->active_help_notes();
       $exclude_help_notes = array( 'h_general' );
       $ssl_help_notes = array_diff( $ssl_help_notes, $exclude_help_notes );
       
        if ( ! in_array( get_post_type(),  $ssl_help_notes ) || is_archive()) {
            return false; 
		} else {
            return true; 
		}
			
	}
	
    /**
	 * Returns the array of active Help Notes roles or if a post_type is provided the single role associated.
	 *
	 * @access public
	 * @param string $help_note_post_type post_type name.	 
	 * @return array of active Help Notes roles
	 */		
	public function help_notes_role( $help_note_post_type  = null ) {
		$post_types_array 		= get_option( 'rbhn_post_types' );

		$help_note_role = array();
		if (  ! empty( $post_types_array )) {
			foreach( $post_types_array as $array ) {	
				foreach( $array as $active_role=>$active_posttype ) {
					if ( $help_note_post_type == $active_posttype ) {
						return $active_role; 
					}
					$help_note_role[] = $active_role;
				}
			}
		}
		return $help_note_role; 
	}
	
	/**
	 * Include the Help Note Custom Post Type in the author post listing
	 *
	 * @access public
	 * @param object $query 
	 * @return void
	 */		
	function rbhn_custom_post_author_archive( $query ) {
		$role_based_help_notes = RBHN_Role_Based_Help_Notes::get_instance();
		
		if( !is_admin() && $query->is_main_query() && empty( $query->query_vars['suppress_filters'] )) {

			// For author queries add Help Note post types
			if ( $query->is_author ) {
				$include_post_types = $role_based_help_notes->active_help_notes();
				$include_post_types[] = 'post';
				$query->set( 'post_type', $include_post_types );
			}

			// remove the filter after running, run only once!
			remove_action( 'pre_get_posts', 'rbhn_custom_post_author_archive' ); 
		}
	}    

	/**
	 * Registers all required Help Notes
	 *
	 * @access public	 
	 * @return void
	 */
	public function init() {

		$this->action_init_store_user_meta();
		
		// option collection  
		$general_help_enabled 	= get_option( 'rbhn_general_enabled' );
		$post_types_array 		= get_option( 'rbhn_post_types' );
		
		
		if ( isset( $general_help_enabled ) && ! empty( $general_help_enabled )) {
			// generate a genetic help note post type
			call_user_func_array( array( $this, 'help_register_posttype' ), array( 'general', 'General (Public )', 'h_general' ));  
		}
		 
		
		//  loop through the site roles and create a custom post for each
		global $wp_roles;
		
		// Load roles if not set
		if ( ! isset( $wp_roles ))
			$wp_roles = new WP_Roles();

		$roles = $wp_roles->get_names();

		if (  ! empty( $post_types_array )) {
			foreach( $post_types_array as $array ) {	
				foreach( $array as $active_role=>$active_posttype ) {
					if ( array_key_exists ( $active_role, $roles )) {
						if ( $this->help_notes_current_user_has_role( $active_role )) {
							call_user_func_array( array( $this, 'help_register_posttype' ), array( $active_role, $roles[$active_role], $active_posttype )); 
						}
					} 
				}
			}
		}
		

		// Add the HelpNotesExtra Email Methods to the Settings CLASS
		if ( class_exists( 'RBHNE_Settings' )) {
			RBHN_Settings::get_instance()->registerHandler( new RBHNE_Settings_Additional_Methods());
		}
		
		
	}

	/**
	 * Registers custom post type for a single Help Note
	 *
	 * @access public
	 * @param text $role_key Role Key id
	 * @param text $role_name Role Name
	 * @param text $post_type_name Post Type name/id	 
	 * @return void
	 */	
	public function help_register_posttype( $role_key, $role_name, $post_type_name ) {
		
		$role_name = ( strcasecmp( "note", $role_name ) ?  $role_name . ' ' : '' );

		$help_labels = array(

			'name'               => sprintf( _x( '%1$s Notes', 'name', 'role-based-help-notes-text-domain' ), $role_name ),
			'singular_name'      => sprintf( _x( '%1$s Note', 'singular_name', 'role-based-help-notes-text-domain' ), $role_name ),
			'add_new'            => _x( 'Add New', 'Help Notes', 'role-based-help-notes-text-domain' ),
			'add_new_item'       => sprintf( _x( 'Add New %1$s Note', 'Help Notes', 'role-based-help-notes-text-domain' ), $role_name ),
			'edit_item'          => sprintf( _x( 'Edit %1$s Note', 'Help Notes', 'role-based-help-notes-text-domain' ), $role_name ),
			'new_item'           => sprintf( _x( 'New %1$s Note', 'Help Notes', 'role-based-help-notes-text-domain' ), $role_name ),
			'view_item'          => sprintf( _x( 'View %1$s Note', 'Help Notes', 'role-based-help-notes-text-domain' ), $role_name ),
			'search_items'       => sprintf( _x( 'Search %1$s Notes', 'Help Notes', 'role-based-help-notes-text-domain' ), $role_name ),
			'not_found'          => sprintf( _x( 'No %1$s Notes found', 'Help Notes', 'role-based-help-notes-text-domain' ), $role_name ),
			'not_found_in_trash' => sprintf( _x( 'No %1$s Notes found in Trash', 'Help Notes', 'role-based-help-notes-text-domain' ), $role_name ),
			'parent_item_colon'  => '',
			'menu_name'          =>  $role_name,
		);
		
		if ( $role_key == 'general' ) {
			$help_capabilitytype    = 'post';
			$explicitly_mapped_caps	= array();
		} else {
			$help_capabilitytype    = $post_type_name;
			$explicitly_mapped_caps = array( 'create_posts' 	=> 'create_' . $help_capabilitytype . 's' );
		};
		
		// Place the help notes under the main menu by default
		// However, if the focus is on a specific Help Note then add this to the main menu.
		$show_in_menu =   HELP_MENU_PAGE ;
		if (( isset( $_GET['post_type'] ) && ( $post_type_name === $_GET['post_type'] ))) {
			$show_in_menu =  true ;
		}

		$help_args = array(
			'labels'              => $help_labels,
			'public'              => true, 
			'publicly_queryable'  => true,
			'exclude_from_search' => false,
			'show_ui'             => true,
			'show_in_menu'        => $show_in_menu,
			'menu_position'       => 5,
			'show_in_admin_bar'   => true,
			'capability_type'     => $help_capabilitytype,	
			'capabilities'        => $explicitly_mapped_caps,
			'map_meta_cap'        => true,
			'hierarchical'        => true,
			'supports'            => array( 'title', 'editor', 'comments', 'thumbnail', 'page-attributes' , 'revisions', 'author' ),
			'has_archive'         => true,
			'rewrite'             => true,
			'query_var'           => true,
			'can_export'          => true,
			'show_in_nav_menus'   => false,
			'menu_icon'			  => apply_filters( 'rbhn_dashicon', 'dashicons-format-aside' ),
		);

		register_post_type( $post_type_name, $help_args );

	}

	/**
	 * Returns the post content with the Help Notes index appended.
	 *
	 * @access public
	 * @param text $content post content	 
	 * @return $content
	 */	
	public function rbhn_add_post_content( $content ) {
		
		global $post;

		// drop out if not a page
		if ( 'page' != get_post_type())
			return $content;
			
		if (( get_option( 'rbhn_contents_page' ) != "0" ) && is_page( get_option( 'rbhn_contents_page' )) && is_main_query()) {
			
			$active_role_notes = $this->active_help_notes();
			
			foreach( $active_role_notes as $posttype_selected ) {
				
				$posttype = get_post_type_object( $posttype_selected );
				$posttype_Name = $posttype->labels->name; 
			
				$args = array(
							'depth'        => 0,
							'show_date'    => '',
							'date_format'  => get_option( 'date_format' ),
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
							'post_status'  => ( current_user_can( 'read_private_posts' ) ? 'publish,private' : 'publish' ),
						);
						
				$content =  $content . '<p>' . wp_list_pages( $args ) . '</p>';
			}
			
		}
		return $content;
	}

	/**
	 * Returns a cleaned custom post type name.
	 *
	 * @access public
	 * @param string $role_key current suggested Help Notes post type name
	 * @return $post_type_name
	 */	
	public function clean_post_type_name( $role_key ) {
		// limit to 20 characters length for the WP limitation of custom post type names
		$post_type_name = sanitize_key( 'h_' . substr( $role_key , -18 )); 
		return $post_type_name;
	}


	/**
	 * Add capabilities and Flush your rewrite rules for plugin activation.
	 *
	 * @access public
	 * @return $settings
	 */	
	public function help_do_on_activation() {

		// Record plugin activation date.
		add_option( 'rbhn_install_date',  time()); 
		
		// create the plugin_version store option if not already present.
		$plugin_version = $this->plugin_get_version();
		update_option( 'rbhn_plugin_version', $plugin_version ); 

		// create the tracking enabled capabilities option if not already present.
		update_option( 'rbhn_caps_created', get_option( 'rbhn_caps_created', array())); 
		
		// Add the selected role capabilities for use with the role help notes
		RBHN_Capabilities::rbhn_add_role_caps();

		flush_rewrite_rules();
	}
	
	/**
	 * Add capabilities and Flush your rewrite rules for plugin activation.
	 *
	 * @access public
	 * @return $settings
	 */	
	public function plugin_do_on_activation() {

		// Record plugin activation date.
		add_option( 'rbhn_install_date',  time()); 
		
		// create the plugin_version store option if not already present.
		$plugin_version = $this->plugin_get_version();
		update_option( 'rbhn_plugin_version', $plugin_version ); 

		// create the tracking enabled capabilities option if not already present.
		update_option( 'rbhn_caps_created', get_option( 'rbhn_caps_created', array())); 
		
		// Add the selected role capabilities for use with the role help notes
		RBHN_Capabilities::rbhn_add_role_caps();

		flush_rewrite_rules();
	}


		
	/**
	 * Returns current plugin version.
	 *
	 * @access public
	 * @return $plugin_version
	 */	
	public function plugin_get_version() {
		$plugin_data = get_plugin_data( $this->plugin_full_path );	
		$plugin_version = $plugin_data['Version'];
		return filter_var( $plugin_version, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
	}
	
	/**
	 * Returns current plugin filename.
	 *
	 * @access public
	 * @return $plugin_file
	 */	
	public function get_plugin_file() {
		
		$plugin_data = get_plugin_data( $this->plugin_full_path );	
		$plugin_name = $plugin_data['Name'];
		
		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		$plugins = get_plugins();
		foreach( $plugins as $plugin_file => $plugin_info ) {
			if ( $plugin_info['Name'] == $plugin_name ) return $plugin_file;
		}
		return null;
	}
		
	/**
	 * Register Plugin Deactivation Hooks for all the currently 
	 * enforced active extension plugins.
	 *
	 * @access public
	 * @return null
	 */
	public function deactivation_notice() {

		$plugins = RBHN_Settings::get_instance()->selected_plugins( 'rbhn_plugin_extension' );

			foreach ( $plugins as $plugin ) {
			
				$filename = ( isset( $plugin['filename'] ) ? $plugin['filename'] : $plugin['slug'] );
				$plugin_main_file =  trailingslashit( $plugin['plugin_dir']. $plugin['slug'] ) .  $filename . '.php' ;			

				register_deactivation_hook( $plugin_main_file, array( 'RBHN_Role_Based_Help_Notes', 'on_deactivation' ));
			}

	}

	/**
	 * This function is hooked into plugin deactivation for 
	 * enforced active extension plugins.
	 *
	 * @access public
	 * @return null
	 */
	public static function on_deactivation()
    {
        if ( ! current_user_can( 'activate_plugins' ))
            return;
        $plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
        check_admin_referer( "deactivate-plugin_{$plugin}" );

		$plugin_slug = explode( "/", $plugin );	
		$plugin_slug = $plugin_slug[0];	
		update_option( "rbhn_deactivate_{$plugin_slug}", true );
    }
	
	/**
	 * Display the admin warnings.
	 *
	 * @access public
	 * @return null
	 */
	public function action_admin_notices() {
	
		$plugins = RBHN_Settings::get_instance()->selected_plugins( 'rbhn_plugin_extension' );

		foreach ( $plugins as $plugin ) {
			$this->action_admin_plugin_forced_active_notices( $plugin["slug"] );
		}
		
		// Prompt for rating
		$this->action_admin_rating_prompt_notices();	
		
	}
	
	/**
	 * Display the admin error message for plugin forced active.
	 *
	 * @access public
	 * @return null
	 */
	public function action_admin_plugin_forced_active_notices( $plugin ) {

		$plugin_message = get_option( "rbhn_deactivate_{$plugin}" );
		if ( ! empty( $plugin_message )) {
			?>
			<div class="error">
				  <p><?php esc_html_e( sprintf( __( 'Error the %1$s plugin is forced active with ', 'role-based-help-notes-text-domain' ), $plugin )); ?>
				  <a href="options-general.php?page=<?php echo $this->menu ; ?>&tab=rbhn_plugin_extension"> <?php echo esc_html(__( 'Help Note Settings!', 'role-based-help-notes-text-domain' )); ?> </a></p>
			</div>
			<?php
			update_option( "rbhn_deactivate_{$plugin}", false ); 
		}
	}
	
	/**
	 * Store the current users start date with Help Notes.
	 *
	 * @access public
	 * @return null
	 */
	public function action_init_store_user_meta() {
		
		if ( help_notes_available()) {
			// start meta for a user
			add_user_meta( get_current_user_id(), 'rbhn_start_date', time(), true );
			add_user_meta( get_current_user_id(), 'rbhn_prompt_timeout', time() + 60*60*24*  PROMPT_DELAY_IN_DAYS, true );
		}
	}

	/**
	 * Display the admin message for plugin rating prompt.
	 *
	 * @access public
	 * @return null
	 */
	public function action_admin_rating_prompt_notices() {

		$help_note_post_types =  get_option( 'rbhn_post_types' );
		$help_note_post_types = array_filter(( array ) $help_note_post_types );  // Filter out any empty entries, if non active.	

		$number_of_help_notes_acitve 	= ( empty( $help_note_post_types ) ? 0 : count( $help_note_post_types ));

		$user_responses =  array_filter(( array )get_user_meta( get_current_user_id(), PROMPT_ARGUMENT, true ));	
		if ( in_array(  "done_now", $user_responses )) 
			return;

		if ( current_user_can( 'install_plugins' ) && (( $number_of_help_notes_acitve > 0 ) || get_option( 'rbhn_general_enabled' ))) {
			
			$next_prompt_time = get_user_meta( get_current_user_id(), 'rbhn_prompt_timeout', true );
			if (( time() > $next_prompt_time )) {
				$plugin_user_start_date = get_user_meta( get_current_user_id(), 'rbhn_start_date', true );
				?>
				<div class="update-nag">
					
					<p><?php esc_html( printf( __( "You've been using <b>Role Based Help Notes</b> for more than %s.  How about giving it a review by logging in at wordpress.org ?", 'role-based-help-notes-text-domain' ), human_time_diff( $plugin_user_start_date ))); ?>
					
					<?php if ( get_option( 'rbhn_general_enabled' )) { ?>
						<li><?php esc_html( printf( __( "The site is using General Help Notes type.", 'role-based-help-notes-text-domain' ))); ?>
					<?php } ?>
					
					<?php if ( $number_of_help_notes_acitve ) { ?>
						<LI><?php esc_html( printf( _n( "You are using Help Notes with 1 user role.",  "You are using Help Notes with %d user roles.", $number_of_help_notes_acitve, 'role-based-help-notes-text-domain' ), $number_of_help_notes_acitve )); ?>
						<?php  for ( $x=0; $x<$number_of_help_notes_acitve; $x++) echo '  :-) '; ?>
					<?php } ?>						
					</p>
					<p>

						<?php echo '<a href="' .  esc_url( add_query_arg( array( PROMPT_ARGUMENT => 'doing_now' )))  . '">' .  esc_html__( "Yes, please take me there.", 'role-based-help-notes-extra-text-domain' ) . '</a> '; ?>
						
						| <?php echo ' <a href="' .  esc_url( add_query_arg( array( PROMPT_ARGUMENT => 'not_now' )))  . '">' .  esc_html__( "Not right now thanks.", 'role-based-help-notes-extra-text-domain' ) . '</a> ';?>
						
						<?php
						if ( in_array(  "not_now", $user_responses ) || in_array(  "doing_now", $user_responses )) { 
							echo '| <a href="' .  esc_url( add_query_arg( array( PROMPT_ARGUMENT => 'done_now' )))  . '">' .  esc_html__( "I've already done this !", 'role-based-help-notes-extra-text-domain' ) . '</a> ';
						}?>

					</p>
				</div>
				<?php
			}
		}
	}
	
	/**
	 * Store the user selection from the rate the plugin prompt.
	 *
	 * @access public
	 * @return null
	 */
	public function catch_hide_notice() {
	
		if ( isset( $_GET[PROMPT_ARGUMENT] ) && $_GET[PROMPT_ARGUMENT] && current_user_can( 'install_plugins' )) {
			
			$user_user_hide_message = array( sanitize_key( $_GET[PROMPT_ARGUMENT] )) ;				
			$user_responses =  array_filter(( array )get_user_meta( get_current_user_id(), PROMPT_ARGUMENT, true ));	

			if ( ! empty( $user_responses )) {
				$response = array_unique( array_merge( $user_user_hide_message, $user_responses ));
			} else {
				$response =  $user_user_hide_message;
			}
			
			check_admin_referer();	
			update_user_meta( get_current_user_id(), PROMPT_ARGUMENT, $response );

			if ( in_array( "doing_now", ( array_values(( array )$user_user_hide_message ))))  {
				$next_prompt_time = time() + ( 60*60*24*  PROMPT_DELAY_IN_DAYS ) ;
				update_user_meta( get_current_user_id(), 'rbhn_prompt_timeout' , $next_prompt_time );
				wp_redirect( 'http://wordpress.org/support/view/plugin-reviews/role-based-help-notes' );
				exit;					
			}

			if ( in_array( "not_now", ( array_values(( array )$user_user_hide_message ))))  {
				$next_prompt_time = time() + ( 60*60*24*  PROMPT_DELAY_IN_DAYS ) ;
				update_user_meta( get_current_user_id(), 'rbhn_prompt_timeout' , $next_prompt_time );		
			}
				
				
			wp_redirect( remove_query_arg( PROMPT_ARGUMENT ));
			exit;		
		}
	}	

	

	public function deregister_helpnote_for_settings_page_of_email_post_changes_plugin() {

		// collect the email-post-changes options for the active post_types
		$email_post_types_options = ( array ) get_option( 'email_post_changes' );
		$email_notes_selection = ( array ) get_option( 'rbhne_email_change_notification' );

		$role_based_help_notes = RBHN_Role_Based_Help_Notes::get_instance();
		$site_help_notes = $role_based_help_notes->site_help_notes();
		$site_help_notes_cpts = array_values( $site_help_notes );
		
		//remove general help notes so the it remains pickable in the email_post_changes plugin settings. 
		$pos = array_search( 'h_general', $site_help_notes_cpts );
		unset( $site_help_notes_cpts[$pos] );
		
		foreach ( $site_help_notes_cpts as $post_type ) {
			$this->unregister_post_type( $post_type );
		}

		
	}

	/**
     * function to de-register custom post types
     *
     * @return rue on success.
     */	
	private function unregister_post_type( $post_type ) {
		global $wp_post_types;
		if ( isset( $wp_post_types[ $post_type ] )) {
			unset( $wp_post_types[ $post_type ] );
			return true;
		}
		return false;
	}

	
	/**
     * Creates or returns an instance of this class.
     *
     * @return   A single instance of this class.
     */
    public static function get_instance() {
 
        if ( null == self::$instance ) {
            self::$instance = new self;
        }
        return self::$instance;
    }
}
		
/**
 * Init role_based_help_notes
 */
RBHN_Role_Based_Help_Notes::get_instance();


?>