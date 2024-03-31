@extends('master')

@section('styles')
@yield('css')
@stop

@section('body')
<div id="app" class="flex">
  <div class="fixed hidden z-30 inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full" id="modal-container">
  </div>

  @include('layouts.frontend.sidebar')
  <div class="flex flex-col flex-1">
    <main class="flex-1 min-h-screen bg-white">
      @include('layouts.frontend.navbar')
      <div>
        @yield('content')
      </div>
    </main>
  </div>
</div>

@stop

@section('scripts')
@yield('js')
@endsection