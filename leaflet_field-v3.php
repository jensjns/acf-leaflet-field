<?php

class acf_field_leaflet_field extends acf_Field
{
	
	// vars
	var $settings, // will hold info such as dir / path
		$defaults; // will hold default field options
		
		
	/*--------------------------------------------------------------------------------------
	*
	*	Constructor
	*	- This function is called when the field class is initalized on each page.
	*	- Here you can add filters / actions and setup any other functionality for your field
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function __construct($parent)
	{
		// do not delete!
    	parent::__construct($parent);
    	
    	// set name / title
    	$this->name = 'leaflet_field';
		$this->title = __('Leaflet Field');
		$this->defaults = array(
			'lat'           => '55.606',
            'lng'           => '13.002',
            'zoom_level'    => 13,
            'height'        => 350,
            'api_key'       => ''
		);
		
		
		// settings
		// settings
		$this->settings = array(
			'path' => $this->helpers_get_path( __FILE__ ),
			'dir' => $this->helpers_get_dir( __FILE__ ),
			'version' => '1.0.0'
		);
		
   	}
   	
   	
   	/*
    *  helpers_get_path
    *
    *  @description: calculates the path (works for plugin / theme folders)
    *  @since: 3.6
    *  @created: 30/01/13
    */
    
    function helpers_get_path( $file )
    {
        return trailingslashit(dirname($file));
    }
    
    
    
    /*
    *  helpers_get_dir
    *
    *  @description: calculates the directory (works for plugin / theme folders)
    *  @since: 3.6
    *  @created: 30/01/13
    */
    
