@php
    $isUserRoute = str_contains(url()->current(), 'user');
    $isProfileRoute = str_contains(url()->current(), 'profile');

    $isBusinessRoute = str_contains(url()->current(), 'business');
  
    if (!$isBusinessRoute) {
        $isBusinessRoute = 0;
    }
    if (!$isProfileRoute) {
        $isProfileRoute = 0;
    }
    if (!$isUserRoute) {
        $isUserRoute = 0;
    }
@endphp
<div class="fixed inset-0 flex z-40 md:hidden transition-opacity ease-linear duration-300 side-nav pointer-events-none opacity-0"
    role="dialog" aria-modal="true">
    <div onclick="toggleSidebar()" class="overlay fixed inset-0 bg-gray-700 bg-opacity-75" aria-hidden="true"></div>
    <div
        class="relative transition ease-in-out duration-300 transform side-nav-container -translate-x-full flex-1 flex flex-col max-w-xs w-full bg-gray-800 pointer-events-auto">
        <div class="absolute top-0 right-0 -mr-12 pt-2 ease-in-out duration-300">
            <button onclick="toggleSidebar()" type="button"
                class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                <span class="sr-only">{{ __('Close sidebar') }}</span>
                <!-- Heroicon name: outline/x -->
                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
            @include('layouts._nav')

        </div>

    </div>

    <div class="flex-shrink-0 w-14">
        <!-- Force sidebar to shrink to fit close icon -->
    </div>
</div>


<!-- Static sidebar for desktop -->
<div class="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0">
    <!-- Sidebar component, swap this element with another sidebar if you like -->
    <div class="flex-1 flex flex-col min-h-0 bg-white">
        <div class="flex flex-col pb-4 overflow-y-auto custom-scroll2 h-full">
            @include('layouts._nav')
        </div>
    </div>
</div>
