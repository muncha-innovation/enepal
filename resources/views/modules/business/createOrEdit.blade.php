@extends('layouts.app')
@php
    $isEdit = isset($business) && $business->id;
    if ($isEdit) {
        $title = 'Edit Business / Organization';
    } else {
        $title = 'Create Business / Organization';
        $business = new \App\Models\Business();
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
                    id="contact-tab" data-tab="contact" type="button" role="tab" aria-selected="false">
                    {{ __('Contact') }}
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

                        <div class="mb-2">
                            <label for="name" class="block text-sm font-medium leading-6 text-gray-900 required">{{ __('business.business_name') }}</label>
                            <div class="mt-2 rounded-md shadow-sm">
                                <input required type="text" name="name" id="name" value="{{ $business->name }}"
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                    placeholder="Eg. Nepalese Association of Houston">
                                <div class="validation-error" id="name-error">{{ __('Business name is required') }}</div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label for="type_id" class="block text-sm font-medium leading-6 text-gray-900 required">{{ __('business.type') }}</label>
                            <select required id="type_id" name="type_id"
                                class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                @foreach ($businessTypes as $type)
                                    <option value="{{ $type->id }}" @if ($type->id == $business->type_id) selected @endif>
                                        {{ $type->title }}</option>
                                @endforeach
                            </select>
                            <div class="validation-error" id="type_id-error">{{ __('Business type is required') }}</div>
                        </div>
                        @foreach (config('app.supported_locales') as $locale)
                            <div>
                                <label for="description[{{ $locale }}]"
                                    class="block text-sm font-medium text-gray-700">{{ __('description.' . $locale) }}</label>
                                <textarea rows="2" id="description[{{ $locale }}]" name="description[{{ $locale }}]" type="text"
                                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ trim($business->getTranslation('description', $locale)) }}</textarea>
                            </div>
                        @endforeach
                        <div class="flex justify-between w-full mt-6">
                            <button type="submit" class="px-8 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">{{ __('Save General') }}</button>
                            <button type="button" class="next-btn inline-block px-8 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" data-next-tab="details">{{ __('Next') }}</button>
                        </div>
                    </form>
                </div>

                <!-- Details Tab -->
                <div id="details" class="tab-pane hidden">
                    @if($isEdit)
                    <form action="{{ route('business.saveDetails', $business) }}" method="POST" enctype="multipart/form-data" id="detailsForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="_section" value="details">
                        
                        <div class="mb-4">
                            <label for="logo" class="block text-sm font-medium leading-6 text-gray-900 {{ !$business->logo ? 'required' : '' }}">{{ __('business.logo') }}</label>
                            <input type="file" {{ !$business->logo ? 'required' : '' }} name="logo" id="logo"
                                accept="image/*"
                                class="cursor-pointer block w-full mt-2 text-sm text-gray-600 bg-white border border-gray-200 rounded-md file:bg-gray-200 file:text-gray-700 file:text-sm file:px-4 file:border-none file:py-2 focus:border-blue-400 focus:outline-none focus:ring focus:ring-blue-300 focus:ring-opacity-40" />
                            <div class="validation-error" id="logo-error">{{ __('Logo image is required') }}</div>
                            @if ($business->logo)
                                <img src="{{ getImage($business->logo, 'business/logo/') }}" alt="logo"
                                    class="w-20 h-20 mt-2">
                            @endif
                        </div>
                        <div class="mb-4">
                            <label for="cover_image" class="block text-sm font-medium leading-6 text-gray-900 {{ !$business->cover_image ? 'required' : '' }}">{{ __('business.cover_image') }}</label>
                            <input type="file" {{ !$business->cover_image ? 'required' : '' }} name="cover_image"
                                id="cover_image" accept="image/*"
                                class="cursor-pointer block w-full mt-2 text-sm text-gray-600 bg-white border border-gray-200 rounded-md file:bg-gray-200 file:text-gray-700 file:text-sm file:px-4 file:border-none file:py-2 focus:border-blue-400 focus:outline-none focus:ring focus:ring-blue-300 focus:ring-opacity-40" />
                            <div class="validation-error" id="cover_image-error">{{ __('Cover image is required') }}</div>
                            @if ($business->cover_image)
                                <img src="{{ getImage($business->cover_image, 'business/cover_image/') }}" alt="cover_image"
                                    class="w-20 h-20 mt-2">
                            @endif
                        </div>
                        <div id="facilities-section" class="mt-4">
                            <h2 class="text-lg font-semibold text-gray-700">{{ __('business.facilities') }}</h2>
                            <div id="facilities-container">
                                @include('modules.business.components.type_facilities', [
                                    'business' => $business,
                                    'typeFacilities' => $typeFacilities
                                ])
                            </div>
                        </div>
                        @include('modules.business.components.opening_closing', ['business' => $business])
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
                        
                        <p class="text-sm mb-2 mt-4">{{ __('business.business_address') }}</p>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="country" class="block text-sm font-medium text-gray-700 required">
                                    {{ __('business.country') }}</label>
                                <div class="mt-1">
                                    <select id="country" name="address[country_id]" required
                                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id }}" @if ($country->id == $business->address?->country_id) selected @endif>
                                                {{ $country->name }} ({{ $country->dial_code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="validation-error" id="country-error">{{ __('Country is required') }}</div>
                                </div>
                            </div>
                            <div>
                                <label for="state" class="block text-sm font-medium text-gray-700">
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
                            <label for="city" class="block text-sm font-medium leading-6 text-gray-900 required">{{ __('business.city') }}</label>
                            <div class="mt-2 rounded-md shadow-sm">
                                <input type="text" name="address[city]" id="city" required
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                    placeholder="Eg. Kathmandu" value="{{ $business->address?->city }}">
                                <div class="validation-error" id="city-error">{{ __('City is required') }}</div>
                            </div>
                        </div>

                        <!-- Additional address fields -->
                        <div class="mb-2">
                            <label for="address_line_1" class="block text-sm font-medium leading-6 text-gray-900">{{ __('business.address_1') }}</label>
                            <div class="mt-2 rounded-md shadow-sm">
                                <input type="text" value="{{ $business->address?->address_line_1 }}"
                                    name="address[address_line_1]" id="address_line_1"
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                    placeholder="Eg. Kathmandu, Nepal">
                            </div>
                        </div>

                        <div class="mb-2">
                            <label for="address_line_2" class="block text-sm font-medium leading-6 text-gray-900">{{ __('business.address_2') }}</label>
                            <div class="mt-2 rounded-md shadow-sm">
                                <input type="text" name="address[address_line_2]"
                                    value="{{ $business->address?->address_line_2 }}" id="address_line_2"
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                    placeholder="Eg. Kathmandu, Nepal">
                            </div>
                        </div>

                        <div class="mb-2">
                            <label for="postal_code"
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
                        <div class="flex justify-between w-full mt-6">
                            <button type="button" class="prev-btn inline-block px-8 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400" data-prev-tab="details">{{ __('Previous') }}</button>
                            <button type="submit" class="px-8 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">{{ __('Save Address') }}</button>
                            <button type="button" class="next-btn inline-block px-8 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" data-next-tab="contact">{{ __('Next') }}</button>
                        </div>
                    </form>
                    @else
                    <div class="text-center py-8">
                        <p class="text-gray-600">{{ __('Please save the General information first to continue.') }}</p>
                        <button type="button" class="prev-btn mt-4 px-8 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300" data-prev-tab="general">{{ __('Back to General') }}</button>
                    </div>
                    @endif
                </div>

                <!-- Contact Tab -->
                <div id="contact" class="tab-pane hidden">
                    @if($isEdit)
                    <form action="{{ route('business.saveContact', $business) }}" method="POST" id="contactForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="_section" value="contact">
                        
                        <div class="mb-2">
                            <label for="email" class="block text-sm font-medium leading-6 text-gray-900 required">{{ __('business.email') }}</label>
                            <input required type="email" name="email" id="email" value="{{ $business->email }}"
                                placeholder="Eg. abc@gmail.com"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <div class="validation-error" id="email-error">{{ __('Valid email is required') }}</div>
                        </div>
                        <div>
                            <label for="phone_1" class="block text-sm font-medium text-gray-700 required">
                                {{ __('business.phone_number') }}</label>
                            <div class="mt-1">
                                <input id="phone_1" name="phone_1" type="text" value="{{ $business->phone_1 }}" required
                                    minLength="6" maxLength="15" placeholder={{__("Eg:9812312323")}}
                                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <div class="validation-error" id="phone_1-error">{{ __('Phone number is required') }}</div>
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
                                <select name="is_active" id="active"
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    <option value="1" @if ($business->is_active) selected @endif>{{ __('business.active') }}
                                    </option>
                                    <option value="0" @if (!$business->is_active) selected @endif>{{ __('business.inactive') }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        @role('super-admin')
                            @if (isset($business->settings))
                                @foreach ($business->settings as $setting)
                                    <div class="mb-2">
                                        <label for="settings[{{ $setting->key }}]"
                                            class="block text-sm font-medium leading-6 text-gray-900">{{ __($setting->key) }}</label>
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
                                            class="block text-sm font-medium leading-6 text-gray-900">{{ __($setting) }}</label>
                                        <input type="text" name="settings[{{ $setting }}]" id="{{ $setting }}"
                                            value="{{ isset($business) && isset($business->settings) && $business->settings->isNotEmpty() ? $business?->settings?->$setting : '' }}"
                                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                            placeholder="">
                                    </div>
                                @endforeach
                            @endif
                        @endrole

                        @include('modules.business.components.social_networks', [
                            'business' => $business,
                            'socialNetworks' => $socialNetworks
                        ])
                        
                        <div class="flex justify-between w-full mt-6">
                            <button type="button" class="prev-btn px-8 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300" data-prev-tab="address">{{ __('Previous') }}</button>
                            <button type="submit" class="px-8 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">{{ __('Save Contact Information') }}</button>
                        </div>
                    </form>
                    @else
                    <div class="text-center py-8">
                        <p class="text-gray-600">{{ __('Please save the General information first to continue.') }}</p>
                        <button type="button" class="prev-btn mt-4 px-8 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300" data-prev-tab="general">{{ __('Back to General') }}</button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
@push('js')
    @include('modules.shared.state_prefill', ['entity' => $business, 'countries' => $countries])
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps.api_key') }}&libraries=places"></script>

    <script>
        // Single DOMContentLoaded event handler to avoid conflicts
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize map
            initMap();
            
            // Initialize section-specific form validation
            setupFormValidation();
            
            // Initialize tab navigation
            setupTabNavigation();
            
            // Initialize business type dependent fields
            const typeSelect = document.getElementById('type_id');
            if (typeSelect) {
                toggleEducationFields(typeSelect.value);
            }
        });
        
        function setupTabNavigation() {
            const tabs = ['general', 'details', 'address', 'contact'];
            let currentTabIndex = 0;
            
            // Switch to a specific tab
            function switchToTab(tabId) {
                // Find the tab index
                const targetTabIndex = tabs.indexOf(tabId);
                if (targetTabIndex === -1) return;
                
                currentTabIndex = targetTabIndex;
                
                // Hide all tabs
                document.querySelectorAll('.tab-pane').forEach(tab => {
                    tab.classList.add('hidden');
                });
                
                // Show the target tab
                const targetTab = document.getElementById(tabId);
                if (targetTab) {
                    targetTab.classList.remove('hidden');
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
                
                // Make sure the map renders correctly when switching to address tab
                if (tabId === 'address') {
                    setTimeout(() => {
                        google.maps.event.trigger(window, 'resize');
                    }, 100);
                }
            }
            
            // Tab navigation handler
            document.querySelectorAll('[data-tab]').forEach(button => {
                button.addEventListener('click', () => {
                    const targetTab = button.dataset.tab;
                    switchToTab(targetTab);
                });
            });
            
            // Next button click handler
            document.querySelectorAll('.next-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const nextTab = this.dataset.nextTab;
                    switchToTab(nextTab);
                });
            });
            
            // Previous button click handler
            document.querySelectorAll('.prev-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const prevTab = this.dataset.prevTab;
                    switchToTab(prevTab);
                });
            });
            
            // Initialize the form with the first tab
            switchToTab('general');
        }
        
        function setupFormValidation() {
            const generalForm = document.getElementById('generalForm');
            const detailsForm = document.getElementById('detailsForm');
            const addressForm = document.getElementById('addressForm');
            const contactForm = document.getElementById('contactForm');
            
            // Validation rules for form fields
            const validationRules = {
                'general': {
                    'name': {
                        required: true,
                        message: '{{ __("Business name is required") }}'
                    },
                    'type_id': {
                        required: true,
                        message: '{{ __("Business type is required") }}'
                    }
                },
                'details': {
                    'logo': {
                        required: false,
                        message: '{{ __("Logo image is required") }}'
                    },
                    'cover_image': {
                        required: false,
                        message: '{{ __("Cover image is required") }}'
                    }
                },
                'address': {
                    'country': {
                        required: true,
                        message: '{{ __("Country is required") }}'
                    },
                    'city': {
                        required: true,
                        message: '{{ __("City is required") }}'
                    }
                },
                'contact': {
                    'email': {
                        required: true,
                        pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
                        message: '{{ __("Valid email is required") }}'
                    },
                    'phone_1': {
                        required: true,
                        minLength: 6,
                        maxLength: 15,
                        message: '{{ __("Phone number is required") }}'
                    }
                }
            };
            
            // Form submission handlers
            if (generalForm) {
                generalForm.addEventListener('submit', function(e) {
                    if (!validateForm('general')) {
                        e.preventDefault();
                    }
                });
            }
            
            if (detailsForm) {
                detailsForm.addEventListener('submit', function(e) {
                    if (!validateForm('details')) {
                        e.preventDefault();
                    }
                });
            }
            
            if (addressForm) {
                addressForm.addEventListener('submit', function(e) {
                    if (!validateForm('address')) {
                        e.preventDefault();
                    }
                });
            }
            
            if (contactForm) {
                contactForm.addEventListener('submit', function(e) {
                    if (!validateForm('contact')) {
                        e.preventDefault();
                    }
                });
            }
            
            // Validate form fields for a specific section
            function validateForm(section) {
                let isValid = true;
                const rules = validationRules[section];
                
                for (const [fieldId, rule] of Object.entries(rules)) {
                    const field = document.getElementById(fieldId);
                    const errorElement = document.getElementById(`${fieldId}-error`);
                    
                    if (!field) continue;
                    
                    field.classList.remove('error-border');
                    if (errorElement) errorElement.style.display = 'none';
                    
                    let fieldValid = true;
                    
                    // Skip validation for file inputs in edit mode
                    if (field.type === 'file' && !rule.required) {
                        continue;
                    }
                    
                    // Required validation
                    if (rule.required && !field.value.trim()) {
                        fieldValid = false;
                    }
                    
                    // Pattern validation
                    if (fieldValid && rule.pattern && field.value.trim() && !rule.pattern.test(field.value.trim())) {
                        fieldValid = false;
                    }
                    
                    if (!fieldValid) {
                        isValid = false;
                        
                        if (errorElement) {
                            errorElement.style.display = 'block';
                            field.classList.add('error-border');
                            field.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    }
                }
                
                return isValid;
            }
        }
        
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
                // Initialize hidden location input with existing coordinates
                document.getElementById('location').value = `POINT(${lng} ${lat})`;
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
                const lat = latlng.lat();
                const lng = latlng.lng();
                document.getElementById('coordinates').value = `${lat},${lng}`;
                document.getElementById('location').value = `POINT(${lng} ${lat})`;
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

        // Add dynamic form handling for languages and destinations
        document.getElementById('add-language')?.addEventListener('click', async function() {
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

        document.getElementById('add-destination')?.addEventListener('click', async function() {
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
        document.getElementById('type_id')?.addEventListener('change', function() {
            toggleEducationFields(this.value);
        });
    </script>
@endpush