    function helpers_get_dir( $file )
    {
        $dir = trailingslashit(dirname($file));
        $count = 0;
        
        
        // sanitize for Win32 installs
        $dir = str_replace('\\' ,'/', $dir); 
        
        
        // if file is in plugins folder
        $wp_plugin_dir = str_replace('\\' ,'/', WP_PLUGIN_DIR); 
        $dir = str_replace($wp_plugin_dir, WP_PLUGIN_URL, $dir, $count);
        
        
        if( $count < 1 )
        {
	        // if file is in wp-content folder
	        $wp_content_dir = str_replace('\\' ,'/', WP_CONTENT_DIR); 
	        $dir = str_replace($wp_content_dir, WP_CONTENT_URL, $dir, $count);
        }
        
        
        if( $count < 1 )
        {
	        // if file is in ??? folder
	        $wp_dir = str_replace('\\' ,'/', ABSPATH); 
	        $dir = str_replace($wp_dir, site_url('/'), $dir);
        }
        

        return $dir;
    }

	
	/*--------------------------------------------------------------------------------------
	*
	*	create_options
	*	- this function is called from core/field_meta_box.php to create extra options
	*	for your field
	*
	*	@params
	*	- $key (int) - the $_POST obejct key required to save the options to the field
	*	- $field (array) - the field object
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function create_options($key, $field)
	{
		// defaults?
		$field = array_merge($this->defaults, $field);
		
		
		// Create Field Options HTML
		?>
			<tr class="field_option field_option_<?php echo $this->name; ?>">
                <td class="label">
                    <label><?php _e('Cloudmade API-key','acf'); ?></label>
                    <p class="description"><?php _e('Register for an API-key at ','acf'); ?><a href="http://account.cloudmade.com/register" target="_blank">CloudMade</a>.</p>
                </td>
                <td>
                    <?php
                    $this->parent->create_field(array(
                        'type'  => 'text',
                        'name'  => 'fields['.$key.'][api_key]',
                        'value' => $field['api_key']
                    ));
                    ?>
                </td>
            </tr>
            
            <tr class="field_option field_option_<?php echo $this->name; ?>">
                <td class="label">
                    <label><?php _e('Zoom level','acf'); ?></label>
                    <p class="description"><?php _e('','acf'); ?></p>
                </td>
                <td>
                    <?php
                    $this->parent->create_field(array(
                        'type'  => 'number',
                        'name'  => 'fields['.$key.'][zoom_level]',
                        'value' => $field['zoom_level']
                    ));
                    ?>
                </td>
            </tr>

            <tr class="field_option field_option_<?php echo $this->name; ?>">
                <td class="label">
                    <label><?php _e('Latitude','acf'); ?></label>
                    <p class="description"><?php _e('','acf'); ?></p>
                </td>
                <td>
                    <?php
                    $this->parent->create_field(array(
                        'type'  => 'number',
                        'name'  => 'fields['.$key.'][lat]',
                        'value' => $field['lat']
                    ));
                    ?>
                </td>
            </tr>
            
            <tr class="field_option field_option_<?php echo $this->name; ?>">
                <td class="label">
                    <label><?php _e('Longitude','acf'); ?></label>
                    <p class="description"><?php _e('','acf'); ?></p>
                </td>
                <td>
                    <?php
                    $this->parent->create_field(array(
                        'type'      => 'number',
                        'name'      => 'fields['.$key.'][lng]',
                        'value'     => $field['lng']
                    ));
                    ?>
                </td>
            </tr>

            <tr class="field_option field_option_<?php echo $this->name; ?>">
                <td class="label">
                    <label><?php _e('Height','acf'); ?></label>
                    <p class="description"><?php _e('The map needs a specified height to be rendered correctly.','acf'); ?></p>
                </td>
                <td>
                    <?php
                    $this->parent->create_field(array(
                        'type'      => 'number',
                        'name'      => 'fields['.$key.'][height]',
                        'value'     => $field['height']
                    ));
                    ?>
                </td>
            </tr>
		<?php
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	pre_save_field
	*	- this function is called when saving your acf object. Here you can manipulate the
	*	field object and it's options before it gets saved to the database.
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function pre_save_field($field)
	{
		// Note: This function can be removed if not used
		
		// do stuff with field (mostly format options data)
		
		return parent::pre_save_field($field);
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	create_field
	*	- this function is called on edit screens to produce the html for this field
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function create_field($field)
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

                        window.map_settings[uid] = null;

                        // check if we have a saved value
                        if( field.val().length > 0 ) {
                            window.map_settings[uid] = JSON.parse(field.val());
                        }
                        else {
                            window.map_settings[uid] = {
                                zoom_level:null,
                                center:{
                                    lat:null,
                                    lng:null
                                },
                                markers:{}
                            };
                        }

                        if( window.map_settings[uid].center.lat == null ) {
                            window.map_settings[uid].center.lat = field.attr('data-lat');
                        }

                        if( window.map_settings[uid].center.lng == null ) {
                            window.map_settings[uid].center.lng = field.attr('data-lng');
                        }

                        // check if the zoom level is set and within 1-18
                        if( window.map_settings[uid].zoom_level == null || window.map_settings[uid].zoom_level > 18 || window.map_settings[uid].zoom_level < 1 ) {
                            if( field.attr('data-zoom-level') > 0 && field.attr('data-zoom-level') < 19 ) {
                                window.map_settings[uid].zoom_level = field.attr('data-zoom-level');
                            }
                            else {
                                window.map_settings[uid].zoom_level = 13;
                            }
                        }

                        window.maps[uid] = L.map( "map_" + uid, {
                            center: new L.LatLng( window.map_settings[uid].center.lat, window.map_settings[uid].center.lng ),
                            zoom: window.map_settings[uid].zoom_level,
                            doubleClickZoom: false
                        });

                        L.tileLayer('http://{s}.tile.cloudmade.com/' + api_key + '/997/256/{z}/{x}/{y}.png', {
                            attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://cloudmade.com">CloudMade</a>',
                            maxZoom: 18
                        }).addTo(window.maps[uid]);

                        // render existing markers if we have any
                        if( Object.keys(window.map_settings[uid].markers).length > 0 ) {
                            var newMarkers = {};
                            $.each(window.map_settings[uid].markers, function(index, marker) {
                                var newMarker = L.marker([marker.coords.lat, marker.coords.lng], {draggable: true});
                                index = add_marker(newMarker);
                                marker.id = index;
                                newMarkers['m_' + index] = marker;
                            });

                            window.map_settings[uid].markers = newMarkers;
                            update_field(uid);
                        }

                        window.maps[uid].on('click', function(e){
                            var active_tool = $('#leaflet_field-wrapper_' + uid + ' .tools .tool.active');

                            if( active_tool.hasClass('tool-marker') ) {
                                // the marker-tool is currently being used
                                var marker = L.marker(e.latlng, {draggable: true});
                                index = add_marker( marker );
                                window.map_settings[uid].markers['m_' + index] = {coords:e.latlng};
                                window.map_settings[uid].markers['m_' + index].id = index;
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
                            window.maps[uid].stopLocate();
                            update_field(uid);
                        }).on('locationerror', function(e){
                            // users location could not be found
                            window.maps[uid].stopLocate();
                        });

                        function add_marker( marker ) {
                            window.maps[uid].addLayer(marker);

                            marker.on('click', function(e) {
                                var active_tool = $('#leaflet_field-wrapper_' + uid + ' .tools .tool.active');

                                if( active_tool.hasClass('tool-remove') ) {
                                    delete window.map_settings[uid].markers['m_' + e.target._leaflet_id];
                                    window.maps[uid].removeLayer(marker);
                                }
                                else if( active_tool.hasClass('tool-tag') ) {
                                    if( typeof window.map_settings[uid].markers['m_' + marker._leaflet_id].popup_content == 'undefined' ) {
                                        content = '';
                                    }
                                    else {
                                        content = window.map_settings[uid].markers['m_' + marker._leaflet_id].popup_content;
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
                                window.map_settings[uid].markers['m_' + e.target._leaflet_id].coords.lat = newLatLng.lat;
                                window.map_settings[uid].markers['m_' + e.target._leaflet_id].coords.lng = newLatLng.lng;
                                update_field(uid);
                            });

                            // return the id of this marker
                            return marker._leaflet_id;
                        } 

                        function update_field(uid) {
                            // update center and zoom-level
                            var center = window.maps[uid].getCenter();
                            window.map_settings[uid].center.lat = center.lat;
                            window.map_settings[uid].center.lng = center.lng;
                            window.map_settings[uid].zoom_level = window.maps[uid].getZoom();
                            var field = $('#field_' + uid);
                            field.val(JSON.stringify(window.map_settings[uid]));
                        }

                        $(document).on('keyup', '.leaflet-map .acf-leaflet-field-popup-textarea', function(e){

                            var uid = $(this).parents('.leaflet-map').attr('data-uid');
                            var textarea = $(this);
                            var marker_id = 'm_' + textarea.data('marker-id');
                            window.map_settings[uid].markers[marker_id].popup_content = textarea.val();

                            if( textarea.val().length == 0 ) {
                                delete window.map_settings[uid].markers[marker_id].popup_content;
                            }

                            update_field(uid);
                        });
                    }

                    $(document).on('click', '.leaflet-map .tools .tool', function(e){
                        var uid = $(this).parents('.leaflet-map').attr('data-uid');

                        if( $(this).hasClass('tool-reset') ) {
                            // TODO: Clear map and the field-value
                        }
                        else if( $(this).hasClass('tool-compass') ) {
                            // try to locate the user
                            window.maps[uid].locate();
                        }
                        else {
                            $('#leaflet_field-wrapper_' + uid + ' .leaflet-map .tools .active').removeClass('active');
                            $(this).addClass('active');
                        }
                    });
                };

                jQuery(document).on('acf/setup_fields', function(e, postbox){
                    if( typeof window.maps == 'undefined' ) {
                        window.maps = {};
                    }

                    if( typeof window.map_settings == 'undefined' ) {
                        window.map_settings = {};
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
            <div id="leaflet_field-wrapper_<?php echo $uid; ?>">
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
            </div>
		<?php
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	admin_head
	*	- this function is called in the admin_head of the edit screen where your field
	*	is created. Use this function to create css and javascript to assist your 
	*	create_field() function.
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function admin_head()
	{
		// Note: This function can be removed if not used
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	admin_print_scripts / admin_print_styles
	*	- this function is called in the admin_print_scripts / admin_print_styles where 
	*	your field is created. Use this function to register css and javascript to assist 
	*	your create_field() function.
	*
	*	@author Elliot Condon
	*	@since 3.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function admin_print_scripts()
	{
		// scripts
        wp_enqueue_script( 'jquery' );
        wp_register_script( 'leaflet', plugins_url( '/js/leaflet/leaflet.js', __FILE__ ), array(), '0.5.1', true );
        wp_enqueue_script( 'leaflet' );
	}
	
	function admin_print_styles()
	{
		// styles
        wp_enqueue_style( 'leaflet', plugins_url( '/js/leaflet/leaflet.css', __FILE__ ), array(), '0.5.1', 'all' );
        wp_enqueue_style( 'leaflet-ie', plugins_url( '/js/leaflet/leaflet.ie.css', __FILE__ ), array( 'leaflet' ), '0.5.1' );
        $GLOBALS['wp_styles']->add_data( 'leaflet-ie', 'conditional', 'lte IE 8' );
        wp_enqueue_style( 'icomoon', plugins_url( '/css/icomoon/style.css', __FILE__ ), array(), '1.0.0', 'all' );
        wp_enqueue_style( 'leaflet-field', plugins_url( '/css/input.css', __FILE__ ), array( 'leaflet', 'icomoon' ), '1', 'all' );
	}

	
	/*--------------------------------------------------------------------------------------
	*
	*	update_value
	*	- this function is called when saving a post object that your field is assigned to.
	*	the function will pass through the 3 parameters for you to use.
	*
	*	@params
	*	- $post_id (int) - usefull if you need to save extra data or manipulate the current
	*	post object
	*	- $field (array) - usefull if you need to manipulate the $value based on a field option
	*	- $value (mixed) - the new value of your field.
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function update_value($post_id, $field, $value)
	{
		// Note: This function can be removed if not used
		
		// do stuff with value
		
		// save value
		parent::update_value($post_id, $field, $value);
	}
	
	
	
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	get_value
	*	- called from the edit page to get the value of your field. This function is useful
	*	if your field needs to collect extra data for your create_field() function.
	*
	*	@params
	*	- $post_id (int) - the post ID which your value is attached to
	*	- $field (array) - the field object.
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function get_value($post_id, $field)
	{
		// Note: This function can be removed if not used
		
		// get value
		$value = parent::get_value($post_id, $field);
		
		// format value
		
		// return value
		return $value;		
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	get_value_for_api
	*	- called from your template file when using the API functions (get_field, etc). 
	*	This function is useful if your field needs to format the returned value
	*
	*	@params
	*	- $post_id (int) - the post ID which your value is attached to
	*	- $field (array) - the field object.
	*
	*	@author Elliot Condon
	*	@since 3.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function get_value_for_api($post_id, $field)
	{
		// Note: This function can be removed if not used
		
		// get value
		$value = $this->get_value($post_id, $field);
		
		// format value
		$value = json_decode($value);

		// return value
		return $value;

	}
	
}

?>