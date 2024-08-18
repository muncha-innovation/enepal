@php
    $isUserRoute = str_contains(url()->current(), 'user');
    $isProfileRoute = str_contains(url()->current(), 'profile');

    $isBusinessRoute = str_contains(url()->current(), 'business');
    $isBusinessTypesRoute = str_contains(url()->current(), 'businessTypes');
    $isFacilitiesRoute = str_contains(url()->current(), 'facilities');
    $isSettingsRoute = str_contains(url()->current(), 'settings');
    $isTemplatesRoute = str_contains(url()->current(), 'template');
    if (!$isBusinessRoute) {
        $isBusinessRoute = 0;
    }
    if (!$isProfileRoute) {
        $isProfileRoute = 0;
    }
    if (!$isUserRoute) {
        $isUserRoute = 0;
    }
    if (!$isBusinessTypesRoute) {
        $isBusinessTypesRoute = 0;
    }
    if (!$isFacilitiesRoute) {
        $isFacilitiesRoute = 0;
    }
    if (!$isSettingsRoute) {
        $isSettingsRoute = 0;
    }
    if (!$isTemplatesRoute) {
        $isTemplatesRoute = 0;
    }
@endphp

<div class="flex items-center flex-shrink-0 px-4 justify-center py-3">
    <a href="{{ route('dashboard') }}">
        <img class="h-20 w-auto" src="{{ asset('logo.png') }}" alt="{{ __('Enepal') }}" />
    </a>
</div>

