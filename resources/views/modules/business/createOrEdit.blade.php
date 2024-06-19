@extends('layouts.app')
@php
    $isEdit = isset($business);
    if ($isEdit) {
        $title = 'Edit Business / Organization';
        $action = route('business.update', $business);
    } else {
        $title = 'Create Business / Organization';
        $business = new \App\Models\Business();
        $action = route('business.store');
    }

@endphp
@section('css')
<style>
    #map {
        height: 500px;
        width: 100%;
    }
    .controls {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 1000;
        background-color: #fff;
        padding: 10px;
        font-size: 15px;
        border: 1px solid #ccc;
        box-shadow: 0 2px 6px rgba(0,0,0,0.3);
    }
</style>
@endsection
@section('content')
    @if (isset($showSettings))
        @include('modules.business.header', ['title' => 'Settings'])
    @else
        <h1 class="text-2xl font-semibold text-gray-700 mb-2">{{ $title }}</h1>
    @endif
    <section>
        <div class="bg-white p-4 shadow rounded">
            <form action="{{ $action }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if ($isEdit)
                    @method('PUT')
                @endif
                @include('modules.shared.success_error')
                <div class="mb-2">
                    <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Business Name</label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <input required type="text" name="name" id="name" value="{{ $business->name }}"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            placeholder="Eg. Nepalese Association of Houston">
                    </div>
                </div>
                <div id="map"></div>
    <input id="pac-input" class="controls" type="text" placeholder="Search Box">
    <input type="hidden" id="coordinates" name="coordinates">
                <div class="mb-2">
                    <label for="type_id" class="block text-sm font-medium leading-6 text-gray-900">Type</label>
                    <select required id="type_id" name="type_id"
                        class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        @foreach ($businessTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->title }}</option>
                        @endforeach
                    </select>
                </div>
                @foreach (config('app.supported_locales') as $locale)
                    <div>
                        <label for="description[{{ $locale }}]" class="block text-sm font-medium text-gray-700">
                            {{ __('description.' . $locale) }}</label>
                        <textarea rows="2" id="description[{{ $locale }}]" name="description[{{ $locale }}]" type="text"
                            autocomplete="description[{{ $locale }}]" autofocus
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ $business->getTranslation('description', $locale) }}
                            </textarea>
                    </div>
                @endforeach

                <p class="text-sm mb-2 mt-4">Business Address</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="address[country_id]" class="block text-sm font-medium text-gray-700">
                            {{ __('Country') }}</label>
                        <div class="mt-1">
                            <select id="country" name="address[country_id]" required
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}" @if ($country->id == $business->address?->country->id) selected @endif>
                                        {{ $country->name }} ({{ $country->dial_code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label for="address[state_id]" class="block text-sm font-medium text-gray-700">
                            {{ __('State') }}</label>
                        <div class="mt-1">
                            <select id="state" name="address[state_id]"
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @if (isset($business->address?->state_id))
                                    <option value="{{ $business->address?->state_id }}" selected>
                                        {{ $business->address?->state->name }}
                                    </option>
                                @endif
                            </select>
                        </div>
                    </div>

                </div>

                <div class="mb-2">
                    <label for="address[city]" class="block text-sm font-medium leading-6 text-gray-900">City</label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <input type="text" name="address[city]" id="city" required
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            placeholder="Eg. Kathmandu" value="{{ $business->address?->city }}">
                    </div>
                </div>



                <div class="mb-2">
                    <label for="address[address_line_1]" class="block text-sm font-medium leading-6 text-gray-900">Address
                        1</label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <input type="text" value="{{ $business->address?->address_line_1 }}"
                            name="address[address_line_1]" id="address_line_1"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            placeholder="Eg. Kathmandu, Nepal">
                    </div>
                </div>

                <div class="mb-2">
                    <label for="address[address_line_2]" class="block text-sm font-medium leading-6 text-gray-900">Address
                        2</label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <input type="text" name="address[address_line_2]"
                            value="{{ $business->address?->address_line_2 }}" id="address_line_2"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            placeholder="Eg. Kathmandu, Nepal">
                    </div>
                </div>


                <div class="mb-2">
                    <label for="address[postal_code]" class="block text-sm font-medium leading-6 text-gray-900">Postal
                        Code</label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <input type="text" name="address[postal_code]" value="{{ $business->address?->postal_code }}"
                            id="postal_code"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            placeholder="Eg. 1234">
                    </div>
                </div>
                <div class="mb-2">
                    <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email</label>
                    <input required type="email" name="email" id="email" value="{{ $business->email }}"
                        placeholder="Eg. abc@gmail.com"
                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                </div>
                <div>
                    <label for="phone_1" class="block text-sm font-medium text-gray-700">
                        {{ __('Phone Number') }}</label>
                    <div class="mt-1">
                        <input id="phone_1" name="phone_1" type="text" value="{{ $business->phone_1 }}" required
                            minLength="6" maxLength="15" placeholder="Eg. 9812312323"
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                </div>
                <div class="mb-2">
                    <label for="phone_2" class="block text-sm font-medium leading-6 text-gray-900">Contact Person
                        Phone</label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <input type="text" name="phone_2" id="phone_2" value="{{ $business->phone_2 }}"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            placeholder="Eg. 9812312323">
                    </div>
                </div>
                <div class="mb-2">
                    <label for="active" class="block text-sm font-medium leading-6 text-gray-900">Status</label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <select name="active" id="active"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option value="1" @if ($business->active) selected @endif>Active</option>
                            <option value="0" @if (!$business->active) selected @endif>Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="mb-2">
                    <label for="logo"
                        class="block text-sm font-medium leading-6 text-gray-900">{{ 'Logo' }}</label>
                    <input type="file" @if (!$isEdit) required @endif name="logo"
                        accept="image/*"
                        class="cursor-pointer block w-full mt-2 text-sm text-gray-600 bg-white border border-gray-200 rounded-md file:bg-gray-200 file:text-gray-700 file:text-sm file:px-4 file:border-none file:py-2  focus:border-blue-400 focus:outline-none focus:ring focus:ring-blue-300 focus:ring-opacity-40" />
                    @if ($isEdit)
                        <img src="{{ getImage($business->logo, 'business/logo/') }}" alt="logo"
                            class="w-20 h-20 mt-2">
                    @endif
                </div>
                <div class="mb-2">
                    <label for="cover_image" class="block text-sm font-medium leading-6 text-gray-900">Cover Image</label>
                    <input type="file" @if (!$isEdit) required @endif name="cover_image"
                        accept="image/*"
                        class="cursor-pointer block w-full mt-2 text-sm text-gray-600 bg-white border border-gray-200 rounded-md file:bg-gray-200 file:text-gray-700 file:text-sm file:px-4 file:border-none file:py-2  focus:border-blue-400 focus:outline-none focus:ring focus:ring-blue-300 focus:ring-opacity-40" />
                    @if ($isEdit)
                        <img src="{{ getImage($business->cover_image, 'business/cover_image/') }}" alt="logo"
                            class="w-20 h-20 mt-2">
                    @endif

                </div>
                @role('super-admin')
                    @if (isset($business->settings))
                        @foreach ($business->settings as $setting)
                            <div class="mb-2">
                                <label for="settings[{{ $setting->key }}]"
                                    class="block text
                    -sm font-medium leading-6 text-gray-900">{{ __($setting->key) }}</label>
                                <input type="text" name="settings[{{ $setting->key }}]" id="{{ $setting->key }}"
                                    value="{{ $setting->value }}"
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                    placeholder="">
                            </div>
                        @endforeach
                    @else
                        @foreach (\App\Models\Business::$SETTINGS as $setting)
                            <div class="mb-2">
                                <label for="settings[{{ $setting }}]"
                                    class="block text
                    -sm font-medium leading-6 text-gray-900">{{ __($setting) }}</label>
                                <input type="text" name="settings[{{ $setting }}]" id="{{ $setting }}"
                                    value="{{ isset($business) && isset($business->settings) && $business->settings->isNotEmpty() ? $business?->settings?->$setting : '' }}"
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                    placeholder="">
                            </div>
                        @endforeach
                    @endif
                @endrole
                <div class="flex justify-end w-full">
                    <div>
                        <button type="submit"
                            class="inline-block w-full px-8 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection

@push('js')
    @include('modules.shared.state_prefill', ['entity' => $business, 'countries' => $countries])
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCueHSgyZ3YQWATjjMY6sMjZAAUnDCuDbw&libraries=places"></script>
    
    <script>
        function initMap() {
            // Initialize the map centered at a default location
            var map = new google.maps.Map(document.getElementById('map'), {
                center: {lat: -33.8688, lng: 151.2195},
                zoom: 13
            });
    
            // Create the search box and link it to the UI element
            var input = document.getElementById('pac-input');
            var searchBox = new google.maps.places.SearchBox(input);
            map.controls[google.maps.ControlPosition.TOP_RIGHT].push(input);
    
            // Create a marker
            var marker = new google.maps.Marker({
                map: map,
                draggable: true,
            });
    
            // Listen for the event fired when the user selects a prediction from the search box
            searchBox.addListener('places_changed', function() {
                var places = searchBox.getPlaces();
    
                if (places.length == 0) {
                    return;
                }
    
                // For each place, get the icon, name and location
                var bounds = new google.maps.LatLngBounds();
                places.forEach(function(place) {
                    if (!place.geometry) {
                        console.log("Returned place contains no geometry");
                        return;
                    }
    
                    // Create a marker for each place
                    marker.setPosition(place.geometry.location);
                    document.getElementById('coordinates').value = place.geometry.location.lat() + ',' + place.geometry.location.lng();
    
                    if (place.geometry.viewport) {
                        // Only geocodes have viewport
                        bounds.union(place.geometry.viewport);
                    } else {
                        bounds.extend(place.geometry.location);
                    }
                });
                map.fitBounds(bounds);
            });
    
            // Listen for map clicks to place a marker and get coordinates
            map.addListener('click', function(e) {
                var latlng = e.latLng;
                marker.setPosition(latlng);
                document.getElementById('coordinates').value = latlng.lat() + ',' + latlng.lng();
            });
    
            // Update coordinates when dragging the marker
            marker.addListener('dragend', function(e) {
                var latlng = e.latLng;
                document.getElementById('coordinates').value = latlng.lat() + ',' + latlng.lng();
            });
        }
    
        // Initialize the map when the window loads
        google.maps.event.addDomListener(window, 'load', initMap);
    </script>
    @endpush
