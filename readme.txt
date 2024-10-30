=== Buoy Plugin ===
Contributors: buoyalerts.com 
Plugin Name: BuoyAlerts WP Buoy Plugin
Plugin URI: http://www.buoyalerts.com/wordpress_buoy_plugin/
Requires at least: 1.0
Tested up to: 3.3
Stable tag: 1.0.3
License: GPLv2 or later
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=DECX4QFHCV8C8
Author URI: http://www.buoyalerts.com/wordpress_buoy_plugin/
Tags: navigation, buoys, sidebar, surf, surfing, weather, sports

This Buoy Alerts Wordpress plugin will display the current conditions for any buoy available on the buoyalerts.com site in your wordpress sidebar.

== Description ==
This Buoy Alerts Wordpress plugin will display the current conditions for any buoy available on the buoyalerts.com site in your wordpress sidebar. We've gone the distance to provide a flexible and capable plugin that is easy to install, configure, and keep your blog readers informed with the ocean conditions that are relevant to you. Perfect for surfing, sailing, scuba diving or other ocean related blogs. We are incredibly open to feedback, so if you have a feature request please let us know. Free support is available, see the [website](http://www.buoyalerts.com/wordpress_buoy_plugin/) for details.


== Installation ==

1. Upload `buoyalerts_wp_buoy.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Visit [this page](http://buoyalerts.com/buoys/ "Buoy Alerts Buoy List") and note the id of the buoy you want to display.
1. Go to Settings > Buoy Conditions page and enter the buoy id you want to display
1. Customize display settings as needed
1. Your Done!


== Screenshots ==

1. A screen shot of the plugin showing the San Francisco Buoy Conditions on a clean install of Wordpress with the default theme 
2. The plugin showing the West Santa Barbara Channel buoy as it appears on the Christian Surfers Santa Barbara [website](http://cssantabarbara.com)
3. Buoy Conditions admin page accessible via Settings > Buoy Conditions link in the admin panel
4. Multiple options are available for displaying information in feet or meters, celcius or fahrenheight, meters or knots, compass cardinal direction or degrees. You can also turn information on or off with the click of a checkbox.
5. The CSS that controls the display of the sidebar is completely customizable and controlled via the web based admin panel. No need to manually edit files. If you create your own css, please email a copy to wp-plugin-support at buoyalerts dot com. We are creating a gallery of styles and will recognize your contribution with a link or credit. 


== Frequently Asked Questions ==

= How often is the buoy data updated =

Buoy data is updated every 30 - 60 minutes.

= Why isn't the data changing? =

Periodically buoys go offline, or go adrift. In this case the last available reading will be displayed. 

= Will this plugin slow down my site? =

Absolutely not. We request and cache data on your wordpress site every 15 minutes. At most, it will add a total of 4 requests per hour (max) to your site. JSON is used as a transport layer, keeping things speedy and fast. 

= Where can I go for help =

[The plugin page](http://www.buoyalerts.com/wordpress_buoy_plugin/) has our contact info, you can also hit us up on twitter @buoyalerts

= This is cool, how can I say thanks =

[Buy us a Beer](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=DECX4QFHCV8C8) or send us an email to say thanks. We love to see our code in the wild. Also, sending suggestions about how we could make it better . . . feedback from users who care is invaluable. 



== Changelog ==

= 1.0 = 
* Initial Public Release

= 1.0.1 =
* Fixed caching bug by using delete_transient and set_transient in place of wp_cache
* Fixed a typo that was doing a var_dump on the admin panel
* Fixed a bug that caused swell height to show in meters even after selecting feet in the admin page

= 1.0.2 = 
* Updated version properly so 1.0 users can update via admin panel
* Cleaned up formatting
* Added screen shots from users

= 1.0.3 = 
* Word Press 3.3 Compatibility check
* Fixed 404 on readme
* Added additional buoys per user request