<script src="https://maps.googleapis.com/maps/api/js?key={{ config('app.map_key') }}&libraries=places,drawing"></script>
<script>
let map;
let drawingManager;
let markers = [];
let circles = {};

function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        center: { lat: 27.7172, lng: 85.3240 },
        zoom: 8
    });

    // Add search box
    const input = document.createElement('input');
    input.className = 'map-search-box';
    input.setAttribute('type', 'text');
    input.setAttribute('placeholder', 'Search location...');
    input.style.cssText = 'margin: 10px; padding: 8px; border: 1px solid #ccc; border-radius: 4px; width: 250px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);';
    
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
    const searchBox = new google.maps.places.SearchBox(input);

    // Bias the SearchBox results towards current map's viewport
    map.addListener('bounds_changed', function() {
        searchBox.setBounds(map.getBounds());
    });

    // Listen for the event fired when the user selects a prediction
    searchBox.addListener('places_changed', function() {
        const places = searchBox.getPlaces();

        if (places.length == 0) {
            return;
        }

        // For each place, get the location
        const bounds = new google.maps.LatLngBounds();
        places.forEach(function(place) {
            if (!place.geometry || !place.geometry.location) {
                console.log("Returned place contains no geometry");
                return;
            }

            if (place.geometry.viewport) {
                bounds.union(place.geometry.viewport);
            } else {
                bounds.extend(place.geometry.location);
            }
        });
        map.fitBounds(bounds);
    });

    drawingManager = new google.maps.drawing.DrawingManager({
        drawingMode: google.maps.drawing.OverlayType.CIRCLE,
        drawingControl: true,
        drawingControlOptions: {
            position: google.maps.ControlPosition.TOP_CENTER,
            drawingModes: ['circle']
        },
        circleOptions: {
            fillColor: '#FF0000',
            fillOpacity: 0.2,
            strokeWeight: 1,
            editable: true,
            draggable: true
        }
    });
    drawingManager.setMap(map);

    google.maps.event.addListener(drawingManager, 'circlecomplete', function(circle) {
        const center = circle.getCenter();
        const radius = circle.getRadius() / 1000; // Convert to kilometers
        
        const locationName = prompt('Enter location name:');
        if (locationName) {
            const locationId = Date.now();
            addLocationToForm(locationName, center.lat(), center.lng(), radius, locationId);
            circles[locationId] = circle;

            google.maps.event.addListener(circle, 'radius_changed', function() {
                updateLocationInForm(locationId, circle);
            });

            google.maps.event.addListener(circle, 'center_changed', function() {
                updateLocationInForm(locationId, circle);
            });
        } else {
            circle.setMap(null);
        }
    });

    loadExistingLocations();
}

function updateLocationInForm(locationId, circle) {
    const container = document.querySelector(`[data-location-id="${locationId}"]`);
    if (container) {
        const center = circle.getCenter();
        const radius = circle.getRadius() / 1000; // Convert to kilometers
        
        container.querySelector('[name*="[latitude]"]').value = center.lat();
        container.querySelector('[name*="[longitude]"]').value = center.lng();
        container.querySelector('[name*="[radius]"]').value = radius;
    }
}

function addLocationToForm(name, lat, lng, radius, locationId) {
    const html = `
        <div class="flex items-center gap-2 bg-gray-50 p-2 rounded" data-location-id="${locationId}">
            <input type="hidden" name="locations[${locationId}][name]" value="${name}">
            <input type="hidden" name="locations[${locationId}][latitude]" value="${lat}">
            <input type="hidden" name="locations[${locationId}][longitude]" value="${lng}">
            <input type="hidden" name="locations[${locationId}][radius]" value="${radius}">
            <span class="flex-1">${name}</span>
            <button type="button" onclick="removeLocation(this, ${locationId})" class="text-red-600 hover:text-red-800">Remove</button>
        </div>
    `;
    document.getElementById('location-tags').insertAdjacentHTML('beforeend', html);
}

function removeLocation(button, locationId) {
    if (circles[locationId]) {
        circles[locationId].setMap(null);
        delete circles[locationId];
    }
    button.closest('[data-location-id]').remove();
}

function loadExistingLocations() {
    document.querySelectorAll('[data-location-id]').forEach(elem => {
        const locationId = elem.dataset.locationId;
        const lat = parseFloat(elem.querySelector('[name*="[latitude]"]').value);
        const lng = parseFloat(elem.querySelector('[name*="[longitude]"]').value);
        const radius = parseFloat(elem.querySelector('[name*="[radius]"]').value);
        
        const circle = new google.maps.Circle({
            map: map,
            center: { lat, lng },
            radius: radius * 1000, // Convert km to meters
            fillColor: '#FF0000',
            fillOpacity: 0.2,
            strokeWeight: 1,
            editable: true,
            draggable: true
        });
        
        circles[locationId] = circle;

        google.maps.event.addListener(circle, 'radius_changed', function() {
            updateLocationInForm(locationId, circle);
        });

        google.maps.event.addListener(circle, 'center_changed', function() {
            updateLocationInForm(locationId, circle);
        });
    });
}

google.maps.event.addDomListener(window, 'load', initMap);
</script> 