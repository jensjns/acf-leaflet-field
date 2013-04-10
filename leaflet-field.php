<?php

class acf_field_leaflet extends acf_field
{
    // vars
    var $defaults; // will hold default field options
        
        
    /*
    *  __construct
    *
    *  Set name / label needed for actions / filters
    *
    *  @since   4.0.2
    *  @date    06/04/13
    */
    
    function __construct()
    {
        // vars
        $this->name = 'leaflet-field';
        $this->label = __('Leaflet Map');
        $this->category = __('Content','acf');
        $this->defaults = array(
            'lat'           => '55.606',
            'lng'           => '13.002',
            'zoom_level'    => 13,
            'height'        => 350,
            'api_key'       => ''
        );
        
        
        // do not delete!
        parent::__construct();
    }
    
    
    /*
    *  input_admin_enqueue_scripts()
    *
    *  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
    *  Use this action to add css + javascript to assist your create_field() action.
    *
    *  $info    http://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
    *  @type    action
    *  @since   3.6
    *  @date    23/01/13
    */

    function input_admin_enqueue_scripts()
    {
        
    }
    
    
    /*
    *  input_admin_head()
    *
    *  This action is called in the admin_head action on the edit screen where your field is created.
    *  Use this action to add css and javascript to assist your create_field() action.
    *
    *  @info    http://codex.wordpress.org/Plugin_API/Action_Reference/admin_head
    *  @type    action
    *  @since   3.6
    *  @date    23/01/13
    */

    function input_admin_head()
    {
        // styles
        wp_enqueue_style( 'leaflet', plugins_url( '/js/leaflet/leaflet.css', __FILE__ ), array(), '0.5.1', 'all' );
        wp_enqueue_style( 'leaflet-ie', plugins_url( '/js/leaflet/leaflet.ie.css', __FILE__ ), array( 'leaflet' ), '0.5.1' );
        $GLOBALS['wp_styles']->add_data( 'leaflet-ie', 'conditional', 'lte IE 8' );
        wp_enqueue_style( 'icomoon', plugins_url( '/css/icomoon/style.css', __FILE__ ), array(), '1.0.0', 'all' );
        wp_enqueue_style( 'leaflet-field', plugins_url( '/css/leaflet-field.css', __FILE__ ), array( 'leaflet', 'icomoon' ), '1', 'all' );

        // scripts
        wp_enqueue_script( 'jquery' );
        wp_register_script( 'leaflet', plugins_url( '/js/leaflet/leaflet.js', __FILE__ ), array(), '0.5.1', true );
        wp_register_script( 'leaflet-field', plugins_url( '/js/leaflet-field.js', __FILE__ ), array( 'jquery', 'leaflet' ), '1', true );
        wp_enqueue_script( 'leaflet' );
        wp_enqueue_script( 'leaflet-field' );
    }
    
    
    /*
    *  create_options()
    *
    *  Create extra options for your field. This is rendered when editing a field.
    *  The value of $field['name'] can be used (like bellow) to save extra data to the $field
    *
    *  @type    action
    *  @since   3.6
    *  @date    23/01/13
    *
    *  @param   $field  - an array holding all the field's data
    */
    
