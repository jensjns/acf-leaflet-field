<?php

class acf_field_leaflet_field extends acf_field
{

    // vars
    var $settings, // will hold info such as dir / path
        $defaults; // will hold default field options

    // holds information about supported tile-providers
    static $map_providers = array(
        'openstreetmap' => array(
            'url'           => 'http://tile.openstreetmap.org/{z}/{x}/{y}.png',
            'requires_key'  => false,
            'nicename'      => 'OpenStreetMap',
            'attribution'   => 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors'
        ),
        'openstreetmap_blackandwhite' => array(
            'url'           => 'http://{s}.www.toolserver.org/tiles/bw-mapnik/{z}/{x}/{y}.png',
            'requires_key'  => false,
            'nicename'      => 'OpenStreetMap Black and White',
            'attribution'   => '&copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>'
        ),
        'cloudmade'     => array(
            'url'           => "http://{s}.tile.cloudmade.com/{api_key}/997/256/{z}/{x}/{y}.png",
            'requires_key'  => true,
            'nicename'      => 'CloudMade',
            'attribution'   => 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://cloudmade.com">CloudMade</a>'
        )
    );

    /*
    *  __construct
    *
    *  This function will setup the field type data
    *
    *  @type    function
    *  @date    5/03/2014
    *  @since   5.0.0
    *
    *  @param   n/a
    *  @return  n/a
    */

    function __construct() {
        // vars
        $this->name = 'leaflet_field';
        $this->label = __( 'Leaflet Field' );
        $this->category = __( 'Content','acf' ); // Basic, Content, Choice, etc
        $this->defaults = array(
            'lat'           => '55.606',
            'lng'           => '13.002',
            'zoom_level'    => 13,
            'height'        => 400,
            'api_key'       => '',
            'map_provider'  => 'openstreetmap',
        );


        // do not delete!
        parent::__construct();


        // settings
        $this->settings = array(
            'path' => apply_filters( 'acf/helpers/get_path', __FILE__ ),
            'dir' => apply_filters( 'acf/helpers/get_dir', __FILE__ ),
            'version' => '1.2.1'
        );

        add_action( 'acf/field_group/admin_head', array( $this, 'conditional_options' ) );

    }


    /*
    *  render_field_settings()
    *
    *  Create extra settings for your field. These are visible when editing a field
    *
    *  @type    action
    *  @since   3.6
    *  @date    23/01/13
    *
    *  @param   $field (array) the $field being edited
    *  @return  n/a
    */

    function render_field_settings( $field ) {

        /*
        *  acf_render_field_setting
        *
        *  This function will create a setting for your field. Simply pass the $field parameter and an array of field settings.
        *  The array of settings does not require a `value` or `prefix`; These settings are found from the $field array.
        *
        *  More than one setting can be added by copy/paste the above code.
        *  Please note that you must also have a matching $defaults value for the field name (font_size)
        */

        //error_log(print_r($field, true));

        $providers = array();

        foreach( acf_field_leaflet_field::$map_providers as $key => $value ) {
            $providers[$key] = $value['nicename'];
        }

        acf_render_field_setting( $field, array(
            'label'         => __('Map Provider', 'acf-leaflet-field'),
            'instructions'  => __('Select map provider', 'acf-leaflet-field'),
            'type'          => 'radio',
            'name'          => 'map_provider',
            'layout'        => 'horizontal',
            'choices'       => $providers
        ));

        acf_render_field_setting( $field, array(
            'label'         => __('Cloudmade API-key', 'acf-leaflet-field'),
            'instructions'  => __('Register for an API-key at <a href="http://account.cloudmade.com/register" target="_blank">CloudMade</a>.', 'acf-leaflet-field'),
            'type'          => 'text',
            'name'          => 'api_key'
        ));

        acf_render_field_setting( $field, array(
            'label'         => __('Zoom level', 'acf-leaflet-field'),
            //'instructions'  => __('', 'acf-leaflet-field'),
            'type'          => 'number',
            'name'          => 'zoom_level'
        ));

        acf_render_field_setting( $field, array(
            'label'         => __('Default latitude', 'acf-leaflet-field'),
            //'instructions'  => __('', 'acf-leaflet-field'),
            'prepend'       => 'lat',
            'type'          => 'number',
            'name'          => 'lat'
        ));

        acf_render_field_setting( $field, array(
            'label'         => __('Default longitude', 'acf-leaflet-field'),
            //'instructions'  => __('', 'acf-leaflet-field'),
            'prepend'       => 'lng',
            'type'          => 'number',
            'name'          => 'lng'
        ));

        acf_render_field_setting( $field, array(
            'label'         => __('Height', 'acf-leaflet-field'),
            'instructions'  => __('The map needs a specified height to be rendered correctly.', 'acf-leaflet-field'),
            'prepend'       => 'px',
            'type'          => 'number',
            'name'          => 'height'
        ));

    }

