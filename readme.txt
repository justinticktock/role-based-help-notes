=== Plugin Name ===
Contributors: justinticktock
Tags: multisite, roles, user, help, notes, cms, documents, groups, teams, collaboration
Requires at least: 3.5
Tested up to: 3.7.1
Stable tag: 1.2.01
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Help Notes/Posts dedicated per role.

== Description ==

This plugin allows you to quickly create a custom post type for user roles.  This allows users, with a specific role, the ability to create and edit their own 'help notes' providing a private set of notes for use.  It can be used for anything else that fits with that role (e.g. creating minutes-of-meetings notes etc...).

To add new roles to the basic wordpress roles (Administrator, Editor, Subscriber ..etc) you will need to use another plugin refer to [Roles_and_Capabilities](http://codex.wordpress.org/Roles_and_Capabilities) and Resources.. Plugins.  You can then use one of these plugins (e.g. [User Role Editor](http://wordpress.org/extend/plugins/user-role-editor/ "User Role Editor")) to allocate users to multiple roles.

So if you want to quickly give a group a private area to share and post ideas/notes ..
1. Create a new role.
2. Add users to the role.
3. Enable the Help Notes from the settings.

A global 'General Help Notes' type is also available which is not tided to a specific role, this has the same access/capabilities as the standard wordpress 'post' type for read/write access.

Extensions:

If you select the options for extending functionality through other plugins the following are selectable for ease of installing..

1. [Post type archive in menu](http://wordpress.org/plugins/post-type-archive-in-menu/) plugin by [lpeharda](http://profiles.wordpress.org/lpeharda/) to add 'Help Notes' archives to your menus.
2. [Simple FootNotes](http://wordpress.org/plugins/simple-footnotes/) by [Andrew Nacin](http://profiles.wordpress.org/nacin/) to add a footnote shortcode [ref][/ref].
3. [Simple Page Ordering](http://wordpress.org/plugins/simple-page-ordering/) for easing re-ordering of Help Notes.


[GitHub page](http://github.com/justinticktock/role-based-help-notes).

== Installation ==

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Goto the "Settings" Menu and "Help Notes" sub menu, select which roles are to have Help Notes.

== Frequently Asked Questions ==

= I have a new role how can I add it? =

You will need to use another plugin to manage roles and capabilities such as the [User Role Editor](http://wordpress.org/extend/plugins/user-role-editor/ "User Role Editor") plugin.

== Screenshots ==

1. The Settings Screen.
2. The Help Notes for the enabled 'Proof Reader' role.


== Changelog ==


= 1.2.1 =
* 2013-**-**
* Added Settings link to the admin acitve plugin listing 
* Added Widget to allow sidebars to list all users with access to read/edit a Help Note type.
* Code refactoring
* todo ....bring into wordpress coding standards.

= 1.2.01 =
* 2013-11-11
* Fix for where no Contents Page is defined (thanks to [Vernon Fowler](http://wordpress.org/support/profile/vernonfowler))
* Grammatical corrections. 

= 1.2.0 =
* 2013-11-04
* Added new setting to enable/disable the 'General Help Notes'.
* Added the Plugin Extension for Simple Page Ordering.

= 1.1.0 =
* 2013-10-21
* Added new setting to select a page for a Help Notes Contents to be listed.
* Added the Plugin Extension for Simple FootNotes by [Andrew Nacin](http://profiles.wordpress.org/nacin/)
* Fixed settings listing order to alphanumeric.
* Fixed php warnings.

= 1.01 =
* Added Capabilities for Administrator.
* capability naming now match post_type name
* Help notes now at the top admin level, this is a workround to the non-admins not being able to create new posts when the post type is beneath a menu page.

= 1.0 =
* Release into the wild.


== Upgrade Notice ==

= 1.2.0 =
The 'General Help Notes' are now selectable and initally not-selected, if you wish to continue to use the 'General Help Notes' go to settings and make the selection.

= 1.01 =
* uninstall 1.0 completely to clean up capabilities before installing 1.01.
