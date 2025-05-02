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
                            <button type="button" class="next-btn inline-block px-8 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" data-next-tab="details">{{ __('Next') }}</button>
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
                            <button type="button" class="next-btn inline-block px-8 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" data-next-tab="address">{{ __('Next') }}</button>
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
                            <button type="button" class="next-btn inline-block px-8 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" data-next-tab="social-media">{{ __('Next') }}</button>
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
                                <button type="button" class="next-btn inline-block px-8 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" data-next-tab="manpower-consultancy">{{ __('Next') }}</button>
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
        // Store functions to be called when Google Maps loads
        let mapInitialized = false;
        let map = null;
        let marker = null;
        
        function initializeGoogleMaps() {
            mapInitialized = true;
            console.log('Google Maps API loaded');
            
            // If the address tab is already visible, initialize the map immediately
            if (document.getElementById('map') && !document.getElementById('map').closest('.tab-pane').classList.contains('hidden')) {
                initMap();
            }
        }
        
        // Single DOMContentLoaded event handler to avoid conflicts
        document.addEventListener('DOMContentLoaded', function() {
            // Define the tab sequence for automatic advancement using tab IDs
            window.tabSequence = {
                'generalForm': 'details',
                'detailsForm': 'address',
                'addressForm': 'social-media',
                'socialMediaForm': 'manpower-consultancy',
                'manpowerConsultancyForm': null // No next tab after last one
            };
            
            // Initialize tab navigation
            setupTabNavigation();
            
            // Initialize Ajax form submission
            setupAjaxForms();
            
            // Initialize form validation
            setupFormValidation();
            
            // Initialize business type dependent fields
            const typeSelect = document.getElementById('type_id');
            if (typeSelect) {
                toggleEducationFields(typeSelect.value);
                toggleManpowerTab(typeSelect.value);
                
                // Handle type selection changes
                typeSelect.addEventListener('change', function() {
                    toggleEducationFields(this.value);
                    toggleManpowerTab(this.value);
                });
            }

            // First check for URL hash fragment (e.g., #details)
            if (window.location.hash) {
                const tabId = window.location.hash.substring(1); // Remove the # character
                if (document.getElementById(tabId)) {
                    // If tab exists, switch to it
                    console.log('Switching to tab from hash:', tabId);
                    switchToTab(tabId);
                    return;
                }
            }
            
            // If no hash or hash doesn't match a tab, check query parameters
            const urlParams = new URLSearchParams(window.location.search);
            const tabParam = urlParams.get('tab');
            if (tabParam && document.getElementById(tabParam)) {
                // If tab exists, switch to it
                console.log('Switching to tab from query param:', tabParam);
                switchToTab(tabParam);
            }
        });
        
        // Setup form validation function
        function setupFormValidation() {
            // This can be expanded based on your validation needs
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const requiredFields = form.querySelectorAll('[required]');
                    let hasError = false;
                    
                    requiredFields.forEach(field => {
                        if (!field.value.trim()) {
                            field.classList.add('border-red-500');
                            hasError = true;
                            
                            // Add error message if not already present
                            let errorMsg = field.nextElementSibling;
                            if (!errorMsg || !errorMsg.classList.contains('text-red-500')) {
                                errorMsg = document.createElement('p');
                                errorMsg.classList.add('text-red-500', 'text-xs', 'mt-1');
                                errorMsg.textContent = 'This field is required';
                                field.parentNode.insertBefore(errorMsg, field.nextSibling);
                            }
                        } else {
                            field.classList.remove('border-red-500');
                            
                            // Remove error message if exists
                            const errorMsg = field.nextElementSibling;
                            if (errorMsg && errorMsg.classList.contains('text-red-500')) {
                                errorMsg.remove();
                            }
                        }
                    });
                    
                    if (hasError) {
                        e.preventDefault();
                        return false;
                    }
                    
                    return true;
                });
            });
        }

        // Function to validate a form
        function validateForm(form) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            // Clear previous error messages
            form.querySelectorAll('.error-message').forEach(el => el.remove());
            form.querySelectorAll('.border-red-500').forEach(el => el.classList.remove('border-red-500'));
            
            // Check each required field
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('border-red-500');
                    isValid = false;
                    
                    // Add error message
                    const errorMsg = document.createElement('p');
                    errorMsg.classList.add('text-red-500', 'text-xs', 'mt-1', 'error-message');
                    errorMsg.textContent = 'This field is required';
                    field.parentNode.insertBefore(errorMsg, field.nextSibling);
                }
            });
            
            // Add more specific validation rules as needed
            
            return isValid;
        }

        // Tab switching function
        function switchToTab(tabId) {
            console.log('Switching to tab:', tabId);
            
            // Hide all tabs
            document.querySelectorAll('.tab-pane').forEach(tab => {
                tab.classList.add('hidden');
            });
            
            // Show the target tab
            const targetTab = document.getElementById(tabId);
            if (targetTab) {
                targetTab.classList.remove('hidden');
            } else {
                console.error('Tab not found:', tabId);
            }
            
            // Update tab buttons
            document.querySelectorAll('[data-tab]').forEach(btn => {
                btn.classList.remove('border-indigo-600', 'text-indigo-600');
                btn.classList.add('border-transparent');
                
                if (btn.dataset.tab === tabId) {
                    btn.classList.add('border-indigo-600', 'text-indigo-600');
                    btn.classList.remove('border-transparent');
                }
            });
            
            // Initialize map when switching to address tab
            if (tabId === 'address' && document.getElementById('map')) {
                setTimeout(() => {
                    if (typeof google !== 'undefined' && google.maps) {
                        console.log('Initializing map on tab switch');
                        if (!map) {
                            initMap();
                        } else {
                            // Trigger resize to fix rendering issues
                            google.maps.event.trigger(map, 'resize');
                            // Re-center the map
                            if (map.getCenter()) {
                                map.setCenter(map.getCenter());
                            }
                        }
                    } else {
                        console.log('Google Maps not loaded yet');
                    }
                }, 200); // Small delay to ensure DOM is ready
            }
            
            // Scroll to top of the form
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        
        // Setup Ajax forms
        function setupAjaxForms() {
            // Find all form elements in business form
            const forms = [
                document.getElementById('generalForm'),
                document.getElementById('detailsForm'), 
                document.getElementById('addressForm'),
                document.getElementById('socialMediaForm'),
                document.getElementById('manpowerConsultancyForm')
            ].filter(form => form !== null);
            
            // Setup Ajax submission for all forms
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    if (validateForm(form)) {
                        submitFormAjax(form, function(data) {
                            const nextTabId = form.querySelector('.next-btn')?.dataset.nextTab;
                            if (nextTabId) {
                                switchToTab(nextTabId);
                            }
                        });
                    }
                });
            });
        }
        
        function submitFormAjax(form, successCallback) {
            // Show loading indicator
            showLoading(form);
            
            // Create FormData object
            let formData = new FormData(form);
            
            // Fix the route to ensure it's prefixed correctly
            let formAction = form.action;
            
            // Use fetch to submit the form
            fetch(formAction, {
                method: form.method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Hide loading indicator
                hideLoading(form);
                
                if (!data) return; // Handle redirected responses
                
                if (data.success) {
                    // Show success message
                    showMessage('success', data.message || 'Changes saved successfully!');
                    
                    // Check if we need to redirect (for new business creation)
                    if (data.redirect) {
                        // Directly redirect to the business edit page
                        window.location.href = data.redirect;
                        return;
                    }
                    
                    // If this is the general form and we have a business ID now, update URLs
                    if (form.id === 'generalForm' && data.business_id) {
                        updateFormUrls(data.business_id);
                    }
                    
                    // Execute success callback if provided
                    if (typeof successCallback === 'function') {
                        successCallback(data);
                    }
                } else {
                    // Show error message
                    showMessage('error', data.message || 'An error occurred while saving.');
                    
                    // Display validation errors if any
                    if (data.errors) {
                        displayValidationErrors(form, data.errors);
                    }
                }
            })
            .catch(error => {
                hideLoading(form);
                showMessage('error', 'An unexpected error occurred. Please try again.');
                console.error('Error:', error);
            });
        }
        
        // Helper function to show loading indicator
        function showLoading(form) {
            // Disable submit buttons
            form.querySelectorAll('button[type="submit"]').forEach(btn => {
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
                btn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Saving...';
            });
        }
        
        // Helper function to hide loading indicator
        function hideLoading(form) {
            // Re-enable submit buttons
            form.querySelectorAll('button[type="submit"]').forEach(btn => {
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
                btn.innerHTML = 'Save';
            });
        }
        
        // Display error messages next to form fields
        function displayValidationErrors(form, errors) {
            // Clear previous error messages
            form.querySelectorAll('.error-message').forEach(el => el.remove());
            
            // Add new error messages
            for (const field in errors) {
                const inputField = form.querySelector(`[name="${field}"], [name^="${field}["]`);
                if (inputField) {
                    inputField.classList.add('border-red-500');
                    
                    // Add error message
                    const errorMsg = document.createElement('p');
                    errorMsg.classList.add('text-red-500', 'text-xs', 'mt-1', 'error-message');
                    errorMsg.textContent = errors[field][0];
                    inputField.parentNode.insertBefore(errorMsg, inputField.nextSibling);
                }
            }
        }
        
        // Show a toast message
        function showMessage(type, message) {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 px-6 py-4 rounded-md text-white ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} z-50`;
            toast.innerHTML = message;
            document.body.appendChild(toast);
            
            // Remove after 3 seconds
            setTimeout(() => {
                toast.remove();
            }, 3000);
        }
        
        // Update form URLs with a new business ID
        function updateFormUrls(businessId) {
            const detailsForm = document.getElementById('detailsForm');
            const addressForm = document.getElementById('addressForm');
            const socialMediaForm = document.getElementById('socialMediaForm');
            const manpowerConsultancyForm = document.getElementById('manpowerConsultancyForm');
            
            if (detailsForm) {
                detailsForm.action = detailsForm.action.replace('/business/new/', `/business/${businessId}/`);
            }
            
            if (addressForm) {
                addressForm.action = addressForm.action.replace('/business/new/', `/business/${businessId}/`);
            }
            
            if (socialMediaForm) {
                socialMediaForm.action = socialMediaForm.action.replace('/business/new/', `/business/${businessId}/`);
            }
            
            if (manpowerConsultancyForm) {
                manpowerConsultancyForm.action = manpowerConsultancyForm.action.replace('/business/new/', `/business/${businessId}/`);
            }
        }
        
        // Toggle education-related fields based on business type
        function toggleEducationFields(typeId) {
            const educationFields = document.getElementById('education-fields');
            if (!educationFields) return;
            
            // Assuming IDs 5 and 6 are for education-related business types
            const showEducationFields = [5, 6].includes(parseInt(typeId));
            
            educationFields.classList.toggle('hidden', !showEducationFields);
        }
        
        // Toggle manpower tab visibility based on business type
        function toggleManpowerTab(typeId) {
            const manpowerTab = document.querySelector('[data-tab="manpower-consultancy"]');
            const manpowerTabPane = document.getElementById('manpower-consultancy');
            
            if (!manpowerTab || !manpowerTabPane) return;
            
            // Assuming IDs 5 and 6 are for manpower/consultancy business types
            const showManpowerTab = [5, 6].includes(parseInt(typeId));
            
            manpowerTab.classList.toggle('hidden', !showManpowerTab);
            
            // If currently on manpower tab and it's being hidden, switch to a different tab
            if (!showManpowerTab && !manpowerTabPane.classList.contains('hidden')) {
                switchToTab('general');
            }
        }
        
        // Map initialization code
        function initMap() {
            console.log('Initializing map');
            const mapContainer = document.getElementById('map');
            if (!mapContainer) {
                console.error('Map container not found');
                return;
            }
            
            try {
                // Default to a position in Nepal if no specific location
                const defaultPosition = { lat: 27.7172, lng: 85.3240 };  // Kathmandu
                
                // Get saved position if available
                const lat = parseFloat(document.getElementById('latitude')?.value || 0);
                const lng = parseFloat(document.getElementById('longitude')?.value || 0);
                const position = (lat && lng) ? { lat, lng } : defaultPosition;
                
                console.log('Map position:', position);
                
                // Create the map
                map = new google.maps.Map(mapContainer, {
                    zoom: 15,
                    center: position,
                    mapTypeControl: true,
                    streetViewControl: true,
                    fullscreenControl: true,
                });
                
                // Create marker for the location
                marker = new google.maps.Marker({
                    position: position,
                    map: map,
                    draggable: true,
                    title: 'Business Location'
                });
                
                // Add click listener to the map
                map.addListener('click', function(event) {
                    // Update marker position
                    marker.setPosition(event.latLng);
                    
                    // Update form fields with new coordinates
                    updateLocationFields(event.latLng);
                    
                    // Reverse geocode to get address
                    const geocoder = new google.maps.Geocoder();
                    geocoder.geocode({ location: event.latLng }, function(results, status) {
                        if (status === 'OK' && results[0]) {
                            const addressInput = document.getElementById('address_search');
                            if (addressInput) {
                                addressInput.value = results[0].formatted_address;
                            }
                            
                            // Update address components
                            updateAddressComponentsFromGeocode(results[0].address_components);
                        }
                    });
                });
                
                // Initialize the places search box
                const input = document.getElementById('address_search');
                if (input) {
                    const searchBox = new google.maps.places.SearchBox(input);
                    
                    // Bias search results to current map view
                    map.addListener('bounds_changed', function() {
                        searchBox.setBounds(map.getBounds());
                    });
                    
                    // Listen for place selection
                    searchBox.addListener('places_changed', function() {
                        const places = searchBox.getPlaces();
                        if (places.length === 0) return;
                        
                        const place = places[0];
                        if (!place.geometry || !place.geometry.location) return;
                        
                        // Update marker and map
                        marker.setPosition(place.geometry.location);
                        map.panTo(place.geometry.location);
                        map.setZoom(17);
                        
                        // Update form fields
                        updateLocationFields(place.geometry.location);
                        
                        // Update address components
                        updateAddressComponentsFromGeocode(place.address_components);
                    });
                }
                
                // Update fields when marker is dragged
                marker.addListener('dragend', function() {
                    const position = marker.getPosition();
                    updateLocationFields(position);
                    
                    // Reverse geocode to update address fields
                    const geocoder = new google.maps.Geocoder();
                    geocoder.geocode({ location: position }, function(results, status) {
                        if (status === 'OK' && results[0]) {
                            const addressInput = document.getElementById('address_search');
                            if (addressInput) {
                                addressInput.value = results[0].formatted_address;
                            }
                            
                            // Update address components
                            updateAddressComponentsFromGeocode(results[0].address_components);
                        }
                    });
                });
                
                console.log('Map initialized successfully');
            } catch (error) {
                console.error('Error initializing map:', error);
            }
        }
        
        // Function to set up tab navigation
        function setupTabNavigation() {
            // Set up tab button click handlers
            document.querySelectorAll('[data-tab]').forEach(button => {
                button.addEventListener('click', function() {
                    const tabId = this.dataset.tab;
                    switchToTab(tabId);
                });
            });
            
            // Set up previous/next button handlers
            document.querySelectorAll('.prev-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const prevTabId = this.dataset.prevTab;
                    if (prevTabId) {
                        switchToTab(prevTabId);
                    }
                });
            });
            
            document.querySelectorAll('.next-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Find the parent form
                    const form = this.closest('form');
                    if (!form) return;
                    
                    // Get the next tab ID
                    const nextTabId = this.dataset.nextTab;
                    if (!nextTabId) return;
                    
                    // Validate the form
                    if (validateForm(form)) {
                        // Submit the form and move to next tab on success
                        submitFormAjax(form, function(data) {
                            // On successful save, move to the next tab
                            if (data && data.success) {
                                switchToTab(nextTabId);
                            }
                        });
                    }
                });
            });
            
            // Handle form submissions that should advance to next tab
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    const formId = this.id;
                    const nextTabId = window.tabSequence[formId];
                    
                    // Store the next tab to navigate to after successful form submission
                    if (nextTabId) {
                        // We'll handle this in the AJAX success callback
                        console.log(`Next tab after ${formId} submission: ${nextTabId}`);
                    }
                });
            });
        }
        
        // Helper to update location fields
        function updateLocationFields(position) {
            if (!position) return;
            
            const latInput = document.getElementById('latitude');
            if (latInput) latInput.value = position.lat();
            
            const lngInput = document.getElementById('longitude');
            if (lngInput) lngInput.value = position.lng();
            
            const locationInput = document.getElementById('location');
            if (locationInput) locationInput.value = `POINT(${position.lng()} ${position.lat()})`;
            
            console.log('Updated location fields:', {
                lat: position.lat(),
                lng: position.lng(),
                point: `POINT(${position.lng()} ${position.lat()})`
            });
        }
        
        // Helper to update address components from geocode results
        function updateAddressComponentsFromGeocode(addressComponents) {
            if (!addressComponents) return;
            
            let street = '', city = '', state = '', country = '', postalCode = '';
            
            for (const component of addressComponents) {
                const types = component.types;
                
                if (types.includes('street_number')) {
                    street = component.long_name + ' ' + street;
                } else if (types.includes('route')) {
                    street += component.long_name;
                } else if (types.includes('locality')) {
                    city = component.long_name;
                } else if (types.includes('administrative_area_level_1')) {
                    state = component.long_name;
                } else if (types.includes('country')) {
                    country = component.long_name;
                } else if (types.includes('postal_code')) {
                    postalCode = component.long_name;
                }
            }
            
            // Update address fields
            const streetInput = document.getElementById('street');
            if (streetInput) streetInput.value = street.trim();
            
            const cityInput = document.getElementById('city');
            if (cityInput) cityInput.value = city;
            
            const stateInput = document.getElementById('state_province');
            if (stateInput) stateInput.value = state;
            
            const postalInput = document.getElementById('postal_code');
            if (postalInput) postalInput.value = postalCode;
            
            // Try to select the country in dropdown
            const countrySelect = document.getElementById('country');
            if (countrySelect && country) {
                for (const option of countrySelect.options) {
                    if (option.text.includes(country)) {
                        option.selected = true;
                        break;
                    }
                }
            }
            
            console.log('Updated address fields from geocode:', {
                street, city, state, country, postalCode
            });
        }
    </script>
@endpush
