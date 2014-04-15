<?php
/*
Plugin Name: Role Based Help Notes
Plugin URI: http://justinandco.com/plugins/role-based-help-notes/
Description: The addition of Custom Post Type to cover site help notes
Version: 1.2.9.0
Author: Justin Fletcher
Author URI: http://justinandco.com
License: GPLv2 or later
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * RBHN_Role_Based_Help_Notes class.
 *
 * Main Class which inits the CPTs and plugin
 */
class RBHN_Role_Based_Help_Notes {

	const HELP_NOW_DELAY_IN_DAYS = 1;
   
	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		// Set the constants needed by the plugin.
		add_action( 'plugins_loaded', array( $this, 'constants' ), 1 );
		
		/* Load the functions files. */
		add_action( 'plugins_loaded', array( $this, 'includes' ), 2 );
		
		/* Hooks... */
		
		// A settings page to the admin acitve plugin listing
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'role_based_help_notes_action_links' ));

		// Attached to set_current_user. Loads the plugin installer CLASS after themes are set-up to stop duplication of the CLASS.
		add_action( 'set_current_user', array( $this, 'set_current_user' ));
		
		// Attached to admin_init. Loads the textdomain and the upgrade routine.
		add_action( 'admin_init', array( $this, 'admin_init' ));
		
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
		
		define( 'HELP_MYPLUGINNAME_PATH', plugin_dir_path(__FILE__) );
		define( 'HELP_MYPLUGINNAME_FULL_PATH', HELP_MYPLUGINNAME_PATH . 'role-based-help-notes.php' );
		define( 'HELP_PLUGIN_URI', plugins_url('', __FILE__) );
		define( 'HELP_PLUGIN_DIR', plugin_dir_path( HELP_MYPLUGINNAME_PATH ) );
		define( 'HELP_SETTINGS_PAGE', 'notes-settings');
		define( 'HELP_MENU_PAGE', 'notes.php');
		
	}

	/**
	 * Loads the initial files needed by the plugin.
	 *
	 * @return void
	 */
	function includes() {
	
		// settings 
		require_once( HELP_MYPLUGINNAME_PATH . 'includes/class-rbhn-settings.php' );  

		// custom post type capabilities
		require_once( HELP_MYPLUGINNAME_PATH . 'includes/class-rbhn-capabilities.php' );  

		// Load the widgets functions file.
		require_once( HELP_MYPLUGINNAME_PATH . 'includes/widgets.php' );

		// Load code for better compatibility with other plugins.
		require_once( HELP_MYPLUGINNAME_PATH . 'includes/plugin-compatibility.php' );
		
	}

	
	/**
	 * Append new links to the Plugin admin side
	 *
	 * @param array $links Current links/urls provided for the plugin admin.
	 * @return array $links
	 */
	public function role_based_help_notes_action_links( $links ) {
		array_unshift( $links, '<a href="options-general.php?page=' . HELP_SETTINGS_PAGE . '">' . __( 'Settings' ) . "</a>" );
		return $links;
	}

	
	
	
	/**
	 * Initialise the plugin installs
	 *
	 * @return void
	 */
	public function set_current_user() {

		// install the plugins and force activation if they are selected within the plugin settings
		require_once( HELP_MYPLUGINNAME_PATH . 'includes/class-rbhn-install-plugins.php' );    	
		
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

		if ( empty($plugin_current_version) || $plugin_current_version < $plugin_new_version ) {
		
			$plugin_current_version = isset( $plugin_current_version ) ? $plugin_current_version : 0;

			$this->rbhn_upgrade( $plugin_current_version );

			// set default options if not already set..
			$this->help_do_on_activation();
			
			// Update the option again after rbhn_upgrade() changes and set the current plugin revision	
			update_option('rbhn_plugin_version', $plugin_new_version ); 
		}
			
		load_plugin_textdomain('role-based-help-notes-text-domain', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		if ( help_notes_available() ) 
			add_menu_page( __( 'Notes', 'role-based-help-notes-text-domain' ), __( 'Help Notes', 'role-based-help-notes-text-domain' ), 'read', HELP_MENU_PAGE, array( &$this, 'menu_page' ), 'dashicons-format-aside', 6 ); 		
	}

	/**
	 * Provides an upgrade path for older versions of the plugin
	 *
	 * @param float $current_plugin_version the local plugin version prior to an update 
	 * @return void
	 */
	public function rbhn_upgrade( $current_plugin_version ) {
		
		// move current database stored values into the next structure
		if ( $current_plugin_version < '1.2.8' ) {

			//rbhn_caps_created
			$db_help_note_caps_created = get_option( 'help_note_caps_created' );
			if ( ! empty( $db_help_note_caps_created ));
				update_option( 'rbhn_caps_created', (array) $db_help_note_caps_created );

			$settings_options = get_option( 'help_note_option' );
		
			//rbhn_plugin_version
			if ( ! empty( $settings_options['help_notes_version'] ));
				update_option( 'rbhn_plugin_version', $settings_options['help_notes_version'] );

			//rbhn_general_enabled
			if ( ! empty( $settings_options['help_note_general_enabled'] ));
				update_option( 'rbhn_general_enabled', $settings_options['help_note_general_enabled'] );
				
			//rbhn_post_types
			if ( ! empty( $settings_options['help_note_post_types'] ));
				update_option( 'rbhn_post_types', (array) $settings_options['help_note_post_types'] );
				
			//rbhn_contents_page
			if ( ! empty( $settings_options['help_note_contents_page'] ));
				update_option( 'rbhn_contents_page', $settings_options['help_note_contents_page'] );
				
			//rbhn_user_widget_enabled
			if ( ! empty( $settings_options['user_widget_enabled'] ));
				update_option( 'rbhn_user_widget_enabled', $settings_options['user_widget_enabled'] );
				
			//rbhn_simple_page_ordering
			if ( ! empty( $settings_options['help_note_simple_page_ordering'] ));
				update_option( 'rbhn_simple_page_ordering', $settings_options['help_note_simple_page_ordering'] );
				
			//rbhn_simple_footnotes_plugin
			if ( ! empty( $settings_options['help_note_simple_footnotes_plugin'] ));
				update_option( 'rbhn_simple_footnotes_plugin', $settings_options['help_note_simple_footnotes_plugin'] );
				
			//rbhn_disable_comments_plugin
			if ( ! empty( $settings_options['help_note_disable_comments_plugin'] ));
				update_option( 'rbhn_disable_comments_plugin', $settings_options['help_note_disable_comments_plugin'] );
				
			//rbhn_email_post_changes_plugin
			if ( ! empty( $settings_options['help_note_email_post_changes_plugin'] ));
				update_option( 'rbhn_email_post_changes_plugin', $settings_options['help_note_email_post_changes_plugin'] );
				
			//rbhn_post_type_switcher_plugin
			if ( ! empty( $settings_options['help_note_post_type_switcher_plugin'] ));
				update_option( 'rbhn_post_type_switcher_plugin', $settings_options['help_note_post_type_switcher_plugin'] );
				
			//rbhn_post_type_archive_in_menu_plugin
			if ( ! empty( $settings_options['help_note_menu_plugin'] ));
				update_option( 'rbhn_post_type_archive_in_menu_plugin', $settings_options['help_note_menu_plugin'] );
				
			// remove old options from DB
			delete_option('rbhn_update_request');
			delete_option('help_note_option');
			delete_option('help_note_caps_created');

		}
	}
	
	/**
	 * Checks if a particular user has a role. 
	 * Returns true if a match was found.
	 *
	 * @param string $role Role name.
	 * @param int $user_id (Optional) The ID of a user. Defaults to the current user.
	 * @return bool
	 */
	public function help_notes_current_user_has_role( $role, $user_id = null ) {
	 
		if ( is_numeric( $user_id ) )
		$user = get_userdata( $user_id );
		else
			$user = wp_get_current_user();
	 
		if ( empty( $user ) )
		return false;
	 
		return in_array( $role, (array) $user->roles );
	}

	/**
	 * Returns the selected-active Help Note Custom Post Types
	 *
	 * @access public
	 * @return array of active Help Notes post types
	 */		
	public function active_help_notes() {

		$active_posttypes = array();

		//  loop through the site roles and create a custom post for each
		global $wp_roles;
		
		// Load roles if not set
		if ( ! isset( $wp_roles ) )
			$wp_roles = new WP_Roles();

		$roles = $wp_roles->get_names();


		// option collection  
		$general_help_enabled 	= get_option('rbhn_general_enabled');
		$post_types_array 		= get_option('rbhn_post_types');
			
	   if ( isset( $general_help_enabled ) && ! empty( $general_help_enabled ) ) {
		
			$active_posttypes[] = "h_general"; 
		}
		
		if (  ! empty($post_types_array ) ) {	
			foreach( $post_types_array as $array) {
				foreach( $array as $active_role=>$active_posttype) {
					if ($this->help_notes_current_user_has_role( $active_role )) {
						$active_posttypes[] = $active_posttype;
					}				
				}
			}	
		}
		return $active_posttypes;
	}

    /**
	 * Returns the array of active Help Notes roles or if a post_type is provided the single role associated.
	 *
	 * @access public
	 * @param string $help_note_post_type post_type name.	 
	 * @return array of active Help Notes roles
	 */		
	public function help_notes_role( $help_note_post_type  = null ) {
		$post_types_array 		= get_option('rbhn_post_types');

		$help_note_role = array();
		if (  ! empty( $post_types_array ) ) {
			foreach( $post_types_array as $array) {	
				foreach( $array as $active_role=>$active_posttype) {
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
		global $role_based_help_notes;
		
		if( !is_admin() && $query->is_main_query() && empty( $query->query_vars['suppress_filters'] ) ) {

			// For author queries add Help Note post types
			if ($query->is_author) {
				$include_post_types = $role_based_help_notes->active_help_notes();
				$include_post_types[] = 'post';
				$query->set( 'post_type', $include_post_types);
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
		
		$this->action_init_store_user_start_date();
		
		// option collection  
		$general_help_enabled 	= get_option('rbhn_general_enabled');
		$post_types_array 		= get_option('rbhn_post_types');
		
		
		if ( isset( $general_help_enabled ) && ! empty( $general_help_enabled ) ) {
			// generate a genetic help note post type
			call_user_func_array( array( $this, 'help_register_posttype' ), array("general", "General", "h_general") );  
		}
		 
		
		//  loop through the site roles and create a custom post for each
		global $wp_roles;
		
		// Load roles if not set
		if ( ! isset( $wp_roles ) )
			$wp_roles = new WP_Roles();

		$roles = $wp_roles->get_names();

		if (  ! empty( $post_types_array ) ) {
			foreach( $post_types_array as $array) {	
				foreach( $array as $active_role=>$active_posttype) {
					if (array_key_exists ($active_role, $roles)) {
						if ( $this->help_notes_current_user_has_role($active_role) ) {
							call_user_func_array( array( $this, 'help_register_posttype' ), array($active_role, $roles[$active_role], $active_posttype) ); 
						}
					} 
				}
			}
		}
		
		/**
		 * Init rbhn_settings class
		 */
		$rbhn_settings = new RBHN_Settings();

		if (class_exists( 'RBHNE_Settings' )) {
			$rbhn_settings->registerHandler(new RBHNE_Settings_Additional_Methods());
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
	public function help_register_posttype($role_key, $role_name, $post_type_name) {
		
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
		
		$help_args = array(
			'labels'              => $help_labels,
			'public'              => true, 
			'publicly_queryable'  => true,
			'exclude_from_search' => false,
			'show_ui'             => true,
			'show_in_menu'        => HELP_MENU_PAGE,
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
			'menu_icon'			  => apply_filters( 'rbhn_dashicon', 'dashicons-format-aside'),
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
	public function rbhn_add_post_content($content) {
		
		global $post;

		// drop out if not a page
		if ( 'page' != get_post_type() )
			return $content;
			
		if ( ( get_option('rbhn_contents_page') != "0") && is_page( get_option('rbhn_contents_page') ) && is_main_query() ) {
			
			$active_role_notes = $this->active_help_notes();
			
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
							'post_status'  => ( is_user_logged_in() ? 'publish,private' : 'publish' ),
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
	public function clean_post_type_name($role_key) {
		// limit to 20 characters length for the WP limitation of custom post type names
		$post_type_name = sanitize_key('h_' . substr($role_key , -18)); 
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
		add_option('rbhn_install_date',  time() ); 
		
		// create the plugin_version store option if not already present.
		$plugin_version = $this->plugin_get_version();
		update_option('rbhn_plugin_version', $plugin_version ); 

		// create the tracking enabled capabilities option if not already present.
		update_option( 'rbhn_caps_created', get_option( 'rbhn_caps_created', array() )); 
		
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
		$plugin_data = get_plugin_data( HELP_MYPLUGINNAME_FULL_PATH );	
		$plugin_version = $plugin_data['Version'];	
		return filter_var($plugin_version, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
	}

	
		
	/**
	 * Register Plugin Deactivation Hooks for all the currently 
	 * enforced active extension plugins.
	 *
	 * @access public
	 * @return null
	 */
	public function deactivation_notice() {

		// loop plugins forced active.
		$plugins = RBHN_Settings::install_plugins();

		foreach ( $plugins as $plugin ) {
			$plugin_file = HELP_PLUGIN_DIR . $plugin["slug"] . '\\' . $plugin['slug'] . '.php' ;
			register_deactivation_hook( $plugin_file, array( 'RBHN_Role_Based_Help_Notes', 'on_deactivation' ) );
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
        if ( ! current_user_can( 'activate_plugins' ) )
            return;
        $plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
        check_admin_referer( "deactivate-plugin_{$plugin}" );
	
		$plugin_slug = explode( "/", $plugin);
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

		// loop plugins forced active.
		$plugins = RBHN_Settings::install_plugins();

		// for each extension plugin enabled (forced active) add a error message for deactivation.
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
	
		$plugin_message = get_option("rbhn_deactivate_{$plugin}");
		if ( ! empty( $plugin_message ) ) {
			?>
			<div class="error">
				  <p><?php esc_html_e(sprintf( __( 'Error the %1$s plugin is forced active with ', 'role-based-help-notes-text-domain'), $plugin)); ?>
				  <a href="options-general.php?page=<?php echo HELP_SETTINGS_PAGE ?>&tab=rbhn_plugin_extension"> <?php echo esc_html(__( 'Help Note Settings!', 'role-based-help-notes-text-domain')); ?> </a></p>
			</div>
			<?php
			update_option("rbhn_deactivate_{$plugin}", false); 
		}
	}

		
	/**
	 * Store the current users start date with Help Notes.
	 *
	 * @access public
	 * @return null
	 */
	public function action_init_store_user_start_date() {
		
		if ( help_notes_available() ) {
			// start date for a user
			add_user_meta( get_current_user_id(), 'rbhn_start_date', time(), true );
			//add_option('rbhn_install_date',  time() );
		}
	}

	
	/**
	 * Display the admin message for plugin rating prompt.
	 *
	 * @access public
	 * @return null
	 */
	public function action_admin_rating_prompt_notices( ) {
	
		$help_note_post_types =  get_option('rbhn_post_types');
		$help_note_post_types = array_filter( $help_note_post_types );  // Filter out any empty entries, if non active.	


		$number_of_help_notes_acitve 	= ( empty( $help_note_post_types ) ? 0 : count($help_note_post_types) );

		$user_responses =  array_filter( get_user_meta( get_current_user_id(), 'rbhn_prompt_response', true ));	
//		if ( in_array(  "not_now", $user_responses )) {
//			echo  "not_now is true";
//		}
		
		if ( in_array(  "done_now", $user_responses )) 
			return;

//echo "rbhn_prompt_timeout = " . get_user_meta( get_current_user_id(), 'rbhn_prompt_timeout', true ) ; 
			
		if (( $number_of_help_notes_acitve > 0 ) || get_option('rbhn_general_enabled') ) {
		
			$plugin_user_start_date = get_user_meta( get_current_user_id(), 'rbhn_start_date', true );
			$timeout_days = get_user_meta( get_current_user_id(), 'rbhn_prompt_timeout', true );
			if ( ! empty( $plugin_user_start_date ) && ( time() > ( $plugin_user_start_date + ( 60*60*24* $timeout_days )))) {
				?>
				<div class="update-nag">
				
					<p><?php esc_html(printf( __("You've been using <b>Role Based Help Notes</b> for %s now.  How about giving it a review by logging in at wordpress.org ?", 'role-based-help-notes-text-domain'), human_time_diff( $plugin_user_start_date) )); ?>
					
					<?php if ( get_option('rbhn_general_enabled') ) { ?>
						<li><?php esc_html(printf( __("The site is using General Help Notes type.", 'role-based-help-notes-text-domain'))); ?>
					<?php } ?>
					
					<?php if ( $number_of_help_notes_acitve ) { ?>
						<LI><?php esc_html(printf( _n("You are using Help Notes with 1 user role.",  "You are using Help Notes with %d user roles.", $number_of_help_notes_acitve, 'role-based-help-notes-text-domain'), $number_of_help_notes_acitve ) ); ?>
						<?php  for ($x=0; $x<$number_of_help_notes_acitve; $x++) echo '  :-) '; ?>
					<?php } ?>						
					</p>
					<p>
						  <?php echo ' <a href="' .  esc_url(add_query_arg( array( 'rbhn_hide_notice' => 'done_now' )))  . '">' .  esc_html__( "I've already done this !", 'role-based-help-notes-extra-text-domain' ) . '</a> ';?>
						| <a href='http://wordpress.org/support/view/plugin-reviews/role-based-help-notes' target='_blank'><?php esc_html_e( __("Yes, please take me there.", 'role-based-help-notes-text-domain'));?></a> 
						| <?php echo ' <a href="' .  esc_url(add_query_arg( array( 'rbhn_hide_notice' => 'not_now' )))  . '">' .  esc_html__( "Not right now thanks.", 'role-based-help-notes-extra-text-domain' ) . '</a> ';?>
					</p>
				</div>
				<?php
				update_option("rbhn_deactivate_{$plugin}", false); 
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
	

		//if ( ! empty( $user_user_hide_message ) && current_user_can( 'install_plugins' )) {
		if ( isset($_GET['rbhn_hide_notice']) && $_GET['rbhn_hide_notice'] && current_user_can( 'install_plugins' )) {
			
			$user_user_hide_message = array( sanitize_key( $_GET['rbhn_hide_notice'] )) ;				
			$user_responses =  array_filter( get_user_meta( get_current_user_id(), 'rbhn_prompt_response', true ));	
//echo var_dump ( $user_responses ) ."  </br>";

			if ( ! empty( $user_responses )) {
				$response = array_unique( array_merge( $user_user_hide_message, $user_responses ));
			} else {
				$response =  $user_user_hide_message;
			}
//echo	 var_dump ( (array)$response );	

			update_user_meta( get_current_user_id(), 'rbhn_prompt_response', $response );
//echo var_dump($user_user_hide_message		);
			if ( in_array( "not_now", (array_values((array)$user_user_hide_message ))))  {
				$timeout =  get_user_meta( get_current_user_id(), 'rbhn_prompt_timeout', true ) + self::HELP_NOW_DELAY_IN_DAYS ;		
				update_user_meta( get_current_user_id(), 'rbhn_prompt_timeout' , $timeout );		
			}
				
			wp_redirect( remove_query_arg( 'rbhn_hide_notice' ) );
			exit;		
		}
	}	
}

/**
 * Init role_based_help_notes class
 */
$role_based_help_notes = new RBHN_Role_Based_Help_Notes();

?>