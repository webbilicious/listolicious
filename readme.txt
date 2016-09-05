=== Listolicious ===
Contributors: webbilicious
Donate link: http://webbilicious.se/en/donate/
Tags: shortcode, custom post type, list, movie
Requires at least: 4.5.3
Tested up to: 4.6
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The shortcode displays a list in the style of Mubi

== Description ==
The plugin creates the shortcode [listolicious] for displaying a movie list in the style of Mubi. As the plugin is made specifically for displaying a movie list with custom fields, it creates the custom post type "Movies".

* [Demo](http://www.filmkultur.se/the-list)
* [GitHub](https://github.com/webbilicious/listolicious)

= Features =
* The shortcode displays posts of the custom post type "movies" as a list in the style of Mubi.
* When clicked it takes you to the custom post type post you have created.

= Usage =
1. Insert the shortcode [listolicious] in the content of the page/post. 
2. You can set two options:
	list:		slug of a list you've created (default: shows all movies)
	orderby:	title or year (default: year)  
Example:	[listolicious list="favourites" orderby="title"]  

= Contributors =
Daniel Hånberg Alonso at [webbilicious](https://profiles.wordpress.org/webbilicious/)  

= Translations/Languages =
So far Listolicious is translated to:

* Swedish

== Installation ==

1. Upload the full listolicious directory into your wp-content/plugins directory.
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Insert the shortcode [listolicious] in the content of your page/post. 
4. Populate the list by adding posts of the custom post type "Movies".

== Screenshots ==

1. The list as it is displayed.

== Changelog ==

= 1.1 =

* Created a custom taxonomy and the ability to create multiple lists.

= 1.0 =

* Initial release.

== Upgrade Notice ==