    /*
    *  ACF { Conditional Logic
    *
    *  @description: hide / show fields based on a "trigger" field
    *  @created: 17/07/12
    */

    function conditional_options()
    {
        ?>
        <style type="text/css">
            [data-type="leaflet_field"] [data-name="api_key"] {
                display: none;
            }
        </style>
        <script type="text/javascript">
        (function($){
            /*
            *  Map provider change
            */

            $(document).on('change', '[data-name="map_provider"] input' , function(){
                // vars
                var value = $(this).val();

                <?php
                    // iterate map providers and check if they require an api-key
                    $conditions = '';
                    foreach( acf_field_leaflet_field::$map_providers as $key => $map_provider )
                    {
                        $conditions .= 'if( value == "' . $key . '" ) { $(this).parents("[data-name=\'map_provider\']").siblings("[data-name=\'api_key\']").';

                        if( $map_provider['requires_key'] )
                        {
                            $conditions .= 'show';
                        }
                        else {
                            $conditions .= 'hide';
                        }

                        $conditions .= '(); }';
                    }

                    echo $conditions;
                ?>
            });

        })(jQuery);
        </script>
        <?php
    }



    /*
    *  render_field()
    *
    *  Create the HTML interface for your field
    *
    *  @param   $field (array) the $field being rendered
    *
    *  @type    action
    *  @since   3.6
    *  @date    23/01/13
    *
    *  @param   $field (array) the $field being edited
    *  @return  n/a
    */

    function render_field( $field ) {


        // defaults
        $field = array_merge($this->defaults, $field);

        // Build an unique id based on ACF's one.
        $pattern = array( '/\[/', '/\]/' );
        $replace = array( '_', '' );
        $uid = preg_replace($pattern, $replace, $field['name']);

        $field['id'] = $uid;

        // resolve tile-layer and attribution
        $tile_layer = str_replace( '{api_key}', $field['api_key'], acf_field_leaflet_field::$map_providers[$field['map_provider']]['url'] );
        $attribution = acf_field_leaflet_field::$map_providers[$field['map_provider']]['attribution'];

        // include the javascript
        include_once("js/input.js.php");

        // render the field container,
        ?>
            <div id="leaflet_field-wrapper_<?php echo $uid; ?>" class="tool-marker-active">
                <input type="hidden" value='<?php echo $field['value']; ?>' id="field_<?php echo $uid; ?>" name="<?php echo $field['name']; ?>" data-zoom-level="<?php echo $field['zoom_level']; ?>" data-lat="<?php echo $field['lat']; ?>" data-lng="<?php echo $field['lng']; ?>" />
                <div class="leaflet-map" data-uid="<?php echo $uid; ?>" data-tile-layer="<?php echo $tile_layer; ?>" data-attribution='<?php echo $attribution; ?>'>
                    <div id="map_<?php echo $uid; ?>" style="height:<?php echo $field['height']; ?>px;"></div>
                </div>
            </div>
        <?php
    }


    /*
    *  input_admin_enqueue_scripts()
    *
    *  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
    *  Use this action to add CSS + JavaScript to assist your render_field() action.
    *
    *  @type    action (admin_enqueue_scripts)
    *  @since   3.6
    *  @date    23/01/13
    *
    *  @param   n/a
    *  @return  n/a
    */

