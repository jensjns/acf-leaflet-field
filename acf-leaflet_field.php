<?php
/*
    Plugin Name: Advanced Custom Fields: Leaflet Field
    Plugin URI: https://github.com/jensjns/acf-leaflet-field
    Description: Adds a Leaflet map-field to Advanced Custom Fields.
    Version: 1.2.1
    Author: Jens Nilsson
    Author URI: http://jensnilsson.nu/
    License: GPLv2 or later
    License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/


class acf_field_leaflet_field_plugin
{
    /*
    *  Construct
    *
    *  @description:
    *  @since: 3.6
    *  @created: 1/04/13
    */

    function __construct()
    {
        // set text domain
        $domain = 'acf-leaflet_field';
        $mofile = trailingslashit(dirname(__File__)) . 'lang/' . $domain . '-' . get_locale() . '.mo';
        load_textdomain( $domain, $mofile );

        // version 5 (PRO)
        add_action('acf/include_field_types', array($this, 'register_fields_v5'));

        // version 4+
        add_action('acf/register_fields', array($this, 'register_fields_v4'));

        // version 3-
        if(function_exists('register_field'))
        {
            add_action( 'init', array( $this, 'init' ));
        }
    }

    /*
    *  Init
    *
    *  @description:
    *  @since: 3.6
    *  @created: 1/04/13
    */

    function init()
    {
        register_field('acf_field_leaflet_field', dirname(__File__) . '/leaflet_field-v3.php');
    }

    /*
    *  register_fields
    *
    *  @description:
    *  @since: 3.6
    *  @created: 1/04/13
    */

    function register_fields_v4()
    {
        include_once('leaflet_field-v4.php');
    }

    function register_fields_v5()
    {
        include_once('leaflet_field-v5.php');
    }

}

new acf_field_leaflet_field_plugin();

    /**
     *  the_leaflet_field()
     *
     *  Renders leaflet field
     *
     *  @param   $field_name - Required, The name of the field
     *  @param   $post_id - Optional, the id of the post (will try to render for the current page if no id is specified)
     *
     *  @since   0.1.0
     *  @date    10/04/13
     */
    function the_leaflet_field( $field_name, $post_id = false ) {
        if( !$post_id ) {
            global $post;
            $post_id = $post->ID;
        }

        $field_obj = get_field_object(
            $field_name,
            $post_id,
            array(
                'load_value' => true
            )
        );

        $field_obj['map_provider'] = acf_field_leaflet_field::$map_providers[$field_obj['map_provider']];

        if( $field_obj['map_provider']['requires_key'] ) {
            $field_obj['map_provider']['url'] = str_replace( '{api_key}', $field_obj['api_key'], $field_obj['map_provider']['url'] );
        }

        if( $field_obj['value'] ) {
            // enqueue styles
            wp_enqueue_style( 'leaflet', plugins_url( '/js/leaflet/leaflet.css', __FILE__ ), array(), '0.7.3', 'all' );

            // enqueue scripts
            wp_enqueue_script( 'jquery' );
            wp_enqueue_script( 'leaflet', plugins_url( '/js/leaflet/leaflet.js', __FILE__ ), array(), '0.7.3', true );
            wp_enqueue_script( 'leaflet-frontend', plugins_url( '/js/leaflet-frontend.js', __FILE__ ), array( 'jquery', 'leaflet' ), '1.2.1', true );
            wp_localize_script( 'leaflet-frontend', 'leaflet_field', $field_obj );
            echo '<div id="' . $field_obj['id'] . '_map" class="leaflet-map" style="height:' . $field_obj['height'] . 'px;"></div>';
        }
    }

?>
