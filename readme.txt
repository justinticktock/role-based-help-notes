=== Plugin Name ===
Contributors: justinticktock
Tags: multisite, roles, user, help, notes, cms, documents, groups, teams, collaboration, BuddyPress, intranet
Requires at least: 3.5
Tested up to: 4.4
Stable tag: 1.7
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

* [Role Includer]( https://wordpress.org/plugins/role-includer/ ) simple interface to handle multiple roles with users.  If you wish to hide/mask-out a particular role from "staff" ( such as "Administrator" ) so that staff cannot allocate the higher access level then you can exclude higher roles by using the ["Role Excluder"]( https://justinandco.com/plugins/downloads/role-excluder/) plugin.
* [Email Users]( https://wordpress.org/plugins/email-users/ ) allows users within a Help Note group/role to email others.
* [User Switching]( https://wordpress.org/plugins/user-switching/) great tool for admins to switch to test any users access/capability.
* [Simple Page Ordering]( https://wordpress.org/plugins/simple-page-ordering/) for easing re-ordering of Help Notes.
* [Post Type Switcher]( http://wordpress.org/plugins/post-type-switcher ) allows users with two or more roles to change the role assigned to a help note, useful if you created a Help Note under the wrong role.
* [Pixabay Images]( https://wordpress.org/plugins/pixabay-images/ ) provides a quick method for all enabled roles within Help Notes to upload Public Domain images.


Front End Plugins..

* [Simple FootNotes]( https://wordpress.org/plugins/simple-footnotes/) by [Andrew Nacin]( https://profiles.wordpress.org/nacin/) to add a footnote shortcode [ref][/ref].
* [Tabby Response Tabs]( https://wordpress.org/plugins/tabby-responsive-tabs/) by [cubecolour]( https://profiles.wordpress.org/numeeja/) to add a tabbed effect to you contents page.
* [disable_comments]( https://wordpress.org/plugins/disable-comments/) by [solarissmoke]( https://profiles.wordpress.org/solarissmoke/) allows you to easily remove comments from 'Help Note' use.
* [Menu Item Visibility Control]( https://wordpress.org/plugins/menu-items-visibility-control/) plugin by [shazdeh]( https://profiles.wordpress.org/shazdeh/) to add/hide 'Help Notes' to your menus.

[Plugin site]( https://justinandco.com/plugins/role-based-help-notes/).  

== Installation ==

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Goto the "Settings" Menu and "Help Notes" sub menu, select which roles are to have Help Notes.
4. Allocate roles to users for the Help Notes to appear in their Admin menu.
5. `NOTE ! if you want to edit Help Notes yourself you need to have the role(s) assigned to your user profile!`
   (Just being an Administrator does not give you access).

== Frequently Asked Questions ==

= I have a new role how can I add it? =

You will need to use another plugin to manage roles and capabilities such as the [User Role Editor]( https://wordpress.org/extend/plugins/user-role-editor/ "User Role Editor" ) plugin.

= Is there a theme template I can modify in my child theme? =

Yes ... [Answer]( https://wordpress.org/support/topic/is-there-a-theme-template-i-can-modify-in-my-child-theme?replies=3#post-4929519)

== Screenshots ==

1. The Settings 'General' Tab Screen.
2. The Settings 'Role' Tab showing 4 user roles enabled for Private Help Notes.
3. Dashboard showing the 'Staff Admin" user role access.
4. The Settings 'Email Groups' shows when the email-users plugin is active and enables the email/widget Help Note functionality.
5. The Twenty Fifteen theme showing a help note with the the email and user listing widgets enabled.
6. The Twenty Fifteen theme showing the contents page for the 'Staff Admin' role, also with the 'Tabby Responsive Tabs' Plugin Active. The other tabs for 'Contributor', 'Author' and 'Developer' are present as the logged user also has these roles.


== Changelog ==

Change log is maintained on [the plugin website]( https://justinandco.com/plugins/role-based-help-notes-change-log/ "Role Based Help Notes Plugin Changelog" )

== Upgrade Notice ==

= 1.2.9.1 =
The User Widget has been changed to fix conflicts.  
Due to this you will need to replace the widget in your sidebar!