    function input_admin_enqueue_scripts() {

        // styles
        wp_enqueue_style( 'leaflet', plugins_url( '/js/leaflet/leaflet.css', __FILE__ ), array(), '0.7.3', 'all' );
        wp_enqueue_style( 'leaflet.draw', plugins_url( '/js/Leaflet.draw/dist/leaflet.draw.css', __FILE__ ), array(), 'ccca4b11ba4ff545433bf70f610b215053a2615e', 'all' );
        wp_enqueue_style( 'icomoon', plugins_url( '/css/icomoon/style.css', __FILE__ ), array(), '1.0.0', 'all' );
        wp_enqueue_style( 'leaflet-field', plugins_url( '/css/input.css', __FILE__ ), array( 'leaflet', 'icomoon' ), '1', 'all' );

        // scripts
        wp_enqueue_script( 'jquery' );
        wp_register_script( 'leaflet', plugins_url( '/js/leaflet/leaflet.js', __FILE__ ), array(), '0.7.3', true );
        wp_register_script( 'leaflet.draw', plugins_url( '/js/Leaflet.draw/dist/leaflet.draw.js', __FILE__ ), array( 'leaflet' ), 'ccca4b11ba4ff545433bf70f610b215053a2615e', true );
        wp_enqueue_script( 'leaflet' );
        wp_enqueue_script( 'leaflet.draw' );


    }


    /*
    *  input_admin_head()
    *
    *  This action is called in the admin_head action on the edit screen where your field is created.
    *  Use this action to add CSS and JavaScript to assist your render_field() action.
    *
    *  @type    action (admin_head)
    *  @since   3.6
    *  @date    23/01/13
    *
    *  @param   n/a
    *  @return  n/a
    */

    /*

    function input_admin_head() {



    }

    */


    /*
    *  input_form_data()
    *
    *  This function is called once on the 'input' page between the head and footer
    *  There are 2 situations where ACF did not load during the 'acf/input_admin_enqueue_scripts' and
    *  'acf/input_admin_head' actions because ACF did not know it was going to be used. These situations are
    *  seen on comments / user edit forms on the front end. This function will always be called, and includes
    *  $args that related to the current screen such as $args['post_id']
    *
    *  @type    function
    *  @date    6/03/2014
    *  @since   5.0.0
    *
    *  @param   $args (array)
    *  @return  n/a
    */

    /*

    function input_form_data( $args ) {



    }

    */


    /*
    *  input_admin_footer()
    *
    *  This action is called in the admin_footer action on the edit screen where your field is created.
    *  Use this action to add CSS and JavaScript to assist your render_field() action.
    *
    *  @type    action (admin_footer)
    *  @since   3.6
    *  @date    23/01/13
    *
    *  @param   n/a
    *  @return  n/a
    */

    /*

    function input_admin_footer() {



    }

    */


    /*
    *  field_group_admin_enqueue_scripts()
    *
    *  This action is called in the admin_enqueue_scripts action on the edit screen where your field is edited.
    *  Use this action to add CSS + JavaScript to assist your render_field_options() action.
    *
    *  @type    action (admin_enqueue_scripts)
    *  @since   3.6
    *  @date    23/01/13
    *
    *  @param   n/a
    *  @return  n/a
    */

    /*

    function field_group_admin_enqueue_scripts() {

    }

    */


    /*
    *  field_group_admin_head()
    *
    *  This action is called in the admin_head action on the edit screen where your field is edited.
    *  Use this action to add CSS and JavaScript to assist your render_field_options() action.
    *
    *  @type    action (admin_head)
    *  @since   3.6
    *  @date    23/01/13
    *
    *  @param   n/a
    *  @return  n/a
    */

    /*

    function field_group_admin_head() {

    }

    */


    /*
    *  load_value()
    *
    *  This filter is applied to the $value after it is loaded from the db
    *
    *  @type    filter
    *  @since   3.6
    *  @date    23/01/13
    *
    *  @param   $value (mixed) the value found in the database
    *  @param   $post_id (mixed) the $post_id from which the value was loaded
    *  @param   $field (array) the field array holding all the field options
    *  @return  $value
    */

