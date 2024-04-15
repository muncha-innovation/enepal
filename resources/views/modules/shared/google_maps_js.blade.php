<script src="https://maps.googleapis.com/maps/api/js?key=@json(config('app.map_key'))&callback=initMap" defer></script>
<script>
    function initMap() {
        navigator.geolocation.getCurrentPosition(function(position) {
            // if user denies location access, set default
            if (!position) {
                position = {
                    coords: {
                        latitude: 40.7128,
                        longitude: -74.0060
                    }
                };
            }
            var map = new google.maps.Map(document.getElementById('map'), {
                center: {lat: position.coords.latitude, lng: position.coords.longitude},
                zoom: 8
            });

            var marker = new google.maps.Marker({
                position: {lat: position.coords.latitude, lng: position.coords.longitude},
                map: map,
                draggable: true // Enable marker dragging
            });

            marker.addListener('dragend', function(event) {
                var newPosition = event.latLng.toJSON();
                console.log(newPosition);
            });
        });
    }
</script>