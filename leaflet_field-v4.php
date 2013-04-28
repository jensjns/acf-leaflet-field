<?php

class acf_field_leaflet_field extends acf_field
{
	// vars
	var $settings, // will hold info such as dir / path
		$defaults; // will hold default field options
		
		
	/*
	*  __construct
	*
	*  Set name / label needed for actions / filters
	*
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function __construct()
	{
		// vars
		$this->name = 'leaflet_field';
		$this->label = __('Leaflet Field');
		$this->category = __("Content",'acf'); // Basic, Content, Choice, etc
		$this->defaults = array(
			'lat'           => '55.606',
            'lng'           => '13.002',
            'zoom_level'    => 13,
            'height'        => 350,
            'api_key'       => ''
		);
		
		
		// do not delete!
    	parent::__construct();
    	
    	
    	// settings
		$this->settings = array(
			'path' => apply_filters('acf/helpers/get_path', __FILE__),
			'dir' => apply_filters('acf/helpers/get_dir', __FILE__),
			'version' => '1.0.0'
		);

	}
	
	
	/*
	*  create_options()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field	- an array holding all the field's data
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
	*  @param	$field - an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function create_field( $field )
	{
		// defaults
        $field = array_merge($this->defaults, $field);

        // Build an unique id based on ACF's one.
        $pattern = array('/\[/', '/\]/');
        $replace = array('_', '');
        $uid = preg_replace($pattern, $replace, $field['name']);
        //error_log( $field['name'] );
        //error_log($uid);
        $field['id'] = $uid;

        ?>
            <script type="text/javascript">
                var leaflet_init = function(uid, $) {
                    // only render the map if an api-key is present
                    var api_key = <?php echo '"'.$field['api_key'].'"'; ?>;

                    if( api_key.length > 0 ) {
                        render_leaflet_map(uid);
                    }

                    function render_leaflet_map(uid) {
                        // Get the hidden input-field
                        var field = $('#field_' + uid);

                        var map_settings = null;

                        // check if we have a saved value
                        if( field.val().length > 0 ) {
                            map_settings = JSON.parse(field.val());
                        }
                        else {
                            map_settings = {
                                zoom_level:null,
                                center:{
                                    lat:null,
                                    lng:null
                                },
                                markers:{}
                            };
                        }

                        if( map_settings.center.lat == null ) {
                            map_settings.center.lat = field.attr('data-lat');
                        }

                        if( map_settings.center.lng == null ) {
                            map_settings.center.lng = field.attr('data-lng');
                        }

                        // check if the zoom level is set and within 1-18
                        if( map_settings.zoom_level == null || map_settings.zoom_level > 18 || map_settings.zoom_level < 1 ) {
                            if( field.attr('data-zoom-level') > 0 && field.attr('data-zoom-level') < 19 ) {
                                map_settings.zoom_level = field.attr('data-zoom-level');
                            }
                            else {
                                map_settings.zoom_level = 13;
                            }
                        }

                        window.maps[uid] = L.map( "map_" + uid, {
                            center: new L.LatLng( map_settings.center.lat, map_settings.center.lng ),
                            zoom: map_settings.zoom_level,
                            doubleClickZoom: false
                        });

                        L.tileLayer('http://{s}.tile.cloudmade.com/' + api_key + '/997/256/{z}/{x}/{y}.png', {
                            attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://cloudmade.com">CloudMade</a>',
                            maxZoom: 18
                        }).addTo(window.maps[uid]);

                        // render existing markers if we have any
                        if( Object.keys(map_settings.markers).length > 0 ) {
                            var newMarkers = {};
                            $.each(map_settings.markers, function(index, marker) {
                                var newMarker = L.marker([marker.coords.lat, marker.coords.lng], {draggable: true});
                                index = add_marker(newMarker);
                                marker.id = index;
                                newMarkers['m_' + index] = marker;
                            });

                            map_settings.markers = newMarkers;
                            update_field(uid);
                        }

                        window.maps[uid].on('click', function(e){
                            active_tool = $('.tools .tool.active');

                            if( active_tool.hasClass('tool-marker') ) {
                                // the marker-tool is currently being used
                                var marker = L.marker(e.latlng, {draggable: true});
                                index = add_marker( marker );
                                map_settings.markers['m_' + index] = {coords:e.latlng};
                                map_settings.markers['m_' + index].id = index;
                            }

                            update_field(uid);
                        }).on('zoomend', function(e){
                            // the map was zoomed, update field
                            update_field(uid);
                        }).on('dragend', function(e){
                            // the map was dragged, update field
                            update_field(uid);
                        }).on('locationfound', function(e){
                            // users location was found, pan to the location and update field
                            window.maps[uid].panTo(e.latlng);
                            update_field(uid);
                        }).on('locationerror', function(e){
                            // users location could not be found
                        });

                        function add_marker( marker ) {
                            window.maps[uid].addLayer(marker);

                            marker.on('click', function(e) {
                                active_tool = $('.tools .tool.active');

                                if( active_tool.hasClass('tool-remove') ) {
                                    delete map_settings.markers['m_' + e.target._leaflet_id];
                                    window.maps[uid].removeLayer(marker);
                                }
                                else if( active_tool.hasClass('tool-tag') ) {
                                    if( typeof map_settings.markers['m_' + marker._leaflet_id].popup_content == 'undefined' ) {
                                        content = '';
                                    }
                                    else {
                                        content = map_settings.markers['m_' + marker._leaflet_id].popup_content;
                                    }

                                    popup_html = '<textarea class="acf-leaflet-field-popup-textarea" data-marker-id="' + marker._leaflet_id + '" style="width:200px;height:120px;min-height:0;">' + content + '</textarea>';

                                    if( typeof marker._popup == 'undefined' ) {
                                        // bind a popup to the marker
                                        marker.bindPopup(popup_html, {maxWidth:300, maxHeight:200}).openPopup();
                                    }
                                    else {
                                        // open this markers popup
                                        marker._popup.setContent(popup_html);
                                        marker.openPopup();
                                    }
                                }

                                update_field(uid);
                            }).on('dragend', function(e) {
                                newLatLng = e.target.getLatLng();
                                map_settings.markers['m_' + e.target._leaflet_id].coords.lat = newLatLng.lat;
                                map_settings.markers['m_' + e.target._leaflet_id].coords.lng = newLatLng.lng;
                                update_field(uid);
                            });

                            // return the id of this marker
                            return marker._leaflet_id;
                        } 

                        function update_field(uid) {
                            // update center and zoom-level
                            var center = window.maps[uid].getCenter();
                            map_settings.center.lat = center.lat;
                            map_settings.center.lng = center.lng;
                            map_settings.zoom_level = window.maps[uid].getZoom();
                            field.val(JSON.stringify(map_settings));
                        }

                        $(document).on('keyup', '.leaflet-map .acf-leaflet-field-popup-textarea', function(e){
                            var textarea = $(this);
                            var marker_id = 'm_' + textarea.data('marker-id')
                            map_settings.markers[marker_id].popup_content = textarea.val();

                            if( textarea.val().length == 0 ) {
                                delete map_settings.markers[marker_id].popup_content;
                            }
                            update_field(uid);
                        });
                    }

                    $(document).on('click', '.leaflet-map .tools .tool', function(e){

                        if( $(this).hasClass('tool-reset') ) {
                            // TODO: Clear map and the field-value
                        }
                        else if( $(this).hasClass('tool-compass') ) {
                            // try to locate the user
                            var uid = $(this).parents('.leaflet-map').attr('data-uid');
                            window.maps[uid].locate();
                        }
                        else {
                            $('.leaflet-map .tools .active').removeClass('active');
                            $(this).addClass('active');
                        }
                    });
                };

                jQuery(document).on('acf/setup_fields', function(e, postbox){
                    if( typeof window.maps == 'undefined' ) {
                        window.maps = {};
                    }
        
                    jQuery(postbox).find('.leaflet-map').each(function(){
                        uid = jQuery(this).attr('data-uid');
                        if( typeof window.maps[uid] == 'undefined' ) {
                            window.maps[uid] = null;
                            leaflet_init(uid, jQuery);
                        }
                    });
                });
            </script>
            <input type="hidden" value='<?php echo $field['value']; ?>' id="field_<?php echo $uid; ?>" name="<?php echo $field['name']; ?>" data-zoom-level="<?php echo $field['zoom_level']; ?>" data-lat="<?php echo $field['lat']; ?>" data-lng="<?php echo $field['lng']; ?>" />
            <div class="leaflet-map" data-uid="<?php echo $uid; ?>">
                <ul class="tools">
                    <li class="tool tool-compass icon-compass"></li>
                    <li class="tool tool-marker icon-location active"></li>
                    <li class="tool tool-tag icon-comment-alt2-fill"></li>
                    <!--<li class="tool tool-path icon-share"></li>-->
                    <li class="tool tool-remove icon-cancel-circle red"></li>
                    <!--<li class="tool tool-reset icon-reload right red"></li>-->
                </ul>
                <div id="map_<?php echo $uid; ?>" style="height:<?php echo $field['height']; ?>px;"></div>
            </div>
		<?php
	}
	
	
	/*
	*  input_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
	*  Use this action to add css + javascript to assist your create_field() action.
	*
	*  $info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function input_admin_enqueue_scripts()
	{
		// styles
        wp_enqueue_style( 'leaflet', plugins_url( '/js/leaflet/leaflet.css', __FILE__ ), array(), '0.5.1', 'all' );
        wp_enqueue_style( 'leaflet-ie', plugins_url( '/js/leaflet/leaflet.ie.css', __FILE__ ), array( 'leaflet' ), '0.5.1' );
        $GLOBALS['wp_styles']->add_data( 'leaflet-ie', 'conditional', 'lte IE 8' );
        wp_enqueue_style( 'icomoon', plugins_url( '/css/icomoon/style.css', __FILE__ ), array(), '1.0.0', 'all' );
        wp_enqueue_style( 'leaflet-field', plugins_url( '/css/input.css', __FILE__ ), array( 'leaflet', 'icomoon' ), '1', 'all' );

        // scripts
        wp_enqueue_script( 'jquery' );
        wp_register_script( 'leaflet', plugins_url( '/js/leaflet/leaflet.js', __FILE__ ), array(), '0.5.1', true );
        //wp_register_script( 'leaflet-field', plugins_url( '/js/leaflet-field.js', __FILE__ ), array( 'jquery', 'leaflet' ), '1', true );
        wp_enqueue_script( 'leaflet' );
        //wp_enqueue_script( 'leaflet-field' );
	}
	
	
	/*
	*  input_admin_head()
	*
	*  This action is called in the admin_head action on the edit screen where your field is created.
	*  Use this action to add css and javascript to assist your create_field() action.
	*
	*  @info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_head
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function input_admin_head()
	{
		// Note: This function can be removed if not used
	}
	
	
	/*
	*  field_group_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is edited.
	*  Use this action to add css + javascript to assist your create_field_options() action.
	*
	*  $info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function field_group_admin_enqueue_scripts()
	{
		// Note: This function can be removed if not used
	}

	
	/*
	*  field_group_admin_head()
	*
	*  This action is called in the admin_head action on the edit screen where your field is edited.
	*  Use this action to add css and javascript to assist your create_field_options() action.
	*
	*  @info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_head
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function field_group_admin_head()
	{
		// Note: This function can be removed if not used
	}


	/*
	*  load_value()
	*
	*  This filter is appied to the $value after it is loaded from the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value - the value found in the database
	*  @param	$post_id - the $post_id from which the value was loaded from
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$value - the value to be saved in te database
	*/
	
