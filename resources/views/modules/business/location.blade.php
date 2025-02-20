@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-6">
    <h1 class="text-2xl font-semibold text-gray-700 mb-4">Update Business Location</h1>
    
    <form action="{{ route('business.updateLocation', $business) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div id="map" class="h-96 mb-4 rounded-lg"></div>
        
        <input type="hidden" name="location" id="location">
        
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg">Update Location</button>
    </form>
</div>
@endsection

@push('js')
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&callback=initMap" async defer></script>
<script>
let map, marker;

function initMap() {
    const defaultLocation = {
        lat: {{ $business->location ? $business->location->getLat() : 27.7172 }}, 
        lng: {{ $business->location ? $business->location->getLng() : 85.3240 }}
    };

    map = new google.maps.Map(document.getElementById("map"), {
        zoom: 13,
        center: defaultLocation,
    });

    marker = new google.maps.Marker({
        position: defaultLocation,
        map: map,
        draggable: true
    });

    // Update hidden input when marker is dragged
    marker.addListener('dragend', function() {
        const position = marker.getPosition();
        document.getElementById('location').value = `POINT(${position.lng()} ${position.lat()})`;
    });

    // Initial value
    document.getElementById('location').value = `POINT(${defaultLocation.lng} ${defaultLocation.lat})`;
}
</script>
@endpush