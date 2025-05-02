{{-- Manpower & Education Consultancy Fields --}}
@include('modules.business.components.education_fields', [
    'showEducationFields' => true,
    'business' => $business,
    'languages' => $languages,
    'countries' => $countries
])