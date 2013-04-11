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

This plugin adds a [Leaflet](http://leafletjs.com) map field to the [Advanced Custom Fields](http://wordpress.org/extend/plugins/advanced-custom-fields/) plugin. Use it to display beutiful maps with markers along with your posts and pages.

* Add multiple markers to the map.
* The field saves both your zoom-level and viewport location.
* Function to render the map in your theme is included in the plugin: `<?php the_leaflet_field( $field_name ); ?>`, just plug and play!

== Installation ==

1. Upload `acf-leaflet-field` to the `/wp-content/plugins/` directory
1. Make sure you have [Advanced Custom Fields](http://wordpress.org/extend/plugins/advanced-custom-fields/) installed and activated
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Register for an account at CloudMade to get you API-key
1. Add a Leaflet field to a ACF field group and save
1. The field is now ready for use

== Instructions ==
To render a map all you have to do is use `the_leaflet_field( $field_name );` where you want to render the map.

== To do ==
Thing I plan on adding in the near future.
1. Popups that can be added to each marker.
1. Support for drawing polylines.
1. Support for alternative tile-providers including your own tiles.
1. Support for alternative marker-icons.

== GitHub ==

If you want the latest development version of this plugin it is available over at my [github repository](https://github.com/jensjns/acf-leaflet-field/). The github repository will always have the latest code and may occasionally be broken and not work at all.

== Frequently Asked Questions ==

= I activated the plugin but nothing cool happened ;( =

This is not a standalone plugin, you need to have [Advanced Custom Fields](http://wordpress.org/extend/plugins/advanced-custom-fields/) installed and activated for it to work.

= What is CloudMade and why do I need an API-key from them? =

CloudMade delivers the map-data needed. Alternative tile-providers will be investigated in the near future.

== Changelog ==

= 0.1.0 =
* Release

== Upgrade Notice ==

= 0.1.0 =
* Release