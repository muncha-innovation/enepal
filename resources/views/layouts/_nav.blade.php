<div class="flex items-center flex-shrink-0 px-4">
    <a href="{{ route('dashboard') }}">

        <img class="w-auto" src="{{ asset('logo.png') }}" alt="{{ __('Enepal') }}" />
    </a>
</div>

<nav class="flex flex-col justify-between flex-1 px-2 mt-5 space-y-4 divide-y-2 divide-gray-300">
    {{--
    @role($superAdmin)
    <div class="space-y-1">
        <a href="{{ route('logs.all') }}" type="button" class="flex items-center justify-between w-full px-2 py-2 text-sm font-medium text-gray-500 rounded-md hover:bg-gray-700 hover:text-white group @if (url()->current() == route('logs.all')) bg-gray-700 @endif">
            <div class="flex items-center pointer-events-none">
                <div class="pr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div> {{ __('Logs') }}
            </div>
            <svg x-bind:class="!open ? '' : 'rotate-90'" class="flex-shrink-0 w-5 h-5 mr-2 text-gray-500 transition-colors duration-150 ease-in-out transform pointer-events-none group-hover:text-gray-400" viewBox="0 0 20 20" aria-hidden="true">
                <path d="M6 6L14 10L6 14V6Z" fill="currentColor"></path>
            </svg>
        </a>
    </div>
    @endrole --}}
    <div class="pt-5 space-y-1">
        @if (auth()->user()->isSupervisorOrAdmin())
        <h1 class="px-2 mb-5 text-xs font-bold text-gray-400">{{ __('SETUP') }}</h1>
        @endif
        @if (auth()->user()->isSuperAdmin())
        <div x-data="{ open: {{ $isUserRoute }} }" class="space-y-1">
            <button type="button" x-on:click="open = !open" class="flex items-center justify-between w-full px-2 py-2 text-sm font-medium text-gray-500 rounded-md hover:bg-gray-700 hover:text-white group">
                <div class="flex items-center pointer-events-none">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 w-6 h-6 mr-3 text-gray-500 group-hover:text-gray-500" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z">
                            </path>
                        </svg>
                    </div>
                    {{ __('Users') }}
                </div>
                <svg x-bind:class="!open ? '' : 'rotate-90'" class="flex-shrink-0 w-5 h-5 mr-2 text-gray-500 transition-colors duration-150 ease-in-out transform pointer-events-none group-hover:text-gray-400" viewBox="0 0 20 20" aria-hidden="true">
                    <path d="M6 6L14 10L6 14V6Z" fill="currentColor"></path>
                </svg>
            </button>
            <div x-show="open" class="space-y-1">
                <a href="{{ route('users.create') }}" class="flex items-center w-full py-2 pl-10 pr-2 text-sm font-medium text-gray-500 rounded-md group hover:bg-gray-700 hover:text-white @if (url()->current() == route('users.create')) bg-gray-700 @endif">{{ __('Create') }}</a>
                <a href="{{ route('users.index') }}" class="flex items-center w-full py-2 pl-10 pr-2 text-sm font-medium text-gray-500 rounded-md group hover:bg-gray-700 hover:text-white @if (url()->current() == route('users.index')) bg-gray-700 @endif">{{ __('View') }}</a>
            </div>
        </div>
        @endif
        @if (auth()->user()->isSuperAdmin())
        <div x-data="{ open: {{ $isChecklistRoute }} }" class="space-y-1">
            <button type="button" x-on:click="open = !open" class="flex items-center justify-between w-full px-2 py-2 text-sm font-medium text-gray-500 rounded-md hover:bg-gray-700 hover:text-white group">
                <div class="flex items-center pointer-events-none">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 w-6 h-6 mr-3 text-gray-500 group-hover:text-gray-500" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z">
                            </path>
                        </svg>
                    </div>
                    {{ __('Checklist') }}
                </div>
                <svg x-bind:class="!open ? '' : 'rotate-90'" class="flex-shrink-0 w-5 h-5 mr-2 text-gray-500 transition-colors duration-150 ease-in-out transform pointer-events-none group-hover:text-gray-400" viewBox="0 0 20 20" aria-hidden="true">
                    <path d="M6 6L14 10L6 14V6Z" fill="currentColor"></path>
                </svg>
            </button>
            <div x-show="open" class="space-y-1">
                <a href="{{ route('checklist.create') }}" class="flex items-center w-full py-2 pl-10 pr-2 text-sm font-medium text-gray-500 rounded-md group hover:bg-gray-700 hover:text-white @if (url()->current() == route('users.create')) bg-gray-700 @endif">{{ __('Create') }}</a>
                <a href="{{ route('checklist.index') }}" class="flex items-center w-full py-2 pl-10 pr-2 text-sm font-medium text-gray-500 rounded-md group hover:bg-gray-700 hover:text-white @if (url()->current() == route('users.index')) bg-gray-700 @endif">{{ __('View') }}</a>
            </div>
        </div>
        @endif
    </div>

    <div class="mt-6">
        <div class="flex items-center justify-between mt-6">
            <a href="#" class="flex items-center gap-x-2">
                <img class="object-cover rounded-full h-7 w-7" src="https://images.unsplash.com/photo-1531427186611-ecfd6d936c79?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=634&h=634&q=80" alt="avatar" />
                <span class="text-sm font-medium text-gray-700">John Doe</span>
            </a>

            <a href="#" class="text-gray-500 transition-colors duration-200 rotate-180 rtl:rotate-0 hover:text-blue-500" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                </svg>
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                {{ csrf_field() }}
            </form>
        </div>
    </div>
</nav>