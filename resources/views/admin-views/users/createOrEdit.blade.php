@extends('layouts.app')

@section('content')

<section>
  <div class="bg-white p-4 shadow rounded">
    <form class="space-y-6" action="{{ route('admin.users.store') }}" method="POST">
      @csrf
      
      <div>
          <label for="email" class="block text-sm font-medium text-gray-700">
              {{ __('Email') }}</label>
          <div class="mt-1">
              <input id="email" name="email" type="text" autocomplete="new-email" required autofocus
                  class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
          </div>
      </div>
      <div>
          <label for="name" class="block text-sm font-medium text-gray-700">
              {{ __('Full Name') }}</label>
          <div class="mt-1">
              <input id="name" name="name" type="text" autocomplete="name" required autofocus
                  class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
          </div>
      </div>
      {{-- input field for country code (selectable from variable $countries) and phone number in same row--}}
      <div class="grid grid-cols-2 gap-4">
          <div>
              <label for="country" class="block text-sm font-medium text-gray-700">
                  {{ __('Country') }}</label>
              <div class="mt-1">
                  <select id="country" name="country" required
                      class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                      @foreach ($countries as $country)
                          <option value="{{ $country->id }}">
                          {{-- country flag and country code --}}
                          <img class="pointer-events-none h-8 w-8 rounded-full"
                              src="{{$country->flag}}" alt="" style="display: inline">
                          {{$country->name}} ({{ $country->dial_code }})
                          </option>
                      @endforeach
                  </select>
              </div>
          </div>
          <div>
              <label for="phone" class="block text-sm font-medium text-gray-700">
                  {{ __('Phone Number') }}</label>
              <div class="mt-1">
                  <input id="phone" name="phone" type="text" autocomplete="phone" required
                      class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
              </div>
          </div>
      </div>

      <div>
          <label for="password" class="block text-sm font-medium text-gray-700"> {{ __('Password') }} </label>
          <div class="mt-1">
              <input id="password" name="password" type="password" autocomplete="new-password" required
                  class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
          </div>
      </div>
      <div>
          <label for="password_confirmation" class="block text-sm font-medium text-gray-700"> {{ __('Confirm Password') }} </label>
          <div class="mt-1">
              <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                  class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
          </div>
      </div>
      

      <div>
          <button type="submit"
              class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
              {{ __('Create') }}</button>
      </div>
      
  </form>
  </div>
</section>
@endsection