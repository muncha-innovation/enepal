@extends('layouts.app')

@section('content')
{{-- @include('modules.business.header', ['title' => 'Create Business / Organization']) --}}
<h1 class="text-2xl font-semibold text-gray-700 mb-2">Create Business / Organization</h1>

<section>
  <div class="bg-white p-4 shadow rounded">
    <form action="{{route('business.store')}}" method="POST" enctype="multipart/form-data">
      @csrf
      <div class="mb-2">
        <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Business Name</label>
        <div class="mt-2 rounded-md shadow-sm">
          <input type="text" name="name" id="name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Eg. Nepalese Association of Houston">
        </div>
      </div>

      <div class="mb-2">
        <label for="type_id" class="block text-sm font-medium leading-6 text-gray-900">Type</label>
        <select id="type_id" name="type_id" class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
          @foreach ($businessTypes as $type)
            <option value="{{$type->id}}">{{ $type->title }}</option>
            
          @endforeach
        </select>
      </div>

      <p class="text-sm mb-2 mt-4">Business Address</p>

      <div class="grid grid-cols-2 gap-4 my-2">
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
            <label for="phone_1" class="block text-sm font-medium text-gray-700">
                {{ __('Phone Number') }}</label>
            <div class="mt-1">
                <input id="phone_1" name="phone_1" type="text" autocomplete="phone_1" required
                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
        </div>
    </div>

      {{-- <div class="mb-2">
        <label for="address1" class="block text-sm font-medium leading-6 text-gray-900">Address 1</label>
        <div class="mt-2 rounded-md shadow-sm">
          <input type="text" name="address1" id="address1" class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Eg. Kathmandu, Nepal">
        </div>
      </div>

      <div class="mb-2">
        <label for="address2" class="block text-sm font-medium leading-6 text-gray-900">Address 2</label>
        <div class="mt-2 rounded-md shadow-sm">
          <input type="text" name="address2" id="address2" class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Eg. Kathmandu, Nepal">
        </div>
      </div> --}}

      <div class="mb-2">
        <label for="city" class="block text-sm font-medium leading-6 text-gray-900">City</label>
        <div class="mt-2 rounded-md shadow-sm">
          <input type="text" name="city" id="city" class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Eg. Kathmandu">
        </div>
      </div>

      <div class="mb-2">
        <label for="state" class="block text-sm font-medium leading-6 text-gray-900">State</label>
        <div class="mt-2 rounded-md shadow-sm">
          <input type="text" name="state" id="state" class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Eg. Bagmati">
        </div>
      </div>

      <div class="mb-2">
        <label for="zip" class="block text-sm font-medium leading-6 text-gray-900">Zip</label>
        <div class="mt-2 rounded-md shadow-sm">
          <input type="text" name="zip" id="zip" class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Eg. 1234">
        </div>
      </div>

      <div class="mb-2">
        <label for="phone_2" class="block text-sm font-medium leading-6 text-gray-900">Contact Person Phone</label>
        <div class="mt-2 rounded-md shadow-sm">
          <input type="text" name="phone_2" id="phone_2" class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Eg. 9812312323">
        </div>
      </div>
      <div class="mb-2">
        <label for="logo" class="block text-sm font-medium leading-6 text-gray-900">{{('Logo')}}</label>
        <input type="file" name="logo" class="cursor-pointer block w-full mt-2 text-sm text-gray-600 bg-white border border-gray-200 rounded-md file:bg-gray-200 file:text-gray-700 file:text-sm file:px-4 file:border-none file:py-2  focus:border-blue-400 focus:outline-none focus:ring focus:ring-blue-300 focus:ring-opacity-40" />
      </div>
      <div class="mb-2">
        <label for="cover_image" class="block text-sm font-medium leading-6 text-gray-900">Cover Image</label>
        <input type="file" name="cover_image" class="cursor-pointer block w-full mt-2 text-sm text-gray-600 bg-white border border-gray-200 rounded-md file:bg-gray-200 file:text-gray-700 file:text-sm file:px-4 file:border-none file:py-2  focus:border-blue-400 focus:outline-none focus:ring focus:ring-blue-300 focus:ring-opacity-40" />
      </div>

      <div class="flex justify-end w-full">
        <div>
          <button type="submit" class="inline-block w-full px-8 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Save</button>
        </div>
      </div>
    </form>
  </div>
</section>

@endsection