	function load_value( $value, $post_id, $field )
	{
		// Note: This function can be removed if not used
		return $value;
	}
	
	
	/*
	*  update_value()
	*
	*  This filter is appied to the $value before it is updated in the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value - the value which will be saved in the database
	*  @param	$post_id - the $post_id of which the value will be saved
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$value - the modified value
	*/
	
	function update_value( $value, $post_id, $field )
	{
		// Note: This function can be removed if not used
		return $value;
	}
	
	
	/*
	*  format_value()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is passed to the create_field action
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value	- the value which was loaded from the database
	*  @param	$post_id - the $post_id from which the value was loaded
	*  @param	$field	- the field array holding all the field options
	*
	*  @return	$value	- the modified value
	*/
	
	function format_value( $value, $post_id, $field )
	{
		// defaults?
		/*
		$field = array_merge($this->defaults, $field);
		*/
		
		// perhaps use $field['preview_size'] to alter the $value?
		
		
		// Note: This function can be removed if not used
		return $value;
	}
	
	
	/*
	*  format_value_for_api()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is passed back to the api functions such as the_field
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value	- the value which was loaded from the database
	*  @param	$post_id - the $post_id from which the value was loaded
	*  @param	$field	- the field array holding all the field options
	*
	*  @return	$value	- the modified value
	*/
	
	function format_value_for_api( $value, $post_id, $field )
	{
		// defaults?
		$field = array_merge($this->defaults, $field);
		
		// perhaps use $field['preview_size'] to alter the $value?
		
		
		// Note: This function can be removed if not used
		return $value;
	}
	
	
	/*
	*  load_field()
	*
	*  This filter is appied to the $field after it is loaded from the database
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$field - the field array holding all the field options
	*/
	
	function load_field( $field )
	{
		// Note: This function can be removed if not used
		return $field;
	}
	
	
	/*
	*  update_field()
	*
	*  This filter is appied to the $field before it is saved to the database
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field - the field array holding all the field options
	*  @param	$post_id - the field group ID (post_type = acf)
	*
	*  @return	$field - the modified field
	*/

	function update_field( $field, $post_id )
	{
		// Note: This function can be removed if not used
		return $field;
	}

	
}


// create field
new acf_field_leaflet_field();

?>