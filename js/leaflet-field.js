jQuery(document).ready(function($) {

    // only render the map if an api-key is present
    if( typeof leaflet_field.api_key != 'undefined' ) {
        render_leaflet_map();
    }

    function render_leaflet_map() {
        var map_settings = {};

        var value = {
            markers:[]
        };

        var field = $('#' + leaflet_field.id);

        if( typeof leaflet_field.lat != 'undefined' ) {
            map_settings.lat = leaflet_field.lat;
        }
        else {
            map_settings.lat = 0;
        }

        if( typeof leaflet_field.lng != 'undefined' ) {
            map_settings.lng = leaflet_field.lng;
        }
        else {
            map_settings.lng = 0;
        }

        // check if the zoom level is set and within 1-18
        if( typeof leaflet_field.zoom_level != 'undefined' || leaflet_field.zoom_level > 18 || leaflet_field.zoom_level < 1 ) {
            map_settings.zoom_level = leaflet_field.zoom_level;
        }
        else {
            map_settings.zoom_level = 13;
        }


        console.log(map_settings);

        var map = L.map('map').setView( [map_settings.lat, map_settings.lng], map_settings.zoom_level );

        L.tileLayer('http://{s}.tile.cloudmade.com/' + leaflet_field.api_key + '/997/256/{z}/{x}/{y}.png', {
            attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://cloudmade.com">CloudMade</a>',
            maxZoom: 18
        }).addTo(map);

        if( field.val.length > 0 ) {
            var test = JSON.parse(field.val());
            
            $.each(test.markers, function(index, marker){
                L.marker(marker, {draggable: true}).addTo(map);
                value.markers.push(marker);
            });
        }

        map.on('click', function(e){
            var marker = L.marker(e.latlng, {draggable: true}).addTo(map);
            value.markers.push(e.latlng);

            field.val(JSON.stringify(value));
        });
    }

});