    function create_options( $field )
    {
        // defaults
        $field = array_merge($this->defaults, $field);

        // key is needed in the field names to correctly save the data
        $key = $field['name'];
        
        
        // Create Field Options HTML
        ?>
            <tr class="field_option field_option_<?php echo $this->name; ?>">
                <td class="label">
                    <label><?php _e('Cloudmade API-key','acf-location-field'); ?></label>
                    <p class="description"><?php _e('Register for an API-key at ','acf-leaflet-field'); ?><a href="http://account.cloudmade.com/register" target="_blank">CloudMade</a>.</p>
                </td>
                <td>
                    <?php
                    do_action('acf/create_field', array(
                        'type'  => 'text',
                        'name'  => 'fields['.$key.'][api_key]',
                        'value' => $field['api_key']
                    ));
                    ?>
                </td>
            </tr>
            
            <tr class="field_option field_option_<?php echo $this->name; ?>">
                <td class="label">
                    <label><?php _e('Zoom level','acf-leaflet-field'); ?></label>
                    <p class="description"><?php _e('','acf-leaflet-field'); ?></p>
                </td>
                <td>
                    <?php
                    do_action('acf/create_field', array(
                        'type'  => 'number',
                        'name'  => 'fields['.$key.'][zoom_level]',
                        'value' => $field['zoom_level']
                    ));
                    ?>
                </td>
            </tr>

            <tr class="field_option field_option_<?php echo $this->name; ?>">
                <td class="label">
                    <label><?php _e('Latitude','acf-leaflet-field'); ?></label>
                    <p class="description"><?php _e('','acf-leaflet-field'); ?></p>
                </td>
                <td>
                    <?php
                    do_action('acf/create_field', array(
                        'type'  => 'number',
                        'name'  => 'fields['.$key.'][lat]',
                        'value' => $field['lat']
                    ));
                    ?>
                </td>
            </tr>
            
            <tr class="field_option field_option_<?php echo $this->name; ?>">
                <td class="label">
                    <label><?php _e('Longitude','acf-leaflet-field'); ?></label>
                    <p class="description"><?php _e('','acf-leaflet-field'); ?></p>
                </td>
                <td>
                    <?php
                    do_action('acf/create_field', array(
                        'type'      => 'number',
                        'name'      => 'fields['.$key.'][lng]',
                        'value'     => $field['lng']
                    ));
                    ?>
                </td>
            </tr>

            <tr class="field_option field_option_<?php echo $this->name; ?>">
                <td class="label">
                    <label><?php _e('Height','acf-leaflet-field'); ?></label>
                    <p class="description"><?php _e('The map needs a specified height to be rendered correctly.','acf-leaflet-field'); ?></p>
                </td>
                <td>
                    <?php
                    do_action('acf/create_field', array(
                        'type'      => 'number',
                        'name'      => 'fields['.$key.'][height]',
                        'value'     => $field['height']
                    ));
                    ?>
                </td>
            </tr>
        <?php
    }
    
    
    /*
    *  create_field()
    *
    *  Create the HTML interface for your field
    *
    *  @param   $field - an array holding all the field's data
    *
    *  @type    action
    *  @since   3.6
    *  @date    23/01/13
    */
    
    function create_field( $field )
    {
        // defaults
        $field = array_merge($this->defaults, $field);

        // Build an unique id based on ACF's one.
        $pattern = array('/\[/', '/\]/');
        $replace = array('_', '');
        $uid = preg_replace($pattern, $replace, $field['name']);
        error_log( $field['name'] );
        $field['id'] = 'leaflet_' . $uid;

        wp_localize_script( 'leaflet-field', 'leaflet_field', $field );

        ?>
            <input type="hidden" value='<?php echo $field['value']; ?>' id="leaflet_<?php echo $uid; ?>" name="<?php echo $field['name']; ?>"/>
            <div class="leaflet-map">
                <ul class="tools">
                    <!--<li class="tool tool-compass icon-compass"></li>-->
                    <li class="tool tool-marker icon-location active"></li>
                    <!--<li class="tool tool-tag icon-comment-alt2-fill"></li>-->
                    <!--<li class="tool tool-path icon-share"></li>-->
                    <li class="tool tool-remove icon-cancel-circle red"></li>
                    <!--<li class="tool tool-reset icon-reload right red"></li>-->
                </ul>
                <div id="<?php echo $field['id'] . '_map'; ?>" style="height:<?php echo $field['height']; ?>px;" data-uid="leaflet_map_<?php echo $uid; ?>"></div>
            </div>
        <?php
    }
    
    
    
    /*
    *  format_value_for_api()
    *
    *  This filter is appied to the $value after it is loaded from the db and before it is passed back to the api functions such as the_field
    *
    *  @type    filter
    *  @since   3.6
    *  @date    23/01/13
    *
    *  @param   $value  - the value which was loaded from the database
    *  @param   $field  - the field array holding all the field options
    *
    *  @return  $value  - the modified value
    */
    
    function format_value_for_api( $value, $post_id, $field )
    {
        // defaults
        $field = array_merge($this->defaults, $field);
    

        // format value
        return $value;
    }
    
}

// create field
new acf_field_leaflet();

?>