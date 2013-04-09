=== Advanced Custom Fields: Leaflet field ===
Contributors: jensnilsson
Donate link: http://jensnilsson.nu/
Tags: Advanced Custom Fields, acf, acf4, custom fields, admin, wp-admin
Requires at least: 3.0.0
Tested up to: 3.5.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Addon for Advanced Custom Fields that adds a Leaflet field to the available field types.

== Description ==

This plugin adds a leaflet field to the [Advanced Custom Fields](http://wordpress.org/extend/plugins/advanced-custom-fields/) plugin.
The field itself is a leaflet-map which you can add markers to.

== Installation ==

1. Upload `acf-leaflet-field` to the `/wp-content/plugins/` directory
1. Make sure you have [Advanced Custom Fields](http://wordpress.org/extend/plugins/advanced-custom-fields/) installed and activated
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Register for an account at CloudMade to get you API-key
1. Add a Leaflet field to a ACF field group and save
1. The field is now ready for use

== Frequently Asked Questions ==

= I activated the plugin but nothing cool happened ;( =

This is not a standalone plugin, you need to have [Advanced Custom Fields](http://wordpress.org/extend/plugins/advanced-custom-fields/) installed and activated for it to work.

= What is CloudMade and why do I need an API-key from them? =

CloudMade delivers the map-data needed to actually see a map (the tiled images).
The first 500k tiles you load with your API-key will be free. Please refer to [cloudmade.com](http://cloudmade.com/) for further details about their pricing.  Alternative providers will be investigated in the future.

== Changelog ==

= 0.1 =
* Release

== Upgrade Notice ==

= 0.1 =
* Release