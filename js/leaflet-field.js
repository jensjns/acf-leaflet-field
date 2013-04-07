jQuery(document).ready(function($) {

    // only render the map if an api-key is present
    if( typeof leaflet_field.api_key != 'undefined' ) {
        render_leaflet_map();
    }

    function render_leaflet_map() {
        var field = $('#' + leaflet_field.id);

        var map_settings = {
            zoom_level:null,
            center:{
                lat:null,
                lng:null
            },
            markers:[],
        };

        // check if we have a saved value
        if( field.val().length > 0 ) {
            map_settings = JSON.parse(field.val());
        }

        if( typeof map_settings.center.lat == 'undefined' ) {
            map_settings.center.lat = leaflet_field.lat;
        }

        if( typeof map_settings.center.lng == 'undefined' ) {
            map_settings.center.lng = leaflet_field.lng;
        }

        // check if the zoom level is set and within 1-18
        if( typeof map_settings.zoom_level == 'undefined' || map_settings.zoom_level > 18 || map_settings.zoom_level < 1 ) {
            if( leaflet_field.zoom_level > 0 && leaflet_field.zoom_level < 19 ) {
                map_settings.zoom_level = leaflet_field.zoom_level;
            }
            else {
                map_settings.zoom_level = 13;
            }
        }

        var map = L.map('map').setView( [map_settings.center.lat, map_settings.center.lng], map_settings.zoom_level );

        L.tileLayer('http://{s}.tile.cloudmade.com/' + leaflet_field.api_key + '/997/256/{z}/{x}/{y}.png', {
            attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://cloudmade.com">CloudMade</a>',
            maxZoom: 18
        }).addTo(map);


        // render existing markers
        if( map_settings.markers.length > 0 ) {
            $.each(map_settings.markers, function(index, marker){
                L.marker(marker, {draggable: true}).addTo(map);
            });
        }

        map.on('click', function(e){
            var marker = L.marker(e.latlng, {draggable: true}).addTo(map);
            map_settings.markers.push(e.latlng);
            field.val(JSON.stringify(map_settings));
        }).on('zoomend', function(e){
            map_settings.zoom_level = map.getZoom();
            field.val(JSON.stringify(map_settings));
        }).on('dragend', function(e){
            var center = map.getCenter();
            map_settings.center.lat = center.lat;
            map_settings.center.lng = center.lng;
            field.val(JSON.stringify(map_settings));
        });
    }

});