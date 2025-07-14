@extends('layouts.app')
@php
    $isEdit = isset($business) && $business->id;
    if ($isEdit) {
        $title = 'Edit Business / Organization';
    } else {
        $title = 'Create Business / Organization';
        $business = new \App\Models\Business();
    }
    
    // Business types that should show manpower/consultancy tab
    $educationBusinessTypes = [5, 6]; // IDs for manpower and education consultancy types
    $showEducationFields = in_array($business->type_id ?? old('type_id'), $educationBusinessTypes) || 
        (isset($businessTypes) && in_array($business->type_id ?? old('type_id'), 
            $businessTypes->whereIn('title', ['Manpower', 'Education Consultancy'])->pluck('id')->toArray()
        ));
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
        
        /* Required field indicator and validation styles */
        .required::after {
            content: "*";
            color: #e53e3e;
            margin-left: 2px;
        }
        
        .validation-error {
            color: #e53e3e;
            font-size: 0.75rem;
            margin-top: 0.25rem;
            display: none;
        }
        
        .error-border {
            border-color: #e53e3e !important;
        }
        .pac-container {
            z-index: 10000 !important;
        }
        
        /* Coordinate input error styling */
        .ring-red-500 {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 1px #ef4444 !important;
        }
    </style>
@endsection
@section('content')
    @if (isset($showSettings))
        @include('modules.business.header', ['title' => __('business.settings')])
    @else
        <h1 class="text-2xl font-semibold text-gray-700 mb-2">{{ $title }}</h1>
    @endif

    <!-- Tab Navigation -->
    <div class="border-b border-gray-200 mb-6">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="businessTabs" role="tablist">
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 border-indigo-600 rounded-t-lg text-indigo-600" 
                    id="general-tab" data-tab="general" type="button" role="tab" aria-selected="true">
                    {{ __('General') }}
                </button>
            </li>
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300" 
                    id="details-tab" data-tab="details" type="button" role="tab" aria-selected="false">
                    {{ __('Details') }}
                </button>
            </li>
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300" 
                    id="address-tab" data-tab="address" type="button" role="tab" aria-selected="false">
                    {{ __('Address') }}
                </button>
            </li>
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300" 
                    id="social-media-tab" data-tab="social-media" type="button" role="tab" aria-selected="false">
                    {{ __('Social Media') }}
                </button>
            </li>
            <li class="mr-2 manpower-tab-container" role="presentation" id="manpower-consultancy-tab-container" style="{{ $showEducationFields ? '' : 'display: none;' }}">
                <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300" 
                    id="manpower-consultancy-tab" data-tab="manpower-consultancy" type="button" role="tab" aria-selected="false">
                    {{ __('Manpower/Consultancy') }}
                </button>
            </li>
        </ul>
    </div>

    @include('modules.shared.success_error')

    <!-- Tab Content -->
    <section>
        <div class="bg-white p-4 shadow rounded">
            <div class="tab-content">
                <!-- General Tab -->
                <div id="general" class="tab-pane active">
                    <form action="{{ $isEdit ? route('business.saveGeneral.update', $business) : route('business.saveGeneral.create') }}" method="POST" id="generalForm">
                        @csrf
                        <input type="hidden" name="_section" value="general">
                        @if ($isEdit)
                            @method('PUT')
                        @endif

                        @include('modules.business.partials._general', ['business' => $business])
                        
                        <div class="flex justify-between w-full mt-6">
                            <button type="submit" class="px-8 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">{{ __('Save General') }}</button>
                            <button type="submit" class="next-btn inline-block px-8 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" data-next-tab="details">{{ __('Next') }}</button>
                        </div>
                    </form>
                </div>

                <!-- Details Tab -->
                <div id="details" class="tab-pane hidden">
                    @if($isEdit)
                    <form action="{{ route('business.saveDetails', $business) }}" method="POST" id="detailsForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="_section" value="details">
                        
                        @include('modules.business.partials._details', [
                            'business' => $business,
                            'typeFacilities' => $typeFacilities ?? []
                        ])
                        
                        <div class="flex justify-between w-full mt-6">
                            <button type="button" class="prev-btn inline-block px-8 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400" data-prev-tab="general">{{ __('Previous') }}</button>
                            <button type="submit" class="px-8 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">{{ __('Save Details') }}</button>
                            <button type="submit" class="next-btn inline-block px-8 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" data-next-tab="address">{{ __('Next') }}</button>
                        </div>
                    </form>
                    @else
                    <div class="text-center py-8">
                        <p class="text-gray-600">{{ __('Please save the General information first to continue.') }}</p>
                        <button type="button" class="prev-btn mt-4 px-8 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300" data-prev-tab="general">{{ __('Back to General') }}</button>
                    </div>
                    @endif
                </div>

                <!-- Address Tab -->
                <div id="address" class="tab-pane hidden">
                    @if($isEdit)
                    <form action="{{ route('business.saveAddress', $business) }}" method="POST" id="addressForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="_section" value="address">
                        
                        @include('modules.business.partials._address', [
                            'business' => $business,
                            'countries' => $countries ?? []
                        ])
                        
                        <div class="flex justify-between w-full mt-6">
                            <button type="button" class="prev-btn inline-block px-8 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400" data-prev-tab="details">{{ __('Previous') }}</button>
                            <button type="submit" class="px-8 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">{{ __('Save Address') }}</button>
                            <button type="submit" class="next-btn inline-block px-8 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" data-next-tab="social-media">{{ __('Next') }}</button>
                        </div>
                    </form>
                    @else
                    <div class="text-center py-8">
                        <p class="text-gray-600">{{ __('Please save the General information first to continue.') }}</p>
                        <button type="button" class="prev-btn mt-4 px-8 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300" data-prev-tab="general">{{ __('Back to General') }}</button>
                    </div>
                    @endif
                </div>

                <!-- Social Media Tab -->
                <div id="social-media" class="tab-pane hidden">
                    @if($isEdit)
                    <form action="{{ route('business.saveSocialMedia', $business) }}" method="POST" id="socialMediaForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="_section" value="social_media">
                        
                        @include('modules.business.partials._social_media', [
                            'business' => $business,
                            'socialNetworks' => $socialNetworks ?? []
                        ])
                        
                        <div class="flex justify-between w-full mt-6">
                            <button type="button" class="prev-btn inline-block px-8 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400" data-prev-tab="address">{{ __('Previous') }}</button>
                            <button type="submit" class="px-8 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">{{ __('Save Social Media') }}</button>
                            @if($showEducationFields)
                                <button type="submit" class="next-btn inline-block px-8 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" data-next-tab="manpower-consultancy">{{ __('Next') }}</button>
                            @endif
                        </div>
                    </form>
                    @else
                    <div class="text-center py-8">
                        <p class="text-gray-600">{{ __('Please save the General information first to continue.') }}</p>
                        <button type="button" class="prev-btn mt-4 px-8 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300" data-prev-tab="general">{{ __('Back to General') }}</button>
                    </div>
                    @endif
                </div>

                <!-- Manpower Consultancy Tab -->
                <div id="manpower-consultancy" class="tab-pane hidden">
                    @if($isEdit && $showEducationFields)
                    <form action="{{ route('business.saveManpowerConsultancy', $business) }}" method="POST" id="manpowerConsultancyForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="_section" value="manpower_consultancy">
                        
                        @include('modules.business.partials._manpower_consultancy', [
                            'business' => $business,
                            'languages' => $languages ?? [],
                            'countries' => $countries ?? []
                        ])
                        
                        <div class="flex justify-between w-full mt-6">
                            <button type="button" class="prev-btn inline-block px-8 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400" data-prev-tab="social-media">{{ __('Previous') }}</button>
                            <button type="submit" class="px-8 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">{{ __('Save Manpower/Consultancy Info') }}</button>
                        </div>
                    </form>
                    @else
                    <div class="text-center py-8">
                        <p class="text-gray-600">{{ __('Please save the General information first and select a Manpower or Education Consultancy business type.') }}</p>
                        <button type="button" class="prev-btn mt-4 px-8 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300" data-prev-tab="social-media">{{ __('Back to Social Media') }}</button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
