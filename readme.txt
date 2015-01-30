=== Plugin Name ===
Contributors: justinticktock
Tags: multisite, roles, user, help, notes, cms, documents, groups, teams, collaboration, BuddyPress, intranet
Requires at least: 3.5
Tested up to: 4.1
Stable tag: 1.3.1.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Help Notes/Posts private to assigned users of a WordPress role.

== Description ==

Do you want to give users a private area to share information based on a role they have been assigned? 

...this plugin allows you to quickly create a custom post type for user roles.  This allows users, with a specific role, the ability to create and edit their own 'help notes' providing a private set of notes for use.  It can be used for anything else that fits with that role ( e.g. creating and sharing ideas within teams, minutes-of-meetings, formal notes... etc.).

To add new roles to the basic WordPress roles (Administrator, Editor, Subscriber ..etc ) you will need to use another plugin refer to [Roles_and_Capabilities]( http://codex.wordpress.org/Roles_and_Capabilities#Resources ) and Resources.. Plugins.  You can then use one of these plugins ( e.g. [User Role Editor]( http://wordpress.org/extend/plugins/user-role-editor/ "User Role Editor" ) ) to allocate users to multiple roles.

So if you want to quickly give a group a private area to share and post ideas/notes ..
1. Create a new role.
2. Add users to the role.
3. Enable the Help Notes from the settings.

A global 'General Help Notes' type is also available which is not tided to a specific role, this has the same access/capabilities as the standard WordPress 'post' type for write access.  Unlike the other Help Notes the General Type is public to read, you can see an example over at an [example content page]( https://justinandco.com/plugins/help-notes/).

There is a widget available to list all users with access to the current Help Note type on display. The user display_name is shown and can be selected by each user within their own profile.

Extensions:

If you select the options for extending functionality through other plugins the following are available for ease of installing..

Admin side Plugins..

* [User Switching]( http://wordpress.org/plugins/user-switching/) great tool for admins to switch to test any users access/capability.
* [Simple Page Ordering]( http://wordpress.org/plugins/simple-page-ordering/) for easing re-ordering of Help Notes.
* [post_type_switcher]( http://wordpress.org/plugins/post-type-switcher/) plugin by [John James Jacoby]( http://profiles.wordpress.org/johnjamesjacoby/) & [Matthew Gerring]( http://profiles.wordpress.org/beatpanda/), useful to change the role associated with a Help Note after it has been created.

Front End Plugins..

* [Simple FootNotes]( http://wordpress.org/plugins/simple-footnotes/) by [Andrew Nacin]( http://profiles.wordpress.org/nacin/) to add a footnote shortcode [ref][/ref].
* [disable_comments]( http://wordpress.org/plugins/disable-comments/) by [solarissmoke]( http://profiles.wordpress.org/solarissmoke/) allows you to easily remove comments from 'Help Note' use.
* [Post type archive in menu]( http://wordpress.org/plugins/post-type-archive-in-menu/) plugin by [lpeharda]( http://profiles.wordpress.org/lpeharda/) to add 'Help Notes' archives to your menus.

[Plugin site]( http://justinandco.com/plugins/role-based-help-notes/).  	
[GitHub page]( http://github.com/justinticktock/role-based-help-notes ).

== Installation ==

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Goto the "Settings" Menu and "Help Notes" sub menu, select which roles are to have Help Notes.
4. Allocate roles to users for the Help Notes to appear in their Admin menu.

== Frequently Asked Questions ==

= I have a new role how can I add it? =

You will need to use another plugin to manage roles and capabilities such as the [User Role Editor]( http://wordpress.org/extend/plugins/user-role-editor/ "User Role Editor" ) plugin.

= Is there a theme template I can modify in my child theme? =

Yes ... [Answer]( http://wordpress.org/support/topic/is-there-a-theme-template-i-can-modify-in-my-child-theme?replies=3#post-4929519)

== Screenshots ==

1. The Settings Screen.
2. A front end example Help Note for a user with the 'Proof Reader' role, also showing the user widget listing all proof readers.
3. Dashboard showing Help Notes at WordPress 3.8
4. The 'Appearance..Widgets' Admin screen using the 'Help Note Users' widget with the 'twenty fourteen' theme sidebar
5. The 'twenty fourteen' theme showing the Contents page for a user with 'Proof Reader' role access.

== Changelog ==

Change log is maintained on [the plugin website]( https://justinandco.com/plugins/role-based-help-notes-change-log/ "Role Based Help Notes Plugin Changelog" )

== Upgrade Notice ==

= 1.2.9.1 =
The User Widget has been changed to fix conflicts.  
Due to this you will need to replace the widget in your sidebar!