jQuery(document).ready(function($) {

    // only render the map if an api-key is present
    if( typeof leaflet_field.api_key != 'undefined' ) {
        render_leaflet_map();
    }

    function render_leaflet_map() {
        var field = $('#' + leaflet_field.id);

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
            map_settings.center.lat = leaflet_field.lat;
        }

        if( map_settings.center.lng == null ) {
            map_settings.center.lng = leaflet_field.lng;
        }

        // check if the zoom level is set and within 1-18
        if( map_settings.zoom_level == null || map_settings.zoom_level > 18 || map_settings.zoom_level < 1 ) {
            if( leaflet_field.zoom_level > 0 && leaflet_field.zoom_level < 19 ) {
                map_settings.zoom_level = leaflet_field.zoom_level;
            }
            else {
                map_settings.zoom_level = 13;
            }
        }

        var map = L.map( leaflet_field.id + '_map', {
            center: new L.LatLng( map_settings.center.lat, map_settings.center.lng ),
            zoom: map_settings.zoom_level,
            doubleClickZoom: false
        });

        L.tileLayer('http://{s}.tile.cloudmade.com/' + leaflet_field.api_key + '/997/256/{z}/{x}/{y}.png', {
            attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://cloudmade.com">CloudMade</a>',
            maxZoom: 18
        }).addTo(map);

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
            update_field();
        }

        map.on('click', function(e){
            active_tool = $('.tools .tool.active');

            if( active_tool.hasClass('tool-marker') ) {
                // the marker-tool is currently being used
                var marker = L.marker(e.latlng, {draggable: true});
                index = add_marker( marker );
                map_settings.markers['m_' + index] = {coords:e.latlng};
                map_settings.markers['m_' + index].id = index;
            }

            update_field();
        }).on('zoomend', function(e){
            // the map was zoomed
            update_field();
        }).on('dragend', function(e){
            // the map was dragged
            update_field();
        });

        function add_marker( marker ) {
            map.addLayer(marker);

            marker.on('click', function(e) {
                active_tool = $('.tools .tool.active');

                if( active_tool.hasClass('tool-remove') ) {
                    delete map_settings.markers['m_' + e.target._leaflet_id];
                    map.removeLayer(marker);
                }
                else if( active_tool.hasClass('tool-tag') ) {
                    marker.bindPopup('Tjena');
                }

                update_field();
            }).on('dragend', function(e) {
                newLatLng = e.target.getLatLng();
                map_settings.markers['m_' + e.target._leaflet_id].coords.lat = newLatLng.lat;
                map_settings.markers['m_' + e.target._leaflet_id].coords.lng = newLatLng.lng;
                update_field();
            });

            // return the id of this marker
            return marker._leaflet_id;
        } 

        function update_field() {
            // update center and zoom-level
            var center = map.getCenter();
            map_settings.center.lat = center.lat;
            map_settings.center.lng = center.lng;
            map_settings.zoom_level = map.getZoom();
            field.val(JSON.stringify(map_settings));
        }
    }


    $(document).on('click', '.leaflet-map .tools .tool', function(e){

        if( $(this).hasClass('tool-reset') ) {
            // TODO: Clear map and the field-value
        }
        else {
            $('.leaflet-map .tools .active').removeClass('active');
            $(this).addClass('active');
        }
    });

});