@push('js')
    @include('modules.shared.state_prefill', ['entity' => $business, 'countries' => $countries])
    <!-- Loading Google Maps script with async attribute -->
    <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps.api_key') }}&libraries=places&callback=initializeGoogleMaps&v=weekly"></script>

    <script>
        let map = null;
        let marker = null;
        let googleMapsApiLoaded = false;

        // This function is the callback for the Google Maps script load.
        function initializeGoogleMaps() {
            console.log('Google Maps API script has been loaded.');
            googleMapsApiLoaded = true;

            // If the address tab is already visible when the script loads, init the map.
            const addressTabPane = document.getElementById('address');
            if (addressTabPane && !addressTabPane.classList.contains('hidden')) {
                initMap();
            }
        }

        // This function contains the actual map initialization logic.
        function initMap() {
            // Prevent re-initialization
            if (map) {
                return;
            }
            console.log('Initializing map...');

            const coordinatesInput = document.getElementById('coordinates');
            let initialLat = 27.7172;
            let initialLng = 85.3240;

            // Check for pending coordinates first (manually entered before map load)
            if (window.pendingCoordinates) {
                initialLat = window.pendingCoordinates.lat;
                initialLng = window.pendingCoordinates.lng;
                delete window.pendingCoordinates;
            } else if (coordinatesInput && coordinatesInput.value) {
                const parts = coordinatesInput.value.split(',');
                if (parts.length === 2) {
                    initialLat = parseFloat(parts[0]);
                    initialLng = parseFloat(parts[1]);
                }
            }
            
            const initialPosition = { lat: initialLat, lng: initialLng };

            map = new google.maps.Map(document.getElementById('map'), {
                center: initialPosition,
                zoom: 13
            });

            marker = new google.maps.Marker({
                map: map,
                position: initialPosition,
                draggable: true
            });

            const input = document.getElementById('pac-input');
            if (!input) {
                console.error('Search input #pac-input not found!');
                return;
            }

            const searchBox = new google.maps.places.SearchBox(input);
            
            // Push the search box to the top right of the map.
            map.controls[google.maps.ControlPosition.TOP_RIGHT].push(input);

            map.addListener('bounds_changed', () => {
                searchBox.setBounds(map.getBounds());
            });

            searchBox.addListener('places_changed', () => {
                const places = searchBox.getPlaces();
                if (places.length == 0) return;
                const place = places[0];
                if (!place.geometry || !place.geometry.location) return;
                
                map.setCenter(place.geometry.location);
                map.setZoom(17);
                marker.setPosition(place.geometry.location);

                updateLocationFields(place.geometry.location.lat(), place.geometry.location.lng());
                updateAddressFields(place);
            });

            marker.addListener('dragend', (event) => {
                updateLocationFields(event.latLng.lat(), event.latLng.lng());
                reverseGeocode(event.latLng);
            });

            // Add click event to map to place marker at clicked location
            map.addListener('click', (event) => {
                const clickedLocation = event.latLng;
                
                // Move marker to clicked location
                marker.setPosition(clickedLocation);
                
                // Update coordinate fields
                updateLocationFields(clickedLocation.lat(), clickedLocation.lng());
                
                // Reverse geocode to update address fields
                reverseGeocode(clickedLocation);
            });

            updateLocationFields(initialLat, initialLng);
            
            // Add address field listeners for auto-geocoding
            addAddressListeners();
            
            // Add coordinate input listener for manual entry
            addCoordinateInputListener();
        }
        
        function updateLocationFields(lat, lng) {
            document.getElementById('location').value = `POINT(${lng} ${lat})`;
            document.getElementById('coordinates').value = `${lat},${lng}`;
        }

        function updateAddressFields(place) {
            const components = place.address_components;
            if (!components) return;

            let address = {};

            for (const component of components) {
                const type = component.types[0];
                const value = component.long_name;

                switch (type) {
                    case 'street_number':
                        address.street_number = value;
                        break;
                    case 'route':
                        address.route = value;
                        break;
                    case 'locality':
                        const cityEl = document.getElementById('city');
                        if (cityEl) cityEl.value = value;
                        break;
                    case 'administrative_area_level_1':
                        // For state, we need to find the matching state in the dropdown
                        const stateSelect = document.getElementById('state');
                        if (stateSelect) {
                            for (let i = 0; i < stateSelect.options.length; i++) {
                                if (stateSelect.options[i].text.toLowerCase().includes(value.toLowerCase())) {
                                    stateSelect.selectedIndex = i;
                                    break;
                                }
                            }
                        }
                        break;
                    case 'country':
                        const countrySelect = document.getElementById('country');
                        if (countrySelect) {
                            for (let i = 0; i < countrySelect.options.length; i++) {
                                if (countrySelect.options[i].text === value) {
                                    countrySelect.selectedIndex = i;
                                    // Trigger country change event to load states
                                    countrySelect.dispatchEvent(new Event('change'));
                                    break;
                                }
                            }
                        }
                        break;
                    case 'postal_code':
                        const postalEl = document.getElementById('postal_code');
                        if (postalEl) postalEl.value = value;
                        break;
                }
            }

            const streetAddress = `${address.street_number || ''} ${address.route || ''}`.trim();
            const addressEl = document.getElementById('address_line_1');
            if (addressEl) addressEl.value = streetAddress;
        }

        // Function to geocode address from form fields
        function geocodeAddress() {
            const country = document.getElementById('country').selectedOptions[0]?.text || '';
            const state = document.getElementById('state').selectedOptions[0]?.text || '';
            const city = document.getElementById('city').value || '';
            const address1 = document.getElementById('address_line_1').value || '';
            const address2 = document.getElementById('address_line_2').value || '';
            const postal = document.getElementById('postal_code').value || '';

            // Build address string
            const addressParts = [address1, address2, city, state, country, postal].filter(part => part.trim());
            const fullAddress = addressParts.join(', ');

            if (fullAddress.trim().length < 10) return; // Too short to geocode

            const geocoder = new google.maps.Geocoder();
            geocoder.geocode({ address: fullAddress }, (results, status) => {
                if (status === 'OK' && results[0] && map) {
                    const location = results[0].geometry.location;
                    map.setCenter(location);
                    map.setZoom(15);
                    if (marker) {
                        marker.setPosition(location);
                    }
                    updateLocationFields(location.lat(), location.lng());
                }
            });
        }

        // Add event listeners for address fields to trigger geocoding
        function addAddressListeners() {
            const addressFields = ['country', 'state', 'city', 'address_line_1', 'address_line_2', 'postal_code'];
            
            addressFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    field.addEventListener('input', debounce(geocodeAddress, 1000));
                    field.addEventListener('change', debounce(geocodeAddress, 500));
                }
            });
        }

        // Debounce function to limit API calls
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Add coordinate input listener for manual entry
        function addCoordinateInputListener() {
            const coordinatesInput = document.getElementById('coordinates');
            const errorElement = document.querySelector('[data-error-for="coordinates"]');
            
            if (!coordinatesInput) return;
            
            coordinatesInput.addEventListener('input', debounce(function(event) {
                const value = event.target.value.trim();
                
                // Clear previous errors
                if (errorElement) {
                    errorElement.style.display = 'none';
                    errorElement.textContent = '';
                }
                coordinatesInput.classList.remove('ring-red-500');
                
                if (!value) {
                    return; // Allow empty value
                }
                
                // Validate coordinate format (lat,lng)
                const coordinatePattern = /^-?([1-8]?\d(\.\d+)?|90(\.0+)?),\s*-?(180(\.0+)?|((1[0-7]\d)|([1-9]?\d))(\.\d+)?)$/;
                
                if (!coordinatePattern.test(value)) {
                    showCoordinateError('Invalid coordinate format. Please use: latitude,longitude (e.g., 27.7172,85.3240)');
                    return;
                }
                
                const parts = value.split(',');
                const lat = parseFloat(parts[0].trim());
                const lng = parseFloat(parts[1].trim());
                
                // Additional validation for coordinate ranges
                if (lat < -90 || lat > 90) {
                    showCoordinateError('Latitude must be between -90 and 90 degrees');
                    return;
                }
                
                if (lng < -180 || lng > 180) {
                    showCoordinateError('Longitude must be between -180 and 180 degrees');
                    return;
                }
                
                // Update the hidden location field immediately
                updateLocationFields(lat, lng);
                
                // Update map if it exists
                if (map && marker) {
                    const newPosition = { lat: lat, lng: lng };
                    map.setCenter(newPosition);
                    map.setZoom(15);
                    marker.setPosition(newPosition);
                    
                    // Reverse geocode to update address fields
                    reverseGeocode(new google.maps.LatLng(lat, lng));
                } else {
                    // Store coordinates for when map initializes
                    window.pendingCoordinates = { lat: lat, lng: lng };
                }
                
            }, 500));
            
            function showCoordinateError(message) {
                coordinatesInput.classList.add('ring-red-500');
                if (errorElement) {
                    errorElement.textContent = message;
                    errorElement.style.display = 'block';
                }
            }
        }

        function reverseGeocode(latLng) {
            const geocoder = new google.maps.Geocoder();
            geocoder.geocode({ location: latLng }, (results, status) => {
                if (status === 'OK' && results[0]) {
                    updateAddressFields(results[0]);
                }
            });
        }
        
        document.addEventListener('DOMContentLoaded', function () {
            const tabs = document.querySelectorAll('#businessTabs button');
            const tabPanes = document.querySelectorAll('.tab-pane');
            const prevButtons = document.querySelectorAll('.prev-btn');
            
            // Initialize coordinate input listener immediately (even before map loads)
            addCoordinateInputListener();

            function switchTab(tabId) {
                tabs.forEach(tab => {
                    const isSelected = tab.dataset.tab === tabId;
                    tab.classList.toggle('border-indigo-600', isSelected);
                    tab.classList.toggle('text-indigo-600', isSelected);
                    tab.classList.toggle('border-transparent', !isSelected);
                    tab.setAttribute('aria-selected', isSelected);
                });

                tabPanes.forEach(pane => {
                    pane.classList.toggle('hidden', pane.id !== tabId);
                });

                // Defer map initialization until the tab is visible.
                if (tabId === 'address') {
                    setTimeout(() => {
                        if (googleMapsApiLoaded && !map) {
                            initMap();
                        } else if (map) {
                            // If map was already initialized, trigger resize to fix display issues
                            google.maps.event.trigger(map, 'resize');
                            if(marker) {
                               map.setCenter(marker.getPosition());
                            }
                        }
                    }, 50); // A small delay to ensure the DOM is ready before initializing the map.
                }
            }

            tabs.forEach(tab => {
                tab.addEventListener('click', () => switchTab(tab.dataset.tab));
            });
            
            prevButtons.forEach(button => {
                button.addEventListener('click', () => switchTab(button.dataset.prevTab));
            });

            // Handle form submissions with centralized function
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function (event) {
                    event.preventDefault();
                    handleFormSubmit(this);
                });
            });
        });

        async function handleFormSubmit(form) {
            // Clear previous errors
            form.querySelectorAll('.validation-error').forEach(el => el.style.display = 'none');
            form.querySelectorAll('.error-border').forEach(el => el.classList.remove('error-border'));

            const formData = new FormData(form);
            const url = form.action;
            const method = form.method;

            try {
                const response = await fetch(url, {
                    method: method,
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    }
                });

                const data = await response.json();

                if (!response.ok) {
                    if (response.status === 422 && data.errors) {
                        // Handle validation errors
                        Object.keys(data.errors).forEach(key => {
                            const errorKey = key.replace(/\./g, '\\.').replace(/\[/g, '\\[').replace(/\]/g, '\\]');
                            const errorElement = form.querySelector(`[data-error-for="${errorKey}"]`);
                            const inputElement = form.querySelector(`[name="${key}"]`);
                            
                            if (errorElement) {
                                errorElement.textContent = data.errors[key][0];
                                errorElement.style.display = 'block';
                            }
                            if (inputElement) {
                                inputElement.classList.add('error-border');
                            }
                        });
                        // Show generic error if something unexpected happened
                        if(!data.errors) {
                            alert(data.message || 'An unexpected error occurred. Please try again.');
                        }

                    } else {
                        alert(data.message || 'An unexpected error occurred. Please try again.');
                    }
                    return; // Stop execution
                }
                
                // Show success message
                if(data.message) {
                    const successDiv = document.querySelector('#success-error-container');
                    if(successDiv) {
                        successDiv.innerHTML = `<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert"><span class="block sm:inline">${data.message}</span></div>`;
                    }
                }
                
                const nextButtonInForm = form.querySelector('.next-btn');

                if (data.redirect) {
                    window.location.href = data.redirect;
                } else if (nextButtonInForm) {
                    const nextTabId = nextButtonInForm.dataset.nextTab;
                    if (nextTabId) {
                        const nextTabNavButton = document.querySelector(`#businessTabs button[data-tab='${nextTabId}']`);
                        if (nextTabNavButton) {
                            nextTabNavButton.click();
                        }
                    }
                }

            } catch (error) {
                console.error('Submission error:', error);
                alert('An unexpected error occurred. Please check your connection and try again.');
            }
        }
    </script>
@endpush
