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
            height: 400px;
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
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        }
    </style>
@endsection
@section('content')
    @if (isset($showSettings))
        @include('modules.business.header', ['title' => __('business.settings')])
    @else
        <h1 class="text-2xl font-semibold text-gray-700 mb-2">{{ $title }}</h1>
    @endif
    <section>
        <div class="bg-white p-4 shadow rounded">
            <form action="{{ $action }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if ($isEdit)
                    <input type="hidden" id="business_id" name="business_id" value="{{ $business->id }}">
                    @method('PUT')
                @endif
                @include('modules.shared.success_error')
                <div class="mb-2">
                    <label for="name"
                        class="block text-sm font-medium leading-6 text-gray-900">{{ __('business.business_name') }}</label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <input required type="text" name="name" id="name" value="{{ $business->name }}"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            placeholder="Eg. Nepalese Association of Houston">
                    </div>
                </div>
                <div class="mb-2">
                    <label for="type_id"
                        class="block text-sm font-medium leading-6 text-gray-900">{{ __('business.type') }}</label>
                    <select required id="type_id" name="type_id"
                        class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        @foreach ($businessTypes as $type)
                            <option value="{{ $type->id }}" @if ($type->id == $business->type_id) selected @endif>
                                {{ $type->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="facilities-section" class="mt-4">
                    <h2 class="text-lg font-semibold text-gray-700">{{ __('business.facilities') }}</h2>
                    <div id="facilities-container">
                        @if ($isEdit)
                            @include('modules.business.components.existing_facilities', [
                                'business' => $business,
                            ])
                        @endif
                    </div>
                </div>
                @foreach (config('app.supported_locales') as $locale)
                    <div>
                        <label for="description[{{ $locale }}]"
                            class="block text-sm font-medium text-gray-700">{{ __('description.' . $locale) }}</label>
                        <textarea rows="2" id="description[{{ $locale }}]" name="description[{{ $locale }}]" type="text"

                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ trim($business->getTranslation('description', $locale)) }}</textarea>
                    </div>
                @endforeach

                <p class="text-sm mb-2 mt-4">{{ __('business.business_address') }}</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="address[country_id]" class="block text-sm font-medium text-gray-700">
                            {{ __('business.country') }}</label>
                        <div class="mt-1">
                            <select id="country" name="address[country_id]" id="address[country_id]" required
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
                            {{ __('business.region_state') }}</label>
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
                    <label for="address[city]" class="block text-sm font-medium leading-6 text-gray-900">{{ __('business.city') }}</label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <input type="text" name="address[city]" id="city" required
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            placeholder="Eg. Kathmandu" value="{{ $business->address?->city }}">
                    </div>
                </div>



                <div class="mb-2">
                    <label for="address[address_line_1]" class="block text-sm font-medium leading-6 text-gray-900">{{ __('business.address_1') }}</label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <input type="text" value="{{ $business->address?->address_line_1 }}"
                            name="address[address_line_1]" id="address_line_1"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            placeholder="Eg. Kathmandu, Nepal">
                    </div>
                </div>

                <div class="mb-2">
                    <label for="address[address_line_2]" class="block text-sm font-medium leading-6 text-gray-900">{{ __('business.address_2') }}</label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <input type="text" name="address[address_line_2]"
                            value="{{ $business->address?->address_line_2 }}" id="address_line_2"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            placeholder="Eg. Kathmandu, Nepal">
                    </div>
                </div>


                <div class="mb-2">
                    <label for="address[postal_code]"
                        class="block text-sm font-medium leading-6 text-gray-900">{{ __('business.postal_code') }}</label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <input type="text" name="address[postal_code]" value="{{ $business->address?->postal_code }}"
                            id="postal_code"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            placeholder="Eg. 1234">
                    </div>
                </div>
                <div class="mb-2">
                    <label for="coordinates"
                        class="block text-sm font-medium leading-6 text-gray-900">{{ __('business.location') }}</label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <input type="text" name="coordinates" id="coordinates"
                            value="{{ $business->address?->location ? $business->address->location->getLat().','.$business->address->location->getLng() : '' }}"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            placeholder="{{ __('business.please_select_from_map') }}" disabled>
                        <input type="hidden" name="address[location]" id='location'>
                    </div>
                    <div id="map"></div>
                    <input id="pac-input" class="controls" type="text" placeholder="{{ __('business.search_box') }}">
                </div>
                <div class="mb-2">
                    <label for="email"
                        class="block text-sm font-medium leading-6 text-gray-900">{{ __('business.email') }}</label>
                    <input required type="email" name="email" id="email" value="{{ $business->email }}"
                        placeholder="Eg. abc@gmail.com"
                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                </div>
                <div>
                    <label for="phone_1" class="block text-sm font-medium text-gray-700">
                        {{ __('business.phone_number') }}</label>
                    <div class="mt-1">
                        <input id="phone_1" name="phone_1" type="text" value="{{ $business->phone_1 }}" required
                            minLength="6" maxLength="15" placeholder={{__("Eg:9812312323")}}
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                </div>
                <div class="mb-2">
                    <label for="phone_2"
                        class="block text-sm font-medium leading-6 text-gray-900">{{ __('business.contact_person_phone') }}</label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <input type="text" name="phone_2" id="phone_2" value="{{ $business->phone_2 }}"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            placeholder={{ __('Eg:9812312323') }}>
                    </div>
                </div>

                @php
                    $educationBusinessTypes = [5, 6]; // IDs for manpower and consultancy
                    $showEducationFields = in_array($business->type_id ?? old('type_id'), $educationBusinessTypes) || 
                        (isset($businessTypes) && in_array($business->type_id ?? old('type_id'), 
                            $businessTypes->whereIn('title', ['Manpower', 'Consultancy'])->pluck('id')->toArray()
                        ));
                @endphp

                @include('modules.business.components.education_fields', [
                    'showEducationFields' => $showEducationFields,
                    'business' => $business ?? null,
                    'languages' => $languages,
                    'countries' => $countries
                ])

                <div class="mb-2">
                    <label for="established_year" class="block text-sm font-medium leading-6 text-gray-900">{{ __('business.established_year') }}</label>
                    <input type="number" name="established_year" id="established_year" 
                        value="{{ $business->established_year }}"
                        min="1900" max="{{ date('Y') }}"
                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                        placeholder="{{ __('e.g. 2010') }}">
                </div>

                <div class="mb-2">
                    <label for="custom_email_message"
                        class="block text-sm font-medium leading-6 text-gray-900">{{ __('business.custom_email_message') }}</label>
                    <textarea name="custom_email_message" id="custom_email_message" class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="{{ __('This message will be sent to the member in the email') }}">{{ $business->custom_email_message }}</textarea>
                </div>
                <div class="mb-2">
                    <label for="active"
                        class="block text-sm font-medium leading-6 text-gray-900">{{ __('business.status') }}</label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <select name="active" id="active"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option value="1" @if ($business->active) selected @endif>{{ __('business.active') }}
                            </option>
                            <option value="0" @if (!$business->active) selected @endif>{{ __('business.inactive') }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="mb-2">
                    <label for="logo"
                        class="block text-sm font-medium leading-6 text-gray-900">{{ __('business.logo') }}</label>
                    <input type="file" @if (!$isEdit) required @endif name="logo" id="logo"
                        accept="image/*"
                        class="cursor-pointer block w-full mt-2 text-sm text-gray-600 bg-white border border-gray-200 rounded-md file:bg-gray-200 file:text-gray-700 file:text-sm file:px-4 file:border-none file:py-2  focus:border-blue-400 focus:outline-none focus:ring focus:ring-blue-300 focus:ring-opacity-40" />
                    @if ($isEdit)
                        <img src="{{ getImage($business->logo, 'business/logo/') }}" alt="logo"
                            class="w-20 h-20 mt-2">
                    @endif
                </div>
                <div class="mb-2">
                    <label for="cover_image"
                        class="block text-sm font-medium leading-6 text-gray-900">{{ __('business.cover_image') }}</label>
                    <input type="file" @if (!$isEdit) required @endif name="cover_image"
                        id="cover_image" accept="image/*"
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
                            class="inline-block w-full px-8 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">{{ __('business.save') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
@push('js')
    @include('modules.business._js_load_facilities')

    @include('modules.shared.state_prefill', ['entity' => $business, 'countries' => $countries])
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps.api_key') }}&libraries=places"></script>

    <script>
        async function initMap() {
            // Default center
            var center = {
                lat: -33.8688,
                lng: 151.2195
            };

            // Get existing coordinates if any
            var coordsInput = document.getElementById('coordinates');
            if (coordsInput && coordsInput.value) {
                let [lat, lng] = coordsInput.value.split(',');
                center = {
                    lat: parseFloat(lat),
                    lng: parseFloat(lng)
                };
            }

            // Initialize map
            var map = new google.maps.Map(document.getElementById('map'), {
                center: center,
                zoom: 13
            });

            // Create the search box
            var input = document.getElementById('pac-input');
            var searchBox = new google.maps.places.SearchBox(input);
            map.controls[google.maps.ControlPosition.TOP_RIGHT].push(input);

            // Create marker
            var marker = new google.maps.Marker({
                map: map,
                draggable: true,
                position: center
            });

            // Update location function
            function updateLocation(latlng) {
                document.getElementById('coordinates').value = latlng.lat() + ',' + latlng.lng();
                document.getElementById('location').value = `POINT(${latlng.lng()} ${latlng.lat()})`;
            }

            // Search box event listener
            searchBox.addListener('places_changed', function() {
                var places = searchBox.getPlaces();
                if (places.length == 0) return;

                places.forEach(function(place) {
                    if (!place.geometry) return;

                    marker.setPosition(place.geometry.location);
                    updateLocation(place.geometry.location);
                    
                    if (place.geometry.viewport) {
                        map.fitBounds(place.geometry.viewport);
                    } else {
                        map.setCenter(place.geometry.location);
                        map.setZoom(17);
                    }
                });
            });

            // Map click event
            map.addListener('click', function(e) {
                marker.setPosition(e.latLng);
                updateLocation(e.latLng);
            });

            // Marker drag event
            marker.addListener('dragend', function(e) {
                updateLocation(e.latLng);
            });

            // Try to get user's location if no existing coordinates
            if (!coordsInput.value && navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const pos = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    map.setCenter(pos);
                    marker.setPosition(pos);
                    updateLocation(new google.maps.LatLng(pos.lat, pos.lng));
                });
            }
        }

        // Initialize the map when the window loads
        google.maps.event.addDomListener(window, 'load', initMap);
    </script>

    <script>
        // Add dynamic form handling for languages and destinations
        document.getElementById('add-language').addEventListener('click', async function() {
            const container = document.getElementById('languages-container');
            document.getElementById('no-languages-message')?.remove(); // Remove the message before adding new item
            const index = container.children.length;
            
            try {
                const response = await fetch(`/admin/business/language-row/${index}`);
                const html = await response.text();
                container.insertAdjacentHTML('beforeend', html);
                
                // Re-apply Tailwind classes to new elements
                const newRow = container.lastElementChild;
                newRow.querySelectorAll('select, input').forEach(el => {
                    el.classList.add('rounded-md', 'border-gray-300');
                });
            } catch (error) {
                console.error('Error:', error);
            }
        });

        document.getElementById('add-destination').addEventListener('click', async function() {
            const container = document.getElementById('destinations-container');
            document.getElementById('no-destinations-message')?.remove(); // Remove the message before adding new item
            const index = container.children.length;
            
            try {
                const response = await fetch(`/admin/business/destination-row/${index}`);
                const html = await response.text();
                container.insertAdjacentHTML('beforeend', html);
                
                // Re-apply Tailwind classes to new elements
                const newRow = container.lastElementChild;
                newRow.querySelectorAll('select, input').forEach(el => {
                    el.classList.add('rounded-md', 'border-gray-300');
                });
            } catch (error) {
                console.error('Error:', error);
            }
        });

        function toggleEducationFields(selectedType) {
            const educationFields = document.querySelector('.education-fields');
            const educationBusinessTypes = ['5', '6']; // Update these IDs to match your manpower/consultancy type IDs
            const showFields = educationBusinessTypes.includes(selectedType);
            educationFields.style.display = showFields ? 'block' : 'none';
        }

        // Handle type selection changes
        document.getElementById('type_id').addEventListener('change', function() {
            toggleEducationFields(this.value);
        });

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('type_id');
            if (typeSelect) {
                toggleEducationFields(typeSelect.value);
            }
        });
    </script>
@endpush
