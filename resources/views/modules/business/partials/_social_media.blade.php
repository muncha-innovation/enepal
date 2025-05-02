{{-- Social Media Accounts using existing component --}}
@include('modules.business.components.social_networks', [
    'business' => $business,
    'socialNetworks' => $socialNetworks ?? []
])