    /*

    function load_value( $value, $post_id, $field ) {

        return $value;

    }

    */


    /*
    *  update_value()
    *
    *  This filter is applied to the $value before it is saved in the db
    *
    *  @type    filter
    *  @since   3.6
    *  @date    23/01/13
    *
    *  @param   $value (mixed) the value found in the database
    *  @param   $post_id (mixed) the $post_id from which the value was loaded
    *  @param   $field (array) the field array holding all the field options
    *  @return  $value
    */

    /*

    function update_value( $value, $post_id, $field ) {

        return $value;

    }

    */


    /*
    *  format_value()
    *
    *  This filter is appied to the $value after it is loaded from the db and before it is returned to the template
    *
    *  @type    filter
    *  @since   3.6
    *  @date    23/01/13
    *
    *  @param   $value (mixed) the value which was loaded from the database
    *  @param   $post_id (mixed) the $post_id from which the value was loaded
    *  @param   $field (array) the field array holding all the field options
    *
    *  @return  $value (mixed) the modified value
    */

    /*

    function format_value( $value, $post_id, $field ) {

        // bail early if no value
        if( empty($value) ) {

            return $value;

        }


        // apply setting
        if( $field['font_size'] > 12 ) {

            // format the value
            // $value = 'something';

        }


        // return
        return $value;
    }

    */


    /*
    *  validate_value()
    *
    *  This filter is used to perform validation on the value prior to saving.
    *  All values are validated regardless of the field's required setting. This allows you to validate and return
    *  messages to the user if the value is not correct
    *
    *  @type    filter
    *  @date    11/02/2014
    *  @since   5.0.0
    *
    *  @param   $valid (boolean) validation status based on the value and the field's required setting
    *  @param   $value (mixed) the $_POST value
    *  @param   $field (array) the field array holding all the field options
    *  @param   $input (string) the corresponding input name for $_POST value
    *  @return  $valid
    */

    /*

    function validate_value( $valid, $value, $field, $input ){

        // Basic usage
        if( $value < $field['custom_minimum_setting'] )
        {
            $valid = false;
        }


        // Advanced usage
        if( $value < $field['custom_minimum_setting'] )
        {
            $valid = __('The value is too little!','acf-FIELD_NAME'),
        }


        // return
        return $valid;

    }

    */


    /*
    *  delete_value()
    *
    *  This action is fired after a value has been deleted from the db.
    *  Please note that saving a blank value is treated as an update, not a delete
    *
    *  @type    action
    *  @date    6/03/2014
    *  @since   5.0.0
    *
    *  @param   $post_id (mixed) the $post_id from which the value was deleted
    *  @param   $key (string) the $meta_key which the value was deleted
    *  @return  n/a
    */

    /*

    function delete_value( $post_id, $key ) {



    }

    */


    /*
    *  load_field()
    *
    *  This filter is applied to the $field after it is loaded from the database
    *
    *  @type    filter
    *  @date    23/01/2013
    *  @since   3.6.0
    *
    *  @param   $field (array) the field array holding all the field options
    *  @return  $field
    */

    /*

    function load_field( $field ) {

        return $field;

    }

    */


    /*
    *  update_field()
    *
    *  This filter is applied to the $field before it is saved to the database
    *
    *  @type    filter
    *  @date    23/01/2013
    *  @since   3.6.0
    *
    *  @param   $field (array) the field array holding all the field options
    *  @return  $field
    */

    /*

    function update_field( $field ) {

        return $field;

    }

    */


    /*
    *  delete_field()
    *
    *  This action is fired after a field is deleted from the database
    *
    *  @type    action
    *  @date    11/02/2014
    *  @since   5.0.0
    *
    *  @param   $field (array) the field array holding all the field options
    *  @return  n/a
    */

    /*

    function delete_field( $field ) {



    }

    */


}


// create field
new acf_field_leaflet_field();

?>