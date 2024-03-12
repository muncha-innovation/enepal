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
            {{-- @include('modules.includes.navbar') --}}
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
    <script>
        $("input[required], select[required], textarea[required]").on("invalid", function() {
            this.setCustomValidity(@json(trans('Please fill out this field')));
        });
        $("input[required], select[required], textarea[required]").on("input", function() {
            this.setCustomValidity('');
        });

        function toggleSidebar(event) {
            document.getElementsByClassName('side-nav')[0].classList.toggle('opacity-0');
            document.getElementsByClassName('overlay')[0].classList.toggle('pointer-events-auto');
            document.getElementsByClassName('side-nav-container')[0].classList.toggle('translate-x-0');
            document.getElementsByClassName('side-nav-container')[0].classList.toggle('-translate-x-full');
        }
    </script>
    @yield('js')
    @stack('js')
@endsection
