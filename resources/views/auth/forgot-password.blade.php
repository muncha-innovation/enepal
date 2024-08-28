@extends('master')

@section('body')
  <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gray-200 h-screen">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 tracking-widest">{{ __('Enepal') }}</h2>
      <p class="mt-2 text-center text-sm text-gray-600">
        {{ __('Reset your password') }}
      </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="rounded border-b border-gray-200 sm:rounded-lg bg-white py-8 px-4 shadow sm:px-10">
        @include('modules.shared.success_error')
        <form class="space-y-6" action="{{ route('password.email') }}" method="POST">
          @csrf
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700"> {{ __('Email') }}</label>
            <div class="mt-1">
              <input id="email" name="email" type="email" autocomplete="email" required autofocus
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
          </div>

          <div class="grid grid-cols-2 xs:grid-cols-1">
            <button type="submit"
              class="w-full xs:mb-2 mr-2 text-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
              {{ __('Submit') }}
            </button>
            <a href="/" type="submit"
              class="w-full ml-2 text-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
              {{ __('Cancel') }}
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection
