=== LogMyTrip ===
Contributors: johnwaters, frsh
Tags: travel, adventure travel, maps, google map, route maps, trip, travel, journey, route
Donate link: http://www.LogMyTrip.co.uk
Requires at least: 3.1
Tested up to: 4.1
Stable tag: 1.9

Viewing your posts as a route plotted on a Google map is simple with this plugin.
Just add the shortcode [logmytripmap] to a page to see the map.

== Description ==

Your posts are plotted on a Google map as points on a route in date order. Just create a page for your map, then add the shortcode [logmytripmap] anywhere in the page to see your map appear. Clicking on a point icon can show a picture taken at that location if one is attached to a post.  A small Google map is displayed on the "Edit Post" screen to allow users to geotag their posts.

Hovering over the address when viewing individual posts reveals a map of the post location. See <a href="http://www.LogMyTrip.co.uk">www.LogMyTrip.co.uk</a> for more info.

== Installation ==

1. Upload the 'logmytrip' directory to the '/wp-content/plugins/' directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Create a page and add the shortcode [logmytripmap] anywhere on the page.
4. Add posts and geolocate them on a Google map. Posts can include photo's, either directly in the text or as attachments.

== Frequently Asked Questions ==

= Can I upload pictures directly from my mobile and have them show up on a LogMyTrip map? =

You can upload your pictures using Wordpress for iPhone, Android, Blackberry or Nokia. Geolocate your post using these apps and then it will show up on a LogMyTrip map.

= Does the plugin read EXIF (latitude/longitude) data from my photo and use it to plot my location? =

It does on the <a href="http://www.LogMyTrip.co.uk">www.LogMyTrip.co.uk</a> site, but not using the plugin alone.

= Can I create more than one trip on the <a href="http://www.LogMyTrip.co.uk">www.LogMyTrip.co.uk</a> website? =

It's only one trip per user, but it's easy to create an additional user using an alternative email address, then use it to create another trip.

= Does the LogMyTrip plugin work with the Geolocation plugin? =

The LogMyTrip plugin works with, but does not require, the Geolocation plugin.

= Please Note: = This plugin plots ALL posts that have associated geolocation information in date order for the default (post_author = 1) user. Points without geolocation data, or for other users, will not be plotted. However, you can switch to a different user by creating a cookie called 'tid' on the root of your site with a value equal to the ID required; e.g. Cookie: tid=4 for post_author 4. 

== Screenshots ==

1. Example of the LogMyTrip map in use.

== Changelog ==

= 1.6 Initial non beta release in plugin form. readme.txt updated to clarify installation and use =
= 1.7 Deleted =
= 1.8 Update to increase hover map size and fix some css interaction problems with other plugins =
= 1.9 Update to work with latest version of google maps api =

== Upgrade Notice ==

To upgrade, simply overwrite the existing logmytrip directory with the newer files

