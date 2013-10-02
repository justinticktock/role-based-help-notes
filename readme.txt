=== Plugin Name ===
Contributors: justinticktock
Tags: multisite, roles, user, help, notes, cms, documents, groups, teams
Requires at least: 3.5
Tested up to: 3.6.1
Stable tag: 1.01
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Help Notes/Posts dedicated per role.

== Description ==

This plugin allows you to quickly create a custom post type for user roles.  This allows users, with a specific role, the ability to create and edit their own role related 'help notes' providing a private set of notes for use.  It can be used for anything else that fits with that role (e.g. creating minutes-of-meetings notes etc...).

Once activated a 'General' default post type is enabled with the same access permissions as for the 'post' type, others can then be enabled by role as required from the settings page.

To add new roles to the basic wordpress roles (Administrator, Editor, Subscriber ..etc) you will need to use another plugin refer to [Roles_and_Capabilities](http://codex.wordpress.org/Roles_and_Capabilities) and Resources.. Plugins.  You can then use one of these plugins (e.g. [User Role Editor](http://wordpress.org/extend/plugins/user-role-editor/ "User Role Editor")) to allocate users to multiple roles.

Extensions:

If you select the option it also takes advantage of the [Post type archive in menu](http://wordpress.org/plugins/post-type-archive-in-menu/) plugin (by lpeharda) to add 'Help Notes' archives to your menus.

== Installation ==

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
2. Goto the "Settings" Menu and "Help Notes" sub menu, select which roles are to have Help Notes.

== Frequently Asked Questions ==

= I have a new role how can I add it? =

You will need to use another plugin to manage roles and capabilities such as the [User Role Editor](http://wordpress.org/extend/plugins/user-role-editor/ "User Role Editor") plugin.

== Screenshots ==

1. The Settings Screen.
2. The Help Notes for the enabled 'subscriber role'.


== Changelog ==

= 1.01 =
Added Capabilities for Administrator.
capability naming now match post_type name
Help notes now at the top admin level, this is a workround to the non-admins not being able to create new posts when the post type is beneath a menu page.

= 1.0 =
Release into the wild.


== Upgrade Notice ==

= 1.01 =
uninstall 1.0 completely to clean up capabilities before installing 1.01.
