<?php

/*
    Plugin Name: Advanced Custom Fields: Leaflet field
    Plugin URI: https://github.com/jensjns/acf-leaflet-field/
    Description: Adds a Leaflet field to Advanced Custom Fields.
    Version: Not working
    Author: Jens Nilsson
    Author URI: http://jensnilsson.nu/
    License: GPLv2
*/

class acf_field_leaflet_plugin
{

    function __construct()
    {
        // set text domain
        $domain = 'acf-leaflet-field';
        $mofile = trailingslashit(dirname(__File__)) . 'lang/' . $domain . '-' . get_locale() . '.mo';
        load_textdomain( $domain, $mofile );

        add_action('acf/register_fields', array($this, 'register_field')); 
    }

    function register_field()
    {
        include_once('leaflet-field.php');
    }

}

new acf_field_leaflet_plugin();

?>