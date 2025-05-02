{{-- Facilities Section --}}
<div id="facilities-section" class="mt-4 mb-6">
    <h2 class="text-lg font-semibold text-gray-700">{{ __('business.facilities') }}</h2>
    <div id="facilities-container">
        @include('modules.business.components.type_facilities', [
            'business' => $business,
            'typeFacilities' => $typeFacilities ?? []
        ])
    </div>
</div>

{{-- Business Hours Section --}}
@include('modules.business.components.opening_closing', ['business' => $business])