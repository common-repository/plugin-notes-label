=== Plugin Notes Label ===
Contributors: WPGear
Donate link: wpgear.xyz/plugin-notes-label/
Tags: plugin note,dashboard,notes,plugin list,comments for plugins,memo,label,custom note
Requires at least: 4.1
Tested up to: 6.6
Requires PHP: 5.4
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Stable tag: 4.17

Add your Notes to each plugin.

== Description ==
This Plugin allows you to Add personal Notes, comments, memo to each of the Plugins.
You can change or delete any Note at any time.
Notes are available to anyone who has access to the Plugins page.

Here are some reasons why "Plugin Notes Label" is needed and useful:
- Over time, sometimes you forget exactly why this or that Plugin was installed.
- Some Plugins have to be modified (although this is not correct), and their updating requires special attention. That is, you need to remind yourself and others that you cannot update such a Plugin without careful preparation.
- Sometimes you expect a specific new promised functionality from a certain Plugin, and up to this point, all intermediate updates are not particularly important.
- Anyone who administers WordPress can remember a few more similar reasons. ))

= Features =
* Add, Edit, Delete Notes - does not require page refresh.
* Notes fit neatly and compactly into the general list of Plugins without breaking the original style.
* Displays Notes on the page: "update-core". This is especially important if any Plugin requires special attention.
* Works correctly with Translated Plugin Names.
* Works correctly with HTML Entity in Plugin Names.
* Setup-Page:
	- "Enable Setup-Page for Admin only" On/Off
	- "Show note Author" On/Off
	- "Show note Date" On/Off
* "Import" - "Export":	
	- "Import". All Notes are imported, along with information about the creation date and author name.
	- "Export". If the Note already exists, but it does not exist in the Export file, then the Note remains unchanged. If the File contains a Note about a Plugin that is not on this site, then the Note will be saved and will be displayed when such a Plugin is installed.
* "Clear All Notes":
	- It can be useful in some cases when you need to completely Delete all Notes.

== Installation ==

1. Upload 'plugin-notes-label' folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. If you have any problems - please ask for support. 

== Frequently Asked Questions ==

== Screenshots ==
1. screenshot-1.png This is the Admin Dashboard Plugins-Page with a "Plugin Notes Label".
2. screenshot-2.png Edit Content box interface.
3. screenshot-3.png Setup-Page with Options.
4. screenshot-4.png This is the Admin Dashboard Plugins-Page with a "Plugin Notes Label" with Options: "Show note Author" & "Show note Date".
5. screenshot-5.png This is the Admin Dashboard Update-Page with a "Plugin Notes Label".

== Changelog ==
= 4.17 =
	2024.08.05
	* Fix Hide "note Author" & "note Date" - if Note is Deleted.
	* Tested with WP 6.6
	
= 4.16 =
	2024.04.02
	* Fix minor PHP Warning. 
	* Tested with WP 6.4.3
	
= 4.14 =
	2021.11.05
	* Fix minor PHP Warning.
	
= 4.14 =
	2021.10.07
	* Fix JS deprecated methods.
	* Tested with WP 5.8.1
	
= 4.13 =
	2021.05.01
	* Update style Message-Box for page: "update-core".
	
= 4.12 =
	2021.04.24
	* Set Clear List-Style.
	* Add Message-Box on the page: "update-core".
	
= 4.11 =
	2021.04.23
	* Fix processing with HTML Entity in Plugin Names.
	
= 4.10 =
	2021.04.22
	* Update Readme.txt	
	
= 4.9 =
	2021.04.22
	* Fix processing with Translated Plugin Names.
	
= 4.8 =
	2021.04.22
	* Fix compatible whith old versions.
	
= 4.7 =
	2021.04.22
	* Disable "Plugin Notes Label" scripts, if run process Plugin Update.
	
= 4.6 =
	2021.04.22
	* Now displays Notes on the page: "update-core". Add, Edit, Delete Notes - enable.
	
= 3.5 =
	2021.04.20
	* Added "Clear All Notes". Сompletely Delete all Notes.
	
= 3.4 =
	2021.04.19
	* Added "Import" - "Export". Now, you can easily transfer multiple Notes as a Collection to different sites.
	
= 2.3 =
	2021.04.16
	* Added Settings Page with Options:
		"Enable Setup-Page for Admin only" On/Off, 
		"Show note Author" On/Off, 
		"Show note Date" On/Off
	
= 1.2 =
	2021.04.15
	* Fix Edit after Edit Notes.
	
= 1.1 =
	2021.04.15
	* Published in the Repository. Go!
	
= 1.0 =
	2021.04.13
	* Initial release

== Upgrade Notice ==
= 3.4 =
	* Added "Import" - "Export". Now, you can easily transfer multiple Notes as a Collection to different sites.