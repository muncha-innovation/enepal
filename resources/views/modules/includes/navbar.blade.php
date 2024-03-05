<nav>
    <div class="sticky top-0 z-10 flex-shrink-0 flex h-16 bg-white shadow">
        <button type="button"
            class="w-12 h-full text-center border-r border-gray-200 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 md:hidden">

            <svg class="h-6 w-6 pointer-events-none mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
            </svg>
        </button>
        <div class="flex-1 px-4 flex justify-between">
            <div class="flex-1 flex"></div>
            <div class="ml-4 flex items-center md:ml-6">
                @auth
                    <p class="text-gray-500 font-sm">{{ __('Welcome') }},<br> {{ auth()->user()->last_name }}
                        {{ auth()->user()->first_name }}
                    </p>
                @endauth


                <div class="ml-3 relative">

                    <button type="button"
                        class="dropdown bg-white rounded-full flex text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        id="language-menu" aria-expanded="false" aria-haspopup="true">
                        <span class="sr-only">{{ __('Select Language') }}</span>
                        <img class="pointer-events-none h-8 w-8 rounded-full"
                            src="{{ asset(app()->getLocale() . '_flag.png') }}" alt=""> </button>

                    <div class=" hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                        role="menu" aria-orientation="vertical" aria-labelledby="language-menu" tabindex="-1">
                        <!-- Active: "bg-gray-100", Not Active: "" -->
                        @foreach (config('app.supported_locales') as $locale)
                            <a href="{{ route('change-locale', $locale) }}"
                                class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1">
                                <img class="pointer-events-none h-8 w-8 rounded-full"
                                    src="{{ asset($locale . '_flag.png') }}" alt="" style="display: inline">
                                {{ getLanguageFromCode($locale) }}

                            </a>
                        @endforeach
                    </div>

                </div>
                @auth
                    <!-- Profile dropdown -->
                    <div class="ml-3 relative">

                        <button type="button"
                            class="dropdown bg-white rounded-full flex text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                            <span class="sr-only">{{ __('Open user menu') }}</span>
                            <img class="pointer-events-none h-8 w-8 rounded-full" src="{{ auth()->user()->full_path }}"
                                alt="">
                        </button>

                        <div class=" hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                            role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                            <!-- Active: "bg-gray-100", Not Active: "" -->
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700" role="menuitem" tabindex="-1"
                                id="user-menu-item-2"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{ __('Sign Out') }}</a>
                        </div>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>

                    </div>
                @endauth
            </div>
        </div>
    </div>
</nav>
