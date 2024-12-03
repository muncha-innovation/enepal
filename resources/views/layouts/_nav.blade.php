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
                        <svg class="mr-3 text-gray-500" width="24" height="24" fill="currentColor"
                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M20 7h-3V5a2 2 0 0 0-2-2h-6a2 2 0 0 0-2 2v2H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2ZM9 5h6v2H9V5Zm11 14H4V9h16v10Z" />
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
                            <svg class="mr-3 text-gray-500" width="24" height="24" fill="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M16 11a4 4 0 1 0-8 0 4 4 0 0 0 8 0Zm-8 5c-3.31 0-6 2.69-6 6v1h12v-1c0-3.31-2.69-6-6-6Zm10-5a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm1 5h-2a3.986 3.986 0 0 1 2 3v1h4v-1c0-1.7-1.3-3-3-3Z" />
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
                            <svg class="mr-3 text-gray-500" width="24" height="24" fill="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 4h16v3H4V4Zm0 5h10v3H4V9Zm0 5h16v3H4v-3Zm0 5h10v3H4v-3Z" />
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
                            <svg class="mr-3 text-gray-500" width="24" height="24" fill="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M12 2C9.243 2 7 4.243 7 7s2.243 5 5 5 5-2.243 5-5-2.243-5-5-5Zm0 8c-1.654 0-3-1.346-3-3s1.346-3 3-3 3 1.346 3 3-1.346 3-3 3ZM6 14h12c1.103 0 2 .897 2 2v5H4v-5c0-1.103.897-2 2-2Zm12 5v-3H6v3h12Z" />
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
                            <svg class="mr-3 text-gray-500" width="24" height="24" fill="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Zm0 2v12h16V6H4Zm3 3h4a1 1 0 1 1 0 2H7a1 1 0 1 1 0-2Zm0 4h10a1 1 0 1 1 0 2H7a1 1 0 1 1 0-2Z" />
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
                            <svg class="mr-3 text-gray-500" width="24" height="24" fill="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M12 15.5a3.5 3.5 0 1 1 0-7 3.5 3.5 0 0 1 0 7Zm9-3.5a7.998 7.998 0 0 0-.252-1.879l2.045-1.589a1.003 1.003 0 0 0 .206-1.259l-2-3.464a1 1 0 0 0-1.227-.455l-2.434.974a7.94 7.94 0 0 0-1.636-.947L14.834 2.64A1 1 0 0 0 13.88 2h-3.76a1 1 0 0 0-.955.64l-.76 2.55a7.928 7.928 0 0 0-1.636.947l-2.434-.974a1 1 0 0 0-1.227.455l-2 3.464a1.003 1.003 0 0 0 .206 1.259L3.252 9.62A7.998 7.998 0 0 0 3 11.5c0 .643.088 1.267.252 1.879l-2.045 1.589a1.003 1.003 0 0 0-.206 1.259l2 3.464a1 1 0 0 0 1.227.455l2.434-.974c.518.385 1.066.712 1.636.947l.76 2.55a1 1 0 0 0 .955.64h3.76a1 1 0 0 0 .955-.64l.76-2.55c.57-.235 1.118-.562 1.636-.947l2.434.974a1 1 0 0 0 1.227-.455l2-3.464a1.003 1.003 0 0 0-.206-1.259l-2.045-1.589A7.998 7.998 0 0 0 21 11.5ZM12 14a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z" />
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
                            <svg class="mr-3 text-gray-500" width="24" height="24" fill="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20ZM4.929 8h4.536a21.697 21.697 0 0 1 0 8H4.929a8.045 8.045 0 0 1 0-8Zm1.707-2A7.953 7.953 0 0 1 12 4.06 7.953 7.953 0 0 1 17.364 6h-3.576A14.532 14.532 0 0 0 12 4.506 14.532 14.532 0 0 0 10.212 6H6.636ZM12 19.94A7.953 7.953 0 0 1 6.636 18h3.576A14.532 14.532 0 0 0 12 19.494 14.532 14.532 0 0 0 13.788 18h3.576A7.953 7.953 0 0 1 12 19.94Zm3.536-3.94h-3.072a19.596 19.596 0 0 0 0-8h3.072a8.045 8.045 0 0 1 0 8Z" />
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