<nav class="flex flex-col justify-between flex-1 px-2 mt-5 space-y-4 divide-y-2 divide-gray-300">
    <div class="space-y-1">
        <div x-data="{ open: {{ $isProfileRoute }} }" class="space-y-1">
            <a href="{{ route('profile') }}">
                <button
                    class="flex items-center justify-between w-full px-2 py-2 text-sm font-medium text-gray-500 rounded-md hover:bg-gray-100 hover:text-gray-900 group">
                    <div class="flex items-center pointer-events-none">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="flex-shrink-0 w-6 h-6 mr-3 text-gray-500 group-hover:text-gray-500"
                                fill="currentColor" viewBox="0 0 16 16">
                                <path
                                    d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z">
                                </path>
                            </svg>
                        </div>
                        {{ __('Profile') }}
                    </div>
                </button>
            </a>
        </div>
        <div x-data="{ open: {{ $isBusinessRoute }} }" class="space-y-1">
            <button type="button" x-on:click="open = !open"
                class="flex items-center justify-between w-full px-2 py-2 text-sm font-medium text-gray-500 rounded-md hover:bg-gray-100 hover:text-gray-900 group">
                <div class="flex items-center pointer-events-none">
                    <div>
                        <svg class="mr-3" class="text-gray-500" width="24" height="24" fill="currentColor"
                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M9.47 9.643 8.768 3.77l-.002-.074c0-.889.72-1.61 1.61-1.61.802 0 1.545.42 1.977 1.135l.087.161 2.945 6.09 3.957-.117a2.6 2.6 0 0 1 2.672 2.53l.001.066a2.602 2.602 0 0 1-2.62 2.597l-3.963-.116-2.992 6.188a2.293 2.293 0 0 1-2.065 1.295c-.889 0-1.609-.72-1.609-1.631l.007-.118.707-5.908-2.132-.063-.27.736a1.946 1.946 0 0 1-1.827 1.278c-.876 0-1.586-.71-1.586-1.587v-.76l-.154-.032a1.92 1.92 0 0 1 0-3.758l.155-.032v-.76c0-.803.597-1.475 1.434-1.579l.151-.008c.745 0 1.423.426 1.765 1.127l.063.15.27.736 2.12-.062Zm.906-6.057c-.06 0-.109.049-.11.087l.887 7.422-4.84.141-.628-1.715-.032-.079c-.075-.152-.23-.25-.354-.251l-.058.002a.086.086 0 0 0-.075.085l.001 1.98-1.35.282a.419.419 0 0 0 0 .821l1.35.281v1.98c0 .047.038.086.085.086a.446.446 0 0 0 .419-.293l.64-1.751 4.854.141-.897 7.471v.03c0 .06.048.108.108.108a.793.793 0 0 0 .714-.448l3.415-7.063 4.914.144c.606 0 1.097-.491 1.097-1.086v-.043a1.097 1.097 0 0 0-1.13-1.064l-4.928.144-3.351-6.932-.053-.099a.793.793 0 0 0-.678-.381Z" />
                        </svg>
                    </div>
                    {{ __('Business') }}
                </div>
                <svg x-bind:class="!open ? '' : 'rotate-90'"
                    class="flex-shrink-0 w-5 h-5 mr-2 text-gray-500 transition-colors duration-150 ease-in-out transform pointer-events-none group-hover:text-gray-400"
                    viewBox="0 0 20 20" aria-hidden="true">
                    <path d="M6 6L14 10L6 14V6Z" fill="currentColor"></path>
                </svg>
            </button>
            <div x-show="open" class="space-y-1">
                <a href="{{ route('business.index') }}"
                    class="flex items-center w-full py-2 pl-10 pr-2 text-sm font-medium text-gray-500 rounded-md group hover:bg-gray-100 hover:text-gray-900 @if (url()->current() == route('business.index')) bg-gray-200 @endif">{{ __('List') }}</a>
                <a href="{{ route('business.create') }}"
                    class="flex items-center w-full py-2 pl-10 pr-2 text-sm font-medium text-gray-500 rounded-md group hover:bg-gray-100 hover:text-gray-900 @if (url()->current() == route('business.create')) bg-gray-200 @endif">{{ __('Create') }}</a>
            </div>
        </div>
        @role('super-admin')
            <div x-data="{ open: {{ $isUserRoute }} }" class="space-y-1">
                <button type="button" x-on:click="open = !open"
                    class="flex items-center justify-between w-full px-2 py-2 text-sm font-medium text-gray-500 rounded-md hover:bg-gray-100 hover:text-gray-900 group">
                    <div class="flex items-center pointer-events-none">
                        <div>
                            <svg class="mr-3" class="text-gray-500" width="24" height="24" fill="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M9.47 9.643 8.768 3.77l-.002-.074c0-.889.72-1.61 1.61-1.61.802 0 1.545.42 1.977 1.135l.087.161 2.945 6.09 3.957-.117a2.6 2.6 0 0 1 2.672 2.53l.001.066a2.602 2.602 0 0 1-2.62 2.597l-3.963-.116-2.992 6.188a2.293 2.293 0 0 1-2.065 1.295c-.889 0-1.609-.72-1.609-1.631l.007-.118.707-5.908-2.132-.063-.27.736a1.946 1.946 0 0 1-1.827 1.278c-.876 0-1.586-.71-1.586-1.587v-.76l-.154-.032a1.92 1.92 0 0 1 0-3.758l.155-.032v-.76c0-.803.597-1.475 1.434-1.579l.151-.008c.745 0 1.423.426 1.765 1.127l.063.15.27.736 2.12-.062Zm.906-6.057c-.06 0-.109.049-.11.087l.887 7.422-4.84.141-.628-1.715-.032-.079c-.075-.152-.23-.25-.354-.251l-.058.002a.086.086 0 0 0-.075.085l.001 1.98-1.35.282a.419.419 0 0 0 0 .821l1.35.281v1.98c0 .047.038.086.085.086a.446.446 0 0 0 .419-.293l.64-1.751 4.854.141-.897 7.471v.03c0 .06.048.108.108.108a.793.793 0 0 0 .714-.448l3.415-7.063 4.914.144c.606 0 1.097-.491 1.097-1.086v-.043a1.097 1.097 0 0 0-1.13-1.064l-4.928.144-3.351-6.932-.053-.099a.793.793 0 0 0-.678-.381Z" />
                            </svg>
                        </div>
                        {{ __('Users') }}
                    </div>
                    <svg x-bind:class="!open ? '' : 'rotate-90'"
                        class="flex-shrink-0 w-5 h-5 mr-2 text-gray-500 transition-colors duration-150 ease-in-out transform pointer-events-none group-hover:text-gray-400"
                        viewBox="0 0 20 20" aria-hidden="true">
                        <path d="M6 6L14 10L6 14V6Z" fill="currentColor"></path>
                    </svg>
                </button>
                <div x-show="open" class="space-y-1">
                    <a href="{{ route('admin.users.index') }}"
                        class="flex items-center w-full py-2 pl-10 pr-2 text-sm font-medium text-gray-500 rounded-md group hover:bg-gray-100 hover:text-gray-900 @if (url()->current() == route('business.index')) bg-gray-200 @endif">{{ __('List') }}</a>
                    <a href="{{ route('admin.users.create') }}"
                        class="flex items-center w-full py-2 pl-10 pr-2 text-sm font-medium text-gray-500 rounded-md group hover:bg-gray-100 hover:text-gray-900 @if (url()->current() == route('business.create')) bg-gray-200 @endif">{{ __('Create') }}</a>
                </div>
            </div>
        @endrole


        @role('super-admin')
            <div x-data="{ open: {{ $isBusinessTypesRoute }} }" class="space-y-1">
                <button type="button" x-on:click="open = !open"
                    class="flex items-center justify-between w-full px-2 py-2 text-sm font-medium text-gray-500 rounded-md hover:bg-gray-100 hover:text-gray-900 group">
                    <div class="flex items-center pointer-events-none">
                        <div>
                            <svg class="mr-3" class="text-gray-500" width="24" height="24" fill="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M9.47 9.643 8.768 3.77l-.002-.074c0-.889.72-1.61 1.61-1.61.802 0 1.545.42 1.977 1.135l.087.161 2.945 6.09 3.957-.117a2.6 2.6 0 0 1 2.672 2.53l.001.066a2.602 2.602 0 0 1-2.62 2.597l-3.963-.116-2.992 6.188a2.293 2.293 0 0 1-2.065 1.295c-.889 0-1.609-.72-1.609-1.631l.007-.118.707-5.908-2.132-.063-.27.736a1.946 1.946 0 0 1-1.827 1.278c-.876 0-1.586-.71-1.586-1.587v-.76l-.154-.032a1.92 1.92 0 0 1 0-3.758l.155-.032v-.76c0-.803.597-1.475 1.434-1.579l.151-.008c.745 0 1.423.426 1.765 1.127l.063.15.27.736 2.12-.062Zm.906-6.057c-.06 0-.109.049-.11.087l.887 7.422-4.84.141-.628-1.715-.032-.079c-.075-.152-.23-.25-.354-.251l-.058.002a.086.086 0 0 0-.075.085l.001 1.98-1.35.282a.419.419 0 0 0 0 .821l1.35.281v1.98c0 .047.038.086.085.086a.446.446 0 0 0 .419-.293l.64-1.751 4.854.141-.897 7.471v.03c0 .06.048.108.108.108a.793.793 0 0 0 .714-.448l3.415-7.063 4.914.144c.606 0 1.097-.491 1.097-1.086v-.043a1.097 1.097 0 0 0-1.13-1.064l-4.928.144-3.351-6.932-.053-.099a.793.793 0 0 0-.678-.381Z" />
                            </svg>
                        </div>
                        {{ __('Business Types') }}
                    </div>
                    <svg x-bind:class="!open ? '' : 'rotate-90'"
                        class="flex-shrink-0 w-5 h-5 mr-2 text-gray-500 transition-colors duration-150 ease-in-out transform pointer-events-none group-hover:text-gray-400"
                        viewBox="0 0 20 20" aria-hidden="true">
                        <path d="M6 6L14 10L6 14V6Z" fill="currentColor"></path>
                    </svg>
                </button>
                <div x-show="open" class="space-y-1">
                    <a href="{{ route('admin.businessTypes.index') }}"
                        class="flex items-center w-full py-2 pl-10 pr-2 text-sm font-medium text-gray-500 rounded-md group hover:bg-gray-100 hover:text-gray-900 @if (url()->current() == route('business.index')) bg-gray-200 @endif">{{ __('List') }}</a>
                    <a href="{{ route('admin.businessTypes.create') }}"
                        class="flex items-center w-full py-2 pl-10 pr-2 text-sm font-medium text-gray-500 rounded-md group hover:bg-gray-100 hover:text-gray-900 @if (url()->current() == route('business.create')) bg-gray-200 @endif">{{ __('Create') }}</a>
                </div>
            </div>
        @endrole


        @role('super-admin')
            <div x-data="{ open: {{ $isFacilitiesRoute }} }" class="space-y-1">
                <button type="button" x-on:click="open = !open"
                    class="flex items-center justify-between w-full px-2 py-2 text-sm font-medium text-gray-500 rounded-md hover:bg-gray-100 hover:text-gray-900 group">
                    <div class="flex items-center pointer-events-none">
                        <div>
                            <svg class="mr-3" class="text-gray-500" width="24" height="24" fill="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M9.47 9.643 8.768 3.77l-.002-.074c0-.889.72-1.61 1.61-1.61.802 0 1.545.42 1.977 1.135l.087.161 2.945 6.09 3.957-.117a2.6 2.6 0 0 1 2.672 2.53l.001.066a2.602 2.602 0 0 1-2.62 2.597l-3.963-.116-2.992 6.188a2.293 2.293 0 0 1-2.065 1.295c-.889 0-1.609-.72-1.609-1.631l.007-.118.707-5.908-2.132-.063-.27.736a1.946 1.946 0 0 1-1.827 1.278c-.876 0-1.586-.71-1.586-1.587v-.76l-.154-.032a1.92 1.92 0 0 1 0-3.758l.155-.032v-.76c0-.803.597-1.475 1.434-1.579l.151-.008c.745 0 1.423.426 1.765 1.127l.063.15.27.736 2.12-.062Zm.906-6.057c-.06 0-.109.049-.11.087l.887 7.422-4.84.141-.628-1.715-.032-.079c-.075-.152-.23-.25-.354-.251l-.058.002a.086.086 0 0 0-.075.085l.001 1.98-1.35.282a.419.419 0 0 0 0 .821l1.35.281v1.98c0 .047.038.086.085.086a.446.446 0 0 0 .419-.293l.64-1.751 4.854.141-.897 7.471v.03c0 .06.048.108.108.108a.793.793 0 0 0 .714-.448l3.415-7.063 4.914.144c.606 0 1.097-.491 1.097-1.086v-.043a1.097 1.097 0 0 0-1.13-1.064l-4.928.144-3.351-6.932-.053-.099a.793.793 0 0 0-.678-.381Z" />
                            </svg>
                        </div>
                        {{ __('Business Facilities') }}
                    </div>
                    <svg x-bind:class="!open ? '' : 'rotate-90'"
                        class="flex-shrink-0 w-5 h-5 mr-2 text-gray-500 transition-colors duration-150 ease-in-out transform pointer-events-none group-hover:text-gray-400"
                        viewBox="0 0 20 20" aria-hidden="true">
                        <path d="M6 6L14 10L6 14V6Z" fill="currentColor"></path>
                    </svg>
                </button>
                <div x-show="open" class="space-y-1">
                    <a href="{{ route('admin.facilities.index') }}"
                        class="flex items-center w-full py-2 pl-10 pr-2 text-sm font-medium text-gray-500 rounded-md group hover:bg-gray-100 hover:text-gray-900 @if (url()->current() == route('business.index')) bg-gray-200 @endif">{{ __('List') }}</a>
                    <a href="{{ route('admin.facilities.create') }}"
                        class="flex items-center w-full py-2 pl-10 pr-2 text-sm font-medium text-gray-500 rounded-md group hover:bg-gray-100 hover:text-gray-900 @if (url()->current() == route('business.create')) bg-gray-200 @endif">{{ __('Create') }}</a>
                </div>
            </div>
        @endrole
        @role('super-admin')
            <div x-data="{ open: {{ $isTemplatesRoute }} }" class="space-y-1">
                <a href="{{ route('admin.templates.index') }}"
                    class="flex items-center justify-between w-full px-2 py-2 text-sm font-medium text-gray-500 rounded-md hover:bg-gray-100 hover:text-gray-900 group">
                    <div class="flex items-center pointer-events-none">
                        <div>
                            <svg class="mr-3" class="text-gray-500" width="24" height="24" fill="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M9.47 9.643 8.768 3.77l-.002-.074c0-.889.72-1.61 1.61-1.61.802 0 1.545.42 1.977 1.135l.087.161 2.945 6.09 3.957-.117a2.6 2.6 0 0 1 2.672 2.53l.001.066a2.602 2.602 0 0 1-2.62 2.597l-3.963-.116-2.992 6.188a2.293 2.293 0 0 1-2.065 1.295c-.889 0-1.609-.72-1.609-1.631l.007-.118.707-5.908-2.132-.063-.27.736a1.946 1.946 0 0 1-1.827 1.278c-.876 0-1.586-.71-1.586-1.587v-.76l-.154-.032a1.92 1.92 0 0 1 0-3.758l.155-.032v-.76c0-.803.597-1.475 1.434-1.579l.151-.008c.745 0 1.423.426 1.765 1.127l.063.15.27.736 2.12-.062Zm.906-6.057c-.06 0-.109.049-.11.087l.887 7.422-4.84.141-.628-1.715-.032-.079c-.075-.152-.23-.25-.354-.251l-.058.002a.086.086 0 0 0-.075.085l.001 1.98-1.35.282a.419.419 0 0 0 0 .821l1.35.281v1.98c0 .047.038.086.085.086a.446.446 0 0 0 .419-.293l.64-1.751 4.854.141-.897 7.471v.03c0 .06.048.108.108.108a.793.793 0 0 0 .714-.448l3.415-7.063 4.914.144c.606 0 1.097-.491 1.097-1.086v-.043a1.097 1.097 0 0 0-1.13-1.064l-4.928.144-3.351-6.932-.053-.099a.793.793 0 0 0-.678-.381Z" />
                            </svg>
                        </div>
                        {{ __('Templates') }}
                    </div>
                    <svg x-bind:class="!open ? '' : 'rotate-90'"
                        class="flex-shrink-0 w-5 h-5 mr-2 text-gray-500 transition-colors duration-150 ease-in-out transform pointer-events-none group-hover:text-gray-400"
                        viewBox="0 0 20 20" aria-hidden="true">
                        <path d="M6 6L14 10L6 14V6Z" fill="currentColor"></path>
                    </svg>
                </a>

            </div>
        @endrole

        @role('super-admin')
            <div x-data="{ open: {{ $isSettingsRoute }} }" class="space-y-1">
                <a href="{{ route('admin.settings.index') }}"
                    class="flex items-center justify-between w-full px-2 py-2 text-sm font-medium text-gray-500 rounded-md hover:bg-gray-100 hover:text-gray-900 group">
                    <div class="flex items-center pointer-events-none">
                        <div>
                            <svg class="mr-3" class="text-gray-500" width="24" height="24" fill="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M9.47 9.643 8.768 3.77l-.002-.074c0-.889.72-1.61 1.61-1.61.802 0 1.545.42 1.977 1.135l.087.161 2.945 6.09 3.957-.117a2.6 2.6 0 0 1 2.672 2.53l.001.066a2.602 2.602 0 0 1-2.62 2.597l-3.963-.116-2.992 6.188a2.293 2.293 0 0 1-2.065 1.295c-.889 0-1.609-.72-1.609-1.631l.007-.118.707-5.908-2.132-.063-.27.736a1.946 1.946 0 0 1-1.827 1.278c-.876 0-1.586-.71-1.586-1.587v-.76l-.154-.032a1.92 1.92 0 0 1 0-3.758l.155-.032v-.76c0-.803.597-1.475 1.434-1.579l.151-.008c.745 0 1.423.426 1.765 1.127l.063.15.27.736 2.12-.062Zm.906-6.057c-.06 0-.109.049-.11.087l.887 7.422-4.84.141-.628-1.715-.032-.079c-.075-.152-.23-.25-.354-.251l-.058.002a.086.086 0 0 0-.075.085l.001 1.98-1.35.282a.419.419 0 0 0 0 .821l1.35.281v1.98c0 .047.038.086.085.086a.446.446 0 0 0 .419-.293l.64-1.751 4.854.141-.897 7.471v.03c0 .06.048.108.108.108a.793.793 0 0 0 .714-.448l3.415-7.063 4.914.144c.606 0 1.097-.491 1.097-1.086v-.043a1.097 1.097 0 0 0-1.13-1.064l-4.928.144-3.351-6.932-.053-.099a.793.793 0 0 0-.678-.381Z" />
                            </svg>
                        </div>
                        {{ __('Settings') }}
                    </div>
                    <svg x-bind:class="!open ? '' : 'rotate-90'"
                        class="flex-shrink-0 w-5 h-5 mr-2 text-gray-500 transition-colors duration-150 ease-in-out transform pointer-events-none group-hover:text-gray-400"
                        viewBox="0 0 20 20" aria-hidden="true">
                        <path d="M6 6L14 10L6 14V6Z" fill="currentColor"></path>
                    </svg>
                </a>

            </div>
        @endrole

        @role('super-admin')
            <div class="space-y-1">
                <button type="button" x-on:click="open = !open"
                    class="flex items-center justify-between w-full px-2 py-2 text-sm font-medium text-gray-500 rounded-md hover:bg-gray-100 hover:text-gray-900 group">
                    <div class="flex items-center pointer-events-none">
                        <div>
                            <svg class="mr-3" class="text-gray-500" width="24" height="24" fill="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M9.47 9.643 8.768 3.77l-.002-.074c0-.889.72-1.61 1.61-1.61.802 0 1.545.42 1.977 1.135l.087.161 2.945 6.09 3.957-.117a2.6 2.6 0 0 1 2.672 2.53l.001.066a2.602 2.602 0 0 1-2.62 2.597l-3.963-.116-2.992 6.188a2.293 2.293 0 0 1-2.065 1.295c-.889 0-1.609-.72-1.609-1.631l.007-.118.707-5.908-2.132-.063-.27.736a1.946 1.946 0 0 1-1.827 1.278c-.876 0-1.586-.71-1.586-1.587v-.76l-.154-.032a1.92 1.92 0 0 1 0-3.758l.155-.032v-.76c0-.803.597-1.475 1.434-1.579l.151-.008c.745 0 1.423.426 1.765 1.127l.063.15.27.736 2.12-.062Zm.906-6.057c-.06 0-.109.049-.11.087l.887 7.422-4.84.141-.628-1.715-.032-.079c-.075-.152-.23-.25-.354-.251l-.058.002a.086.086 0 0 0-.075.085l.001 1.98-1.35.282a.419.419 0 0 0 0 .821l1.35.281v1.98c0 .047.038.086.085.086a.446.446 0 0 0 .419-.293l.64-1.751 4.854.141-.897 7.471v.03c0 .06.048.108.108.108a.793.793 0 0 0 .714-.448l3.415-7.063 4.914.144c.606 0 1.097-.491 1.097-1.086v-.043a1.097 1.097 0 0 0-1.13-1.064l-4.928.144-3.351-6.932-.053-.099a.793.793 0 0 0-.678-.381Z" />
                            </svg>
                        </div>
                        {{ __('Language') }}
                    </div>
                    <svg x-bind:class="!open ? '' : 'rotate-90'"
                        class="flex-shrink-0 w-5 h-5 mr-2 text-gray-500 transition-colors duration-150 ease-in-out transform pointer-events-none group-hover:text-gray-400"
                        viewBox="0 0 20 20" aria-hidden="true">
                        <path d="M6 6L14 10L6 14V6Z" fill="currentColor"></path>
                    </svg>
                </button>
                <div x-show="open" class="space-y-1">
                    <a href="{{ route('change-locale', 'en') }}"
                        class="flex items-center w-full py-2 pl-10 pr-2 text-sm font-medium text-gray-500 rounded-md group hover:bg-gray-100 hover:text-gray-900">{{ __('English') }}</a>
                    <a href="{{ route('change-locale', 'np') }}"
                        class="flex items-center w-full py-2 pl-10 pr-2 text-sm font-medium text-gray-500 rounded-md group hover:bg-gray-100 hover:text-gray-900">{{ __('Nepali') }}</a>
                </div>
            </div>
        @endrole
    </div>

    <div class="mt-6">
        <div class="flex items-center justify-between mt-6">
            <a href="#" class="flex items-center gap-x-2">
                <img class="object-cover rounded-full h-7 w-7"
                    src="{{ getImage(auth()->user()->profile_picture, 'profile/') }}" alt="avatar" />
                <span class="text-sm font-medium text-gray-700">{{ auth()->user()->name }}</span>
            </a>

            <a href="#"
                class="text-gray-500 transition-colors duration-200 rotate-180 rtl:rotate-0 hover:text-blue-500"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                </svg>
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                {{ csrf_field() }}
            </form>
        </div>
    </div>
</nav>
