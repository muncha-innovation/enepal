@extends('master')

@section('styles')
    @stack('css')
    @yield('css')
@stop

@php
    $superAdmin = App\Models\User::SuperAdmin;
@endphp

@section('body')
    <div id="app">
        <div class="fixed hidden z-30 inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full" id="modal-container">
        </div>

        @include('layouts.sidebar')
        <div class="md:pl-64 flex flex-col flex-1">
            {{-- Mobile header with hamburger menu --}}
            <div class="sticky top-0 z-10 md:hidden pl-1 pt-1 sm:pl-3 sm:pt-3 bg-gray-100 flex">
                <button onclick="toggleSidebar()" type="button" class="-ml-0.5 -mt-0.5 h-12 w-12 inline-flex items-center justify-center rounded-md text-gray-500 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                    <span class="sr-only">Open sidebar</span>
                    <!-- Heroicon name: outline/menu -->
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div class="flex items-center justify-center flex-1">
                    <img class="h-8 w-auto" src="{{ asset('logo.png') }}" alt="{{ __('Enepal') }}" />
                </div>
            </div>
            
            <main class="flex-1 min-h-screen bg-gray-100">
                <div class="p-4">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
@stop

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.0.0-alpha.1/axios.min.js"></script>
    <script src="{{ asset('js/thread-management.js') }}"></script>
    <script>
        $("input[required], select[required], textarea[required]").on("invalid", function() {
            this.setCustomValidity(@json(trans('Please fill out this field')));
        });
        $("input[required], select[required], textarea[required]").on("input", function() {
            this.setCustomValidity('');
        });

        function toggleSidebar(event) {
            document.getElementsByClassName('side-nav')[0].classList.toggle('opacity-0');
            document.getElementsByClassName('side-nav')[0].classList.toggle('pointer-events-none');
            document.getElementsByClassName('overlay')[0].classList.toggle('pointer-events-auto');
            document.getElementsByClassName('side-nav-container')[0].classList.toggle('translate-x-0');
            document.getElementsByClassName('side-nav-container')[0].classList.toggle('-translate-x-full');
        }
    </script>
    @yield('js')
    @stack('js')
@endsection

